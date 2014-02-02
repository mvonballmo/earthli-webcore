<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage config
 * @version 3.4.0
 * @since 2.7.0
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
include_once ('webcore/sys/resolver.php');

/**
 * Creates an {@link APPLICATION}.
 * Inherit from this class to customize startup for a given application.
 * Redefine the {@link _init_application()} method.
 * @package webcore
 * @subpackage config
 * @version 3.4.0
 * @since 2.7.0
 */
class APPLICATION_ENGINE extends RESOLVER
{
  /**
   * Application created by this engine.
   * Call {@link init()} to create and initialize.
   * @var APPLICATION
   */
  public $app;

  /**
   * Initialize global objects for this session.
   * Creates {@link $env}, {@link $logger} and {@link $page}; call this
   * before calling {@link run()}.
   * @param PAGE $page
   */
  public function init ($page)
  {
    $this->app = $this->_make_application ($page);
    $this->_init_application ($page, $this->app);
  }

  /**
   * Start up the {@link APPLICATION}.
   * Must be called after {@link init()} and calls {@link APPLICATION::start()}
   * on {@link $app}, by default.
   */
  public function start ()
  {
    $this->_start_application ($this->app);
  }

  /**
   * Set the main application for the page.
   * Initialize the application with {@link init()} beforehand, so that {@link
   * $app} has been created.
   * @param PAGE $page
   */
  public function set_main_app_for ($page)
  {
    $this->_apply_to ($page, $this->app);
  }

  /**
   * Register plugins in {@link $classes} during initialization.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('PAGE', 'THEMED_PAGE', 'webcore/sys/themed_page.php');
  }

  /**
   * Make the application object.
   * Calls {@link _init_application()} to allow customization.
   * @see _init_application()
   * @param PAGE $page
   * @return APPLICATION
   * @access private
   */
  protected function _make_application ($page)
  {
    $class_name = $this->final_class_name ('APPLICATION', 'webcore/sys/application.php');
    return new $class_name ($page);
  }

  /**
   * Customize the application object.
   * Called immediately after {@link _make_application()}.
   * @see _make_application()
   * @param PAGE $page
   * @param APPLICATION $app
   * @access private
   */
  protected function _init_application ($page, $app)
  {
    /* Logged in user information is stored in a cookie. This is the name
     * of the key to use when storing the cookie. You can also set the path
     * for the cookie, to determine in which pages the login is valid. The
     * login will always count for all pages under the application folder,
     * but you can set the path to the site root, as below, so that application
     * code embedded in pages outside the applciation root (e.g. like showing
     * project statuses on a home page) can also detect the log-in status.
     */

  //  $app->storage->set_path ('/');

    /* You can also set the name of the cookie that will be used to store the
     * login information. By default, each application stores its own login
     * info, but you can redirect the cookie used so that all applications share
     * the same login information. The example below shows a site-wide cookie name
     * (not application-specific).
     */

    $app->storage->prefix = 'webcore_app_';
    $app->storage_options->login_user_name = 'login_user';

    /* These are the publication options. Anytime mail is sent from the site,
     * either from a publication script or when a user explicitly sends an object,
     * it arrives sent from the address below. The example below is for a projects
     * application.
     */

  //  $app->mail_options->send_from_address = 'projects@' . $app->env->domain;
  //  $app->mail_options->send_from_name = $page->title->group . ' Projects';

    /* All mail activity is logged. Specify a path here. Leave the path empty
     * to disable logging.
     */

  //  $app->mail_options->log_file_name = '{logs}projects/project_mail.log';

    /* This is the name and password of the user for the publication script. The
     * publication script needs access to all folders from which content should
     * be published. The default SQL script creates such a user, with 'view'
     * rights on all content.
     */

  //  $app->mail_options->publisher_user_name = 'auto_publisher';
  //  $app->mail_options->publisher_user_password = 'password';

    /* Set up the tables for stand-alone operation.
     * This is only important if you plan on adding more WebCore modules. Modules
     * can share users if desired. That means that one log-in will work for all
     * WebCore applications on your site. Permissions are still assigned per-
     * application. If you make an application stand-alone, it has it's own
     * database of users. Note below that you are simply rerouting the user and
     * group tables from the standard generic ones to project-specific ones. The
     * example below shows how to rename these tables so that Projects has
     * standalone users.
     */

  //  $app->table_names->users = 'project_users';
  //  $app->table_names->groups = 'project_groups';
  //  $app->table_names->users_to_groups = 'project_users_to_groups';
  }

  /**
   * Start up the application object.
   * Called from {@link start()}.
   * @see _init_application()
   * @param APPLICATION $app
   * @access private
   */
  protected function _start_application ($app)
  {
    $app->load_login ();
  }

  /**
   * Make this application the main application for the given page.
   * @param PAGE $page
   * @param APPLICATION $app
   * @access private
   */
  protected function _apply_to ($page, $app)
  {
    /* Set the main name of the page. (this uses a PAGE_TITLE object; see the
     * documentation for more information.) Also sets up some values in the page
     * template.
     */

    $page->title->group = $app->title;
    $page->template_options->title = $app->short_title;
    $page->template_options->app_info = '<a href="' . $app->support_url . '">' . $app->description () . '</a>';
    $page->template_options->icon = $app->resolve_file ($app->icon);

    /* Finally, make this the "main" application for the page. */

    $page->app = $app;
  }
}

?>