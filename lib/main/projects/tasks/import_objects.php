<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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

  require_once ('projects/init.php');

  // Set up the page

  if ($Env->is_http_server ())
  {
    $Page->theme->font_name = 'courier';
    $Page->theme->font_size = 'small';
    $Page->title->subject = 'Import Changes';

    $Page->add_style_sheet ($Env->logger_style_sheet);
    $Page->start_display ();
    echo "<div class=\"chart\"><div class=\"chart-title\">Importing VCS Changes</div><div class=\"log-box\">";
  }

  // replace the default logger with one that writes to the page instead of a separate popup

  define ('Msg_channel_xml', 'XML');

  include_once ('webcore/log/echo_logger.php');
  $color_logger = new ECHO_LOGGER ($Env);
  $Logger->copy_settings_to ($color_logger);
  $Logger = $color_logger;

  $Logger->set_channel_enabled (Msg_channel_xml, Msg_type_all);

  include_once ('webcore/log/file_logger.php');
  $file_logger = new FILE_LOGGER ();
  $file_logger->set_file_name ($App->xml_options->log_file_name);
  $Logger->add_logger ($file_logger);

  // define XML event handlers

  function raise_xml ($msg, $type = Msg_type_error)
  {
    global $xml_parser;
    global $errors_occurred;
    $num = xml_get_current_line_number ($xml_parser);
    log_message ("$msg [line $num]", $type, Msg_channel_xml);
    if ($type == Msg_type_error)
    {
      $errors_occurred += 1;
    }
  }

  function raise_if_not_in_object ($tag_name)
  {
    global $obj;
    if (! $obj)
    {
      raise_xml ("[$tag_name] cannot occur outside of an object.");
    }
  }

  function start_xml_element ($parser, $name, $attrs)
  {
    global $App;
    global $obj;
    global $kinds;
    global $folders;

    switch ($name)
    {
    case 'CHANGE':
      include_once ('projects/obj/change.php');
      $obj = new CHANGE ($App);

      $obj->title = $attrs ['TITLE'];

      $kind = $kinds [strtolower ($attrs ['KIND'])];
      if (! $kind)
      {
        raise_xml ("[{$attrs ['KIND']}] is not a valid change type.");
      }
      $obj->kind = $kind->value;

      $t = $App->make_date_time ($attrs ['TIME'], Date_time_iso);
      if (! $t->is_valid ())
      {
        raise_xml ("[{$attrs ['TIME']}] is not a valid date/time.");
      }
      else
      {
        $obj->time_created = $t;
        $obj->time_modified = $t;
      }

      break;

    case 'CREATOR':
      raise_if_not_in_object ($name);
      $orig_name = $attrs ['TITLE'];
      $user_name = $orig_name;
      $at = strpos ($user_name, '@');
      if ($at !== false)
      {
        $user_name = substr ($user_name, 0, $at);
        raise_xml ("Truncated name from [$orig_name] to [$user_name]", Msg_type_warning);
      }
      if (! $user_name)
      {
        raise_xml ("Creator title cannot be empty.");
      }
      $user_query = $App->user_query ();
      $user = $user_query->object_at_name ($user_name);
      if (! $user)
      {
        raise_xml ("User [$user_name] does not exist.");
      }
      $obj->creator_id = $user->id;
      $obj->modifier_id = $user->id;
      break;

    case 'FOLDER':
      raise_if_not_in_object ($name);
      $id = $attrs ['ID'];
      $folder = $folders [$id];
      if (! $folder)
      {
        raise_xml ("Folder [$id] does not exist.");
      }
      $obj->set_parent_folder ($folder);
      break;

    case 'DESCRIPTION':
      raise_if_not_in_object ($name);
      break;

    case 'FILES':
      raise_if_not_in_object ($name);
      break;
    }
  }

  function finish_xml_element ($parser, $name)
  {
    global $objs;
    global $obj;
    global $text_buffer;

    switch ($name)
    {
    case 'CHANGE':
      // change is complete. Render it for now.
      if (! $obj->_folder)
      {
        $error_occurred = true;
        raise_xml ("Folder is not set.");
      }

      if (! $obj->title && ! $obj->description)
      {
        $error_occurred = true;
        raise_xml ("Content cannot be empty (must have a title or a description or both).");
      }

      if (! $obj->modifier_id || ! $obj->creator_id)
      {
        $error_occurred = true;
        raise_xml ("Creator is not set.");
      }

      if (! isset ($error_occurred))
      {
        $objs [] = $obj;
      }

      unset ($obj);
      break;

    case 'DESCRIPTION':
      $obj->description = trim ($text_buffer);
      $text_buffer = '';
      break;

    case 'FILES':
      $obj->files = trim ($text_buffer);
      $text_buffer = '';
      break;
    }
  }

  function process_char_data ($parser, $data)
  {
    global $text_buffer;
    $text_buffer .= $data;
  }

  // Create the XML parser

  $xml_parser = xml_parser_create();
  // use case-folding so we are sure to find the tag in $map_array
  xml_parser_set_option ($xml_parser, XML_OPTION_CASE_FOLDING, true);

  xml_set_element_handler ($xml_parser, "start_xml_element", "finish_xml_element");
  xml_set_character_data_handler ($xml_parser, "process_char_data");

  // If the file opened, then process the XML inside, otherwise, issue a warning
  // and close the log

  $fn = $App->xml_options->import_file_name;
  $fhandle = @fopen ($fn, 'r');

  if (! $fhandle)
  {
    log_message ("File [$fn] was not found. No objects imported.", Msg_type_warning, Msg_channel_xml);
    $Logger->close ();
  }
  else
  {
    $indexed_kinds = $App->entry_kinds ();
    foreach ($indexed_kinds as $kind)
    {
      $kinds [strtolower ($kind->title)] = $kind;
    }

    $App->impersonate ($App->mail_options->publisher_user_name, $App->mail_options->publisher_user_password);
    $folder_query = $App->login->folder_query ();
    $folders = $folder_query->indexed_objects ();

    $App->display_options->show_local_times = false;

    while (($data = fread($fhandle, 4096)))
    {
      if (! xml_parse ($xml_parser, $data, feof($fhandle))) {
        raise ( sprintf ("XML error: %s at line %d",
                         xml_error_string (xml_get_error_code ($xml_parser)), xml_get_current_line_number ($xml_parser)));
        }
    }

    xml_parser_free($xml_parser);

    $commit = ! read_var ('testing');

    if (isset ($errors_occurred))
    {
      log_message ("[$errors_occurred] errors detected. Objects not imported.", Msg_type_warning, Msg_channel_xml);
    }
    else
    {
      if ($commit)
      {
        $action = 'Imported';
      }
      else
      {
        $action = 'Found';
      }

      $count = sizeof ($objs);
      log_message ("[$count] changes found. Importing...", Msg_type_info, Msg_channel_xml);
      $index = 0;
      while ($index < $count)
      {
        $obj = $objs [$index];
        $folder = $obj->parent_folder ();
        $creator = $obj->creator ();
        if ($commit)
        {
          // Now, get the main trunk,
          // create branch information from that,
          // adjust the applier id to be the submitted user (do so before setting main branch info because it makes a copy for cached info)
          // Set the main branch to use for the change (do not store or this will reset the user id to whichever user is logged in)
          // Store the branch information
          // Store the change again, to update the cached branch information

          $trunk = $obj->_folder->trunk ();

          $branch_info = $obj->new_branch_info ($trunk);
          $branch_info->applier_id = $obj->modifier_id;
          $branch_info->time_applied = $obj->time_created;
          $obj->add_branch_info ($branch_info);

          $obj->set_main_branch_info ($branch_info);

          $history_item = $obj->new_history_item ();
          $history_item->user_id = $obj->creator_id;
          $history_item->time_created = $obj->time_created;
          $obj->store_as_is_if_different ($history_item);

          $branch_info->entry_id = $obj->id;
          $branch_info->store ();
        }
        log_message ("$action [" . $obj->title_as_plain_text () . "] into [" . $folder->title_as_plain_text () . "] by [" . $creator->title_as_plain_text () . "] on [" . $obj->time_created->format () . "]", Msg_type_info, Msg_channel_xml);
        $index += 1;
      }

      // close the file and delete it

      fclose ($fhandle);
      if ($commit)
      {
        unlink ($fn);
      }
    }

    $Logger->close ();
  }

  // finish the page

  if ($Env->is_http_server ())
  {
    echo "</div></div></div>";
    $Page->finish_display ();
  }
?>