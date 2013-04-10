<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
 * @version 3.3.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('webcore/sys/files.php');
require_once ('webcore/sys/url.php');

/**
 * File name key in the uploaded data.
 */
define ('Uploaded_file_name', 'name');
/**
 * File size key in the uploaded data.
 */
define ('Uploaded_file_size', 'size');
/**
 * File error key in the uploaded data.
 */
define ('Uploaded_file_error', 'error');
/**
 * File type key in the uploaded data.
 */
define ('Uploaded_file_type', 'type');
/**
 * File temp name key in the uploaded data.
 */
define ('Uploaded_file_temp_name', 'tmp_name');

/**
 * No upload error.
 */
define ('Uploaded_file_error_none', 0);
/**
 * File size exceeds size specified in the INI.
 */
define ('Uploaded_file_error_ini_size', 1);
/**
 * File size exceeds size specified in the form.
 */
define ('Uploaded_file_error_form_size', 2);
/**
 * File was only partially uploaded.
 */
define ('Uploaded_file_error_partial', 3);
/**
 * File was no uploaded.
 */
define ('Uploaded_file_error_missing', 4);

/**
 * Generate unique file name; do not overwrite existing file.
 */
define ('Uploaded_file_unique_name', 'unique');
/**
 * Overwrite existing file.
 */
define ('Uploaded_file_overwrite', 'overwrite');

/**
 * Special PHP field for limiting the size of file uploads.
 * This name is reserved and should not be used for any {@link FIELD::$id}s.
 */
define ('Form_max_file_size_field_name', 'MAX_FILE_SIZE');

/**
 * Represents a file uploaded by PHP.
 * @package webcore
 * @subpackage util
 * @version 3.3.0
 * @since 2.5.0
 */
class UPLOADED_FILE extends RAISABLE
{
  /**
   * Original name (name from client file system).
   * @var string
   */
  public $name;

  /**
   * Number of bytes in file.
   * @var integer
   */
  public $size;

  /**
   * MIME type of the file.
   * May be empty if the server/browser does not specify it or figure it out.
   * @var string
   */
  public $mime_type;

  /**
   * Error code associated with this file.
   * May be {@link Uploaded_file_error_none}, {@link Uploaded_file_error_ini_size},
   * {@link Uploaded_file_error_form_size}, {@link Uploaded_file_error_partial} or
   * {@link Uploaded_file_error_missing}.
   * @var integer
   */
  public $error;

  /**
   * Version of {@link $name} that is file-system-valid.
   * This name is initialized using {@link normalize_file_name()}.
   * @var string
   */
  public $normalized_name;

  /**
   * Has the file for this field been processed?
   * Mark the file as processed when it has been uploaded and successfully processed (moved, copied
   * extracted, etc.) and does not need to be uploaded again. A call to {@link move_to()} will automatically
   * mark a file as processed.
   *
   * If so, the form will display the file name itself instead of a selector field so that the
   * user doesn't upload another file when the form is re-submitted (in case of validation errors
   * in the form).
   * @var boolean
   */
  public $processed = false;

  /**
   * @param UPLOADER $uploader Attached to this uploader.
   * @param string $name Original name of the uploaded file.
   * @param integer $size Size of the file.
   * @param string $type MIME type of the file.
   * @param string $temp_name Name of the file as uploaded.
   * @param integer $error Error code.
   */
  public function __construct ($uploader, $name, $size, $mime_type, $temp_name, $error)
  {
    $this->_uploader = $uploader;
    $this->name = $name;
    $this->size = $size;
    $this->mime_type = $mime_type;
    $this->temp_name = $temp_name;
    $this->error = $error;

    $this->normalized_name = normalize_file_name ($name);
  }

  /**
   * Full name and path for the file.
   * Use this to access the file without knowing whether or not it has been moved.
   * @return string
   */
  public function current_name ()
  {
    if (isset ($this->_final_name_and_path))
    {
      return $this->_final_name_and_path;
    }

    return $this->temp_name;
  }

