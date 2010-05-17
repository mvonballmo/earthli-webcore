<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
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

/**
 * @see MAIL_MESSAGE::set_priority()
 */
define ('Mail_message_priority_high', 1);
/**
 * @see MAIL_MESSAGE::set_priority()
 */
define ('Mail_message_priority_normal', 3);

require_once ('webcore/obj/webcore_object.php');

/**
 * Encapsulates the contents of an email.
 * Configure the mail, then call {@link send()}.
 * @see MAIL_PROVIDER
 * @package webcore
 * @subpackage mail
 * @version 3.3.0
 * @since 2.2.1
 */
class MAIL_MESSAGE extends WEBCORE_OBJECT
{
  /**
   * @var string
   */
  public $send_from_address;

  /**
   * @var string
   */
  public $return_path_address;

  /**
   * @var string
   */
  public $send_from_name;

  /**
   * @var boolean
   */
  public $send_as_html = true;

  /**
   * @var array[string]
   */
  public $send_to = array ();

  /**
   * @var array[string]
   */
  public $cc = array ();

  /**
   * @var array[string]
   */
  public $bcc = array ();

  /**
   * @var array[string]
   */
  public $custom_headers = array ();

  /**
   * @var integer
   */
  public $priority = Mail_message_priority_normal;

  /**
   * @var string
   */
  public $body;

  /**
   * @var string
   */
  public $subject;

  /**
   * @param string $name
   * @param string $email
   */
  public function set_sender ($name, $email)
  {
    $this->assert (! empty ($name) & ! empty ($email), 'name and email cannot be empty', 'set_sender', 'MAIL_MESSAGE');
    $this->send_from_name = $name;
    $this->send_from_address = $email;
    $this->return_path_address = $email;
  }

  /**
   * @param string $subject
   * @param string $body
   */
  public function set_content ($subject, $body)
  {
    $this->assert (! empty ($subject) && ! empty ($body), 'subject and body cannot be empty', 'set_content', 'MAIL_MESSAGE');
    $this->subject = $subject;
    $this->body = $body;
  }

  /**
   * Can be set to 'Mail_message_priority_normal' or 'Mail_message_priority_high'.
   * @param integer $priority
   */
  public function set_priority ($priority)
  {
    $is_valid_priority = ($priority >= Mail_message_priority_high) && ($priority <= Mail_message_priority_normal);
    $this->assert ($is_valid_priority, 'priority must be high or normal', 'add_header', 'MAIL_MESSAGE');
    $this->priority = $priority;
  }

  /**
   * Replace the recipient(s) with this address or addresses.
   * Pass in either a single email or an array of emails.
   * @param string $address
   */
  public function set_send_to ($address)
  {
    $this->assert (! empty ($address), 'address cannot be empty', 'set_send_to', 'MAIL_MESSAGE');
    $this->send_to = array ();
    if (is_array ($address))
    {
      $this->send_to = $address;
    }
    else
    {
      $this->send_to [] = $address;
    }
  }

  /**
   * Add these recipient(s).
   * Pass in either a single email or an array of emails.
   * @param string $address
   */
  public function add_send_to ($address)
  {
    $this->assert (! empty ($address), 'address cannot be empty', 'add_send_to', 'MAIL_MESSAGE');
    if (is_array ($address))
    {
      $this->send_to = array_merge ($this->send_to, $address);
    }
    else
    {
      $this->send_to [] = $address;
    }
  }

  /**
   * Add these recipient(s) as CCs.
   * Pass in either a single email or an array of emails.
   * @param string $address
   */
  public function add_cc ($address)
  {
    $this->assert (! empty ($address), 'address cannot be empty', 'add_cc', 'MAIL_MESSAGE');
    if (is_array ($address))
    {
      $this->cc = array_merge ($this->cc, $address);
    }
    else
    {
      $this->cc [] = $address;
    }
  }

  /**
   * Add these recipient(s) as BCCs.
   * Pass in either a single email or an array of emails.
   * @param string $address
   */
  public function add_bcc ($address)
  {
    $this->assert (! empty ($address), 'address cannot be empty', 'add_bcc', 'MAIL_MESSAGE');
    if (is_array ($address))
    {
      $this->bcc = array_merge ($this->bcc, $address);
    }
    else
    {
      $this->bcc [] = $address;
    }
  }

  /**
   * Add this command to the email header.
   * Pass in either a single header or an array of headers.
   * @param string $header
   */
  public function add_custom_header ($header)
  {
    $this->assert (! empty ($header), 'header cannot be empty', 'add_header', 'MAIL_MESSAGE');
    if (is_array ($header))
    {
      $this->custom_headers = array_merge ($this->custom_headers, $header);
    }
    else
    {
      $this->custom_headers [] = $header;
    }
  }

  /**
   * Send the message using the given provider.
   * @param PROVIDER $provider
   */
  public function send ($provider)
  {
    $provider->send ($this);
  }
}

?>