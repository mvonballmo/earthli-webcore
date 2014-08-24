<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage config
 * @version 3.5.0
 * @since 2.5.0
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

/**
 * Request no text validation.
 * @see CONTEXT::tag_validator()
 */
define ('Tag_validator_none', '');
/**
 * Request a validator for multi-line text.
 * @see CONTEXT::tag_validator()
 */
define ('Tag_validator_single_line', 'single-line');
/**
 * Request a validator for single-line text.
 * @see CONTEXT::tag_validator()
 */
define ('Tag_validator_multi_line', 'multi-line');

/**
 * Request a {@link CSS_TAG_BUILDER}.
 * @see CONTEXT::make_tag_builder()
 */
define ('Tag_builder_css', 'css_tag');
/**
 * Request an {@link HTML_TAG_BUILDER}.
 * @see CONTEXT::make_tag_builder()
 */
define ('Tag_builder_html', 'html_tag');

/**
 * @see CONTEXT::$display_options
 * @package webcore
 * @subpackage config
 * @version 3.5.0
 * @since 2.2.1
 */
class CONTEXT_DISPLAY_OPTIONS
{
  /**
   * Separator string used between items in menus
   * @var string
   */
  public $menu_separator = ' | ';

  public $menu_class = 'standard';
  public $location_class = 'location';
  public $object_class = 'objects';

  /**
   * Separator string used between page numbers in page navigators.
   * @var string
   */
  public $page_separator = '';

  /**
   * Separator string used between locations in the navigation bar.
   * @var string
   */
  public $location_separator = ' &gt; ';

  /**
   * Separator string used between objects in any context.
   * @var string
   */
  public $object_separator = ' &gt; ';

  /**
   * Maximum number of page numbers to show in page navigators.
   * @var integer
   */
  public $pages_to_show = 7;

  /**
   * Maximum number of objects to show in the list of an entry navigator.
   * @var integer
   */
  public $objects_to_show = 25;

  /**
   * Use this extensions for icons that don't specify one.
   * @var string
   */
  public $default_icon_extension = 'png';

  /**
   * Should the interface use DHTML to render?
   * This option is assumed False if the browser is not DHTML-capable. Use
   * {@link CONTEXT::dhtml_allowed()} to check this option.
   * @var boolean
   */
  public $use_DHTML = true;

  /**
   * Use JavaScript to display local times in this application?
   * @var boolean
   */
  public $show_local_times = true;

  /**
   * Specify a maximum number of characters to display for object titles.
   * @var integer
   */
  public $default_max_title_size = 40;

  /**
   * Use this title size for all elements (temporarily).
   * Set this if you are rendering content in such a way that you know the titles will
   * always fit. The maximum title sizes are actually for navigation bars and lists; generally
   * grids and title bars have enough room for the whole title.
   * @var integer
   */
  public $overridden_max_title_size = 0;

  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    $this->context = $context;
  }

  /**
   * @param CONTEXT_DISPLAY_OPTIONS
   */
  public function copy_from ($opts)
  {
    $this->menu_separator = $opts->menu_separator;
    $this->page_separator = $opts->page_separator;
    $this->location_separator = $opts->location_separator;
    $this->object_separator = $opts->object_separator;
    $this->pages_to_show = $opts->pages_to_show;
    $this->objects_to_show = $opts->objects_to_show;
    $this->default_icon_extension = $opts->default_icon_extension;
    $this->use_DHTML = $opts->use_DHTML;
    $this->show_local_times = $opts->show_local_times;
    $this->default_max_title_size = $opts->default_max_title_size;
    $this->overridden_max_title_size = $opts->overridden_max_title_size;
  }

  /**
   * @var CONTEXT
   * @access private
   */
  public $context;
}

/**
 * @see CONTEXT::$mail_options
 * @package webcore
 * @subpackage config
 * @version 3.5.0
 * @since 2.2.1
 */
class CONTEXT_MAIL_OPTIONS
{
  /**
   * @var boolean
   */
  public $send_as_html = true;

  /**
   * @var boolean
   */
  public $enabled = true;

  /**
   * @var string
   */
  public $webmaster_address = 'webmaster@my-domain-name.com';

  /**
   * @var string
   */
  public $send_from_address = 'webcore@my-domain-name.com';

  /**
   * @var string
   */
  public $send_from_name = 'WebCore';

  /**
   * @var string
   */
  public $log_file_name;

  /**
   * @var boolean
   */
  public $logging_enabled = true;

  /**
   * @var string
   */
  public $SMTP_server = '';

  /**
   * Determines which entry history items should be published.
   * Publishes all history items if empty.
   * @see History_item_state_constants
   * @var string
   */
  public $entry_publication_filter;

  /**
   * Determines which comment history items should be published.
   * Publishes all history items if empty.
   * @see History_item_state_constants
   * @var string
   */
  public $comment_publication_filter;

  /**
   * @param CONTEXT_MAIL_OPTIONS
   */
  public function copy_from ($opts)
  {
    $this->send_as_html = $opts->send_as_html;
    $this->enabled = $opts->enabled;
    $this->webmaster_address = $opts->webmaster_address;
    $this->send_from_address = $opts->send_from_address;
    $this->send_from_name = $opts->send_from_name;
    $this->log_file_name = $opts->log_file_name;
    $this->logging_enabled = $opts->logging_enabled;
    $this->SMTP_server = $opts->SMTP_server;
  }
}

/**
 * @see CONTEXT::$database_options
 * @package webcore
 * @subpackage config
 * @version 3.5.0
 * @since 2.5.0
 */
class CONTEXT_DATABASE_OPTIONS
{
  /**
   * @var string
   */
  public $host = 'localhost';

  /**
   * @var string
   */
  public $name;

  /**
   * @var string
   */
  public $user_name = 'root';

  /**
   * @var string
   */
  public $password;

  /**
   * @param CONTEXT_DATABASE_OPTIONS
   */
  public function copy_from ($opts)
  {
    $this->host = $opts->host;
    $this->name = $opts->name;
    $this->user_name = $opts->user_name;
    $this->password = $opts->password;
  }
}

/**
 * @see CONTEXT::$upload_options
 * @package webcore
 * @subpackage config
 * @version 3.5.0
 * @since 2.5.0
 */
class CONTEXT_UPLOAD_OPTIONS
{
  /**
   * If this is non-empty, successful uploads for invalid forms will be stored
   * here until the form is submitted correctly. That is, if a user submits a
   * form with an upload and the upload succeeds while the form does not, the
   * user will not have to upload the file again in order to submit the form
   * with that file.
   *
   * In order for preview for uploaded files to function correctly, this folder
   * should be under the document root. A simple scheduled task can periodically
   * cull files that have been uploaded and abandoned.
   * @see FORM::_process_uploaded_file()
   */
  public $temp_folder = '{data}temp';

  /**
   * Indicate the maximum upload size.
   * Uses PHP units like "2M"
   * @var string
   */
  public $max_size = '2M';

  /**
   * Indicate whether file names should be converted to lower case by default.
   * @var boolean
   */
  public $use_lower_case_file_names = true;

  /**
   * @param CONTEXT_UPLOAD_OPTIONS $opts
   */
  public function copy_from ($opts)
  {
    $this->temp_folder = $opts->temp_folder;
  }
}

/**
 * Options used to store date to a client.
 * @package webcore
 * @subpackage config
 * @version 3.5.0
 * @since 2.6.1
 */
class CONTEXT_STORAGE_OPTIONS
{
  /**
   * Number of days to store a setting.
   * @var integer
   */
  public $setting_duration = 180;
}