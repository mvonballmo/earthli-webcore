<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.7.0
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

/** */
require_once ('webcore/obj/webcore_object.php');

/**
 * Shows all commands in a line. 
 * Used by the {@link MENU_RENDERER}.
 * @see Menu_vertical
 * @see Menu_horizontal_with_dropdown
 * @see Menu_vertical_with_dropdown
 */
define ('Menu_horizontal', 'h');
/**
 * Shows all commands in a list. 
 * Used by the {@link MENU_RENDERER}.
 * @see Menu_horizontal
 */
define ('Menu_vertical', 'v');
/**
 * Shows some commands in a line; uses a dropdown for others.
 * The drop-down list is displayed with CSS for compliant browsers and emulated
 * with JavaScript for non-compliant ones. If {@link APPLICATION::
 * dhtml_allowed()} returns <code>False</code>, this option reverts to {@link
 * Menu_horizontal}. Used by the {@link MENU_RENDERER}.
 * @see Menu_horizontal
 */
define ('Menu_horizontal_with_dropdown', 'hd');
/**
 * Shows some commands in a list; uses a dropdown for others.
 * The drop-down list is displayed with CSS for compliant browsers and emulated
 * with JavaScript for non-compliant ones. If {@link APPLICATION::
 * dhtml_allowed()} returns <code>False</code>, this option reverts to {@link
 * Menu_vertical}. Used by the {@link MENU_RENDERER}.
 * @see Menu_horizontal
 */
define ('Menu_vertical_with_dropdown', 'vd');

/**
 * Show the icon for {@link COMMAND}s.
 * Used by the {@link MENU_RENDERER}.
 * @see Menu_show_all_as_buttons
 */
define ('Menu_show_icon', 1);
/**
 * Show the title for {@link COMMAND}s.
 * Used by the {@link MENU_RENDERER}.
 * @see Menu_show_all_as_buttons
 */
define ('Menu_show_title', 2);
/**
 * Show {@link COMMAND}s as buttons.
 * Used by the {@link MENU_RENDERER}. If <code>False</code>, the commands are
 * shown separated by the {@link CONTEXT_DISLAY_OPTIONS::$menu_separator}.
 * @see Menu_show_all_as_buttons
 */
define ('Menu_show_as_buttons', 4);
/**
 * All menu display options.
 * Used by the {@link MENU_RENDERER}.
 * @see Menu_show_icon
 * @see Menu_show_title
 * @see Menu_show_as_buttons
 */
define ('Menu_show_all_as_buttons', 7);
/**
 * All menu display options.
 * Used by the {@link MENU}.
 * @see Menu_show_all_as_buttons
 */
define ('Menu_show_all_as_list', 3);

/**
 * Renders {@link COMMAND}s to HTML.
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.7.0
 * @abstract
 */
class MENU_RENDERER extends WEBCORE_OBJECT
{
  /**
   * Determines the content of a menu item
   * Can be a combination of {@link Menu_show_icon}, {@link
   * Menu_show_title} or {@link Menu_show_as_buttons}.
   * @var string
   */
  public $content_mode = Menu_show_all_as_buttons;

  /**
   * Rendering mode for commands.
   *  This options is ignored if {@link
   * APPLICATION::dhmtl_allowed()} is <code>False</code>.
   */
  public $display_mode = Menu_horizontal_with_dropdown;

  /**
   * Number of commands to show outside of the drop-down.
   * Used only if {@link $display_mode} is {@link Menu_horizontal_with_dropdown}
   * or {@link Menu_vertical_with_dropdown}. In that case, this is the maximum
   * number of commands displayed outside of the drop-down; commands are sorted
   * by {@link COMMAND::$importance}. The full list is available in the drop-
   * down list. This option is ignored if {@link APPLICATION:: dhtml_allowed()}
   * returns <code>False</code>.
   * @var integer
   */
  public $num_important_commands = 3;

  /**
   * Attachment for the menu in its container.
   * Can be {@link Menu_align_default}, {@link Menu_align_left}, {@link
   * Menu_align_right} or {@link Menu_align_center}. The default option doesn't
   * manipulate the menu's position, letting the CSS of the container govern the
   * menu position.
   * @var string
   */
  public $alignment = Menu_align_right;

