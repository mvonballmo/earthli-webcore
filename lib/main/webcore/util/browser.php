<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
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

/**
 * Does the browser support DHTML?
 */
define ('Browser_DHTML', 1);

/**
 * Does the browser support PNG images with alpha-transparency?
 */
define ('Browser_alpha_PNG', 2);

/**
 * Does the browser support CSS Level 1?
 */
define ('Browser_CSS_1', 3);

/**
 * Does the browser support CSS Level 2?
 */
define ('Browser_CSS_2', 4);

/**
 * Does the browser support CSS Tables?
 */
define ('Browser_CSS_Tables', 5);

/**
 * Does the browser support Javascript?
 */
define ('Browser_JavaScript', 6);

/**
 * Does the browser support setting/getting cookies?
 */
define ('Browser_cookie', 7);

/**
 * Are HTML anchors in a form submission allowed?
 * Determines whether the "action" of a {@link FORM} can contain
 * an HTML anchor (#) or not.
 */
define ('Browser_anchors_in_posts', 8);

/**
 * Does the browser support DOM level 2?
 */
define ('Browser_DOM_2', 9);

/**
 * Does the browser accept full HTML in newsfeeds?
 */
define ('Browser_extended_HTML_newsfeeds', 10);

/**
 * Browser uses the Netscape 4 renderer.
 */
define ('Browser_netscape_4', 'netscape_4');

/**
 * Browser uses the Gecko renderer.
 * @see BROWSER::gecko_date()
 */
define ('Browser_gecko', 'gecko');

/**
 * Browser uses the KHTML renderer.
 * As of this writing, this is used by Safari and Omniweb 4.5 on the Mac and
 * Konqueror on Linux.
 */
define ('Browser_khtml', 'khtml');

/**
 * Browser uses the Opera renderer.
 */
define ('Browser_opera', 'opera');

/**
 * Browser uses the WebTV renderer.
 */
define ('Browser_webtv', 'webtv');

/**
 * Browser uses the Internet Explorer renderer.
 */
define ('Browser_ie', 'ie');

/**
 * Browser uses the iCab renderer.
 * Mac OS X only.
 */
define ('Browser_icab', 'icab');

/**
 * Browser uses the Omniweb renderer.
 * Mac OS X only.
 */
define ('Browser_omniweb', 'omniweb');

/**
 * Browser uses a text renderer.
 */
define ('Browser_text', 'lynx');

/**
 * Browser is a search or other robot.
 */
define ('Browser_robot', 'robot');

/**
 * Browser is a newsfeed reader.
 */
define ('Browser_newsreader', 'newsreader');

/**
 * Browser is a previewer, like the bot Facebook uses to generate previews for linked URLs.
 */
define ('Browser_previewer', 'previewer');

/**
 * Browser is running on Win32.
 */
define ('Browser_os_windows', 'windows');

/**
 * Browser is running on MacOS (Classic or OS X).
 */
define ('Browser_os_mac', 'macos');

/**
 * Browser is running on Linux (some form).
 */
define ('Browser_os_linux', 'linux');

/**
 * Browser element could not be determined.
 * @access private
 */
define ('Browser_unknown', 'Unknown');

/**
 * Encapsulates all information about the client.
 * The class reads the values from the current session, by default, but can be
 * loaded using {@link load_from_string()}. Use {@link supports()} and {@link
 * is()} to determine the capabilities of the underlying renderer.
 *
 * The user agent detection mechanism makes a distinction between the name of
 * the browser and the technology used to render content. The browser name (e.
 * g. MSN) is available through {@link name()} and {@link version()}, while the
 * underlying renderer (e.g. IE 6.0) is available through ({@link
 * renderer_name()} and {@link renderer_version()}). Use {@link system_id()} to
 * get a formatted operating system name or {@link system_name()} and {@link
 * system_version()} to access the information directly.
 * @see is()
 * @see supports()
 * @package webcore
 * @subpackage util
 * @version 3.1.0
 * @since 2.2.1
 */
class BROWSER
{
  /**
   * User-agent string received in HTTP request.
   * Set this property with {@link load_from_server()} or {@link load_from_string()}.
   * @var string
   */
  public $user_agent_string;

  /**
   * Calls {@link load_from_server()} by default.
   */
  public function BROWSER ()
  {
    $this->load_from_server ();
  }

  /**
   * Read the user agent from the server environment.
   * Called automatically from the constructor.
   * @see load_from_string()
   */
  public function load_from_server ()
  {
    $this->load_from_string (read_array_index ($_SERVER, 'HTTP_USER_AGENT'));
  }

