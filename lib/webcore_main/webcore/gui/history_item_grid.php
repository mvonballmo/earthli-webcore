<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.0.0
 * @since 2.4.0
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
require_once ('webcore/gui/grid.php');

/**
 * Displays {@link HISTORY_ITEM}s from a {@link QUERY}.
 * @package webcore
 * @subpackage grid
 * @version 3.0.0
 * @since 2.4.0
 */
class HISTORY_ITEM_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  var $box_style = 'object-in-list';
  /**
   * @var string
   */
  var $object_name = 'history item';
  /**
   * @var integer
   */
  var $padding = 0;
  var $show_separator = FALSE;

  /**
   * Render the grid itself.
    * @param array[object] &$objs
    * @access private
    */
  function _draw (&$objs)
  {
    foreach ($objs as $obj)
    {
      if (isset ($this->_last_time))
        $this->time_diffs [] = $this->_last_time->diff ($obj->time_created);

      $this->_last_time = $obj->time_created;
    }

    parent::_draw ($objs);
  }

  /**
   * @param ARTICLE &$obj
    * @access private
    */
  function _start_row (&$obj)
  {
    $curr_date = $obj->time_created;
    if (! isset ($this->last_date) || (! $curr_date->equals ($this->last_date, Date_time_date_part)))
    {
      $this->last_date = $curr_date;
?>
  <tr>
    <td>
      <h2>
      <?php
        $t = $curr_date->formatter ();
        $t->type = Date_time_format_date_only;
        echo $curr_date->format ($t);
        $now = new DATE_TIME ();
        if ($curr_date->equals ($now, Date_time_date_part))
          echo ' (Today)';
        $now_yesterday = new DATE_TIME (time () - 86400);
        if ($curr_date->equals ($now_yesterday, Date_time_date_part))
          echo ' (Yesterday)';
      ?>
      </h2>
    </td>
  </tr>
<?php
    }

    parent::_start_row ($obj);
  }

  /**
   * @param HISTORY_ITEM &$obj
    * @access private
    */
  function _draw_box (&$obj)
  {
    $item_number = $this->_num_objects - ($this->_num_rows * $this->_num_columns * ($this->paginator->page_number - 1)) - $this->_item_number;
    $this->_item_number++;
    $creator =& $obj->creator ();
    
    $layer = $this->context->make_layer ('obj_' . $obj->id);
    $layer->visible = FALSE;
?>
  <div style="margin-left: 2em">
    <div style="float: left">
      <?php $layer->draw_toggle (); ?>
      <?php echo $obj->kind_as_icon ('16px'); ?>
    </div>
    <div style="margin-left: 40px">
      <div style="margin-bottom: .25em">
        <?php echo $obj->title_as_html (); ?>
      </div>
      <div class="detail">
      <?php
        $icon = $creator->icon_as_html ('16px');
        if ($icon)
          echo $icon . ' ';
        echo $creator->title_as_link () . ' - ' . $obj->time_created->format ();
      ?>
      </div>
    </div>
    <?php
      $layer->start ();
    ?>
    <dl class="detail" style="margin-left: 40px">
      <dt class="field">Kind of modification</dt>
      <dd>
        <?php echo $obj->kind_as_icon ('16px'); ?>
        <?php echo $obj->kind; ?>
      </dd>
      <dt class="field">Email notifications</dt>
      <dd>
        <?php echo $obj->publication_state_as_icon () . ' ' . $obj->publication_state_as_text (); ?>
      </dd>
      <?php
        if (isset ($this->time_diffs [$this->_item_number - 1]))
        {
      ?>
      <dt class="field">Time since previous revision</dt>
      <dd>
        <?php echo $this->time_diffs [$this->_item_number - 1]->format (); ?>
      </dd>
      <?php
        }
        
        if ($obj->description || $obj->system_description)
        {
      ?>
      <dt class="field">Description</dt>
      <dd>
        <?php echo $obj->description_as_html (); ?>
        <?php echo $obj->system_description_as_html (); ?>
      </dd>
      <?php
        }
      ?>
    </dl>
    <?php
      $layer->finish ();
  ?>
  </div>
  <?php
  }

  /**
   * @var integer
   * @access private
   */
  var $_item_number = 0;
  /**
   * @var DATE_TIME
   * @access private
   */
  var $_last_time;
}
?>