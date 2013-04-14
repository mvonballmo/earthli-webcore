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

  if ($App->login->is_allowed (Privilege_set_global, Privilege_configure))
  {
    $class_name = $App->final_class_name ('PUBLISHER_TASK', 'webcore/mail/publisher_task.php');
    $task = new $class_name ($App);
    $task->entry_filter = $App->mail_options->entry_publication_filter;
    $task->comment_filter = $App->mail_options->comment_publication_filter;

    include_once ($App->page_template_for ('webcore/pages/execute_task.php'));
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to publish notifications for this application.');
  }