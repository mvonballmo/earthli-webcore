<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tree
 * @version 3.0.0
 * @since 2.2.1
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
require_once ('webcore/gui/tree.php');

/**
 * Static tree rendered using simple HTML tables.
 * @package webcore
 * @subpackage tree
 * @version 3.0.0
 * @since 2.2.1
 */
class STATIC_TREE extends HTML_TREE
{
  /**
   * Specifies the minimum depth before 'plus' or 'ell' icons are drawn.
    * For statically-rendered trees, the minimum depth of 1 prevents symbols from being drawn in front
    * of the root items; there is no need for them since they can't be manipulated anyway (unlike the
    * dynamic tree where they could be used to open/close the branch).
    * @var integer
    * @access private
    */
  var $_min_depth_for_icons = 1;
}

?>