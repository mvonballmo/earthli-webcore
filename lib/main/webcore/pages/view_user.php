<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
    $panel_manager = new $class_name ($user);
    $panel = $panel_manager->selected_panel ();

    $Page->title->add_object ($user);
    $Page->title->subject = $panel->num_objects () . ' ' . $panel->raw_title();

    $Page->newsfeed_options->title->group = $App->title;
    $Page->newsfeed_options->title->add_object ($user);
    $Page->newsfeed_options->file_name = '{app}/view_user_rss.php?name=' . $user->title;

    $Page->location->add_root_link ();
    $Page->location->append ("Users", "view_users.php");
    $Page->location->add_object_text ($user);
    $Page->location->append($Page->title->subject);

    $Page->start_display ();
?>
  <div class="box">
      <div class="box-body">
        <div class="top-box">
          <?php
          $box = $Page->make_box_renderer ();
          $box->start_column_set ();
          $box->new_column_of_type ('description-box');

          $renderer = $user->handler_for (Handler_html_renderer);
          $options = $renderer->options ();
          $options->show_as_summary = true;
          $options->show_users = false;
          $renderer->display ($user);

          $box->new_column_of_type ('contents-box');

          echo '<h4>Contents</h4>';
          $panel_manager->display ();

          $box->new_column_of_type ('tools-box');
          echo '<h4>Tools</h4>';

          $subscription_status = $user->handler_for (Handler_subscriptions);
          $subscription_status->display ($user);

          $renderer = $user->handler_for (Handler_menu);
          $renderer->alignment = Menu_align_inline;
          $renderer->set_size(Menu_size_compact);
          $renderer->display_as_toolbar ($user->handler_for (Handler_commands));

          $box->finish_column_set ();
          ?>
        </div>
        <?php if ($panel->uses_time_selector) { ?>
          <div class="menu-bar-top">
            <?php $panel_manager->display_time_menu (); ?>
          </div>
        <?php } ?>
      <?php $panel->display (); ?>
      </div>
    <?php
      if ($panel->num_objects () && $panel->uses_time_selector)
      {
        // don't show the bottom selector if there are no objects
    ?>
      <div class="menu-bar-bottom">
        <?php $panel_manager->display_time_menu (); ?>
      </div>
    <?php
      }
    ?>
  </div>
<?php
    $box->finish_column_set ();
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