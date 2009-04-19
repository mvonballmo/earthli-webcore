<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/forms/form.php');
require_once ('webcore/gui/layer.php');
require_once ('webcore/obj/search.php');

/**
 * A set of fields used by search forms.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 * @abstract
 */
abstract class SEARCH_FIELDS extends WEBCORE_OBJECT
{
  /**
   * Field id in object and form.
   * @var string
   */
  public $base_name;

  /**
   * Display name used for sorting.
   * @var string
   */
  public $title;

  /**
   * @var boolean
   */
  public $sortable;

  /**
   * @param CONTEXT $context
   * @param string $base_name
   */
  public function __construct ($context, $base_name, $title = '', $sortable = true, $table_name = '')
  {
    parent::__construct ($context);

    if (! $title)
    {
      $title = $base_name;
    }

    $this->base_name = $base_name;
    $this->title = $title;
    $this->sortable = $sortable;
    $this->_table_name = $table_name;
  }

  /**
   * Return text describing this search field.
   * @return string
   * @abstract
   */
  public function description ($obj)
  {
    throw new METHOD_NOT_IMPLEMENTED_EXCEPTION();
  }

  /**
   * Add fields for search properties to this form.
   * @param FORM $form
   * @abstract
   */
  public abstract function add_fields ($form);

  /**
   * Add sortable values to the array.
   * @param array[string, string] $values
   */
  public function add_sort_fields ($values)
  {
    if ($this->sortable)
    {
      $values [$this->base_name] = $this->title;
    }
  }

  /**
   * Set default properties for these fields in the form.
   * @param FORM $form
   */
  public function load_with_defaults ($form) {}

  /**
   * Load properties from object into form.
   * @param FORM $form
   * @param object $obj
   * @abstract
   */
  public abstract function load_from_object ($form, $obj);

  /**
   * Make sure data is correct.
   * @param FORM $form
   * @param object $obj
   */
  public function validate ($form, $obj) {}

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param object $obj
   * @abstract
   */
  public abstract function store_to_object ($form, $obj);

  /**
   * Restrict the query by these fields.
   * @param QUERY $query The query to which to apply parameters.
   * @param object $obj The object from which to extract parameters.
   * @abstract
   */
  public function apply_to_query ($query, $obj)
  {
    throw new METHOD_NOT_IMPLEMENTED_EXCEPTION();
  }

  /**
   * Are there errors or values for these fields?
   * @param FORM $form
   * @return boolean
   */
  public function needs_visible ($form)
  {
    return false;
  }

  /**
   * To which table should these fields store?
   * @param QUERY $query
   * @param string $field_name
   * @return string
   */
  public function full_name ($query, $field_name)
  {
    $Result = $this->_table_name;
    if (! $Result && (strpos ($field_name, '.') === false))
    {
      $Result = $query->alias;
    }
    $Result .= '.' . $field_name;
    return $Result;
  }

  /**
   * Draw selectors for these fields in the form.
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @abstract
   */
  public function draw_fields ($form, $renderer)
  {
    throw new METHOD_NOT_IMPLEMENTED_EXCEPTION();
  }

  /**
   * Store field values as hidden fields in the form.
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @abstract
   */
  public abstract function draw_fields_hidden ($form, $renderer);

  /**
   * Name of the database table to store to/read from.
   * @var string
   * @access private
   */
  protected $_table_name;
}

