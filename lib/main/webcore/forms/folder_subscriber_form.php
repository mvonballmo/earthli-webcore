<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

require_once ('webcore/forms/form.php');

/**
 * Update list of {@link USER}s subscribed to a {@link FOLDER}.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.2.1
 */
class FOLDER_SUBSCRIBER_FORM extends ID_BASED_FORM
{
  /**
   * @var string
   */
  public $button = 'Save';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/save';

  /**
   * @param FOLDER $folder Update subscribers to this folder.
   */
  public function __construct ($folder)
  {
    parent::__construct ($folder->app);

    $this->_folder = $folder;

    $field = new ARRAY_FIELD ();
    $field->id = 'subscriber_ids';
    $field->title = 'Subscribers';
    $this->add_field ($field);
  }

  /**
   * Store the form's values to this folder.
   * @param FOLDER $obj
   * @access private
   */
  public function commit ($obj)
  {
    $selected_emails = $this->value_for ('subscriber_ids');

    $query = $this->_folder->subscriber_query ();
    $subscribers = $query->objects ();
    foreach ($subscribers as $subscriber)
    {
      if (! in_array ($subscriber->email, $selected_emails))
      {
        $subscriber->unsubscribe ($this->_folder->id, Subscribe_folder);
      }
    }
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    $query = $this->_folder->subscriber_query ();

    $class_name = $this->app->final_class_name ('SUBSCRIBER_GRID', 'webcore/gui/subscriber_grid.php');
    /** @var $grid SUBSCRIBER_GRID */
    $grid = new $class_name ($this->app);
    
    $num_rows = max ($query->size (), 1);
    $grid->set_ranges ($num_rows, 1);
    $grid->set_query ($query);

    $ctrl_name = $this->js_name ('subscriber_ids');

    $renderer->start ();

    if ($query->size () > 0)
    {
      $buttons [] = $renderer->javascript_button_as_HTML ('Select All', "select_all ($ctrl_name)", '{icons}buttons/select');
      $buttons [] = $renderer->javascript_button_as_HTML ('Clear All', "select_none ($ctrl_name)", '{icons}buttons/close');
      $buttons [] = $renderer->submit_button_as_HTML ();
      $renderer->draw_buttons_in_row ($buttons);

      $renderer->draw_separator ();
    }

    $renderer->start_row ();
    $grid->display ();
    $renderer->finish_row ();

    $renderer->draw_separator ();
    if (! empty ($buttons))
    {
      $renderer->draw_buttons_in_row ($buttons);
    }

    $renderer->finish ();
  }
}
?>