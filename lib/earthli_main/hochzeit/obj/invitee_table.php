<?php

require_once ('webcore/obj/webcore_object.php');

class INVITEE_TABLE extends WEBCORE_OBJECT
{
  var $number;

  function invitee_query ()
  {
    $class_name = $this->context->final_class_name ('INVITEE_QUERY', 'hochzeit/db/invitee_query.php');
    $Result = new $class_name ($this->context);
    $Result->restrict ("table_number = $this->number");
    $Result->restrict ('reception = 1');
    return $Result;
  }

  function load (&$db)
  {
    $this->number = $db->f ('table_number');
  }

}

?>