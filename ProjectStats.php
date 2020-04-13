<NEW!DOCTYPE html>
<?php
include('verify.php');
include ('siteFuncs.php');
include ('calculateGADate.php');
$link=sqlConnect();
$logfile = 'pslog.txt';
$myfile = fopen("pslog.txt", "w") or die("Unable to open file!");
fwrite($myfile, "PROJECT STATS LOG --------------------------------------------------------");
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="Stylesheet" href="styles.css" type="text/css" />

<title>SQA Project Tracking</title>

</head>

<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" marginheight="0" marginwidth="0" bgcolor="#FFFFFF">

<table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#123456" height="100">
  <tr>
    <td width="100%">
      <p align="center" style="font-family:Arial, Helvetica, sans-serif; color:white;"><font size="5"><b>SQA Project Dashboard</b></font></td>
  </tr>
</table>
<table id='table2' border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#123456">
  <tr valign="bottom">
    <td width="75%">
		<?php echo "&nbsp;&nbsp<a href=ProjectList.php>Projects In Progress</font></a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectQueue.php>Projects In Queue</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ProjectComplete.php>Projects Complete</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
               <?php echo "&nbsp;&nbsp<a href=ProjectOn-Hold.php>Projects On-Hold</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=ITLList.php>Projects In ITL</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;";?>
                <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php>Dev Projects</a>
                &nbsp;&nbsp;";?>
	</td>
	<td width="25%" align="right">Project Statistics<font color=707070> |
	  <?php if(false){echo "<a href=admins.php>Admins</a>
	  &nbsp;&nbsp;|&nbsp;";} echo $username."  | ";
	  $group="";
	  if(isAdmin($username) || isSQAuser($username))
	  {
		//echo "Administrator";
		echo '<a href="projectStatsLog.php"><img src="images/log.png" width="30" height="25" /></a>';
	  }
	  else
	  {
		//echo "Guest";
	  }
	  ?></font>
      <?php echo "|&nbsp;&nbsp; <a href=logout.php>Logout</a> &nbsp;&nbsp;&nbsp;&nbsp;"; ?>
	</td>
  </tr>
</table>
<br><br>
<?php
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $agent = $_SERVER['HTTP_USER_AGENT'];
}
$showLive=false;
if (strlen(strstr($agent, 'Firefox')) > 0) {
    $showLive = true;
}
//echo "<br>";
echo '<center><img src="images/projectstats.png" /></center>';

echo "<div class='header-layout'>";
echo "<div class='header'>";
echo "<table id='table1' cellspacing='0'>";
echo "<thead>";
echo "<tr>";

if(isset($_GET['filter']) && isset($_GET['direction']))
{
	$filter=$_GET['filter'];
	$direction=$_GET['direction'];
	if(!$direction)
	{
		$direction=1;
		$filter.=" desc";
	}
	else
	{
		$direction=0;
	}
}
else
{
	$filter="ProjectName";
	$direction=0;
}
//Table headers
echo "<th id='projectnameH5'><a href='ProjectStats.php?filter=ProjectName&direction=".$direction."'>Project Name</a></th>";
echo "<th id='projecttypeH5'><a href='ProjectStats.php?filter=ProjectType&direction=".$direction."'>Type</a></th>";
echo "<th id='marketH5'><a href='ProjectStats.php?filter=Class&direction=".$direction."'>Market</a></th>";
echo "<th id='hodateH5'><a href='ProjectStats.php?filter=HandoffDate&direction=".$direction."'>Handoff</a></th>";
echo "<th id='stdateH5'><a href='ProjectStats.php?filter=StartDate&direction=".$direction."'>SQA Start</a></th>";
echo "<th id='testdaysH5'>Testing</th>";
echo "<th id='rejectdaysH5'>Rejected&nbsp;</th>";
echo "<th id='onholddaysH5'>On-Hold</th>";
echo "<th id='revisonsH5'>Revisions&nbsp;&nbsp;&nbsp;</th>";
//if(isAdmin($username))
//{
//	echo "<th>Delete</th>";
//}
echo "</tr>";
echo "</thead>";
echo "</table>";

echo "</div>";//layout div
echo "</div>";//container div
?>

