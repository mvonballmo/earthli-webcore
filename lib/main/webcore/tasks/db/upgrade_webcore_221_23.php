<?php

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

http://www.earthli.com/software/webcore/

****************************************************************************/

require_once ('webcore/init.php');
require_once ('webcore/db/migrator_task.php');

class UPGRADE_WEBCORE_221_23_TASK extends MIGRATOR_TASK
{
  public $application_name = 'earthli WebCore';
  public $version_from = '2.2.1';
  public $version_to = '2.3.0';

  protected function _execute ()
  {
    log_open_block ("Updating theme table fields...");
      $this->_query ("ALTER TABLE `themes` CHANGE `name` `title` VARCHAR( 100 ) NOT NULL , CHANGE `file` `main_CSS_file_name` VARCHAR( 100 ) NOT NULL , CHANGE `color` `icon_set` VARCHAR( 100 ) NOT NULL , CHANGE `renderer` `renderer_class_name` VARCHAR( 100 ) NOT NULL");
      $this->_query ("ALTER TABLE `themes` ADD `font_name_CSS_file_name` VARCHAR( 100 ) NOT NULL AFTER `main_CSS_file_name` ,ADD `font_size_CSS_file_name` VARCHAR( 100 ) NOT NULL AFTER `font_name_CSS_file_name`");
      $this->_query ("ALTER TABLE `themes` ADD `icon_extension` VARCHAR( 5 ) NOT NULL AFTER `icon_set`");
      $this->_query ("UPDATE themes SET icon_set = ''");
      $this->_query ("UPDATE themes SET renderer_class_name = 'DEFAULT_PAGE_RENDERER' WHERE renderer_class_name = 'EARTHLI_RENDERER'");
      $this->_query ("UPDATE themes SET renderer_class_name = 'OPUS_PAGE_RENDERER' WHERE renderer_class_name = 'OPUS_RENDERER'");
      $this->_query ("ALTER TABLE `themes` CHANGE `renderer_class_name` `renderer_class_name` VARCHAR( 100 ) NOT NULL ");
      $this->_query ("ALTER TABLE `themes` DROP `group_name`");
      $this->_query ("ALTER TABLE `themes` CHANGE `name` `title` VARCHAR( 100 ) NOT NULL , CHANGE `file` `main_CSS_file_name` VARCHAR( 100 ) NOT NULL , CHANGE `color` `icon_set` VARCHAR( 100 ) NOT NULL , CHANGE `renderer` `renderer_class_name` VARCHAR( 100 ) NOT NULL");
      $this->_query ("ALTER TABLE `themes` ADD `font_name_CSS_file_name` VARCHAR( 100 ) NOT NULL AFTER `main_CSS_file_name` ,ADD `font_size_CSS_file_name` VARCHAR( 100 ) NOT NULL AFTER `font_name_CSS_file_name`");
      $this->_query ("ALTER TABLE `themes` ADD `icon_extension` VARCHAR( 5 ) NOT NULL AFTER `icon_set`");
      $this->_query ("UPDATE themes SET icon_set = ''");
      $this->_query ("ALTER TABLE `themes` CHANGE `renderer_class_name` `renderer_class_name` VARCHAR( 100 ) NOT NULL ");
    log_close_block ();
  }
}

?>