<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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

/**
 * Indicates that a permissions record is for all users.
  * @access private
  */
define ('All_users_type', 0x01);
/**
 * Indicates that a permissions record is for a group.
  * @access private
  */
define ('Group_type', 0x03);

/**
 * Indicates an anonymous user.
  * @internal Indicates that a permissions record is for anonymous users.
  */
define ('Anon_user_type', 0x02);
/**
 * Indicates a registered user.
  * @internal Indicates that a permissions record is for a user.
  */
define ('User_type', 0x04);

/**
 * Flags that grant all rights (content and user).
  * @access private
  */
define ('Admin_permissions', 0x81FFFFF);
/**
 * Flags that grant all rights on content.
  * @access private
  */
define ('All_folder_permissions', 0x8000FFF);

/**
 * Grants right to view invisible content.
  * @access private
  */
define ('View_invisible', 0x8000000);

/**
 * Grants right to purge groups.
  * @access private
  */
define ('Purge_group', 0x400000);
/**
 * Grants right to purge users.
  * @access private
  */
define ('Purge_user', 0x200000);
/**
 * Grants right to change user security settings.
  * @access private
  */
define ('Secure_user', 0x100000);
/**
 * Grants right to see users.
  * @access private
  */
define ('View_user', 0x80000);
/**
 * Grants right to delete users.
  * @access private
  */
define ('Delete_user', 0x40000);
/**
 * Grants right to edit user information.
  * @access private
  */
define ('Modify_user', 0x20000);
/**
 * Grants right to create new users.
  * @access private
  */
define ('Create_user', 0x10000);
/**
 * Grants right to see groups.
  * @access private
  */
define ('View_group', 0x8000);
/**
 * Grants right to delete groups.
  * @access private
  */
define ('Delete_group', 0x4000);
/**
 * Grants right to edit group information.
  * This grants the right to change the group roster as well.
  * @access private
  */
define ('Modify_group', 0x2000);
/**
 * Grants right to create groups.
  * @access private
  */
define ('Create_group', 0x1000);

/**
 * Grants right to change content security settings.
  * @access private
  */
define ('Secure_folder', 0x800);
/**
 * Grants right to purge folders.
  * @access private
  */
define ('Purge_folder', 0x400);
/**
 * Grants right to delete folders.
  * @access private
  */
define ('Delete_folder', 0x200);
/**
 * Grants right to edit folder information.
  * @access private
  */
define ('Modify_folder', 0x100);
/**
 * Grants right to create folders.
  * @access private
  */
define ('Create_folder', 0x80);
/**
 * Grants right to see folders.
  * @access private
  */
define ('View_folder', 0x40);
/**
 * Grants right to purge content.
  * @access private
  */
define ('Purge_content', 0x20);
/**
 * Grants right to delete content.
  * @access private
  */
define ('Delete_content', 0x10);
/**
 * Grants right to edit content.
  * @access private
  */
define ('Modify_content', 0x08);
/**
 * Grants right to create content.
  * @access private
  */
define ('Create_content', 0x04);
/**
 * Grants right to add comments to content.
  * @access private
  */
define ('Annotate_content', 0x02);
/**
 * Grants right to see content.
  * @access private
  */
define ('View_content', 0x01);

class UPGRADE_PER_APP_24_25_TASK extends MIGRATOR_TASK
{
  public function update_user_folder_permissions ($table_name, $new_name, $old_name)
  {
    $this->_query ("UPDATE `$table_name` SET `$new_name` = `$new_name` | " . Privilege_view . " | " . Privilege_view_history . " where `$old_name` & " . View_folder);
    $this->_query ("UPDATE `$table_name` SET `$new_name` = `$new_name` | " . Privilege_create . " where `$old_name` & " . Create_folder);
    $this->_query ("UPDATE `$table_name` SET `$new_name` = `$new_name` | " . Privilege_modify . " where `$old_name` & " . Modify_folder);
    $this->_query ("UPDATE `$table_name` SET `$new_name` = `$new_name` | " . Privilege_delete . " where `$old_name` & " . Delete_folder);
    $this->_query ("UPDATE `$table_name` SET `$new_name` = `$new_name` | " . Privilege_purge . " where `$old_name` & " . Purge_folder);
    $this->_query ("UPDATE `$table_name` SET `$new_name` = `$new_name` | " . Privilege_secure . " where `$old_name` & " . Secure_folder);
    $this->_query ("UPDATE `$table_name` SET `$new_name` = `$new_name` | " . Privilege_view_hidden . " where `$old_name` & " . View_invisible);
  }

