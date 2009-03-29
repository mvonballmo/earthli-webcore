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

require_once ('webcore/db/migrator_task.php');

class UPGRADE_PER_APP_23_24_TASK extends MIGRATOR_TASK
{
  function _create_actions ($obj_type, $table_name)
  {
    global $Page;

    while ($Page->database->next_record ())
    {
      $id = $Page->database->f ('id');
      if (! $id)
      {
        $id = $Page->database->f ('entry_id');
      }

      switch ($obj_type)
      {
      case Action_user:
      case Action_group:
      case Action_folder:
        $access_id = $Page->database->f ('id');
        break;
      default:
        $access_id = $Page->database->f ('folder_id');
      }


      $objs [$id] = array ($Page->database->f ('time_created'),
                           $Page->database->f ('creator_id'),
                           $Page->database->f ('time_modified'),
                           $Page->database->f ('modifier_id'),
                           $Page->database->f ('publication_state'),
                           $access_id);
    }


    log_message ("Building history for [" . sizeof ($objs) . "] {$obj_type}s...", Msg_type_info, Msg_channel_migrate);

    foreach ($objs as $id => $obj)
    {
      $creator_id = $obj [1];
      $time_created = $obj [0];
      $access_id = $obj [5];

      $pub = Action_sent;

      $this->_query ("INSERT INTO $table_name (object_id, object_type, user_id, access_id, kind, time_created, publication_state, title)" .
                     " VALUES($id, '$obj_type', $creator_id, $access_id, 'Created', '$time_created', '$pub', 'Created')");

      $modifier_id = $obj [3];
      $time_modified = $obj [2];

      if ($time_modified != $time_created)
      {
        $this->_query ("INSERT INTO $table_name (object_id, object_type, user_id, access_id, kind, time_created, publication_state, title)" .
                       " VALUES($id, '$obj_type', $modifier_id, $access_id, 'Updated', '$time_modified', '$pub', 'Updated')");
      }
    }
  }

  function clean_text ($table_name)
  {
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&gt;', '>'), description = REPLACE(description, '&gt;', '>');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, ' &lt; ', ' < '), description = REPLACE(description, ' &lt; ', ' < ');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&lt;', '<'), description = REPLACE(description, '&lt;', '<<');");

    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&amp;', '&'), description = REPLACE(description, '&amp;', '&');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&quot;', '\"'), description = REPLACE(description, '&quot;', '\"');");

    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&uuml;', 'ü'), description = REPLACE(description, '&uuml;', 'ü');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&Uuml;', 'Ü'), description = REPLACE(description, '&Uuml;', 'Ü');");

    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&ouml;', 'ö'), description = REPLACE(description, '&ouml;', 'ö');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&Ouml;', 'Ö'), description = REPLACE(description, '&Ouml;', 'Ö');");

    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&auml;', 'ä'), description = REPLACE(description, '&auml;', 'ä');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&Auml;', 'Ä'), description = REPLACE(description, '&Auml;', 'Ä');");

    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&egrave;', 'é'), description = REPLACE(description, '&egrave;', 'é');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&Egrave;', 'É'), description = REPLACE(description, '&Egrave;', 'É');");

    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&eacute;', 'è'), description = REPLACE(description, '&eacute;', 'è');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&Eacute;', 'È'), description = REPLACE(description, '&Eacute;', 'È');");

    $this->_query ("UPDATE $table_name SET title = REPLACE(title, '&deg;', '°'), description = REPLACE(description, '&deg;', '°');");

    $this->_query ("UPDATE $table_name SET title = REPLACE(title, 'Ã¤', 'ä'), description = REPLACE(description, 'Ã¤', 'ä');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, 'Ã„', 'Ä'), description = REPLACE(description, 'Ã„', 'Ä');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, 'Ã¼', 'ü'), description = REPLACE(description, 'Ã¼', 'ü');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, 'Ãœ', 'Ü'), description = REPLACE(description, 'Ãœ', 'Ü');");
    $this->_query ("UPDATE $table_name SET title = REPLACE(title, 'Ã¶', 'ö'), description = REPLACE(description, 'Ã¶', 'ö');");

    // deliberate hack here because there is one entry with this combination that should obviously translate to Ü
    // and it's in the title. The other two are in the description and appear to map to Ä

    $this->_query ("UPDATE $table_name SET title = REPLACE(title, 'Ã?', 'Ü'), description = REPLACE(description, 'Ã?', 'Ä');");
  }
}

?>