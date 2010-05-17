<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

  $id = read_var ('id');
  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->folder_for_release_at_id ($id);

  if (isset ($folder))
  {
    $rel_query = $folder->release_query ();
    $rel = $rel_query->object_at_id ($id);
  }

  if (isset ($rel) && $App->login->is_allowed (Privilege_set_release, Privilege_view, $rel))
  {
    $class_name = $App->final_class_name ('SEND_OBJECT_IN_FOLDER_FORM', 'webcore/forms/send_object_in_folder_form.php', 'release');
    $form = new $class_name ($App);

    $form->process_existing ($rel);
    if ($form->committed ())
    {
      $Env->redirect_local ($rel->home_page ());
    }

    $Page->title->add_object ($folder);
    $Page->title->add_object ($rel);
    $Page->title->subject = 'Send Release';

    $Page->location->add_folder_link ($folder);
    $Page->location->add_object_link ($rel);
    $Page->location->append ("Send");

    $Page->start_display ();
?>
<div class="box">
  <div class="box-title">
    <?php echo $App->title_bar_icon ('{icons}buttons/send'); ?> <?php echo $Page->title->subject; ?>
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
    $Page->raise_security_violation ('You are not allowed to view this release.', $folder);
  }
?>