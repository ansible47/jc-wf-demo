<!DOCTYPE  html>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		
		<link rel="stylesheet" type="text/css" href="style/main.css" media="screen">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
		<script type="text/javascript" src="js/main.js" ></script>
		
	</head>
	<body>
		<?php	
			include 'functions.php';
		?>
				
		<row>
			<h3>Information</h3>
			<h4>Here are our tables:</h4>
			<column_third>
			Every person that has visited a place, and how many times they've traveled total. 
			<br/>
			<?php
				//Start DB connection. 
				//Grabs every person who has visited a place, includes count.
				$con=connectDB();
				$result = mysqli_query($con,"SELECT people.personName, COUNT(people.personName) FROM people, trips WHERE people.personID=trips.personID GROUP BY people.personName");
				if($result===false){failure("Bad query!");}
				else{toHtmlTable($result, "People|Trips Made");}
			?>
			</column_third>
			<column_third>
			Every place that has ever been visited, and how many times total.
			<?php
				//grabs every place that has been visited, includes count
				$result = mysqli_query($con,"SELECT cities.cityName, COUNT(cities.cityName) FROM cities INNER JOIN trips ON cities.cityID=trips.cityID GROUP BY cities.cityName");
				if($result===false){failure("Bad query!");}
				else{toHtmlTable($result, "Place|Visited Count");}			

			?>
			</column_third>
			<column_third>
			<div id="travelHistory"> What places has Jon been to? (default)
			<?php
				//On pageload, grabs the locations that personID=1 has been to with count.
				//This is the default.
				//Closes DB connection, since it's the display default.
				$query = "SELECT cities.cityName, COUNT(trips.cityID) AS num_trips FROM trips INNER JOIN cities ON cities.cityID=trips.cityID INNER JOIN people ON people.personID=trips.personID WHERE people.personID='"; 
				$query .= '1' . "' GROUP BY trips.cityID";
				$result = mysqli_query($con, $query);
				if($result===false){error("Bad query!", $con);}
				else{toHtmlTable($result, "Where have they been?|How Many Times?");}
			?>
			</div>
		</column_third>
		</row>
		<p>Menu:</p>
		<div class="menuLink" id="menu_openEdit">Edit tables</div>
		<div class="menuSpace">-</div> 
		<div class="menuLink" id="menu_openTripEdit">Make a person go on trip</div>
		<div class="menuSpace">-</div> 
		<div class="menuLink" id="menu_openData">Switch Data Display</div>
		<div id="menuSelect">
			<row>
				<h3>Here are our two object tables.</h3>
					<column_quarter>
						Cities:
						<br/>
							<?php
							//Grabs a list of cities.
							//Formats the result as an html select field.
								$result = mysqli_query($con,"SELECT * FROM cities");
								toSelect("cities", "cityID", "cityName", $result);
								
							?>
					</column_quarter>	
					<column_quarter>
						People:
						<br/>
							<?php	
								//Grabs a list of cities.
								//Formats the result as an html select field.
			  					$result = mysqli_query($con,"SELECT * FROM people");
								toSelect("people", "personID", "personName", $result);
							?>
					</column_quarter>
	
			</row>
		</div>
		<div id="menuAddRem">
			<row>
				<h3>Things you can do to the tables...</h3>
				<p>It's good practice for me, so I made it so that you could edit the table from this page. No sql injections, plz. </p>
				<column_third>
					Add a person...
					<br/>
					<input type="text" id="personAdd" placeholder="[Name to add]">
			 		<input type="submit" value="Add" onclick="changeTable('people', '#personAdd', 'add')">
			 	</column_third>
			 	<column_third>
					Add a city...
					<br/>
					<input type="text" id="cityAdd" placeholder="[City to add]">
			 		<input type="submit" value="Add" onclick="changeTable('cities', '#cityAdd', 'add')">
			 		<br/>
			 	</column_third>	 
			</row>
			 		
		 	<row ><div style="position:absolute;">To remove a person/city, select it above and click remove...</div></row>
		 	<row>
		 	<column_third>
		 		<br/>
		 		<input type="text" id="personRem" placeholder="[Name to remove]" readonly>
		 		<input type="submit" value="Remove" onclick="changeTable('people', '#personRem', 'rem')">
		 		<br>
			</column_third>
			
			<column_third>	
		 		<br/>
		 		<input type="text" id="cityRem" placeholder="[Place to remove]" readonly>
		 		<input type="submit" value="Remove" onclick="changeTable('cities', '#cityRem', 'rem')">
		 		<br>
			</column_third>	
			<column_third>
				<div class="tableOpResult"></div>
			</column_third>
			</row>
		</div>
		<div id="menuTravel">
			<row>
				<column_large>
				<h3>Trips</h3>
				<p>Want to make a person visit a city? Select them both below, and then click "Visit"</p>
				<input type="text" id="personVisitTravel" placeholder="[Person visiting...]" readonly> &rarr; 
				<input type="text" id="cityVisitTravel" placeholder="[City to visit...]" readonly>
				<input type="submit" value="Visit" onclick="changeTable('trips', '#people option:selected|#cities option:selected', 'add')">
				</column_large>
				<column_quarter>
					
						<div class="tableOpResult"></div>
			
				</column_quarter>
			

			</row>
			
		</div>
		<div id="menuData">
			<row>
				<h3>Data Change</h3>
				<p>Want to display different information on the right?<br/>
				Select a person from the left to see their travel history.<br/>
				Select a city from the right to see its travel history.
				</p>
			</row>
			<row>
				
			</row>
		</div>

		
	
		<script>
			//Add jquery onclick event to menu options
			$(document).ready(function() {	
				bindClickToMenu("#menu_openEdit", "#menuSelect", "#menuAddRem", 475, 650, "#menuTravel, #menuData, .tableOpsResult");
				bindClickToMenu("#menu_openTripEdit", "#menuSelect", "#menuTravel", 475, 650, "#menuAddRem, #menuData, .tableOpsResult");
				bindClickToMenu("#menu_openData", "#menuSelect", "#menuData", 475, 650, "#menuAddRem, #menuTravel, .tableOpsResult");
			});
			//every time selection in #people changes, we want to:
			//			1. Update fields that use personID(text()) and personName(val())
			//          2. Update travel history
			$( "#people" )
				//
				.change(function () {
					str = $("#people option:selected" ).text();
					val = $("#people option:selected" ).val();
					$('#personRem,#personVisit,#personVisitTravel').text(val);
					$('#personRem,#personVisit,#personVisitTravel').val(str);
					updateTravelHistory("#travelHistory", val, str);
				});
				
			//every time selection in #city changes, we want to:
			//			1. Update fields that use cityID(text()) and cityName(val())
			//          2. Update travel history
			$( "#cities" )
				.change(function () {
					str = $("#cities option:selected" ).text();
					val = $("#cities option:selected" ).val();
					$('#cityRem,#cityVisit,#cityVisitTravel').text(val);
					$('#cityRem,#cityVisit,#cityVisitTravel').val(str);
					updateTravelHistoryCity("#travelHistory", val, str);
					
				});
		</script>
		
	</body>
</html>