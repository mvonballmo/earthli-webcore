<?php

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli Webcore.

earthli Webcore is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Webcore is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Webcore; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Webcore, visit:

http://www.earthli.com/software/webcore

****************************************************************************/

  $Page->title->subject = "Subscriptions";

  $email = read_var ('email');

  if ($email)
  {
    $subscriber_query = $App->subscriber_query ();
    $subscriber = $subscriber_query->object_at_email ($email);

    if (isset ($subscriber))
    {
      $user = $subscriber->user ();
    }
    else
    {
      $user_query = $App->user_query ();
      $user = $user_query->object_at_email ($email);
      if (! isset ($user))
      {
        $Env->redirect_local ("create_subscriber.php?email=$email");
      }
      else
      {
        $subscriber = $user->subscriber ();
        $subscriber->store ();
      }
    }

    if (! isset ($user) || $App->login->is_allowed (Privilege_set_global, Privilege_subscribe, $user))
    {
      $Page->title->add_object ($subscriber);

      $Page->location->add_root_link ();
      if (isset ($user))
      {
        $Page->location->append ('Users', 'view_users.php');
        $Page->location->add_object_link ($user);
      }
      else
      {
        $url = new URL($Env->url(Url_part_no_host_path));
        $url->replace_argument ('panel', 'summary');
        $Page->location->append ($email, $url->as_text ());
      }
      $Page->location->append ($Page->title->subject, '', '{icons}buttons/subscriptions');

      $class_name = $App->final_class_name ('SUBSCRIPTION_PANEL_MANAGER', 'webcore/gui/subscription_panel.php');
      /** @var $panel_manager PANEL_MANAGER */
      $panel_manager = new $class_name ($subscriber);
      /** @var $selected_panel FORM_PANEL */
      $selected_panel = $panel_manager->selected_panel ();
      $selected_panel->check ();

      $Page->start_display ();
  ?>
    <div class="box">
      <div class="box-body">
      <?php
      $box = $Page->make_box_renderer();
      $box->start_column_set();
      $box->new_column_of_type('left-sidebar-column');
?>
        <div class="left-sidebar">
          <h2>
            Subscriptions
          </h2>
          <?php
          $panel_manager->display (true);
          ?>
        </div>
      <?php
      $box->new_column_of_type('content-column');
      ?>
        <div class="form-content">
      <?php
      $selected_panel->display ();
      ?>
        </div>
      <?php
      $box->finish_column_set();
      ?>
      </div>
    </div>
  <?php
      $box->finish_column_set ();
      $Page->finish_display ();
    }
    else
    {
      $Page->raise_security_violation ('You are not allowed to see the subscriptions for this user. Registered users must log in before they can view or update subscriptions.', $subscriber);
    }
  }
  else
  {
    $Page->raise_error ('Please enter an email address.');
  }
?>
