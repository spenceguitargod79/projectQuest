<?php
include('siteFuncs.php');
include('calculateGADate.php');
$link=sqlConnect();

//emailGroup("Group email Test from Project Tracker");
checkRevisionDate($link);
//Get a list of projects whos latest revision end date has exceeded actual sqa release date 
function checkRevisionDate($link)
{
	//Query ProjectDetails table for highest revisions
        if($results=mysqli_query($link,"SELECT tt.* FROM ProjectDetails tt INNER JOIN (SELECT ProjectID, MAX(Revision) as maxRev FROM ProjectDetails GROUP BY ProjectID) groupedtt ON tt.ProjectID = groupedtt.ProjectID AND tt.Revision = groupedtt.maxRev ORDER BY ProjectID ASC;"))
        {
                while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
                {
                        //writeToLog("\nProject ID = ".$row["ProjectID"]."--");
			$pid = $row["ProjectID"];
			//writeToLog("\nRevision = ".$row["Revision"]."--");
			//writeToLog("\nRevision End Date = ".$row["EndDate"]."--");
			$revEndDate = $row["EndDate"];
			//writeToLog("\n++++++++++++++++++++++++++++++++++++++++++");

			checkSqaReleaseDate($link,$pid,$revEndDate);
                }
        }
        else
        {
                echo("Error description 1: " . mysqli_error($link));
                echo "ERROR running query!";
        }
}

function checkSqaReleaseDate($link,$pid,$revEndDate)
{
        //Query Projects table for pid and actual sqa release dates
        if($results=mysqli_query($link,"SELECT * from Projects;"))
        {
                while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
                {
                        writeToLog("\nProject Name = ".$row["ProjectName"]."--");
                        writeToLog("\nProject ID = ".$row["ProjectID"]."--");
                        writeToLog("\nActual SQA Release Date = ".$row["actualSQAComplete"]."--");
			writeToLog("\nPID from 1st function = ".$pid."--");
			writeToLog("\nRev end date from 1st function = ".$revEndDate."--");
                        writeToLog("\n++++++++++++++++++++++++++++++++++++++++++");

                }
        }
        else
        {
                echo("Error description 2: " . mysqli_error($link));
                echo "ERROR running query!";
        }
}

?>
