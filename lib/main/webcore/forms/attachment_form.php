<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
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
require_once ('webcore/forms/object_in_folder_form.php');

/**
 * Form that stores {@link ATTACHMENT}s.
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
 */
class ATTACHMENT_FORM extends OBJECT_IN_FOLDER_FORM
{
  /**
   * @var string
   */
  public $name = 'attachment_form';

  /**
   * @param ENTRY $entry Will be attached to this object.
   */
  public function __construct ($entry)
  {
    $folder = $entry->parent_folder ();
    parent::__construct ($folder);

    $this->_entry = $entry;

    $field = new TEXT_FIELD ();
    $field->id = 'type';
    $field->caption = 'Type';
    $field->required = true;
    $field->visible = false;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'create_thumbnail';
    $field->caption = 'Generate thumbnail';
    $field->sticky = true;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'thumbnail_size';
    $field->caption = 'Thumbnail';
    $field->min_value = 32;
    $field->max_value = 400;
    $field->sticky = true;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'overwrite';
    $field->caption = 'Overwrite existing file';
    $field->sticky = true;
    $this->add_field ($field);

    $field = new UPLOAD_FILE_FIELD ();
    $field->id = 'file_name';
    $field->caption = 'File';
    $field->required = true;
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this object.
   * @param ATTACHMENT $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('type', $obj->type);
    $this->set_required ('file_name', false);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->load_from_client ('thumbnail_size', 200);
    $this->load_from_client ('overwrite', true);
    $this->load_from_client ('create_thumbnail', true);
    $this->set_value ('type', read_var ('type'));
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
  }

  /**
   * Called before fields are validated.
   * @access private
   */
  protected function _pre_validate ($obj)
  {
    parent::_pre_validate ($obj);
    $this->set_required ('file_name', ! $obj->exists ());
  }

  /**
   * Apply post-validation operations to the object.
   * @param object $obj Object being validated.
   * @access private
   */
  protected function _prepare_for_commit ($obj)
  {
    parent::_prepare_for_commit ($obj);

    /* Move the file to its final location and set the normalized file name. */
    
    $file = $this->upload_file_for ('file_name');
    if (isset ($file))
    {
      $obj->file_name = $file->normalized_name;
      $url = new FILE_URL ($obj->full_file_name ());
      $this->_move_uploaded_file ($this->field_at ('file_name'), $file, $url->path ());
    }
    
    /* Create the thumbnail if needed. */
    
    if ($obj->is_image && $this->value_for ('create_thumbnail'))
    {
      $class_name = $this->app->final_class_name ('THUMBNAIL_CREATOR', 'webcore/util/image.php');
      $creator = new $class_name ($this->app);
      $creator->create_thumbnail_for ($obj->full_file_name (), $this->value_for ('thumbnail_size'));
      if ($creator->error_message)
      {
        $this->record_error ('create_thumbnail', $creator->error_message);
      }
    }
  }

  /**
   * Store the form's values to this object.
   * @param STORABLE $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    parent::_store_to_object ($obj);

    $file = $this->upload_file_for ('file_name');
    if (isset ($file))
    {
      $obj->set_full_file_name ($file->current_name ());
      $obj->original_file_name = $file->name;
      $obj->mime_type = $file->mime_type;
      $obj->size = $file->size;
      $obj->file_name = $file->current_name ();
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
    }
  }

  function on_click_thumbnail (ctrl)
  {
    ctrl.form.thumbnail_size.disabled = ! ctrl.checked;
  }
<?php
  }

  /**
   * Draw the file upload controls.
   * @var FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_file_controls ($renderer)
  {
    $options = new FORM_TEXT_CONTROL_OPTIONS ();
    $options->on_change_script = 'upload_file_changed (this)';

    $renderer->draw_file_row ('file_name', $options);

    $props = $renderer->make_list_properties ();
    $props->add_item ('overwrite', 1);
    
    $options = new FORM_TEXT_CONTROL_OPTIONS ();
    $options->width = '4em';
    
    $check_props = $renderer->make_check_properties ('create_thumbnail');
    $check_props->on_click_script = 'on_click_thumbnail (this)';
    $check_props->text = ' no larger than ' . $renderer->text_line_as_html ('thumbnail_size', $options) . ' pixels.';
    $check_props->css_class = 'text-line';
    $props->add_item_object ($check_props);

    $renderer->draw_check_boxes_row (' ', $props);
    $renderer->draw_error_row ('thumbnail_size');
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();

    if ($this->object_exists ())
    {
      $img = $this->_object->icon_as_html (Thirty_two_px);
      if ($this->_object->is_image)
      {
        $thumb = $this->_object->thumbnail_as_html ();
        if ($thumb)
        {
          $img = $thumb;
        }
      }

      $renderer->draw_text_row ('Current file', '<div style="float: left; margin-right: .5em">' . $img . '</div><div>' . $this->_object->original_file_name . '</div><div style="margin-top: .5em">' . $this->_object->mime_type . ' (' . file_size_as_text ($this->_object->size) . ')</div>', 'detail');

      $renderer->draw_text_line_row ('title');
      $renderer->draw_check_box_row ('is_visible');
      $renderer->draw_text_box_row ('description');

      if ($this->login->is_allowed (Privilege_set_attachment, Privilege_upload, $this->_folder))
      {
        $renderer->start_row (' ');
          $renderer->start_block (true);
            $renderer->draw_text_row (' ', 'Replacing the file for the attachment is optional; you can regenerate the thumbnail from the current image by clicking "Save" below.', 'notes');
            $this->_draw_file_controls ($renderer);
          $renderer->finish_block ();
        $renderer->finish_row ();
      }
    }
    else
    {
      if ($this->login->is_allowed (Privilege_set_attachment, Privilege_upload, $this->_folder))
      {
        $this->_draw_file_controls ($renderer);
      }

      $renderer->draw_text_line_row ('title');
      $renderer->draw_check_box_row ('is_visible');
      $renderer->draw_text_box_row ('description');
    }

    $renderer->draw_submit_button_row ();
    
    $this->_draw_history_item_controls ($renderer, false);
    
    $renderer->finish ();
  }

  /**
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_entry;

  /**
   * @var ENTRY
   * @access private
   */
  protected $_entry;
}

?>