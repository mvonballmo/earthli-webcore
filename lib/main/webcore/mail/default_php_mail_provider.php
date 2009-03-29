<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.2.1
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
require_once ('webcore/mail/mail_provider.php');

/**
 * Send mail using the PHP 'mail' function.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.2.1
 */
class DEFAULT_PHP_MAIL_PROVIDER extends MAIL_PROVIDER
{
  /**
   * @param MAIL_MESSAGE $message
    * @access private
    */
  function _internal_send ($message)
  {
    $mailer_id = "PHP " . phpversion ();
    $header = $this->renderer->_line ("From: $message->send_from_name <$message->send_from_address>");
    $header .= $this->renderer->_line ("X-Sender: <$message->send_from_address>");
    $header .= $this->renderer->_line ("X-Mailer: <$mailer_id>");
    $header .= $this->renderer->_line ("X-Priority: $message->priority");
    $header .= $this->renderer->_line ("Return-Path: <$message->return_path_address>");
    $header .= $this->renderer->_line ("MIME-Version: 1.0");
    if ($message->send_as_html)
    {
      $header .= $this->renderer->_line ("Content-Type: text/html; charset=iso-8859-1");
    }
    else
    {
      $header .= $this->renderer->_line ("Content-Type: text/plain");
    }
    if (sizeof ($message->cc))
    {
      $header .= $this->renderer->_line ("Cc: " . implode (",", $message->cc));
    }
    if (sizeof ($message->bcc))
    {
      $header .= $this->renderer->_line ("Bcc: " . implode (",", $message->bcc));
    }
    if (sizeof ($message->custom_headers))
    {
      $header .= implode ($this->eol, $message->custom_headers);
    }

    $header = trim ($header);

    $this->record ("[$header]");
    $this->record ("SMTP: <{$this->context->mail_options->SMTP_server}>");

    ini_set ('SMTP', $this->context->mail_options->SMTP_server);
    ini_set ('sendmail_from', $message->send_from_address);
    ini_set ('track_errors', TRUE);

    $this->_last_error = '';
    $result = @mail (implode (',', $message->send_to), $message->subject, $message->body, $header);
    if (isset ($php_errormsg))
    {
      $this->_last_error = $php_errormsg;
    }

    return $result;
  }

  /**
   * @return string
    * @access private
    */
  function last_error_as_html ()
  {
    return $this->_last_error;
  }

  /**
   * @return string
    * @access private
    */
  function last_error_as_plain_text ()
  {
    return html_entity_decode ($this->_last_error);
  }

  /**
   * @var string
   * @access private
   */
  protected $_last_error;
}

?>