<?php
echo "<div class='layout'>";
echo "<div class='container'>";
echo "<table id='table1' cellspacing='0'>";
$rowCount=0;
echo "<tbody>";
if($results=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete, Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status, ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName, ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID ORDER BY ".$filter))
//if($results=mysqli_query($link,"SELECT * FROM Projects"))
{
	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
	{
		if($rowCount%2==0)
		{
			echo "<tr class='even'>";
		}
		else
		{
			echo "<tr>";
		}
		//================PROJECT STATS FUNCTION CALLS==================================
		//Logging data
		fwrite($myfile, "<br>+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++<br>");
		$datetime=getCurrentDateTime();
		fwrite($myfile, "TIMESTAMP:".$datetime."<br>PID: ".$row["ProjectID"]);
		fwrite($myfile, "<br>Project Name: ".$row["ProjectName"]);
		fwrite($myfile, "<br>Class: ".$row["Class"]);
                //Adds project to ProjectStats table, if it doesn't already exist
                createProjStatsDbEntry($link,$row["ProjectID"]);
		//get a count of revisions for this project
		$revCount = calculateTotalRevisionCount($link,$row["ProjectID"]);
		//Get start date for each revision
		$firstRevStartDate = getFirstRevisionStartDate($link,$row["ProjectID"],$myfile);
		//checkStatusChanges contains most logic for tracking days
		checkStatusChanges($link,$row["ProjectID"],$myfile);
		echo "<td id='projectnameD5'><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
		echo "<td id='projecttypeD5'>".$row["ProjectType"]."</td>";
                echo "<td id='marketD5'>".$row["Class"]."</td>";
                echo "<td id='hodateD5'>".convertDateFormat($row["HandoffDate"], "m/d/Y")."</td>";
		echo "<td id='stdateD5'>".convertDateFormat($row["StartDate"], "m/d/Y")."</td>";
                echo "<td id='testdaysD5'>".getTestDaysTotal($link,$row["ProjectID"])."</td>";
                echo "<td id='rejectdaysD5'>".getRejectDaysTotal($link,$row["ProjectID"])."</td>";
                echo "<td id='onholddaysD5'>".getOnHoldDaysTotal($link,$row["ProjectID"])."</td>";
		//Reset these values so everytime the page loads, they don't accumulate in the db
		resetTestDays($link,$row["ProjectID"]);
                resetRejectDays($link,$row["ProjectID"]);
		resetOnHoldDays($link,$row["ProjectID"]);

		//Display revision count, in the table.
		echo "<td id='revisionsD5'>".$revCount."</td>";
                //==================================================================
		echo "</tr>";
	$rowCount++;
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
echo "</tbody>";
echo "</table>";

mysqli_close($link);
?>
</div>
</div>

<p style="margin-left: 20"><font face="Arial" size="2" color="#000000">&nbsp;</font></p>
<p style="margin-left: 20"><font face="Arial" size="2" color="#000000">&nbsp;</font></p>
<p style="margin-left: 20"><font face="Arial" size="2" color="#000000">&nbsp; </font></p>
<p style="margin-left: 20"><font face="Arial" size="2" color="#000000">&nbsp;</font></p>
<p style="margin-left: 20"><font face="Arial" size="2" color="#000000">&nbsp;</font></p>
<p style="margin-left: 20"><font face="Arial" size="2" color="#000000">&nbsp;</font></p>
<p style="margin-left: 20"><font face="Arial" size="2" color="#000000">&nbsp;</font></p>
<p style="margin-left: 20"><font face="Arial" size="2" color="#000000">&nbsp;</font></p>
<p style="margin-left: 20" align="center"><font face="Arial" color="#000000" size="1">©
AGS 2018</font></p>
<table border="0" width="100%" cellspacing="0" cellpadding="0" background="img/botline.gif">
  <tr>
    <td width="100%"><img border="0" src="img/botline.gif" width="41" height="12"></td>
  </tr>
</table>

</body>

</html>

<?php
//Functions----------------------------

//If the last revision history entry 'change to' status is Rejected, On-Hold, or Testing,
//Get the difference between the current date and the date that the history entry was created.
//Add the days to rejected, on-hold, or testing days.
function addRealTimeDays($link, $pid, $myfile, $status, $lastChangeDate){
	//current date
        $today = getCurrentDate();

	fwrite($myfile, "ADD REALTIME DAYS: Current date is ".$today."---");
	fwrite($myfile, "ADD REALTIME DAYS: Last history entry status change date is ".$lastChangeDate."---");
	fwrite($myfile, "ADD REALTIME DAYS: Status to add days to is ".$status."---");
	fwrite($myfile, "ADD REALTIME DAYS: PID is ".$pid."---");

        //Days difference
        $daysDiff = calculateDays($lastChangeDate,$today);
        fwrite($myfile, "ADD REALTIME DAYS: Days to add = ".$daysDiff."---");

	//get the current value of days and add daysDiff to it
	newTotalDays = "";

	//update the DB
	$sql = "UPDATE ProjectStats SET '$status' = '$addDays' where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Updated previousStatusNew value for Pid ".$pid;
                }
                else{
                        //echo "previousChangeDate update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "addRealTimeDays QUERY FAILED";
        }
}



//Get a list of all revisions for a project. Return an array that contains those revisions.
function getRevisionsList($link,$projID){
	$i=0;
	$sql = "SELECT Revision FROM ProjectDetails where ProjectID=$projID";
        if($results=mysqli_query($link,$sql))
        {
                while($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
			//echo "\n---ProjectID:".$projID;
			//echo "Revision:".$row["Revision"]."---";
			//add revisions to array
			$revisions[$i]=$row["Revision"];
			$i++;
		}
		return  $revisions;
        }
	else
	{
		echo "GetRevisionsList: Error executing query!!";
	}
}

function getFirstRevisionStartDate($link,$pid,$myfile){
        $sql = "SELECT StartDate,Revision FROM ProjectDetails where ProjectID=$pid ORDER BY Revision LIMIT 1";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
		//Logging data
		fwrite($myfile, "<br>---ProjectID:".$pid);
		fwrite($myfile, " First Revision:".$row["Revision"]."---");
		fwrite($myfile, " First Revision StartDate:".$row["StartDate"]."---");

                return $row["StartDate"];
        }
        else
        {
                echo "GetFirstRevisionStartDate: Error executing query!!";
        }
}

//Determine if revision passed in is the project's 1st revision
function isFirstRevision($link,$pid,$rev,$myfile){
        $sql = "SELECT Revision FROM ProjectDetails where ProjectID='$pid' ORDER BY Revision LIMIT 1";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //Logging data
                fwrite($myfile, "<br>---ProjectID:".$pid);
                fwrite($myfile, "<br>First Revision:".$row["Revision"]."---");
                if($row["Revision"] == $rev){
                        fwrite($myfile, "<br>This revision (rev from query = ".$row["Revision"].") is the FIRST revision. Rev passed in = ".$rev);
                        return "true";
                }
                else{
                        fwrite($myfile, "<br>This revision (rev from query = ".$row["Revision"].") is NOT the FIRST revision. Rev passed in = ".$rev);
                        return "false";
                }
        }
        else
        {
                echo "isFirstRevision(): Error executing query!!";
        }
}

