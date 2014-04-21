<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage forms
 * @version 3.5.0
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
require_once ('webcore/forms/form.php');

/**
 * Edit or create a {@link PICTURE}.
 * @package albums
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
 */
class UPLOAD_PICTURES_FORM extends ID_BASED_FORM
{
  /**
   * @var string
   */
  public $button = 'Upload';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/upload';

  /**
   * @param FOLDER $folder
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder->app);

    $this->_folder = $folder;

    $field = new UPLOAD_FILE_FIELD ();
    $field->id = 'zipfile';
    $field->caption = 'Zip file';
    $field->description = 'Please specify a zip file containing your pictures.';
    $field->required = true;
    $field->max_bytes = text_to_file_size ('30MB');
    $this->add_field ($field);

    $field = new MUNGER_TITLE_FIELD ();
    $field->id = 'title';
    $field->caption = 'Title';
    $field->description = 'Each picture entry will have this title. Use {#} to include the picture number and {file} to include the file name without the extension. For example, if the title is \'Picture {#} - {file}\', file1.jpg will have the title \'Picture 1 - file1\').';
    $field->required = true;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'read_exif';
    $field->caption = 'Read EXIF info';
    $this->add_field ($field);

    $field = new DATE_TIME_FIELD ();
    $field->id = 'day';
    $field->caption = 'Day';
    $field->required = true;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'create_thumbnail';
    $field->caption = 'Create thumbnails';
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'thumbnail_size';
    $field->caption = 'Thumbnail size';
    $field->min_value = 32;
    $field->max_value = 300;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'starting_index';
    $field->caption = 'First number';
    $field->required = true;
    $field->description = 'Set the next number to use for {#} in the title (if uploading in multiple batches).';
    $field->min_value = 1;
    $this->add_field ($field);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('day', $this->_folder->first_day);
    $this->set_value ('title', '{file}');
    $this->set_value ('thumbnail_size', 200);
    $this->set_value ('create_thumbnail', true);
    $this->set_value ('read_exif', true);
    $this->set_value ('starting_index', 1);
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
   * @access private
   */
  public function commit ($obj)
  {
    include_once ('albums/tasks/batch_create_pictures.php');
    $task = new BATCH_CREATE_PICTURES_TASK ($this->_folder);

    $file_set = $this->value_for ('zipfile');
    $file = $file_set->files [0];
    $task->archive_file_name = $file->current_name ();

    $task->create_thumbnail = $this->value_for ('create_thumbnail');
    $task->read_exif = $this->value_for ('read_exif');
    $task->thumbnail_size = $this->value_for ('thumbnail_size');
    $task->file_name_template = $this->value_for ('title');
    $task->default_date = $this->value_for ('day');
    $task->starting_index = $this->value_for ('starting_index');

    $task->execute ();
  }

  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
?>
  function on_click_thumbnail (ctrl)
  {
    ctrl.form.thumbnail_size.disabled = ! ctrl.checked;
  }
<?php
  }

  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');
    $renderer->draw_text_line_row ('starting_index');
    $renderer->draw_separator ();
    $renderer->start_row ('Day');
    $renderer->start_block (true);

      $props = $renderer->make_list_properties ();
      $props->add_item ('Use date stored by a digital camera (if possible)', 1);
      $props->add_item ('Use the date below (if no date is found)', 0);
      $renderer->start_row ();
        echo $renderer->radio_group_as_html ('read_exif', $props);
      $renderer->finish_row ();
      $renderer->start_row ();
        $renderer->start_indent ();
        echo $renderer->date_as_html ('day');
        $renderer->finish_indent ();
      $renderer->finish_row ();
  
      $renderer->draw_error_row ('read_exif');
      $renderer->draw_error_row ('day');

    $renderer->finish_block ();
    $renderer->finish_row ();

    $renderer->draw_separator ();

    $renderer->draw_file_row ('zipfile');

    $options = new FORM_TEXT_CONTROL_OPTIONS ();
    $options->width = '4em';
    
    $renderer->start_row ('Thumbnails');
      $props = $renderer->make_check_properties ();
      $props->text = ' no larger than ' . $renderer->text_line_as_html ('thumbnail_size', $options). ' pixels.';
      $props->on_click_script = 'on_click_thumbnail (this)';
      echo $renderer->check_box_as_html ('create_thumbnail', $props);
    $renderer->finish_row ();
    $renderer->draw_error_row ('thumbnail_size');

    $renderer->draw_submit_button_row ();
    $renderer->finish ();
  }
}
?>