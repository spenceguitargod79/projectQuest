<!DOCTYPE html>
<?php
include('verify.php');
include ('siteFuncs.php');
include('calculateGADate.php');
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
	  <?php echo "Reporting Tool &nbsp;&nbsp;|&nbsp;&nbsp;";?>
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
<center><h3>REPORTING TOOL (class 3 games)</H3></center>
<div id='div1'>
<?php

$link=sqlConnect();

//$id=$_GET['name'];
//echo "Project Name: ";
//if($results=mysqli_query($link,"SELECT ProjectName FROM Projects WHERE ProjectID='".$id."' "))
//{
//	$ProjectName=mysqli_fetch_array($results,MYSQLI_ASSOC);
//}
//echo $ProjectName["ProjectName"];
//echo "<br><br>";

//$id=$_GET['name'];
echo "<form action=\"GenerateReport.php\" method=\"post\">";
echo "<table id='table1' cellspacing='0'>";
echo "<tbody>";
echo "<tr>";
//echo "<tr class='even'>";
//echo "<td>Owner:</td> <td><input type=\"text\" name=\"owner\"></td>";
//echo "</tr>";
//echo "<tr class='even'>";
//echo "<td>Revision:</td> <td><input type=\"text\" name=\"revision\"></td>";
//echo "</tr>";
//$startDate = convertDateFormat(getCurrentDate(),"m/d/Y");//Auto populate start date with today's date
//echo "<td>Start Date (MM/DD/YYYY):</td>     <td><h3>".$startDate."</h3></td>";
//echo "<tr class='even'>";
//echo "<td>End Date (MM/DD/YYYY):</td> <td><input type=\"text\" name=\"end\"></td>";
//echo "</tr>";
echo "<tr>";
//$sql = "SELECT * FROM Status";
$sql = "SELECT ProjectName,ProjectID FROM Projects where Class = 'C3' ORDER BY ProjectID DESC";
echo "<td>Select a game: </td><td><select name=\"projectid\">";
if($results=mysqli_query($link,$sql))
{
	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
	{
		$pName=$row['ProjectName'];
		$pID=$row['ProjectID'];
		echo "<option value='".$pID."'>".$pName."-".$pID."</option>";
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
echo "</select></td>";
echo "</tr>";
echo "</tbody>";
echo "</table>";
echo "<br>";
echo "<input type=\"submit\" value=\"Generate Report\">";
echo "</form>";

//TODO:show this section ONLY if generate report was requested (button click)
if($_GET["pid"]){
	//sleep(1);
	//echo "pid generated!";
	//Get project name after user requests report
	$projID=$_GET["pid"];
	$sql = "SELECT ProjectName FROM Projects where ProjectID = $projID";
	if($results=mysqli_query($link,$sql))
	{
        	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
        	{
                	$pName=$row['ProjectName'];
        	}
	}
	else
	{
        	echo("Error description: " . mysqli_error($link));
	}

        //if status is empty, then it is 'in queue'
        if($_GET["status"] == ""){
                $s = "In Queue";
        }
        else{
                $s = $_GET["status"];
        }

	//show results in a table
	echo "<br>";
	echo "<table style='width:100%' border='1px solid'>";
	echo "<tr>";
	echo "<th>Project Name</th>";
	echo "<th>PID</th>";
	echo "<th>Current Status</th>";
	echo "<th>Original GA</th>";
	echo "<th>Current GA</th>";
	echo "<th>Ga date Changes</th>";
	echo "<th>ITL Rejects</th>";
	echo "</tr>";
	echo "<tr>";
	echo "<td>".$pName."</td>";
	echo "<td>".$_GET["pid"]."</td>";
	echo "<td>".$s."</td>";
	echo "<td>".$_GET["ogGA"]."</td>";
	echo "<td>".$_GET["currGA"]."</td>";
	echo "<td>".$_GET["gaCount"]."</td>";
	echo "<td>".$_GET["itlRejCount"]."</td>";
	echo "</tr>";
	echo "</table>";
	//end of generate report results
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
