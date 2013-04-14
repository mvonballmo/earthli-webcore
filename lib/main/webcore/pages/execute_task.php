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

  /* Set these globals before using this template:
   *
   * $task: Task to execute.
  */

  if (isset ($App))
  {
    $context = $App;
  }
  else
  {
    $context = $Page;
  }

  $Page->title->subject = $task->title_as_text ();

  $Page->location->add_root_link ();
  $Page->location->append ('Configure', 'configure.php');
  $Page->location->append ($Page->title->subject, '', $task->icon);

  $Page->add_style_sheet ($Env->logger_style_sheet);
  $Page->start_display ();
?>
  <div class="box">
    <div class="box-body form-content">
      <?php
        $form = $task->form ();
        $form->process_existing ($task);
        if (! $form->committed ())
        {
          $form->display ();
        }
      ?>
    </div>
  </div>
<?php
  $Page->finish_display ();
?>