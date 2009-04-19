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
 * A decorator that adds a radio button to each selectable node.
 * @package webcore
 * @subpackage tree
 * @version 3.1.0
 * @since 2.2.1
 * @access private
 */
class SELECTOR_TREE_DECORATOR extends TREE_DECORATOR
{
  /**
   * The name of the control to use for the selector.
   * @var string
   */
  public $control_name = 'selector';

  /**
   * Render the decorator for this node.
   * @param object $node
   * @param string $text
   */
  public function draw ($node, $text)
  {
    $node_info = $this->node_info ();
    $id = $node_info->id ($node);
    $disabled = ! ($node_info->selectable ($node));
    $dom_id = $this->control_name . '_' . $id;
?>
    <input type="radio" id="<?php echo $dom_id; ?>" name="<?php echo $this->control_name; ?>" value="<?php echo $id; ?>" style="vertical-align: middle"<?php if ($disabled) echo ' disabled'; ?>>
    <label for="<?php echo $dom_id; ?>"><?php echo $text; ?></label>
<?php
  }
}

/**
 * A decorator that adds a checkbox to each selectable node.
 * @package webcore
 * @subpackage tree
 * @version 3.1.0
 * @since 2.2.1
 * @access private
 */
class MULTI_SELECTOR_TREE_DECORATOR extends TREE_DECORATOR
{
  /**
   * Name of controls embedded in the tree nodes.
   * @var string
   */
  public $control_name;

  /**
   * Should child nodes be toggled with the parent?
   * @var boolean
   */
  public $auto_toggle_children = true;

  /**
   * @var string
   */
  public $form_name = 'update_form';

  /**
   * @param TREE $tree Decorate this tree.
   * @param array[integer] $selected_node_ids Initially selected node ids
   */
  public function __construct ($tree, $selected_node_ids)
  {
    parent::__construct ($tree);
    $this->selected_node_ids = $selected_node_ids;
  }

  /**
   * Node is about to be rendered.
   * @param object $node
   */
  public function node_found ($node)
  {
    if ($this->auto_toggle_children)
    {
      $node_info = $this->node_info ();
      $parent = $node_info->parent ($node);
      if ($parent)
      {
        $parent_id = $node_info->id ($parent);
        $id = $node_info->id ($node);
        $this->child_map [$parent_id] [] = $id;
      }
    }
  }

  /**
   * Called before displaying any nodes.
   */
  public function pre_iterate ()
  {
    if ($this->auto_toggle_children)
    {
      $this->child_map = array ();
    }
  }

  /**
   * Called after all nodes have been displayed.
   */
  public function post_iterate ()
  {
    if ($this->auto_toggle_children)
    {
      $control_name = $this->control_name;
      $form_name = $this->form_name;
?>
<script type="text/javascript">

  var <?php echo $control_name ?>_children = new Array ();

  <?php
  foreach ($this->child_map as $id => $child_ids)
  {
    $child_ids = join (',', $child_ids);
  ?>
  <?php echo $control_name ?>_children [<?php echo $id; ?>] = [<?php echo $child_ids; ?>];
  <?php
  }
  ?>

  function _on_click_<?php echo $control_name ?> (ctrl)
  {
    var f = document.getElementById ('<?php echo $form_name; ?>');
    var id = ctrl.value;
    var child_ids = <?php echo $control_name ?>_children [id];
    if (child_ids)
    {
      child_ids = '[' + child_ids.join ('][') + ']';
      var node_ids = f.elements ["<?php echo $control_name ?>[]"];
      for (var i = 0; i < node_ids.length; i++)
      {
        var node = node_ids [i];
        if (child_ids.indexOf ('[' + node.value + ']') >= 0)
        {
          node.checked = ctrl.checked;
          _on_click_<?php echo $control_name ?> (node);
        }
      }
    }
  }

  </script>
  <?php
    }
  }

  /**
   * Render the decorator for this node.
   * @param object $node
   * @param string $text
   */
  public function draw ($node, $text)
  {
    $control_name = $this->control_name;
    $node_info = $this->node_info ();
    $id = $node_info->id ($node);

    if (isset ($this->selected_node_ids [$id]))
    {
      $state = ' checked';
    }
    else
    {
      $state = '';
    }

    if ($this->auto_toggle_children)
    {
      $on_click = "_on_click_$control_name (this)";
    }

    $disabled = ! ($this->tree->node_info->selectable ($node));
    $dom_id = $this->control_name . '_' . $id;
?>
    <input type="checkbox" id="<?php echo $dom_id; ?>" name="<?php echo $control_name; ?>[]" <?php if (isset ($on_click)) echo "onclick=\"$on_click\""; ?> value="<?php echo $node->id; ?>" <?php echo $state; ?><?php if ($disabled) echo ' disabled'; ?>>
    <label for="<?php echo $dom_id; ?>"><?php echo $text; ?></label>
<?php
  }
}
?>