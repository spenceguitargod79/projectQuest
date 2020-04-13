<?php
//Site Functions
function sqlConnect()
{ 
   $link= mysqli_connect('localhost','sqaadmin','Medusa18','Projects');  
   return $link;
}

function isAdmin($username)
{
		$link=sqlConnect();
		$sql="SELECT * FROM admins WHERE name='".$username."'";
        if($results=mysqli_query($link,$sql))
		{		
			mysqli_close($link);
			return mysqli_num_rows($results);			
		}
		mysqli_close($link);
		return 0;
}
function isGuest($username)
{
		$link=sqlConnect();
		$sql="SELECT * FROM guests WHERE name='".$username."'";
        if($results=mysqli_query($link,$sql))
		{		
			mysqli_close($link);
			return mysqli_num_rows($results);			
		}
		mysqli_close($link);
		return 0;
}
function isSQAuser($username)
{
		$link=sqlConnect();
		$sql="SELECT * FROM sqausers WHERE name='".$username."'";
        if($results=mysqli_query($link,$sql))
		{		
			mysqli_close($link);
			return mysqli_num_rows($results);			
		}
		mysqli_close($link);
		return 0;    
}
function isDEVuser($username)
{
		$link=sqlConnect();
		$sql="SELECT * FROM DevUsers WHERE name='".$username."'";
        if($results=mysqli_query($link,$sql))
		{		
			mysqli_close($link);
			return mysqli_num_rows($results);			
		}
		mysqli_close($link);
		return 0;    
}

//If the logged in user does NOT exist in any user tables
function isNOuser($username)
{
	$adminResult = isAdmin($username);
	$sqaResult = isSQAuser($username);
	$devResult = isDEVUser($username);
	$guestResult = isGuest($username);

	if($adminResult || $sqaResult || $devResult || $guestResult){
		//user is in one of the tables
		return true;
	}
	else{
		return false;//user is not in any of the user tables
	}
}