//TODO:Get the rev start date of passed in revision
//If there's a NEW revision, the start date will need to be used in stats calculation
function getRevisionStartDate($link,$pid,$rev,$myfile){
        $sql = "SELECT StartDate FROM ProjectDetails where ProjectID='$pid' AND Revision=$rev LIMIT 1";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
		//log data
		fwrite($myfile, "<br>---ProjectID:".$pid);
		fwrite($myfile, "Revision:".$row["Revision"]."---");
		fwrite($myfile, "StartDate:".$row["StartDate"]."---");

                return $row["StartDate"];
        }
        else
        {
                echo "GetRevisionStartDate: Error executing query!!";
        }
}

//Return onhold, rejected, testing, or new revision. This will be used to determine what variable to increment in the ProjectStats table.
function determineWhereToAddDays($changeTo,$changeFrom,$changeType,$myfile){
	if($changeTo == "Rejected" && $changeFrom == "Testing"){
		fwrite($myfile, "Change from = ".$changeFrom." and change to = ".$changeTo." ADD to TESTING DAYS value.");
		return "Test";
	}
	elseif($changeTo == "On-Hold" && $changeFrom == "Testing"){
		fwrite($myfile, "Change from = ".$changeFrom." and change to = ".$changeTo." ADD to TESTING DAYS value.");
                return "Test";
	}
	elseif($changeTo == "On-Hold" && $changeFrom == "Rejected"){
		fwrite($myfile, "Change from = ".$changeFrom." and change to = ".$changeTo." ADD to REJECTED DAYS value.");
        	return "Reject";
        }
        elseif($changeTo == "Testing" && $changeFrom == "Rejected"){
		fwrite($myfile, "Change from = ".$changeFrom." and change to = ".$changeTo." ADD to REJECTED DAYS value.");
                return "Reject";
        }
        elseif($changeTo == "Rejected" && $changeFrom == "On-Hold"){
		fwrite($myfile, "Change from = ".$changeFrom." and change to = ".$changeTo." ADD to ON-HOLD DAYS value.");
                return "Hold";
        }
        elseif($changeTo == "Testing" && $changeFrom == "On-Hold"){
		fwrite($myfile, "Change from = ".$changeFrom." and change to = ".$changeTo." ADD to ON-HOLD DAYS value.");
                return "Hold";
        }
        elseif($changeType == "NewRevision"){
		fwrite($myfile, "Change type = ".$changeType." This is a new revision.");
                return "NEW";
        }
	else{
		fwrite($myfile, "These changes do not match any cases.");
	}
}


