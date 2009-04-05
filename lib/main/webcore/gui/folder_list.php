<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.1.0
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
require_once ('webcore/gui/select_list.php');

/**
 * Show {@link FOLDER}s in a selectable, tabular list.
 * @package webcore
 * @subpackage grid
 * @version 3.1.0
 * @since 2.2.1
 */
class FOLDER_LIST extends SELECT_LIST
{
  /**
   * @var string
   */
  public $object_name = 'folder';

  /**
   * @var string
   */
  public $control_name = 'folder_ids';

  /**
   * Link folders to this page.
   * This redirects folders to this page instead of their default home page.
   * @var string
   */
  public $page_name = '';

  /**
   * @param APPLICATION $app Main application.
   */
  public function FOLDER_LIST ($app)
  {
    SELECT_LIST::SELECT_LIST ($app);
    $this->append_column ('Name');
    $this->append_column ('Summary');
  }

  /**
   * Draw the given column's data using the given object.
   * @param FOLDER $obj
   * @param integer $index
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
      if ($this->page_name)
      {
        $t->set_name ($this->page_name);
      }
      echo $obj->title_as_link ($t);
      break;
    case 2:
      $t = $obj->html_formatter ();
      $t->force_paragraphs = false;
      echo $obj->summary_as_html ($t);
    }
  }
}
?>