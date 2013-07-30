<?php

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli Webcore.

earthli Webcore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Webcore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Webcore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Webcore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

  $id = read_var ('id');
  /** @var $folder_query USER_PROJECT_QUERY */
  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->folder_for_release_at_id ($id);

  if (isset ($folder))
  {
    $rel_query = $folder->release_query ();
    /** @var $release RELEASE */
    $release = $rel_query->object_at_id ($id);

    include ('projects/forms/ship_release_form.php');
    $form = new SHIP_RELEASE_FORM ($App);

    $form->process_existing ($release);
    if ($form->committed ())
    {
      $App->return_to_referer ($release->home_page ());
    }

    $branch = $release->branch ();

    $Page->title->add_object ($folder);
    $Page->title->add_object ($branch);
    $Page->title->add_object ($release);
    $Page->title->subject = 'Ship';

    include_once ('projects/gui/project_panel.php');
    $panel_manager = new PROJECT_RELEASE_PANEL_MANAGER ($release);

    $Page->location->add_folder_link ($folder);
    $Page->location->add_object_link ($branch, '', $App->resolve_file('{app_icons}buttons/new_branch'));
    $Page->location->add_object_link ($release, '', $App->resolve_file('{app_icons}buttons/new_release'));
    $Page->location->append ($Page->title->subject, '', '{icons}/buttons/ship');

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
    $Page->raise_security_violation ('You are not allowed to view releases in this folder.', $folder);
  }
?>