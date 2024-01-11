<?php	 		 		 	
class db_driver {

    var $obj = array ( "sql_database"   => ""         ,
                       "sql_user"       => ""     ,
                       "sql_pass"       => ""         ,
                       "sql_host"       => "",
                       "sql_port"       => 3306         ,
                       "sql_socket"       => NULL         ,
                       "persistent"     => "0"         ,
                       "sql_tbl_prefix"        => "mkp_"      ,
                       "cached_queries" => array(),
                       'debug'          => 0,
                     );

     var $query_id      = "";
     var $connection_id = "";
     var $query_count   = 0;
     var $record_row    = array();
     var $return_die    = 0;
     var $error         = "";
     var $failed        = 0;

    /*========================================================================*/
    // Connect to the database                 
    /*========================================================================*/  
                   
    function connect() {
		define('SQL_PREFIX', '');
		$this->connection_id = mysqli_connect( $this->obj['sql_host'] ,
											  $this->obj['sql_user'] ,
											  $this->obj['sql_pass'] ,
											  NULL ,
											  $this->obj['sql_port'] ,
											  $this->obj['sql_socket'] 
											);
		
        if (!$this->connection_id)
        {    
             $this->fatal_error('ERROR: Link-ID == false, connect failed');
             return false;
        }  
   
        if ( !mysqli_select_db($this->connection_id, $this->obj['sql_database']) )
        {
             $this->fatal_error("ERROR: Cannot find database ".$this->obj['sql_database']);
             return false;            
        }
    }

    /*========================================================================*/
    // Process a query
    /*========================================================================*/
    
    function query($the_query, $bypass=0) {
    	
		//-- mod_mysql5 begin
        if (!preg_match("`^create\s`ims", $the_query) && !preg_match("`^select\s`ims", $the_query))
            $the_query = preg_replace("`([^\\\])''`", "\\1NULL", $the_query);
		//-- mod_mysql5 end

        if ($this->obj['debug'])
        {
    		global $Debug, $mklib;
    		
    		$Debug->startTimer();
    	}
    	
        $this->query_id = mysqli_query($this->connection_id, $the_query);
      
        if (!$this->query_id )
        {	
            $this->fatal_error("mySQL query error: $the_query");
        }
        
        if ($this->obj['debug'])
        {
        	$endtime = $Debug->endTimer();
        	
        	if ( preg_match( "/^select/i", $the_query ) )
        	{
        		$eid = mysqli_query($this->connection_id, "EXPLAIN $the_query");
        		$mklib->debug_html .= "<table width='95%' border='1' cellpadding='6' cellspacing='0' bgcolor='#FFE8F3' align='center'>
										   <tr>
										   	 <td colspan='8' style='font-size:14px' bgcolor='#FFC5Cb'><b>Select Query</b></td>
										   </tr>
										   <tr>
										    <td colspan='8' style='font-family:courier, monaco, arial;font-size:14px;color:black'>$the_query</td>
										   </tr>
										   <tr bgcolor='#FFC5Cb'>
											 <td><b>table</b></td><td><b>type</b></td><td><b>possible_keys</b></td>
											 <td><b>key</b></td><td><b>key_len</b></td><td><b>ref</b></td>
											 <td><b>rows</b></td><td><b>Extra</b></td>
										   </tr>\n";
				while( $array = mysqli_fetch_array($eid) )
				{
					$type_col = '#FFFFFF';
					
					if ($array['type'] == 'ref' or $array['type'] == 'eq_ref' or $array['type'] == 'const')
					{
						$type_col = '#D8FFD4';
					}
					else if ($array['type'] == 'ALL')
					{
						$type_col = '#FFEEBA';
					}
					
					$mklib->debug_html .= "<tr bgcolor='#FFFFFF'>
											 <td>$array[table]&nbsp;</td>
											 <td bgcolor='$type_col'>$array[type]&nbsp;</td>
											 <td>$array[possible_keys]&nbsp;</td>
											 <td>$array[key]&nbsp;</td>
											 <td>$array[key_len]&nbsp;</td>
											 <td>$array[ref]&nbsp;</td>
											 <td>$array[rows]&nbsp;</td>
											 <td>$array[Extra]&nbsp;</td>
										   </tr>\n";
				}
				
				if ($endtime > 0.1)
				{
					$endtime = "<span style='color:red'><b>$endtime</b></span>";
				}
				
				$mklib->debug_html .= "<tr>
										  <td colspan='8' bgcolor='#FFD6DC' style='font-size:14px'><b>mySQL time</b>: $endtime</b></td>
										  </tr>
										  </table>\n<br />\n";
			}
			else
			{
			  $mklib->debug_html .= "<table width='95%' border='1' cellpadding='6' cellspacing='0' bgcolor='#FEFEFE'  align='center'>
										 <tr>
										  <td style='font-size:14px' bgcolor='#EFEFEF'><b>Non Select Query</b></td>
										 </tr>
										 <tr>
										  <td style='font-family:courier, monaco, arial;font-size:14px'>$the_query</td>
										 </tr>
										 <tr>
										  <td style='font-size:14px' bgcolor='#EFEFEF'><b>mySQL time</b>: $endtime</span></td>
										 </tr>
										</table><br />\n\n";
			}
		}
		
		$this->query_count++;
        
        $this->obj['cached_queries'][] = $the_query;
        
        return $this->query_id;
    }

