<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.2.0
 * @since 2.2.1
 */

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

/** */
require_once ('webcore/db/object_in_folder_query.php');

/**
 * Return {@link FOLDER}s visible to a {@link USER}.
 * @package webcore
 * @subpackage db
 * @version 3.2.0
 * @since 2.2.1
 */
class USER_FOLDER_QUERY extends OBJECT_IN_FOLDER_QUERY
{
  /**
   * SQL alias for the "main" table.
   * @var string
   */
  public $alias = 'fldr';

  /**
   * @param USER $user The user for which folders are to be retrieved.
   */
  public function __construct ($user)
  {
    parent::__construct ($user->app);

    /* Folders may be loaded as another query is executing;
     * make sure not to execute in the existing connection.
     */
    $this->ensure_has_own_database_connection ();
    
    $this->_user = $user;
    $this->_user->load_permissions (); // Make sure permissions are available
  }

  /**
   * Apply default restrictions and tables.
   */
  public function apply_defaults ()
  {
    $this->set_select ("perm.*, fldr.*");
    $this->set_order ('fldr.title');
    $this->_add_sorting_for_security ();
  }

  /**
   * @return array[FOLDER]
   */
  public function objects ()
  {
    if (! isset ($this->_objects))
    {
      $this->_used_ids = array ();
      $this->_num_folders_counted = 0;
    }

    return parent::objects ();
  }

  /**
   * Provides additional filtering of indexed object ids.
   * @see QUERY::indexed_ids_as_string()
   * @param array[string] $set_names array of permission sets to restrict.
   * @param array[integer] $types Permission(s) to restrict.
   * @return string
   * @access private
   */
  public function filtered_ids_as_string ($set_names, $types)
  {
    return $this->_filtered_data ('indexed_ids_as_string', $set_names, $types);
  }

  /**
   * Provides additional filtering of indexed objects.
   * @see QUERY::indexed_objects()
   * @param array[string] $set_names array of permission sets to restrict.
   * @param array[integer] $types Permission(s) to restrict.
   * @return array[object]
   * @access private
   */
  public function filtered_objects ($set_names, $types)
  {
    return $this->_filtered_data ('indexed_objects', $set_names, $types);
  }

  /**
   * Get the folder for the specified entry id.
   * @param integer $id
   * @return FOLDER
   */
  public function folder_for_entry_at_id ($id)
  {
    $id = $this->validate_as_integer ($id);
    if ($id)
    {
      $this->db->logged_query ("SELECT folder_id FROM {$this->app->table_names->entries} WHERE id = $id");

      if ($this->db->next_record ())
      {
        return $this->object_at_id ($this->db->f ("folder_id"));
      }
    }
    
    return null;
  }

  /**
   * Get the folder for the specified comment id.
   * @param integer $id
   * @return FOLDER
   */
  public function folder_for_comment_at_id ($id)
  {
    $id = $this->validate_as_integer ($id);
    if ($id)
    {
      $this->db->logged_query (
        "SELECT folder_id FROM {$this->app->table_names->entries} entry" .
        " INNER JOIN {$this->app->table_names->comments} com on com.entry_id = entry.id" .
        " WHERE com.id = $id"
      );

      if ($this->db->next_record ())
      {
        return $this->object_at_id ($this->db->f ('folder_id'));
      }
    }

    return null;
  }

  /**
   * Get the folder for this attachment id and type.
   * @param integer $id
   * @param string $type Can be {@link History_item_entry}, {@link History_item_comment}, {@link History_item_folder}, {@link History_item_user} or {@link History_item_group}.
   * @return FOLDER
   */
  public function folder_for_attachment_at_id ($id, $type)
  {
    $id = $this->validate_as_integer ($id);
    if ($id)
    {
      $type_known = true;
      switch ($type)
      {
      case History_item_entry:
        $this->db->logged_query (
          "SELECT folder_id FROM {$this->app->table_names->entries} host" .
          " INNER JOIN {$this->app->table_names->attachments} a on a.object_id = host.id" .
          " WHERE a.id = $id"
        );
        break;
      case History_item_comment:
        $this->db->logged_query (
          "SELECT folder_id FROM {$this->app->table_names->entries} entry" .
          " INNER JOIN {$this->app->table_names->comments} host on host.entry_id = entry.id" .
          " INNER JOIN {$this->app->table_names->attachments} a on a.object_id = host.id" .
          " WHERE a.id = $id"
        );
        break;
      case History_item_folder:
        $this->db->logged_query (
          "SELECT host.id as folder_id FROM {$this->app->table_names->folders} host" .
          " INNER JOIN {$this->app->table_names->attachments} a on a.object_id = host.id" .
          " WHERE a.id = $id"
        );
        break;
      default:
        $type_known = false;
      }

      if ($type_known && $this->db->next_record ())
      {
        return $this->object_at_id ($this->db->f ('folder_id'));
      }
    }
    
    return null;
  }

