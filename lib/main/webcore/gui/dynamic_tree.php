<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tree
 * @version 3.1.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
 * Dynamic tree displayed using simple DHTML.
 * @package webcore
 * @subpackage tree
 * @version 3.1.0
 * @since 2.2.1
 */
class DYNAMIC_TREE extends HTML_TREE
{
  /**
   * @param CONTEXT $context
   */
  public function DYNAMIC_TREE ($context)
  {
    TREE::TREE ($context);

    /* Make a copy. */    
    $this->_layer = $context->make_layer ();
    $this->_layer->margin_top = '.1em';
  }

  /**
   * Draw the specified icon for this node.
   * Overridden to allow the dynamic layer to draw the toggle.
   * @param integer $kind Any of the tree constants are valid here.
   * @param object $node
   * @access private
   */
  public function draw_icon ($kind, $node)
  {
    switch ($kind)
    {
    case Tree_tee_plus:
    case Tree_ell_plus:
      $this->_layer->name = 'obj_' . $this->node_info->id ($node);
      $this->_layer->visible = ! $this->node_info->closed ($node);
      $this->_layer->draw_toggle ();
      break;

    default:
      parent::draw_icon ($kind, $node);
    }
  }

  /**
   * Called before sub-nodes are rendered.
   * Overridden to open the dynamic layer before drawing the child nodes.
   * @param object $node
   * @access private
   */
  public function pre_draw_children ($node)
  {
    echo "</div>\n<div class=\"tree-node\">";
    $this->_layer->name = 'obj_' . $this->node_info->id ($node);
    $this->_layer->visible = ! $this->node_info->closed ($node);
    $this->_layer->start ();
  }

  /**
   * Called after sub-nodes are rendered.
   * Overridden to close the dynamic layer after drawing the child nodes.
   * @param object $node
   * @access private
   */
  public function post_draw_children ($node)
  {
    $this->_layer->finish ();
  }
}

?>