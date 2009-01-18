<?php

require_once ('webcore/obj/unique_object.php');

class INVITEE extends UNIQUE_OBJECT
{
  var $party;
  var $first_name;
  var $middle_name;
  var $last_name;
  var $reception;
  var $picnic;
  var $picnic_guests;
  var $table_number;

  function INVITEE (&$context)
  {
    UNIQUE_OBJECT::UNIQUE_OBJECT ($context);
    $this->time_registered = $context->make_date_time ();
  }

  function full_name ()
  {
    if ($this->first_name)
      $names [] = $this->first_name;
    if ($this->middle_name)
      $names [] = $this->middle_name;
    if ($this->last_name)
      $names [] = $this->last_name;

    if (sizeof ($names))
      return join (' ', $names);
    else
      return '{unknown user}';
  }

  function reception_status ()
  {
    switch ($this->reception)
    {
    case 0: return '--';
    case 1: return '<span class="selected">yes</span>';
    case 2: return '<span class="error">no</span>';
    default: return '--';
    }
  }

  function picnic_status ()
  {
    switch ($this->picnic)
    {
    case 0: return '--';
    case 1:
      if ($this->picnic_guests)
        return "<span class=\"selected\">yes</span> ($this->picnic_guests)";
      else
        return "<span class=\"selected\">yes</span>";
    case 2: return '<span class="error">no</span>';
    default: return '--';
    }
  }

  function load (&$db)
  {
    parent::load ($db);

    $this->party = $db->f ("party");
    $this->table_number = $db->f ("table_number");
    $this->first_name = $db->f ("first_name");
    $this->last_name = $db->f ("last_name");
    $this->middle_name = $db->f ("middle_name");
    $this->reception = $db->f ("reception");
    $this->picnic = $db->f ("picnic");
    $this->picnic_guests = $db->f ("picnic_guests");
    $this->time_registered->set_from_iso ($db->f ("time_registered"));
  }

  function update ()
  {
    if (! $this->picnic_guests)
      $this->picnic_guests = 0;
    $t = $this->time_registered->as_iso ();
    if ($t)
      $this->db->query ("UPDATE hochzeit_guests SET picnic_guests = $this->picnic_guests, reception = $this->reception, picnic = $this->picnic, time_registered = '$t' WHERE id = $this->id");
    else
      $this->db->query ("UPDATE hochzeit_guests SET picnic_guests = $this->picnic_guests, reception = $this->reception, picnic = $this->picnic, time_registered = NULL WHERE id = $this->id");
  }

}

?>