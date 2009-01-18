<?php

/**
 * WebCore Testsuite Component.
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage tests
 * @version 3.0.0
 * @since 2.7.0
 * @access private
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

require_once ('webcore/tests/test_task.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.0.0
 * @since 2.7.0
 * @access private
 */
class URL_TEST_TASK extends TEST_TASK
{
  function _execute ()
  {
    $this->_test_url_functions ();
    $this->_test_url_class ();
  }

  function _test_url_functions ()
  {
    $url_text = 'http://earthli.com/pages/page.php?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/';

    $url = new URL ($url_text);
    $this->_check_equal (TRUE, $url->has_domain ('earthli.com'));
    $this->_check_equal (TRUE, $url->has_domain ('[^\.]*[\.]?earthli.com'));
    $this->_check_equal (TRUE, $url->has_domain ('[^\.]*[\.]?earthli.[com|net|org]'));
    $this->_check_equal (TRUE, $url->has_domain ('[www.]?earthli\.[com|net|org]'));
    $this->_check_equal ($url_text, ensure_has_protocol($url_text, 'http'));

    $url_text = 'http://earthli.net/pages/page.php?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/';

    $url = new URL ($url_text);
    $this->_check_equal (TRUE, $url->has_domain ('earthli.net'));
    $this->_check_equal (TRUE, $url->has_domain ('[^\.]*[\.]?earthli.net'));
    $this->_check_equal (TRUE, $url->has_domain ('[^\.]*[\.]?earthli.[com|net|org]'));
    $this->_check_equal (TRUE, $url->has_domain ('[www.]?earthli\.[com|net|org]'));
    $this->_check_equal ($url_text, ensure_has_protocol($url_text, 'http'));

    $url_text = 'http://www.earthli.com/pages/page.php?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/';

    $url = new URL ($url_text);
    $this->_check_equal (TRUE, $url->has_domain ('www.earthli.com'));
    $this->_check_equal (TRUE, $url->has_domain ('[^\.]*[\.]?earthli.com'));
    $this->_check_equal (TRUE, $url->has_domain ('[^\.]*[\.]?earthli.[com|net|org]'));
    $this->_check_equal (TRUE, $url->has_domain ('[www.]?earthli\.[com|net|org]'));
    $this->_check_equal ($url_text, ensure_has_protocol($url_text, 'http'));
  }

