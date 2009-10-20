<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
 * @version 3.2.0
 * @since 2.5.0
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

/** */
require_once ('webcore/gui/renderer.php');

/**
 * Render details for an {@link APPLICATION}.
 * Used from the configuration/administration section.
 * @package webcore
 * @subpackage renderer
 * @version 3.2.0
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
    $main_style = '';
    if ($obj->app->icon)
    {
      $main_style .= ' margin-left: 60px';
      echo '<div style="float: left; margin-right: 10px">';
      echo $this->app->resolve_icon_as_html ($obj->app->icon, ' ', '50px');
      echo '</div>';
      echo '<div style="' . $main_style . '">';
    }
?>      
  <dl>
    <dt class="field">
      Title
    </dt>
    <dd>
      <?php echo $obj->app->name (); ?>
    </dd>
    <dt class="field">
      Database Name
    </dt>
    <dd>
      <?php echo $obj->app->database_options->name; ?>
    </dd>
    <dt class="field">
      Frameworks
    </dt>
    <dd>
      <?php echo $obj->app_info->description (); ?><br>
      <?php echo $obj->lib_info->description (); ?>
    </dd>
    <dt class="field">
      Databases
    </dt>
    <dd>
      <?php echo $obj->app_info->description (false); ?><br>
      <?php echo $obj->lib_info->description (false); ?>
    </dd>
    <dt class="field">
      Support URL
    </dt>
    <dd>
      <a href="<?php echo $obj->app->support_url; ?>"><?php echo $obj->app->support_url; ?></a>
    </dd>
    <dt class="field">
      Log Files
    </dt>
    <dd>
      <?php
        echo $this->app->resolve_file ($obj->app->mail_options->log_file_name);
      ?>
    </dd>
  </dl>
<?php
    if ($obj->app->icon)
    {
      echo '</div>';
    }
  }
}

?>