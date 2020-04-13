<?php
include('verify.php');
include('siteFuncs.php');
if(!isAdmin($username))
{
    header("location: ProjectList.php");
}
$id = $_GET['ProjectID'];
$table=$_GET['table'];
$link=sqlConnect();
$sql="DELETE FROM ".$table." where ProjectID='".$id."'";
if(!mysqli_query($link,$sql))
{
	echo("Error description: " . mysqli_error($link));
	mysqli_close($link);
}
else
{
	mysqli_close($link);
	//emailGroup($id." Deleted From ".$table." by ".$username);
	if($table=="Projects")
	{
		header("location: Projects.php");
	}
	else
	{
		header("location: ProjectList.php");
	}
}
?>
