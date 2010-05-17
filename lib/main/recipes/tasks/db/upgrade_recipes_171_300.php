<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
require_once ('webcore/tasks/db/upgrade_per_app_271_300.php');

class RECIPES_171_300_MIGRATOR_TASK extends UPGRADE_PER_APP_271_300_TASK
{
  public $application_name = 'com.earthli.recipes';
  public $version_from = '1.7.1';
  public $version_to = '3.0.0';

  protected function _execute ()
  {
    log_open_block ("Updating summary in folders");
      $this->_query ('ALTER TABLE `recipe_folders` DROP INDEX `id_2`');
      $this->_query ('ALTER TABLE `recipe_subscriptions` DROP INDEX `email`');
      $this->_update_folders ('recipe_folders');
    log_close_block ();
  }
}

?>