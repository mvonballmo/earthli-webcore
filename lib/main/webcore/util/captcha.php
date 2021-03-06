<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.6.0
 * @since 2.2.1
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
 * Interface for CAPTCHA validation.
 * Used by {@link FORM}s to validate that a human is filling out as opposed to a
 * robot. Use {@link as_html()} to get the question for the expression and
 * {@link encode()} and {@link decode()} to store and retrieve the expression in
 * a form field. {@link validate()} checks a given answer.
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.7.1
 */
abstract class CAPTCHA extends WEBCORE_OBJECT
{
  /**
   * Automatically generates an initial expression.
   */
  public function __construct ()
  {
    $this->generate ();
  }
  
  /**
   * Create a random expression. 
   * @abstract
   */
  public abstract function generate ();
  
  /**
   * Return an HTML representation of the question.
   * @return string
   * @abstract
   */
  public abstract function as_html ();
  
  /**
   * Encode the question for a form field.
   * Use {@link decode()} to restore the question from the form data.
   * @return string
   * @abstract
   */
  public abstract function encode ();
  
  /**
   * Restore a question from an encoded value.
   * Use {@link encode()} to store the question.
   * @param string $text
   * @abstract
   */
  public abstract function decode ($text);

  /**
   * Determine whether the answer matches the question.
   * @param $answer string The answer given by the user.
   * @return boolean
   * @abstract
   */
  public abstract function validate ($answer);
}

/**
 * Used with {@link NUMERIC_CAPTCHA}.
 */
define ('Captcha_operator_plus', 0);
/**
 * Used with {@link NUMERIC_CAPTCHA}.
 */
define ('Captcha_operator_minus', 1);
/**
 * Used with {@link NUMERIC_CAPTCHA}.
 */
define ('Captcha_operator_times', 2);

/**
 * Basic numeric CAPTCHA tester.
 * Evaluates, encodes and decodes simple mathematical expressions. Retains two
 * operands and an operator. Use {@link as_html()} to get the question for the
 * expression and {@link encode()} and {@link decode()} to store and retrieve
 * the expression in a form field. {@link validate()} checks a given answer.
 * @package webcore
 * @subpackage util
 * @version 3.6.0
 * @since 2.7.1
 */
class NUMERIC_CAPTCHA extends CAPTCHA
{
  /**
   * @var integer
   */
  public $operand_left;

  /**
   * @var integer
   */
  public $operator;

  /**
   * @var integer
   */
  public $operand_right;
  
  /**
   * Automatically generates an initial expression.
   */
  public function __construct ()
  {
    $this->generate ();
  }
  
  /**
   * Create a random expression.
   */
  public function generate ()
  {
    $this->operand_left = rand (1, 9);
    $this->operator = rand (Captcha_operator_plus, Captcha_operator_times);
    if ($this->operator == Captcha_operator_minus)
    {
      $this->operand_right = rand (1, $this->operand_left);
    }
    else
    {
      $this->operand_right = rand (1, 9);
    }
  }
  
  /**
   * Return the question as HTML.
   * @return string
   */
  public function as_html ()
  {
    $numbers = array ('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine');
    $operations = array ('plus', 'minus', 'times');
    return 'What is ' . $numbers [$this->operand_left] . ' ' . $operations [$this->operator] . ' ' . $numbers [$this->operand_right] . '?';
  }
  
  /**
   * Encode the question for a form field.
   * @return string
   */
  public function encode ()
  {
    return $this->operand_left . ',' . $this->operator . ',' . $this->operand_right;
  }
  
  /**
   * Restore a question from an encoded value.
   * @param string $text
   */
  public function decode ($text)
  {
    $params = explode (',', $text);
    $this->operand_left = $params [0]; 
    $this->operator = $params [1];
    $this->operand_right = $params [2];
  }
  
  /**
   * Determine whether the answer matches the question.
   * @param integer $proposal
   * @return boolean
   */
  public function validate ($proposal)
  {
    switch ($this->operator)
    {
      case Captcha_operator_plus:
        $answer = $this->operand_left + $this->operand_right;
        break;
      case Captcha_operator_minus:
        $answer = $this->operand_left - $this->operand_right;
        break;
      case Captcha_operator_times:
        $answer = $this->operand_left * $this->operand_right;
        break;
      default:
        $answer = null;
    }
    
    return $answer == $proposal;
  }
}