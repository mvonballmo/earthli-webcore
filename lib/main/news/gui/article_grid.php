<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package news
 * @subpackage gui
 * @version 3.4.0
 * @since 2.4.0
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

This file is part of earthli News.

earthli News is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli News is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli News; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli News, visit:

http://earthli.com/software/webcore/app_news.php

****************************************************************************/

/** */
require_once ('webcore/gui/entry_grid.php');

/**
 * Base rendering for {@link ARTICLE}s from a {@link QUERY}.
 * @package news
 * @subpackage gui
 * @version 3.4.0
 * @since 2.7.1
 */
class BASE_ARTICLE_GRID extends CONTENT_OBJECT_GRID
{
  /**
   * @var string
   */
  public $object_name = 'Article';

  /**
   * @var boolean
   */
  public $show_folder = false;

  /**
   * @var boolean
   */
  public $show_user = true;

  /**
   * @var integer
   */
  public $chars_to_show_for_hidden = 200;  
  /**
   * @var integer
   */
  public $chars_to_show_for_visible = 0;

  /**
   * @var boolean
   */
  public $show_description = false;

  /**
   * @param DRAFTABLE_ENTRY $obj
   */
  protected function _draw_box ($obj)
  {
    $folder = $obj->parent_folder ();
?>
  <div class="grid-item">
    <div class="minimal-commands">
      <?php $this->_draw_menu_for ($obj, Menu_size_minimal, Menu_align_inline); ?>
    </div>
    <div class="minimal-commands-content">
      <?php
        if ($this->show_folder && $folder->icon_url)
        {
      ?>
      <div style="float: left; padding-right: 5px">
        <?php echo $folder->icon_as_html (); ?>
      </div>
      <?php
        }
      ?>
      <h3>
        <?php echo $this->obj_link ($obj); ?>
      </h3>
      <div class="info-box-top">
        <?php
          if ($this->show_user)
          {
            if ($obj->unpublished ())
            {
              $user = $obj->creator ();
              echo 'Created by ' . $user->title_as_link () . ' on ';
            }
            else
            {
              $user = $obj->publisher ();
              echo 'Published by ' . $user->title_as_link () . ' on ';
            }
          }
          else
          {
            if ($obj->unpublished ())
            {
              echo 'Created on ';
            }
            else
            {
              echo 'Published on ';
            }
          }

          if ($obj->unpublished ())
          {
            $time = $obj->time_created;
          }
          else
          {
            $time = $obj->time_published;
          }

          echo $time->format ();

          if ($this->show_folder)
          {
            echo ' in ' . $folder->title_as_link ();
          }
      ?>
      </div>
      <?php
      if ($this->show_description)
      {
        ?>
        <div class="text-flow">
          <?php
          $munger = $obj->html_formatter ();
          if ($obj->invisible ())
          {
            $munger->max_visible_output_chars = $this->chars_to_show_for_hidden;
          }
          else
          {
            $munger->max_visible_output_chars = $this->chars_to_show_for_visible;
          }

          echo $obj->description_as_html ($munger);
          ?>
        </div>
    </div>
  </div>
  <?php
    }
  }  
}

/**
 * Display {@link ARTICLE}s from a {@link QUERY}.
 * @package news
 * @subpackage gui
 * @version 3.4.0
 * @since 2.4.0
 */
class ARTICLE_GRID extends BASE_ARTICLE_GRID
{
  /**
   * @var boolean
   */
  public $show_description = true;

  /**
   * @var boolean
   */
  public $fuzzy_dates = true;
  
  /**
   * @var boolean
   */
  public $group_by_time = true;

  /**
   * @param DRAFTABLE_ENTRY $obj
   */
  protected function _start_row ($obj)
  {
  	if ($this->group_by_time)
  	{
	    $curr_date = $obj->time_published;
	    if (! $curr_date->is_valid ())
	    {
	      $curr_date = $obj->time_modified;
	    }
	      
	    $now = new DATE_TIME ();
	    
	    if ($curr_date->equals ($now, Date_time_date_part))
	    {
	      $interval_text = 'Today';
	    }
	    else
	    {
	      $yesterday = new DATE_TIME (time () - 86400);
	      if ($curr_date->equals ($yesterday, Date_time_date_part))
	      {
	        $interval_text = 'Yesterday'; 
	      }
	      else
	      {
	        $two_days_ago = new DATE_TIME (time () - (86400 * 2));
	        if ($curr_date->equals ($two_days_ago, Date_time_date_part))
	        {
	          $interval_text = 'Two Days Ago';
	        }
	        else
	        {
	          if ($this->fuzzy_dates)
	          {
	            $interval = $now->diff ($curr_date);
	            $interval_text = $interval->format (1) . ' Ago';
	          }
	          else
	          {
	            $t = $curr_date->formatter ();
	            $t->type = Date_time_format_date_only;
	            $interval_text = $curr_date->format ($t);
	          }
	        }
	      }
	    }
	
	    $dates_are_different = empty ($this->last_interval) || $this->last_interval != $interval_text;
	      
	    if ($dates_are_different)
	    {
	      $this->last_interval = $interval_text;
	      $this->last_date = $curr_date;
	?>
	  <tr>
	    <?php if ($this->items_are_selectable) { ?>
	    <td></td>
	    <?php } ?>
	    <td class="object-in-list">
	      <h3>
	        <?php echo $interval_text; ?>
	      </h3>
	    </td>
	  </tr>
	  <tr><td>&nbsp;</td></tr>
	<?php
	    }
	  }

    parent::_start_row ($obj);
  }

  /**
   * @var string
   * @access private
   */
  public $last_interval;
}

/**
 * Display {@link ARTICLE}s from a {@link QUERY}.
 * @package news
 * @subpackage gui
 * @version 3.4.0
 * @since 2.4.0
 */
class ARTICLE_SUMMARY_GRID extends DRAFTABLE_ENTRY_SUMMARY_GRID
{
  /**
   * @var string
   */
  public $object_name = 'Article';
}

?>