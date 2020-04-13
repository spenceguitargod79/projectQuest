<!DOCTYPE html>
<?php
include('verify.php');
include ('siteFuncs.php');
include ('calculateGADate.php');
$link=sqlConnect();
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
	  <?php echo "&nbsp;&nbsp<a href=ProjectList.php>Projects In Progress</a>
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
                <?php
                if(isAdmin($username)){
                        echo "|&nbsp&nbsp;&nbsp;&nbsp<a href=Projects.php>All Projects</a>
                        &nbsp;&nbsp;";
                }
                ?>
	</td>
	<td width="25%" align="right">
	
	 <?php if(isAdmin($username)){
	      //echo "<a href=AddProject.php>Add New Project</a> &nbsp;&nbsp;|&nbsp;&nbsp;";
	  }?>

	  <?php echo "Game Release &nbsp;&nbsp;|&nbsp;&nbsp;";?>
	  <font color="#707070">
	  <?php if(false){echo "<a href=admins.php>Admins</a>
	  &nbsp;&nbsp;|&nbsp;";} echo $username."  " ; 
	  $group="";
	  if(isAdmin($username))
	  {
		//echo "Administrator";
	  }
	  else if(isSQAuser($username))
	  {
		//echo "SQA User";
	  }
	  else
	  {
		//echo "Guest";
	  }
	  ?></font>	
      <?php echo "&nbsp;&nbsp;|&nbsp;&nbsp; <a href=logout.php>Logout</a> &nbsp;&nbsp;&nbsp;&nbsp;"; ?>
	</td>
  </tr>
</table>
<br><br>
<div id='div1'>
<?php
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $agent = $_SERVER['HTTP_USER_AGENT'];
}
$showLive=false;
if (strlen(strstr($agent, 'Firefox')) > 0) {
    $showLive = true;
}
if(!$showLive)
{
	//echo "-Live View Available in Firefox-<br>";
}
echo "<h2>Game Release Forecast</h2>";
echo "<i>Today is " . date("m-d-Y") . "</i><br>";
echo "<br>";
echo "<a href=\"#Next60\">Next 60</a> | <a href=\"#Past30\">Past 30</a> | <a href=\"#Past60\">Past 60</a> | <a href=\"#Reject\">Rejected</a>";
echo "<br></br>";
echo "<table id='table1' cellspacing='0'>";
echo "<caption><strong>RELEASING IN 30 DAYS</strong></caption>";
echo "<thead>";
echo "<tr>";
//echo "<th>EPS Name</th>";
if($showLive)
{
	//echo "<th>Live Video</th>";
}
//if(isSQAuser($username))
//{
//	echo "<th>Check In/Out</th>";
//	echo "<th>Edit</th>";
//}
if(isAdmin($username) || isSQAuser($username) || isGuest($username) || isNOuser($username)){

echo "<th>Project Name</th>";
echo "<th>Project Type</th>";
echo "<th>Market</th>";
echo "<th>Project Details</th>";
echo "<th>Estimated GA</th>";
echo "<th>Status</th>";
//echo "<th>HandoffDate</th>";
//echo "<th>Estimated Start Date</th>";

//if(isAdmin($username))
//{
//	echo "<th>Delete</th>";
//}
echo "</tr>";
echo "<thead>";
$rowCount=0;
echo "<tbody>";
//GAMES RELEASING IN THE NEXT 30 days-----------------------------------------------------------------------------------------------
//need a seperate query so we can check if zero games are slated for release in the next 30 days..
//It seems mysqli_fetch_array can only be ran 1 time on a result, so declare a new variable for this query.
$resultThirtyDay=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete,
Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class,
Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status,
ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID)
from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName,
ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID
WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID
WHERE (Tbl1.Status = 'ITL' OR Tbl1.Status = 'Testing') AND (ProjectType = 'Game') AND (actualITLComplete BETWEEN NOW() AND NOW() + INTERVAL 30 DAY) AND (Tbl1.Class = 'C2' OR Tbl1.Class = 'C3') ORDER BY actualITLComplete ASC;");