  /**
   * Read from a given user agent.
   * Used primarily for testing.
   * @see load_from_server()
   */
  public function load_from_string ($s)
  {
    $this->user_agent_string = $s;
    $tables = $this->_make_user_agent_parse_tables ();
    $parser = $this->_make_user_agent_parser ($tables);
    $this->_ua = $parser->make_properties_from ($s);
  }

  /**
   * Resolve the domain name of the browser.
   * Performs a reverse name lookup of the {@link ip_address()}.
   * @return string
   */
  public function domain ()
  {
    return @gethostbyaddr ($this->ip_address ());
  }

  /**
   * The actual ip address of the browser.
   * Will resolve the proxy forward, if one is present in the HTTP header.
   * @see domain()
   * @return string
   */
  public function ip_address ()
  {
    $forwarded = read_array_index ($_SERVER, 'HTTP_X_FORWARDED_FOR');
    $remote_addr = read_array_index ($_SERVER, 'REMOTE_ADDR');
    if ($forwarded)
    {
      return $forwarded;
    }
    else if ($remote_addr)
 {
   return $remote_addr;
 }

    return read_array_index ($_SERVER, 'REMOTE_HOST');
  }

  /**
   * Return a description as HTML.
   * @return string
   */
  public function description_as_html ()
  {
    return $this->_description_as_text (false);
  }

  /**
   * Return a description as plain text.
   * @return string
   */
  public function description_as_plain_text ()
  {
    return $this->_description_as_text (true);
  }

  /**
   * Identifies the technology used by this browser.
   * This is often different than the name, since many browsers employ embedded
   * renderers like Gecko, or are rebranded like Opera Composer browsers.
   * @return string
   */
  public function renderer_name ()
  {
    return $this->_ua->renderer_name;
  }

  /**
   * Identifies the version of the technology used by this browser.
   * This is the number used internall to identify whether a feature is supported.
   * @see BROWSER::supports()
   * @return string
   */
  public function renderer_version ()
  {
    return $this->_ua->renderer_version;
  }

  /**
   * Name of the browser.
   * Not necessarily the same as the renderer name. AOL uses the IE engine or the
   * Gecko engine, depending on version. MSN uses the IE engine.
   * @return string
   */
  public function name ()
  {
    return $this->_ua->name;
  }

  /**
   * Version of the browser.
   * Not necessarily the same as the renderer version. Largely useless identifier
   * for determining feature support, but nice to use when showing a user which
   * browser they are running. e.g. displaying 'MSN 7.0' instead of 'IE 5.5sp1'.
   * @return string
   */
  public function version ()
  {
    return $this->_ua->version;
  }

  /**
   * Name of the icon to use.
   * Use this location with {@link CONTEXT::icon_as_html()} to render the icon in
   * a WebCore application.
   * @return string
   */
  public function icon_name ()
  {
    $n = strtolower ($this->name ());

    if (strpos ($n, 'opera') !== false)
    {
      $Result = 'opera1_t';
    }
    elseif (strpos ($n, 'firefox') !== false)
    {
      $Result = 'firefox';
    }
    elseif (strpos ($n, 'omniweb') !== false)
    {
      $Result = 'omniweb5';
    }
    elseif (strpos ($n, 'safari') !== false)
    {
      $Result = 'safari';
    }
    elseif (strpos ($n, 'camino') !== false)
    {
      $Result = 'camino';
    }
    elseif (strpos ($n, 'internet explorer') !== false)
    {
      $Result = 'ie';
    }
    elseif (strpos ($n, 'shiira') !== false)
    {
      $Result = 'shiira';
    }
    elseif (strpos ($n, 'chrome') !== false)
    {
      $Result = 'chrome';
    }
    else
    {
      $Result = '';
    }

    return $Result;
  }

  /**
   * Specific name of the operating system.
   * @return string
   */
  public function system_name ()
  {
    return $this->_ua->system_name;
  }

  /**
   * Operating system version.
   * This is specific, like Windows NT 5.1 (Windows 2000) will return 5.1, not 2000.
   * @return string
   */
  public function system_version ()
  {
    return $this->_ua->system_version;
  }

  /**
   * Calculated system name.
   * The most likely operating system derived from the user agent string.
   * @return string
   */
  public function calculated_system_name ()
  {
    return $this->_ua->calculated_system_name;
  }

