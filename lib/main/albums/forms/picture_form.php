<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage forms
 * @version 3.6.0
 * @since 2.5.0
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
require_once ('albums/forms/album_entry_form.php');

/**
 * Edit or create a {@link PICTURE}.
 * @package albums
 * @subpackage forms
 * @version 3.6.0
 * @since 2.5.0
 */
class PICTURE_FORM extends ALBUM_ENTRY_FORM
{
  /**
   * @var string
   */
  public $name = 'picture_form';

  /**
   * @param ALBUM $folder Album in which to add or edit the picture.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new TEXT_FIELD ();
    $field->id = 'file_name';
    $field->caption = 'File Name';
    $field->min_length = 1;
    $field->max_length = 1000;
    $this->add_field ($field);

    $field = new UPLOAD_FILE_FIELD ();
    $field->id = 'upload_file';
    $field->caption = 'Picture';
    $field->max_bytes = text_to_file_size ('2MB');
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'create_thumbnail';
    $field->caption = 'Create a thumbnail';
    $field->sticky = true;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'thumbnail_size';
    $field->caption = 'Thumbnail size';
    $field->min_value = 32;
    $field->max_value = 400;
    $field->sticky = true;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'overwrite';
    $field->caption = 'Overwrite existing file';
    $field->sticky = true;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'read_exif';
    $field->caption = 'Read EXIF info';
    $field->sticky = true;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'use_upload';
    $field->sticky = true;
    $this->add_field ($field);

    $field = $this->field_at ('day');
    $field->required = false;
  }

  /**
   * Load initial properties from this picture.
   * @param PICTURE $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    /** @var ALBUM $folder */
    $folder = $this->_folder;

    $this->set_value ('thumbnail_size', 200);
    $this->set_value ('file_name', $obj->file_name);

