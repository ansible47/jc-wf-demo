//Opens alert on failure condition.
//Inputs:
//		 cause: string with error info
function failure(cause){
	alert("Error!\n" + cause);	
}


//Handles ajax calls to update tables.
//Inputs:
//		 table: which table we want to operate on
//		 dataID: element ID with the value we want to grab
//		 op: operation we want to do (add or rem)
function changeTable(table, dataID, op){
	temp = dataID.split("|"); //
	data = "";
	if (temp.length>1){
		//We know that there's more than one field we want to grab.
		//Iterate through temp and add the values to data
		for (x=0; x<temp.length; x++){
			tempVal = $(temp[x]).val();
			
			if (tempVal != null | tempVal != ""){
				//Seperates values by a comma, as they will be in mySQL
				if (x < temp.length-1){
					data += tempVal  + ","
				}
				else{
					data +=  tempVal; 	
				}
			}
			else{
				failure ("Bad ajax call. No value in id=" + temp[x]);	
				
			}
		} 

			
        $.ajax({
            url: "tableOps.php",
            type: 'POST',
            data: {data:data,op:op,table:table},
            success: function (data) {
                $('.tableOpResult').html(data);
            }
        });

	}
	else{
		
		data = $(dataID)
		dataOut = ""
		
		if (data.val() != null | data.val() != ""){
			if (op =="rem"){
				//If we're removing something, we want to do it by ID, which
				//is contained in the html() of the element.
				//Done because the input element prioritizes the display of value over html, 
				//and the user never needs to see the ID.
				dataOut = data.html()
			}
			else{
				//if we're adding something and we only recieve one dataID field, 
				//we only need the name to add to city/persons, which will be in the VALUE attr of the input
				dataOut = data.val()
			}
			$.ajax({
	            url: "tableOps.php",
	            type: 'POST',
	            data: {data:dataOut,op:op,table:table},
	            success: function (data) {
	                $('.tableOpResult').html(data);
	            }
	        });

		
		}
		else{
			failure ("Bad ajax call. No value in id=" + data.id)	

			
			
		}
	}

}

//Updates travel history table with info from DB for PEOPLE
//Inputs:
//		 divToUpdate: id of div we want to update
//		 personID: personID we want info about
//		 personName: person's actual name
function updateTravelHistory(divToUpdate, personID, personName){
		op = "queryPerson";
		$(divToUpdate).empty();
		$(divToUpdate).html("What places has " + personName + " been to?");
		$.ajax({
	        url: "tableOps.php",
	        type: 'POST',
	        data: {data:personID,op:op},
	        success: function (data) {
	                $(divToUpdate).html($(divToUpdate).html() + data);
	            }
	        });

}

//Updates travel history table with info from DB for CITIES
//Inputs:
//		 divToUpdate: id of div we want to update
//		 cityID: cityID we want info about
//		 cityName: city's actual name
function updateTravelHistoryCity(divToUpdate, cityID, cityName){
		op = "queryCity";
		$(divToUpdate).empty();
		$(divToUpdate).html("Who has been to " + cityName + " and how many times?");
		$.ajax({
	        url: "tableOps.php",
	        type: 'POST',
	        data: {data:cityID,op:op},
	        success: function (data) {
	                $(divToUpdate).html($(divToUpdate).html() + data);
	            }
	        });
}

//Adds onclick even to select div and does the styling necessary for interactive menu.
//Inputs:
//		 divToBind: id of div we want to update
//		 divToSelect: personID we want info about
//		 heightOfSelect: height to place Select at
//		 heightOfMenu: height to place Menu at
//		 divToHide: identifier of div(s) we want to hide. Seperate multiple divs by commas.
function bindClickToMenu(divToBind, divOfSelect, divOfMenu, heightOfSelect, heightOfMenu, divToHide){
				$(divToBind).bind( "click", function( event ) {
					var opacity = $(divOfMenu).css("opacity");
					if (opacity== 0){
						//Show menu and select
						$(divOfMenu+","+divOfSelect).show();
						$(divOfMenu+","+divOfSelect).css("opacity", 1);
						$(divOfMenu).css("top",heightOfMenu);
						$(divOfSelect).css("top",heightOfSelect);
						$(divToHide).hide();
					}
					else{
						//hide div
						$(divOfMenu+","+divOfSelect).css("opacity", 0);
						$(divOfMenu+","+divOfSelect).hide();
					
						$(divToHide).hide();
					}
				});
}