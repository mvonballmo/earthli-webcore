<?php

require_once ('webcore/gui/entry_grid.php');

class EARTHLI_ARTICLE_GRID extends CONTENT_OBJECT_GRID
{
  var $object_name = 'article';
  var $num_visible_chars = 150;
  var $show_separator = FALSE;

  /**
   * @param ARTICLE &$obj
    * @access private
    */
  function _draw_box (&$obj)
  {
?>
  <div style="float: left">
    <?php echo $this->app->resolve_icon_as_html ('{app_icons}app/news', 'News', '20px'); ?>
  </div>
  <div class="detail" style="margin-left: 25px">
    <?php echo $obj->title_as_link (); ?>
    <div style="text-align: right">
      <?php
        $fd =& $obj->time_created->formatter ();
        $fd->type = Date_time_format_short_date;
        echo $obj->time_created->format ($fd);
      ?>
    </div>
  </div>
  <div class="detail" style="margin-top: 1em; margin-bottom: .5em; clear: both">
  <?php
    $munger =& $obj->html_formatter ();
    $munger->max_visible_output_chars = $this->num_visible_chars;
//    echo $obj->description_as_html ($munger);
  ?>
  </div>
<?php
  }
}

/**
 * Display {@link ARTICLE}s from a query.
  * This formats the articles for the earthli software home page.
  * @package extensions
  */
class EARTHLI_SOFTWARE_ARTICLE_GRID extends STANDARD_GRID
{
  /**
   * @var string
   */
  var $object_name = 'article';

  /**
   * @param ARTICLE &$obj
    * @access private
    */
  function _draw_box (&$obj)
  {
?>
  <div class="grid-title">
    <?php echo $obj->title_as_html (); ?>
  </div>
  <div class="text-flow" style="margin-top: 1em; margin-bottom: 1em">
  <?php
    echo $obj->description_as_html ();
  ?>
  </div>
  <div class="info-box-bottom">
    Posted on
    <?php
      echo $obj->time_created->format ();
    ?>
  </div>
<?php
  }
}
?>