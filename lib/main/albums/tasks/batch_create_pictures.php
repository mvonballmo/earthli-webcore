<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage tasks
 * @version 3.4.0
 * @since 2.8.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

/** */
require_once ('webcore/sys/task.php');

/**
 * Extract and create a set of {@link PICTURE}s in an {@link ALBUM}.
 * Used by the {@link UPLOAD_PICTURE_FORM}.
 * @package albums
 * @subpackage forms
 * @version 3.4.0
 * @since 2.8.0
 * @access private
 */
class BATCH_CREATE_PICTURES_TASK extends TASK
{
  /**
   * Title of the processing web page.
   * @var string
   */
  public $page_title = 'Import pictures';

  /**
   * Log all messages in this channel.
   * @var string
   */
  public $log_channel = 'Import';

  /**
   * Archive from which to extract pictures.
   * @var string
   */
  public $archive_file_name;

  /**
   * Extract the date from each picture?
   * @var boolean
   */
  public $read_exif;

  /**
   * Create a thumbnail for each picture?
   * @var boolean
   */
  public $create_thumbnail;

  /**
   * Maximum width or height of the thumbnail.
   * Aspect ratio is preserved.
   * @var integer
   */
  public $thumbnail_size;

  /**
   * Picture titles are created using this template.
   * Use {#} to include the picture number and {file} to include the file name
   * without the extension. For example, if the template is 'Picture {#} - {file}',
   * file1.jpg will have the title 'Picture 1 - file1'.
   * @var string
   */
  public $file_name_template;

  /**
   * Use this date if picture dates are not available or wanted.
   * @see $read_exif
   * @var DATE_TIME
   */
  public $default_date;

  /**
   * Start numbering new pictures from here.
   * @var integer
   */
  public $starting_index = 1;

  /**
   * @param FOLDER $folder Create pictures in this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder->context);
    $this->_folder = $folder;
  }

  /**
   * Initialize any loggers needed for the migration process.
   * @access private
   */
  protected function _set_up_logging ()
  {
    parent::_set_up_logging ();
    $this->_logger->show_date = false;
  }

  /**
   * Perform the actual process actions.
   * This is task- and application-specific.
   * @access private
   */
  protected function _execute ()
  {
    $class_name = $this->app->final_class_name ('ARCHIVE', 'webcore/util/archive.php');
    $archive = new $class_name ($this->archive_file_name);

    log_open_block ('Extracting files from archive...');
      $this->_target_directory = url_to_file_name ($this->_folder->picture_folder_url (Force_root_on));
      $this->_num_pictures_imported = 0;
      $archive->for_each (new CALLBACK_METHOD ('process_image', $this), new CALLBACK_METHOD ('show_error', $this));
    log_close_block ();

    $php_errormsg = null;
    @unlink ($this->archive_file_name);
    if (isset ($php_errormsg))
    {
      $this->_log ("Could not delete archive: " . $php_errormsg, Msg_type_warning);
    }
    else
    {
      $this->_log ("Deleted archive.", Msg_type_info);
    }
  }

  /**
   * Initialize any loggers needed for the migration process.
   * @access private
   */
  protected function _finish ()
  {
    $t = $this->_folder->title_formatter ();
    $t->text = 'View pictures';
    $t->add_argument ('time_frame', 'recent');
    $t->add_argument ('panel', 'picture');
    $link_text = $this->_folder->title_as_link ($t);
    $this->_log ("Imported [$this->_num_pictures_imported] pictures. ($link_text)", Msg_type_info, true);
    parent::_finish ();
  }

  /**
   * Called once for each image in the archive.
   * @param COMPRESSED_FILE $archive
   * @param COMPRESSED_FILE_ENTRY $entry
   * @param CALLBACK $error_callback
   * @access private
   */
  public function process_image ($archive, $entry, $error_callback)
  {
    log_open_block ("Extracting [$entry->name]...");
    $entry->extract_to ($this->_target_directory, $error_callback);

    $class_name = $this->app->final_class_name ('IMAGE', 'webcore/util/image.php');
    /** @var IMAGE $img */
    $img = new $class_name ();
    $img->set_file ($entry->extracted_name, $this->read_exif);
    if ($img->loadable ())
    {
      $img->load_from_file ();

      $url = new FILE_URL ($entry->extracted_name);

      if ($this->create_thumbnail)
      {
        if ($img->saveable ())
        {
          $url->append_to_name ('_tn');
          $thumbnail_name = $url->as_text ();

          $this->_log ("Creating thumbnail...", Msg_type_info);

          $img->resize_to_fit ($this->thumbnail_size, $this->thumbnail_size);
          $img->save_to_file ($thumbnail_name);
        }
        else
        {
          $this->_log ("Could not create thumbnail.", Msg_type_error);
        }
      }

      $pic_date = $this->default_date;
      if ($this->read_exif)
      {
        if ($img->properties->exists () && $img->properties->time_created->is_valid ())
        {
          $pic_date = $img->properties->time_created;
        }
        else
        {
          $this->_log ("Could not read date from file (using default).", Msg_type_warning);
        }
      }

      $original_url = new FILE_URL ($entry->name);
      $file_name_only = $original_url->name_without_extension ();
      $pic_title = $this->file_name_template;
      $pic_title = str_replace ('{#}', $this->_num_pictures_imported + $this->starting_index, $pic_title);
      $pic_title = str_replace ('{file}', $file_name_only, $pic_title);

      $pic = $this->_folder->new_object ('picture');
      $pic->title = $pic_title;
      $pic->file_name = $entry->normalized_name;
      $pic->date = $pic_date;
      $history_item = $pic->new_history_item ();
      $pic->store_if_different ($history_item);
      $this->_num_pictures_imported = $this->_num_pictures_imported + 1;

      $this->_log ("Created picture [$pic->title]", Msg_type_info);
    }
    else
    {
      $php_errormsg = null;
      @unlink ($entry->extracted_name);
      if (isset ($php_errormsg))
      {
        $this->_log ("This is not an image file (could not delete file on server): " . $php_errormsg, Msg_type_error);
      }
      else
      {
        $this->_log ("This is not an image file (file was deleted on the server).", Msg_type_warning);
      }
    }

    log_close_block ();
  }

  /**
   * Called by the archive to report errors.
   * @param COMPRESSED_FILE $archive
   * @param string $msg
   * @param COMPRESSED_FILE_ENTRY $entry
   * @access private
   */
  public function show_error ($archive, $msg, $entry)
  {
    $this->_log ($msg, Msg_type_error);
  }

  /**
   * Location of the album's image folder on the server.
   * @var string
   * @access private
   */
  protected $_target_directory;

  /**
   * @var integer
   * @access private
   */
  protected $_num_pictures_imported;
}
?>