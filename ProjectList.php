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
      <p align="center" style="font-family:Arial, Helvetica, sans-serif; color:white;"><font size="5"><b>SQA Project Dashboard</b></font>
	<a href="ReleaseForecast.php"><img src="images/PTSUN.png" align="center" alt="AGS" width="80" height="50"></a></td>
  </tr>
</table>
<table id='table2' border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#123456">
  <tr valign="bottom">
    <td width="75%">
		<?php echo "&nbsp;&nbsp<a href=ProjectList.php><font color=707070>Projects In Progress</font></a>
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
	<td width="25%" align="right"><font color=707070>
	  <?php if(false){echo "<a href=admins.php>Admins</a>
	  &nbsp;&nbsp;|&nbsp;";} echo $username."  | ";
	  $group="";
	  if(isAdmin($username) || isSQAuser($username))
	  {
		//echo "Administrator";
		echo '<a href="DemoReadyQueue.php?projectname='.$username.'"><img src="images/DQ.png" width="30" height="25" /></a>';
	  }
	  else
	  {
		//echo "Guest";
	  }
	  ?></font>
      <?php echo "|&nbsp;&nbsp; <a href=logout.php>Logout</a> &nbsp;&nbsp;&nbsp;&nbsp;"; ?>
	</td>
  </tr>
</table>
<br><br>
<?php
//Filters
echo '<center>';
echo '<a href="ProjectList.php?studioname=turbo"> TURBO</a>';
echo '<a href="ProjectList.php?studioname=terminus"> | TERMINUS</a>';
echo '<a href="ProjectList.php?studioname=austin"> | AUSTIN</a>';
echo '<a href="ProjectList.php?studioname=sydney 1"> | SYDNEY 1</a>';
echo '<a href="ProjectList.php?studioname=sydney 2"> | SYDNEY 2</a>';
echo '<a href="ProjectList.php?studioname=allstudios"> | ALL</a>';
echo '</center>';
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $agent = $_SERVER['HTTP_USER_AGENT'];
}
$showLive=false;
if (strlen(strstr($agent, 'Firefox')) > 0) {
    $showLive = true;
}
//echo "<br>";
echo "<div class='header-layout'>";
echo "<div class='header'>";
echo "<table id='table1' cellspacing='0'>";
echo "<thead>";
echo "<tr>";

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
//echo "<th id='projectnameH'><a href='ProjectList.php?filter=ProjectName&direction=".$direction."'>Project Name</a></th>";
echo "<th id='projectnameH'><a href='ProjectList.php?studioname=".$studioFilter."&filter=ProjectName&direction=".$direction."'>Project Name</a></th>";
echo "<th id='projecttypeH'><a href='ProjectList.php?studioname=".$studioFilter."&filter=ProjectType&direction=".$direction."'>Project Type</a></th>";
echo "<th id='marketH'><a href='ProjectList.php?studioname=".$studioFilter."&filter=Class&direction=".$direction."'>Market</a></th>";
echo "<th id='ownerH'><a href='ProjectList.php?studioname=".$studioFilter."&filter=Owner&direction=".$direction."'>Owner</a></th>";
//echo "<th id='hodateH'><a href='ProjectList.php?filter=HandoffDate&direction=".$direction."'>HandoffDate&nbsp&nbsp</a></th>";
echo "<th id='hodateH'><a href='ProjectList.php?studioname=".$studioFilter."&filter=studio&direction=".$direction."'>Studio&nbsp&nbsp</a></th>";
echo "<th id='stdateH'><a href='ProjectList.php?studioname=".$studioFilter."&filter=StartDate&direction=".$direction."'>Start Date&nbsp&nbsp&nbsp&nbsp</a></th>";
echo "<th id='enddateH'><a href='ProjectList.php?studioname=".$studioFilter."&filter=EndDate&direction=".$direction."'>Rev End Date&nbsp&nbsp</a></th>";
echo "<th id='sqareleaseH'><a href='ProjectList.php?studioname=".$studioFilter."&filter=actualSQAComplete&direction=".$direction."'>SQA Rel Date&nbsp&nbsp</a></th>";
echo "<th id='gadateH'><a href='ProjectList.php?studioname=".$studioFilter."&filter=actualITLComplete&direction=".$direction."'>Est GA Date&nbsp&nbsp</a></th>";
//echo "<th><a href='ProjectList.php?filter=gaTargetHealth&direction=".$direction."'>On Target</a></th>";
echo "<th id='statusH'><a href='ProjectList.php?studioname=".$studioFilter."&filter=Status&direction=".$direction."'>Status&nbsp&nbsp</a></th>";
//echo "<th><a href='ProjectList.php?filter=Revision&direction=".$direction."'>Revision</a></th>";
echo "<th id='detailsH'><a href='ProjectList.php?studioname=".$studioFilter."&filter=AdditionalProjectDetails&direction=".$direction."'><center>Details&nbsp&nbsp&nbsp&nbsp</center></a></th>";
//if(isAdmin($username))
//{
//	echo "<th>Delete</th>";
//}
echo "</tr>";
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
//$id=$_GET['name'];

