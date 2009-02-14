<?php
/**
 * Define constants for PHP 4 compatability
 */
if (!defined('T_ML_COMMENT')) {
   define('T_ML_COMMENT', T_COMMENT);
} else {
   define('T_DOC_COMMENT', T_ML_COMMENT);
}

/*

- Added public/private/protected defines
- Changed tab size to 2

*/

if (!defined('T_OLD_FUNCTION'))
  define('T_OLD_FUNCTION', 390);
if (!defined('T_PRIVATE'))
  define ('T_PRIVATE', 342);
if (!defined('T_PROTECTED'))
  define ('T_PROTECTED', 343);
if (!defined('T_PUBLIC'))
  define ('T_PUBLIC', 344);


/**
 * Improved PHP syntax highlighting.
 *
 * Generates valid XHTML output with function referencing
 * and line numbering.
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.0
 */
class PHP_Highlight
{
    /**
     * Hold the source
     */
    var $source;

    /**
     * Toggle for function referencing
     */
    var $funcref = true;

    /**
     * Hold highlight colors
     */
    var $highlight;

    /**
     * Array of things to be replaced per token
     */
    var $tokmatch = array("\t", ' ');

    /**
     * Array of things to be replaced with per token
     */
    var $tokreplace = array('&nbsp;&nbsp;', '&nbsp;');

    var $link_functions = TRUE;


    /**
     * Construct
     */
    function __construct() {
        $this->highlight = array(
            'string'    => ini_get('highlight.string'),
            'comment'   => ini_get('highlight.comment'),
            'keyword'   => ini_get('highlight.keyword'),
            'bg'        => ini_get('highlight.bg'),
            'default'   => ini_get('highlight.default'),
            'html'      => ini_get('highlight.html')
        );
    }

    /**
     * Load a file
     *
     * @param   string      $file       The file to load
     */
    function loadFile($file)
    {
        ob_start ();
          readfile ($file);
          $this->source = ob_get_contents ();
        ob_end_clean ();
    }

    /**
     * Load a string
     *
     * @param   string      $string     The string to load
     */
    function loadString($string)
    {
        $this->source = $string;
    }

    /**
     * Parse the loaded string into an array
     */
    function toArray()
    {
        // Init
        $tokens     = token_get_all($this->source);
        $manual     = '<a href="http://www.php.net/function.%s">%s</a>';
        $span       = '<span style="color: %s;">%s</span>';
        $i          = 0;
        $out        = array();
        $out[$i]    = '';

        // Loop through each token
        foreach ($tokens as $j => $token) {
            // Single char
            if (is_string($token)) {
                $out[$i] .= sprintf($span, $this->_token2color(), $token);

                // Heredocs behave strangely
                list($tb) = isset($tokens[$j - 1]) ? $tokens[$j - 1] : false;
                if ($tb == T_END_HEREDOC) {
                    $out[++$i] = '';
                }

                continue;
            }

            // Proper token
            list ($token, $value) = $token;

            // Make the value safe
            $value = htmlentities($value);
            $value = str_replace($this->tokmatch, $this->tokreplace, $value);

            // Process
            if ($value == "\n") {
                // End this line and start the next
                $out[++$i] = '';
            } else {

                // Function linking
                if ($this->funcref === true) {
                    // Link the function
                    if ($token == T_STRING) {
                        // Look ahead 1, look ahead 2, and look behind 3
                        $t1 = isset($tokens[$j]) ? $tokens[$j] : false;
                        $t2 = isset($tokens[$j + 1]) ? $tokens[$j + 1] : false;
                        list($t3) = isset($tokens[$j - 3]) ? $tokens[$j - 3] : false;

                        if (($t1 == '(' || $t2 == '(') && $t3 != T_FUNCTION
                            && function_exists($value)
                            && $this->link_functions) {
                              $value = sprintf($manual, $value, $value);
                        }
                    }
                }

                // Explode token block
                $lines = explode("\n", $value);
                foreach ($lines as $jj => $line) {
                    $nextline = (bool) isset($lines[$jj + 1]);
                    $line = trim($line);

                    if (!(empty($line) && $nextline === true)) {
                        $out[$i] .= sprintf($span, $this->_token2color($token), $line);
                    }

                    // Start a new line
                    if ($nextline === true) {
                        $out[++$i] = '';
                    }
                }
            }
        }

        return $out;
    }