  /**
   * Fully formatted operating system id.
   * Returns as much information about the operating system as possible,
   * favoring the interpreted system name because it's generally capitalized
   * better.
   * <ul>
   * <li>'Windows 5.1' will return 'Windows 5.1 (Windows 2000)'</li>
   * <li>'Linux i686 ... Debian/1.2.0' will return 'Debian 1.2.0 (Linux)'</li>
   * </ul>
   * @return string
   */
  public function system_id ()
  {
    if (isset ($this->_ua->system_name))
    {
      $Result = $this->_ua->system_name;
      if ($this->_ua->system_version)
      {
        $Result .= ' ' . $this->_ua->system_version;
      }
      if (strcasecmp ($this->_ua->system_name, $this->_ua->calculated_system_name) != 0)
      {
        $Result .= ' (' . $this->_ua->calculated_system_name . ')';
      }
      return $Result;
    }

    return $this->_ua->calculated_system_name;
  }

  /**
   * Build date of the client, if built with Gecko.
   * Gecko is the mozilla browser technology. Conforming user agent strings include
   * the build date of the Gecko component. (Can be empty)
   * @return DATE_TIME
   */
  public function gecko_date ()
  {
    return $this->_ua->gecko_date;
  }

  /**
   * Does the client match the given code?
   * The code can be an operating system (like {@link Browser_os_mac}) or
   * a browser identifier (like {@link Browser_os_opera}).
   * @param string $code
   * @return boolean
   */
  public function is ($code)
  {
    return (isset ($this->_ua->renderer_id) && ($code == $this->_ua->renderer_id))
        || (isset ($this->_ua->os_id) && ($code == $this->_ua->os_id));
  }

  /**
   * Is the requested functionality supported?
   * See the browser functionality constants.
   * @param integer $code
   * @return boolean
   */
  public function supports ($code)
  {
    switch ($code)
    {
    case Browser_DHTML:
      return (($this->is (Browser_gecko)) ||
              ($this->is (Browser_opera) && ($this->_ua->major_version >= 7)) ||
              ($this->is (Browser_ie) && $this->_ua->major_version >= 5) ||
              ($this->is (Browser_khtml)));

    case Browser_alpha_PNG:
      return (($this->is (Browser_gecko)) ||
              ($this->is (Browser_opera) && (($this->_ua->major_version >= 6) ||
                                             ($this->is (Browser_os_mac) && ($this->_ua->major_version >= 5)))) ||
              ($this->is (Browser_ie) && (($this->is (Browser_os_mac) && ($this->_ua->major_version >= 5)) ||
                                         ($this->is (Browser_os_windows) && ($this->_ua->major_version >= 7)))) ||
              ($this->is (Browser_omniweb) && ($this->_ua->major_version >= 4)) ||
              ($this->is (Browser_khtml)));

    case Browser_CSS_1:
      return (($this->is (Browser_gecko)) ||
              ($this->is (Browser_opera) && ($this->_ua->major_version >= 4)) ||
              ($this->is (Browser_ie) && ($this->_ua->major_version >= 5)) ||
              ($this->is (Browser_khtml)));

    case Browser_CSS_2:
      return (($this->is (Browser_gecko)) ||
              ($this->is (Browser_opera) && ($this->_ua->major_version >= 7)) ||
              ($this->is (Browser_khtml)) ||
              ($this->is (Browser_omniweb) && (($this->_ua->major_version >= 5) || (($this->_ua->major_version >= 4) && ($this->_ua->minor_version >= 5)))));
    case Browser_CSS_Tables:
      return (($this->is (Browser_opera) && ($this->_ua->major_version >= 7)) ||
              ($this->is (Browser_omniweb) && (($this->_ua->major_version >= 5) || (($this->_ua->major_version >= 4) && ($this->_ua->minor_version >= 5)))));

    case Browser_JavaScript:
      return $this->_ua->renderer_name != Browser_unknown;

    case Browser_cookie:
      return $this->_ua->renderer_name != Browser_unknown;

    case Browser_anchors_in_posts:
      return ! ($this->is (Browser_ie) && $this->is (Browser_os_mac));

    case Browser_DOM_2:
      return $this->is (Browser_opera) && ($this->_ua->major_version >= 9);

    case Browser_extended_HTML_newsfeeds:
      return true;
    }

    return false;
  }

  /**
   * Build a human-readable description.
   * @param boolean $text_only May include HTML tags if not set.
   * @return string
   * @access private
   */
  protected function _description_as_text ($text_only = false)
  {
    $app_name = $this->name () . ' ' . $this->version ();
    $eng_name = $this->renderer_name () . ' ' . $this->renderer_version ();

    if ($app_name == $eng_name)
    {
      $Result = $app_name;
    }
    else
    {
      $Result = "$app_name - $eng_name";
    }

    if ($this->is (Browser_gecko))
    {
      $gd = $this->gecko_date ();
      if ($gd)
      {
        $t = $gd->formatter ();
        $t->type = Date_time_format_date_only;
        if ($text_only)
        {
          $t->clear_flags ();
        }
        $Result .= ' (Released ' . $gd->format ($t) . ')';
      }
    }

    return $Result;
  }

