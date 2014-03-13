<?php

/**
 * WebCore Testsuite Component.
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.5.0
 * @since 2.7.0
 * @access private
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
require_once ('webcore/tests/test_task.php');
require_once ('webcore/sys/resources.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.5.0
 * @since 2.7.0
 * @access private
 */
class RESOURCE_TEST_TASK extends TEST_TASK
{
  protected function _check_basic_aliases ($res)
  {
    $this->_check_equal ('', $res->path_to ('not_an_alias'));    
    $this->_check_equal ('/', $res->path_to ('root'));    
    $this->_check_equal ('/resources/', $res->path_to ('resources'));    
    $this->_check_equal ('/resources/pictures/', $res->path_to ('pictures'));    
    $this->_check_equal ('/resources/pictures/mypic.jpg', $res->resolve_file ('{pictures}/mypic.jpg'));    
    $this->_check_equal ('/resources/pictures/mypic.jpg', $res->resolve_file ('{pictures}mypic.jpg'));    
    $this->_check_equal ('/resources/pictures/vacation/mypic.jpg', $res->resolve_file ('{pictures}/vacation/mypic.jpg'));    
    $this->_check_equal ('/resources/pictures/vacation/mypic.jpg', $res->resolve_file ('{pictures}vacation/mypic.jpg'));
    $this->_check_equal ('/resources/pictures/vacation/mypic.png', $res->resolve_file ('{pictures}vacation/mypic'));
    $this->_check_equal ('/resources/pictures/vacation/mypic/', $res->resolve_file ('{pictures}vacation/mypic/'));
    $this->_check_equal ('/resources/pictures/vacation/mypic.png', $res->resolve_file_for_alias ('pictures', '/vacation/mypic'));
    $this->_check_equal ('/resources/pictures/vacation/mypic.png', $res->resolve_file_for_alias ('pictures', 'vacation/mypic'));
    
    $this->_check_equal ('not_an_alias/path/', $res->resolve_path ('{not_an_alias/path'));    
    $this->_check_equal ('not_an_alias/path/', $res->resolve_path ('not_an_alias/path'));    

    $this->_check_equal ('attachments/vacation/mypic', $res->resolve_file_for_alias ('attachments', 'vacation/mypic'));
    $res->set_extension ('attachments', 'jpeg');
    $this->_check_equal ('attachments/vacation/mypic.jpeg', $res->resolve_file_for_alias ('attachments', 'vacation/mypic'));
    $this->_check_equal ('<img src="/resources/pictures/vacation/mypic.png" title="My Picture" alt="My Picture" style="vertical-align: middle">', $res->resolve_icon_as_html ('{pictures}vacation/mypic', 'My Picture'));
    $res->set_extension ('attachments', '');
    $this->_check_equal ('attachments/vacation/mypic', $res->resolve_file_for_alias ('attachments', 'vacation/mypic'));

    $this->_check_equal ('attachments/vacation/mypic.tiff', $res->resolve_file_for_alias ('attachments', 'vacation/mypic.tiff'));
    $this->_check_equal ('attachments/entry/vacation/mypic.tiff', $res->resolve_file ('{entry_attachments}vacation/mypic.tiff'));

    $this->_check_equal ('/external/software/vacation/old_file.large', $res->resolve_file ('{external}vacation/old_file.large'));
    $this->_check_equal ('/external/software/vacation/old_file.large/', $res->resolve_file ('{external}vacation/old_file.large/'));
    $this->_check_equal ('/external/software/vacation/old_file.large/', $res->resolve_path ('{external}vacation/old_file.large'));
    $this->_check_equal ('/external/software/vacation/old_file.large/', $res->resolve_path ('{external}vacation/old_file.large/'));
    $this->_check_equal ('/external/software/vacation/old_file_large/', $res->resolve_file ('{external}vacation/old_file_large/'));
    $this->_check_equal ('/external/software/vacation/old_file_large.zip', $res->resolve_file ('{external}vacation/old_file_large'));
  }
  