  /**
   * @return FOLDER
   * @access private
   */
  protected function _make_object ()
  {
    $class_name = $this->app->final_class_name ('FOLDER', 'webcore/obj/folder.php');
    return new $class_name ($this->app);
  }

  /**
   * Prepare security- and filter-based restrictions.
   * @access private
   */
  protected function _prepare_restrictions ()
  {
    parent::_prepare_restrictions ();

    if (! $this->_returns_no_data ())
    {
      $p = $this->_user->permissions ();
      $vis = $p->value_for (Privilege_set_folder, Privilege_view);
      $invis = $p->value_for (Privilege_set_folder, Privilege_view_hidden);

      /* The all-denied case is already checked in OBJECT_IN_FOLDER_QUERY. */

      if ($vis == Privilege_always_granted)
      {
        // all visible folders are returned, regardless of folder permissions

        $join_type = "LEFT";

        // All folders are returned, filtered by user request

        if ($invis == Privilege_always_granted)
        {
          if ($this->_filter != All)
          {
            $usable = "fldr.state & $this->_filter = fldr.state";
          }
          else
          {
            $usable = '1';
          }
        }
        else
        {
          if ($invis == Privilege_always_denied)
          {
            // all visible folders are returned, regardless of folder permissions
            // all invisible folders are blocked, regardless of folder permissions
            
            $usable = "fldr.state & " . ($this->_filter & ~Invisible) . " = fldr.state";
              // Disallow any invisible folders
          }
          else
          {
            // Return all visible folders and only invisible folders with folder-level permissions
            
            if ($this->_filter & Visible)
            {
                if ($this->_filter & Invisible)
                {
                  $usable = "((fldr.state & " . Visible . ")" .
                            " OR ((fldr.state & " . Invisible . ") AND (perm.folder_permissions & " . Privilege_view_hidden . ")))" .
                            " AND (fldr.state & $this->_filter = fldr.state)";
                }
                else
                {
                  $usable = "(fldr.state & $this->_filter)";
                }
            }
            else
            {
              if ($this->_filter & Invisible)
              {
                $usable = "((fldr.state & $this->_filter = fldr.state) AND (perm.folder_permissions & " . Privilege_view_hidden . "))";
              }
              else
              {
                $this->raise ('Unreachable state (logic error)', '_prepare_restrictions', 'USER_FOLDER_QUERY');
              }
            }
          }
        }
      }
      else
      {
        // only those folders for which folder permissions allow this user are returned
        
        $join_type = "INNER";

        if ($invis == Privilege_always_denied)
        {
          // no invisible folders are returned, regardless of folder permissions

          $usable = "(fldr.state & " . ($this->_filter & ~Invisible) . " = fldr.state) AND (perm.folder_permissions & " . Privilege_view . ")";
        }
        else
        {
          if ($invis == Privilege_always_granted)
          {
            // all folder-granted visible folders and all invisible folders, regardless
            // of whether invisible is granted (view_folder is sufficient)
            
            $usable = "(perm.folder_permissions & " . Privilege_view . ") AND (fldr.state & $this->_filter = fldr.state)";
          }
          else
          {
            // no user-rights case: all visible folders with view_folder permissions
          }
            // all invisible folders with view_folder, view_invisible permissions
          {
            if ($this->_filter & Visible)
            {
              if ($this->_filter & Invisible)
              {
                $usable = "(perm.folder_permissions & " . Privilege_view . ") AND ((fldr.state & " . Visible . ")" .
                          " OR ((fldr.state & " . Invisible . ") AND (perm.folder_permissions & " . Privilege_view_hidden .")))" .
                          " AND (fldr.state & $this->_filter = fldr.state)";
              }
              else
              {
                $usable = "(perm.folder_permissions & " . Privilege_view . ") AND (fldr.state & $this->_filter = fldr.state)";
              }
            }
            else
            {
              if ($this->_filter & Invisible)
              {
                $usable = "(perm.folder_permissions & " . Privilege_view . ") AND (perm.folder_permissions & " . Privilege_view_hidden . ") AND (fldr.state & $this->_filter = fldr.state)";
              }
              else
              {
                $this->raise ('Unreachable state (logic error)', '_prepare_restrictions', 'USER_FOLDER_QUERY');
              }
            }
          }
        }
      }

      if (! $this->_returns_no_data ())
      {
        $this->add_select ("($usable) AS usable");
        
        $this->_usable_restriction = $usable;
        
        $this->_tables = "{$this->app->table_names->folders} fldr" .
                         " $join_type JOIN {$this->app->table_names->folder_permissions} perm ON perm.folder_id = fldr.permissions_id" .
                         " LEFT JOIN {$this->app->table_names->groups} grp on perm.ref_id = grp.id" .
                         " LEFT JOIN {$this->app->table_names->users_to_groups} utg ON utg.group_id = grp.id";

        if ($this->_num_records)
        {
          // this query is special and can't use the LIMIT function
          // so record the number of records elsewhere. The record
          // count will be limited with '_is_valid_object'
          
          $this->_num_folders = $this->_num_records;
          $this->_first_folder = $this->_first_record;
          $this->_num_records = 0;
        }

        if ($this->_user->is_anonymous ())
        {
          $global_type = Privilege_kind_anonymous;
        }
        else
        {
          $global_type = Privilege_kind_registered;
        }

        $user_id = $this->_user->id;

        $this->_calculated_restrictions [] =  "(perm.kind = '$global_type')" .
                                              " OR ((perm.kind = '" . Privilege_kind_user . "') AND (perm.ref_id = $user_id))" .
                                              " OR ((perm.kind = '" . Privilege_kind_group . "') AND (perm.ref_id = grp.id) AND (utg.user_id = $user_id))";
      }
    }
  }

