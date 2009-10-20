<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.2.0
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
require_once ('webcore/obj/storable.php');

/**
 * Manages and displays a title.
 * The title can be emitted as html text, an html link or plain text.
 * @package webcore
 * @subpackage obj
 * @version 3.2.0
 * @since 2.2.1
 * @abstract
 */
abstract class NAMED_OBJECT extends STORABLE
{
  /**
   * An object that knows how to format this object's name.
   * Filled in with defaults. Call this function if you need to adjust the default
   * formatting for a title or link. Make your changes on the returned object, then
   * pass it back in to {@link NAMED_OBJECT::title_as_link()}, {@link NAMED_OBJECT::title_as_html()}
   * or {@link NAMED_OBJECT::title_as_plain_text()} for custom behavior.
   * @return TITLE_FORMATTER
   */
  public function title_formatter ()
  {
    $Result = $this->context->title_formatter ();
    if (isset ($this->app))
    {
      $Result->max_visible_output_chars = $this->app->max_title_size (get_class ($this));
    }
    $Result->location = $this->home_page ();
    $Result->text = $this->raw_title ();
    $Result->CSS_class = 'visible';
    $Result->title = '';
    return $Result;
  }

  /**
   * A string representing a link to this object's url.
   * Uses the user-defined object home page and user-defined maximum title length.
   * @param TITLE_FORMATTER $formatter Optional formatter to use.
   * @return string
   */
  public function title_as_link ($formatter = null)
  {
    if (! isset ($formatter))
    {
      $formatter = $this->title_formatter ();
    }

    return $formatter->as_html_link ();
  }

  /**
   * A string showing this object's title.
   * Uses the user-defined maximum title length.
   * @param TITLE_FORMATTER $formatter Optional formatter to use.
   * @return string
   */
  public function title_as_html ($formatter = null)
  {
    if (! isset ($formatter))
    {
      $formatter = $this->title_formatter ();
      $formatter->max_visible_output_chars = 0;
    }

    return $formatter->as_html_text ();
  }

  /**
   * A string showing this object's title.
   * Uses the user-defined maximum title length.
   * @param TITLE_FORMATTER $formatter Optional formatter to use.
   * @return string
   */
  public function title_as_plain_text ($formatter = null)
  {
    if (! isset ($formatter))
    {
      $formatter = $this->title_formatter ();
    }

    return $formatter->as_plain_text ();
  }

  /**
   * Location within object hierarchy.
   * Return the scope of this object within the system, ending with the object itself. This is
   * an path to the object with the tree of objects, not a URL to the object's detail page; use
   * {@link home_page()} instead.
   * @param string $separator Optional separator. If not set, {@link APPLICATION_DISPLAY_OPTIONS::$obj_url_separator} is used.
   * @param TITLE_FORMATTER $formatter Optional formatter to use.
   * @return string
   */
  public function object_url_as_text ($separator = null, $formatter = null)
  {
    return $this->_object_url (false, $separator, $formatter);
  }

  /**
   * Linked location within object hierarchy.
   * Return the scope of this object within the system, ending with the object itself. This is
   * an path to the object with the tree of objects, not a URL to the object's detail page; use
   * {@link home_page()} instead.
   * @param string $separator Optional separator. If not set, {@link APPLICATION_DISPLAY_OPTIONS::$obj_url_separator} is used.
   * @param TITLE_FORMATTER $formatter Optional formatter to use.
   * @return string
   */
  public function object_url_as_link ($separator = null, $formatter = null)
  {
    return $this->_object_url (true, $separator, $formatter);
  }

  /**
   * This object's home page (with query arguments).
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   */
  public function home_page ($root_override = null)
  {
    $page_name = $this->page_name ();
    $page_args = $this->page_arguments ();
    if (! empty ($page_args))
    {
      $page_name .= '?' . $page_args;
    }
    return $this->context->resolve_file ($page_name, $root_override);
  }

  /**
   * This object's home page (with query arguments) as HTML.
   * HTML characters are escaped.
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   */
  public function home_page_as_html ($root_override = null)
  {
    return $this->context->text_options->convert_to_html_attribute ($this->home_page ($root_override));
  }

  /**
   * Name of the home page name for this object.
   * @return string
   * @abstract
   */
  public abstract function page_name ();
  
  /**
   * Arguments to the home page url for this object.
   * @return string
   */
  public function page_arguments ()
  {
    return '';
  }
  
  /**
   * Rewrite the given url to point to this object.
   * @param string $page_url The url to modify.
   */
  public function replace_page_arguments ($page_url)
  {
    $args = $this->page_arguments ();
    
    if (! empty ($args))
    {
      $url = new URL ($page_url);
      $url->replace_arguments ($args);
      return $url->as_text ();
    }
    
    return $page_url;
  }

  /**
   * Returns an HTML formatter customized for this object.
   * @return HTML_MUNGER
   */
  public function html_formatter ()
  {
    $munger = $this->app->html_text_formatter ();
    $munger->complete_text_url = $this->home_page ();
    return $munger;
  }

  /**
   * Returns a plain text formatter customized for this object.
   * @return TEXT_MUNGER
   */
  public function plain_text_formatter ()
  {
    return $this->app->plain_text_formatter ();
  }

  /**
   * Expand all folder aliases and return a usable URL.
   * @param string $url
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   */
  public function resolve_url ($url, $root_override = null)
  {
    return $this->context->resolve_file ($url, $root_override);
  }

  /**
   * Any extra information needed to summarize this object.
   * Generally, this will be displayed along with a truncated description.
   * @return string
   */
  public function preview () {}


  /**
   * A string representing the entire title of the object.
   * Does not truncate or format the title in any way.
   * @return string
   * @abstract
   */
  public abstract function raw_title ();
  
  /**
   * Render the location within the object hierarchy.
   * Return the scope of this object within the system, ending with the object itself. The path can be
   * rendered as plain text or with each portion linked to the object's home page.
   * @param boolean $use_links Show objects as links?
   * @param string $separator Optional separator. If not set, {@link APPLICATION_DISPLAY_OPTIONS::$obj_url_separator} is used.
   * @param TITLE_FORMATTER $formatter Optional formatter to use.
   * @access private
   */
  protected function _object_url ($use_links, $separator = null, $formatter = null)
  {
    if (! isset ($formatter))
    {
      $formatter = $this->title_formatter ();
      $formatter->max_visible_output_chars = 0;
    }

    if ($use_links)
    {
      return $this->title_as_link ($formatter);
    }

    return $this->title_as_plain_text ($formatter);
  }

  /**
   * Transform a text for this object into HTML.
   * If no specific munger is provided, the one from {@link NAMED_OBJECT::html_formatter()} is used.
   * @param string $text
   * @param HTML_MUNGER $munger
   * @access private
   */
  protected function _text_as_html ($text, $munger = null)
  {
    if (! isset ($munger))
    {
      $munger = $this->html_formatter ();
      $munger->force_paragraphs = true;
    }

    return $munger->transform ($text, $this);
  }

  /**
   * Transform a text for this object into HTML.
   * If no specific munger is provided, the one from {@link NAMED_OBJECT::html_formatter()} is used.
   * @param string $text
   * @param HTML_MUNGER $munger
   * @access private
   */
  protected function _text_as_plain_text ($text, $munger = null)
  {
    if (! isset ($munger))
    {
      $munger = $this->plain_text_formatter ();
      $munger->force_paragraphs = true;
    }

    return $munger->transform ($text, $this);
  }
}

?>