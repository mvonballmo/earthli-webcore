<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.4.0
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
require_once ('webcore/gui/renderer.php');

/**
 * Render details for an {@link APPLICATION}.
 * Used from the configuration/administration section.
 * @package webcore
 * @subpackage renderer
 * @version 3.4.0
 * @since 2.7.0
 */
class APPLICATION_RENDERER extends RENDERER
{
  /**
   * Renders application details as HTML.
   * @param APPLICATION_CONFIGURATION_INFO $obj
   */
  public function display_as_html ($obj)
  {
?>
<table class="basic columns left-labels top">
  <tr>
    <th>Title</th>
    <td><?php echo $this->app->get_text_with_icon($obj->app->icon, $obj->app->name (), '20px'); ?></td>
  </tr>
  <tr>
    <th>Database name</th>
    <td><?php echo $obj->app->database_options->name; ?></td>
  </tr>
  <tr>
    <th>Frameworks</th>
    <td class="text-flow">
      <?php echo $obj->app_info->description (); ?><br>
      <?php echo $obj->lib_info->description (); ?>
    </td>
  </tr>
  <tr>
    <th>Databases</th>
    <td class="text-flow">
      <?php echo $obj->app_info->description (false); ?><br>
      <?php echo $obj->lib_info->description (false); ?>
    </td>
  </tr>
  <tr>
    <th>Support URL</th>
    <td><a href="<?php echo $obj->app->support_url; ?>"><?php echo $obj->app->support_url; ?></a></td>
  </tr>
  <tr>
    <th>Log Files</th>
    <td><?php echo $this->app->resolve_file ($obj->app->mail_options->log_file_name); ?></td>
  </tr>
</table>
<?php
  }
}

?>