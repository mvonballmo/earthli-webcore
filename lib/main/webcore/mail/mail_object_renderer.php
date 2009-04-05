<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
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

/** */
require_once ('webcore/mail/mail_renderer.php');
require_once ('webcore/gui/object_renderer.php');

/**
 * Renders the contents of an object for text or html email.
 * @package webcore
 * @subpackage mail
 * @version 3.1.0
 * @since 2.5.0
 * @abstract
 */
abstract class MAIL_OBJECT_RENDERER extends MAIL_RENDERER
{
  /**
   * @param object $obj
   * @return string
   */
  public function subject ($obj, $options)
  {
    return '';
  }

  /**
   * Returns the object's contents as HTML.
   * @param object $obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @return string
   */
  public function html_body ($obj, $options)
  {
    return $this->_body ($obj, $options, '_echo_html_content');
  }

  /**
   * Returns the object's contents as text.
   * @param object $obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @return string
   */
  public function text_body ($obj, $options)
  {
    return $this->_body ($obj, $options, '_echo_text_content');
  }

  /**
   * @param object $obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @param method $func The method {@link _echo_text_content} or {@link _echo_html_content}.
   * @access private
   */
  protected function _body ($obj, $options, $func)
  {
    $state = new stdClass();
    $this->_start_rendering ($options, $state);

    ob_start ();
      $this->$func ($obj, $options);
      $Result = ob_get_contents ();
    ob_end_clean ();

    $this->_finish_rendering ($options, $state);

    return $Result;
  }

  /**
   * @param object $obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @access private
   * @abstract
   */
  protected abstract function _echo_text_content ($obj, $options);

  /**
   * @param object $obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @access private
   * @abstract
   */
  protected abstract function _echo_html_content ($obj, $options);
}

/**
 * Renders the contents of a mail using a sub-renderer.
 * @package webcore
 * @subpackage mail
 * @version 3.1.0
 * @since 2.7.1
 * @access private
 * @abstract
 */
abstract class RENDERER_BASED_MAIL_RENDERER extends MAIL_OBJECT_RENDERER
{
  /**
   * @param object $obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @access private
   */
  protected function _echo_html_content ($obj, $options)
  {
    $renderer = $this->_make_renderer ();
    $renderer->display_as_html ($obj, $options);
  }

  /**
   * @param object $obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @access private
   */
  protected function _echo_text_content ($obj, $options)
  {
    $renderer = $this->_make_renderer ();
    $renderer->display_as_plain_text ($obj, $options);
  }
  
  /**
   * @return OBJECT_RENDERER
   * @access private
   * @abstract
   */
  protected abstract function _make_renderer ();
}

/**
 * Rendering options passed to a {@link MAIL_OBJECT_RENDERER}
 * @package webcore
 * @subpackage mail
 * @version 3.1.0
 * @since 2.5.0
 * @access private
 */
class MAIL_OBJECT_RENDERER_OPTIONS extends OBJECT_RENDERER_OPTIONS
{
  /**
   * How many items are being displayed at once?
   * Some renderers will alter their appearance if included in a block with other renderers.
   * {@link HISTORY_ITEM_RENDERER}s, for example, display their title if rendered in groups,
   * but leave it off when rendered alone.
   * @var integer
   */
  public $num_items;

  /**
   * Brief description of the contained objects.
   * Some renderers will preface their object list with this summary, to indicate what is
   * contained in a longer email.
   * @var string
   */
  public $content_summary;

  /**
   * Ignore the subscriber's message length preference if true.
   * Use this option to truncate mail text and drive traffic to the site.
   * @var boolean
   */
  public $ignore_subscriber_preferred_text_length = false;
}

?>