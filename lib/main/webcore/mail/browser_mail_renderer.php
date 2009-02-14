<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

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
require_once ('webcore/mail/mail_object_renderer.php');

/**
 * Renders the contents of an {@link SUBMIT_BROWSER_FORM} for text or html email.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.2.1
 */
class BROWSER_MAIL_RENDERER extends RENDERER_BASED_MAIL_RENDERER
{
  /**
   * @return BROWSER_RENDERER
   * @access private
   */
  function _make_renderer ()
  {
    $class_name = $this->context->final_class_name ('BROWSER_RENDERER', 'webcore/gui/browser_renderer.php');
    $Result = new $class_name ($this->context);
    $Result->show_user_agent = TRUE;
    return $Result;
  }
}

?>