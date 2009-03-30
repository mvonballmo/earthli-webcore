<?php

/**
 * @copyright Copyright (c) 2002-2008 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.6.0
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
require_once ('webcore/mail/themed_mail_body_renderer.php');

/**
 * Generates the object listing for an {@link OBJECT_IN_FOLDER}.
 * Used by the {@link MAIL_TOC_GROUP_RENDERER} to show an object with all of its history items.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */
class MAIL_TOC_ENTRY_RENDERER extends RENDERER
{
  /**
   * Represents this entry.
   * @var MAIL_BODY_RENDERER_OBJECT
   */
  public $main_pair;

  /**
   * List of {@link MAIL_BODY_RENDERER_OBJECT}s.
   * @var array[MAIL_BODY_RENDERER_OBJECT]
   */
  public $pairs = array ();

  /**
   * Reference to the parent container.
   * @var MAIL_TOC_RENDERER
   */
  public $toc;

  /**
   * Create a table of contents group.
   * @param MAIL_TOC_RENDERER $toc
   */
  public function MAIL_TOC_ENTRY_RENDERER ($toc, $group)
  {
    RENDERER::RENDERER ($toc->context);
    $this->toc = $toc;
    $this->group = $group;
  }

  /**
   * Returns whether this folder matches this group.
   * Called during {@link MAIL_TOC_RENDERER} generation.
   * @param OBJECT_IN_FOLDER $obj
   * @return boolean
   */
  public function accepts_item ($obj)
  {
    if (is_a ($obj, 'OBJECT_IN_FOLDER'))
    {
      return $this->_obj_id == $obj->id;
    }
    
    if (is_a ($obj, 'HISTORY_ITEM'))
    {
      return $this->_obj_id == $obj->object_id;
    }
    
    return false;
  }

  /**
   * Adds an object to the group.
   * Called during {@link MAIL_TOC_RENDERER} generation.
   * @param MAIL_BODY_RENDERER_OBJECT $pair
   */
  public function add_pair ($pair)
  {
    if (! isset ($this->main_pair) && is_a ($pair->obj, 'OBJECT_IN_FOLDER'))
    {
      $this->toc->num_entries++;
      $this->group->subject->add_object ($pair->obj);
      $this->_obj_id = $pair->obj->id;
      $this->main_pair = $pair;
    }
    elseif (is_a ($pair->obj, 'HISTORY_ITEM'))
    {
      $this->_obj_id = $pair->obj->object_id;
      $this->pairs [] = $pair;
    }
    else
    {
      $this->pairs [] = $pair;
    }
  }

  public function location_for_obj ($obj)
  {
    $type_info = $obj->type_info ();
    return $type_info->id . '_' . $obj->id;
  }

  /**
   * Render an item in the TOC as HTML.
   * Displays the item's title with an anchor to link directly to that item's body.
   * Called from {@link as_html_for_table()}.
   * @param MAIL_BODY_RENDERER_OBJECT $pair
   */
  public function pair_as_html_for_table ($pair)
  {
    $obj = $pair->obj;
    $t = $obj->title_formatter ();
    $t->CSS_class = '';
    $t->location = '#' . $this->location_for_obj ($obj);
    if (is_a ($pair->obj, 'HISTORY_ITEM'))
    {
      return $obj->title_as_link ($t) . '<br>';
    }
    else
    {
      $type_info = $obj->type_info ();
      return $this->context->resolve_icon_as_html ($type_info->icon, $type_info->singular_title, '16px') . ' ' . $obj->title_as_link ($t) . '<br>';
    }
  }

  /**
   * Render the group for the TOC as HTML.
   * Displays the group's header, then each item title using {@link pair_as_html_for_table()}.
   */
  public function as_html_for_table ()
  {
    $Result = '';
    if (isset ($this->main_pair))
    {
      $Result = $this->pair_as_html_for_table ($this->main_pair);
      $Result .= "\n<ul class=\"detail\" style=\"margin: .2em 0em\">";
    }

    foreach ($this->pairs as $pair)
    {
      $Result .= '<li>' . $this->pair_as_html_for_table ($pair) . '</li>';
    }

    if (isset ($this->main_pair))
    {
      $Result .= '</ul>';
    }

    return $Result;
  }

  /**
   * Render an item as html.
   * Displays the item's full content. Called from {@link as_html_for_items()}.
   * @param MAIL_BODY_RENDERER_OBJECT $pair
   * @param MAIL_RENDERER_OPTIONS $options
   * @param string $top_link HTML text for the link back to the top of the page.
   */
  public function pair_as_html_for_items ($pair, $options, $top_link = '')
  {
    $obj = $pair->obj;
    $Result = '';
    $Result .= '<a id="' . $this->location_for_obj ($obj) . "\"></a>\n";
    if ($top_link)
    {
      $Result .= '<div style="margin: 1em 0em">' . $top_link . "</div>\n";
    }
    $Result .= $pair->renderer->html_body ($obj, $options);
    return $Result;
  }

