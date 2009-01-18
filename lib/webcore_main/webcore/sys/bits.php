<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.2.1
 * @access private
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

/**
 * Maintains bit flags.
 * @package webcore
 * @subpackage sys
 * @version 3.0.0
 * @since 2.2.1
 * @access private
 */
class BITS
{
  /**
   * Are all the bits matching 'flag' enabled?
   * @param integer $flag
   * @return boolean
   */
  function enabled ($flag)
  {
    return $this->bits & $flag;
  }

  /**
   * Set all bits in 'flag' to 'enabled'.
   * @var integer $flag
   * @param boolean $enabled
   */
  function set_enabled ($flag, $enabled)
  {
    if ($enabled)
      $this->set ($flag);
    else
      $this->clear ($flag);
  }

  /**
   * Enable all the bits in 'flag'.
   * @param integer $flag
   */
  function set ($flag)
  {
    $this->bits = $this->bits | $flag;
  }

  /**
   * Clear all the bits in 'flag'.
   * @param integer $flag
   */
  function clear ($flag)
  {
    $this->bits = $this->bits & ~$flag;
  }

  /**
   * Return true if there are no bits set.
   * @return boolean
   */
  function is_empty ()
  {
    return $this->bits == 0;
  }

  /**
   * Load a set of flags from 'bits'.
   * @param integer $bits
   */
  function load ($bits)
  {
    $this->bits = $bits;
  }

  /**
   * @var integer
   * @access private
   */
  var $bits = 0;
}

?>