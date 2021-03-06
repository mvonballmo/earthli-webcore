<?php

/**
 * @copyright Copyright (c) 2002-2014 Marco Von Ballmoos
 * @author Marco Von Ballmoos
 * @filesource
 * @package news
 * @subpackage gui
 * @version 3.6.0
 * @since 2.7.0
 */

/****************************************************************************

Copyright (c) 2002-2014 Marco Von Ballmoos

This file is part of earthli News.

earthli News is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

earthli News is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with earthli News; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

For more information about the earthli News, visit:

http://earthli.com/software/webcore/app_news.php

****************************************************************************/

/** */
require_once ('webcore/gui/content_object_grid.php');

/**
 * Display {@link ARTICLE}s from a {@link QUERY}.
 * Shows only the title and date of the article. Good for showing recent links.
 * @package news
 * @subpackage gui
 * @version 3.6.0
 * @since 2.7.0
 */
class TINY_ARTICLE_GRID extends CONTENT_OBJECT_GRID
{
  /**
   * @param CONTEXT $context Context to which this grid belongs.
   */
  public function __construct($context)
  {
    parent::__construct($context);

    $this->css_class .= ' full-width';
  }

  protected function _draw_box ($obj)
  {
    $t = $obj->title_formatter ();
    $t->css_class = '';
    $fd = $obj->time_published->formatter ();
    $fd->type = Date_time_format_short_date;
?>
  <p>
    <?php echo $obj->time_published->format ($fd); ?>: <?php echo $obj->title_as_link ($t); ?>
  </p>
<?php
  }
}

?>