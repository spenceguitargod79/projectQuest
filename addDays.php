<?php
include('calculateGADate.php');
//include('verify.php');
include ('siteFuncs.php');

$link=sqlConnect();
$arrayResult=getListOfProjects($link);
//writeToLog(" Number of games that have its latest revision ON-Hold: ".count($arrayResult)." ++++++ ");
//writeToLog(" First element in the array contains PID ".$arrayResult[0]." ||||| ");
updateProjectsSQARelease($link,$arrayResult,"1");
updateProjectsITLRelease($link,$arrayResult,"1");
//query the ProjectDetails table, each pids highest revision's status.
//If the status = "On-Hold", add 1 day to estimated sqa release and 1 day to estimated ITL release.
//Add a history entry to the revision.
function getListOfProjects($link)
{
	$pidsArray = array();
	if($results=mysqli_query($link,"SELECT tt.* FROM ProjectDetails tt INNER JOIN (SELECT ProjectID, MAX(Revision) as maxRev FROM ProjectDetails GROUP BY ProjectID) groupedtt ON tt.ProjectID = groupedtt.ProjectID AND tt.Revision = groupedtt.maxRev AND Status = 'On-Hold' ORDER BY ProjectID ASC;"))
	{
        	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
        	{
                	//Do stuff
                	//writeToLog("Project ID = ".$row["ProjectID"]."--");
			//Add pid to an array
			array_push($pidsArray,$row["ProjectID"]);
        	}

		//return the array of project ids
		return $pidsArray;
	}
	else
	{
		echo("Error description 1: " . mysqli_error($link));
		echo "ERROR running query!";
	}
}

//Update theProjects table. Add 1 day to act sqa release
//for each project in the passed in array.
function updateProjectsSQARelease($link,$pidArray,$days)
{
	//Loop through array of pids, and update that pids dates in the Projects table
	for($i = 0; $i <= count($pidArray)-1; $i++)
	{
		//echo "\nIndex".$i." = ".$pidArray[$i];
		if($results=mysqli_query($link,"SELECT actualSQAComplete from Projects where ProjectID='$pidArray[$i]'"))
		{
			$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
			echo "\nCURRENT SQA RELEASE DATE FOR pid ".$pidArray[$i]." is ".$row["actualSQAComplete"];
			$currentDate = $row["actualSQAComplete"];
			//add 1 day (only if date isn't null)
			if($currentDate == "")
			{
				echo "\n Project ID ".$pidArray[$i]." does NOT have an actual sqa release date.";
				historyEntry("cronjob",$pidArray[$i],$link,"actSQARel-err","NA","NA", "ERROR:SQA Rel has an invalid date");
			}
			else
			{
				echo "\n Current date = ".$currentDate;
				//add $days to the current actual sqa release date
				$newDate = date("Y-m-d", strtotime($currentDate."+".$days." days"));
				echo "\n New date = ".$newDate;
				//insert new date into db
                        	saveToDB($newDate,$link,"actualSQAComplete",$pidArray[$i]);
				//Add project history entry
				historyEntry("cronjob",$pidArray[$i],$link,"actSQARel",$currentDate,$newDate, "added $days day(s) due to On Hold status");
			}
		}
		else
		{
			echo("Error description 2: " . mysqli_error($link));
                	echo "ERROR running query 2!";
        	}
	}
}

function updateProjectsITLRelease($link,$pidArray,$days)
{
        //Loop through array of pids, and update that pids dates in the Projects table
        for($i = 0; $i <= count($pidArray)-1; $i++)
        {
                //echo "\nIndex".$i." = ".$pidArray[$i];
                if($results=mysqli_query($link,"SELECT actualITLComplete from Projects where ProjectID='$pidArray[$i]'"))
                {
                        $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                        echo "\nCURRENT ITL RELEASE DATE FOR pid ".$pidArray[$i]." is ".$row["actualITLComplete"];
                        $currentDate = $row["actualITLComplete"];
                        //add 1 day (only if date isn't null)
                        if($currentDate == "")
                        {
                                echo "\n Project ID ".$pidArray[$i]." does NOT have an actual ITL release date.";
				historyEntry("cronjob",$pidArray[$i],$link,"actITLRel-err","NA","NA", "ERROR:ITL Rel has an invalid date");
                        }
                        else
                        {
                                echo "\n Current date = ".$currentDate;
                                //add $days to the current actual ITL release date
                                $newDate = date("Y-m-d", strtotime($currentDate."+".$days." days"));
                                echo "\n New date = ".$newDate;
                                //insert new date into db
                                saveToDB($newDate,$link,"actualITLComplete",$pidArray[$i]);
                                //Add project history entry
                                historyEntry("cronjob",$pidArray[$i],$link,"actITLRel",$currentDate,$newDate, "added $days day(s) due to On Hold status");
                        }
                }
                else
                {
                        echo("Error description 2: " . mysqli_error($link));
                        echo "ERROR running query 2!";
                }
        }
}

function saveToDB($date,$link,$field,$pid)
{
	//print arguments
	echo "\nPassed in new date is ".$date;
	echo "\nPassed in db field is ".$field;
	echo "\nPassed in pid is ".$pid;
	//$sql="INSERT INTO Projects($field) VALUES('$date') WHERE ProjectID = $pid";
	$sql="UPDATE Projects SET $field = '$date' WHERE ProjectID = '$pid'";
	if(!mysqli_query($link,$sql))
	{
		echo("\nError description 3: " . mysqli_error($link));
	}
	else
	{
		echo "\nNEW DATE INSERTED INTO PROJECTS TABLE :)";
	}
}

function historyEntry($user,$pid,$link,$changeType,$changeFrom,$changeTo,$note)
{
	$date = date("Y-m-d");//will return ex: 2018-08-02
        $time = date("h:i:s");// will return ex: 05:22:29

	if($changeType == "actSQARel"){
		$sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,actSqaCmpOld,actSqaCmpNew,note) VALUES('$pid','$user','$date','$time','$changeType','$changeFrom','$changeTo','$note')";
	}
	else if($changeType == "actITLRel"){
		$sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,actItlCmpOld,actItlCmpNew,note) VALUES('$pid','$user','$date','$time','$changeType','$changeFrom','$changeTo','$note')";
	}
        else if($changeType == "actITLRel-err"){
                $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note) VALUES('$pid','$user','$date','$time','$changeType','$note')";
        }
        else if($changeType == "actSQARel-err"){
                $sql = "INSERT INTO ProjectHistory (projectID,user,dateOfChange,timeOfChange,changeType,note) VALUES('$pid','$user','$date','$time','$changeType','$note')";
        }
	else{
		echo "ERROR: incorrect changetype";
	}
        if(!mysqli_query($link,$sql))
        {
                echo("Error description: " . mysqli_error($link));
                //mysqli_close($link);
        }
        else
        {
		echo "\nProject History entry added!";
               //mysqli_close($link);
        }
}
?>
