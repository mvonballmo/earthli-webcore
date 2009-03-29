<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage newsfeed
 * @version 3.0.0
 * @since 2.7.1
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
require_once ('webcore/gui/newsfeed_renderer.php');

/**
 * Escape text for atom html format.
 * @param string $text
 * @return string
 * @access private
 */
function _text_to_atom_html ($text)
{
  return str_replace ('<', "&lt;", str_replace ('&', "&amp;", $text));
}

/**
 * Output a piece of text to Atom format.
 * @param string $tag
 * @param string $text
 * @param string $language
 * @param boolean $is_html
 * @access private
 */
function _echo_atom_text_tag ($tag, $text, $language, $is_html)
{
  if ($is_html)
  {
    $type = 'html';
  }
  else
  {
    $type = 'text';
  }
?>
  <<?php echo $tag; ?> type="<?php echo $type; ?>" xml:lang="<?php echo $language; ?>">
    <![CDATA[<?php echo $text; ?>]]>
  </<?php echo $tag; ?>>
<?php
  }

/**
 * Renders the shell for an Atom 0.3 feed.
 * @see RSS_RENDERER
 * @package webcore
 * @subpackage newsfeed
 * @version 3.0.0
 * @since 2.7.1
 */
class ATOM_RENDERER extends NEWSFEED_RENDERER
{
  /**
   * @var string
   */
  var $style_sheet = '{styles}atom.css';
  /**
   * @var string 
   * The following override is commented because Firefox offers to download
   * this file type instead of displaying it.
   */
////  var $content_type = 'application/atom+xml';

  /**
   * Called from {@link start_display()}.
   * @param DATE_TIME $time_modified
   * @access private
   * @abstract
   */
  function _start_display ($time_modified)
  {
    parent::_start_display ($time_modified);
?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <generator uri="<?php echo $this->base_url; ?>" version="<?php echo $this->context->version; ?>">
    <?php echo $this->_as_xml ($this->generator); ?>

  </generator>
  <?php _echo_atom_text_tag ('title', $this->title->as_text (), $this->language, $this->html); ?>
  <id><?php echo $this->_as_xml ($this->base_url); ?></id>
  <link rel="self" href="<?php echo $this->_as_xml ($this->env->url (Url_part_all)); ?>"/>
  <updated><?php echo $time_modified->as_RFC_3339 (); ?></updated>
  <icon><?php echo $this->context->sized_icon ($this->icon_file, '100px'); ?></icon>
  <?php _echo_atom_text_tag ('subtitle', $this->description, $this->language, $this->html); ?>
  <?php _echo_atom_text_tag ('rights', $this->copyright, $this->language, $this->html); ?>
<?php
  }

  /**
   * Called from {@link finish_display()}.
   * @access private
   * @abstract
   */
  function _finish_display ()
  {
?>
</feed>
<?php
  }

  /**
   * Handler type to use for rendered entries.
   * @var string
   * @access private
   */
  var $_handler_type = Handler_atom_renderer;
}

/**
 * Render an {@link ENTRY} as an Atom entry.
 * @package webcore
 * @subpackage newsfeed
 * @version 3.0.0
 * @since 2.7.1
 */
class ENTRY_ATOM_RENDERER extends NEWSFEED_OBJECT_RENDERER
{
  /**
   * Draws the RSS item for this entry.
   * @param ENTRY &$obj
   * @param OBJECT_RENDERER_OPTIONS $options
   */
  function display (&$obj, $options = null)
  {
    $t = $this->_publication_date_for ($obj);
    $modifier =& $obj->modifier ();
    $content = $this->_content_for ($obj, $options);
    $html = $this->_is_html($options);

    if ($html)
    {
      $munger =& $obj->html_formatter ();
    }
    else
    {
      $munger =& $obj->plain_text_formatter ();
    }
      
    $munger->max_visible_output_chars = 300;
    $summary = $munger->transform ($obj->description, $obj);
?>
  <entry>
    <?php _echo_atom_text_tag ('title', $obj->title_as_plain_text (), $this->language, $this->html); ?>
    <id><?php echo $this->_as_xml ($obj->home_page (Force_root_on)); ?></id>
    <link href="<?php echo $this->_as_xml ($obj->home_page (Force_root_on)); ?>"/>
    <updated><?php echo $t->as_RFC_3339 (); ?></updated>
    <author>
      <?php _echo_atom_text_tag ('name', $modifier->real_name (), $this->language, $this->html); ?>
<?php if (! empty ($modifier->home_page_url)) { ?>
      <uri><?php echo $this->_as_xml ($modifier->home_page_url); ?></uri>
<?php } ?>
<?php if ($modifier->email_visibility == User_email_visible) { ?>
      <email><?php echo $this->_as_xml ($modifier->email_as_text ()); ?></email>
<?php } ?>
    </author>
    <?php _echo_atom_text_tag ('summary', $summary, $options->language, $html); ?>
    <?php _echo_atom_text_tag ('content', $content, $options->language, $html); ?>
  </entry>
<?php
  }
  
  /**
   * Get the time to display for the entry.
   * @param ENTRY &$obj
   * @return DATE_TIME
   * @access private
   */
  function _publication_date_for (&$obj)
  {
    return $obj->time_created;
  }
}

/**
 * Render a {@link DRAFTABLE_ENTRY} as an Atom entry.
 * @package webcore
 * @subpackage newsfeed
 * @version 3.0.0
 * @since 2.7.1
 */
class DRAFTABLE_ENTRY_ATOM_RENDERER extends ENTRY_ATOM_RENDERER
{
  /**
   * Get the time to display for the entry.
   * @param ENTRY &$obj
   * @return DATE_TIME
   * @access private
   */
  function _publication_date_for (&$obj)
  {
    return $obj->time_published;
  }
}

?>