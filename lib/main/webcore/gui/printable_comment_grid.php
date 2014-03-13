<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
 * @since 2.2.1
 */

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

/** */
require_once ('webcore/gui/grid.php');

/**
 * Displays {@link COMMENT}s from a query with support for pagination.
 * @package webcore
 * @subpackage grid
 * @version 3.5.0
 * @since 2.2.1
 */
abstract class PRINTABLE_COMMENT_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  public $object_name = 'comment';

  /**
   * @var boolean
   */
  public $show_separator = false;

  /**
   * @var boolean
   */
  public $show_controls = true;

  /**
   * Paginate this set of comments?
   * @var boolean
   */
  public $show_paginated = true;

  /**
   * @var boolean
   */
  public $show_user_info = true;

  /**
   * @param APPLICATION $app
   * @param COMMENT $comment Comments belong to this comment (can be empty).
   */
  public function __construct ($app, $comment)
  {
    parent::__construct ($app);
    $this->_comment = $comment;
  }

  /**
   * Show the pager only if not printing.
   * @param boolean $include_anchor_id If true, renders the id for the
   * pager.
   * @access private
   */
  protected function _draw_pager ($include_anchor_id)
  {
    if ($this->_show_pager)
    {
      parent::_draw_pager ($include_anchor_id);
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
  protected $_show_pager = false;

}