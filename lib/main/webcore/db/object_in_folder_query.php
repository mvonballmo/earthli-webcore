<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage db
 * @version 3.4.0
 * @since 2.5.0
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
require_once ('webcore/db/query.php');

/**
 * Base class for queries that return {@link OBJECT_IN_FOLDER} objects.
 * @package webcore
 * @subpackage db
 * @version 3.4.0
 * @since 2.5.0
 * @abstract
 */
abstract class OBJECT_IN_FOLDER_QUERY extends HIERARCHICAL_QUERY
{
  /**
   * Returns if the filter is included in the query.
   * @return boolean
   */
  public function includes ($filter)
  {
    return ($this->_filter & $filter) == $filter;
  }

  /**
   * Specify which objects to return.
   * This works with the {@link OBJECT_IN_OBJECT_IN_FOLDER::$state} property.
   * @param integer $filter
   */
  public function set_filter ($filter)
  {
    $this->_invalidate ();
    $this->_filter = $filter;
  }

  /**
   * Leaves out items with this state.
   * This works with the {@link OBJECT_IN_FOLDER::$state} property.
   * @param integer $filter
   */
  public function filter_out ($filter)
  {
    $this->_invalidate ();
    $this->_filter = $this->_filter & ~$filter;
  }

  /**
   * Prepare security- and filter-based restrictions.
   * @access private
   */
  protected function _prepare_restrictions ()
  {
    $this->assert (isset ($this->_privilege_set), 'Cannot prepare without a permission set', '_prepare_restrictions', 'OBJECT_IN_FOLDER_QUERY');

    parent::_prepare_restrictions ();

    if (! $this->_returns_no_data ())
    {
      $vis = $this->_visible_objects_available ();
      $invis = $this->_invisible_objects_available ();

      if (! $vis || (! $this->includes (Visible) && ! $this->includes (Invisible)))
      {
        $this->_set_returns_no_data ();
      }
      else
      {
        /* if the filter specifies invisible objects, but the user doesn't
           have the right to them, remove it from the filter. */

        if ($this->includes (Invisible) && ! $invis)
        {
          $actual_filter = $this->_filter & ~Invisible;
        }
        else
        {
          $actual_filter = $this->_filter;
        }

        if ($actual_filter == None)
        {
          $this->_set_returns_no_data ();
        }
        elseif ($actual_filter != All)
        {
          $this->_calculated_restrictions [] = $this->_filter_restriction ($actual_filter);
        }
      }
    }
  }

  /**
   * Return whether the user can see visible objects.
   * @return boolean
   * @access private
   */
  protected function _visible_objects_available ()
  {
    $p = $this->login->permissions ();
    return $p->value_for ($this->_privilege_set, Privilege_view) != Privilege_always_denied;
  }

  /**
   * Return whether the user can see invisible objects.
   * @return boolean
   * @access private
   */
  protected function _invisible_objects_available ()
  {
    $p = $this->login->permissions ();
    return $p->value_for ($this->_privilege_set, Privilege_view_hidden) != Privilege_always_denied;
  }

  /**
   * Transform the calculated filter to SQL.
   * Used by {@link restrictions()} to restrict the query by the desired filter.
   * @param string $calculated_filter Actual filter to use.
   * @return string
   * @access private
   */
  protected function _filter_restriction ($calculated_filter)
  {
    return "{$this->alias}.state & {$calculated_filter} = {$this->alias}.state";
  }

  /**
   * Name of the default permission set to use.
   * @var string
   * @access private
   */
  protected $_privilege_set = '';

  /**
   * Filter objects by this state.
   * This is taken as a suggestion by {@link _update()} and can be strengthened to exclude more objects.
   * @var integer
   * @access private
   */
  protected $_filter = All;
}

/**
 * Base class for queries based on a single folder.
 * @package webcore
 * @subpackage db
 * @version 3.4.0
 * @since 2.5.0
 * @abstract
 */
abstract class OBJECT_IN_SINGLE_FOLDER_QUERY extends OBJECT_IN_FOLDER_QUERY
{
  /**
   * Builds a query for an object in a folder.
   * @param FOLDER $folder
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder->app);
    $this->_folder = $folder;
  }

  /**
   * Return whether the user can see visible objects.
   * @return boolean
   * @access private
   */
  protected function _visible_objects_available ()
  {
    return $this->login->is_allowed ($this->_privilege_set, Privilege_view, $this->_folder, $this->login);
  }

  /**
   * Return whether the user can see invisible objects.
   * @return boolean
   * @access private
   */
  protected function _invisible_objects_available ()
  {
    return $this->login->is_allowed ($this->_privilege_set, Privilege_view_hidden, $this->_folder, $this->login);
  }

  /**
   * Perform any setup needed on each returned object.
   * Sets the parent folder for the object.
   * @param OBJECT_IN_FOLDER $obj
   * @access private
   */
  protected function _prepare_object ($obj)
  {
    parent::_prepare_object ($obj);
    $obj->set_parent_folder ($this->_folder);
  }

  /**
   * @var FOLDER
   * @access private
   */
  protected $_folder;
}

?>