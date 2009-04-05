<?php

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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

  /* Set these globals before using this template:

       $entry_type_info: TYPE_INFO
  */

  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->object_at_id (read_var ('id'));

  if (! isset ($folder))
  {
    $Env->redirect_local ('select_folder.php?last_page=' . urlencode ($Env->url (Url_part_all)));
  }
  else
  {
    if ($App->login->is_allowed (Privilege_set_entry, Privilege_create, $folder))
    {
      $class_name = $App->final_class_name ('ENTRY_FORM', 'webcore/forms/object_in_folder_form.php', $entry_type_info->id);
      $form = new $class_name ($folder);

      $entry = $folder->new_object ($entry_type_info->id);

      include_once ('webcore/util/options.php');
      $opt_stay_on_page = new STORED_OPTION ($App, "stay_on_{$entry_type_info->id}_page");
      $opt_stay_on_page->add_argument ('id', $folder->id);

      $form->process_new ($entry);
      if ($form->committed ())
      {
        if ($form->is_field ('quick_save') && $form->value_for ('quick_save'))
        {
          $Env->redirect_local ($entry_type_info->edit_page . '?id=' . $entry->id);
        }
        else
        {
          if ($opt_stay_on_page->value ())
          {
            $url = new URL ($folder->home_page ());
            $url->replace_name_and_extension ($Env->url (Url_part_file_name));
            $url->add_argument ('last_id', $entry->id);
            $Env->redirect_local ($url->as_text ());
          }
          else
          {
            $Env->redirect_local ($entry->home_page ());
          }
        }
      }

      $Page->title->add_object ($folder);
      $Page->title->subject = "Create {$entry_type_info->singular_title}";

      $Page->location->add_folder_link ($folder);
      $Page->location->append ($Page->title->subject);

      if ($entry_type_info->icon)
      {
        $icon = $entry_type_info->icon;
      }
      else
      {
        $icon = '{icons}buttons/create';
      }

      $Page->start_display ();
    ?>
    <div class="box">
      <div class="box-title">
        <?php echo $App->title_bar_icon ($icon); ?> Create <?php echo $entry_type_info->singular_title; ?>
      </div>
      <?php
        $url = $opt_stay_on_page->setter_url_as_html (! $opt_stay_on_page->value ());
        if ($opt_stay_on_page->value ())
        {
          $icon = $App->resolve_icon_as_html ('{icons}indicators/pinned', 'Unpin', '16px', '');
          $text = 'You are "pinned" here and will come back to this form after creating your ' . strtolower ($entry_type_info->singular_title) . 
                  '. (<a href="' . $url . '">Unpin</a>)';
        }
        else
        {
          $icon = $App->resolve_icon_as_html ('{icons}indicators/unpinned', 'Pin', '16px', '');
          $text = 'Use the "<a href="' . $url . '">pin</a>" to stay on this page; it makes creating multiple '
                  . strtolower ($entry_type_info->plural_title) . ' easier.';
        }
        
        echo '<div class="status-indicator"><div style="float: left"><a href="' . $url . '">' . $icon . '</a></div><div style="margin-left: 20px">' . $text . '</div></div>';
      ?>
      <div class="box-body">
      <?php
        $last_id = read_var ('last_id');
        if ($last_id)
        {
          $entry_query = $folder->entry_query ();
          $entry_query->set_type ($entry_type_info->id);
          $last_entry = $entry_query->object_at_id ($last_id);
      ?>
      <div style="margin-bottom: 1em; margin-top: -1em">
        <?php echo $App->resolve_icon_as_html ('{icons}indicators/info', 'Info', '16px'); ?>
        Added <?php echo $last_entry->title_as_link (); ?>.
        <span class="notes">(create another <?php echo $entry_type_info->singular_title; ?> below)</span>
      </div>
      <?php
        }

        $form->display ();
      ?>
      </div>
    </div>
    <?php
      $Page->finish_display ();
    }
    else
    {
      $Page->raise_security_violation ("You are not allowed to create {$entry_type_info->plural_title} in this folder.", $folder);
    }
  }
?>