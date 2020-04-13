<!DOCTYPE html>
<?php
include('verify.php');
include('siteFuncs.php');
//if(!isSQAUser($username))
//{
//    header("location: epslist.php");
//}
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
	</td>
	<td width="25%" align="right">
	  <?php echo "Edit Project Details &nbsp;&nbsp;|&nbsp;&nbsp;";?>
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
$projectid=$_GET['projectID'];
//echo "Project Name: ";
if($results=mysqli_query($link,"SELECT ProjectName FROM Projects WHERE ProjectID='".$projectid."' "))
{
	$ProjectName=mysqli_fetch_array($results,MYSQLI_ASSOC);
}
echo $ProjectName["ProjectName"];
echo "<br><br>";

$taskid=$_GET['name'];

$sql=("SELECT * FROM ProjectDetails where TaskID='".$taskid."'");
if($results=mysqli_query($link,$sql))
{
	$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
	$revision=$row["Revision"];
	$owner=$row["Owner"];
	$status=$row["Status"];
	$notes=$row["Notes"];
	$startdate=convertFromSQLDate($row["StartDate"]);
	$enddate=convertFromSQLDate($row["EndDate"]);
	$projectid=$row["ProjectID"];
	$checkboxValue=$row["demoIsChecked"];
	$demoComplete=$row["demoComplete"];
	//echo "DemoRequested? -> ".$checkboxValue;
	//echo "DemoCompleted? -> ".$demoComplete;
}
else
{
	echo("Error description: " . mysqli_error($link));
}
echo "<form action=\"updateProjectDetails.php\" method=\"post\">";
echo "<table id='table1' cellspacing='0'>";

	echo "<tr class='even'>";
	echo "<td>Owner:</td> <td><input type=\"text\" name=\"owner\" value=\"".$owner."\"></td>";
	echo "</tr>";

	echo "<tr class='even'>";
	echo "<td>Revision:</td> <td><input type=\"text\" name=\"revision\" value='".$revision."'></td>";
	echo "</tr>";

	echo "<tr class='even'>";
	echo "<td>Start Date (MM/DD/YYYY):</td>	<td><input type=\"text\" name=\"startdate\" value='".$startdate."'></td>";
	echo "</tr>";
	echo "<tr class='even'>";
	echo "<td>End Date   (MM/DD/YYYY):</td>	<td><input type=\"text\" name=\"enddate\" value='".$enddate."'></td>";
	echo "</tr>";

	echo "<tr>";
$latestRev = getLatestProjectRevision($link,$projectid);
$sql = "SELECT * FROM Status";

//Only allow editing status if this is the latest project revision, otherwise just show the current status value.
if($revision == $latestRev)
{
	echo "<td>Status: </td><td><select name=\"status\">";
	if($results=mysqli_query($link,$sql))
	{
        	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
        	{
                	$newstatus=$row['Status'];
                	if(!strcmp($newstatus,$status))
                	{
                        	echo "<option value='".$newstatus."' selected>".$newstatus."</option>";
                	}
                	else
                	{
                        	echo "<option value='".$newstatus."'>".$newstatus."</option>";
                	}
        	}
	}
	else
	{
        	echo("Error description: " . mysqli_error($link));
	}
	echo "</select></td>";
}
else
{
	echo "<td>Status: </td><td>".$status;
	echo "<input type='hidden' name='status' value='".$status."'>";//handles nulling out status issue when form is submitted
}
echo "</tr>";


	echo "<tr class='even'>";
	echo "<td>Notes:</td>	<td><input type=\"text\" name=\"notes\" value=\"".$notes."\"></td>";
	echo "</tr>";

	echo "<tr class='even'>";
	if($checkboxValue == "Y" && is_null($demoComplete)){//if checkbox is checked and demo complete is null
		echo "<td>Ready for Demo:</td>   <td><input type=\"checkbox\" name=\"checkValue\" value=\"Y\" checked></td>";
	}else if($checkboxValue == "N" && is_null($demoComplete)){//if checkbox is unchecked and demo complete is null
        	echo "<td>Ready for Demo:</td>   <td><input type=\"checkbox\" name=\"checkValue\" value=\"Y\"></td>";
	}
	else if($demoComplete == "YES"){//if the demo is in completed state
		echo "<td>Ready for Demo:</td>   <td> <h3><strong><font color=\"green\"> COMPLETED </font></strong></h3></td>";
		//echo "<td>Ready for Demo:</td>   <td> <img src=\"images/completed.png\"></td>";
	}
	else if(is_null($demoComplete) && is_null($checkboxValue)){//if values are null
		echo "<td>Ready for Demo:</td>   <td><input type=\"checkbox\" name=\"checkValue\" value=\"Y\"></td>";
	}
        else if(!isset($demoComplete) && !isset($checkboxValue)){//if values are empty
                echo "<td>Ready for Demo:</td>   <td><input type=\"checkbox\" name=\"checkValue\" value=\"Y\"></td>";
        }
        else if($checkboxValue == "Y" && isset($demoComplete)){//if checkbox is checked and demo is not set
                echo "<td>Ready for Demo:</td>   <td><input type=\"checkbox\" name=\"checkValue\" value=\"Y\" checked></td>";
        }
	else if($checkboxValue == "N" && isset($demoComplete)){//if checkbox is un-checked and demo is not set
                echo "<td>Ready for Demo:</td>   <td><input type=\"checkbox\" name=\"checkValue\" value=\"Y\"></td>";
        }
	else{
		echo "<td>Ready for Demo:</td><td> <font color=\"red\"> ERROR :^( </font></td>";
	}

        echo "</tr>";

echo "<input type='hidden' name='taskid' value='".$taskid."'>";
echo "<input type='hidden' name='projectid' value='".$projectid."'>";
echo "<input type='hidden' name='demoCmp' value='".$demoComplete."'>";
echo "<input type='hidden' name='checkBoxVal' value='".$checkboxValue."'>";
echo "</table>";
echo "<br>";
echo "<input type=\"submit\" value=\"Submit\">";
echo "</form>";
mysqli_close($link);

function getLatestProjectRevision($link,$pid){
        $sql = "SELECT Revision FROM ProjectDetails where ProjectID='$pid' ORDER BY Revision DESC LIMIT 1";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //echo "<br>---ProjectID:".$pid;
                //echo "Latest Revision:".$row["Revision"]."---";

                return $row["Revision"];
        }
        else
        {
                echo "getLatestProjectRevision: Error executing query!!";
        }
}
?>
<h4><i> <font color ="blue">Status</font> is only editable in the latest revision </i></h4>
<h4><i> <font color ="blue">End date</font> will be automatically saved as today's date if status is changed to any rejected, ITL, On-Hold, or Obsolete status </i></h4>
<h4><i> <font color ="blue">SQA Release date</font> will be automatically saved as today's date if status is changed to ITL </i></h4>
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
Cadillac Jack 2015</font></p>
<table border="0" width="100%" cellspacing="0" cellpadding="0" background="img/botline.gif">
  <tr>
    <td width="100%"><img border="0" src="img/botline.gif" width="41" height="12"></td>
  </tr>
</table>

</body>

</html>
