<?php

/****************************************************************************

Copyright (c) 2002 Marco Von Ballmoos

This file is part of earthli Webcore.

earthli Webcore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Webcore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Webcore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Webcore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/
  
  $id = read_var ('id');
  /** @var $folder_query USER_PROJECT_QUERY */
  $folder_query = $App->login->folder_query ();  
  $folder = $folder_query->folder_for_component_at_id ($id);

  if (isset ($folder))
  {
    $App->set_referer ();

    $comp_query = $folder->component_query ();
    /** @var $component COMPONENT */
    $component = $comp_query->object_at_id ($id);

    $class_name = $App->final_class_name ('PROJECT_COMPONENT_PANEL_MANAGER', 'projects/gui/project_panel.php');
    /** @var $panel_manager PROJECT_COMPONENT_PANEL_MANAGER */
    $panel_manager = new $class_name ($component);
    $panel = $panel_manager->selected_panel ();

    $Page->title->add_object ($folder);
    $Page->title->add_object ($component);
    $Page->title->subject = $panel->num_objects() . ' ' . $panel->raw_title ();
    
    $Page->location->add_folder_link ($folder, 'panel=' . $panel_manager->selected_panel_id);
    $Page->location->add_object_text ($component);
    
    $Page->start_display ();
?>
<div class="top-box">
<?php
    $box = $Page->make_box_renderer ();
    $box->start_column_set ();

    /** @var $renderer OBJECT_RENDERER */
    $renderer = $component->handler_for (Handler_html_renderer);
    $options = $renderer->options ();
    $options->show_as_summary = true;
    $options->show_users = false;

    $text = $renderer->display_to_string ($component);

    if ($text)
    {
      $box->new_column_of_type ('description-box');

      echo $text;
    }

    $box->new_column_of_type ('contents-box');

    echo '<h4>Contents</h4>';
    echo '<div class="panels">';

    $panel_manager->display ();

    echo '</div>';

    $box->new_column_of_type ('tools-box');

    echo '<h4>Tools</h4>';

    echo '<div class="button-content">';
    /** @var $renderer MENU_RENDERER */
    $renderer = $App->make_menu_renderer ();
    $renderer->set_size(Menu_size_compact);
    /** @var $commands COMMANDS */
    $commands = $component->handler_for(Handler_commands);
    $renderer->display ($commands);
    echo '</div>';

    $box->finish_column_set ();
?>
</div>
<div class="main-box">
  <div class="menu-bar-top">
    <?php
    if ($panel->uses_time_selector)
    {
      $panel_manager->display_time_menu ();
    }
    $pager = $panel->get_pager();

    if ($pager)
    {
      $pager->pages_to_show = 0;
      $pager->display();
    }

    $grid = $panel->get_grid();
    if ($grid)
    {
      $grid->show_pager = false;
    }

    /** @var MENU_RENDERER $renderer */
    $renderer = $component->handler_for (Handler_menu);
    $renderer->set_size(Menu_size_standard);
    $renderer->num_important_commands = 2;
    /** @var COMMANDS $commands */
    $commands = $component->handler_for(Handler_commands);
    $renderer->display($commands);
    ?>
  </div>
  <?php
  $panel->display ();

  if ($panel->num_objects ())
  {
    // don't show the bottom selector if there are no objects
    ?>
    <div class="menu-bar-bottom">
      <?php
      if ($panel->uses_time_selector)
      {
        $panel_manager->display_time_menu ();
      }
      if ($pager)
      {
        $pager->pages_to_show = 5;
        $pager->display(true);
      }
      ?>
    </div>
  <?php
  }
  ?>
</div>
<?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to view components in this folder.', $folder);
  }
?>