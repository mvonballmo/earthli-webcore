<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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

require_once ('webcore/init.php');
require_once ('webcore/tasks/db/upgrade_per_app_260_270.php');

class RECIPES_160_170_MIGRATOR_TASK extends UPGRADE_PER_APP_260_270_TASK
{
  public $application_name = 'com.earthli.recipes';
  public $version_from = '1.6.0';
  public $version_to = '1.7.0';

  protected function _execute ()
  {
    log_open_block ("Cleaning up indexes");
      $this->clean_up_folder_permissions ('recipe_folder_permissions');
      $this->clean_up_user_permissions ('recipe_user_permissions');
      $this->clean_up_id_indexes ('recipes');
      $this->clean_up_id_indexes ('recipe_folders');
      $this->clean_up_id_indexes ('recipe_comments');
      $this->clean_up_subscriptions ('recipe_subscriptions');
      $this->add_full_text_to_comments ('recipe_comments');
      $this->add_folder_id_index ('recipes');
    log_close_block ();
    log_open_block ("Renaming subscriber fields");
      $this->rename_subscriber_fields ('recipe_subscribers');
      $this->rename_action_table ('recipe_actions', 'recipe_history_items');
    log_close_block ();
  }
}

?>