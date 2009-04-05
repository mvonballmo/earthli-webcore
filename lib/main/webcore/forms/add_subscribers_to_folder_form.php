<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
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
require_once ('webcore/forms/form.php');

/**
 * Adds a list of email addresses as {@link SUBSCRIBER}s to a {@link FOLDER}.
 * @package webcore
 * @subpackage forms
 * @version 3.1.0
 * @since 2.2.1
 */
class ADD_SUBSCRIBERS_TO_FOLDER_FORM extends ID_BASED_FORM
{
  /**
   * @var string
   */
  public $button = 'Add';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/add';

  /**
   * @param FOLDER $folder Add subscribers to this folder.
   */
  public function ADD_SUBSCRIBERS_TO_FOLDER_FORM ($folder)
  {
    ID_BASED_FORM::ID_BASED_FORM ($folder->app);

    $this->_folder = $folder;

    $field = new TEXT_FIELD ();
    $field->id = 'emails';
    $field->title = 'Subscribers';
    $field->description = 'Place each email on its own line in the list.';
    $field->required = true;
    $this->add_field ($field);
  }

  /**
   * Store the form's values to this folder.
   * @param FOLDER $obj
   * @access private
   */
  public function commit ($obj)
  {
    // strip the '\r' for Windows systems, then split the string at '\n'
    $emails = str_replace ("\r", '', $this->value_for ('emails'));
    $emails = split ("\n", $emails);

    if (sizeof ($emails))
    {
      foreach ($emails as $email)
      {
        $email = trim ($email);
        if ($email)   // Skip blank lines
        {
          $class_name = $this->app->final_class_name ('SUBSCRIBER', 'webcore/obj/subscriber.php');
          $subscriber = new $class_name ($this->app);
          $subscriber->email = $email;
          $subscriber->set_subscribed ($this->_folder, Subscribe_folder, true);
        }
      }
    }
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->start ();
    $renderer->draw_text_box_row ('emails', '25em', '6em');
    $renderer->draw_separator ();
    $renderer->draw_submit_button_row ();
    $renderer->finish ();
  }
}
?>