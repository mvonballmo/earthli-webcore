<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage forms
 * @version 3.0.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/forms/object_in_folder_form.php');

/**
 * Edit or create a {@link RELEASE}.
 * @package projects
 * @subpackage forms
 * @version 3.0.0
 * @since 1.4.1
 */
class RELEASE_FORM extends OBJECT_IN_FOLDER_FORM
{
  /**
   * @param PROJECT &$folder Project in which to add or edit the job.
   */
  function RELEASE_FORM (&$folder)
  {
    OBJECT_IN_FOLDER_FORM::OBJECT_IN_FOLDER_FORM ($folder);

    $field = new INTEGER_FIELD ();
    $field->id = 'state';
    $field->title = '';
    $this->add_field ($field);

    $field = new DATE_FIELD ();
    $field->id = 'time_scheduled';
    $field->title = 'Ship date';
    $this->add_field ($field);

    $field = new DATE_FIELD ();
    $field->id = 'time_testing_scheduled';
    $field->title = 'Test date';
    $this->add_field ($field);

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'summary';
    $field->title = 'Summary';
    $field->max_length = 65535;
    $this->add_field ($field);
  }

  function load_with_defaults ()
  {
    parent::load_with_defaults ();
    
    $this->set_value ('state', Planned);

    $field =& $this->field_at ('id');
    $field->visible = TRUE;
    $field->title = 'Branch';
  }

  /**
   * Load initial properties from this branch.
   * @param BRANCH &$obj
   */
  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('state', $obj->state);
    $this->set_value ('summary', $obj->summary);
    $this->set_value ('time_scheduled', $obj->time_scheduled);
    $this->set_value ('time_testing_scheduled', $obj->time_testing_scheduled);
  }

  /**
   * Called after fields are validated.
   * @param object &$obj Object being validated.
   * @access private
   */
  function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    $test = $this->value_for ('time_testing_scheduled');
    $ship = $this->value_for ('time_scheduled');

    if ($test->is_valid () && $ship->is_valid ())
    {
      if (! $test->less_than_equal ($ship))
      {
        $this->record_error ('time_testing_scheduled', 'Test date must be before or on ship date.');
      }
    }
  }

  /**
   * Store the form's values for this change.
    * @param RELEASE &$obj
    * @access private
    */
  function _store_to_object (&$obj)
  {
    $obj->summary = $this->value_for ('summary');
    $obj->set_time_scheduled ($this->value_for ('time_scheduled'));
    $obj->set_time_testing_scheduled ($this->value_for ('time_testing_scheduled'));

    parent::_store_to_object ($obj);

    switch ($this->value_for ('state'))
    {
    case Hidden:
      $obj->hide (Defer_database_update);
      break;
    case Planned:
      $obj->plan (Defer_database_update);
      break;
    case Testing:
      $obj->test (Defer_database_update);
      break;
    case Shipped:
      $obj->ship (Defer_database_update);
      break;
    case Locked:
      $obj->lock (Defer_database_update);
      break;
    }    
  }

  /**
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_controls (&$renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');
    $renderer->draw_separator ();

    if ($this->visible ('id'))
    {
      $branch_query = $this->_folder->branch_query ();
      $branches =& $branch_query->objects ();
      $props = $renderer->make_list_properties ();
      $props->width = '10em';
      foreach ($branches as $branch)
        $props->add_item ($branch->title_as_plain_text (), $branch->id);

      $renderer->draw_drop_down_row ('id', $props);
      $renderer->draw_separator ();
    }

    if (! $this->object_exists () || ! $this->_object->shipped ())
    {
      $renderer->draw_date_row ('time_testing_scheduled');
      $renderer->draw_date_row ('time_scheduled');
    }

    $renderer->draw_separator ();
    $renderer->start_row ('Status');
    $renderer->start_block (TRUE);
    
    if (! $this->object_exists () || $this->_object->planned ())
    {
      $props = $renderer->make_list_properties ();
      $props->show_descriptions = TRUE;
      $props->width = '';
      if ($this->visible ('is_visible'))
      {
        $props->add_item ($this->app->resolve_icon_as_html ('{icons}indicators/invisible', ' ', '16px') . ' Hidden', Hidden, 'Prevent searching or browsing by non-admin users.');
      }
      $props->add_item ($this->app->resolve_icon_as_html ('{icons}buttons/calendar', ' ', '16px') . ' Planned', Planned, 'Release is in development.');
      $props->add_item ($this->app->resolve_icon_as_html ('{app_icons}statuses/working', ' ', '16px') . ' Testing', Testing, 'Release is feature-complete and in testing.');
      $renderer->draw_radio_group_row ('state', $props);
    }
    else
    {
      $props = $renderer->make_list_properties ();
      $props->show_descriptions = TRUE;
      if ($this->visible ('is_visible'))
      {
        $props->add_item ($this->app->resolve_icon_as_html ('{icons}indicators/invisible', ' ', '16px') . ' Hidden', Hidden, 'Prevent searching or browsing by non-admin users.');
      }
      $props->add_item ($this->app->resolve_icon_as_html ('{icons}buttons/ship', ' ', '16px') . ' Shipped', Shipped, 'Jobs and changes can still be added and removed.');
      $props->add_item ($this->app->resolve_icon_as_html ('{icons}indicators/locked', ' ', '16px') . ' Locked', Locked, 'Changes and jobs cannot be added or removed.');
      $renderer->draw_radio_group_row ('state', $props);
    }
    
    $renderer->finish_block ();
    $renderer->finish_row ();

    $renderer->draw_separator ();
    $renderer->draw_text_box_row ('summary');
    $renderer->draw_separator ();
    $renderer->draw_text_box_row ('description');
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();
    $this->_draw_history_item_controls ($renderer, FALSE);
    $renderer->finish ();
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  var $_privilege_set = Privilege_set_folder;
}
?>