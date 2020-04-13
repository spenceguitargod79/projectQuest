<!DOCTYPE html>
<?php
include('verify.php');
include('siteFuncs.php');
$id=$_GET['name'];
$msID=$_GET['milestoneID'];
//if(!isSQAUser($username))
//{
//    header("location:  DevProjectsList.php");
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
	  <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php><font color=707070>Dev Projects</a>
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
$projectid=$_GET['name'];
//echo "Project Name: ";
if($results=mysqli_query($link,"SELECT name FROM DevProjects WHERE projectID='".$id."' "))
{
	$ProjectName=mysqli_fetch_array($results,MYSQLI_ASSOC);
}
echo $ProjectName["name"];
echo "<br><br>";

$taskid=$_GET['name'];

$sql=("SELECT * FROM DevProjects where projectID='".$id."'");
if($results=mysqli_query($link,$sql))
{
	$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
	$name=$row["name"];
	$descrip=$row["description"];
	$status=$row["status"];
	$projectid=$row["projectID"];
}
else
{
	echo("Error description: " . mysqli_error($link));
}
echo "<form action=\"UpdateDevProjectDetails.php\" method=\"post\">";
echo "<table id='table1' cellspacing='0'>";

	echo "<tr class='even'>";
	echo "<td>Project Name:</td> <td><input type=\"text\" name=\"name\" value=\"".$name."\"></td>";
        echo "</tr>";
	echo "<td>Description:</td> <td><input type=\"text\" name=\"description\" value=\"".$descrip."\"></td>";
        echo "</tr>";

	echo "<tr>";
$sql = "SELECT * FROM DevMilestoneStatus";
echo "<td>Status: </td><td><select name=\"status\">";
if($results=mysqli_query($link,$sql))
{
	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
	{
		$newstatus=$row["status"];
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
echo "</tr>";
	
echo "<input type='hidden' name='projid' value='".$id."'>";
//echo "<input type='hidden' name='milestoneid' value='".$msID."'>";
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
Cadillac Jack 2015</font></p>
<table border="0" width="100%" cellspacing="0" cellpadding="0" background="img/botline.gif">
  <tr>
    <td width="100%"><img border="0" src="img/botline.gif" width="41" height="12"></td>
  </tr>
</table>

</body>

</html>
