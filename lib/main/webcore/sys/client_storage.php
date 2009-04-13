<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.1.0
 * @since 2.7.0
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
require_once ('webcore/sys/system.php');
require_once ('webcore/sys/date_time.php');

/**
 * Messages sent from {@link CLIENT_STORAGE} are recorded in this channel.
 * Used with {@link log_message()}.
 * @access private
 */
define ('Msg_channel_client', 'Storage');

/**
 * Interface to loading/storing client-side data.
 * This is a base class that provides both an interface and a large part of the
 * implementation for sophisicated data storage and retrieval. Use {@link
 * exists_on_client()} and {@link value()} to read data and {@link set_value()}
 * and {@link clear_value()} to store data. Multiple values can be stored
 * together with {@link start_multiple_value()} and read back with {@link
 * load_multiple_values()}. Values expire at the end of the session, by default;
 * set the {@link $expire_date} to change storage duration.
 * @see COOKIE
 * @package webcore
 * @subpackage sys
 * @version 3.1.0
 * @since 2.7.0
 * @abstract
 */
abstract class CLIENT_STORAGE extends RAISABLE
{
  /**
   * Set values expire on this day.
   * Calls to {@link set_value()} will use this value for the expiration date.
   * Call {@link expire_when_session_ends()} to reset to single-session
   * duration.
   * @var DATE_TIME
   */
  public $expire_date;

  /**
   * Identifier to prepend when storing or retrieving values.
   * @var string
   */
  public $prefix = '';
  
  public function CLIENT_STORAGE ()
  {
    $this->expire_date = new DATE_TIME ();
    $this->expire_when_session_ends ();
  }

  /**
   * Return the current value for 'key'.
   * If this value has already been set through this storage, it reads the
   * current value instead of that sent by the client (which would be stale).
   * @param string $key
   * @return string
   */
  public function value ($key)
  {
    if (isset ($this->_values [$key]))
    {
      $Result = $this->_values [$key];
    }
    else
    {
      $Result = $this->_read ($this->prefix . $key);
    }

    return $Result;
  }
  
  /**
   * Returns True if the value is available on the client.
   * Allows a page to check exactly which values were already set before this
   * page loaded. 
   * @param string $key
   * @return boolean
   */
  public function exists_on_client ($key)
  {
    return $this->_exists ($this->prefix . $key);    
  }

  /**
   * Set the value for a key.
   * Calls {@link clear_value()} if 'value' is null or an empty string.
   * @param string $key
   * @param string $value
   */
  public function set_value ($key, $value)
  {
    if (isset ($this->_multiple_values))
    {
      if (strpos ($key, $this->_multiple_value_key) === 0)
      {
        $key = substr ($key, strlen ($this->_multiple_value_key));
        $this->_multiple_values [$key] = $value;
      }
      else
      {
        $this->_multiple_values [$key] = $value;
      }
    }
    else
    {
      if (! isset ($value) || ($value === ''))
      {
        $this->clear_value ($key);
      }
      else
      {
        $this->_values [$key] = $value;
        $this->_write ($this->prefix . $key, $value);
        log_message ("Wrote [$value] to [$key]", Msg_type_debug_info, Msg_channel_client);
      }
    }
  }

  /**
   * Removes the value at 'key' from the client.
   * @param string $key
   */
  public function clear_value ($key)
  {
    $this->_values [$key] = '';
    $this->_clear ($this->prefix . $key);
    log_message ("Cleared value for [$key]", Msg_type_debug_info, Msg_channel_client);
  }

  /**
   * Sets the {@link $expire_date} 'n' days in the future.
   * @see expire_when_session_ends()
   * @param integer $days
   */
  public function expire_in_n_days ($days)
  {
    $this->expire_date->set_from_php (time () + ($days * 86400));
  }
  
  /**
   * Values will expire when the browser is closed.
   */
  public function expire_when_session_ends ()
  {
    $this->expire_date->clear ();
  }

  /**
   * Load all the settings out of a client value.
   * Call {@link store_multiple_values()} to put multiple settings into a single
   * value. Call this function to reload those settings, treating them as
   * individual values. Invididual values will get the name 'key' + 'sub_key'.
   * That is, if the key is 'preferences' and it contains 'save_login' and
   * 'save_email', these values are associated with the keys
   * 'preferences_save_login' and 'preferences_save_email'.
   * @see start_multiple_value()
   * @param string $key Load all settings stored in this key.
   * @param boolean $prepend_key Prepends the key to all loaded values.
   */
  public function load_multiple_values ($key, $prepend_key = true)
  {
    if ($this->exists_on_client ($key))
    {
      $raw_data = $this->value ($key);
      $pairs = explode ('|', $raw_data);
      foreach ($pairs as $pair) 
      {
        list ($sub_key, $value) = explode ('=', $pair);
        if ($prepend_key)
        {
          $sub_key = $key . $sub_key;
        }
        $this->_add ($this->prefix . $sub_key, $value);
      } 
    }    
  }

  /**
   * Start storing subsequent settings as a single value.
   * Call {@link finish_multiple_value()} to store the cached values as a single
   * setting.
   * @see finish_multiple_value()
   * @param string $key Store the accumulated settings under this key.
   */
  public function start_multiple_value ($key)
  {
    $this->_multiple_value_key = $key;
    $this->_multiple_values = array ();
  }
  
  /**
   * Store multiple settings into a single setting.
   * Use {@link load_multiple_values()} to read these values back into
   * individual settings.
   * @see start_multiple_value()
   */
  public function finish_multiple_value ()
  {
    if (! empty ($this->_multiple_values))
    {
      foreach ($this->_multiple_values as $sub_key => $value )
      {
        $this->_values [$sub_key] = $value;
        $settings [] = $sub_key . '=' . $value;
      }
      unset ($this->_multiple_values);  
      $this->set_value ($this->_multiple_value_key, implode ('|', $settings));
    }    
  }
  
  /**
   * Load the requested value from storage.
   * @param string $key
   * @return string
   * @access private
   * @abstract
   */
  protected abstract function _read ($key);
  
  /**
   * Is this value in local storage?
   * @param string $key
   * @return boolean
   * @access private
   * @abstract
   */
  protected abstract function _exists ($key);

  /**
   * Add the requested value to storage.
   * It should be available to calls to {@link _read()}, but should <i>not</i>
   * be written to client storage. Used by {@link load_multiple_values()}.
   * @param string $key
   * @param string $value
   * @access private
   * @abstract
   */
  protected abstract function _add ($key, $value);
  
  /**
   * Store the requested value to client storage.
   * @param string $key
   * @param string $value
   * @access private
   * @abstract
   */
  protected abstract function _write ($key, $value);

  /**
   * Remove the requested value from client storage.
   * @param string $key
   * @access private
   * @abstract
   */
  protected abstract function _clear ($key);
  
  /**
   * Maintains the local buffer to the client storage.
   * Newly set or cleared values are read out of here to avoid reading "stale"
   * values from the actual client storage (which is only updated when the page
   * is submitted).
   * @var array[string,string]
   * @access private
   */
  protected $_values = array ();
  
  /**
   * Contains settings when storing multiple values.
   * Use {@link start_multiple_value()} to start storing to a list of
   * values. Use {@link finish_multiple_value()} to write the accumulated
   * values to a single setting.
   * @var array[string,string]
   * @access private
   */
  protected $_multiple_values;
  
  /**
   * Multiple values will be stored to this key.
   * @var string
   * @access private
   */
  protected $_multiple_value_key;
}

?>