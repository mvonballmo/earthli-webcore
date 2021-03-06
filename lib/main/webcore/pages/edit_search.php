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

  $search_query = $App->login->search_query ();
  /** @var $search SEARCH */
  $search = $search_query->object_at_id (read_var ('id'));

  if (isset ($search))
  {
    $class_name = $App->final_class_name ('STORE_SEARCH_FORM', 'webcore/forms/store_search_form.php');
    /** @var $form STORE_SEARCH_FORM */
    $form = new $class_name ($App, $search->fields);

    $form->process_existing ($search);
    if ($form->committed ())
    {
      $Env->redirect_local ($search->home_page ());
    }

    $Page->title->add_object ($search);
    $Page->title->subject = "Edit";

    $Page->location->add_root_link ();
    $Page->location->append ('Searches', 'view_searches.php');
    $Page->location->add_object_link ($search);
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
    $Page->raise_security_violation ('You are not allowed to view this search.');
  }
?>