<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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

	$page_name = read_var ('page_name');
	
	if (!empty($page_name))
	{
    $Page->title->subject = 'Select folder';

	  $Page->location->add_root_link ();
	  $Page->location->append ($Page->title->subject);
	
	  $Page->start_display ();
	?>
	  <div class="main-box">
	    <div class="text-flow">
	      <p>Please select a folder in order to continue.</p>
	    <?php    
		    $create_entry_url = $App->resolve_file_for_alias (Folder_name_application, $page_name);
		    
		    include_once ('webcore/gui/folder_tree_node_info.php');
		    $tree = $App->make_tree_renderer ();
		    $tree->node_info = new FOLDER_TREE_NODE_INFO ($App);
		    $tree->node_info->page_link = "$create_entry_url";
		    
		    $folder_query = $App->login->folder_query ();
		    $folders = $folder_query->tree ();
		    $tree->display ($folders);
	    ?>
	    </div>
	  </div>
	<?php
	  $Page->finish_display ();
	}
	else
	{
	  $Page->raise_error ('Incorrect arguments for selecting a folder.', 'Select folder');
	}
?>
