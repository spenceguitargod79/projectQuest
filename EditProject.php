<!DOCTYPE html>
<?php
include('verify.php');
include('siteFuncs.php');
include('calculateGADate.php');
//if(!isSQAUser($username))
//{
//    header("location: epslist.php");
//}
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="Stylesheet" href="styles.css" type="text/css" />

<style>
.messages {
    //background-color: grey;
    text-align: left;
    display: inline-block;
    color: black;
    padding: 10px;
}
</style>

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
	</td>
	<td width="25%" align="right">
	  <?php echo "Edit Project &nbsp;&nbsp;|&nbsp;&nbsp;";?>
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

$link=sqlConnect();

$id=$_GET['name'];
//$projectid=$_GET['projectID'];
//echo "Project Name: ";
if($results=mysqli_query($link,"SELECT ProjectName FROM Projects WHERE ProjectID='".$id."' "))
{
	$ProjectName=mysqli_fetch_array($results,MYSQLI_ASSOC);
}
echo $ProjectName["ProjectName"];
echo "<br><br>";

$taskid=$_GET['name'];

$sql=("SELECT * FROM Projects where ProjectID='".$id."'");
if($results=mysqli_query($link,$sql))
{
	$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
	$projectname=$row["ProjectName"];
	$projecttype=$row["ProjectType"];
	$class=$row["Class"];
	$complexity=$row["complexityID"];
	$details=$row["AdditionalProjectDetails"];
	$handoff=convertFromSQLDate($row["HandoffDate"]);
	$startdate=convertFromSQLDate($row["EstimatedStartDate"]);
	$actualStartDate=convertFromSQLDate($row["actualStartDate"]);
	$estimatedCompleteDate=convertFromSQLDate($row["estSQAComplete"]);
        $actualCompleteDate=convertFromSQLDate($row["actualSQAComplete"]);
	$estimatedITLDate=convertFromSQLDate($row["estITLComplete"]);
	$actualITLDate=convertFromSQLDate($row["actualITLComplete"]);
	$gaTargetHealth=$row["gaTargetHealth"];
	$currentStudio=$row["studio"];
}
else
{
	echo("Error description: " . mysqli_error($link));
}
echo "<form action=\"updateProject.php\" method=\"post\">";
echo "<table id='table1' cellspacing='0'>";

	echo "<tr class='even'>";
	echo "<td>Project Name:</td> <td><input type=\"text\" name=\"projectname\" value=\"".$projectname."\"></td>";
	echo "</tr>";

