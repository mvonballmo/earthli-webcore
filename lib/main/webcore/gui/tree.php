<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tree
 * @version 3.3.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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

/**
 * A vertical line indicating an open branch in the parent in that column.
 */
define ('Tree_line', 1);
/**
 * An open space indicating no open branch in the parent in that column.
 */
define ('Tree_blank', 2);
/**
 * An 'L' indicating a terminal node in a list.
 */
define ('Tree_ell', 3);
/**
 * A sideways 'T' indicating a non-terminal node in the list.
 */
define ('Tree_tee', 4);
/**
 * A plus sign indicating a terminal node with sub-nodes.
 */
define ('Tree_ell_plus', 5);
/**
 * A plus sign indicating a non-terminal node with sub-nodes.
 */
define ('Tree_tee_plus', 6);

require_once ('webcore/obj/webcore_object.php');

/**
 * Abstract representation of a tree.
 * Makes no commitment to rendering as static or dynamic HTML.
 * @abstract 
 * @package webcore
 * @subpackage tree
 * @version 3.3.0
 * @since 2.2.1
 */
abstract class TREE extends WEBCORE_OBJECT
{
  /**
   * Used to add non-node-specific rendering to tree nodes.
   * The 'node_info' is node-type-specific, but this just decorates nodes
   * according to a pattern.
   * @see TREE_NODE_INFO
   * @var TREE_DECORATOR
   */
  public $decorator;

  /**
   * Used to render specific node-types.
   * This type must match that of the nodes passed to 'display'. Must be
   * assigned.
   * @var TREE_NODE_INFO
   */
  public $node_info;

  /**
   * Render the tree using the given nodes.
   * @param array [object] $nodes
   */
  public function display ($nodes)
  {
    if (sizeof ($nodes))
    {
      if (isset ($this->env->profiler)) $this->env->profiler->start ('ui');
      
      if (isset ($this->decorator))
      {
        $this->decorator->pre_iterate ();
      }
  
      $this->_depth = 0;
      $this->_stack = array ();
      $this->start ();
      $this->iterate_nodes ($nodes);
      $this->finish ();
  
      if (isset ($this->decorator))
      {
        $this->decorator->post_iterate ();
      }
  
      if (isset ($this->env->profiler)) $this->env->profiler->stop ('ui');
    }
  }

  /**
   * Render this node as opened.
   * Static renderers may render all nodes as open (so this call is moot on those trees).
   * @param object $node
   */
  public function set_open_node ($node)
  {
    $this->node_info->set_open_node ($node);
  }

  /**
   * Render this node as visible.
   * That means that all parent nodes need to be open. Static renderers may render
   * all nodes as open (so this call is moot on those trees).
   * @param object $node
   */
  public function set_visible_node ($node)
  {
    $this->node_info->set_visible_node ($node);
  }

  /**
   * Render all of these nodes as opened.
   * @see TREE::set_open_node()
   * @param array [object] $nodes
   */
  public function set_open_nodes ($nodes)
  {
    if (isset ($nodes) && sizeof ($nodes))
    {
      foreach ($nodes as $node)
      {
        $this->set_open_node ($node);
      }
    }
  }

  /**
   * Render all of these nodes as visible.
   * @see TREE::set_visible_node()
   * @param array [object] $nodes
   */
  public function set_visible_nodes ($nodes)
  {
    if (isset ($nodes) && sizeof ($nodes))
    {
      foreach ($nodes as $node)
      {
        $this->set_visible_node ($node);
      }
    }
  }

  /**
   * Start rendering the tree.
   * @access private
   */
  public function start () {}
  /**
   * Finish rendering the tree.
   * @access private
   */
  public function finish () {}

  /**
   * Start rendering a node.
   * @param object $node
   * @param boolean $is_last Is this a terminal node in this list?
   * @access private
   */
  public function start_node ($node, $is_last) {}
  /**
   * Finish rendering a node.
   * @param object $node
   * @param boolean $is_last Is this a terminal node in this list?
   * @access private
   */
  public function finish_node ($node, $is_last) {}

  /**
   * Called before sub-nodes are rendered.
   * @param object $node
   * @access private
   */
  public function pre_draw_children ($node) {}
  /**
   * Called after sub-nodes are rendered.
   * @param object $node
   * @access private
   */
  public function post_draw_children ($node) {}

  /**
   * Draw the specified icon for this node.
   * This builds the customary branch structure next to the nodes and provides for indenting. This
   * function will be called once per nesting level for the given node.
   * @param integer $kind Any of the tree constants are valid here.
   * @param object $node
   * @access private
   */
  public function draw_icon ($kind, $node)
  {
    switch ($kind)
    {
    case Tree_line:
      echo $this->context->resolve_icon_as_html ('{icons}tree/vert', '|');
      break;
    case Tree_blank:
      echo $this->context->resolve_icon_as_html ('{icons}tree/blank', ' ');
      break;
    case Tree_ell:
    case Tree_ell_plus:
      echo $this->context->resolve_icon_as_html ('{icons}tree/ell', 'L');
      break;
    case Tree_tee:
    case Tree_tee_plus:
      echo $this->context->resolve_icon_as_html ('{icons}tree/tee', '|-');
      break;
    }
  }

