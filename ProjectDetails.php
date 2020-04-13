<!DOCTYPE html>
<?php
include('verify.php');
include ('siteFuncs.php');
include ('calculateGADate.php');
$link=sqlConnect();
?>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<link rel="Stylesheet" href="styles.css" type="text/css" />

<title>SQA Project Tracking</title>

</head>
<style>

DIV.container {
    min-height: 6em;
    display: inline-block;
    width: 10%;
    border: 4px double blue;
    overflow: auto;
    padding: 10px 10px 10px 10px;
}

</style>
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
          <?php echo "&nbsp;&nbsp<a href=ITLList.php>Projects In ITL</a>
          &nbsp;&nbsp;|&nbsp;&nbsp;";?>
	  <?php echo "&nbsp;&nbsp<a href=DevProjectsList.php>Dev Projects</a>
	  &nbsp;&nbsp;";?>
	</td>
	<td width="25%" align="right">
	  <?php echo "Project Details &nbsp;&nbsp;|&nbsp;&nbsp;";?>
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
$id=$_GET['name'];

//echo "Project Name: ";
if($results=mysqli_query($link,"SELECT ProjectName FROM Projects WHERE ProjectID='".$id."' "))
{
	$ProjectName=mysqli_fetch_array($results,MYSQLI_ASSOC);
}
//The css for this div is defined up top
echo "<DIV class=\"container\">";
echo "<strong>".$ProjectName["ProjectName"]."</strong>";
echo "<br>";

echo "<br>";

//Show Studio name and manager for the project
if($results=mysqli_query($link,"SELECT studio FROM Projects WHERE ProjectID='".$id."' "))
{
        $studioName=mysqli_fetch_array($results,MYSQLI_ASSOC);
}
echo "<strong>Studio:</strong> ".$studioName["studio"];
echo "<br>";
echo "<br>";
$sn=$studioName["studio"];
if($results=mysqli_query($link,"SELECT manager FROM Studios WHERE name='".$sn."' "))
{
        $studioMan=mysqli_fetch_array($results,MYSQLI_ASSOC);
}
echo "<strong>Assignee:</strong> ".$studioMan["manager"];
echo "</DIV>";
//---------------------------

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
echo "<th>Revision</th>";
echo "<th>Owner</th>";
echo "<th>Start Date</th>";
echo "<th>End Date</th>";
echo "<th>Status</th>";
echo "<th>Notes</th>";

//if(isAdmin($username))
//{
//	echo "<th>Delete</th>";
//}
echo "</tr>";
echo "<thead>";
$rowCount=0;
echo "<tbody>";

if($results=mysqli_query($link,"SELECT * FROM ProjectDetails where ProjectID='".$id."' ORDER BY Revision"))
{
	while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
	{
		if($rowCount%2==0)
		{
			//echo "<tr class='even'>";
		}
		else
		{
			echo "<tr>";
		}
		if(isAdmin($username) || isSQAuser($username)){
                   echo "<td><a href='EditProjectDetails.php?name=".$row["TaskID"]."&projectID=".$row["ProjectID"]."'>".$row["Revision"]."</a></td>";
		}
		else{
		   echo "<td>".$row['Revision']."</td>";
		}
		//echo "<td>".$row["Revision"]."</td>";
		echo "<td>".$row["Owner"]."</td>";
		echo "<td>".convertFromSQLDate($row["StartDate"])."</td>";
		echo "<td>".convertFromSQLDate($row["EndDate"])."</td>";
		echo "<td>".$row["Status"]."</td>";
		echo "<td>".$row["Notes"]."</td>";
		$latestRev = getLatestProjectRevision($link,$row["ProjectID"]);

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

//Only allow clicking add revision if the latest revision is rejected
$latestRev = getLatestProjectRevision($link,$id);
$latestRevStatus = getLatestProjectRevisionStatus($link,$id,$latestRev);
if((isAdmin($username) || isSQAuser($username)) && ($latestRevStatus == "Rejected" || $latestRevStatus == "ITL Reject" || $latestRevStatus == "Field Reject")){
	echo "<a href=AddProjectDetails.php?name=".$id.">Add Revision</a>";
	//echo "TEST 1 passed";
}
else if(!oneRevisionExists($id,$link) && !multipleRevisionsExist($id,$link)){//if a project is in queue, it has no revisions yet, so allow adding a revision.
	echo "<a href=AddProjectDetails.php?name=".$id.">Add Revision</a>";
	//echo "test 2 passed";
}
else{
	//display un-clickable link, in a different color
	echo "<font color=red> Add Revision </font>";
	//echo "Test 3 passed";
}

//Display revision history link
if(isAdmin($username) || isSQAuser($username)){
    echo " | <a href=RevisionHistoryList.php?name=".$id.">History</a>";//page that lists project revision changes
}
else{
                //Don't show link for guests
} 

//Link to EditProject
if(isAdmin($username)){
        echo " | <a href=ProjectHistoryList.php?name=".$id.">P-History</a>";
	echo " | <a href=EditProject.php?name=".$id.">Edit Project</a>";
}
mysqli_close($link);

function getLatestProjectRevisionStatus($link,$pid,$rev){
        $sql = "SELECT Status FROM ProjectDetails where ProjectID='$pid' and Revision='$rev' ORDER BY TaskID DESC LIMIT 1";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //echo "<br>---ProjectID:".$pid;
                //echo "Latest Revision Status:".$row["Status"]."---";

                return $row["Status"];
        }
        else
        {
                echo "getLatestProjectRevisionStatus: Error executing query!!";
        }
}

function getLatestProjectRevision($link,$pid){
        $sql = "SELECT Revision FROM ProjectDetails where ProjectID='$pid' ORDER BY Revision DESC LIMIT 1";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                //echo "<br>---ProjectID:".$pid;
                //echo "Latest Revision:".$row["Revision"]."---";

                return $row["Revision"];
        }
        else
        {
                echo "getLatestProjectRevision: Error executing query!!";
        }
}

?>
<h4><i> Adding a new revision is only available if the latest revision is in a "Rejected" state.</i></h4>
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
