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
                <?php echo "&nbsp;&nbsp<a href=ProjectQueue.php><font color=707070>Projects In Queue</font></a>
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

echo "<th id='projectnameH2'><a href='ProjectQueue.php?filter=ProjectName&direction=".$direction."'>Project Name</a></th>";
echo "<th id='projecttypeH2'><a href='ProjectQueue.php?filter=ProjectType&direction=".$direction."'>Project Type</a></th>";
echo "<th id='marketH2'><a href='ProjectQueue.php?filter=Class&direction=".$direction."'>&nbspMarket</a></th>";
echo "<th id='studioH2'><a href='ProjectQueue.php?filter=studio&direction=".$direction."'>&nbsp&nbsp&nbsp&nbspStudio</a></th>";
//echo "<th><a href='ProjectQueue.php?filter=complexityID&direction=".$direction."'>Complexity</a></th>";
echo "<th id='hodateH2'><a href='ProjectQueue.php?filter=HandoffDate&direction=".$direction."'>Handoff Date&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a></th>";
echo "<th id='stdateH2'><a href='ProjectQueue.php?filter=EstimatedStartDate&direction=".$direction."'>Estimated Start Date&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</a></th>";
echo "<th id='sqareleaseH2'><a href='ProjectQueue.php?filter=actualSQAComplete&direction=".$direction."'>SQA Release Date&nbsp&nbsp&nbsp</a></th>";
echo "<th id='gadateH2'><a href='ProjectQueue.php?filter=actualITLComplete&direction=".$direction."'>Est GA Date</a></th>";
echo "<th id='detailsH2'><a href='ProjectQueue.php?filter=AdditionalProjectDetails&direction=".$direction."'>Project Details</a></th>";

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

//if($results=mysqli_query($link,"SELECT Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl1.Revision, Tbl2.StartDate, Tbl1.EndDate, Tbl1.Status FROM (SELECT Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.HandoffDate, ProjectDetails.Revision AS Revision, ProjectDetails.EndDate AS EndDate, ProjectDetails.Status AS Status, ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName, ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectName = Tbl2.ProjectName WHERE Tbl1.Status <> 'Approved'"))
if($results=mysqli_query($link,"SELECT Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.AdditionalProjectDetails, Projects.HandoffDate, Projects.EstimatedStartDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.studio
FROM Projects
WHERE (((Projects.ProjectID) Not In (SELECT ProjectID FROM ProjectDetails))) ORDER BY ".$filter))
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
		echo "<td id='projectnameD2'><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
		//echo "<td>".$row["ProjectName"]."</td>";
		echo "<td id='projecttypeD2'>".$row["ProjectType"]."</td>";
		echo "<td id='marketD2'>".$row["Class"]."</td>";
		echo "<td id='studioD2'>".$row["studio"]."</td>";
		//echo "<td>".$row["complexityID"]."</td>";
		echo "<td id='hodateD2'>".convertFromSQLDate($row["HandoffDate"])."</td>";
		echo "<td id='stdateD2'>".convertFromSQLDate($row["EstimatedStartDate"])."</td>";
		echo "<td id='sqareleaseD2'>".convertFromSQLDate($row["actualSQAComplete"])."</td>";
		echo "<td id='gadateD2'>".convertFromSQLDate($row["actualITLComplete"])."</td>";
		echo "<td id='detailsD2'>".$row["AdditionalProjectDetails"]."</td>";
		
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
