<?php

require_once ('webcore/db/query.php');

class INVITEE_TABLE_QUERY extends QUERY
{
  /**
   * Apply default restrictions and tables.
   */
  function apply_defaults () 
  {
    QUERY::apply_defaults ();
    $this->set_table ('hochzeit_guests obj');
    $this->set_select ('distinct table_number');
    $this->set_order ('table_number');
    $this->restrict ('table_number <> 0');
  }

  function _make_object ()
  {
    $class_name = $this->context->final_class_name ('INVITEE_TABLE', 'hochzeit/obj/invitee_table.php');
    return new $class_name ($this->context);
  }
}

?>