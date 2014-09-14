<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/sys/system.php');
require_once ('webcore/sys/callback.php');
require_once ('webcore/sys/files.php');

/**
 * Wrapper class for accessing {@link COMPRESSED_FILE}s.
 * Where a {@link COMPRESSED_FILE} can only handle a single type of file,
 * the archive manages a list of handlers (registered with {@link
 * register_handler()}). Use the {@link readable()} function to determine
 * whether the archive has a handler capable of reading a file set by {@link
 * set_file_name()} or passed to the constructor. If the file is readable, use
 * {@link extract_to()} and {@link for_each()} to manipulate the contents.
 *
 * The zip format is supported natively through the PHP zip functions.
 *
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.5.0
 */
class ARCHIVE
{
  /**
   * Can be in any URL usable by PHP.
   * @var string
   */
  public $file_name;

  /**
   * @param string $file_name
   */
  public function __construct ($file_name)
  {
    $this->register_handler ('ZIP_FILE', '');
    $this->set_file_name ($file_name);
  }

  public function set_file_name ($file_name)
  {
    $this->_handler = null;
    $this->file_name = $file_name;
  }

  /**
   * Add an archive format handler.
   * The class should inherit from {@link COMPRESSED_FILE} so it has the correct interface. The archive
   * uses this list of handlers to open a given file; as soon as a handler can open the file, it will be
   * used.
   * @param string $class_name
   * @param string $file_name
   */
  public function register_handler ($class_name, $file_name)
  {
    $handler = new stdClass();
    $handler->class_name = $class_name;
    $handler->file_name = $file_name;
    $this->_handlers [] = $handler;
  }

  /**
   * Is this an archive?
   * @return boolean
   */
  public function readable ()
  {
    $this->_init_handler ();
    return isset ($this->_handler);
  }

  /**
   * Execute the 'file_callback' for each file entry.
   * @param WEBCORE_CALLBACK $file_callback Function prototype: function ({@link
   * COMPRESSED_FILE} $archive, {@link COMPRESSED_FILE_ENTRY} $entry, {@link
   * CALLBACK} $error_callback = null)
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function ({@link
   * COMPRESSED_FILE} $archive, string $msg, {@link COMPRESSED_FILE_ENTRY}
   * $entry)
   */
  public function for_each ($file_callback, $error_callback = null)
  {
    $this->_init_handler ();
    if (isset ($this->_handler))
    {
      $this->_handler->open ($error_callback);
      $this->_handler->for_each ($file_callback, $error_callback);
    }
  }

  /**
   * Extract all files in {@link $file_name} to 'path'.
   * @param string $path Valid path in the local file system.
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function ({@link
   * COMPRESSED_FILE} $archive, string $msg, {@link COMPRESSED_FILE_ENTRY}
   * $entry)
   */
  public function extract_to ($path, $error_callback = null)
  {
    $this->_init_handler ();
    if (isset ($this->_handler))
    {
      $this->_handler->open ($error_callback);
      $this->_handler->extract_to ($path, $error_callback);
    }
  }
  
  /**
   * Find and initialize a handler for the current file.
   * Searches the handlers registered with {@link register_handler()}.
   * @access private
   */
  protected function _init_handler ()
  {
    if (! isset ($this->_handler))
    {
      if ($this->file_name)
      {
        $idx_handler = 0;
        while (! isset ($this->_handler) && ($idx_handler < sizeof ($this->_handlers)))
        {
          $handler = $this->_handlers [$idx_handler];
          $class_name = $handler->class_name;
          if ($handler->file_name)
          {
            include_once ($handler->file_name);
          }
          $this->_handler = new $class_name ($this->file_name);
          $this->_handler->open ();
          if ($this->_handler->is_open ())
          {
            $this->_handler->close ();
          }
          else
          {
            $this->_handler = null;
          }
          $idx_handler += 1;
        }
      }
    }
  }

  /**
   * Actual reader/extractor.
   * If this is not set, {@link _init_handler()} is called to find a compressed file reader
   * that can read the file.
   * @var COMPRESSED_FILE
   * @access private
   */
  protected $_handler;

  /**
   * List of archive format handlers.
   * @see ARCHIVE_HANDLER
   * @var ARCHIVE_HANDLER[]
   * @access private
   */
  protected $_handlers;
}

/**
 * Meta information for an {@link ARCHIVE} handler.
 * The archive uses these internally to create registered handlers for {@link
 * COMPRESSED_FILE}s.
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.7.0
 * @access private
 */
class ARCHIVE_HANDLER
{
  /**
   * Name of the handler class to create.
   * @var string
   */
  public $class_name;

  /**
   * Location of the {@link $class_name}.
   * May be empty if the file is guaranteed to be included.
   */
  public $file_name;
}

/**
 * General support for a compressed file.
 * Create with a file name and call {@link open()}, then {@link is_open()}
 * to determine whether the file can be read with this class. If so, use {@link
 * extract_to()} and {@link for_each()} to manipulate the file. Used by the
 * {@link ARCHIVE} internally, but descendents may also be used directly.
 * @package webcore
 * @subpackage util
 * @access private
 * @version 3.6.0
 * @since 2.5.0
 * @abstract
 */