echo "<tr>";
$sql = "SELECT * FROM ProjectType";
echo "<td>Project Type: </td><td><select name=\"projecttype\">";
if($results=mysqli_query($link,$sql))
{
	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
	{
		$newprojecttype=$row['ProjectType'];
		if(!strcmp($newprojecttype,$projecttype))
		{
			echo "<option value='".$newprojecttype."' selected>".$newprojecttype."</option>";
		}
		else
		{
			echo "<option value='".$newprojecttype."'>".$newprojecttype."</option>";
		}
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
	echo "</select></td>";
	echo "</tr>";
echo "<tr>";
$sql = "SELECT * FROM Market";
echo "<td>Market: </td><td><select name=\"class\">";
if($results=mysqli_query($link,$sql))
{
	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
	{
		$newclass=$row['Market'];
		if(!strcmp($newclass,$class))
		{
			echo "<option value='".$newclass."' selected>".$newclass."</option>";
		}
		else
		{
			echo "<option value='".$newclass."'>".$newclass."</option>";
		}
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
	echo "</select></td>";
	echo "</tr>";

//Project Studio drop list
echo "<tr>";
$sql = "SELECT name FROM Studios";
echo "<td>Studio: </td><td><select name=\"studioname\">";

if($results=mysqli_query($link,$sql))
{
        while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
        {
                $currStudio=$row['name'];

                if(!strcmp($currStudio,$currentStudio))
                {
                        echo "<option value='".$currStudio."' selected>".$currStudio."</option>";
                }
                else
                {
                        echo "<option value='".$currStudio."'>".$currStudio."</option>";
                }
        }
}
else
{
        echo("Studio Error description: " . mysqli_error($link));
}
echo "</select></td>";
echo "</tr>";
//-----------------------------

//Project complexity input - option value should be the complex id from Projects table,
//but also display all possible complex ids, in the droplist, from GADuration table.
echo "<tr>";
$sql = "SELECT * FROM GADuration";
echo "<td>Complexity: </td><td><select name=\"complexityID\">";

if($results=mysqli_query($link,$sql))
{
	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
        {
		$complex=$row['complexityID'];

		if(!strcmp($complex,$complexity))
                {
                        echo "<option value='".$complex."' selected>".$complex."</option>";
                }
                else
                {
                	echo "<option value='".$complex."'>".$complex."</option>";
                }
	}
}
else
{
        echo("Error description: " . mysqli_error($link));
}
echo "</select></td>";
echo "</tr>";
//-----------------------------
		
	echo "<td>Project Details:</td>	<td><input type=\"text\" name=\"details\" value=\"".$details."\"></td>";
	echo "</tr>";
	//Only allow editing if no project revisions exist yet
	if(oneRevisionExists($id, $link) || multipleRevisionsExist($id, $link)){
		//DO NOT allow editing handoff
		//echo "<tr class='even'>";
        	//echo "<td>Handoff Date (MM/DD/YYYY):</td>     <td><h3>".$handoff."</h3></td>";
        	//echo "</tr>";
		//echo "<input type='hidden' name='handoff' value='".$handoff."'>";

		//Management requested that handoff be editable 3/12/2019-SH
		echo "<tr class='even'>";
                echo "<td>Handoff Date (MM/DD/YYYY):</td>       <td><input type=\"text\" name=\"handoff\" value='".$handoff."'></td>";
                echo "</tr>";
	}
	else{
		//Allow handoff editing
        	echo "<tr class='even'>";
        	echo "<td>Handoff Date (MM/DD/YYYY):</td>       <td><input type=\"text\" name=\"handoff\" value='".$handoff."'></td>";
        	echo "</tr>";
	}

	echo "<tr class='even'>";
	echo "<td>Estimated Start Date (MM/DD/YYYY):</td>     <td><h3>".$startdate."</h3></td>";
	echo "</tr>";
	//echo "<td>Actual Start Date (MM/DD/YYYY):</td>     <td><input type=\"text\" name=\"startdate\" value='".$actualStartDate."'></td>";
        echo "<tr class='even'>";
        echo "<td>Actual Start Date (MM/DD/YYYY):</td>     <td><h3>".$actualStartDate."</h3></td>";
        echo "</tr>";

        echo "<tr class='even'>";
        echo "<td>Estimated SQA Complete Date (MM/DD/YYYY):</td>     <td><h3>".$estimatedCompleteDate."</h3></td>";
        echo "</tr>";

        echo "<tr class='even'>";
        echo "<td>Actual SQA Complete Date (MM/DD/YYYY):</td>       <td><input type=\"text\" name=\"actCmp\" value='".$actualCompleteDate."'></td>";
        echo "</tr>";

        echo "<tr class='even'>";
        echo "<td>Estimated ITL Complete Date (MM/DD/YYYY):</td>     <td><h3>".$estimatedITLDate."</h3></td>";
        echo "</tr>";

        echo "<tr class='even'>";
        echo "<td>Actual ITL Complete Date (MM/DD/YYYY):</td>       <td><input type=\"text\" name=\"actITLCmp\" value='".$actualITLDate."'></td>";
        echo "</tr>";

        echo "<tr class='even'>";
        echo "<td>Note:</td>       <td><input type=\"text\" name=\"projectnote\" id=\"projectnote\"></td>";
        echo "</tr>";


	/*echo "<tr class='even'>";
	echo "<td>GA Target Health:</td>";
	if($gaTargetHealth == 1)
	{
		echo "<td><input type=\"radio\" checked name=\"health\" value=\"ok\"><img src=\"good.jpg\" alt=\"On track Jack\" width=\"50\" height=\"50\">";
		echo "<input type=\"radio\" name=\"health\" value=\"caution\"><img src=\"warning.png\" alt=\"Proceed with caution\" width=\"50\" height=\"50\">";
        	echo "<input type=\"radio\" name=\"health\" value=\"danger\"><img src=\"screwed.jpg\" alt=\"Abort!\" width=\"50\" height=\"50\">";
	}
	else if($gaTargetHealth == 2)
	{
		echo "<td><input type=\"radio\" name=\"health\" value=\"ok\"><img src=\"good.jpg\" alt=\"On track Jack\" width=\"50\" height=\"50\">";
		echo "<input type=\"radio\" checked name=\"health\" value=\"caution\"><img src=\"warning.png\" alt=\"Proceed with caution\" width=\"50\" height=\"50\">";
		echo "<input type=\"radio\" name=\"health\" value=\"danger\"><img src=\"screwed.jpg\" alt=\"Abort!\" width=\"50\" height=\"50\">";
	}
	else if($gaTargetHealth == 3)
        {
		echo "<td><input type=\"radio\" name=\"health\" value=\"ok\"><img src=\"good.jpg\" alt=\"On track Jack\" width=\"50\" height=\"50\">";
        	echo "<input type=\"radio\" name=\"health\" value=\"caution\"><img src=\"warning.png\" alt=\"Proceed with caution\" width=\"50\" height=\"50\">";
		echo "<input type=\"radio\" checked name=\"health\" value=\"danger\"><img src=\"screwed.jpg\" alt=\"Abort!\" width=\"50\" height=\"50\">";
        }
	else//the project doesn't have this value yet
	{
		echo "<td><input type=\"radio\" name=\"health\" value=\"ok\"><img src=\"good.jpg\" alt=\"On track Jack\" width=\"50\" height=\"50\">";
                echo "<input type=\"radio\" name=\"health\" value=\"caution\"><img src=\"warning.png\" alt=\"Proceed with caution\" width=\"50\" height=\"50\">";
                echo "<input type=\"radio\" name=\"health\" value=\"danger\"><img src=\"screwed.jpg\" alt=\"Abort!\" width=\"50\" height=\"50\">";
	}
	echo "</td>";
	echo "</tr>";*/

echo "<input type='hidden' name='projectid' value='".$id."'>";
echo "<input type='hidden' name='user' value='".$username."'>";
echo "</table>";
echo "<br>";
echo "<input type=\"submit\" value=\"Submit\">";
echo "</form>";

echo "<div class='messages'";
echo "<br><br><li><b>Changing complexity ONLY updates dates if the project doesn't have any revisions yet (if its still 'in queue').</li> ";
echo "<br><li>Attempting to edit and save Actual SQA Complete or Actual ITL Complete will only work if the project has 1 or more revisions.</li>";
echo "<br><li>Once the 1st project revision is created, Actual Start Date will become the date of that revision, and all other dates will change accordingly.</li>";
echo "</div>";

//SHOW PROJECT HISTORY ENTRIES
echo "<br>";
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
	$filter="dateOfChange";
	$direction=0;
}
//if($results=mysqli_query($link,"SELECT ProjectName FROM Projects WHERE ProjectID='".$id."' "))
//{
//        $ProjectName=mysqli_fetch_array($results,MYSQLI_ASSOC);
//}
//echo "<h2>".$ProjectName["ProjectName"]."</h2>";
//echo "<br>";

echo "<h1> Project History </h1>";
echo "<th><a href='EditProject.php?name=$id&filter=user&direction=".$direction."'>User</a></th>";
echo "<th><a href='EditProject.php?name=$id&filter=dateOfChange&direction=".$direction."'>Date</a></th>";
echo "<th><a href='EditProject.php?name=$id&filter=timeOfChange&direction=".$direction."'>Time</a></th>";
echo "<th><a href='EditProject.php?name=$id&filter=changeType&direction=".$direction."'>Changed</a></th>";
echo "<th><a href='EditProject.php?name=$id&filter=changeType&direction=".$direction."'>From</a></th>";
echo "<th><a href='EditProject.php?name=$id&filter=changeType&direction=".$direction."'>To</a></th>";
echo "<th><a href='EditProject.php?name=$id&filter=note&direction=".$direction."'>Note</a></th>";
echo "</tr>";
echo "<thead>";
$rowCount=0;
echo "<tbody>";
if($results=mysqli_query($link,"SELECT * FROM ProjectHistory where projectID='$id' ORDER BY ".$filter))
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
		echo "<td>".$row["user"]."</td>";
		echo "<td>".$row["dateOfChange"]."</td>";
		echo "<td>".$row["timeOfChange"]."</td>";
		echo "<td>".$row["changeType"]."</td>";
		//Display the correct changes
		switch ($row["changeType"]) {
                        case "USER NOTE":
                                echo "<td>N/A</td>";
                                echo "<td>N/A</td>";
                                break;
			case "ActualStart":
				echo "<td>".$row["actSqaStDateOld"]."</td>";
				echo "<td>".$row["actSqaStDateNew"]."</td>";
        			break;
    			case "Class":
				echo "<td>".$row["classOld"]."</td>";
                                echo "<td>".$row["classNew"]."</td>";
        			break;
    			case "Handoff":
				echo "<td>".$row["handoffOld"]."</td>";
                                echo "<td>".$row["handoffNew"]."</td>";
        			break;
                        case "Project Name":
                                echo "<td>".$row["projNameOld"]."</td>";
                                echo "<td>".$row["projNameNew"]."</td>";
                                break;
                        case "Complexity":
                                echo "<td>".$row["complexityOld"]."</td>";
                                echo "<td>".$row["complexityNew"]."</td>";
                                break;
                        case "Details":
                                echo "<td>".$row["detailsOld"]."</td>";
                                echo "<td>".$row["detailsNew"]."</td>";
                                break;
                        case "Project Type":
                                echo "<td>".$row["projTypeOld"]."</td>";
                                echo "<td>".$row["projTypeNew"]."</td>";
                                break;
                        case "EstimatedSQAStart":
                                echo "<td>".$row["estSqaStDateOld"]."</td>";
                                echo "<td>".$row["estSqaStDateNew"]."</td>";
                                break;
                        case "EstimatedSQAComplete":
                                echo "<td>".$row["estSqaCmpOld"]."</td>";
                                echo "<td>".$row["estSqaCmpNew"]."</td>";
                                break;
			case "actSQARel":
                                echo "<td>".$row["actSqaCmpOld"]."</td>";
                                echo "<td>".$row["actSqaCmpNew"]."</td>";
                                break;
                        case "EstimatedITLComplete":
                                echo "<td>".$row["estItlCmpOld"]."</td>";
                                echo "<td>".$row["estItlCmpNew"]."</td>";
                                break;
			case "actITLRel":
                                echo "<td>".$row["actItlCmpOld"]."</td>";
                                echo "<td>".$row["actItlCmpNew"]."</td>";
                                break;
                        case "ActualITLComplete":
                                echo "<td>".$row["actItlCmpOld"]."</td>";
                                echo "<td>".$row["actItlCmpNew"]."</td>";
                                break;
                        case "ActualSQAComplete":
                                echo "<td>".$row["actSqaCmpOld"]."</td>";
                                echo "<td>".$row["actSqaCmpNew"]."</td>";
                                break;
                        case "Target":
                                echo "<td>".$row["onTargetOld"]."</td>";
                                echo "<td>".$row["onTargetNew"]."</td>";
                                break;
                        case "Studio":
                                echo "<td>".$row["studioOld"]."</td>";
                                echo "<td>".$row["studioNew"]."</td>";
                                break;
    			default:
                                echo "<td>ERROR</td>";
                                echo "<td>ERROR</td>";

		}

		//Show the history's note here
		echo "<td>".$row["note"]."</td>";
		echo "</tr>";
	$rowCount++;
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
echo "</table>";

//Query all notes for this project, then display them.
/*$sql=("SELECT * FROM ProjectNotes where ProjectID='".$id."'");
if($results=mysqli_query($link,$sql))
{
	echo "<table style=\"width:100%\" border=\"1px solid black\">";
	echo "<tr>";
        echo "<th>User</th>";
        echo "<th>Note</th>";
        echo "<th>Timestamp</th>";
        echo "</tr>";
	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
	{
		$user=$row["username"];
		$note=$row["note"];
        	$datetime=$row["timestamp"];

		//Display all notes for this project
		echo "<tr>";
		echo "<td>".$user."</td>";
		echo "<td>".$note."</td>";
		echo "<td>".$datetime."</td>";
		echo "</tr>";
	}
	echo "</table>";
}
else
{
        echo("Error description: " . mysqli_error($link));
}*/ 

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
