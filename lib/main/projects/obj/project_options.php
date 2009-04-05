<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage obj
 * @version 3.1.0
 * @since 1.4.1
 * @access private
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

require_once ('webcore/obj/folder_inheritable_settings.php');

/**
 * Inheritable options for {@link PROJECT}s.
 * @see PROJECT::options()
 * @package projects
 * @subpackage obj
 * @version 3.1.0
 * @since 1.4.1
 * @access private
 */
class PROJECT_OPTIONS extends FOLDER_INHERITABLE_SETTINGS
{
  /**
   * Which set of users are allowed to be assignees in this project?
   * @var integer
   * @see Project_user_all
   * @see Project_user_registered_only
   * @see Project_user_group
   */
  public $assignee_group_type;

  /**
   * Unique id of the group that contains the possible assignees.
   * Used only if {@link $assignee_group_type} is {@link Project_user_group}.
   * @var integer
   */
  public $assignee_group_id;

  /**
   * Which set of users are allowed to be reporters in this project?
   * @var integer
   * @see Project_user_all
   * @see Project_user_registered_only
   * @see Project_user_group
   */
  public $reporter_group_type;

  /**
   * Unique id of the group that contains the possible reporters.
   * Used only if {@link $reporter_group_type} is {@link
   * Project_user_group}.
   * @var integer
   */
  public $reporter_group_id;

  /**
   * In non-zero, shows release deadline warnings.
   * The warning is issued if the deadline is in less than this many seconds.
   * @var integer
   */
  public $seconds_until_deadline;

  /**
   * Do not create an history item for the creating folder.
   * @var boolean
   * @access private
   */
  public $create_history_item_for_self = false;

  /**
   * Assignees are selected from this user group.
   * This is only valid when the {@link $assignee_group_type} is {@link Project_user_group}.
   * @return GROUP
   */
  public function assignee_group ()
  {
    if (! isset ($this->_assignee_group))
    {
      $query = $this->app->group_query ();
      $this->_assignee_group = $query->object_at_id ($this->assignee_group_id);
    }
    return $this->_assignee_group;
  }

  /**
   * Reporters are selected from this user group.
   * This is only valid when the {@link $reporter_group_type} is {@link
   * Project_user_group}.
   * @return GROUP
   */
  public function reporter_group ()
  {
    if (! isset ($this->_reporter_group))
    {
      $query = $this->app->group_query ();
      $this->_reporter_group = $query->object_at_id ($this->reporter_group_id);
    }
    return $this->_reporter_group;
  }

  /**
   * Query returning potential job assignees.
   * Depends on the setting stored in {@link $assignee_group_type}.
   * @return USER_QUERY
   */
  public function assignee_query ()
  {
    return $this->_user_query_for ($this->assignee_group_type, $this->assignee_group_id);
  }
  
  /**
   * Query returning potential job reporters.
   * Depends on the setting stored in {@link $reporter_group_type}.
   * @return USER_QUERY
   */
  public function reporter_query ()
  {
    return $this->_user_query_for ($this->reporter_group_type, $this->reporter_group_id);
  }
  
  /**
   * Return true if reporter and assigner lists are the same.
   * @return boolean
   */
  public function reporters_equals_assigners ()
  {
    return ($this->assignee_group_type == $this->reporter_group_type) 
        && ($this->assignee_group_id == $this->reporter_group_id);
  }
  
  /**
   * Return the release deadline warning units as text.
   * @return string
   */
  public function release_warning_description ()
  {
    if (! $this->seconds_until_deadline)
    {
      return 'None';
    }
     
    switch ($this->seconds_until_deadline)
    {
    case 86400:
      return 'one day';
    case 2 * 86400:
      return 'two days';
    case 3 * 86400:
      return 'three days';
    case 5 * 86400:
      return 'five days';
    case 7 * 86400:
      return 'one week';
    case 14 * 86400:
      return 'two weeks';
    case 30 * 86400:
      return 'one month';
    default:
      return $this->seconds_until_deadline . ' seconds';
    }
  }
  
  /**
   * Query returning potential user set.
   * Depends on the setting passed in to "type".
   * @return USER_QUERY
   * @access private
   */
  protected function _user_query_for ($type, $group_id)
  {
    switch ($type)
    {
    case Project_user_all:
      $Result = $this->app->user_query ();
      break;
    case Project_user_registered_only:
      $Result = $this->app->user_query ();
      $Result->set_kind (Privilege_kind_registered);
      break;
    case Project_user_group:
      $group_query = $this->app->group_query ();
      $group = $group_query->object_at_id ($group_id);
      if (! empty ($group))
      {
        $Result = $group->user_query ();
      }
      break;
    }
    return $Result;
  }

  /**
   * @param DATABASE $db
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->assignee_group_type = $db->f ('assignee_group_type');
    $this->assignee_group_id = $db->f ('assignee_group_id');
    $this->reporter_group_type = $db->f ('reporter_group_type');
    $this->reporter_group_id = $db->f ('reporter_group_id');
    $this->seconds_until_deadline = $db->f ('seconds_until_deadline');
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname = $this->_settings_table_name ();
    $storage->add ($tname, 'assignee_group_type', Field_type_integer, $this->assignee_group_type);
    $storage->add ($tname, 'assignee_group_id', Field_type_integer, $this->assignee_group_id);
    $storage->add ($tname, 'reporter_group_type', Field_type_integer, $this->reporter_group_type);
    $storage->add ($tname, 'reporter_group_id', Field_type_integer, $this->reporter_group_id);
    $storage->add ($tname, 'seconds_until_deadline', Field_type_integer, $this->seconds_until_deadline);
  }

  /**
   * Title for a folder's history item for inheriting this option.
   * @param boolean $adding Is the option being added?
   * @return string
   * @access private
   */
  protected function _history_item_title ($adding)
  {
    return 'Options inheritance changed';
  }

  /**
   * Description for a folder's history item for inheriting this option.
   * @param boolean $adding Is the option being added?
   * @return string
   * @access private
   */
  protected function _history_item_description ($adding, $folder)
  {
    return 'Project options are now inherited from ' . $folder->title_as_plain_text () . '.';
  }

  /**
   * Name of the table in which settings are stored.
   * @return string
   * @access private
   */
  protected function _settings_table_name ()
  {
    return $this->app->table_names->folder_options;
  }

  /**
   * Name of the object field in the folder and database.
   * @var string
   * @access private
   */
  protected $_field_name = 'options_id';

  /**
   * @see assignee_group()
   * @var GROUP
   * @access private
   */
  protected $_assignee_group;

  /**
   * @see reporter_group()
   * @var GROUP
   * @access private
   */
  protected $_reporter_group;
}

?>