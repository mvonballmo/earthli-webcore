<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

  include ('projects/init.php');

  $Env->set_buffered (FALSE);
  set_time_limit (600);
  $Page->start_display ();

  $Page->database->query ("SELECT entry.id, chng.kind, chng.release_id FROM project_objects obj INNER JOIN project_changes chng ON obj.id = chng.object_id");

  $objs = array ();
  while ($Page->database->next_record ())
  {
    $obj->kind = $Page->database->f ('kind');
    $obj->release_id = $Page->database->f ('release_id');
    $objs [$Page->database->f ('id')] = $obj;
  }

  echo "<p>Updating [" . sizeof ($objs) . "] changes</p>";
  foreach ($objs as $id => $obj)
  {
    echo "Updating object at [$id] to kind=[$obj->kind] and release_id=[$obj->release_id]<br>";
    $Page->database->query ("UPDATE project_objects set kind = $obj->kind, release_id = $obj->release_id WHERE id = $id");
  }

  $Page->database->query ("SELECT obj.id, job.kind, job.release_id FROM project_objects obj INNER JOIN project_jobs job ON obj.id = job.object_id");

  $objs = array ();
  while ($Page->database->next_record ())
  {
    $obj->kind = $Page->database->f ('kind');
    $obj->release_id = $Page->database->f ('release_id');
    $objs [$Page->database->f ('id')] = $obj;
  }

  echo "<p>Updating [" . sizeof ($objs) . "] jobs</p>";
  foreach ($objs as $id => $obj)
  {
    echo "Updating object at [$id] to kind=[$obj->kind] and release_id=[$obj->release_id]<br>";
    $Page->database->query ("UPDATE project_objects set kind = $obj->kind, release_id = $obj->release_id WHERE id = $id");
  }

  echo "<p>Dropping release id from projects table</p>";
  $Page->database->query ("ALTER TABLE project_folders DROP release_id");

  echo "<p>Adding options to projects table</p>";
  $Page->database->query ("ALTER TABLE project_folders ADD options_id INT UNSIGNED DEFAULT '1' NOT NULL");

  echo "<p>Creating new options table</p>";
  $Page->database->query ("CREATE TABLE project_options (" .
                          " folder_id int(10) unsigned NOT NULL default '0'," .
                          " owner_group_type tinyint(4) NOT NULL default '0'," .
                          " owner_group_id int(10) unsigned NOT NULL default '0'," .
                          " KEY folder_id (folder_id)" .
                          " ) TYPE=MyISAM;");

  echo "<p>Creating default project options</p>";
  $Page->database->query ("INSERT INTO project_options VALUES(1, 1, 0)");

  echo "<p>Encrypting passwords</p>";
  $Page->database->query ("UPDATE users SET password = MD5(password)");

  echo "<p>Changing folder permissions table properties</p>";
  $Page->database->query ("ALTER TABLE project_folder_permissions ADD importance TINYINT UNSIGNED DEFAULT '0' NOT NULL");

  echo "<p>Changing group table properties</p>";
  $Page->database->query ("ALTER TABLE groups CHANGE name title VARCHAR( 100 ) NOT NULL");

  echo "<p>Changing user table properties</p>";
  $Page->database->query ("ALTER TABLE users CHANGE name title VARCHAR( 50 ) NOT NULL");

  echo "<p>Creating new subscribers table</p>";
  $Page->database->query ("CREATE TABLE project_subscribers (" .
                          " id int(10) unsigned NOT NULL auto_increment," .
                          " email varchar(250) NOT NULL default ''," .
                          " send_as_html tinyint(4) NOT NULL default '1'," .
                          " send_as_newsletter tinyint(4) NOT NULL default '0'," .
                          " send_own_changes tinyint(4) NOT NULL default '1'," .
                          " PRIMARY KEY  (id) INDEX (email)" .
                          " ) TYPE=MyISAM;");

  echo "<p>Building subscriber table</p>";
  $Page->database->query ("SELECT DISTINCT email FROM project_subscriptions");
  while ($Page->database->next_record ())
    $emails [] = $Page->database->f ('email');

  foreach ($emails as $email)
    $Page->database->query ("INSERT INTO project_subscribers (email, send_as_html) VALUES ('$email', 0)");

  echo "<p>Adding foreign key to subscriptions</p>";
  $Page->database->query ("ALTER TABLE `project_subscriptions` ADD `subscriber_id` INT UNSIGNED NOT NULL FIRST");

  echo "<p>Connecting subscriptions to subscribers</p>";
  $Page->database->query ("SELECT id, email FROM project_subscribers");
  $emails = array ();
  while ($Page->database->next_record ())
    $emails [$Page->database->f ('id')] = $Page->database->f ('email');

  foreach ($emails as $id => $email)
    $Page->database->query ("UPDATE project_subscriptions SET subscriber_id = $id WHERE email = '$email'");

  echo "<p>Removing email from subscriptions</p>";
  $Page->database->query ("ALTER TABLE `project_subscriptions` DROP `email`");
?>