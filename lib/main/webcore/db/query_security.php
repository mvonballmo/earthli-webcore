<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.6.0
 */

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

/** */
require_once ('webcore/obj/webcore_object.php');

/**
 * Restricts a query to a set of user permissions.
 * Used by {@link USER_ENTRY_QUERY} and {@link USER_COMMENT_QUERY} to return
 * only the entries and comments that the logged-in user is allowed to see. Performs
 * all filtering for user- and content-level security.
 *
 * Simply pass the sets that the query must restrict to to {@link as_sql()} to
 * generate a text statement to incorporate into a where clause.
 *
 * @see QUERY::includes()
 * @see QUERY::table_for_set()
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */
class QUERY_SECURITY_RESTRICTION extends WEBCORE_OBJECT
{
  /**
   * @param QUERY &$query Query to which the restriction is applied.
   */
  function QUERY_SECURITY_RESTRICTION (&$query)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($query->context);
    $this->_query =& $query;
  }

  /**
   * Returns an SQL text for the restriction.
   * The privilege sets passed to this function are assumed to be nested levels of security,
   * which can be read from the user's permissions. For example, {@link USER_ENTRY_QUERY}
   * passes {@link Privilege_set_folder} and {@link Privilege_set_entry}. If the user does
   * not have rights to the outer set (folders), then rights to the inner set are not even
   * checked (i.e. denied).
   * @param array[string] List of privilege sets to check.
   * @return string
   */
  function as_sql ($set_names)
  {
    $this->_set_returns_no_data ();
    $this->_generate_sql_for_sets ($set_names);
    return $this->_text;
  }

  /**
   * Incorporate the item's properties into the restriction text.
   * Processing as SQL generates multiple {@link QUERY_SECURITY_RESTRICTION_SET} objects, each
   * of which has one or more {@link QUERY_SECURITY_RESTRICTION_SET_ITEM}. The set applies
   * itself to the restriction by applying all of its items.
   *
   * An item will typically map to a restriction like (fldr.state = 1) AND (entry.state = 1)
   * AND (fldr.id IN (1,2,4,6)).
   *
   * @param QUERY_SECURITY_RESTRICTION_SET_ITEM $item
   */
  function apply_item ($item)
  {
    if (! $this->_returns_all_data ())
    {
      $text = '';

      $needs_folder_filter = sizeof ($this->_vis_sets) || sizeof ($item->sets);
      if ($needs_folder_filter)
        $ids = $this->_load_ids_for ($item);

      if (! $needs_folder_filter || $ids)
      {
        if (sizeof ($item->states))
        {
          $idx_state = 0;
          foreach ($item->states as $state)
          {
            if ($state)
              $parts [] = $this->_format_state ($item, $state, $idx_state);
            $idx_state++;
          }
        }

        if ($needs_folder_filter)
          $parts [] = "fldr.permissions_id IN ($ids)";

        if (isset ($parts))
        {
          if (sizeof ($parts) > 1)
            $text = '(' . implode (') AND (', $parts) . ')';
          else
            $text = $parts [0];

          if ($this->_text)
            $this->_text .= ' OR (' . $text . ')';
          else
            $this->_text = '(' . $text . ')';
        }
        else
          $this->_set_returns_all_data ();
      }
    }
  }

  /**
   * The core routine for generating SQL.
   * This function is called from {@link as_sql()} and builds a chain of
   * {@link QUERY_SECURITY_RESTRICTION_SET}s, which represent the request. These sets
   * are then applied to this restriction, generating the SQL text.
   * @param array[string] $set_names
   * @access private
   */
  function _generate_sql_for_sets ($set_names)
  {
    $this->_set_names = $set_names;
    $permissions = $this->login->permissions ();
    for ($idx_set = 0; $idx_set < sizeof ($set_names); $idx_set++)
      $privs [] = $permissions->value_for ($set_names [$idx_set], Privilege_view);

    if (! in_array (Privilege_always_denied, $privs))
    {
      $this->_vis_sets = array ();
      $this->_vis_privs = array ();
      for ($idx_priv = 0; $idx_priv < sizeof ($privs); $idx_priv++)
      {
        if ($privs [$idx_priv] != Privilege_always_granted)
        {
          $this->_vis_sets [] = $set_names[ $idx_priv ];
          $this->_vis_privs [] = Privilege_view;
        }
      }

      foreach ($set_names as $set_name)
      {
        $privilege = $permissions->value_for ($set_name, Privilege_view_hidden);
        $set = new QUERY_SECURITY_RESTRICTION_SET ($set_name, $privilege);

        if (isset ($last_set))
          $set->set = $last_set;
        $last_set = $set;
      }

      $last_set->apply ($this);
    }
  }

  /**
   * Retrieve the folder ids matching this item.
   * Called from {@link apply_item()} if the restriction is content-based.
   * @param QUERY_SECURITY_RESTRICTION_SET_ITEM $item
   * @access private
   */
  function _load_ids_for ($item)
  {
    $sets = array_merge ($this->_vis_sets, $item->sets);
    $privs = array_merge ($this->_vis_privs, $item->privileges);
    $fq = $this->login->folder_query ();
    return $fq->filtered_ids_as_string ($sets, $privs);
  }

  /**
   * Format the state restriction for a given set.
   * An item may specify that folders and entries must be visible. This function
   * formats that restriction, taking {@link Draft} states into account for entries.
   * @param QUERY_SECURITY_RESTRICTION_SET_ITEM $item
   * @param integer $state
   * @param integer $idx
   * @access private
   */
  function _format_state ($item, $state, $idx)
  {
    $set_name = $this->_set_names [$idx];
    $table_name = $this->_query->table_for_set ($set_name);
    $Result = "$table_name.state & $state";
    if ($set_name == Privilege_set_entry)
    {
      if ($item->include_drafts && ($state == Visible) && $this->_query->includes (Unpublished))
        $Result = "($Result) OR (($table_name.state & " . Unpublished . " = " . Unpublished . ") AND ($table_name.owner_id = {$this->login->id}))";
    }
    return $Result;
  }

  /**
   * Indicate that there is no restriction to apply.
   * The user has rights to see absolutely everything, regardless of state.
   * @see _returns_all_data()
   * @see _set_returns_no_data()
   * @access private
   */
  function _set_returns_all_data ()
  {
    $this->_text = '1';
  }

  /**
   * Return True if the user can see everything.
   * @see _set_returns_all_data()
   * @see _set_returns_no_data()
   * @return boolean
   * @access private
   */
  function _returns_all_data ()
  {
    return $this->_text == '1';
  }

  /**
   * Indicate that the user can see nothing.
   * The user has rights to see none of the requested content, regardless of state.
   * @see _set_returns_all_data()
   * @see _returns_all_data()
   * @access private
   */
  function _set_returns_no_data ()
  {
    $this->_text = '';
  }

  /**
   * List of sets whose visible states must be checked.
   * A list of folder ids is retrieved for which the user has the matching privileges
   * and included in the restriction.
   * @var array[string]
   * @access private
   */
  var $_vis_sets;
  /**
   * List of privileges for the visible states to check.
   * This array is always the same size as {@link $vis_sets} and only ever includes the
   * {@link Privilege_view} flag.
   * @var array[integer]
   * @access private
   */
  var $_vis_privs;
}

