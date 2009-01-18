<?php

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

include_once ('webcore/config/themed_engine.php');

/* Called from the main WebCore init file to determine which engine
 * to create on startup.
 */
function engine_class_name ()
{
  return 'EARTHLI_ENGINE';
//  return 'EARTHLI_ONLINE_DEV_ENGINE';
//  return 'EARTHLI_STEN_DEV_ENGINE';
//  return 'EARTHLI_YMIR_DEV_ENGINE';
}

/* Customized startup engine for earthli.com.
 * Used for deployment; does not initialize debugging.
 */
class EARTHLI_ENGINE extends THEMED_ENGINE
{
  function _init_environment (&$env)
  {
    parent::_init_environment ($env);

    $env->title = 'earthli';

    $env->set_host_properties (
      'earthli.com',
      array (
        'http://(www.)?earthli.(com|net|org)' => '/var/www/earthli/main/site/',
        'http://data.earthli.com/' => '/var/www/earthli/data/',
      )
    );

    $this->_apply_european_settings ($env);
    $this->_apply_release_settings ($env);

    $env->set_path (Folder_name_resources, '/common/webcore');
    $env->set_path (Folder_name_pages, '{root}');
    $env->set_path (Folder_name_data, 'http://data.earthli.com/');
    $env->set_path ('assets', '{root}/common');
    $env->set_path ('site_icons', '{assets}/earthli/icons');

    $env->set_path (Folder_name_logs, '/var/log/earthli/');
  }

  function _init_page (&$env, &$page)
  {
    parent::_init_page ($env, $page);

    /* Tell the page to apply icon theme settings for this alias. */
    $page->add_icon_alias ($env, 'site_icons');

    $page->template_options->logo_file = '{site_icons}logos/earthli_rabbit_logo_with_text';
    $page->template_options->logo_title = 'earthli.com';
    $page->template_options->copyright = "Copyright (c) 1999-" . date ('Y') . " earthli.com. All Rights Reserved.";

    $page->icon_options->file_name = '{site_icons}logos/earthli_rabbit_icon';
    $page->icon_options->mime_type = 'image/png';

    $page->database_options->name = 'earthli';
    $page->database_options->password = 'tuttifrutti';

    $page->mail_options->webmaster_address = 'marco@earthli.com';
    $page->mail_options->send_from_address = 'marco@earthli.com';

    $page->register_application ('com.earthli.albums', 'EARTHLI_ALBUM_APPLICATION_ENGINE');
    $page->register_application ('com.earthli.news', 'EARTHLI_NEWS_APPLICATION_ENGINE');
    $page->register_application ('com.earthli.projects', 'EARTHLI_PROJECT_APPLICATION_ENGINE');
    $page->register_application ('com.earthli.recipes', 'EARTHLI_RECIPE_APPLICATION_ENGINE');
    $page->register_application ('com.earthli.tests', 'EARTHLI_TEST_APPLICATION_ENGINE');
  }

  function _make_logger (&$env)
  {
    return $this->_make_text_file_logger ($env, '/var/log/earthli/webcore_errors.log');
  }
}

/* Customized startup engine for earthli.com.
 * Used for development; initializes debugging and sets up a local server.
 */
class EARTHLI_DEV_ENGINE extends EARTHLI_ENGINE
{
  function _init_environment (&$env)
  {
    parent::_init_environment ($env);

    $this->_apply_debug_settings ($env);
  }

  function _init_page (&$env, &$page)
  {
    parent::_init_page ($env, $page);

    $page->database_options->password = '';
  }

  function _make_logger (&$env)
  {
    $Result = $this->_make_console_logger ($env);
    $Result->show_time = TRUE;
    return $Result;
  }
}

/* Startup for developing online. */
class EARTHLI_ONLINE_DEV_ENGINE extends EARTHLI_DEV_ENGINE
{
  function _init_environment (&$env)
  {
    parent::_init_environment ($env);

    $env->set_host_properties (
      'dev.earthli.com',
      array (
        'http://dev.earthli.com' => '/var/www/earthli/dev-main/site/',
        'http://data.earthli.com/' => '/var/www/earthli/data/',
      )
    );

    $env->set_path (Folder_name_logs, '/var/log/earthli/');
  }
}

/* Startup for developing on Ymir (Mac Mini). */
class EARTHLI_YMIR_DEV_ENGINE extends EARTHLI_DEV_ENGINE
{
  function _init_environment (&$env)
  {
    parent::_init_environment ($env);

    $env->set_host_properties (
      'localhost',
      array (
        'http://ymir.local/~marco/earthli/web-sites/earthli.com/main/site' => '/Users/Marco/Sites/earthli/web-sites/earthli.com/main/site',
      )
    );


    $env->set_host_properties ('ymir.local', array ('ymir.local', 'localhost'), '/Users/marco/Sites/', '~marco/earthli/dev');
    $env->set_path (Folder_name_logs, '/Users/marco/Code/logs');
  }
}

/* Startup for developing on Sten (Windows Laptop). */
class EARTHLI_STEN_DEV_ENGINE extends EARTHLI_DEV_ENGINE
{
  function _init_environment (&$env)
  {
    parent::_init_environment ($env);

    $env->set_host_properties (
      'localhost',
      array (
        'http://localhost/earthli' => 'D:\Marco\earthli\web-sites\earthli.com\main\site',
        'http://localhost/images' => 'D:\Marco\earthli\web-sites\earthli.com\main\data',
        'http://localhost/resources' => 'D:\Marco\earthli\web-sites\earthli.com\main\lib\webcore_resources',
      )
    );

    $env->set_path (Folder_name_root, '/earthli/');
    $env->set_path (Folder_name_resources, 'http://localhost/resources/');
//    $env->set_path (Folder_name_data, '/images/');

    $env->set_path (Folder_name_logs, 'D:\Marco\earthli\logs');
  }
}

?>
