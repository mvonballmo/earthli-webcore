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
    $class_name = $App->final_class_name ('APPLICATION_CONFIGURATION_INFO', 'webcore/obj/framework_info.php');
    $info = new $class_name ($App);
    
    if (read_var ('framework'))
    {
      $base_url = $Env->source_path ();
      $info_to_uprade = $info->lib_info;
    }
    else
    {
      $base_url = $App->source_path ();
      $info_to_uprade = $info->app_info;
    }
    
    if (! $info_to_uprade->exists () || $info_to_uprade->needs_upgrade ())
    {
      $version_tag = 'public $version_from = \'' . $info_to_uprade->database_version . '\';';

      $base_url->append ('tasks/db');
      $path = $base_url->as_text ();
      $files = file_list_for ($path);
      foreach ($files as $f)
      {
        $text = file_get_contents ($path . $f);
        if (strpos ($text, $version_tag) !== false)
        {
          $class_names = null;
          $Result = preg_match ('/class ([a-zA-Z_0-9]+) extends/', $text, $class_names);
          if (sizeof ($class_names) != 2)
          {
            log_message ("Could not find class name in [$f].", Msg_type_warning);
          }
          else
          {
            $task_class_name = $class_names [1];
            $task_file_name = $path . $f;
            break;
          } 
        }
      }
      
      if (empty ($task_class_name))
      {
        $error_message = 'Could not find an upgrade for <span class="field">' . $info_to_uprade->title . ' ' . $info_to_uprade->database_version . '</span>. Please contact support.'; 
      }
    }
    else
    {
      $error_message = $info_to_uprade->message ();
    }
    
    if (! empty ($task_class_name))
    {
      $class_name = $App->final_class_name ($task_class_name, $task_file_name);
      $task = new $task_class_name ($info_to_uprade);
      
      include_once ($App->page_template_for ('webcore/pages/execute_task.php'));
    }    
    else
    {
      $Page->title->subject = 'Upgrade';

      $Page->location->add_root_link ();
      $Page->location->append ('Configure', 'configure.php');
      $Page->location->append ($Page->title->subject);
  
      $Page->raise_error ($error_message);
    }    
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to upgrade this application.');
  }
?>