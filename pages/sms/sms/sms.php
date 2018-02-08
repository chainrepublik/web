<?
  include "../../../kernel/db.php";  
 
  $db=new db();
  
  $mes=$_REQUEST['INCOMING'];
  
  $db->log($mes);
  
  if (strpos($mes, ":")===FALSE || strlen($mes)<50 || strlen($mes)>100) 
  {   
       header("HTTP/1.1 200 OK");
	   return false;
  }
  
 
  $v=explode("#", $mes);
  $v=explode(":", $v[1]);
  
   
   if ($v[0]==-1)
   {
	 $src=$v[1];
	 $dest=$v[2];
	 $user=trim(strtolower($db->hexToStr($v[7])));
   }
   
   
   // User exist 
   $query="SELECT * from web_users WHERE LCASE(user)='".$user."'"; 
   $result=$db->execute($query);  
   if (mysqli_num_rows($result)==0)
   {
		header("HTTP/1.1 200 OK");
		die();
   }
   
   // User data
   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
   
   // Telephone used 
   $query="SELECT * FROM used_numbers WHERE tel='".$src."'"; 
   $result=$db->execute($query);  
   if (mysqli_num_rows($result)>0)
   {
	    $query="UPDATE web_users 
		           SET sms_tel='', 
				       sms_status='ID_USED' 
				 WHERE LCASE(user)='".$user."'"; 
        $result=$db->execute($query);  
   
		header("HTTP/1.1 200 OK");
		die();
   }
   
   // Insert number
   $query="INSERT INTO used_numbers 
                   SET userID='".$row['ID']."', 
				       tel='".$src."', 
					   tstamp='".time()."'";
   $result=$db->execute($query); 
   
   // UPDATE web_users
   $query="UPDATE web_users 
		           SET sms_tel='".$src."', 
				       sms_status='ID_OK' 
				 WHERE LCASE(user)='".$user."'"; 
   $result=$db->execute($query);  
   
   header("HTTP/1.1 200 OK");
?>