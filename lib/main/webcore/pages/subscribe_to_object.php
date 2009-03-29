<?php

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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

  $Page->location->append ($App->short_title, './');
  $Page->title->subject = 'Subscribe';

  if (isset ($obj))
  { 
    $email = read_var ('email');
    $subscribed = read_var ('subscribed');
    
    if ($email)
    {
      $user_query = $App->user_query ();
      $user = $user_query->object_at_email ($email);
      $subscriber = $user->subscriber ();
    }
  }  
    
  if (isset ($user) && $App->login->is_allowed (Privilege_set_user, Privilege_modify, $user))
  {
    $subscriber->set_subscribed ($obj, $sub_type, $subscribed);
    $App->return_to_referer ($obj->home_page ());
  }
  else
  {
    $Page->raise_security_violation ('Invalid subscription request or access denied.');
  }
?>