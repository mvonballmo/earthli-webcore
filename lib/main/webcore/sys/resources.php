<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.5.0
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
require_once ('webcore/sys/system.php');
require_once ('webcore/sys/url.php');
require_once ('webcore/config/text_config.php');

/**
 * Resolves paths against an alias-URL dictionary.
 * Use {@link set_path()} to assign a URL to an alias. URLs can be based on other
 * resources, so that a 'pictures' alias is based on a 'resources' one. Use
 * {@link inherit_resources_from()} to use another resource manager's aliases as a fallback.
 * to other resources. Use {@link resolve_file()}, {@link resolve_path()},
 * {@link resolve_path_for_alias()} and {@link resolve_file_for_alias()} to
 * retrieve a fully- resolved URL. (without aliases).
 *
 * Only aliases specified at the beginning of a URL fragment are resolved; aliases are
 * delimited in text with {@link $alias_open_delimiter} and {@link $alias_close_delimiter}.
 * References to empty or nonexistent aliases are elided from the output.
 *
 * If {@link $resolve_to_root} is True, {@link $root_url} is prepended to resolved URLs
 * (if not already present).
 *
 * Alias caching is enabled by default to provide a significant performance
 * boost when calculating many similar paths. Use {@link set_caching_enabled()} to
 * turn it off.
 *
 * @package webcore
 * @subpackage sys
 * @version 3.5.0
 * @since 2.7.0
 */
class RESOURCE_MANAGER extends RAISABLE
{
  /**
   * Denotes the start of an alias in a URL fragment.
   * @var string
   */
  public $alias_open_delimiter = '{';

  /**
   * Denotes the end of an alias in a URL fragment.
   * @var string
   */
  public $alias_close_delimiter = '}';

  /**
   * Denotes a local anchor in a URL.
   * @var string
   */
  public $anchor_delimiter = '#';

  /**
   * Should urls be expanded to the root?
   * Use {@link set_root_behavior()} and {@link restore_root_behavior()} to
   * set this value properly for chained resource managers.
   * @var boolean
   */
  public $resolve_to_root = false;

  /**
   * What is the absolute root of all resources?
   * This value is prepended to resolved URLs if {@link $resolve_to_root} is True.
   * This allows a manager to resolve URLs regardless of context (when displayed in
   * an email, for instance).
   */
  public $root_url = '';

  /**
   * Is alias-caching enabled?
   * Set this value with {@link set_caching_enabled()}.
   * @var boolean
   */
  public $caching_enabled = true;

  public function __construct ()
  {
    $this->_url_options = global_url_options ();
    $this->_text_options = global_text_options ();
    $this->restore_root_behavior ();
  }

  /**
   * Inherit resource patterns from this object.
   * @param RESOURCE_MANAGER $parent
   */
  public function inherit_resources_from ($parent)
  {
    $this->_parent_resources = $parent;
  }

  /**
   * Specify which file options to use when building URLs.
   * @param FILE_OPTIONS $options
   */
  public function set_url_options ($options)
  {
    $this->_url_options = $options;
  }

  /**
   * Specify which text options to use when formatting HTML.
   * @param TEXT_OPTIONS $options
   */
  public function set_text_options ($options)
  {
    $this->_text_options = $options;
  }

  /**
   * Turn alias-caching on or off.
   * Caching improves performance, but the implemenation only invalidates the
   * cache for an alias in the affected resource manager; others that used
   * {@link inherit_resources_from()} are not automatically invalidated.
   *
   * Pages that simply set aliases initially and do not change them can safely
   * leave caching enabled.
   *
   * @param boolean $value
   */
  public function set_caching_enabled ($value)
  {
    if ($this->caching_enabled != $value)
    {
      $this->caching_enabled = $value;
      $this->clear_cache ();
    }
  }

  /**
   * Remove all cached paths.
   * @see set_caching_enabled()
   */
  public function clear_cache ()
  {
    $this->_cache = array ();
  }

  /**
   * Set the path for an alias.
   * @param string $alias
   * @param string $path
   */
  public function set_path ($alias, $path)
  {
    $this->_paths [$alias] = $path;
    $this->_notify_listeners ($alias, $path);
    unset ($this->_cache [$alias]);
  }

  /**
   * Append a path to the path for the alias.
   * @param string $alias
   * @param string $path
   */
  public function add_to_path ($alias, $path)
  {
    $this->set_path ($alias, join_paths ($this->path_for_alias ($alias), $path, $this->_url_options));
  }