  /**
   * Show the word "Commands" for the drop-down?
   * Turned off when using {@link Menu_size_minimal} with {@link set_size()}.
   * @var boolean
   */
  public $show_commands_title = true;

  /**
   * Use this separator if {@link Menu_show_as_buttons} is <code>False</code>.
   * Defaults to {@link CONTEXT_DISPLAY_OPTIONS::$menu_separator}.
   * @var string
   */
  public $separator;

  /**
   * Target frame for generated links.
   * Use only from within framesets; usually used to target the "_top" frame.
   * @var string
   */
  public $target = '';

  /**
   * @param CONTEXT $context
   */
  public function MENU_RENDERER ($context)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($context);
    $browser = $this->env->browser ();
    $this->_supports_css_2 = $browser->supports (Browser_CSS_2);
    $this->_is_ie = $browser->is (Browser_ie);
  }
  
  /**
   * Sets up the renderer for a general size.
   * The various rendering options can be set as a group using one of the size
   * options detailed below.
   * @param string $size_option May be one of {@link Menu_size_compact}, {@link
   * Menu_size_standard} or {@link Menu_size_full}. Descendents may add more
   * supported types if desired.
   */
 public function set_size ($size_option)
 {
    $this->display_mode = Menu_horizontal_with_dropdown;
    $this->show_commands_title = true;
    switch ($size_option)
    {
    case Menu_size_minimal:
      $this->num_important_commands = 0;
      $this->show_commands_title = false;
      break;
    case Menu_size_compact:
      $this->num_important_commands = 0;
      break;
    case Menu_size_standard:
      $this->num_important_commands = 3;
      break;
    case Menu_size_toolbar:
      $this->content_mode = Menu_show_all_as_buttons - Menu_show_title;
      $this->display_mode = Menu_horizontal;
      break;
    case Menu_size_full:
      $this->display_mode = Menu_horizontal;
      break;
    }
 }
 
  /**
   * Render the commands into a toolbar container.
   * Renders the buttons into a DIV that functions as a toolbar if there are any
   * executable commands, otherwise renders nothing.
   * @param COMMANDS $commands
   * @param string $CSS_class
   */
  public function display_as_toolbar ($commands, $CSS_class = 'menu-bar-top')
  {
    if ($commands->num_executable_commands () > 0)
    {
      ?>
      <div class="<?php echo $CSS_class; ?>">
        <?php $this->display ($commands); ?>
        <div style="clear: both"></div>
      </div>
      <?php
    }
  }

  /**
   * Render the commands to HTML.
   * Depending on settings, this will render as a toolbar (across) or as a menu
   * list (vertical) and may include styles and/or javascript to make it a drop-
   * down menu.
   * @param COMMANDS $commands
   */
  public function display ($commands)
  {
    if (isset ($this->env->profiler)) $this->env->profiler->start ('ui');
    if ($commands->num_executable_commands ())
    {
      $this->_start_alignment ();
      switch ($this->display_mode)
      {
        case Menu_horizontal:
          $this->_draw_commands ($commands, false);
          break;
        case Menu_vertical:
          $this->_draw_vertical_menu ($commands, false);
          break;
        case Menu_horizontal_with_dropdown:
        case Menu_vertical_with_dropdown:
          $this->_draw_important_with_dropdown ($commands, $this->display_mode);
          break;
      }
      $this->_finish_alignment ();
    }
    if (isset ($this->env->profiler)) $this->env->profiler->stop ('ui');
  }
  
  /**
   * Start drawing the container for {@link $alignment}.
   * Called by {@link display()} to open the container closed by {@link
   * _start_alignment()}.
   * @access private
   */
  protected function _start_alignment ()
  {
    switch ($this->alignment)
    {
    case Menu_align_left:
    case Menu_align_right:
      $style = ' style="float: ' . $this->alignment . '"';
      break;
    case Menu_align_center:
      $style = ' style="margin: auto; display: table; text-align: center;"';
      break;
    case Menu_align_inline:
      $style = ' style="display: inline-block"';
      break;
    default:
      $style = '';
    }
    
    echo '<div'. $style . '>' . "\n  ";
  }
  
  /**
   * Finish drawing the container for {@link $alignment}.
   * Called by {@link display()} to close the container opened by {@link
   * _start_alignment()}.
   * @access private
   */
  protected function _finish_alignment ()
  {
    echo '</div>' . "\n";
  }
  
  /**
   * Draw some or all of the given commands.
   * @param COMMANDS $commands
   * @param boolean $important_only If <code>True</code>, shows only the first
   * {@link $num_important_commands} when sorted by {@link
   * COMMAND::$importance}.
   * @param string $CSS_class CSS class used for each {@link COMMAND}.
   * @access private
   */
  protected function _draw_commands ($commands, $important_only, $CSS_class = 'menu-button')
  {
    $cmds = $commands->command_list ();
    $num_cmds_to_be_shown = $commands->num_executable_commands (); 
    if ($important_only)
    {
      usort ($cmds, '_compare_commands_by_importance');
      $num_cmds_to_be_shown = min ($this->num_important_commands, $num_cmds_to_be_shown);
    }

    $idx_cmd = 0;
    if ($this->separator)
    {
      $sep = $this->separator;
    }
    else
    {
      $sep = $this->context->display_options->menu_separator;
    }
      
    foreach ($cmds as $cmd)
    {
      if ($cmd->executable)
      {
        if ($this->content_mode & Menu_show_as_buttons)
        {
          echo '  ' . $this->_command_as_html ($cmd, $CSS_class) . "\n";
        }
        else
        {
          if ($idx_cmd > 0)
          {
            echo $sep;
          }
          echo '  ' . $this->_command_as_html ($cmd, '') . "\n";
        }
        $idx_cmd++;
        if ($idx_cmd == $num_cmds_to_be_shown)
        {
          break;
        }
      }
    }
  }
  
  /**
   * Create an HTML link for the command.
   * @param COMMAND $cmd
   * @return string
   * @access private
   */
  protected function _command_as_html ($cmd, $CSS_class)
  {
    $Result = '';
    
    if ($cmd->icon && ($this->content_mode & Menu_show_icon))
    {
      $parts [] = $this->context->resolve_icon_as_html ($cmd->icon, ' ', '16px');
    }
    if ($cmd->title && (($this->content_mode & Menu_show_title) || ! $cmd->icon))
    {
      $parts [] = $cmd->title;
    }
    
    if (isset ($parts))
    {
      $sep = '&nbsp;';
      $Result = implode ($sep, $parts);
      if ($CSS_class)
      {
        $CSS_class = ' class="' . $CSS_class . '"';
      }
      if ($cmd->link)
      {
        $link = htmlspecialchars ($this->context->resolve_file ($cmd->link));
        $tag = '<a' . $CSS_class . ' href="' . $link . '"';
        if ($this->target)
        {
          $tag .= ' target="' . $this->target . '"';
        }
        $tag .= '>';
          
        /* Important! IE displays the last character in this last link of the last 
         * button in the menu again underneath the menus. The code below makes
         * sure that it is a space so it doesn't appear on the screen. IE - so
         * crappy it hurts.
         */
        
        if ($this->_is_ie)
        {
          $Result = $tag . $Result . ' </a>';
        }
        else
        {
          $Result = $tag . $Result . '</a>';
        }
      }
      elseif ($CSS_class)
      {
        $Result = '<span' . $CSS_class . '>' . $Result . '</span>';        
      }
    }
    
    return $Result;
  }
  
  /**
   * Draw all commands in groups vertically.
   * Used by the drop-down renderer and lists that use the {@link
   * Menu_vertical} or {@link Menu_vertical_with_dropdown} style.
   * @param COMMANDS $commands
   * @param string $CSS_class Used for the menu container.
   * @access private
   */
  protected function _draw_vertical_menu ($commands, $important_only)
  {
    echo '    <div class="menu">' . "\n";
    if ($important_only)
    {
      $this->_draw_commands ($commands, true, 'menu-item');
    }
    else
    {
      $groups = $commands->groups ();
      foreach ($groups as $group)
      {
        if (! $group->is_empty ())
        {
          echo '      <div class="menu-group">' . "\n";
          if ($group->name)
          {
            echo '        <div class="menu-group-title">' . $group->name . '</div>' . "\n";
          }
          foreach ($group->commands as $cmd)
          {
            if ($cmd->executable)
            {
              echo '        ' . $this->_command_as_html ($cmd, 'menu-item') . '' . "\n";
            }
          }
          echo '      </div>' . "\n";
        }
      }
    }
    echo "    </div>\n";
  }  

  /**
   * Draw the groups inside a vertical dropdown menu.
   * Uses JavaScript for browsers that don't support {@link Browser_CSS_2}.
   * @param COMMANDS $commands
   * @param string $display_mode One of {@link Menu_horizontal_with_dropdown} or
   * {@link Menu_vertical_with_dropdown}.
   * @access private
   */
  protected function _draw_important_with_dropdown ($commands, $display_mode)
  {
    if ($this->context->dhtml_allowed () && ($commands->num_executable_commands () > $this->num_important_commands))
    {
      if ($this->num_important_commands > 0)
      {
        if ($display_mode == Menu_horizontal_with_dropdown)
        {
          $this->_draw_horizontal_with_dropdown ($commands);
        }
        else
        {
          $this->_draw_vertical_menu ($commands, true);
          $this->_draw_dropdown ($commands);
        }
      }
      else
      {
        $this->_draw_dropdown ($commands);
      }
    }
    else
    {
      if ($display_mode == Menu_horizontal_with_dropdown)
      {
        $this->_draw_commands ($commands, false);
      }
      else
      {
        $this->_draw_vertical_menu ($commands, false);
      }
    }
  }
  
  /**
   * Draw the commands inside a vertical dropdown menu.
   * Uses JavaScript for browsers that don't support {@link Browser_CSS_2}. Uses
   * {@link _draw_vertical_menu()} to draw the contents of the drop-down.
   * @param COMMANDS $commands
   * @access private
   */
  protected function _draw_dropdown ($commands)
  {
    $trigger_class = 'menu-trigger';
    $menu_class = 'menu-dropdown';
    $menu_tag = '';
    if (! $this->_supports_css_2)
    {
      $trigger_class .= '-ie';
      $menu_class .= '-ie';
      $menu_id = 'menu_' . mt_rand ();
      $menu_tag = ' id="' . $menu_id . '"';
    }
    $trigger = $this->context->resolve_icon_as_html ("{icons}buttons/menu", ' ', '16px');
    if ($this->show_commands_title)
    {
      $trigger .= '&nbsp;Commands';
    }

    echo '  <div class="' . $trigger_class . '"' . $menu_tag . '>' . "\n";
    echo '    <div class="menu-button" style="float: none">' . $trigger . "</div>\n";
    echo '    <div class="' . $menu_class . '">' . "\n";
    $this->_draw_vertical_menu ($commands, false);
    echo '    </div>' . "\n";
    echo '  </div>' . "\n";

    if (! $this->_supports_css_2)
    {
      echo "<script type=\"text/javascript\">init_menu ('$menu_id');</script>";
    }
  }
  
  /**
   * Draw important commands and a trigger side-by-side.
   * Uses a {@link BOX_RENDERER} to draw cross-platform.
   * @param COMMANDS $commands
   * @access private
   */
  protected function _draw_horizontal_with_dropdown ($commands)
  {
    $_menu_box_renderer = $this->context->make_box_renderer ();
    $_menu_box_renderer->start_column_set ();
      $_menu_box_renderer->new_column ('padding: 0px');
        $this->_draw_commands ($commands, true);
      $_menu_box_renderer->new_column ('padding: 0px');
        $this->_draw_dropdown ($commands);
    $_menu_box_renderer->finish_column_set ();
  }
  
  /**
   * Initialized to <code>True</code> if browser is IE.
   * @var boolean
   * @access private
   */
  protected $_is_ie; 
  /**
   * Initialized to <code>True</code> if browser supports CSS 2.
   * @var boolean
   * @access private
   */
  protected $_supports_css_2; 
}

/**
 * Compare two {@link COMMAND}s.
 * Used by the {@link MENU_RENDERER} to determine which commands to
 * selectively show in the main menu section.
 * @param COMMAND $cmd1
 * @param COMMAND $cmd2
 * @return integer
 * @access private
 */
function _compare_commands_by_importance ($cmd1, $cmd2) 
{
  if ($cmd1->importance == $cmd2->importance)
  {
    return -1;
  }
  return ($cmd1->importance > $cmd2->importance) ? -1 : 1;
}

/**
 * Used by the {@link MENU_RENDERER}.
 * Used in place of class static variable.
 * @global BOX_RENDERER $_menu_box_renderer
 * @access private
 */
$_menu_box_renderer = null;

?>