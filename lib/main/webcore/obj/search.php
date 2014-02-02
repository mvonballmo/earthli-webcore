<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
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

/**
 * Search using user from context.
 * If the search is applied to a query based on a user, apply that user to this search property.
 * If there is no user in the context, omit this restriction entirely.
 */
define ('Search_user_context_none', 'context_none');
/**
 * Search using user from context.
 * If the search is applied to a query based on a user, apply that user to this search property.
 * If there is no user in the context, use the login user.
 */
define ('Search_user_context_login', 'context_login');
/**
 * Search using given object.
 * Use the object(s) given for this field when searching.
 */
define ('Search_user_constant', 'constant');
/**
 * Search everything except this object.
 * Use everything but the object(s) given for this field when searching.
 */
define ('Search_user_not_constant', 'not_constant');

/**
 * Match only current day.
 */
define ('Search_date_today', 'today');
/**
 * Match last 7 days.
 */
define ('Search_date_this_week', 'last_week');
/**
 * Match last 30 days
 */
define ('Search_date_this_month', 'last_month');
/**
 * Match given date bounds. 
 */
define ('Search_date_constant', 'constant');

/** */
require_once ('webcore/obj/content_object.php');

/**
 * A filter for objects in the WebCore.
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
 * @abstract
 */
abstract class SEARCH extends CONTENT_OBJECT
{
  /**
   * @var string
   */
  public $title;
  
  /**
   * @var string
   */
  public $type;
  
  /**
   * @var object
   */
  public $parameters;
  
  /**
   * @var string
   */
  public $description;

  /**
   * @var SEARCH_OBJECT_FIELDS
   */
  public $fields;

  /**
   * @param APPLICATION $app Main application.
   * @param SEARCH_OBJECT_FIELDS $fields
   */
  public function __construct ($app, $fields)
  {
    parent::__construct ($app);
    $this->fields = $fields;
  }

  /**
   * @param USER $user
   */
  public function set_user_from_context ($user)
  {
    $this->fields->user_from_context = $user;
  }

  /**
   * Description transformed into HTML.
   * If no specific munger is provided, the one from {@link html_formatter()} is used.
   * @param HTML_MUNGER $munger
   * @return string
   */
  public function description_as_html ($munger = null)
  {
    return $this->_text_as_html ($this->description, $munger);
  }

  /**
   * Description transformed into formatted plain text.
   * If no specific munger is provided, the one from {@link plain_text_formatter()} is used.
   * @param PLAIN_TEXT_MUNGER $munger
   * @return string
   */
  public function description_as_plain_text ($munger = null)
  {
    return $this->_text_as_plain_text ($this->description, $munger);
  }

  /**
   * Description of search parameters as HTML.
   * @return string
   */
  public function system_description_as_html ()
  {
    return $this->fields->description_as_html ($this);
  }

  /**
   * Description of search parameters as plain text.
   * @return string
   */
  public function system_description_as_plain_text ()
  {
    return $this->fields->description_as_plain_text ($this);
  }

  /**
   * Return a query restricted by this search.
   * The search examines its parameters and generates a query with all restrictions applied.
   * @return QUERY
   */
  public function prepared_query ()
  {
    $Result = $this->_base_query ();
    $this->_apply_to_query ($Result);
    return $Result;
  }

  /**
   * A grid to display the results of the search.
   * @return GRID
   * @abstract
   */
  public abstract function grid ();

  /**
   * The text that was searched.
   * The grids use this value to highlight the searched words in their text.
   * @return string
   */
  public function search_text ()
  {
    return $this->parameters ['search_text'];
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->title = $db->f ('title');
    $this->type = $db->f ('type');
    $this->parameters = unserialize ($db->f ('parameters'));
    $this->description = $db->f ('description');
    $this->folder_based = $db->f ('folder_based');
    $this->user_based = $db->f ('user_based');
    $this->user_id = $db->f ('user_id');
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname = $this->table_name ();
    $storage->add ($tname, 'user_id', Field_type_integer, $this->user_id, Storage_action_create);
    $storage->add ($tname, 'type', Field_type_string, $this->type, Storage_action_create);

    $storage->add ($tname, 'title', Field_type_string, $this->title);
    $storage->add ($tname, 'parameters', Field_type_string, serialize ($this->parameters));
    $storage->add ($tname, 'description', Field_type_string, $this->description);
    $storage->add ($tname, 'folder_based', Field_type_boolean, $this->folder_based);
    $storage->add ($tname, 'user_based', Field_type_boolean, $this->user_based);
  }

  /**
   * @return string
   */
  public function raw_title ()
  {
    return $this->title;
  }

  /**
   * Restrict the query for these search parameters.
   * @param QUERY $query
   * @access private
   */
  protected function _apply_to_query ($query)
  {
    $this->fields->apply_to_query ($query, $this);
  }

  /**
   * Search parameters are applied to this query.
   * @return QUERY
   * @access private
   * @abstract
   */
  protected abstract function _base_query ();

  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->searches;
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->search_home;
  }
  
  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
   * @return object
   * @access private
   */
  protected function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('webcore/gui/search_renderer.php');
        return new SEARCH_RENDERER ($this->app, $options);
      case Handler_commands:
        include_once ('webcore/cmd/search_commands.php');
        return new SEARCH_COMMANDS ($this);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }
}

/**
 * A filter for {@link OBJECT_IN_FOLDER} objects.
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
 */