/**
 * Handles display and processing for date fields.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEARCH_DATE_FIELDS extends SEARCH_FIELDS
{
  public function before_name ()
  {
    return $this->base_name . '_before';
  }

  public function after_name ()
  {
    return $this->base_name . '_after';
  }

  public function search_type_name ()
  {
    return $this->base_name . '_search_type';
  }

  /**
   * Return text describing this search field.
   * @return string
   */
  public function description ($obj)
  {
    switch ($obj->parameters [$this->search_type_name ()])
    {
    case Search_date_today:
      return $this->title . ' in the last day.';
    case Search_date_this_week:
      return $this->title . ' in the last week.';
    case Search_date_this_month:
      return $this->title . ' in the last month.';
    case Search_date_constant:
      $date_before = $obj->parameters [$this->before_name ()];
      $date_after = $obj->parameters [$this->after_name ()];
      $clauses = array ();
      $f = $date_before->formatter ();
      $f->type = Date_time_format_short_date;
      $f->show_local_time = false;
      if ($date_after->is_valid ())
      {
        $clauses [] = $this->title . ' after ' . $date_after->format ($f);
      }
      if ($date_before->is_valid ())
      {
        $clauses [] = $this->title . ' before ' . $date_before->format ($f);
      }
      if (sizeof ($clauses))
      {
        return join (' and ', $clauses);
      }
      
      return ''; 
    default:
      throw new UNKNOWN_VALUE_EXCEPTION($obj->parameters [$this->search_type_name ()]);
    }
  }

  /**
   * Add fields for search properties to this form.
   * @param FORM $form
   */
  public function add_fields ($form)
  {
    $field = new ENUMERATED_FIELD ();
    $field->id = $this->search_type_name ();
    $field->title = 'on';
    $field->add_value (Search_date_today);
    $field->add_value (Search_date_this_week);
    $field->add_value (Search_date_this_month);
    $field->add_value (Search_date_constant);
    $form->add_field ($field);

    $field = new DATE_TIME_FIELD ();
    $field->id = $this->after_name ();
    $field->title = 'after';
    $form->add_field ($field);

    $field = new DATE_TIME_FIELD ();
    $field->id = $this->before_name ();
    $field->title = 'before';
    $form->add_field ($field);
  }

  /**
   * Set default properties for these fields in the form.
   * @param FORM $form
   */
  public function load_with_defaults ($form)
  {
    $form->set_value ($this->search_type_name (), Search_date_constant);
  }

  /**
   * Load properties from object into form.
   * @param FORM $form
   * @param object $obj
   */
  public function load_from_object ($form, $obj)
  {
    $form->set_value ($this->search_type_name (), $obj->parameters [$this->search_type_name ()]);
    $form->set_value ($this->after_name (), $obj->parameters [$this->after_name ()]);
    $form->set_value ($this->before_name (), $obj->parameters [$this->before_name ()]);
  }

  /**
   * Make sure data is correct.
   * @param FORM $form
   * @param object $obj
   */
  public function validate ($form, $obj)
  {
    $after = $form->value_for ($this->after_name ());
    $before = $form->value_for ($this->before_name ());

    if ($before->is_valid () && $after->is_valid () && $before->less_than ($after))
    {
      $form->record_error ($this->base_name, "Make sure 'before' is before 'after'");
    }
  }

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param object $obj
   */
  public function store_to_object ($form, $obj)
  {
    $obj->parameters [$this->search_type_name ()] = $form->value_for ($this->search_type_name ());
    $obj->parameters [$this->after_name ()] = $form->value_for ($this->after_name ());
    $obj->parameters [$this->before_name ()] = $form->value_for ($this->before_name ());
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query The query to which to apply parameters.
   * @param object $obj The object from which to extract parameters.
   */
  public function apply_to_query ($query, $obj)
  {
    $now = time ();

    switch ($obj->parameters [$this->search_type_name ()])
    {
    case Search_date_today:
      $date_after = new DATE_TIME (mktime (0, 0, 0, date ('n'), date ('d'), date ('Y')));
      $date_before = new DATE_TIME ($now);
      break;
    case Search_date_this_week:
      $date_after = new DATE_TIME ($now - (86400 * 7));
      $date_before = new DATE_TIME ($now);
      break;
    case Search_date_this_month:
      $date_after = new DATE_TIME ($now - (86400 * 30));
      $date_before = new DATE_TIME ($now);
      break;
    case Search_date_constant:
      $date_before = $obj->parameters [$this->before_name ()];
      $date_after = $obj->parameters [$this->after_name ()];
      break;
    }

    if (isset ($date_before) && isset ($date_after))
    {
      $query->restrict_date ($this->full_name ($query, $this->base_name), $date_after, $date_before);
    }
  }

  /**
   * Are there errors or values for these fields?
   * @param FORM $form
   * @return boolean
   */
  public function needs_visible ($form)
  {
    $date_before = $form->value_for ($this->before_name ());
    $date_after = $form->value_for ($this->after_name ());

    return ($date_before->is_valid () || $form->num_errors ($this->before_name ())
            || $date_after->is_valid () || $form->num_errors ($this->after_name ())
            || ($form->value_for ($this->search_type_name ()) != Search_date_constant));
  }

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   */
  public function draw_fields ($form, $renderer)
  {
    $props = $renderer->make_list_properties ();
    $props->add_item ('Today', Search_date_today);
    $props->add_item ('This week', Search_date_this_week);
    $props->add_item ('This month', Search_date_this_month);
    $props->add_item ('Selected dates', Search_date_constant);
    $renderer->draw_drop_down_row ($this->search_type_name (), $props);

    $renderer->draw_date_row ($this->after_name ());
    $renderer->draw_date_row ($this->before_name ());
    $renderer->draw_error_row ($this->base_name);
  }

  /**
   * Store field values as hidden fields in the form.
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   */
  public function draw_fields_hidden ($form, $renderer)
  {
    $renderer->draw_hidden ($this->search_type_name ());
    $renderer->draw_hidden ($this->after_name ());
    $renderer->draw_hidden ($this->before_name ());
  }
}

