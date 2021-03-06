<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.6.0
 * @since 2.2.1
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

/**
 * Manages buildup of simple command lists.
 * Uses a {@link MENU_RENDERER} to render when {@link display()} is called.
 * Use {@link append()} and {@link prepend()} to add items.
 * @package webcore
 * @subpackage gui
 * @version 3.6.0
 * @since 2.2.1
 */
class MENU extends WEBCORE_OBJECT
{
  /**
   * Reference to the renderer for this menu.
   * @var MENU_RENDERER
   */
  public $renderer;

  /**
   * {@link append()} and {@link prepend()} add items here.
   * Created by {@link _ensure_commands_exist()}.
   * @var COMMANDS
   */
  public $commands;

  /**
   *
   * Create a menu in the given context.
   * @param CONTEXT $context
   */  
  public function __construct ($context)
  {
    parent::__construct ($context);
    
    $this->renderer = $this->context->make_menu_renderer ();
    $this->renderer->set_size (Menu_size_full);
    $this->renderer->content_mode = Menu_show_all_as_list;

    include_once ('webcore/cmd/commands.php');
    $this->commands = new COMMANDS ($context);
  }
  
  /**
   * Number of items in the menu.
   * @return integer
   */
  public function size ()
  {
    if (isset ($this->commands))
    {
      return $this->commands->size ();
    }
    
    return 0;
  }

  /**
   * Render the menu as HTML.
   * @see MENU_RENDERER::display()
   */
  public function display ()
  {
    if (isset ($this->commands))
    {
      $this->renderer->display ($this->commands);
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
   * @param bool $selected Renders as selected without the link if <c>True</c>.
   * @param $link_title string The title to use for the url if the command is not selected.
   * @return \COMMAND
   */
  public function append ($title, $url = '', $icon = '', $selected = false, $link_title = '')
  {
    $result = $this->_make_command($title, $url, $icon, $selected, $link_title);
    $this->commands->append ($result);

    return $result;
  }
  
  /**
   * Add an item to the beginning of the menu.
   * @param string $title Title shown for the link.
   * @param string $url Action to execute.
   * @param string $icon Path to the optional icon.
   * @param bool $selected Renders as selected without the link if <c>True</c>.
   * @param $link_title string The title to use for the url if the command is not selected.
   */
  public function prepend ($title, $url = '', $icon = '', $selected = false, $link_title = '')
  {
    $this->commands->prepend ($this->_make_command ($title, $url, $icon, $selected, $link_title));
  }
  
  /**
   * Copy properties from the given object. 
   * @param MENU $other
   * @access private
   */
  protected function copy_from ($other)
  {
    $this->renderer = clone($other->renderer);
    $this->commands = clone($other->commands);
  }

  /**
   * Create and configure a {@link COMMAND}.
   * @param string $caption Text of the command.
   * @param string $url Link for the command action.
   * @param string $icon Path to the icon for the command.
   * @param bool $selected Renders as selected without the link if
   * <code>True</code>.
   * @param $link_title string The title to use for the url if the command is not selected.
   * @return COMMAND
   * @access private
   */
  protected function _make_command ($caption, $url, $icon, $selected, $link_title)
  {
    $Result = $this->commands->make_command ();
    $Result->id = 'item_' . mt_rand ();
    if ($selected)
    {
      $Result->caption = "<span class=\"selected\">$caption</span>";
    }
    else
    {
      $Result->caption = $caption;
      $Result->link = $url;
      $Result->link_title = $link_title;
    }
    $Result->icon = $icon;
    return $Result;
  }
}