  public function update_user_content_permissions ($table_name, $new_entry_name, $new_comment_name, $old_name)
  {
    $this->_query ("UPDATE `$table_name` SET `$new_entry_name` = `$new_entry_name` | " . Privilege_view . " | " . Privilege_view_history . " | " . Privilege_view_hidden . " where `$old_name` & " . View_content);
    $this->_query ("UPDATE `$table_name` SET `$new_entry_name` = `$new_entry_name` | " . Privilege_create . " where `$old_name` & " . Create_content);
    $this->_query ("UPDATE `$table_name` SET `$new_entry_name` = `$new_entry_name` | " . Privilege_modify . " where `$old_name` & " . Modify_content);
    $this->_query ("UPDATE `$table_name` SET `$new_entry_name` = `$new_entry_name` | " . Privilege_delete . " where `$old_name` & " . Delete_content);
    $this->_query ("UPDATE `$table_name` SET `$new_entry_name` = `$new_entry_name` | " . Privilege_purge . " where `$old_name` & " . Purge_content);
    $this->_query ("UPDATE `$table_name` SET `$new_entry_name` = `$new_entry_name` | " . Privilege_view_hidden . " where `$old_name` & " . View_invisible);

    $this->_query ("UPDATE `$table_name` SET `$new_comment_name` = `$new_comment_name` | " . Privilege_view . " | " . Privilege_view_history . " | " . Privilege_view_hidden . " where `$old_name` & " . View_content);
    $this->_query ("UPDATE `$table_name` SET `$new_comment_name` = `$new_comment_name` | " . Privilege_create . " where `$old_name` & " . Annotate_content);
    $this->_query ("UPDATE `$table_name` SET `$new_comment_name` = `$new_comment_name` | " . Privilege_modify . " where `$old_name` & " . Modify_content);
    $this->_query ("UPDATE `$table_name` SET `$new_comment_name` = `$new_comment_name` | " . Privilege_delete . " where `$old_name` & " . Delete_content);
    $this->_query ("UPDATE `$table_name` SET `$new_comment_name` = `$new_comment_name` | " . Privilege_purge . " where `$old_name` & " . Purge_content);
    $this->_query ("UPDATE `$table_name` SET `$new_comment_name` = `$new_comment_name` | " . Privilege_view_hidden . " where `$old_name` & " . View_invisible);
  }

  public function update_user_group_permissions ($table_name, $new_user_name, $new_group_name, $old_name)
  {
    $this->_query ("UPDATE `$table_name` SET `$new_user_name` = `$new_user_name` | " . Privilege_view . " | " . Privilege_view_history . " | " . Privilege_view_hidden . " where `$old_name` & " . View_user);
    $this->_query ("UPDATE `$table_name` SET `$new_user_name` = `$new_user_name` | " . Privilege_create . " where `$old_name` & " . Create_user);
    $this->_query ("UPDATE `$table_name` SET `$new_user_name` = `$new_user_name` | " . Privilege_modify . " where `$old_name` & " . Modify_user);
    $this->_query ("UPDATE `$table_name` SET `$new_user_name` = `$new_user_name` | " . Privilege_delete . " where `$old_name` & " . Delete_user);
    $this->_query ("UPDATE `$table_name` SET `$new_user_name` = `$new_user_name` | " . Privilege_purge . " where `$old_name` & " . Purge_user);
    $this->_query ("UPDATE `$table_name` SET `$new_user_name` = `$new_user_name` | " . Privilege_secure . " where `$old_name` & " . Secure_user);

    $this->_query ("UPDATE `$table_name` SET `$new_group_name` = `$new_group_name` | " . Privilege_view . " | " . Privilege_view_history . " | " . Privilege_view_hidden . " where `$old_name` & " . View_group);
    $this->_query ("UPDATE `$table_name` SET `$new_group_name` = `$new_group_name` | " . Privilege_create . " where `$old_name` & " . Create_group);
    $this->_query ("UPDATE `$table_name` SET `$new_group_name` = `$new_group_name` | " . Privilege_modify . " where `$old_name` & " . Modify_group);
    $this->_query ("UPDATE `$table_name` SET `$new_group_name` = `$new_group_name` | " . Privilege_delete . " where `$old_name` & " . Delete_group);
    $this->_query ("UPDATE `$table_name` SET `$new_group_name` = `$new_group_name` | " . Privilege_purge . " where `$old_name` & " . Purge_group);
  }

