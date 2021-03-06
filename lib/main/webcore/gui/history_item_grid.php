<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
 * @since 2.4.0
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
require_once ('webcore/gui/grid.php');

/**
 * Displays {@link HISTORY_ITEM}s from a {@link QUERY}.
 * @package webcore
 * @subpackage grid
 * @version 3.6.0
 * @since 2.4.0
 */
class HISTORY_ITEM_GRID extends STANDARD_GRID
{
  /**
   * @param CONTEXT $context Context to which this grid belongs.
   */
  public function __construct($context)
  {
    parent::__construct($context);

    $this->css_class .= ' full-width';
  }

  /**
   * Render the grid itself.
   * @param HISTORY_ITEM[] $objects
   * @access private
   */
  protected function _draw ($objects)
  {
    foreach ($objects as $obj)
    {
      if (isset ($this->_last_time))
      {
        $this->_time_diffs [] = $this->_last_time->diff ($obj->time_created);
      }

      $this->_last_time = $obj->time_created;
    }

    parent::_draw ($objects);
  }

  /**
   * @param HISTORY_ITEM $obj
   * @access private
   */
  protected function _draw_box ($obj)
  {
    $curr_date = $obj->time_created;
    if (! isset ($this->last_date) || (! $curr_date->equals ($this->last_date, Date_time_date_part)))
    {
      $this->last_date = $curr_date;
      ?>
      <h2>
        <?php
        $t = $curr_date->formatter ();
        $t->type = Date_time_format_date_only;
        echo $curr_date->format ($t);
        $now = new DATE_TIME ();
        if ($curr_date->equals ($now, Date_time_date_part))
        {
          echo ' (Today)';
        }
        $now_yesterday = new DATE_TIME (time () - 86400);
        if ($curr_date->equals ($now_yesterday, Date_time_date_part))
        {
          echo ' (Yesterday)';
        }
        ?>
      </h2>
    <?php
    }

    $this->_item_number += 1;
    $creator = $obj->creator ();
?>
  <h3>
    <?php echo $obj->title_as_html (); ?>
  </h3>
  <?php
  if ($obj->description || $obj->system_description)
  {
    echo $obj->description_as_html ();
    echo $obj->system_description_as_html ();
  }
  ?>
  <table class="basic columns left-labels top">
    <tr>
      <th>Time</th>
      <td>
      <?php
        $tf = $obj->time_created->formatter();
        $tf->type = Date_time_format_date_and_time;
        echo $obj->time_created->format ();
      ?>
      </td>
    </tr>
    <tr>
      <th>User</th>
      <td>
        <?php echo $this->context->get_icon_with_text($creator->icon_url, Sixteen_px, $creator->title_as_link()); ?>
      </td>
    </tr>
    <tr>
      <th>Kind</th>
      <td>
        <?php echo $this->context->get_icon_with_text($obj->kind_icon_url(), Sixteen_px, $obj->kind); ?>
      </td>
    </tr>
    <tr>
      <th>Emails</th>
      <td>
        <?php echo $this->context->get_icon_with_text($obj->publication_state_icon_url(), Sixteen_px, $obj->publication_state_as_text()); ?>
      </td>
    </tr>
  </table>
  <?php
  if (isset ($this->_time_diffs [$this->_item_number - 1]))
  {
    ?>
    <p>&dArr; <?php echo $this->_time_diffs [$this->_item_number - 1]->format (); ?> &dArr;</p>
  <?php
  }
  ?>
  <?php
  }

  /**
   * @var integer
   * @access private
   */
  protected $_item_number = 0;

  /**
   * @var DATE_TIME
   * @access private
   */
  protected $_last_time;

  /**
   * @var TIME_INTERVAL[]
   */
  protected $_time_diffs;
}
?>