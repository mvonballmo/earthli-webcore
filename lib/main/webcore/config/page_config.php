<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @version 3.6.0
 * @since 2.5.0
 * @package webcore
 * @subpackage config
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
require_once ('webcore/config/context_config.php');

/**
 * @see PAGE_TITLE, PAGE::$page_title
 * @package webcore
 * @subpackage config
 * @version 3.6.0
 * @since 2.2.1
 */
class PAGE_TITLE_OPTIONS
{
  /**
   * Separates objects added to the title.
   * Also separates the object list from the group and subject.
   * @var string
   */
  public $separator = ' &gt; ';

  /**
   * Starts every page title.
   * @var string
   */
  public $prefix = '..:: ';

  /**
   *  Finishes every page title.
   * @var string
   */
  public $suffix = ' ::..';

  /**
   * @var string
   */
  public $group;
}

/**
 * @see PAGE::$refresh_options
 * @package webcore
 * @subpackage config
 * @version 3.6.0
 * @since 2.2.1
 */
class PAGE_REFRESH_OPTIONS
{
  /**
   * @var boolean
   */
  public $enabled = false;

  /**
   * @var string
   */
  public $url;

  /**
   * Given in seconds.
   * @var integer
   */
  public $duration = 5;

  public function display ()
  {
    if ($this->enabled)
    {
      if ($this->url)
      {
        ?>
        <meta http-equiv="refresh" content="<?php echo $this->duration; ?>;url=<?php echo $this->url; ?>">
        <?php
      }
      else
      {
        ?>
        <meta http-equiv="refresh" content="<?php echo $this->duration; ?>">
        <?php
      }
    }
  }

  /**
   * @param PAGE $page
   */
  public function __construct ($page)
  {
    $this->page = $page;
  }
}

/**
 * @see PAGE::$social_options
 * @package webcore
 * @subpackage config
 * @version 3.6.0
 * @since 3.6.0
 */
class PAGE_SOCIAL_OPTIONS
{
  /**
   * @var string
   *
   * This required tag works in a similar way to og:type. It describes the type of content you are sharing.
   * There are 7 options to choose from: summary, photo, video, product, app, gallery, and “large version" summary.
   *
   * Maps to <meta name="twitter:card" content="summary" />
   */
  public $twitter_card = 'summary_large_image';

  /**
   * @var string
   *
   * Maps to <meta name="twitter:site" content="@mvonballmo">
   */
  public $twitter_site = '';

  /**
   * @var string
   *
   * Maps to <meta name="twitter:creator" content="@mvonballmo">
   */
  public $twitter_account = ''; // TODO Add this to the user and use that instead

  /**
   * @var string
   *
   * Maps to <meta property="article:author" content="Your eye-catching title here" />
   */
  public $author = '';

  /**
   * @var string
   *
   * Maps to <meta name="twitter:title" content="Your title here" />
   * Maps to <meta property="og:title" content="Your eye-catching title here" />
   */
  public $title = '';

  /**
   * @var string
   *
   * Maps to <meta name="twitter:description" content="Your 200-character description here" />
   * Maps to <meta property="og:description" content="Your entertaining and descriptive copy here, if your meta description is good, use it." />
   */
  public $description = '';

  /**
   * @var string
   *
   * Maps to <meta name="twitter:image" content="http://www.yourdomain.com /image-name.jpg" />
   * Maps to <meta property="og:image" content="http://www.yourdomain.com/image-name.jpg" />
   */
  public $image = '';

  /**
   * @var string
   *
   * If the page (object) you are sharing is part of a larger network
   *
   * Maps to <meta property="og:site_name" content="name of your web site" />
   */
  public $site_name = '';

  /**
   * @var string
   *
   * Possible values are "website", "article", "blog" (see https://ogp.me/#types) for more information).
   *
   * Maps to <meta property="og:type" content="website" />
   */
  public $type = 'website';

  /**
   * @var string
   *
   * Maps to <meta name="twitter:url" content="http://www.yourdomain.com" />
   * Maps to <meta property="og:url" content="http://www.yourdomain.com" />
   */
  public $url;