  public function update_folder_permissions ($table_name)
  {
    $this->_query ("UPDATE `$table_name` SET `folder_permissions` = `folder_permissions` | " . Privilege_view . " | " . Privilege_view_history . " where `flags` & " . View_folder);
    $this->_query ("UPDATE `$table_name` SET `folder_permissions` = `folder_permissions` | " . Privilege_create . " where `flags` & " . Create_folder);
    $this->_query ("UPDATE `$table_name` SET `folder_permissions` = `folder_permissions` | " . Privilege_modify . " where `flags` & " . Modify_folder);
    $this->_query ("UPDATE `$table_name` SET `folder_permissions` = `folder_permissions` | " . Privilege_delete . " where `flags` & " . Delete_folder);
    $this->_query ("UPDATE `$table_name` SET `folder_permissions` = `folder_permissions` | " . Privilege_purge . " where `flags` & " . Purge_folder);
    $this->_query ("UPDATE `$table_name` SET `folder_permissions` = `folder_permissions` | " . Privilege_secure . " where `flags` & " . Secure_folder);
    $this->_query ("UPDATE `$table_name` SET `folder_permissions` = `folder_permissions` | " . Privilege_view_hidden . " where `flags` & " . View_invisible);

    $this->_query ("UPDATE `$table_name` SET `comment_permissions` = `comment_permissions` | " . Privilege_view . " | " . Privilege_view_history . " where `flags` & " . View_content);
    $this->_query ("UPDATE `$table_name` SET `comment_permissions` = `comment_permissions` | " . Privilege_create . " where `flags` & " . Create_content);
    $this->_query ("UPDATE `$table_name` SET `comment_permissions` = `comment_permissions` | " . Privilege_modify . " where `flags` & " . Modify_content);
    $this->_query ("UPDATE `$table_name` SET `comment_permissions` = `comment_permissions` | " . Privilege_delete . " where `flags` & " . Delete_content);
    $this->_query ("UPDATE `$table_name` SET `comment_permissions` = `comment_permissions` | " . Privilege_purge . " where `flags` & " . Purge_content);
    $this->_query ("UPDATE `$table_name` SET `comment_permissions` = `comment_permissions` | " . Privilege_view_hidden . " where `flags` & " . View_invisible);

    $this->_query ("UPDATE `$table_name` SET `entry_permissions` = `entry_permissions` | " . Privilege_view . " | " . Privilege_view_history . " where `flags` & " . View_content);
    $this->_query ("UPDATE `$table_name` SET `entry_permissions` = `entry_permissions` | " . Privilege_create . " where `flags` & " . Create_content);
    $this->_query ("UPDATE `$table_name` SET `entry_permissions` = `entry_permissions` | " . Privilege_modify . " where `flags` & " . Modify_content);
    $this->_query ("UPDATE `$table_name` SET `entry_permissions` = `entry_permissions` | " . Privilege_delete . " where `flags` & " . Delete_content);
    $this->_query ("UPDATE `$table_name` SET `entry_permissions` = `entry_permissions` | " . Privilege_purge . " where `flags` & " . Purge_content);
    $this->_query ("UPDATE `$table_name` SET `entry_permissions` = `entry_permissions` | " . Privilege_view_hidden . " where `flags` & " . View_invisible);
  }

