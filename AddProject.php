<!DOCTYPE html>
<?php
include('verify.php');
include ('siteFuncs.php');
?>
<html>
<style>
table.complexity {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 50%;
}

table.complexity td, table.complexity th {
  border: 1px dotted #dddddd;
  border-color: green;
  text-align: center;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
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
          <?php echo "&nbsp;&nbsp<a href=ITLList.php>Projects ITL</a>
          &nbsp;&nbsp;|&nbsp;&nbsp;";?>
	  <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php>Dev Projects</a>
	  &nbsp;&nbsp;";?>
	</td>
	<td width="25%" align="right">
	  <?php echo "Add Project &nbsp;&nbsp;|&nbsp;&nbsp;";?>
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
<center><img src="images/newProject.png"/></center>
<div id='div1'>
<?php

$link=sqlConnect();

echo "<form action=\"updateNewProject.php\" method=\"post\">";
echo "<table id='table1' cellspacing='0'>";
echo "<tbody>";
echo "<tr class='even'>";
echo "<td>Project Name:</td> <td><input type=\"text\" name=\"name\"></td>";
echo "</tr>";
echo "<tr>";
$sql = "SELECT * FROM ProjectType";
echo "<td>Project Type: </td><td><select name=\"type\">";
if($results=mysqli_query($link,$sql))
{
	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
	{
		$projecttype=$row['ProjectType'];
		echo "<option value='".$projecttype."'>".$projecttype."</option>";
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
		$market=$row['Market'];
		echo "<option value='".$market."'>".$market."</option>";
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
echo "</select></td>";
echo "</tr>";

//Project complexity input
echo "<tr>";
$sql = "SELECT * FROM GADuration";
echo "<td>Complexity: </td><td><select name=\"complexityID\">";
if($results=mysqli_query($link,$sql))
{
        while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
        {
                $complexity=$row['complexityID'];
                echo "<option value='".$complexity."'>".$complexity."</option>";
        }
}
else
{
        echo("Error description: " . mysqli_error($link));
}
echo "</select></td>";
echo "</tr>";
//-----------------------------

//Studio drop list
echo "<tr>";
$sql = "SELECT name FROM Studios";
echo "<td>Studio: </td><td><select name=\"studioname\">";
if($results=mysqli_query($link,$sql))
{
        while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
        {
                $name=$row['name'];
                echo "<option value='".$name."'>".$name."</option>";
        }
}
else
{
        echo("Studio Error description: " . mysqli_error($link));
}
echo "</select></td>";
echo "</tr>";
//------------------------------

echo "<tr class='even'>";
echo "<td>Project Details:</td> <td><input type=\"text\" name=\"details\"></td>";
echo "</tr>";

echo "<tr class='even'>";
echo "<td>Handoff Date (MM/DD/YYYY):</td> <td><input type=\"text\" name=\"handoff\"></td>";
echo "</tr>";

//echo "<tr class='even'>";
//echo "<td>Estimated Start Date (MM/DD/YYYY):</td> <td><input type=\"text\" name=\"end\"></td>";
//echo "</tr>";

echo "</tbody>";
echo "</table>";
echo "<br>";
echo "<input type=\"submit\" value=\"Submit\">";
echo "</form>";

//------ LEGEND OF COMPLEXITIES ------
echo "<br>";
echo "<br>";
echo "<table class ='complexity' align='center'>";
echo "<caption><b>THE LEGEND OF COMPLEXITIES</b></caption>";
  echo "<tr>";
   echo " <th>Complexity</th>";
    echo "<th>Description</th>";
    echo "<th>SQA Start Days</th>";
    echo "<th>SQA Complete Days</th>";
    echo "<th>ITL Complete Days</th>";
    echo "<th>Total Days</th>";
  echo "</tr>";
//Show all rows from GADuration table
if($results=mysqli_query($link,"SELECT * FROM GADuration"))
{
        while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
        {
                echo "<tr>";
                echo "<td>".$row["complexityID"]."</td>";
                echo "<td>".$row["complexityDescription"]."</td>";
                echo "<td>".$row["sqaStart"]."</td>";
                echo "<td>".$row["sqaComplete"]."</td>";
                echo "<td>".$row["itlComplete"]."</td>";
		$totalDays= $row["sqaComplete"] + $row["sqaStart"] +$row["itlComplete"];
                echo "<td>".$totalDays."</td>";
		echo "</tr>";
	}
}
else
{
        echo("Error description: " . mysqli_error($link));
}

echo "</table>";

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
