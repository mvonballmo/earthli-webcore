<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
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

/**
 * Manages buildup of simple command lists.
 * Uses a {@link MENU_RENDERER} to render when {@link display()} or {@link
 * display_as_toolbar()} is called. Use {@link append()} and {@link prepend()}
 * to add items.
 * @package webcore
 * @subpackage gui
 * @version 3.1.0
 * @since 2.2.1
 */
class MENU extends WEBCORE_OBJECT
{
  public $renderer;

  /**
   *
   * Create a menu in the given context.
   * @param CONTEXT $context
   */  
  public function MENU ($context)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($context);
    
    $this->renderer = $this->context->make_menu_renderer ();
    $this->renderer->set_size (Menu_size_full);
    $this->renderer->content_mode = Menu_show_all_as_list;
    $this->renderer->alignment = Menu_align_default;

    include_once ('webcore/cmd/commands.php');
    $this->_commands = new COMMANDS ($context);
  }
  
  /**
   * Number of items in the menu.
   * @return integer
   */
  public function size ()
  {
    if (isset ($this->_commands))
    {
      return $this->_commands->size ();
    }
    
    return 0;
  }

  /**
   * Render the menu into a toolbar container.
   * @param string $CSS_class
   * @see MENU_RENDERER::display_as_toolbar()
   */
  public function display_as_toolbar ($CSS_class = 'menu-bar-top')
  {
    if (isset ($this->_commands))
    {
      $this->renderer->display_as_toolbar ($this->_commands, $CSS_class);
    }
  }

  /**
   * Render the menu as HTML.
   * @see MENU_RENDERER::display()
   */
  public function display ()
  {
    if (isset ($this->_commands))
    {
      $this->renderer->display ($this->_commands);
    }
  }
  
  /**
   * Return the menu as HTML.
   * Use {@link display()} to show the menu directly.
   */
  public function as_html ()
  {
    ob_start ();
      $this->display ();
      $Result = ob_get_contents ();
    ob_end_clean ();
    return $Result;
  }
  
  /**
   * Add an item to the end of the menu.
   * @param string $title Title shown for the link.
   * @param string $url Action to execute.
   * @param string $icon Path to the optional icon.
   * @param string $selected Renders as selected without the link if
   * <code>True</code>.
   */
  public function append ($title, $url = '', $icon = '', $selected = false)
  {
    $this->_commands->append ($this->_make_command ($title, $url, $icon, $selected));
  }
  
  /**
   * Add an item to the beginning of the menu.
   * @param string $title Title shown for the link.
   * @param string $url Action to execute.
   * @param string $icon Path to the optional icon.
   * @param string $selected Renders as selected without the link if
   * <code>True</code>.
   */
  public function prepend ($title, $url = '', $icon = '', $selected = false)
  {
    $this->_commands->prepend ($this->_make_command ($title, $url, $icon, $selected));
  }
  
  /**
   * Copy properties from the given object. 
   * @param MENU $other
   * @access private
   */
  protected function copy_from ($other)
  {
    $this->renderer = clone($other->renderer);
    $this->_commands = clone($other->_commands);
  }
  
  /**
   * Create and configure a {@link COMMAND}.
   * @param string $text Title of the command.
   * @param string $url Link for the command action.
   * @param string $icon Path to the icon for the command. 
   * @param string $selected Renders as selected without the link if
   * <code>True</code>.
   * @access private
   */
  protected function _make_command ($title, $url, $icon, $selected)
  {
    $Result = $this->_commands->make_command ();
    $Result->id = 'item_' . mt_rand ();
    if ($selected)
    {
      $Result->title = "<span class=\"selected\">$title</span>";
    }
    else
    {
      $Result->title = $title;
      $Result->link = $url;
    }
    $Result->icon = $icon;
    return $Result;
  }

  /**
   * {@link append()} and {@link prepend()} add items here.
   * Created by {@link _ensure_commands_exist()}.
   * @var COMMANDS
   * @access private
   */
  protected $_commands;
}

?>