  public function update_permissions ($folder_table_name, $user_table_name)
  {
    log_open_block ("Updating permissions tables");
      $this->_query ("ALTER TABLE `$user_table_name` ADD `deny_general_permissions` TINYINT UNSIGNED NOT NULL , ADD `deny_folder_permissions` SMALLINT UNSIGNED NOT NULL ,ADD `deny_comment_permissions` SMALLINT UNSIGNED NOT NULL ,ADD `deny_entry_permissions` SMALLINT UNSIGNED NOT NULL");
      $this->_query ("ALTER TABLE `$user_table_name` ADD `allow_general_permissions` TINYINT UNSIGNED NOT NULL ,ADD `allow_folder_permissions` SMALLINT UNSIGNED NOT NULL ,ADD `allow_comment_permissions` SMALLINT UNSIGNED NOT NULL ,ADD `allow_entry_permissions` SMALLINT UNSIGNED NOT NULL");
      $this->_query ("ALTER TABLE `$folder_table_name` ADD `general_permissions` TINYINT UNSIGNED NOT NULL ,ADD `folder_permissions` SMALLINT UNSIGNED NOT NULL ,ADD `comment_permissions` SMALLINT UNSIGNED NOT NULL ,ADD `entry_permissions` SMALLINT UNSIGNED NOT NULL ;");
      $this->_query ("ALTER TABLE `$user_table_name` ADD `user_permissions` SMALLINT UNSIGNED NOT NULL ,ADD `group_permissions` SMALLINT UNSIGNED NOT NULL ,ADD `global_permissions` TINYINT UNSIGNED NOT NULL ;");
      $this->_query ("ALTER TABLE `$folder_table_name` CHANGE `user_id` `ref_id` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL");
      $this->_query ("ALTER TABLE `$folder_table_name` ADD `kind` ENUM( 'user', 'group', 'registered', 'anonymous' ) DEFAULT 'anonymous' NOT NULL");
    log_close_block ();

    log_open_block ("Migrating permissions");
      $this->_query ("UPDATE `$folder_table_name` SET folder_permissions = 0, entry_permissions = 0, comment_permissions = 0, general_permissions = 0");
      $this->_query ("UPDATE `$user_table_name` SET allow_folder_permissions = 0, allow_entry_permissions = 0, allow_comment_permissions = 0, allow_general_permissions = 0, deny_folder_permissions = 0, deny_entry_permissions = 0, deny_comment_permissions = 0, deny_general_permissions = 0, user_permissions = 0, group_permissions = 0");
      $this->update_user_folder_permissions ($user_table_name, 'deny_folder_permissions', 'deny_flags');
      $this->update_user_folder_permissions ($user_table_name, 'allow_folder_permissions', 'allow_flags');
      $this->update_user_content_permissions ($user_table_name, 'deny_entry_permissions', 'deny_comment_permissions', 'deny_flags');
      $this->update_user_content_permissions ($user_table_name, 'allow_entry_permissions', 'allow_comment_permissions', 'allow_flags');
      $this->update_user_group_permissions ($user_table_name, 'user_permissions', 'group_permissions', 'deny_flags');
      $this->update_user_group_permissions ($user_table_name, 'user_permissions', 'group_permissions', 'allow_flags');
      $this->_query ("UPDATE `$folder_table_name` SET kind = 'registered' WHERE type = " . All_users_type);
      $this->_query ("UPDATE `$folder_table_name` SET kind = 'user' WHERE type = " . User_type);
      $this->_query ("UPDATE `$folder_table_name` SET kind = 'group' WHERE type = " . Group_type);
      $this->update_folder_permissions ($folder_table_name);
    log_close_block ();

    log_open_block ("Cleaning unused fields in permissions tables");
      $this->_query ("ALTER TABLE `$user_table_name` DROP `allow_flags`, DROP `deny_flags`");
      $this->_query ("ALTER TABLE `$folder_table_name `DROP `type`, DROP `flags`");
    log_close_block ();

    log_open_block ("Add attachment permissions...");
      $this->_query ("ALTER TABLE `$user_table_name` ADD `deny_attachment_permissions` SMALLINT UNSIGNED NOT NULL AFTER `deny_entry_permissions` ;");
      $this->_query ("ALTER TABLE `$user_table_name` ADD `allow_attachment_permissions` SMALLINT UNSIGNED NOT NULL AFTER `allow_entry_permissions` ;");
      $this->_query ("ALTER TABLE `$folder_table_name` ADD `attachment_permissions` SMALLINT UNSIGNED NOT NULL AFTER `entry_permissions` ;");
    log_close_block ();
  }

  public function update_subscriptions ($subscribers_table, $subscriptions_table)
  {
    log_open_block ("Updating subcribers table");
      $this->_query ("ALTER TABLE `$subscribers_table` DROP `group_objects`;");
      $this->_query ("ALTER TABLE `$subscribers_table` ADD `max_individual_messages` INT UNSIGNED DEFAULT '3' NOT NULL , ADD `max_items_per_message` INT UNSIGNED DEFAULT '25' NOT NULL , ADD `min_hours_to_wait` INT DEFAULT '24' NOT NULL , ADD `show_actions` TINYINT UNSIGNED DEFAULT '0' NOT NULL , ADD `show_action_as_subject` TINYINT UNSIGNED DEFAULT '0' NOT NULL , ADD `time_messages_sent` DATETIME NOT NULL , ADD `queued_action_ids` TEXT NOT NULL , ADD `preferred_text_length` INT UNSIGNED DEFAULT '0' ;");
    log_close_block ();

    log_open_block ("Updating subscription records");
      $this->_query ("ALTER TABLE `$subscriptions_table` ADD `kind` ENUM( 'folder', 'entry', 'comment', 'user' ) DEFAULT 'folder' NOT NULL AFTER `subscriber_id`");
      $this->_query ("ALTER TABLE `$subscriptions_table` DROP `type`");
    log_close_block ();
  }

