<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.5.0
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
 * Shows some commands in a line; uses a drop-down for others.
 * The drop-down list is displayed with CSS for compliant browsers and emulated
 * with JavaScript for non-compliant ones. If {@link APPLICATION::
 * dhtml_allowed()} returns <code>False</code>, this option reverts to {@link
 * Menu_horizontal}. Used by the {@link MENU_RENDERER}.
 * @see Menu_horizontal
 */
define ('Menu_horizontal_with_drop_down', 'hd');

/**
 * Shows some commands in a list; uses a drop-down for others.
 * The drop-down list is displayed with CSS for compliant browsers and emulated
 * with JavaScript for non-compliant ones. If {@link APPLICATION::
 * dhtml_allowed()} returns <code>False</code>, this option reverts to {@link
 * Menu_vertical}. Used by the {@link MENU_RENDERER}.
 * @see Menu_horizontal
 */
define ('Menu_vertical_with_drop_down', 'vd');

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
 * Show the {@link MENU_RENDERER::$trigger_title} when rendering.
 * Used by the {@link MENU_RENDERER}.
 */
define ('Menu_options_show_trigger_title', 1);

/**
 * Show the {@link MENU_RENDERER::$trigger_title} when rendering.
 * Used by the {@link MENU_RENDERER}.
 */
define ('Menu_options_show_trigger_icon', 2);

/**
 * Show the {@link MENU_RENDERER::$trigger_title} when rendering.
 * Used by the {@link MENU_RENDERER}.
 */
define ('Menu_options_show_selected_as_trigger_title', 4);

/**
 * Show compact menu items with the selected item in the trigger title.
 * Used by the {@link MENU_RENDERER}.
 */
define ('Menu_options_show_as_select', 7);

/**
 * Show the {@link MENU_RENDERER::$trigger_title} when rendering.
 * Used by the {@link MENU_RENDERER}.
 */
define ('Menu_options_show_trigger_title_and_icon', 3);