abstract class COMPRESSED_FILE extends RAISABLE
{
  /**
   * Can be in any URL usable by PHP.
   * @var string
   */
  public $file_name;

  /**
   * @param string $file_name
   */
  public function __construct ($file_name)
  {
    $this->file_name = $file_name;
  }

  /**
   * @abstract
   */
  public abstract function is_open ();

  /**
   * Open the {@link $file_name} for reading.
   * 
   * If it cannot be opened, an error is recorded to the given 'error_callback'. 
   * 
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function (string, {@link COMPRESSED_FILE_ENTRY})
   */
  public function open ($error_callback = null)
  {
    $this->_open ();
    if (! $this->is_open () && isset ($error_callback))
    {
      $error_callback->execute (array ($this, "Could not open file."));
    }
  }

  /**
   * @abstract
   */
  public abstract function close ();

  /**
   * Execute the 'file_callback' for each file entry.
   * @param WEBCORE_CALLBACK $file_callback Function prototype: function ({@link COMPRESSED_FILE} $archive, {@link COMPRESSED_FILE_ENTRY} $entry, {@link CALLBACK} $error_callback = null)
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function ({@link COMPRESSED_FILE} $archive, string $msg, {@link COMPRESSED_FILE_ENTRY} $entry)
   */
  public function for_each ($file_callback, $error_callback = null)
  {
    $this->assert ($this->is_open (), "[$this->file_name] is not open.", 'for_each', 'COMPRESSED_FILE');
    $this->_for_each ($file_callback, $error_callback);
  }

  /**
   * Extract all files to 'path'.
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function (string, {@link COMPRESSED_FILE_ENTRY})
   */
  public function extract_to ($path, $error_callback = null)
  {
    $this->_target_path = $path;
    $this->for_each (new CALLBACK_METHOD ('_extract_file', $this), $error_callback);
  }

  /**
   * Extract 'entry' to the target path.
   * This function is called for each file in the compressed file when {@link extract_to()} is called.
   * @param COMPRESSED_FILE $comp_file
   * @param COMPRESSED_FILE_ENTRY $entry
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function ({@link COMPRESSED_FILE} $archive, string $msg, {@link COMPRESSED_FILE_ENTRY} $entry)
   * @access private
   */
  public function _extract_file ($comp_file, $entry, $error_callback)
  {
    $entry->extract_to ($this->_target_path, $error_callback);
  }

  /**
   * Opens the file using the implementation-specific underlying driver.
   * 
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function (string, {@link COMPRESSED_FILE_ENTRY})
   * @access private
   * @abstract
   */
  protected abstract function _open ($error_callback = null);

  /**
   * Execute the 'file_callback' for each file entry.
   * @param WEBCORE_CALLBACK $file_callback Function prototype: function ({@link COMPRESSED_FILE}, {@link COMPRESSED_FILE_ENTRY})
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function ({@link COMPRESSED_FILE} $archive, string $msg, {@link COMPRESSED_FILE_ENTRY} $entry)
   * @access private
   * @abstract
   */
  protected abstract function _for_each ($file_callback, $error_callback = null);
}

/**
 * A single entry in a {@link COMPRESSED_FILE}.
 * This class is format-independent (represents an interface that can be a zip, tar, gzip file).
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.5.0
 * @access private
 * @abstract
 */
abstract class COMPRESSED_FILE_ENTRY extends RAISABLE
{
  /**
   * Name of the entry in the compressed file.
   * May include path information if the compressed file has folders included.
   * @var string
   */
  public $name;

  /**
   * Position of the file in the archive.
   * @var integer
   */
  public $number;

  /**
   * Size of file in the archive.
   * May not be available for all compressed file types.
   * @var integer
   */
  public $compressed_size;

  /**
   * Uncompressed size of the file.
   * @var integer
   */
  public $size;

  /**
   * The path to the location where the file was extracted.
   * This property holds the last file produced when {@link extract_to()} was called. May be empty.
   * @var string
   */
  public $extracted_name;

  /**
   * The {@link $name} normalized for the target file system.
   * This name is calculated using the standard FILE_URL options when the entry is created.
   * @var string
   */
  public $normalized_name;

  /**
   * @param COMPRESSED_FILE $file
   */
  public function __construct ($file)
  {
    $this->_file = $file;
  }

  /**
   * Returns  '0' if the compression is not defined.
   * @return integer
   */
  public function compression_percentage ()
  {
    if (isset ($this->compressed_size))
    {
      if ($this->size <= $this->compressed_size)
      {
        return 0;
      }

      return 100 * round (($this->size - $this->compressed_size) / $this->size, 2);
    }

    return 0;
  }

