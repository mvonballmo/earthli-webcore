<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage newsfeed
 * @version 3.1.0
 * @since 2.7.1
 */

/****************************************************************************

Copyright (c) 2002-2009 Marco Von Ballmoos

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
require_once ('webcore/obj/webcore_object.php');
require_once ('webcore/gui/object_renderer.php');
require_once ('webcore/util/html_munger.php');
require_once ('webcore/gui/default_page_renderer.php');

/**
 * Provides basic functionality for newsfeed pages.
 * Call {@link start_display()} and {@link finish_display()} to draw the
 * header and footer or pass a query to {@link display()} to render a feed from
 * a list of objects. Most properties are automatically initialized from the
 * {@link $context}, but you should set the {@link $description} to a
 * descriptive summary.
 *
 * Note: this renderer will most likely place an XML document into the response.
 * Do not place any other content into the response or there will be a warning
 * when PHP tries to set the header to "text/xml".
 *
 * @package webcore
 * @subpackage newsfeed
 * @version 3.1.0
 * @since 2.7.1
 */
abstract class NEWSFEED_RENDERER extends WEBCORE_OBJECT
{
  /**
   * @var PAGE_TITLE
   */
  public $title;

  /**
   * @var string
   */
  public $base_url;

  /**
   * @var string
   */
  public $description = '';

  /**
   * @var string
   */
  public $language = 'en-us';

  /**
   * @var string
   */
  public $style_sheet = '{styles}newsfeed.css';

  /**
   * Image to display with the RSS feed.
   * Initialized to the {@link PAGE_TEMPLATE_OPTIONS::$icon} in the constructor.
   * @var string
   */
  public $icon_file;

  /**
   * Copyright notice to display with the RSS feed.
   * Initialized to the {@link PAGE_TEMPLATE_OPTIONS::$copyright} in the
   * constructor.
   * @var string
   */
  public $copyright;

  /**
   * The name of the generator for the RSS.
   * Initialized to the {@link CONTEXT::title()} in the constructor.
   * @var string
   */
  public $generator;

  /**
   * Describes the format of the newsfeed articles.
   * @var boolean
   */
  public $article_format = Newsfeed_content_full_html;

  /**
   * The content=type to use for the HTTP response.
   * @var string
   */
  public $content_type = 'application/xml';

  /**
   * The character set to use for the HTTP response.
   * @var string
   */
  public $character_set = 'iso-8859-1';

  /**
   * @param CONTEXT $context
   */
  public function __construct ($context)
  {
    parent::__construct ($context);

    $class_name = $this->page->final_class_name ('PAGE_TITLE', 'webcore/gui/page_title.php');
    $this->title = new $class_name ($this->page);
    $this->title->group = $context->description ();
    $this->title->prefix = '';
    $this->title->suffix = '';

    $this->base_url = $this->context->url ();
    $this->generator = $this->context->description ();
    $this->copyright = $this->page->template_options->copyright;
    $this->icon_file = $this->page->template_options->icon;
  }

  /**
   * Read in the description from this object.
   * Descendents can decide which description (HTML or plain text) to use based
   * on the {@link $html} flag or other properties.
   * @param CONTENT_OBJECT $obj
   */
  public function set_description_from ($obj)
  {
    switch ($this->article_format)
    {
      case Newsfeed_content_html:
      case Newsfeed_content_full_html:
        $this->description = $obj->description_as_html ();
        break;
      case Newsfeed_content_text:
        $this->description = $obj->description_as_plain_text ();
        break;
    }
  }

