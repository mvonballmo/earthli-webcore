<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/forms/send_mail_form.php');

/**
 * Send information about a browser in an email.
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.7.0
 */
class SUBMIT_BROWSER_FORM extends SEND_MAIL_FORM
{
  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $this->set_required ('sender_name', false);
    $this->set_required ('sender_email', false);

    $field = $this->field_at ('sender_email');
    $field->description = 'Optional, but lets us follow up if we have any questions.';
    
    $field = $this->field_at ('message');
    $field->description = 'Briefly describe the problem with our browser detection (very useful).';
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();

    $this->set_value ('send_to', $this->context->mail_options->webmaster_address);
    $this->set_value ('subject', "Problem with browser detection");
  }

  /**
   * @param object $obj Get renderer for this object.
   * @access private
   */
  protected function _make_obj_renderer ($obj)
  {
    $class_name = $this->context->final_class_name ('BROWSER_MAIL_RENDERER', 'webcore/mail/browser_mail_renderer.php');
    return new $class_name ($this->context);
  }
}

?>