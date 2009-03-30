<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.7.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/forms/multiple_object_action_form.php');

/**
 * Restores one or more {@link ENTRY}s or {@link FOLDER}s.
 * Restoring sets the entry or folder as visible again, removing the hidden or deleted flag.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.7.1
 */
class MULTIPLE_OBJECT_PUBLISHER_FORM extends MULTIPLE_OBJECT_ACTION_FORM
{
  /**
   * Execute action for a single folder.
   * @param FOLDER $fldr
   * @access private
   */
  protected function _folder_run ($fldr)
  {
  }

  /**
   * Execute action for a single entry
   * @param ENTRY $entry
   * @access private
   */
  protected function _entry_run ($entry)
  {
    $entry->set_state (Visible, true);
  }

  /**
   * Draw a confirmation message for this action.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_message ($renderer)
  {
    $renderer->draw_text_row ('', 'Are you sure you want to publish ' . $this->object_list->description () . '?');
    $renderer->draw_separator ();
    $buttons [] = $renderer->button_as_HTML ('No', "view_explorer.php?id={$this->_folder->id}");
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);
  }
}
?>