  /**
   * Set the default extension for an alias.
   * @param string $alias
   * @param string $ext
   */
  public function set_extension ($alias, $ext)
  {
    $this->_extensions [$alias] = $ext;
  }

  /**
   * Declare an alias that doesn't use the {@link $link_url}.
   * URLs that begin with this alias will always be processed using
   * 'force_root' instead of the 'root_override' parameter passed to
   * the method.
   * @param string $alias
   * @param boolean $force_root
   */
  public function set_forced_root ($alias, $force_root)
  {
    $this->_forced_roots [$alias] = $force_root;
  }

  /**
   * Add a listener to call when a path is changed.
   * Sends a message when the path associated with an alias is
   * changed with {@link set_path()}.
   * @param WEBCORE_CALLBACK $listener
   */
  public function add_listener ($listener)
  {
    if (! isset ($this->_listeners))
    {
      include_once ('webcore/sys/callback.php');
      $this->_listeners = new CALLBACK_LIST ();
    }
    $this->_listeners->add_item ($listener);
  }

  /**
   * Return the path registered with the given alias.
   * Does not resolve aliases contained within the registered path. Use {@link resolve_path()}
   * or {@link resolve_file()} to completely evaluate a URL.
   * @param string $alias
   * @return string
   */
  public function path_for_alias ($alias)
  {
    $Result = '';
    if (isset ($this->_paths [$alias]))
    {
      $Result = $this->_paths [$alias];
    }
    else
    {
      if (isset ($this->_parent_resources))
      {
        $Result = $this->_parent_resources->path_for_alias ($alias);
      }
    }
    return $Result;
  }

  /**
   * Return the extension registered with the given alias.
   * Returns empty if there is no default extension for the alias.
   * @param string $alias
   * @return string
   */
  public function extension_for_alias ($alias)
  {
    $Result = '';
    if (isset ($this->_extensions [$alias]))
    {
      $Result = $this->_extensions [$alias];
    }
    else
    {
      if (isset ($this->_parent_resources))
      {
        $Result = $this->_parent_resources->extension_for_alias ($alias);
      }
    }
    return $Result;
  }

  /**
   * Return the root override registered with the given alias.
   * Returns empty if there is no root override for the alias.
   * @param string $alias
   * @return string
   */
  public function forced_root_for_alias ($alias)
  {
    $Result = null;
    if (isset ($this->_forced_roots [$alias]))
    {
      $Result = $this->_forced_roots [$alias];
    }
    else
    {
      if (isset ($this->_parent_resources))
      {
        $Result = $this->_parent_resources->forced_root_for_alias ($alias);
      }
    }
    return $Result;
  }

  /**
   * Return the fully-resolved path registered with the given alias.
   * Use {@link path_for_alias()} to return the registered path without resolving aliases
   * within the path itself.
   * @param string $alias
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   */
  public function path_to ($alias, $root_override = null)
  {
    return $this->resolve_path_for_alias ($alias, '', $root_override);
  }

  /**
   * Fully expands a URL file fragment containing an alias.
   * Aliases are recursively resolved using repeated calls to {@link resolve_file_using_alias()}
   * until the URL is complete (contains no aliases). The $fragment is assumed to be a file; if
   * it does not have an extension, the default extension for the given alias is applied to the
   * last directory-separated element in the fragment. Use {@link resolve_path()} to expand a path
   * without applying a default file extension.
   *
   * Assume the alias 'pictures' resolves to '/users/jon/pictures/'. Using an 'alias'
   * of 'pictures', which has a default extension of 'jpg', the fragment
   * 'stuff/uploads/pic_of_junior' resolves to '/users/jon/pictures/stuff/uploads/pic_of_junior.jpg'.
   *
   * @param string $fragment
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   * @see resolve_path()
   * @see resolve_file_for_alias()
   * @see resolve_path_for_alias()
   * @see RESOURCE_MANAGER for general documentation on aliases.
   */
  public function resolve_file ($fragment, $root_override = null)
  {
    list ($alias, $url) = $this->_extract_alias_and_resolved_path ($fragment, $root_override);
    return $this->_apply_extension_for_alias ($alias, $url);
  }

  /**
   * Fully expands a URL path fragment containing an alias.
   * Aliases are recursively resolved using repeated calls to {@link join_paths()}
   * until the URL is complete (contains no aliases). Use {@link resolve_file()} to expand a
   * file, applying a default extension, if necessary.
   * @param string $fragment
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to {@link Force_root_on}.
   * @return string
   * @see resolve_file()
   * @see resolve_file_for_alias()
   * @see resolve_path_for_alias()
   * @see RESOURCE_MANAGER for general documentation on aliases.
   */
  public function resolve_path ($fragment, $root_override = null)
  {
    list ($alias, $url) = $this->_extract_alias_and_resolved_path ($fragment, $root_override);
    return ensure_ends_with_delimiter ($url, $this->_url_options);
  }

