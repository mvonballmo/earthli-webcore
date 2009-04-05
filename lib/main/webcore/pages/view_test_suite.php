<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

  require_once ('webcore/tests/init.php');

  if ($App->login->is_allowed (Privilege_set_global, Privilege_configure))
  {
    $class_name = $App->final_class_name ('TEST_SUITE', 'webcore/util/test_suite.php');
    $test_suite = new $class_name ($App);
    
    $class_name = $App->final_class_name ('EXECUTE_TEST_SUITE_FORM', 'webcore/forms/execute_test_suite_form.php');
    $form = new $class_name ($App);
    
    $form->process_existing ($test_suite);
    if ($form->committed ())
    {
      $Env->redirect_local ('run_test.php?test_name=' . $form->value_for ('test_name'));
    }
  
    $Page->title->subject = 'Run Test Suite';
    
    $Page->location->add_root_link ();
    $Page->location->append ('Configure', 'configure.php');
    $Page->location->append ($Page->title->subject);
  
    $Page->start_display ();
  ?>
    <div class="box">
      <div class="box-title">
        <?php echo $App->title_bar_icon ('{icons}buttons/test'); ?> <?php echo $Page->title->subject; ?>
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
    $Page->raise_security_violation ('You are not allowed to test this application.');
  }
?>