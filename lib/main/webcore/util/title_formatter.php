<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage util
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
require_once ('webcore/obj/webcore_object.php');

/**
 * Handles all aspects of display for an object title.
 * Includes formatting for object state, maximum title length and writing links or text.
 * @package webcore
 * @subpackage util
 * @version 3.0.0
 * @since 2.2.1
 */
class TITLE_FORMATTER extends WEBCORE_OBJECT
{
  /**
   * CSS Class used for the text.
   * Used for both text and link.
   * @var string
   */
  public $CSS_class = '';
  /**
   * The maximum displayed length of the text.
   * If the title given exceeds this length, the excess characters are cut from
   * the middle of the title and the full title is given as the title of the
   * link or as the title of a span wrapped around the text.
   * @var integer
   */
  public $max_visible_output_chars = 0;
  /**
   * URL used when formatting the title as a link.
   * @var string
   */
  public $location;
  /**
   * Full text of the title to display.
   * @var string
   */
  public $text;
  /**
   * Contents of the link's title (usually a tooltip).
   * This is displayed in addition to the entire text if the text has to be
   * truncated, or displayed alone if not.
   * @var string
   */
  public $title = '';

  /**
   * Append a query string name and value to the url.
   * Should be formatted as 'name=value'.
   * @var string $arg
   */
  function add_arguments ($args)
  {
    $url = new URL ($this->location);
    $url->replace_arguments($args);
    $this->location = $url->as_text ();
  }

  function add_argument ($name, $value)
  {
    $this->add_arguments ($name . '=' . $value);
  }

  /**
   * Change the location for links.
   * This will retain any arguments applied to the previous home page
   * @param string $page_name
   */
  function set_name ($page_name)
  {
    $qmark = strpos ($this->location, '?');
    if ($qmark !== FALSE)
    {
      $this->location = $page_name . substr ($this->location, $qmark);
    }
    else
    {
      $this->location = $page_name;
    }
  }

  /**
   * Return contents as plain text.
   * This means all tags are stripped. Do not use this function to output to an HTML page because
   * it does not <i>absolutely</i> prevent a user from inserting malicious code; to do that, use
   * {@link as_html()} instead.
   * @return string
   */
  function as_plain_text ()
  {
    $munger = $this->context->plain_text_title_formatter ();
    $text_to_use = $munger->transform ($this->text);
    return $this->_truncate ($text_to_use);
  }

  /**
   * Return contents as text.
    * @return string
    */
  function as_html_text ()
  {
    return $this->_make_html_tag ('<span', '</span>');
  }

  /**
   * Return contents as an HTML link.
   * This will strip most tags and render the others harmless by converting to HTML entities. The only
   * tags not stripped are 'b', 'i' and 'code'. This still allows classes and styles to be attached to
   * these tags that would force display:block or white-space: pre, but that's just an accepted risk for
   * allowing <i>some</i> formatting in titles.
   * @return string
   */
  function as_html_link ()
  {
    $page = htmlspecialchars ($this->_page ());
    return $this->_make_html_tag ("<a href=\"$page\"", '</a>');
  }

  /**
   * Return the url for this formatter.
    * This will check application options to determine whether to render as a fully-qualified URL.
    * It will also resolve the query arguments.
    * @return string
    * @access private
    */
  function _page ()
  {
    /* Add in the currently active panel. */

    $old_location = $this->location;
    $panel_id = read_var ('panel');
    if ($panel_id)
    {
      $this->add_argument ('panel', $panel_id);
    }
    $Result = $this->context->resolve_file ($this->location);
    $this->location = $old_location;

    return $Result;
  }

  /**
   * Trim text to required length.
   * This routine extracts the center of the text until the number of required visible characters
   * is shown.
   * @var string $text
   * @return string
   * @access private
   */
  function _truncate ($text)
  {
    $len = strlen ($text);

    $max_size = $this->context->display_options->overridden_max_title_size;

    if (! $max_size)
    {
      $max_size = $this->max_visible_output_chars;
    }

    if (($max_size > 0) && ($len > $max_size))
    {
      $text = strip_tags ($text);
      $start_chars = round ($max_size / 2, 0);
      $end_chars = $max_size - $start_chars;
      return substr ($text, 0, $start_chars) . "..." . substr ($text, $len - $end_chars, $end_chars);
    }

    return $text;
  }

  /**
   * Fill an HTML tag with this formatter's properties.
   * This truncates the text and generates a title, if necessary.
   * @param string $prefix
   * @param string $suffix
   * @return string
   * @access private
   */
  function _make_html_tag ($prefix, $suffix)
  {
    $stripped_text = strip_tags ($this->text);
    $text_to_use = $this->_truncate ($stripped_text);

    /* If the text was truncated, then we proceed with an entirely tagless title. We don't
       attempt to parse around the tags, as the munger would, because the truncation occurs in the
       middle of the title, which complicates finding the right spot to do it. */

    $title_to_use = strip_tags ($this->title);

    if ($stripped_text != $text_to_use)
    {
      if (! $this->title)
      {
        $title_to_use = $stripped_text;
      }
      else
      {
        $title_to_use = $stripped_text . ' (' . $title_to_use . ')';
      }
      $text_to_use = $this->context->text_options->convert_to_html_attribute ($text_to_use);
    }
    else
    {
      $munger = $this->context->html_title_formatter ();
      $text_to_use = $munger->transform ($this->text);
    }

    $Result = $prefix;

    if ($title_to_use)
    {
      $title_to_use = $this->context->text_options->convert_to_html_attribute ($title_to_use);
      $Result .= " title=\"$title_to_use\" style=\"cursor: help\"";
    }

    if ($this->CSS_class)
    {
      $Result .= " class=\"$this->CSS_class\"";
    }

    $Result .= '>' . $text_to_use . $suffix;

    return $Result;
  }
}

?>