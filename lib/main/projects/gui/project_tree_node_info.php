<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.0.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/gui/folder_tree_node_info.php');

/**
 * Render a tree for {@link PROJECT}s indicating which ones define options.
 * Used when editing a project to decorate the tree on that page. Makes sure
 * that only editable folders are links and shows an edit icon next to projects
 * that define their own options.
 * @package projects
 * @subpackage gui
 * @version 3.0.0
 * @since 1.4.1
 * @see PROJECT::defines_options ()
 */
class PROJECT_TREE_NODE_INFO extends FOLDER_TREE_NODE_INFO
{
  /**
   * Return the icon for the given node.
   * @param PROJECT &$node Draw the title for this project.
   * @return string
   */
  function icon_for (&$node)
  {
    if ($node->defines_options ())
    {
      return $this->app->resolve_icon_as_html ('{icons}buttons/edit', 'Defines Assignee Options', '16px');
    }


    return parent::icon_for ($node);
  }
  
  /**
   * @param PROJECT &$node Is this project editable?
    * @access private
    */
  function visitable (&$node)
  {
    return $this->login->is_allowed (Privilege_set_folder, Privilege_modify, $node);
  }

  /**
   * @param array[PROJECT] &$nodes If a node in this list defines its own options, make sure its parent node is open (make it visible). Works with a tree of projects as well.
    * @access private
    */
  function set_defined_nodes_visible (&$nodes)
  {
    if (sizeof ($nodes))
    {
      foreach ($nodes as $node)
      {
        if ($node->defines_options ())
        {
          $this->set_visible_node ($node);
        }
        $this->set_defined_nodes_visible ($this->sub_nodes ($node));
      }
    }
  }
}

?>