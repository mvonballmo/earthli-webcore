<?php

require_once ('webcore/forms/form.php');

class RSVP_FORM extends FORM
{
  var $button = 'Save';
  var $button_icon = '{icons}buttons/save';

  function RSVP_FORM (&$context, &$objs)
  {
    FORM::FORM ($context);

    $field = new TEXT_FIELD ();
    $field->id = "party";
    $field->title = "Party";
    $field->required = TRUE;
    $field->min_length = 1;
    $field->max_length = 100;
    $field->visible = FALSE;
    $this->add_field ($field);

    $field = new INTEGER_FIELD ();
    $field->id = "picnic_guests";
    $field->title = "Picnic Guests";
    $field->min_value = 0;
    $field->max_value = 5;
    $this->add_field ($field);

    foreach ($objs as $obj)
    {
      $this->_objects =& $objs;

      $field = new INTEGER_FIELD ();
      $field->id = "reception_$obj->id";
      $field->title = 'Reception';
      $field->required = TRUE;
      $field->min_value = 0;
      $field->max_value = 2;
      $this->add_field ($field);

      $field = new INTEGER_FIELD ();
      $field->id = "picnic_$obj->id";
      $field->title = 'Picnic';
      $field->required = TRUE;
      $field->min_value = 0;
      $field->max_value = 2;
      $this->add_field ($field);
    }

  }

  function load_with_defaults ()
  {
    $this->set_value ('party', read_var ('party'));

    $pg = FALSE;

    foreach ($this->_objects as $obj)
    {
      if (! $pg)
      {
        if ($obj->picnic == 1)
        {
          $this->set_value ('picnic_guests', $obj->picnic_guests);
          $pg = TRUE;
        }
      }

      $this->set_value ("reception_$obj->id", $obj->reception);
      $this->set_value ("picnic_$obj->id", $obj->picnic);
    }
  }

  function commit (&$objs)
  {
    $pg = null;
    foreach ($objs as $obj)
    {
      $recep = $this->value_for ("reception_$obj->id");
      $pic = $this->value_for ("picnic_$obj->id");
      if ($recep || $pic)
      {
        if (! $obj->reception && ! $obj->picnic)
          $obj->time_registered->set_from_php (time ());
      }
      if (! $recep && ! $pic)
        // undoing registration
        $obj->time_registered->set_from_php (0);

      $obj->reception = $this->value_for ("reception_$obj->id");
      $obj->picnic = $this->value_for ("picnic_$obj->id");
      if (! isset ($pg))
      {
        if ($obj->picnic == 1)
        {
          $obj->picnic_guests = $this->value_for ("picnic_guests");
          $pg = TRUE;
        }
        else
          $obj->picnic_guests = 0;
      }
      else
        $obj->picnic_guests = 0;

      $obj->update ();
    }
  }

  /**
   * @param FORM_RENDERER &$renderer
   * @access private
   */
  function _draw_controls (&$renderer)
  {
    $props = $renderer->make_list_properties ();
    $props->add_item ('Undecided', 0);
    $props->add_item ('Yes', 1);
    $props->add_item ('No', 2);
?>
  <table cellspacing="0" cellpadding="4" style="margin: auto">
    <tr>
      <th>Name</th>
      <th>Reception</th>
      <th>Picnic</th>
    </tr>
<?php
    foreach ($this->_objects as $obj)
    {
?>
    <tr>
      <td class="label"><?php echo $obj->full_name (); ?></td>
      <td>
        <?php echo $renderer->drop_down_as_html ("reception_$obj->id", $props); ?>
      </td>
      <td>
        <?php echo $renderer->drop_down_as_html ("picnic_$obj->id", $props); ?>
      </td>
    </tr>
<?php
    }
?>
    <tr>
      <td class="label">Picnic Guests*</td>
      <td></td>
      <td>
        <?php
          $options = new FORM_TEXT_CONTROL_OPTIONS ();
          $options->width = '3em';
          echo $renderer->text_line_as_html ('picnic_guests', $options); ?>
      </td>
    </tr>
    <tr>
      <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" style="text-align: right">
        <?php echo $renderer->submit_button_as_html (); ?>
      </td>
    </tr>
  </table>
  <p class="notes" style="text-align: center; width: 50%; margin: auto">*The reception is adults-only, but you can bring your kids to the picnic
    on the following day. Please indicate how many extra people you plan to bring.</p>
<?php
  }

}

?>