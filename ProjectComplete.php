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
	  <?php echo "&nbsp;&nbsp<a href=ProjectComplete.php><font color=707070>Projects Complete</font></a>
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
echo '<a href="ProjectComplete.php?studioname=turbo"> TURBO</a>';
echo '<a href="ProjectComplete.php?studioname=terminus"> | TERMINUS</a>';
echo '<a href="ProjectComplete.php?studioname=austin"> | AUSTIN</a>';
echo '<a href="ProjectComplete.php?studioname=sydney 1"> | SYDNEY 1</a>';
echo '<a href="ProjectComplete.php?studioname=sydney 2"> | SYDNEY 2</a>';
echo '<a href="ProjectComplete.php?studioname=allstudios"> | ALL</a>';
echo '</center>';
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $agent = $_SERVER['HTTP_USER_AGENT'];
}
$showLive=false;
if (strlen(strstr($agent, 'Firefox')) > 0) {
    $showLive = true;
}

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

echo "<th id='projectnameH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=ProjectName&direction=".$direction."'>ProjectName</a></th>";
echo "<th id='projecttypeH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=ProjectType&direction=".$direction."'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspType</a></th>";
echo "<th id='marketH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=Class&direction=".$direction."'>&nbsp&nbsp&nbsp&nbspMarket&nbsp&nbsp&nbsp&nbsp</a></th>";
echo "<th id='ownerH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=Owner&direction=".$direction."'>&nbsp&nbsp&nbsp&nbsp&nbspOwner&nbsp&nbsp</a></th>";
echo "<th id='studioH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=studio&direction=".$direction."'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspStudio</a></th>";
echo "<th id='hodateH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=HandoffDate&direction=".$direction."'>&nbspHandoffDate</a></th>";
echo "<th id='stdateH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=StartDate&direction=".$direction."'>StartDate</a></th>";
echo "<th id='enddateH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=EndDate&direction=".$direction."'>EndDate</a></th>";
echo "<th id='sqareleaseH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=actualSQAComplete&direction=".$direction."'>SQARelDate</a></th>";
echo "<th id='gadateH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=actualITLComplete&direction=".$direction."'>GADate</a></th>";
echo "<th id='statusH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=Status&direction=".$direction."'>&nbsp&nbspStatus</a></th>";
echo "<th id='revisionH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=Revision&direction=".$direction."'>&nbspRevision</a></th>";
echo "<th id='detailsH3'><a href='ProjectComplete.php?studioname=".$studioFilter."&filter=AdditionalProjectDetails&direction=".$direction."'>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspDetails&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a></th>";
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

//STUDIO FILTER LOGIC
if($studioFilter == "" || $studioFilter == "allstudios"){
	//set $sql to original query that shows all projects
	$results=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl1.Revision, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete, Tbl1.actualITLComplete, Tbl1.Status, Tbl1.studio FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.HandoffDate, ProjectDetails.Revision AS Revision, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, ProjectDetails.Status AS Status, Projects.studio, ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName, ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID WHERE Tbl1.Status = 'Approved' OR Tbl1.Status = 'Obsoleted' ORDER BY ".$filter);
}
else{
	//pass $studioFilter into query as the studio
	$results=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl1.Revision, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete, Tbl1.actualITLComplete, Tbl1.Status, Tbl1.studio FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.HandoffDate, ProjectDetails.Revision AS Revision, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, ProjectDetails.Status AS Status, Projects.studio AS studio, ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName, ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from ProjectDetails Group By ProjectDetails.ProjectID)) AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID WHERE (Tbl1.Status = 'Approved' OR Tbl1.Status = 'Obsoleted') AND (Tbl1.studio = '$studioFilter') ORDER BY ".$filter);
}

if($results)
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
		echo "<td id='projectnameD3'><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
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
		echo "<td id='projecttypeD3'>".$row["ProjectType"]."</td>";		
		echo "<td id='marketD3'>".$row["Class"]."</td>";
		echo "<td id='ownerD3'>".$row["Owner"]."</td>";
		echo "<td id='studioD3'>".$row["studio"]."</td>";
		echo "<td id='hodateD3'>".convertFromSQLDate($row["HandoffDate"])."</td>";		
		echo "<td id='stdateD3'>".convertFromSQLDate($row["StartDate"])."</td>";
		echo "<td id='enddateD3'>".convertFromSQLDate($row["EndDate"])."</td>";
		echo "<td id='sqareleaseD3'>".convertFromSQLDate($row["actualSQAComplete"])."</td>";
		echo "<td id='gadateD3'>".convertFromSQLDate($row["actualITLComplete"])."</td>";
		echo "<td id='statusD3'>".$row["Status"]."</td>";
		echo "<td id='revisionD3'>".$row["Revision"]."</td>";
		echo "<td id='detailsD3'>".$row["AdditionalProjectDetails"]."</td>";
		
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