//Most of the main logic for ProjectStats is here
function checkStatusChanges($link,$pid,$myfile){
        //$sql = "SELECT dateOfChange,revision,timeOfChange,changeType,statusOld,statusNew,ID FROM ProjectDetailHistory WHERE projectID='$pid' ORDER BY dateOfChange, timeOfChange";
	$sql = "SELECT ID,dateOfChange,revision,timeOfChange,changeType,statusOld,statusNew,ID FROM ProjectDetailHistory WHERE projectID='$pid' AND (changeType = 'status' OR changeType = 'NewRevision') ORDER BY dateOfChange, timeOfChange";
        if($results=mysqli_query($link,$sql))
        {
                while($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){

			$prevDate=getPrevChangeDate($link,$pid);//used in calculating days past
			$idRev=$row["ID"];
			fwrite($myfile, "<br>---ProjectID:".$pid);
			fwrite($myfile, ":ChangeRevision: ".$row["revision"]."---");
			fwrite($myfile, ":ChangeDate: ".$row["dateOfChange"]."---");
			fwrite($myfile, ":ChangeTime: ".$row["timeOfChange"]."---");
			fwrite($myfile, ":ChangeType: ".$row["changeType"]."---");
			fwrite($myfile, ":ChangeFROMStatus: ".$row["statusOld"]."---");
			fwrite($myfile, ":ChangeTOStatus: ".$row["statusNew"]."---");
			fwrite($myfile, ":Previous Change Date = ".$prevDate);
			fwrite($myfile, ":ID = ".$idRev);
			fwrite($myfile, ":ID of history entry when project went to ITL = ".getHistEntryIdITL($link,$pid));

			$entryID = getHistEntryIdITL($link,$pid);
			fwrite($myfile, "<br> entryID data type is : ".gettype($entryID));
			fwrite($myfile, "<br> entryID is : ".$entryID);
			$firstRevResult = isFirstRevision($link,$pid,$row["revision"],$myfile);
			$combinedResult = "";//used to determine if any days should be added
                        //check if this is the first revision and its the history entry where the 1st revision was entered.
			if($row["changeType"] == "NewRevision" && $firstRevResult == "true"){
				//don't use previousChange date in this case
				fwrite($myfile, "<br>--This IS the first revision AND a New Revision!--");
				$combinedResult = "stop";
			}
			else if($row["changeType"] == "NewRevision" && $firstRevResult == "false"){
				fwrite($myfile, "<br>--This is NOT first revision but IS a new revision added.--");
				$combinedResult = "continue";
			}
			else{
				fwrite($myfile, "<br>--This is NOT first revision and IS NOT a new revision.--");
				$combinedResult = "continue";
			}

			//If The current revision history entry id is less than the history id of the entry where status was changed TO ITL, then continue calculations. 
			//if(empty($entryID) && ($row["ID"] < $entryID)){
				if($row["statusNew"] == "ITL"){
					//setIsComplete($link,$pid,"YES");
					fwrite($myfile, "<br> ITL status detected, setting the hist id to this one so the loop skips this andy and new status changes. No further calculations needed. Breaking out of loop.");
					//TODO:Set the history entry id itl value
					setHistEntryIdITL($link,$pid,$idRev);
				}
				//TODO:place all code here
				fwrite($myfile, "<br> Calculations not complete yet... Be patient..");
			//Determine where to add days
			$addTo=determineWhereToAddDays($row["statusNew"],$row["statusOld"],$row["changeType"],$myfile);

			//TODO:If only 1 revision exists, and only 1 project detail history 
			//$isOneRev=oneRevisionExists($pid,$link);
			//call a method that will see if there is a project detail history entry.
			//If there isnt, tha its the 1st revision, and no status changes have been made
			//hasNoRevisionHistory();

			//add the days only if its not the very first revision entered
			if($addTo == "Test" && $combinedResult == "continue"){
				$daysToAdd=calculateDays($prevDate,$row["dateOfChange"]);
                        	addToTestDaysTotal($link,$pid,$daysToAdd);
				fwrite($myfile, "<br>".$daysToAdd." days will be added to ".$addTo);
			}
			elseif($addTo == "Reject" && $combinedResult == "continue"){
				$daysToAdd=calculateDays($prevDate,$row["dateOfChange"]);
                        	addToRejectDaysTotal($link,$pid,$daysToAdd);
				fwrite($myfile, "<br>".$daysToAdd." days will be added to ".$addTo);
			}
                        elseif($addTo == "Hold" && $combinedResult == "continue"){
				$daysToAdd=calculateDays($prevDate,$row["dateOfChange"]);
                                addToOnHoldDaysTotal($link,$pid,$daysToAdd);
				fwrite($myfile, "<br>".$daysToAdd." days will be added to ".$addTo);
                        }
			elseif($addTo == "NEW" && $combinedResult == "continue"){//new revision
				$revresult = oneRevisionExists($pid, $link);
				//If this new revision is the 1st revision (and was just added), then don't grab last status from DB, so no days are added to stats.
				if(!$revresult){
					$lastStatus=getStatusNewLastHistoryEntry($link,$pid,$idRev,$myfile);
					fwrite($myfile, "<br>More than one revision exists =  ".$revresult);
				}
				else{
					$lastStatus = "NULL";
					fwrite($myfile, "<br>This is a new revision and the 1st revision. ");
				}

				//call this again to see where exactly to add days
				$stat = getRevisionStatus($link,$pid,$row["revision"]);
				$prevStatusNew = getPreviousChangeTO($link,$pid);
				fwrite($myfile, "<br>STATUS NEW of last history entry for this pid = ".$lastStatus);
				fwrite($myfile, "<br>NEW REVISION DETECTED. Determining where to add days...");
				fwrite($myfile, "Current revision status is: ".$stat);
				fwrite($myfile, "Previous status NEW is: ".$prevStatusNew);
				$daysToAdd=calculateDays($prevDate,$row["dateOfChange"]);//date of change for current pid/revision
				fwrite($myfile, "<br>NEWREV: days to add =: ".$daysToAdd);
				if($lastStatus == "Rejected"){
					fwrite($myfile, "<br>NEW REV:DETECTED REJECTED DAYS");
					//subtract testing days from rejected because its somehow getting added to it.
					//$daysToAdd=getTestDaysTotal($link,$pid);
					//echo "<br>Subtracting ".getTestDaysTotal($link,$pid)." testdays from rejected to avoid a bug...";

					addToRejectDaysTotal($link,$pid,$daysToAdd);
					fwrite($myfile, "<br>".$daysToAdd." days will be added to REJECTED");
				}
				else if($lastStatus == "On-Hold"){
					fwrite($myfile, "<br>NEW REV:DETECTED ON-HOLD DAYS");
					addToOnHoldDaysTotal($link,$pid,$daysToAdd);
					fwrite($myfile, "<br>".$daysToAdd." days will be added to ON-HOLD");
				}
                                else if($lastStatus == "Testing"){
					fwrite($myfile, "<br>NEW REV:DETECTED TESTING DAYS");
					addToTestDaysTotal($link,$pid,$daysToAdd);
					fwrite($myfile, "<br>".$daysToAdd." days will be added to TESTING");
                                }
				else{
					fwrite($myfile, "<br>NEW REV:NOT adding days in the New revision if/elseif because all cases failed");
				}
			}

			//Update the previous change date to current value so next time around it can be use for comparison
			addPrevChangeDate($link,$pid,$row["dateOfChange"]);
			setPreviousChangeTO($link,$pid,$row["statusNew"]);
			setPreviousHistoryEntryID($link,$pid,$idRev);
			//Update previousStatusNew to current value
			//setPreviousStatusNew($link,$pid,$row["statusNew"]);
			//}//end of getIsComplete if block
		}
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "checkIfTestStatusChangeFrom(): Error executing query!!";
        }
}

