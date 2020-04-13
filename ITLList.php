<!DOCTYPE html>
<?php
include('verify.php');
include ('siteFuncs.php');
$link=sqlConnect();
$studioFilter = $_GET['studioname'];
//echo "STUDIO = ".$studioFilter;
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
                <?php echo "&nbsp;&nbsp<a href=ITLList.php><font color=707070>Projects In ITL</font></a>
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
//Filters
echo '<center>';
echo '<a href="ITLList.php?studioname=turbo"> TURBO</a>';
echo '<a href="ITLList.php?studioname=terminus"> | TERMINUS</a>';
echo '<a href="ITLList.php?studioname=austin"> | AUSTIN</a>';
echo '<a href="ITLList.php?studioname=sydney 1"> | SYDNEY 1</a>';
echo '<a href="ITLList.php?studioname=sydney 2"> | SYDNEY 2</a>';
echo '<a href="ITLList.php?studioname=allstudios"> | ALL</a>';
echo '</center>';
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
//echo "<br>";
echo "<div class='header-layout'>";
echo "<div class='header'>";
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

echo "<th id='projectnameH4'><a href='ITLList.php?studioname=".$studioFilter."&filter=ProjectName&direction=".$direction."'>Project Name</a></th>";
echo "<th id='projecttypeH4'><a href='ITLList.php?studioname=".$studioFilter."&filter=ProjectType&direction=".$direction."'>&ensp;&emsp;&emsp;&ensp;&ensp;&ensp;Type</a></th>";
echo "<th id='marketH4'><a href='ITLList.php?studioname=".$studioFilter."&filter=Class&direction=".$direction."'>&ensp;&emsp;&emsp;&emsp;&emsp;&emsp;Market</a></th>";
echo "<th id='ownerH4'><a href='ITLList.php?studioname=".$studioFilter."&filter=Owner&direction=".$direction."'>&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;Owner</a></th>";
echo "<th id='hodateH3'><a href='ITLList.php?studioname=".$studioFilter."&filter=studio&direction=".$direction."'>&emsp;&emsp;&emsp;&emsp;Studio&ensp;</a></th>";
//echo "<th id='hodateH4'><a href='ITLList.php?filter=HandoffDate&direction=".$direction."'>Handoff Date</a></th>";
//echo "<th id='stdateH4'><a href='ITLList.php?filter=StartDate&direction=".$direction."'>Start Date</a></th>";
//echo "<th id='enddateH4'><a href='ITLList.php?filter=EndDate&direction=".$direction."'>Rev End Date</a></th>";
echo "<th id='sqareleaseH4'><a href='ITLList.php?studioname=".$studioFilter."&filter=actualSQAComplete&direction=".$direction."'>&emsp;SQARelDate&emsp;&emsp;</a></th>";
echo "<th id='gadateH4'><a href='ITLList.php?studioname=".$studioFilter."&filter=actualITLComplete&direction=".$direction."'>EstGADate&ensp;</a></th>";
//echo "<th><a href='ITLList.php?filter=gaTargetHealth&direction=".$direction."'>On Target</a></th>";
echo "<th id='statusH4'><a href='ITLList.php?studioname=".$studioFilter."&filter=Status&direction=".$direction."'>Status&emsp;</a></th>";
//echo "<th><a href='ProjectList.php?filter=Revision&direction=".$direction."'>Revision</a></th>";
echo "<th id='detailsH4'><a href='ITLList.php?studioname=".$studioFilter."&filter=AdditionalProjectDetails&direction=".$direction."'>Details&emsp;&emsp;&emsp;&emsp;</a></th>";
//if(isAdmin($username))
//{
//	echo "<th>Delete</th>";
//}
echo "</tr>";
//echo "<thead>";
echo "</thead>";
echo "</table>";

echo "</div>";//layout div
echo "</div>";//container div
?>

<?php
echo "<div class='layout'>";
echo "<div class='container'>";
echo "<table id='table1' cellspacing='0'>";
$rowCount=0;
echo "<tbody>";

//need a seperate query so we can check if zero games are at the lab or not.
//It seems mysqli_fetch_array can only be ran 1 time on a result
$resultsCheck=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete, Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status, Tbl1.studio FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status, Projects.studio, ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName, ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID WHERE Tbl1.Status = 'ITL' ORDER BY ".$filter);