  public function update_actions ($actions_table, $comments_table = '', $folders_table = '', $entry_table = '')
  {
    log_open_block ("Updating actions table");
      $this->_query ("ALTER TABLE `$actions_table` CHANGE `kind` `kind` ENUM( 'Created', 'Updated', 'Deleted', 'Restored', 'Hidden', 'Hidden update', 'Locked', 'Published' ) DEFAULT 'Created' NOT NULL");
      $this->_query ("ALTER TABLE `$actions_table` CHANGE `publication_state` `publication_state` ENUM( 'silent', 'sent', 'queued' ) DEFAULT 'silent' NOT NULL");
      $this->_query ("UPDATE `$actions_table` SET publication_state = 'sent' WHERE publication_state = '';");
      $this->_query ("UPDATE `$actions_table` SET kind = 'Published' WHERE kind = '';");
      if ($entry_table)
      {
        $this->_query ("ALTER TABLE `$entry_table` DROP `publication_state`;");
      }
      if ($comments_table)
      {
        $this->_query ("ALTER TABLE `$comments_table` DROP `publication_state`;");
      }
      if ($folders_table)
      {
        $this->_query ("ALTER TABLE `$folders_table` DROP `publication_state`;");
      }
    log_close_block ();
  }

  public function update_drafting ($entry_table)
  {
    log_open_block ("Adding drafting to [$entry_table]");
      $this->_query ("ALTER TABLE $entry_table ADD `publisher_id` INT UNSIGNED NOT NULL;");
      $this->_query ("ALTER TABLE $entry_table ADD `time_published` DATETIME NOT NULL ;");
      $this->_query ("UPDATE $entry_table SET `publisher_id` = `modifier_id` WHERE state <> " . Draft);
      $this->_query ("UPDATE $entry_table SET `time_published` = `time_modified` WHERE state <> " . Draft);
    log_close_block ();
  }

  public function update_users ($users_table)
  {
    log_open_block ("Updating user permissions");
      $this->_query ("UPDATE `$users_table` SET global_permissions = global_permissions | " . Privilege_login);
      $this->_query ("UPDATE `$users_table` SET global_permissions = 255 WHERE user_id = 1");
    log_close_block ();
  }

  public function update_folders ($folders_table)
  {
    log_open_block ("Updating folders table");
      $this->_query ("ALTER TABLE `$folders_table` CHANGE `picture_url` `icon_url` VARCHAR( 250 ) NOT NULL");
    log_close_block ();
  }

  public function add_attachments ($table_name, $action_table_name)
  {
    log_open_block ("Adding attachments...");

$s = <<<EOD
CREATE TABLE $table_name (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
              `object_id` INT UNSIGNED NOT NULL ,
              `state` TINYINT UNSIGNED NOT NULL ,
              `type` ENUM( 'folder', 'entry', 'comment', 'user', 'group' ) NOT NULL ,
              `title` VARCHAR( 200 ) NOT NULL ,
              `description` TEXT NOT NULL ,
              `file_name` VARCHAR( 200 ) NOT NULL ,
              `original_file_name` VARCHAR( 200 ) NOT NULL ,
              `size` INT NOT NULL ,
              `mime_type` VARCHAR( 100 ) NOT NULL ,
              `is_image` TINYINT UNSIGNED NOT NULL ,
              `is_archive` TINYINT UNSIGNED NOT NULL ,
              `time_created` datetime default '0000-00-00 00:00:00',
              `time_modified` datetime default '0000-00-00 00:00:00',
              `creator_id` int(10) unsigned NOT NULL default '0',
              `modifier_id` int(10) unsigned NOT NULL default '0',
              PRIMARY KEY ( `id` )
              );
EOD;

      $this->_query ($s);
      $this->_query ("ALTER TABLE $action_table_name CHANGE `object_type` `object_type` ENUM( 'folder', 'entry', 'comment', 'group', 'user', 'attachment' ) DEFAULT 'folder' NOT NULL");
    log_close_block ();
  }
}

?>