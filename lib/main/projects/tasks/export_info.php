<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

  function process_folders ($folders, $depth)
  {
    $depth++;

    global $fhandle;

    if (sizeof ($folders))
    {
      foreach ($folders as $folder)
      {
        // format it as a tree

        for ($i = 0; $i < $depth; $i++)
        {
          fwrite ($fhandle, '  ');
        }

        // export the folder

        $parent = $folder->parent_folder ();

        if ($parent)
        {
          fwrite ($fhandle, "<folder name=\"" . $folder->title_as_plain_text () . "\" parent=\"" . $parent->title_as_plain_text () . "\" id=\"$folder->id\"/>\n");
        }
        else
        {
          fwrite ($fhandle, "<folder name=\"" . $folder->title_as_plain_text () . "\" id=\"$folder->id\"/>\n");
        }

        echo "Exported [" . $folder->title_as_plain_text () . "]<br>";

        // export sub folders

        process_folders ($folder->sub_folders (), $depth);
      }
    }

    $depth--;
  }

  require_once ('projects/init.php');

  $deployment = $_REQUEST ['deployment'];
  if (! $deployment)
  {
    $deployment = 'opus';
  }

  $fn = $App->xml_options->export_file_name;
  $fhandle = fopen ($fn, 'w+');

  if (! $fhandle)
  {
    raise ("Could not open file [$fn] for folder export.");
  }

  fwrite ($fhandle, "<?xml version=\"1.0\"?>\n");
  fwrite ($fhandle, "<OpusVCS>\n");

  $Page->start_display ();

  $folder_query = $App->login->folder_query ();

  if ($deployment == 'opus')
  {
    $App->database->set_database ('opus_internal');

    // retrieve the tree of folders to guarantee that each folder
    // is written before its children

    $folder_query->set_order ("fldr.title, perm.type DESC");
  }

  $folders = $folder_query->tree ();

  fwrite ($fhandle, "  <earthliProjects>\n");
  fwrite ($fhandle, "    <folderList>\n");

  echo "<h3>Exporting folders...</h3>";
  process_folders ($folders, 2);

  fwrite ($fhandle, "    </folderList>\n");
  fwrite ($fhandle, "    <kindList>\n");

  echo "<h3>Exporting kinds...</h3>";
  $indexed_kinds = $App->entry_kinds ();
  foreach ($indexed_kinds as $kind)
  {
    fwrite ($fhandle, "      <kind name=\"$kind->title\"/>\n");
    echo "Exported [$kind->title]<br>";
  }

  fwrite ($fhandle, "    </kindList>\n");
  fwrite ($fhandle, "  </earthliProjects>\n");
  fwrite ($fhandle, "</OpusVCS>\n");

  $Page->finish_display ();

?>