  /**
   * Extract the file to the given location.
   * If there is an error in the extraction, it is communicated to the optional 'error_callback'
   * instead of raising an exception (which stops the script).
   * @param string $path
   * @param WEBCORE_CALLBACK $error_callback
   */
  public function extract_to ($path, $error_callback = null)
  {
    $url = new FILE_URL ($path);
    $url->append ($this->normalized_name);
    ensure_path_exists ($url->path ());
    $this->extracted_name = $url->as_text ();

    $this->_extract_to ($this->extracted_name, $error_callback);
  }

  /**
   * Format-dependent extraction algorithm.
   * @param string $path
   * @param WEBCORE_CALLBACK $error_callback
   * @access private
   * @abstract
   */
  protected abstract function _extract_to ($file, $error_callback);

  /**
   * Report an error.
   * @param WEBCORE_CALLBACK $error_callback
   * @param string $msg
   * @access private
   */
  protected function _report_error ($error_callback, $msg)
  {
    if (isset ($error_callback))
    {
      $error_callback->execute (array ($this->_file, $msg, $this));
    }
  }

  /**
   * File in which this entry was found.
   * @var COMPRESSED_FILE
   * @access private
   */
  protected $_file;
}

/**
 * PHP-native support for ZIP files.
 * Used by the {@link ARCHIVE} to provide seamless support for ZIP files.
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.5.0
 * @access private
 */
class ZIP_FILE extends COMPRESSED_FILE
{
  public function is_open ()
  {
    return isset ($this->_handle);
  }

  public function close ()
  {
    zip_close ($this->_handle);
    $this->_handle = null;
  }

  /**
   * Execute the 'file_callback' for each file entry.
   * The zip file is automatically closed when complete (the iterator returned by PHP is one-way).
   * @param WEBCORE_CALLBACK $file_callback Function prototype: function ({@link COMPRESSED_FILE} $archive, {@link COMPRESSED_FILE_ENTRY} $entry, {@link CALLBACK} $error_callback = null)
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function ({@link COMPRESSED_FILE} $archive, string $msg, {@link COMPRESSED_FILE_ENTRY} $entry)
   */
  protected function _for_each ($file_callback, $error_callback = null)
  {
    $opts = global_file_options ();
    $file_num = 0;

    while (($zip_entry = zip_read ($this->_handle)))
    {
      $size = zip_entry_filesize ($zip_entry);
      if ($size > 0)
      {
        $file_num += 1;

        $entry = new ZIP_ENTRY ($this, $this->_handle, $zip_entry, $opts);
        $entry->name = zip_entry_name ($zip_entry);
        $entry->normalized_name = normalize_path ($entry->name, $opts);
        $entry->number = $file_num;
        $entry->size = $size;
        $entry->compressed_size = zip_entry_compressedsize ($zip_entry);

        $file_callback->execute (array ($this, $entry, $error_callback));
      }
    }

    $this->close ();
  }

  /**
   * Opens the file using the implementation-specific underlying driver.
   * 
   * @param WEBCORE_CALLBACK $error_callback Function prototype: function (string, {@link COMPRESSED_FILE_ENTRY})
   * @access private
   */
  protected function _open ($error_callback = null)
  {
    $this->_handle = null;
    if (function_exists ('zip_open'))
    {
      $this->_handle = @zip_open ($this->file_name);
      if (!is_resource($this->_handle))
      {
        $this->_handle = null;
      }
    }
  }

  /**
   * @var resource
   * @access private
   */
  protected $_handle;
}

/**
 * A single entry found in a {@link ZIP_FILE}.
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.5.0
 * @access private
 */
class ZIP_ENTRY extends COMPRESSED_FILE_ENTRY
{
  /**
   * Size of block to use when reading zip files.
   * @var integer
   */
  public $read_block_size = 2048;

  /**
   * @param COMPRESSED_FILE $file
   * @param resource $zh Handle returned by {@link PHP_MANUAL#zip_open()}
   * @param resource $h Handle returned by {@link PHP_MANUAL#zip_entry_open()}
   */
  public function __construct ($file, $zh, $h)
  {
    parent::__construct ($file);
    $this->_zip_handle = $zh;
    $this->_handle = $h;
  }

  /**
   * Extract using the zip algorithm.
   * @param string $path
   * @param WEBCORE_CALLBACK $error_callback
   * @access private
   */
  protected function _extract_to ($path, $error_callback)
  {
    if (zip_entry_open ($this->_zip_handle, $this->_handle))
    {
      $f = @fopen ($path, 'wb');
      if ($f === false)
      {
        $this->_report_error ($error_callback, "Could not open destination file [$path].");
      }
      else
      {
        while (($data = zip_entry_read ($this->_handle, $this->read_block_size)) != false)
        {
          fwrite ($f, $data);
        }
        fclose ($f);
      }
      zip_entry_close ($this->_handle);
    }
    else
    {
      $this->_report_error ($error_callback, "Could not open zip entry [$this->name].");
    }
  }

  /**
   * Reference to a value returned from {@link PHP_MANUAL#zip_entry_open()}
   * @var resource
   * @access private
   */
  protected $_handle;

  /**
   * Reference to a value returned from {@link PHP_MANUAL#zip_open()}
   * @var resource
   * @access private
   */
  protected $_zip_handle;
}

?>