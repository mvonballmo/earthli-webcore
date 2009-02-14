<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
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
require_once ('webcore/obj/webcore_object.php');

/**
 * Renders boxes and columns in HTML.
 * Uses HTML tables if the browser does not support the proper CSS box model.
 * @package webcore
 * @subpackage renderer
 * @version 3.0.0
 * @since 2.7.0
 */
class BOX_RENDERER extends WEBCORE_OBJECT
{
  /**
   * @param CONTEXT &$context
   */
  function BOX_RENDERER (&$context)
  {
    WEBCORE_OBJECT::WEBCORE_OBJECT ($context);
    $browser = $this->env->browser ();
    $this->_supports_css_tables = $browser->supports (Browser_CSS_Tables);
  }
  
  /**
   * Starts a set of columns.
   * Call {@link new_column()} to make columns in the set. Call {@link
   * finish_column_set()} to close.
   */
  function start_column_set ()
  {
    if ($this->_supports_css_tables)
      echo '<div style="display: table">' . "\n";
    else
      echo '<table cellpadding="0" cellspacing="0"><tr>' . "\n";
  }
  
  /**
   * Open a column after calling {@link start_column_set()}.
   * Closes a previously opened column automatically.
   * @param string $CSS_style Use this style for the column.
   */
  function new_column ($CSS_style = '')
  {
    if ($this->_column_started)
      $this->_close_column ();
      
    if ($this->_supports_css_tables)
    {
      $tag_name = 'div';
      $style = 'display: table-cell; vertical-align: top';
    }
    else
    {
      $tag_name = 'td';
      $style = 'vertical-align: top';
    }
    
    if ($CSS_style)
      $style .= '; ' . $CSS_style;
      
    echo '  <' . $tag_name . ' style="' . $style . '">' . "\n";

    $this->_column_started = TRUE; 
  }
  
  /**
   * Close a set opened with {@link start_column_set()}.
   * Closes a previously opened column automatically.
   */
  function finish_column_set ()
  {
    if ($this->_column_started)
      $this->_close_column ();
      
    if ($this->_supports_css_tables)
      echo '</div>' . "\n";
    else
      echo '</tr></table>' . "\n";
  }
  
  /**
   * Close an column opened with {@link new_column()}.
   * Called automatically from {@link finish_column_set()} and
   * <code>new_column()</code> when needed.
   * @access private
   */
  function _close_column ()
  {
    if ($this->_supports_css_tables)
      echo '  </div>' . "\n";
    else
      echo '  </td>' . "\n";
  }
  
  /**
   * Can CSS 2 be used to render controls?
   * @var boolean
   */
  var $_supports_css_tables;

  /**
   * @var boolean 
   * @access private
   */
  var $_column_started = FALSE;
}

?>