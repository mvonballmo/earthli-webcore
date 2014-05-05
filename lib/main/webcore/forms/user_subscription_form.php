<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Webcore.

earthli Webcore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Webcore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Webcore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Webcore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

/** */
require_once ('webcore/forms/subscription_form.php');

/**
 * Display a list of {@link USER}s to which a {@link SUBSCRIBER} is subscribed.
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.7.0
 */
class USER_SUBSCRIPTION_FORM extends CONTENT_OBJECT_SUBSCRIPTION_FORM
{
  /**
   * Name of the panel in which the form is displayed.
   * @var string
   * @access private
   */
  public $panel_name = 'users';

  /**
   * @param APPLICATION $context Main application.
   */
  public function __construct ($context)
  {
    parent::__construct ($context);
    $this->_type_info = $this->app->type_info_for ('USER');
  }

  /**
   * @return QUERY
   * @access private
   */
  protected function _make_query ()
  {
    return $this->app->user_query ();
  }
  
  /**
   * @return SELECTABLE_GRID
   * @access private
   */
  protected function _make_grid ()
  {
    $class_name = $this->app->final_class_name ('SELECT_USER_GRID', 'webcore/gui/user_grid.php');
    $Result = new $class_name ($this->app);
    $Result->show_menus = false;
    return $Result;
  }

  /**
   * Type of subscriptions to manage.
   * @var string
   */
  protected $_sub_type = Subscribe_user;
}