<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tree
 * @version 3.4.0
 * @since 2.2.1
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
require_once ('webcore/gui/tree.php');

/**
 * Handles tree-rendering for nodes of type {@link FOLDER}.
 * @package webcore
 * @subpackage tree
 * @version 3.4.0
 * @since 2.2.1
 */
class FOLDER_TREE_NODE_INFO extends TREE_NODE_INFO
{
  /**
   * Link folders to this page.
   * This redirects folders to this page instead of their default home page.
   * @var string
   */
  public $page_link = '';

  /**
   * Add arguments to this page.
   * These constant arguments are appended to each generated link.
   * @var string
   */
  public $page_args = '';

  /**
   * Show the icon for a folder?
   * Overridden in specialized displayers that show other icons.
   * @var boolean
   */
  public $show_folder_icon = true;

  /**
   * Return list of sub-nodes for 'node'.
   * @param FOLDER $node
   * @return FOLDER[]
   * @access private
   */
  public function sub_nodes ($node)
  {
    return $node->sub_folders ();
  }

  /**
   * Return the title for the given node.
   * @param FOLDER $node Draw the title for this project.
   * @return string
   */
  public function get_caption ($node)
  {
    if ($this->selected ($node))
    {
      $t = $node->title_formatter ();
      $t->CSS_class = 'selected';
      $Result = $node->title_as_html ($t);
    }
    else
    {
      if ($this->visitable ($node))
      {
        if ($this->page_link || $this->page_args)
        {
          $t = $node->title_formatter ();
          if ($this->page_link)
          {
            $t->set_name ($this->page_link);
          }
          if ($this->page_args)
          {
            $t->add_arguments ($this->page_args);
          }
          $Result = $node->title_as_link ($t);
        }
        else
        {
          $Result = $node->title_as_link ();
        }
      }
      else
      {
        $Result = $node->title_as_html ();
      }
    }

    return $Result;
  }

  /**
   * Return the icon for the given node.
   * @param PROJECT $node Draw the title for this project.
   * @return string
   */
  public function get_icon_url ($node)
  {
    if ($this->show_folder_icon && $node->icon_url)
    {
      return $node->icon_url;
    }
    
    return '';
  }

  /**
   * Is this node closed in this tree?
   * @param FOLDER $node
   * @return boolean
   * @access private
   */
  public function closed ($node)
  {
    if (! isset ($this->open_nodes) || ! sizeof ($this->open_nodes))
    {
      return $node->id != $this->app->root_folder_id;
    }

    return parent::closed ($node);
  }

  /**
   * Return the node's parent node.
   * @param FOLDER $node
   * @return FOLDER
   * @access private
   */
  public function parent ($node)
  {
    return $node->parent_folder ();
  }

  /**
   * Return the node's id.
   * @param FOLDER $node
   * @return integer
   * @access private
   */
  public function id ($node)
  {
    return $node->id;
  }
}

/**
 * Handles tree-rendering for {@link FOLDER}s in the explorer window.
 * @package webcore
 * @subpackage tree
 * @version 3.4.0
 * @since 2.2.1
 */
class EXPLORER_FOLDER_TREE_NODE_INFO extends FOLDER_TREE_NODE_INFO
{
  /**
   * Is this node selectable in this tree?
   * @param FOLDER $node
   * @return boolean
   * @access private
   */
  public function selectable ($node)
  {
    return $this->app->login->is_allowed (Privilege_set_entry, Privilege_create, $node);
  }
}

/**
 * Handles tree-rendering for {@link FOLDER}s in the security window.
 * @package webcore
 * @subpackage tree
 * @version 3.4.0
 * @since 2.2.1
 */
class SECURITY_FOLDER_TREE_NODE_INFO extends FOLDER_TREE_NODE_INFO
{
  /**
   * Show the icon for a folder?
   * Overridden in specialized displayers that show other icons.
   * @var boolean
   */
  public $show_folder_icon = false;

  /**
   * Make sure all nodes that define their permissions are visible in the tree.
   * @param array[FOLDER] $nodes
   */
  public function set_defined_nodes_visible ($nodes)
  {
    if (sizeof ($nodes))
    {
      foreach ($nodes as $node)
      {
        if ($node->defines_security ())
        {
          $this->set_visible_node ($node);
        }

        $sub_nodes = $this->sub_nodes ($node);
        $this->set_defined_nodes_visible ($sub_nodes);
      }
    }
  }

  /**
   * Is this node visitable in this tree?
   * @param FOLDER $node
   * @return boolean
   * @access private
   */
  public function visitable ($node)
  {
    return $this->login->is_allowed (Privilege_set_folder, Privilege_secure, $node);
  }

  /**
   * Return the icon for the given node.
   * @param PROJECT $node Draw the title for this project.
   * @return string
   */
  public function get_icon_url ($node)
  {
    if ($node->defines_security ())
    {
      return '{icons}buttons/security';
    }

    return parent::get_icon_url ($node);
  }
}

/**
 * Handles tree-rendering for {@link FOLDER}s in the folder subscription views.
 * @package webcore
 * @subpackage tree
 * @version 3.4.0
 * @since 2.2.1
 */
class SUBSCRIPTION_FOLDER_TREE_NODE_INFO extends FOLDER_TREE_NODE_INFO
{
  /**
   * Is this node selectable in this tree?
   * @param FOLDER $node
   * @return bool
   * @access private
   */
  public function visitable ($node)
  {
    return $this->app->login->is_allowed (Privilege_set_folder, Privilege_modify, $node);
  }
}

?>