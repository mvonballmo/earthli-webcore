<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage forms
 * @version 3.5.0
 * @since 2.7.0
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
require_once ('webcore/forms/search_fields.php');

/**
 * Create a filter for {@link PICTURE}s.
 * @package albums
 * @subpackage forms
 * @version 3.5.0
 * @since 2.7.0
 */
class SEARCH_PICTURE_FIELDS extends SEARCH_ENTRY_FIELDS
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->_add_text ('file_name', 'File name', false, false, 'pic');
  }
}

/**
 * Create a filter for {@link JOURNAL}s.
 * @package albums
 * @subpackage forms
 * @version 3.5.0
 * @since 2.7.0
 */
class SEARCH_JOURNAL_FIELDS extends SEARCH_ENTRY_FIELDS
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->_add_text ('weather', 'Weather', false, false, 'jrnl');

    $this->_add_synced_field ('colder_than', '');
    $this->_add_synced_field ('warmer_than', '');
    $this->_add_synced_field ('weather_type', array ());
    $this->_add_synced_field ('not_weather_type', false);
  }

  /**
   * Add fields for search properties to this form.
   * @param FORM $form
   * @param boolean $extra_visibility Default visibility for extra search fields.
   */
  public function add_fields ($form, $extra_visibility = true)
  {
    parent::add_fields ($form);

    $field = new INTEGER_FIELD ();
    $field->id = 'colder_than';
    $field->caption = 'Colder than';
    $form->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'warmer_than';
    $field->caption = 'Warmer than';
    $form->add_field ($field);

    $field = new ARRAY_FIELD ();
    $field->id = 'weather_type';
    $field->caption = ' ';
    $form->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'not_weather_type';
    $field->caption = 'Weather';
    $field->description = 'Invert selection';
    $form->add_field ($field);
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query The query to which to apply parameters.
   * @param object $obj The object from which to extract parameters.
   */
  public function apply_to_query ($query, $obj)
  {
    parent::apply_to_query ($query, $obj);

    if ($obj->parameters ['colder_than'] != '')
    {
      $query->restrict ('jrnl.hi_temp < ' . $obj->parameters ['colder_than']);
    }

    if ($obj->parameters ['warmer_than'] != '')
    {
      $query->restrict ('jrnl.lo_temp > ' . $obj->parameters ['warmer_than']);
    }

    if ($obj->parameters ['weather_type'] != '')
    {
      if ($obj->parameters ['not_weather_type'])
      {
        $operator = Operator_not_in;
      }
      else
      {
        $operator = Operator_in;
      }

      $query->restrict_by_op ('jrnl.weather_type', $obj->parameters ['weather_type'], $operator);
    }
  }

  /**
   * List of sortable values
   * @return string[]
   */
  protected function _sort_values ()
  {
    $Result = parent::_sort_values ();
    $Result ['jrnl.lo_temp'] = 'Low temp';
    $Result ['jrnl.hi_temp'] = 'High temp';
    $Result ['jrnl.weather_type'] = 'Weather';
    return $Result;
  }

  /**
   * Text representation of applied search fields.
   * @param object $obj
   * @return string
   * @access private
   */
  protected function _restrictions_as_text ($obj)
  {
    $Result = parent::_restrictions_as_text ($obj);
    $props = $this->app->display_options->weather_icons ();

    if (sizeof ($obj->parameters ['weather_type']))
    {
      foreach ($obj->parameters ['weather_type'] as $type)
      {
        $weather_types [] = $props [$type]->title;
      }

      if ($obj->parameters ['not_weather_type'])
      {
        $Result [] = 'Weather is not one of ' . join (', ', $weather_types);
      }
      else
      {
        $Result [] = 'Weather is one of ' . join (', ', $weather_types);
      }
    }

    return $Result;
  }

  /**
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_date_fields ($form, $renderer)
  {
    $renderer->draw_check_box_row ('not_weather_type');

    $props = $renderer->make_list_properties ();
    $props->items_per_row = 7;

    $i = 0;
    $icons = $this->app->display_options->weather_icons ();
    foreach ($icons as $icon)
    {
      $i += 1;
      $props->add_item ($icon->icon_as_html ('20px'), $i);
    }
    $renderer->draw_check_group_row ('weather_type', $props);

    $renderer->start_row (' ');

      $options = new FORM_TEXT_CONTROL_OPTIONS ();
      $options->width = '3em';

      echo 'Warmer than ' . $renderer->text_line_as_html ('warmer_than', $options);
      echo '&deg; and cooler than ' . $renderer->text_line_as_html ('colder_than', $options) . '&deg';
    $renderer->finish_row ();

    parent::_draw_date_fields ($form, $renderer);
  }
}

?>
