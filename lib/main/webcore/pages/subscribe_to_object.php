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

  $Page->title->subject = 'Subscriptions';

  $email = read_var ('email');
  if (! $email)
  {
    $Env->redirect_local('create_subscriber.php');
  }

  $subscribed = read_var ('subscribed');
  $return_url = read_var ('return_url');

  if (isset ($obj) && $email)
  { 
    $user_query = $App->user_query ();
    $user = $user_query->object_at_email ($email);
    $subscriber = $user->subscriber ();
  }
    
  if (isset($obj) && isset($subscriber) && isset ($user) && $App->login->is_allowed (Privilege_set_user, Privilege_modify, $user))
  {
    if ($subscriber->subscribed($obj, $sub_type) != $subscribed)
    {
      $subscriber->set_subscribed ($obj, $sub_type, $subscribed);
    }

    if ($return_url)
    {
      $Env->redirect_local($return_url);
    }

    $Page->location->append($Page->title->subject, '', '{icons}indicators/subscribed');

    $Page->start_display();

?>
<div class="top-box">
  <div class="button-content"><?php
    $menu = $Page->make_menu();
    $menu->renderer = $Page->make_menu_renderer();
    $menu->append('Manage all your subscriptions', "view_user_subscriptions.php?email=$email", '{icons}/buttons/subscriptions');
    $menu->display();
    ?></div>
</div>
<div class="button-content">
  <?php
    $subscription_status = $obj->handler_for (Handler_subscriptions);
    $subscription_status->display ($obj);
  ?>
</div>
<?php
    $Page->finish_display();
  }
  else
  {
    $Page->raise_security_violation ('Invalid subscription request or access denied.');
  }
?>