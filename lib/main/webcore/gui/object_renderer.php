<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.6.0
 * @since 2.5.0
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
require_once ('webcore/gui/renderer.php');

/**
 * Base class for renderers used as handlers.
 * These classes can be registered with {@link RESVOLER::register_handler()}.
 * @see Handler_constants
 * @package webcore
 * @subpackage renderer
 * @version 3.6.0
 * @since 2.7.0
 */
class HANDLER_RENDERER extends RENDERER
{
  /**
   * Determines the default handling when calling {@link display()}.
   * @var string
   */
  public $handler_type;
  
  /**
   * @param CONTEXT $context
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function __construct ($context, $options = null)
  {
    parent::__construct ($context);
    if (isset ($options))
    {
      $this->_options = $options;
    }
    else
    {
      $this->_options = $this->_make_options ();
    }
  }

  /**
   * Return the options set for this renderer.
   * Options can be passed to the constructor or to any of the rendering functions.
   * A renderer always retains the last options used with it.
   * @return OBJECT_RENDERER_OPTIONS
   */
  public function options ()
  {
    return $this->_options;
  }
  
  /**
   * Outputs the object as the configured {@link $handler_type}.
   * @param RENDERABLE $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display ($obj, $options = null)
  {
  }
  
  /**
   * Capture the ouptut of {@link display()} to text.
   * @param RENDERABLE $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   * @return string
   */
  public function display_to_string ($obj, $options = null)
  {
    ob_start ();
      $this->display ($obj, $options);
      $Result = ob_get_contents ();
    ob_end_clean ();
    return $Result;
  }

  /**
   * @return OBJECT_RENDERER_OPTIONS
   * @access private
   */
  protected function _make_options ()
  {
    return new OBJECT_RENDERER_OPTIONS ();
  }

  /**
   * @var OBJECT_RENDERER_OPTIONS
   * @access private
   */
  protected $_options;
}

/**
 * Renders an object as plain text, HTML or print.
 * All WebCore objects render using these to maintain a consistent appearance wherever they're displayed.
 * {@link FORM}s, {@link PUBLISHER}s and home pages all display objects as HTML or plain text using renderers.
 * {@link STORABLE} objects provide an object-specific renderer through the {@link STORABLE::renderer()} method.
 * @package webcore
 * @subpackage renderer
 * @version 3.6.0
 * @since 2.5.0
 * @abstract
 */
abstract class OBJECT_RENDERER extends HANDLER_RENDERER
{
  /**
   * Outputs the object as the configured {@link $handler_type}.
   * @param RENDERABLE $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display ($obj, $options = null)
  {
    switch ($this->handler_type)
    {
      case Handler_html_renderer:
        $this->display_as_html ($obj, $options);
        break;
      case Handler_text_renderer:
        $this->display_as_plain_text ($obj, $options);
        break;
      case Handler_print_renderer:
        $this->display_as_printable ($obj, $options);
        break;
    }
  }

  /**
   * Outputs the object as HTML.
   * @param RENDERABLE $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display_as_html ($obj, $options = null)
  {
    if (isset ($options))
    {
      $this->_options = $options;
    }
    $this->_display_as_html ($obj);
  }

  /**
   * Outputs the object as plain text.
   * @param RENDERABLE $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display_as_plain_text ($obj, $options = null)
  {
    if (isset ($options))
    {
      $this->_options = $options;
    }
    $this->_display_as_plain_text ($obj);
  }

  /**
   * Outputs the object in printable format.
   * 
   * @param RENDERABLE $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display_as_printable ($obj, $options = null)
  {
    if (isset ($options))
    {
      $this->_options = $options;
    }
    $this->_options->show_interactive = false;
    $this->_display_as_printable($obj);
  }

  /**
   * Outputs the object as HTML.
   * 
   * @param RENDERABLE $obj
   * @access private
   * @abstract
   */
  protected abstract function _display_as_html ($obj);

  /**
   * Outputs the object as plain text.
   * 
   * @param RENDERABLE $obj
   * @access private
   * @abstract
   */
  protected abstract function _display_as_plain_text ($obj);

  /**
   * Outputs the object in printable format.
   * 
   * @param RENDERABLE $obj
   * @access private
   * @abstract
   */
  protected function _display_as_printable ($obj)
  {
    $this->_display_as_html ($obj);
  }
}

/**
 * Rendering options passed to an {@link OBJECT_RENDERER}.
 * 
 * @package webcore
 * @subpackage renderer
 * @version 3.6.0
 * @since 2.5.0
 * @access private
 */
class OBJECT_RENDERER_OPTIONS
{
  /**
   * Limit text to this length.
   * 
   * Renderers will interpret this option differently. Some will treat this as a flag to
   * omit more detailed information and include only the description block (limited to this
   * many characters).
   * 
   * @var integer
   */
  public $preferred_text_length = 0;

  /**
   * Show users and create/modify times?
   * 
   * @var boolean
   */
  public $show_users = true;

  /**
   * Show only details for a summary.
   * 
   * This should be optimized for tall, skinny viewing and contain less text.
   * 
   * @var boolean
   */
  public $show_as_summary = false;

  /**
   * Show buttons/etc.
   * 
   * Set to false when rendering in an email/printing or other non-interactive media.
   * 
   * @var boolean
   */
  public $show_interactive = true;
  
  /**
   * Wrap text at this margin for text-based formats.
   *
   * @var integer
   */
  public $right_margin = 80;

  /**
   * A value indicating whether to show only minimal details.
   *
   * @var bool
   */
  public $minimal = false;

  /**
   * Load values from the HTTP request.
   */
  public function load_from_request ()
  {
    $this->preferred_text_length = read_var ('preferred_text_length', $this->preferred_text_length);
    $this->show_users = read_var ('show_users', $this->show_users);
    $this->show_as_summary = read_var ('show_as_summary', $this->show_as_summary);
    $this->right_margin = read_var ('right_margin', $this->right_margin);
    $this->minimal = read_var ('minimal', $this->minimal);
  }
}