  /**
   * @var DATE_TIME
   */
  public $publication_time;

  /**
   * @var DATE_TIME
   */
  public $modification_time;

  /**
   * @var integer
   */
  public $image_width = 0;

  /**
   * @var integer
   */
  public $image_height = 0;

  /**
   * @var boolean
   */
  public $enabled;

  public function display ()
  {
    if ($this->enabled)
    {
      ?>
      <meta name="twitter:card" content="<?php echo $this->twitter_card ?>" />
      <meta name="twitter:site" content="<?php echo $this->twitter_site ?>" />
      <meta name="twitter:creator" content="<?php echo $this->twitter_account ?>" />
      <meta name="twitter:title" content="<?php echo $this->title ?>" />
      <meta name="twitter:description" content="<?php echo $this->description ?>" />
      <meta name="twitter:image" content="<?php echo $this->image ?>" />

      <meta property="og:url" content="<?php echo $this->url ?>" />
      <meta property="og:title" content="<?php echo $this->title ?>" />
      <meta property="og:type" content="<?php echo $this->type ?>" />
      <meta property="og:description" content="<?php echo htmlentities ($this->description) ?>" />
      <meta property="article:author" content="<?php echo $this->author ?>" />
      <meta property="article:published_time" content="<?php echo $this->publication_time->format ($this->formatter) ?>" />
      <meta property="article:modified_time" content="<?php echo $this->modification_time->format ($this->formatter) ?>" />
      <meta property="og:image" content="<?php echo $this->image ?>" />
      <?php
      if ($this->image_width > 0)
      {
        ?>
        <meta property="og:image:width " content="<?php echo $this->image_width ?>" />
        <?php
      }
      if ($this->image_height > 0)
      {
        ?>
        <meta property="og:image:height " content="<?php echo $this->image_height ?>" />
        <?php
      }
    }
  }

  /**
   * @param PAGE $page
   */
  public function __construct ($page)
  {
    $this->page = $page;

    $this->formatter = new ISO_DATE_TIME_FORMATTER();
    $this->formatter->show_CSS = false;
    $this->formatter->show_local_time = false;
    $this->formatter->type = Date_time_format_short_date_and_time;
    $this->publication_time = new DATE_TIME();
    $this->modification_time = new DATE_TIME();
  }

  /**
   * @var ISO_DATE_TIME_FORMATTER
   */
  private $formatter;
}

/**
 * Options for the referenced page icon.
 * This icon is visible in most browsers on the page tab, in the address bar,
 * for bookmarks, etc. Internet Explorer versions only recognize the Windows
 * icon format (.ico); older versions only retrieve the "favicon.ico" file
 * from the site root. The default values reflect the lowest common denominator
 * represented by IE. Does nothing is no {@link $file_name} is assigned.
 * @see PAGE::$icon_options
 * @package webcore
 * @subpackage config
 * @version 3.6.0
 * @since 2.2.1
 */
class PAGE_ICON_OPTIONS
{
  /**
   * @var boolean
   */
  public $enabled = true;

  /**
   * Standard mime type For example, "image/png" or "image/x-icon". Mime type
   * should match the file give in {@link $file_name}.
   * @var string
   */
  public $mime_type = 'image/x-icon';

  /**
   * URL of the icon for this page.
   * Locations can be absolute or relative to resource locations defined in the
   * {@link PAGE}. For example, "{icons}/page_icons/my_icon.ico".
   * @var string
   */
  public $file_name = '/favicon.ico';

  /**
   * @param PAGE $page
   */
  public function __construct ($page)
  {
    $this->page = $page;
  }

  /**
   * Render the settings as HTML.
   */
  public function display ()
  {
    if ($this->enabled)
    {
      $url = $this->page->resolve_file ($this->file_name);
      ?>
      <link rel="shortcut icon" href="<?php echo $url; ?>" type="<?php echo $this->mime_type; ?>">
      <?php
    }
  }
}

/**
 * Options for the newsfeeds associated with a page.
 * Most modern browsers will detect that there is are one or more associated
 * news feeds for a page and offer to subscribe. Does nothing is no {@link
 * $file_name} is assigned.
 * @see PAGE::$newsfeed_options
 * @package webcore
 * @subpackage config
 * @version 3.6.0
 * @since 2.7.0
 */
