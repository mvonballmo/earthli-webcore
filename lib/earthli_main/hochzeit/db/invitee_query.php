<?php

require_once ('webcore/db/query.php');

class INVITEE_QUERY extends QUERY
{
  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    QUERY::apply_defaults ();
    $this->set_table ('hochzeit_guests obj');
    $this->set_order ('last_name, first_name, middle_name');
    $this->set_day_field ('time_registered');
  }

  function _make_object ()
  {
    $class_name = $this->context->final_class_name ('INVITEE', 'hochzeit/obj/invitee.php');
    return new $class_name ($this->context);
  }
}

?>