/**
 * A possible way a user could see content.
 * Used by {@link QUERY_SECURITY_RESTRICTION} to create a condition under which a
 * user could see content. A {@link QUERY_SECURITY_RESTRICTION_SET} generates a list of
 * these to indicate all of the ways that a user could see content.
 *
 * Each item is passed to the {@link QUERY_SECURITY_RESTRICTION::apply_item()} function
 * to generate SQL for the {@link $states} and {@link $sets} it has.
 *
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */
class QUERY_SECURITY_RESTRICTION_SET_ITEM
{
  /**
   * List of states to restrict.
   * If an entry in this array is non-zero, the state of any matching item must
   * contain the given state (typically matching the {@link Visible} flag).
   * @var array[integer]
   */
  var $states;
  /**
   * List of privilege sets to restrict.
   * A list of folder ids is retrieved for which the user has the matching privileges
   * and included in the restriction.
   * @var array[string]
   */
  var $sets;
  /**
   * List of privileges to restrict.
   * This array is always the same size as {@link $sets} and only ever includes the
   * {@link Privilege_view_hidden} flag.
   * @var array[integer]
   */
  var $privileges;
  /**
   * True if the generator should include {@link Draft}s.
   * If the user's visible and invisible rights are both content-based, then that user's
   * drafts can be included in the result set.
   * @var boolean
   */
  var $include_drafts = FALSE;

  /**
   * @param QUERY_SECURITY_RESTRICTION_SET $set
   * @param integer $state Initial state to include in {@link $states}.
   * @param string $set_name Initial set to include in {@link $sets}.
   */
  function QUERY_SECURITY_RESTRICTION_SET_ITEM ($set, $state = null, $set_name = null)
  {
    $this->update_draft_status ($set);
    $this->sets = array ();
    $this->states = array ();
    $this->privileges = array ();
    if (isset ($state))
      $this->states [] = $state;
    if (isset ($set_name))
      $this->add_set ($set_name);
  }

  /**
   * Add a privilege set to this item.
   * @param string $set_name
   */
  function add_set ($set_name)
  {
    $this->sets [] = $set_name;
    $this->privileges [] = Privilege_view_hidden;
  }

  /**
   * Merge another item into this one.
   * @param QUERY_SECURITY_RESTRICTION_SET_ITEM $other
   */
  function append ($other)
  {
    $this->states = array_merge ($this->states, $other->states);
    $this->sets = array_merge ($this->sets, $other->sets);
    $this->privileges = array_merge ($this->privileges, $other->privileges);
  }

