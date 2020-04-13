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
	  <?php echo "&nbsp;&nbsp<a href=ProjectOn-Hold.php><font color=707070>Projects On-Hold</a>
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

echo "<th id='projectnameH6'><a href='ProjectOn-Hold.php?filter=ProjectName&direction=".$direction."'>&emsp;ProjectName</a></th>";
echo "<th id='projecttypeH6'><a href='ProjectOn-Hold.php?filter=ProjectType&direction=".$direction."'>&nbsp&nbsp&nbsp&emsp;Type</a></th>";
echo "<th id='marketH6'><a href='ProjectOn-Hold.php?filter=Class&direction=".$direction."'>&nbsp&nbsp&nbsp&nbsp&nbspMarket</a></th>";
echo "<th id='ownerH6'><a href='ProjectOn-Hold.php?filter=Owner&direction=".$direction."'>&ensp;&ensp;&ensp;Owner</a></th>";
//echo "<th id='hodateH3'><a href='ProjectOn-Hold.php?filter=HandoffDate&direction=".$direction."'>Handoff Date</a></th>";
echo "<th id='studioH6'><a href='ProjectOn-Hold.php?filter=studio&direction=".$direction."'>&nbsp&nbsp&nbspStudio</a></th>";
echo "<th id='stdateH6'><a href='ProjectOn-Hold.php?filter=StartDate&direction=".$direction."'>StartDate&ensp;</a></th>";
echo "<th id='enddateH6'><a href='ProjectOn-Hold.php?filter=EndDate&direction=".$direction."'>EndDate&ensp;</a></th>";
echo "<th id='sqareleaseH6'><a href='ProjectOn-Hold.php?filter=actualSQAComplete&direction=".$direction."'>SQARelDate&emsp;&ensp;&emsp;</a></th>";
echo "<th id='gadateH6'><a href='ProjectOn-Hold.php?filter=actualITLComplete&direction=".$direction."'>EstGADate&emsp;&ensp;&ensp;&ensp;&ensp;</a></th>";
//echo "<th><a href='ProjectOn-Hold.php?filter=gaTargetHealth&direction=".$direction."'>On Target</a></th>";
echo "<th id='statusH6'><a href='ProjectOn-Hold.php?filter=Status&direction=".$direction."'>Status&emsp;&ensp;&emsp;</a></th>";
echo "<th id='revisionH6'><a href='ProjectOn-Hold.php?filter=Revision&direction=".$direction."'>Revision&emsp;&emsp;&ensp;</a></th>";
echo "<th id='detailsH6'><a href='ProjectOn-Hold.php?filter=AdditionalProjectDetails&direction=".$direction."'>Details&emsp;&emsp;&emsp;</a></th>";//if(isAdmin($username))
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

if($results=mysqli_query($link,"SELECT Tbl1.AdditionalProjectDetails, Tbl1.ProjectID, Tbl1.ProjectName, Tbl1.ProjectType, Tbl1.Class, Tbl1.HandoffDate, Tbl1.Owner, Tbl1.Revision, Tbl2.StartDate, Tbl1.EndDate, Tbl1.actualSQAComplete, Tbl1.actualITLComplete, Tbl1.gaTargetHealth, Tbl1.Status, Tbl1.studio FROM (SELECT Projects.AdditionalProjectDetails, Projects.ProjectID, Projects.ProjectName, Projects.ProjectType, Projects.Class, Projects.HandoffDate, ProjectDetails.Revision AS Revision, ProjectDetails.EndDate AS EndDate, Projects.actualSQAComplete, Projects.actualITLComplete, Projects.gaTargetHealth, ProjectDetails.Status AS Status, Projects.studio, ProjectDetails.Owner AS Owner FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select MAX(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl1 INNER JOIN (SELECT Projects.ProjectID as ProjectID, ProjectDetails.TaskID AS TaskID, Projects.ProjectName AS ProjectName, ProjectDetails.StartDate AS StartDate FROM Projects INNER JOIN ProjectDetails ON Projects.ProjectID=ProjectDetails.ProjectID WHERE ProjectDetails.TaskID  in (Select Min(ProjectDetails.TaskID) from  ProjectDetails Group By ProjectDetails.ProjectID))  AS Tbl2 ON Tbl1.ProjectID = Tbl2.ProjectID WHERE Tbl1.Status = 'On-Hold' ORDER BY ".$filter))
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
		echo "<td id='projectnameD6'><a href='ProjectDetails.php?name=".$row["ProjectID"]."'>".$row["ProjectName"]."</a></td>";
		echo "<td id='projecttypeD6'>".$row["ProjectType"]."</td>";
		echo "<td id='marketD6'>".$row["Class"]."</td>";
		echo "<td id='ownerD6'>".$row["Owner"]."</td>";
		//echo "<td id='hodateD3'>".convertFromSQLDate($row["HandoffDate"])."</td>";
		echo "<td id='studioD6'>".$row["studio"]."</td>";
		echo "<td id='stdateD6'>".convertFromSQLDate($row["StartDate"])."</td>";
		echo "<td id='enddateD6'>".convertFromSQLDate($row["EndDate"])."</td>";
		echo "<td id='sqareleaseD6'>".convertFromSQLDate($row["actualSQAComplete"])."</td>";
		echo "<td id='gadateD6'>".convertFromSQLDate($row["actualITLComplete"])."</td>";

		//Display target health icon
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

		echo "<td id='statusD6'>".$row["Status"]."</td>";
		echo "<td id='revisionD6'>".$row["Revision"]."</td>";
		echo "<td id='detailsD6'>".$row["AdditionalProjectDetails"]."</td>";
		
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
