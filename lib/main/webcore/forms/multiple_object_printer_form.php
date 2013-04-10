<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
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
require_once ('webcore/forms/multiple_object_action_form.php');
require_once ('webcore/gui/print_preview.php'); // for constants

/**
 * Sends one or more {@link ENTRY}s to a print preview page.
 * @package webcore
 * @subpackage forms
 * @version 3.3.0
 * @since 2.2.1
 */
class MULTIPLE_OBJECT_PRINTER_FORM extends MULTIPLE_OBJECT_ACTION_FORM
{
  /**
   * HTTP submission method.
   * @var string
   */
  public $method = 'get';

  /**
   * @var string
   */
  public $button = 'View printable';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/print';

  /**
   * @param FOLDER $folder Objects are from this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder);

    $field = new INTEGER_FIELD ();
    $field->id = 'show_comments';
    $field->title = 'Comments';
    $field->min_value = 0;
    $field->max_value = 2;
    $this->add_field ($field);

    $field = new BOOLEAN_FIELD ();
    $field->id = 'show_users';
    $field->title = 'Users/dates';
    $field->description = 'Display users and create/modify times.';
    $this->add_field ($field);
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    $this->set_value ('show_comments', Print_comments_threaded);
    $this->set_value ('show_users', 1);
  }

  /**
   * Do nothing with folders.
   * Redirects to a different page, so there is no action needed here.
   * @param FOLDER $fldr
   * @access private
   */
  protected function _folder_run ($fldr) {}

  /**
   * Do nothing with entries.
   * Redirects to a different page, so there is no action needed here.
   * @param ENTRY $entry
   * @access private
   */
  protected function _entry_run ($obj) {}

  /**
   * Draw a confirmation message for this action.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_message ($renderer)
  {
    $renderer->draw_text_row ('', 'To the left are the items you selected. Please select your printing options below and click <span class="reference">View printable</span> to see a preview. Then simply print the preview page from the browser window.');
    $renderer->draw_separator ();
    $this->_draw_print_options ($renderer);
    $renderer->draw_separator ();
    $buttons [] = $renderer->button_as_HTML ('Cancel', "view_explorer.php?id={$this->_folder->id}", '{icons}buttons/close');
    $buttons [] = $renderer->submit_button_as_HTML ();
    $renderer->draw_buttons_in_row ($buttons);
  }

  /**
   * Hook to allow descendants to use the default form layout, but add print options.
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_print_options ($renderer)
  {
    $props = $renderer->make_list_properties ();
    $props->smart_wrapping = true;
    $props->add_item ('Don\'t show comments', Print_comments_off);
    $props->add_item ('Show comments threaded', Print_comments_threaded);
    $props->add_item ('Show comments flat', Print_comments_flat);

    $renderer->draw_radio_group_row ('show_comments', $props);
    $renderer->draw_check_box_row ('show_users');
  }
}

?>