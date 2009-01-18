<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package polls
 * @subpackage forms
 * @version 3.0.0
 * @since 2.6.0
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

require_once ('webcore/forms/form.php');

class POLL_FORM extends ID_BASED_FORM
{
  var $action = 'polls/record_poll.php';

  function POLL_FORM (&$context)
  {
    ID_BASED_FORM::ID_BASED_FORM ($context);

    $field = new TEXT_FIELD ();
    $field->id = 'poll_page';
    $field->title = 'Page';
    $field->visible = FALSE;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = 'poll_answer';
    $field->title = '';
    $this->add_field ($field);
  }

  function load_from_object (&$obj)
  {
    parent::load_from_object ($obj);
    $this->set_value ('poll_page', $this->env->url ());
  }

  /**
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_controls (&$renderer)
  {
    $renderer->start ();

    $renderer->draw_text_row ('', $this->_object->display_text_as_html ());

    $props = $renderer->make_list_properties ();
    $props->on_click_script = 'this.form.submit ()';
    $props->smart_wrapping = TRUE;
    $props->line_spacing = '.3em';

    $answers = $this->_object->answers ();
    foreach ($answers as $ans)
      $props->add_item ($ans->display_text, $ans->id);

    $renderer->draw_radio_group_row ('poll_answer', $props);

    $renderer->finish ();
  }
}
?>