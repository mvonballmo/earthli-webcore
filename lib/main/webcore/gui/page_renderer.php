<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage page
 * @version 3.0.0
 * @since 2.2.1
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
require_once ('webcore/obj/webcore_object.php');

/**
 * Renders the header and footer of a {@link PAGE}.
 * Call {@link start_display()} and {@link finish_display()} to draw the
 * header and footer. Use {@link display_doc_type()}, {@link display_head()} and
 * other functions to draw individual elements of a page (when custom
 * rendering).
 * @package webcore
 * @subpackage page
 * @version 3.0.0
 * @since 2.2.1
 */
class PAGE_RENDERER extends WEBCORE_OBJECT
{
  /**
   * Start painting the page (show the header)
   */
  function start_display ()
  {
    $this->display_doc_type ();
?>
<html>
  <head>
  <?php
    $this->display_head ();
  ?>
  </head>
<?php
    $opts =& $this->page->template_options;
    if ($opts->body_load_script)
    {
?>
  <body onload="<?php echo $opts->body_load_script; ?>">
<?php
    }
    else
    {
?>
  <body>
<?php
    }

    $this->_start_body ();
  }

  /**
   * Finish painting the page (show the footer)
   */
  function finish_display ()
  {
    $this->_finish_body ();
?>
  </body>
</html>
<?php
  }

  /**
   * Draws the standard HTML header using {@link PAGE} settings.
   * Called from {@link start_display()}.
   * @see display_title()
   * @see display_content_type()
   * @see display_styles_and_scripts()
   * @see PAGE_ICON_OPTIONS::display()
   * @see PAGE_REFRESH_OPTIONS::display()
   */
  function display_head ()
  {
    $this->display_title ();
    $this->display_content_type ();
    $this->display_meta_information ();
    $this->page->icon_options->display ();
    $this->page->newsfeed_options->display ();
    $this->page->refresh_options->display ();
    $this->display_styles_and_scripts ();
  }

  /**
   * Outputs the {@link PAGE::$doc_type}.
   */
  function display_doc_type ()
  {
    echo $this->page->doc_type;
  }

  /**
   * Draws the {@link PAGE::$title} as HTML.
   * Called from {@link display_head()}; call individually if not using
   * start/finish display functions.
   */
  function display_title ()
  {
    echo '<title>' . $this->page->title->as_text () . '</title>' . "\n";
  }

  /**
   * Draws the {@link PAGE::$content_type} as HTML.
   * Called from {@link display_head()}; call individually if not using
   * start/finish display functions.
   */
  function display_content_type ()
  {
    $this->display_meta_header_tag ('Content-Type', $this->page->content_type);
  }

  /**
   * Called from {@lihk start_display()} to build the header.
   * Calls {@link _include_styles()} and {@link _include_scripts()}.
   * @access private
   */
  function display_styles_and_scripts ()
  {
    $this->display_styles ();
    if ($this->page->template_options->include_scripts)
    {
      $this->display_scripts ();
    }
  }
  
  function display_meta_name_tag ($tag_name, $tag_content)
  {
    $this->display_meta_tag ('name', $tag_name, $tag_content);
  }

  function display_meta_header_tag ($tag_name, $tag_content)
  {
    $this->display_meta_tag ('http-equiv', $tag_name, $tag_content);
  }

  function display_meta_tag ($meta_type, $tag_name, $tag_content)
  {
    if (! empty($tag_content))
    {
      $options =& $this->context->text_options;
      $tag_name = $options->convert_to_html_entities($tag_name);
      $tag_content = $options->convert_to_html_entities($tag_content);

      echo '<meta ' . $meta_type . '="' . $tag_name . '" content="' . $tag_content . '">' . "\n";
    }
  }
  
  function display_meta_information ()
  {
    $this->display_meta_name_tag ('author', $this->page->author);
    $this->display_meta_name_tag ('keywords', $this->page->keywords);
    $this->display_meta_name_tag ('description', $this->page->description);
  }

  function start_display_as_text ()
  {
    ob_start ();
      $this->start_display ();
      $Result = ob_get_contents ();
    ob_end_clean ();
    return $Result;
  }

  function finish_display_as_text ()
  {
    ob_start ();
      $this->finish_display ();
      $Result = ob_get_contents ();
    ob_end_clean ();
    return $Result;
  }

  /**
   * Helper function called from {@link _include_styles_and_scripts()}.
   * Includes and resolves all styles found in {@link PAGE::$styles}. Override
   * to include other stylesheets.
   */
  function display_styles ()
  {
    $page =& $this->page;
    if (! empty ($page->styles))
    {
      $res =& $page->resources ();
      foreach ($page->styles as $style)
      {
        $style = $res->resolve_file ($style);
        if ($style)
        {
?>
  <link rel="stylesheet" type="text/css" href="<?php echo $style; ?>">
<?php
        }
      }
    }
  }

  /**
   * Helper function called from {@link _include_styles_and_scripts()}.
   * Includes and resolves all scripts found in {@link PAGE::$scripts}. Override
   * to include other scripts.
   */
  function display_scripts ()
  {
    $page =& $this->page;
    if (! empty ($page->scripts))
    {
      foreach ($page->scripts as $script)
      {
        $script = $page->resolve_file ($script);
        if ($script)
        {
?>
  <script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php
        }
      }
    }
  }

  /**
   * Called from {@link start_display()}.
   * @access private
   */
  function _start_body () {}

  /**
   * Called from {@link finish_display()}.
   * @access private
   */
  function _finish_body () {}
}

?>