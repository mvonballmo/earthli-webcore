<?php

/****************************************************************************

Copyright (c) 2002-2005 Marco Von Ballmoos

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

http://www.earthli.com/software/webcore/recipes

****************************************************************************/

require_once ('webcore/init.php');
require_once ('webcore/tasks/db/upgrade_per_app_24_25.php');
require_once ('webcore/tasks/migrate.php');

class RECIPES_14_15_MIGRATOR_TASK extends UPGRADE_PER_APP_24_25_TASK
{
  var $application_name = 'earthli Recipes';
  var $version_from = '1.4.0';
  var $version_to = '1.5.0';

  function _execute ()
  {
    $this->update_actions ('recipe_actions', 'recipe_comments', 'recipe_folders', 'recipes');
    $this->update_subscriptions ('recipe_subscribers', 'recipe_subscriptions');
    $this->update_permissions ('recipe_folder_permissions', 'recipe_user_permissions');
    $this->update_drafting ('recipes');
    $this->update_folders ('recipe_folders');
    $this->update_users ('recipe_user_permissions');
  }
}

?>
