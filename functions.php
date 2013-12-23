<?php

//General purpose PHP functions

//Function triggers failure text. accepts 
function failure($cause){
		echo "<error>Failure!<br/>" . $cause. "</error>";		
}


//connects to database
//returns connection object
function connectDB(){
  $con = mysqli_connect("tunnel.pagodabox.com:3306","kasi","JsHd0D2g","jc-demo-wf-db");

  if (mysqli_connect_errno($con))
  {
  echo "<error>Failed to connect to MySQL: " . mysqli_connect_error() . "</error>";
  }
	return $con;
}

//ONLY for $_POST variable names!
//Checks to see if variable is set. If so, it returns the actual data from the $_POST
function checkPost($var){
	if(isset($_POST[$var]) && !empty($_POST[$var])) {
   		$op = $_POST[$var];
		return $op;
    }
	else{
		failure($var . " is not defined!");
	}
}


//Turns inputs into an html select object
//ONLY works for ID - STRING two element data format.
//Inputs:
//			selectID: ID of the select being made
//			$id: key we need to access variable (cityID or personID)
//			$display: key we need to access name (cityName or personName)
//			$res: result of SQL query.
function toSelect($selectID, $id, $display, $res){
	
	echo "<select id='" . $selectID . "' size='6' multiple='no'>";
	
	while($row = mysqli_fetch_array($res)){
		echo "<option value='" . $row[$id]  . "'>";
		echo $row[$display] . "</option>";
		
	}
	echo "</select>";
}

//Turns inputs into a beautifully formatted html table.
//Inputs:
//			$res: result of sql query
//          $columns: header name of each column. Will make a table the length of columns. 
function toHtmlTable($res, $columns){
		
		$temp = explode( "|", $columns);
		$out = '<table border="1">';
		foreach ($temp as $str) {
			$out .= "<th>".$str."</th>";
		}
		$count=0;
		while ($row = $res->fetch_assoc()) {
		if ($count%2!=0 && $count != 0){$out .= "<tr class='odd'>";}
		else{$out .= "<tr>";}
		foreach ($row as $col) $out .= '<td>'.$col.'</td>';
		$out .= "</tr>";
		$count+=1;
	}
	$out .= "</table>";
	echo $out;
}



?>