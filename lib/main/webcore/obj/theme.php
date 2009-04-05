<?php

/**
 * @copyright Copyright (c) 2002-2009 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage obj
 * @version 3.1.0
 * @since 2.2.1
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

/***/
require_once ('webcore/obj/renderable.php');

/**
 * Skin for a web page.
 * Used by {@link THEMED_PAGE}.
 * @package webcore
 * @subpackage obj
 * @version 3.1.0
 * @since 2.2.1
 */
class THEME extends RENDERABLE
{
  /**
   * English name of the the theme.
   * @var string
   */
  public $title;

  /**
   * The name of the PHP class to use to render this theme.
   *  In addition to specifyin icon sets and style sheets, a theme can also specify which renderer
   * will layout the basic structure of the page (the header/body/footer).
   * @var string
   */
  public $renderer_class_name;

  /**
   * Name of the CSS Stylesheet for the colors, borders, spacing, etc.
   * @var string
   */
  public $main_CSS_file_name;

  /**
   * Name of the CSS font-face stylesheet to use.
   * The font is completely separated from the stylesheet, so that users can mix and match font-styles
   * with different themes.
   * @var string
   */
  public $font_name_CSS_file_name;

  /**
   * Name of the CSS font-size stylesheet to use.
   * The font is completely separated from the stylesheet, so that users can mix and match font-sizes
   * with different themes (font size is nice to adjust to suit their needs).
   * @var string
   */
  public $font_size_CSS_file_name;

  /**
   * Path to icons (appended to {@link Folder_name_icons} folder).
   * Each theme can provide a specialized set of icons.
   * @var string
   */
  public $icon_set;

  /**
   * Default extension for images in this theme.
   * Specify which set of icons to use within the path.
   * @var string
   */
  public $icon_extension;

  /**
   * @var DATE_TIME
   */
  public $time_created;

  /**
   * @param CONTEXT $context
   */
  public function THEME ($context)
  {
    RENDERABLE::RENDERABLE ($context);
    $this->time_created = $context->make_date_time ();
  }

  /**
   * Return the URL for a thumbnail of this theme.
   * @return string
   */
  public function snapshot_thumbnail_name ()
  {
    return $this->_resource_name ('snapshot_tn.png');
  }

  /**
   * Return the URL for a full-size snapshot of the theme.
   * @return string
   */
  public function snapshot_name ()
  {
    return $this->_resource_name ('snapshot.png');
  }

  /**
   * @return string
   */
  public function raw_title ()
  {
    return $this->title;
  }

  /**
   * @param DATABASE $db Database from which to load values.
   */
  public function load ($db)
  {
    parent::load ($db);
    $this->title = $db->f ('title');
    $this->renderer_class_name = $db->f ('renderer_class_name');
    $this->main_CSS_file_name = $db->f ('main_CSS_file_name');
    $this->font_name_CSS_file_name = $db->f ('font_name_CSS_file_name');
    $this->font_size_CSS_file_name = $db->f ('font_size_CSS_file_name');
    $this->icon_set = $db->f ('icon_set');
    $this->icon_extension = $db->f ('icon_extension');
    $this->time_created->set_from_iso ($db->f ('time_created'));
  }

  /**
   * @param SQL_STORAGE $storage Store values to this object.
   */
  public function store_to ($storage)
  {
    parent::store_to ($storage);
    $tname = $this->table_name ();
    $storage->add ($tname, 'title', Field_type_string, $this->title);
    $storage->add ($tname, 'renderer_class_name', Field_type_string, $this->renderer_class_name);
    $storage->add ($tname, 'main_CSS_file_name', Field_type_string, $this->main_CSS_file_name);
    $storage->add ($tname, 'font_name_CSS_file_name', Field_type_string, $this->font_name_CSS_file_name);
    $storage->add ($tname, 'font_size_CSS_file_name', Field_type_string, $this->font_size_CSS_file_name);
    $storage->add ($tname, 'icon_set', Field_type_string, $this->icon_set);
    $storage->add ($tname, 'icon_extension', Field_type_string, $this->icon_extension);
    $storage->add ($tname, 'time_created', Field_type_date_time, $this->time_created, Storage_action_create);
  }

  /**
   * Name of the home page name for this object.
   * @return string
   */
  public function page_name ()
  {
    return $this->snapshot_name ();
  }
  
  /**
   * Name of this object's database table.
   * @return string
   * @access private
   */
  public function table_name ()
  {
    return $this->page->theme_options->table_name;
  }

  /**
   * External resource for this theme
   * @param string $file_name
   * @return string
   * @access private
   */
  protected function _resource_name ($file_name)
  {
    $url = new URL ($this->main_CSS_file_name);
    $url->replace_extension ('');
    $url->append_to_name ('_' . $file_name);
    return $this->context->resolve_file ($url->as_html ());
  }

  /**
   * Return default handler objects for supported tasks.
   * @param string $handler_type Specific functionality required.
   * @param object $options
   * @return object
   * @access private
   */
  protected function _default_handler_for ($handler_type, $options = null)
  {
    switch ($handler_type)
    {
      case Handler_html_renderer:
        include_once ('webcore/gui/theme_renderer.php');
        return new THEME_RENDERER ($this->context);
      case Handler_commands:
        include_once ('webcore/cmd/theme_commands.php');
        return new THEME_COMMANDS ($this);
      default:
        return parent::_default_handler_for ($handler_type, $options);
    }
  }
}

?>