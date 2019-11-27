<?php

/****************************************************************************
 *
 * Copyright (c) 2002-2014 Marco Von Ballmoos
 *
 * This file is part of earthli WebCore.
 *
 * earthli WebCore is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * earthli WebCore is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with earthli WebCore; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * For more information about the earthli WebCore, visit:
 *
 * http://www.earthli.com/software/webcore
 ****************************************************************************/

$id = read_var ('id');
$folder_query = $App->login->folder_query ();
$folder = $folder_query->folder_for_entry_at_id ($id);

if (isset ($folder))
{
  $entry_query = $folder->entry_query ();

  /* Set this global value to optimize the query for accessing the particular
     type of entry. */
  if (isset ($entry_type_id))
  {
    $entry_query->set_type ($entry_type_id);
  }

  /** @var $entry ENTRY */
  $entry = $entry_query->object_at_id ($id);
}

if (isset($entry_query) && isset ($entry) && $App->login->is_allowed (Privilege_set_entry, Privilege_view, $entry))
{
  $App->set_referer ();
  $App->set_search_text (read_var ('search_text'));

  $entry_info = $entry->type_info ();

  /** @var $location_renderer LOCATION_RENDERER */
  $location_renderer = $entry->handler_for (Handler_location);
  $location_renderer->add_to_page_as_text ($Page, $entry);

  /** @var $navigator OBJECT_NAVIGATOR */
  $navigator = $entry->handler_for (Handler_navigator);
  $navigator->set_query ($entry_query);
  $navigator->set_selected ($id);
  $has_multiple_entries = $navigator->size () > 1;

  include_once ('webcore/util/options.php');
  $mobile_option = new STORED_OPTION ($App, "mobile_mode_{$entry_info->id}");
  $mobile_mode = $mobile_option->value ();

  if ($mobile_mode === null)
  {
    $mobile_mode = isset($mobile_mode_default) ? $mobile_mode_default : 0;
  }

  $Page->add_script_file ('{scripts}swiped-events.min.js');

  if (!$mobile_mode)
  {
    $Page->start_display ();
    ?>
    <div class="top-box">
      <?php

      if ($has_multiple_entries)
      {
        $show_list_option = new STORED_OPTION ($App, "show_{$entry_info->id}_list");
        $show_entry_list = $show_list_option->value ();

        $show_links = ($navigator->size () > 1) && $show_entry_list;

        echo '<div class="object-navigator">';

        if ($show_links)
        {
          ?>
          <div class="links">
            <?php echo $navigator->list_near_selected (); ?>
          </div>
          <?php
        }

        echo '</div>';

        echo '<div class="button-content">';
        echo '<span class="pager">';
        echo $navigator->controls ();
        echo "\n<script>" . $navigator->shortcut_key_scripts () . "</script>";
        echo '</span>';

        if (!$show_entry_list)
        {
          $icon = '{icons}buttons/show_list';
          $caption = 'Show list';
        }
        else
        {
          $icon = '{icons}buttons/close';
          $caption = 'Hide list';
        }

        $show_list_option_link = $show_list_option->setter_url_as_html (!$show_entry_list);

        ?><a href="<?php echo $show_list_option_link; ?>"
             class="button"><?php echo $Page->get_icon_with_text ($icon, Sixteen_px, $caption); ?></a><?php

        $icon = '{icons}indicators/high_green';
        $caption = 'Mobile Mode';

        $mobile_mode_link = $mobile_option->setter_url_as_html (!$mobile_mode);

        ?><a href="<?php echo $mobile_mode_link; ?>"
             class="button"><?php echo $Page->get_icon_with_text ($icon, Sixteen_px, $caption); ?></a><?php
      }
      else
      {
        echo '<div class="button-content">';
      }

      /** @var $command_renderer MENU_RENDERER */
      $menu_renderer = $entry->handler_for (Handler_menu);
      $menu_renderer->set_size (Menu_size_standard);
      /** @var COMMANDS $commands */
      $commands = $entry->handler_for (Handler_commands);
      $menu_renderer->display ($commands);

      echo '</div>';
      ?>
    </div>
    <div class="main-box">
      <div class="text-flow">
        <h1>
          <?php
          $t = $entry->title_formatter ();
          $t->max_visible_output_chars = 0;
          echo $entry->title_as_html ($t);
          ?>
        </h1>
        <?php
        /** @var $object_renderer OBJECT_RENDERER */
        $object_renderer = $entry->handler_for (Handler_html_renderer);
        $object_renderer->display ($entry);

        /** @var $associated_data ENTRY_ASSOCIATED_DATA_RENDERER */
        $associated_data = $entry->handler_for (Handler_associated_data);
        if (isset ($associated_data))
        {
          ?>
          <div class="clear-both">
            <?php
            $associated_data->display ($entry);
            ?>
          </div>
          <?php
        }
        ?>
      </div>
    </div>
    <?php
  }
  else
  {
    $Page->template_options->header_visible = false;
    $Page->template_options->footer_visible = false;
    $Page->start_display ();
    if ($has_multiple_entries)
    {
      echo "\n<script>" . $navigator->shortcut_key_scripts () . "</script>";
    }
    ?>
    <div class="text-flow minimal">

      <div class="minimal-commands">
        <?php
        /** @var MENU_RENDERER $renderer */
        $renderer = $entry->handler_for (Handler_menu);
        $renderer->set_size (Menu_size_minimal);
        /** @var COMMANDS $commands */

        // TODO Prepare a new list of commands

        $quick_commands = new COMMANDS ($App);

        $location_commands = array_reverse ($Page->location->commands->command_list ());

        $mobile_option_command = new COMMAND();
        $mobile_option_command->caption = 'Close Mobile Mode';
        $mobile_option_command->icon = '{icons}/buttons/close';
        $mobile_option_command->link = $mobile_option->setter_url_as_html (!$mobile_mode);

        $quick_commands->append ($mobile_option_command);

        foreach ($location_commands as $command)
        {
          if ($command->link || substr ($command->caption, 0, strlen ('<a')) === '<a')
          {
            $quick_commands->append ($command, 'Locations');
          }
        }

        $renderer->display ($quick_commands);
        ?>
      </div>
      <div class="minimal-commands-content">
        <h1>
          <?php
          $t = $entry->title_formatter ();
          $t->max_visible_output_chars = 0;
          echo $entry->title_as_html ($t);

          $counts = $navigator->counts ();
          echo " ($counts)";
          ?>
        </h1>
      </div>
    </div>
    <?php
    /** @var $object_renderer OBJECT_RENDERER */
    $object_renderer = $entry->handler_for (Handler_html_renderer);
    $options = new OBJECT_RENDERER_OPTIONS();
    $options->minimal = true;
    $object_renderer->display ($entry, $options);

    /** @var $associated_data ENTRY_ASSOCIATED_DATA_RENDERER */
    $associated_data = $entry->handler_for (Handler_associated_data);
    if (isset ($associated_data))
    {
      ?>
      <div class="clear-both">
        <?php
        $associated_data->display ($entry);
        ?>
      </div>
      <?php
    }
  }
  $Page->finish_display ();
}
else
{
  $Page->raise_security_violation ("You are not allowed to view this item.", $folder);
}
?>