function setHistEntryIdITL($link,$pid,$histID){
        $sql = "UPDATE ProjectStats SET histEntryIdITL = '$histID' where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Updated previousStatusNew value for Pid ".$pid;
                }
                else{
                        //echo "previousChangeDate update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "setHistEntryIdITL QUERY FAILED";
        }
}

function getHistEntryIdITL($link,$pid){
        $sql = "SELECT histEntryIdITL FROM ProjectStats where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        return $row["histEntryIdITL"];
                }
                else{
                        //echo "getPreviousChangeDate FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "getHistEntryIdITL QUERY FAILED";
        }
}

function setIsComplete($link,$pid,$result){
        $sql = "UPDATE ProjectStats SET isComplete = '$result' where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Updated previousStatusNew value for Pid ".$pid;
                }
                else{
                        //echo "previousChangeDate update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "setIsComplete QUERY FAILED";
        }
}

function getIsComplete($link,$pid){
        $sql = "SELECT isComplete FROM ProjectStats where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        return $row["isComplete"];
                }
                else{
                        //echo "getPreviousChangeDate FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "getIsComplete QUERY FAILED";
        }
}

function setPreviousChangeTO($link,$pid,$changeTO){
        $sql = "UPDATE ProjectStats SET previousChangeTo = '$changeTO' where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Updated previousStatusNew value for Pid ".$pid;
                }
                else{
                        //echo "previousChangeDate update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "setPreviousChangeTO QUERY FAILED";
        }
}

