<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
 * @version 3.6.0
 * @since 2.2.1
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
require_once ('webcore/log/loggable.php');

/**
 * Interface for mailing capability.
 * Actual implementation are derived from this one.
 * @package webcore
 * @subpackage mail
 * @version 3.6.0
 * @since 2.2.1
 * @abstract
 */
abstract class MAIL_PROVIDER extends LOGGABLE
{
  /**
   * Used to implement basic text-display.
   * @var RENDERER
   */
  public $renderer;

  /**
   * {@link record()} uses this channel, by default.
   * @var string
   */
  public $default_channel = Msg_channel_mail;

  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $class_name = $this->context->final_class_name ('RENDERER', 'webcore/gui/renderer.php');
    $this->renderer = new $class_name ($context);
  }

  /**
   * Send the 'message'.
   * Handles all logging and error messages.
   * @param MAIL_MESSAGE $message
   */
  public function send ($message)
  {
    $this->assert (sizeof ($message->send_to) > 0, 'must send to at least one address', 'send', 'MAIL_PROVIDER');
    $this->assert (! empty ($message->subject) && ! empty ($message->body), 'subject and body cannot be empty', 'send', 'MAIL_PROVIDER');

    $sent = $this->_internal_send ($message);

    if (isset ($this->logs->logger))
    {
      $recipients = implode (';', $message->send_to);
      if ($sent)
      {
        $this->record ("[$recipients] was sent [$message->subject]", Msg_type_info);
      }
      else
      {
        $this->record ("[$recipients] was NOT sent [$message->subject]", Msg_type_error);
        $this->record_more ("because [" . $this->last_error_as_plain_text () . "]");
      }
    }
  }

  /**
   * Contents of the last provider-specific error message.
   * @return string
   * @access private
   * @abstract
   */
  public abstract function last_error_as_plain_text ();

  /**
   * Contents of the last provider-specific error message.
   * @return string
   * @access private
   * @abstract
   */
  public abstract function last_error_as_html ();

  /**
   * @param MAIL_MESSAGE $message
   * @access private
   * @abstract
   */
  protected abstract function _internal_send ($message);
}

?>