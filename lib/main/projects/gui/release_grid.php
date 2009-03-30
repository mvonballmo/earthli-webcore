<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package projects
 * @subpackage gui
 * @version 3.0.0
 * @since 1.4.1
 */

/****************************************************************************

Copyright (c) 2002-2008 Marco Von Ballmoos

This file is part of earthli Projects.

earthli Projects is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Projects is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Projects; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Projects, visit:

http://www.earthli.com/software/webcore/projects

****************************************************************************/

/** */
require_once ('webcore/gui/content_object_grid.php');

/**
 * Display a list of {@link RELEASE}s from a {@link QUERY}.
 * @package projects
 * @subpackage gui
 * @version 3.0.0
 * @since 1.4.1
 */
class RELEASE_GRID extends CONTENT_OBJECT_GRID
{
  /**
   * @var string
   */
  public $box_style = 'object-in-list';

  /**
   * @var string
   */
  public $object_name = 'Release';

  /**
   * @var integer
   */
  public $spacing = 4;

  /**
   * @var boolean
   */
  public $even_columns = false;

  /**
   * @var boolean
   */
  public $show_separator = false;

  /**
   * @var boolean Show creator/modifier with releases?
   */
  public $show_user = true;

  /**
   * @var boolean Show project for release?
   */
  public $show_folder = false;

  /**
   * @var boolean Show branch for release?
   */
  public $show_branch = false;

  /**
   * @param RELEASE $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    $folder = $obj->parent_folder ();
    $branch = $obj->branch ();
    $creator = $obj->creator ();
?>
<div style="margin-left: 24px">
  <?php
    $this->_draw_menu_for ($obj, Menu_size_compact);
  ?>
  <div class="grid-title">
  <?php
    if ($this->show_folder && isset ($folder))
    {
      echo $folder->title_as_link () . $this->app->display_options->object_separator;
    }
    if ($this->show_branch && isset ($branch))
    {
      echo $branch->title_as_link () . $this->app->display_options->object_separator;
    }
    echo $obj->state_as_icon () . ' ';
    echo $this->obj_link ($obj);
  ?>
  </div>
</div>
<div style="padding-right: .5em; float: left">
<?php
  $layer = $this->context->make_layer ("id_{$obj->id}_details");
  $layer->CSS_class = 'description';
  $layer->margin_left = '24px';
  $layer->draw_toggle ();
?>
</div>
<div style="margin-left: 24px">
  <div class="detail">
    <?php
      $status = $obj->status ();
      echo $status->as_html ();
    ?>
  </div>
</div>
<?php
    $layer->start ();
?>
<p class="detail">
<?php
  echo 'Created ';
  if ($this->show_user)
  {
    echo 'by ' . $creator->title_as_link () . ' - ';
  }
  echo $obj->time_created->format ();

  if (! $obj->time_created->equals ($obj->time_modified))
  {
    $modifier = $obj->modifier ();
    echo '<br>Updated ';
    if ($this->show_user)
    {
      echo 'by ' . $modifier->title_as_link () . ' - ';
    }
    echo $obj->time_modified->format ();
  }
?>
</p>
<?php
    echo $obj->description_as_html ();
    $layer->finish ();
  }
}
?>