  /**
   * Render the group's full items as HTML.
   * Displays the group's header, then each item body using {@link pair_as_html_item()}.
   * @param MAIL_RENDERER_OPTIONS $options
   * @param string $top_link HTML text for the link back to the top of the page.
   */
  public function as_html_for_items ($options, $top_link)
  {
    $Result = '<div class="horizontal-separator" style="margin-top: 2em"></div>';
    if (isset ($this->main_pair))
    {
      $Result .= $this->pair_as_html_for_items ($this->main_pair, $options, $top_link);
    }
    foreach ($this->pairs as $pair)
    {
      $Result .= $this->pair_as_html_for_items ($pair, $options);
    }
    return $Result;
  }

  /**
   * Render an item in the TOC as plain text.
   * Displays the item's title. Called from {@link as_plain_text_for_table()}.
   * @param MAIL_BODY_RENDERER_OBJECT $pair
   */
  public function pair_as_plain_text_for_table ($pair, $indent)
  {
    return $this->_line ($indent . $pair->obj->title_as_plain_text ());
  }

  /**
   * Render the entry for the TOC as plain text.
   * Displays the main object first, then each item title using {@link pair_as_plain_text_for_table()}.
   */
  public function as_plain_text_for_table ()
  {
    $Result = '';
    if (isset ($this->main_pair))
    {
      $Result = $this->pair_as_plain_text_for_table ($this->main_pair, '   ');
    }
    foreach ($this->pairs as $pair)
    {
      $Result .= $this->pair_as_plain_text_for_table ($pair, '      ');
    }
    return $Result;
  }

  /**
   * Render a single object as plain text.
   * Displays the item's full content. Called from {@link as_plain_text_for_items()}.
   * @param MAIL_BODY_RENDERER_OBJECT $pair
   * @param MAIL_RENDERER_OPTIONS $options
   */
  public function pair_as_plain_text_item ($pair, $options)
  {
    return $pair->renderer->text_body ($pair->obj, $options);
  }

  /**
   * Render all objects in this entry fully.
   * Renders each object with {pair_as_plain_text_item()}.
   * @param MAIL_RENDERER_OPTIONS $options
   */
  public function as_plain_text_for_items ($options)
  {
    $Result = '';
    if (isset ($this->main_pair))
    {
      $Result .= $this->_line ($this->pair_as_plain_text_item ($this->main_pair, $options));
    }
    foreach ($this->pairs as $pair)
    {
      $Result .= $this->_line ($this->pair_as_plain_text_item ($pair, $options));
    }
    return $Result;
  }

  /**
   * @var integer
   * @access private
   */
  protected $_obj_id = 0;
}

/**
 * Generates the object listing for a {@link FOLDER}.
 * Used by the {@link MAIL_TOC_RENDERER}.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */
class MAIL_TOC_GROUP_RENDERER extends RENDERER
{
  /**
   * Number of objects contained in all {@link $groups}.
   * @var integer */
  public $num_entries = 0;

  /**
   * Group represents this folder.
   * May be empty.
   * @var FOLDER */
  public $folder;

  /**
   * List of {@link MAIL_TOC_ENTRY_RENDERER} in this group.
   * @var array[MAIL_TOC_ENTRY_RENDERER]
   */
  public $entries = array ();

  /**
   * Reference to the parent container.
   * @var MAIL_TOC_RENDERER
   */
  public $toc;

  /**
   * Create a table of contents group.
   * @param MAIL_TOC_RENDERER $toc
   * @param object $obj
   */
  public function MAIL_TOC_GROUP_RENDERER ($toc, $obj)
  {
    RENDERER::RENDERER ($toc->context);
    $this->subject = new PUBLISHER_MESSAGE_SUBJECT ($toc->context);
    $this->toc = $toc;
    if (is_a ($obj, 'OBJECT_IN_FOLDER'))
    {
      $this->folder = $obj->parent_folder ();
      $this->folder_id = $this->folder->id;
    }
    elseif (is_a ($obj, 'HISTORY_ITEM'))
    {
      $this->folder_id = $obj->access_id;
    }
  }

  /**
   * Returns whether this folder matches this group.
   * Called during {@link MAIL_TOC_RENDERER} generation.
   * @param object $obj
   * @return boolean
   */
  public function accepts_item ($obj)
  {
    if (is_a ($obj, 'OBJECT_IN_FOLDER'))
    {
      $fldr = $obj->parent_folder ();
      $Result = isset ($this->folder_id) && ($this->folder_id == $fldr->id);
      if ($Result)
      {
        $this->folder = $fldr;
      }

      return $Result;
    }
    elseif (is_a ($obj, 'HISTORY_ITEM'))
    {
      return isset ($this->folder_id) && ($this->folder_id == $obj->access_id);
    }
    
    return false;
  }

