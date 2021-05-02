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

/**
 * Formatting options used by {@link FORM} and {@link MUNGER}
 * Available as {@link CONTEXT::$text_options} or through {@link
 * global_text_options()}.
 * @package webcore
 * @subpackage text
 * @version 3.6.0
 * @since 2.7.1
 */
class TEXT_OPTIONS
{
  /**
   * Convert all characters to HTML entities, including quotes.
   * Calls {@link convert_to_html_entities}, passing "ENT_QUOTES" for the
   * quote_style.
   * @param string $value
   * @return string
   */
  public function convert_to_html_attribute ($value)
  {
    return $this->convert_to_html_entities ($value, ENT_QUOTES);
  }

  /**
   * Convert all characters to HTML entities.
   *
   * Called when displaying stored, normalized text as HTML. First calls {@link
   * PHP_MANUAL#get_html_translation_table} to perform the standard
   * translations.
   *
   * @see convert_from_html_entities()
   * @param string $value
   * @param int $quote_style A flag that indicates how quotes should be converted.
   * @return string
   */
  public function convert_to_html_entities ($value, $quote_style = ENT_NOQUOTES)
  {
    return htmlentities ($value, $quote_style, 'utf-8', false);
  }

  /**
   * Convert all HTML entities to characters.
   * 
   * Called to normalize submitted HTML text. Uses {@link
   * PHP_MANUAL#get_html_translation_table} to perform the standard
   * translations.
   * 
   * @see convert_to_html_entities()
   * @param string $value
   * @param int $quote_style A flag that indicates how quotes should be converted.
   * @return string
   */
  public function convert_from_html_entities ($value, $quote_style = ENT_QUOTES)
  {
    return html_entity_decode ($value, $quote_style);
  }
}

/**
 * Returns the global text options.
 * Used when no options are passed to a text function.
 * @return TEXT_OPTIONS
 */
function global_text_options ()
{
  global $_g_text_options;
  if (! isset ($_g_text_options))
  {
    $_g_text_options = new TEXT_OPTIONS ();
  }
  return $_g_text_options;
}

/**
 * Cached copy of text options.
 * Accessed using {@link global_text_options()}.
 * @global TEXT_OPTIONS
 * @access private
 */
$_g_text_options = null;
