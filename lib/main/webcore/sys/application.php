<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/sys/context.php');
require_once ('webcore/config/application_config.php');
require_once ('webcore/sys/webcore_type_infos.php');

/**
 * Used internally to register search types.
 * This string is prefixed to the search type and registered as a class name in
 * the application's class registry. See {@link APPLICATION::register_search()}
 * for more information.
 * @access private
 */
define ('App_search_reg_prefix', '__search_');

/**
 * Used internally to register entry types.
 * This string is prefixed to the entry type and registered as a class name in
 * the application's class registry. See {@link
 * APPLICATION::register_entry_type()} for more information.
 * @access private
 */
define ('App_entry_reg_prefix', '__entry_');

/**
 * Encapsulates a specific application in a page.
 * There can be multiple applications per page.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.2.1
 */
class APPLICATION extends CONTEXT
{
  /**
   * Displayed in the {@link PAGE_TITLE::$group}.
   * @var string
   */
  var $title = 'WebCore Application';
  /**
   * Displayed in the banner by the {@link DEFAULT_PAGE_RENDERER}.
   * @var string
   */
  var $short_title = 'WebCore';
  /**
   * Unique ID for this framework.
   * Change only in descendents; used for versioning and migration.
   * @var string
   */
  var $framework_id = 'com.earthli.generic-app';
  /**
   * @var integer
   */
  var $version = '1.0';
  /**
   * @var string
   */
  var $icon = '{icons}products/webcore';
  /**
   * Home page for the application.
   * Used to format the application info for support requests.
   * @var string
   */
  var $support_url = 'http://earthli.com/software/webcore/';
  /**
   * Id of the folder that serves as the tree root (can be empty)
   * @var integer
   */
  var $root_folder_id = 1;
  /**
   * Is this application online?
   * Call {@link set_offline()} to change this value.
   * @var boolean
   */
  var $offline = FALSE;
  /**
   * Text to display as having been searched.
   * @var string
   */
  var $search_text = '';

  /**
   * Page in which this application is anchored.
   * @var PAGE
   */
  var $page;
  /**
   * The logged-in user (can be anonymous)
   * @var USER
   */
  var $login;

  /**
   * Map the physical table names to the aliases used by the WebCore.
    * @var APPLICATION_TABLE_NAMES
    */
  var $table_names;
  /**
   * Map the physical page names to the aliases used by the WebCore.
    * @var APPLICATION_PAGE_NAMES
    */
  var $page_names;
  /**
   * Application-specific display settings.
    * @var APPLICATION_DISPLAY_OPTIONS
    */
  var $display_options;
  /**
   * Options related to users.
    * @var APPLICATION_USER_OPTIONS
    */
  var $user_options;
  /**
   * Additional mailing options (added to those declared in CONTEXT).
    * @var APPLICATION_MAIL_OPTIONS
    */
  var $mail_options;
  /**
   * Options used to build anonymous user names.
    * @var APPLICATION_ANON_OPTIONS
    */
  var $anon_options;
  /**
   * @var array[integer]
   */
  var $max_title_sizes;

  /**
   * @param PAGE &$page Page to which this application is attached.
   */
  function APPLICATION (&$page)
  {
    $this->page =& $page;
    $this->inherit_resources_from ($page);

    CONTEXT::CONTEXT ($page->env);

    $this->mail_options->copy_from ($page->mail_options);
    $this->display_options->copy_from ($page->display_options);
    $this->database_options->copy_from ($page->database_options);
    $this->upload_options->copy_from ($page->upload_options);

    $page->add_as_icon_listener_to ($this);
    $page->add_icon_alias ($this, Folder_name_app_icons);

    $this->set_path (Folder_name_application, '/');
    $this->set_path (Folder_name_attachments, '{' . Folder_name_data . '}attachments');
    $this->set_path (Folder_name_app_icons, '{' . Folder_name_application . '}icons');
    $this->set_path (Folder_name_app_styles, '{' . Folder_name_application . '}styles');
    $this->set_path (Folder_name_app_scripts, '{' . Folder_name_application . '}scripts');
    $this->set_extension (Folder_name_app_styles, 'css');
    $this->set_extension (Folder_name_app_scripts, 'js');

    $this->exception_handler =& $page->exception_handler;

    $class_name = $this->final_class_name ('APPLICATION_TABLE_NAMES');
    $this->table_names = new $class_name ();
    $class_name = $this->final_class_name ('APPLICATION_PAGE_NAMES');
    $this->page_names = new $class_name ();

    $this->anon_options = new APPLICATION_ANON_OPTIONS ();
    $this->user_options = new APPLICATION_USER_OPTIONS ();

    $this->max_title_sizes = array ();

    $this->_page_templates = array ();
  }