  /**
   * Render the node itself.
   * Defaults to drawing the 'title' of the node.
   * @see TREE::draw_title()
   * @param object $node
   * @access private
   */
  public function draw_node ($node)
  {
    $this->draw_title ($node);
  }
  
  /**
   * Render the title for this node.
   * Defers this function to the 'decorator' passed in to the constructor.
   * @see TREE_DECORATOR
   * @param object $node
   * @access private
   */
  public function draw_title ($node)
  {
    $icon = $this->node_info->icon_for ($node);
    $title = $this->node_info->title_for ($node);
    
    if (isset ($this->decorator))
    {
      $this->decorator->draw ($node, $title, $icon);
    }
    else
    {
      if ($icon)
      {
        echo ' ' . $icon . ' ';
      }
      echo $title;
    }
  }

  /**
   * Render the sub-tree for this node.
   * @param object $node
   * @param boolean $is_last Is this a terminal node in this list?
   * @access private
   */
  public function iterate_node ($node, $is_last)
  {
    $this->_depth += 1;
    if ($node)
    {
      $this->start_node ($node, $is_last);

      $nodes = $this->node_info->sub_nodes ($node);
      $has_children = !empty ($nodes);

      if (count ($this->_stack))
      {
        foreach ($this->_stack as $s)
        {
          if ($s)
          {
            $this->draw_icon (Tree_line, $node);
          }
          else
          {
            $this->draw_icon (Tree_blank, $node);
          }
        }
      }

      if ($this->_depth > $this->_min_depth_for_icons)
      {
        if ($is_last)
        {
          if ($has_children)
          {
            $this->draw_icon (Tree_ell_plus, $node);
          }
          else
          {
            $this->draw_icon (Tree_ell, $node);
          }
          array_push ($this->_stack, 0);
        }
        else
        {
          if ($has_children)
          {
            $this->draw_icon (Tree_tee_plus, $node);
          }
          else
          {
            $this->draw_icon (Tree_tee, $node);
          }
          array_push ($this->_stack, 1);
        }
      }

      $this->draw_node ($node);

      if ($has_children)
      {
        $this->pre_draw_children ($node);
        $this->iterate_nodes ($nodes);
        $this->post_draw_children ($node);
      }

      $this->finish_node ($node, $is_last);

      array_pop ($this->_stack);
    }
    $this->_depth -= 1;
  }

  /**
   * Renders the given array of nodes.
   * @param array [object] $nodes
   * @access private
   */
  public function iterate_nodes ($nodes)
  {
    $count = count ($nodes);
    $index = 0;
    while ($index < $count)
    {
      $node = $nodes [$index];
      if (isset ($this->decorator))
      {
        $this->decorator->node_found ($node);
      }
      $this->iterate_node ($node, $index == $count - 1);
      $index += 1;
    }
  }

  /**
   * Tracks whether a depth in the tree has an open branch or not.
   * Used to determine whether to draw a 'blank' or a 'line' in that column.
   * @var array[integer]
   * @access private
   */
  protected $_stack;

  /**
   * Tracks the current depth in the tree during rendering.
   * @var integer
   * @access private
   */
  protected $_depth;

  /**
   * Specifies the minimum depth before 'plus' or 'ell' icons are drawn.
   * If the tree is rendered statically, then it doesn't make sense to render
   * the 'plus' or 'ell' symbols for the root level, since the tree can't be
   * opened or closed anyway. Static trees reset this value to '1' to avoid
   * drawing those icons.
   * @var integer
   * @access private
   */
  protected $_min_depth_for_icons = 0;
}

/**
 * Handles rendering for specific node types in a {@link TREE}.
 * @package webcore
 * @subpackage tree
 * @version 3.3.0
 * @since 2.2.1
 * @abstract
 * @access private
 */
abstract class TREE_NODE_INFO extends WEBCORE_OBJECT
{
  /**
   * @var boolean
   */
  public $nodes_are_links = true;
  
  /**
   * Render this node as opened.
   * Iterates all parents to make sure they are open as well.
   * @param object $node
   */
  public function set_open_node ($node)
  {
    while ($node)
    {
      $this->open_nodes [$this->id ($node)] = $node;
      $node = $this->parent ($node);
    }
  }
  
  /**
   * Render this node as visible.
   * Iterates all parents to make sure they are open as well.
   * @param object $node
   */
  public function set_visible_node ($node)
  {
    $this->set_open_node ($this->parent ($node));
  }
  
  /**
   * Render this node as selected.
   * Use 'set_visible_node' if you want this node to also be visible on initial
   * display.
   * @see TREE_NODE_INFO::set_visible_node()
   * @param object $node
   */
  public function set_selected_node ($node)
  {
    $this->selected_nodes [$this->id ($node)] = $node;
  }