  /**
   * Fully resolve the given alias, appending the fragment as a file.
   * Resolve all aliases in 'alias' recursively; the 'fragment' may not contain aliases
   * and is appended directly to the expanded path.
   * @param string $alias
   * @param string $fragment
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to {@link Force_root_on}.
   * @see resolve_path_for_alias()
   * @see resolve_file()
   * @see resolve_path()
   * @param string $alias
   * @param string $fragment
   * @return string */
  public function resolve_file_for_alias ($alias, $fragment, $root_override = null)
  {
    $Result = $this->_resolve_path_for_alias ($alias, $fragment, $root_override);
    return $this->_apply_extension_for_alias ($alias, $Result);
  }

  /**
   * Fully resolve the given alias, appending the fragment as a path.
   * Resolve all aliases in 'alias' recursively; the 'fragment' may not contain aliases
   * and is appended directly to the expanded path.
   * @param string $alias
   * @param string $fragment
   * @param boolean $root_override Overrides {@link $resolve_to_root} if True.
   * @see resolve_file_for_alias()
   * @see resolve_file()
   * @see resolve_path()
   * @param string $alias
   * @param string $fragment
   * @return string
   */
  public function resolve_path_for_alias ($alias, $fragment, $root_override = null)
  {
    $Result = $this->_resolve_path_for_alias ($alias, $fragment, $root_override);
    return ensure_ends_with_delimiter ($Result, $this->_url_options);
  }

  /**
   * Resolve the file fragment as an HTML image.
   * @param string $fragment location of icon.
   * @param string $text used for the alt and title attributes.
   * @param string $size The size of icon to render; defaults to Sixteen_px.
   * @param string $style an optional CSS style (not a class).
   * @param int|string $dom_id An optional DOM id to allow JavaScript access to the image.
   * @return string
   */
  public function resolve_icon_as_html ($fragment, $text, $size = '', $style = 'vertical-align: middle', $dom_id = 0)
  {
    return $this->_image_as_html ($this->get_icon_url ($fragment, $size), $text, $style, $dom_id);
  }

  /**
   * Get the URL for the requested icon size.
   * Returns an icon 'size' which conforms to the WebCore naming conventions for icon sizes. Sized icons
   * have several files, all in the same folder. If a size is specified, it is appended to the file name
   * with a preceding underscore. e.g. get_icon_url ('logo', Sixteen_px) returns logo_16px. This algorithm is
   * subject to change.
   * @param string $base_url Location of the icon file.
   * @param string $size Size modifier to use to find the correct icon.
   * @return string
   */
  public function get_icon_url ($base_url, $size)
  {
    if ($base_url)
    {
      if ($size)
      {
        $url = new URL ($base_url);
        $url->append_to_name ('_' . $size);
        $Result = $url->as_text ();
      }
      else
      {
        $Result = $base_url;
      }

      return $this->resolve_file ($Result);
    }

    return '';
  }

  /**
   * Set whether URLs are resolved to {@link $root_url} by default.
   * @param boolean $value Can be {@link Force_root_on} or {@link
   * Force_root_off}.
   * @see restore_root_behavior()
   */
  public function set_root_behavior ($value)
  {
    $this->resolve_to_root = $value;
    if (isset ($this->_parent_resources))
    {
      $this->_parent_resources->set_root_behavior ($value);
    }
  }

  /**
   * Reset whether {@link $root_url} is used by default.
   * Callers should set the desired value using {@link set_root_behavior()} and
   * restore it using this function, so that the resource manager can maintain the
   * flag correctly. The default sets it back to False, but descendents can impose
   * their own default value.
   * @see set_root_behavior()
   */
  public function restore_root_behavior ()
  {
    $this->resolve_to_root = $this->_default_resolve_to_root ();
    if (isset ($this->_parent_resources))
    {
      $this->_parent_resources->restore_root_behavior ();
    }
  }

  /**
   * Trigger a notification for the given alias.
   * Call this method if an external change is made to paths (e.g. a path
   * modifier is changed and must be re-appended in the event handler).
   * @param string $alias
   */
  public function refresh ($alias)
  {
    $path = $this->path_for_alias ($alias);
    if ($path)
    {
      $this->_notify_listeners ($alias, $path);
    }
  }

