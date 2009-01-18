<?php

require_once ('webcore/gui/grid.php');

class INVITEE_GRID extends STANDARD_GRID
{
  var $object_name = 'invitee';
  var $even_columns = FALSE;
  var $width = '';
  var $show_separator = FALSE;
  var $show_links = TRUE;

  function _draw_header ()
  {
?>
  <tr>
    <th style="text-align: left">
      Person
    </th>
    <th class="spacer">&nbsp;</th>
    <th style="text-align: center">
      Reception
    </th>
    <th class="spacer">&nbsp;</th>
    <th style="text-align: center">
      Picnic
    </th>
    <th class="spacer">&nbsp;</th>
    <th style="text-align: center">
      Registered on
    </th>
  </tr>
<?php
  }

  function _draw_box (&$obj)
  {
    if ($this->show_links)
    {
?>
    <a href="view_party.php?party=<?php echo $obj->party; ?>"><?php echo $obj->full_name (); ?></a>
<?php
    }
    else
    {
?>
    <span class="field"><?php echo $obj->full_name (); ?></span>
<?php
    }
    echo "</td>\n<td>&nbsp;</td>\n<td style=\"text-align: center\">";
    echo $obj->reception_status ();
    echo "</td>\n<td>&nbsp;</td>\n<td style=\"text-align: center\">";
    echo $obj->picnic_status ();
    echo "</td>\n<td>&nbsp;</td>\n<td style=\"text-align: center\">";
    if ($obj->time_registered->is_valid ())
      echo date ("m-d-Y H:i", $obj->time_registered->as_php ());
    else
      echo '--';
  }

}

?>