  /**
   * @return USER_AGENT_PARSE_TABLES
   * @access private
   */
  protected function _make_user_agent_parse_tables ()
  {
    return new USER_AGENT_PARSE_TABLES ();
  }

  /**
   * @return USER_AGENT_PARSER
   * @access private
   */
  protected function _make_user_agent_parser ($tables)
  {
    return new USER_AGENT_PARSER ($tables);
  }
}

/**
 * Raw information container for client properties.
 * Used by the {@link BROWSER} to determine client properties and capabilities.
 * The user agent tracks both the client name and version ({@link $name} and
 * {@link $version}) and the rendering technology ({@link $renderer_name} and
 * {@link $renderer_version}) for that client. Can be created from a string by
 * the {@link USER_AGENT_PARSER}.
 * @package webcore
 * @subpackage util
 * @version 3.1.0
 * @since 2.6.0
 */
class USER_AGENT_PROPERTIES
{
  /**
   * Name of the client application.
   * @var string
   */
  public $name = Browser_unknown;

  /**
   * Version of the client application.
   * @var string
   */
  public $version = '';

  /**
   * Name of the rendering technology.
   * @var string
   */
  public $renderer_name = Browser_unknown;

  /**
   * @var string
   */
  public $renderer_version = '';

  /**
   * @var string
   */
  public $renderer_id = Browser_unknown;

  /**
   * @var string
   */
  public $system_name = Browser_unknown;

  /**
   * @var string
   */
  public $system_version = '';

  /**
   * @var string
   */
  public $calculated_system_name = Browser_unknown;

  /**
   * @var string
   */
  public $os_id = '';

  /**
   * @var DATE_TIME
   */
  public $gecko_date;

  /**
   * @var boolean
   */
  public $is_robot;

  /**
   * @var integer
   */
  public $major_version = 0;

  /**
   * @var integer
   */
  public $minor_version = 0;

  /**
   * @var integer
   */
  public $build_number = 0;

  public function USER_AGENT_PROPERTIES ()
  {
    $this->gecko_date = new DATE_TIME ();
    $this->gecko_date->clear ();
  }
}

/**
 * Creates a {@link USER_AGENT_PROPERTIES} object from a string.
 * Used by the {@link BROWSER} to generate a set of properties from the user
 * agents passed in by PHP.
 * @package webcore
 * @subpackage util
 * @version 3.1.0
 * @since 2.7.0
 * @access private
 */
class USER_AGENT_PARSER
{
  /**
   * @param USER_AGENT_PARSE_TABLES $tables
   */
  public function USER_AGENT_PARSER ($tables)
  {
    $this->_tables = $tables;
  }

