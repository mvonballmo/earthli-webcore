<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.6.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('webcore/forms/form.php');

/**
 * Look up an email address and redirect to its home page.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.6.0
 */
class CHECK_SUBSCRIPTIONS_FORM extends ID_BASED_FORM
{
  /**
   * @var string
   */
  public $button = 'Go';

  /**
   * @var string
   */
  public $button_icon = '{icons}indicators/subscribed';

  /**
   * @var string
   */
  public $name = 'subscription_form';

  /**
   * To which url is this form submitted?
   * Defaults to the current page.
   * @var string
   */
  public $action = 'view_user_subscriptions.php';

  /**
   * @var string
   * @access private
   */
  public $method = 'get';

  /**
   * @var boolean
   */
  public $controls_visible = true;

  /**
   * @param APPLICATION $app Main application.
   */
  public function __construct ($app)
  {
    parent::__construct ($app);

    $field = new EMAIL_FIELD ();
    $field->id = 'email';
    $field->caption = 'Email';
    $field->description = 'Enter an email to create or view subscriptions';
    $this->add_field ($field);
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $renderer->set_width ('13em');
    $renderer->start ();
    $renderer->start_row ();
      echo $renderer->text_line_as_html ('email');
    $renderer->finish_row ();
    $renderer->draw_buttons_in_row (array ($renderer->submit_button_as_html ()));
    $renderer->finish ();
  }
}

?>