  /**
   * Ensures ordering by security relevance.
   * @access private */  
  protected function _add_sorting_for_security ()
  {
    $this->add_order ('perm.kind, perm.importance DESC');
  }

  /**
   * Internal function for filtering output.
   * @param array[string] $set_names array of permission sets to restrict.
   * @param array[integer] $types Permission(s) to restrict.
   * @return array[object]
   * @access private
   */
  protected function _filtered_data ($func_name, $set_names, $types)
  {
    $arrays_match = is_array ($set_names) && is_array ($types) && (sizeof ($types) == sizeof ($set_names)); 
    $this->assert ($arrays_match, 'Set name and types must be arrays of equal size.', '_filtered_data', 'USER_FOLDER_QUERY');

    /* Always regenerate the filtered list to make sure the proper filter
       has been applied. Clear the generated list internally so that it isn't
       returned when calling 'indexed_objects'. */

    $this->_indexed_objects = null;
    $this->_filter_by_sets = $set_names;
    $this->_filter_by_types = $types;
    $this->_calculated_restrictions [] =  'fldr.id = fldr.permissions_id';

/*
    $db = $this->raw_output ();
    while ($db->next_record ())
    {
      if ($this->_is_valid_object ($db))
      {
        $ids [] = $db->f ('id');
      }
    }
    
    if (isset ($ids))
    {
      $Result = implode (", ", $ids);
    }
    else
    {
      $Result = '';
    }
*/
    $Result = $this->$func_name ();

    $this->_filter_by_sets = null;
    $this->_filter_by_type = null;
    $this->_indexed_objects = null;

    return $Result;
  }

  /**
   * @param FOLDER $obj
   * @return boolean
   * @access private
   */
  protected function _is_indexable_object ($obj)
  {
    $Result = ! isset ($this->_filter_by_sets);
    if (! $Result)
    {
      $Result = $obj->defines_security ();
      if ($Result)
      {
        $idx_set = 0;
        while ($Result && ($idx_set < sizeof ($this->_filter_by_sets)))
        {
          $Result = $this->_user->is_allowed ($this->_filter_by_sets [$idx_set], $this->_filter_by_types [$idx_set], $obj);
          $idx_set += 1;
        }
      }
    }
    return $Result;
  }

