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
  /** @var $folder_query USER_PROJECT_QUERY */
  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->folder_for_release_at_id ($id);

  if (isset ($folder))
  {
    $release_query = $folder->release_query ();
    $rel = $release_query->object_at_id ($id);
  }

  if (isset ($rel) && $App->login->is_allowed (Privilege_set_release, Privilege_modify, $rel))
  {
    $class_name = $App->final_class_name ('RELEASE_FORM', 'projects/forms/release_form.php');
    /** @var $form RELEASE_FORM */
    $form = new $class_name ($folder);

    $form->process_existing ($rel);
    if ($form->committed ())
    {
      $Env->redirect_local ($rel->home_page ());
    }

    $Page->title->add_object ($folder);
    $Page->title->add_object ($rel);
    $Page->title->subject = 'Edit Release ' . $rel->title_as_plain_text ();

    $Page->location->add_folder_link ($folder);
    $Page->location->add_object_link ($rel->branch ());
    $Page->location->add_object_link ($rel);
    $Page->location->append ('Edit', '', '{icons}buttons/edit');

    $Page->start_display ();
?>
    <div class="box">
      <div class="box-body form-content">
        <?php
          $form->button = "Update";
          $form->display ();
        ?>
      </div>
    </div>
<?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to edit this release.', $folder);
  }
?>