class PAGE_NEWSFEED_OPTIONS
{
  /**
   * @var boolean
   */
  public $enabled = true;

  /**
   * List of supported newsfeed formats.
   *
   * Can contain any of the formats listed with the {@link Newsfeed_constants}.
   *
   * @var string[]
   */
  public $formats = array(
    Newsfeed_format_atom => "Atom", Newsfeed_format_rss => "RSS"
  );

  /**
   * List of supported newsfeed content formats.
   *
   * Can contain any of the content formats listed with the {@link Newsfeed_constants}.
   *
   * @var string[]
   */
  public $content_formats = array(
    Newsfeed_content_text => array("Plain text", "Plain text content; not appropriate for HTML newsreaders."),
    Newsfeed_content_html => array("Simple html", "HTML body with minimal styling; good for integrating into online newsreaders."),
    Newsfeed_content_full_html => array("Styled html", "Fully-styled HTML; perfect for standalone article newsreaders.")
  );

  /**
   * Name of the feed presented to the user when subscribing.
   * @var PAGE_TITLE
   */
  public $title;

  /**
   * URL of the newsfeed.
   * Locations can be absolute or relative to resource locations defined in the
   * {@link PAGE}. For example, "{app}/rss.php".
   * @var string
   */
  public $file_name = '';

  /**
   * @param PAGE $page
   */
  public function __construct ($page)
  {
    $this->page = $page;
    $class_name = $page->final_class_name ('PAGE_TITLE', 'webcore/gui/page_title.php');
    $this->title = new $class_name ($page);
    $this->title->prefix = '';
    $this->title->suffix = '';
  }

  /**
   * Render the settings as HTML.
   */
  public function display ()
  {
    if ($this->enabled && $this->file_name)
    {
      $title = $this->title->as_text ();
      $url = new URL ($this->page->resolve_file ($this->file_name, Force_root_on));

      foreach ($this->formats as $format => $format_title)
      {
        foreach ($this->content_formats as $content => $content_texts)
        {
          $url->replace_argument ('format', $format);
          $url->replace_argument ('content', $content);

          $content_title = $content_texts [0];
          ?>
          <link rel="alternate"
                title="<?php echo $title; ?> (<?php echo $format_title; ?>/<?php echo $content_title; ?>)"
                href="<?php echo $url->as_html (); ?>" type="application/rss+xml">
          <?php
        }
      }
    }
  }

  public function make_commands ($context)
  {
    $Result = new COMMANDS($context);

    if ($this->enabled && $this->file_name)
    {
      $url = new URL ($this->page->resolve_file ($this->file_name, Force_root_on));

      foreach ($this->formats as $format => $format_title)
      {
        foreach ($this->content_formats as $content => $content_texts)
        {
          $content_title = $content_texts [0];
          $content_description = $content_texts [1];

          $url->replace_argument ('format', $format);
          $url->replace_argument ('content', $content);

          $cmd = $Result->make_command ();
          $cmd->id = $format . '_' . $content;
          $cmd->caption = '<b>' . $content_title . '</b> (' . $format_title . ')';
          if ($format == 'rss')
          {
            $cmd->icon = '{icons}indicators/newsfeed_rss';
          }
          else
          {
            $cmd->icon = '{icons}indicators/newsfeed_atom';
            $cmd->description = $content_description;
          }
          $cmd->link = $url->as_text ();
          $Result->append ($cmd);
        }
      }
    }

    return $Result;
  }
}

/**
 * Options and hints for rendering a page template.
 * Replaces functionality previously found in PAGE_HEADER_OPTIONS and
 * PAGE_FOOTER_OPTIONS.
 * @see PAGE::$template_options
 * @package webcore
 * @subpackage config
 * @version 3.6.0
 * @since 2.6.0
 */
class PAGE_TEMPLATE_OPTIONS
{
  /**
   * The CSS class to apply to the body.
   * Use this to use a different layout for certain pages.
   * @var string
   */
  public $css_class = '';