/**
 * Handles display and processing for a user field.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEARCH_USER_FIELDS extends SEARCH_FIELDS
{
  public function search_type_name ()
  {
    return $this->base_name . '_search_type';
  }

  public function ids_name ()
  {
    return $this->base_name . '_ids';
  }

  /**
   * Return text describing this search field.
   * @return string
   * @abstract
   */
  public function description ($obj)
  {
    $search_type = $obj->parameters [$this->search_type_name ()];

    switch ($search_type)
    {
    case Search_user_context_none:
      return $this->title . ' matches user from context';
    case Search_user_context_login:
      return $this->title . ' matches logged-in user';
    case Search_user_constant:
      $user_names = $obj->parameters [$this->base_name];
      if ($user_names)
      {
        $user_names = explode (';', $user_names);
        if (sizeof ($user_names))
        {
          return $this->title . ' is ' . join (' or ', $user_names);
        }
      }
    default:
      return '';
    }
  }

  /**
   * Add fields for search properties to this form.
   * @param FORM $form
   */
  public function add_fields ($form)
  {
    $field = new ENUMERATED_FIELD ();
    $field->id = $this->search_type_name ();
    $field->title = '';
    $field->add_value (Search_user_context_none);
    $field->add_value (Search_user_context_login);
    $field->add_value (Search_user_constant);
    $form->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = $this->base_name;
    $field->title = 'by';
    $field->tag_validator_type = Tag_validator_none;
    $form->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = $this->ids_name ();
    $field->title = $this->ids_name ();
    $field->tag_validator_type = Tag_validator_none;
    $form->add_field ($field);
  }

  /**
   * Set default properties for these fields in the form.
   * @param FORM $form
   */
  public function load_with_defaults ($form)
  {
    $form->set_value ($this->search_type_name (), Search_user_constant);
  }

  /**
   * Load properties from object into form.
   * @param FORM $form
   * @param object $obj
   */
  public function load_from_object ($form, $obj)
  {
    $form->set_value ($this->base_name, $obj->parameters [$this->base_name]);
    $form->set_value ($this->search_type_name (), $obj->parameters [$this->search_type_name ()]);
    $form->set_value ($this->ids_name (), $obj->parameters [$this->ids_name ()]);
  }

  /**
   * Make sure data is correct.
   * @param FORM $form
   * @param object $obj
   */
  public function validate ($form, $obj)
  {
    $user_list = $form->value_for ($this->base_name);

    if ($user_list)
    {
      $user_query = $this->app->user_query ();
      $user_names = explode (';', $user_list);
      $user_ids = array ();

      foreach ($user_names as $name)
      {
        $user = $user_query->object_at_name ($name);
        if (! $user)
        {
          $form->record_error ($this->base_name, "[$name] does not exist.");
        }
        else
        {
          $user_ids [] = $user->id;
        }
      }

      if (sizeof ($user_ids) > 0)
      {
        $form->set_value ($this->ids_name (), join (',', $user_ids));
      }
    }
  }

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param object $obj
   */
  public function store_to_object ($form, $obj)
  {
    $obj->parameters [$this->base_name] = $form->value_for ($this->base_name);
    $obj->parameters [$this->search_type_name ()] = $form->value_for ($this->search_type_name ());
    $obj->parameters [$this->ids_name ()] = $form->value_for ($this->ids_name ());
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query
   * @param object $obj
   */
  public function apply_to_query ($query, $obj, $user)
  {
    $search_type = $obj->parameters [$this->search_type_name ()];

    if ($search_type != Search_user_constant)
    {
      if (isset ($user))
      {
        $search_user = $user;
      }
      else if ($search_type == Search_user_context_login)
      {
        $search_user = $this->app->login;
      }

      if (isset ($search_user))
      {
        $user_restriction = $this->full_name ($query, $this->base_name) . ' = ' . $search_user->id;
        $query->restrict ($user_restriction);
      }
    }
    else
    {
      $user_ids = $obj->parameters [$this->ids_name ()];
      if ($user_ids)
      {
        $user_restriction = $this->full_name ($query, $this->base_name) . ' IN (' . $user_ids . ')';
        $query->restrict ($user_restriction);
      }
    }
  }

  /**
   * Are there errors or values for these fields?
   * @param FORM $form
   * @return boolean
   */
  public function needs_visible ($form)
  {
    return $form->value_for ($this->base_name) || ($form->value_for ($this->search_type_name ()) != Search_user_constant);
  }

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   */
  public function draw_fields ($form, $renderer)
  {
    $props = $renderer->make_list_properties ();
    $props->add_item ('Context or none', Search_user_context_none);
    $props->add_item ('Context or login', Search_user_context_login);
    $props->add_item ('Name(s) listed', Search_user_constant);

    $renderer->start_row ('by');
      echo $renderer->drop_down_as_html ($this->search_type_name (), $props);
      echo ' ';
      echo $renderer->text_line_as_html ($this->base_name);
    $renderer->finish_row ();

    $renderer->draw_text_row (' ', 'Separate multiple names with a semi-colon.', 'notes');
    $renderer->draw_error_row ($this->base_name, ' ', $renderer->width);
  }

  /**
   * Store field values as hidden fields in the form.
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   */
  public function draw_fields_hidden ($form, $renderer)
  {
    $renderer->draw_hidden ($this->search_type_name ());
    $renderer->draw_hidden ($this->base_name);
  }
}

/**
 * Handles display and processing for a searchable text field.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEARCH_TEXT_FIELDS extends SEARCH_FIELDS
{
  /**
   * Is this text search initially selected?
   * @var boolean
   */
  public $selected_by_default = true;

  /**
   * Return text describing this search field.
   * @return string
   * @abstract
   */
  public function description ($obj)
  {
    if ($obj->parameters [$this->base_name])
    {
      return $this->title;
    }
    
    return '';
  }

  /**
   * Add fields for search properties to this form.
   * @param FORM $form
   */
  public function add_fields ($form)
  {
    $field = new BOOLEAN_FIELD ($form->app);
    $field->id = $this->base_name;
    $field->title = $this->title;
    $form->add_field ($field);
  }

  /**
   * Set default properties for these fields in the form.
   * @param FORM $form
   */
  public function load_with_defaults ($form)
  {
    $form->set_value ($this->base_name, $this->selected_by_default);
  }

  /**
   * Load properties from object into form.
   * @param FORM $form
   * @param object $obj
   */
  public function load_from_object ($form, $obj)
  {
    $form->set_value ($this->base_name, $obj->parameters [$this->base_name]);
  }

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param object $obj
   */
  public function store_to_object ($form, $obj)
  {
    $obj->parameters [$this->base_name] = $form->value_for ($this->base_name);
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query
   * @param object $obj
   * @param array[string]
   */
  public function apply_to_query ($query, $obj, &$fields)
  {
    if ($obj->parameters [$this->base_name])
    {
      $fields [] = $this->full_name ($query, $this->base_name);
    }
  }

  public function draw_fields ($form, $renderer)
  {
    throw new METHOD_NOT_IMPLEMENTED_EXCEPTION();    
  }
  
  /**
   * Store field values as hidden fields in the form.
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   */
  public function draw_fields_hidden ($form, $renderer)
  {
    $renderer->draw_hidden ($this->base_name);
  }
}

