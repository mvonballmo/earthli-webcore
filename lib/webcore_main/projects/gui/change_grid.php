<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.0.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('projects/gui/project_entry_grid.php');

/**
 * Display {@link CHANGE}s from a {@link QUERY}.
 * @package projects
 * @subpackage gui
 * @version 3.0.0
 * @since 1.4.1
 */
class CHANGE_GRID extends PROJECT_ENTRY_GRID
{
  /**
   * @var string
   */
  var $object_name = 'Change';
  
  /**
   * Draw entry-specific information for the given release.
   * @param CHANGE &$obj
   * @param CHANGE_BRANCH_INFO &$branch_info
   */
  function _draw_release_details (&$obj, &$branch_info)
  {
    $rel =& $branch_info->release ();
    if (isset ($rel))
    {
      $status = $rel->status ();
      echo $status->as_html ();
    }
    else
      echo 'Next release';
  }

  /**
   * Draw user-specific information for the given release.
   * @param CHANGE &$obj
   * @param CHANGE_BRANCH_INFO &$branch_info
   */
  function _draw_user_details (&$obj, &$branch_info)
  {
    echo 'Applied ';
    if ($this->show_user)
    {
      $applier =& $branch_info->applier ();
      echo 'by ' . $applier->title_as_link ();
    }
    echo  ' on ' . $obj->time_created->format ();
  }
  
  /**
   * Draw extra description information for the entry.
   * @param CHANGE &$obj
   * @param CHANGE_BRANCH_INFO &$branch_info
   */
  function _draw_description (&$obj)
  {
    if ($obj->files)
    {
      echo '<h4>' . $obj->num_files () . ' Files</h4>';
      echo $obj->files_as_html ();
    }
  }
}

/**
 * Display {@link CHANGE}s from a {@link QUERY}.
 * @package projects
 * @subpackage gui
 * @version 3.0.0
 * @since 1.4.1
 */
class CHANGE_SUMMARY_GRID extends PROJECT_ENTRY_SUMMARY_GRID
{
  /**
   * @var string
   */
  var $object_name = 'Change';
}

?>