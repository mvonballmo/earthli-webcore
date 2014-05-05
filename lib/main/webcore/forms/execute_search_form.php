<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
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
require_once ('webcore/forms/form.php');
require_once ('webcore/gui/layer.php');
require_once ('webcore/obj/search.php');

/**
 * Create a filter for objects in a WebCore application.
 * @package webcore
 * @subpackage forms
 * @version 3.5.0
 * @since 2.5.0
 */
class EXECUTE_SEARCH_FORM extends ID_BASED_FORM
{
  /**
   * @var string
   */
  public $name = 'search_form';

  /**
   * @var string
   * @access private
   */
  public $method = 'get';

  /**
   * @var string
   */
  public $button = 'Search';

  /**
   * @var string
   */
  public $button_icon = '{icons}buttons/view';

  /**
   * @var boolean
   */
  public $controls_visible = true;

  /**
   * @var string
   */
  public $action = 'search.php';

  /**
   * @var string
   */
  public $action_anchor = 'search-results';

  /**
   * Initialize the form with a search object.
   * If the search is <c>null</c>, the form will be shown in "quick search"
   * mode. This shows only the text search and a drop-down to select the type
   * of object to search.
   * @param APPLICATION $context Main application.
   * @param SEARCH $search Build the form based on this search object.
   */
  public function __construct ($context, $search)
  {
    parent::__construct ($context);

    $this->_search = $search;
    
    $entry_type_infos = $this->app->entry_type_infos ();
    $type = $entry_type_infos [0]->id;

    if (isset ($this->_search))
    {
      $search->fields->add_fields ($this);    
    }
    else
    {
      $this->action_anchor = '';

      $field = new TEXT_FIELD ();
      $field->id = 'search_text';
      $field->caption = '';
      $this->add_field ($field);

      $search_template = $this->app->make_search ($type);
      $search_template->fields->add_fields ($this, false);
    }

    $field = new BOOLEAN_FIELD ();
    $field->id = 'quick_search';
    $field->caption = 'Quick Search';
    $field->visible = false;
    $field->set_value (true);
    $this->add_field ($field);

    $field = new ENUMERATED_FIELD ();
    $field->id = 'type';
    $field->caption = '';
    $field->visible = ! isset ($this->_search);
    
    /* Fill with all the registered search types. */
    $type_infos = $this->app->search_type_infos ();
    foreach ($type_infos as $t)
    {
      $field->add_value ($t->id);
    }
    $this->add_field ($field);

    /* Set the first entry type as the default search. */
    if (! empty ($entry_type_infos))
    {
      $this->set_value ('type', $type);
    }
  }

  /**
   * Initialize the form's fields with default values and visibilities.
   */
  public function load_with_defaults ()
  {
    parent::load_with_defaults ();
    
    if (isset ($this->_search))
    {
      $this->_search->fields->load_with_defaults ($this);
      $this->set_value ('type', $this->_search->type);
      $this->set_value ('quick_search', false);
    }

    if ($this->is_field ('folder_search_type'))
    {
      $this->set_value ('folder_search_type', Search_user_context_none);
    }
  }

  /**
   * Load initial properties from this branch.
   * @param SEARCH $obj
   */
  public function load_from_object ($obj)
  {
    parent::load_from_object ($obj);
    if (isset ($this->_search))
    {
      $this->_search->fields->load_from_object ($this, $obj);
    }
  }

  /**
   * Called after fields are validated.
   * @param SEARCH $obj
   * @access private
   */
  protected function _post_validate ($obj)
  {
    parent::_post_validate ($obj);
    if (isset ($this->_search))
    {
      $this->_search->fields->validate ($this, $obj);
    }
  }

  /**
   * Read in values from the {@link $method} array.
   * @access private
   */
  protected function _load_from_request ()
  {
    parent::_load_from_request ();
    if ($this->value_for ('quick_search'))
    {
      $this->load_with_defaults ();
      $this->load_from_request_for ('search_text');
      if ($this->is_field ('folder_ids'))
      {
        $this->load_from_request_for ('folder_ids');
        $this->load_from_request_for ('folder_search_type');
      }
      
      if ($this->is_field ('state'))
      {
        $this->load_from_request_for ('state');
        $this->load_from_request_for ('not_state');
      }
    }
  }

  /**
   * Execute the form.
   * @param SEARCH $obj
   * @access private
   */
  public function commit ($obj)
  {
    $this->_search->fields->store_to_object ($this, $obj);
  }

  /**
   * Draw any Javascript that the form needs to enable/disable controls.
   * @access private
   */
  protected function _draw_scripts ()
  {
    if (isset ($this->_search))
    {
?>
  function save_search (f)
  {
    <?php if ($this->object_exists ()) { ?>
    f.action = 'edit_search.php';
    <?php } else { ?>
    f.action = 'create_search.php';
    <?php } ?>
    f.submit ();
  }
<?php
    }
  }

  /**
   * @param FORM_RENDERER $renderer
   * @access private
   */
  protected function _draw_controls ($renderer)
  {
    if (! isset ($this->_search))
    {
      $renderer->labels_css_class = 'top';

      $this->button = 'Go';

      $renderer->start ();
        $renderer->draw_text_line_with_button_row('search_text', $renderer->submit_button_as_html ());
      
        $props = $renderer->make_list_properties ();
        $props->css_class = 'detail';

        /* Fill with all the registered search types. */
        $type_infos = $this->app->search_type_infos ();
        foreach ($type_infos as $t)
        {
          $props->add_item ($t->plural_title, $t->id);
        }

        $renderer->start_row();
        echo '<div class="two-inputs">';
        echo $renderer->drop_down_as_html('type', $props);
        echo '</div>';
        $renderer->finish_row();
      $renderer->finish ();
    }
    else
    {
      $renderer->start ();
      $this->_search->fields->draw_fields ($this, $renderer);

      $renderer->start_row();
      echo $renderer->submit_button_as_HTML ();
      $renderer->finish_row();

      $renderer->finish ();
    }
  }
}

?>