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

  $Page->title->subject = 'Browse for group';
  
  if ($App->login->is_allowed (Privilege_set_group, Privilege_view))
  {
    $folder_query = $App->login->folder_query ();
    $folder = $folder_query->object_at_id (read_var ('id'));
    if ($folder)
    {
      $Page->template_options->header_visible = false;
      $Page->template_options->footer_visible = false;
      $Page->add_script_file ('{scripts}webcore_forms.js');
      $Page->start_display ();
    ?>
    <div class="box">
      <div class="box-title">
        Browse for group
      </div>
      <?php 
        if ($App->login->is_allowed (Privilege_set_group, Privilege_create)) 
        {
          $menu = $App->make_menu ();
          $menu->append ('Create Group', 'create_group.php', '{icons}buttons/create');
          $menu->display_as_toolbar (); 
        }
      ?>
      <div class="box-body">
        <p class="notes">Groups that already have permissions for <?php echo $folder->title_as_link (); ?> 
          are <em>not</em> displayed.</p>
      <?php
        $group_query = $App->group_query ();

        /* Show only groups that do not have permission in this folder. */
  
        $security = $folder->security_definition ();
        $permissions = $security->group_permissions ();
  
        $ids = array ();
        foreach ($permissions as $permission)
        {
          $ids [] = $permission->ref_id;      
        }
        if (sizeof ($ids))
        {
          $group_query->restrict_by_op ('grp.id', $ids, Operator_not_in);
        }
  
        $class_name = $App->final_class_name ('GROUP_BROWSER_GRID', 'webcore/gui/group_browser_grid.php');
        $grid = new $class_name ($App);
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
    {
      $Page->raise_security_violation ('You are not allowed to edit this folder\'s permissions.', $folder);
    }
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to view groups.', $folder);
  }
?>