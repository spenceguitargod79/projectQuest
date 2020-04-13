<!DOCTYPE html>
<?php
include('verify.php');
include ('siteFuncs.php');
$link=sqlConnect();
$id=$_GET['name'];
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
      <p align="center" style="font-family:Arial, Helvetica, sans-serif; color:white;"><font size="5"><b>SQA Project Dashboard</b></font>
		<a href="Projects.php"><img src="AGS-6.gif" align="center" alt="AGS"></a></td>
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

	<td width="25%" align="right">
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
	$filter="dateOfChange";
	$direction=0;
}
if($results=mysqli_query($link,"SELECT ProjectName FROM Projects WHERE ProjectID='".$id."' "))
{
        $ProjectName=mysqli_fetch_array($results,MYSQLI_ASSOC);
}
echo "<h2>".$ProjectName["ProjectName"]."</h2>";
//echo "<br>";

echo "<h3> Project History </h3>";
echo "<th><a href='ProjectHistoryList.php?name=$id&filter=user&direction=".$direction."'>User</a></th>";
echo "<th><a href='ProjectHistoryList.php?name=$id&filter=dateOfChange&direction=".$direction."'>Date</a></th>";
echo "<th><a href='ProjectHistoryList.php?name=$id&filter=timeOfChange&direction=".$direction."'>Time</a></th>";
echo "<th><a href='ProjectHistoryList.php?name=$id&filter=changeType&direction=".$direction."'>Changed</a></th>";
echo "<th><a href='ProjectHistoryList.php?name=$id&filter=changeType&direction=".$direction."'>From</a></th>";
echo "<th><a href='ProjectHistoryList.php?name=$id&filter=changeType&direction=".$direction."'>To</a></th>";
echo "<th><a href='ProjectHistoryList.php?name=$id&filter=note&direction=".$direction."'>Note</a></th>";
//if(isAdmin($username))
//{
//	echo "<th>Delete</th>";
//}
echo "</tr>";
echo "<thead>";
$rowCount=0;
echo "<tbody>";
//$id=$_GET['name'];
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
		//$epsUser=$row["epsUser"];
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
                        case "EstimatedITLComplete":
                                echo "<td>".$row["estItlCmpOld"]."</td>";
                                echo "<td>".$row["estItlCmpNew"]."</td>";
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
			case (preg_match('/actSQARel.*/', $row["changeType"])? true: false):
                                echo "<td>".$row["actSqaCmpOld"]."</td>";
                                echo "<td>".$row["actSqaCmpNew"]."</td>";
                                break;
			case (preg_match('/actITLRel.*/', $row["changeType"])? true: false):
                                echo "<td>".$row["actItlCmpOld"]."</td>";
                                echo "<td>".$row["actItlCmpNew"]."</td>";
                                break;
    			default:
                                echo "<td>ERROR</td>";
                                echo "<td>ERROR</td>";
		}

		//Show the history's note here
		echo "<td>".$row["note"]."</td>";

		//echo "<td>".$row["Owner"]."</td>";
		//echo "<td>".convertFromSQLDate($row["HandoffDate"])."</td>";		
		//echo "<td>".convertFromSQLDate($row["StartDate"])."</td>";
		//echo "<td>".convertFromSQLDate($row["EndDate"])."</td>";
		//echo "<td>".$row["Status"]."</td>";
		//echo "<td>".$row["Revision"]."</td>";
		//echo "<td>".$row["AdditionalProjectDetails"]."</td>";

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
