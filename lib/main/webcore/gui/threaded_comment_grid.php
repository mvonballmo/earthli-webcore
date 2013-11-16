<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.4.0
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
require_once ('webcore/gui/flat_comment_grid.php');

/**
 * Displays {@link COMMENT}s from a {@link QUERY}, threaded to show nesting.
 * @package webcore
 * @subpackage grid
 * @version 3.4.0
 * @since 2.2.1
 */
class THREADED_COMMENT_GRID extends FLAT_COMMENT_GRID
{
  /**
   * @param array[COMMENT] $objs
   * @access private
   */
  protected function _draw_cells ($objs)
  {
    $depth = 0;
    $this->_draw_comments ($objs, $depth);
  }

  /**
   * Start rendering the grid.
   * @access private
   */
  protected function _start_grid () {}

  /**
   * Finish rendering the grid.
   * @access private
   */
  protected function _finish_grid () {}

  /**
   * Get the list of objects for the requested page.
   * @return array[COMMENT]
   * @access private
   */
  protected function _get_objects ()
  {
    $this->_query->set_order ('time_created ASC');

    if ($this->_comment)
    {
      $Result = $this->_query->tree ($this->_comment->id, $this->_comment->root_id);
      $sub_comments = $Result;  // break the reference deliberately, otherwise there is an infinite loop
      $this->_comment->set_sub_comments ($sub_comments);
      $Result = array ($this->_comment);
    }
    else
    {
      if ($this->show_paginated)
      {
        $this->_query->restrict ('com.root_id = com.id');
        $num_threads = $this->_query->size ();
          // get the number of threads

        if ($num_threads > $this->_num_rows)
        {
          // if there are more threads than will show on one page AND
          // we are not printing (when printing, all threads are displayed)
          
          $num_objects_per_page = $this->_num_rows * $this->_num_columns;
          $this->_query->set_select ('com.id, com.time_created');
          $this->_query->set_limits (($this->pager->page_number - 1) * $num_objects_per_page, $num_objects_per_page);
          $db = $this->_query->raw_output ();

          if ($db)
          {
            $com_ids = array ();
            while ($db->next_record ())
            {
              $com_ids [$db->f ("id")] = $db->f ("id");
            }

            if (sizeof ($com_ids) > 0)
            {
              $keys = array_keys ($com_ids);
              $str_com_ids = implode (", ", $keys);
              $this->_query->clear_restrictions ();
              $this->_query->set_limits (0, 0);  // clear the limits
              $this->_query->set_select ('com.*');
              $this->_query->restrict ("com.root_id IN ($str_com_ids)");
              $Result = $this->_query->tree ();
              $this->_show_pager = true;
            }
          }
        }
        else
        {
          $this->_query->clear_restrictions ();
          $Result = $this->_query->tree ();
        }
      }
      else
      {
        $Result = $this->_query->tree ();
      }
    }

    return $Result;
  }

  /**
   * @param array[COMMENT] $objs
   * @param integer $depth Nesting level.
   * @access private
   */
  protected function _draw_comments ($objs, $depth)
  {
    if ($depth > 0)
    {
?>
    <div class="comment-block">
<?php
    }
    $depth += 1;
    foreach ($objs as &$obj)
    {
      $this->_draw_comment ($obj, $depth);
    }
    $depth -= 1;
    if ($depth > 0)
    {
?>
    </div>
<?php
    }
  }

  /**
   * @param COMMENT $obj
   * @param integer $depth Nesting level.
   * @access private
   */
  protected function _draw_comment ($obj, $depth)
  {
    $this->_draw_comment_contents ($obj);
    $objs = $obj->sub_comments ();  // break the reference deliberately or there is an infinite loop
    if ($objs)
    {
      $this->_draw_comments ($objs, $depth);
    }
  }
}

?>