    $this->set_required ('file_name', ! $folder->uploads_allowed ());
    $this->set_value ('overwrite', true);
    $this->set_value ('use_upload', false);
    $this->set_value ('create_thumbnail', false);
    $this->set_value ('read_exif', false);
  }

  /**
   * Load initial properties from the object, but store as a new object.
   * @param STORABLE $obj
   */
  public function load_from_clone ($obj)
  {
    parent::load_from_clone ($obj);

    /** @var ALBUM $folder */
    $folder = $this->_folder;

    $this->set_value ('create_thumbnail', $folder->uploads_allowed ());
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();

    /** @var ALBUM $folder */
    $folder = $this->_folder;
    $uploads_allowed = $folder->uploads_allowed();

    $this->set_required ('file_name', !$uploads_allowed);

    $this->load_from_client ('thumbnail_size', 200);
    $this->load_from_client ('overwrite', true);
    $this->load_from_client ('create_thumbnail', $uploads_allowed);
    $this->load_from_client ('use_upload', $uploads_allowed);
    $this->load_from_client ('read_exif', true);
  }

  /**
   * Called after fields are loaded with data.
   * @param object $obj Object from which data was loaded. May be null.
   * @access private
   */
  protected function _post_load_data ($obj)
  {
    parent::_post_load_data ($obj);
    $this->set_enabled ('thumbnail_size', $this->value_for ('create_thumbnail'));
    $this->set_enabled ('day', ! $this->value_for ('read_exif'));
  }

  /**
   * Get the raw file name specified by the user.
   * If the file was uploaded, this is the path to the current image location. If not,
   * it's the file name given in the form.
   * @return string
   * @access private
   */
  protected function _selected_file_name ()
  {
    if ($this->value_for ('use_upload'))
    {
      $file = $this->upload_file_for ('upload_file');
      if (isset ($file))
      {
        return $file->current_name ();
      }
    }

    return $this->value_as_text ('file_name');
  }

  /**
   * Apply post-validation operations to the object.
   * @param PICTURE $obj Object being validated.
   * @access private
   */
  protected function _prepare_for_commit ($obj)
  {
    parent::_prepare_for_commit ($obj);

    /* Move the file to its final location and set the normalized file name. */

    $file = $this->upload_file_for ('upload_file');
    if (isset ($file))
    {
      $obj->file_name = $file->normalized_name;
      /** @var ALBUM $folder */
      $folder = $this->_folder;
      /** @var UPLOAD_FILE_FIELD $upload_file_field */
      $upload_file_field = $this->field_at('upload_file');

      $this->_move_uploaded_file ($upload_file_field, $file, url_to_file_name ($folder->picture_folder_url ()));
    }

    /* Create the thumbnail if needed. */

    if ($this->value_for ('create_thumbnail'))
    {
      $class_name = $this->app->final_class_name ('THUMBNAIL_CREATOR', 'webcore/util/image.php');
      /** @var THUMBNAIL_CREATOR $creator */
      $creator = new $class_name ($this->app);

      $url = $obj->location ();
      $creator->create_thumbnail_for ($url->as_text (), $this->value_for ('thumbnail_size'));
      if ($creator->error_message)
      {
        $this->record_error ('create_thumbnail', $creator->error_message);
      }
    }
  }

  /**
   * Store the form's values to this picture.
   * @param PICTURE $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    parent::_store_to_object ($obj);

    $file_name = $this->_selected_file_name ();
    if ($this->value_for ('use_upload'))
    {
      $file_name = $this->context->resolve_file (file_name_to_url ($file_name), Force_root_on);
    }

    $obj->file_name = $file_name;
    if (isset ($this->_exif_date))
    {
      $obj->date = $this->_exif_date;
    }
  }

  /**
   * Called before fields are validated.
   * @param PICTURE $obj
   * @access private
   */
  protected function _pre_validate ($obj)
  {
    parent::_pre_validate ($obj);
    $use_upload = $this->value_for ('use_upload');
    $this->set_required ('upload_file', $use_upload);
    $this->set_required ('file_name', ! $use_upload);
  }

  /**
   * Called after fields are validated.
   * @param PICTURE $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    $file_name = $this->_selected_file_name ();

    if (! $this->value_for ('use_upload'))
    {
      $old_file_name = $obj->file_name;
      $obj->file_name = $file_name;
      $file_name = url_to_file_name ($obj->full_file_name ());
      $obj->file_name = $old_file_name;
    }

    if ($this->value_for ('read_exif'))
    {
      $url_is_empty = $this->value_is_empty ('file_name');
      $use_upload = $this->value_for ('use_upload');
      if (($use_upload && ! $this->num_errors ('upload_file', Form_first_control_for_field)) || ! $url_is_empty)
      {
        $class_name = $this->context->final_class_name ('IMAGE', 'webcore/util/image.php');
        /** @var IMAGE $img */
        $img = new $class_name ();
        $img->set_file ($file_name, true);
        if ($img->properties->exists () && $img->properties->time_created->is_valid ())
        {
          $this->_exif_date = $img->properties->time_created;
        }
        else
        {
          $this->record_error ('day', 'Could not extract date information from the picture.');
        }
      }
    }

    if (! $this->value_for ('read_exif') && $this->value_is_empty ('day'))
    {
      $this->record_error ('day', 'Please set a date.');
    }
  }

  /**
   * Specify how to store this uploaded file.
   * @param UPLOAD_FILE_FIELD $field
   * @param UPLOADED_FILE $file
   * @param boolean $form_is_valid Will the form be committed?
   * @return string Can be {@link Uploaded_file_unique_name} or {@link Uploaded_file_overwrite}.
   * @access private
   */
  protected function _upload_file_copy_mode ($field, $file, $form_is_valid)
  {
    if (! $this->previewing () && $this->value_for ('overwrite'))
    {
      return Uploaded_file_overwrite;
    }

    return Uploaded_file_unique_name;
  }

  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
?>
  function upload_file_changed (ctrl)
  {
    if (ctrl.value)
    {
      ctrl.form.create_thumbnail.checked = true;
      select_item (ctrl.form.use_upload, 1, true);
      select_item (ctrl.form.read_exif, 1, true);
    }
  }

  function file_name_changed (ctrl)
  {
    if (ctrl.value)
    {
      ctrl.form.create_thumbnail.checked = true;
      select_item (ctrl.form.use_upload, 0, true);
    }
  }

  function file_option_changed (ctrl)
  {
    var is_uploading = is_selected (ctrl, 1);
    ctrl.form.file_name.disabled = is_uploading;
    ctrl.form.upload_file.disabled = ! is_uploading;
    ctrl.form.overwrite.disabled = ! is_uploading;
  }

  function on_click_thumbnail (ctrl)
  {
    ctrl.form.thumbnail_size.disabled = ! ctrl.checked;
  }

  function on_date_changed (ctrl)
  {
    ctrl.form.day.disabled = is_selected (ctrl, 1);
  }