  /**
   * Display RSS for a query.
   * Will apply sorting/filtering appropriate to the RSS feed using {@link
   * _prepare_query()} and {@link _prepare_sort()}; descendents can override
   * this behavior. Configures {@link NEWSFEEDER_RENDERER_OPTIONS} and passes
   * them with each object from the query result to a {@link
   * NEWSFEED_OBJECT_RENDERER}.
   * @param QUERY $query
   */
  public function display ($query)
  {
    $this->_prepare_globals ();

    $this->_prepare_query ($query);
    $objs = $query->objects ();

    $time_modified = $this->context->make_date_time ();
    if (! empty ($objs))
    {
      $time_modified->clear ();
      foreach ($objs as $obj)
      {
        if ($time_modified->less_than ($obj->time_modified))
        {
          $time_modified = $obj->time_modified;
        }
      }
    }

    /* Determine the handler to use for rendering the actual content of the
     * item. Each newsfeed item renderer can determine how it interprets this
     * hint. For items that render as HTML, a special page renderer is
     * provided which items should use to wrap their content.
     */

    $class_name = $this->context->final_class_name ('NEWSFEEDER_RENDERER_OPTIONS');
    $options = new $class_name ();

    $options->language = $this->language;
    $options->use_envelope = false;
    
    switch ($this->article_format)
    {
      case Newsfeed_content_html:
        $options->handler_type = Handler_html_renderer;
        break;
      case Newsfeed_content_full_html:
        $options->use_envelope = true;
        $options->handler_type = Handler_html_renderer;
        $class_name = $this->context->final_class_name ('NEWSFEED_PAGE_RENDERER');
        $options->page_renderer = new $class_name ($this->context);
        break;
      case Newsfeed_content_text:
        $options->handler_type = Handler_text_renderer;
        break;
    }

    $this->start_display ($time_modified, $options);

    if (sizeof ($objs))
    {
      /* The renderer is assumed to be stateless, so use the same one
       * for all objects. We use the renderer returned by the first
       * element.
       */

      $renderer = $objs [0]->handler_for ($this->_handler_type);
      foreach ($objs as $obj)
      {
        $renderer->display ($obj, $options);
      }
    }

    $this->finish_display ($options);
  }

  /**
   * Start the RSS feed (show the header).
   * 
   * @param DATE_TIME $time_modified
   * @param NEWSFEED_RENDERER_OPTIONS $options
   */
  public function start_display ($time_modified, $options)
  {
    $this->assert (! empty ($this->content_type), 'Content type cannot be empty.', 'start_display', 'NEWSFEED_RENDERER');

    ob_clean ();
    $type = 'Content-type: ' . $this->content_type;
    if (! empty ($this->character_set))
    {
      $type .= '; charset=' . $this->character_set;
    }
    header ($type);

    $this->_start_display ($time_modified, $options);
  }

  /**
   * Finish the RSS feed (show the footer)
   * 
   * @param NEWSFEED_RENDERER_OPTIONS $options
   */
  public function finish_display ($options)
  {
    $this->_finish_display ($options);
  }

  /**
   * Called from {@link start_display()}.
   * 
   * @param DATE_TIME $time_modified
   * @param NEWSFEED_RENDERER_OPTIONS $options
   * @access private
   */
  protected function _start_display ($time_modified, $options)
  {
    if (! empty ($this->character_set))
    {
      echo '<?xml version="1.0" encoding="' . $this->character_set . '" ?>';
    }
    else
    {
      echo '<?xml version="1.0" ?>';
    }
    echo '<?xml-stylesheet type="text/css" href="' . $this->context->resolve_file ($this->style_sheet) . '" ?>';
  }

  /**
   * Called from {@link finish_display()}.
   * 
   * @param NEWSFEED_RENDERER_OPTIONS $options
   * @access private
   * @abstract
   */
  protected abstract function _finish_display ($options);

  /**
   * Adjust the query for RSS display.
   * Called from {@link display()}; calls {@link _prepare_sort()}.
   * @param QUERY $query
   * @access private
   */
  protected function _prepare_query ($query)
  {
    $query->set_filter (Visible);
    $this->_prepare_sort ($query);
    $class_name = $this->context->final_class_name ('TIME_FRAME_SELECTOR', 'webcore/gui/time_frame_selector.php');
    $selector = new $class_name ($this->context, Time_frame_recent);
    $selector->prepare_query ($query);
  }

  /**
   * Apply the desired sorting for RSS.
   * Called from {@link prepare_query()}.
   * @param QUERY $query
   * @access private
   */
  protected function _prepare_sort ($query)
  {
    $query->set_order ('entry.time_created DESC');
  }

