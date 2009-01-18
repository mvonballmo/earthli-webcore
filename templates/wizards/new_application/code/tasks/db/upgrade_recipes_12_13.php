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
require_once ('webcore/tasks/db/upgrade_per_app_221_23.php');
require_once ('webcore/tasks/migrate.php');

class RECIPES_12_13_MIGRATOR_TASK extends UPGRADE_PER_APP_221_23_TASK
{
  var $application_name = 'earthli Recipes';
  var $version_from = '1.2.0';
  var $version_to = '1.3.0';

  function _execute ()
  {
    log_open_block ("Updating names [object => entry]...");
      $this->_query ("ALTER TABLE `recipe_subscriptions` CHANGE `watch_objects` `watch_entries` TINYINT( 4 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `recipe_comments` CHANGE `object_id` `entry_id` INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL");
    log_close_block ();

    log_open_block ("Building recipe tree...");
      $this->_query ("CREATE TABLE `recipe_tree` (`parent_id` INT UNSIGNED NOT NULL ,`child_id` INT UNSIGNED NOT NULL)");
      $this->_query ("SELECT * from recipe_folders");
      $this->build_folder_tree ('recipe_tree');
    log_close_block ();
  }
}

?>
