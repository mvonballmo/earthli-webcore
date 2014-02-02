<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package albums
 * @subpackage config
 * @version 3.4.0
 * @since 2.9.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli Albums.

earthli Albums is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli Albums is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli Albums; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli Albums, visit:

http://www.earthli.com/software/webcore/albums

****************************************************************************/

/** */
include_once ('webcore/config/application_engine.php');

/**
 * Creates an {@link ALBUM_APPLICATION}.
 * Inherit from this class to customize startup for the application.
 * @package albums
 * @subpackage config
 * @version 3.4.0
 * @since 2.9.0
 */
class ALBUM_APPLICATION_ENGINE extends APPLICATION_ENGINE
{
  /**
   * Register plugins in {@link $classes} during initialization.
   * @access private
   */
  protected function _initialize_class_registry ()
  {
    parent::_initialize_class_registry ();
    $this->register_class ('APPLICATION', 'ALBUM_APPLICATION', 'albums/sys/album_application.php');
  }
  
  /**
   * Customize the application object.
   * @param PAGE $page
   * @param APPLICATION $app
   * @access private
   */
  protected function _init_application ($page, $app)
  {
    parent::_init_application ($page, $app);
    $app->mail_options->send_from_address = 'albums@' . $app->env->default_domain ();
    $app->mail_options->send_from_name = $page->title->group . ' Albums';
    $app->mail_options->log_file_name = '{logs}albums_mail.log';
    $app->mail_options->entry_publication_filter = array (History_item_created);
    $app->mail_options->comment_publication_filter = array (History_item_created);

    $app->storage->prefix = 'webcore_albums_';
  }
}

?>