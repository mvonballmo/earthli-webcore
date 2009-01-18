<?php

require_once ('webcore/gui/grid.php');

class INVITEE_TABLE_GRID extends STANDARD_GRID
{
  var $object_name = 'table';
  var $even_columns = FALSE;
  var $width = '';
  var $padding = 8;
  var $show_separator = FALSE;
  var $show_links = FALSE;

  function _draw_box (&$obj)
  {
?>
<h3>Table #<?php echo $obj->number; ?></h3>
<?php
    $q = $obj->invitee_query ();
    $objs = $q->objects ();
    foreach ($objs as $obj)
    {
      if ($this->show_links)
      {
  ?>
      <a href="view_party.php?party=<?php echo $obj->party; ?>"><?php echo $obj->full_name (); ?></a><br>
  <?php
      }
      else
      {
  ?>
      <?php echo $obj->full_name (); ?><br>
  <?php
      }
    }
?>
<p class="notes">(<span class="field"><?php echo sizeof ($objs); ?></span> people)</p>
<?php
  }
}

?>