/**
 * Renders {@link COMMAND}s to HTML.
 * @package webcore
 * @subpackage renderer
 * @version 3.5.0
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
  public $display_mode = Menu_horizontal_with_drop_down;

  /**
   * Rendering options for the menu itself.
   */
  public $options = Menu_options_show_trigger_title_and_icon;

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
   * The text to show when {@link $show_trigger_title} is true.
   *
   * @var string
   */
  public $trigger_title = 'Commands';

  /**
   * The icon to show when {@link $show_trigger_icon} is true.
   *
   * @var string
   */
  public $trigger_icon = '{icons}buttons/menu';

  /**
   * The CSS class to use for the entire drop-down trigger.
   *
   * @var string
   */
  public $trigger_button_css_class = 'button';

  /**
   * The class to include when {@link Menu_show_as_buttons} is <code>False</code>.
   * Defaults to {@link CONTEXT_DISPLAY_OPTIONS::$object_class}.
   * @var string
   */
  public $separator_class;

  /**
   * Target frame for generated links.
   * @var string
   */
  public $target = '';

  /**
   * Sets up the renderer for a general size.
   * The various rendering options can be set as a group using one of the size
   * options detailed below.
   * @param string $size_option May be one of {@link Menu_size_compact}, {@link
   * Menu_size_standard} or {@link Menu_size_full}. Descendants may add more
   * supported types if desired.
   */
  public function set_size ($size_option)
  {
    $this->display_mode = Menu_horizontal_with_drop_down;
    $this->options |= Menu_options_show_trigger_title;
    switch ($size_option)
    {
    case Menu_size_minimal:
      $this->num_important_commands = 0;
      $this->options &= ~Menu_options_show_trigger_title;
      break;
    case Menu_size_compact:
      $this->num_important_commands = 0;
      $this->content_mode |= Menu_show_as_buttons;
      break;
    case Menu_size_standard:
      $this->num_important_commands = 3;
      $this->options &= ~Menu_options_show_trigger_title;
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
      if ($this->content_mode & Menu_show_as_buttons)
      {
        $class = "menu-items buttons";
      }
      else
      {
        $class = $this->separator_class;
        if (!$class)
        {
          $class = $this->context->display_options->menu_class;
        }
        $class = "menu-items " . $class;
      }

      echo "<ul class=\"$class\">";

      switch ($this->display_mode)
      {
        case Menu_horizontal:
          $this->_draw_commands ($commands, false, 'button');
          break;
        case Menu_vertical:
          $this->_draw_vertical_menu ($commands, false);
          break;
        case Menu_horizontal_with_drop_down:
        case Menu_vertical_with_drop_down:
          $this->_draw_important_with_dropdown ($commands, $this->display_mode);
          break;
      }
      echo '</ul>';
    }
    if (isset ($this->env->profiler)) $this->env->profiler->stop ('ui');
  }

  /**
   * Draw some or all of the given commands.
   * @param COMMANDS $commands
   * @param boolean $important_only If <code>True</code>, shows only the first
   * {@link $num_important_commands} when sorted by {@link
   * COMMAND::$importance}.
   * @param string $css_class CSS class used for each {@link COMMAND}.
   * @access private
   */
  protected function _draw_commands ($commands, $important_only, $css_class)
  {
    $cmds = $commands->command_list ();
    $num_cmds_to_be_shown = $commands->num_executable_commands ();
    if ($important_only)
    {
      usort ($cmds, '_compare_commands_by_importance');
      $num_cmds_to_be_shown = min ($this->num_important_commands, $num_cmds_to_be_shown);
    }

    $idx_cmd = 0;

    foreach ($cmds as $cmd)
    {
      if ($cmd->executable)
      {
        if ($this->content_mode & Menu_show_as_buttons)
        {
          echo '<li>' . $this->_command_as_html ($cmd, $css_class) . '</li>';
        }
        else
        {
          echo '<li>' . $this->_command_as_html ($cmd, '') . '</li>';
        }
        $idx_cmd += 1;
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
   * @param string $css_class
   * @return string
   * @access private
   */
  protected function _command_as_html ($cmd, $css_class)
  {
    $Result = '';
    $text = '';

    if ($cmd->caption && (($this->content_mode & Menu_show_title) || ! $cmd->icon))
    {
      $text = $cmd->caption;
    }

    if ($cmd->icon && ($this->content_mode & Menu_show_icon))
    {
      $text = $this->context->get_text_with_icon($cmd->icon, $text, Sixteen_px);
    }

    if (!empty ($text))
    {
      $Result = $text;

      $class_statement = $css_class ? ' class="' . $css_class . '"' : '';

      if ($cmd->link)
      {
        $link = htmlspecialchars ($this->context->resolve_file ($cmd->link));
        $tag = '<a' . $class_statement . ' href="' . $link . '" title="' . global_text_options()->convert_to_html_attribute($cmd->link_title) . '"';
        if ($this->target)
        {
          $tag .= ' target="' . $this->target . '"';
        }
        if ($cmd->on_click)
        {
          $tag .= ' onclick="' .  $cmd->on_click . '; return false;"';
        }
        $tag .= '>';

	      if (!empty($cmd->description) && $css_class == 'menu-item')
	      {
          $description_class_statement = $css_class ? ' class="' . $css_class . '-description"' : '';
          $Result .= ' <span ' . $description_class_statement . '>' . $cmd->description . '</span>';
	      }

        $Result = $tag . $Result;

        $Result .= '</a>';
      }
      elseif ($class_statement)
      {
        $tag = '';

        if ($class_statement)
        {
          $tag = '<span ' . $class_statement . '>';
        }

        $Result = $tag . $Result;

        if ($class_statement)
        {
          $Result .= '</span>';
        }

	      if (!empty($cmd->description) && $css_class == 'menu-item')
	      {
	        $Result .= '<span class="menu-item-description">' . $cmd->description . '</span>';
	      }
      }
    }

    return $Result;
  }

  /**
   * Draw all commands in groups vertically.
   * Used by the drop-down renderer and lists that use the {@link
   * Menu_vertical} or {@link Menu_vertical_with_dropdown} style.
   * @param COMMANDS $commands
   * @param bool $important_only If true, only important commands are rendered.
   * @access private
   */
  protected function _draw_vertical_menu ($commands, $important_only)
  {
    echo '<ul class="menu">' . "\n";
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
          if ($group->name)
          {
            echo '<li class="menu-group-title">' . $group->name . '</li>' . "\n";
          }
          foreach ($group->commands as $cmd)
          {
            if ($cmd->executable)
            {
              echo '        <li>' . $this->_command_as_html ($cmd, 'menu-item') . '</li>' . "\n";
            }
          }
        }
      }
    }
    echo "</ul>\n";
  }

  /**
   * Draw the groups inside a vertical drop-down menu.
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
        if ($display_mode == Menu_horizontal_with_drop_down)
        {
          $this->_draw_horizontal_with_dropdown ($commands);
        }
        else
        {
          $this->_draw_vertical_menu ($commands, true);
          $this->_draw_drop_down ($commands);
        }
      }
      else
      {
        $this->_draw_drop_down ($commands);
      }
    }
    else
    {
      if ($display_mode == Menu_horizontal_with_drop_down)
      {
        $this->_draw_commands ($commands, false, 'button');
      }
      else
      {
        $this->_draw_vertical_menu ($commands, false);
      }
    }
  }

  /**
   * Draw the commands inside a vertical drop-down menu.
   * Uses JavaScript for browsers that don't support {@link Browser_CSS_2}. Uses
   * {@link _draw_vertical_menu()} to draw the contents of the drop-down.
   * @param COMMANDS $commands
   * @access private
   */
  protected function _draw_drop_down ($commands)
  {
    $trigger_class = 'menu-trigger';
    $menu_class = 'menu-dropdown';
    $menu_tag = '';

    $trigger = '';

    if ($this->options & Menu_options_show_trigger_title)
    {
      if ($this->options & Menu_options_show_selected_as_trigger_title)
      {
        foreach ($commands->command_list() as $command)
        {
          if (empty($command->link))
          {
            // TODO Add support for setting the selected item instead of just assuming that the one without a link is selected
            // TODO Should we show the icon of the selected item in the trigger as well? Should we use the menu icon as a badge?

            $trigger = $command->caption;
          }
        }
      }
      else
      {
        $trigger = $this->trigger_title;
      }
    }

    echo '<li class="' . $trigger_class . '"' . $menu_tag . '><div class="' . $this->trigger_button_css_class . '">';
    if ($this->options & Menu_options_show_trigger_icon)
    {
      if (empty ($trigger))
      {
        echo $this->context->resolve_icon_as_html($this->trigger_icon, '', Sixteen_px);
      }
      else
      {
        echo $this->context->get_text_with_icon($this->trigger_icon, $trigger, Sixteen_px);
      }
    }
    else
    {
      echo $trigger;
    }
    echo '<div class="' . $menu_class . '">';
    $this->_draw_vertical_menu ($commands, false);
    echo '</div></div></li>';
  }

  /**
   * Draw important commands and a trigger side-by-side.
   * Uses a {@link BOX_RENDERER} to draw cross-platform.
   * @param COMMANDS $commands
   * @access private
   */
  protected function _draw_horizontal_with_dropdown ($commands)
  {
    $this->_draw_commands ($commands, true, 'button');
    $this->_draw_drop_down ($commands);
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