/**
 * Handles display and processing for sort values.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SORT_FIELDS extends SEARCH_FIELDS
{
  public function sort_name ()
  {
    return 'sort_' . $this->base_name;
  }

  public function direction_name ()
  {
    return 'sort_direction_' . $this->base_name;
  }

  /**
   * Return text describing this search field.
   * @param object $obj
   * @param array[string,string] $sort_values
   * @return string
   */
  public function description ($obj, $sort_values)
  {
    if ($obj->parameters [$this->sort_name ()])
    {
      $Result = $sort_values [$obj->parameters [$this->sort_name ()]];

      if ($obj->parameters [$this->direction_name ()] == 'asc')
      {
        $Result .= ' Ascending';
      }
      else
      {
        $Result .= ' Descending';
      }

      return $Result;
    }
    
    return '';
  }

  /**
   * Add fields for search properties to this form.
   * @param FORM $form
   */
  public function add_fields ($form)
  {
    $field = new TEXT_FIELD ($form->app);
    $field->id = $this->sort_name ();
    $field->title = '';
    $field->tag_validator_type = Tag_validator_none;
    $form->add_field ($field);

    $field = new ENUMERATED_FIELD ($form->app);
    $field->id = $this->direction_name ();
    $field->title = '';
    $field->add_value ('asc');
    $field->add_value ('desc');
    $form->add_field ($field);
  }

  /**
   * Set default properties for these fields in the form.
   * @param FORM $form
   */
  public function load_with_defaults ($form)
  {
    $form->set_value ($this->direction_name (), 'asc');
  }

  /**
   * Load properties from object into form.
   * @param FORM $form
   * @param object $obj
   */
  public function load_from_object ($form, $obj)
  {
    $form->set_value ($this->sort_name (), $obj->parameters [$this->sort_name ()]);
    $form->set_value ($this->direction_name (), $obj->parameters [$this->direction_name ()] . $this->base_name);
  }

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param object $obj
   */
  public function store_to_object ($form, $obj)
  {
    $obj->parameters [$this->sort_name ()] = $form->value_for ($this->sort_name ());
    $obj->parameters [$this->direction_name ()] = $form->value_for ($this->direction_name ());
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query The query to which to apply parameters.
   * @param object $obj The object from which to extract parameters.
   * @param array[string] &$orders Add orderings to this list.
   */
  public function apply_to_query ($query, $obj, &$orders)
  {
    $sort = $obj->parameters [$this->sort_name ()];
    if ($sort)
    {
      $dir = $obj->parameters [$this->direction_name ()];
      $orders [] = $this->full_name ($query, $sort) . ' ' . $dir;
    }
  }

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @param array[string,string] $values
   */
  public function draw_fields ($form, $renderer, $sort_values)
  {
    $props = $renderer->make_list_properties ();
    $props->add_item ('[Default]', '');
    foreach ($sort_values as $key => $value)
    {
      $props->add_item ($value, $key);
    }
    $renderer->draw_drop_down_row ($this->sort_name (), $props);

    $props = $renderer->make_list_properties ();
    $props->items_per_row = 2;
    $props->add_item ($this->context->resolve_icon_as_html ('{icons}indicators/sort_ascending', 'Asc', '16px'), 'asc');
    $props->add_item ($this->context->resolve_icon_as_html ('{icons}indicators/sort_descending', 'Desc', '16px'), 'desc');
    $renderer->draw_radio_group_row ($this->direction_name (), $props);
  }

  /**
   * Store field values as hidden fields in the form.
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   */
  public function draw_fields_hidden ($form, $renderer)
  {
    $renderer->draw_hidden ($this->sort_name ());
    $renderer->draw_hidden ($this->direction_name ());
  }
}

