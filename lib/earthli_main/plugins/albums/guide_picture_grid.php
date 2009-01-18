<?php

require_once ('webcore/gui/grid.php');

class GUIDE_PICTURE_GRID extends CSS_FLOW_GRID
{
  var $object_name = 'Picture';
  var $box_style = '';
  var $padding = 0;
  var $spacing = 0;
  var $width = '';
  var $show_paginator = FALSE;

  function _draw_box (&$obj)
  {
?>
    <div style="clear: right; float: right; width: 20em; text-align: center; margin: 1.5em 0em">
      <a href="<?php echo $obj->full_file_name (); ?>"><img class="frame" src="<?php echo $obj->full_thumbnail_name (); ?>" alt="<?php echo $obj->title_as_plain_text (); ?>"></a>
    </div>
    <div style="margin-right: 21em">
      <h3><?php echo $this->app->resolve_icon_as_html ('{icons}/indicators/working', '', '20px'); ?> <?php echo $obj->title_as_html (); ?></h3>
      <?php echo $obj->description_as_html (); ?>
      <div style="clear: both"></div>
    </div>
<?php
    $this->show_paginator = TRUE; // enable it only for the end of the grid
  }
}

?>