function emailGroup($body)
{
		$link=sqlConnect();
		$sql="SELECT * FROM emailGroup";
        if($results=mysqli_query($link,$sql))
		{		
			while($row=mysqli_fetch_array($results,MYSQLI_ASSOC))
			{
				$email=$row["name"];
				emailOne($body,$email);
			}
		}
		mysqli_close($link);
		return 0;    
}
function emailOne($body,$email)
{
	$emails=explode(',',$email);
	foreach($emails as $showEmail)
	{
		//Commenting out until IT allows access to 10.0.4.30
		//$cmd="C:\wamp\www\downloads\plink.exe medusa@10.0.4.60 -pw CadillacP455 php mailtest.php 'Medusa Alert' '".$body."' '".$showEmail."'";
		$cmd="C:\wamp\www\downloads\plink.exe sqaadmin@10.0.4.54 -pw Medusa18 php mailtest.php 'Project Tracker Alert' '".$body."' '".$showEmail."'";
		exec($cmd);
	}
	
}
function getLogs($ipaddress,$name,$playerVersion)
{
	$successful=-1;
	$attempts=0;
	$cmd="c:\\wamp\\www\\downloads\\plink.exe cjgamer@".$ipaddress." -pw CJgamer1 sudo mkdir /home/cjgamer/medusa";
	
	while($successful!=0 && $attempts<10)
	{
		echo $successful;
		exec($cmd,$blank,$successful);
		$attempts=$attempts+1;
		echo "Attempt";
		if($successful!=0)
		{
			sleep(15);
		}
	}
	$cmd="c:\\wamp\\www\downloads\\plink.exe cjgamer@".$ipaddress." -pw CJgamer1 sudo chmod 777 /home/cjgamer/medusa";
	exec($cmd);
	//Unused
	//$cmd="c:\\wamp\\www\downloads\\plink.exe cjgamer@".$ipaddress." -pw CJgamer1 sudo cp ``ls -A /var/player/*` | grep -v core\` /home/cjgamer/medusa";
	//exec($cmd);
	
	
	$cmd="c:\\wamp\\www\downloads\\plink.exe cjgamer@".$ipaddress." -pw CJgamer1 sudo cp -R /var/player/* /home/cjgamer/medusa";
	exec($cmd);
	
	$cmd="c:\\wamp\\www\downloads\\plink.exe cjgamer@".$ipaddress." -pw CJgamer1 sudo chmod 777 /home/cjgamer/medusa/*";
	exec($cmd);
	
	//CF Health***************
	//$cmd="c:\\wamp\\www\downloads\\pscp.exe -pw CJgamer1 c:/wamp/www/downloads/iSMART cjgamer@".$ipaddress.":/home/cjgamer/medusa/";
	//exec($cmd);
	//$cmd="c:\\wamp\\www\downloads\\pscp.exe -pw CJgamer1 c:/wamp/www/downloads/nvram_tool cjgamer@".$ipaddress.":/home/cjgamer/medusa/";
	//if(strpos($playerVersion,'15')!==false)
	//{
	//	exec($cmd);
	//}
	//$cmd="c:\\wamp\\www\downloads\\pscp.exe -pw CJgamer1 c:/wamp/www/downloads/medusa-health.sh cjgamer@".$ipaddress.":/home/cjgamer/medusa/";
	//exec($cmd);
	//$cmd="c:\\wamp\\www\downloads\\plink.exe cjgamer@".$ipaddress." -pw CJgamer1 sudo chmod 777 /home/cjgamer/medusa/*";
	//exec($cmd);
	//$cmd="c:\\wamp\\www\downloads\\plink.exe cjgamer@".$ipaddress." -pw CJgamer1 sudo dos2unix /home/cjgamer/medusa/medusa-health.sh";
	//exec($cmd);
	//$cmd="c:\\wamp\\www\downloads\\plink.exe cjgamer@".$ipaddress." -pw CJgamer1 sudo /home/cjgamer/medusa/medusa-health.sh";
	//exec($cmd);
	//$cmd="c:\\wamp\\www\downloads\\plink.exe cjgamer@".$ipaddress." -pw CJgamer1 sudo rm -f /home/cjgamer/medusa/iSMART /home/cjgamer/medusa/medusa-health.sh /home/cjgamer/medusa/nvram_tool";
	//exec($cmd);
	echo $name;
	echo $ipaddress;
	$cmd="C:\\wamp\\www\\downloads\\transfer.bat ".$name." ".$ipaddress;
	exec($cmd);
	$cmd="c:\\wamp\\www\downloads\\plink.exe cjgamer@".$ipaddress." -pw CJgamer1 sudo rm -rf /home/cjgamer/medusa";
	exec($cmd);
	$link=sqlConnect();
	$sql="SELECT * from eps where name='".$name."'";
	if($results=mysqli_query($link,$sql))
	{
		$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
		$id=$row["currentTestId"];
		$archiveLocation="E:\\archives\\".$name;
		if(!file_exists($archiveLocation)) 
		{
			mkdir($archiveLocation, 0777);
		}
		if(!empty($id))
		{
			$archiveLocation.="\\".$id;
		}
		else
		{
			$archiveLocation.="\\noTestID";
		}
		if(!file_exists($archiveLocation)) 
		{
			mkdir($archiveLocation, 0777);
		}
		foreach (glob("C:\\wamp\\www\\downloads\\".$name."\\*") as $file)
		{
			if(is_dir($file))
			{
				$archiveLocationPlus=$archiveLocation."\\".basename($file);
				mkdir($archiveLocationPlus);
				foreach(glob($file."\\*") as $filename)
				{
					rename($filename,$archiveLocationPlus."\\".basename($filename));
				}
				rmdir($file);
			}
		}
	}
	else
	{	
		echo("Error description: " . mysqli_error($link));
	}
	mysqli_close($link);
}
function logger($name,$message)
{
	if (!file_exists("downloads\\".$name)) 
	{
		mkdir("downloads\\" . $name, 0777);
	}
	$write="\n".date("Y-m-d H:i:s")." | ".$message;
	echo file_put_contents("downloads\\".$name."\\".$name.".log",$write,FILE_APPEND);
}
function sqlValidate($string)
{
	return mysql_real_escape_string($string);
}
function reverseValidate($string)
{
	$search = array( "\\0", "\\n", "\\r", "\\\\", "\\'", "\\\"", "\Z", );
	$replace = array( "\x00", "\n", "\r", "\\", "'", "\"", "\x1a" );
	return str_replace( $search, $replace, $string );
}
function convertFromSQLDate($original)
{	
		if((bool)strtotime($original) && $original!="0000-00-00")
		{
			try
			{
				$myDateTime = DateTime::createFromFormat('Y-m-d', $original);
				return $myDateTime->format('m/d/Y');
			}
			catch (Exception $e)
			{
				return '00/00/0000';
			}	
		}
		else
		{
			return '00/00/0000';
		}
}
function convertToSQLDate($original)
{	
		if((bool)strtotime($original) && $original!="00/00/0000")
		{
			try
			{
				$myDateTime = DateTime::createFromFormat('m/d/Y', $original);
				return $myDateTime->format('Y-m-d');
			}
			catch (Exception $e)
			{
				return '1970-01-01';
			}	
		}
		else
		{
			return '1970-01-01';
		}
}
function archiveFiles($name)
{
	$link=sqlConnect();
	$sql="SELECT * from eps where name='".$name."'";
	if($results=mysqli_query($link,$sql))
	{
		$row=mysqli_fetch_array($results,MYSQLI_ASSOC);
		$id=$row["currentTestId"];
		$archiveLocation="E:\\archives\\".$name;
		if(!file_exists($archiveLocation)) 
		{
			mkdir($archiveLocation, 0777);
		}
		if(!empty($id))
		{
			$archiveLocation.="\\".$id;
		}
		else
		{
			$archiveLocation.="\\noTestID";
		}
		if(!file_exists($archiveLocation)) 
		{
			mkdir($archiveLocation, 0777);
		}
		$dir = glob("c:\\wamp\\www\\downloads\\video\\".$name."\\*.mp4");
		foreach($dir as $file) 
		{
			rename($file,$archiveLocation."\\".basename($file));				
		}
		$dir = glob("c:\\wamp\\www\\downloads\\".$name."\\*.7z");
		foreach($dir as $file) 
		{
			rename($file,$archiveLocation."\\".basename($file));	
		}
		$dir = glob("c:\\wamp\\www\\downloads\\".$name."\\*.log");
		foreach($dir as $file) 
		{
			rename($file,$archiveLocation."\\".basename($file));
		}
		$dir = glob("c:\\wamp\\www\\downloads\\".$name."\\*.txt");
		foreach($dir as $file) 
		{
			rename($file,$archiveLocation."\\".basename($file));
		}
	}
	else
	{	
		echo("Error description: " . mysqli_error($link));
	}
	mysqli_close($link);
}
?>