//STUDIO FILTER LOGIC
if($studioFilter == "" || $studioFilter == "allstudios"){
	//set $sql to original query that shows all projects
	$results=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete, Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status, Tbl1.studio FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status, Projects.studio, ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName, ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID WHERE Tbl1.Status = 'ITL' ORDER BY ".$filter);
}
else{
	//pass $studioFilter into query as the studio
	$results=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete, Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status, Tbl1.studio FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status, Projects.studio AS studio, ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName, ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from ProjectDetails Group By ProjectDetails.ProjectID)) AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID WHERE (Tbl1.Status = 'ITL') AND (Tbl1.studio = '$studioFilter') ORDER BY ".$filter);
}

//$id=$_GET['name'];
if($results)
//if($results=mysqli_query($link,"SELECT * FROM Projects"))
{
	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
	{
		//echo "Size of array: ".count($row);
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
		//echo "<td>".$row["ProjectName"]."</td>";
		if(isRejected($row["Status"]))
		{
			echo "<td><a href='ProjectDetails.php?name=".$row["ProjectID"]."'><font color='red'>".$row["ProjectName"]."</font></a></td>";
			displayField($row["ProjectType"], "red");
			displayField($row["Class"], "red");
			displayField($row["Owner"], "red");
			//displayField(convertFromSQLDate($row["HandoffDate"]), "red");
			//displayField(convertFromSQLDate($row["StartDate"]), "red");
			//displayField(convertFromSQLDate($row["EndDate"]), "red");
			displayField(convertFromSQLDate($row["actualSQAComplete"]), "red");
			displayField(convertFromSQLDate($row["actualITLComplete"]), "red");
		}
		else
		{
			echo "<td id='projectnameD4'><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
			echo "<td id='projecttypeD4'>".$row["ProjectType"]."</td>";
                	echo "<td id='marketD4'>".$row["Class"]."</td>";
                	echo "<td id='ownerD4'>".$row["Owner"]."</td>";
			echo "<td id='hodateD3'>".$row["studio"]."</td>";
                	//echo "<td id='hodateD4'>".convertFromSQLDate($row["HandoffDate"])."</td>";
                	//echo "<td id='stdateD4'>".convertFromSQLDate($row["StartDate"])."</td>";
                	//echo "<td id='enddateD4'>".convertFromSQLDate($row["EndDate"])."</td>";
                	echo "<td id='sqareleaseD4'>".convertFromSQLDate($row["actualSQAComplete"])."</td>";
                	echo "<td id='gadateD4'>".convertFromSQLDate($row["actualITLComplete"])."</td>";
		}
		//Display target health icon alongside the ga date
		/*$gaTargetHealth = $row["gaTargetHealth"];
		if($gaTargetHealth == 1)//good
		{
			echo "<td><img src=\"good.jpg\" alt=\"all good\" width=\"15\" height=\"15\"></td>";
		}
		else if($gaTargetHealth == 2)//caution
		{
			echo "<td><img src=\"warning.png\" alt=\"Proceed with caution\" width=\"15\" height=\"15\"></td>";
		}
		else if($gaTargetHealth == 3)//danger
		{
			echo "<td><img src=\"screwed.jpg\" alt=\"shit!\" width=\"15\" height=\"15\"></td>";
		}
		else
		{
			//show on track if the field doesn't have a value yet
			echo "<td><img src=\"good.jpg\"  alt=\"all good\"  width=\"15\" height=\"15\"></td>";
		}*/
		//if status is rejected then make text display red
		if(isRejected($row["Status"])){
			//echo "<td><font color='red'>".$row["Status"]."</font></td>";
			displayField($row["Status"], "red");
		}
		else{
			echo "<td id='statusD4'>".$row["Status"]."</td>";
		}
		//echo "<td>".$row["Revision"]."</td>";
		if(isRejected($row["Status"])){
			displayField($row["AdditionalProjectDetails"], "red");

		}else{
			echo "<td id='detailsD4'>".$row["AdditionalProjectDetails"]."</td>";
		}

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

	//show if no games are currently ITL
        if(count($rowCheck=mysqli_fetch_array($resultsCheck,MYSQLI_ASSOC)) == 0){
                //echo "No games in ITL!!!";
                //display image
                echo "<img src=\"nogamesITL.png\" alt=\"No games in Lab\" width=\"500\" height=\"375\">";
        }
        else{
                //echo "Some games are in the lab!";
        }

mysqli_close($link);
?>
</div>
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

<?php
//Functions----------------------------
function isRejected($status){
	if($status == "Rejected"){
		return true;
	}
	else{
		return false;
	}
}

function displayField($value, $color){
	echo "<td><font color='$color'>".$value."</font></td>";
}

?>

