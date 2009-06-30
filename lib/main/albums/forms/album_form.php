<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/forms/folder_form.php');

/**
 * Edit or create an {@link ALBUM}.
 * @package albums
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class ALBUM_FORM extends FOLDER_FORM
{
  /**
   * @param ALBUM $folder Album to edit or create.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new URI_FIELD ();
    $field->id = 'url_root';
    $field->title = 'Root URL';
    $field->required = true;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'show_times';
    $field->title = 'Show times';
    $field->description = 'Shows times with albums, picture and journals for finer detail.';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'show_celsius';
    $field->title = 'Show celsius';
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'max_picture_height';
    $field->title = 'Maximum picture height';
    $field->min_value = 0;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'max_picture_width';
    $field->title = 'Maximum picture width';
    $field->min_value = 0;
    $this->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'date_style';
    $field->title = '';
    $field->add_value (Album_is_single_day);
    $field->add_value (Album_is_span);
    $field->add_value (Album_is_journal);
    $field->add_value (Album_is_adjusted);
    $field->required = true;
    $this->add_field ($field);

    $field = new DATE_FIELD ();
    $field->id = 'first_day';
    $field->title = 'First day';
    $this->add_field ($field);

    $field = new DATE_FIELD ();
    $field->id = 'last_day';
    $field->title = 'Last day';
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'constrain_picture_size';
    $field->title = 'Display pictures as';
    $field->description = 'Initial size for larger pictures; clicking shows it full size.';
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'main_picture_id';
    $field->title = 'Main picture';
    $field->min_value = 0;
    $field->visible = false;
    $this->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'location';
    $field->title = 'Location';
    $field->required = true;
    $field->add_value (Album_location_type_local);
    $field->add_value (Album_location_type_remote);
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'url_root_enabled';
    $field->title = '';
    $field->description = 'Check to change the Root URL. For advanced users only -- pictures are not moved when the root is changed.';
    $this->add_field ($field);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();

    if (isset ($this->_folder))
    {
      $this->set_value ('location', $this->_folder->location);
      $this->set_value ('url_root', $this->_folder->url_root);

      $this->set_value ('date_style', $this->_folder->date_style ());
      $this->set_value ('first_day', $this->_folder->first_day);
      $this->set_value ('last_day', $this->_folder->last_day);

      $this->set_value ('show_times', $this->_folder->show_times);
      $this->set_value ('show_celsius', $this->_folder->show_celsius);

      $this->set_value ('max_picture_width', $this->_folder->max_picture_width);
      $this->set_value ('max_picture_height', $this->_folder->max_picture_height);
    }
    else
    {
      $this->set_value ('location', Album_location_type_remote);
      $this->set_value ('show_celsius', true);

      $this->set_value ('max_picture_width', 640);
      $this->set_value ('max_picture_height', 480);

      $this->set_value ('first_day', new DATE_TIME ());
      $this->set_value ('last_day', new DATE_TIME ());

      $this->set_value ('date_style', Album_is_adjusted);
    }

    $this->set_value ('constrain_picture_size', $this->value_for ('max_picture_width') || $this->value_for ('max_picture_height'));
    $this->set_visible ('location', $this->login->is_allowed (Privilege_set_entry, Privilege_upload, $this->_folder));
    $this->set_visible ('url_root_enabled', ($this->value_for ('url_root') != '') && ($this->value_for ('location') == Album_location_type_local));
    $this->set_enabled ('url_root', ! $this->visible ('url_root_enabled'));
  }

  /**
   * Load initial properties from this album.
   * @param ALBUM $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('publication_state', 'silent');
    $this->set_value ('show_times', $obj->show_times);
    $this->set_value ('show_celsius', $obj->show_celsius);
    $this->set_value ('max_picture_width', $obj->max_picture_width);
    $this->set_value ('max_picture_height', $obj->max_picture_height);
    $this->set_value ('url_root', $obj->url_root);

    $this->set_value ('location', $obj->location);
    $this->set_visible ('location', $this->login->is_allowed (Privilege_set_entry, Privilege_upload, $this->_folder));
    if (! $this->visible ('location'))
    {
      $this->set_value ('location', Album_location_type_remote);
    }

    $this->set_visible ('url_root_enabled', ($this->value_for ('url_root') != '') && ($this->value_for ('location') == Album_location_type_local));
    $this->set_enabled ('url_root', ! $this->visible ('url_root_enabled'));

    $main_picture_id = read_var ('main_picture_id', 'not set');
    if ($main_picture_id == 'not set')
    {
      $this->set_value ('main_picture_id', $obj->main_picture_id);
    }
    else
    {
      $this->set_value ('main_picture_id', $main_picture_id);
    }

    $this->set_value ('constrain_picture_size', $this->value_for ('max_picture_width') || $this->value_for ('max_picture_height'));

    if (! $this->value_for ('constrain_picture_size'))
    {
      $this->set_value ('max_picture_width', $this->app->picture_options->default_max_picture_width);
      $this->set_value ('max_picture_height', $this->app->picture_options->default_max_picture_height);
    }

    $this->set_value ('date_style', $obj->date_style ());
    $this->set_value ('first_day', $obj->first_day);
    $this->set_value ('last_day', $obj->last_day);
  }

  /**
   * Called after fields are loaded with data.
   * @param object $obj Object from which data was loaded. May be null.
   * @access private
   */
  protected function _post_load_data ($obj)
  {
    $constrain_pics = $this->value_for ('constrain_picture_size');
    $this->set_enabled ('max_picture_width', $constrain_pics);
    $this->set_enabled ('max_picture_height', $constrain_pics);

    switch ($this->value_for ('date_style'))
    {
    case Album_is_single_day:
    case Album_is_journal:
      $this->set_enabled ('last_day', false);
      break;
    case Album_is_adjusted:
      $this->set_enabled ('first_day', false);
      $this->set_enabled ('last_day', false);
      break;
    }
  }

  /**
   * Does this form hold valid data for this album?
   * @param ALBUM $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    switch ($this->value_for ('date_style'))
    {
    case Album_is_journal:
      $now = new DATE_TIME ();
      if ($now->less_than ($this->value_for ('first_day')))
      {
        $this->record_error ('First day of a journal cannot be in the future.');
      }
      break;
    case Album_is_span:
      $last_day = $this->value_for ('last_day');
      if ($last_day->less_than ($this->value_for ('first_day')))
      {
        $this->record_error ('First day must come before the last day.');
      }
      break;
    }

    if ($this->value_for ('constrain_picture_size') && 
      (($this->value_for ('max_picture_width') == 0) || ($this->value_for ('max_picture_width') == 0)))
    {
      $this->record_error ('picture', 'Please make sure that both picture width and height are greater than 0.');
    }
  }

  /**
   * Store the form's values to this album.
   * @param ALBUM $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    $obj->url_root = $this->value_as_text ('url_root');
    $obj->location = $this->value_as_text ('location');

    $obj->show_times = $this->value_for ('show_times');
    $obj->show_celsius = $this->value_for ('show_celsius');
    $obj->set_date_style ($this->value_for ('date_style'), $this->value_for ('first_day'), $this->value_for ('last_day'));
    $obj->main_picture_id = $this->value_for ('main_picture_id');

    if ($this->value_for ('constrain_picture_size'))
    {
      $obj->max_picture_width = $this->value_for ('max_picture_width');
      $obj->max_picture_height = $this->value_for ('max_picture_height');
    }
    else
    {
      $obj->max_picture_width = 0;
      $obj->max_picture_height = 0;
    }

    parent::_store_to_object ($obj);
  }

  /**
   * @access private
   */
  protected function _draw_scripts ()
  {
    parent::_draw_scripts ();
    $js_form = $this->js_form_name ();
?>

  function PICTURE_VALUE_FIELD ()
  {
  }
  PICTURE_VALUE_FIELD.prototype = new OBJECT_VALUE_FIELD;

  PICTURE_VALUE_FIELD.prototype.set_value = function (v)
  {
    if (this.original_value === undefined)
    {
      this.original_value = v;
    }
    else
    {
      if (this.original_value != v)
      {
        var parts = v.split ("|");
        this.control.value = parts [0];
        if (parts [1])
        {
          document.getElementById ('main_picture_image').setAttribute('src', parts [1]);
          document.getElementById ('main_picture_image').setAttribute('style', 'display: inline');
          document.getElementById ('main_picture_none_text').setAttribute('style', 'display: none');
        }
        else
        {
          document.getElementById ('main_picture_image').setAttribute('style', 'display: none');
          document.getElementById ('main_picture_none_text').setAttribute('style', 'display: inline');
        }
        document.getElementById ('main_picture_changed').setAttribute('style', 'display: block');

        this.original_value = v;
      }
    }
  }

  var field = new PICTURE_VALUE_FIELD ();
  field.attach (<?php echo $js_form . '.main_picture_id'; ?>);
  field.object_id = <?php echo $this->value_for ('id'); ?>;
  field.width = 700;
  field.height = 700;
  field.page_name = 'browse_picture.php';

  function on_day_mode_changed (ctrl)
  {
    var form = <?php echo $this->js_form_name (); ?>;
    var now_as_text = format_date_time (new Date (), '<?php echo $this->app->date_time_toolkit->formatter->format_string_for (Date_time_format_short_date); ?>');

    switch (ctrl.value)
    {
    case '<?php echo Album_is_single_day; ?>':
      form.first_day.disabled = false;
      form.last_day.disabled = true;
      form.last_day.value = form.first_day.value;
      break;
    case '<?php echo Album_is_journal; ?>':
      form.first_day.disabled = false;
      form.last_day.disabled = true;
      form.last_day.value = now_as_text;
      break;
    case '<?php echo Album_is_span; ?>':
      form.first_day.disabled = false;
      form.last_day.disabled = false;
      break;
    case '<?php echo Album_is_adjusted; ?>':
      form.first_day.disabled = true;
      form.last_day.disabled = true;
      form.first_day.value = now_as_text;
      form.last_day.value = now_as_text;
      break;
    }
  }

  function on_pic_size_constraint_changed (ctrl)
  {
    var ctrls_disabled = ! ctrl.checked;
    var form = <?php echo $this->js_form_name (); ?>;

    form.max_picture_width.disabled = ctrls_disabled;
    form.max_picture_height.disabled = ctrls_disabled;
  }

  function ensure_trailing_delimiter (path)
  {
    if (path.charAt (path.length - 1) != '/')
    {
      path += '/';
    }
    return path;
  }

  function on_url_root_changed (ctrl)
  {
    ctrl.value = ensure_trailing_delimiter (ctrl.value.toLowerCase ());
  }
  
  function on_url_root_enabled_changed (ctrl)
  {
    var form = <?php echo $this->js_form_name (); ?>;
    form.url_root.disabled = ! ctrl.checked;
  }

<?php
    $adjust_url_root = intval(! $this->object_exists ());
    $parent_url = new URL ($this->_folder->url_root);
    $parent_url->ensure_ends_with_delimiter ();
    $parent_url_root = $parent_url->path ();
?>
  var adjust_url_root = <?php echo $adjust_url_root; ?>;
  var parent_url_root = "<?php echo $parent_url_root; ?>";
  var last_title = "<?php echo $this->value_for ('title'); ?>";

  function normalize_string (s)
  {
    if (s)
    {
      re = /[ ]+/g;
      s = s.replace (re, '_');
      re = /[^a-zA-Z0-9_]+/g
      s = s.replace (re, '');
      re = /[_]+/g;
      s = s.replace (re, '_');
      s += '/';
    }
    return s.toLowerCase ();
  }

  function on_title_changed (ctrl)
  {
    /* Basically, if the current url root (path) is the same as the one that
       would be automatically set (based on the previous title), then keep the
       url root in sync. If the user has modified the URL root, then do nothing.
   */
    if (adjust_url_root && is_selected (ctrl.form.location, 'local'))
    {
      var auto_url_root = parent_url_root + normalize_string (last_title);
      var current_url = ensure_trailing_delimiter (ctrl.form.url_root.value);
      if ((current_url == auto_url_root) || ((current_url == parent_url_root)))
      {
        ctrl.form.url_root.value = parent_url_root + normalize_string (ctrl.value);
      }
      last_title = ctrl.value;
    }
  }
<?php
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();

    $options = new FORM_TEXT_CONTROL_OPTIONS ();
    $options->on_change_script = 'on_title_changed (this)';

    $renderer->draw_text_line_row ('title', $options);
    $renderer->draw_separator ();

    if ($this->visible ('location'))
    {
      $props = $renderer->make_list_properties ();
      $props->show_descriptions = true;
      $props->add_item ('Local', Album_location_type_local, 'Store pictures on this server under URL Root; uploading is supported.');
      $props->add_item ('Remote', Album_location_type_remote, 'Retrieve pictures from another server at URL Root; uploading is <b>not</b> supported.');
      $renderer->draw_radio_group_row ('location', $props);
    }

    $options->on_change_script = 'on_url_root_changed (this)';

    $renderer->draw_text_line_row ('url_root', $options);
    
    $item = $renderer->make_check_properties ();
    $item->on_click_script = 'on_url_root_enabled_changed (this)';
    $item->smart_wrapping = true;
    
    $renderer->draw_check_box_row ('url_root_enabled', $item);
    
    $renderer->draw_separator ();

    $options->on_change_script = null;
    $options->width = '12em';

    $renderer->start_row ('Dates');
    $renderer->start_block (true);

    $props = $renderer->make_list_properties ();
    $props->on_click_script = 'on_day_mode_changed (this)';
    $props->show_descriptions = true;
    $props->width = '30em';
    $props->add_item ('One day', Album_is_single_day, 'For parties or sporting events.');
    $props->add_item ('Several days', Album_is_span, 'For trips; both first and last day are fixed.');
    $props->add_item ('Journal', Album_is_journal, 'First day is fixed; last day is always today\'s date.');
    $props->add_item ('Freeform', Album_is_adjusted, 'Calculated automatically from pictures and journals.');
    $renderer->start_row ('');
    echo $renderer->radio_group_as_html ('date_style', $props);
    $renderer->finish_row ();

    $renderer->draw_date_row ('first_day');
    $renderer->draw_date_row ('last_day');

    $renderer->draw_error_row ('dates');

    $renderer->finish_block ();
    $renderer->finish_row ();

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $renderer->draw_separator ();

    $renderer->start_row ('Settings');
    $renderer->start_block (true);
      $props = $renderer->make_list_properties ();
      $props->show_descriptions = true;
      $props->width = '30em';

      $props->add_item ('is_visible', 1);
      $props->add_item ('show_times', 1);
      $props->add_item ('is_organizational', 1);

      $options = new FORM_TEXT_CONTROL_OPTIONS ();
      $options->width = '3em';
      $item = $renderer->make_check_properties ();
      $item->title = 'constrain_picture_size';
      $item->text = ' ' . $renderer->text_line_as_HTML ('max_picture_width', $options) . ' x ' . $renderer->text_line_as_HTML ('max_picture_height', $options);
      $item->on_click_script = 'on_pic_size_constraint_changed (this)';
      $props->add_item_object ($item);

      $renderer->draw_check_boxes_row('', $props);

      $renderer->start_row ();
        echo 'Show temperatures in ';
        $props = $renderer->make_list_properties ();
        $props->items_per_row = 2;
        $props->add_item ('Celsius', 1);
        $props->add_item ('Fahrenheit', 0);
        echo $renderer->radio_group_as_HTML ('show_celsius', $props);
      $renderer->finish_row ();

      $renderer->draw_error_row ('picture');

    $renderer->finish_block ();
    $renderer->finish_row ();

    $renderer->draw_separator ();
    $renderer->start_row ('Cover picture');
      $this->_draw_cover_picture ($renderer);
    $renderer->finish_row ();
    $renderer->draw_separator ();

    $renderer->draw_text_box_row ('summary', $renderer->default_control_width, '3em');
    $renderer->draw_text_box_row ('description');
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $this->_draw_history_item_controls ($renderer, false);

    $renderer->finish ();
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_cover_picture ($renderer)
  {
    if ($this->object_exists ())
    {
      $main_picture_id = $this->value_for ('main_picture_id');

      if ($main_picture_id)
      {
        $pic_query = $this->_object->entry_query ();
        $pic_query->set_type ('picture');
        $main_picture = $pic_query->object_at_id ($main_picture_id);
      }

      $renderer->start_block (true);
      $renderer->start_row ();

      if (isset ($main_picture))
      {
        $f = $main_picture->date->formatter ();
        $f->clear_flags ();
        $title = $main_picture->title_as_plain_text () . " (" . $this->_object->format_date ($main_picture->date, $f) . ")";
        $image_display = 'inline';
        $text_display = 'none';
        $image_source = $main_picture->full_thumbnail_name ();
      }
      else
      {
        $image_display = 'none';
        $text_display = 'inline';
        $image_source = '';
      }
?>
      <div style="display: table; margin: auto; text-align: center">
        <div style="margin-bottom: .5em">
          <span id="main_picture_none_text" style="display: <?php echo $text_display; ?>">[None]</span>
          <img id="main_picture_image" class="frame" style="display: <?php echo $image_display; ?>" src="<?php echo $image_source; ?>" alt="<?php echo $title; ?>">
        </div>
        <div style="text-align: right">
          <?php echo $renderer->javascript_button_as_HTML ('Browse...', 'field.show_picker ()', '{icons}buttons/browse'); ?>
        </div>
        <div class="notes" style="margin-top: .5em">
        <div id="main_picture_changed" class="caution" style="display: none">
          <?php echo $this->app->resolve_icon_as_html ('{icons}indicators/warning', 'Warning', '16px'); ?>
          Modified - click "Save" to store changes
        </div>
      </div>
  <?php
      $renderer->finish_row ();
      $renderer->finish_block ();
    }
  }
}
?>
