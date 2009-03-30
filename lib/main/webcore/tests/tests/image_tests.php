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

/** */
require_once ('webcore/tests/test_task.php');
require_once ('webcore/util/image.php');

/**
 * @package webcore
 * @subpackage tests
 * @version 3.0.0
 * @since 2.7.0
 * @access private
 */
class IMAGE_TEST_TASK extends TEST_TASK
{
  protected function _set_image ($img, $f, $include_exif = false, $acceptable_time = 10)
  {
    $this->env->profiler->restart ('image.load');
    $img->set_file ($f, $include_exif);
    $actual_time = $this->env->profiler->elapsed ('image.load');
    $this->_check ($actual_time < $acceptable_time, 'Image [' . $f . '] loaded in less than ' . $acceptable_time . 's (actual time was ' . $actual_time . 's)');
  }

  protected function _check_local_urls ()
  {
    $this->page->prefer_relative_urls = false;
    $local_url = $this->context->resolve_file ('{icons}/buttons/blank_16px.png');
    $root_url = $this->context->path_to (Folder_name_root);
    $image_file_name = substr ($local_url, strlen ($root_url));
    $local_url_with_domain = $this->env->resolve_file ($local_url, Force_root_on);
    $local_path = url_to_file_name ($local_url_with_domain);

    $img = new IMAGE ();

    $this->_set_image ($img, $local_url_with_domain);
    $this->_check_equal ($local_url_with_domain, $img->properties->url);
    $this->_check_equal ($local_path, $img->properties->file_name);

    $this->_set_image ($img, $local_url_with_domain, true);
    $this->_check_equal ($local_url_with_domain, $img->properties->url);
    $this->_check_equal ($local_path, $img->properties->file_name);

    $this->_set_image ($img, $local_path);

    $relative_image_url = path_between ($img->properties->url, $local_url, global_url_options());

    $this->_check_equal ($local_url, $relative_image_url);
    $this->_check_equal ($local_path, $img->properties->file_name);

    $relative_image_url = path_between ($img->properties->url, $local_url, global_url_options());

    $this->_set_image ($img, $local_path, true);
    $this->_check_equal ($local_url, $relative_image_url);
    $this->_check_equal ($local_path, $img->properties->file_name);

    /* Check non-existent local file. */

    $this->_set_image ($img, $local_url_with_domain . '.does.not_exist');
    $this->_check_equal ($local_url_with_domain . '.does.not_exist', $img->properties->url);
    $this->_check_equal ('', $img->properties->file_name);
  }

  protected function _check_external_urls ()
  {
    $remote_url = 'http://www.oalgar.com/albums/orhan/images/';
    $img = new IMAGE ();

    /* Check external file. */

    $this->_set_image ($img, $remote_url . '04020111_tn.jpg');
    $this->_check_equal ($remote_url . '04020111_tn.jpg', $img->properties->url);
    $this->_check_equal ('', $img->properties->file_name);

    $this->_set_image ($img, $remote_url . '04020111_tn.jpg', true);
    $this->_check_equal ($remote_url . '04020111_tn.jpg', $img->properties->url);
    $this->_check_equal ('', $img->properties->file_name);

    /* Check non-existent external file. */

    $this->_set_image ($img, $remote_url . '04020111_tn.jpg.does.not.exist');
    $this->_check_equal ($remote_url . '04020111_tn.jpg.does.not.exist', $img->properties->url);
    $this->_check_equal ('', $img->properties->file_name);

    $this->_set_image ($img, $remote_url . '04020111_tn.jpg.does.not.exist', true);
    $this->_check_equal ($remote_url . '04020111_tn.jpg.does.not.exist', $img->properties->url);
    $this->_check_equal ('', $img->properties->file_name);
  }

  protected function _execute ()
  {
    $this->_check_local_urls ();
    $this->_check_external_urls ();
  }
}

?>