  /**
   * Notify attached listeners of a change in path or extension.
   * This also ensures that the listener list is properly locked and avoids a
   * notification "echo" (duplicate events).
   * @param string $alias
   * @param string $path
   * @access private
   */
  protected function _notify_listeners ($alias, $path)
  {
    if (isset ($this->_listeners))
    {
      $this->_listeners->execute (array ($this, $alias, $path));
    }
  }

  /**
   * Called from {@link restore_root_behavior()}.
   * Override this function to customize the default value.
   * @return boolean
   * @access private
   */
  protected function _default_resolve_to_root ()
  {
    return false;
  }

  /**
   * Return an alias and expanded URL given a URL fragment.
   * Recursively expand the URL fragment, returning the fully expanded URL and the original
   * alias (if any). This function is used internally to resolve all aliases in an URL fragment,
   * but allow higher-level functions to perform URL post-processing based on the alias given.
   * {@link resolve_file()} uses this alias to assign a default extension based on the alias
   * given in the fragment.
   * @param string $fragment
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to {@link Force_root_on}.
   * @return string[]
   * @access private
   */
  protected function _extract_alias_and_resolved_path ($fragment, $root_override)
  {
    list ($alias, $fragment) = $this->_extract_alias ($fragment);
    $fragment = $this->_resolve_path_for_alias ($alias, $fragment, $root_override);
    return array ($alias, $fragment);
  }

  /**
   * Fully resolve the given alias, appending the fragment as a path.
   * Resolve all aliases in 'alias' recursively; the 'fragment' may not contain aliases
   * and is appended directly to the expanded path. Used internally by
   * {@link resolve_path_for_alias()} and {@link resolve_file_for_alias()}.
   * @param string $alias
   * @param string $fragment
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to {@link Force_root_on}.
   * @return string
   * @access private
   */
  protected function _resolve_path_for_alias ($alias, $fragment, $root_override)
  {
    if ($alias != '')
    {
      $forced_root = $this->forced_root_for_alias ($alias);
      if (isset ($forced_root))
      {
        $root_override = $forced_root;
      }

      if ($this->caching_enabled && isset ($this->_cache [$alias]))
      {
        $fragment = join_paths ($this->_cache [$alias], $fragment, $this->_url_options);
      }
      else
      {
        $orig_alias = $alias;
        $orig_fragment = $fragment;
        while ($alias != '')
        {
          $fragment = join_paths ($this->path_for_alias ($alias), $fragment, $this->_url_options);
          list ($alias, $fragment) = $this->_extract_alias ($fragment);
        }
        if ($this->caching_enabled)
        {
          $len = strlen ($orig_fragment);
          if ($len)
          {
            $this->_cache [$orig_alias] = substr ($fragment, 0, -strlen ($orig_fragment));
          }
          else
          {
            $this->_cache [$orig_alias] = substr ($fragment, 0);
          }
        }
      }
    }

    return $this->_finalize_url ($fragment, $root_override);
  }

  /**
   * Called on a fully-resolved URL before returning it.
   * Once all aliases in a URL have been expanded, it is expanded to the root if
   * {@link $resolve_to_root} is True. URLs beginning with a {@link $local_anchor}
   * or a domain are not expanded (both can be resolved without a relative context).
   * @see _can_have_root()
   * @param string $url
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to {@link Force_root_on}.
   * @return string
   * @access private
   */
  protected function _finalize_url ($url, $root_override)
  {
    if ($this->_needs_root ($url, $root_override))
    {
      $url = join_paths ($this->root_url, $url, $this->_url_options);
    }
    if (isset ($this->_parent_resources))
    {
      $url = $this->_parent_resources->_finalize_url ($url, $root_override);
    }
    return $url;
  }

  /**
   * Return a (possibly empty) alias and URL fragment.
   * Result is an array with two elements, the alias and the remaining fragment.
   * If there is no alias in the fragment, the first part is empty and the second
   * contains the entire unchanged fragment. The remaining fragment will never
   * contain aliases (i.e. it is always a completely converted URL).
   * @param string $fragment
   * @return string[]
   */
  protected function _extract_alias ($fragment)
  {
    $alias = '';
    if ($fragment)
    {
      if ($fragment [0] == $this->alias_open_delimiter)
      {
        $end_of_alias = strpos ($fragment, $this->alias_close_delimiter, 1);
        $alias = substr ($fragment, 1, $end_of_alias - 1);
        if ($end_of_alias == strlen ($fragment) - 1)
        {
          $fragment = '';
        }
        else
        {
          if ($fragment [$end_of_alias + 1] == $this->_url_options->path_delimiter)
          {
            $fragment = substr ($fragment, $end_of_alias + 2);
          }
          else
          {
            $fragment = substr ($fragment, $end_of_alias + 1);
          }
        }
      }
    }

    return array ($alias, $fragment);
  }

