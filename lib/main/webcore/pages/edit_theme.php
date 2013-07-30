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

  $theme_query = $App->theme_query ();
  /** @var THEME $theme */
  $theme = $theme_query->object_at_id (read_var ('id'));

  if (isset ($theme) && $App->login->is_allowed (Privilege_set_global, Privilege_resources))
  {
    $class_name = $App->final_class_name ('THEME_FORM', 'webcore/forms/theme_form.php');
    /** @var THEME_FORM $form */
    $form = new $class_name ($App);

    $form->process_existing ($theme);
    if ($form->committed ())
    {
      $App->return_to_referer ('view_themes.php');
    }

    $Page->title->add_object ($theme);
    $Page->title->subject = 'Edit theme';

    $Page->location->add_root_link ();
    $Page->location->append ('Themes', 'view_themes.php');
    $Page->location->add_object_link ($theme);
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/edit');

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
    $Page->raise_security_violation ('You are not allowed to edit this theme.', $theme);
  }
?>