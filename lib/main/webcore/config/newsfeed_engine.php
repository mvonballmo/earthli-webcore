<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage config
 * @version 3.6.0
 * @since 2.7.1
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
require_once ('webcore/constants.php');
require_once ('webcore/obj/webcore_object.php');

/**
 * Creates newsfeed renderers from a request.
 * Use {@link make_renderer()} to create a handler for the requested protocol
 * and format.
 * @package webcore
 * @subpackage config
 * @version 3.6.0
 * @since 2.7.1
 */
class NEWSFEED_ENGINE extends WEBCORE_OBJECT
{
  /**
   * Type of newsfeed to create with {@link make_renderer()}.
   * @var string
   */
  public $format = Newsfeed_format_atom;

  /**
   * Type of content in newsfeed items/entries.
   * @var string
   */
  public $content = Newsfeed_content_html;
  
  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);
    $this->format = read_var ('format', Newsfeed_format_rss);
    $this->content = read_var ('content', Newsfeed_content_html);
  }
  
  /**
   * Return a newsfeed renderer for the given folder.
   * @param OBJECT_IN_FOLDER $obj
   */
  public function make_renderer ($obj)
  {
    $f = $this->_format_with_fallback ();
    switch ($f)
    {
      case Newsfeed_format_atom:
        $class_name = $this->context->final_class_name ('ATOM_RENDERER', 'webcore/gui/atom_renderer.php', 'index');
        break;
      case Newsfeed_format_rss:
        $class_name = $this->context->final_class_name ('RSS_RENDERER', 'webcore/gui/rss_renderer.php', 'index');
        break;
    }

    $Result = new $class_name ($this->context);
  
    $Result->article_format = $this->_content_with_fallback ();
    $Result->set_description_from ($obj);
    
    if (is_a ($obj, 'FOLDER'))
    {
      if ($obj->is_root ())
      {
        $Result->title->subject = 'all';
      }
      else
      {
        $Result->title->add_object ($obj);
      }
    }
      
    return $Result;
  }

  /**
   * Validate {@link $format}, return a default for non-valid input.
   * @return string
   * @access private
   */
  protected function _format_with_fallback ()
  {
    $supported_formats = array (Newsfeed_format_atom, Newsfeed_format_rss);
    if (in_array ($this->format, $supported_formats))
    {
      $Result = $this->format;
    }
    else
    {
      log_message ("[$this->format] is not a valid newsfeed format.", Msg_type_warning, Msg_channel_newsfeed);
      $Result = Newsfeed_format_atom;
    }
    return $Result;
  }
  
  /**
   * Validate {@link $content}, return a default for non-valid input.
   * @return string
   * @access private
   */
  protected function _content_with_fallback ()
  {
    $supported_formats = array (Newsfeed_content_full_html, Newsfeed_content_html, Newsfeed_content_text);
    if (in_array ($this->content, $supported_formats))
    {
      $Result = $this->content;
    }
    else
    {
      log_message ("[$this->content] is not a valid newsfeed content type.", Msg_type_warning, Msg_channel_newsfeed);
      $Result = Newsfeed_content_html;
    }
    return $Result;
  }
}

?>