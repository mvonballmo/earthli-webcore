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

$group_query = $App->group_query ();
/** @var $group GROUP */
$group = $group_query->object_at_id (read_var ('id'));

if (isset ($group))
{
  $App->set_referer ();

  $Page->title->add_object ($group);

  $Page->location->add_root_link ();
  $Page->location->append ('Groups', 'view_groups.php');
  $Page->location->add_object_text ($group);

  $Page->start_display ();
?>
<div class="top-box button-content">
<?php
  /** @var $renderer MENU_RENDERER */
  $renderer = $group->handler_for (Handler_menu);
  $renderer->display ($group->handler_for (Handler_commands));
?>
</div>
<div class="box">
  <div class="box-body">
<?php
  $renderer = $group->handler_for (Handler_html_renderer);
  $renderer->display ($group);

  $user_query = $group->user_query ();

  $class_name = $App->final_class_name ('GROUP_USER_GRID', 'webcore/gui/group_user_grid.php');
  /** @var $grid GROUP_USER_GRID */
  $grid = new $class_name ($group);
  $grid->show_folder = true;
  $grid->set_ranges (25, 1);
  $grid->set_query ($user_query);
  $grid->display ();
?>
  </div>
</div>
<?php
  $Page->finish_display ();
}
else
{
  $Page->raise_security_violation ('You are not allowed to see this group.', $group);
}
?>