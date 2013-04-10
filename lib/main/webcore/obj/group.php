<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.3.0
 * @since 2.2.1
 */

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

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/obj/content_object.php');

/**
 * Contains {@link USER}s and can have {@link PERMISSIONS} in {@link FOLDER}s.
 * @package webcore
 * @subpackage obj
 * @version 3.3.0
 * @since 2.2.1
 */
class GROUP extends CONTENT_OBJECT
{
  /**
   * Query for the users in this group.
   * @return GROUP_USER_QUERY
   */
  public function user_query ()
  {
    $class_name = $this->app->final_class_name ('GROUP_USER_QUERY', 'webcore/db/group_user_query.php');
    return new $class_name ($this);
  }

  /**
   * Add this user to the group.
   * Does not check whether the user is already a member of the group.
   * @param USER $user
   */
  public function add_user ($user)
  {
    $this->db->logged_query ("INSERT INTO {$this->app->table_names->users_to_groups} (group_id, user_id)" .
                             " VALUES ($this->id, $user->id)");

    // update the history with the change

    $history_item = $this->new_history_item ();
    $history_item->kind = History_item_updated;
    $history_item->record_difference ('Added [' . $user->title_as_plain_text () . '].');
    $history_item->store ();

    $history_item = $user->new_history_item ();
    $history_item->kind = History_item_updated;
    $history_item->record_difference ('Added to group [' . $this->title_as_plain_text () . ']');
    $history_item->store ();
  }

  /**
   * Delete this user from the group.
   * Does not check whether the user is a member of the group.
   * @param USER $user
   */
  public function remove_user ($user)
  {
    $this->db->logged_query ("DELETE FROM {$this->app->table_names->users_to_groups} WHERE user_id = $user->id");

    // update the history with the change

    $history_item = $this->new_history_item ();
    $history_item->kind = History_item_updated;
    $history_item->record_difference ('Removed [' . $user->title_as_plain_text () . '].');
    $history_item->store ();

    $history_item = $user->new_history_item ();
    $history_item->kind = History_item_updated;
    $history_item->record_difference ('Removed from group [' . $this->title_as_plain_text () . ']');
    $history_item->store ();
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->app->page_names->group_home;
  }
  
  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->app->table_names->groups;
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
   * @return object
   * @access private
   */
  protected function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_commands:
        include_once ('webcore/cmd/group_commands.php');
        return new GROUP_COMMANDS ($this);
      case Handler_history_item:
        include_once ('webcore/obj/webcore_history_items.php');
        return new GROUP_HISTORY_ITEM ($this->app);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }
}

?>