/**
 * Provides methods for building object searches.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEARCH_OBJECT_FIELDS extends WEBCORE_OBJECT
{
  /**
   * Used for some user fields.
   * @var USER
   */
  public $user_from_context;

  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);
    $this->_add_synced_field ('search_text', '');
  }

  /**
   * Describe this set of search fields as HTML.
   * @param object $obj
   * @return string
   */
  public function description_as_html ($obj)
  {
    $desc = $this->_description_as_munger_text ($obj);
    $munger = $this->app->html_text_formatter ();
    return $munger->transform ($desc);
  }

  /**
   * Describe this set of search fields as plain text.
   * @param object $obj
   * @return string
   */
  public function description_as_plain_text ($obj)
  {
    $desc = $this->_description_as_munger_text ($obj);
    $munger = $this->app->plain_text_formatter ();
    return $munger->transform ($desc);
  }

  /**
   * Add fields for search properties to this form.
   * @param FORM $form
   */
  public function add_fields ($form)
  {
    $field = new TEXT_FIELD ();
    $field->id = 'search_text';
    $field->title = 'Find';
    $field->tag_validator_type = Tag_validator_none;
    $form->add_field ($field);

    foreach ($this->_sets as $set)
    {
      $set->add_fields ($form);
    }
  }

  /**
   * Load default properties.
   * @param FORM $form
   */
  public function load_with_defaults ($form)
  {
    foreach ($this->_synced_fields as $name => $value)
    {
      $form->set_value ($name, $value);
    }

    foreach ($this->_sets as $set)
    {
      $set->load_with_defaults ($form);
    }
  }

  /**
   * Load initial properties from this branch.
   * @param FORM $form
   * @param object $obj
   */
  public function load_from_object ($form, $obj)
  {
    foreach (array_keys ($this->_synced_fields) as $name)
    {
      $form->set_value ($name, $obj->parameters [$name]);
    }

    foreach ($this->_sets as $set)
    {
      $set->load_from_object ($form, $obj);
    }
  }

  /**
   * Make sure values are valid.
   * @param FORM $form
   * @param object $obj
   */
  public function validate ($form, $obj)
  {
    foreach ($this->_sets as $set)
    {
      $set->validate ($form, $obj);
    }
  }

  /**
   * Store form values to this object.
   * @param FORM $form
   * @param object $obj
   */
  public function store_to_object ($form, $obj)
  {
    foreach (array_keys ($this->_synced_fields) as $name)
    {
      $obj->parameters [$name] = $form->value_for ($name);
    }

    foreach ($this->_sets as $set)
    {
      $set->store_to_object ($form, $obj);
    }
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query
   * @param object $obj
   */
  public function apply_to_query ($query, $obj)
  {
    foreach ($this->_dates as $date)
    {
      $date->apply_to_query ($query, $obj);
    }
    foreach ($this->_users as $user)
    {
      $user->apply_to_query ($query, $obj, $this->user_from_context);
    }

    if ($obj->parameters ['search_text'])
    {
      $fields = array ();

      foreach ($this->_texts as $text)
      {
        $text->apply_to_query ($query, $obj, $fields);
      }

      if (sizeof ($fields))
      {
        $query->add_search ($obj->parameters ['search_text'], $fields);
      }
    }

    $orders = array ();
    foreach ($this->_sorts as $sort)
    {
      $sort->apply_to_query ($query, $obj, $orders);
    }
    if (sizeof ($orders))
    {
      $query->set_order (join (', ', $orders));
    }
  }

  /**
   * Draw all fields into the form.
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   */
  public function draw_fields ($form, $renderer)
  {
    $renderer->draw_text_line_row ('search_text');

    if (read_var ('quick_search'))
    {
      $layer = $this->context->make_layer ('advanced-search-settings');
      $renderer->draw_text_row (' ', $layer->toggle_as_html () . ' Click the arrow for advanced settings.', 'notes');
      $renderer->start_row ();
      $layer->start ();
      $renderer->start_block ();
    }

    $props = $renderer->make_list_properties ();

    foreach (array_keys ($this->_texts) as $id)
    {
      $props->add_item ($id, 1);
    }

    $renderer->draw_check_boxes_row ('In', $props);

    $renderer->draw_separator ();

    $this->_draw_user_fields ($form, $renderer);
    $this->_draw_date_fields ($form, $renderer);

    $renderer->draw_separator ();
    $this->_draw_sort_fields ($form, $renderer);

    if (isset ($layer))
    {
      $renderer->finish_block ();
      $layer->finish ();
      $renderer->finish_row ();
    }
  }

  /**
   * Return text describing this set of search fields.
   * @param object $obj
   * @return string
   * @access private
   */
  protected function _description_as_munger_text ($obj)
  {
    $restrictions = $this->_restrictions_as_text ($obj);

    if (! sizeof ($restrictions))
    {
      $Result = '';
    }
    else
    {
      $Result = '<ul>' . join ("\n", $restrictions);
    }

    $orders = array ();
    $sort_values = $this->_sort_values ();
    foreach ($this->_sorts as $sort)
    {
      $desc = $sort->description ($obj, $sort_values);
      if ($desc)
      {
        $orders [] = $desc;
      }
    }

    if (sizeof ($orders))
    {
      $Result .= "\nSorted by " . join (', ', $orders) . '</ul>';
    }
    else
    {
      $Result .= '</ul>';
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
    $old_width = $renderer->default_control_width;
    $renderer->default_control_width = '10em';

    foreach ($this->_dates as $date)
    {
      if (isset ($this->_linked_fields [$date->base_name]))
      {
        $user = $this->_users [$this->_linked_fields [$date->base_name]];
      }

      $layer = $this->context->make_layer ($date->title);
      $layer->visible = $date->needs_visible ($form) || (isset ($user) && $user->needs_visible ($form));

      $renderer->draw_text_row ($date->title, $layer->toggle_as_html () . ' Click the arrow to search by ' . $date->title . '.', 'notes');
      $renderer->start_row (' ');
        $layer->start ();
          $renderer->start_block ();

            if (isset ($user))
            {
              $user->draw_fields ($form, $renderer);
            }

            $date->draw_fields ($form, $renderer);

          $renderer->finish_block ();
        $layer->finish ();
      $renderer->finish_row ();
    }

    $renderer->default_control_width = $old_width;
  }

  /**
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_user_fields ($form, $renderer)
  {
    $old_width = $renderer->default_control_width;
    $renderer->default_control_width = '10em';

    foreach ($this->_users as $user)
    {
      if (! isset ($this->_linked_fields [$user->base_name]))
      {
        $renderer->start_row ($user->title);
          $renderer->start_block ();
            $user->draw_fields ($form, $renderer);
          $renderer->finish_block ();
        $renderer->finish_row ();
      }
    }

    $renderer->default_control_width = $old_width;
  }

  /**
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_sort_fields ($form, $renderer)
  {
    $sort_values = $this->_sort_values ();

    foreach ($this->_sorts as $sort)
    {
      $renderer->start_column ('Sort by');
      $sort->draw_fields ($form, $renderer, $sort_values);
    }

    $renderer->finish_column ();
  }

  /**
   * Add a set of date search fields.
   * @param string $base_name
   * @access private
   */
  protected function _add_date ($base_name, $title = '', $sortable = true)
  {
    $date = new SEARCH_DATE_FIELDS ($this->context, $base_name, $title, $sortable);
    $this->_sets [] = $date;
    $this->_dates [$base_name] = $date;
  }

  /**
   * Add a set of user search fields.
   * @param string $base_name
   * @access private
   */
  protected function _add_user ($base_name, $title = '', $sortable = true)
  {
    $user = new SEARCH_USER_FIELDS ($this->context, $base_name, $title, $sortable);
    $this->_sets [] = $user;
    $this->_users [$base_name] = $user;
  }

  /**
   * Add a set of text search fields.
   * @param string $base_name
   * @access private
   */
  protected function _add_text ($base_name, $title = '', $sortable = true, $selected_by_default = true, $table_name = '')
  {
    $text = new SEARCH_TEXT_FIELDS ($this->context, $base_name, $title, $sortable, $table_name);
    $text->selected_by_default = $selected_by_default;
    $this->_sets [] = $text;
    $this->_texts [$base_name] = $text ;
  }

  /**
   * Add a set of sort fields.
   * @param string $base_name
   * @access private
   */
  protected function _add_sort ($base_name)
  {
    $sort = new SORT_FIELDS ($this->context, $base_name, '', false);
    $this->_sets [] = $sort;
    $this->_sorts [$base_name] = $sort;
  }

  /**
   * Associate two fields so they are displayed together.
   * @param string $field1
   * @param string $field2
   * @access private
   */
  protected function _link_fields ($field1, $field2)
  {
    $this->_linked_fields [$field1] = $field2;
    $this->_linked_fields [$field2] = $field1;
  }

  /**
   * Keep this field synchronized with search data.
   * Some fields simply copy data in and out of the search data when loaded or stored. Register
   * those fields with this method to have that bookkeeping occur automatically.
   * @param string $name
   * @param string $default_value
   * @access private
   */
  protected function _add_synced_field ($name, $default_value)
  {
    $this->_synced_fields [$name] = $default_value;
  }

  /**
   * List of sortable values
   * @return array[string, string]
   */
  protected function _sort_values ()
  {
    $Result = array ();
    foreach ($this->_sets as $set)
    {
      $set->add_sort_fields ($Result);
    }
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
    $Result = array ();

    if ($obj->parameters ['search_text'])
    {
      $fields = array ();
      foreach ($this->_texts as $text)
      {
        $desc = $text->description ($obj);
        if ($desc)
        {
          $fields [] = $desc;
        }
      }

      if (sizeof ($fields))
      {
        $search_text = htmlspecialchars ($obj->parameters ['search_text']);
        if (sizeof ($fields) < sizeof ($this->_texts))
        {
          $Result [] = join (' or ', $fields) . ' contains "' . $search_text . '"';
        }
        else
        {
          $Result [] = 'Any text contains "' . $search_text . '"';
        }
      }
    }

    foreach ($this->_dates as $date)
    {
      $desc = $date->description ($obj);
      if ($desc)
      {
        $Result [] = $desc;
      }
    }

    foreach ($this->_users as $user)
    {
      $desc = $user->description ($obj);
      if ($desc)
      {
        $Result [] = $desc;
      }
    }

    return $Result;
  }

  /**
   * All registered date search field sets.
   * @see SEARCH_DATE_FIELDS
   * @var array[SEARCH_DATE_FIELDS]
   * @access private
   */
  protected $_dates;

  /**
   * All registered user search field sets.
   * @see SEARCH_USER_FIELDS
   * @var array[SEARCH_USER_FIELDS]
   * @access private
   */
  protected $_users;

  /**
   * All registered text search field sets.
   * @see SEARCH_TEXT_FIELDS
   * @var array[SEARCH_TEXT_FIELDS]
   * @access private
   */
  protected $_texts;

  /**
   * All registered sort field sets.
   * @see SORT_FIELDS
   * @var array[SORT_FIELDS]
   * @access private
   */
  protected $_sorts;

  /**
   * All registered search field sets.
   * @see SEARCH_FIELDS
   * @var array[SEARCH_FIELDS]
   * @access private
   */
  protected $_sets;

  /**
   * Indicates which fields should be drawn together.
   * @var array[string,string]
   * @access private
   */
  protected $_linked_fields;

  /**
   * Fields that are automatically synced with search data.
   * Loading and storing is handled automatically for these fields. The value part of the array holds
   * the fields default value.
   * @var array[string, mixed]
   * @access private
   */
  protected $_synced_fields;
}

/**
 * Create a filter for {@link AUDITABLE} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEARCH_AUDITABLE_FIELDS extends SEARCH_OBJECT_FIELDS
{
  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $this->_add_text ('title', 'Title', true);

    $this->_add_date ('time_created', 'Created');
    $this->_add_date ('time_modified', 'Modified');

    $this->_add_user ('creator_id', 'Creator');
    $this->_add_user ('modifier_id', 'Modifier');

    $this->_link_fields ('time_created', 'creator_id');
    $this->_link_fields ('time_modified', 'modifier_id');

    $this->_add_sort ('1');
    $this->_add_sort ('2');
    $this->_add_sort ('3');
  }
}

/**
 * Create a filter for {@link AUDITABLE} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEARCH_CONTENT_OBJECT_FIELDS extends SEARCH_AUDITABLE_FIELDS
{
  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $this->_add_text ('description', 'Description', false);
  }
}

/**
 * Create a filter for {@link OBJECT_IN_FOLDER} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEARCH_OBJECT_IN_FOLDER_FIELDS extends SEARCH_CONTENT_OBJECT_FIELDS
{
  /**
   * Used for some the folder selector.
   * @var FOLDER
   */
  public $folder_from_context;

  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $this->_add_synced_field ('state', Visible);
    $this->_add_synced_field ('not_state', false);
    $this->_add_synced_field ('folder_search_type', Search_user_constant);
    $this->_add_synced_field ('folder_ids', array ());
  }

  /**
   * Add fields for search properties to this form.
   * @param FORM $form
   * @param boolean $visible
   */
  public function add_fields ($form, $visible = true)
  {
    parent::add_fields ($form);

    $field = new BOOLEAN_FIELD ($form->app);
    $field->id = 'not_state';
    $field->title = 'Not';
    $field->visible = $visible;
    $form->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'state';
    $field->title = 'State';
    $field->add_value (0);
    $states = $this->_states ();
    foreach (array_keys ($states) as $state)
    {
      $field->add_value ($state);
    }
    $field->visible = $visible;
    $form->add_field ($field);

    $field = new ARRAY_FIELD ();
    $field->id = 'folder_ids';
    $field->title = 'Folders';
    $field->visible = $visible;
    $form->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'folder_search_type';
    $field->title = '';
    $field->add_value (Search_user_context_none);
    $field->add_value (Search_user_constant);
    $field->add_value (Search_user_not_constant);
    $field->visible = $visible;
    $form->add_field ($field);
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query
   * @param object $obj
   */
  public function apply_to_query ($query, $obj)
  {
    parent::apply_to_query ($query, $obj);

    if ($obj->parameters ['state'])
    {
      $state_restriction = $query->alias . '.state = ' . $obj->parameters ['state'];

      if ($obj->parameters ['not_state'])
      {
        $query->restrict ("NOT ($state_restriction)");
      }
      else
      {
        $query->restrict ($state_restriction);
      }
    }

    if ($obj->parameters ['folder_search_type'] != Search_user_context_none)
    {
      if (sizeof ($obj->parameters ['folder_ids']))
      {
        if ($obj->parameters ['folder_search_type'] == Search_user_constant)
        {
          $query->restrict_by_op ('fldr.id', $obj->parameters ['folder_ids'], Operator_in);
        }
        else
        {
          $query->restrict_by_op ('fldr.id', $obj->parameters ['folder_ids'], Operator_not_in);
        }
      }
    }
    else
    {
      if (isset ($this->folder_from_context))
      {
        $query->restrict ('fldr.id = ' . $this->folder_from_context->id);
      }
    }
  }

  /**
   * List of sortable values.
   * @return array[string, string]
   */
  protected function _sort_values ()
  {
    $Result = parent::_sort_values ();
    $Result ['state'] = 'State';
    $Result ['folder_id'] = 'Folder';
    return $Result;
  }

  /**
   * List of possible object states.
   * @return array[string]
   */
  protected function _states ()
  {
    return array (Visible => 'Visible',
                  Locked => 'Locked',
                  Hidden => 'Hidden',
                  Deleted => 'Deleted');
  }

  protected function _draw_state_selector ($form, $renderer)
  {
    $old_width = $renderer->default_control_width;
    $renderer->default_control_width = '10em';

    $renderer->start_row ('State');
      echo $renderer->check_box_as_html ('not_state');
      echo ' ';

      $props = $renderer->make_list_properties ();
      $props->add_item ('[all]', 0);
      $states = $this->_states ();
      foreach ($states as $state => $state_title)
      {
        $props->add_item ($state_title, $state);
      }

      echo $renderer->drop_down_as_html ('state', $props);
    $renderer->finish_row ();

    $renderer->default_control_width = $old_width;
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

    if ($obj->parameters ['folder_search_type'] != Search_user_context_none)
    {
      if ($obj->parameters ['folder_ids'])
      {
        $folder_query = $this->login->folder_query ();
        $folders = $folder_query->objects_at_ids ($obj->parameters ['folder_ids']);
        if (sizeof ($folders))
        {
          foreach ($folders as $folder)
          {
            $folder_names [] = $folder->title_as_plain_text ();
          }
          $folder_names = join (', ', $folder_names);
          if ($obj->parameters ['folder_search_type'] == Search_user_constant)
          {
            $folder_text = 'Folder is ';
          }
          else
          {
            $folder_text = 'Folder is not ';
          }

          if (sizeof ($folder_names) == 1)
          {
            $Result [] = $folder_text . $folder_names;
          }
          else
          {
            $Result [] = $folder_text . 'one of ' . $folder_names;
          }
        }
      }
    }
    else
    {
      $Result [] = 'Folder matches folder from context';;
    }

    if ($obj->parameters ['state'])
    {
      $state_text = $this->_state_as_text ($obj);

      if ($obj->parameters ['not_state'])
      {
        $Result [] = 'State is not ' . $state_text;
      }
      else
      {
        $Result [] = 'State is ' . $state_text;
      }
    }

    return $Result;
  }

  /**
   * Return string for the selected state.
   * @param object $obj
   * @return string
   * @access private
   */
  protected function _state_as_text ($obj)
  {
    $states = $this->_states ();
    return $states [$obj->parameters ['state']];
  }

  /**
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_folder_selector ($form, $renderer)
  {
    $old_width = $renderer->default_control_width;
    $renderer->default_control_width = '10em';

    $props = $renderer->make_list_properties ();
    $props->add_item ('Context or none', Search_user_context_none);
    $props->add_item ('Selected folder(s)', Search_user_constant);
    $props->add_item ('NOT selected folder(s)', Search_user_not_constant);

    $id_values = $form->value_for ('folder_ids');
    $selected_folder_ids = array ();

    if (sizeof ($id_values))
    {
      foreach ($id_values as $id)
      {
        if ($id)
        {
          $selected_folder_ids [$id] = $id;
        }
      }
    }

    $layer = $this->context->make_layer ('folders');
    $layer->visible = ($form->value_for ('folder_search_type') != Search_user_context_none
                       || (sizeof ($selected_folder_ids) > 0));

    $renderer->draw_text_row ('Folder(s)', $layer->toggle_as_html () . ' Click the arrow to search by folder.', 'notes');

    $renderer->start_row (' ');
      $layer->start ();
        $renderer->start_block ();

          $renderer->start_row (' ');
            echo $renderer->drop_down_as_html ('folder_search_type', $props);
            echo ' ';
          $renderer->finish_row ();
          $renderer->start_row (' ');

          $folder_query = $this->login->folder_query ();
          $folder_query->clear_results ();
          $folders = $folder_query->root_tree ($this->app->root_folder_id);
          $selected_folders = $folder_query->objects_at_ids ($selected_folder_ids);

          /* Make a copy (not a reference). */
          $tree = $this->app->make_tree_renderer ();

          include_once ('webcore/gui/folder_tree_node_info.php');
          $tree_node_info = new FOLDER_TREE_NODE_INFO ($this->app);

          include_once ('webcore/gui/selector_tree_decorator.php');
          $decorator = new MULTI_SELECTOR_TREE_DECORATOR ($tree, $selected_folder_ids);
          $decorator->control_name = 'folder_ids';
          $decorator->form_name = $form->name;
          $decorator->auto_toggle_children = true;

          $tree->node_info = $tree_node_info;
          $tree->decorator = $decorator;
          $tree->set_visible_nodes ($selected_folders);
          $tree->centered = false;

          $tree->display ($folders);

          $renderer->finish_row ();

        $renderer->finish_block ();
      $layer->finish ();
    $renderer->finish_row ();

    $renderer->default_control_width = $old_width;
  }

  /**
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_date_fields ($form, $renderer)
  {
    $this->_draw_state_selector ($form, $renderer);
    $renderer->draw_separator ();
    $this->_draw_folder_selector ($form, $renderer);
    parent::_draw_date_fields ($form, $renderer);
  }
}

/**
 * Create a filter for {@link DRAFTABLE} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEARCH_ENTRY_FIELDS extends SEARCH_OBJECT_IN_FOLDER_FIELDS
{
}

/**
 * Create a filter for {@link DRAFTABLE} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEARCH_DRAFTABLE_FIELDS extends SEARCH_ENTRY_FIELDS
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->_add_date ('time_published', 'Published');
    $this->_add_user ('publisher_id', 'Publisher');
    $this->_link_fields ('time_published', 'publisher_id');
  }

  /**
   * List of possible object states.
   * @return array[string]
   */
  protected function _states ()
  {
    $Result = parent::_states ();
    $Result [Draft] = 'Draft';
    $Result [Queued] = 'Queued';
    $Result [Abandoned] = 'Abandoned';
    return $Result;
  }
}

