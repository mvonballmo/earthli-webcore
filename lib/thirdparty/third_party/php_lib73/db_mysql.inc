<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998-2000 NetUSE AG
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: db_mysql.inc,v 1.3 2001/05/17 00:57:31 chrisj Exp $
 *
 */

class DB_Sql
{
  /* public: connection parameters */
  public $Host = "";
  public $Database = "";
  public $User = "";
  public $Password = "";
  
  /* public: configuration parameters */
  public $Auto_Free = 0; ## Set to 1 for automatic mysql_free_result()
  public $Debug = 0; ## Set to 1 for debugging messages.
  public $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
  public $Seq_Table = "db_sequence";
  
  /* public: result array and current row number */
  public $Record = array ();
  public $Row;
  
  /* public: current error number and error text */
  public $Errno = 0;
  public $Error = "";
  
  /* public: this is an api revision, not a CVS revision. */
  public $type = "mysql";
  public $revision = "1.2";
  
  /* private: link and query handles */
  protected $Link_ID = 0;
  protected $Query_ID = 0;

  private $_mysqli;

  /* public: connection management */
  function connect ($Database = "", $Host = "", $User = "", $Password = "")
  {
    /* Handle defaults */
    if ("" == $Database)
      $Database = $this->Database;
    if ("" == $Host)
      $Host = $this->Host;
    if ("" == $User)
      $User = $this->User;
    if ("" == $Password)
      $Password = $this->Password;
      
    /* establish connection, select database */
    if (0 == $this->Link_ID)
    {
      $this->_mysqli = new mysqli($this->Host, $this->User, $this->Password, $this->Database);
      if ($this->_mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
      }
      $this->Link_ID = @mysql_pconnect ($Host, $User, $Password);
      if (!$this->Link_ID)
      {
        $this->halt ("pconnect($Host, $User, \$Password) failed.");
        return 0;
      }
      
      if (!@mysql_select_db ($Database, $this->Link_ID))
      {
        $this->halt ("cannot use database " . $this->Database);
        return 0;
      }
    }
    
    return $this->Link_ID;
  }

  /* public: discard the query result */
  function free ()
  {
    if (is_resource($this->Query_ID))
    {
      @mysql_free_result ($this->Query_ID);
    }
    $this->Query_ID = 0;
  }

  /* public: perform a query */
  function query ($Query_String)
  {
    /* No empty queries, please, since PHP4 chokes on them. */
    if ($Query_String == "")
      /* The empty query string is passed on from the constructor,
       * when calling the class without a query, e.g. in situations
       * like these: '$db = new DB_Sql_Subclass;'
       */
      return 0;
    
    if (!$this->connect ())
    {
      return 0; /* we already complained in connect() about that. */
    }
    ;
    
    # New query, discard previous result.
    if ($this->Query_ID)
    {
      $this->free ();
    }
    
    if ($this->Debug)
      printf ("Debug: query = %s<br>\n", $Query_String);
    
    $this->Query_ID = @mysql_query ($Query_String, $this->Link_ID);
    $this->Row = 0;
    $this->Errno = mysql_errno ();
    $this->Error = mysql_error ();
    if (!$this->Query_ID)
    {
      $this->halt ("Invalid SQL: " . $Query_String);
    }
    
    # Will return nada if it fails. That's fine.
    return $this->Query_ID;
  }

  /* public: walk result set */
  function next_record ()
  {
    if (!$this->Query_ID)
    {
      $this->halt ("next_record called with no query pending.");
      return 0;
    }
    
    $this->Record = @mysql_fetch_array ($this->Query_ID);
    $this->Row += 1;
    $this->Errno = mysql_errno ();
    $this->Error = mysql_error ();
    
    $stat = is_array ($this->Record);
    if (!$stat && $this->Auto_Free)
    {
      $this->free ();
    }
    return $stat;
  }

