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

  $search_type = read_var ('type');

  /** @var OBJECT_IN_FOLDER_SEARCH $search */
  $search = $App->make_search ($search_type);

  if (isset ($search))
  {
    $App->set_search_text (read_var ('search_text'));
    $type_info = $App->search_type_info_for ($search_type);

    if (is_a ($search, 'OBJECT_IN_FOLDER_SEARCH'))
    {
      $id = read_var ('id');
      if (! $id)
      {
        $id = $App->root_folder_id;
      }
      
      $folder_query = $App->login->folder_query ();
      $folder = $folder_query->object_at_id ($id);
    }
    
    $class_name = $App->final_class_name ('EXECUTE_SEARCH_FORM', 'webcore/forms/execute_search_form.php');
    /** @var $form EXECUTE_SEARCH_FORM */
    $form = new $class_name ($App, $search);

    $form->process ($search);

    if ($form->committed ())
    {
      $search_query = $search->prepared_query ();
      $num_search_results = $search_query->size ();
    }

    $Page->title->subject = "Search for {$type_info->plural_title}";

    if (isset ($folder))
    {
      $search->set_folder_from_context($folder);
      $Page->location->add_folder_link ($folder);
      $Page->title->add_object ($folder);          
    }
    else
    {
      $Page->location->add_root_link ();
    }

    $Page->location->append ($Page->title->subject);

    $Page->start_display ();
  ?>
  <div class="main-box">
    <div class="columns text-flow">
      <div class="left-sidebar">
        <div class="form-content" id="search-form">
          <?php $form->display (); ?>
        </div>
      </div>
    <?php
      if ($form->committed ())
      {
/*
        $class_name = $App->final_class_name ('EXPLORER_COMMANDS', 'webcore/cmd/explorer_commands.php');
        $commands = new $class_name ($folder, $form->name);
        $renderer = $folder->handler_for (Handler_menu);
        $renderer->display ($commands);
*/

        $label = $num_search_results == 1 ? 'Result' : 'Results';
    ?>
        <div>
          <h2 id="search-results"><?php echo "$num_search_results $label"; ?></h2>
          <?php echo $search->system_description_as_html (); ?>
          <?php
            $grid = $search->grid ();
            $grid->show_folder = true;
            $grid->items_are_selectable = false;
            $grid->selector_name = "{$type_info->id}_ids";
            $grid->set_page_size (Default_page_size);
            $grid->set_query ($search_query);
            $grid->display ();
          ?>
        </div>
    <?php
      }
    ?>
    </div>
  </div>
  <?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ("Cannot search for objects of type [$search_type]");
  }
?>