    function fetch_row($query_id = "") {

    	if ($query_id == "") {
    		$query_id = $this->query_id;
    	}

        $result = mysqli_fetch_array($query_id, MYSQLI_ASSOC);
        return $result;

    }


    function get_num_rows() {
        return mysqli_num_rows($this->query_id);
    }


	function get_insert_id() {
        return mysqli_insert_id($this->connection_id);
    }


    function free_result($query_id="") {

   		if ($query_id == "") {
    		$query_id = $this->query_id;
		}
    	@mysqli_free_result($query_id);
    }

    function close_db() {
        return mysqli_close($this->connection_id);
    }

    /*========================================================================*/
    // Quick function
    /*========================================================================*/
    
    function do_update( $tbl, $arr, $where="" )
    {
    	$dba = $this->compile_db_update_string( $arr );
    	
    	$query = "UPDATE ".SQL_PREFIX."$tbl SET $dba";
    	
    	if ( $where )
    	{
    		$query .= " WHERE ".$where;
    	}
    	
    	$ci = $this->query( $query );
    	
    	return $ci;
    
    }

    function do_insert( $tbl, $arr )
    {
    	$dba = $this->compile_db_insert_string( $arr );
    	$ci = $this->query("INSERT INTO ".SQL_PREFIX."$tbl ({$dba['FIELD_NAMES']}) VALUES({$dba['FIELD_VALUES']})");
    	
    	return $ci;
    }
    
    /*========================================================================*/
    // Simple elements
    /*========================================================================*/
    
    function simple_construct( $a )
    {
    	if ( $a['select'] )
    	{
    		$this->simple_select( $a['select'], $a['from'], $a['where'] );
    	}
    	
    	if ( $a['update'] )
    	{
    		$this->simple_update( $a['update'], $a['set'], $a['where'], $a['lowpro'] );
    	}
    	
    	if ( $a['delete'] )
    	{
    		$this->simple_delete( $a['delete'], $a['where'] );
    	}
    	
    	if ( $a['order'] )
    	{
    		$this->simple_order( $a['order'] );
    	}
    	
    	if ( is_array( $a['limit'] ) )
    	{
    		$this->simple_limit( $a['limit'][0], $a['limit'][1] );
    	}
    }

    //------------------------------------
    // UPDATE
    //------------------------------------
    
    function simple_update( $tbl, $set, $where, $low_pro )
    {
    	if ( $low_pro )
    	{
    		$low_pro = ' LOW_PRIORITY ';
    	}
    	
    	$this->cur_query .= "UPDATE ". $low_pro . SQL_PREFIX."$tbl SET $set";
    	
    	if ( $where )
    	{
    		$this->cur_query .= " WHERE $where";
    	}
    }
    
    //------------------------------------
    // DELETE
    //------------------------------------
    
    function simple_delete( $tbl, $where )
    {
    	$this->cur_query .= "DELETE FROM ".SQL_PREFIX."$tbl";
    	
    	if ( $where )
    	{
    		$this->cur_query .= " WHERE $where";
    	}
    }
    
    //------------------------------------
    // EXEC QUERY
    //------------------------------------
    
    function simple_exec()
    {
    	if ( $this->cur_query != "" )
    	{
    		$ci = $this->query( $this->cur_query );
    	}
    	
    	$this->cur_query   = "";
    	$this->is_shutdown = 0;
    	return $ci;
    }
    
    //------------------------------------
    // Exec and return simple row
    //------------------------------------
    
    function simple_exec_query( $a )
    {
    	$this->simple_construct( $a );
    	
    	$ci = $this->simple_exec();
    	
    	if ( $a['select'] )
    	{
    		return $this->fetch_row( $ci );
    	}
    }
    
    //------------------------------------
    // ORDER
    //------------------------------------
    
    function simple_order( $a )
    {
    	if ( $a )
    	{
    		$this->cur_query .= ' ORDER BY '.$a;
    	}
    }
    
    //------------------------------------
    // LIMIT
    //------------------------------------
    
    function simple_limit_with_check( $offset, $limit="" )
    {
    	if ( ! preg_match( "#LIMIT\s+?\d+,#i", $this->cur_query ) )
		{
			$this->simple_limit( $offset, $limit );
		}
    }
    
