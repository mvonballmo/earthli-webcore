<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tree
 * @version 3.0.0
 * @since 2.2.1
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

/**
 * Simple node implementation with link and text.
 * @package webcore
 * @subpackage tree
 * @version 3.0.0
 * @since 2.2.1
 */
class TREE_NODE
{
  /**
   * @param string $title Name of the node.
   * @param string $link Link the name to this url.
   * @param boolean $closed Is this node closed by default?
   * @param string $target Target the link to this frame.
   */
  function TREE_NODE ($title, $link = '', $closed = TRUE, $target = '')
  {
    static $id;
    $id++;

    $this->_id = $id;
    $this->_title = $title;
    $this->_link = $link;
    $this->_closed = $closed;
    $this->_target = $target;
  }

  /**
   * Add the given 'node' as a child.
   * @param TREE_NODE $node
   */
  function append ($node)
  {
    $node->_parent = $this;
    $this->_nodes [] = $node;
  }

  /**
   * Display text of this node (with optional link)
   * @return string
   */
  function text ()
  {
    if ($this->_link)
    {
      if ($this->_target)
      {
        return "<a href=\"$this->_link\" target=\"$this->_target\">$this->_title</a>";
      }

      return "<a href=\"$this->_link\">$this->_title</a>";
    }

    return $this->_title;
  }

  /**
   * Display text.
   * @var string
   * @access private
   */
  protected $_title;
  /**
   * URL for the link.
   * @var string
   * @access private
   */
  protected $_link;
  /**
   * Direct links to this target.
   * @var string
   * @access private
   */
  protected $_target;
  /**
   * Used by the tree to toggle DHTML sections.
   * @var integer
   * @access private
   */
  protected $_id;
  /**
   * Optional parent node.
   * @var TREE_NODE
   * @access private
   */
  protected $_parent;
  /**
   * Is this node closed initially?
   * @var boolean
   * @access private
   */
  protected $_closed;
}

require_once ('webcore/gui/tree.php');

/**
 * Handles tree-rendering for {@link TREE_NODE}s.
 * @package webcore
 * @subpackage tree
 * @version 3.0.0
 * @since 2.2.1
 */
class GENERIC_TREE_NODE_INFO extends TREE_NODE_INFO
{
  /**
   * Return list of sub-nodes for 'node'.
   * @param TREE_NODE $node
   * @access private
   */
  function sub_nodes ($node)
  {
    return $node->_nodes;
  }

  /**
   * Draw title for 'node'.
   * @param TREE_NODE $node
   * @return string
   * @access private
   */
  function title_for ($node)
  {
    return $node->text ();
  }

  /**
   * Is this node closed in this tree?
   * @param TREE_NODE $node
   * @return bool
   * @access private
   */
  function closed ($node)
  {
    return $node->_closed;
  }

  /**
   * Return the node's parent node.
   * @param TREE_NODE $node
   * @return TREE_NODE
   * @access private
   */
  function parent ($node)
  {
    return $node->_parent;
  }

  /**
   * Return the node's id.
   * @param TREE_NODE $node
   * @return integer
   * @access private
   */
  function id ($node)
  {
    return $node->_id;
  }
}
?>