  /**
   * Return list of sub-nodes for 'node'.
   * Default behavior returns no nodes.
   * @param object $node
   * @access private
   * @return TREE_NODE_INFO[]
   */
  public function sub_nodes ($node) {}

  /**
   * Return the title for the given node.
   * @param NAMED_OBJECT $node Draw the title for this project.
   * @return string
   * @abstract
   */
  public abstract function title_for ($node);

  /**
   * Return the icon for the given node.
   * @param PROJECT $node Draw the title for this project.
   * @return string
   */
  public function icon_for ($node)
  {
  }
  
  /**
   * Return the node's id.
   * @param object $node
   * @return integer
   * @access private
   * @abstract
   */
  public abstract function id ($node);
  
  /**
   * Return the node's parent node.
   * @param object $node
   * @return object
   * @access private
   * @abstract
   */
  public abstract function parent ($node);

  /**
   * Is this node closed in this tree?
   * @param object $node
   * @return boolean
   */
  public function closed ($node)
  {
    return ! read_array_index ($this->open_nodes, $this->id ($node));
  }
  
  /**
   * Is this node selected in this tree?
   * If so, most trees will render this node differently (perhaps highlighting
   * it and not making it a link).
   * @param object $node
   * @return boolean
   * @access private
   */
  public function selected ($node)
  {
    if (isset ($this->selected_nodes))
    {
      return read_array_index ($this->selected_nodes, $this->id ($node));
    }
    return false;
  }
  
  /**
   * Is this node selectable in this tree?
   * Useful for selectable trees, return false to render the selector as
   * disabled or invisible for this node.
   * @param object $node
   * @return boolean
   * @access private
   */
  public function selectable ($node) { return true; }
  
  /**
   * Is this node visitable in this tree?
   * This is useful for making certain nodes non-linkable (e.g. if can see the
   * node, but can't perform the action or see the page for the node, then
   * return false).
   * @param object $node
   * @return boolean
   * @access private
   */
  public function visitable ($node) { return $this->nodes_are_links; }

  /**
   * @var array[integer => object]
   * @access private
   */
  public $open_nodes;

  /**
   * @var array[integer => object]
   * @access private
   */
  public $selected_nodes;
}

/**
 * Renders non-node-specific information to a tree.
 * Used to render the form for a selector or multi-selector tree. Since the selectors can be applied
 * to many different node types, this functionality is encapsulated in the decorator rather then the
 * node info (since node info gets and renders node-specific information).
 * @see TREE_NODE_INFO
 * @package webcore
 * @subpackage tree
 * @version 3.3.0
 * @since 2.2.1
 * @access private
 */
class TREE_DECORATOR extends WEBCORE_OBJECT
{
  /**
   * @param TREE $tree Decorate this tree.
   */
  public function __construct ($tree)
  {
    parent::__construct ($tree->app);
    $this->tree = $tree;
  }

  /**
   * Node is about to be rendered.
   * @param object $node
   */
  public function node_found ($node) {}

  /**
   * Tree is about to start display.
   */
  public function pre_iterate () {}

  /**
   * Tree is about to finish display.
   */
  public function post_iterate () {}

  /**
   * Render the decorator for this node.
   * @param object $node
   * @param string $text
   * @param string $icon
   */
  public function draw ($node, $text, $icon) {}

  /**
   * Reference to the parent tree's node info object.
   * @var TREE_NODE_INFO
   */
  public function node_info ()
  {
    return $this->tree->node_info;
  }
}

/**
 * Tree drawn with simple HTML 'div' containers.
 * @package webcore
 * @subpackage tree
 * @version 3.3.0
 * @since 2.5.0
 */
class HTML_TREE extends TREE
{
  /**
   * Is this tree centered in its parent container?
   * @var boolean
   */
  public $centered = false;

  public $CSS_class = 'tree';

  /**
   * Start rendering the tree.
   * @access private
   */
  public function start ()
  {
    $class = $this->CSS_class ? "class=\"$this->CSS_class\"" : '';
    $style = $this->centered ? "style=\"margin: auto; display: table\"" : '';

    if ($class || $style)
    {
      echo "<div $class $style>";
    }
  }

  /**
   * Finish rendering the tree.
   * @access private
   */
  public function finish ()
  {
    if ($this->centered || $this->CSS_class)
    {
?>
</div>
<?php
    }
  }

  /**
   * Start rendering a node.
   * @param object $node
   * @param boolean $is_last Is this a terminal node in this list?
   * @access private
   */
  public function start_node ($node, $is_last)
  {
?>
  <div class="tree-node">
<?php
  }

  /**
   * Finish rendering a node.
   * @param object $node
   * @param boolean $is_last Is this a terminal node in this list?
   * @access private
   */
  public function finish_node ($node, $is_last)
  {
?>
  </div>
<?php
  }
}

?>