  /**
   * Can the file be moved?
   * @return boolean
   */
  public function is_valid ()
  {
    return ($this->error == Uploaded_file_error_none);
  }

  /**
   * Does the normalized file name exist in the given path?
   * @param string $path
   * @return boolean
   */
  public function exists_in ($path)
  {
    return file_exists ($path . $this->normalized_name);
  }

  /**
   * Can the normalized file name be overwritten in the given path?
   * @param string $path
   * @return boolean
   */
  public function overwritable_in ($path)
  {
    return $this->exists_in ($path) && is_writable ($path . $this->normalized_name);
  }

  public function is_moveable_to ($path, $options = Uploaded_file_unique_name)
  {
    return (is_uploaded_file ($this->temp_name)
            && is_dir ($path)
            && (($options == Uploaded_file_unique_name)
                || ! $this->exists_in ($path)
                || $this->overwritable_in ($path)
                )
            );
  }

  /**
   * Returns a readable version of the given size.
   * Translates to MB, KB, etc.
   * @param integer $size
   * @return string
   */
  public function size_as_text ()
  {
    return file_size_as_text ($this->size);
  }

  /**
   * Move the file to the new location.
   * @param string $path
   * @param string $options Can be {@link Uploaded_file_unique_name} or {@link Uploaded_file_overwrite}.
   */
  public function move_to ($path, $options = Uploaded_file_unique_name)
  {
    $final_name = $this->normalized_name;
    
    if (($options == Uploaded_file_unique_name) || ($this->current_name () != ($path . $final_name)))
    {
      if ($options == Uploaded_file_unique_name)
      {
        while (file_exists ($path . $final_name))
        {
          $url = new FILE_URL ($final_name);
          $url->append_to_name ('_' . uniqid (rand ()));
          $final_name = $url->as_text ();
        }
      }

      ensure_path_exists ($path);

      if (! file_exists ($path))
      {
        $this->raise ("Could not create [$path] on server.", 'move_to', 'UPLOADED_FILE');
      }
      else
      {
        /* If the file has already been moved, use the normal move function to place it in the
           new directory. */

        if (isset ($this->_final_name_and_path))
        {
          if (($options == Uploaded_file_overwrite) && file_exists ($path . $final_name))
          {
            unlink ($path . $final_name);
          }
          rename ($this->_final_name_and_path, $path . $final_name);
          $this->processed = true;
        }
        else
        {
          if (move_uploaded_file ($this->temp_name, $path . $final_name))
          {
            $this->_final_name_and_path = $path . $final_name;
            $opts = global_file_options (); 
            chmod ($this->_final_name_and_path, $opts->default_access_mode);
            $this->processed = true;
          }          
        }
        
        if (! file_exists ($path . $final_name))
        {
          $this->raise ("Could not move [" . $this->current_name () . "] to [$path$final_name]", 'move_to', 'UPLOADED_FILE');        
        }
      }
    }
  }

  /**
   * Return an error message for this file.
   * @return string
   */
  public function error_message ()
  {
    switch ($this->error)
    {
    case Uploaded_file_error_none:
      return 'No error.';
    case Uploaded_file_error_ini_size:
      return "$this->name is too large.";
    case Uploaded_file_error_form_size:
      return "$this->name is too large.";
    case Uploaded_file_error_partial:
      return "$this->name was only partially uploaded.";
    case Uploaded_file_error_missing:
      if ($this->name)
      {
        return "$this->name was not uploaded.";
      }

      return "Required file was not uploaded.";
    default:
      return "Unknown error (code $this->error).";
    }
  }

  /**
   * Load file information from a string.
   * The file set to which this file belongs is returned.
   * @param string $info
   * @return string $file_set_id
   */
  public function load_from_text ($info)
  {
    $parts = explode (':', $info);
    $this->name = urldecode ($parts [1]);
    $this->size = $parts [2];
    $this->mime_type = $parts [3];
    $this->normalized_name = normalize_file_name ($this->name);
    $this->_final_name_and_path = urldecode ($parts [4]);
    $this->processed = true;
    return $parts [0];
  }

