<?php

/**
 * @copyright Copyright (c) 2006 [[_author_name_]]
 * @author [[_author_name_]] <[[_author_email_]]>
 * @filesource
 * @package [[_app_name_]]
 * @subpackage config
 * @version 1.0.0
 * @since 1.0.0
 */

/****************************************************************************

Copyright (c) 2006 [[_author_name_]]

This file is part of [[_app_title_]].

[[_app_title_]] is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

[[_app_title_]] is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with [[_app_title_]]; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the [[_app_title_]], visit:

[[_app_url_]]

****************************************************************************/

/** */
include_once ('webcore/config/application_engine.php');

/** Creates an {@link [[_prefix_uc_]]_APPLICATION}.
 * Inherit from this class to customize startup for the application.
 * @package [[_app_name_]]
 * @subpackage config
 * @version 1.0.0
 * @since 1.0.0 */
class [[_prefix_uc_]]_APPLICATION_ENGINE extends APPLICATION_ENGINE
{
  /** Register plugins using {@link register_class()} during initialization.
   * @access private */
  function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('APPLICATION', '[[_prefix_uc_]]_APPLICATION', '[[_app_folder_]]/sys/[[_prefix_lc_]]_application.php');
  }
  
  /** Customize the application object.
   * @param PAGE &$page
   * @param APPLICATION &$app
   * @access private */
  function _init_application (&$page, &$app)
  {
    parent::_init_application ($page, $app);
    $app->mail_options->send_from_address = '[[_app_name_]]@' . $app->env->default_domain ();
    $app->mail_options->send_from_name = [[_app_title_]];
    $app->mail_options->log_file_name = '{logs}[[_app_name_]]_mail.log';
    $app->mail_options->entry_publication_filter = array (History_item_created);
    $app->mail_options->comment_publication_filter = array (History_item_created);
  }
}

?>