    function simple_limit( $offset, $limit="" )
    {
    	if ( $limit )
    	{
    		$this->cur_query .= ' LIMIT '.$offset.','.$limit;
    	}
    	else
    	{
    		$this->cur_query .= ' LIMIT '.$offset;
    	}
    }
    
    //------------------------------------
    // SELECT
    //------------------------------------
    
    function simple_select( $get, $table, $where="" )
    {
    	$this->cur_query .= "SELECT $get FROM ".SQL_PREFIX."$table";
    	
    	if ( $where != "" )
    	{
    		$this->cur_query .= " WHERE ".$where;
    	}
    }
    
    /*========================================================================*/
    // Create an array from a multidimensional array returning formatted
    // strings ready to use in an INSERT query, saves having to manually format
    // the (INSERT INTO table) ('field', 'field', 'field') VALUES ('val', 'val')
    /*========================================================================*/
    
    function compile_db_insert_string($data) {
    
    	$field_names  = "";
		$field_values = "";
		
		foreach ($data as $k => $v)
		{
			$v = preg_replace( "/'/", "\\'", $v );
			//$v = preg_replace( "/#/", "\\#", $v );
			$field_names  .= "$k,";
			$field_values .= "'$v',";
		}
		
		$field_names  = preg_replace( "/,$/" , "" , $field_names  );
		$field_values = preg_replace( "/,$/" , "" , $field_values );
		
		return array( 'FIELD_NAMES'  => $field_names,
					  'FIELD_VALUES' => $field_values,
					);
	}
	
	/*========================================================================*/
    // Create an array from a multidimensional array returning a formatted
    // string ready to use in an UPDATE query, saves having to manually format
    // the FIELD='val', FIELD='val', FIELD='val'
    /*========================================================================*/
    
    function compile_db_update_string($data) {
		
		$return_string = "";
		
		foreach ($data as $k => $v)
		{
			$v = preg_replace( "/'/", "\\'", $v );
			$return_string .= $k . "='".$v."',";
		}
		
		$return_string = preg_replace( "/,$/" , "" , $return_string );
		
		return $return_string;
	}

   	/*========================================================================*/
    // Return an array of fields
    /*========================================================================*/
    
    function get_result_fields($query_id="") {
    
   		if ($query_id == "")
   		{
    		$query_id = $this->query_id;
    	}
    
		while ($field = mysqli_fetch_field($query_id))
		{
            $Fields[] = $field;
		}
		
		//mysql_free_result($query_id);
		
		return $Fields;
   	}
    /*========================================================================*/
    // Return an array of tables
    /*========================================================================*/
    
    function get_table_names() {
    
		$result     = mysql_list_tables($this->obj['sql_database']);
		$num_tables = @mysql_numrows($result);
		for ($i = 0; $i < $num_tables; $i++)
		{
			$tables[] = mysql_tablename($result, $i);
		}
		
		mysql_free_result($result);
		
		return $tables;
   	}

    /*========================================================================*/
    // Basic error handler
    /*========================================================================*/
    
    function fatal_error($the_error) {
	global $mkportals;
    	
    	// Are we simply returning the error?
    	
    	$this->error    = mysqli_error();
    	$this->error_no = mysqli_errno();
     
    	if ($this->return_die == 1)
    	{
        	 $this->failed   = 1;
	         return;
    	} 
    $message  = "Database error in:\r\n{$the_error}\r\n\r\n";
    $message .= 'mySQL error: ' . $this->error . "\r\n\r\n";
    $message .= 'mySQL error number: ' . $this->error_no . "\r\n\r\n";
    $message .= 'Script URI: '. $_SERVER['REQUEST_URI'] . "\r\n";  
    $message .= 'Date: ' . date('l dS of F Y h:i:s A') . "\r\n";
    $message .= 'IP Address: ' . $_SERVER['REMOTE_ADDR'] . "\r\n";
 
    $mail_headers  = "From: \"".$mkportals->vars['email_tech']."\" <".$mkportals->vars['email_tech'].">\n";
    $mail_headers .= "To: ".$mkportals->vars['email_tech']."\n";  
    $mail_headers .= "Subject: Database error!\n";  
    $mail_headers .= "Content-Type: text/plain; charset=\"utf-8\"\n";
    $mail_headers .= "X-Priority: 3\n";
    $mail_headers .= "X-Mailer: MKP PHP Mailer\n";  
 
    @mail ($INFO['email_tech'], 'Database error!', $message , $mail_headers);

    echo "<html><head><title>System Error</title>";
    echo "</head>\r\n";
    echo "<body>\r\n";
    echo "<b>Возникли некоторые проблемы в работе форума.</b><br />\r\n";
    echo "Попробуйте зайти позже. Либо нажмите кнопку<a href=\"javascript:window.location=window.location;\">обновить</a> в вашем браузере.";
    echo "<p>Приносим извинения за причиненные неудобства.</p>{$the_error}";
    echo "\r\n\r\n</body></html>";
    exit; 
    }

}
?>