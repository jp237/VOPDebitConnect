<?php

class dbConn{

    /** @var  mysqli */
	var $openConnection;
	var $dbSettings;
	var $instance;
	var $lastError;
	var $lastErrors;
	// LOADING SHOPWARE CONFIG FILE	
	
	
	function __construct()
	{
	    $connection = Shopware()->Container()->get('dbal_connection');
        $this->dbsettings = ['db' => Shopware()->Container()->getParameter('shopware.db')];
        $this->dbsettings['db']['port'] = $this->dbsettings['db']['port'] ?? 3306;

	}
	
	
	
	function dbOpen(){
	
	$this->openConnection = mysqli_connect($this->dbsettings["db"]['host'],$this->dbsettings["db"]['username'],$this->dbsettings["db"]['password'],$this->dbsettings["db"]['dbname'],$this->dbsettings['db']['port']);
	mysqli_set_charset($this->openConnection,"utf8");
        // REMOVING ONLY_FULL_GROUP_BY FOR $this->openConnection
        $sqlMode = $this->singleResult("SELECT @@sql_mode as mode ");
        if(strpos($sqlMode['mode'],"ONLY_FULL_GROUP_BY,") !==false){
            $newMode = str_replace("ONLY_FULL_GROUP_BY,","",$sqlMode['mode']);
            $this->dbQuery("SET SESSION sql_mode = '".$newMode."'");
        }else if(strpos($sqlMode['mode'],",ONLY_FULL_GROUP_BY") !==false){
            $newMode = str_replace(",ONLY_FULL_GROUP_BY","",$sqlMode['mode']);
            $this->dbQuery("SET SESSION sql_mode = '".$newMode."'");
        }else if(strpos($sqlMode['mode'],"ONLY_FULL_GROUP_BY") !==false){
            $newMode = str_replace("ONLY_FULL_GROUP_BY","",$sqlMode['mode']);
            $this->dbQuery("SET SESSION sql_mode = '".$newMode."'");
        }

	}
	function dbClose(){
		
		mysqli_close($this->openConnection);
	}
	
	function dbTransActionList($queryList,$echo = false)
	{
		return false;
	}
	
	function dbInsertList($tbl,$arr)
	{
			$this->openConnection->begin_transaction();
			
			foreach($arr as $row) 
			{	
				if(!$this->dbInsert($tbl,$row,false))
				{
					$this->openConnection->rollback();
					return false;
				}
			}
			$this->openConnection->commit();
			return true;
	}
	function dbInsert($tbl,$object,$echo = false,$queryoutput = false)
	{
		foreach($object as $key => $value)
		{
			$keys[] = $key;
			if(isset($value))
			{
				$values[] = gettype($value)=="string" ? "'".$this->dbEscape($value)."'" : (int) $value;
			}
			else
			{
				$values[] = "null";
			}
		}
	
        $stmt = "INSERT INTO " . $tbl . " (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ")";
		if($queryoutput) return $stmt;
		mysqli_query($this->openConnection,$stmt);
		if(mysqli_error($this->openConnection)){
			 $this->dbError(mysqli_error($this->openConnection),$stmt,$echo);
			 return false;
		}
		if ($echo) {
            echo $stmt;
        }
	return mysqli_affected_rows($this->openConnection) > 0 ? true : false;
	}
	function dbQuery($sql,$echo = false) {

		if($echo) echo $sql;
		mysqli_query($this->openConnection,$sql);
		if(mysqli_error($this->openConnection)){
			//echo $sql. " > ".mysqli_error($this->openConnection); 
			$this->dbError(mysqli_error($this->openConnection),$sql,$echo);
			return false;
			}
		return true;
		
	}
	function tableExists($tblname,$prefix = "dc_")
	{
		if($prefix != "dc_"){
		    $tblname = $prefix.$tblname;
        }
		$allTables = $this->getSQLResults("show tables LIKE '".$prefix."%'");

		foreach($allTables as $row)
		{
			foreach($row as $key => $val)
			{
				if(strtoupper($val) == strtoupper($tblname))
				{
					return true;
				}
			}
		}
		return false;
		
	}
	function dbError($errortxt,$query , $output = false)
	{
	    $this->lastError = $errortxt;
	    $this->lastErrors[] = $errortxt;

		if(DC()->getConf("output_query_failure",0,true) == 1) $errortxt.="<br>".$query;
		DC()->View("SQL_ERROR",$this->dbEscape($errortxt));
	}
	function dbUpdate($tbl,$object,$where,$echo = false,$queryoutput = false)
	{

		$values= array();
			foreach($object as $key => $value)
		{
			if(isset($value))
			{
			$values[] = gettype($value)=="string" ? $key." = '".$this->dbEscape($value)."'" : $key ." = ".(int) $value;
			}
			else
			$values[] =  $key." = null";
			
			
		}
		$stmt = 'UPDATE ' . $tbl . ' SET ' . implode(',', $values) . " where ".$where;
		if($queryoutput) return $stmt;
        if ($echo) {
            echo $stmt;
        }
		mysqli_query($this->openConnection,$stmt);
		if(mysqli_error($this->openConnection)) $this->dbError(mysqli_error($this->openConnection),$stmt,$echo);
		return mysqli_affected_rows($this->openConnection) > 0 ? true : false;
	}
	function dbEscape($value)
	{
		global $connect;
		return mysqli_real_escape_string($this->openConnection,$value);
	}
	function getSQLResults($query,$echo = false)
		{
			if($echo) echo $query;
		$arr = array();
			if ($result = mysqli_query($this->openConnection,$query)) {
				while ($row = $result->fetch_assoc()) {
					$arr[] = $row;
				}
				$result->free();
			}
			if(mysqli_error($this->openConnection)) $this->dbError(mysqli_error($this->openConnection),$query,$echo);
			return $arr;
		}
		
		function singleResult($sql,$echo = false) {
			if($echo) echo $sql;
		$returnwert = False;
		$result = mysqli_query($this->openConnection,$sql);
		if(mysqli_error($this->openConnection)) $this->dbError(mysqli_error($this->openConnection),$sql,$echo);
		 return mysqli_fetch_assoc($result);
		
		}
	
}
?>