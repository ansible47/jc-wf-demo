<?php
	
	include 'functions.php';
	
	//start connection
	$con=connectDB();
	
	
	//we always need the operation, so check that first
	$op = checkPost('op');

	//I escape a few things we get from javascript, since anyone can change javascript. It's better than nothing, as far as security.
	$op = mysqli_real_escape_string($con, $op);	

	
	$msg = "";

	//handles different $ops
	switch ($op){
		case "add":
			$table = checkPost('table');
			$data = checkPost('data');
			$table = mysqli_real_escape_string($con, $table);
			$data = mysqli_real_escape_string($con, $data);
			//data=name of person to add
			if($table == "people"){
				$result = mysqli_query($con,"INSERT INTO " . $table . " (personName)  VALUES ('" . $data . "')");
				$msg = $data . " added to people.";
			}
			//data=name of city to add
			else if ($table =="cities"){
				$result = mysqli_query($con,"INSERT INTO " . $table . " (cityName)  VALUES ('" . $data . "')");
				$msg = $data . " added to cities.";
			}
			//data=(id of person,id of city)
			else if ($table =="trips"){
				$temp = explode(",", $data);
				$out = "'" . $temp[0] . "','" . $temp[1] . "'" ;
				$result = mysqli_query($con,"INSERT INTO " . $table . " (personID,cityID)  VALUES (" . $out . ")");
				$msg = "Added trip to database.";
			}

			if ( false===$result ) {
		  	 failure(mysqli_error($con) . "<br/><br/> table:" . $table . "<br/> data:" . $data . "<br/> op:" . $op);
			}
			else{	
			 	echo "Success!<br/>" . $msg . "<br/><a href='/jc_demo'>Refresh</a> to see changes.";
			}
			break;
			
		case "rem":
			$table = checkPost('table');
			$data = checkPost('data');
			//data=personID of person to remove
			if ($table == "people"){
				$result = 	mysqli_query($con,"DELETE FROM " . $table  . " WHERE personID='" . $data . "'");
				$msg = "Person removed successfully (if he even existed).";
			}
			//date=cityID of city to be removed
			else if ($table =="cities"){
				$result = 	mysqli_query($con,"DELETE FROM " . $table  . " WHERE cityID='" . $data . "'");
				$msg = "City removed from successfully (if it even existed).";
			
			}
			if ( false===$result ) {
		  	 failure(mysqli_error($con) . "<br/><br/> table:" . $table . "<br/> data:" . $data . "<br/> op:" . $op);
				
				
			}
			else{	
			 	echo "Success!<br/>" . $msg . "<br/><a href='/jc_demo'>Refresh</a> to see changes.";
				
			}
			
			break;
		case "queryPerson":
			//data=id of person we want information about
			$data = checkPost('data');
			$query = "SELECT cities.cityName, COUNT(trips.cityID) AS num_trips FROM trips INNER JOIN cities ON cities.cityID=trips.cityID INNER JOIN people ON people.personID=trips.personID WHERE people.personID='"; 
			$query .= $data . "' GROUP BY trips.cityID";
			$result = mysqli_query($con, $query);
			return toHtmlTable($result, "Where have they been?|How Many Times?");
			break;
		case "queryCity":
			//data=id of city we want information about
			$data = checkPost('data');
			$query = "SELECT people.personName, COUNT(trips.cityID) AS num_trips FROM trips INNER JOIN cities ON cities.cityID=trips.cityID INNER JOIN people ON people.personID=trips.personID WHERE cities.cityID='"; 
			$query .= $data . "' GROUP BY trips.personID";
			$result = mysqli_query($con, $query);
			return toHtmlTable($result, "Where has been?|How Many Times?");
			break;
		default:
			failure("Unrecognize command!");
		
			
	}
	

	mysqli_close($con);


?>