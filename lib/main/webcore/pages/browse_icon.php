<?php

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

  $last_page = read_var ('last_page');

  $Page->title->subject = 'Browse for icon';
  $Page->template_options->header_visible = false;
  $Page->template_options->footer_visible = false;
  $Page->add_script_file ('{scripts}webcore_forms.js');
  $Page->start_display ();
?>
  <div class="box">
    <div class="box-title">
      Browse for icon
    </div>
    <?php
      if ($App->login->is_allowed (Privilege_set_global, Privilege_resources))
      {
        $menu = $App->make_menu ();
        $menu->renderer->content_mode = Menu_show_all_as_buttons;
        $menu->renderer->alignment = Menu_align_right;
        $menu->append ('Create icon', 'create_icon.php', '{icons}buttons/create');
        $menu->display_as_toolbar ();
      }
    ?>
    <div class="box-body">
    <?php
      $icon_query = $App->icon_query ();

      $class_name = $App->final_class_name ('ICON_GRID', 'webcore/gui/icon_grid.php');
      $grid = new $class_name ($App);
      $grid->is_chooser = true;
      $grid->set_ranges (4, 4);
      $grid->set_query ($icon_query);
      $grid->display ();
    ?>
    </div>
  </div>
<?php
  $Page->finish_display ();
?>