  /* public: position in result set */
  function seek ($pos = 0)
  {
    $status = @mysql_data_seek ($this->Query_ID, $pos);
    if ($status)
      $this->Row = $pos;
    else
    {
      $this->halt ("seek($pos) failed: result has " . $this->num_rows () . " rows");
      
      /* half assed attempt to save the day, 
       * but do not consider this documented or even
       * desireable behaviour.
       */
      @mysql_data_seek ($this->Query_ID, $this->num_rows ());
      $this->Row = $this->num_rows;
      return 0;
    }
    
    return 1;
  }

  /* public: table locking */
  function lock ($table, $mode = "write")
  {
    $this->connect ();
    
    $query = "lock tables ";
    if (is_array ($table))
    {
      while (list ($key, $value) = each ($table))
      {
        if ($key == "read" && $key != 0)
        {
          $query .= "$value read, ";
        }
        else
        {
          $query .= "$value $mode, ";
        }
      }
      $query = substr ($query, 0, -2);
    }
    else
    {
      $query .= "$table $mode";
    }
    $res = @mysql_query ($query, $this->Link_ID);
    if (!$res)
    {
      $this->halt ("lock($table, $mode) failed.");
      return 0;
    }
    return $res;
  }

  function unlock ()
  {
    $this->connect ();
    
    $res = @mysql_query ("unlock tables", $this->Link_ID);
    if (!$res)
    {
      $this->halt ("unlock() failed.");
      return 0;
    }
    return $res;
  }

  /* public: evaluate the result (size, width) */
  function affected_rows ()
  {
    return @mysql_affected_rows ($this->Link_ID);
  }

  function num_rows ()
  {
    return @mysql_num_rows ($this->Query_ID);
  }

  function num_fields ()
  {
    return @mysql_num_fields ($this->Query_ID);
  }

  /* public: shorthand notation */
  function nf ()
  {
    return $this->num_rows ();
  }

  function np ()
  {
    print $this->num_rows ();
  }

  function f ($Name)
  {
    return $this->Record [$Name];
  }

  function p ($Name)
  {
    print $this->Record [$Name];
  }

  /* public: sequence numbers */
  function nextid ($seq_name)
  {
    $this->connect ();
    
    if ($this->lock ($this->Seq_Table))
    {
      /* get sequence number (locked) and increment */
      $q = sprintf ("select nextid from %s where seq_name = '%s'", $this->Seq_Table, $seq_name);
      $id = @mysql_query ($q, $this->Link_ID);
      $res = @mysql_fetch_array ($id);
      
      /* No current value, make one */
      if (!is_array ($res))
      {
        $currentid = 0;
        $q = sprintf ("insert into %s values('%s', %s)", $this->Seq_Table, $seq_name, $currentid);
        $id = @mysql_query ($q, $this->Link_ID);
      }
      else
      {
        $currentid = $res ["nextid"];
      }
      $nextid = $currentid + 1;
      $q = sprintf ("update %s set nextid = '%s' where seq_name = '%s'", $this->Seq_Table, $nextid, $seq_name);
      $id = @mysql_query ($q, $this->Link_ID);
      $this->unlock ();
    }
    else
    {
      $this->halt ("cannot lock " . $this->Seq_Table . " - has it been created?");
      return 0;
    }
    return $nextid;
  }

  /* private: error handling */
  function halt ($msg)
  {
    $this->Error = @mysql_error ($this->Link_ID);
    $this->Errno = @mysql_errno ($this->Link_ID);
    if ($this->Halt_On_Error == "no")
      return;
    
    $this->haltmsg ($msg);
    
    if ($this->Halt_On_Error != "report")
      die ("Session halted.");
  }

  function haltmsg ($msg)
  {
    printf ("</td></tr></table><b>Database error:</b> %s<br>\n", $msg);
    printf ("<b>MySQL Error</b>: %s (%s)<br>\n", $this->Errno, $this->Error);
  }

  function table_names ()
  {
    $this->query ("SHOW TABLES");
    $i = 0;
    while (($info = mysql_fetch_row ($this->Query_ID)))
    {
      $return [$i] ["table_name"] = $info [0];
      $return [$i] ["tablespace_name"] = $this->Database;
      $return [$i] ["database"] = $this->Database;
      $i++;
    }
    return $return;
  }
}

?>