  /**
   * Pack file information into a single string.
   * This string should be encoded for storage in a form field value property.
   * @param string $id Unique id under which to store the information.
   * @return string
   */
  public function store_to_text ($id)
  {
    $name = urlencode ($this->name);
    return $id . ':' . $name . ':' . $this->size . ':' . $this->mime_type . ':' . urlencode ($this->current_name ());
  }

  /**
   * @var UPLOADER
   * @access private
   */
  protected $_uploader;

  /**
   * Full and path after the file has been moved.
   * Once the file is moved with {@link move_to()}, this variable is set to the name
   * that was used. The file name may be adjusted if that file already existed there.
   * @var string
   * @access private
   */
  protected $_final_name_and_path;

  /**
   * Current location of file on server.
   * The uploaded file is stored to this name when it is uploaded to the server.
   * @var string
   * @access private
   */
  public $temp_name;
}

/**
 * A set of files associated with an upload field.
 * Each unique input in a form is associated with a file set, regardless of how many controls
 * were actually associated with the input name.
 * @package webcore
 * @subpackage util
 * @version 3.3.0
 * @since 2.5.0
 */
class UPLOADED_FILE_SET
{
  /**
   * List of uploaded files.
   * @var array[UPLOADED_FILE]
   * @see UPLOADED_FILE
   */
  public $files = array ();

  /**
   * @param UPLOADER $uploader Attached to this uploader.
   * @param string $field_name Name of the field for which this set was submitted.
   */
  public function __construct ($uploader, $field_name)
  {
    $this->_uploader = $uploader;

    if (isset ($_FILES [$field_name]))
    {
      $file_info = $_FILES [$field_name];

      if (is_array ($file_info [Uploaded_file_name]))
      {
        foreach ($file_info [Uploaded_file_name] as $idx => $name)
        {
          if (isset ($file_info [Uploaded_file_error][$idx]))
          {
            $error = $file_info [Uploaded_file_error][$idx];
          }
          else
          {
            $error = Uploaded_file_error_none;
          }

          $this->_process_file ($file_info [Uploaded_file_name][$idx],
                                $file_info [Uploaded_file_size][$idx],
                                $file_info [Uploaded_file_type][$idx],
                                $file_info [Uploaded_file_temp_name][$idx],
                                $error);
        }
      }
      else
      {
        $error = read_array_index ($file_info, Uploaded_file_error, Uploaded_file_error_none);

        $this->_process_file ($file_info [Uploaded_file_name],
                              $file_info [Uploaded_file_size],
                              $file_info [Uploaded_file_type],
                              $file_info [Uploaded_file_temp_name],
                              $error);
      }
    }
  }

  /**
   * Were all files successfully uploaded?
   * @return boolean
   */
  public function is_valid ()
  {
    foreach ($this->files as $file)
    {
      if (! $file->is_valid ())
      {
        return false;
      }
    }

    return true;  // All files are valid
  }

  /**
   * Move all files to the new location.
   * @param string $path
   * @param string $options Can be {@link Uploaded_file_unique_name} or {@link Uploaded_file_overwrite}.
   */
  public function move_to ($path, $options = Uploaded_file_unique_name)
  {
    foreach ($this->files as &$file)
    {
      $file->move_to ($path, $options);
    }
  }

  /**
   * Number of files in this set.
   * @return integer
   */
  public function size ()
  {
    return sizeof ($this->files);
  }

  /**
   * Process a potential uploaded file.
   * PHP versions older than 4.2 do not record an error. If there is no error recorded and
   * there is no file name, then skip the file -- the browser sent a spurious file upload.
   * @param string $name
   * @param integer $size
   * @param string $type
   * @param string $temp_name
   * @param integer $error Can be {@link Uploaded_file_error_none}, {@link Uploaded_file_error_missing}, {@link Uploaded_file_error_partial}, {@link Uploaded_file_error_form_size} or {@link Uploaded_file_error_ini_size}.
   */
  protected function _process_file ($name, $size, $type, $temp_name, $error)
  {
    if (! (($error == Uploaded_file_error_none) && (! $size || ! $name)))
    {
      $this->files [] = new UPLOADED_FILE ($this->_uploader, $name, $size, $type, $temp_name, $error);
    }
  }

