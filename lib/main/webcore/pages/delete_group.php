<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
  $group = $group_query->object_at_id (read_var ('id'));

  if (isset ($group) && $App->login->is_allowed (Privilege_set_group, Privilege_delete))
  {
    $class_name = $App->final_class_name ('PURGE_OBJECT_FORM', 'webcore/forms/purge_form.php', 'group');
    $form = new $class_name ($App);

    $form->process_existing ($group);
    if ($form->committed ())
    {
      if ($App->login->is_allowed (Privilege_set_group, Privilege_hidden) && ! $form->value_for ('purge'))
      {
        $App->return_to_referer ($group->home_page ());
      }
      else
      {
        $Env->redirect_local ("view_groups.php");
      }
    }

    $Page->title->add_object ($group);
    $Page->title->subject = 'Delete group';
    $Page->location->add_object_link ($group);
    $Page->location->append ($Page->title->subject);

    $Page->start_display ();
?>
<div class="box">
  <div class="box-title">
    <?php echo $App->title_bar_icon ('{icons}buttons/delete'); ?> Delete <?php echo $group->title_as_html (); ?>?
  </div>
  <div class="box-body">
  <?php
    $form->display ();
  ?>
  </div>
</div>
<?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to delete this group.', $group);
  }
?>