  public function make_properties_from ($s)
  {
    $Result = new USER_AGENT_PROPERTIES ();

    $parts = null; // Compiler warning
    
//    preg_match_all ('/([a-zA-Z][ \-&a-zA-Z]*)[-\/: ]?[vV]?([0-9][0-9a-z]*([\.-][0-9][0-9a-z]*)*)/', $s, $parts);
    preg_match_all ('/([a-zA-Z]|[a-zA-Z]+[0-9]+|[a-zA-Z]+[ 0-9]+[a-zA-Z]|[a-zA-Z][ \-&a-zA-Z]*[a-zA-Z])[-\/: ]?[vV]?([0-9][0-9a-z]*([\.-][0-9][0-9a-z]*)*)/', $s, $parts);
//    preg_match_all ('/(\w[-&\w ]*[-&\w]*)[-\/: ]([vV]?[0-9][0-9a-z]*([\.-][0-9][0-9a-z]*)*)/', $s, $parts);

    $ids = $parts [1];
    $vers = $parts [2];

    $ignored_ids = $this->_tables->ignored_ids ();
    $system_ids = $this->_tables->system_ids ();
    $renderers = $this->_tables->renderer_ids ();

    $continue_processing = true;
    $index = 0;
    $current_renderer = null;

    while ($continue_processing && ($index < sizeof ($ids)))
    {
      $ver = $this->_extract_version ($vers [$index]);
      $id = strtolower ($ids [$index]);
      
      // Remove the trailing version marker if needed
      
      if (strcasecmp(substr($id, -2), ' v') == 0)
      {
        $id = substr($id, 0, -2);
        $ids [$index] = substr($ids [$index], 0, -2);
      }

      // Don't bother processing ids only one character long
      
      if (strlen($id) > 1)
      {
        if (isset ($renderers [$id]))
        {
          $renderer = $renderers [$id];
  
          if (empty ($current_renderer) || ($current_renderer->renderer_can_be_overridden ()))
          {
            if ($renderer->is_mozilla_gecko ($ver))
            {
              $current_renderer = $renderers [Browser_gecko];
            }
            else
            {
              $current_renderer = $renderer;
            }
  
            if ($id == Browser_gecko)
            {
              $Result->gecko_date = $this->_determine_gecko_date ($ver);
            }
            else
            {
              $current_version = $ver;
            }
          }
        }
  
        if (! isset ($ignored_ids [$id]) && !isset($system_ids[$id]))
        {
          if (empty ($current_renderer) || $current_renderer->browser_can_be_overridden ())
          {
            $Result->version = $ver;
            if (isset ($renderers [$id]))
            {
              $renderer = $renderers [$id];
              if ($renderer->is_mozilla_gecko ($ver))
              {
                $Result->name = 'Mozilla';
              }
              else
              {
                $Result->name = $renderer->display_name;
              }
  
              $continue_processing = $renderer->continue_processing_ids ();
            }
            else
            {
              $Result->name = $ids [$index]; // Use the id in original case
            }
          }
        }
  
        if (isset ($system_ids [$id]))
        {
          $Result->system_name = $system_ids [$id];
          $Result->system_version = $ver;
        }
      }
      
      $index += 1;
    }

    if (! empty ($current_renderer))
    {
      $Result->renderer_id = $current_renderer->id;
      $Result->renderer_name = $current_renderer->technology_name;
      $Result->renderer_version = $current_version;
      @list ($Result->major_version, $Result->minor_version, $Result->build_number) = explode ('.', $Result->renderer_version);

      if (! $current_renderer->browser_can_be_overridden ())
      {
        $Result->name = $current_renderer->display_name;
        $Result->version = $Result->renderer_version;
      }
    }

    $this->_determine_os ($Result, $s);
    $this->_determine_robot ($Result);

    return $Result;
  }

  /**
   * Pull out the version from the given string.
   * @param string $version
   * @return true
   * @access private
   */
  protected function _extract_version ($version)
  {
    $Result = $version;
    if (($Result [0] == 'v') || ($Result [0] == 'V'))
    {
      $Result = substr ($Result, 1);
    }
    return $Result;
  }

  /**
   * Read the gecko publication date from the version number.
   * @param string $version
   * @return DATE_TIME
   * @access private
   */
  protected function _determine_gecko_date ($version)
  {
    $parts = null; // Compiler warning
    preg_match ('/([0-9]{4})([0-9]{2})([0-9]{2})/', $version, $parts);
    if (sizeof ($parts))
    {
      return new DATE_TIME (mktime (0, 0, 0, $parts [2], $parts [3], $parts [1]));
    }
    return null;
  }

  /**
   * Determine the operating system.
   * Since most user agents don't specify an OS with version (Linux is a
   * standout here), we examine the user agent in a non version-specific way.
   * @param USER_AGENT_PROPERTIES $props
   * @param string $raw_data User agent to examine, in lower-case format.
   * @access private
   */
  protected function _determine_os ($props, $raw_data)
  {
    $raw_data = strtolower ($raw_data);
    $props->calculated_system_name = Browser_unknown;
    $oss = $this->_tables->os_ids ();
    while (($props->calculated_system_name == Browser_unknown) && (list ($key, $value) = each ($oss)))
    {
      if ($key)
      {
        $keys = split (',', $key);
        $match = true;
        foreach ($keys as $key)
        {
          $match = $match && ($this->_contains ($raw_data, $key));
        }
        if ($match)
        {
          $props->calculated_system_name = $value;
        }
      }
    }

    /* If the actual system has not been set through other means, use the
     * calculated system.
     */

    if ($props->system_name == Browser_unknown)
    {
      $props->system_name = $props->calculated_system_name;
    }

    /* Set the standard OS id, if one exists. */

    if ($this->_contains ($props->calculated_system_name, 'Windows'))
    {
      $props->os_id = Browser_os_windows;
    }
    if ($this->_contains ($props->calculated_system_name, 'MacOS'))
    {
      $props->os_id = Browser_os_mac;
    }
    if ($this->_contains ($props->calculated_system_name, 'Linux'))
    {
      $props->os_id = Browser_os_linux;
    }
  }

  /**
   * Check if the client is a search engine or robot.
   * Only checks if the {@link $renderer_id} has not already been set.
   * @param USER_AGENT_PROPERTIES $props
   * @access private
   */
  protected function _determine_robot ($props)
  {
    if ($props->renderer_id == Browser_unknown)
    {
      $robot_names = $this->_tables->robot_names ();
      foreach ($robot_names as $n)
      {
        if ($this->_contains (strtolower ($props->name), $n))
        {
          $props->renderer_id = Browser_robot;
        }
      }
    }
  }