if($results=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete,
Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class,
Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status,
ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID)
from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName,
ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID
WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID
WHERE (Tbl1.Status = 'ITL' OR Tbl1.Status = 'Testing') AND (ProjectType = 'Game') AND (actualITLComplete BETWEEN NOW() AND NOW() + INTERVAL 30 DAY) AND (Tbl1.Class = 'C2' OR Tbl1.Class = 'C3') ORDER BY actualITLComplete ASC;"))
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
		//$epsUser=$row["epsUser"];
		//Only let sqa or admin access project details
		if(isAdmin($username) || isSQAuser($username)){
			echo "<td><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
		}
		else{
			echo "<td>".$row["ProjectName"]."</td>";
		}
		//if($showLive)
		//{
		//	echo "<td><a target='_blank' href='http://medusa:256/?camid=".$row["camid"]."&w=500&h=500'><img src='video.png' style='max-height: 25px; max-width: 20px;' /></a></td>";
		//	//echo "<td><a target='_blank' href='http://localhost:256/?camid=".$row["camid"]."&w=500&h=500'><img src='video.png' style='max-height: 25px; max-width: 20px;' /></a></td>";
		//}
		//if(isSQAuser($username))
		//{
		//	if(empty($row["epsUser"]))
		//	{
		//		echo "<td><a href='checkout.php?name=".urlencode($row["name"])."'>Check Out</a></td>";
		//	}
		//	else if(!empty($epsUser) && (($epsUser==$username) || isAdmin($username)))
		//	{
		//		echo "<td><a href='checkindb.php?name=".urlencode($row["name"])."'>Check In</a></td>";
		//	}
		//	else
		//	{
		//		echo "<td>In Use</td>";
		//	}
		//}
		//if(isSQAuser($username))
		//{
		//	if(!empty($epsUser))
		//	{
		//		echo "<td><a href='editEPS.php?name=".$row["name"]."'>Edit</a></td>";
		//	}
		//	else
		//	{
		//		echo "<td></td>";
		//	}
		//}
		//echo "<td>".$row["ProjectName"]."</td>";
		echo "<td>".$row["ProjectType"]."</td>";		
		echo "<td>".$row["Class"]."</td>";
		echo "<td>".$row["AdditionalProjectDetails"]."</td>";
		echo "<td>".convertDateFormat($row["actualITLComplete"], "m/d/y")."</td>";
		echo "<td>".$row["Status"]."</td>";
		//echo "<td>".convertFromSQLDate($row["HandoffDate"])."</td>";		
		//echo "<td>".convertFromSQLDate($row["EstimatedStartDate"])."</td>";		
		
		if(isAdmin($username))
		{
			//echo "<td><a href='adminDelete.php?ProjectID=".urlencode($row["ProjectID"])."&table=Projects'>Delete</a></td>";
		}
		
		echo "</tr>";
	$rowCount++;
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
}
else{
	echo "You hath not access to this page.. seek an SQA manager for extended access please. ";
}
echo "</tbody>";
echo "</table>";

	//show if no games are currently scheduled for release in 30 days
        if(count($rowCheck=mysqli_fetch_array($resultThirtyDay,MYSQLI_ASSOC)) == 0){
                //echo "No games releasing in 30 days";
                //display image
                echo "<img src=\"images/marioSAd.png\" alt=\"No games in Lab\" width=\"100\" height=\"100\">";
        }
        else{
                //echo "Some games releasing in 30 days!";
        }
echo "<br><br>";
//echo "<hr>";
//GAMES Releasing in the next 60 days (between (current date+30) + 30 days) -------------------------------------------------------------------------
echo "<table id='table1' cellspacing='0'>";
echo "<caption><a id=\"Next60\"></a><strong>RELEASING IN 60 DAYS</strong></caption>";
echo "<thead>";
echo "<tr>";
if(isAdmin($username) || isSQAuser($username) || isGuest($username) || isNOuser($username)){
echo "<br></br>";
echo "<th>Project Name</th>";
echo "<th>Project Type</th>";
echo "<th>Market</th>";
echo "<th>Project Details</th>";
echo "<th>Estimated GA</th>";
echo "<th>Status</th>";
//echo "<th>HandoffDate</th>";
//echo "<th>Estimated Start Date</th>";

echo "</tr>";
echo "<thead>";
$rowCount=0;
echo "<tbody>";
//need a seperate query so we can check if zero games are slated for release in the next 60 days..
//It seems mysqli_fetch_array can only be ran 1 time on a result, so declare a new variable for this query.
$resultSixtyDay=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete,
Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class,
Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status,
ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID)
from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName,
ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID
WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID
WHERE (Tbl1.Status = 'ITL' OR Tbl1.Status = 'Testing') AND (ProjectType = 'Game') AND (actualITLComplete BETWEEN NOW() + INTERVAL 30 DAY AND NOW() + INTERVAL 60 DAY) AND (Tbl1.Class = 'C2' OR Tbl1.Class = 'C3') ORDER BY actualITLComplete ASC;");