  /**
   * @return string
   * @access private
   */
  protected function _count_command_as_SQL ()
  {
    return 'SELECT COUNT(DISTINCT ' . $this->alias . '.' . $this->id . ') FROM ' . $this->_tables;
  }

  /**
   * @access private
   */
  protected function _invalidate ()
  {
    parent::_invalidate ();
    $this->_used_ids = null;
  }

  /**
   * Should this object be added to the current result set?
   * The query returns a list of folder information. It's very likely that a
   * folder will show up multiple times in the list. The list is sorted by the
   * folder id and sorted so that the first row is the one that *most* applied
   * to the user. Therefore we use the first row for a folder id, then ignore
   * subsequent rows until the id changes. Additionally, the row contains a
   * 'usable' field which indicates whether the user is allowed to use this
   * folder. This addresses the possibility that the most applicable permissions
   * *exclude* a user from seeing it.
   * @param DATABASE $db
   * @return bool
   * @access private
   */
  protected function _is_valid_object ($db)
  {
    $id = $db->f ("id");
    $usable = $db->f ("usable");
    $Result = $usable && ! isset ($this->_used_ids [$id]);

    if ($Result)
    {
      $this->_used_ids [$id] = $id;
      if ($this->_num_folders)
      {
        if ($this->_num_folders_counted < $this->_first_folder)
        {
          $this->_num_folders_counted += 1;
        }
        else
        {
          $this->_num_folders_counted += 1;
          $Result = $this->_num_folders_counted <= $this->_first_folder + $this->_num_folders;
        }
      }
    }
    
    return $Result;
  }

  /**
   * Return the list of restrictions for calculating size.
   * @see _prepare_restrictions()
   * @return array[string]
   * @access private
   */
  protected function _count_restrictions ()
  {
    $Result = parent::_count_restrictions ();
    $Result [] = $this->_usable_restriction;
    return $Result;
  }

  /**
   * @param FOLDER $parent
   * @param FOLDER $obj
   * @access private
   */
  protected function _obj_connect_to_parent ($parent, $obj)
  {
    $parent->add_sub_folder ($obj);
  }

  /**
   * @param FOLDER $obj
   * @access private
   */
  protected function _obj_set_sub_objects_cached ($obj)
  {
    $obj->set_sub_folders_cached ();
  }

  /**
   * @return array[FOLDER]
   * @param FOLDER $obj
   * @access private
   */
  protected function _obj_sub_objects ($obj)
  {
    return $obj->sub_folders ();
  }

  /**
   * Return whether the user can see visible objects.
   * @return boolean
   * @access private
   */
  protected function _visible_objects_available ()
  {
    $p = $this->_user->permissions ();
    return $p->value_for (Privilege_set_folder, Privilege_view) != Privilege_always_denied;
  }

  /**
   * Return whether the user can see visible objects.
   * @return boolean
   * @access private
   */
  protected function _invisible_objects_available ()
  {
    $p = $this->_user->permissions ();
    return $p->value_for (Privilege_set_folder, Privilege_view_hidden) != Privilege_always_denied;
  }

  /**
   * Return whether the history item type is for a content item.
   * @param string $object_type
   * @return boolean
   * @access private
   */
  protected function _is_content_type ($object_type)
  {
    switch ($object_type)
    {
    case History_item_folder:
    case History_item_entry:
    case History_item_comment:
      return true;
    default:
      return false;
    }
  }

  /**
   * The user to use for access control.
   * 
   * @var USER
   */
  private $_user;
  
  /**
   * @var integer
   */
  protected $_num_folders = 0;

  /**
   * @var array[string]
   * @see _filtered_data()
   */
  protected $_filter_by_sets;

  /**
   * @var integer
   * @see filtered_objects()
   */
  protected $_filter_by_type;

  /**
   * Name of the default permission set to use.
   * @var string
   */
  protected $_privilege_set = Privilege_set_folder;
}

?>