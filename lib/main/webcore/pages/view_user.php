<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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

  $name = read_var ('name');

  if ($name)
  {
    $user_query = $App->user_query ();
    $user = $user_query->object_at_name ($name);
  }
  
  if (isset ($user) && $App->login->is_allowed (Privilege_set_user, Privilege_view, $user))
  {
    $App->set_referer ();
    $App->set_search_text (read_var ('search_text'));

    $class_name = $App->final_class_name ('USER_PANEL_MANAGER', 'webcore/gui/panel.php');
    /** @var $panel_manager USER_PANEL_MANAGER */
    $panel_manager = new $class_name ($user);
    $panel = $panel_manager->selected_panel ();

    $Page->title->add_object ($user);
    $num_objects = $panel->num_objects();
    if ($num_objects)
    {
      $Page->title->subject = $num_objects . ' ' . $panel->raw_title();
    }
    else
    {
      $Page->title->subject = $panel->raw_title();
    }

    $Page->newsfeed_options->title->group = $App->title;
    $Page->newsfeed_options->title->add_object ($user);
    $Page->newsfeed_options->file_name = '{app}/view_user_rss.php?name=' . $user->title;

    $Page->location->add_root_link ();
    $Page->location->append ("Users", "view_users.php");
    $Page->location->add_object_text ($user);
    $Page->location->append($Page->title->subject);

    $Page->start_display ();
?>
    <div class="top-box">
      <div class="columns text-flow">
        <div class="grid-content">
          <?php
          $renderer = $user->handler_for (Handler_html_renderer);
          $options = $renderer->options ();
          $options->show_as_summary = true;
          $options->show_users = false;
          $renderer->display ($user);
          ?>
        </div>
        <div>
          <h4>Contents</h4>
          <div class="panels">
            <?php $panel_manager->display (); ?>
          </div>
        </div>
      </div>
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
        $renderer = $user->handler_for (Handler_menu);
        $renderer->set_size(Menu_size_standard);
        $renderer->num_important_commands = 2;
        /** @var COMMANDS $commands */
        $commands = $user->handler_for(Handler_commands);
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
    if (isset($user))
    {
      $Page->raise_security_violation ('You are not allowed to see this user.', $user);
    }
    else 
    {
      $Page->raise_error ('Please specify a user.', 'Invalid parameters');
    }
  }
?>