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
                <?php
                if(isAdmin($username)){
                        echo "|&nbsp&nbsp;&nbsp;&nbsp<a href=Projects.php>All Projects</a>
                        &nbsp;&nbsp;";
                }
                ?>
	</td>

	<td width="25%" align="right">
	 <?php if(isAdmin($username)){
		echo "Demo Queue | ";
	      //echo "<a href=AddDevProject.php>Add New Project</a> &nbsp;&nbsp;|&nbsp;&nbsp;";
 	     // echo "<a href=AddDevProject.php><img src='Add3.png' width='30' height='30'></a> &nbsp;&nbsp;|&nbsp;&nbsp;";
	  }?>
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
<img src="images/DemoQueue.png"/>
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
	$filter="EndDate";
	$direction=0;
}

echo "<th><a href='DemoReadyQueue.php?filter=EndDate&direction=".$direction."'>Project Name</a></th>";
echo "<th><a href='DemoReadyQueue.php?filter=Owner&direction=".$direction."'>User</a></th>";
echo "<th><a href='DemoReadyQueue.php?filter=demoRequestTime&direction=".$direction."'>DateTime Req</a></th>";
echo "<th><a href='DemoReadyQueue.php?filter=Revision&direction=".$direction."'>Revision</a></th>";
echo "<th><a href='DemoReadyQueue.php?filter=EndDate&direction=".$direction."'>Rev End Date</a></th>";
//if(isAdmin($username))
//{
//	echo "<th>Delete</th>";
//}
echo "</tr>";
echo "<thead>";
$rowCount=0;
echo "<tbody>";

//$id=$_GET['name'];
if($results=mysqli_query($link,"SELECT * FROM ProjectDetails where demoIsChecked = 'Y' ORDER BY ".$filter))
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

		//Run a query to get ProjectName from the Projects table
		$pid = $row["ProjectID"];
		if($results2=mysqli_query($link,"SELECT ProjectName FROM Projects WHERE ProjectID = $pid"))
		{
        		while($row2=mysqli_fetch_array($results2,MYSQLI_ASSOC))
        		{
				echo "<td>".$row2["ProjectName"]."</td>";
			}

		}
		else
		{
        		echo("Error description ProjName Retrieval failed: " . mysqli_error($link));
		}

		//-------------------
		//$projNameResult = mysqli_query($link,"SELECT ProjectName FROM Projects WHERE ProjectID = $pid");
		//if(!$projNameResult)
		//{
    			//die('Invalid query: ' . mysqli_error());
		//	echo("Error description 5: " . mysqli_error($link2));
		//	echo "<td> ERROR: Could not retrieve Project Name</td>";
		//}
		//else
		//{
		//	 echo "<td>".$projNameResult."</td>";
		//}

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
		echo "<td>".$row["Owner"]."</td>";		
		echo "<td>".$row["demoRequestTime"]."</td>";
		echo "<td>".$row["Revision"]."</td>";
		echo "<td>".$row["EndDate"]."</td>";
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

		if(isAdmin($username) || isDEVuser($username))
		{
			//echo "<td><a href='EditDevProject.php?name=".urlencode($row["projectID"])."&table=Projects'><font color='green'>EDIT</font></a></td>";
			//echo "<td><a href='EditDevProject.php?name=".urlencode($row["projectID"])."&table=Projects'><img src='bluePencil.jpg' alt='Delete Row' height='30' width='30'></a></td>";
		}

		if(isAdmin($username)){
			//echo "<td><a href='DeleteDevProject.php?name=".urlencode($row["projectID"])."&table=Projects'><font color='red'>DELETE</font></a></td>";
			echo "<td><a href='removeFromDemoQueue.php?rev=".$row["Revision"]."&projectid=".$row["ProjectID"]."'><img src='images/button4.png' alt='Delete Row' height='30' width='30'></a></td>";
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
