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
	  <?php echo "Add Project Details &nbsp;&nbsp;|&nbsp;&nbsp;";?>
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
//echo "Project Name: ";
if($results=mysqli_query($link,"SELECT ProjectName FROM Projects WHERE ProjectID='".$id."' "))
{
	$ProjectName=mysqli_fetch_array($results,MYSQLI_ASSOC);
}
echo $ProjectName["ProjectName"];
echo "<br><br>";

$id=$_GET['name'];
echo "<form action=\"insertProjectDetails.php\" method=\"post\">";
echo "<table id='table1' cellspacing='0'>";
echo "<tbody>";
echo "<tr>";
echo "<tr class='even'>";
echo "<td>Owner:</td> <td><input type=\"text\" name=\"owner\"></td>";
echo "</tr>";
echo "<tr class='even'>";
echo "<td>Revision:</td> <td><input type=\"text\" name=\"revision\"></td>";
echo "</tr>";
$startDate = convertDateFormat(getCurrentDate(),"m/d/Y");//Auto populate start date with today's date
echo "<td>Start Date (MM/DD/YYYY):</td>     <td><h3>".$startDate."</h3></td>";
echo "<tr class='even'>";
echo "<td>End Date (MM/DD/YYYY):</td> <td><input type=\"text\" name=\"end\"></td>";
echo "</tr>";
echo "<tr>";
$sql = "SELECT * FROM Status";
echo "<td>Status: </td><td><select name=\"status\">";
if($results=mysqli_query($link,$sql))
{
	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
	{
		$status=$row['Status'];
		echo "<option value='".$status."'>".$status."</option>";
	}
}
else
{
	echo("Error description: " . mysqli_error($link));
}
echo "</select></td>";
echo "</tr>";
echo "<tr class='even'>";
echo "<td>Notes:</td> <td><input type=\"text\" name=\"notes\"></td>";
echo "</tr>";
echo "<input type='hidden' name='ProjectID' value='".$id."'>";
echo "<input type='hidden' name='StartDate' value='".$startDate."'>";
echo "</tbody>";
echo "</table>";
echo "<br>";
echo "<input type=\"submit\" value=\"Submit\">";
echo "</form>";
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
