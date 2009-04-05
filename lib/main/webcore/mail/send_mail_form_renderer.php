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
require_once ('webcore/mail/mail_object_renderer.php');

/**
 * Renders a request from a {@link SEND_MAIL_FORM}.
 * @package webcore
 * @subpackage mail
 * @version 3.1.0
 * @since 2.2.1
 */
class SEND_MAIL_FORM_RENDERER extends MAIL_OBJECT_RENDERER
{
  /**
   * @param SEND_MAIL_FORM $obj
   * @param EXCEPTION_MAIL_OBJECT_RENDERER_OPTIONS $options
   * @return string
   */
  public function subject ($obj, $options)
  {
    return $obj->value_for ('subject');
  }

  /**
   * @param SEND_MAIL_FORM $obj
   * @param MAIL_RENDERER_OPTIONS $options
   * @access private
   */
  protected function _echo_html_content ($obj, $options)
  {
    if ($obj->value_for ('message'))
    {
      $munger = $this->context->html_text_formatter ();
      $sender_name = $obj->value_for ('sender_name');
?>
    <div class="horizontal-separator" style="margin-bottom: 1em"></div>
    <div class="quote-block"><?php echo $munger->transform ($obj->value_for ('message')); ?></div>
    <?php if ($sender_name) { ?>
    <p class="quoter">- <?php echo $sender_name; ?></p>
    <?php } ?>
    <div class="horizontal-separator"></div>
<?php
    }
  }

  /**
   * @param SEND_MAIL_FORM $obj
   * @param MAIL_RENDERER_OPTIONS $options
   * @access private
   */
  protected function _echo_text_content ($obj, $options)
  {
    if ($obj->value_for ('message'))
    {
      $munger = $this->context->plain_text_formatter ();
      echo $this->line ($munger->transform ($obj->value_for ('message')));
      echo $this->par ('- ' . $obj->value_for ('sender_name'));
    }
  }
}
?>