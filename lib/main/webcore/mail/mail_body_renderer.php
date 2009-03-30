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
require_once ('webcore/mail/mail_renderer.php');

/**
 * Generates a full HTML or plain-text email.
 * Manages a list of objects and their renderers, using them to create the actual content,
 * then filling in the correct headers and footers.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.2.1
 */
class MAIL_BODY_RENDERER extends MAIL_RENDERER
{
  /**
   * @var integer
   * Wrap html at this right margin.
   * Some mailers needs hard returns; this is a reasonable default.
   */
  public $html_wrap_limit = 200;

  /**
   * @var integer
   * Wrap text at this right margin.
   * Many mailers can't handle unwrapped (or lines longer than 255 characters) plain-text emails.
   */
  public $text_wrap_limit = 72;

  /**
   * List of objects in the email.
   * @see MAIL_BODY_RENDERER_OBJECT
   * @var array [MAIL_BODY_RENDERER_OBJECT]
   */
  public $objects;

  /**
   * Add an object to be renderered when the full email is rendered.
   * You can add multiple objects; renderer must be matched with the object.
   * @param object $obj
   * @param OBJECT_MAIL_RENDERER $renderer
   */
  public function add ($obj, $renderer)
  {
    $pair = null; // Compiler warning
    $pair->obj = $obj;
    $pair->renderer = $renderer;
    $this->objects [] = $pair;
  }

  /**
   * Retrieve the body of the email as HTML.
   * Return only 'excerpt_length' visible characters from each object's description if non-zero.
   * @param MAIL_RENDERER_OPTIONS $options
   * @return string
   */
  public function as_html ($options)
  {
    $state = null; // Compiler warning
    $this->_start_rendering ($options, $state);
    $Result = $this->_html_header () . $this->_html_content ($options) . $this->_html_footer ();
    $this->_finish_rendering ($options, $state);

    if ($this->html_wrap_limit)
    {
      $Result = wordwrap ($Result, $this->html_wrap_limit);
    }

    return $Result;
  }

  /**
   * Retrieve the body of the email as text.
   * Return only 'excerpt_length' visible characters from each object's description if non-zero.
   * @param MAIL_RENDERER_OPTIONS $options
   * @return string
   */
  public function as_text ($options)
  {
    $state = null; // Compiler warning
    $this->_start_rendering ($options, $state);
    $Result = $this->_text_content ($options);
    $this->_finish_rendering ($options, $state);

    if ($this->text_wrap_limit)
    {
      $Result = wordwrap ($Result, $this->text_wrap_limit);
    }

    return $Result;
  }

  /**
   * Start of the html email (includes head and body-open tags).
   * @return string
   * @access private
   */
  protected function _html_header ()
  {
    if (! isset ($this->_cached_html_header))
    {
      $this->_cached_html_header = $this->_build_html_header ();
    }

    return $this->_cached_html_header;
  }

  /**
   * All objects' contents returned as HTML.
   * @param MAIL_RENDERER_OPTIONS $options
   * @return string
   * @access private
   */
  protected function _html_content ($options)
  {
    $Result = '';

    if (sizeof ($this->objects))
    {
      foreach ($this->objects as $pair)
      {
        $Result .= $pair->renderer->html_body ($pair->obj, $options);
      }
    }

    return $Result;
  }

  /**
   * End of the html email (includes body-close and footer tags).
   * @return string
   * @access private
   */
  protected function _html_footer ()
  {
    if (! isset ($this->_cached_html_footer))
    {
      $this->_cached_html_footer = $this->_build_html_footer ();
    }

    return $this->_cached_html_footer;
  }

  /**
   * All objects' contents returned as text.
   * @param MAIL_RENDERER_OPTIONS $options
   * @return string
   * @access private
   */
  protected function _text_content ($options)
  {
    $num_objs = sizeof ($this->objects);
    if ($num_objs)
    {
      $obj_idx = 0;
      $Result = '';

      while ($obj_idx < $num_objs)
      {
        $pair = $this->objects [$obj_idx];

        $obj_text = $pair->renderer->text_body ($pair->obj, $options);

        if ($obj_text)
        {
          $Result .= $obj_text;

          if ($obj_idx < $num_objs - 1)
          {
            $Result .= $this->_line ($this->_sep ('='));
          }
        }

        $obj_idx++;
      }
    }

    return $Result;
  }

  /**
   * Default HTML header.
   * @return string
   * @access private
   */
  protected function _build_html_header ()
  {
    $Result = $this->_line ("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">");
    $Result .= $this->_line ("<html>");
    $Result .= $this->_line ("<head>");
    $Result .= $this->_line ("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">");

    $Result .= $this->_html_head_content ();

    $Result .= $this->_line ("</head>");
    $Result .= $this->_line ("<body>");

    return $Result;
  }

  /**
   * Override to provide custom HTML head contents.
   * Include and scripts or style sheets needed to render this HTML email.
   * @return string
   * @access private
   */
  protected function _html_head_content () {}

  /**
   * Default HTML footer.
   * @return string
   * @access private
   */
  protected function _build_html_footer ()
  {
    $Result = $this->_line ("</body>");
    $Result .= $this->_line ("</html>");

    return $Result;
  }
}

/**
 * List item that holds an {@link $obj} and its {@link $renderer}.
 * Used by a {@link MAIL_BODY_RENDERER} to maintain its list of objects
 * and their renderers.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class MAIL_BODY_RENDERER_OBJECT
{
  /**
   * @var object $obj
   */
  public $obj;

  /**
   * @var OBJECT_MAIL_RENDERER
   */
  public $renderer;
}

?>