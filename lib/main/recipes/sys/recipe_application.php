<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @subpackage sys
 * @version 3.1.0
 * @since 1.3.0
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/sys/application.php');
require_once ('recipes/sys/recipe_type_infos.php');

/**
 * @package recipes
 * @subpackage sys
 * @version 3.1.0
 * @since 1.3.0
 */
class RECIPE_APPLICATION_PAGE_NAMES extends APPLICATION_PAGE_NAMES
{
  /**
   * @var string
   */
  public $entry_home = 'view_recipe.php';
}

/**
 * @package recipes
 * @subpackage sys
 * @version 3.1.0
 * @since 1.3.0
 */
class RECIPE_APPLICATION_TABLE_NAMES extends APPLICATION_TABLE_NAMES
{
  /**
   * @var string
   */
  public $folders = 'recipe_folders';

  /**
   * @var string
   */
  public $comments = 'recipe_comments';

  /**
   * @var string
   */
  public $entries = 'recipes';

  /**
   * @var string
   */
  public $user_permissions = 'recipe_user_permissions';

  /**
   * @var string
   */
  public $folder_permissions = 'recipe_folder_permissions';

  /**
   * @var string
   */
  public $subscriptions = 'recipe_subscriptions';

  /**
   * @var string
   */
  public $subscribers = 'recipe_subscribers';

  /**
   * @var string
   */
  public $history_items = 'recipe_history_items';

  /**
   * @var string
   */
  public $searches = 'recipe_searches';

  /**
   * @var string
   */
  public $attachments = 'recipe_attachments';
}

/**
 * A WebCore application that lets users enter {@link RECIPE}s.
 * @package recipes
 * @subpackage sys
 * @version 3.1.0
 * @since 1.3.0
 */
class RECIPE_APPLICATION extends DRAFTABLE_APPLICATION
{
  /**
   * @var string
   */
  public $title = 'earthli Recipes';

  /**
   * @var string
   */
  public $short_title = 'Recipes';

  /**
   * @var string
   */
  public $icon = '{app_icons}app/recipes';

  /**
   * @var string
   */
  public $support_url = 'http://earthli.com/software/webcore/app_recipes.php';

  /**
   * Unique ID for this framework.
   * @var string
   */
  public $framework_id = 'com.earthli.recipes';

  /**
   * @var integer
   */
  public $version = '3.0.0';

  /**
   * @param PAGE $page Page to which this application is attached.
   * @access private
   */
  public function RECIPE_APPLICATION ($page)
  {
    APPLICATION::APPLICATION ($page);

    $this->set_path (Folder_name_application, '{' . Folder_name_apps . '}recipes');
    $this->set_path (Folder_name_attachments, '{' . Folder_name_data . '}recipes/attachments');

    $this->storage_options->return_to_page_name = 'recipes_page';
  }

  /**
   * Add classes to the {@link $classes} object factory.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('ENTRY', 'RECIPE', 'recipes/obj/recipe.php');
    $this->register_class ('FOLDER_TYPE_INFO', 'RECIPE_BOOK_TYPE_INFO');
    $this->register_class ('FOLDER_ENTRY_QUERY', 'FOLDER_RECIPE_QUERY', 'recipes/db/folder_recipe_query.php');
    $this->register_class ('ENTRY_GRID', 'RECIPE_GRID', 'recipes/gui/recipe_grid.php');
    $this->register_class ('ENTRY_FORM', 'RECIPE_FORM', 'recipes/forms/recipe_form.php');
    $this->register_class ('FOLDER_GRID', 'RECIPE_BOOK_GRID', 'recipes/gui/recipe_book_grid.php');
    $this->register_class ('ENTRY_SUMMARY_GRID', 'RECIPE_SUMMARY_GRID', 'recipes/gui/recipe_grid.php');
    $this->register_class ('PANEL_MANAGER_HELPER', 'RECIPE_PANEL_MANAGER_HELPER', 'recipes/gui/recipe_panel.php');
    $this->register_class ('APPLICATION_TABLE_NAMES', 'RECIPE_APPLICATION_TABLE_NAMES');
    $this->register_class ('APPLICATION_PAGE_NAMES', 'RECIPE_APPLICATION_PAGE_NAMES');

    $this->register_handler ('FOLDER', Handler_commands, 'RECIPE_BOOK_COMMANDS', 'recipes/cmd/recipe_book_commands.php');

    $this->register_entry_class ('recipe', 'RECIPE', 'recipes/obj/recipe.php');
    $this->register_search ('recipe', 'RECIPE', 'RECIPE_SEARCH', 'recipes/obj/recipe_search.php');
  }

  /**
   * Name used for version information.
   * @return string
   */
  public function name ()
  {
    return 'earthli Recipes';
  }

  /**
   * The actual file system location of the application source. Copy/paste to
   * descendents to return the correct location.
   * @return string
   * @access private
   */
  protected function _source_path ()
  {
    return __FILE__;
  }
}

?>