  /**
   * Render the group for the TOC as HTML.
   * Displays the group's header, then each item title using {@link pair_as_html_for_table()}.
   */
  public function as_html_for_table ()
  {
    if (isset ($this->folder))
    {
      $fldr = $this->folder;
      $t = $fldr->title_formatter ();
      $t->CSS_class = '';
      $t->location = '#fldr_' .  $fldr->id;
      $Result = '<dt class="field">' . $this->subject->as_text () . ' in ' . $fldr->title_as_link ($t) . "</dt>\n";
      $Result .= "<dd>\n";
      foreach ($this->entries as $entry)
      {
        $Result .= $entry->as_html_for_table ();
      }
      $Result .= "</dd>\n";
      
      return $Result;
    }
    
    return '';
  }

  /**
   * Render the group's full items as HTML.
   * Displays the group's header, then each item body using {@link pair_as_html_item()}.
   * @param MAIL_RENDERER_OPTIONS $options
   * @param string $top_link HTML text for the link back to the top of the page.
   */
  public function as_html_for_items ($options, $top_link)
  {
    $Result = '';

    if (isset ($this->folder))
    {
      $fldr = $this->folder;
      $Result = "<div class=\"horizontal-separator\" style=\"margin-top: 3.5em\"></div>\n";
      $Result .= '<a id="fldr_' . $fldr->id . "\"></a>\n";
      $Result .= '<h2>' . $fldr->title_as_html () . "</h2>\n";
    }

    foreach ($this->entries as $entry)
    {
      $Result .= $entry->as_html_for_items ($options, $top_link);
    }

    return $Result;
  }

  /**
   * Render the group for the TOC as plain text.
   * Displays the group's header, then each item title using {@link pair_as_plain_text_for_table()}.
   */
  public function as_plain_text_for_table ()
  {
    if (isset ($this->folder))
    {
      $Result = $this->_line ($this->subject->as_text () . ' in ' . $this->folder->title_as_plain_text ());
      foreach ($this->entries as $entry)
      {
        $Result .= $entry->as_plain_text_for_table ();
      }
      $Result .= $this->_line ();

      return $Result;
    }
    
    return '';
  }

  /**
   * Render the group's full items as plain text.
   * Displays the group's header, then each item body using {@link pair_as_plain_text_item()}.
   * @param MAIL_RENDERER_OPTIONS $options
   */
  public function as_plain_text_for_items ($options)
  {
    $Result = '';

    if (isset ($this->folder))
    {
      $Result = $this->_line ();
      $Result .= $this->_line ();
      $Result .= $this->_sep ('=');
      $Result .= $this->_line ($this->folder->title_as_plain_text ());
      $Result .= $this->_sep ('=');
      $Result .= $this->_line ();
    }

    $idx = 0;
    foreach ($this->entries as $entry)
    {
      if ($idx != 0)
      {
        $Result .= $this->_par ();
        $Result .= $this->_line ($this->_sep ('='));
      }
      $idx++;
      $Result .= $entry->as_plain_text_for_items ($options);
    }

    return $Result;
  }

  /**
   * Create a new TOC entry.
   * @param object $obj
   * @return MAIL_TOC_ENTRY_RENDERER
   * @access private
   */
  public function new_entry ($obj)
  {
    $Result = $this->_make_entry ($obj);
    $this->num_entries++;
    $this->entries [] = $Result;
    return $Result;
  }

  /**
   * Make a TOC entry.
   * Called from {@link new_entry()}.
   * @param object $obj
   * @return MAIL_TOC_ENTRY_RENDERER
   * @access private
   */
  protected function _make_entry ($obj)
  {
    $class_name = $this->app->final_class_name ('MAIL_TOC_ENTRY_RENDERER');
    return new $class_name ($this->toc, $this);
  }
}

/**
 * Generates an HTML or plain-text table of contents.
 * Manages a list of {@link MAIL_TOC_GROUP_RENDERER}s.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.6.0
 * @access private
 */
class MAIL_TOC_RENDERER extends RENDERER
{
  /**
   * Number of objects contained in all {@link $groups}.
   * @var integer */
  public $num_entries = 0;

  /**
   * List of {@link MAIL_TOC_GROUP_RENDERER}s.
   * @var array[MAIL_TOC_GROUP_RENDERER]
   */
  public $groups;

