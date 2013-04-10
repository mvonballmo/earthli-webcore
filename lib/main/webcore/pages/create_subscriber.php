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

  $email = read_var ('email');

  $subscriber_query = $App->subscriber_query ();

  if (!empty($email))
  {
    $subscriber = $subscriber_query->object_at_email ($email);
  }

  if (isset ($subscriber))
  {
    $Env->redirect_local ($subscriber->home_page ());
  }
  else
  {
    $class_name = $App->final_class_name ('USER_SUBSCRIPTION_OPTIONS_FORM', 'webcore/forms/user_subscription_options_form.php');
    /** @var $form USER_SUBSCRIPTION_OPTIONS_FORM */
    $form = new $class_name ($App);

    $subscriber = $App->new_subscriber ();

    $form->process_new ($subscriber);
    if ($form->committed ())
    {
      $Env->redirect_local ($subscriber->home_page ());
    }

    $Page->title->subject = "Create subscriber";

    $Page->location->add_root_link ();
    $Page->location->append ($Page->title->subject, '', '{icons}buttons/create');

    $Page->start_display ();
?>
<div class="box">
  <div class="box-body form-content">
  <?php
    $form->button = 'Create';
    $form->button_icon = '{icons}buttons/create';
    $form->display ();
  ?>
  </div>
</div>
<?php
    $Page->finish_display ();
  }
?>