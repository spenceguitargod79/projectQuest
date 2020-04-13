<!DOCTYPE html>
<?php
include('verify.php');
include ('siteFuncs.php');
$link=sqlConnect();
$id=$_GET['projID'];
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
	  <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php><font color=707070>Dev Projects</a>
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

<?php
//Show project name for the selected project
if($results=mysqli_query($link,"SELECT name FROM DevProjects WHERE ProjectID='".$id."' "))
{
	$ProjectName=mysqli_fetch_array($results,MYSQLI_ASSOC);
}
echo "<center>".$ProjectName["name"]." Milestones"."</center>";
?>

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

//Get the projectID before the user sorts column and send through ahref
//$id=$_GET['projID'];

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
	$filter="version";
	$direction=0;
}

echo "<th><a href='DevMilestonesList.php?projID=$id&filter=name&direction=".$direction."'>Milestone Name</a></th>";
echo "<th><a href='DevMilestonesList.php?projID=$id&filter=description&direction=".$direction."'>Description</a></th>";
echo "<th><a href='DevMilestonesList.php?projID=$id&filter=assignee&direction=".$direction."'>Assignee</a></th>";
echo "<th><a href='DevMilestonesList.php?projID=$id&filter=startDate&direction=".$direction."'>Start Date</a></th>";
echo "<th><a href='DevMilestonesList.php?projID=$id&filter=deployedDate&direction=".$direction."'>Deployed Date</a></th>";
echo "<th><a href='DevMilestonesList.php?projID=$id&filter=notes&direction=".$direction."'>Notes</a></th>";
echo "<th><a href='DevMilestonesList.php?projID=$id&filter=status&direction=".$direction."'>Status</a></th>";
echo "<th><a href='DevMilestonesList.php?projID=$id&filter=priority&direction=".$direction."'>Priority</a></th>";
echo "<th><a href='DevMilestonesList.php?projID=$id&filter=version&direction=".$direction."'>Version</a></th>";

//if(isAdmin($username))
//{
//	echo "<th>Delete</th>";
//}
echo "</tr>";
echo "<thead>";
$rowCount=0;
echo "<tbody>";

//$id=$_GET['name'];
if($results=mysqli_query($link,"SELECT * FROM DevMilestones where projectID='$id' ORDER BY ".$filter))
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
		//Only allow Admin and dev users to edit milestone details
		if(isAdmin($username)){
			echo "<td><a href='EditMilestoneDetails.php?name=".$row["projectID"]."&milestoneID=".$row["milestoneID"]."'>".$row["name"]."</a></td>";
		}
		else{
			echo "<td>".$row["name"]."</td>";
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
		//echo "<td>".$row["name"]."</td>";
		echo "<td>".$row["description"]."</td>";		
		echo "<td>".$row["assignee"]."</td>";
		echo "<td>".convertFromSQLDate($row["startDate"])."</td>";		
		echo "<td>".convertFromSQLDate($row["deployedDate"])."</td>";
		echo "<td>".$row["notes"]."</td>";
		echo "<td>".$row["status"]."</td>";
		echo "<td>".$row["priority"]."</td>";
		echo "<td>".$row["version"]."</td>";
		//if(isAdmin($username))
		//{
		//	echo "<td><a href='adminDelete.php?name=".urlencode($row["name"])."&table=eps'>Delete</a></td>";
		//}

		if(isAdmin($username)){
			echo "<td><a href='DeleteDevMilestone.php?name=".urlencode($row["projectID"])."&table=Projects'><img src='TrashCan.png' alt='Delete Row' height='30' width='30'></a></td>";
		}
		
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

if(isAdmin($username)){
    echo "<a href=AddDevMilestone.php?name=".$id.">Add Milestone</a>";
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
