<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.1.0
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
require_once ('webcore/gui/select_list.php');

/**
 * Display the details of a set of {@link JOB}s.
  * Used primarily in the explorer page.
 * @package projects
 * @subpackage gui
 * @version 3.1.0
 * @since 1.4.1
 */
class JOB_LIST extends SELECT_LIST
{
  /**
   * @var string display jobs only.
   */
  public $object_name = 'job';

  /**
   * @var string Unique name for the selector controls for this list.
   */
  public $control_name = 'job_ids';

  /**
   * @param PROJECT_APPLICATION $app Main application.
   */
  public function JOB_LIST ($app)
  {
    SELECT_LIST::SELECT_LIST ($app);
    $this->append_column ('Name');
    $this->append_column ('Age');
    $this->append_column ('Priority');
    $this->append_column ('Status');
  }

  /**
   * @param JOB $obj Draw fields from this job.
   * @param integer $index Draw the column at this index.
   * @access private
   */
  protected function _draw_column_contents ($obj, $index)
  {
    switch ($index)
    {
    case 0:
      $this->_draw_selector ($obj);
      break;
    case 1:
      $t = $obj->title_formatter ();
      $t->max_visible_output_chars = 0;
      echo $obj->title_as_link ($t);
      break;
    case 2:
      $branch_info = $obj->main_branch_info ();
      $interval = $branch_info->age ();
      echo $interval->format ();
      break;
    case 3:
      $branch_info = $obj->main_branch_info ();
      echo $branch_info->priority_as_text ();
      break;
    case 4:
      $branch_info = $obj->main_branch_info ();
      echo $branch_info->status_as_text ();
      break;
    }
  }
}

?>