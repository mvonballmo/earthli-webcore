<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package news
 * @subpackage forms
 * @version 3.5.0
 * @since 2.4.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli News.

earthli News is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli News is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli News; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli News, visit:

http://earthli.com/software/webcore/app_news.php

****************************************************************************/

/** */
require_once ('webcore/forms/object_in_folder_form.php');

/**
 * Edit or create an {@link ARTICLE}.
 * @package news
 * @subpackage forms
 * @version 3.5.0
 * @since 2.4.0
 */
class ARTICLE_FORM extends DRAFTABLE_ENTRY_FORM
{
  /**
   * @param FOLDER $folder Object is created/edited in this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $this->field_at('update_modifier_on_change')->visible = true;
  }

  /**
   * Draw options between the title and description.
   * This is a good place for controls that add content to the description
   * (toolbars, etc.).
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_options ($renderer)
  {
    parent::_draw_options ($renderer);
    $renderer->draw_check_box_row ('update_modifier_on_change');
  }
  
  /**
   * Return true if there are options to be drawn by {@link _draw_options()}.
   * @return boolean 
   * @access private
   */
  protected function _has_options ()
  {
    return $this->object_exists () || parent::_has_options(); 
  }
    
  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->default_control_height = '550px';
    $renderer->inline_operations_enabled = true;
    $renderer->draw_inline_preview_area();

//    echo '<div style="width: 50%">';
    parent::_draw_controls ($renderer);
//    echo '</div>';
  }
}