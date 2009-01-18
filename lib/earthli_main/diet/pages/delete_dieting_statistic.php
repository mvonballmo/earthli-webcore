<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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

  require_once ('diet/db/dieting_statistic_query.php');
  $query = new DIETING_STATISTIC_QUERY ($Page);

  $stat =& $query->object_at_id (read_var ('id'));

  if (isset ($stat))
  {
    $class_name = $Page->final_class_name ('DELETE_FORM', 'webcore/forms/delete_form.php');
    $form = new $class_name ($Page);

    $form->process_existing ($stat);
    if ($form->committed ())
      $Env->redirect_local ("index.php");

    $Page->title->add_object ($stat);
    $Page->title->subject = 'Delete dieting statistic';
    $Page->location->add_object_link ($stat);
    $Page->location->append ($Page->title->subject);

    $Page->start_display ();
?>
<div class="box">
  <div class="box-title">
    <?php echo $Page->title_bar_icon ('{icons}buttons/delete'); ?> Delete <?php echo $stat->title_as_html (); ?>?
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
    $Page->redirect_security_violations = FALSE;
    $Page->raise_security_violation ('You are not allowed to delete this dieting statistic.', $stat);
  }
?>