  protected function _check_force_root_override ($page_res, $app_res)
  {
    $this->_check_equal ('#location_in_page', $app_res->resolve_file ('#location_in_page', Force_root_on));
    $this->_check_equal ('/var/log/mail/mylog.txt', $app_res->resolve_file ('{logs}/mail/mylog.txt', Force_root_on));
    $this->_check_equal ('http://earthli.com/resources/pictures/mypic.jpg', $page_res->resolve_file ('{pictures}/mypic.jpg', Force_root_on));    
    $this->_check_equal ('http://earthli.com/resources/pictures/mypic.jpg', $app_res->resolve_file ('{pictures}/mypic.jpg', Force_root_on));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/attachments/entry/mypic.jpg', $page_res->resolve_file ('{entry_attachments}/mypic.jpg', Force_root_on));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/attachments/entry/mypic.jpg', $app_res->resolve_file ('{entry_attachments}/mypic.jpg', Force_root_on));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/attachments/entry/hello/', $app_res->resolve_path ('{entry_attachments}/hello', Force_root_on));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/attachments/entry/hello/', $app_res->resolve_path_for_alias ('entry_attachments', 'hello', Force_root_on));    

    $this->_check_equal ('/resources/pictures/mypic.jpg', $page_res->resolve_file ('{pictures}/mypic.jpg', Force_root_off));
    $this->_check_equal ('/resources/pictures/mypic.jpg', $app_res->resolve_file ('{pictures}/mypic.jpg', Force_root_off));
    $this->_check_equal ('attachments/entry/mypic.jpg', $page_res->resolve_file ('{entry_attachments}/mypic.jpg', Force_root_off));
    $this->_check_equal ('attachments/entry/mypic.jpg', $app_res->resolve_file ('{entry_attachments}/mypic.jpg', Force_root_off));
    $this->_check_equal ('attachments/entry/hello/', $app_res->resolve_path ('{entry_attachments}/hello', Force_root_off));
    $this->_check_equal ('attachments/entry/hello/', $app_res->resolve_path_for_alias ('entry_attachments', 'hello', Force_root_off));
  }
  
  protected function _check_force_root_default ($env_res, $page_res, $app_res)
  {
    $page_res->set_path ('attachments', '');
    $page_res->set_path ('entry_attachments', '');
    $app_res->set_path ('attachments', 'attachments');
    $app_res->set_path ('entry_attachments', '{attachments}/entry');

    /* Test root-resolution using the property. */

    $app_res->resolve_to_root = true;
    $page_res->resolve_to_root = true;
    $env_res->resolve_to_root = true;

    $this->_check_equal ('#location_in_page', $app_res->resolve_file ('#location_in_page'));
    $this->_check_equal ('/var/log/mail/mylog.txt', $app_res->resolve_file ('{logs}/mail/mylog.txt'));
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/attachments/entry/vacation/mypic.tiff', $app_res->resolve_file ('{entry_attachments}/vacation/mypic.tiff'));    
    $this->_check_equal ('http://earthli.com/resources/pictures/vacation/mypic.tiff', $app_res->resolve_file ('{pictures}/vacation/mypic.tiff'));    
    $this->_check_equal ('http://earthli.com/resources/pictures/vacation/mypic.png', $app_res->resolve_file ('{pictures}/vacation/mypic'));    
    $this->_check_equal ('http://earthli.com/resources/pictures/vacation/mypic.tiff/', $app_res->resolve_path ('{pictures}/vacation/mypic.tiff'));    
    $this->_check_equal ('http://earthli.com/resources/pictures/vacation/mypic/', $app_res->resolve_path ('{pictures}/vacation/mypic'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/attachments/vacation/mypic.tiff', $app_res->resolve_file ('{attachments}/vacation/mypic.tiff'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/attachments/vacation/mypic', $app_res->resolve_file ('{attachments}/vacation/mypic'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/attachments/vacation/mypic.tiff/', $app_res->resolve_path ('{attachments}/vacation/mypic.tiff'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/attachments/vacation/mypic/', $app_res->resolve_path ('{attachments}/vacation/mypic'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/vacation/mypic.first.jpeg', $app_res->resolve_file ('vacation/mypic.first.jpeg'));    
    $this->_check_equal ('http://earthli.com/vacation/mypic.first.jpeg', $app_res->resolve_file ('/vacation/mypic.first.jpeg'));    
    $this->_check_equal ('http://earthli.com/external/software/downloads/mypic.first', $app_res->resolve_file ('{external}downloads/mypic.first'));
    $this->_check_equal ('http://earthli.com/external/software/downloads/mypic_first.zip', $app_res->resolve_file ('{external}downloads/mypic_first'));
    $this->_check_equal ('http://earthli.com/external/software/downloads/mypic.first/', $app_res->resolve_path ('{external}downloads/mypic.first'));
    $this->_check_equal ('http://earthli.com/external/software/downloads/mypic.first/', $app_res->resolve_file ('{external}downloads/mypic.first/'));
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/vacation/mypic.first.jpeg', $app_res->resolve_file ('vacation/mypic.first.jpeg'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/vacation/mypic.first.jpeg', $app_res->resolve_file ('albums/vacation/mypic.first.jpeg'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/vacation/mypic.first.jpeg', $app_res->resolve_file ('albums/vacation/mypic.first.jpeg'));    
    $this->_check_equal ('http://earthli.com/albums/vacation/mypic.first.jpeg', $app_res->resolve_file ('/albums/vacation/mypic.first.jpeg'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/home/users/~bob/albums/vacation/mypic.first.jpeg', $app_res->resolve_file ('home/users/~bob/albums/vacation/mypic.first.jpeg'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/vacation/mypic.first.jpeg', $app_res->resolve_file ('/site_root/home/users/~bob/albums/vacation/mypic.first.jpeg'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/site_root/home/users/~bob/albums/vacation/mypic.first.jpeg', $app_res->resolve_file ('site_root/home/users/~bob/albums/vacation/mypic.first.jpeg'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/vacation/mypic.first.jpeg', $app_res->resolve_file ('http://earthli.com/site_root/home/users/~bob/albums/vacation/mypic.first.jpeg'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/attachments/entry/vacation/mypic.tiff', $app_res->resolve_file ('{entry_attachments}/vacation/mypic.tiff'));
    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/fldr/', $app_res->resolve_path ('fldr'));    
    $this->_check_equal ('http://earthli.com/fldr/', $app_res->resolve_path ('/fldr'));    
    $this->_check_equal ('http://earthli.com/site_root/home/users/~bob/albums/', $app_res->resolve_path (''));    
    $app_res->root_url = '/webcore/albums/';
    $this->_check_equal ('http://earthli.com/webcore/albums/fldr/', $app_res->resolve_path ('fldr'));
    $this->_check_equal ('http://earthli.com/fldr/', $app_res->resolve_path ('/fldr'));    
    $this->_check_equal ('http://earthli.com/webcore/albums/', $app_res->resolve_path (''));
    $page_res->resolve_to_root = false;
    $app_res->root_url = 'webcore/albums/';
    $this->_check_equal ('http://earthli.com/webcore/albums/fldr/', $app_res->resolve_path ('fldr'));
    $this->_check_equal ('http://earthli.com/fldr/', $app_res->resolve_path ('/fldr'));    
    $this->_check_equal ('http://earthli.com/webcore/albums/', $app_res->resolve_path (''));
    $env_res->resolve_to_root = false;
    $this->_check_equal ('webcore/albums/fldr/', $app_res->resolve_path ('fldr'));
    $this->_check_equal ('/fldr/', $app_res->resolve_path ('/fldr'));    
    $this->_check_equal ('webcore/albums/', $app_res->resolve_path (''));

    $app_res->add_to_path ('entry_attachments', 'another/folder/');
    $this->_check_equal ('webcore/albums/attachments/entry/another/folder/vacation/mypic.tiff', $app_res->resolve_file ('{entry_attachments}/vacation/mypic.tiff'));
  }
  
  protected function _execute ()
  {
    $env_res = new RESOURCE_MANAGER ();
    $env_res->root_url = 'http://earthli.com';

    $page_res = new RESOURCE_MANAGER ();
    $page_res->root_url = '/site_root/home/users/~bob/';
    $page_res->inherit_resources_from ($env_res);

    $page_res->set_path ('root', '/');
    $page_res->set_path ('resources', '{root}/resources');
    $page_res->set_path ('pictures', '{resources}/pictures');
    $page_res->set_path ('external', '/external/software');
    $page_res->set_path ('attachments', 'attachments');
    $page_res->set_path ('entry_attachments', '{attachments}/entry');
    $page_res->set_path ('logs', '/var/log');
    $page_res->set_forced_root ('logs', false);
    $page_res->set_extension ('pictures', 'png');
    $page_res->set_extension ('external', 'zip');
    
    $app_res = new RESOURCE_MANAGER ();
    $app_res->inherit_resources_from ($page_res);
    $app_res->root_url = 'albums/';

    $this->_check_basic_aliases ($app_res);
    $this->_check_basic_aliases ($page_res);
    $this->_check_force_root_override ($page_res, $app_res);
    $this->_check_force_root_default ($env_res, $page_res, $app_res);
  }
}

?>