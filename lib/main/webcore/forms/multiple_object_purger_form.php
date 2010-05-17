<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
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
require_once ('webcore/forms/multiple_object_action_form.php');

/**
 * Purges one or more {@link ENTRY}s or {@link FOLDER}s.
 * Purging removes the entry or folder and all contents from the database.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
 */
class MULTIPLE_OBJECT_PURGER_FORM extends MULTIPLE_OBJECT_ACTION_FORM
{
  /**
   * @param APPLICATION $app
   */
  public function __construct ($app)
  {
    parent::__construct ($app);
    
    $field = new BOOLEAN_FIELD ();
    $field->id = 'remove_resources';
    $field->title = 'Remove associated files';
    $field->description = 'Leave this unchecked if any other object references the same files (e.g. you are purging a duplicate).';
    $field->visible = false;
    $this->add_field ($field);
  }

  /**
   * Execute action for a single folder.
   * @param FOLDER $fldr
   * @access private
   */
  protected function _folder_run ($fldr)
  {
    $fldr->purge ($this->_purge_options_for ($fldr));
  }

  /**
   * Execute action for a single entry.
   * @param ENTRY $entry
   * @access private
   */
  protected function _entry_run ($entry)
  {
    $entry->purge ($this->_purge_options_for ($entry));
  }

  /**
   * Options for purging an object.
   * @param OBJECT_IN_FOLDER $obj
   * @return PURGE_OPTIONS
   * @access private
   */
  protected function _purge_options_for ($obj)
  {
    $Result = $obj->make_purge_options ();
    $Result->remove_resources = $this->value_for ('remove_resources');
    return $Result;
  }

  /**
   * Draw a confirmation message for this action.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_message ($renderer)
  {
    $renderer->start_row ();

    $renderer->draw_text_row ('', 'Are you sure you want to purge ' . $this->object_list->description () . '?*');
    
    if ($this->visible ('remove_resources'))
    {
      $renderer->start_row (' ');
      echo $renderer->check_box_as_html ('remove_resources');
      $renderer->finish_row ();
    }

    $renderer->draw_text_row ('', '*Purging an object permanently removes it and all contained content (entries, comments, etc.) from the database.', 'notes');

    $renderer->finish_row ();
    $buttons [] = $renderer->button_as_HTML ('No', "view_explorer.php?id={$this->_folder->id}");
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);
  }
}
?>