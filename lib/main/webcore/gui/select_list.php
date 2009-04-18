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
require_once ('webcore/gui/list_grid.php');

/**
 * Tabular grid with row selection.
 * A grid specialized to draw columns of information within each cell and offer a selector for
 * multiple rows (commonly implemented with a checkbox on each row).
 * @package webcore
 * @subpackage grid
 * @version 3.1.0
 * @since 2.2.1
 */
class SELECT_LIST extends LIST_GRID
{
  /**
   * Image used for the select/deselect all button.
   * Looks for the image in the {@link Folder_name_icons} folder of the {@link
   * APPLICATION}. Extension is also filled in by the theme or the application.
   * @var string
   */
  public $toggle_image = '{icons}tree/collapse';

  /**
   * Title used for the select/deselect all button.
   * @var string
   */
  public $toggle_title = 'Select/Deselect All';

  /**
   * Name of the form embedded in the list.
   * This form is used to submit the selected rows to a worker page.
   * @var string
   */
  public $form_name = 'update_form';

  /**
   * Name assigned to the selector controls.
   * @var string
   */
  public $control_name = 'entry_ids';

  /**
   * @access private
   */
  protected function _draw_header ()
  {
    $col = '<div style="text-align: center">';
    $col .= "<a href=\"#\" onclick=\"toggle_selected (document.getElementById('$this->form_name')['{$this->control_name}[]'])\" title=\"$this->toggle_title\">";
    if ($this->toggle_image)
    {
      $col .= $this->app->resolve_icon_as_html ($this->toggle_image, $this->toggle_title);
    }
    else
    {
      $col .= $this->toggle_title;
    }
    $col .= "</a>";
    $col .= '</div>';
    $this->prepend_column ($col);
    parent::_draw_header ();
  }

  /**
   * @access private
   */
  protected function _draw_selector ($obj)
  {
?><input type="checkbox" name="<?php echo $this->control_name; ?>[]" value="<?php echo $obj->id; ?>" id="<?php echo $this->control_name . '_' . $obj->id; ?>">
<?php
  }

  /**
   * Draw the given column's data using the given object.
   * @param UNIQUE_OBJECT $obj
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
    }
  }
}

?>