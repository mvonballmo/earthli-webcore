<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package webcore
 * @subpackage renderer
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

/** */
require_once ('webcore/gui/object_renderer.php');

/**
 * Render details for {@link FOLDER}s.
 * @package webcore
 * @subpackage renderer
 * @version 3.6.0
 * @since 2.5.0
 */
class HISTORY_ITEM_RENDERER extends OBJECT_RENDERER
{
  /**
   * Outputs the object as HTML.
   * @param HISTORY_ITEM $obj
   * @access private
   */
  protected function _display_as_html ($obj)
  {
    $creator = $obj->creator ();
?>
  <p class="detail">
    <?php echo $obj->kind_as_icon () . ' ' . $creator->title_as_link () . ' - ' . $this->time ($obj->time_created); ?>
  </p>
<?php
    if (! $obj->description && ! $obj->system_description)
    {
      switch ($obj->kind)
      {
      case History_item_created:
        echo '<p>Created.</p>';
        break;
      case History_item_deleted:
        echo '<p>Deleted.</p>';
        break;
      }
    }
    else
    {
      echo $obj->description_as_html ();
      echo $obj->system_description_as_html ();
    }
  }

  /**
   * Outputs the object as plain text.
   * @param HISTORY_ITEM $obj
   * @access private
   */
  protected function _display_as_plain_text ($obj)
  {
    $creator = $obj->creator ();
    echo $this->line (ucfirst ($obj->kind_as_text ()) . ' by ' . $creator->title_as_plain_text () . ' - ' . $this->time ($obj->time_created));
    echo $this->line ($this->sep ());

    if (! $obj->description && ! $obj->system_description)
    {
      switch ($obj->kind)
      {
      case History_item_created:
        echo $this->line ('Created.');
        break;
      case History_item_deleted:
        echo $this->line ('Deleted.');
        break;
      }
    }
    else
    {
      if ($obj->description)
      {
        echo $this->line ($obj->description_as_plain_text ());
      }

      echo $obj->system_description_as_plain_text ();
    }
  }
}

?>