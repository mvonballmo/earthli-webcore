<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.5.0
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

/** */
require_once ('webcore/obj/webcore_object.php');
require_once ('webcore/util/tags.php');

/**
 * Creates and manages a DHTML layer.
 * Use {@link start()} and {@link finish()} to mark regions that can be toggled
 * visible/invisible. Use {@link draw_toggle()} to show a control for
 * controlling the layer. If {@link CONTEXT::dhtml_allowed()} returns
 * <c>False</c>, the layer cannot be toggled and is always {@link $visible}.
 * @package webcore
 * @subpackage gui
 * @version 3.5.0
 * @since 2.2.1
 */
class LAYER extends WEBCORE_OBJECT
{
  /**
   * Must be unique to the page.
   * @var string
   */
  public $name = 'layer';

  /**
   * Name of a CSS class used as the style for the layer.
   * @var string
   */
  public $css_class = '';

  /**
   * Is this layer initially displayed?
   * @var boolean
   */
  public $visible = false;

  /**
   * CSS style used for the top margin of the layer.
   * Overrides that specified in the {@link $css_class} property.
   * @var string
   */
  public $margin_top = '';

  /**
   * CSS style used for the left margin of the layer.
   * Overrides that specified in the {@link $css_class} property.
   * @var string
   */
  public $margin_left = '';

  /**
   * The toggle for this layer in HTML.
   * @see draw_toggle()
   * @return string
   */
  public function toggle_as_html ()
  {
    if ($this->context->dhtml_allowed ())
    {
      if ($this->visible)
      {
        $icon = $this->context->resolve_icon_as_html ('{icons}tree/collapse', '', '[-]', 'inline-icon', "{$this->name}_image");
      }
      else
      {
        $icon = $this->context->resolve_icon_as_html ('{icons}tree/expand', '', '[+]', 'vertical-align: middle', "{$this->name}_image");
      }

      return '<a href="#" onclick="toggle_visibility(\'' . $this->name . '\'); return false;">' . $icon . '</a>';
    }
    
    return '';
  }

  /**
   * Draw a control to show/hide this layer.
   * @see toggle_as_html()
   */
  public function draw_toggle ()
  {
    echo $this->toggle_as_html ();
  }

  /**
   * Start the container for the layer.
   * All content emitted until {@link finish()} is called will be shown/hidden
   * with this layer.
   */
  public function start ($tag_name = 'div')
  {
    if (isset ($this->env->profiler)) $this->env->profiler->start ('ui');
    $css = $this->context->make_tag_builder (Tag_builder_css);
    if ($this->context->dhtml_allowed () && ! $this->visible)
    {
      $css->add_attribute ('display', 'none');
    }
    if ($this->margin_left)
    {
      $css->add_attribute ('margin-left', $this->margin_left);
    }
    if ($this->margin_top)
    {
      $css->add_attribute ('margin-top', $this->margin_top);
    }
    $css_style = $css->as_text ();

    $div = $this->context->make_tag_builder (Tag_builder_html);
    $div->set_name ($tag_name);
    if ($this->css_class)
    {
      $div->add_attribute ('class', $this->css_class);
    }
    if ($this->context->dhtml_allowed ())
    {
      $div->add_attribute ('id', $this->name . '_layer');
    }
    if ($css_style)
    {
      $div->add_attribute ('style', $css_style);
    }
      
    echo $div->as_html () . "\n";      
    if (isset ($this->env->profiler)) $this->env->profiler->stop ('ui');
  }

  /**
   * Close the container for the layer.
   * Call only after calling {@link start()}.
   */
  public function finish ($tag_name = 'div')
  {
    echo "</$tag_name>\n";
  }
}

?>