if($results2=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete,
Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class,
Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status,
ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID)
from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName,
ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID
WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID
WHERE (Tbl1.Status = 'ITL' OR Tbl1.Status = 'Testing') AND (ProjectType = 'Game') AND (actualITLComplete BETWEEN NOW() + INTERVAL 30 DAY AND NOW() + INTERVAL 60 DAY) AND (Tbl1.Class = 'C2' OR Tbl1.Class = 'C3') ORDER BY actualITLComplete ASC;"))
{
	while($row=mysqli_fetch_array($results2,MYSQLI_ASSOC))
	{
		if($rowCount%2==0)
		{
			echo "<tr class='even'>";
		}
		else
		{
			echo "<tr>";
		}
		//Only let sqa or admin access project details
                if(isAdmin($username) || isSQAuser($username)){
                        echo "<td><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
                }
                else{
                        echo "<td>".$row["ProjectName"]."</td>";
                }
		echo "<td>".$row["ProjectType"]."</td>";		
		echo "<td>".$row["Class"]."</td>";
		echo "<td>".$row["AdditionalProjectDetails"]."</td>";
		echo "<td>".convertDateFormat($row["actualITLComplete"], "m/d/y")."</td>";
		echo "<td>".$row["Status"]."</td>";
		//echo "<td>".convertFromSQLDate($row["HandoffDate"])."</td>";		
		//echo "<td>".convertFromSQLDate($row["EstimatedStartDate"])."</td>";		
		
		if(isAdmin($username))
		{
			//echo "<td><a href='adminDelete.php?ProjectID=".urlencode($row["ProjectID"])."&table=Projects'>Delete</a></td>";
		}
		
		echo "</tr>";
	$rowCount++;
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
}
else{
	 echo "You hath not access to this page.. seek an SQA manager for extended access please. ";
}
echo "</tbody>";
echo "</table>";

	//show if no games are currently scheduled for release in 60 days
        if(count($rowCheck2=mysqli_fetch_array($resultSixtyDay,MYSQLI_ASSOC)) == 0){
                //echo "No games releasing in 60 days";
                //display image
                echo "<img src=\"images/marioSAd.png\" alt=\"No games in Lab\" width=\"100\" height=\"100\">";
        }
        else{
                //echo "Some games releasing in 60 days!";
        }

//GAMES Released in the PAST 30 days (between (current date) - 30 days) -------------------------------------------------------------------------
echo "<table id='table1' cellspacing='0'>";
echo "<caption><a id=\"Past30\"></a><font color=\"green\">RELEASED IN THE PAST 30 DAYS</font></caption>";
echo "<thead>";
echo "<tr>";
echo "<br></br>";
if(isAdmin($username) || isSQAuser($username) || isGuest($username) || isNOuser($username)){
echo "<br></br>";
echo "<th>Project Name</th>";
echo "<th>Project Type</th>";
echo "<th>Market</th>";
echo "<th>Project Details</th>";
echo "<th>Release Date</th>";
echo "<th>Status</th>";
//echo "<th>HandoffDate</th>";
//echo "<th>Estimated Start Date</th>";

echo "</tr>";
echo "<thead>";
$rowCount=0;
echo "<tbody>";
//need a seperate query so we can check if zero games were released in the past 30 days..
//It seems mysqli_fetch_array can only be ran 1 time on a result, so declare a new variable for this query.
$resultPastThirtyDay=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete,
Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class,
Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status,
ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID)
from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName,
ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID
WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID
WHERE (Tbl1.Status = 'Approved') AND (ProjectType = 'Game') AND (actualITLComplete BETWEEN NOW() - INTERVAL 30 DAY AND NOW()) AND (Tbl1.Class = 'C2' OR Tbl1.Class = 'C3') ORDER BY actualITLComplete ASC;");

if($results3=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete,
Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class,
Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status,
ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID)
from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName,
ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID
WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID
WHERE (Tbl1.Status = 'Approved') AND (ProjectType = 'Game') AND (actualITLComplete BETWEEN NOW() - INTERVAL 30 DAY AND NOW()) AND (Tbl1.Class = 'C2' OR Tbl1.Class = 'C3') ORDER BY actualITLComplete ASC;"))
{
	while($row=mysqli_fetch_array($results3,MYSQLI_ASSOC))
	{
		if($rowCount%2==0)
		{
			echo "<tr class='even'>";
		}
		else
		{
			echo "<tr>";
		}
                //Only let sqa or admin access project details
                if(isAdmin($username) || isSQAuser($username)){
                        echo "<td><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
                }
                else{
                        echo "<td>".$row["ProjectName"]."</td>";
                }
		echo "<td>".$row["ProjectType"]."</td>";		
		echo "<td>".$row["Class"]."</td>";
		echo "<td>".$row["AdditionalProjectDetails"]."</td>";
		echo "<td>".convertDateFormat($row["actualITLComplete"], "m/d/y")."</td>";
		echo "<td>".$row["Status"]."</td>";
		//echo "<td>".convertFromSQLDate($row["HandoffDate"])."</td>";		
		//echo "<td>".convertFromSQLDate($row["EstimatedStartDate"])."</td>";		
		
		if(isAdmin($username))
		{
			//echo "<td><a href='adminDelete.php?ProjectID=".urlencode($row["ProjectID"])."&table=Projects'>Delete</a></td>";
		}
		
		echo "</tr>";
	$rowCount++;
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
}
else{
	echo "You hath not access to this page.. seek an SQA manager for extended access please. ";
}
echo "</tbody>";
echo "</table>";

	//show if no games were released in the past 30 days
        if(count($rowCheck3=mysqli_fetch_array($resultPastThirtyDay,MYSQLI_ASSOC)) == 0){
                //echo "No games released in past 30 days";
                //display image
                echo "<img src=\"images/marioSAd.png\" alt=\"No games in Lab\" width=\"100\" height=\"100\">";
        }
        else{
                //echo "Some games released in past 30 days!";
        }


//GAMES Released in the PAST 60 days (between (current date) - 60 days) -------------------------------------------------------------------------
echo "<table id='table1' cellspacing='0'>";
echo "<caption><a id=\"Past60\"></a><font color=\"green\">RELEASED IN THE PAST 60 DAYS</font></caption>";
echo "<thead>";
echo "<tr>";
echo "<br></br>";
if(isAdmin($username) || isSQAuser($username) || isGuest($username) || isNOuser($username)){
echo "<br></br>";
echo "<th>Project Name</th>";
echo "<th>Project Type</th>";
echo "<th>Market</th>";
echo "<th>Project Details</th>";
echo "<th>Release Date</th>";
echo "<th>Status</th>";
//echo "<th>HandoffDate</th>";
//echo "<th>Estimated Start Date</th>";

echo "</tr>";
echo "<thead>";
$rowCount=0;
echo "<tbody>";
//need a seperate query so we can check if zero games were released in the past 60 days..
//It seems mysqli_fetch_array can only be ran 1 time on a result, so declare a new variable for this query.
$resultPastSixtyDay=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete,
Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class,
Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status,
ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID)
from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName,
ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID
WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID
WHERE (Tbl1.Status = 'Approved') AND (ProjectType = 'Game') AND (actualITLComplete BETWEEN NOW() - INTERVAL 60 DAY AND NOW()) AND (Tbl1.Class = 'C2' OR Tbl1.Class = 'C3') ORDER BY actualITLComplete ASC;");

if($results4=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete,
Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class,
Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status,
ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID)
from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName,
ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID
WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID
WHERE (Tbl1.Status = 'Approved') AND (ProjectType = 'Game') AND (actualITLComplete BETWEEN NOW() - INTERVAL 60 DAY AND NOW() - INTERVAL 30 DAY) AND (Tbl1.Class = 'C2' OR Tbl1.Class = 'C3') ORDER BY actualITLComplete ASC;"))
{
	while($row=mysqli_fetch_array($results4,MYSQLI_ASSOC))
	{
		if($rowCount%2==0)
		{
			echo "<tr class='even'>";
		}
		else
		{
			echo "<tr>";
		}
                //Only let sqa or admin access project details
                if(isAdmin($username) || isSQAuser($username)){
                        echo "<td><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
                }
                else{
                        echo "<td>".$row["ProjectName"]."</td>";
                }
		echo "<td>".$row["ProjectType"]."</td>";		
		echo "<td>".$row["Class"]."</td>";
		echo "<td>".$row["AdditionalProjectDetails"]."</td>";
		echo "<td>".convertDateFormat($row["actualITLComplete"], "m/d/y")."</td>";
		echo "<td>".$row["Status"]."</td>";
		//echo "<td>".convertFromSQLDate($row["HandoffDate"])."</td>";		
		//echo "<td>".convertFromSQLDate($row["EstimatedStartDate"])."</td>";		
		
		if(isAdmin($username))
		{
			//echo "<td><a href='adminDelete.php?ProjectID=".urlencode($row["ProjectID"])."&table=Projects'>Delete</a></td>";
		}
		
		echo "</tr>";
	$rowCount++;
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
}
else{
	echo "You hath not access to this page.. seek an SQA manager for extended access please. ";
}
echo "</tbody>";
echo "</table>";

	//show if no games were released in the past 60 days
        if(count($rowCheck4=mysqli_fetch_array($resultPastSixtyDay,MYSQLI_ASSOC)) == 0){
                //echo "No games released in past 60 days";
                //display image
                echo "<img src=\"images/marioSAd.png\" alt=\"No games in Lab\" width=\"100\" height=\"100\">";
        }
        else{
                //echo "Some games released in past 60 days!";
        }


//GAMES in a rejected state -------------------------------------------------------------------------
echo "<table id='table1' cellspacing='0'>";
echo "<caption><a id=\"Reject\"></a><font color=\"red\">CURRENT REJECTED GAMES</font></caption>";
echo "<thead>";
echo "<tr>";
echo "<br></br>";
if(isAdmin($username) || isSQAuser($username) || isGuest($username) || isNOuser($username)){
echo "<br></br>";
echo "<th>Project Name</th>";
echo "<th>Project Type</th>";
echo "<th>Market</th>";
echo "<th>Project Details</th>";
//echo "<th>Release Date</th>";
echo "<th>Status</th>";
//echo "<th>HandoffDate</th>";
//echo "<th>Estimated Start Date</th>";

echo "</tr>";
echo "<thead>";
$rowCount=0;
echo "<tbody>";
//need a seperate query so we can check if there are zero rejected games currently
//It seems mysqli_fetch_array can only be ran 1 time on a result, so declare a new variable for this query.
$resultRejected=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete,
Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class,
Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status,
ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID)
from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName,
ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID
WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID
WHERE (Tbl1.Status = 'Rejected' OR Tbl1.Status = 'ITL Reject' OR Tbl1.Status = 'Field Reject') AND (ProjectType = 'Game') AND (Tbl1.Class = 'C2' OR Tbl1.Class = 'C3') ORDER BY actualITLComplete ASC;");

if($results5=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete,
Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class,
Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status,
ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID)
from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName,
ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID
WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID
WHERE (Tbl1.Status = 'Rejected' OR Tbl1.Status = 'ITL Reject' OR Tbl1.Status = 'Field Reject') AND (ProjectType = 'Game') AND (Tbl1.Class = 'C2' OR Tbl1.Class = 'C3') ORDER BY Status DESC;"))
{
	while($row=mysqli_fetch_array($results5,MYSQLI_ASSOC))
	{
		if($rowCount%2==0)
		{
			echo "<tr class='even'>";
		}
		else
		{
			echo "<tr>";
		}
                //Only let sqa or admin access project details
                if(isAdmin($username) || isSQAuser($username)){
                        echo "<td><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
                }
                else{
                        echo "<td>".$row["ProjectName"]."</td>";
                }
		echo "<td>".$row["ProjectType"]."</td>";
		echo "<td>".$row["Class"]."</td>";
		echo "<td>".$row["AdditionalProjectDetails"]."</td>";
		//echo "<td>".$row["actualITLComplete"]."</td>";
		echo "<td>".$row["Status"]."</td>";
		//echo "<td>".convertFromSQLDate($row["HandoffDate"])."</td>";
		//echo "<td>".convertFromSQLDate($row["EstimatedStartDate"])."</td>";
		
		if(isAdmin($username))
		{
			//echo "<td><a href='adminDelete.php?ProjectID=".urlencode($row["ProjectID"])."&table=Projects'>Delete</a></td>";
		}
		
		echo "</tr>";
	$rowCount++;
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
}
else{
	echo "You hath not access to this page.. seek an SQA manager for extended access please. ";
}
echo "</tbody>";
echo "</table>";

	//show if no games are currently in a rejected related status
        if(count($rowCheck5=mysqli_fetch_array($resultRejected,MYSQLI_ASSOC)) == 0){
                //echo "No games currently rejected";
                //display image
                echo "<img src=\"images/marioSAd.png\" alt=\"No games in Lab\" width=\"100\" height=\"100\">";
        }
        else{
                //echo "Some games are rejected!";
        }

mysqli_close($link);
?>
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
