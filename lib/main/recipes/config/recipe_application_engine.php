<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage config
 * @version 3.4.0
 * @since 1.7.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli Recipes.

earthli Recipes is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Recipes is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Recipes; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Recipes, visit:

http://earthli.com/software/webcore/app_recipes.php

****************************************************************************/

/** */
include_once ('webcore/config/application_engine.php');

/**
 * Creates an {@link RECIPE_APPLICATION}.
 * Inherit from this class to customize startup for the application.
 * @package recipes
 * @subpackage config
 * @version 3.4.0
 * @since 1.7.0
 */
class RECIPE_APPLICATION_ENGINE extends APPLICATION_ENGINE
{
  /**
   * Register plugins in {@link $classes} during initialization.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('APPLICATION', 'RECIPE_APPLICATION', 'recipes/sys/recipe_application.php');
  }
  
  /**
   * Customize the application object.
   * @param PAGE $page
   * @param APPLICATION $app
   * @access private
   */
  protected function _init_application ($page, $app)
  {
    parent::_init_application ($page, $app);
    $app->mail_options->send_from_address = 'recipes@' . $app->env->default_domain ();
    $app->mail_options->send_from_name = $page->title->group . ' Recipes';
    $app->mail_options->log_file_name = '{logs}recipes_mail.log';
    $app->mail_options->entry_publication_filter = array (History_item_published);
    $app->mail_options->comment_publication_filter = array (History_item_created);

    $app->storage->prefix = 'webcore_recipes_';
  }
}

?>