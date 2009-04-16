<?php

/**
 * WebCore Testsuite Component.
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.1.0
 * @since 2.7.0
 * @access private
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
require_once ('webcore/tests/test_task.php');
require_once ('webcore/sys/date_time.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.1.0
 * @since 2.7.0
 * @access private
 */
class DATE_TIME_TEST_TASK extends TEST_TASK
{
  protected function _execute_all ()
  {
    $from_date = $this->context->make_date_time ('2005-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2005-01-13 12:00:00');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('1 week 5 days', $diff->format ());
    $this->_check_equal ('1w 5d', $diff->format (2, false));
    $this->_check_equal ('2 weeks', $diff->format (1));
    $this->_check_equal ('1.7 weeks', $diff->format (1, true, 1));
    $this->_check_equal ('1.71 weeks', $diff->format (1, true, 2));
    
    $from_date = $this->context->make_date_time ('2005-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2005-01-08 12:00:00');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('1 week', $diff->format ());
    $this->_check_equal ('1w', $diff->format (2, false));
    $this->_check_equal ('1 week', $diff->format (1));    
    $this->_check_equal ('1 week', $diff->format (1, true, 1));
    $this->_check_equal ('1 week', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2005-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2005-01-01 12:00:00');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('0 seconds', $diff->format ());
    $this->_check_equal ('0s', $diff->format (2, false));
    $this->_check_equal ('0 seconds', $diff->format (1));    
    $this->_check_equal ('0 seconds', $diff->format (1, true, 1));
    $this->_check_equal ('0 seconds', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2005-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2005-01-01 12:01:29');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('1 minute 29 seconds', $diff->format ());
    $this->_check_equal ('1m 29s', $diff->format (2, false));
    $this->_check_equal ('1 minute', $diff->format (1));    
    $this->_check_equal ('1.5 minutes', $diff->format (1, true, 1));
    $this->_check_equal ('1.48 minutes', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2005-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2005-01-01 12:01:30');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('1 minute 30 seconds', $diff->format ());
    $this->_check_equal ('1m 30s', $diff->format (2, false));
    $this->_check_equal ('2 minutes', $diff->format (1));    
    $this->_check_equal ('1.5 minutes', $diff->format (1, true, 1));
    $this->_check_equal ('1.5 minutes', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2005-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2006-03-19 16:01:43');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('1 year 2 months 3 weeks 1 day 4 hours 1 minute 43 seconds', $diff->format (7));
    $this->_check_equal ('1 year 2 months 3 weeks 1 day 4 hours 2 minutes', $diff->format (6));
    $this->_check_equal ('1 year 2 months 3 weeks 1 day 4 hours', $diff->format (5));
    $this->_check_equal ('1 year 2 months 3 weeks 1 day', $diff->format (4));
    $this->_check_equal ('1 year 2 months 3 weeks', $diff->format (3));
    $this->_check_equal ('1 year 3 months', $diff->format (2));
    $this->_check_equal ('1 year', $diff->format (1));
    $this->_check_equal ('1.2 years', $diff->format (1, true, 1));
    $this->_check_equal ('1.23 years', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2005-01-01 02:00:00');
    $to_date = $this->context->make_date_time ('2006-06-05 14:29:30');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('1y 5m 1w 3d 11h 29m 30s', $diff->format (7, false));
    $this->_check_equal ('1y 5m 1w 3d 11h 30m', $diff->format (6, false));
    $this->_check_equal ('1y 5m 1w 3d 12h', $diff->format (5, false));
    $this->_check_equal ('1y 5m 1w 4d', $diff->format (4, false));
    $this->_check_equal ('1y 5m 2w', $diff->format (3, false));
    $this->_check_equal ('1y 6m', $diff->format (2, false));
    $this->_check_equal ('2y', $diff->format (1, false));
    $this->_check_equal ('1.5 years', $diff->format (1, true, 1));
    $this->_check_equal ('1.45 years', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2005-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2005-01-01 13:00:01');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('1 hour', $diff->format ());
    $this->_check_equal ('1h', $diff->format (2, false));
    $this->_check_equal ('1 hour', $diff->format (1));    
    $this->_check_equal ('1 hour', $diff->format (1, true, 1));
    $this->_check_equal ('1 hour', $diff->format (1, true, 2));

    // Note the two tests below show the logical errors that arise
    // when forcing 4 week months (acceptable for most cases).
    
    $from_date = $this->context->make_date_time ('2005-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2006-01-01 12:00:01');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('1 year', $diff->format (7));
    $this->_check_equal ('1 year', $diff->format ());
    $this->_check_equal ('1y', $diff->format (2, false));
    $this->_check_equal ('1 year', $diff->format (1));    
    $this->_check_equal ('1 year', $diff->format (1, true, 1));
    $this->_check_equal ('1.02 years', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2005-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2005-12-27 12:00:01');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('1 year', $diff->format (7));
    $this->_check_equal ('1 year', $diff->format ());
    $this->_check_equal ('1y', $diff->format (2, false));
    $this->_check_equal ('1 year', $diff->format (1));
    $this->_check_equal ('1 year', $diff->format (1, true, 1));
    $this->_check_equal ('1 year', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2005-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2005-01-01 14:00:01');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('2 hours', $diff->format (7));
    $this->_check_equal ('2 hours', $diff->format ());
    $this->_check_equal ('2h', $diff->format (2, false));
    $this->_check_equal ('2 hours', $diff->format (1));
    $this->_check_equal ('2 hours', $diff->format (1, true, 1));
    $this->_check_equal ('2 hours', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2005-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2005-01-01 12:02:01');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('2 minutes 1 second', $diff->format (7));
    $this->_check_equal ('2 minutes 1 second', $diff->format ());
    $this->_check_equal ('2m 1s', $diff->format (2, false));
    $this->_check_equal ('2 minutes', $diff->format (1));
    $this->_check_equal ('2 minutes', $diff->format (1, true, 1));
    $this->_check_equal ('2.02 minutes', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2006-01-01 12:00:00');
    $to_date = $this->context->make_date_time ('2005-01-01 12:02:01');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('0 seconds', $diff->format (7));
    $this->_check_equal ('0 seconds', $diff->format ());
    $this->_check_equal ('0s', $diff->format (2, false));
    $this->_check_equal ('0 seconds', $diff->format (1));
    $this->_check_equal ('0 seconds', $diff->format (1, true, 1));
    $this->_check_equal ('0 seconds', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2005-01-01 00:00:00');
    $to_date = $this->context->make_date_time ('2005-01-02 11:30:05');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('1 day 11 hours 30 minutes 5 seconds', $diff->format (7));
    $this->_check_equal ('1 day 12 hours', $diff->format ());
    $this->_check_equal ('1d 12h', $diff->format (2, false));
    $this->_check_equal ('2 days', $diff->format (1));
    $this->_check_equal ('1.5 days', $diff->format (1, true, 1));
    $this->_check_equal ('1.48 days', $diff->format (1, true, 2));

    $from_date = $this->context->make_date_time ('2005-01-01 00:00:00');
    $to_date = $this->context->make_date_time ('2005-01-14 14:30:05');

    $diff = $to_date->diff ($from_date);

    $this->_check_equal ('1 week 6 days 14 hours 30 minutes 5 seconds', $diff->format (7));
    $this->_check_equal ('2 weeks', $diff->format ());
    $this->_check_equal ('2w', $diff->format (2, false));
    $this->_check_equal ('2 weeks', $diff->format (1));
    $this->_check_equal ('1.9 weeks', $diff->format (1, true, 1));
    $this->_check_equal ('1.94 weeks', $diff->format (1, true, 2));
  }
  
  protected function _execute_unassigned_tests ()
  {
    $from_date = $this->context->make_date_time ('2005-01-01 00:00:00');
    $from_date->clear ();
    
    $this->_check_equal (Date_time_unassigned, $from_date->as_iso ());
    $this->_check_equal (Date_time_unassigned, $from_date->as_php ());
    
    $from_date->set_from_php(Date_time_unassigned);

    $this->_check_equal (Date_time_unassigned, $from_date->as_iso ());
    $this->_check_equal (Date_time_unassigned, $from_date->as_php ());
    
    $from_date->set_from_iso(Date_time_unassigned);

    $this->_check_equal (Date_time_unassigned, $from_date->as_iso ());
    $this->_check_equal (Date_time_unassigned, $from_date->as_php ());

    $from_date->set_from_iso('0000-00-00 00:00:00');

    $this->_check_equal (Date_time_unassigned, $from_date->as_iso ());
    $this->_check_equal (Date_time_unassigned, $from_date->as_php ());

    $from_date->set_from_iso(null);

    $this->_check_equal (Date_time_unassigned, $from_date->as_iso ());
    $this->_check_equal (Date_time_unassigned, $from_date->as_php ());
  }
  
  protected function _execute_converter_tests ()
  {
    $php_time = mktime(12, 10, 01, 5, 4, 2007);
    $php_time_no_time = mktime(0, 0, 0, 5, 4, 2007);
    $php_time_no_date = mktime(12, 10, 01, date('m'), date('d'), date('Y'));
    $date = $this->context->make_date_time();
    $no_date = $this->context->make_date_time($php_time_no_date)->as_iso();

    $date->set_from_text ('', Date_time_both_parts);
    
    $this->_check_equal(Date_time_unassigned, $date->as_iso ());
    $this->_check_equal(Date_time_unassigned, $date->as_php ());

    // Check setting both date and time
    
    $date->set_from_text ('2007-05-04 12:10:01', Date_time_both_parts);
    
    $this->_check_equal('2007-05-04 12:10:01', $date->as_iso ());
    $this->_check_equal($php_time, $date->as_php ());

    $date->set_from_text ('05/04/2007 12:10:01', Date_time_both_parts);
    
    $this->_check_equal('2007-05-04 12:10:01', $date->as_iso ());
    $this->_check_equal($php_time, $date->as_php ());

    $date->set_from_text ('04.05.2007 12:10:01', Date_time_both_parts);
    
    $this->_check_equal('2007-05-04 12:10:01', $date->as_iso ());
    $this->_check_equal($php_time, $date->as_php ());
    
    $date->set_from_text ('2007:05:04 12:10:01', Date_time_both_parts);
    
    $this->_check_equal('2007-05-04 12:10:01', $date->as_iso ());
    $this->_check_equal($php_time, $date->as_php ());

    // Check setting only the date
    
    $date->set_from_text ('2007-05-04 14:20:02', Date_time_date_part);
    
    $this->_check_equal('2007-05-04 00:00:00', $date->as_iso ());
    $this->_check_equal($php_time_no_time, $date->as_php ());

    $date->set_from_text ('05/04/2007 14:20:02', Date_time_date_part);
    
    $this->_check_equal('2007-05-04 00:00:00', $date->as_iso ());
    $this->_check_equal($php_time_no_time, $date->as_php ());

    $date->set_from_text ('04.05.2007 14:20:02', Date_time_date_part);
    
    $this->_check_equal('2007-05-04 00:00:00', $date->as_iso ());
    $this->_check_equal($php_time_no_time, $date->as_php ());
    
    $date->set_from_text ('2007:05:04 14:20:02', Date_time_date_part);
    
    $this->_check_equal('2007-05-04 00:00:00', $date->as_iso ());
    $this->_check_equal($php_time_no_time, $date->as_php ());

    // Check setting only the time
    
    $date->set_from_text ('2007-06-07 12:10:01', Date_time_time_part);
    
    $this->_check_equal($no_date, $date->as_iso ());
    $this->_check_equal($php_time_no_date, $date->as_php ());

    $date->set_from_text ('06/07/2007 12:10:01', Date_time_time_part);
    
    $this->_check_equal($no_date, $date->as_iso ());
    $this->_check_equal($php_time_no_date, $date->as_php ());

    $date->set_from_text ('07.06.2007 12:10:01', Date_time_time_part);
    
    $this->_check_equal($no_date, $date->as_iso ());
    $this->_check_equal($php_time_no_date, $date->as_php ());
    
    $date->set_from_text ('2007:06:07 12:10:01', Date_time_time_part);
    
    $this->_check_equal($no_date, $date->as_iso ());
    $this->_check_equal($php_time_no_date, $date->as_php ());
    
  }
  
  protected function _execute_testbed ()
  {
  }
  
  protected function _execute ()
  {
    $this->context->display_options->show_local_times = false;
    $this->context->date_time_toolkit->formatter->show_CSS = false;

    $this->_execute_all ();
    $this->_execute_testbed ();
    $this->_execute_unassigned_tests ();
    $this->_execute_converter_tests ();
  }
}

?>