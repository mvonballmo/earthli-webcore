<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage config
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

define ('ISO_8859_1_ellipsis', chr(133));
define ('ISO_8859_1_left_single_curly_quote', chr(145));
define ('ISO_8859_1_right_single_curly_quote', chr(146));
define ('ISO_8859_1_left_double_curly_quote', chr(147));
define ('ISO_8859_1_right_double_curly_quote', chr(148));
define ('ISO_8859_1_en_dash', chr(150));
define ('ISO_8859_1_em_dash', chr(151));
define ('ISO_8859_1_trademark', chr(153));
define ('ISO_8859_1_copyright', chr(169));
define ('ISO_8859_1_registered_symbol', chr(174));

/**
 * Formatting options used by {@link FORM} and {@link MUNGER}
 * Available as {@link CONTEXT::$text_options} or through {@link
 * global_text_options()}.
 * @package webcore
 * @subpackage text
 * @version 3.0.0
 * @since 2.7.1
 */
class TEXT_OPTIONS
{
  /**
   * Characters to translate from HTML in a form. These are in addition
   * to the rather large set returned by {@link
   * PHP_MANUAL#get_html_translation_table}.
   * @var array[string,string]
   */
  public $html_entity_translations = array ( ISO_8859_1_em_dash => '&mdash;'
                                        , ISO_8859_1_en_dash => '&#8211;'
                                        , ISO_8859_1_em_dash => '&#8212;'
                                        , ISO_8859_1_trademark => '&trade;'
                                        , ISO_8859_1_registered_symbol => '&reg;'
                                        , ISO_8859_1_copyright => '&copy;'
                                        , ISO_8859_1_trademark => '&#8482;'
                                        , ISO_8859_1_ellipsis => '&#8230;'
                                        , ISO_8859_1_left_double_curly_quote => '&#8220;'
                                        , ISO_8859_1_right_double_curly_quote => '&#8221;'
                                        , ISO_8859_1_left_single_curly_quote => '&#8216;'
                                        , ISO_8859_1_right_single_curly_quote => '&#8217;'
                                        );

  /**
   * Convert all characters to HTML entities, including quotes.
   * Calls {@link convert_to_html_entities}, passing "ENT_QUOTES" for the
   * quote_style.
   * @param string $value
   * @return string
   */
  function convert_to_html_attribute ($value)
  {
    return $this->convert_to_html_entities ($value, ENT_QUOTES);
  }

  /**
   * Convert all characters to HTML entities.
   * Called when displayed stored, normalized text as HTML. First converts all
   * characters found in {@link $html_entity_translations} and then calls {@link
   * PHP_MANUAL#get_html_translation_table} to perform the standard
   * translations.
   * @see convert_from_html_entities()
   * @param string $value
   * @return string
   */
  function convert_to_html_entities ($value, $quote_style = ENT_NOQUOTES)
  {
    if (is_php_5 ())
    {
      // The fourth parameter "double_encode" is only supported on PHP 5.2.3 and higher
      //$Result = htmlentities ($value, $quote_style, 'ISO-8859-1', FALSE);
      $Result = htmlentities ($value, $quote_style, 'ISO-8859-1');
    }
    else
    {
      $Result = htmlentities ($value, $quote_style);
    }

    return strtr ($Result, $this->html_entity_translations);
  }

  /**
   * Convert all HTML entities to characters.
   * Called to normalize submitted HTML text. First converts all characters
   * found in {@link $html_entity_translations}, then uses {@link
   * PHP_MANUAL#get_html_translation_table} to perform the standard
   * translations.
   * @see convert_to_html_entities()
   * @param string $value
   * @return string
   */
  function convert_from_html_entities ($value, $quote_style = ENT_QUOTES)
  {
    /* Extra characters are defined as [char] => [&entity;], so flip the
     * array in order to translate FROM html.
     */
    $Result = html_entity_decode ($value, $quote_style);
    return strtr ($Result, array_flip ($this->html_entity_translations));
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

?>