<?php
  }

  /**
   * @param FORM_RENDERER $renderer
   * @param string $row_title
   * @access private
   */
  protected function _draw_thumbnail_options ($renderer, $row_title)
  {
    $options = new FORM_TEXT_CONTROL_OPTIONS ();
    $options->css_class = 'tiny';

    $renderer->start_row ($row_title);
      $props = $renderer->make_check_properties ();
      $props->text = ' no larger than ' . $renderer->text_line_as_html ('thumbnail_size', $options). ' pixels.';
      $props->on_click_script = 'on_click_thumbnail (this)';
      $props->css_class = 'text-line';
      echo $renderer->check_box_as_html ('create_thumbnail', $props);
    $renderer->finish_row ();
    $renderer->draw_error_row ('create_thumbnail');
    $renderer->draw_error_row ('thumbnail_size');
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    /** @var UPLOADED_FILE_SET $file_set */
    $file_set = $this->value_for ('upload_file');
    /** @var ALBUM $folder */
    $folder = $this->_folder;

    $upload_allowed = $folder->uploads_allowed ();
    $upload_found = isset ($file_set) && $file_set->is_valid ();
    $is_uploading = $this->value_for ('use_upload');

    $this->set_enabled ('file_name', ! $is_uploading);
    $this->set_enabled ('overwrite', $is_uploading);
    $this->set_enabled ('upload_file', $is_uploading);
    if (! $upload_allowed || $upload_found)
    {
      $renderer->draw_hidden ('use_upload');
    }
    if ($upload_found)
    {
      $renderer->draw_hidden ('overwrite');
    }

    $renderer->start ();

    $renderer->draw_text_line_row ('title');

    if (!$this->object_exists ())
    {
      $renderer->start_block ('Day');
      $props = $renderer->make_list_properties ();
      $props->on_click_script = 'on_date_changed (this)';
      if (isset ($this->_exif_date))
      {
        $caption = 'Use <span class="field">' . $this->_exif_date->format () . '</span> (snapshot date)';
      }
      else
      {
        $caption = 'Use date stored by a digital camera';
      }
      $props->add_item ($caption, 1);
      $props->add_item ('Use the date below', 0);
      $renderer->start_row ();
      echo $renderer->radio_group_as_html ('read_exif', $props);
      $renderer->finish_row ();
      $renderer->draw_error_row ('read_exif');

      $renderer->draw_date_row('day');

      $renderer->finish_block ();
    }
    else
    {
      $renderer->draw_date_row('day');
    }

    $renderer->draw_text_box_row ('description', 'short-medium');

    if ($upload_allowed)
    {
      if (! $upload_found)
      {
        $options = new FORM_TEXT_CONTROL_OPTIONS ();

        $renderer->start_block ('Picture');

        if ($this->object_exists())
        {
          /** @var PICTURE $pic */
          $pic = $this->_object;

          echo '<img class="pullquote right align-right" src="' . $pic->full_thumbnail_name() . '" alt="Picture">';
        }

        $props = $renderer->make_list_properties ();
        $props->on_click_script = 'file_option_changed (this)';
        $props->add_item ($this->app->resolve_icon_as_html ('{icons}buttons/upload', Sixteen_px, 'Upload') . ' Upload the picture below', 1);
        $renderer->start_row ();
        echo $renderer->radio_group_as_html ('use_upload', $props);
        $renderer->finish_row ();

        $renderer->start_row ();
        $options->on_change_script = 'upload_file_changed (this)';
        echo $renderer->file_as_html ('upload_file', $options);
        echo $renderer->check_box_as_html ('overwrite');
        $renderer->finish_row ();
        $renderer->draw_error_row ('upload_file');

        $props->clear_items ();
        $props->add_item ('Show the picture from the URL below', 0);
        $renderer->start_row ();
        echo $renderer->radio_group_as_html ('use_upload', $props);
        $renderer->finish_row ();

        $renderer->start_row ();
        $options->on_change_script = 'file_name_changed (this)';
        echo $renderer->text_line_as_html ('file_name', $options);
        $renderer->finish_row ();
        $renderer->draw_error_row ('file_name');

        $this->_draw_thumbnail_options ($renderer, '');

        $renderer->finish_block ();
      }
      else
      {
        $renderer->draw_file_row ('upload_file');
        $this->_draw_thumbnail_options ($renderer, ' ');
      }
    }
    else
    {
      $renderer->start_row();
      if ($this->object_exists())
      {
        /** @var PICTURE $pic */
        $pic = $this->_object;

        echo '<img class="pullquote right align-right" src="' . $pic->full_thumbnail_name() . '" alt="Picture">';
      }
      $renderer->finish_row();
      $renderer->draw_text_line_row ('file_name');
      $this->_draw_thumbnail_options ($renderer, ' ');
    }

    $renderer->draw_check_box_row ('is_visible');

    $renderer->draw_submit_button_row ();
    $this->_draw_history_item_controls ($renderer);

    $renderer->finish ();
  }

  /** @var DATE_TIME */
  private $_exif_date;
}

?>