  /**
   * Sets whether drafts should be included for this item.
   * @param QUERY_SECURITY_RESTRICTION_SET $set
   */
  function update_draft_status ($set)
  {
    $this->include_drafts |= ($set->name == Privilege_set_entry) &&
                             ($set->privilege == Privilege_controlled_by_content);
  }
}

/**
 * Used to generate a list of {@link QUERY_SECURITY_RESTRICTION_SET_ITEM}s.
 * Used by {@link QUERY_SECURITY_RESTRICTION} to create a set of conditions under which a
 * user could see content. These sets are nested within one another and generate a list
 * of {@link QUERY_SECURITY_RESTRICTION_SET_ITEM}s, which are then applied to the restriction
 * to generate the final SQL.
 *
 * The restriction calls {@link apply()} to generate the items.
 *
 * @package webcore
 * @subpackage db
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */
class QUERY_SECURITY_RESTRICTION_SET
{
  /**
   * Nested restriction set.
   * If assigned, this set's settings are applied recursively to the items
   * generated by this nested set.
   * @var QUERY_SECURITY_RESTRICTION_SET
   */
  var $set;
  /**
   * Name of the privilege set.
   * @var string
   */
  var $name;
  /**
   * Kind of access the user has for objects in this set.
   * @var integer
   */
  var $privilege;

  /**
   * @param string $name
   * @param integer $privielege
   */
  function QUERY_SECURITY_RESTRICTION_SET ($name, $privilege)
  {
    $this->name = $name;
    $this->privilege = $privilege;
  }

  /**
   * Generate required items and apply to this restriction.
   * @param QUERY_SECURITY_RESTRICTION
   */
  function apply (&$res)
  {
    if ($this->privilege == Privilege_always_granted)
      $this->_apply_items ($res, TRUE, 0);
    else
      $this->_apply_items ($res, TRUE, Visible);

    if ($this->privilege != Privilege_always_denied)
      $this->_apply_items ($res, FALSE, 0);
  }

  /**
   * Get a set of items and apply to the restriction.
   * Used internally by {@link apply()}
   * @param QUERY_SECURITY_RESTRICTION
   * @param boolean $is_vis Generate items for visible objects?
   * @param integer $state State to apply for this item at this level.
   * @access private
   */
  function _apply_items (&$res, $is_vis, $state)
  {
    $items = $this->_items_for ($is_vis, $state);
    foreach ($items as $item)
      $res->apply_item ($item);
  }

  /**
   * Get a set of items.
   * Used internally by {@link apply()}
   * @param boolean $is_vis Generate items for visible objects?
   * @param integer $state State to apply for this item at this level.
   * @access private
   */
  function _items_for ($is_vis, $state)
  {
    if (! isset ($this->set))
      return $this->_items ($is_vis, ! $is_vis);
    else
    {
      $items = $this->set->_all_items ();
      if (sizeof ($items))
      {
        foreach ($items as $item)
        {
          $item->states [] = $state;
          if (! $is_vis && ($this->privilege == Privilege_controlled_by_content))
            $item->add_set ($this->name);
          $item->update_draft_status ($this);
          $Result [] = $item;
        }
      }
      else
        $Result = $items;

      return $Result;
    }
  }

  /**
   * Gather list of all items recursively.
   * Gets all items, regardless of state and type. Returns {@link _items()} if
   * there is no nested {@link $set}.
   * Used internally by {@link _items_for()}.
   * @access private
   */
  function _all_items ()
  {
    $items = $this->_items ();

    if (isset ($this->set))
    {
      $other_items = $this->set->_all_items ();

      foreach ($items as $item)
      {
        foreach ($other_items as $other_item)
        {
          $other_clone = clone_object($other_item);
          $other_clone->append ($item);
          $Result [] = $other_clone;
        }
      }
    }
    else
      $Result = $items;

    return $Result;
  }

  /**
   * Gather list of items for this set only.
   * Gets all items, regardless of state and type.
   * Used internally by {@link _items_for()}
   * @param boolean $include_vis Include states and sets for visible objects?
   * @param boolean $include_invis Include states and sets for hidden objects?
   * @access private
   */
  function _items ($include_vis = TRUE, $include_invis = TRUE)
  {
    $Result = array ();

    switch ($this->privilege)
    {
    case Privilege_always_granted:
      if ($include_vis)
        $Result [] = new QUERY_SECURITY_RESTRICTION_SET_ITEM ($this, 0);
      break;
    case Privilege_always_denied:
      if ($include_invis)
        $Result [] = new QUERY_SECURITY_RESTRICTION_SET_ITEM ($this, Visible);
      break;
    case Privilege_controlled_by_content:
      if ($include_vis)
        $Result [] = new QUERY_SECURITY_RESTRICTION_SET_ITEM ($this, Visible);
      if ($include_invis)
        $Result [] = new QUERY_SECURITY_RESTRICTION_SET_ITEM ($this, 0, $this->name);
      break;
    }

    return $Result;
  }
}

?>