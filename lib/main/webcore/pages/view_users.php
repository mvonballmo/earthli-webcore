<?php

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

  if ($App->login->is_allowed (Privilege_set_user, Privilege_view))
  {
    $App->set_referer ();

    $user_query = $App->user_query ();

    $show_anon = read_var ('show_anon');
    if ($show_anon)
    {
      $user_query->set_kind (Privilege_kind_anonymous);
    }
    else
    {
      $user_query->set_kind (Privilege_kind_registered);
    }

    $caption = $show_anon ? ' Anonymous Users' : ' Registered Users';

    $Page->title->subject = $user_query->size () . $caption;

    $Page->location->add_root_link ();
    $Page->location->append ($Page->title->subject);

    $Page->start_display ();
  ?>
  <div class="top-box">
    <?php
    $box = $Page->make_box_renderer ();
    $box->start_column_set ();
    $box->new_column_of_type ('description-box');
    ?>
    <p>This page lists all of the registered users for <?php echo $App->title; ?>.</p>
    <?php
    $box->new_column_of_type ('tools-box');

    echo '<h4>Search</h4>';
    echo '<div class="form-content">';

    $class_name = $App->final_class_name ('EXECUTE_SEARCH_FORM', 'webcore/forms/execute_search_form.php');
    $search = null;
    /** @var $form EXECUTE_SEARCH_FORM */
    $form = new $class_name ($App, $search);
    $form->load_with_defaults ();
    $form->set_value ('type', 'user');
    $form->display ();

    echo '</div>';

    $box->finish_column_set ();
    ?>
  </div>
  <div class="main-box">
    <div class="menu-bar-top">
      <?php
      $class_name = $Page->final_class_name ('USER_GRID', 'webcore/gui/user_grid.php');
      /** @var $grid USER_GRID */
      $grid = new $class_name ($App);
      $grid->set_ranges (10, 3);
      $grid->set_query ($user_query);

      $pager = $grid->get_pager();

      if ($pager)
      {
        $pager->pages_to_show = 0;
        $pager->display();
      }

      $grid->show_pager = false;

      /** @var MENU_RENDERER $renderer */
      $renderer = $App->make_menu_renderer ();
      $renderer->set_size(Menu_size_standard);
      $renderer->num_important_commands = 2;
      $class_name = $App->final_class_name ('USER_MANAGEMENT_COMMANDS', 'webcore/cmd/user_management_commands.php');
      /** @var COMMANDS $commands */
      $commands = new $class_name ($App);
      $renderer->display($commands);
      ?>
    </div>
    <div class="grid-content">
      <?php
      $grid->display ();
      ?>
    </div>
    <?php

    if ($pager && $pager->num_items() > 0)
    {
      // don't show the bottom selector if there are no objects
      ?>
      <div class="menu-bar-bottom">
        <?php
          $pager->pages_to_show = 5;
          $pager->display(true);
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
    $Page->raise_security_violation ('You are not allowed to see users.');
  }
?>