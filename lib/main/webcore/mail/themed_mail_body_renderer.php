<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
 * @version 3.1.0
 * @since 2.2.1
 */

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

/** */
require_once ('webcore/mail/mail_body_renderer.php');

/**
 * Renders html emails using WebCore renderers.
 * @package webcore
 * @subpackage mail
 * @version 3.1.0
 * @since 2.2.1
 */
class THEMED_MAIL_BODY_RENDERER extends MAIL_BODY_RENDERER
{
  /**
   * @return string
   * @access private
   */
  protected function _build_html_header ()
  {
    ob_start ();
      $this->page->start_display ();
?>
<div class="box"><div class="box-body">
<?php
      $Result = ob_get_contents ();
    ob_end_clean ();
    return $Result;
  }

  /**
   * @return string
   * @access private
   */
  protected function _build_html_footer ()
  {
    ob_start ();
?>
</div></div>
<?php
      $this->page->finish_display ();
      $Result = ob_get_contents ();
    ob_end_clean ();
    return $Result;
  }  
}
?>