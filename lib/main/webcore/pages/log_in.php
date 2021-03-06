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

  $class_name = $App->final_class_name ('LOG_IN_FORM', 'webcore/forms/log_in_form.php');
  /** @var LOG_IN_FORM $form */
  $form = new $class_name ($App);

  $Page->title->subject = 'Log in';

  $form->process_plain ();
  if ($form->committed ())
  {
    $App->return_to_referer ('index.php');
  }
  else
  {
    if ($form->submitted ())
    {
      $Page->title->subject = 'Login Failed';
    }
  }

  $Page->location->add_root_link ();
  $Page->location->append ($App->resolve_icon_as_html ('{icons}buttons/login', Sixteen_px, ' ') . ' ' . $Page->title->subject);

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
?>