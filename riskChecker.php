
<?php
include('calculateGADate.php');
//Purpose: Check if a project is at risk of missing its SQA release date
 
//calculateGADate methods: getCurrentDate(), writeToLog()

//MAIN-------------------
$todaysDate = getCurrentDate();

//get a list of all projects with testing, rejected, or on-hold status' in the projects table via a query
$sql=("SELECT * FROM ProjectDetails WHERE status='Approved' OR status='Rejected' OR status='On-Hold'");

//loop through each project to get the sqa release date

//Run each projects sqa release date through the switch case
//Switch case that updates project health value based on whatever function returns true
switch (calculateRiskFactor($todaysDate, $sqaRel)){
    case "ontrack":
        //update the project's projectHealth value
        break;
    case "warning":
        //update the project's projectHealth value
        break;
    case "alert":
        //update the project's projectHealth value
        break;
    default:
	//if no other cases trigger
}

//FUNCTIONS------------------------
function calculateRiskFactor($today,$sqaRelDate){

	//Calculate number of days between sqarelease date and today

	//if number is great than 5 than return "ontrack"
	//if the number is greater than 3 and less than 6 than return "warning"
	//if the number less than or equal to 3, then return "alert"
  
	return result;
}
?>