/**
 * Create a filter for {@link USER}s.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEARCH_USER_OBJECT_FIELDS extends SEARCH_CONTENT_OBJECT_FIELDS
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->_add_text ('real_first_name', 'First name');
    $this->_add_text ('real_last_name', 'Last name');
    $this->_add_text ('home_page_url', 'Home page', false, false);
    $this->_add_text ('email', 'Email', false, false);
    $this->_add_text ('picture_url', 'Picture', false, false);
    $this->_add_text ('signature', 'Signature', false, false);

    $this->_add_synced_field ('user_kind', Privilege_kind_registered);
  }

  /**
   * Add fields for search properties to this form.
   * @param FORM $form
   */
  public function add_fields ($form)
  {
    parent::add_fields ($form);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'user_kind';
    $field->title = 'Kind';
    $field->description = 'Restrict to certain types of users.';
    $field->add_value ('all');
    $field->add_value (Privilege_kind_anonymous);
    $field->add_value (Privilege_kind_registered);
    $form->add_field ($field);
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query
   * @param object $obj
   */
  public function apply_to_query ($query, $obj)
  {
    parent::apply_to_query ($query, $obj);

    if ($obj->parameters ['user_kind'])
    {
      $query->restrict ($query->alias . '.kind = \'' . $obj->parameters ['user_kind'] . '\'');
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

    if ($obj->parameters ['user_kind'])
    {
      $Result [] = 'Kind is ' . $obj->parameters ['user_kind'];
    }

    return $Result;
  }

  protected function _draw_kind_selector ($form, $renderer)
  {
    $old_width = $renderer->default_control_width;
    $renderer->default_control_width = '10em';

    $layer = $this->context->make_layer ('user_kind');
    $layer->visible = $form->value_for ('user_kind') != Privilege_kind_registered;

    $renderer->draw_text_row ('Advanced', $layer->toggle_as_html () . ' Click the arrow for advanced search options.', 'notes');

    $renderer->start_row (' ');
      $layer->start ();
        $renderer->start_block ();

          $props = $renderer->make_list_properties ();
          $props->show_description_on_same_line = true;
          $props->add_item ('[all]', 'all');
          $props->add_item ('Anonymous', Privilege_kind_anonymous);
          $props->add_item ('Registered', Privilege_kind_registered);
          $renderer->draw_drop_down_row ('user_kind', $props);

        $renderer->finish_block ();
      $layer->finish ();
    $renderer->finish_row ();

    $renderer->default_control_width = $old_width;
  }

  /**
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_date_fields ($form, $renderer)
  {
    $this->_draw_kind_selector ($form, $renderer);
    parent::_draw_date_fields ($form, $renderer);
  }
}

/**
 * Create a filter for {@link USER}s.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.5.0
 */
class SEARCH_FOLDER_FIELDS extends SEARCH_OBJECT_IN_FOLDER_FIELDS
{
  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $this->_add_text ('summary', 'Summary');
  }
}

?>
