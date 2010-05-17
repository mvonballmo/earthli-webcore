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

  $Page->template_options->title = 'Privacy';
  $Page->title->subject = 'Privacy';

  $Page->location->add_root_link ();
  $Page->location->append ('Privacy');
  $Page->start_display ();
?>
<div class="box">
  <div class="box-title">Privacy</div>
  <div class="box-body">
    <h2>Short and Sweet</h2>
    <p>The owners and operators of this site will not give out any of your information to
    any third parties. Ever. Period.</p>
    <h2>The Nitty Gritty</h2>
    <p>As with most dynamic web sites, this one requires you to create an account
    so that you can participate fully.</p>
    <h3>Email addresses</h3>
    <p>Your email address will not be shared with anyone, nor will it be displayed
    on the web site (unless you ask us to in the user preferences). Email 
    notifications are opt-in and can be completely disabled at any time. 
    Anonymous subscriptions, because of system limitations, cannot be protected
    fully&mdash;create an account in order to ensure complete privacy.</p> 
    <h3>Passwords</h3>
    <p>Your password is immediatly encrypted before storing in the database and is not
    known to the operators of this web site (therefore it cannot be lost or
    shared). That also means that a lost password is lost forever and cannot be
    sent to you. It can, however, be reset.</p>
    <h3>Cookies</h3>
    <p>This site uses cookies to enhance the user experience (read: make stuff
    work better). These cookies can be deleted at any time without losing any
    information (though you will have to log in again). No user information is
    stored in the cookie.</p>
    <h3>Log Files</h3>
    <p>This site employs logging to track its usage. These logs include information
    about which computer visited what when, but the data cannot be linked to a
    user's personal information.</p>
    <h3>Legal Loopholes</h3>
    <p>It is possible that the law of the country in which this server resides 
    may require the site to disclose any of the personal information mentioned above 
    in order to comply with a current judicial proceeding, a court order or 
    other legal process. In this case, this site will do its best to inform users 
    of the changed circumstances. If you want more detail, you're kind of on your 
    own&mdash;suffice it to say that it's a big, bad world out there and life 
    isn't always fair.</p>
    <h3>Selling Out</h3>
    <p>If this site is sold at a fantasical profit to another entity, all user 
    information will belong to the new entity and their rules will apply. Again,
    this site will do its best to inform users of the changed circumstances.</p>
  </div>
</div>
<?php
  $Page->finish_display ();
?>