  /**
   * Add classes to the {@link $classes} object factory.
   * @access private
   */
  function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();

    $this->register_class ('CONTEXT_STORAGE_OPTIONS', 'APPLICATION_STORAGE_OPTIONS');
    $this->register_class ('CONTEXT_MAIL_OPTIONS', 'APPLICATION_MAIL_OPTIONS');
    $this->register_class ('CONTEXT_DISPLAY_OPTIONS', 'APPLICATION_DISPLAY_OPTIONS');

    $this->register_search ('comment', 'COMMENT', 'COMMENT_SEARCH', 'webcore/obj/search.php');
    $this->register_search ('user', 'USER', 'USER_SEARCH', 'webcore/obj/search.php');
  }

  /**
   * Name used for version information.
   * @return string
   */
  function name ()
  {
    return 'earthli Application';
  }

  /**
   * Name and version of the application.
   * @return string
   */
  function description ()
  {
    return $this->title . ' ' . $this->version;
  }

  /**
   * Full path to the install location of this application.
   * @return FILE_URL
   */
  function source_path ()
  {
    $Result = new FILE_URL (realpath ($this->_source_path ()));
    $Result->strip_name ();
    $Result->go_back ();
    return $Result;
  }

  /**
   * Should renderers/grids/etc. use DHTML?
   * @return boolean
   */
  function dhtml_allowed ()
  {
    return $this->page->template_options->include_scripts && parent::dhtml_allowed ();
  }

  /**
   * Should dates use JavaScript to render local times?
   * @return boolean
   */
  function local_times_allowed ()
  {
    return $this->page->template_options->include_scripts && parent::local_times_allowed ();
  }

  /**
   * Query that can see all groups in the application
   * @return GROUP_QUERY
   */
  function group_query ()
  {
    return $this->make_object ('group_query', 'GROUP_QUERY', 'webcore/db/group_query.php');
  }

  /**
   * Query that can see all users in the application
   * @return USER_QUERY
   */
  function user_query ()
  {
    return $this->make_object ('user_query', 'USER_QUERY', 'webcore/db/user_query.php');
  }

  /**
   * Query that can see all users in the application
   * @return SUBSCRIBER_QUERY
   */
  function subscriber_query ()
  {
    return $this->make_object ('subscriber_query', 'SUBSCRIBER_QUERY', 'webcore/db/subscriber_query.php');
  }

  /**
   * All icons available to this application.
   * @return ICON_QUERY
   */
  function icon_query ()
  {
    return $this->make_object ('icon_query', 'ICON_QUERY', 'webcore/db/icon_query.php');
  }

  /**
   * All themes available to this application.
   * @return THEME_QUERY
   */
  function theme_query ()
  {
    return $this->make_object ('theme_query', 'APPLICATION_THEME_QUERY', 'webcore/db/theme_query.php');
  }

  /**
   * Make a new user of the requested type.
   * Should be overridden to return an application-specific user object.
   * Handles setup for anonymous users by setting the ip address.
   * Does not store to the database.
   * @param string $kind
   * @return USER
   */
  function new_user ($kind = Privilege_kind_registered)
  {
    $Result = $this->_make_user ();
    $Result->kind = $kind;
    $Result->use_default_permissions ();

    /* Record the IP address used to create this user. Useful for banning a
     * troublesome registered user.
     */
    $browser = $this->env->browser ();
    $Result->ip_address = $browser->ip_address ();

    if ($kind == Privilege_kind_anonymous)
      $Result->title = $this->_make_anon_name ();

    return $Result;
  }

  /**
   * Make a new subscriber.
   * Can be overridden to return an application-specific user object.
   * Does not store to the database.
   * @return SUBSCRIBER
   */
  function new_subscriber ()
  {
    $class_name = $this->final_class_name ('SUBSCRIBER', 'webcore/obj/subscriber.php');
    return new $class_name ($this);
  }

  /**
   * Make a new group object.
   * Does not store to database.
   * @return GROUP
   */
  function new_group ()
  {
    $class_name = $this->final_class_name ('GROUP', 'webcore/obj/group.php');
    return new $class_name ($this);
  }

  /**
   * Create a new folder object.
    * Does not store to the database.
    * @return FOLDER
    */
  function new_folder ()
  {
    $class_name = $this->final_class_name ('FOLDER', 'webcore/obj/folder.php');
    $Result = new $class_name ($this);
    $Result->root_id = 0;
    return $Result;
  }

  /**
   * Run the application in offline mode.
   * Users without {@link Privilege_offline} will be automatically redirected to an offline page.
   */
  function set_offline ()
  {
    $this->offline = TRUE;
    if (isset ($this->login))
      $this->_enforce_offline ();
  }

  /**
   * Call this function before accessing the {@link $login} property.
   */
  function load_login ()
  {
    $this->set_login ($this->_logged_in_user ());

    if ($this->offline)
      $this->_enforce_offline ();
  }

  /**
   * Make sure there is a login available (anonymous or registered).
   * Call this function when about to update the database with information
   * that is recorded relative to a user. This will force an anonymous user
   * to be created if one doesn't already exist.
   * @see force_login_for_user_create()
   */
  function force_login ()
  {
    if ($this->login->id == Anon_user_id)
      $this->set_login ($this->_logged_in_user (TRUE));
  }

  /**
   * {@link force_login()} for creating users.
   * Normally, we force a login to create an anonymous user. However, when that
   * user is created, it also needs to force a login in order to get the creator
   * id for that user. This loops endlessly, so when a user is created, we
   * handle the need for a forced login specially, using {@link
   * APPLICATION_USER_OPTIONS::$default_user_creator_id}.
   * @see force_login()
   */
  function force_login_for_user_create ()
  {
    if ($this->login->id == Anon_user_id)
    {
      $this->set_login ($this->user_at_id ($this->user_options->default_user_creator_id, TRUE));
      $this->login->ad_hoc_login = TRUE;
    }
  }

  /**
   * Log in and store the user on the client.
   * Does not perform any password checking and replaces the login user stored
   * on the local client. Use this function only for a legitimate login. To
   * change the logged-in user when performing tasks, use {@link set_login()}.
   * To log in using a name and password, use {@link impersonate()}.
   * @param USER &$user Log in as this user
   * @param boolean $remember Remember this login between sessions?
   */
  function log_in (&$user, $remember)
  {
    if (isset ($user) && ! $user->is_allowed (Privilege_set_global, Privilege_login))
      $this->page->raise_security_violation ('You are not allowed to log in.');

    if ($remember)  // user wants the password stored between sessions
      $this->storage->expire_in_n_days ($this->storage_options->login_duration);
    else
      $this->storage->expire_when_session_ends ();

    $this->storage->set_value ($this->storage_options->login_user_name, $this->_encode_user ($user));
    $this->set_login ($user);
  }

  /**
   * Changes the {@link $login} to the given user.
   * Does not perform any password checking. Does not store this user on the
   * local client. Use this function from tasks that need to retrieve data as
   * other users. To log in using a name and password, use
   * {@link impersonate()}.
   * @param USER &$user
   */
  function set_login (&$user)
  {
    $this->login = $user;

    /* Since this user might have been created without a login,
     * it has no reference to the application's user. Update the
     * login variable manually.
     */

    $user->login =& $user;
  }

  /**
   * Changes the {@link $login} to the given user.
   * If the user name and password validate, this routine calls
   * {@link set_login()} to replace the logged-in user. Does not store this user
   * on the local client.
   * @param string $name Name of the user you want to impersonate
   * @param string $password Password for that user
   */
  function impersonate ($name, $password)
  {
    if ($name)
    {
      $user_query = $this->user_query ();
      $user_query->include_permissions (TRUE);
      $user =& $user_query->object_at_name ($name);
      $user_query->include_permissions (FALSE);
    }

    if ($user)
    {
      if (! $user->password_matches ($password))
        $this->raise ("That is not the correct password for user [$name].", 'impersonate', 'APPLICATION');
      else
        $this->set_login ($user);
    }
    else
    {
      $this->raise ("User [$name] does not exist.", 'impersonate', 'APPLICATION');
    }
  }

  /**
   * Log out the current user.
   * Application will then only have anonymous rights.
   */
  function log_out ()
  {
    $this->storage->clear_value ($this->storage_options->login_user_name);
  }

  /**
   * Get the user for this id.
   * Returns empty if not found. Returns {@link non_existent_user()} if the ID is empty.
   * @param integer $id The id of the user to retrieve.
   * @param boolean $include_permissions If TRUE, returns user permissions as
   * well (requires more processing).
   * @param boolean $allow_null If TRUE, returns NULL if the user is not found,
   * otherwise, returns {@link non_existent_user()}.
   * @return USER
   */
  function &user_at_id ($id, $include_permissions = FALSE, $allow_null = FALSE)
  {
    $Result = null;

    if (! empty ($id))
    {
      $cache =& $this->user_cache ();
      $old = $cache->_query->permissions_included;
      $cache->_query->include_permissions ($include_permissions);
      $Result =& $cache->object_at_id ($id);
      $cache->_query->include_permissions ($old);
    }

    if (empty ($Result))
    {
      if ($allow_null)
      {
        global $Null_reference;
        $Result = $Null_reference;
      }
      else
        $Result =& $this->non_existent_user ();
    }

    return $Result;
  }

  /**
   * Return a user representing a generic anonymous user
   * @return USER
   */
  function &non_existent_user ()
  {
    if (! isset ($this->_non_existent_user))
    {
      $this->_non_existent_user = $this->_make_user ();
      $this->_non_existent_user->title = 'unknown';
      $this->_non_existent_user->id = Non_existent_user_id;
      $this->_non_existent_user->kind = Privilege_kind_anonymous;
    }

    return $this->_non_existent_user;
  }

  /**
   * Return a user representing a generic anonymous user
   * @return USER
   */
  function &anon_user ()
  {
    if (! isset ($this->_anon_user))
    {
      /* Explicitly create the database as the "load permissions" call below
       * requires the user to have a database.
       */
      $this->ensure_database_exists ();
      $this->_anon_user = $this->_make_user ();
      $this->_anon_user->title = 'anonymous';
      $this->_anon_user->id = Anon_user_id;
      $this->_anon_user->kind = Privilege_kind_anonymous;
      $this->_anon_user->load_permissions ();
      $cache =& $this->user_cache ();
      $cache->add_object ($this->_anon_user);
    }

    return $this->_anon_user;
  }

  /**
   * Return a user representing a the global user.
   * Used primarily for reading global permissions.
   * @return USER
   */
  function &global_user ()
  {
    if (! isset ($this->_global_user))
    {
      /* Explicitly create the database as the "load permissions" call below
       * requires the user to have a database.
       */
      $this->ensure_database_exists ();
      $this->_global_user = $this->_make_user ();
      $this->_global_user->title = 'global';
      $this->_global_user->id = All_users_id;
      $this->_global_user->kind = Privilege_kind_registered;
      $this->_global_user->load_permissions ();
    }

    return $this->_global_user;
  }

  /**
   * Mark this page as the last refering page.
   * The application tracks this page and uses it again
   * when asked to 'return_to_referer'. This is useful for implementing functions that are called from
   * several pages. For example, an object can be edited from its own page and from a list in which it
   * appears. The edit page uses the referer link to determine which page to return to when done editing.
   */
  function set_referer ()
  {
    $this->storage->set_value ($this->storage_options->return_to_page_name, $this->env->url (Url_part_all));
  }

  /**
   * Set the text to highlight in this application.
   * All HTML formatters are updated so that the given words are highlighted.
   * @see html_text_formatter ()
   * @see html_title_formatter ()
   * @param string $text
   */
  function set_search_text ($text)
  {
    $this->search_text = $text;
    $munger =& $this->html_text_formatter ();
    $munger->highlighted_words = $text;
    $munger =& $this->html_title_formatter ();
    $munger->highlighted_words = $text;
  }

  /**
   * Return to the last marked as a refering page.
   * Use this when finished submitting a form to return to the page from
   * which the user requested the form.
   * @param string $fallback_url
   */
  function return_to_referer ($fallback_url)
  {
    $stored_url = $this->storage->value ($this->storage_options->return_to_page_name);
    if ($stored_url)
      $this->env->redirect_remote ($stored_url);
    else
      $this->env->redirect_local ($fallback_url);
  }

  /**
   * Set the path for an alias.
   * If this changes the appliction path, then recalculate the offset url for accessing that
   * path from this location. If this location is not on the same server (or is command-line),
   * use the entire URL. If this location is a sub-folder of the application root, use a
   * relative offset (../../). If this location is somewhere else on the same server, use the
   * absolute root to the application.
   * @param string $alias Can be {@link Folder_name_application}, {@link Folder_name_attachments}, {@link Folder_name_resources}, {@link Folder_name_pages}, {@link Folder_name_icons}, {@link Folder_name_scripts} or {$link Folder_name_styles}.
   * @param string $path
   */
  function set_path ($alias, $path)
  {
    parent::set_path ($alias, $path);
    switch ($alias)
    {
      case Folder_name_application:
        $this->restore_root_behavior ();
        break;
    }
  }

  /**
   * The URL for this application
   * @return string
   */
  function url ()
  {
    return $this->path_to (Folder_name_application, Force_root_on);
  }

  /**
   * The maximum display size of an object's title.
   * If there is a specific restriction for the requested object type, that is
   * returned; otherwise the general default is returned.
   * @param string $entry_type request the size for this object type
   * @return integer
   */
  function max_title_size ($entry_type = '')
  {
    if ($entry_type && isset ($this->max_title_sizes [$entry_type]))
      return $this->max_title_sizes [$entry_type];
    else
      return $this->display_options->default_max_title_size;
  }

  /**
   * The publisher used by this application.
   * When an application's pending subscriptions are published, this method is called
   * to retrieve a publisher capable of sending all the needed information.
   * @return PUBLISHER
   */
  function make_publisher ()
  {
    $class_name = $this->final_class_name ('PUBLISHER', 'webcore/mail/publisher.php');
    return new $class_name ($this->make_mail_provider ());
  }

  /**
   * The privileges used by this application.
   * Each application can define domain-specific privileges for the different objects
   * it defines.
   * @return CONTENT_PRIVILEGES
   */
  function make_privileges ()
  {
    include_once ('webcore/sys/permissions.php');
    return new SINGLE_ENTRY_PRIVILEGES ($this);
  }

  /**
   * Renders permissions for forms and display.
   * Each application can define how privileges will be displayed to the user. Since there
   * are so many privileges, it's often best to set them in groups. For example, the application
   * can specify that the 'purge' privilege will be either granted or denied in a folder,
   * regardless of the type of object. This is defined by overriding {@link PERMISSIONS_FORMATTER::privilege_groups}
   * and defining a new set of {@link PRIVILEGE_GROUP}s and {@link PRIVILEGE_MAP}s.
   * @return CONTENT_PRIVILEGES
   */
  function make_permissions_formatter ()
  {
    $class_name = $this->final_class_name ('PERMISSIONS_FORMATTER', 'webcore/sys/security.php');
    return new $class_name ($this);
  }

  /**
   * Register a search class for a {@link CONTENT_OBJECT}.
   * Overrides the search class to use for a particular object type using
   * this function. The default handler for a search can also be
   * overridden using the {@link register_class()} (the same way other
   * functionality is overridden). If a search handler is registered for the
   * type 'user', like this:
   *
   * $this->register_search ('user', 'USER_SEARCH');
   *
   * You can override the handler by calling either one of:
   * <code>
   * $this->register_search ('user', 'MY_USER_SEARCH');
   * $this->register_class ('USER_SEARCH', 'MY_USER_SEARCH', '...');
   * </code>
   *
   * This function should generally only be called from {@link
   * _initialize_class_registry()}.
   *
   * @see make_search()
   *
   * @param string $obj_type Name of the search type.
   * @param string $base_class Name of the class to search.
   * @param string $search_class Name of the search class to use.
   * @param string $search_file Location of the search class definition.
   */
  function register_search ($obj_type, $base_class, $search_class, $search_file = '')
  {
    $this->_searches [$obj_type] = $base_class;
    $this->classes->register_class (App_search_reg_prefix . $obj_type, $search_class, $search_file);
  }

  /**
   * Return an object to search the given type.
   * @param string $type
   * @return SEARCH
   */
  function make_search ($type)
  {
    return $this->_make_special_registered_type ($type, App_search_reg_prefix);
  }

  /**
   * Register a class that is an {@link ENTRY}.
   * Defines the base entry class to use for this function. The default class
   * for an entry can also be overridden using the {@link register_class()} (the
   * same way other functionality is overridden). If an entry class is
   * registered for the type 'job', like this:
   *
   * $this->register_entry_class ('job', 'JOB', 'projects/obj/job.php');
   *
   * You can override the handler by calling either one of:
   * <code>
   * $this->register_search ('job', 'MY_JOB');
   * $this->register_class ('JOB', 'MY_JOB', '...');
   * </code>
   *
   * This function should generally only be called from {@link
   * _initialize_class_registry()}.
   *
   * @see make_entry()
   *
   * @param string $obj_type Name of the entry type.
   * @param string $entry_class Name of the entry class to use.
   * @param string $entry_file Location of the entry class definition.
   */
  function register_entry_class ($obj_type, $entry_class, $entry_file)
  {
    $this->register_class (App_entry_reg_prefix . $obj_type, $entry_class, $entry_file);
  }

  /**
   * Return an object for the given entry type.
   * @see register_entry_class()
   * @param string $type
   * @return ENTRY
   */
  function make_entry ($type)
  {
    return $this->_make_special_registered_type ($type, App_entry_reg_prefix);
  }

  /**
   * Return a list of registered entry types.
   * @return array[string,TYPE_INFO]
   * @see TYPE_INFO
   */
  function entry_type_infos ()
  {
    if (! isset ($this->_entry_type_infos))
    {
      $this->_entry_type_infos = array ();
      $entry_classes = $this->classes->classes_with_prefix (App_entry_reg_prefix);
      foreach ($entry_classes as $class_name)
        $this->_entry_type_infos [] = $this->type_info_for ($class_name);
    }

    return $this->_entry_type_infos;
  }

  /**
   * Get the type info for a particular search type.
   * @param string $type
   * @return TYPE_INFO
   */
  function search_type_info_for ($type)
  {
    return $this->type_info_for ($this->_searches [$type]);
  }

  /**
   * Return a list of registered search types.
   * @return array[string,TYPE_INFO]
   * @see TYPE_INFO
   */
  function search_type_infos ()
  {
    $Result = array ();
    foreach ($this->_searches as $id => $class_name)
      $Result [] = $this->type_info_for ($class_name);
    return $Result;
  }

  /**
   * The actual file system location of the application source.
   * Copy/paste to descendents to return the correct location.
   * @return string
   * @access private
   */
  function _source_path ()
  {
    return __FILE__;
  }

  /**
   * Creates the user cache on demand and returns it.
   * @return QUERY_BASED_CACHE
   * @access private
   */
  function &user_cache ()
  {
    if (! isset ($this->_user_cache))
    {
      include_once ('webcore/db/query.php');
      $query = $this->user_query ();
      $this->_user_cache = new QUERY_BASED_CACHE ($query);
    }
    return $this->_user_cache;
  }

  /**
   * Redirect to the offline page, if necessary.
   * @access private
   */
  function _enforce_offline ()
  {
    if (! isset ($this->login) || ! $this->login->is_allowed (Privilege_set_global, Privilege_offline))
    {
      if ($this->env->url (Url_part_file_name) != $this->page_names->offline)
        $this->env->redirect_local ($this->page_names->offline . '?app_name=' . $this->title);
    }
  }

  /**
   * Build an anonymous user name based on application options.
   * Uses the ip address as a base, then decides whether to resolve the host,
   * how to obfuscate it (if at all) and which prefix and suffix.
   * @see $anon_options
   * @return string
   * @access private
   */
  function _make_anon_name ()
  {
    $opts =& $this->anon_options;
    $browser = $this->env->browser ();

    if ($opts->resolve_host)
      $Result = $browser->domain ();
    else
    {
      if ($opts->show_ip_address)
        $Result = $browser->ip_address ();
    }

    $Result = $opts->name_prefix . $Result . $opts->name_suffix;

    return $Result;
  }

  /**
   * Called from {@link restore_root_behavior()}.
   * @return boolean
   * @access private
   */
  function _default_resolve_to_root ()
  {
    if ($this->env->running_on_declared_host ())
    {
      $app_url = $this->path_to (Folder_name_application);
      $curr_url = $this->env->url (Url_part_path);
      $opts = global_url_options ();

      /* Remove the domain to prevent prepending a root that is
       * already defined in the environment.
       */

      $this->root_url = strip_domain (path_between ($curr_url, $app_url, $opts), $opts);
    }
    else
      $this->root_url = $this->path_to (Folder_name_application, Force_root_off);

    return ! empty ($this->root_url);
  }

  /**
   * @return USER
   * @access private
   */
  function _make_user ()
  {
    $class_name = $this->final_class_name ('USER', 'webcore/obj/user.php');
    return new $class_name ($this);
  }

  /**
   * Get a logged in user.
   * If 'force' is false, the result is a pure anonymous user. It can never be empty.
   *
   * Call with 'force' equal to True before recording information with the returned user.
   * If this is true, then the application will create an anonymous user based on IP address
   * if there is no registered user logged in.
   * @param boolean $force
   * @return USER
   * @access private
   */
  function &_logged_in_user ($force = FALSE)
  {
    $info_name = $this->storage_options->login_user_name;
    $user_info = $this->storage->value ($info_name);

    /* no login yet, so find out if this ip address already has a recorded
     * anonymous user. Anonymous users are recorded when they create content.
     */

    if (! isset ($user_info))
    {
      $user_query = $this->user_query ();
      $browser = $this->env->browser ();
      $ip_address = $browser->ip_address ();

      /* if the IP Address is empty, then this is running from the command line. */

      if ($ip_address)
      {
        $user_query->include_permissions (TRUE);
        $Result =& $user_query->object_with_fields (array ('ip_address', 'kind'), array(ip2long ($ip_address), 'anonymous'));
        $user_query->include_permissions (FALSE);

        if ($Result)
        {
          $cache =& $this->user_cache ();
          $cache->add_object ($Result);
        }
        elseif ($force)
        {
          $Result =& $this->new_user (Privilege_kind_anonymous);
          $history_item =& $Result->new_history_item ();
          $Result->store_if_different ($history_item);

          $cache =& $this->user_cache ();
          $cache->add_object ($Result);
        }
      }
    }
    else
      $Result =& $this->_decode_user ($user_info);

    /* Enforce login privilege. If not allowed to log in, log them back out. */

    if (isset ($Result) && ! $Result->is_allowed (Privilege_set_global, Privilege_login))
      unset ($Result);

    /* No user found, use the anonymous */

    if (! isset ($Result))
    {
      $Result =& $this->anon_user ();
      $this->storage->clear_value ($info_name);
    }

    return $Result;
  }

  /**
   * Return a unique identifier for a user.
   * This is stored in client-side storage (cookie) in order to remember a login.
   * @param USER &$user
   * @return string
   * @access private
   */
  function _encode_user (&$user)
  {
    return $user->title . '|' . $user->password;
  }

  /**
   * Return a user based on a unique identifier.
   * Matches a user in the database to the user information stored on the client-side
   * using {@link _encode_user()}
   * @param string $user_id Unique identifier created with '_encode_user'.
   * @return USER
   */
  function &_decode_user ($user_id)
  {
    $params = explode ('|', $user_id);
    if (sizeof ($params) == 2)
    {
      $user_query = $this->user_query ();
      $user_query->include_permissions (TRUE);
      $user_query->restrict ("title = '{$params[0]}'");
      $user_query->restrict ("password = '{$params[1]}'");
      $Result =& $user_query->first_object ();
      $user_query->include_permissions (FALSE);
      if (isset ($Result))
      {
        $cache =& $this->user_cache ();
        $cache->add_object ($Result);
      }
      return $Result;
    }
  }

  /**
   * Resolve the class for the given type and prefix.
   * @param string $type
   * @param string $type_prefix
   * @return object
   * @access private
   */
  function _make_special_registered_type ($type, $type_prefix)
  {
    /* First, retrieve the actual handler from the registry.
     * Then, check for an override of the default handler.
     */

    $full_type = $type_prefix . $type;
    $class_name = $this->final_class_name ($full_type);
    $class_name = $this->final_class_name ($class_name);

    if ($class_name == $full_type)
      $this->raise ("Unknown [$type_prefix] type [$type]", '_make_special_registered_type', 'APPLICATION');
    else
      return new $class_name ($this);
  }

  /**
   * @access private
   * @var USER
   */
  var $_anon_user;
  /**
   * @access private
   * @var USER
   */
  var $_global_user;
  /**
   * @access private
   * @var USER
   */
  var $_logged_in_user;

  /**
   * Access using {@link user_cache()}.
   * @access private
   * @var QUERY_BASED_CACHE
   */
  var $_user_cache;

  /**
   * List of {@link ENTRY} classes.
   * This list is used to create a list of {@link TYPE_INFO} objects describing the available
   * entry types when requested with {@link entry_type_infos()}.
   * @var array[string,string]
   * @access private
   */
  var $_entry_classes;
  /**
   * Cached list of {@link TYPE_INFO} objects.
   * @var array[TYPE_INFO]
   * @see entry_type_infos()
   * @access private*/
  var $_entry_type_infos;
  /**
   * Map from a search name to a class name.
   * Used to get type information for the object being searched.
   * @see search_type_infos()
   * @var array [string,string]
   * @access private
   */
  var $_searches;
  /**
   * @var array[string]
   * @access private
   */
  var $_page_templates;
}

/**
 * Encapsulates an application that uses {@link DRAFTABLE_ENTRY}s.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.7.1
 */
class DRAFTABLE_APPLICATION extends APPLICATION
{
  /**
   * Add classes to the {@link $classes} object factory.
   * @access private
   */
  function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('ENTRY', 'DRAFTABLE_ENTRY', 'webcore/obj/entry.php');
    $this->register_class ('EXPLORER_COMMANDS', 'DRAFTABLE_EXPLORER_COMMANDS');
    $this->register_class ('MULTIPLE_OBJECT_MOVER_FORM', 'MULTIPLE_DRAFTABLE_OBJECT_MOVER_FORM');
    $this->register_class ('FOLDER_ENTRY_QUERY', 'FOLDER_DRAFTABLE_ENTRY_QUERY', 'webcore/db/folder_entry_query.php');
    $this->register_class ('USER_ENTRY_QUERY', 'USER_DRAFTABLE_ENTRY_QUERY', 'webcore/db/user_entry_query.php');
  }
}

?>