<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.0.0
 * @since 2.5.0
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
require_once ('webcore/obj/auditable.php');

/**
 * Base class for textual objects.
 * All content objects have a {@link $title} and a {@link $description}.
 * @package webcore
 * @subpackage obj
 * @version 3.0.0
 * @since 2.5.0
 */
class CONTENT_OBJECT extends AUDITABLE
{
  /**
   * @var string
   */
  public $title;
  /**
   * @var string
   */
  public $description;

  /**
   * Description transformed into HTML.
   * If no specific munger is provided, the one from {@link html_formatter()} is used.
   * @param HTML_MUNGER $munger
   * @return string
   */
  function description_as_html ($munger = null)
  {
    return $this->_text_as_html ($this->description, $munger);
  }

  /**
   * Description transformed into formatted plain text.
   * If no specific munger is provided, the one from {@link plain_text_formatter()} is used.
   * @param PLAIN_TEXT_MUNGER $munger
   * @return string
   */
  function description_as_plain_text ($munger = null)
  {
    return $this->_text_as_plain_text ($this->description, $munger);
  }

  /**
   * @return string
   */
  function raw_title ()
  {
    return $this->title;
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  function load ($db)
  {
    parent::load ($db);
    $this->title = $db->f ('title');
    $this->description = $db->f ('description');
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname = $this->_table_name ();
    $storage->add ($tname, 'title', Field_type_string, $this->title);
    $storage->add ($tname, 'description', Field_type_string, $this->description);
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
   * @return object
   * @access private
   */
  function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_print_renderer:
      case Handler_html_renderer:
      case Handler_text_renderer:
        include_once ('webcore/gui/content_object_renderer.php');
        return new CONTENT_OBJECT_RENDERER ($this->app, $options);
      case Handler_mail:
        include_once ('webcore/mail/content_object_mail_renderer.php');
        return new CONTENT_OBJECT_MAIL_RENDERER ($this->app);
      case Handler_history_item:
        include_once ('webcore/obj/webcore_history_items.php');
        return new CONTENT_OBJECT_HISTORY_ITEM ($this->app);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }
}

?>