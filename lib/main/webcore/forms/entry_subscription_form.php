<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
 * Display a list of {@link ENTRY}s to which a {@link SUBSCRIBER} is subscribed.
 * @package webcore
 * @subpackage forms
 * @version 3.0.0
 * @since 2.2.1
 */
class ENTRY_SUBSCRIPTION_FORM extends CONTENT_OBJECT_SUBSCRIPTION_FORM
{
  /**
   * Name of the panel in which the form is displayed.
   * @var string
   * @access private
   */
  public $panel_name = 'entries';

  /**
   * @param APPLICATION $app Main application.
   * @param TYPE_INFO $type_info
   */
  function ENTRY_SUBSCRIPTION_FORM ($app, $type_info)
  {
    CONTENT_OBJECT_SUBSCRIPTION_FORM::CONTENT_OBJECT_SUBSCRIPTION_FORM ($app);
    $this->_type_info = $type_info;
    $this->_type = $type_info->id;
    $this->panel_name = $type_info->id;
  }

  /**
   * @return QUERY
   * @access private
   */
  function _make_query ()
  {
    $Result = $this->login->all_entry_query ();
    $Result->set_type ($this->_type_info->id);
    return $Result;
  }
  
  /**
   * @return SELECTABLE_GRID
   * @access private
   */
  function _make_grid ()
  {
    $class_name = $this->app->final_class_name ('ENTRY_SUMMARY_GRID', 'webcore/gui/entry_grid.php', $this->_type_info->id);
    $Result = new $class_name ($this->app);
    $Result->show_user = TRUE;
    $Result->show_folder = TRUE;
    return $Result;
  }

  /**
   * Type of subscriptions to manage.
   * @var string
   */
  protected $_sub_type = Subscribe_entry;
}

?>