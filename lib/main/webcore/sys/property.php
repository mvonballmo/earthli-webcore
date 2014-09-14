<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.6.0
 * @since 2.5.0
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
require_once ('webcore/obj/webcore_object.php');

/**
 * Represents a generic value.
 * Also includes a title and icon.
 * @package webcore
 * @subpackage obj
 * @version 3.6.0
 * @since 2.5.0
 */
class PROPERTY_VALUE extends WEBCORE_OBJECT
{
  /**
   * The actual stored value.
   * Usually stored as this value in a database. The {@link $title} and {@link $icon} are for
   * displaying properties with this value.
   * @var integer
   */
  public $value;

  /**
   * @var string
   */
  public $title;

  /**
   * Name and path of icon.
   * Can contain a location alias that will be resolved with a {@link RESOURCE_MANAGER}.
   * @var string
   */
  public $icon;

  /**
   * Icon rendered as an HTML image tag.
   * @param string $size
   * @return string
   */
  public function icon_as_html ($size = Sixteen_px)
  {
    return $this->context->resolve_icon_as_html($this->icon, $size, $this->title);
  }
}