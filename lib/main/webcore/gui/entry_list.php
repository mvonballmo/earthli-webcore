<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.3.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
 * Displayes a multi-columned list of {@link ENTRY}s.
 * Used with the explorer view.
 * @package webcore
 * @subpackage grid
 * @version 3.3.0
 * @since 2.2.1
 */
class ENTRY_LIST extends SELECT_LIST
{
  /**
   * @var string
   * @access private
   */
  public $object_name = 'object';

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);
    $this->append_column ('Name');
    $this->append_column ('Date');
    $this->append_column ('Creator');
  }

  /**
   * Draw the given column's data using the given object.
   * @param ENTRY $obj
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
      echo $obj->title_as_link ($t);
      break;
    case 2:
      $t = $obj->time_created->formatter ();
      $t->type = Date_time_format_short_date;
      echo '<span style="white-space: nowrap">' . $obj->time_created->format ($t) . '</span>';
      break;
    case 3:
      $creator = $obj->creator ();
      echo $creator->title_as_link ();
      break;
    }
  }
}
?>