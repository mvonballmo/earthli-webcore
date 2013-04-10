<?php

/**
 * @copyright Copyright (c) 2002-2013 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.4.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2013 Marco Von Ballmoos

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
require_once ('webcore/obj/webcore_object.php');

/**
 * Show only the most recently-created objects.
  * Exact number of objects to retrieve is adjusted via the 'num_in_recent' option.
  * @see TIME_FRAME_SELECTOR::$num_in_recent
  */
define ('Time_frame_recent', 'recent');
/**
 * Show only objects created today.
 */
define ('Time_frame_today', 'today');
/**
 * Show only objects created in the last week.
 */
define ('Time_frame_last_week', 'last_week');
/**
 * Show only objects created in the last month.
 */
define ('Time_frame_last_month', 'last_month');
/**
 * Show all objects.
 */
define ('Time_frame_all', 'all');

/**
 * Selects different set of objects based on time.
 * Manages a query object, restricting it to the selected 'time frame'. Displays the
 * available time frames as a {@link MENU}.
 * @package webcore
 * @subpackage gui
 * @version 3.4.0
 * @since 2.2.1
 */
class TIME_FRAME_SELECTOR extends WEBCORE_OBJECT
{
  /**
   * Direct user selections to this page.
   * @var string
   */
  public $page_link;

  /**
   * Number of objects to show in the 'recent' time frame.
   * @var integer
   */
  public $num_in_recent = 10;

  /**
   * Selected time period to which to restrict.
   * This is applied to the query in {@link prepare_query()} and shown as
   * selected in {@link display()}. Set to {@link Time_frame_recent} on
   * creation if no override in the page request is found (under the variable )
   * "time_frame"). Value can also be {@link Time_frame_today}, {@link
   * Time_frame_last_week}, {@link Time_frame_last_month} or {@link
   * Time_frame_all}.
   */
  public $period;

  /**
   * @param APPLICATION $app Main application.
   * @param integer $default Default time frame to show.
   * @see Time_frame_recent
   */
  public function __construct ($app, $default = Time_frame_recent)
  {
    parent::__construct ($app);
    $this->load_period_from_request ($default);
    $this->page_link = $app->env->url (Url_part_no_host_path);
  }

  /**
   * Render the time frame choices.
   */
  public function display ()
  {
    $this->assert (! empty ($this->page_link), 'Page name cannot be empty.', 'display', 'TIME_FRAME_SELECTOR');
    $menu = $this->context->make_menu ();
    $menu->renderer->separator_class = $this->context->display_options->menu_class;

    $url = new URL ($this->page_link);
    $url->replace_argument ('time_frame', Time_frame_recent);
    $menu->append ('Recent', $url->as_text (), '', $this->period == Time_frame_recent);

    $url->replace_argument ('time_frame', Time_frame_all);
    $menu->append ('All', $url->as_text (), '', $this->period == Time_frame_all);

    $url->replace_argument ('time_frame', Time_frame_today);
    $menu->append ('Today', $url->as_text (), '', $this->period == Time_frame_today);

    $url->replace_argument ('time_frame', Time_frame_last_week);
    $menu->append ('Last Week', $url->as_text (), '', $this->period == Time_frame_last_week);

    $url->replace_argument ('time_frame', Time_frame_last_month);
    $menu->append ('Last Month', $url->as_text (), '', $this->period == Time_frame_last_month);

    $menu->display ();
  }

  /**
   * Restrict the query to the time frame.
   * @param QUERY $query
   */
  public function prepare_query ($query)
  {
    if (! $query)
    {
      $this->raise ("'query' cannot be empty", 'prepare_query', 'TIME_FRAME_SELECTOR');
    }

    switch ($this->period)
    {
    case Time_frame_recent:
      $query->set_limits (0, $this->num_in_recent);
      break;
    case Time_frame_today:
      $first = new DATE_TIME (mktime (0, 0, 0, date ('n'), date ('d'), date ('Y')));
      $last = new DATE_TIME (time ());
      $query->set_days ($first->as_iso (), $last->as_iso ());
      break;
    case Time_frame_last_week:
      $now = time ();
      $last = new DATE_TIME ($now);
      $first = new DATE_TIME ($now - (86400 * 7));
      $query->set_days ($first->as_iso (), $last->as_iso ());
      break;
    case Time_frame_last_month:
      $now = time ();
      $last = new DATE_TIME ($now);
      $first = new DATE_TIME ($now - (86400 * 30));
      $query->set_days ($first->as_iso (), $last->as_iso ());
      break;
    case Time_frame_all:
      break;
    }
    
    if ($this->period != Time_frame_all)    
      $query->order_by_recent ();
  }

  /**
   * Load the initial time frame.
   * Uses the default if there is no time frame in the page request.
   * @param string $default
   * @access private
   */
  public function load_period_from_request ($default)
  {
    $this->period = read_var ('time_frame', $default);
  }
}

?>