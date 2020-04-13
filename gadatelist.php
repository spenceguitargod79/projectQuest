<!DOCTYPE html>
<?php
include('verify.php');
include ('siteFuncs.php');
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
          <?php echo "&nbsp;&nbsp<a href=ITLList.php>Projects ITL</a>
          &nbsp;&nbsp;|&nbsp;&nbsp;";?>
	  <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php>Dev Projects</a>
	  &nbsp;&nbsp;";?>
	</td>
	<td width="25%" align="right"><font color=707070>
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
echo "<br>";
echo "<table id='table1' cellspacing='0'>";
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

echo "<th><a href='gadatelist.php?filter=ProjectName&direction=".$direction."'>Project Name</a></th>";
echo "<th><a href='gadatelist.php?filter=ProjectType&direction=".$direction."'>Project Type</a></th>";
echo "<th><a href='gadatelist.php?filter=Class&direction=".$direction."'>Market</a></th>";
echo "<th><a href='gadatelist.php?filter=complexityID&direction=".$direction."'>Complexity</a></th>";
echo "<th><a href='gadatelist.php?filter=Status&direction=".$direction."'>Status</a></th>";
echo "<th><a href='gadatelist.php?filter=HandoffDate&direction=".$direction."'>Handoff Date</a></th>";
echo "<th><a href='gadatelist.php?filter=EstimatedStartDate&direction=".$direction."'>Est SQA Start</a></th>";
echo "<th><a href='gadatelist.php?filter=actualStartDate&direction=".$direction."'>Act Start Date</a></th>";
echo "<th><a href='gadatelist.php?filter=estSQAComplete&direction=".$direction."'>Est SQA Complete</a></th>";
echo "<th><a href='gadatelist.php?filter=actualSQAComplete&direction=".$direction."'>Act SQA Complete</a></th>";
echo "<th><a href='gadatelist.php?filter=estITLComplete&direction=".$direction."'>Est ITL Complete</a></th>";
echo "<th><a href='gadatelist.php?filter=actualITLComplete&direction=".$direction."'>Act ITL Complete/GA</a></th>";
//if(isAdmin($username))
//{
//	echo "<th>Delete</th>";
//}
echo "</tr>";
echo "<thead>";
$rowCount=0;
echo "<tbody>";

//$id=$_GET['name'];
if($results=mysqli_query($link,"SELECT Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.complexityID, Tbl1.EstimatedStartDate, Tbl1.actualStartDate, Tbl1.estSQAComplete, Tbl1.actualSQAComplete, Tbl1.estITLComplete, Tbl1.actualITLComplete, Tbl1.Status FROM (SELECT Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.HandoffDate, Projects.complexityID, Projects.EstimatedStartDate, Projects.actualStartDate, Projects.estSQAComplete, Projects.actualSQAComplete, Projects.estITLComplete, Projects.actualITLComplete, ProjectDetails.Status AS Status FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID WHERE Tbl1.Status = 'Testing' OR Tbl1.Status = 'Rejected' OR Tbl1.Status = 'On-Hold' OR Tbl1.Status = 'Obsoleted' OR Tbl1.Status = 'ITL' ORDER BY ".$filter))
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
		//$epsUser=$row["epsUser"];
	        //echo "<td><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
		echo "<td>".$row['ProjectName']."</td>";
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
		echo "<td>".$row["ProjectType"]."</td>";
		echo "<td>".$row["Class"]."</td>";
		echo "<td>".$row["complexityID"]."</td>";
		echo "<td>".$row["Status"]."</td>";
		echo "<td>".convertFromSQLDate($row["HandoffDate"])."</td>";
		echo "<td>".convertFromSQLDate($row["EstimatedStartDate"])."</td>";
		echo "<td>".convertFromSQLDate($row["actualStartDate"])."</td>";
		echo "<td>".convertFromSQLDate($row["estSQAComplete"])."</td>";
		echo "<td>".convertFromSQLDate($row["actualSQAComplete"])."</td>";
		echo "<td>".convertFromSQLDate($row["estITLComplete"])."</td>";
		echo "<td>".convertFromSQLDate($row["actualITLComplete"])."</td>";//AKA 'GA Date'
		
		//if(isAdmin($username))
		//{
		//	echo "<td><a href='adminDelete.php?name=".urlencode($row["name"])."&table=eps'>Delete</a></td>";
		//}
		
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