  /**
   * Initialize display options for remote content.
   * Called from {@link display()}.
   * @access private
   */
  protected function _prepare_globals ()
  {
    /* Make all URLs absolute. */
    $this->context->set_root_behavior (Force_root_on);

    $opts = $this->context->display_options;
    $opts->overridden_max_title_size = 150;
    $opts->use_DHTML = false;
    $opts->show_local_times = false;

    $opts = $this->page->template_options;

    /* No Javascript in newsfeeds and no browser check. */
    $opts->include_scripts = false;
    $opts->check_browser = false;

    /* Remove footer details, but keep contact/support/privacy links */
    $opts->show_statistics = false;
    $opts->show_last_time_modified = false;
    $opts->show_links = true;
    
    /* Remove header links */
    $opts->show_login = false;
    $opts->settings_url = '';
    $opts->show_source_url = '';
    /* Just remove the header entirely (options above have effect only if this
     * is toggled back to true.
     */
    $opts->header_visible = false;
    
    $this->context->register_class ('HTML_TEXT_MUNGER', 'NEWSFEED_HTML_TEXT_MUNGER');
  }

  /**
   * Convert all reserved characters to XML entities.
   * @param string $text Text to convert
   * @return string
   * @access private
   */
  protected function _as_xml ($text)
  {
    return $this->context->text_options->convert_to_html_entities ($text);
  }

  /**
   * Handler type to use for rendered entries.
   * Use any of the {@link Handler_constants}.
   * @var string
   * @access private
   */
  protected $_handler_type;
}

/**
 * Base class for newsfeed item renderers.
 * @package webcore
 * @subpackage newsfeed
 * @version 3.1.0
 * @since 2.7.1
 */
class NEWSFEED_OBJECT_RENDERER extends HANDLER_RENDERER
{
  /**
   * Return the appropriate renderer for the given object and options.
   * @param RENDERABLE $obj
   * @param NEWSFEEDER_RENDERER_OPTIONS $options
   * @access private
   */
  protected function _content_for ($obj, $options = null)
  {
    if (isset ($options))
    {
      $handler_type = $options->handler_type;
      $use_envelope = $options->use_envelope;
    }
    else
    {
      $handler_type = Handler_text_renderer;
      $use_envelope = false;
    }

    $renderer = $obj->handler_for ($handler_type);
    $obj_options = $renderer->options ();
    $obj_options->show_interactive = false;
    $obj_options->preferred_text_length = $options->preferred_text_length;
    $Result = $renderer->display_to_string ($obj);

    if ($use_envelope && ($handler_type == Handler_html_renderer))
    {
      $browser = $this->env->browser ();
      if ($browser->supports (Browser_extended_HTML_newsfeeds))
      {
        $Result = $options->page_renderer->start_display_as_text () .
                  $Result .
                  $options->page_renderer->finish_display_as_text ();
      }
    }

    return $Result;
  }

  /**
   * Return a {@link MUNGER} for the given object.
   * 
   * @param NAMED_OBJECT $obj
   * @param OBJECT_RENDERER_OPTIONS $options
   * @return MUNGER
   * @access private
   */
  protected function _make_formatter ($obj, $options)
  {
    if ($this->_is_html ($options))
    {
      return $obj->html_formatter ();
    }

    return $obj->plain_text_formatter ();
  }
  
  /**
   * Returns true if the content is to be rendered as HTML.
   *
   * @param OBJECT_RENDERER_OPTIONS $options
   * @return boolean
   * @access private
   */
  protected function _is_html ($options)
  {
    return isset ($options) && ($options->handler_type == Handler_html_renderer);
  }

  /**
   * Convert all reserved characters to XML entities.
   * @param string $text Text to convert
   * @return string
   * @access private
   */
  protected function _as_xml ($text)
  {
    return $this->context->text_options->convert_to_html_entities ($text);
  }
}

/**
 * Rendering options used by a {@link NEWSFEED_RENDERER}.
 * @package webcore
 * @subpackage newsfeed
 * @version 3.1.0
 * @since 2.7.1
 * @access private
 */
class NEWSFEEDER_RENDERER_OPTIONS extends OBJECT_RENDERER_OPTIONS
{
  /**
   * Which handler should be used to render sub-items?
   * 
   * Newsfeed items will generally wrap another renderer with feed-specific
   * tags. The renderer for the item should get a handler for this type (or
   * at least use the type to determine whick kind of custom content it will
   * render. Can be any of the {@link Handler_constants}.
   * 
   * @var string
   */
  public $handler_type;

