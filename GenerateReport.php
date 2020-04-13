<?php
include('verify.php');
include('siteFuncs.php');
include('calculateGADate.php');
$link=sqlConnect();

//Get variables passed in from ReportTool.php
$projectID = mysqli_real_escape_string($link,$_POST['projectid']);
$projectName = mysqli_real_escape_string($link,$_POST['projectname']);

//Function calls
$status = getHighestRevisionStatus($link, $projectID);
$ogGA = getOriginalGADate($link, $projectID);
$currGA = getCurrentGADate($link, $projectID);
$countGAChanges = getCountOfGAdateChanges($link, $projectID);
$countITLRejects = getCountOfITLRejects($link, $projectID);

//TODO:redirect back to ReportTool.php, sending function return variables over so they can be displayed in a table for the user.
header('Location: ReportTool.php?pid='.$projectID.'&status='.$status.'&ogGA='.$ogGA.'&currGA='.$currGA.'&gaCount='.$countGAChanges.'&itlRejCount='.$countITLRejects);

function getHighestRevisionStatus($link, $projectID){
        $sql = "SELECT Status FROM ProjectDetails where ProjectID='$projectID' order by TaskID DESC LIMIT 1";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $stat=$row['Status'];
                return $stat;
        }
}

function getOriginalGADate($link, $projectID){
        $sql = "SELECT estITLComplete FROM Projects where ProjectID=$projectID";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $ogGA=$row['estITLComplete'];
		return $ogGA;
        }
}

function getCurrentGADate($link, $projectID){
        $sql = "SELECT actualITLComplete FROM Projects where ProjectID=$projectID";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $currGA=$row['actualITLComplete'];
                return $currGA;
        }
}

function getCountOfGAdateChanges($link, $projectID){
        $sql = "SELECT count(*) as total FROM ProjectHistory where ProjectID='$projectID' and (changeType = 'actITLRel' or changeType = 'ActualITLComplete')";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $count=$row['total'];
                return $count;
        }
}

function getCountOfITLRejects($link, $projectID){
        $sql = "SELECT count(*) as total FROM ProjectDetailHistory where ProjectID='$projectID' and (changeType = 'status' and statusNew = 'ITL Reject')";
        if($results=mysqli_query($link,$sql))
        {
                $row=mysqli_fetch_array($results,MYSQLI_ASSOC);
                $count=$row['total'];
                return $count;
        }
}
?>
