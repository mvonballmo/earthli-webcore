<?php

include_once ('webcore/gui/basic_calendar.php');

class HOCHZEIT_CALENDAR extends BASIC_CALENDAR
{
  function HOCHZEIT_CALENDAR (&$context)
  {
    BASIC_CALENDAR::BASIC_CALENDAR ($context);

    include_once ('webcore/sys/date_time.php');

    $this->set_ranges (new DATE_TIME ('2002-07-08 00:00:00', Date_time_iso),
                       new DATE_TIME ('2002-10-17 23:59:59', Date_time_iso));

    $class_name = $this->context->final_class_name ('INVITEE_QUERY', 'hochzeit/db/invitee_query.php');
    $q = new $class_name ($context);

    for ($i = 1; $i <= 31; $i++)
    {
      $q->set_days ("2002-08-$i 00:00:00", "2002-08-$i 23:59:59");
      $size = $q->size ();

      if ($i < 10)
        $day = "0$i";
      else
        $day = $i;

      if ($size)
      {
        if ($this->is_late (8, $day))
          $this->_events ["2002-8-$i"] [] = "<a href=\"./?panel=guest_list&amp;day=08-$day&amp;sort_by_date=1\"><span class=\"error\">$size guest(s)</span></a>";
        else
          $this->_events ["2002-8-$i"] [] = "<a href=\"./?panel=guest_list&amp;day=08-$day&amp;sort_by_date=1\">$size guest(s)</a>";
      }
    }

    $this->_events ['2002-7-13'] [] = 'Camping in Maine';
    $this->_events ['2002-7-14'] [] = 'Camping in Maine';
    $this->_events ['2002-7-15'] [] = 'Camping in Maine';
    $this->_events ['2002-7-16'] [] = 'Camping in Maine';
    $this->_events ['2002-7-17'] [] = 'Camping in Maine';
    $this->_events ['2002-7-18'] [] = 'Camping in Maine';
    $this->_events ['2002-7-19'] [] = 'Camping in Maine';
    $this->_events ['2002-7-20'] [] = 'Camping in Maine';

    $this->_events ['2002-8-1'] [] = 'Send out Invitations';
    $this->_events ['2002-8-21'] [] = '<span class="error">R.S.V.P. Deadline</span>';

    $this->_events ['2002-8-30'] [] = 'Last Day at Logicat';

    $this->_events ['2002-9-14'] [] = '<a href="index.php?panel=reception">Wedding Reception</a>';
    $this->_events ['2002-9-15'] [] = '<a href="index.php?panel=picnic">Picnic</a>';

    $this->_events ['2002-9-17'] [] = 'Bermuda Honeymoon';
    $this->_events ['2002-9-18'] [] = 'Bermuda Honeymoon';
    $this->_events ['2002-9-19'] [] = 'Bermuda Honeymoon';
    $this->_events ['2002-9-20'] [] = 'Bermuda Honeymoon';
    $this->_events ['2002-9-21'] [] = 'Bermuda Honeymoon';
    $this->_events ['2002-9-22'] [] = 'Bermuda Honeymoon';
    $this->_events ['2002-9-23'] [] = 'Bermuda Honeymoon';
    $this->_events ['2002-9-24'] [] = 'Bermuda Honeymoon';

    $this->_events ['2002-10-17'] [] = 'Move to Switzerland';

  }

  function month_has_content ($month, $year)
  {
    return TRUE; // don't skip any months here
  }

  function is_late ($month, $day)
  {
    return $day > 21;
  }

  /**
   * Render the actual content.
    * Table cell for the day is already created.
    * @param integer $day
    * @param integer $week
    * @param integer $month
    * @param integer $year
    * @access private
    */
  function _get_content_for_day ($day, $week, $month, $year)
  {
    if (isset ($this->_events ["$year-$month-$day"]))
    {
      foreach ($this->_events ["$year-$month-$day"] as $event)
        echo "<div style=\"text-align: center\">$event</div>";
    }
  }
}

?>