    /**
     * Convert the source to a HTML ordered list
     */
    function toList($return = true)
    {
        $source = $this->toArray($this->source);

        $out = "<ol>\n";

        foreach ($source as $line) {
            $out .= "    <li>";
            $out .= empty($line) ? '&nbsp;' : "<code>$line</code>";
            $out .= "</li>\n";
        }

        $out .= "</ol>\n";

        if ($return === true) {
            return $out;
        } else {
            echo $out;
        }
    }

    /**
     * Convert the source to simple HTML
     */
    function toHtml($return = true, $linenum = false, $linenummod = null)
    {
        $source = $this->toArray($this->source);

        if ($linenum === true && $linenummod === null) {
            $linenummod = '<span>%02d</span> ';
        }

        $out = "<code>\n";

        foreach ($source as $i => $line) {
            $out .= "    ";

            if ($linenum === true) {
                $out .= sprintf($linenummod, $i);
            }

            $out .= empty($line) ? '&nbsp;' : $line;
            $out .= "<br />\n";
        }

        $out .= "</code>\n";

        if ($return === true) {
            return $out;
        } else {
            echo $out;
        }
    }

    /**
     * Assign a color based on the name of a token
     *
     * Uses the default colors as specified in php.ini
     *
     * @param   int     $token      The token
     */
    function _token2color($token = null)
    {
        switch ($token):
            case T_CONSTANT_ENCAPSED_STRING:
                return $this->highlight['string'];

            case T_INLINE_HTML:
                return $this->highlight['html'];

            case T_COMMENT:
            case T_ML_COMMENT:
            case T_DOC_COMMENT:
                return $this->highlight['comment'];

            case null:
            case T_REQUIRE:
            case T_REQUIRE_ONCE:
            case T_INCLUDE:
            case T_INCLUDE_ONCE:
            case T_PAAMAYIM_NEKUDOTAYIM:
            case T_CONCAT_EQUAL:
            case T_CASE:
            case T_RETURN:
            case T_DEFAULT:
            case T_BREAK:
            case T_FUNCTION:
            case T_ARRAY:
            case T_ECHO:
            case T_ELSE:
            case T_EXTENDS:
            case T_GLOBAL:
            case T_ELSEIF:
            case T_CLASS:
            case T_STATIC:
            case T_NEW:
            case T_FOREACH:
            case T_VAR:
            case T_IS_IDENTICAL:
            case T_INC:
            case T_IS_EQUAL:
            case T_WHILE:
            case T_BOOL_CAST:
            case T_STRING_CAST:
            case T_PRIVATE:
            case T_PROTECTED:
            case T_PUBLIC:
            case T_OBJECT_CAST:
            case T_ARRAY_CAST:
            case T_INT_CAST:
            case T_UNSET_CAST:
            case T_DOUBLE_CAST:
            case T_ENDDECLARE:
            case T_ENDFOR:
            case T_ENDFOREACH:
            case T_ENDIF:
            case T_ENDSWITCH:
            case T_ENDWHILE:
            case T_END_HEREDOC:
            case T_EXIT:
            case T_START_HEREDOC:
            case T_ISSET:
            case T_EMPTY:
            case T_CONTINUE:
            case T_BOOLEAN_AND:
            case T_BOOLEAN_OR:
            case T_SL:
            case T_SL_EQUAL:
            case T_SR:
            case T_SR_EQUAL:
            case T_OBJECT_OPERATOR:
            case T_IS_SMALLER_OR_EQUAL:
            case T_IS_NOT_IDENTICAL:
            case T_DOUBLE_ARROW:
            case T_IF:
                return $this->highlight['keyword'];

            case T_OPEN_TAG:
            case T_OPEN_TAG_WITH_ECHO:
            case T_CLOSE_TAG:
            default:
                return $this->highlight['default'];

        endswitch;
    }

}

?>