  /**
   * @var UPLOADER
   * @access private
   */
  protected $_uploader;
}

/**
 * Full support for files uploaded in a {@link FORM}.
 * This class interprets the PHP upload variables as well as integrating with the extended upload
 * support found in {@link FORM}. The uploader reads information stored in the form, indicating previously
 * uploaded files which should be treated as part of the most recent form transmission. This reduces the
 * amount of re-uploading a user must do when a form cannot be committed.
 * @package webcore
 * @subpackage util
 * @version 3.3.0
 * @since 2.5.0
 */
class UPLOADER extends RAISABLE
{
  /**
   * Number of files uploaded.
   * @var integer
   */
  public $total_files = 0;

  /**
   * Maximum file size specified in the form.
   * @var integer
   */
  public $form_max_file_size;

  /**
   * Maximum file size in PHP configuration.
   * @var integer
   */
  public $ini_max_file_size;

  /**
   * Value of 'upload_max_filesize'.
   * This is a PHP configuration value and controls the maximum size of a single uploaded file.
   * @var integer
   */
  public $upload_max_filesize;

  /**
   * Value of 'post_max_size'.
   * This is a PHP configuration value and controls the maximum size of a form's data.
   * @var integer
   */
  public $post_max_size;

  /**
   * Actual maximum file size for this upload.
   * Calculated as the minimum of {@link $form_max_file_size} and {@link $ini_max_file_size}.
   * @var integer
   */
  public $max_file_size;

  /**
   * Map of field name to list of files.
   * Each submitted fields may be associated with one of more uploaded files.
   * @var array[string,UPLOADED_FILE_SET]
   * @see UPLOADED_FILE_SET
   */
  public $file_sets = array ();

  /**
   * Name of the previously uploaded file information.
   * @var string
   */
  public $stored_info_name = 'webcore_saved_uploads';

  public function __construct ()
  {
    $this->load_from_request ();
  }

  public function load_from_request ()
  {
    $this->upload_max_filesize = text_to_file_size (ini_get ('upload_max_filesize'));
    $this->post_max_size = text_to_file_size (ini_get ('post_max_size'));

    $this->ini_max_file_size = min ($this->upload_max_filesize, $this->post_max_size);
    $this->max_file_size = $this->ini_max_file_size;

    $this->form_max_file_size = read_var (Form_max_file_size_field_name, 0);
    if ($this->form_max_file_size && ($this->form_max_file_size < $this->max_file_size))
    {
      $this->max_file_size = $this->form_max_file_size;
    }

    $this->_file_sets = array ();
    $this->total_files = 0;

    foreach ($_FILES as $file_set_id => $file_info)
    {
      $file_set = new UPLOADED_FILE_SET ($this, $file_set_id);
      if ($file_set->size ())
      {
        $this->total_files += $file_set->size ();
        $this->file_sets [$file_set_id] = $file_set;
      }
    }

    /*
       In order to smoothly support uploading with form validation, we allow a form to process
       a previously uploaded file and store its properties in the form. The uploader reads those
       values and 'pretends' that this is a valid PHP upload file. This way, form validation can
       occur over multiple submissions but a successfully uploaded file need only be uploaded once.
   */

    $uploads = read_var ($this->stored_info_name);

    if (is_array ($uploads))
    {
      for ($idx = sizeof ($uploads) - 1; $idx >= 0; $idx--)
      {
        $upload = $uploads [$idx];
        $file = new UPLOADED_FILE ($this, '', 0, '', '', 0);
        $file_set_id = $file->load_from_text ($upload);
        if (isset ($this->file_sets [$file_set_id]))
        {
          $file_set = $this->file_sets [$file_set_id];
        }
        else
        {
          $file_set = new UPLOADED_FILE_SET ($this, $file_set_id);
          $this->file_sets [$file_set_id] = $file_set;
        }
        array_unshift ($file_set->files, $file);
      }
    }
  }
}

?>