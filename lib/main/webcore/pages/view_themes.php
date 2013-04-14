<?php

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

  $Page->title->subject = 'Themes';

  $App->set_referer ();

  $Page->location->add_root_link ();
  $Page->location->append ($Page->title->subject);

  $Page->start_display ();
?>
<div class="top-box">
  <div class="button-content">
  <?php
  if ($App->login->is_allowed (Privilege_set_global, Privilege_resources))
  {
    $menu = $App->make_menu ();
    $menu->renderer->content_mode = Menu_show_all_as_buttons;
    $menu->renderer->alignment = Menu_align_inline;
    $menu->append ('Create theme', 'create_theme.php', '{icons}/buttons/create');
    $menu->display ();
  }
  ?>
  </div>
</div>
  <div class="box">
    <div class="box-body">
    <?php
      $theme_query = $App->theme_query ();

      $class_name = $App->final_class_name ('THEME_GRID', 'webcore/gui/theme_grid.php');
      /** @var $grid THEME_GRID */
      $grid = new $class_name ($App);
      $grid->set_ranges (3, 3);
      $grid->set_query ($theme_query);
      $grid->display ();
    ?>
    </div>
  </div>
<?php
  $Page->finish_display ();
?>