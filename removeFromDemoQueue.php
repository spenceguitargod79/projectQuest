
<html>
<head>
    <title>Redirecting...</title>
    <meta http-equiv="refresh" 
content="1;URL=http://10.0.4.54/SQAProjectTracking/DemoReadyQueue.php">
<link rel="Stylesheet" href="styles.css" type="text/css" />
</head>
<title>Delete from Demo Queue</title>
<body>


</body>
</html>
<?php
include('verify.php');
include('siteFuncs.php');
include('calculateGADate.php');

$link=sqlConnect();
$projectid = mysqli_real_escape_string($link,$_GET['projectid']);
$revision = mysqli_real_escape_string($link,$_GET['rev']);

//echo "PROJECT ID = ".$projectid;
//echo "REVISION = ".$revision;
echo "<br><br><br><br><br><br>";
echo '<center><img src="images/beerDrinks.gif"/></center>';

echo "<center><H2>REMOVING FROM DEMO QUEUE and MARKING as COMPLETE!</H2></center>";
//$dateTime = getCurrentDateTime();
//writeToLog(" Current datTime is: : ".$dateTime);

$sql = "UPDATE ProjectDetails SET demoIsChecked='NO', demoComplete='YES' WHERE ProjectID=$projectid AND Revision=$revision";
if(!mysqli_query($link,$sql))
{
      echo("Error description Delta Quasar: " . mysqli_error($link));
      mysqli_close($link);
}
else
{
              //header("location:DemoReadyQueue.php");
              mysqli_close($link);
}
?>
