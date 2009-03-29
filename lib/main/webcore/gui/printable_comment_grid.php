<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
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
require_once ('webcore/gui/grid.php');

/**
 * Displays {@link COMMENT}s from a query with support for pagination.
 * @package webcore
 * @subpackage grid
 * @version 3.0.0
 * @since 2.2.1
 */
class PRINTABLE_COMMENT_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  public $object_name = 'comment';
  /**
   * @var boolean
   */
  public $show_separator = FALSE;
  /**
   * @var boolean
   */
  public $show_controls = TRUE;
  /**
   * Paginate this set of comments?
    * @var boolean
    */
  public $show_paginated = TRUE;
  /**
   * @var boolean
   */
  public $show_user_info = TRUE;

  /**
   * @param APPLICATION $application
   * @param COMMENT $comment Comments belong to this comment (can be empty).
   */
  function PRINTABLE_COMMENT_GRID ($app, $comment)
  {
    GRID::GRID ($app);
    $this->_comment = $comment;
  }

  /**
   * Show the paginator only if not printing.
   * @access private
   */
  function _draw_paginator ()
  {
    if ($this->_show_paginator)
    {
      parent::_draw_paginator ();
    }
  }

  /**
   * Show only the comment tree based on this comment.
    * If empty, shows all comments from '_entry'.
    * @var COMMENT
    * @access private
    */
  protected $_comment;
  /**
   * @var boolean
    * @access private
    */
  protected $_show_paginator = FALSE;

}
?>