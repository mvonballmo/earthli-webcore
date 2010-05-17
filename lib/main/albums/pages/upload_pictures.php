<?php

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/
  
  $folder_query = $App->login->folder_query ();
  $folder = $folder_query->object_at_id (read_var ('id'));

  if (isset ($folder)
      && $App->login->is_allowed (Privilege_set_entry, Privilege_create, $folder)
      && $App->login->is_allowed (Privilege_set_entry, Privilege_upload, $folder))
  {    
    $Page->title->add_object ($folder);
    $Page->title->subject = 'Upload pictures';
    
    $Page->location->add_folder_link ($folder);
    $Page->location->append ($Page->title->subject);

    $class_name = $App->final_class_name ('UPLOAD_PICTURES_FORM', 'albums/forms/upload_pictures_form.php');
    $form = new $class_name ($folder);
    $form->process_plain ();
    if (! $form->committed ())
    {
      $Page->start_display ();
?>
<div class="box">
  <div class="box-title">
    <?php echo $App->title_bar_icon ('{icons}buttons/upload'); ?> <?php echo $Page->title->subject; ?>
  </div>
  <div class="box-body">
  <?php
    $form->display ();
  ?>
  </div>
</div>
<?php
     $Page->finish_display ();
    }
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to upload pictures.', $folder);
  }
?>