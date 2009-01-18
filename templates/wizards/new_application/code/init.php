<?php

/**
 * @copyright Copyright (c) 2006 [?_template_author_name_?]
 * @author [?_template_author_name_?] <[?_template_author_email_?]>
 * @filesource
 * @package [?_template_app_name_?]
 * @subpackage pages
 * @version 1.0.0
 * @since 1.0.0
 */

/****************************************************************************

Copyright (c) 2006 [?_template_author_name_?]

This file is part of [?_template_app_title_?].

[?_template_app_title_?] is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

[?_template_app_title_?] is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with [?_template_app_title_?]; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the [?_template_app_title_?], visit:

[?_template_app_url_?]

****************************************************************************/

/** Make sure the WebCore environment is present.
 * This file must be included at global scope so that the global variables,
 * {@link $Page}, {@link $Env}, {@link $Profiler} and {@link Logger} are
 * defined at global scope. */
include_once ('webcore/init.php');

/** Unique id for the [?_template_app_name_?] application.
 * Used with {@link ENVIRONMENT::register_application()} and {@link
 * ENVIRONMENT::make_application()}. */
define ('[?_template_prefix_mc_?]_application_id', 'com.earthli.[?_template_app_name_?]');

$Page->register_application ([?_template_prefix_mc_?]_application_id, '[?_template_prefix_uc_?]_APPLICATION_ENGINE', '[?_template_folder_name_?]/config/[?_template_prefix_lc_?]_application_engine.php');

?>
