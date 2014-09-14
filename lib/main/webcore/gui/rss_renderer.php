<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage newsfeed
 * @version 3.6.0
 * @since 2.7.0
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
require_once ('webcore/gui/newsfeed_renderer.php');

/**
 * Renders the shell for an RSS 2.0 feed.
 * Adds some RSS-specific properties to the standard {@link NEWSFEED_RENDERER},
 * including a link to a {@link $more_info_page}, on which the site should
 * explain what newsfeeds are.
 * @see ATOM_RENDERER
 * @package webcore
 * @subpackage newsfeed
 * @version 3.6.0
 * @since 2.7.0
 */
class RSS_RENDERER extends NEWSFEED_RENDERER
{
  /**
   * @var string
   */
  public $style_sheet = '{styles}rss.css';

  /**
   * @var string
   */
  public $more_info_page = '{pages}rss_info.php';

  /**
   * @var integer
   */
  public $ttl_in_minutes = 720;

  /**
   * @var string
   * The following override is commented because Firefox offers to download
   * this file type instead of displaying it.
   */
////  public $content_type = 'application/rss+xml';
  
  /**
   * Read in the description from this object.
   * RSS always uses plain text for descriptions (HTML is not allowed).
   * @param CONTENT_OBJECT $obj
   */
  public function set_description_from ($obj)
  {
    $this->description = $obj->description_as_plain_text ();
  }
  
  /**
   * Called from {@link start_display()}.
   * 
   * @param DATE_TIME $time_modified
   * @param NEWSFEED_RENDERER_OPTIONS $options
   * @access private
   * @abstract
   */
  protected function _start_display ($time_modified, $options)
  {
    parent::_start_display ($time_modified, $options);
?>
<rss version="2.0">
  <channel>
    <docs>http://www.rssboard.org/rss-specification</docs>
    <generator><?php echo $this->_as_xml ($this->generator); ?></generator>
    <language><?php echo $this->_as_xml ($this->language); ?></language>
    <ttl><?php echo $this->_as_xml ($this->ttl_in_minutes); ?></ttl>
    <title><![CDATA[<?php $this->title->display (); ?>]]></title>
    <link><?php echo $this->_as_xml ($this->base_url); ?></link>
    <pubDate><?php echo $time_modified->as_RFC_2822 (); ?></pubDate>
    <lastBuildDate><?php echo $time_modified->as_RFC_2822 (); ?></lastBuildDate>
    <image>
      <link><?php echo $this->_as_xml ($this->base_url); ?></link>
      <title><![CDATA[<?php $this->title->display (); ?>]]></title>
      <url><?php echo $this->context->get_icon_url ($this->icon_file, One_hundred_px); ?></url>
    </image>
    <description><![CDATA[<?php echo $this->_as_xml ($this->description); ?>]]></description>
    <copyright><![CDATA[<?php echo $this->_as_xml ($this->copyright); ?>]]></copyright>
<?php
  }

  /**
   * Called from {@link finish_display()}.
   *
   * @param NEWSFEED_RENDERER_OPTIONS $options
   * @access private
   * @abstract
   */
  protected function _finish_display ($options)
  {
?>
  </channel>
</rss>
<?php
  }

  /**
   * Handler type to use for rendered entries.
   * @var string
   * @access private
   */
  protected $_handler_type = Handler_rss_renderer;
}

/**
 * Render an {@link ENTRY} as an RSS item.
 * @package webcore
 * @subpackage newsfeed
 * @version 3.6.0
 * @since 2.7.0
 */
class ENTRY_RSS_RENDERER extends NEWSFEED_OBJECT_RENDERER
{
  /**
   * Draws the RSS item for this entry.
   * @param ENTRY $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  public function display ($obj, $options = null)
  {
    $t = $this->_publication_date_for ($obj);
    $content = $this->_content_for ($obj, $options);
?>
  <item>
    <guid><?php echo $obj->home_page (Force_root_on); ?></guid>
    <title><![CDATA[<?php echo $this->_as_xml ($obj->title_as_plain_text ()); ?>]]></title>
    <link><?php echo $this->_as_xml ($obj->home_page (Force_root_on)); ?></link>
    <pubDate><?php echo $t->as_RFC_2822 (); ?></pubDate>
    <description><![CDATA[<?php echo $content; ?>]]></description>
  </item>
<?php
  }
  
  /**
   * Get the time to display for the entry.
   * @param ENTRY $obj
   * @return DATE_TIME
   * @access private
   */
  protected function _publication_date_for ($obj)
  {
    return $obj->time_created;
  }
}

/**
 * Render a {@link DRAFTABLE_ENTRY} as an RSS item.
 * @package webcore
 * @subpackage newsfeed
 * @version 3.6.0
 * @since 2.7.0
 */
class DRAFTABLE_ENTRY_RSS_RENDERER extends ENTRY_RSS_RENDERER
{
  /**
   * Get the time to display for the entry.
   * @param ENTRY $obj
   * @return DATE_TIME
   * @access private
   */
  protected function _publication_date_for ($obj)
  {
    return $obj->time_published;
  }
}

?>
