<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.2.1
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
 * Subscribe/unsubscribe to one or more {@link FOLDER}s.
 * @package webcore
 * @subpackage forms
 * @version 3.4.0
 * @since 2.2.1
 */
class FOLDER_SUBSCRIPTION_FORM extends SUBSCRIPTION_FORM
{
  /**
   * Name of the panel in which the form is displayed.
   * @var string
   * @access private
   */
  public $panel_name = 'folders';

  /**
   * Updates the subscriber's folder subscriptions.
   * @param SUBSCRIBER $obj
   * @access private
   */
  public function commit ($obj)
  {
    $obj->update_subscriptions_for (Subscribe_folder, $this->value_for ('ids'));
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    if ($this->object_exists ())
    {
      $selected_folder_ids = $this->_object->subscribed_ids_for (Subscribe_folder);
    }
    
    $user = $this->_object->user ();  // Try to get the user associated with this subscriber
    if (!isset($user))
    {
    	$user = $this->app->anon_user();
    }

    $folder_query = $user->folder_query ();
    $folders = $folder_query->tree ($this->app->root_folder_id);
    $selected_folders = $folder_query->objects_at_ids ($selected_folder_ids);

    /* Make a copy (not a reference). */
    $tree = $this->app->make_tree_renderer ();

    include_once ('webcore/gui/folder_tree_node_info.php');
    $tree_node_info = new FOLDER_TREE_NODE_INFO ($this->app);
    $tree_node_info->nodes_are_links = false;

    include_once ('webcore/gui/selector_tree_decorator.php');
    $decorator = new MULTI_SELECTOR_TREE_DECORATOR ($tree, $selected_folder_ids);
    $decorator->control_name = 'ids';
    $decorator->form_name = $this->name;
    $decorator->auto_toggle_children = true;

    $tree->node_info = $tree_node_info;
    $tree->decorator = $decorator;
    $tree->set_visible_nodes ($selected_folders);

    $ctrl_name = $this->js_name ('ids');

    $renderer->start ();

    if (sizeof ($folders) > 0)
    {
      $buttons [] = $renderer->javascript_button_as_HTML ('Select All', "select_all ($ctrl_name)", '{icons}buttons/select');
      $buttons [] = $renderer->javascript_button_as_HTML ('Clear All', "select_none ($ctrl_name)", '{icons}buttons/close');

      $renderer->start_row();
      $renderer->draw_buttons ($buttons);
      $renderer->finish_row();

      $renderer->draw_separator ();
      $renderer->draw_error_row ('ids');
    }

    $renderer->start_row ();
    $tree->display ($folders);
    $renderer->finish_row ();

    $renderer->draw_separator ();
    $renderer->draw_submit_button_row();

    $renderer->finish ();
  }
}

?>