  /**
   * Create a table of contents for the given list.
   * @see MAIL_BODY_RENDERER_OBJECT
   * @param CONTEXT $context
   * @param array[MAIL_BODY_RENDERER_OBJECT] $pairs
   */
  public function MAIL_TOC_RENDERER ($context, $pairs)
  {
    RENDERER::RENDERER ($context);

    $entry = null;
    $group = null;
    $this->groups = array ();

    foreach ($pairs as $pair)
    {
      $obj = $pair->obj;

      if (! isset ($group) || ! $group->accepts_item ($obj))
      {
        $group = $this->new_group ($obj);
        unset ($entry);
      }

      if (! isset ($entry) || ! $entry->accepts_item ($obj))
      {
        $entry = $group->new_entry ($obj);
      }

      $entry->add_pair ($pair);
    }
  }

  /**
   * Render the table of contents as HTML.
   * Generally called before outputting the item bodies with {@link items_as_html()}.
   * @param MAIL_RENDERER_OPTIONS $options
   */
  public function table_as_html ($options)
  {
    $Result = "<a id=\"top\"></a>\n";
    $Result .= "<p>There are <span class=\"field\">{$options->content_summary}</span> in this email.</p>\n";
    $Result .= "<dl>\n";
    foreach ($this->groups as $group)
    {
      $Result .= $group->as_html_for_table ();
    }
    $Result .= "</dl>\n";
    return $Result;
  }

  /**
   * Render the full items in TOC order as HTML.
   * Generally called after outputting the table with {@link table_as_html()}.
   * @param MAIL_RENDERER_OPTIONS $options
   */
  public function items_as_html ($options)
  {
    $top_link = '<a href="#top">' . $this->app->resolve_icon_as_html ('{icons}indicators/top', 'Go to top of email', '16px') . "</a>\n";
    $Result = '';
    foreach ($this->groups as $group)
    {
      $Result .= $group->as_html_for_items ($options, $top_link);
    }
    return $Result;
  }

  /**
   * Render the table of contents as plain text.
   * Generally called before outputting the item bodies with {@link items_as_plain_text()}.
   * @param MAIL_RENDERER_OPTIONS $options
   */
  public function table_as_plain_text ($options)
  {
    $Result = $this->_par ("There are [{$options->content_summary}] items in this email.");
    foreach ($this->groups as $group)
    {
      $Result .= $group->as_plain_text_for_table ();
    }
    return $Result;
  }

  /**
   * Render the full items in TOC order as plain text.
   * Generally called after outputting the table with {@link table_as_plain_text()}.
   * @param MAIL_RENDERER_OPTIONS $options
   */
  public function items_as_plain_text ($options)
  {
    $Result = '';
    foreach ($this->groups as $group)
    {
      $Result .= $group->as_plain_text_for_items ($options);
    }
    return $Result;
  }

  /**
   * Create a new TOC group.
   * The group may or may not be based on a folder.
   * @param FOLDER $fldr
   * @return MAIL_TOC_GROUP_RENDERER
   * @access private
   */
  public function new_group ($fldr)
  {
    $Result = $this->_make_group ($fldr);
    $this->groups [] = $Result;
    return $Result;
  }

  /**
   * Make a TOC group.
   * Called from {@link new_group()}.
   * @param object $obj
   * @return MAIL_TOC_GROUP_RENDERER
   * @access private
   */
  protected function _make_group ($obj)
  {
    $class_name = $this->app->final_class_name ('MAIL_TOC_GROUP_RENDERER');
    return new $class_name ($this, $obj);
  }
}

/**
 * Generates an HTML or plain-text email with table of contents.
 * Organizes {@link OBJECT_IN_FOLDER}s into lists based on their
 * containing {@link FOLDER}s.
 * @package webcore
 * @subpackage mail
 * @version 3.0.0
 * @since 2.6.0
 */
class WEBCORE_MAIL_BODY_RENDERER extends THEMED_MAIL_BODY_RENDERER
{
  /**
   * All objects' contents returned as HTML.
   * @param MAIL_RENDERER_OPTIONS $options
   * @return string
   * @access private
   */
  protected function _html_content ($options)
  {
    $toc = $this->_make_toc ();
    if ($toc->num_entries > 1)
    {
      $Result = $toc->table_as_html ($options);
      $Result .= $toc->items_as_html ($options);
      return $Result;
    }

    return parent::_html_content ($options);
  }

  /**
   * All objects' contents returned as text.
   * @param MAIL_RENDERER_OPTIONS $options
   * @return string
   * @access private
   */
  protected function _text_content ($options)
  {
    $toc = $this->_make_toc ();
    if ($toc->num_entries > 1)
    {
      $Result = $toc->table_as_plain_text ($options);
      $Result .= $toc->items_as_plain_text ($options);
      return $Result;
    }

    return parent::_text_content ($options);
  }

  /**
   * Make a table of contents renderer.
   * @return MAIL_TOC_RENDERER
   * @access private */
  protected function _make_toc ()
  {
    $class_name = $this->app->final_class_name ('MAIL_TOC_RENDERER');
    return new $class_name ($this->app, $this->objects);
  }
}

?>