<?php

  /**
   * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
   * @author Marco Von Ballmoos
   * @filesource
   * @package news
   * @subpackage gui
   * @version 3.5.0
   * @since 2.4.0
   */

  /****************************************************************************
   *
   * Copyright (c) 2002-2014 Marco Von Ballmoos
   *
   * This file is part of earthli News.
   *
   * earthli News is free software; you can redistribute it and/or modify
   * it under the terms of the GNU General Public License as published by
   * the Free Software Foundation; either version 2 of the License, or
   * (at your option) any later version.
   *
   * earthli News is distributed in the hope that it will be useful,
   * but WITHOUT ANY WARRANTY; without even the implied warranty of
   * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   * GNU General Public License for more details.
   *
   * You should have received a copy of the GNU General Public License
   * along with earthli News; if not, write to the Free Software
   * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
   *
   * For more information about the earthli News, visit:
   *
   * http://earthli.com/software/webcore/app_news.php
   ****************************************************************************/

  /** */
  require_once('webcore/gui/entry_grid.php');

  /**
   * Base rendering for {@link ARTICLE}s from a {@link QUERY}.
   * @package news
   * @subpackage gui
   * @version 3.5.0
   * @since 2.7.1
   */
  class BASE_ARTICLE_GRID extends CONTENT_OBJECT_GRID
  {
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
    protected function _draw_box($obj)
    {
      $folder = $obj->parent_folder();
      $this->_display_start_minimal_commands_block($obj);
      if ($this->show_folder && $folder->icon_url)
      {
        $this->context->start_icon_container($folder->icon_url,Thirty_two_px);
      }
      ?>
      <h3>
        <?php echo $this->obj_link($obj); ?>
      </h3>
      <?php
      if ($this->show_folder && $folder->icon_url)
      {
        $this->context->finish_icon_container();
      }
      ?>
      <p class="info-box-top">
        <?php
          if ($this->show_user)
          {
            if ($obj->unpublished())
            {
              $user = $obj->creator();
              echo 'Created by ' . $user->title_as_link() . ' on ';
            }
            else
            {
              $user = $obj->publisher();
              echo 'Published by ' . $user->title_as_link() . ' on ';
            }
          }
          else
          {
            if ($obj->unpublished())
            {
              echo 'Created on ';
            }
            else
            {
              echo 'Published on ';
            }
          }

          if ($obj->unpublished())
          {
            $time = $obj->time_created;
          }
          else
          {
            $time = $obj->time_published;
          }

          echo $time->format();

          if ($this->show_folder)
          {
            echo ' in ' . $folder->title_as_link();
          }
        ?>
      </p>
      <?php
      if ($this->show_description)
      {
        ?>
        <div class="text-flow">
          <?php
            $munger = $obj->html_formatter();
            if ($obj->invisible())
            {
              $munger->max_visible_output_chars = $this->chars_to_show_for_hidden;
            }
            else
            {
              $munger->max_visible_output_chars = $this->chars_to_show_for_visible;
            }

            echo $obj->description_as_html($munger);
          ?>
        </div>
        <?php
      }
      $this->_display_finish_minimal_commands_block();
    }
  }

  /**
   * Display {@link ARTICLE}s from a {@link QUERY}.
   * @package news
   * @subpackage gui
   * @version 3.5.0
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
    protected function _start_row($obj)
    {
      if ($this->group_by_time)
      {
        $curr_date = $obj->time_published;
        if (!$curr_date->is_valid())
        {
          $curr_date = $obj->time_modified;
        }

        $now = new DATE_TIME ();

        if ($curr_date->equals($now, Date_time_date_part))
        {
          $interval_text = 'Today';
        }
        else
        {
          $yesterday = new DATE_TIME (time() - 86400);
          if ($curr_date->equals($yesterday, Date_time_date_part))
          {
            $interval_text = 'Yesterday';
          }
          else
          {
            $two_days_ago = new DATE_TIME (time() - (86400 * 2));
            if ($curr_date->equals($two_days_ago, Date_time_date_part))
            {
              $interval_text = 'Two Days Ago';
            }
            else
            {
              if ($this->fuzzy_dates)
              {
                $interval = $now->diff($curr_date);
                $interval_text = $interval->format(1) . ' Ago';
              }
              else
              {
                $t = $curr_date->formatter();
                $t->type = Date_time_format_date_only;
                $interval_text = $curr_date->format($t);
              }
            }
          }
        }

        $dates_are_different = empty ($this->last_interval) || $this->last_interval != $interval_text;

        if ($dates_are_different)
        {
          $this->last_interval = $interval_text;
          $this->last_date = $curr_date;

          $this->_internal_start_row();
          if ($this->items_are_selectable)
          {
            $this->_internal_start_cell();
            $this->_internal_finish_cell();
          }
          $this->_internal_start_cell();
          echo '<h2>';
          echo $interval_text;
          echo '</h2>';
          $this->_internal_finish_cell();
          $this->_internal_finish_row();
        }
      }

      parent::_start_row($obj);
    }

    /**
     * @var string
     * @access private
     */
    public $last_interval;

    /**
     * @var DATE_TIME
     * @access private */
    public $last_date;
  }

  /**
   * Display {@link ARTICLE}s from a {@link QUERY}.
   * @package news
   * @subpackage gui
   * @version 3.5.0
   * @since 2.4.0
   */
  class ARTICLE_SUMMARY_GRID extends DRAFTABLE_ENTRY_SUMMARY_GRID
  {
  }

?>