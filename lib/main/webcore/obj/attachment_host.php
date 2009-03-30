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
require_once ('webcore/obj/object_in_folder.php');

/**
 * Manages a list of attachments.
 * @package webcore
 * @subpackage obj
 * @version 3.0.0
 * @since 2.5.0
 * @abstract
 */
abstract class ATTACHMENT_HOST extends OBJECT_IN_FOLDER
{
  /**
   * Create a new attachment based on this object.
   * Does not store anything in the database.
   * @return ATTACHMENT
   */
  public function new_attachment ()
  {
    $class_name = $this->context->final_class_name ('ATTACHMENT', 'webcore/obj/attachment.php');
    $Result = new $class_name ($this->context);

    $history_item = $this->handler_for (Handler_history_item);
    $Result->type = $history_item->object_type;
    $Result->set_host ($this);

    return $Result;
  }

  /**
   * @return ATTACHMENT_QUERY
   */
  public function attachment_query ()
  {
    if (! isset ($this->_attachment_query))
    {
      $class_name = $this->context->final_class_name ('ATTACHMENT_QUERY', 'webcore/db/attachment_query.php');
      $this->_attachment_query = new $class_name ($this);
    }
    return $this->_attachment_query;
  }

  /**
   * Expand all folder aliases and return a usable URL.
   * @param string $url
   * @param boolean $root_override Overrides {@link $resolve_to_root} if set to
   * {@link Force_root_on}.
   * @return string
   */
  public function resolve_url ($url, $root_override = null)
  {
    $att_link_key = '{att_link}';
    $att_thumb_key = '{att_thumb}';

    if (strpos ($url, $att_link_key) !== false)
    {
      $key = $att_link_key;
    }
    else if (strpos ($url, $att_thumb_key) !== false)
 {
   $key = $att_thumb_key;
 }

    if (isset ($key))
    {
      $history_item = $this->handler_for (Handler_history_item);

      $url = new URL (substr ($url, strlen ($key)));
      $url->prepend ('{' . Folder_name_attachments . '}/' . $history_item->object_type . '/' . $this->id . '/');

      if ($key == $att_thumb_key)
      {
        $url->append_to_name ('_tn');
      }

      $url = $url->as_text ();
    }
    
    return parent::resolve_url ($url, $root_override);
  }

  /**
   * @var ATTACHMENT_QUERY
   * @access private
   */
  protected $_attachment_query;
}

?>