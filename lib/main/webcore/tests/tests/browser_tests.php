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
require_once ('webcore/util/browser.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.1.0
 * @since 2.7.0
 * @access private
 */
class BROWSER_TEST_TASK extends TEST_TASK
{
  protected function _execute ()
  {
    $this->_test_regular_clients ();
    $this->_test_bots ();
    $this->_test_newsreaders ();
    $this->_test_bed ();

    $browser = $this->env->browser ();
    $browser->load_from_server ();
  }

  protected function _test_regular_clients ()
  {
    $browser = $this->env->browser ();

    $browser->load_from_string ('Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)');

          $this->_check_equal ('Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Mozilla', $browser->name ());
          $this->_check_equal ('5.0', $browser->version ());
          $this->_check_equal ('Gecko', $browser->renderer_name ());
          $this->_check_equal ('5.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');
          $this->_check (!$browser->is (Browser_previewer), "Browser should not be a previewer.");
          
    $browser->load_from_string ('facebookexternalhit/1.0 (+http://www.facebook.com/externalhit_uatext.php)');

          $this->_check_equal ('facebookexternalhit/1.0 (+http://www.facebook.com/externalhit_uatext.php)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Facebook Preview', $browser->name ());
          $this->_check_equal ('1.0', $browser->version ());
          $this->_check_equal ('Facebook Preview', $browser->renderer_name ());
          $this->_check_equal ('1.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');
          $this->_check ($browser->is (Browser_previewer), "Browser should be a previewer.");
          
    $browser->load_from_string ('Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-US) AppleWebKit/125.4 (KHTML, like Gecko, Safari) OmniWeb/v563.22');

          $this->_check_equal ('Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-US) AppleWebKit/125.4 (KHTML, like Gecko, Safari) OmniWeb/v563.22', $browser->user_agent_string);
          $this->_check_equal ('Mac OS X', $browser->system_id ());
          $this->_check_equal ('Mac OS X', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('OmniWeb', $browser->name ());
          $this->_check_equal ('563.22', $browser->version ());
          $this->_check_equal ('Webcore', $browser->renderer_name ());
          $this->_check_equal ('125.4', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/5.0 (Windows; U; Windows NT 5.0; rv:1.7.3) Gecko/20040913 Firefox/0.10.1');

          $this->_check_equal ('Mozilla/5.0 (Windows; U; Windows NT 5.0; rv:1.7.3) Gecko/20040913 Firefox/0.10.1', $browser->user_agent_string);
          $this->_check_equal ('Windows NT 5.0 (Windows 2000)', $browser->system_id ());
          $this->_check_equal ('Windows 2000', $browser->calculated_system_name ());
          $this->_check_equal ('Windows NT', $browser->system_name ());
          $this->_check_equal ('5.0', $browser->system_version ());
          $this->_check_equal ('Firefox', $browser->name ());
          $this->_check_equal ('0.10.1', $browser->version ());
          $this->_check_equal ('Gecko', $browser->renderer_name ());
          $this->_check_equal ('1.7.3', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check_equal ('2004-09-13 00:00:00', $date->as_iso ());
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Opera/7.60 (Windows NT 5.0; U; en)');

          $this->_check_equal ('Opera/7.60 (Windows NT 5.0; U; en)', $browser->user_agent_string);
          $this->_check_equal ('Windows NT 5.0 (Windows 2000)', $browser->system_id ());
          $this->_check_equal ('Windows 2000', $browser->calculated_system_name ());
          $this->_check_equal ('Windows NT', $browser->system_name ());
          $this->_check_equal ('5.0', $browser->system_version ());
          $this->_check_equal ('Opera', $browser->name ());
          $this->_check_equal ('7.60', $browser->version ());
          $this->_check_equal ('Presto (Opera)', $browser->renderer_name ());
          $this->_check_equal ('7.60', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 7.60');

          $this->_check_equal ('Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 7.60', $browser->user_agent_string);
          $this->_check_equal ('Windows NT 5.1 (Windows XP)', $browser->system_id ());
          $this->_check_equal ('Windows XP', $browser->calculated_system_name ());
          $this->_check_equal ('Windows NT', $browser->system_name ());
          $this->_check_equal ('5.1', $browser->system_version ());
          $this->_check_equal ('Opera', $browser->name ());
          $this->_check_equal ('7.60', $browser->version ());
          $this->_check_equal ('Presto (Opera)', $browser->renderer_name ());
          $this->_check_equal ('7.60', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/5.0 (Windows NT 5.0; U; en) Opera 7.60');

          $this->_check_equal ('Mozilla/5.0 (Windows NT 5.0; U; en) Opera 7.60', $browser->user_agent_string);
          $this->_check_equal ('Windows NT 5.0 (Windows 2000)', $browser->system_id ());
          $this->_check_equal ('Windows 2000', $browser->calculated_system_name ());
          $this->_check_equal ('Windows NT', $browser->system_name ());
          $this->_check_equal ('5.0', $browser->system_version ());
          $this->_check_equal ('Opera', $browser->name ());
          $this->_check_equal ('7.60', $browser->version ());
          $this->_check_equal ('Presto (Opera)', $browser->renderer_name ());
          $this->_check_equal ('7.60', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/4.0 (compatible; MSIE 5.5; AOL 9.0; Windows 98; Win 9x 4.90)');

          $this->_check_equal ('Mozilla/4.0 (compatible; MSIE 5.5; AOL 9.0; Windows 98; Win 9x 4.90)', $browser->user_agent_string);
          $this->_check_equal ('Windows 9x 4.90 (Windows 98)', $browser->system_id ());
          $this->_check_equal ('Windows 98', $browser->calculated_system_name ());
          $this->_check_equal ('Windows 9x', $browser->system_name ());
          $this->_check_equal ('4.90', $browser->system_version ());
          $this->_check_equal ('AOL', $browser->name ());
          $this->_check_equal ('9.0', $browser->version ());
          $this->_check_equal ('Trident (IE)', $browser->renderer_name ());
          $this->_check_equal ('5.5', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; .NET CLR 1.1.4322)');

          $this->_check_equal ('Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; .NET CLR 1.1.4322)', $browser->user_agent_string);
          $this->_check_equal ('Windows NT 5.0 (Windows 2000)', $browser->system_id ());
          $this->_check_equal ('Windows 2000', $browser->calculated_system_name ());
          $this->_check_equal ('Windows NT', $browser->system_name ());
          $this->_check_equal ('5.0', $browser->system_version ());
          $this->_check_equal ('Internet Explorer', $browser->name ());
          $this->_check_equal ('6.0', $browser->version ());
          $this->_check_equal ('Trident (IE)', $browser->renderer_name ());
          $this->_check_equal ('6.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Lynx/2.8.4rel.1 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/0.9.6c (human-guided@lerly.net)');

          $this->_check_equal ('Lynx/2.8.4rel.1 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/0.9.6c (human-guided@lerly.net)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Lynx', $browser->name ());
          $this->_check_equal ('2.8.4rel.1', $browser->version ());
          $this->_check_equal ('Text', $browser->renderer_name ());
          $this->_check_equal ('2.8.4rel.1', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2b) Gecko/20021007 Phoenix/0.3');

          $this->_check_equal ('Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2b) Gecko/20021007 Phoenix/0.3', $browser->user_agent_string);
          $this->_check_equal ('Linux', $browser->system_id ());
          $this->_check_equal ('Linux', $browser->calculated_system_name ());
          $this->_check_equal ('Linux', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Phoenix', $browser->name ());
          $this->_check_equal ('0.3', $browser->version ());
          $this->_check_equal ('Gecko', $browser->renderer_name ());
          $this->_check_equal ('1.2b', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check_equal ('2002-10-07 00:00:00', $date->as_iso ());
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('curl/7.7.1 (i386--freebsd4.3) libcurl 7.7.1 (SSL 0.9.6) (ipv6 enabled)');

          $this->_check_equal ('curl/7.7.1 (i386--freebsd4.3) libcurl 7.7.1 (SSL 0.9.6) (ipv6 enabled)', $browser->user_agent_string);
          $this->_check_equal ('FreeBSD 4.3', $browser->system_id ());
          $this->_check_equal ('FreeBSD', $browser->calculated_system_name ());
          $this->_check_equal ('FreeBSD', $browser->system_name ());
          $this->_check_equal ('4.3', $browser->system_version ());
          $this->_check_equal ('curl', $browser->name ());
          $this->_check_equal ('7.7.1', $browser->version ());
          $this->_check_equal ('Unknown', $browser->renderer_name ());
          $this->_check_equal ('', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('DocZilla/1.0 (Windows; U; WinNT4.0; en-US; rv:1.0.0) Gecko/20020804');

          $this->_check_equal ('DocZilla/1.0 (Windows; U; WinNT4.0; en-US; rv:1.0.0) Gecko/20020804', $browser->user_agent_string);
          $this->_check_equal ('Windows NT 4.0 (Windows NT 4.x)', $browser->system_id ());
          $this->_check_equal ('Windows NT 4.x', $browser->calculated_system_name ());
          $this->_check_equal ('Windows NT', $browser->system_name ());
          $this->_check_equal ('4.0', $browser->system_version ());
          $this->_check_equal ('DocZilla', $browser->name ());
          $this->_check_equal ('1.0', $browser->version ());
          $this->_check_equal ('Gecko', $browser->renderer_name ());
          $this->_check_equal ('1.0.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check_equal ('2002-08-04 00:00:00', $date->as_iso ());
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('DoCoMo/2.0 P900iV(c100;TB;W24H11)');

          $this->_check_equal ('DoCoMo/2.0 P900iV(c100;TB;W24H11)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('DoCoMo', $browser->name ());
          $this->_check_equal ('2.0', $browser->version ());
          $this->_check_equal ('Unknown', $browser->renderer_name ());
          $this->_check_equal ('', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/4.5 (compatible; OmniWeb/4.1.1-v423; Mac_PowerPC)');

          $this->_check_equal ('Mozilla/4.5 (compatible; OmniWeb/4.1.1-v423; Mac_PowerPC)', $browser->user_agent_string);
          $this->_check_equal ('MacOS PPC', $browser->system_id ());
          $this->_check_equal ('MacOS PPC', $browser->calculated_system_name ());
          $this->_check_equal ('MacOS PPC', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('OmniWeb', $browser->name ());
          $this->_check_equal ('4.1.1', $browser->version ());
          $this->_check_equal ('OmniWeb', $browser->renderer_name ());
          $this->_check_equal ('4.1.1', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/5.0 Galeon/1.2.7 (X11; Linux i686; U;) Gecko/20021226 Debian/1.2.7-6');

          $this->_check_equal ('Mozilla/5.0 Galeon/1.2.7 (X11; Linux i686; U;) Gecko/20021226 Debian/1.2.7-6', $browser->user_agent_string);
          $this->_check_equal ('Debian 1.2.7-6 (Linux)', $browser->system_id ());
          $this->_check_equal ('Linux', $browser->calculated_system_name ());
          $this->_check_equal ('Debian', $browser->system_name ());
          $this->_check_equal ('1.2.7-6', $browser->system_version ());
          $this->_check_equal ('Galeon', $browser->name ());
          $this->_check_equal ('1.2.7', $browser->version ());
          $this->_check_equal ('Gecko', $browser->renderer_name ());
          $this->_check_equal ('5.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check_equal ('2002-12-26 00:00:00', $date->as_iso ());
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/7.0');

          $this->_check_equal ('Mozilla/7.0', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Mozilla', $browser->name ());
          $this->_check_equal ('7.0', $browser->version ());
          $this->_check_equal ('Gecko', $browser->renderer_name ());
          $this->_check_equal ('7.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.6) Gecko/20040413 Galeon/1.3.14 (Debian package 1.3.14acvs20040504-1)');

          $this->_check_equal ('Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.6) Gecko/20040413 Galeon/1.3.14 (Debian package 1.3.14acvs20040504-1)', $browser->user_agent_string);
          $this->_check_equal ('Debian 1.3.14acvs20040504-1 (Linux)', $browser->system_id ());
          $this->_check_equal ('Linux', $browser->calculated_system_name ());
          $this->_check_equal ('Debian', $browser->system_name ());
          $this->_check_equal ('1.3.14acvs20040504-1', $browser->system_version ());
          $this->_check_equal ('Galeon', $browser->name ());
          $this->_check_equal ('1.3.14', $browser->version ());
          $this->_check_equal ('Gecko', $browser->renderer_name ());
          $this->_check_equal ('1.6', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check_equal ('2004-04-13 00:00:00', $date->as_iso ());
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/4.5 (compatible; iCab 2.9.8; Macintosh; U; PPC; Mac OS X)');

          $this->_check_equal ('Mozilla/4.5 (compatible; iCab 2.9.8; Macintosh; U; PPC; Mac OS X)', $browser->user_agent_string);
          $this->_check_equal ('Mac OS X', $browser->system_id ());
          $this->_check_equal ('Mac OS X', $browser->calculated_system_name ());
          $this->_check_equal ('Mac OS X', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('iCab', $browser->name ());
          $this->_check_equal ('2.9.8', $browser->version ());
          $this->_check_equal ('iCab', $browser->renderer_name ());
          $this->_check_equal ('2.9.8', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 6.0; .NET CLR 2.0.50215; SL Commerce Client v1.0; Tablet PC 2.0; Avalon 6.0.4030; WinFX RunTime 1.0.50215)');

          $this->_check_equal ('Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 6.0; .NET CLR 2.0.50215; SL Commerce Client v1.0; Tablet PC 2.0; Avalon 6.0.4030; WinFX RunTime 1.0.50215)', $browser->user_agent_string);
          $this->_check_equal ('Windows NT 6.0 (Windows Vista)', $browser->system_id ());
          $this->_check_equal ('Windows Vista', $browser->calculated_system_name ());
          $this->_check_equal ('Windows NT', $browser->system_name ());
          $this->_check_equal ('6.0', $browser->system_version ());
          $this->_check_equal ('Internet Explorer', $browser->name ());
          $this->_check_equal ('7.0b', $browser->version ());
          $this->_check_equal ('Trident (IE)', $browser->renderer_name ());
          $this->_check_equal ('7.0b', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.7.7) Gecko/20050414 Firefox/1.0.3 SUSE/1.0.3-0.5');

          $this->_check_equal ('Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.7.7) Gecko/20050414 Firefox/1.0.3 SUSE/1.0.3-0.5', $browser->user_agent_string);
          $this->_check_equal ('SUSE 1.0.3-0.5 (Linux)', $browser->system_id ());
          $this->_check_equal ('Linux', $browser->calculated_system_name ());
          $this->_check_equal ('SUSE', $browser->system_name ());
          $this->_check_equal ('1.0.3-0.5', $browser->system_version ());
          $this->_check_equal ('Firefox', $browser->name ());
          $this->_check_equal ('1.0.3', $browser->version ());
          $this->_check_equal ('Gecko', $browser->renderer_name ());
          $this->_check_equal ('1.7.7', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check ($date->is_valid (), 'Date should be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/5.0 (compatible; Konqueror/3.2; Linux) (KHTML, like Gecko)');

          $this->_check_equal ('Mozilla/5.0 (compatible; Konqueror/3.2; Linux) (KHTML, like Gecko)', $browser->user_agent_string);
          $this->_check_equal ('Linux', $browser->system_id ());
          $this->_check_equal ('Linux', $browser->calculated_system_name ());
          $this->_check_equal ('Linux', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Konqueror', $browser->name ());
          $this->_check_equal ('3.2', $browser->version ());
          $this->_check_equal ('KHTML', $browser->renderer_name ());
          $this->_check_equal ('3.2', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');


    $browser->load_from_string ('Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/417.9 (KHTML, like Gecko) Shiira/1.2.1 Safari/125');

          $this->_check_equal ('Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/417.9 (KHTML, like Gecko) Shiira/1.2.1 Safari/125', $browser->user_agent_string);
          $this->_check_equal ('Mac OS X', $browser->system_id ());
          $this->_check_equal ('Mac OS X', $browser->calculated_system_name ());
          $this->_check_equal ('Mac OS X', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Shiira', $browser->name ());
          $this->_check_equal ('1.2.1', $browser->version ());
          $this->_check_equal ('Webcore', $browser->renderer_name ());
          $this->_check_equal ('417.9', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/4.0 (compatible; MSIE 5.0; Series80/2.0 Nokia9500/4.34.0 Profile/MIDP-2.0 Configuration/CLDC-1.1)');

          $this->_check_equal ('Mozilla/4.0 (compatible; MSIE 5.0; Series80/2.0 Nokia9500/4.34.0 Profile/MIDP-2.0 Configuration/CLDC-1.1)', $browser->user_agent_string);
          $this->_check_equal ('Series 80 2.0', $browser->system_id ());
          $this->_check_equal ('Series 80', $browser->calculated_system_name ());
          $this->_check_equal ('Series 80', $browser->system_name ());
          $this->_check_equal ('2.0', $browser->system_version ());
          $this->_check_equal ('Nokia9500', $browser->name ());
          $this->_check_equal ('4.34.0', $browser->version ());
          $this->_check_equal ('Trident (IE)', $browser->renderer_name ());
          $this->_check_equal ('5.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');

    $browser->load_from_string ('Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13');

          $this->_check_equal ('Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13', $browser->user_agent_string);
          $this->_check_equal ('Windows NT 5.1 (Windows XP)', $browser->system_id ());
          $this->_check_equal ('Windows XP', $browser->calculated_system_name ());
          $this->_check_equal ('Windows NT', $browser->system_name ());
          $this->_check_equal ('5.1', $browser->system_version ());
          $this->_check_equal ('Google Chrome', $browser->name ());
          $this->_check_equal ('0.2.149.27', $browser->version ());
          $this->_check_equal ('Webcore', $browser->renderer_name ());
          $this->_check_equal ('525.13', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');
          $this->_check (!$browser->is (Browser_newsreader), 'Browser should not be a newsreader');
    }

  protected function _test_bots ()
  {
    $browser = $this->env->browser ();

    $browser->load_from_string ('YahooSeeker/1.0 (compatible; Mozilla 4.0; MSIE 5.5; http://help.yahoo.com/help/us/shop/merchant/)');

          $this->_check_equal ('YahooSeeker/1.0 (compatible; Mozilla 4.0; MSIE 5.5; http://help.yahoo.com/help/us/shop/merchant/)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Yahoo Robot', $browser->name ());
          $this->_check_equal ('1.0', $browser->version ());
          $this->_check_equal ('Yahoo Robot', $browser->renderer_name ());
          $this->_check_equal ('1.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check ($browser->is (Browser_robot), 'Browser should be a robot.');

    $browser->load_from_string ('ZBot/1.00 (icaulfield@zeus.com)');

          $this->_check_equal ('ZBot/1.00 (icaulfield@zeus.com)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('ZBot', $browser->name ());
          $this->_check_equal ('1.00', $browser->version ());
          $this->_check_equal ('Unknown', $browser->renderer_name ());
          $this->_check_equal ('', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check ($browser->is (Browser_robot), 'Browser should be a robot.');

    $browser->load_from_string ('WWWeasel Robot v1.00 (http://wwweasel.de)');

          $this->_check_equal ('WWWeasel Robot v1.00 (http://wwweasel.de)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('WWWeasel Robot', $browser->name ());
          $this->_check_equal ('1.00', $browser->version ());
          $this->_check_equal ('Unknown', $browser->renderer_name ());
          $this->_check_equal ('', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check ($browser->is (Browser_robot), 'Browser should be a robot.');

    $browser->load_from_string ('ZipppBot/0.xx (ZipppBot; http://www.zippp.net; webmaster@zippp.net)');

          $this->_check_equal ('ZipppBot/0.xx (ZipppBot; http://www.zippp.net; webmaster@zippp.net)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('ZipppBot', $browser->name ());
          $this->_check_equal ('0', $browser->version ());
          $this->_check_equal ('Unknown', $browser->renderer_name ());
          $this->_check_equal ('', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check ($browser->is (Browser_robot), 'Browser should be a robot.');

    $browser->load_from_string ('WebSearchBench WebCrawler V1.0 (Beta), Prof. Dr.-Ing. Christoph Lindemann, Universität Dortmund, cl@cs.uni-dortmund.de, http://websearchbench.cs.uni-dortmund.de/');

          $this->_check_equal ('WebSearchBench WebCrawler V1.0 (Beta), Prof. Dr.-Ing. Christoph Lindemann, Universität Dortmund, cl@cs.uni-dortmund.de, http://websearchbench.cs.uni-dortmund.de/', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('WebSearchBench WebCrawler', $browser->name ());
          $this->_check_equal ('1.0', $browser->version ());
          $this->_check_equal ('Unknown', $browser->renderer_name ());
          $this->_check_equal ('', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check ($browser->is (Browser_robot), 'Browser should be a robot.');

    $browser->load_from_string ('Googlebot/2.1 (+http://www.googlebot.com/bot.html)');

          $this->_check_equal ('Googlebot/2.1 (+http://www.googlebot.com/bot.html)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Google Robot', $browser->name ());
          $this->_check_equal ('2.1', $browser->version ());
          $this->_check_equal ('Google Robot', $browser->renderer_name ());
          $this->_check_equal ('2.1', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check ($browser->is (Browser_robot), 'Browser should be a robot.');

    $browser->load_from_string ('Googlebot-Image/1.0 (+http://www.googlebot.com/bot.html)');

          $this->_check_equal ('Googlebot-Image/1.0 (+http://www.googlebot.com/bot.html)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Google Robot', $browser->name ());
          $this->_check_equal ('1.0', $browser->version ());
          $this->_check_equal ('Google Robot', $browser->renderer_name ());
          $this->_check_equal ('1.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check ($browser->is (Browser_robot), 'Browser should be a robot.');

    $browser->load_from_string ('MSNBOT/0.xx (http://search.msn.com/msnbot.htm)');

          $this->_check_equal ('MSNBOT/0.xx (http://search.msn.com/msnbot.htm)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('MSN Robot', $browser->name ());
          $this->_check_equal ('0', $browser->version ());
          $this->_check_equal ('MSN Robot', $browser->renderer_name ());
          $this->_check_equal ('0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check ($browser->is (Browser_robot), 'Browser should be a robot.');

    $browser->load_from_string ('YahooSeeker/1.0 (compatible; Mozilla 4.0; MSIE 5.5; rv:1.7.3 Gecko/20040913 http://help.yahoo.com/help/us/shop/merchant/)');

          $this->_check_equal ('YahooSeeker/1.0 (compatible; Mozilla 4.0; MSIE 5.5; rv:1.7.3 Gecko/20040913 http://help.yahoo.com/help/us/shop/merchant/)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Yahoo Robot', $browser->name ());
          $this->_check_equal ('1.0', $browser->version ());
          $this->_check_equal ('Yahoo Robot', $browser->renderer_name ());
          $this->_check_equal ('1.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check ($browser->is (Browser_robot), 'Browser should be a robot.');

    $browser->load_from_string ('FAST-WebCrawler/2.2.10 (Multimedia Search) (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)');

          $this->_check_equal ('FAST-WebCrawler/2.2.10 (Multimedia Search) (crawler@fast.no; http://www.fast.no/faq/faqfastwebsearch/faqfastwebcrawler.html)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('FAST-WebCrawler', $browser->name ());
          $this->_check_equal ('2.2.10', $browser->version ());
          $this->_check_equal ('Unknown', $browser->renderer_name ());
          $this->_check_equal ('', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check ($browser->is (Browser_robot), 'Browser should be a robot.');
  }

  protected function _test_newsreaders ()
  {
    $browser = $this->env->browser ();

    $browser->load_from_string ('AppleSyndication/54');

          $this->_check_equal ('AppleSyndication/54', $browser->user_agent_string);
          $this->_check_equal ('Mac OS X', $browser->system_id ());
          $this->_check_equal ('Mac OS X', $browser->calculated_system_name ());
          $this->_check_equal ('Mac OS X', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Safari Newsreader', $browser->name ());
          $this->_check_equal ('54', $browser->version ());
          $this->_check_equal ('Safari Newsreader', $browser->renderer_name ());
          $this->_check_equal ('54', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');
          $this->_check ($browser->is (Browser_newsreader), 'Browser should be a newsreader');

    $browser->load_from_string ('Bloglines/3.1 (http://www.bloglines.com; 2 subscribers)');

          $this->_check_equal ('Bloglines/3.1 (http://www.bloglines.com; 2 subscribers)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Bloglines', $browser->name ());
          $this->_check_equal ('3.1', $browser->version ());
          $this->_check_equal ('Bloglines', $browser->renderer_name ());
          $this->_check_equal ('3.1', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');
          $this->_check ($browser->is (Browser_newsreader), 'Browser should be a newsreader');

    $browser->load_from_string ('NewsGatorOnline/2.0 (http://www.newsgator.com; 1 subscribers)');

          $this->_check_equal ('NewsGatorOnline/2.0 (http://www.newsgator.com; 1 subscribers)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('NewsGator', $browser->name ());
          $this->_check_equal ('2.0', $browser->version ());
          $this->_check_equal ('NewsGator', $browser->renderer_name ());
          $this->_check_equal ('2.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');
          $this->_check ($browser->is (Browser_newsreader), 'Browser should be a newsreader');

    $browser->load_from_string ('YahooFeedSeeker/2.0 (compatible; Mozilla 4.0; MSIE 5.5; http://publisher.yahoo.com/rssguide; users 0; views 0)');

          $this->_check_equal ('YahooFeedSeeker/2.0 (compatible; Mozilla 4.0; MSIE 5.5; http://publisher.yahoo.com/rssguide; users 0; views 0)', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Yahoo Newsreader', $browser->name ());
          $this->_check_equal ('2.0', $browser->version ());
          $this->_check_equal ('Yahoo Newsreader', $browser->renderer_name ());
          $this->_check_equal ('2.0', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');
          $this->_check ($browser->is (Browser_newsreader), 'Browser should be a newsreader');
  }

  protected function _test_bed ()
  {
    $browser = $this->env->browser ();

    $browser->load_from_string ('Opera/9.80 (J2ME/MIDP; Opera Mini/5.0.2056/866; U; en) Presto/2.2');

          $this->_check_equal ('Opera/9.80 (J2ME/MIDP; Opera Mini/5.0.2056/866; U; en) Presto/2.2', $browser->user_agent_string);
          $this->_check_equal ('Unknown', $browser->system_id ());
          $this->_check_equal ('Unknown', $browser->calculated_system_name ());
          $this->_check_equal ('Unknown', $browser->system_name ());
          $this->_check_equal ('', $browser->system_version ());
          $this->_check_equal ('Opera Mini', $browser->name ());
          $this->_check_equal ('5.0.2056', $browser->version ());
          $this->_check_equal ('Presto (Opera)', $browser->renderer_name ());
          $this->_check_equal ('2.2', $browser->renderer_version ());
          $date = $browser->gecko_date ();
          $this->_check (!$date->is_valid (), 'Date should not be valid');
          $this->_check (!$browser->is (Browser_robot), 'Browser should not be a robot.');
          $this->_check (!$browser->is (Browser_newsreader), 'Browser should not be a newsreader');
  }
}

?>