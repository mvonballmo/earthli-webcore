<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage config
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

if (! isset ($WebCore_init_profiler))
{
  /**
   * Set this value to <code>False</code> to prevent the initialization
   * of the global {@link $Profiler}.
   * @version 3.1.0
   * @since 2.7.0
   * @global boolean
   */
  $WebCore_init_profiler = true;
}

if ($WebCore_init_profiler)
{
  /** */
  include_once ('webcore/util/profiler.php');
  
  /**
   * Used for profiling, or timing, applications.
   * This object is instantiated first, so that it records the page loading time
   * as accurately as possible. The "global" timer is started immediately.
   * @version 3.1.0
   * @since 2.2.1
   * @global PROFILER $Profiler
   * @access private
   */
  $Profiler = new PROFILER ();
  $Profiler->start ('global');
}

/**
 * Try to load a customized startup engine. If the extension file defines
 * a function called "engine_class_name", the class name returned from that
 * function is instantiated.
 */
@include_once ('plugins/com.earthli.webcore.init.php');

if (function_exists ('engine_class_name'))
  $class_name = engine_class_name ();
else
{
  include_once ('webcore/config/themed_engine.php');
  $class_name = 'THEMED_ENGINE';
}

/**
 * Global WebCore startup object.
 * Calls {@link ENGINE::init()} to set up global WebCore objects. These objects
 * are assigned to the global variables {@link $Env}, {@link $Page}, {@link
 * $Logger} below.
 * @see $Env
 * @see $Page
 * @see $Logger
 * @see $Profiler
 * @version 3.1.0
 * @since 2.7.0
 * @global ENGINE $Engine
 */
$Engine = new $class_name ();
$Engine->init ();

/**
 * Reference to the global environment.
 * @see $Engine
 * @see $Page
 * @see $Logger
 * @see $Profiler
 * @version 3.1.0
 * @since 2.2.1
 * @global ENVIRONMENT $Env
 */
$Env = $Engine->env;

/**
 * Reference to the global page.
 * @see $Engine
 * @see $Env
 * @see $Logger
 * @see $Profiler
 * @version 3.1.0
 * @since 2.2.1
 * @global PAGE $Page
 */
$Page = $Engine->page;

/**
 * Reference to the global logger.
 * Also accessible through {@link ENVIRONMENT::$logs}.
 * @see $Engine
 * @see $Env
 * @see $Page
 * @see $Profiler
 * @version 3.1.0
 * @since  2.2.1
 * @global LOGGER $Logger
 * @access private
 */
$Logger = $Env->logs->logger;

?>