  /**
   * Language code to use for output in this feed.
   * 
   * @var string
   */
  public $language = 'en-us';
  
  /**
   * Should content be wrapped in an header and footer?
   * 
   * The HTML renderer, for example, wraps content in a fully valid HTML page
   * when this value is true.
   *
   * @var boolean
   */
  public $use_envelope = true;

  /**
   * Page renderer to use for an HTML envelope.
   * 
   * If HTML output is needed, renderers should call {@link
   * PAGE_RENDERER::start_display_as_text()} and {@link PAGE_RENDERER::
   * finish_display_as_text()} before and after the entry's content.
   * 
   * @var PAGE_RENDERER
   */
  public $page_renderer;
}

/**
 * Formats a video or media for a newsfeed.
 * @package webcore
 * @subpackage newsfeed
 * @version 3.1.0
 * @since 2.7.1
 * @access private
 */
class NEWSFEED_MEDIA_REPLACER extends HTML_MEDIA_REPLACER
{
  /**
   * Return the tags and text for the movie.
   * @param string $src The url to the movie.
   * @param string $type An identifier for the type of tag to embed (e.g.
   * youtube).
   * @param ARRAY[string, string] $attrs List of attributes for the tag.
   * @return string
   * @access private
   */
  protected function _movie_as_text ($src, $type, $attrs)
  {
    return $this->_default_movie_link ($src);
  }
}

/**
 * Formats the header and footer for HTML newsfeed items.
 * @package webcore
 * @subpackage newsfeed
 * @version 3.1.0
 * @since 2.7.1
 * @access private
 */
class NEWSFEED_PAGE_RENDERER extends DEFAULT_PAGE_RENDERER
{
  /**
   * Draws the standard HTML header using {@link PAGE} settings.
   * Display only style sheets in a newsfeed (to provide correct formatting, but
   * no other "confusing" elements for non-conforming newsreaders.
   */
  public function display_head ()
  {
    $this->display_styles_and_scripts ();
  }

  /**
   * Override to suppress writing the document type.
   */
  public function display_doc_type ()
  {
  }
}

class NEWSFEED_HTML_TEXT_MUNGER extends HTML_TEXT_MUNGER
{
  public function __construct ()
  {
    parent::__construct ();

    $this->register_replacer ('fn', new NEWSFEED_FOOTNOTE_REFERENCE_REPLACER (), false);
    $this->register_replacer ('ft', new NEWSFEED_FOOTNOTE_TEXT_REPLACER ());
    $this->register_replacer ('media', new NEWSFEED_MEDIA_REPLACER (), false);
  }
}

/**
 * Links a block of text to a previous footnote reference.
 * @package webcore
 * @subpackage newsfeed
 * @version 3.1.0
 * @since 2.7.1
 * @access private
 */
class NEWSFEED_FOOTNOTE_TEXT_REPLACER extends HTML_FOOTNOTE_TEXT_REPLACER
{
  /**
   * Format the text for the given footnote number.
   * @param MUNGER $munger The transformation context.
   * @param MUNGER_TOKEN $token
   * @param MUNGER_FOOTNOTE_INFO $info
   * @return string
   * @access private
   */
  protected function _format_text ($munger, $token, $info)
  {
    if (! $token->is_start_tag ())
    {
      return '</div>';
    }

    return parent::_format_text ($munger, $token, $info);
  }
}

/**
 * Adds a link to a footnote, numbering automatically.
 * @package webcore
 * @subpackage newsfeed
 * @version 3.1.0
 * @since 2.7.1
 * @access private
 */
class NEWSFEED_FOOTNOTE_REFERENCE_REPLACER extends MUNGER_FOOTNOTE_REFERENCE_REPLACER
{
  /**
   * Format the reference to the given footnote number.
   * 
   * @param MUNGER $munger The munger that generated the call; cannot be null.
   * @param MUNGER_TOKEN $token The token being processed; cannot be null.
   * @param MUNGER_FOOTNOTE_INFO $info The footnote to format; cannot be null.
   * @return string
   * @access private
   */
  protected function _format_reference ($munger, $token, $info)
  {
    return " [$info->number]";
  }
}


?>
