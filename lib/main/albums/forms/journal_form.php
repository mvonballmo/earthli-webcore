<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage forms
 * @version 3.4.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
 * Edit or create a {@link JOURNAL}.
 * @package albums
 * @subpackage forms
 * @version 3.4.0
 * @since 2.5.0
 */
class JOURNAL_FORM extends ALBUM_ENTRY_FORM
{
  /**
   * @param ALBUM $folder Album in which to add or edit the journal entry.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new INTEGER_FIELD ();
    $field->id = 'lo_temp';
    $field->title = 'Low Temperature';
    $field->min_value = -60;
    $field->max_value = 60;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'hi_temp';
    $field->title = 'High Temperature';
    $field->min_value = -60;
    $field->max_value = 60;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'weather_type';
    $field->title = 'Weather Type';
    $field->required = true;
    $this->add_field ($field);

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'weather';
    $field->title = 'Weather';
    $this->add_field ($field);
  }

  /**
   * Load initial properties from this journal entry.
   * @param JOURNAL $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('weather_type', $obj->weather_type);
    $this->set_value ('weather', $obj->weather);
    $this->set_value ('lo_temp', $obj->lo_temp);
    $this->set_value ('hi_temp', $obj->hi_temp);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    $this->set_value ('weather_type', 1);
    $this->set_value ('lo_temp', 0);
    $this->set_value ('hi_temp', 0);
  }

  /**
   * Does this form hold valid data for this journal entry?
   * @param JOURNAL $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    if ($this->value_for ('lo_temp') > $this->value_for ('hi_temp'))
    {
      $this->record_error ('temps', "Please make sure that low temperature is less than the high temperature.");
    }
  }

  /**
   * Store the form's values to this journal entry.
   * @param JOURNAL $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    $obj->weather = $this->value_as_text ('weather');
    $obj->lo_temp = $this->value_as_text ('lo_temp');
    $obj->hi_temp = $this->value_as_text ('hi_temp');
    $obj->weather_type = $this->value_for ('weather_type');

    parent::_store_to_object ($obj);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');
    $renderer->draw_check_box_row ('is_visible');
    $renderer->draw_separator ();

    $renderer->draw_date_row ('day');
    $renderer->draw_separator ();

    $icons = $this->app->display_options->weather_icons ();

    if (sizeof ($icons))
    {
      $props = $renderer->make_list_properties ();
      $props->items_per_row = 7;

      $i = 0;
      foreach ($icons as $icon)
      {
        $i += 1;
        $props->add_item ($icon->icon_as_html ('30px'), $i);
      }

      $renderer->draw_radio_group_row ('weather_type', $props);
    }

    $renderer->draw_text_box_row ('weather', $renderer->default_control_width, '3em');
    $renderer->draw_separator ();

    $options = new FORM_TEXT_CONTROL_OPTIONS ();
    $options->width = '3em';

    $renderer->start_row ('Temperatures');
    echo 'Low ';
    echo $renderer->text_line_as_html ('lo_temp', $options);
    echo ' High ';
    echo $renderer->text_line_as_html ('hi_temp', $options);
    echo ' <span class="notes">(Temperatures are in Celsius)</span>';
    $renderer->finish_row ();
    $renderer->draw_error_row ('lo_temp');
    $renderer->draw_error_row ('hi_temp');

    $renderer->draw_error_row ('temps');
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();
    $renderer->draw_separator ();
    $renderer->draw_text_box_row ('description', $renderer->default_control_width, '20em');

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();

    $this->_draw_history_item_controls ($renderer, false);

    $renderer->finish ();
  }
}
?>