function getPreviousChangeTO($link,$pid){
        $sql = "SELECT previousChangeTO FROM ProjectStats where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        return $row["previousChangeTO"];
                }
                else{
                        //echo "getPreviousChangeDate FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "getPreviousChangeTO QUERY FAILED";
        }
}

function setPreviousHistoryEntryID($link,$pid,$entID){
        $sql = "UPDATE ProjectStats SET previousHistoryEntryID = '$entID' where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Updated previousStatusNew value for Pid ".$pid;
                }
                else{
                        //echo "previousChangeDate update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "setPreviousHistoryEntryID QUERY FAILED";
        }
}

function getPreviousHistoryEntryID($link,$pid){
        $sql = "SELECT previousHistoryEntryID FROM ProjectStats where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        return $row["previousHistoryEntryID"];
                }
                else{
                        //echo "getPreviousChangeDate FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "getPreviousHistoryEntryID QUERY FAILED";
        }
}

function setPreviousStatusNew($link,$pid,$pStat){
        //Update the project's previous 'statusNew' value
        $sql = "UPDATE ProjectStats SET previousStatusNew = '$pStat' where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Updated previousStatusNew value for Pid ".$pid;
                }
                else{
                        //echo "previousChangeDate update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "setPreviousStatusNew QUERY FAILED";
        }
}

function getPreviousStatusNew($link,$pid){
        //Grab previousStatusNew
        $sql = "SELECT previousStatusNew FROM ProjectStats where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        return $row["previousStatusNew"];
                }
                else{
                        //echo "getPreviousChangeDate FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "getPreviousStatusNew QUERY FAILED";
        }
}

