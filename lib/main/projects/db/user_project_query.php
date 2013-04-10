<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage db
 * @version 3.3.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

/** */
require_once ('webcore/db/user_folder_query.php');

/**
 * Retrieves {@link PROJECT}s visible to a {@link PROJECT_USER}.
 * @package projects
 * @subpackage db
 * @version 3.3.0
 * @since 1.4.1
 */
class USER_PROJECT_QUERY extends USER_FOLDER_QUERY
{
  /**
   * Retrieves the folder containing the {@link BRANCH} for '$id'.
   * If the release does not exist or cannot be seen by this user, nothing is returned.
   * @param integer $id Unique id of the branch to retrieve.
   * @return PROJECT
   */
  public function folder_for_branch_at_id ($id)
  {
    $id = $this->validate_as_integer ($id);
    if ($id)
    {
      $this->db->logged_query ("SELECT folder_id FROM {$this->app->table_names->branches} WHERE id = $id");

      if ($this->db->next_record ())
      {
        return $this->object_at_id ($this->db->f ("folder_id"));
      }
    }
    
    return null;
  }

  /**
   * Retrieves the folder containing the {@link RELEASE} for '$id'.
   * If the release does not exist or cannot be seen by this user, nothing is returned.
   * @param integer $id Unique id of the release to retrieve.
   * @return PROJECT
   */
  public function folder_for_release_at_id ($id)
  {
    $id = $this->validate_as_integer ($id);
    if ($id)
    {
      $this->db->logged_query ("SELECT bra.folder_id FROM {$this->app->table_names->releases} rel INNER JOIN {$this->app->table_names->branches} bra ON bra.id = rel.branch_id WHERE rel.id = $id");

      if ($this->db->next_record ())
      {
        return $this->object_at_id ($this->db->f ("folder_id"));
      }
    }
    return null;
  }

  /**
   * Retrieves the folder containing the {@link COMPONENT} for '$id'.
   * If the component does not exist or cannot be seen by this user, nothing is returned.
   * @param integer $id Unique id of the component to retrieve.
   * @return PROJECT
   */
  public function folder_for_component_at_id ($id)
  {
    $id = $this->validate_as_integer ($id);
    if ($id)
    {
      $this->db->logged_query ("SELECT folder_id FROM {$this->app->table_names->components} WHERE id = $id");

      if ($this->db->next_record ())
      {
        return $this->object_at_id ($this->db->f ("folder_id"));
      }
    }
    return null;
  }

  /**
   * Return whether the history item type is for a content item.
   * @param string $object_type
   * @return boolean
   * @access private
   */
  protected function _is_content_type ($object_type)
  {
    return (parent::_is_content_type ($object_type) ||
            ($object_type == History_item_branch) ||
            ($object_type == History_item_release));
  }
}
?>