  /**
   * Adds the default registered extension if necessary.
   * Adds an extension to 'fragment' if it does not end in a delimiter, does not already
   * have an extension and there is a default extension registered for 'alias'.
   * @param string $alias
   * @param string $fragment
   * @return string
   * @access private
   */
  protected function _apply_extension_for_alias ($alias, $fragment)
  {
    if (! ends_with_delimiter ($fragment, $this->_url_options) && ! is_file_name ($fragment, $this->_url_options))
    {
      $ext = $this->extension_for_alias ($alias);
      if ($ext)
      {
        $fragment .= '.' . $ext;
      }
    }

    return $fragment;
  }

  /**
   * Determines whether the root needs to be prepended to the given url.
   * If the url already has a domain, if it starts with an {@link
   * $anchor_delimiter} (assumes the link will be resolvable in page context).
   * or if it starts with a delimiter and the root does not have a domain, no
   * root can be prepended. If the url is empty, the root can be prepended.
   *
   * @see _finalize_url()
   *
   * @param string $url The url to resolve.
   * @param bool $root_override If null, no override is applied; if true, the root is applied; otherwise, no root is applied.
   * @return boolean
   *
   * @access private
   */
  protected function _needs_root ($url, $root_override)
  {
    /* Check that there is a root and that it is desired. */
    $Result = $this->root_url && ($this->resolve_to_root || ! empty ($root_override)) && ! (isset ($root_override) && ! $root_override);
    /* Check that the url is empty or does not begin with an anchor. */
    if ($Result)
    {
      $Result = ! $url || ($url [0] != $this->anchor_delimiter);
    }
    /* Check that the root is not already present. */
    if ($Result)
    {
      $Result = strpos ($url, $this->root_url) !== 0;
    }
    /* Check that the url does not already have a domain. */
    if ($Result)
    {
      $Result = ! has_domain ($url, '', $this->_url_options);
    }
    /* Check that the root has a domain or that the url does not begin
     * with a delimiter.
     */
    if ($Result)
    {
      $Result = has_domain ($this->root_url, '', $this->_url_options) || ! begins_with_delimiter ($url, $this->_url_options);
    }
    return $Result;
  }

  /**
   * Render an image as HTML.
   * @param string $url location of icon.
   * @param string $text used for the alt and title attributes.
   * @param string $style an optional CSS style (not a class).
   * @param int|string $dom_id an optional DOM id to allow JavaScript access to the image.
   * @return string
   */
  private function _image_as_html ($url, $text, $style = 'vertical-align: middle', $dom_id = 0)
  {
    if ($url)
    {
      $text = $this->_text_options->convert_to_html_attribute ($text);
      $Result = "<img src=\"$url\" title=\"$text\" alt=\"$text\"";
      if ($dom_id)
      {
        $Result .= " id=\"$dom_id\"";
      }
      if ($style)
      {
        $Result .= " style=\"$style\"";
      }
      $Result .= ">";
      return $Result;
    }

    return '';
  }

  /**
   * Use these options to manipulate URLs.
   * @var FILE_OPTIONS
   * @access private
   */
  protected $_url_options;

  /**
   * Use these options to format HTML text.
   * @var TEXT_OPTIONS
   * @access private
   */
  protected $_text_options;

  /**
   * Inherit resources from this manager.
   * @var RESOURCE_MANAGER
   * @access private
   */
  protected $_parent_resources;

  /**
   * Alias to path mapping.
   * Each path is a legal url with an optional alias prefix.
   * @var string[]
   * @access private
   */
  protected $_paths;

  /**
   * Alias to extension mapping.
   * @var string[]
   * @access private
   */
  protected $_extensions;

  /**
   * Record aliases that force root resolution.
   * @var boolean[]
   * @access private
   */
  protected $_forced_roots;

  /**
   * List of listeners for changes to aliases.
   * Add listeners with {@link add_listener()} and receive a message
   * when a path is changed with {@link set_path()}.
   * @var CALLBACK_LIST
   */
  protected $_listeners;

  /**
   * Maps aliases to expanded paths.
   * Provides a significant performance boost. Paths are invalidated when
   * changed with {@link set_path()} or {@link add_path()}. Changes to {@link
   * $_parent_resources} do not cause the cache to clear.
   * @var string[]
   * @access private
   */
  protected $_cache;
}