//used for new revisions
function getStatusNewLastHistoryEntry($link,$pid,$idRev,$myfile){

	$previousHistEntryID = getPreviousHistoryEntryID($link,$pid);
	fwrite($myfile, "<br>Previous history entry id = ".$previousHistEntryID);
	$id = $idRev - 1;//the previous history entry
	fwrite($myfile, "<br>Looking up revision id ".$id);
        $sql = "SELECT statusNew FROM ProjectDetailHistory where projectID = '$pid' AND ID = $previousHistEntryID";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        return $row["statusNew"];
                }
                else{
                        //echo "getPreviousChangeDate FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "getStatusNewLastHistoryEntry QUERY FAILED";
        }
}

//Return the status of revision (mainly used to get status of a newly entered revision).
function getRevisionStatus($link,$pid,$rev){

        $sql = "SELECT Status from ProjectDetails WHERE projectID = '$pid' and Revision = '$rev'";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>The status of revision ".$rev.", pid ".$pid.", is ".$row["Status"];
			return $row["Status"];
                }
                else{
                        //echo "query returned zero results";
                }
        }
        else{
                echo "getRevisionStatus QUERY FAILED";
        }
}

//create a new entry if the pid doesn't already exist
function createProjStatsDbEntry($link,$pid){
	$exists=0;
	$sql = "SELECT projectID from ProjectStats WHERE projectID LIKE $pid";
	if($results=mysqli_query($link,$sql))
        {
		if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
			//echo "<br>Pid ".$pid." already exists in ProjectStats table.";
			$exists=1;//represents true
		}
		else{
			//echo "DOES NOT EXIST";
			$exists=0;//represents false
		}
	}
	else{
		echo "createProjStatsDbEntry QUERY FAILED";
	}
	//Insert new project into ProjectStats table
	if($exists == 1){
		//echo "DO NOTHING";

	}else{
		//echo "Adding new project to ProjectStats table...";
		//execute INSERT query
		$sql = "INSERT INTO ProjectStats(projectID) values($pid)";
		if($results=mysqli_query($link,$sql))
        	{
			//echo "<br>Added db entry for pid: ".$pid;
		}
		else{
			//echo "Insert query failed";
		}
	}
	$exists=0;
}

//Update prev change date. This will be used in calculating days passed
function addPrevChangeDate($link,$pid,$dateToAdd){
        //Update the project's testDays
        $sql = "UPDATE ProjectStats SET previousChangeDate = '$dateToAdd' where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Updated previousChangeDate value for Pid ".$pid;
                }
                else{
                        //echo "previousChangeDate update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "addPrevChangeDate QUERY FAILED";
        }
}

//Return prev change date. This will be used in calculating days passed
function getPrevChangeDate($link,$pid){
        //Grab previous date change value
        $sql = "SELECT previousChangeDate FROM ProjectStats where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>previousChangeDate value for Pid ".$pid." is ".$row["previousChangeDate"];
			return $row["previousChangeDate"];
                }
                else{
                        //echo "getPreviousChangeDate FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "getPrevChangeDate QUERY FAILED";
        }
}

//Update column in the database table
function addToTestDaysTotal($link,$pid,$valueToAdd){
	//Get current value of testDays from the db
	$sql = "SELECT testDays from ProjectStats where projectID=$pid";
        if($results=mysqli_query($link,$sql))
        {
		if($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
		{
                        //echo "<br>Pid ".$pid." total testing days:".$row["testDays"];
                }

	}

	$newDaysTotal=$valueToAdd+$row["testDays"];

	//Update the project's testDays
        $sql = "UPDATE ProjectStats SET testDays = '$newDaysTotal' where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Updated testDays value for Pid ".$pid;
                }
                else{
                        //echo "testDays update FAILED :(";
                }
        }
        else
	{
		echo("Error description: " . mysqli_error($link));
                echo "addToTestDaysTotal QUERY FAILED";
        }
}

//Return column value from db
function getTestDaysTotal($link,$pid){
        $sql = "SELECT testDays FROM ProjectStats where ProjectID='$pid'";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                return $row["testDays"];
        }
        else
        {
                echo "getTestDaysTotal: Error executing query!!";
        }
}

