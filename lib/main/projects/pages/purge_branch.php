<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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
  /** @var $folder_query USER_PROJECT_QUERY */
  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->folder_for_branch_at_id ($id);

  if (isset ($folder))
  {
    $branch_query = $folder->branch_query ();
    $branch = $branch_query->object_at_id ($id);
  }

  if (isset ($branch) && $App->login->is_allowed (Privilege_set_branch, Privilege_purge, $branch))
  {
    $class_name = $App->final_class_name ('PURGE_BRANCH_FORM', 'projects/forms/purge_branch_form.php');
    /** @var $form PURGE_BRANCH_FORM */
    $form = new $class_name ($App);

    $form->process_existing ($branch);
    if ($form->committed ())
    {
      $Env->redirect_local ($folder->home_page ());
    }

    $Page->title->add_object ($folder);
    $Page->title->add_object ($branch);
    $Page->title->subject = 'Purge Branch';

    $Page->location->add_folder_link ($folder);
    $Page->location->add_object_link ($branch, '', $App->resolve_file('{app_icons}buttons/new_branch'));
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/purge');

    $Page->start_display ();
  ?>
  <div class="main-box">
    <div class="form-content">
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
    $Page->raise_security_violation ("You are not allowed to purge this branch.", $folder);
  }
?>