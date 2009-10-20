<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.4.1
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

/** */
require_once ('projects/gui/project_entry_navigator.php');

/**
 * Display a list of {@link JOB}s 'around' the current one.
 * @package projects
 * @subpackage gui
 * @version 3.2.0
 * @since 1.4.1
 */
class JOB_NAVIGATOR extends PROJECT_ENTRY_NAVIGATOR
{
  /**
   * Modify the query to navigate.
   * @param QUERY $query
   * @access private
   */
  protected function _adjust_query ($query)
  {
    parent::_adjust_query ($query);
    $query->add_select ('jtob.priority, jtob.status, entry.kind');
    $query->set_type ('job');
  }

  /**
   * @param JOB $obj Retrieve the title from this job.
   * @param boolean $selected Is this the current item in the list?
   * @return string
   * @access private
   */
  protected function _text_for_list ($obj)
  {
    $Result = $obj->kind_icon ('16px') . ' ' . parent::_text_for_list ($obj);
    $branch_info = $obj->main_branch_info ();
    if ($branch_info->is_closed ())
    {
      return "<span class=\"locked\">$Result</span>";
    }
    
    return $Result;
  }

  /**
   * @param JOB $obj Retrieve the title formatter from this job.
   * @return TITLE_FORMATTER
   * @access private
   */
  protected function _formatter_for_object ($obj)
  {
    $Result = parent::_formatter_for_object ($obj);
    $branch_info = $obj->main_branch_info ();
    $Result->title = $branch_info->status_as_text () . ' - ' . $branch_info->priority_as_text ();
     
    return $Result;
  }
}
?>