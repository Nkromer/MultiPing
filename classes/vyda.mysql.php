<?php

class VYDA_MySQL extends VYDA_Generic_Class {
    
    /* Class Variables */
    private $db_conn;
    private $is_connected = false;
    
    /* 
     * Constructor  - Creates DB Connection 
     */
    function __construct($mysql_host, $mysql_db_name, $mysql_user, $mysql_password, $mysql_port=3306){
        
        //Set proper type_name
        $this->type_name = __CLASS__;
        
        //Start the DB Connection
        $this->db_conn = new mysqli($mysql_host, $mysql_user, $mysql_password, $mysql_db_name, $mysql_port);
        
        //Check the connection is OK. Otherwise FAIL.
        if(mysqli_connect_errno()){
            
            //Houston, we've encountered a problem
            $this->last_error = mysqli_connect_error();
            
            //We ain't going nowhere OK!
            return false;
            
        }
        
        //Everything is OK. Keep calm and carry on.
        $this->is_connected = true;
        
        //Return TRUE, sedate the callstack they'll stay calm too!
        return true;
        
    }
    
    /*
     * Destructor - Closes MySQLi DB Link
     *    [ note: theoertically this is always destroyed on automatic script closure ]
     */
    function __destruct() {
        
        //auto log
        $this->AutoLog();
        
        //Close active
        $this->db_conn->close();
        
    }
    
    /*
     * MySQL Escape Function
     *  [ purpose: Sanitize variables before entry into DB ]
     */
    public function Escape($dirty_var){
        
        //Ensure connection
        if(!$this->is_connected){
            
            $this->last_error = "No DB Connection : " . __FUNCTION__;
            return false;
            
        }
        
        //Escape with real_escape_string
        return $this->db_conn->real_escape_string($dirty_var);
        
    }
    
    /*
     * EscapeArray
     *    [ purpose: Escape entire array with seperate real_escape's ]
     */
    public function EscapeArray($value_array){
        
        //Ensure connection
        if(!$this->is_connected){
            
            $this->last_error = "No DB Connection : " . __FUNCTION__;
            return false;
            
        }
        
        
        //define final return
        $final_array = array();
        
        //loop through each provided, sanitize and put into final_array
        foreach($value_array as $col=>$val){
            
            //Sanitize the column and associated value
            $sanitary_col = $this->db_conn->real_escape_string($col);
            $sanitary_val = $this->db_conn->real_escape_string($val);
            
            //drop to final
            $final_array[$sanitary_col] = $sanitary_val;
            
        }
        
        //return the final
        return $final_array;
        
    }
    
    /*
     * ExecuteQuery
     *   [ purpose: RAW Query Execution into MySQL ] 
     *      [ all formats must be properly formed SQL Statements ]
     */
    public function ExecuteQuery($sql_statement){
        
        //Ensure connection
        if(!$this->is_connected){
            
            $this->last_error = "No DB Connection : " . __FUNCTION__;
            return false;
            
        }
        
        
        /* Execute Query & Return Result */
        $query_result = $this->db_conn->query($sql_statement);
        
        if(!$query_result){
            
            $this->last_error = $this->db_conn->error;
            
        }
        
        return $query_result;
        
    }
    
    /*
     * RowReturn
     *    [ goal: Return single-level array result of the MySQL statement ]
     */    
    public function RowReturn($sql_statement){
        
        //Ensure connection
        if(!$this->is_connected){
            
            $this->last_error = "No DB Connection : " . __FUNCTION__;
            return false;
            
        }
                
        /* Query the result against the datatabase */
        $query_result = $this->ExecuteQuery($sql_statement);
        
        if(!$query_result){
            
            //Ensure connection
            $this->last_error = "Could not get RowReturned : \n" . $sql_statement;
            return false;
            
        }
        
        /* Fetch associative array and return */
        return $query_result->fetch_array( MYSQLI_ASSOC );
        
    }
    
    /*
     * FullAssocReturn
     *    [ purpose: Return full associative table result array, no iterting needed]
     */
    public function FullAssocReturn($sql_statement){
        
        //Ensure connection
        if(!$this->is_connected){
            
            $this->last_error = "No DB Connection : " . __FUNCTION__;            
            return false;
            
        }
                
        /* Query the result against the datatabase */
        $query_result = $this->ExecuteQuery($sql_statement);
        
        /* ERROR? */
        if(!$query_result){
            
            $this->autolog_source = __CLASS__ . " -> " . __FUNCTION__ . " -> line #" . __LINE__;
            $this->last_error = "MySQL Result Failed:\n\n" . $sql_statement;
            return false;
            
        }
                
        /* Define Iterated Array */
        $iterated_array = array();
        
        /* Iterate into temporary array */
        while($row = $query_result->fetch_array( MYSQLI_ASSOC )){
            
            // Set row value in the iterated array
            $iterated_array[] = $row;
            
        }

        
        /* Return to user */
        return $iterated_array;
        
    }
    
