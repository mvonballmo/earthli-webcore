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

  require_once ('webcore/tests/init.php');

  if (! isset($App) || $App->login->is_allowed (Privilege_set_global, Privilege_configure))
  {
    $class_name = $App->final_class_name ('TEST_SUITE', 'webcore/util/test_suite.php');
    $test_suite = new $class_name ($App);

    $task_class_name = read_var ('test_name');
    if ($task_class_name)
    {
      $task = $test_suite->test_task_at_name ($task_class_name);
    }

    if (isset ($task))
    {
      include_once ($App->page_template_for ('webcore/pages/execute_task.php'));
    }
    else
    {
      $Page->raise_error ('Please provide a test name.', 'Run Test');
    }
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to run tests.');
  }
?>