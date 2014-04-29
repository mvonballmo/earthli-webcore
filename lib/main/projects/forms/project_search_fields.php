<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage forms
 * @version 3.5.0
 * @since 1.7.0
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
require_once ('webcore/forms/search_fields.php');

/**
 * Create a filter for {@link JOB}s.
 * @package projects
 * @subpackage forms
 * @version 3.5.0
 * @since 1.7.0
 */
class SEARCH_PROJECT_ENTRY_FIELDS extends SEARCH_ENTRY_FIELDS
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->_add_text ('extra_description', 'Extra description');

    $this->_add_synced_field ('component_id', 0);
    $this->_add_synced_field ('kind', array_keys ($this->app->display_options->entry_kinds ()));
    $this->_add_synced_field ('not_kind', false);
  }

  /**
   * Add fields for search properties to this form.
   * @param FORM $form
   * @param boolean $extra_visibility Default visibility for extra search fields.
   */
  public function add_fields ($form, $extra_visibility = true)
  {
    parent::add_fields ($form);

    $field = new ARRAY_FIELD ();
    $field->id = 'component_id';
    $field->caption = 'Component';
    $form->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'not_kind';
    $field->caption = 'Kind';
    $field->description = 'Invert selection';
    $form->add_field ($field);

    $field = new ARRAY_FIELD ();
    $field->id = 'kind';
    $field->caption = ' ';
    $form->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = 'extra_description';
    $field->caption = 'Extra Description';
    $form->add_field ($field);
  }

  /**
   * List of sortable values
   * @return string[]
   */
  protected function _sort_values ()
  {
    $Result = parent::_sort_values ();
    $Result ['kind'] = 'Kind';
//    $Result ['component_id'] = 'Component';
    return $Result;
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query The query to which to apply parameters.
   * @param stdClass $obj The object from which to extract parameters.
   */
  public function apply_to_query ($query, $obj)
  {
    parent::apply_to_query ($query, $obj);

    $kinds = $this->app->display_options->entry_kinds ();

    if (sizeof ($obj->parameters ['kind']) == sizeof ($kinds))
    {
      if ($obj->parameters ['not_kind'])
      {
        $query->restrict ('0');
      }
    }
    else if (sizeof ($obj->parameters ['kind']) == 0)
    {
      if (! $obj->parameters ['not_kind'])
      {
        $query->restrict ('0');
      }
    }
    else
    {
      if ($obj->parameters ['not_kind'])
      {
        $operator = Operator_not_in;
      }
      else
      {
        $operator = Operator_in;
      }

      $query->restrict_by_op ('entry.kind', $obj->parameters ['kind'], $operator);
    }
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

    $kinds = $this->app->display_options->entry_kinds ();

    if (sizeof ($obj->parameters ['kind']) == sizeof ($kinds))
    {
      if ($obj->parameters ['not_kind'])
      {
        $Result [] = '<span class="error">All kinds are removed</span>';
      }
    }
    else if (sizeof ($obj->parameters ['kind']) == 0)
    {
      if (! $obj->parameters ['not_kind'])
      {
        $Result [] = '<span class="error">No kinds are included</span>';
      }
    }
    else
    {
      $param_kinds = $obj->parameters ['kind'];

      foreach ($param_kinds as $kind_id)
      {
        $kind_text [] = $kinds [$kind_id]->title;
      }

      if ($obj->parameters ['not_kind'])
      {
        $Result [] = 'Kind is not ' . join (',', $kind_text);
      }
      else
      {
        $Result [] = 'Kind is ' . join (',', $kind_text);
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
    $renderer->draw_check_box_row ('not_kind');

    /** @var $display_options PROJECT_APPLICATION_DISPLAY_OPTIONS */
    $display_options = $this->app->display_options;
    $kinds = $display_options->entry_kinds ();
    if (sizeof ($kinds))
    {
      $props = $renderer->make_list_properties ();
      $index = 0;
      foreach ($kinds as $kind)
      {
        $props->add_item ($this->context->get_text_with_icon($kind->icon, $kind->title, Sixteen_px), $index);
        $index += 1;
      }
      $renderer->draw_check_group_row ('kind', $props);
    }

    parent::_draw_date_fields ($form, $renderer);
  }
}

/**
 * Create a filter for {@link JOB}s.
 * @package projects
 * @subpackage forms
 * @version 3.5.0
 * @since 1.7.0
 */
class SEARCH_JOB_FIELDS extends SEARCH_PROJECT_ENTRY_FIELDS
{
}

/**
 * Create a filter for {@link CHANGE}s.
 * @package projects
 * @subpackage forms
 * @version 3.5.0
 * @since 1.7.0
 */
class SEARCH_CHANGE_FIELDS extends SEARCH_PROJECT_ENTRY_FIELDS
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->_add_text ('files', 'Files', false, false, 'chng');
  }
}