//STUDIO FILTER LOGIC
if($studioFilter == "" || $studioFilter == "allstudios"){
	//set $sql to original query that shows all projects
	$results=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete, Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status, Tbl1.studio FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status, Projects.studio, ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName, ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID WHERE Tbl1.Status = 'Testing' OR Tbl1.Status = 'Rejected' OR Tbl1.Status = 'ITL Reject' OR Tbl1.Status = 'Field Reject' ORDER BY ".$filter);
}
else{
	//pass $studioFilter into query as the studio
	$results=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete, Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status, Tbl1.studio FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.HandoffDate, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status, Projects.studio AS studio, ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID in (Select MAX(ProjectDetails.TaskID) from ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName, ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID WHERE (Tbl1.Status = 'Testing' OR Tbl1.Status = 'Rejected' OR Tbl1.Status = 'ITL Reject' OR Tbl1.Status = 'Field Reject') AND (Tbl1.studio = '$studioFilter') ORDER BY ".$filter);
}

if($results)
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
		if(isRejected($row["Status"]) || isFieldRejected($row["Status"]) || isITLRejected($row["Status"]))
		{
			echo "<td id='projectnameR'><a href='ProjectDetails.php?name=".$row["ProjectID"]."'><font color='red'>".$row["ProjectName"]."</font></a></td>";
			displayField($row["ProjectType"], "red", false, "ProjectType");
			displayField($row["Class"], "red", false, "Class");
			displayField($row["Owner"], "red", false, "Owner");
			//displayField(convertFromSQLDate($row["HandoffDate"]), "red", false, "HandoffDate");
			displayField($row["studio"], "red", false, "studio");
			displayField(convertFromSQLDate($row["StartDate"]), "red", false, "StartDate");
			displayField(convertFromSQLDate($row["EndDate"]), "red", false, "EndDate");
			displayField(convertFromSQLDate($row["actualSQAComplete"]), "red", false, "actualSQAComplete");
			displayField(convertFromSQLDate($row["actualITLComplete"]), "red", true, "actualITLComplete");
		}
		else
		{
			echo "<td id='projectnameD'><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
			echo "<td id='projecttypeD'>".$row["ProjectType"]."</td>";
                	echo "<td id='marketD'>".$row["Class"]."</td>";
                	echo "<td id='ownerD'>".$row["Owner"]."</td>";
                	//echo "<td id='hodateD'>".convertFromSQLDate($row["HandoffDate"])."</td>";
                        echo "<td id='hodateD'>".$row["studio"]."</td>";
                	echo "<td id='stdateD'>".convertFromSQLDate($row["StartDate"])."</td>";
                	echo "<td id='enddateD'>".convertFromSQLDate($row["EndDate"])."</td>";
                	echo "<td id='sqareleaseD'>".convertFromSQLDate($row["actualSQAComplete"])."</td>";
                	echo "<td id='gadateD'>".convertFromSQLDate($row["actualITLComplete"])."</td>";
		}
		//echo "<td>".$row["ProjectType"]."</td>";		
		//echo "<td>".$row["Class"]."</td>";
		//echo "<td>".$row["Owner"]."</td>";
		//echo "<td>".convertFromSQLDate($row["HandoffDate"])."</td>";		
		//echo "<td>".convertFromSQLDate($row["StartDate"])."</td>";
		//echo "<td>".convertFromSQLDate($row["EndDate"])."</td>";
		//echo "<td>".convertFromSQLDate($row["actualSQAComplete"])."</td>";
		//echo "<td>".convertFromSQLDate($row["actualITLComplete"])."</td>";

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
		if(isRejected($row["Status"]) || isFieldRejected($row["Status"]) || isITLRejected($row["Status"])){
			//echo "<td><font color='red'>".$row["Status"]."</font></td>";
			displayField($row["Status"], "red", false,"Status");
		}
		else{
			echo "<td id='statusD'>".$row["Status"]."</td>";
		}
		//echo "<td>".$row["Revision"]."</td>";
		if(isRejected($row["Status"]) || isFieldRejected($row["Status"]) || isITLRejected($row["Status"])){
			displayDetailsField($row["AdditionalProjectDetails"], "red");

		}else{
			echo "<td id='detailsD'>".$row["AdditionalProjectDetails"]."</td>";
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

mysqli_close($link);
?>
</div>
</div>


<center><a href="logout.php"><img src="images/sqalogo-loop.gif" alt="AGS"></a></center>
<center><i><a href="ReportTool.php">Report Tool</a></i></center>
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

function isFieldRejected($status){
        if($status == "Field Reject"){
                return true;
        }
        else{
                return false;
        }
}

function isITLRejected($status){
        if($status == "ITL Reject"){
                return true;
        }
        else{
                return false;
        }
}

function displayField($value, $color, $showStrikeThrough, $category){
	if($showStrikeThrough){
		echo "<td id='gadateD'><strike><font color='$color'>".$value."</font></strike></td>";
	}
	else{
		if($category == "Class"){
			echo "<td id='marketRej'><font color='$color'>".$value."</font></td>";
		}
		else if($category == "Owner"){
			echo "<td id='ownerRej'><font color='$color'>".$value."</font></td>";
		}
		else if($category == "EndDate"){
                        echo "<td id='enddateRej'><font color='$color'>".$value."</font></td>";
                }
		else if($category == "actualSQAComplete"){
                        echo "<td id='sqareleaseRej'><font color='$color'>".$value."</font></td>";
	        }
		else if($category == "ProjectType"){
                        echo "<td id='projecttypeD'><font color='$color'>".$value."</font></td>";
                }
                else if($category == "studio"){
                        echo "<td id='hodateD'><font color='$color'>".$value."</font></td>";
                }
                else if($category == "StartDate"){
                        echo "<td id='stdateD'><font color='$color'>".$value."</font></td>";
                }
		else if($category == "actualITLComplete"){
                        echo "<td id='gadateD'><font color='$color'>".$value."</font></td>";
                }
                else if($category == "Status"){
                        echo "<td id='statusD'><font color='$color'>".$value."</font></td>";
                }
		else{
			echo "<td><font color='$color'>".$value."</font></td>";
		}
	}
}

function displayDetailsField($value, $color){
      echo "<td id='detailsD'><font color='$color'>".$value."</font></td>";
}
?>
