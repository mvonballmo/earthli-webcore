<?php

/**
 * Initialization file for the {@link RECIPE_APPLICATION}.
 * Use {@link ENVIROMENT::make_application()} with {@link
 * Recipe_application_id} to create an instance.
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package recipes
 * @version 3.6.0
 * @since 1.3.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Recipes.

earthli Recipes is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Recipes is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Recipes; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Recipes, visit:

http://earthli.com/software/webcore/app_recipes.php

****************************************************************************/

/**
 * Make sure the WebCore environment is present.
 * This file must be included at global scope so that the global variables,
 * {@link $Page}, {@link $Env}, {@link $Profiler} and {@link Logger} are
 * defined at global scope.
 */
include_once ('webcore/init.php');

/**
 * Unique id for the recipes application.
 * Used with {@link PAGE::register_application()} and {@link
 * PAGE::make_application()}.
 */
define ('Recipe_application_id', 'com.earthli.recipes');

$Page->register_application (Recipe_application_id, 'RECIPE_APPLICATION_ENGINE', 'recipes/config/recipe_application_engine.php');

?>