  /**
   * Show the standard header for this page?
   * @var boolean
   */
  public $header_visible = true;

  /**
   * Show the standard footer for this page?
   * @var boolean
   */
  public $footer_visible = true;

  /**
   * Include JavaScript files in this page?
   * @var boolean
   */
  public $include_scripts = true;

  /**
   * Short title for the page (usually one word).
   * @var string
   */
  public $title = '';

  /**
   * @var string
   */
  public $language = 'en';

  /**
   * Icon that represents the page content. Paths are resolved relative to the
   * {@link Folder_name_icons} folder in the {@link PAGE}.
   * @var string
   */
  public $icon = '';

  /**
   * Determines whether to show the {@link $icon}.
   * @var boolean
   */
  public $show_icon = false;

  /**
   * Determines whether to show the {@link $title}.
   * @var boolean
   */
  public $show_title = false;

  /**
   * Text of the copyright message for this page.
   * @var string
   */
  public $copyright = '';

  /**
   * URL to the location of the site logo.
   * @var string
   */
  public $logo_file = '';

  /**
   * Alternate text for the site logo.
   * @var string
   */
  public $logo_title = '';

  /**
   * Describes the main application embedded in the page.
   * Generally includes the {@link APPLICATION::$short_title},
   * {@link APPLICATION::$version} and {@link APPLICATION::$url} in an HTML
   * link. May be empty. Include a 'start.php' file to initialize a main
   * application for a page (calls {@link _make_application()}).
   * @var string
   */
  public $app_info = '';

  /**
   * Show the log-in information on this page?
   * @var boolean
   */
  public $show_login = true;

  /**
   * Show the update time for the file itself?
   * @var boolean
   */
  public $show_last_time_modified = false;

  /**
   * Show statistics gathered during page generation?
   * The WebCore keeps track of number of objects created, queries issued and
   * time spent building the page (behavior can be disabled through the {@link
   * ENVIRONMENT}).
   * @var boolean
   */
  public $show_statistics = true;

  /**
   * Show the Contact/Support/Privacy links.
   * Toggle this value to turn off rendering off all extra links in the
   * template.
   * @var boolean
   */
  public $show_links = true;

  /**
   * Show the library and application versions?
   * @var boolean
   */
  public $show_versions = true;

  /**
   * Link to change theme settings.
   * @var string
   */
  public $settings_url = '{pages}settings.php';

  /**
   * Link to view browser properties.
   * @var string
   */
  public $browser_url = '{pages}browser.php';

  /**
   * Link to show current page source.
   * @var string
   */
  public $show_source_url = '{pages}show_source.php';

  /**
   * Link to show site/application privacy policy.
   * @var string
   */
  public $privacy_url = '{pages}privacy.php';

  /**
   * Link to show support options.
   * @var string
   */
  public $support_url = '{pages}support.php';

  /**
   * Link to show contact options.
   * @var string
   */
  public $contact_url = '{pages}contact.php';

  /**
   * Link to show site/application copyright policy.
   * @var string
   */
  public $rights_url = '{pages}rights.php';

  /**
   * Should the page close the logger when rendering the footer?
   * The template closes the logger before outputting a footer so that loggers
   * that write output when closed are properly formatted. A template rendered
   * into an email will not close the global logger and toggles this value off
   * temporarily
   * @see THEMED_MAIL_BODY_RENDERER
   * @var boolean
   */
  public $close_logger = true;

  /**
   * Check the browser and report errors?
   * @var boolean
   */
  public $check_browser = true;
}

/**
 * Names of the storage keys in this deployment.
 * Using these generic keys leaves the page flexible enough to store information
 * into different locations depending on deployment. Some of the storage key names
 * may already be in use, so change those when initializing the {@link PAGE}.
 * @package webcore
 * @subpackage config
 * @version 3.6.0
 * @since 2.2.1
 */
class PAGE_STORAGE_OPTIONS extends CONTEXT_STORAGE_OPTIONS
{
  /**
   * {@link THEME_SETTINGS} are stored under this key.
   * @var string
   */
  public $theme_settings_name = 'theme_settings';
}

?>