    /* InsertID */
    public function InsertID(){
        
        //Ensure connection
        if(!$this->is_connected){
            
            $this->last_error = "No DB Connection : " . __FUNCTION__;            
            return false;
            
        }
                
        /* Set the InsertID */
        $insert_id = $this->db_conn->insert_id;
        
        /* Check if there is an error */
        if(!$insert_id){
                        
            /* Set the last_error to MySQL Conn Last Error */
            $this->last_error = $this->db_conn->error;
            
            /* Houston, we've encountered an issue! Alert the callstack */
            return false;
            
        }
        
        /* Assume OK and Return ID */
        return $insert_id;
        
    }
    
    /*
     * CreateInsertQuery
     *    [ purpose: Simplify the creation of insertion arrays for MySQL ]
     */
    public function CreateInsertQuery($mysql_table_name, $iterative_insertion_array, $perform_escape=false){
        
        /* Count the # of Objs. in Array */
        $object_count = count($iterative_insertion_array);
        
        /* Is this array empty!? */
        if($object_count == 0){
            
            //Set the last error
            $this->last_error = "Provided insertion array rendered a 'NULL/ZERO' Object Count.";
            
            //This aint' good son.
            return false;
            
        }

        
        /* SETs: Index Counter, Column String, Value String */
        $cols_string = "";
        $vals_string = "";
        
        /* ITERATE The Array */
        foreach($iterative_insertion_array as $key => $value){
            
            $cols_string .= "`{$key}`, ";
            $vals_string .= ($perform_escape ? "'".$this->Escape($value)."', " : "'{$value}', ");
        
        }
        
        //Truncate these bitches
        $cols_string = substr($cols_string, 0, strlen($cols_string) - 2);
        $vals_string = substr($vals_string, 0, strlen($vals_string) - 2);
        
        //Create the final query
		$final_query = "INSERT INTO {$mysql_table_name} ($cols_string) VALUES ({$vals_string})";
        
        //Return final generated query to user
        return $final_query;
        
    }
    
    /*
     * CreateUpateQuery
     *    [ purpose: Simplify the creation of a mass SQL Update Query ]
     */
    public function CreateUpdateQuery($table, $conditions, $update_array){
        
        /* Count Array */
        $array_count = count($update_array);
        
        /* Check array */
        if($array_count == 0){
            
            $this->last_error = "Provided UPDATE array rendered a 'NULL/ZERO' Object count";
            
            return false;
            
        }
        
        /* Define Vars */
        $i = 1;
        $sets_string = "";
        
        /* ITERATE Through Entire Array */
        foreach($update_array as $key=>$value){
            
            if($i == $array_count){

                $sets_string .= "`{$key}`='{$value}'";

            } else {

                $sets_string .= "`{$key}`='{$value}', ";

            }

            $i++;
                
        }
	
        //Generate Final Update Query
	$final_query = "UPDATE {$table} SET {$sets_string} WHERE {$conditions};";
        
        //Ernie, send the scooter!
        return $final_query;
        
    }
    
    /*
     * listFullTables
     *   -- Shows a list of tables (right Column, BASE TABLE) in the selected DB
     *   -- Derives result from "SHOW FULL TABLES"
     */
    public function listFullTables(){
        
        $full_table_list = $this->FullAssocReturn("SHOW FULL TABLES");
        
        $returnable = array();
        
        foreach($full_table_list as $row_id=>$row_info){
            
            $ret_workingrow = "undefined";
            
            $id=0;
            foreach($row_info as $sqlassgcolname=>$value){
                
                switch($id){
                    
                    case 0:
                        
                        $ret_workingrow = $value;
                        
                        break;
                    
                    case 1:
                        
                        $returnable[$ret_workingrow] = $value;
                        
                        break;
                    
                }
                
                ++$id;
            }
            
        }
        
        return $returnable;
        
    }
    
    /*
     * listColumnsInTable(String $table_name)
     *   -- Show a list of columns in a given table
     */
    public function listColumnsInTable($table_name){
        
        $table_column_list = $this->FullAssocReturn("SHOW COLUMNS FROM `{$table_name}`");
        
        $final_return = array();
        
        foreach($table_column_list as $row_id=>$column_info){
           
            $working_field_name = $column_info['Field'];
            
            unset($column_info['Field']);
            
            $final_return[$working_field_name] = $column_info;
            
        }
        
        return $final_return;
        
    }
    
}

