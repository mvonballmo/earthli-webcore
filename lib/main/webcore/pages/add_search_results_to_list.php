<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
  $search = $App->make_search ($search_type);

  if (isset ($search))
  {
    $App->set_search_text (read_var ('search_text'));
    $type_info = $App->search_type_info_for ($search_type);

    $class_name = $App->final_class_name ('EXECUTE_SEARCH_FORM', 'webcore/forms/execute_search_form.php');
    $form = new $class_name ($App, $search);

    $form->process ($search);

    if ($form->committed ())
    {
      $search_query = $search->prepared_query ();
      $num_search_results = $search_query->size ();
    }

    $Page->title->subject = "Search for {$type_info->plural_title}";

    $Page->location->add_root_link ();
    $Page->location->append ($Page->title->subject);

    $Page->start_display ();
  ?>
  <div class="box">
    <div class="box-title">
      <?php
        echo $Page->title->subject;
        if ($form->committed ())
        {
          echo " ($num_search_results found)";
        }
      ?>
    </div>
    <div class="box-body">
    <?php
      if ($form->committed ())
      {
        $form->controls_visible = ($num_search_results == 0);
        $form->display ();
    ?>
    <div>
    <?php
        include_once ('webcore/forms/add_to_list_form.php');
        $list_form = new ADD_TO_LIST_FORM ($App, $search, $search_query);
        $list_form->action = 'add_to_list.php';
        $list_form->display ();
    ?>
    </div>
    <?php
      }
      else
      {
        $form->display ();
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