abstract class OBJECT_IN_FOLDER_SEARCH extends SEARCH
{
  /**
   * @param USER $user
   */
  public function set_user_from_context ($user)
  {
    $this->fields->user_from_context = $user;
  }

  /**
   * @param FOLDER $folder
   */
  public function set_folder_from_context ($folder)
  {
    $this->fields->folder_from_context = $folder;
  }
}

/**
 * A filter for {@link ENTRY} objects.
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
 */
class ENTRY_SEARCH extends OBJECT_IN_FOLDER_SEARCH
{
  /**
   * Search parameters are applied to this query.
   * @return QUERY
   * @access private
   * @abstract
   */
  protected function _base_query ()
  {
    if (($this->parameters ['folder_search_type'] == Search_user_context_none) && (isset($this->fields->folder_from_context)))
    {
      return $this->fields->folder_from_context->entry_query ();
    }

    return $this->login->all_entry_query ();
  }

  /**
   * A grid to display the results of the search.
   * @return ENTRY_SUMMARY_GRID
   */
  public function grid ()
  {
    $class_name = $this->app->final_class_name ('ENTRY_SUMMARY_GRID', 'webcore/gui/entry_grid.php', $this->type);
    return new $class_name ($this->app);
  }
}

/**
 * A filter for {@link ENTRY} objects in a multi-entry application.
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.7.0
 */
class MULTI_ENTRY_SEARCH extends ENTRY_SEARCH
{
  /**
   * Search parameters are applied to this query.
   * @return QUERY
   * @access private
   */
  protected function _base_query ()
  {
    $Result = parent::_base_query ();
    $Result->set_type ($this->type);
    return $Result;
  }
}

/**
 * A filter for {@link COMMENT}s.
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
 */
class COMMENT_SEARCH extends OBJECT_IN_FOLDER_SEARCH
{
  /**
   * @var string
   */
  public $type = 'comment';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    $class_name = $app->final_class_name ('SEARCH_OBJECT_IN_FOLDER_FIELDS', 'webcore/forms/search_fields.php');
    parent::__construct ($app, new $class_name ($app));
  }

  /**
   * Search parameters are applied to this query.
   * @return QUERY
   * @access private
   * @abstract
   */
  protected function _base_query ()
  {
    if (($this->parameters ['folder_search_type'] == Search_user_context_none) && ($this->fields->folder_from_context))
    {
      return $this->fields->folder_from_context->comment_query ();
    }

    return $this->login->all_comment_query ();
  }

  /**
   * A grid to display the results of the search.
   * @return GRID
   * @abstract
   */
  public function grid ()
  {
    $class_name = $this->app->final_class_name ('SELECT_COMMENT_GRID', 'webcore/gui/comment_grid.php');
    return new $class_name ($this->app);
  }
}

/**
 * A filter for {@link GROUP}s.
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
 */
class GROUP_SEARCH extends SEARCH
{
  /**
   * @var string
   */
  public $type = 'group';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    $class_name = $app->final_class_name ('SEARCH_AUDITABLE_FIELDS', 'webcore/forms/search_fields.php');
    parent::__construct ($app, new $class_name ($app));
  }

  /**
   * Search parameters are applied to this query.
   * @return QUERY
   * @access private
   * @abstract
   */
  protected function _base_query ()
  {
    return $this->app->group_query ();
  }

  /**
   * A grid to display the results of the search.
   * @return GRID
   * @abstract
   */
  public function grid ()
  {
    $class_name = $this->app->final_class_name ('GROUP_GRID', 'webcore/gui/group_grid.php');
    return new $class_name ($this->app);
  }
}

/**
 * A filter for {@link USER}s.
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
 */
class USER_SEARCH extends SEARCH
{
  /**
   * @var string
   */
  public $type = 'user';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    $class_name = $app->final_class_name ('SEARCH_USER_OBJECT_FIELDS', 'webcore/forms/search_fields.php');
    parent::__construct ($app, new $class_name ($app));
  }

  /**
   * Search parameters are applied to this query.
   * @return QUERY
   * @access private
   * @abstract
   */
  protected function _base_query ()
  {
    return $this->app->user_query ();
  }

  /**
   * A grid to display the results of the search.
   * @return GRID
   * @abstract
   */
  public function grid ()
  {
    $class_name = $this->app->final_class_name ('SELECT_USER_GRID', 'webcore/gui/user_grid.php');
    return new $class_name ($this->app);
  }
}

/**
 * A filter for {@link FOLDER}s.
 * @package webcore
 * @subpackage obj
 * @version 3.4.0
 * @since 2.5.0
 */
class FOLDER_SEARCH extends OBJECT_IN_FOLDER_SEARCH
{
  /**
   * @var string
   */
  public $type = 'folder';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    $class_name = $app->final_class_name ('SEARCH_FOLDER_FIELDS', 'webcore/forms/search_fields.php');
    parent::__construct ($app, new $class_name ($app));
  }

  /**
   * Search parameters are applied to this query.
   * @return QUERY
   * @access private
   * @abstract
   */
  protected function _base_query ()
  {
    return $this->login->folder_query ();
  }

  /**
   * A grid to display the results of the search.
   * @return FOLDER_GRID
   */
  public function grid ()
  {
    $class_name = $this->app->final_class_name ('FOLDER_GRID', 'webcore/gui/folder_grid.php');
    $Result = new $class_name ($this->app);
//    $Result->set_folders ();
    return $Result;
  }
}

?>