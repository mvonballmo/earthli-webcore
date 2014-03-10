<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage command
 * @version 3.4.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
require_once ('webcore/obj/webcore_object.php');

/**
 * Represents a single command in a list of {@link COMMANDS}.
 * Rendered in the interface by a {@link MENU_RENDERER}.
 * @package webcore
 * @subpackage command
 * @version 3.4.0
 * @since 2.7.0
 */
class COMMAND
{
  /**
   * Unique id used to access the command.
   * @var string
   */
  public $id;

  /**
   * Text used inside the link.
   * @var string
   */
  public $caption = '';

  /**
   * Text used for the title of the link.
   * @var string
   */
  public $link_title = '';

  /**
   * URL to execute the command.
   * @var string
   */
  public $link = '';

  /**
   * Name of the image to use for the command when rendered.
   * @var string
   */
  public $icon = '';
  
  /**
   * JavaScript to execute when clicked.
   * @var string
   */
  public $on_click = '';

  /**
   * Text included after the link to describe the effects of executing the command.
   * @var string
   */
  public $description = '';

  /**
   * Can this command be executed?
   * The renderer can decide whether to display this button as disabled or to
   * simply omit it from the list.
   * @var boolean
   */
  public $executable = true;

  /**
   * Weight used to sort commands in lists.
   * Used by the {@link MENU_RENDERER} to determine which commands are shown
   * outside of a dropdown. Values are normalized prior to rendering.
   * @var integer
   */
  public $importance = Command_importance_default;
}

/**
 * A named list of {@link COMMAND}s.
 * Used by {@link COMMANDS} to cluster relevant commands.
 * @package webcore
 * @subpackage command
 * @version 3.4.0
 * @since 2.7.0
 */
class COMMAND_GROUP
{
  /**
   * @var string
   */
  public $name;

  /**
   * @see COMMAND 
   * @var COMMAND[]
   */
  public $commands = array ();  
  
  /**
   * Are there any executable commands in this group?
   * Renderers use this function to determine whether to render the group at
   * all.
   * @return boolean
   */
  public function is_empty ()
  {
    $Result = true;
    foreach ($this->commands as $cmd)
    {
      if ($cmd->executable)
      {
        $Result = false;
        break;
      }
    }
    return $Result;
  }  
}

/**
 * Collects {@link COMMAND}s into {@link COMMAND_GROUP}s.
 * Add commands with {@link append()} or {@link prepend()}. Display this object
 * with a {@link MENU_RENDERER}.
 * @package webcore
 * @subpackage command
 * @version 3.4.0
 * @since 2.7.0
 */
class COMMANDS extends WEBCORE_OBJECT
{
  /**
   * Return the list of {@link COMMAND_GROUP}s.
   * @return COMMAND_GROUP[]
   */
  public function groups ()
  {
    return $this->_groups;
  }
  
  /**
   * Return the list of all {@link COMMAND}s.
   * @return COMMAND[]
   */
  public function command_list ()
  {
    return $this->_ordered_commands;
  }
  
  /**
   * How many commands are there?
   * @return integer
   */
  public function size ()
  {
    return sizeof ($this->_commands);
  }

  /**
   * Return the named command.
   * Returns empty if none has been added with {@link append()} or {@link
   * prepend()}.
   * @param string $id
   * @return COMMAND
   */
  public function command_at ($id)
  {
    if (isset ($this->_commands [$id]))
    {
      return $this->_commands [$id];
    }
      
    return null;
  }

  /**
   * How many executable commands are there?
   * Renderers use this function to determine how to render a list (e.g. if the
   * number of executable commands are less than the number to display, then
   * there is no need to make a drop-down).
   * @return integer
   */
  public function num_executable_commands ()
  {
    $Result = 0;
    foreach ($this->_commands as $cmd)
    {
      if ($cmd->executable)
      {
        $Result += 1;
      }
    }
    return $Result;
  }
  
  /**
   * Start another group at the end of the list.
   * @param string $group
   */
  public function append_group ($group)
  {
    $this->_set_current_group ($group);
    $this->_groups [] = $this->_current_group;    
  }

  /**
   * Start another group at the beginning of the list.
   * @param string $group
   */
  public function prepend_group ($group)
  {
    $this->_set_current_group ($group);
    array_unshift ($this->_groups, $this->_current_group);    
  }

  /**
   * Add a command to the end of the current group.
   * Use the {@link make_command()} function to create the initial command, fill
   * in the parameters and add it to the list.
   * @see prepend()
   * @param COMMAND $cmd
   * @param string $group Add the command to this group. If this is empty, the
   * button is added to the current group. If the group does not exist, the
   * group is created with {@link append_group()}.
   */
  public function append ($cmd, $group = '')
  {
    $this->_add_command ($cmd, $group);
    $this->_current_group->commands [] = $cmd;
    $this->_ordered_commands [] = $cmd;
  }
  
  /**
   * Add a command to the beginning of the current group.
   * Use the {@link make_command()} function to create the initial command, fill
   * in the parameters and add it to the list.
   * @see prepend()
   * @param COMMAND $cmd
   * @param string $group Add the command to this group. If this is empty, the
   * button is added to the current group. If the group does not exist, the
   * group is created with {@link append_group()}.
   */
  public function prepend ($cmd, $group = '')
  {
    $this->_add_command ($cmd, $group);
    array_unshift ($this->_current_group->commands, $cmd);
    array_unshift ($this->_ordered_commands, $cmd);
  }
  
  /**
   * Disable all commands except those given. 
   * @param string[] $ids
   */
  public function disable_all_except ($ids)
  {
    foreach ($this->_ordered_commands as &$cmd)
    {
      if (! in_array ($cmd->id, $ids))
      {
        $cmd->executable = false;
      }
    }
  }

  /**
   * Add a command to the current group and the command list.
   * Called from {@link append()} and {@link prepend()}.
   * @param COMMAND $cmd
   * @param string $group Add the command to this group. If this is empty, the
   * button is added to the current group. If the group does not exist, the
   * group is created with {@link append_group()}.
   * @access private
   */
  protected function _add_command ($cmd, $group)
  {
    if (! isset ($this->_current_group) || (($group != '') && ($group != $this->_current_group->name)))
    {
      $this->append_group ($group);
    }
    $this->_commands [$cmd->id] = $cmd;
  }
  
  /**
   * Set a new group as current.
   * @param string $group
   * @access private
   */
  protected function _set_current_group ($group)
  {
    $this->_current_group = new COMMAND_GROUP ();
    $this->_current_group->name = $group;
  }
  
  /**
   * Make a new command object.
   * Does not add the result to its list. 
   * @return COMMAND
   * @access private
   */
  public function make_command ()
  {
    return $this->context->make_command ();
  }

  /**
   * Grouped lists of commands.
   * @var COMMAND_GROUP[]
   * @see COMMAND_GROUP
   * @see $_commands
   * @access private
   */
  protected $_groups = array ();

  /**
   * All commands indexed by {@link COMMAND::$id}.
   * @var COMMAND[]
   * @see COMMAND
   * @see $_groups
   * @see $_ordered_commands
   * @access private
   */
  protected $_commands = array ();

  /**
   * All commands in order.
   * @var COMMAND[]
   * @see COMMAND
   * @see $_commands
   * @see $_groups
   * @access private
   */
  protected $_ordered_commands = array ();

  /**
   * The command group to which new commands are automatically added.
   * @var COMMAND_GROUP
   */
  protected $_current_group;
}