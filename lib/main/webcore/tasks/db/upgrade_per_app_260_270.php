<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

require_once ('webcore/db/migrator_task.php');

class UPGRADE_PER_APP_260_270_TASK extends MIGRATOR_TASK
{
  public function clean_up_folder_permissions ($table)
  {
    if (! $this->db->table_has_primary_index ($table))
    {
      $this->_query ('ALTER TABLE `' . $table . '` ADD PRIMARY KEY ( `ref_id` , `folder_id` , `kind` ) ');
      $this->_query ('ALTER TABLE `' . $table . '` DROP INDEX `ref_id` ');
      $this->_query ('ALTER TABLE `' . $table . '` DROP INDEX `folder_id` ');
    }
  }
  
  public function clean_up_id_indexes ($table)
  {
    $this->_query ('ALTER TABLE `' . $table . '` DROP INDEX `id`'); 
  }
  
  public function clean_up_user_permissions ($table)
  {
    $this->_query ('ALTER TABLE `' . $table . '` ADD PRIMARY KEY ( `user_id` );');
    $this->_query ('ALTER TABLE `' . $table . '` DROP INDEX `user_id`'); 
  }
  
  public function clean_up_subscriptions ($table)
  {
    $this->_query ('ALTER TABLE `' . $table . '` ADD PRIMARY KEY ( `subscriber_id` , `kind` , `ref_id` ) ;');
  }
  
  public function rename_subscriber_fields ($table)
  {
    $this->_query ("ALTER TABLE  `" . $table . "` CHANGE  `group_actions`  `group_history_items` TINYINT( 4 ) NOT NULL DEFAULT  '0'," .
      "CHANGE  `show_actions`  `show_history_items` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0'," .
      "CHANGE  `show_action_as_subject`  `show_history_item_as_subject` TINYINT( 3 ) UNSIGNED NOT NULL DEFAULT  '0'," .
      "CHANGE  `queued_action_ids`  `queued_history_item_ids` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");    
  }
  
  public function rename_action_table ($old_name, $new_name)
  {
    $this->_query ("ALTER TABLE  `" . $old_name . "` RENAME  `" . $new_name . "` ;");
  }
  
  public function add_full_text_to_comments ($table_name)
  {
    $this->_query ("ALTER TABLE `$table_name` ADD FULLTEXT (`title`)");
    $this->_query ("ALTER TABLE `$table_name` ADD FULLTEXT (`description`)");
  }
  
  public function add_folder_id_index ($table_name)
  {
    $this->_query ("ALTER TABLE  `$table_name` ADD INDEX (  `folder_id` )");
  }

  /**
   * Perform setup for a process that will run.
   * {@link _can_be_executed()} has returned True.
   * @access private
   */
  protected function _pre_execute ()
  {
    if (! $this->db->table_exists ('versions'))
    {
      log_open_block ("Adding version table");
        $this->_query ("CREATE TABLE `versions` (`title` varchar(100) NOT NULL default '', `version` varchar(50) NOT NULL default '')");
        $this->_query ("ALTER TABLE `versions` ADD PRIMARY KEY ( `title` ); ");
      log_close_block ();
    }
  }
}

?>