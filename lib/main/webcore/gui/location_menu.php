<?php

/**
 * @copyright Copyright (c) 2002-2010 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage gui
 * @version 3.3.0
 * @since 2.2.1
 */

/****************************************************************************

Copyright (c) 2002-2010 Marco Von Ballmoos

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
require_once ('webcore/gui/menu.php');

/**
 * Displays location within a page or object hierarchy.
 * Add folders and objects to the menu to build the hierarchy.
 * @package webcore
 * @subpackage gui
 * @version 3.3.0
 * @since 2.2.1
 */
class LOCATION_MENU extends MENU
{
  /**
   * Adds a link to the root page of the application.
   * The text for the link comes from {@link APPLICATION::$short_title} and
   * simply uses './' as a URL. The root page's absolute URL can be retrieved
   * using {@link APPLICATION::url()}. Has no effect if there is no active
   * application on the page.
   * @see add_root_link()
   */
  public function add_application_link ()
  {
    if (isset ($this->page->app) && $this->page->app->short_title)
    {
      $this->prepend ($this->page->app->short_title, $this->page->app->path_to (Folder_name_application));
    }
  }

  /**
   * Adds a link to the root page of the web site.
   * If {@link ENVIRONMENT::$title} is assigned, that text is used for a link
   * to '/'. This link's absolute URL can be retrieved using {@link ENVIRONMENT::host_name()}.
   * @param boolean $include_application If true, {@link add_application_link()} is called as well.
   * @see add_application_link()
   */
  public function add_root_link ($include_application = true)
  {
    if ($include_application)
    {
      $this->add_application_link ();
    }
    if ($this->env->title)
    {
      $this->prepend ($this->env->title, $this->page->path_to (Folder_name_root));
    }
  }
  
  /**
   * Add a folder and its parents as links to the location.
   * Each folder is displayed as a link to the right of its parent.
   * @see add_folder_text()
   * @param FOLDER $folder
   * @param string $page_args Add these arguments to the URL's query string.
   * @param string $page_url Replace the folder's home page with this page.
   */
  public function add_folder_link ($folder, $page_args = '', $page_url = '')
  {
    $parent = $folder;

    while (! empty($parent->id) && ! $parent->is_root ())
    {
      $t = $parent->title_formatter ();
      $t->CSS_class = 'nav-item';
      if ($page_url)
      {
        $t->set_name ($page_url);
      }
      if ($page_args)
      {
        $t->add_arguments ($page_args);
      }
      
      $this->prepend ($parent->title_as_link ($t));
      $parent = $parent->parent_folder ();
    }

    $this->add_root_link ();
  }

  /**
   * Add a folder as the current location.
   * The folder itself is displayed as text, while its parents are listed to the left
   * of it as links.
   * @see add_folder_link()
   * @param FOLDER $folder
   * @param string $page_args Add these arguments to the URL's query string.
   * @param string $page_url Replace the folder's home page with this page.
   */
  public function add_folder_text ($folder, $page_args = '', $page_url = '')
  {
    $this->add_folder_link ($folder->parent_folder ());

    $t = $folder->title_formatter ();
    $t->max_visible_output_chars = 0;
    $title = $folder->title_as_html($t);
    $icon = $folder->icon_as_html('16px');

    if ($icon)
    {
      $title = $icon . ' ' . $title;
    }
    $this->append ($title);
  }

  /**
   * Add this object's home page to the location.
   * @param NAMED_OBJECT $obj
   */
  public function add_object_link ($obj, $page_args = '')
  {
    // Don't allow objects in the list to set the style of the navigation bar.

    $t = $obj->title_formatter ();
    $t->CSS_class = 'nav-item';
    if ($page_args)
    {
      $t->add_arguments ($page_args);
    }
    $this->append ($obj->title_as_link ($t));
  }

  /**
   * Add this object's (unlinked) name to the location.
   * @param NAMED_OBJECT $obj
   */
  public function add_object_text ($obj)
  {
    $t = $obj->title_formatter ();
    $t->max_visible_output_chars = 0;
    $this->append ($obj->title_as_html ($t));
  }
}

?>