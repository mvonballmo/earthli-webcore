<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
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
require_once ('webcore/forms/form.php');
require_once ('webcore/gui/layer.php');
require_once ('webcore/obj/search.php');

/**
 * A set of fields used by search forms.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
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
   * @param string $base_name The name of the field in the form and object.
   * @param string $title The title to show for the field; can be empty.
   * @param bool $sortable If true, the field is added to the list of available sorts.
   * @param string $table_name
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
   * Add fields for search properties to this form.
   * @param FORM $form
   * @abstract
   */
  public abstract function add_fields ($form);

  /**
   * Add sortable values to the array.
   * @param string[] $values
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
   * @param stdClass $obj
   * @abstract
   */
  public abstract function load_from_object ($form, $obj);

  /**
   * Make sure data is correct.
   * @param FORM $form
   * @param stdClass $obj
   */
  public function validate ($form, $obj) {}

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param stdClass $obj
   * @abstract
   */
  public abstract function store_to_object ($form, $obj);

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
 * @version 3.6.0
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
   * @param $obj
   * @throws UNKNOWN_VALUE_EXCEPTION
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
      /** @var DATE_TIME $date_before */
      $date_before = $obj->parameters [$this->before_name ()];
      /** @var DATE_TIME $date_after */
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
    $field->caption = 'on';
    $field->add_value (Search_date_today);
    $field->add_value (Search_date_this_week);
    $field->add_value (Search_date_this_month);
    $field->add_value (Search_date_constant);
    $form->add_field ($field);

    $field = new DATE_TIME_FIELD ();
    $field->id = $this->after_name ();
    $field->caption = 'after';
    $form->add_field ($field);

    $field = new DATE_TIME_FIELD ();
    $field->id = $this->before_name ();
    $field->caption = 'before';
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
   * @param stdClass $obj
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
   * @param stdClass $obj
   */
  public function validate ($form, $obj)
  {
    /** @var $after DATE_TIME */
    $after = $form->value_for ($this->after_name ());
    /** @var $before DATE_TIME */
    $before = $form->value_for ($this->before_name ());

    if ($before->is_valid () && $after->is_valid () && $before->less_than ($after))
    {
      $form->record_error ($this->base_name, "Make sure 'before' is before 'after'");
    }
  }

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param stdClass $obj
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
   * @param stdClass $obj The object from which to extract parameters.
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
    /** @var $date_before DATE_TIME */
    $date_before = $form->value_for ($this->before_name ());
    /** @var $date_after DATE_TIME */
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
    $props->css_class = 'small-medium';
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
 * @version 3.6.0
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
   * @param stdClass $obj
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
      return '';
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
    $field->caption = '';
    $field->add_value (Search_user_context_none);
    $field->add_value (Search_user_context_login);
    $field->add_value (Search_user_constant);
    $form->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = $this->base_name;
    $field->caption = 'by';
    $field->tag_validator_type = Tag_validator_none;
    $form->add_field ($field);

    $field = new TEXT_FIELD ();
    $field->id = $this->ids_name ();
    $field->caption = $this->ids_name ();
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
   * @param stdClass $obj
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
   * @param stdClass $obj
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
   * @param stdClass $obj
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
   * @param stdClass $obj
   * @param USER $user
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
    $props->css_class = 'small';
    $props->add_item ('Context or none', Search_user_context_none);
    $props->add_item ('Context or login', Search_user_context_login);
    $props->add_item ('Name(s) listed', Search_user_constant);

    $text_props = new FORM_TEXT_CONTROL_OPTIONS();
    $text_props->css_class = 'medium';

    $renderer->start_row ('by', 'text-line');
      echo $renderer->drop_down_as_html ($this->search_type_name (), $props);
      echo ' ';
      echo $renderer->text_line_as_html ($this->base_name, $text_props);
    $renderer->finish_row ();

    $renderer->draw_text_row (' ', 'Separate multiple names with a semi-colon.', 'notes');
    $renderer->draw_error_row ($this->base_name, ' ');
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
 * @version 3.6.0
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
   * @param $obj
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
    $field->caption = $this->title;
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
   * @param stdClass $obj
   */
  public function load_from_object ($form, $obj)
  {
    $form->set_value ($this->base_name, $obj->parameters [$this->base_name]);
  }

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param stdClass $obj
   */
  public function store_to_object ($form, $obj)
  {
    $obj->parameters [$this->base_name] = $form->value_for ($this->base_name);
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query
   * @param stdClass $obj
   * @param string[]
   */
  public function apply_to_query ($query, $obj, &$fields)
  {

  }

  /**
   * Get the fully qualified field for this text search.
   * @param QUERY $query
   * @param stdClass $obj
   * @return string
   */
  public function get_search_field($query, $obj)
  {
    if ($obj->parameters [$this->base_name])
    {
      return $this->full_name ($query, $this->base_name);
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
 * @version 3.6.0
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
   * @param stdClass $obj
   * @param string[] $sort_values
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
    $field->caption = '';
    $field->tag_validator_type = Tag_validator_none;
    $form->add_field ($field);

    $field = new ENUMERATED_FIELD ($form->app);
    $field->id = $this->direction_name ();
    $field->caption = '';
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
   * @param stdClass $obj
   */
  public function load_from_object ($form, $obj)
  {
    $form->set_value ($this->sort_name (), $obj->parameters [$this->sort_name ()]);
    $form->set_value ($this->direction_name (), $obj->parameters [$this->direction_name ()] . $this->base_name);
  }

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param stdClass $obj
   */
  public function store_to_object ($form, $obj)
  {
    $obj->parameters [$this->sort_name ()] = $form->value_for ($this->sort_name ());
    $obj->parameters [$this->direction_name ()] = $form->value_for ($this->direction_name ());
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query The query to which to apply parameters.
   * @param stdClass $obj The object from which to extract parameters.
   * @return string
   */
  public function get_search_field ($query, $obj)
  {
    $sort = $obj->parameters [$this->sort_name ()];
    if ($sort)
    {
      $dir = $obj->parameters [$this->direction_name ()];
      return $this->full_name ($query, $sort) . ' ' . $dir;
    }
  }

  /**
   * Store properties from form into object.
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @param string[] $sort_values
   * @param integer $sort_index
   */
  public function draw_fields ($form, $renderer, $sort_values, $sort_index)
  {
    $props = $renderer->make_list_properties ();
    $props->add_item ('[Default]', '');
    $props->css_class = 'small';
    foreach ($sort_values as $key => $value)
    {
      $props->add_item ($value, $key);
    }

    if ($sort_index == 0)
    {
      $renderer->start_row('Sort by', 'text-line');
    }
    else
    {
      $renderer->start_row('Then by', 'text-line');
    }

    echo $renderer->drop_down_as_html ($this->sort_name (), $props);
    $props = $renderer->make_list_properties ();
    $props->items_per_row = 2;
    $props->add_item ('Ascending', 'asc');
    $props->add_item ('Descending', 'desc');

    // TODO This part also needs to be able to render a control group without a surrounding form-row

    $props->css_class = 'small';
    echo $renderer->drop_down_as_html($this->direction_name (), $props);

    $renderer->finish_row();
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
 * @version 3.6.0
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
   * @param stdClass $obj
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
   * @param stdClass $obj
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
   * @param boolean $extra_visibility Default visibility for extra search fields.
   */
  public function add_fields ($form, $extra_visibility = true)
  {
    $field = new TEXT_FIELD ();
    $field->id = 'search_text';
    $field->caption = 'Search';
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
   * @param stdClass $obj
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
   * @param stdClass $obj
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
   * @param stdClass $obj
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
   * @param stdClass $obj
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
        $search_field = $text->get_search_field ($query, $obj);
        if (!empty($search_field))
        {
          $fields [] = $search_field;
        }
      }

      if (sizeof ($fields))
      {
        $query->add_search ($obj->parameters ['search_text'], $fields);
      }
    }

    $orders = array ();
    foreach ($this->_sorts as $sort)
    {
      $order = $sort->get_search_field ($query, $obj);
      if (!empty($order))
      {
        $orders [] = $order;
      }
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
      $layer = $renderer->start_layer_row ('advanced-search-settings', 'Advanced', 'Toggle advanced settings');
    }

    $props = $renderer->make_list_properties ();

    foreach (array_keys ($this->_texts) as $id)
    {
      $props->add_item ($id, 1);
    }

    $renderer->draw_check_boxes_row ('In', $props);

    $this->_draw_user_fields ($form, $renderer);
    $this->_draw_date_fields ($form, $renderer);

    $this->_draw_sort_fields ($form, $renderer);

    if (isset ($layer))
    {
      $renderer->finish_layer_row ($layer);
    }
  }

  /**
   * Return text describing this set of search fields.
   * @param stdClass $obj
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
    foreach ($this->_dates as $date)
    {
      if (isset ($this->_linked_fields [$date->base_name]))
      {
        $user = $this->_users [$this->_linked_fields [$date->base_name]];
      }

      $visible = $date->needs_visible ($form) || (isset ($user) && $user->needs_visible ($form));
      $layer = $renderer->start_layer_row($date->base_name, $date->title, 'Toggle ' . $date->title . ' options.', $visible);

      if (isset ($user))
      {
        $user->draw_fields ($form, $renderer);
      }

      $date->draw_fields ($form, $renderer);

      $renderer->finish_layer_row($layer);
    }
  }

  /**
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_user_fields ($form, $renderer)
  {
    foreach ($this->_users as $user)
    {
      if (! isset ($this->_linked_fields [$user->base_name]))
      {
        $renderer->start_row ($user->title);
          $user->draw_fields ($form, $renderer);
        $renderer->finish_row ();
      }
    }
  }

  /**
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_sort_fields ($form, $renderer)
  {
    $sort_values = $this->_sort_values ();

    $layer = $renderer->start_layer_row('sorts', 'Sorting', 'Toggle sorting options.', false);

    $sort_index = 0;
    foreach ($this->_sorts as $sort)
    {
      $sort->draw_fields ($form, $renderer, $sort_values, $sort_index);
      $sort_index += 1;
    }

    $renderer->finish_layer_row($layer);
  }

  /**
   * Add a set of date search fields.
   * @param string $base_name The name of the field in the form and object.
   * @param string $title The title to show for the field; can be empty.
   * @param bool $sortable If true, the field is added to the list of available sorts.
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
   * @param string $base_name The name of the field in the form and object.
   * @param string $title The title to show for the field; can be empty.
   * @param bool $sortable If true, the field is added to the list of available sorts.
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
   * @param string $base_name The name of the field in the form and object.
   * @param string $title The title to show for the field; can be empty.
   * @param bool $sortable If true, the field is added to the list of available sorts.
   * @param bool $selected_by_default If true, the text field is included in the default search.
   * @param string $table_name The name of the table to search for this text.
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
   * @return string[]
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
   * @param stdClass $obj
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
   * @var SEARCH_DATE_FIELDS[]
   * @access private
   */
  protected $_dates;

  /**
   * All registered user search field sets.
   * @see SEARCH_USER_FIELDS
   * @var SEARCH_USER_FIELDS[]
   * @access private
   */
  protected $_users;

  /**
   * All registered text search field sets.
   * @see SEARCH_TEXT_FIELDS
   * @var SEARCH_TEXT_FIELDS[]
   * @access private
   */
  protected $_texts;

  /**
   * All registered sort field sets.
   * @see SORT_FIELDS
   * @var SORT_FIELDS[]
   * @access private
   */
  protected $_sorts;

  /**
   * All registered search field sets.
   * @see SEARCH_FIELDS
   * @var SEARCH_FIELDS[]
   * @access private
   */
  protected $_sets;

  /**
   * Indicates which fields should be drawn together.
   * @var string[]
   * @access private
   */
  protected $_linked_fields;

  /**
   * Fields that are automatically synced with search data.
   * Loading and storing is handled automatically for these fields. The value part of the array holds
   * the fields default value.
   * @var object[]
   * @access private
   */
  protected $_synced_fields;
}

/**
 * Create a filter for {@link AUDITABLE} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
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
 * @version 3.6.0
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
 * @version 3.6.0
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
   * @param boolean $extra_visibility Default visibility for extra search fields.
   */
  public function add_fields ($form, $extra_visibility = true)
  {
    parent::add_fields ($form);

    $field = new BOOLEAN_FIELD ($form->app);
    $field->id = 'not_state';
    $field->caption = 'Not';
    $field->visible = $extra_visibility;
    $form->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'state';
    $field->caption = 'State';
    $field->add_value (0);
    $states = $this->_states ();
    foreach (array_keys ($states) as $state)
    {
      $field->add_value ($state);
    }
    $field->visible = $extra_visibility;
    $form->add_field ($field);

    $field = new ARRAY_FIELD ();
    $field->id = 'folder_ids';
    $field->caption = 'Folders';
    $field->visible = $extra_visibility;
    $form->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'folder_search_type';
    $field->caption = '';
    $field->add_value (Search_user_context_none);
    $field->add_value (Search_user_constant);
    $field->add_value (Search_user_not_constant);
    $field->visible = $extra_visibility;
    $form->add_field ($field);
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query
   * @param stdClass $obj
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
   * @return string[]
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
   * @return string[]
   */
  protected function _states ()
  {
    return array (Visible => 'Visible',
                  Locked => 'Locked',
                  Hidden => 'Hidden',
                  Deleted => 'Deleted');
  }

  /**
   * @param $form FORM
   * @param $renderer FORM_RENDERER
   */
  protected function _draw_state_selector ($form, $renderer)
  {
    // TODO Make this emit a checkbox that is NOT wrapped in a form-row

    echo $renderer->check_box_as_html ('not_state');

    $renderer->start_row ('State', 'text-line');
      $props = $renderer->make_list_properties ();
      $props->add_item ('[all]', 0);
      $states = $this->_states ();
      foreach ($states as $state => $state_title)
      {
        $props->add_item ($state_title, $state);
      }

      $props->css_class = 'small';

      echo $renderer->drop_down_as_html ('state', $props);
    $renderer->finish_row ();
  }

  /**
   * Text representation of applied search fields.
   * @param stdClass $obj
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
        /** @var $folders FOLDER[] */
        $folders = $folder_query->objects_at_ids ($obj->parameters ['folder_ids']);
        if (sizeof ($folders))
        {
          $folder_names = array();
          foreach ($folders as $folder)
          {
            $folder_names [] = $folder->title_as_plain_text ();
          }

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
            $Result [] = $folder_text . $folder_names[0];
          }
          else
          {
            $Result [] = $folder_text . 'one of ' . join (', ', $folder_names);
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
   * @param stdClass $obj
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
    $props = $renderer->make_list_properties ();
    $props->add_item ('Context or none', Search_user_context_none);
    $props->add_item ('Selected folder(s)', Search_user_constant);
    $props->add_item ('NOT selected folder(s)', Search_user_not_constant);

    /** @var $id_values int[] */
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

    $visible = ($form->value_for ('folder_search_type') != Search_user_context_none
      || (sizeof ($selected_folder_ids) > 0));

    $layer = $renderer->start_layer_row('folders', 'Folder(s)', 'Toggle folder options.', $visible);

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

    $tree->display ($folders);

    $renderer->finish_row ();

    $renderer->finish_layer_row($layer);
  }

  /**
   * @param FORM $form
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_date_fields ($form, $renderer)
  {
    $this->_draw_state_selector ($form, $renderer);
    $this->_draw_folder_selector ($form, $renderer);
    parent::_draw_date_fields ($form, $renderer);
  }
}

/**
 * Create a filter for {@link DRAFTABLE} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.5.0
 */
class SEARCH_ENTRY_FIELDS extends SEARCH_OBJECT_IN_FOLDER_FIELDS
{
}

/**
 * Create a filter for {@link DRAFTABLE} objects.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.5.0
 */
class SEARCH_DRAFTABLE_FIELDS extends SEARCH_ENTRY_FIELDS
{
  /**
   * @param APPLICATION $context Main application.
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $this->_add_date ('time_published', 'Published');
    $this->_add_user ('publisher_id', 'Publisher');
    $this->_link_fields ('time_published', 'publisher_id');
  }

  /**
   * List of possible object states.
   * @return string[]
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
 * @version 3.6.0
 * @since 2.5.0
 */
class SEARCH_USER_OBJECT_FIELDS extends SEARCH_CONTENT_OBJECT_FIELDS
{
  /**
   * @param APPLICATION $context Main application.
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

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
   * @param boolean $extra_visibility Default visibility for extra search fields.
   */
  public function add_fields ($form, $extra_visibility = true)
  {
    parent::add_fields ($form);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'user_kind';
    $field->caption = 'Kind';
    $field->description = 'Restrict to certain types of users.';
    $field->add_value ('all');
    $field->add_value (Privilege_kind_anonymous);
    $field->add_value (Privilege_kind_registered);
    $form->add_field ($field);
  }

  /**
   * Restrict the query by these fields.
   * @param QUERY $query
   * @param stdClass $obj
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
   * @param stdClass $obj
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

  /**
   * @param $form FORM
   * @param $renderer FORM_RENDERER
   */
  protected function _draw_kind_selector ($form, $renderer)
  {
    $props = $renderer->make_list_properties ();
    $props->show_description_on_same_line = true;
    $props->css_class = 'medium';
    $props->add_item ('[all]', 'all');
    $props->add_item ('Anonymous', Privilege_kind_anonymous);
    $props->add_item ('Registered', Privilege_kind_registered);
    $renderer->draw_drop_down_row ('user_kind', $props);
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
 * @version 3.6.0
 * @since 2.5.0
 */
class SEARCH_FOLDER_FIELDS extends SEARCH_OBJECT_IN_FOLDER_FIELDS
{
  /**
   * @param APPLICATION $context Main application.
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $this->_add_text ('summary', 'Summary');
  }
}