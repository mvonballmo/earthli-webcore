<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage forms
 * @version 3.6.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
 * 
 * @package projects
 * @subpackage forms
 * @version 3.6.0
 * @since 1.4.1
 */
class RELEASE_FORM extends OBJECT_IN_FOLDER_FORM
{
  /**
   * @param PROJECT $folder Project in which to add or edit the release.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new INTEGER_FIELD ();
    $field->id = 'state';
    $field->caption = '';
    $this->add_field ($field);

    $field = new DATE_FIELD ();
    $field->id = 'time_scheduled';
    $field->caption = 'Ship date';
    $this->add_field ($field);

    $field = new DATE_FIELD ();
    $field->id = 'time_testing_scheduled';
    $field->caption = 'Test date';
    $this->add_field ($field);

    $field = new MUNGER_TEXT_FIELD ();
    $field->id = 'summary';
    $field->caption = 'Summary';
    $field->max_length = 65535;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'branch_id';
    $field->caption = 'Branch';
    $this->add_field ($field);
    
    $branch_query = $folder->branch_query ();
    $this->_branches = $branch_query->objects ();
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    
    $this->set_value ('state', Planned);
    
    $branch_id = read_var ('id');
    if (empty ($branch_id))
    {
      if (! empty($this->_branches))
      {
        $this->set_value ('branch_id', $this->_branches[0]->id);
      }
    }
    else
    {
      $this->set_value ('branch_id', $branch_id);
    }
  }

  /**
   * Load initial properties from this branch.
   * 
   * @param RELEASE $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);

    $this->set_value ('state', $obj->state);
    $this->set_value ('summary', $obj->summary);
    $this->set_value ('time_scheduled', $obj->time_scheduled);
    $this->set_value ('time_testing_scheduled', $obj->time_testing_scheduled);
    $this->set_value ('branch_id', $obj->branch_id);

    $this->set_visible('branch_id', false);
  }

  /**
   * Called after fields are validated.
   * 
   * @param object $obj Object being validated.
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);

    /** @var DATE_TIME $test */
    $test = $this->value_for ('time_testing_scheduled');
    /** @var DATE_TIME $ship */
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
   * @param RELEASE $obj
   * @access private
   */
  protected function _store_to_object ($obj)
  {
    $obj->summary = $this->value_for ('summary');

    /** @var DATE_TIME $time_scheduled */
    $time_scheduled = $this->value_for('time_scheduled');
    $obj->set_time_scheduled ($time_scheduled);

    /** @var DATE_TIME $time_testing_scheduled */
    $time_testing_scheduled = $this->value_for('time_testing_scheduled');
    $obj->set_time_testing_scheduled ($time_testing_scheduled);

    $obj->branch_id = $this->value_for ('branch_id');

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
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_line_row ('title');

    if ($this->visible ('branch_id'))
    {
      $props = $renderer->make_list_properties ();
      $props->css_class = 'small';
      foreach ($this->_branches as $branch)
      {
        $props->add_item ($branch->title_as_plain_text (), $branch->id);
      }

      $renderer->draw_drop_down_row ('branch_id', $props);
    }

    if (! $this->object_exists () || ! $this->_object->shipped ())
    {
      $renderer->draw_date_row ('time_testing_scheduled');
      $renderer->draw_date_row ('time_scheduled');
    }

    $renderer->start_block ('Status');
    
    if (! $this->object_exists () || $this->_object->planned ())
    {
      $props = $renderer->make_list_properties ();
      $props->show_descriptions = true;
      if ($this->visible ('is_visible'))
      {
        $props->add_item ($this->app->resolve_icon_as_html ('{icons}indicators/invisible', Sixteen_px, ' ') . ' Hidden', Hidden, 'Prevent searching or browsing by non-admin users.');
      }
      $props->add_item ($this->app->resolve_icon_as_html ('{icons}buttons/calendar', Sixteen_px, ' ') . ' Planned', Planned, 'Release is in development.');
      $props->add_item ($this->app->resolve_icon_as_html ('{app_icons}statuses/working', Sixteen_px, ' ') . ' Testing', Testing, 'Release is feature-complete and in testing.');
      $renderer->draw_radio_group_row ('state', $props);
    }
    else
    {
      $props = $renderer->make_list_properties ();
      $props->show_descriptions = true;
      if ($this->visible ('is_visible'))
      {
        $props->add_item ($this->app->resolve_icon_as_html ('{icons}indicators/invisible', Sixteen_px, ' ') . ' Hidden', Hidden, 'Prevent searching or browsing by non-admin users.');
      }
      $props->add_item ($this->app->resolve_icon_as_html ('{icons}buttons/ship', Sixteen_px, ' ') . ' Shipped', Shipped, 'Jobs and changes can still be added and removed.');
      $props->add_item ($this->app->resolve_icon_as_html ('{icons}indicators/locked', Sixteen_px, ' ') . ' Locked', Locked, 'Changes and jobs cannot be added or removed.');
      $renderer->draw_radio_group_row ('state', $props);
    }
    
    $renderer->finish_block ();

    $renderer->draw_text_box_row ('summary');
    $renderer->draw_text_box_row ('description');
    $renderer->draw_submit_button_row ();
    $this->_draw_history_item_controls ($renderer);
    $renderer->finish ();
  }

  /**
   * Name of the default permission set to use.
   * 
   * @var string
   * @access private
   */
  protected $_privilege_set = Privilege_set_folder;
  
  /**
   * Cached list of branches for the folder in which the release resides.
   *
   * @var BRANCH[]
   */
  protected $_branches;
}