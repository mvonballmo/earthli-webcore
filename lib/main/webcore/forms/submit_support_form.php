<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.7.0
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
require_once ('webcore/forms/send_mail_form.php');

/**
 * Send information about a browser in an email.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.7.0
 */
class SUBMIT_SUPPORT_FORM extends SEND_MAIL_FORM
{
  /**
   * @param CONTEXT &$context
   */
  function SUBMIT_SUPPORT_FORM (&$context)
  {
    SEND_MAIL_FORM::SEND_MAIL_FORM ($context);

    $this->set_required ('sender_name', FALSE);
    $this->set_required ('sender_email', FALSE);
    $this->set_required ('message', TRUE);

    $field =& $this->field_at ('sender_email');
    $field->description = 'Optional, but lets us follow up if we have any questions.';
    
    $field =& $this->field_at ('message');
    $field->description = 'Briefly describe the question or problem you\'re having.';
  }

  function load_with_defaults ()
  {
    parent::load_with_defaults ();

    $this->set_value ('send_to', $this->context->mail_options->webmaster_address);
    $this->set_value ('subject', "General Support Question");
  }

  /**
   * @param object &$obj Get renderer for this object.
   * @access private
   */
  function _make_obj_renderer (&$obj)
  {
    return null;
  }
}

?>