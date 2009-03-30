<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli WebCore.

earthli WebCore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli WebCore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli WebCore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli WebCore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/util/archive.php');
require_once ('third_party/pclzip-2-1/pclzip.lib.php');

/**
 * Extended archive that provides PHP-independent support for ZIP files.
 * Some PHP versions include support for ZIP files; others do not. Use this version
 * of the archive to seamlessly fall back to use "PclZip" when support in PHP is
 * missing.
 * @package webcore
 * @subpackage util
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class PCL_ARCHIVE extends ARCHIVE
{
  /**
   * @param string $file_name
   */
  public function PCL_ARCHIVE ($file_name)
  {
    ARCHIVE::ARCHIVE ($file_name);
    $this->register_handler ('PCL_ZIP_FILE', '');
  }
}

/**
 * PHP-independent support for ZIP files.
 * Used by the {@link PCL_ARCHIVE} to provide seamless support for ZIP files, regardless of the software
 * installed on the server.
 * @package webcore
 * @subpackage util
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class PCL_ZIP_FILE extends COMPRESSED_FILE
{
  public function PCL_ZIP_FILE ($file_name)
  {
    COMPRESSED_FILE::COMPRESSED_FILE ($file_name);
    $this->_entries = 0;
  }

  /**
   * @var boolean
   */
  public function is_open ()
  {
    return $this->_entries;
  }

  public function close ()
  {
    $this->_zip = null;
    $this->_entries = 0;
  }

  /**
   * Execute the 'file_callback' for each file entry.
   * The zip file is automatically closed when complete (the iterator returned by PHP is one-way).
   * @param CALLBACK $file_callback Function prototype: function ({@link COMPRESSED_FILE} $archive, {@link COMPRESSED_FILE_ENTRY} $entry, {@link CALLBACK} $error_callback = null)
   * @param CALLBACK $error_callback Function prototype: function ({@link COMPRESSED_FILE} $archive, string $msg, {@link COMPRESSED_FILE_ENTRY} $entry)
   */
  protected function _for_each ($file_callback, $error_callback = null)
  {
    $opts = global_file_options ();
    $sep = $opts->path_delimiter;

    foreach ($this->_entries as $zip_entry)
    {
      if (($zip_entry ['status'] == 'ok') && ! ($zip_entry ['folder']))
      {
        $entry = new PCL_ZIP_ENTRY ($this, $this->_zip, $zip_entry);
        $entry->name = str_replace ('/', $sep, $zip_entry ['filename']);
        $entry->number = $zip_entry ['index'] + 1;
        $entry->size = $zip_entry ['size'];
        $entry->compressed_size = $zip_entry ['compressed_size'];

        $file_callback->execute (array ($this, $entry, $error_callback));
      }
    }
  }

  /**
   * @access private
   */
  protected function _open ()
  {
    $this->_zip = new PclZip ($this->file_name);
    $this->_entries = $this->_zip->listContent ();
  }

  /**
   * @var array
   * @access private
   */
  protected $_entries;
}

/**
 * A single entry found in a {@link PCL_ZIP_FILE}.
 * @package webcore
 * @subpackage util
 * @version 3.0.0
 * @since 2.5.0
 * @access private
 */
class PCL_ZIP_ENTRY extends COMPRESSED_FILE_ENTRY
{
  /**
   * Size of block to use when reading zip files.
   * @var integer
   */
  public $read_block_size = 2048;

  /**
   * @param COMPRESSED_FILE $file
   * @param $PclZip $zip Reference to a object.
   * @param object $entry The entry from which to extract. 
   */
  public function PCL_ZIP_ENTRY ($file, $zip, $entry)
  {
    COMPRESSED_FILE_ENTRY::COMPRESSED_FILE_ENTRY ($file);
    $this->_zip = $zip;
    $this->_entry = $entry;
    $this->_index = $entry ['index'];
  }

  /**
   * Extract using the zip algorithm.
   * @param string $path
   * @param CALLBACK $error_callback
   * @access private
   */
  protected function _extract_to ($path, $error_callback)
  {
    $path = substr ($path, 0, - strlen ($this->name));
    if (! $this->_zip->extractByIndex ($this->_index, PCLZIP_OPT_PATH, $path))
    {
      $this->_report_error ($error_callback, "Could not open zip entry [$this->name].");
    }
  }

  /**
   * Reference to a value returned from {@link PHP_MANUAL#zip_entry_open()}
   * @var resource
   * @access private
   */
  protected $_entry;

  /**
   * Reference to a value returned from {@link PHP_MANUAL#zip_open()}
   * @var resource
   * @access private
   */
  protected $_zip;
}

?>