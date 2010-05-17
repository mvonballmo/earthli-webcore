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

  if ($App->login->is_allowed (Privilege_set_global, Privilege_configure))
  {
    $class_name = $App->final_class_name ('APPLICATION_CONFIGURATION_INFO', 'webcore/obj/framework_info.php');
    $info = new $class_name ($App);
    
    $Page->title->subject = 'Configure';

    $Page->location->add_root_link ();
    $Page->location->append ($Page->title->subject);

    $Page->start_display ();
  ?>
  <div class="box">
    <div class="box-title">
      <?php echo $App->title_bar_icon ('{icons}indicators/working'); ?> <?php echo $Page->title->subject; ?>
    </div>
    <?php
      $class_name = $App->final_class_name ('CONFIGURE_COMMANDS', 'webcore/cmd/configure_commands.php');
      $commands = new $class_name ($info);
      $renderer = $App->make_menu_renderer ();
      $renderer->display_as_toolbar ($commands);
    ?>
    <div class="box-body">
    <?php
      $class_name = $App->final_class_name ('APPLICATION_RENDERER', 'webcore/gui/application_renderer.php');
      $renderer = new $class_name ($App);
      $renderer->display_as_html ($info);
    ?>
    </div>
  </div>
  <?php
    $Page->finish_display ();
  }
  else
  {
    $Page->raise_security_violation ('You are not allowed to configure this application.');
  }
?>