  function _test_url_class ()
  {
    $url_text = 'http://earthli.com/pages/page.php?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/';

    $this->_log ('original URL', Msg_type_info);
    $url = new URL ($url_text);

          $this->_check_equal ('http://earthli.com/pages/page.php?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/', $url->path ());
          $this->_check_equal ('/pages/', $url->path_without_domain ());
          $this->_check_equal ('page.php', $url->name ());
          $this->_check_equal ('php', $url->extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->query_string ());
          $this->_check_equal ('page', $url->name_without_extension ());
          $this->_check_equal ('page.php?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->name_with_query_string ());

    $this->_log ('replaced extension', Msg_type_info);
    $url->replace_extension ('jpg');

          $this->_check_equal ('http://earthli.com/pages/page.jpg?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/', $url->path ());
          $this->_check_equal ('/pages/', $url->path_without_domain ());
          $this->_check_equal ('page.jpg', $url->name ());
          $this->_check_equal ('jpg', $url->extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->query_string ());
          $this->_check_equal ('page', $url->name_without_extension ());
          $this->_check_equal ('page.jpg?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->name_with_query_string ());

    $this->_log ('appended to name', Msg_type_info);
    $url->append_to_name ('_tn');

          $this->_check_equal ('http://earthli.com/pages/page_tn.jpg?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/', $url->path ());
          $this->_check_equal ('/pages/', $url->path_without_domain ());
          $this->_check_equal ('page_tn.jpg', $url->name ());
          $this->_check_equal ('jpg', $url->extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->query_string ());
          $this->_check_equal ('page_tn', $url->name_without_extension ());
          $this->_check_equal ('page_tn.jpg?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->name_with_query_string ());

    $this->_log ('replaced name', Msg_type_info);
    $url->replace_name ('picture');

          $this->_check_equal ('http://earthli.com/pages/picture.jpg?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/', $url->path ());
          $this->_check_equal ('/pages/', $url->path_without_domain ());
          $this->_check_equal ('picture.jpg', $url->name ());
          $this->_check_equal ('jpg', $url->extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->query_string ());
          $this->_check_equal ('picture', $url->name_without_extension ());
          $this->_check_equal ('picture.jpg?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->name_with_query_string ());

    $this->_log ('emptied name and extension', Msg_type_info);
    $url->replace_name_and_extension ('');

          $this->_check_equal ('http://earthli.com/pages/?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/', $url->path ());
          $this->_check_equal ('/pages/', $url->path_without_domain ());
          $this->_check_equal ('', $url->name ());
          $this->_check_equal ('', $url->extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->query_string ());
          $this->_check_equal ('', $url->name_without_extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->name_with_query_string ());

    $url->replace_name_and_extension ('picture.jpg');

          $this->_check_equal ('http://earthli.com/pages/picture.jpg?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/', $url->path ());
          $this->_check_equal ('/pages/', $url->path_without_domain ());
          $this->_check_equal ('picture.jpg', $url->name ());
          $this->_check_equal ('jpg', $url->extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->query_string ());
          $this->_check_equal ('picture', $url->name_without_extension ());
          $this->_check_equal ('picture.jpg?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->name_with_query_string ());

    $this->_log ('replaced name and extension', Msg_type_info);
    $url->replace_name_and_extension ('picture_ex.png');

          $this->_check_equal ('http://earthli.com/pages/picture_ex.png?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/', $url->path ());
          $this->_check_equal ('/pages/', $url->path_without_domain ());
          $this->_check_equal ('picture_ex.png', $url->name ());
          $this->_check_equal ('png', $url->extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->query_string ());
          $this->_check_equal ('picture_ex', $url->name_without_extension ());
          $this->_check_equal ('picture_ex.png?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/', $url->name_with_query_string ());

    $this->_log ('added argument', Msg_type_info);
    $url->add_argument ('arg4', 'new');

          $this->_check_equal ('http://earthli.com/pages/picture_ex.png?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/', $url->path ());
          $this->_check_equal ('/pages/', $url->path_without_domain ());
          $this->_check_equal ('picture_ex.png', $url->name ());
          $this->_check_equal ('png', $url->extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->query_string ());
          $this->_check_equal ('picture_ex', $url->name_without_extension ());
          $this->_check_equal ('picture_ex.png?arg1=http://earthli.com/pages/page.php&arg2=2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->name_with_query_string ());

    $this->_log ('replaced argument', Msg_type_info);
    $url->replace_argument ('arg2', 'not_2');

          $this->_check_equal ('http://earthli.com/pages/picture_ex.png?arg1=http://earthli.com/pages/page.php&arg2=not_2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/', $url->path ());
          $this->_check_equal ('/pages/', $url->path_without_domain ());
          $this->_check_equal ('picture_ex.png', $url->name ());
          $this->_check_equal ('png', $url->extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=not_2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->query_string ());
          $this->_check_equal ('picture_ex', $url->name_without_extension ());
          $this->_check_equal ('picture_ex.png?arg1=http://earthli.com/pages/page.php&arg2=not_2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->name_with_query_string ());

    $this->_log ('moved back a folder', Msg_type_info);
    $url->go_back (1);

          $this->_check_equal ('http://earthli.com/picture_ex.png?arg1=http://earthli.com/pages/page.php&arg2=not_2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/', $url->path ());
          $this->_check_equal ('/', $url->path_without_domain ());
          $this->_check_equal ('picture_ex.png', $url->name ());
          $this->_check_equal ('png', $url->extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=not_2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->query_string ());
          $this->_check_equal ('picture_ex', $url->name_without_extension ());
          $this->_check_equal ('picture_ex.png?arg1=http://earthli.com/pages/page.php&arg2=not_2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->name_with_query_string ());

    $this->_log ('appended folder', Msg_type_info);
    $url->append_folder ('pages');

          $this->_check_equal ('http://earthli.com/pages/picture_ex.png?arg1=http://earthli.com/pages/page.php&arg2=not_2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/', $url->path ());
          $this->_check_equal ('/pages/', $url->path_without_domain ());
          $this->_check_equal ('picture_ex.png', $url->name ());
          $this->_check_equal ('png', $url->extension ());
          $this->_check_equal ('arg1=http://earthli.com/pages/page.php&arg2=not_2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->query_string ());
          $this->_check_equal ('picture_ex', $url->name_without_extension ());
          $this->_check_equal ('picture_ex.png?arg1=http://earthli.com/pages/page.php&arg2=not_2&arg3=user|7d9c2dae9b5b69cbbbdea47f99574ba8/&arg4=new', $url->name_with_query_string ());

    $this->_log ('removed the name', Msg_type_info);
    $url->strip_name ();

          $this->_check_equal ('http://earthli.com/pages/', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/', $url->path ());
          $this->_check_equal ('/pages/', $url->path_without_domain ());
          $this->_check_equal ('', $url->name ());
          $this->_check_equal ('', $url->extension ());
          $this->_check_equal ('', $url->query_string ());
          $this->_check_equal ('', $url->name_without_extension ());
          $this->_check_equal ('', $url->name_with_query_string ());

    $this->_log ('appended folders', Msg_type_info);
    $url->append ('media/other/');

          $this->_check_equal ('http://earthli.com/pages/media/other/', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/media/other/', $url->path ());
          $this->_check_equal ('/pages/media/other/', $url->path_without_domain ());
          $this->_check_equal ('', $url->name ());
          $this->_check_equal ('', $url->extension ());
          $this->_check_equal ('', $url->query_string ());
          $this->_check_equal ('', $url->name_without_extension ());
          $this->_check_equal ('', $url->name_with_query_string ());

    $this->_log ('appended folders (with backticks)', Msg_type_info);
    $url->append ('../../images/new/');

          $this->_check_equal ('http://earthli.com/pages/images/new/', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/images/new/', $url->path ());
          $this->_check_equal ('/pages/images/new/', $url->path_without_domain ());
          $this->_check_equal ('', $url->name ());
          $this->_check_equal ('', $url->extension ());
          $this->_check_equal ('', $url->query_string ());
          $this->_check_equal ('', $url->name_without_extension ());
          $this->_check_equal ('', $url->name_with_query_string ());

    $this->_log ('appended a file name', Msg_type_info);
    $url->append ('more/file.png');

          $this->_check_equal ('http://earthli.com/pages/images/new/more/file.png', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/images/new/more/', $url->path ());
          $this->_check_equal ('/pages/images/new/more/', $url->path_without_domain ());
          $this->_check_equal ('file.png', $url->name ());
          $this->_check_equal ('png', $url->extension ());
          $this->_check_equal ('', $url->query_string ());
          $this->_check_equal ('file', $url->name_without_extension ());
          $this->_check_equal ('file.png', $url->name_with_query_string ());

    $this->_log ('appended a query string', Msg_type_info);
    $url->replace_query_string ('arg1=1&arg2=hello world&arg3=<span class="something">');

          $this->_check_equal ('http://earthli.com/pages/images/new/more/file.png?arg1=1&arg2=hello world&arg3=<span class="something">', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/images/new/more/', $url->path ());
          $this->_check_equal ('/pages/images/new/more/', $url->path_without_domain ());
          $this->_check_equal ('file.png', $url->name ());
          $this->_check_equal ('png', $url->extension ());
          $this->_check_equal ('arg1=1&arg2=hello world&arg3=<span class="something">', $url->query_string ());
          $this->_check_equal ('file', $url->name_without_extension ());
          $this->_check_equal ('file.png?arg1=1&arg2=hello world&arg3=<span class="something">', $url->name_with_query_string ());
          $this->_check_equal ('http://earthli.com/pages/images/new/more/file.png?arg1=1&arg2=hello world&arg3=&lt;span class=&quot;something&quot;&gt;', $url->as_text (TRUE));

    $this->_log ('cleared the extension', Msg_type_info);
    $url->replace_extension ('');

          $this->_check_equal ('http://earthli.com/pages/images/new/more/file?arg1=1&arg2=hello world&arg3=<span class="something">', $url->as_text ());
          $this->_check_equal ('earthli.com', $url->domain ());
          $this->_check_equal ('http://earthli.com/pages/images/new/more/', $url->path ());
          $this->_check_equal ('/pages/images/new/more/', $url->path_without_domain ());
          $this->_check_equal ('file', $url->name ());
          $this->_check_equal ('', $url->extension ());
          $this->_check_equal ('arg1=1&arg2=hello world&arg3=<span class="something">', $url->query_string ());
          $this->_check_equal ('file', $url->name_without_extension ());
          $this->_check_equal ('file?arg1=1&arg2=hello world&arg3=<span class="something">', $url->name_with_query_string ());
          $this->_check_equal ('http://earthli.com/pages/images/new/more/file?arg1=1&arg2=hello world&arg3=&lt;span class=&quot;something&quot;&gt;', $url->as_text (TRUE));

    $this->_log ('stripped the domain', Msg_type_info);
    $url->strip_domain ();

          $this->_check_equal ('/pages/images/new/more/file?arg1=1&arg2=hello world&arg3=<span class="something">', $url->as_text ());
          $this->_check_equal ('', $url->domain ());
          $this->_check_equal ('/pages/images/new/more/', $url->path ());
          $this->_check_equal ('/pages/images/new/more/', $url->path_without_domain ());
          $this->_check_equal ('file', $url->name ());
          $this->_check_equal ('', $url->extension ());
          $this->_check_equal ('arg1=1&arg2=hello world&arg3=<span class="something">', $url->query_string ());
          $this->_check_equal ('file', $url->name_without_extension ());
          $this->_check_equal ('file?arg1=1&arg2=hello world&arg3=<span class="something">', $url->name_with_query_string ());
          $this->_check_equal ('/pages/images/new/more/file?arg1=1&arg2=hello world&arg3=&lt;span class=&quot;something&quot;&gt;', $url->as_text (TRUE));

    $this->_log ('set to root', Msg_type_info);
    $url->set_text ('/');

          $this->_check_equal ('/', $url->as_text ());
          $this->_check_equal ('', $url->domain ());
          $this->_check_equal ('/', $url->path ());
          $this->_check_equal ('/', $url->path_without_domain ());
          $this->_check_equal ('', $url->name ());
          $this->_check_equal ('', $url->extension ());
          $this->_check_equal ('', $url->query_string ());
          $this->_check_equal ('', $url->name_without_extension ());
          $this->_check_equal ('', $url->name_with_query_string ());
          $this->_check_equal ('/', $url->as_text (TRUE));

    $this->_log ('set to root with query', Msg_type_info);
    $url->set_text ('/?arg1=1&arg2=hello world&arg3=<span class="something">');

          $this->_check_equal ('/?arg1=1&arg2=hello world&arg3=<span class="something">', $url->as_text ());
          $this->_check_equal ('', $url->domain ());
          $this->_check_equal ('/', $url->path ());
          $this->_check_equal ('/', $url->path_without_domain ());
          $this->_check_equal ('', $url->name ());
          $this->_check_equal ('', $url->extension ());
          $this->_check_equal ('arg1=1&arg2=hello world&arg3=<span class="something">', $url->query_string ());
          $this->_check_equal ('', $url->name_without_extension ());
          $this->_check_equal ('arg1=1&arg2=hello world&arg3=<span class="something">', $url->name_with_query_string ());
          $this->_check_equal ('/?arg1=1&arg2=hello world&arg3=&lt;span class=&quot;something&quot;&gt;', $url->as_text (TRUE));

    $this->_log ('set to protocol and domain only', Msg_type_info);
    $url->set_text ('http://earthli.org');

          $this->_check_equal ('http://earthli.org', $url->as_text ());
          $this->_check_equal ('earthli.org', $url->domain ());
          $this->_check_equal ('http://earthli.org/', $url->path ());
          $this->_check_equal ('/', $url->path_without_domain ());
          $this->_check_equal ('', $url->name ());
          $this->_check_equal ('', $url->extension ());
          $this->_check_equal ('', $url->query_string ());
          $this->_check_equal ('', $url->name_without_extension ());
          $this->_check_equal ('', $url->name_with_query_string ());
          $this->_check_equal ('http://earthli.org', $url->as_text (TRUE));

    $this->_log ('stripped the protocol', Msg_type_info);
    $url->strip_protocol ();

          $this->_check_equal ('earthli.org', $url->as_text ());
          $this->_check_equal ('earthli.org', $url->domain ());
          $this->_check_equal ('', $url->path ());
          $this->_check_equal ('', $url->path_without_domain ());
          $this->_check_equal ('earthli.org', $url->name ());
          $this->_check_equal ('org', $url->extension ());
          $this->_check_equal ('', $url->query_string ());
          $this->_check_equal ('earthli', $url->name_without_extension ());
          $this->_check_equal ('earthli.org', $url->name_with_query_string ());
          $this->_check_equal ('earthli.org', $url->as_text (TRUE));

  }
}

?>