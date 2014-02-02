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
require_once ('webcore/tasks/db/upgrade_per_app_25_26.php');

class RECIPES_15_16_MIGRATOR_TASK extends UPGRADE_PER_APP_25_26_TASK
{
  public $application_name = 'earthli Recipes';
  public $version_from = '1.5.0';
  public $version_to = '1.6.0';

  protected function _execute ()
  {
    $this->add_attachments ('recipe_attachments', 'recipe_actions');
    $this->add_attachment_permissions('recipe_folder_permissions', 'recipe_user_permissions');

    log_open_block ("Adding owners to objects");
      $this->add_owner_to_table ('recipe_attachments');
      $this->add_owner_to_table ('recipe_comments');
      $this->add_owner_to_table ('recipes');
      $this->add_owner_to_table ('recipe_folders');
    log_close_block ();

    $this->add_version_info ('recipe_application', 'earthli Recipes', '1.6.0');
    $this->add_organizational ('recipe_folders');

    log_open_block ("Updating originator size.");
      $this->_query ('ALTER TABLE `recipes` CHANGE `originator` `originator` VARCHAR( 255 ) NOT NULL');
    log_close_block ();
  }
}

?>