<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.5.0
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
require_once ('webcore/mail/mail_object_renderer.php');

/**
 * Renders mail content for {@link CONTENT_OBJECT}s.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.5.0
 */
class CONTENT_OBJECT_MAIL_RENDERER extends MAIL_OBJECT_RENDERER
{
  /**
   * @param CONTENT_OBJECT &$obj
    * @param MAIL_OBJECT_RENDERER_OPTIONS $options
    * @return string
    */
  function subject (&$obj, $options)
  {
    return $obj->object_url_as_text ();
  }

  /**
   * @param CONTENT_OBJECT &$obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @access private
   */
  function _echo_html_content (&$obj, $options)
  {
?>
  <h3><?php echo $obj->title_as_html (); ?></h3>
  <p class="detail"><?php echo $obj->object_url_as_link ($this->app->display_options->object_separator); ?></p>
<?php
    $renderer = $obj->handler_for (Handler_html_renderer, $options);
    $renderer->display ($obj);
  }

  /**
   * @param CONTENT_OBJECT &$obj
   * @param MAIL_OBJECT_RENDERER_OPTIONS $options
   * @access private
   */
  function _echo_text_content (&$obj, $options)
  {
    $f = $obj->title_formatter ();
    $f->max_visible_output_chars = 0;
    echo $this->_line ($obj->title_as_plain_text ($f));
    echo $this->_sep ();
    echo $this->_line ($obj->object_url_as_text ($this->app->mail_options->object_separator));
    echo $this->_line ('<' . $obj->home_page () . '>');
    echo $this->_sep ();

    $renderer = $obj->handler_for (Handler_text_renderer, $options);
    $renderer->display ($obj);
  }
}
?>