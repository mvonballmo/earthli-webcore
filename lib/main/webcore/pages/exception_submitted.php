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

  $Page->template_options->title = 'Thanks';
  $Page->title->subject = 'Exception Submitted';

  $Page->location->add_root_link ();
  $Page->start_display ();
?>
<div class="box">
  <div class="box-title">Thank You</div>
  <div class="box-body">
    <p>Thank you for submitting a bug report.</p>
    <p>Use your browser's "Back" button to get back to where you were in the application or
      go back to the <a href="<?php echo $Page->resolve_file ('{root}'); ?>">Home Page</a>.</p>
  </div>
</div>
<?php
  $Page->finish_display ();
?>