  /**
   * @param string $haystack
   * @param string $needle
   * @return bool
   * @access private
   */
  protected function _contains ($haystack, $needle)
  {
    return strpos ($haystack, $needle) !== false;
  }
}

/**
 * Used by the {@link USER_AGENT_PARSER}.
 * The tables contain browser- and user agent-specific information built up from
 * a database of known user agents.
 * @package webcore
 * @subpackage util
 * @version 3.1.0
 * @since 2.7.0
 * @access private
 */
class USER_AGENT_PARSE_TABLES
{
  /**
   * A list of the known rendering technologies.
   * Each id maps to the rendering technology to record (retrieved later with the 'is' function),
   * a pretty-printed name for the renderer and a level. The level determines which renderers override
   * others if more than one renderer is specified. That is, almost every user-agent on the planet
   * returns mozilla x.x, but that renderer declaration is overridden by any other one that comes along.
   * IE is the next weakest because many user agents spoof as IE as well. If either or both of these are
   * specified, they are recorded, but if any other renderer is specified, that one is used instead.
   * @see USER_AGENT_RENDERER_INFO
   * @return array[string,USER_AGENT_RENDERER_INFO]
   */
  public function renderer_ids ()
  {
    return array (
    	'mozilla' => new USER_AGENT_RENDERER_INFO (Browser_netscape_4, 'Netscape 4.x', User_agent_temporary_renderer),
			'msie' => new USER_AGENT_RENDERER_INFO (Browser_ie, 'Trident (IE)', User_agent_temporary_renderer, 'Internet Explorer'),
			'rv' => new USER_AGENT_RENDERER_INFO (Browser_gecko, 'Gecko', User_agent_temporary_renderer, 'Mozilla'),
			'gecko' => new USER_AGENT_RENDERER_INFO (Browser_gecko, 'Gecko', User_agent_temporary_renderer, 'Mozilla'),
			'shiira' => new USER_AGENT_RENDERER_INFO (Browser_khtml, 'Webcore', User_agent_final_browser_abort, 'Shiira'),
			'applewebkit' => new USER_AGENT_RENDERER_INFO (Browser_khtml, 'Webcore', User_agent_final_renderer),
			'netscape6' => new USER_AGENT_RENDERER_INFO (Browser_gecko, 'Netscape', User_agent_final_browser),
			'chrome' => new USER_AGENT_RENDERER_INFO (Browser_khtml, 'Google Chrome', User_agent_final_browser_abort),
			'opera' => new USER_AGENT_RENDERER_INFO (Browser_opera, 'Presto (Opera)', User_agent_final_browser, 'Opera'),
			'konqueror' => new USER_AGENT_RENDERER_INFO (Browser_khtml, 'KHTML', User_agent_final_browser, 'Konqueror'),
			'omniweb' => new USER_AGENT_RENDERER_INFO (Browser_omniweb, 'OmniWeb', User_agent_final_browser),
			'webtv' => new USER_AGENT_RENDERER_INFO (Browser_webtv, 'WebTV', User_agent_final_browser),
			'googlebot' => new USER_AGENT_RENDERER_INFO (Browser_robot, 'Google Robot', User_agent_final_browser),
			'msnbot' => new USER_AGENT_RENDERER_INFO (Browser_robot, 'MSN Robot', User_agent_final_browser),
			'yahooseeker' => new USER_AGENT_RENDERER_INFO (Browser_robot, 'Yahoo Robot', User_agent_final_browser),
			'googlebot-image' => new USER_AGENT_RENDERER_INFO (Browser_robot, 'Google Robot', User_agent_final_browser),
			'lynx' => new USER_AGENT_RENDERER_INFO (Browser_text, 'Text', User_agent_final_browser, 'Lynx'),
			'icab' => new USER_AGENT_RENDERER_INFO (Browser_icab, 'iCab', User_agent_final_browser),
			'applesyndication' => new USER_AGENT_RENDERER_INFO (Browser_newsreader, 'Safari Newsreader', User_agent_final_browser),
			'yahoofeedseeker' => new USER_AGENT_RENDERER_INFO (Browser_newsreader, 'Yahoo Newsreader', User_agent_final_browser),
			'newsgatoronline' => new USER_AGENT_RENDERER_INFO (Browser_newsreader, 'NewsGator', User_agent_final_browser),
			'bloglines' => new USER_AGENT_RENDERER_INFO (Browser_newsreader, 'Bloglines', User_agent_final_browser),
			'facebookexternalhit' => new USER_AGENT_RENDERER_INFO (Browser_previewer, 'Facebook Preview', User_agent_final_browser),
    );
  }

