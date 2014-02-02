<?php

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

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

  $Page->template_options->title = 'Rights';
  $Page->title->subject = 'Rights';

  $Page->location->add_root_link ();
  $Page->location->append ('Rights');
  $Page->start_display ();
?>
<div class="main-box">
  <div class="text-flow">
    <p>This site and it's contents (including web pages, email notifications and
    newsfeeds) belongs to the site owner, as indicated in the
    copyright notice at the bottom of each page. Posted content, like
    pictures, articles and comments, belong to the user that posted it, unless
    otherwise indicated.</p>
  </div>
</div>
<?php
  $Page->finish_display ();
?>