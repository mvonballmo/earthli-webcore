<?php

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

  if ($App->login->is_allowed (Privilege_set_group, Privilege_view))
  {
    $App->set_referer ();

    $Page->location->add_root_link ();
    $Page->location->append ('Groups');
    $Page->title->subject = 'Groups';

    $Page->start_display ();
  ?>
  <div class="box">
    <div class="box-title">
      Groups
    </div>
    <?php 
      if ($App->login->is_allowed (Privilege_set_group, Privilege_create)) 
      {
        $menu = $App->make_menu ();
        $menu->renderer->content_mode = Menu_show_all_as_buttons;
        $menu->renderer->alignment = Menu_align_right;
        $menu->append ('Create Group', 'create_group.php', '{icons}buttons/create');
        $menu->display_as_toolbar ();
      } 
    ?>
    <div class="box-body">
    <?php
      $group_query = $App->group_query ();

      include_once ('webcore/gui/group_grid.php');
      $grid = new GROUP_GRID ($App);
      $grid->set_ranges (25, 1);
      $grid->set_query ($group_query);
      $grid->display ();
    ?>
    </div>
  </div>
  <?php
    $Page->finish_display ();
  }
  else
    $Page->raise_security_violation ('You are not allowed to view groups.');
?>