  /**
   * A list of ids known to be spurious or system ids.
   * The algorithm always uses the last non-ignored id as the browser id; this
   * list determines which ids are ignored.
   * @return array[string,boolean]
   */
  public function ignored_ids ()
  {
    return array ('windows' => 1,
                  'windows nt' => 1,
                  'win 9x' => 1,
                  'linux' => 1,
                  'debian' => 1,
                  'suse' => 1,
                  'amigaos' => 1,
                  'gecko' => 1,                 // gecko build date
                  'rv' => 1,                    // gecko version number
                  'libwww-fm' => 1,             // lynx
                  'openssl' => 1,               // lynx
                  'ssl-mm' => 1,                // lynx
                  'ycomp' => 1,                 // IE plugin
                  'sbcydsl' => 1,               // IE plugin
                  'hotbar' => 1,                // IE plugin
                  'yplus' => 1,                 // IE plugin
                  'net clr' => 1,               // msn
                  'i386-unknown-freebsd' => 1,
                  'libcurl' => 1,
                  'r1' => 1,
                  'ssl' => 1,
                  'debian package' => 1,
                  'winfx runtime' => 1,         // windows vista
                  'avalon' => 1,                // windows vista
                  'tablet pc' => 1,             // windows vista
                  'sl commerce client' => 1,    // windows vista
                  'cldc' => 1,                  // Nokia phone
                  'midp' => 1,                  // Nokia phone
                  'views' => 1,                 // Newsfeed readers
                  'users' => 1,                  // Newsfeed readers
                  'ipv' => 1,
                  'ssl' => 1,
                  'linux i' => 1,
                  );
  }

  /**
   * A list of systems known to provide version info in the user agent.
   * @return array[string,string]
   */
  public function system_ids ()
  {
    return array ('windows nt' => 'Windows NT',
                  'win 9x' => 'Windows 9x',
                  'linux' => 'Linux',
                  'debian' => 'Debian',
                  'amigaos' => 'AmigaOS',
                  'debian package' => 'Debian',
                  'suse' => 'SUSE',
                  'series80' => 'Series 80',
                  'winnt' => 'Windows NT',
                  'freebsd' => 'FreeBSD',
    );
  }

  /**
   * A list of names that are commonly robots.
   * Since there are so many different robot clients, we look for common names
   * in the renderer/browser name to mark unknown browsers as robots.
   * @return array[string,boolean]
   */
  public function robot_names ()
  {
    return array ('crawler',
                  'spider',
                  'bot',
                  'robot'
                  );
  }

  /**
   * A mapping of user agent fragments to platform ids.
   * The platform id is a nicely formatted, standardized name for the operating system. This
   * array maps the different user agent platform ids onto these standard ones. (e.g. 'nt 4'
   * and 'nt4' both map onto 'Windows NT 4.x').
   * @return array[string,string]
   */
  public function os_ids ()
  {
    return array ('win,nt 6.0' => 'Windows Vista',
                  'win,nt 5.1' => 'Windows XP',
                  'win,nt 5' => 'Windows 2000',
                  'win,2000' => 'Windows 2000',
                  'win,98' => 'Windows 98',
                  'win,95' => 'Windows 95',
                  'win,nt 4' => 'Windows NT 4.x',
                  'win,nt4' => 'Windows NT 4.x',
                  'win,nt 3' => 'Windows NT 3.x',
                  'win,nt' => 'Windows NT',
                  'win,16' => 'Windows 3.x',
                  'win' => 'Windows',
                  'mac,68k' => 'MacOS 68k',
                  'mac,68000' => 'MacOS 68k',
                  'mac,os x' => 'Mac OS X',
                  'mac,ppc' => 'MacOS PPC',
                  'mac,powerpc' => 'MacOS PPC',
                  'macintosh' => 'MacOS',
                  'applesyndication' => 'Mac OS X', // Safari newsreader
                  'linux' => 'Linux',
                  'series80' => 'Series 80',
                  'amigaos' => 'AmigaOS',
                  'beos' => 'BeOS',
                  'os/2' => 'OS/2',
                  'webtv' => 'WebTV',
                  'sunos' => 'Sun/Solaris',
                  'irix' => 'Irix',
                  'hpux' => 'HP Unix',
                  'aix' => 'AIX',
                  'dec' => 'DEC-Alpha',
                  'alpha' => 'DEC-Alpha',
                  'osf1' => 'DEC-Alpha',
                  'ultrix' => 'DEC-Alpha',
                  'sco' => 'SCO',
                  'unix_sv' => 'SCO',
                  'vax' => 'VMS',
                  'openvms' => 'VMS',
                  'sinix' => 'Sinix',
                  'reliantunix' => 'Reliant/Unix',
                  'freebsd' => 'FreeBSD',
                  'openbsd' => 'OpenBSD',
                  'netbsd' => 'NetBSD',
                  'bsd' => 'BSD',
                  'unix_system_v' => 'UnixWare',
                  'ncr' => 'MPRAS',
                  'x11' => 'Unix'
                  );
  }
}