//Update column in the database table
function addToRejectDaysTotal($link,$pid,$valueToAdd){

	//Get current value of rejectDays from the db
        $sql = "SELECT rejectDays from ProjectStats where projectID=$pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
                {
                        //echo "<br>Pid ".$pid." total rejected days:".$row["rejectDays"];
                }
        }

        $newDaysTotal=$valueToAdd+$row["rejectDays"];

        //Update the project's rejectDays
        $sql = "UPDATE ProjectStats SET rejectDays = '$newDaysTotal' where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Updated rejectDays value for Pid ".$pid;
                }
                else{
                        //echo "rejectDays update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "addToRejectDaysTotal QUERY FAILED";
        }
}

//Return column value from db
function getRejectDaysTotal($link,$pid){
        $sql = "SELECT rejectDays FROM ProjectStats where ProjectID='$pid'";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                return $row["rejectDays"];
        }
        else
        {
                echo "getRejectDaysTotal: Error executing query!!";
        }
}

//Update column in the database table
function addToOnHoldDaysTotal($link,$pid,$valueToAdd){
        //Get current value of onholdDays from the db
        $sql = "SELECT onholdDays from ProjectStats where projectID=$pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
                {
                        //echo "<br>Pid ".$pid." total onhold days:".$row["onholdDays"];
                }
        }

        $newDaysTotal=$valueToAdd+$row["onholdDays"];

        //Update the project's onholdDays
        $sql = "UPDATE ProjectStats SET onholdDays = '$newDaysTotal' where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Updated onholdDays value for Pid ".$pid;
                }
                else{
                        //echo "onholdDays update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "addToOnHoldDaysTotal QUERY FAILED";
        }
}

//Return column value from db
function getOnHoldDaysTotal($link,$pid){
        $sql = "SELECT onholdDays FROM ProjectStats where ProjectID='$pid'";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                return $row["onholdDays"];
        }
        else
        {
                echo "getOnHoldDaysTotal: Error executing query!!";
        }
}

//Get total number of revisions a project has
function calculateTotalRevisionCount($link,$projID){
        $sql = "SELECT count(Revision) FROM ProjectDetails where ProjectID=$projID";
        if($result=mysqli_query($link,$sql))
        {
		$row = mysqli_fetch_array($result);
		$count = $row[0];
		//echo "Total revisions = ".$count." ||| ";
		return $count;
        }
}

//Return the difference, in days.
function calculateDays($prevDate,$currentDate)
{
	//change date format to dd-mm-yyyy
	$prevDate=date("d-m-Y", strtotime($prevDate));
	$currentDate=date("d-m-Y", strtotime($currentDate));

	$diff = strtotime($currentDate) - strtotime($prevDate);

	// 1 day = 24 hours 
    	// 24 * 60 * 60 = 86400 seconds
	return abs(round($diff / 86400));
}

function resetTestDays($link,$pid){
        //Update the project's testDays to 0
        $sql = "UPDATE ProjectStats SET testDays = 0 where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Resetting testDays to 0 value for Pid ".$pid;
                }
                else{
                        //echo "testDays update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "resetTestDays QUERY FAILED";
        }
}

function resetRejectDays($link,$pid){
        //Update the project's rejectDays to 0
        $sql = "UPDATE ProjectStats SET rejectDays = 0 where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Resetting rejectDays to 0 value for Pid ".$pid;
                }
                else{
                        //echo "rejectDays update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "resetRejectDays QUERY FAILED";
        }
}

function resetOnHoldDays($link,$pid){
        //Update the project's onholdDays to 0
        $sql = "UPDATE ProjectStats SET onholdDays = 0 where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Resetting onholdDays to 0 value for Pid ".$pid;
                }
                else{
                        //echo "testDays update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "resetOnHoldDays QUERY FAILED";
        }
}

function resetPrevStatusNew($link,$pid){
        //Update the project's rejectDays to 0
        $sql = "UPDATE ProjectStats SET rejectDays = 'cleared' where projectID = $pid";
        if($results=mysqli_query($link,$sql))
        {
                if($row=mysqli_fetch_array($results,MYSQLI_ASSOC)){
                        //echo "<br>Resetting rejectDays to 0 value for Pid ".$pid;
                }
                else{
                        //echo "rejectDays update FAILED :(";
                }
        }
        else
        {
                echo("Error description: " . mysqli_error($link));
                echo "resetPrevStatusNew QUERY FAILED";
        }
}
?>
