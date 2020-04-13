<?php
//Purpose: Calculate various GA date values and more
//=========================================================

function calculateEstimatedStart($handoff,$start)
{
	//$handoff is a date and $start is a number value that represents days.
	//$result needs to equal the handoff date plus x days,which would be a new date.
	//writeToLog("HandoffDate= ".$handoff." ");
	//writeToLog("StartDays= ".$start." ");
	$result = date("Y-m-d", strtotime($handoff."+".$start." days"));
	//writeToLog("Result= ".$result." ");
	return $result;
}

function calculateEstimatedCompletion($esd,$sqaComp)
{
	//ECD = estimated start date from Projects table + sqa complete days from GADuration table
	$result = date("Y-m-d", strtotime($esd."+".$sqaComp." days"));
	return $result;
}

function calculateActualCompletion($asd,$sqaComp)
{
	//ACD = actual start date from Projects table + sqa complete days from GADuration table
        $result = date("Y-m-d", strtotime($asd."+".$sqaComp." days"));
        return $result;
}

function calculateEstimatedITLComplete($estSQACmp,$itlComp)
{
	//EST ITL CMP = Estimated SQA Complete + ITL complete from GA Duration table.
        $result = date("Y-m-d", strtotime($estSQACmp."+".$itlComp." days"));
        return $result;
}

function calculateActualITLComplete($actSQACmp,$itlCmp)
{
	//writeToLog("EST SQA COMP= ".$estSQACmp." ");
        //writeToLog("ITL COMP DAYS= ".$itlComp." ");
        //Actual ITL CMP = Actual SQA Complete + ITL complete from GA Duration table.
        $result = date("Y-m-d", strtotime($actSQACmp."+".$itlCmp." days"));
        //writeToLog(" Result= ".$result." ");
        return $result;
}

function  getCurrentDate()
{
	$date = date('Y-m-d');
	//writeToLog("Date = ".$date);
	return date('Y-m-d');
}

function getCurrentDateTime(){
	$dateTime = date('Y-m-d-H-i-s');
	return $dateTime;
}

function convertDateFormat($dateToConvert,$conversion)
{
	return date($conversion, strtotime($dateToConvert));
}

//Check if at least 1 project revision exists or not - more than 0 revisions
function oneRevisionExists($projectid, $link)
{
	//query the db
	$sql = "SELECT * FROM ProjectDetails WHERE ProjectID='$projectid'";

	if($result=mysqli_query($link,$sql))
	{
		$count = mysqli_num_rows($result);
		if($count > 0 && $count < 2){
			writeToLog("Returning TRUE");
			return true;
		}
		else{
			writeToLog("Returning FALSE");
			return false;
		}
		writeToLog("ROW COUNT = ".$count);
	}
	else
	{
		echo("Error description: " . mysqli_error($link));
		return false;
	}
}

//Check if MORE than 1 project revision exists
function multipleRevisionsExist($projectid, $link)
{
	//query the db
        $sql = "SELECT * FROM ProjectDetails WHERE ProjectID='$projectid'";

        if($result=mysqli_query($link,$sql))
        {
                $count = mysqli_num_rows($result);
                if($count > 1){
                        writeToLog("Returning TRUE - more than 1 revision exists");
                        return true;
                }
                else{
                        writeToLog("Returning FALSE - Less than 1 revisions exist");
                        return false;
                }
                writeToLog("ROW COUNT = ".$count);
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                return false;
        }
}

function getFirstRevStDate($projectid,$link)
{
	//query the db
        $sql = "SELECT * FROM ProjectDetails WHERE ProjectID='$projectid' ORDER BY 'Revision' ASC Limit 1";
	if($result=mysqli_query($link,$sql))
        {
		$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
		$sd = $row["StartDate"];
		writeToLog("First revision start date = ".$sd);
		return $sd;
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
        }

}

function getTargetHealth($pid, $selection)
{
	//if statement that takes selection and return the cooresponding.. 

	return result;

}

function writeToLog($data)
{
	$file = 'log.txt';
	// Write the contents to the file, 
	// using the FILE_APPEND flag to append the content to the end of the file
	// and the LOCK_EX flag to prevent anyone else writing to the file at the same time
	file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
}
?>