/**
 * Marks a potential renderer and browser.
 * A {@link USER_AGENT_RENDERER_INFO} marked as such will be used for the
 * renderer and browser only if no other renderers or browsers are detected.
 * @see User_agent_final_renderer
 * @see User_agent_final_browser
 */
define ('User_agent_temporary_renderer', 1);

/**
 * Marks a renderer that is not necessarily the final browser.
 * A {@link USER_AGENT_RENDERER_INFO} marked as such will be used for the
 * renderer, but can be replaced as browser by an ensuing name/version pair.
 * @see User_agent_temporary_renderer
 * @see User_agent_final_browser
 */
define ('User_agent_final_renderer', 2);

/**
 * Marks a renderer that cannot be replaced.
 * A {@link USER_AGENT_RENDERER_INFO} marked as such will be used for both
 * renderer and browser information if detected in the user agent.
 * @see User_agent_final_renderer
 * @see User_agent_final_browser
 */
define ('User_agent_final_browser', 3);

/**
 * Marks a final browser name (skips remaining entries).
 * Used only for special cases where a browser sticks its name in front of
 * another valid browser name (that cannot be added to the ignored ids).
 * The "Shiira" browser is such a case as it adds the "Safari" at the end of
 * the user agent.
 */
define ('User_agent_final_browser_abort', 4);

/**
 * Properties for a registered renderer.
 * The {@link USER_AGENT_PROPERTIES::_renderer_ids()} function returns an array
 * of these to use during detection.
 * @package webcore
 * @subpackage util
 * @version 3.1.0
 * @since 2.7.0
 * @access private
 */
class USER_AGENT_RENDERER_INFO
{
  /**
   * Identifier for the renderer.
   * May be {@link Browser_gecko}, {@link Browser_opera} or any of the other
   * browser constants.
   * @var string
   */
  public $id;

  /**
   * Code name for the rendering technology.
   * May be the same as {@link $display_name}.
   * @var string
   */
  public $technology_name;

  /**
   * Replacement for the name found in the user agent.
   * Often the renderer is identified by a non-user-friendly id (e.g. MSIE)
   * instead of the name of the browser. This is the name to use for the browser
   * if no other name is given.
   */
  public $display_name;

  /**
   * Defines the precedence for this renderer during detection.
   * Uses {@link User_agent_temporary_renderer}, {@link
   * User_agent_final_renderer}, {@link User_agent_final_browser}.
   * @var integer
   */
  public $precedence;

  /**
   * Create a description for a renderer.
   * Uses {@link $technology_name} if {@link $display_name} is empty.
   * @param string $id Value for the {@link $id}.
   * @param string $tech Value for the {@link $technology_name}.
   * @param string $disp Value for the {@link $display_name}.
   * @param integer $prec Value for the {@link $precedence}.
   */
  public function USER_AGENT_RENDERER_INFO ($id, $tech, $prec, $disp = '')
  {
    $this->id = $id;
    $this->technology_name = $tech;
    if ($disp)
    {
      $this->display_name = $disp;
    }
    else
    {
      $this->display_name = $tech;
    }
    $this->precedence = $prec;
  }

  /**
   * @return boolean
   */
  public function renderer_can_be_overridden ()
  {
    return $this->precedence == User_agent_temporary_renderer;
  }

  /**
   * @return boolean
   */
  public function browser_can_be_overridden ()
  {
    return $this->precedence != User_agent_final_browser;
  }

  /**
   * @return boolean
   */
  public function continue_processing_ids ()
  {
    return $this->precedence != User_agent_final_browser_abort;
  }

  /**
   * Return <code>True</code> if the browser is Mozilla.
   * If the {@link $id} is {@link Browser_netscape_4}, but the version is
   * greater than 4, it's a Mozilla browser, not Netscape 4 (they use the same
   * technology id).
   * @param string $ver
   * @return boolean
   */
  public function is_mozilla_gecko ($ver)
  {
    return ($this->id == Browser_netscape_4) && ($ver [0] > 4);
  }
}

?>