<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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

  $icon_query = $App->icon_query ();
  /** @var ICON $icon */
  $icon = $icon_query->object_at_id (read_var ('id'));

  if (isset ($icon) && $App->login->is_allowed (Privilege_set_global, Privilege_resources))
  {
    $class_name = $App->final_class_name ('DELETE_FORM', 'webcore/forms/delete_form.php');
    /** @var DELETE_FORM $form */
    $form = new $class_name ($App);

    $form->process_existing ($icon);
    if ($form->committed ())
    {
      $Env->redirect_local ("view_icons.php");
    }

    $Page->title->add_object ($icon);
    $Page->title->subject = 'Delete icon';
    $Page->location->add_root_link();
    $Page->location->append('Icons', 'view_icons.php');
    $Page->location->add_object_link ($icon);
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/delete');

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
    $Page->raise_security_violation ('You are not allowed to delete this icon.', $icon);
  }
?>