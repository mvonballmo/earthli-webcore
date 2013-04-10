<?php

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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

  $Page->template_options->title = 'Contact';
  $Page->title->subject = 'Contact';

  $Page->location->add_root_link ();
  $Page->location->append ('Contact');
  $Page->start_display ();
  
  $res = $Page->resources ();
  $options = $Page->template_options;
?>
<div class="box">
  <div class="box-body">
    <p>To contact the site owners, use the 
      <a href="<?php echo $res->resolve_file ($options->support_url); ?>">support page</a>
      or send an email to <em><?php echo scramble_email ($Page->mail_options->webmaster_address); ?></em>.</p>
  </div>
</div>
<?php
  $Page->finish_display ();
?>