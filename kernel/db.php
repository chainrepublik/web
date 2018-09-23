<?php  
 class db
  {
	 var $html=array("<", ">", "http://", "http:", "http", "javascript");

	 function db()
	 {
		 //die ("Maintainanance in progress. We will be up in 3-4 hours.");
		 //if ($_SERVER['HTTP_CF_CONNECTING_IP']!="109.166.135.48")
		 //    die ("Maintainance in progress. Pls come back in a few hours.");
		 
		 
		 
		// ---------------------------------
        $user="";
		$pass="";
		$db="";
		
	    // ---------------------------------
		
		
         $this->con = mysqli_connect("localhost", $user, $pass, $db)
            or die("Could not connect: " . mysqli_error());
 		
		 
		 error_reporting(E_ERROR);
         ini_set("display_errors", "1");
	}
	
	  function getResult($query, 
	                   $types="", 
					   $par_1="", 
					   $par_2="", 
					   $par_3="", 
					   $par_4="", 
					   $par_5="", 
					   $par_6="", 
					   $par_7="", 
					   $par_8="", 
					   $par_9="",
					   $par_10="",
					   $par_11="", 
					   $par_12="", 
					   $par_13="", 
					   $par_14="", 
					   $par_15="",
					   $par_16="",
					   $par_17="",
					   $par_18="",
					   $par_19="",
					   $par_20="",
					   $par_21="",
					   $par_22="",
					   $par_23="",
					   $par_24="",
					   $par_25="")
	  {
		  $result=$this->execute($query, 
	                             $types, 
					             $par_1, 
					             $par_2, 
					             $par_3, 
					             $par_4, 
					             $par_5, 
					             $par_6, 
					             $par_7, 
					             $par_8, 
					             $par_9,
					             $par_10,
					             $par_11, 
					             $par_12, 
					             $par_13, 
					             $par_14, 
					             $par_15,
					             $par_16,
					             $par_17,
					             $par_18,
					             $par_19,
					             $par_20,
					             $par_21,
					             $par_22,
					             $par_23,
					             $par_24,
					             $par_25);
		  
		  return $result;
	  }
	 
	  function getRows($query, 
	                   $types="", 
					   $par_1="", 
					   $par_2="", 
					   $par_3="", 
					   $par_4="", 
					   $par_5="", 
					   $par_6="", 
					   $par_7="", 
					   $par_8="", 
					   $par_9="",
					   $par_10="",
					   $par_11="", 
					   $par_12="", 
					   $par_13="", 
					   $par_14="", 
					   $par_15="",
					   $par_16="",
					   $par_17="",
					   $par_18="",
					   $par_19="",
					   $par_20="",
					   $par_21="",
					   $par_22="",
					   $par_23="",
					   $par_24="",
					   $par_25="")
	  {
		  $result=$this->getResult($query, 
	                               $types, 
					               $par_1, 
					               $par_2, 
					               $par_3, 
					               $par_4, 
					               $par_5, 
					               $par_6, 
					               $par_7,  
					               $par_8, 
					               $par_9,
					               $par_10,
					               $par_11, 
					               $par_12, 
					               $par_13, 
					               $par_14, 
					               $par_15,
					               $par_16,
					               $par_17,
					               $par_18,
					               $par_19,
					               $par_20,
					               $par_21,
					               $par_22,
					               $par_23,
					               $par_24,
					               $par_25);
		  
		  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		  return $row;
	  }
	 
	  function execute($query, 
	                   $types="", 
					   $par_1="", 
					   $par_2="", 
					   $par_3="", 
					   $par_4="", 
					   $par_5="", 
					   $par_6="", 
					   $par_7="", 
					   $par_8="", 
					   $par_9="",
					   $par_10="",
					   $par_11="", 
					   $par_12="", 
					   $par_13="", 
					   $par_14="", 
					   $par_15="",
					   $par_16="",
					   $par_17="",
					   $par_18="",
					   $par_19="",
					   $par_20="",
					   $par_21="",
					   $par_22="",
					   $par_23="",
					   $par_24="",
					   $par_25="")
	  {
		  //print $query."  ($par_1,$par_2,$par_3,$par_4,$par_5,$par_6,$par_7,$par_8,$par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15, $par_16, $par_17, $par_18, $par_19, $par_20, $par_21, $par_22, $par_23, $par_24, $par_25)<br><br>";
		   
		   		  
		   $stmt = $this->con->prepare($query);
			   
		       if ($stmt!=false)
			   {
				   // Only parameter 1
		           if (strlen($types)==1)
	                  $stmt->bind_param($types, $par_1);
					  
				   // 2 params
		           if (strlen($types)==2)
	                  $stmt->bind_param($types, $par_1, $par_2);
					  
					  // 3 params
		             if (strlen($types)==3)
	                  $stmt->bind_param($types, $par_1, $par_2, $par_3);
			       
				     // 4 params
		             if (strlen($types)==4)
	                  $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4);
					  
					  // 5 params
		             if (strlen($types)==5)
					    $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5);
					 
				   
					  // 6 params
		              if (strlen($types)==6)
	                  $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6);
					  
					  // 7 params
		              if (strlen($types)==7)
	                  $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7);
					  
					  // 8 params
					  if (strlen($types)==8)
					    $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8);
					   
					  // 9 params
		              if (strlen($types)==9)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9);
					  
					  // 10 params
		              if (strlen($types)==10)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10);
					  
					  // 11 params
		              if (strlen($types)==11)
					     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11);
					   
					  // 12 params
		              if (strlen($types)==12)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12);
					  
					  // 13 params
		              if (strlen($types)==13)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13);
					  
					  // 14 params
		              if (strlen($types)==14)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14);
					  
					  // 15 params
		             if (strlen($types)==15)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15);
					  
					  // 16 params
		             if (strlen($types)==16)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15, $par_16);
					  
					  // 17 params
		             if (strlen($types)==17)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15, $par_16, $par_17);
					  
					  // 18 params
		             if (strlen($types)==18)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15, $par_16, $par_17, $par_18);
					  
					  // 19 params
		             if (strlen($types)==19)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15, $par_16, $par_17, $par_18, $par_19);
					  
					  // 20 params
		             if (strlen($types)==20)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15, $par_16, $par_17, $par_18, $par_19, $par_20);
					  
					  // 21 params
		             if (strlen($types)==21)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15, $par_16, $par_17, $par_18, $par_19, $par_20, $par_21);
					  
					  // 22 params
		             if (strlen($types)==22)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15, $par_16, $par_17, $par_18, $par_19, $par_20, $par_21, $par_22);
					  
					  // 23 params
		             if (strlen($types)==23)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15, $par_16, $par_17, $par_18, $par_19, $par_20, $par_21, $par_22, $par_23);
					  
					  // 24 params
		             if (strlen($types)==24)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15, $par_16, $par_17, $par_18, $par_19, $par_20, $par_21, $par_22, $par_23, $par_24);
					  
					  // 25 params
		             if (strlen($types)==25)
	                     $stmt->bind_param($types, $par_1, $par_2, $par_3, $par_4, $par_5, $par_6, $par_7, $par_8, $par_9, $par_10, $par_11, $par_12, $par_13, $par_14, $par_15, $par_16, $par_17, $par_18, $par_19, $par_20, $par_21, $par_22, $par_23, $par_24, $par_25);
				
			       // Execute
		           $stmt->execute();
			   
			       // Return
			       return ($stmt->get_result());
			   }
			   else throw new Exception("Invalid query"); 
		  
	  }
	  
	  function showErr($err, $size=550, $class="inset_red_14")
      { 	   
      ?>

          <table width="<? print $size; ?>" border="0" cellspacing="0" cellpadding="0">
          <tr>
          <td width="50"><img src="../../template/GIF/panel_err_left.png" /></td>
          <td width="<? print ($size-55); ?>" background="../../template/GIF/panel_err_middle.png" class="<? print $class; ?>" align="left">
          <? print $err; ?></td>
          <td width="5"><img src="../../template/GIF/panel_err_right.png" /></td>
          </tr>
          </table>

   <?
   }
   
   function showOk($err, $size=550, $class="inset_green_14")
   {
   ?>
        <br />
        <table width="<? print $size; ?>" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td width="50"><img src="../../template/GIF/panel_ok_left.gif" /></td>
        <td width="<? print ($size-55); ?>" background="../../template/GIF/panel_ok_middle.gif" class="<? print $class; ?>">
        <div align="left">
		<? 
		   print $err; 
		?>
        </div>
        </td>
        <td width="5"><img src="../../template/GIF/panel_ok_right.gif" /></td>
        </tr>
        </table>
        <br />

   <?
}

	
     function timeFromBlock($block)
	 {
		$dif=abs($block-$_REQUEST['sd']['last_block']); 
	    	
		if ($dif<60) return $dif." minutes";
		else if ($dif>60 && $dif<=1440) return round($dif/60)." hours";
		else if ($dif>1440 && $dif<=43200) return round($dif/1440)." days";
		else if ($dif>43200 && $dif<=525600) return round($dif/43200)." months";
		else if ($dif>525600) return round($dif/525600)." years";
	 }
	
	 function redirect($link)
	 {
		 print "<script>window.location='".$link."'</script>";
	 }
	 
	 function hexToStr($hex)
    {
      $string='';
      for ($i=0; $i < strlen($hex)-1; $i+=2)
      {
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
      }
      return $string;
    }
	
	 function sendSMS($dest, $mes)
	 {
		 global $sms_username, $sms_password, $errstr;
		
		$sms_username = "anno17771";
        $sms_password = "dicatrenu";
       
        # Construct an SMS object
        $sms = new SMS();

        # Set the destination address 
        $sms->setDA($dest);

        # Set the source address
        $sms->setSA("chainrepublik");

        # Set the user reference
        $sms->setUR("AF31C0D");

        # Set delivery receipts to 'on'
        $sms->setDR("1");

        # Set the message content
        $sms->setMSG($mes);

        # Send the message and inspect the responses
        $responses = send_sms_object($sms);
		
		if ($responses==false) 
		{
			print "Error ".$errstr;
			return false;
		}
		
		return true;
	 }
	  	  
	  function getTrackID()
	  {
         $t=str_replace(".", "", time());
	     $t=str_replace(" ", "", $t);	  
		 $t=$t.rand(0,9000);
		 return round($t);
	  }
	  
	  
	  
       function begin()  {  mysqli_query($this->con, "BEGIN");  }
       function commit() {  mysqli_query($this->con, "COMMIT");  }
       function rollback() { mysqli_query($this->con, "ROLLBACK"); }
	   
  function getAbsTime($interval, $past=true)
  {
	if ($past==true)  
	  $interval=time()-$interval;
	else
	  $interval=$interval-time();
	
    if ($force_interval=="")
	{
    if ($interval<60) 
	  {
	     $time=$interval." seconds";		 
		 if ($interval==1) $time=$interval." second";
	  }	 
    if ($interval>=60 && $interval<3600) 
	{
	   $time=round($interval/60)." minutes";
	   if (round($interval/60)==1) $time=round($interval/60)." minute";
	}
	   
    if ($interval>=3600 && $interval<86400) 
	{
	   $time=round($interval/3600)." hours";
	   if (round($interval/3600)==1) $time=round($interval/3600)." hour";
	}
	   
    if ($interval>=86400 && $interval<2592000) 
	{
	   $time=round($interval/86400)." days";
	   if (round($interval/86400)==1) $time=round($interval/86400)." day";
	}
	   
    if ($interval>=2592000 && $interval<31104000) 
	{
	   $time=round($interval/2592000)." months";
	   if (round($interval/2592000)==1) $time=round($interval/2592000)." month";
	}
	
	if ($interval>=31104000) 
	{
	   $time=round($interval/31104000)." years";
	   if (round($interval/31104000)==1) $time=round($interval/31104000)." year";
	}   
	}
	
	return $time;
  }
	
	

    function newAct($act, $tID="0000000000")
    {
	   if ($_SERVER['HTTP_CF_CONNECTING_IP']!="") 
	      $IP=$_SERVER['HTTP_CF_CONNECTING_IP'];
	   else
	      $IP=$_SERVER['REMOTE_ADDR'];
	   
	   $query="INSERT INTO actions
                       SET userID=?,
			               act=?,
						   country=?,
                           tstamp=?,
                           IP=?,
						   mID=?,
						   tID=?,
				           URL=?";
	   
	   $this->execute($query, 
	                  "ississss", 
					  $_REQUEST['ud']['ID'], 
					  $act, 
					  $_SERVER["HTTP_CF_IPCOUNTRY"], 
					  time(), 
					  $IP, 
					  $_SESSION['mID'], 
					  $tID, 
					  $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	   
	   $query="UPDATE web_users 
	              SET online=? 
			    WHERE ID=?";
	   $this->execute($query, "ii", time(), $_REQUEST['ud']['ID']);
	}
  
    
    function bb_parse($string) 
	{
	    while (preg_match_all('`\[(.+?)=?(.*?)\](.+?)\[/\1\]`', $string, $matches)) foreach ($matches[0] as $key => $match) {
            list($tag, $param, $innertext) = array($matches[1][$key], $matches[2][$key], $matches[3][$key]);
            switch ($tag) {			    
                case 'b': $replacement = "<strong>$innertext</strong>"; break;
                case 'i': $replacement = "<em>$innertext</em>"; break;
				case 'u': $replacement = "<u>$innertext</u>"; break;
                case 'size': $replacement = "<span style=\"font-size: $param;\">$innertext</a>"; break;
                case 'color': $replacement = "<span style=\"color: $param;\">$innertext</a>"; break;
                case 'center': $replacement = "<div class=\"centered\">$innertext</div>"; break;
                case 'q': $replacement = "<blockquote cite='' class='font_14'>$innertext</blockquote>"; break;
				case 'video' : $replacement="<embed width='600' height='400' src='https://www.youtube.com/v/$innertext'>"; break;
                case 'url': $replacement = "<a target='blank' href='" . ($param? $param : $innertext) . "'>$innertext</a>"; break;
                case 'img':
                    list($width, $height) = preg_split('`[Xx]`', $param);
                    $replacement = "<img style=\"max-width:350px;\" border=\"0\" src=\"$innertext\" " . (is_numeric($width)? "width=\"$width\" " : '') . (is_numeric($height)? "height=\"$height\" " : '') . '/>';
                break;
            }
            $string = str_replace($match, $replacement, $string);
        }
        return $string;
    }
	
	function noEscape($str)
	{
		$str=str_replace("<", "", $str);
		$str=str_replace(">", "", $str);
		return $str;
	}
	
	 function isLoggedIn()
	 {
		 if ($_SESSION['userID']>0)
		   return true;
		 else
		   return false;
	 }
	
	function splitNumber($num, $pos)
	{
		$v=explode(".", $num);
		if (sizeof($v)==1) $v[1]="00";
		return $v[$pos];
	}
	
	// Is hash
	function isHash($var)
	{
		  if (preg_match("/^[A-Fa-f0-9]{64}$/", $var)==1)
		     return true;
		  else 
		     return false;
    }
	
	// Is base64 encoded string ?
    function isBase64($txt) 
    {
		if (preg_match("%^[a-zA-Z0-9/+]*={0,2}$%", $txt)==1)
           return true;
        else 
           return false;
    }
	
	// Is symbol ?
    function isSymbol($txt, $length) 
    {
		if (preg_match("%^[A-Z0-9]{".$length."}$%", $txt)==1)
           return true;
        else 
           return false;
    }
	
	function isAdr($adr) 
	{
		// Default ?
		if ($adr=="default")
		    return true;
		
		// Length valid
        if (strlen($adr)!=120) 
        return false;
	    
        // Characters
	    if ($this->isBase64($adr)==false)
		   return false;
		   
        // Return
	    return true;
	}
	
    function isValidName($name)
	{
		// Length
		if (strlen($name)<5 || 
		    strlen($name)>20)
	    return false;
		
		// Valid ?
		if (preg_match("/^[a-zA-Z0-9]{0,20}$/", $name)==1)
		   return true;
		else 
		   return false;
	}
	
	function isName($name)
	{
		// Length
		if ($this->isValidName($name)==false)
		   return false;
	
		// Load adr
		$query="SELECT * 
		          FROM adr 
			     WHERE name=?"; 
				 
		// Execute		 
	    $result=$this->execute($query, 
	                          "s", 
				              $name); 
									
		// Has data
		if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	// Is country code ?
	function isCou($txt)
	{
		if (preg_match("/^[A-Z]{2}$/", $txt)==1)
           return true;
        else 
		   return false;
	}
	
	// Is link
	function isLink($url)
	{
		if (filter_var($url, FILTER_VALIDATE_URL)) 
          return true;
		else
		  return false;
	}
	
	// Is email
	function isEmail($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
          return true;
		else
		  return false;
	}
     
	 // Is integer
	function isInt($int)
	{
		if (filter_var($int, FILTER_VALIDATE_INT)==true) 
          return true;
		else
		  return false;
	}
	
	// Is float
	function isFloat($float)
	{
		if (filter_var($float, FILTER_VALIDATE_FLOAT)) 
          return true;
		else
		  return false;
	}
	
	// Is IP
	function isIP($ip)
	{
		if (filter_var($ip, FILTER_VALIDATE_IP)) 
          return true;
		else
		  return false;
	}
		
	// Is country
	function isCountry($name)
	{
		// Valid symbol
		if ($this->isCou($name)==false)
		   return false;
		   
		// Load
		$query="SELECT * 
		          FROM countries 
				 WHERE code=?";
	    
		// Result
		$result=$this->execute($query, 
	                          "s", 
				              $name);
			 
		// Country exist ?
		if (mysqli_num_rows($result)==false)
		   return false;
		else
		   return true;
	}
	 
	function countryFromCode($code)
	{
		// Valid code ?
		if ($this->isCou($code)==false)
			return false;
			
		// Load
		$query="SELECT * 
		          FROM countries 
				 WHERE code=?";
	    
		// Result
		$result=$this->execute($query, 
	                          "s", 
				              $code);
		
		 // Load data ?
	     $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		 // Return
		 return ucfirst(strtolower($row['country']));
	}
	
	function isString($str)
	{
		// Check each chatacter
		for ($a=0; $a<=strlen($str)-1; $a++)
		  if (ord($str[$a])<32 || 
		      ord($str[$a])>255)
			     return false;
				
		 
		// Return
		return true;
	}
	
	function isTitle($title)
	{
		// String ?
		if ($this->isString($title)==false)
		   return false;
		   
		// Length
		if (strlen($title)<2 || strlen($title)>100)
		  return false;
		  
	    // Passed
		return true;
	}
	
	function isDesc($desc)
	{
		// String ?
		if ($this->isString($desc)==false)
		   return false;
		   
		// Length
		if (strlen($desc)<5 || strlen($desc)>1000)
		  return false;
		  
		// Passed
		return true;
	}
	
	function isPic($pic)
	{
		// Link ?
		if ($this->isLink($pic)==false)
		   return false;
		   
		// Ends with .jpg ?
		if (substr($pic, strlen($pic)-4)!=".jpg" && 
		   substr($pic, strlen($pic)-5)!=".jpeg" && 
		   substr($pic, strlen($pic)-4)!=".png" && 
		   substr($pic, strlen($pic)-4)!=".gif")
	   return false;
		   
		// Ok
		return true;
	}
	
	function isID($ID)
	{
		// Tweets
		$query="SELECT * 
		          FROM tweets 
				 WHERE tweetID=?";
				 
		// Result
		$result=$this->execute($query, 
	                          "i", 
				              $ID);
			 
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		
		// Comments
		$query="SELECT * 
		          FROM comments 
				 WHERE comID=?";
				 
		// Result
		$result=$this->execute($query, 
	                          "i", 
				              $ID);
			 
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		
		// Assets
		$query="SELECT * 
		          FROM assets 
				 WHERE assetID=?";
				 
		// Result
		$result=$this->execute($query, 
	                          "i", 
				              $ID);
			 
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		   
		// Workplaces
		$query="SELECT * 
		          FROM workplaces 
				 WHERE workplaceID=?";
				 
		// Result
		$result=$this->execute($query, 
	                          "i", 
				              $ID);
			 
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		   
		// Companies
		$query="SELECT * 
		          FROM companies 
				 WHERE comID=?";
				 
		// Result
		$result=$this->execute($query, 
	                          "i", 
				              $ID);
			 
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		   
		// Laws
		$query="SELECT * 
		          FROM laws 
				 WHERE lawID=?";
				 
		// Result
		$result=$this->execute($query, 
	                          "i", 
				              $ID);
			 
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		   
		// Stocuri
		$query="SELECT * 
		          FROM stocuri 
				 WHERE stocID=?";
				 
		// Result
		$result=$this->execute($query, 
	                          "i", 
				              $ID);
			 
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
	}
	
	// Has attribute ?
	function hasAttr($adr, $attr)
	{
		// Is address ?
		if ($this->isAdr($adr)==false)
		  return false;
		  
	   // Check attribute
	   if ($attr!="ID_RES_REC" && 
	       $attr!="ID_TRUST_ASSET")
	   return false;
	   
	   // Has attribute ?
	   $query="SELECT * 
	             FROM adr_attr 
				WHERE adr=? 
				  AND attr=?";
				  
	   // Result
	   $result=$this->execute($query, 
	                         "ss", 
				             $adr,
						     $attr);
			 
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	function isAsset($symbol)
	{
		// Symbol valid ?
		if (!$this->isSymbol($symbol, 5)==false 
		    && !$this->isSymbol($symbol, 6)==false)
		return false;
		
		// Asset exist ?
		$query="SELECT * 
		          FROM assets 
				 WHERE symbol=?"; 
				 
	   // Result
	   $result=$this->execute($query, 
	                         "s", 
				             $symbol);
			 
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	function isCur($cur)
	{
		// CRC ?
		if ($cur=="CRC")
		  return true;
		  
	    // Not CRC ?
		if ($this->isAsset($cur)==false)
		   return false;
		
		// Return
		return true;
	}
	
	function isStringID($ID)
	{
		// Length
		if (strlen($ID)<5 || 
		    strlen($ID)>50)
	    return false;
		
		// String ID ?
		if (preg_match("%^[A-Z0-9_]+$%", $ID)==1)
           return true;
        else 
		   return false;
	}
	
	// Registered address ?
	function isRegistered($adr)
	{
		// Is address ?
		if ($this->isAdr($adr)==false)
		   return false;
		  
	   // Load adr info
	   $query="SELECT * 
	             FROM adr 
				WHERE adr=? 
				  AND cou<>''";
				  
	  // Result
	  $result=$this->execute($query, 
	                         "s", 
				             $adr);
			 
	  // Has data ?
	  if (mysqli_num_rows($result)>0)
		 return true;
	  else
		 return false;
	}
	
	function isGovAdr($adr)
	{
		// Valid address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
		// Find company
		$query="SELECT * 
		          FROM countries 
				 WHERE adr=?";
				 
	   // Result
	   $result=$this->execute($query, 
	                         "s", 
				             $adr);
			 
	  // Has data ?
	  if (mysqli_num_rows($result)>0)
		 return true;
	  else
		 return false;
	}
	
	function isCitAdr($adr)
	{
		// Valid address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
		// No government address or company address ?
		if ($this->isGovAdr($adr)==false && 
	        $this->isCompanyAdr($adr)==false &&
		    $this->isOrgAdr($adr)==false &&
		    $this->isRegistered($adr)==true)
		return true;
		else
		return false;
	}
	
	function isCompanyAdr($adr)
	{
		// Valid address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
	    // Find company
		$query="SELECT * 
		          FROM companies 
				 WHERE adr=?";
				 
	   // Result
	   $result=$this->execute($query, 
	                         "s", 
				             $adr);
			 
	  // Has data ?
	  if (mysqli_num_rows($result)>0)
		 return true;
	  else
		 return false;
	}
	 
	 function isOrgAdr($adr)
	{
		// Valid address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
	    // Find company
		$query="SELECT * 
		          FROM orgs 
				 WHERE adr=?";
				 
	   // Result
	   $result=$this->execute($query, 
	                         "s", 
				             $adr);
			 
	  // Has data ?
	  if (mysqli_num_rows($result)>0)
		 return true;
	  else
		 return false;
	}
	
	function isCompanyAdrExt($adr, $comID)
	{
		// Valid address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
	    // Find company
		$query="SELECT * 
		          FROM companies 
				 WHERE adr=? 
				   AND comID=?";
				 
	   // Result
	   $result=$this->execute($query, 
	                         "si", 
				             $adr,
							 $comID);
			 
	  // Has data ?
	  if (mysqli_num_rows($result)>0)
		 return true;
	  else
		 return false;
	}
	
	function getComType($comID)
	{
		// Find company
		$query="SELECT * 
		          FROM companies 
				 WHERE comID=?";
				 
	   // Result
	   $result=$this->execute($query, 
	                         "i", 
				             $comID);
			 
	  // Load data ?
	  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
	  // Return
	  return $row['tip'];
	}
	
	function adrFromName($name)
	{
		// Valid name ?
		if (!$this->isName($name))
		   return $name;
		
		// Load name data
		$query="SELECT * 
		          FROM adr 
				 WHERE name=?"; 
				 
	   // Result
	   $result=$this->execute($query, 
	                         "s", 
				             $name);
			 
	  // Load data ?
	  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
	  // Return
	  return $row['adr'];   
	}
	
	function nameFromAdr($adr)
	{
		// Valid name ?
		if ($this->isAdr($adr)==false)
		   return $adr;
		   
		// Load name data
		$query="SELECT * 
		          FROM adr 
				 WHERE adr=?";
				 
	   // Result
	   $result=$this->execute($query, 
	                         "s", 
				             $adr);
			 
	  // Load data ?
	  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
	  // Return
	  return $row['name'];   
	}
	
	function isGovernor($adr, $cou)
	{
	    // Address ?
		if ($this->isAdr($adr)==false)
		   return false;
		
		// Country ?	
		if ($this->isCountry($cou)==false)
		   return false;
		   
	    // Load address details
		$query="SELECT * 
		          FROM adr 
				 WHERE adr=? 
				   AND cou=?";
				   
	   // Result
	   $result=$this->execute($query, 
	                         "ss", 
				             $adr,
							 $cou);
			 
	  // Load data ?
	  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	   
	  // Voting rights
	  $query="SELECT COUNT(*) AS total 
	           FROM adr 
			  WHERE pol_endorsed>? 
			    AND cou=?";
				
	  // Result
	  $result=$this->execute($query, 
	                         "is", 
				             $row['pol_endorsed'],
							 $cou);
			 
	  // Load data ?
	  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
	  // In the first 20 ?
	  if ($row['total']<20)
	     return true;
	  else
	     return false;
	   
	}
	
	
	// Can buy product ?
	function canBuy($adr, $prod, $qty, $acc)
	{
		// Is address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
		// Valid product ?
		if ($this->isProd($prod)==false)
		   return false;
		   
		// Address type
		$type=$this->getAdrOwnerType($adr);
		   
		// Can buy ?
		$query="SELECT * 
		          FROM allow_trans 
				 WHERE receiver_type=? 
				   AND prod=? 
				   AND can_buy=?";
				   
		// Result
	    $result=$this->execute($query, 
	                           "sss", 
				               $type,
							   $prod,
							   "YES");
							   
		// Has data ?
		if (mysqli_num_rows($result)>0)
		{
		   // Load data
		   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		   
		   // Is limited ?
		   if ($row['is_limited']=="YES")
		      if ($acc->getNetBalance($adr, $prod)+$qty>$row['max_hold'])
			     return false;
		   
		   // Return
		   return true;
		}
		else
		   return false;
	}
	
	// Can buy product ?
	function canSell($adr, $prod, $qty)
	{
		// Is address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
		// Valid product ?
		if ($this->isProd($prod)==false)
		   return false;
		   
		// Qty
		if ($qty<0.0001)
		   return false;
		  
		// Address type
		$type=$this->getAdrOwnerType($adr);
		   
		// Can buy ?
		$query="SELECT * 
		          FROM allow_trans 
				 WHERE receiver_type=? 
				   AND prod=? 
				   AND can_sell='YES'";
				   
		// Result
	    $result=$this->execute($query, 
	                           "ss", 
				               $type,
							   $prod);
							   
		// Has data ?
		if (mysqli_num_rows($result)>0)
		  return true;
		else
		   return false;
	}
	 
	
	// Can rent product ?
	function canRent($prod)
	{
		// Valid product ?
		if ($this->isProd($prod)==false)
		   return false;
		
		// Can buy ?
		$query="SELECT * 
		          FROM allow_trans 
				 WHERE receiver_type=? 
				   AND prod=? 
				   AND can_rent='YES'"; 
				   
		// Result
	    $result=$this->execute($query, 
	                           "ss",
							   "ID_CIT", 
				               $prod);
							   
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	// Has product type on stock ?
	function hasProd($adr, $prod)
	{
		// Is address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
		// Valid product ?
		if ($this->isProd($prod)==false)
		   return false;
		   
		// Load data
		$query="SELECT * 
		          FROM stocuri 
				 WHERE adr=? 
				   AND tip=?";
				   
		// Result
	    $result=$this->execute($query, 
	                           "ss", 
				               $adr,
							   $prod);
							   
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	// Returns address owner type
	function getAdrOwnerType($adr)
	{
		// Is address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
	    // Government ?
		$query="SELECT * 
		          FROM countries 
				 WHERE adr=?";
				 
	    // Result
	    $result=$this->execute($query, 
	                           "s", 
				               $adr);
				 
	    // Has data ?
		if (mysqli_num_rows($result)>0)
		   return "ID_GUV";
		
		// Company ?
		$query="SELECT * 
		          FROM companies 
				 WHERE adr=?";
				 
	    // Result
	    $result=$this->execute($query, 
	                           "s", 
				               $adr);
				 
	    // Has data ?
		if (mysqli_num_rows($result)>0)
		{
			// Row
		    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			// Return
			return ($row['tip']);
		}
		
		// Citizen
		return "ID_CIT";
	}
	
	function getEnergy($adr)
	{
		$query="SELECT SUM(amount) AS total 
		          FROM trans_pool 
				 WHERE src=? 
				   AND cur=?";
				   
		// Result
	    $result=$this->execute($query, 
	                           "ss", 
				               $adr,
							   "ID_ENERGY");
							   
	   // Row
	   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	   
	   // Energy
	   $energy=$row['total'];
	   
	   // Load adr data
	   $query="SELECT * 
	             FROM adr 
				WHERE adr=?";
				
		// Result
	    $result=$this->execute($query, 
	                           "s", 
				               $adr);
							   
	   // Row
	   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	   
	   // Energy
	   $energy=$energy+$row['energy'];
	   
	   // Return
	   return $energy;
	}
	 
	 // Check account energy
	 function checkEnergy($req=0.1)
	 {
		 if ($this->getEnergy($_REQUEST['ud']['adr'])<$req)
            return false;
		 else
		  return true;
	 }
	
	// Is valid product ?
	function isProd($prod)
	{
		// String ID ?
		if ($this->isStringID($prod)==false)
		   return false;
		 
		// Product exist ?
		$query="SELECT * 
		          FROM tipuri_produse 
				 WHERE prod=?"; 
				 
		 // Load
	     $result=$this->execute($query, 
	                           "s", 
				               $prod);
							   
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	// Is valid licence ?
	function isLic($lic)
	{
		// String ID ?
		if ($this->isStringID($lic)==false)
		   return false;
		   
		// Product exist ?
		$query="SELECT * 
		          FROM tipuri_licente 
				 WHERE tip=?";
				 
		 // Load
	     $result=$this->execute($query, 
	                           "s", 
				               $lic);
							   
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	// Buy split ?
	function buySplit($adr, $prod, $amount)
	{
		// Is address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
	    // Prod ?
		if ($this->isProd($prod)==false)
		   return false;
		   
		// Amount
		if ($amount<0)
		   return false;
		   
		// Receiver type
		$rec_type=$this->getAdrOwnerType($adr);
		
		// Load data
		$query="SELECT * 
		          FROM allow_trans 
				 WHERE rceiver_type=? 
				   AND prod=?";
				   
		// Result
		$result=$this->execute($query, 
	                           "ss", 
				               $rec_type,
							   $prod);
							   
	   // Load data ?
	   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
	    // Return
		if ($row['buy_split']=="YES")
		   return true;
		else
		   return false;
	}
	
	// Get distance between countries
	function getDist($cou_1, $cou_2)
	{
		// Valid countries ?
		if ($this->isCountry($cou_1)==false ||
		    $this->isCountry($cou_2)==false)
	    return false;
		
		// Country 1 data
		$query="SELECT * 
		          FROM countries 
				 WHERE code=?";
				 
	    // Result
		$result=$this->execute($query, 
	                           "s", 
				               $cou_1);
				 
	   // Load data ?
	   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	   
	   // X and Y
	   $x1=$row['x'];
	   $y1=$row['y'];
	   
	   // Country 1 data
		$query="SELECT * 
		          FROM countries 
				 WHERE code=?";
				 
	   // Result
		$result=$this->execute($query, 
	                           "s", 
				               $cou_2);
				 
	   // Load data ?
	   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	   
	   // X and Y
	   $x2=$row['x'];
	   $y2=$row['y'];
	   
	   // Distance
	   return round(sqrt(pow(abs($x1-$x2), 2)+pow(abs($y1-$y2), 2)))*10;
	}
	
	function isMyAdr($adr)
	{
		// Is address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
		// Query
		$query="SELECT * 
		          FROM my_adr 
				 WHERE adr=?
				   AND userID=?";
				   
		// Result
		$result=$this->execute($query, 
	                           "si", 
				               $adr,
							   $_REQUEST['ud']['ID']);
							   
	   // Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
	
	   // Return
	   return false;
							   
	}
	
	function canSpend($adr)
	{
		// Is address ?
		if ($this->isAdr($adr)==false)
		   return false;
		  
	   // Autonomus company ?	   
	   if ($this->getAdrOwnerType($adr)=="ID_COM_AUTONOMUS")
	      return false;	
	}
	
	// Is  my address
	function isMine($adr)
	{
		// Is address ?
		if ($this->isAdr($adr)==false)
		   return false;
		   
		// My address ?
		$query="SELECT * 
		          FROM my_adr 
				 WHERE adr=? 
				   AND userID=?";
				   
		// Result
		$result=$this->execute($query, 
	                           "si", 
				               $adr,
							   $_REQUEST['ud']['ID']);
				   
		// Has data ?
		if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	// Baisc address check
	function basicCheck($fee_adr, 
	                    $adr, 
						$fee, 
						$template,
						$acc,
					    $req_energy=0)
	{
		// Logged in ?
		  if ($this->isLoggedIn()==false)
		  {
			 $this->template->showErr("You need to login to execute this operation");
			 return false;
		  }
		  
	     // Fee ?
		if ($fee<0.0001)
		{
			// Message
		   $template->showErr("Invalid fee");
		   
		   // Return
		   return false;
		}
		
		// Registered ?
		if ($this->isRegistered($fee_adr)==false || 
	        $this->isRegistered($adr)==false)
		{
		   // Message
		   $template->showErr("Invalid entry data");
		   
		   // Return
		   return false;
		}
		
		// Mine ?
		if ($this->isMine($fee_adr)==false || 
	        $this->isMine($adr)==false)
	    {
			// Message
			$template->showErr("Invalid entry data");
			
			// Return
	        return false;
		}
		
		// Fee adr balance
		if ($acc->getTransPoolBalance($adr, "CRC")<$fee)
		{
		   // Message
		   $template->showErr("Insufficient funds to pay the network fee");
		   
		   // Return
		   return false;
		   
		}
		
		// Energy
		if ($req_energy>0)
		{
			// Energy
			$energy=$this->getAdrData($adr, "energy");
			
			// Check
			if ($energy<$req_energy)
			{
				// Message
		        $template->showErr("Insuficient energy to execute this action");
		   
		        // Return
		        return false;
			}
		}
		
		
		// Passed
		return true;
	}
	
	function getUSD($amount)
	{
		return $amount;
	}
	
	function ownedCom($comID)
	{
		$query="SELECT *  
		          FROM companies 
				 WHERE comID=? 
				   AND owner=?";
		
		// Result
		$result=$this->execute($query, 
		                       "is", 
							    $comID,
							    $_REQUEST['ud']['adr']);	
		
		// Owner
		if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	function getComID($adr)
	{
		// Query
		$query="SELECT * 
		          FROM companies 
				 WHERE adr=?";
		
		// Result
		$result=$this->execute($query, 
		                       "s", 
							   $adr);	
		
		// Load data
		$row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return
		return $row['comID'];
	}
	
	function getComAdr($ID)
	{
		// Query
		$query="SELECT * 
		          FROM companies 
				 WHERE comID=?";
		
		// Result
		$result=$this->execute($query, 
		                       "i", 
							   $ID);	
		
		// Load data
		$row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return
		return $row['adr'];
	}
	
	function getComSymbol($ID)
	{
		// Query
		$query="SELECT * 
		          FROM companies 
				 WHERE comID=?";
		
		// Result
		$result=$this->execute($query, 
		                       "i", 
							   $ID);	
		
		// Load data
		$row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return
		return $row['symbol'];
	}
	
	function getMarketID($prod)
	{
		
		// Query
		$query="SELECT * 
		          FROM assets_mkts 
				 WHERE asset=? 
				   AND cur=?";
				   
		// Result
		$result=$this->execute($query, 
		                       "ss", 
							   $prod,
							   "CRC");	
		
		// Load data
		$row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return
		return $row['mktID'];
	}
	
	function hasTools ($comID)
	{
		// Load company data
		$query="SELECT * 
		          FROM companies AS com 
				  JOIN tipuri_companii AS tc ON com.tip=tc.tip 
				 WHERE com.comID=?";
				 
		// Result
		$result=$this->execute($query, 
		                       "i",
							   $comID);	
		
		// Load data
		$row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Tools
		$tools=$row['utilaje'];
		
		// On stock ?
		$query="SELECT * 
		          FROM stocuri 
				 WHERE adr=? 
				   AND tip=? 
				   AND qty=?";
				   
		// Result
		$result=$this->execute($query, 
		                       "ssi", 
							   $row['adr'],
							   $tools,
							   1);	
		
		// Has data
		if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	function hasBuilding($comID)
	{
		// Load company data
		$query="SELECT * 
		          FROM companies AS com 
				  JOIN tipuri_companii AS tc ON com.tip=tc.tip 
				 WHERE com.comID=?";
				 
		// Result
		$result=$this->execute($query, 
		                       "i",
							   $comID);	
		
		// Load data
		$row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Tools
		$building=$row['cladire'];
		
		// On stock ?
		$query="SELECT * 
		          FROM stocuri 
				 WHERE adr=? 
				   AND tip=? 
				   AND qty=?";
				   
		// Result
		$result=$this->execute($query, 
		                       "ssi", 
							   $row['adr'],
							   $building,
							   1);	
		
		// Has data
		if (mysqli_num_rows($result)>0)
		   return true;
		else
		   return false;
	}
	
	function getComTools($comID)
	{
		// Load company data
		$query="SELECT * 
		          FROM companies AS com 
				  JOIN tipuri_companii AS tc ON com.tip=tc.tip 
				 WHERE com.comID=?";
				 
		// Result
		$result=$this->execute($query, 
		                       "i",
							   $comID);	
		
		// Load data
		$row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Tools
		return $row['utilaje'];
	}
	
	function getComBuilding($comID)
	{
		// Load company data
		$query="SELECT * 
		          FROM companies AS com 
				  JOIN tipuri_companii AS tc ON com.tip=tc.tip 
				 WHERE com.comID=?";
				 
		// Result
		$result=$this->execute($query, 
		                       "i",
							   $comID);	
		
		// Load data
		$row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Tools
		return $row['cladire'];
	}
	
	function getRank($points, $type="text")
	{
		// No rank ?
		if ($points<1000) return "none";
		
		// Private
		if ($points>1000 && $points<=3000) 
		{
			if ($type=="text")
			   return "Private";
			else
			   return "../../template/GIF/ranks/r_1.png";
		}
		
		// Private first class
		if ($points>3000 && $points<=6000) 
		{
			if ($type=="text")
			   return "Private First Class";
			else
			   return "../../template/GIF/ranks/r_2.png";
		}
		
		// Sergeant
		if ($points>6000 && $points<=10000) 
		{
			if ($type=="text")
			   return "Sergeant";
			else
			   return "../../template/GIF/ranks/r_3.png";
		}
		
		// Staff Sergeant
		if ($points>10000 && $points<=15000) 
		{
			if ($type=="text")
			   return "Staff Sergeant";
			else
			   return "../../template/GIF/ranks/r_4.png";
		}
		
		// Sergeant first class
		if ($points>15000 && $points<=21000) 
		{
			if ($type=="text")
			   return "Sergeant First Class";
			else
			   return "../../template/GIF/ranks/r_5.png";
		}
		
		// Master Sergeant
		if ($points>21000 && $points<=28000) 
		{
			if ($type=="text")
			   return "Master Sergeant";
			else
			   return "../../template/GIF/ranks/r_6.png";
		}
		
		// First Sergeant
		if ($points>28000 && $points<=36000) 
		{
			if ($type=="text")
			   return "First Sergeant";
			else
			   return "../../template/GIF/ranks/r_7.png";
		}
		
		// Sergeant Major
		if ($points>36000 && $points<=45000) 
		{
			if ($type=="text")
			   return "Sergeant Major";
			else
			   return "../../template/GIF/ranks/r_8.png";
		}
		
		// Command Sergeant Major
		if ($points>45000 && $points<=55000) 
		{
			if ($type=="text")
			   return "Command Sergeant Major";
			else
			   return "../../template/GIF/ranks/r_9.png";
		}
		
		// Seargent Major of the Army
		if ($points>55000) 
		{
			if ($type=="text")
			   return "Seargent Major of the Army";
			else
			   return "../../template/GIF/ranks/r_10.png";
		}
	}
	
	function y($tstamp=0)
	{
		if ($tstamp==0)
		  return date("Y");
		else
		  return date("Y", $tstamp);
	}
	
	function m($tstamp=0)
	{
		if ($tstamp==0)
		  return date("n");
		else
		  return date("n", $tstamp);
	}
	
	function d($tstamp=0)
	{
		if ($tstamp==0)
		  return date("j");
		else
		  return date("j", $tstamp);
	}
	
	function day()
	{
		return date("D");
	}
	
	function month()
	{
		return date("F");
	}
	
	function month_from_number($n)
	{
		switch ($n)
		{
			case 1 : return "January"; break;
			case 2 : return "February"; break;
			case 3 : return "Mars"; break;
			case 4 : return "April"; break;
			case 5 : return "May"; break;
			case 6 : return "June"; break;
			case 7 : return "July"; break;
			case 8 : return "August"; break;
			case 9 : return "September"; break;
			case 10 : return "October"; break;
			case 11 : return "November"; break;
			case 12 : return "December"; break;
		}
	}
	 
	
	
	function h()
	{
		return round(date("H"));
	}
	
	function mi()
	{
		return round(date("i"));
	}
	
	function s()
	{
		return round(date("s"));
	}
	
	function getProdEnergy($prod)
	{
		switch ($prod)
		{
				// Cigars
		     	case "ID_CIG_CHURCHILL" : return 1; break;     
			    case "ID_CIG_PANATELA" : return 2; break;
		  	    case "ID_CIG_TORPEDO" : return 3; break;
			    case "ID_CIG_CORONA" : return 4; break;
			    case "ID_CIG_TORO" : return 5; break;
			
			    // Cocktails
		    	case "ID_SAMPANIE" : return 1.5; break;
			    case "ID_MARTINI" : return 3; break;
			    case "ID_MOJITO" : return 4.5; break;
			    case "ID_MARY" : return 6; break;
			    case "ID_SINGAPORE" : return 7.5; break;
			    case "ID_PINA" : return 9; break;
			
			    // Food (bread, meat, tomatoes, salad)
			    case "ID_CROISANT" : return 2; break;
			    case "ID_HOT_DOG" : return 4; break;
			    case "ID_PASTA" : return 6; break;
			    case "ID_BURGER" : return 8; break;
			    case "ID_BIG_BURGER" : return 10; break;
			    case "ID_PIZZA" : return 12; break;
				
				// Socks Q1 - 0.1 energy, 0.1 Electricity, 0.1 Oil, 0.1 Material, 0.01 leather
				case "ID_SOSETE_Q1" : return 1; break;
				
				// Socks Q2 - 0.2 energy, 0.2 Electricity, 0.2 Oil, 0.2 Material, 0.02 leather
				case "ID_SOSETE_Q2" : return 2; break;
				
				// Socks Q3 - 0.3 energy, 0.3 Electricity, 0.3 Oil, 0.3 Material, 0.03 leather
				case "ID_SOSETE_Q3" : return 3; break;
				
				// Camasa Q1 - 0.4 energy, 0.4 Electricity, 0.4 Oil, 0.4 Material, 0.04 leather
				case "ID_CAMASA_Q1" : return 2; break;
				
				// Camasa Q2 - 0.5 energy, 0.5 Electricity, 0.5 Oil, 0.5 Material, 0.05 leather
				case "ID_CAMASA_Q2" : return 3; break;
				
				// Camasa Q3 - 0.6 energy, 0.6 Electricity, 0.6 Oil, 0.6 Material, 0.06 leather
				case "ID_CAMASA_Q3" : return 4; break;
				
				// Boots Q1 - 0.7 energy, 0.7 Electricity, 0.7 Oil, 0.7 Material, 0.07 leather
				case "ID_GHETE_Q1" : return 3; break;
				
				// Boots Q2 - 0.8 energy, 0.8 Electricity, 0.8 Oil, 0.6 Material, 0.08 leather
				case "ID_GHETE_Q2" : return 4; break;
				
				// Boots Q3 - 0.9 energy, 0.9 Electricity, 0.9 Oil, 0.9 Material, 0.09 leather
				case "ID_GHETE_Q3" : return 5; break;
				
				// Pants Q1 - 43 energy
				case "ID_PANTALONI_Q1" : return 4; break;
				
				// Pants Q2 - 48 energy
				case "ID_PANTALONI_Q2" : return 5; break;
				
				// Pants Q3 - 52 energy
				case "ID_PANTALONI_Q3" : return 6; break;
				
				// Sweater Q1 - 56 energy
				case "ID_PULOVER_Q1" : return 5; break;
				
				// Sweater Q1 - 60 energy
				case "ID_PULOVER_Q2" : return 6; break;
				
				// Sweater Q1 - 65 energy
				case "ID_PULOVER_Q3" : return 7; break;
				
				// Coat Q1 - 69 energy
				case "ID_PALTON_Q1" : return 6; break;
				
				// Coat Q2 - 74 energy
				case "ID_PALTON_Q2" : return 7; break;
				
				// Coat Q3 - 78 energy
				case "ID_PALTON_Q3" : return 8; break;
				
				// --------------------------------- BIJUTERII --------------------------------------------
				
				// Ring Q1 - 30 energy
				case "ID_INEL_Q1" : return 1; break;
				
				// Ring Q2 - 60 energy
				case "ID_INEL_Q2" : return 2; break;
				
				// Ring Q3  - 90 energy
				case "ID_INEL_Q3" : return 3; break;
				
				// Earing Q1 - 60 energy
				case "ID_CERCEI_Q1" : return 2; break;
				
				// Earing Q2 - 90 energy
				case "ID_CERCEI_Q2" : return 3; break;
				
				// Earing Q3 - 120 energy
				case "ID_CERCEI_Q3" : return 4; break;
				
				// Pandant Q1 - 90 energy
				case "ID_COLIER_Q1" : return 3; break;
				
				// Pandant Q2 - 120 energy
				case "ID_COLIER_Q2" : return 4; break;
				
				// Pandant Q3 - 150 energy
				case "ID_COLIER_Q3" : return 5; break;
				
				// Watch Q1 - 120 energy
				case "ID_CEAS_Q1" : return 4; break;
				
				// Watch Q2 - 150 energy
				case "ID_CEAS_Q2" : return 5; break;
				
				// Watch Q3 - 180 energy
				case "ID_CEAS_Q3" : return 6; break;
				
				// Bracelet Q1 - 150 energy
				case "ID_BRATARA_Q1" : return 5; break;
				
				// Bracelet Q2 - 180 energy
				case "ID_BRATARA_Q2" : return 6; break;
				
				// Bracelet Q3 - 210 energy
				case "ID_BRATARA_Q3" : return 7; break;
				
				// --------------------------------MASINA---------------------------------------------------
				
				// Car Q1 - 450 energy (10 hours, 10 electricity, 10 oil, 10 gas, 1 iron, 5 plastic, 10 glass, 0.1 wood, 5 leather)
				case "ID_CAR_Q1" : return 5; break;
				
				// Car Q1 - 900 energy (20 hours, 20 electricity, 20 oil, 20 gas, 2 iron, 10 plastic, 20 glass, 0.2 wood, 10 leather)
				case "ID_CAR_Q2" : return 10; break;
				
				// Car Q1 - 1350 energy (30 hours, 30 electricity, 30 oil, 30 gas, 3 iron, 15 plastic, 30 glass, 0.3 wood, 15 leather)
				case "ID_CAR_Q3" : return 15; break;
				
				// ------------------------------------ CASA -----------------------------------------------
				
				// House Q1 - 1825 energy (30 hours, 30 electricity, 30 oil, 3 wood, 3 iron, 3 glass, 3 cement, 3 bricks, 3 plastic)
				case "ID_HOUSE_Q1" : return 10; break;
				
				// House Q1 - 3650 energy (60 hours, 60 electricity, 60 oil, 6 wood, 6 iron, 6 glass, 6 cement, 6 bricks, 6 plastic)
				case "ID_HOUSE_Q2" : return 20; break;
				
				// House Q1 - 5475 energy (90 hours, 90 electricity, 90 oil, 9 wood, 9 iron, 9 glass, 9 cement, 9 bricks, 9 plastic)
				case "ID_HOUSE_Q3" : return 30; break;
				
				// Wine
				case "ID_WINE" : return 5; break;
				
				// Gift
				case "ID_GIFT" : return 10; break;
			}
			
			return 0;
	}
			
	function isEnergyBooster($prod)
    {
        if ($prod=="ID_CIG_CHURCHILL" || 
            $prod=="ID_CIG_PANATELA" ||
            $prod=="ID_CIG_TORPEDO" ||
            $prod=="ID_CIG_CORONA" ||
            $prod=="ID_CIG_TORO" ||
            $prod=="ID_SAMPANIE" || 
            $prod=="ID_MARTINI" ||
            $prod=="ID_MOJITO" ||
            $prod=="ID_MARY" ||
            $prod=="ID_SINGAPORE" || 
            $prod=="ID_PINA" ||
            $prod=="ID_MOJITO" || 
            $prod=="ID_CROISANT" || 
            $prod=="ID_HOT_DOG" || 
            $prod=="ID_PASTA" ||
            $prod=="ID_BURGER" || 
            $prod=="ID_BIG_BURGER" || 
            $prod=="ID_PIZZA" ||
			$prod=="ID_WINE")
            return true;
        else
            return false;
    }
	
    function isEnergyProd($prod)
    {
		 if ($prod=="ID_CIG_CHURCHILL" ||
            $prod=="ID_CIG_PANATELA" ||
            $prod=="ID_CIG_TORPEDO" ||
            $prod=="ID_CIG_CORONA" ||
            $prod=="ID_CIG_TORO" ||
            $prod=="ID_SAMPANIE" ||
            $prod=="ID_MARTINI" ||
            $prod=="ID_MOJITO" ||
            $prod=="ID_MARY" ||
            $prod=="ID_SINGAPORE" ||
            $prod=="ID_PINA" ||
            $prod=="ID_CROISANT" ||
            $prod=="ID_HOT_DOG" ||
            $prod=="ID_PASTA" ||
            $prod=="ID_BURGER" ||
            $prod=="ID_BIG_BURGER" ||
            $prod=="ID_PIZZA" ||
            $prod=="ID_SOSETE_Q1" ||
			$prod=="ID_SOSETE_Q2" ||
			$prod=="ID_SOSETE_Q3" ||
            $prod=="ID_CAMASA_Q1" ||
			$prod=="ID_CAMASA_Q2" ||
			$prod=="ID_CAMASA_Q3" ||
            $prod=="ID_GHETE_Q1" ||
			$prod=="ID_GHETE_Q2" ||
			$prod=="ID_GHETE_Q3" ||
            $prod=="ID_PANTALONI_Q1" ||
			$prod=="ID_PANTALONI_Q2" ||
			$prod=="ID_PANTALONI_Q3" ||
			$prod=="ID_PULOVER_Q1" ||
			$prod=="ID_PULOVER_Q2" ||
            $prod=="ID_PULOVER_Q3" ||
            $prod=="ID_PALTON_Q1" ||
			$prod=="ID_PALTON_Q2" ||
			$prod=="ID_PALTON_Q3" ||
            $prod=="ID_INEL_Q1" ||
			$prod=="ID_INEL_Q2" ||
			$prod=="ID_INEL_Q3" ||
            $prod=="ID_CERCEI_Q1" ||
			$prod=="ID_CERCEI_Q2" ||
			$prod=="ID_CERCEI_Q3" ||
            $prod=="ID_COLIER_Q1" ||
			$prod=="ID_COLIER_Q2" ||
			$prod=="ID_COLIER_Q3" ||
            $prod=="ID_CEAS_Q1" ||
			$prod=="ID_CEAS_Q2" ||
			$prod=="ID_CEAS_Q3" ||
            $prod=="ID_BRATARA_Q1" ||
			$prod=="ID_BRATARA_Q2" ||
			$prod=="ID_BRATARA_Q3" ||
            $prod=="ID_CAR_Q1" ||
            $prod=="ID_CAR_Q2" ||
            $prod=="ID_CAR_Q3" ||
            $prod=="ID_HOUSE_Q1" ||
            $prod=="ID_HOUSE_Q2" ||
            $prod=="ID_HOUSE_Q3" ||
			$prod=="ID_WINE" ||
		    $prod=="ID_GIFT")
		return true;
			else
        return false;
    }
	 
	function isUsable($prod)
    {
		 if ($prod=="ID_SOSETE_Q1" ||
			$prod=="ID_SOSETE_Q2" ||
			$prod=="ID_SOSETE_Q3" ||
            $prod=="ID_CAMASA_Q1" ||
			$prod=="ID_CAMASA_Q2" ||
			$prod=="ID_CAMASA_Q3" ||
            $prod=="ID_GHETE_Q1" ||
			$prod=="ID_GHETE_Q2" ||
			$prod=="ID_GHETE_Q3" ||
            $prod=="ID_PANTALONI_Q1" ||
			$prod=="ID_PANTALONI_Q2" ||
			$prod=="ID_PANTALONI_Q3" ||
			$prod=="ID_PULOVER_Q1" ||
			$prod=="ID_PULOVER_Q2" ||
            $prod=="ID_PULOVER_Q3" ||
            $prod=="ID_PALTON_Q1" ||
			$prod=="ID_PALTON_Q2" ||
			$prod=="ID_PALTON_Q3" ||
            $prod=="ID_INEL_Q1" ||
			$prod=="ID_INEL_Q2" ||
			$prod=="ID_INEL_Q3" ||
            $prod=="ID_CERCEI_Q1" ||
			$prod=="ID_CERCEI_Q2" ||
			$prod=="ID_CERCEI_Q3" ||
            $prod=="ID_COLIER_Q1" ||
			$prod=="ID_COLIER_Q2" ||
			$prod=="ID_COLIER_Q3" ||
            $prod=="ID_CEAS_Q1" ||
			$prod=="ID_CEAS_Q2" ||
			$prod=="ID_CEAS_Q3" ||
            $prod=="ID_BRATARA_Q1" ||
			$prod=="ID_BRATARA_Q2" ||
			$prod=="ID_BRATARA_Q3" ||
            $prod=="ID_CAR_Q1" ||
            $prod=="ID_CAR_Q2" ||
            $prod=="ID_CAR_Q3" ||
			$prod=="ID_HOUSE_Q1" ||
            $prod=="ID_HOUSE_Q2" ||
            $prod=="ID_HOUSE_Q3" || 
            $prod=="ID_KNIFE" ||
            $prod=="ID_PISTOL" ||
            $prod=="ID_REVOLVER" ||
			$prod=="ID_SHOTGUN" || 
			$prod=="ID_MACHINE_GUN" || 
			$prod=="ID_SNIPER" || 
			$prod=="ID_GLOVES" || 
			$prod=="ID_GOGGLES" || 
			$prod=="ID_HELMET" || 
			$prod=="ID_BOOTS" || 
			$prod=="ID_SHIELD" || 
		    $prod=="ID_VEST")
		return true;
			else
        return false;
    }
	
	function hasQuality($prod)
    {
		if (strpos($prod, "Q1")>0 || 
		    strpos($prod, "Q2")>0 || 
			strpos($prod, "Q3")>0)
		return true;
		else
		return false;
    }
	 
	 function skipQuality($prod)
    {
		$prod=str_replace("_Q1", "", $prod);
		$prod=str_replace("_Q2", "", $prod);
		$prod=str_replace("_Q3", "", $prod);
		return $prod;
    }
    
    function canConsume($adr, $item)
    {
		// Is address
        if ($this->isAdr($adr)==false)
           throw new Exception("Invalid address");
        
        // Is energy booster ?
        if ($this->isEnergyBooster($item) && 
		    $this->isCitAdr($adr))
             return true;
        else
            return false;
    }
    
    function getProdItemEnergy($itemID)
    {
        // Is ID
        if ($this->isID($itemID))
            throw new Exception("Invalid item ID");
        
        // Load data
        $query="SELECT * 
		          FROM stocuri 
				 WHERE stocID=?";
        
        // Has data ?
        $result=$this->execute($query, 
		                       "i", 
							   $itemID);
        
        // Load data
        $row=mysqli_fetch_array($result, MYSQLI_ASSOC);
        
        // Is energy booster ?
        if ($this->isEnergyBooster($row['tip'])==false)
             throw new Exception("Invalid item ID");
        
        // Returns energy
        return $this->getProdEnergy($row['tip']);
    }
	
	function txtExplode($str)
	{
		$f=0;
		$s="";
		
		for ($a=0; $a<=strlen($str)-1; $a++)
		{
			if ($str[$a]!=" ") 
			   $f++;
			else
			   $f=0;
			   
		    $s=$s.$str[$a]; 
			
			if ($f>=50)
			{
			  $s=$s." "; 
			  $f=0;
			}
		}
		
		return $s;
	}
	
	function split($amount, $prec=2, $s1=18, $s2=14)
	{
		// Round
		$amount=round($amount, $prec);
		
		// Explode
		$v=explode(".", $amount);
		
		// Soze
		if (sizeof($v)==1) 
			$v[1]="00";
		
		// Return
		return "<span class='font_".$s1."'>".$v[0]."</span><span class='font_".$s2."'>.".$v[1]."</span>";
	}
	 
	
	// Returns the balance of default address
    function uCoins()
    {
        // Load balance
		$query="SELECT * 
		          FROM adr 
				 WHERE adr=?";
		
		// Has data ?
        $result=$this->execute($query, 
		                       "s", 
							   "default");
							   
		 // Load data
         $row=mysqli_fetch_array($result, MYSQLI_ASSOC);
							   
	    // Return
		return $row['balance'];					
    }
    
    function getRewardPool($reward)
    {
        // Undistributed coins
        $u=$this->uCoins();
        
        // Daily reward
        $daily=$u*0.01/365;
        
        // Reward type
        switch ($reward)
        {
            // Energy 10%
            case "ID_ENERGY" : $amount=$daily*0.10; 
                               break;
                               
            // Miners 10%
            case "ID_MINERS" : $amount=$daily*0.10; 
                               break;
                               
            // Press 10%
            case "ID_PRESS" : $amount=$daily*0.10; 
                              break;
				
			case "ID_COM" : $amount=$daily*0.05; 
                              break;
                               
            // Military 10%
            case "ID_MILITARY" : $amount=$daily*0.1; 
                                 break;
                               
            // Referers 5%
            case "ID_REFS" : $amount=$daily*0.1; 
                             break;
                               
            // Pol influence 5%
            case "ID_POL_INF" : $amount=$daily*0.1; 
                                break;
                               
            // Pol endorement 5%
            case "ID_POL_END" : $amount=$daily*0.05; 
                                break;
                               
            // Country pop 5%
            case "ID_COU_ENERGY" : $amount=$daily*0.05; 
                                   break;
                             
            // Country area 5%
            case "ID_COU_AREA" : $amount=$daily*0.05; 
                                 break;
                             
            // Military Units get 5%
            case "ID_MIL_UNITS" : $amount=$daily*0.05; 
                                  break;
				
		    // Political parties get 5%
            case "ID_POL_PARTIES" : $amount=$daily*0.05; 
                                    break;
				
		    // Nodes reward
            case "ID_NODES" : $amount=$daily*0.1; 
                                    break;
		}
        
        // Return
        return round($amount);
    }
    
	function getPaidRewards($reward)
	{
		// Query
	   	$query="SELECT SUM(par_f) AS total 
			          FROM rewards 
					 WHERE reward='".$reward."' 
					   AND block>?";
            
        // Has data ?
        $result=$this->execute($query, 
		                       "i", 
					           $_REQUEST['sd']['last_block']-1440);
		
		// Load data
        $row=mysqli_fetch_array($result, MYSQLI_ASSOC);
            
        // Get data
        $total=round($row['total']);
		
		// Empty ?
		if ($total=="") 
			$total=1;
		
		// Return
		return $total;
	}
	 
    function getRewardVal($adr, $reward, $block, $type="ID_ADR")
    {
        // Check reward
        if ($reward!="ID_ENERGY" && 
            $reward!="ID_REF" && 
            $reward!="ID_MILITARY" && 
            $reward!="ID_POL_INF" && 
            $reward!="ID_POL_END")
        throw new Exception("Invalid bonus - CUtils.java");
        
        // Energy reward
        if ($reward=="ID_ENERGY")
        {
            // Daily pool
            $pool=$this->getRewardPool($reward);
            
            // Load paid energy rewards in last 24 hours
            $query="SELECT SUM(par_f) AS total 
			          FROM rewards 
					 WHERE reward='ID_ENERGY' 
					   AND block>?";
            
            // Has data ?
            $result=$this->execute($query, 
		                          "i", 
							      $block-1440);
								  
			 // Load data
             $row=mysqli_fetch_array($result, MYSQLI_ASSOC);
            
            // Get data
            $total=round($row['total']);
            
            if ($total>0)
            {
               // Per point
               $per_point=round($pool/$total, 4);
            
               // Minimum ?
               if ($per_point<0.0001) 
                   $per_point=0.0001;
            
               // Maximum ?
               if ($per_point>0.01) 
                   $per_point=0.01;
            
               // Value
			   if ($type=="ID_ADR")
                  $val=$this->getEnergy($adr)*$per_point;
			   else
			      $val=$per_point;
            }
            else 
			{
				// Value
			   if ($type=="ID_ADR")
                  $val=$this->getEnergy($adr)*0.01;
			   else
			      $val=0.01;
			}
        }    
        
        return round($val, 4);
    }
	
	// Converts CRC to USD
	function toUSD($amount, $dec=2)
	{
		return round($_REQUEST['sd']['coin_price']*$amount, $dec); 
	}
	
	// Converts USD to CRC
	function toCRC($amount, $dec=2)
	{
		return round($_REQUEST['sd']['coin_price']/$amount, $dec); 
	}
	 
	 function reserved($packet, $par_name, $par_val)
	 {
		// Query
		$query="SELECT * 
		          FROM packets 
				 WHERE packet_type=? 
				   AND block=? 
				   AND ".$par_name."=?";
		
		// Result	  
		$result=$this->execute($query, 
		                       "sis", 
							   $packet, 
							   $_REQUEST['sd']['last_block']+1,
							   $par_val);	
		
		// Has data
		if (mysqli_num_rows($result)>0)
           return true;
		else
		   return false;
	}
	 
	 function isWorking($adr)
	 {
		 // Logged in
		 if (!$this->isLoggedIn())
			 return true;
		 
		 // Is address
        if ($this->isAdr($adr)==false)
           throw new Exception("Invalid address");
		 
		// Another work packet received ?
		if ($this->reserved("ID_WORK_PACKET", "fee_src", $adr))
			return true;
		 
		// Work process ?
		$query="SELECT * 
		          FROM work_procs 
				 WHERE adr=? 
				   AND end>?";
		 
		 // Result	  
		$result=$this->execute($query, 
		                       "si", 
							   $_REQUEST['ud']['adr'], 
							   $_REQUEST['sd']['last_block']);	
		
		// Has data
		if (mysqli_num_rows($result)>0)
           return true;
		
		// Not working
		return false;
	 }
	 
	 function hasProdLic($adr, $prod)
	 {
		 $query="SELECT * 
		           FROM stocuri AS st 
				   JOIN tipuri_licente AS tl ON st.tip=tl.tip 
				  WHERE st.adr=? 
				    AND tl.prod=?";
		 
		// Result	  
		$result=$this->execute($query, 
		                       "ss", 
							   $adr, 
							   $prod);	
		
		// Has data
		if (mysqli_num_rows($result)>0)
           return true;
		
		// Not working
		return false;
	 }
	 
	 function isAgent($com)
	 {
		 $query="SELECT * 
		           FROM companies 
				  WHERE (symbol=? 
				         OR comID=? 
						 OR adr=?) 
				     AND tip=?";
		 
		 // Result	  
		$result=$this->execute($query, 
		                       "ssss", 
							   $com, 
							   $com,
							   $com,
							   "ID_COM_AUTONOMOUS");	
		
		// Has data
		if (mysqli_num_rows($result)>0)
           return true;
		
		// Not working
		return false;
	 }
	 
	 function crop($pic, $w=30, $h=30)
	 {
		 print "../../../crop.php?src=".$this->noescape(base64_decode($pic))."&w=".$w."&h=".$h;
	 }
	 
	 function encode($str)
	 {
		 return(str_replace("+", "*", $str));
	 }
	 
	 function decode($str)
	 {
		 return(str_replace("*", "+", $str));
	 }
	 
	 function getQuality($prod)
	 {
		  // Q1
		 if (strpos($prod, "Q1")>0) 
			 return "Q1";
		 
		 // Q2
		 if (strpos($prod, "Q2")>0) 
			 return "Q2";
		 
		 // Q3
		 if (strpos($prod, "Q3")>0) 
			 return "Q3";
		 
		 // Q4
		 if (strpos($prod, "Q4")>0) 
			 return "Q4";
		 
		 // Q5
		 if (strpos($prod, "Q5")>0) 
			 return "Q5";
	 }
	 
	 function checkPass($pass)
	 {
		 if (hash("sha256", $pass)==$_REQUEST['ud']['pass'])
			return true;
		else
			return false;
	 }
	 
	 function getUserID($adr)
	 {
		 // Query
		 $query="SELECT * 
		           FROM my_adr 
				  WHERE adr=?";
		 
		 // Result	  
		$result=$this->execute($query, 
		                       "s", 
							   $adr);	
		 
		 // Load data ?
	     $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 // Return
		 return $row['userID'];
	 }
	 
	 function getPacketName($ID)
	 {
		switch ($ID)
		 {
			 // Transaction packet
		     case "ID_TRANS_PACKET" : return "Simple Transaction Packet"; break;
			 
			 // New ad packet
			 case "ID_NEW_AD_PACKET" : return "Ad Packet"; break;
			 
			 // Send message packet
			 case "ID_SEND_MES" : return "Private Message Packet"; break;
			 
			 // Escrowed transaction packet
			 case "ID_ESCROWED_TRANS_SIGN" : return "Escrowed Transaction Signature"; break;
			 
			 // New comment 
			 case "ID_COMMENT_PACKET" : return "Comment Packet"; break;
			 
			 // New tweet 
			 case "ID_NEW_TWEET_PACKET" : return "New Tweet Packet"; break;
			 
			 // Renew packet
			 case "ID_RENEW_PACKET" : return "Renew Packet"; break;
			 
			 // Vote delegate packet
			 case "ID_VOTE_DEL_PACKET" : return "Vote Delegate Packet"; break;
			 
			 // Vote packet
			 case "ID_VOTE_PACKET" : return "Vote Packet"; break;
			 
			 // Profile packet
			 case "ID_PROFILE_PACKET" : return "Profile Packet"; break;
			 
			 // New asset packet
			 case "ID_NEW_ASSET_PACKET" : return "Issue Asset Packet"; break;
			 
			 // New asset market paccket
			 case "ID_NEW_ASSET_MKT_PACKET" : return "New Asset Market Packet"; break;
			 
			 // New asset market pos packet
			 case "ID_NEW_REG_ASSET_MARKET_POS_PACKET" : return "New Asset Market Order Packet"; break;
			 
			 // Close asset market position packet
			 case "ID_REG_ASSET_MARKET_CLOSE_POS_PACKET" : return "Close Asset Market Order Packet"; break;
			 
			 // Read packet
			 case "ID_READ_PACKET" : return "Read Post Packet"; break;
			 
			 // Issue more assets packet
			 case "ID_ISSUE_MORE_ASSETS_PACKET" : return "Issue More Assets Packet"; break;
			 
			 // Add address attribute
			 case "ID_ADD_ATTR_PACKET" : return "Add Address Attribute Packet"; break;
			 
			 // Unfollow packet
			 case "ID_UNFOLLOW_PACKET" : return "Unfollow Address Packet"; break;
			 
			 // Follow packet
			 case "ID_FOLLOW_PACKET" : return "Follow Address Packet"; break;
			 
			 // Work
			 case "ID_WORK_PACKET" : return "Work Packet"; break;
			 
			 // Claim reward
			 case "ID_CLAIM_REWARD_PACKET" : return "Claim Reward Packet"; break;
			 
			 // Consume item
			 case "ID_CONSUME_ITEM_PACKET" : return "Consume Item Packet"; break;
				 
	         // Use item
			 case "ID_USE_ITEM_PACKET" : return "Use Item Packet"; break;
				 
			 // Address Registration
			 case "ID_ADR_REGISTER_PACKET" : return "New Address Registration Packet"; break;
			 
			 // Update workplace
			 case "ID_UPDATE_WORKPLACE_PACKET" : return "Update Workplace Packet"; break;
				 
			 // New workplace
			 case "ID_NEW_WORKPLACE_PACKET" : return "New Workplace Packet"; break;
				
			// New Comapny Packet
			case "ID_NEW_COMPANY_PACKET" : return "New Company Packet"; break;
				
			// Update profile Packet
			case "ID_UPDATE_PROFILE_PACKET" : return "Update Profile Packet"; break;
				
			// Rent Production Licence Packet
			case "ID_RENT_LIC_PACKET" : return "Rent Production Licence Packet"; break;
				
			// Change citizenship
			case "ID_ADR_CHG_CIT_PACKET" : return "Change Citizenship Packet"; break;
				
			// Travel Packet
			case "ID_ADR_TRAVEL_PACKET" : return "Travel Packet"; break;
				
		    // Gift Packet
			case "ID_GIFT_PACKET" : return "Welcome Gift Packet"; break;
				
			// Withdraw Funds Packet
			case "ID_WTH_FUNDS_PACKET" : return "Withdraw Funds Packet"; break;
				
			// Donate Packet
			case "ID_DONATE_ITEM_PACKET" : return "Donate Packet"; break;
				
		    // Set rent price packet
			case "ID_SET_RENT_PRICE_PACKET" : return "Set Rent Price Packet"; break;
				
			// Rent item packet
			case "ID_RENT_ITEM_PACKET" : return "Rent Item Packet"; break;
				
			// Endorse packet
			case "ID_ENDORSE_ADR_PACKET" : return "Endorse Packet"; break;
				
		    // New law packet
			case "ID_NEW_LAW_PACKET" : return "New Law Packet"; break;
				
			// Vote law packet
			case "ID_VOTE_LAW_PACKET" : return "Vote Law Packet"; break;
				
			// Join party packet
			case "ID_JOIN_ORG_PACKET" : return "Join Organization Packet"; break;
				
			// Leave party packet
			case "ID_LEAVE_ORG_PACKET" : return "Leave Organization Packet"; break;
				
			// New org prop packet
			case "ID_NEW_ORG_PROP_PACKET" : return "New Organization Proposal"; break;
				
			// Vote org prop packet
			case "ID_VOTE_ORG_PROP_PACKET" : return "Vote Organization Proposal"; break;
				
		    // Fight packet
			case "ID_FIGHT_PACKET" : return "Fight in War Packet"; break;
				
		    // Transfer adr packet
			case "ID_TRANSFER_ADR_PACKET" : return "Change Keys Packet"; break;
				
			// Update company
			case "ID_UPDATE_COM_PACKET" : return "Update Company Packet"; break;
				
			// Update company
			case "ID_REMOVE_EX_OFFER_PACKET" : return "Remove Exchange Offer Packet"; break;
				
			// Update company
			case "ID_NEW_EX_OFFER_PACKET" : return "New Exchange Offer Packet"; break;
		}
	}
	 
	function isOrg($type, $ID)
	{
		// Type
		if ($type!="ID_POL_PARTY" && 
		   $type!="ID_MIL_UNIT")
		return false;
		
		// Load data
		$query="SELECT * 
		          FROM orgs 
				 WHERE type=? 
				   AND orgID=?";
		
		 // Result	  
		$result=$this->execute($query, 
		                       "si", 
							   $type,
							   $ID);
		
		// Has data ?
		if (mysqli_num_rows($result)>0)
			return true;
		else
			return false;
	}
	 
	function getAdrData($adr, $col)
	{
		 // Is address
         if ($this->isAdr($adr)==false)
           throw new Exception("Invalid address");
		
		 // Column
		 if ($col!="cou" && 
			 $col!="name" && 
			 $col!="description" && 
			 $col!="ref_adr" && 
		 	 $col!="node_adr" && 
			 $col!="balance" && 
			 $col!="pic" && 
			 $col!="pol_inf" && 
			 $col!="energy" && 
			 $col!="energy_block" && 
			 $col!="loc" && 
		  	 $col!="pol_endorsed" && 
			 $col!="created" && 
			 $col!="expires" && 
			 $col!="war_points" && 
			 $col!="premium" && 
			 $col!="travel" && 
			 $col!="travel_cou" && 
			 $col!="work" &&
			 $col!="pol_party" && 
			 $col!="mil_unit")
		 throw new Exception("Invalid address");
		
		 // Load data
		 $query="SELECT * 
		           FROM adr 
				  WHERE adr=?";
		
		 // Result	  
		$result=$this->execute($query, 
		                       "s", 
							   $adr);
		
		 // Load data ?
	     $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return ?
		return $row[$col];
	}
	 
	function trustAsset($adr, $asset)
	{
		 // Is address
         if ($this->isAdr($adr)==false)
           throw new Exception("Invalid address");
		
		// Asset
		if (!$this->isAsset($asset))
			throw new Exception("Invalid asset");
		
		// Load data
		$query="SELECT * 
		          FROM adr_attr 
				 WHERE adr=? 
				   AND attr=? 
				   AND s1=?";
		
		// Result	  
		$result=$this->execute($query, 
		                       "sss", 
							   $adr,
							   "ID_TRUST_ASSET",
							   $asset);
		
		// Has data
		if (mysqli_num_rows($result)>0)
			return true;
		else
			return false;
	}
	 
	function isPrivKey($key)
	{
		// Base 64 ?
		if ($this->isBase64($key)==false)
			return false;
		
		// Private key ?
		if (strlen($key)!=192)
			return false;
		
		// Return true
		return true;
	}
	 
	function showEvents($adr)
	{
		// Load events
		$query="SELECT * 
		          FROM events 
				 WHERE adr=?
			  ORDER BY ID DESC 
				 LIMIT 0,25";
		
		// Load data
		$result=$this->execute($query, 
			    			   "s", 
							   $adr);	
	   
	  
		?>
        
          <table width="95%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="81%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="16%" align="center" class="bold_shadow_white_14">Time</td>
                </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
         <table width="90%" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
                <tr>
                <td width="82%" class="simple_gri_14">
                <?
				   if ($row['viewed']==0)
				     print "<strong>".$row['evt']."</strong>";
				   else
				     print $row['evt'];
				?>
                </td>
                <td width="18%" align="center" class="font_14">
                 <?
				   if ($row['viewed']==0)
				     print "<strong>".$this->timeFromBlock($row['block'])."</strong>";
				   else
				     print $this->timeFromBlock($row['block']);
				?>
                </td>
                </tr>
                <tr>
                <td colspan="2" ><hr></td>
                </tr>
          
          <?
			 }
		  ?>
            
        </table>
        
        <?
		
		// Set unread events to zero
		$query="UPDATE web_users 
		           SET unread_events=0 
				 WHERE ID=?";
				 
		$this->execute($query, 
					   "i", 
					   $_REQUEST['ud']['ID']);
		
		// Set events as read
		$query="UPDATE events 
		           SET viewed=? 
				 WHERE adr=?";
				   
		$this->execute($query, 
					   "is", 
					   time(), 
					   $adr);
	}
	 
	 function breakWord($word)
	 {
		 return preg_replace('/([^\s]{20})(?=[^\s])/', '$1'.' ', $word);
	 }
	 
	 function getCouAdr($cou)
	 {
		 // Valid country code ?
		 if (!$this->isCou($cou))
			 return '';
		 
		 // Query
		 $query="SELECT * 
		           FROM countries 
				  WHERE code=?";
		 
		 // Result
		 $result=$this->execute($query, 
				         	    "s", 
					            $cou);
		 
		 // Data
		 $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 // Return
		 return $row['adr'];
	 }
	 
	 function getCou()
	 {
		 // Country
		if ($_REQUEST['cou']=="")
			$cou=$_REQUEST['ud']['loc'];
		else
			$cou=$_REQUEST['cou'];
		
		 return $cou;
	 }
	 
	 function isCongressman($adr)
	 {
		 $found=false;
		 
		 // Valid address ?
		 if (!$this->isAdr($adr))
			 throw new Exception("Invalid address");
		 
		 
		 // Minimum 100
		 if ($this->getAdrData($adr, "pol_endorsed")<25)
			 return false;
		 
		 // Get address country
		 $cou=$this->getAdrData($adr, "cou");
		 
		 // Parse congress list
		 $query="SELECT * 
		           FROM adr 
				  WHERE cou=? 
			   ORDER BY pol_endorsed DESC 
			      LIMIT 0,25";
		 
		 // Result
		 $result=$this->execute($query, 
				         	    "s", 
					            $cou);
		 
		 // Data
		 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 if ($row['adr']==$adr)
				 $found=true;
		 
		 // Return
		 return $found;
	 }
	 
	 function isCongressActive($cou)
	 {
		 // Min 100 citizens ?
		 $query="SELECT COUNT(*) AS total 
		           FROM adr 
				  WHERE cou=?";
		 
		 // Result
		 $result=$this->execute($query, 
				         	    "s", 
					            $cou);
		 
		 // Load data
		 $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 // Total cit
		 $total_cit=$row['total'];
		 
		 // Min 10000 political influence ?
		 $query="SELECT SUM(pol_inf) AS total 
		           FROM adr 
				  WHERE cou=?";
		 
		 // Result
		 $result=$this->execute($query, 
				         	    "s", 
					            $cou);
		 
		 // Load data
		 $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 // Total cit
		 $total_pol_inf=$row['total'];
		 
		 // Min 25 citizens political endorsed ?
		 $query="SELECT COUNT(*) AS total 
		           FROM adr 
				  WHERE cou=? 
				    AND pol_endorsed>0";
		 
		 // Result
		 $result=$this->execute($query, 
				         	    "s", 
					            $cou);
		 
		 // Load data
		 $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 // Total cit
		 $total_pol_endorsed=$row['total'];
		 
		 // Return 
		 if ($total_cit>25 && 
			 $total_pol_inf>250 && 
			 $total_pol_endorsed>=10)
			 return true;
		 else
			 return false;
	 }
	 
	 
	 function isStateWeapon($weapon)
     {
         if ($weapon!="ID_TANK_ROUND" && 
             $weapon!="ID_TANK" && 
             $weapon!="ID_MISSILE_AIR_SOIL" && 
             $weapon!="ID_MISSILE_SOIL_SOIL" && 
             $weapon!="ID_MISSILE_BALISTIC_SHORT" && 
             $weapon!="ID_MISSILE_BALISTIC_MEDIUM" && 
             $weapon!="ID_MISSILE_BALISTIC_LONG" && 
             $weapon!="ID_MISSILE_BALISTIC_INTERCONTINENTAL" && 
             $weapon!="ID_NAVY_DESTROYER" && 
             $weapon!="ID_AIRCRAFT_CARRIER" && 
             $weapon!="ID_JET_FIGHTER")
        return false;
         else
        return true;
     }
     
     function isAttackWeapon($weapon)
     {
		if ($weapon=="ID_KNIFE" ||
            $weapon=="ID_PISTOL" || 
            $weapon=="ID_REVOLVER" || 
            $weapon=="ID_SHOTGUN" || 
            $weapon=="ID_MACHINE_GUN" || 
            $weapon=="ID_SNIPER")
        return true;
         else
        return false; 
     }
     
     function isDefenseWeapon($weapon)
     {
         if ($weapon=="ID_GLOVES" ||
             $weapon=="ID_GOGGLES" || 
             $weapon=="ID_HELMET" ||
             $weapon=="ID_BOOTS" ||
             $weapon=="ID_VEST" ||
             $weapon=="ID_SHIELD")
        return true;
         else
        return false;
     }
     
     function getWeaponDamage($weapon)
     {
        // Damage
        $damage=0;
         
        // Is weapon ?
        if (!$this->isAttackWeapon($weapon) && 
            !$this->isDefenseWeapon($weapon))
        throw new Exception ("Invalid weapon, db.java, line 2191");
        
        // Select weapon
        switch ($weapon)
        {
            // Knife
            case "ID_KNIFE" :  $damage=10; break;
            
            // Pistoc
            case "ID_PISTOL" :  $damage=20; break;
            
            // Revolver
            case "ID_REVOLVER" :  $damage=30; break;
            
            // Riffle
            case "ID_SHOTGUN" :  $damage=40; break;
            
            // Machine gun
            case "ID_MACHINE_GUN" :  $damage=50; break;
            
            // Grenade launcher
            case "ID_SNIPER" :  $damage=60; break;
            
            // Gloves
            case "ID_GLOVES" :  $damage=10; break;
            
            // Glasses
            case "ID_GOGGLES" :  $damage=20; break;
            
            // Helmet
            case "ID_HELMET" :  $damage=30; break;
            
            // Boots
            case "ID_BOOTS" :  $damage=40; break;
            
            // Vest
            case "ID_VEST" :  $damage=50; break;
            
            // Schield
            case "ID_SHIELD" :  $damage=60; break;
        }
        
        return $damage;
     }
     
     function isAmmo($ammo)
     {
        if ($ammo!="ID_TANK_ROUND" && 
            $ammo!="ID_MISSILE_AIR_SOIL" && 
            $ammo!="ID_MISSILE_SOIL_SOIL" && 
            $ammo!="ID_MISSILE_BALISTIC_SHORT" && 
            $ammo!="ID_MISSILE_BALISTIC_MEDIUM" && 
            $ammo!="ID_MISSILE_BALISTIC_LONG" && 
            $ammo!="ID_MISSILE_BALISTIC_INTERCONTINENTAL")
        return false;
          else
        return true;
     }
     
     function getAmmoDamage($ammo)
     {
         // Damage
         $damage=0;
         
         // Ammo
        if (!$this->isAmmo($ammo))
           throw new Exception ("Invalid ammo, CUtils.java, line 2191");
         
         switch ($ammo)
         {
             // Tank round
             case "ID_TANK_ROUND" : $damage=250; break;
             
             // Air to soil missile
             case "ID_MISSILE_AIR_SOIL" : $damage=1000; break;
             
             // Soil to soil missile
             case "ID_MISSILE_SOIL_SOIL" : $damage=1000; break;
             
             // Balistic short
             case "ID_MISSILE_BALISTIC_SHORT" : $damage=2500; break;
             
             // Balistic medium
             case "ID_MISSILE_BALISTIC_MEDIUM" : $damage=5000; break;
             
             // Balistic long
             case "ID_MISSILE_BALISTIC_LONG" : $damage=7500; break;
             
             // Balistic intercontinental
             case "ID_MISSILE_BALISTIC_INTERCONTINENTAL" : $damage=10000; break;
         }
         
         return $damage;
     }
     
     function getAmmoRange($ammo)
     {
         // Range
         $range=0;
         
         if ($ammo!="ID_TANK_ROUND" && 
             $ammo!="ID_MISSILE_AIR_SOIL" && 
             $ammo!="ID_MISSILE_SOIL_SOIL" && 
             $ammo!="ID_MISSILE_BALISTIC_SHORT" && 
             $ammo!="ID_MISSILE_BALISTIC_MEDIUM" && 
             $ammo!="ID_MISSILE_BALISTIC_LONG" && 
             $ammo!="ID_MISSILE_BALISTIC_INTERCONTINENTAL")
        throw new Exception ("Invalid ammo, db.php, line 2191");
         
         switch ($ammo)
         {
             // Tank round
             case "ID_TANK_ROUND" : $range=0; break;
             
             // Air to soil missile
             case "ID_MISSILE_AIR_SOIL" : $range=1000; break;
             
             // Soil to soil missile
             case "ID_MISSILE_SOIL_SOIL" : $range=1000; break;
             
             // Balistic short
             case "ID_MISSILE_BALISTIC_SHORT" : $range=2500; break;
             
             // Balistic medium
             case "ID_MISSILE_BALISTIC_MEDIUM" : $range=5000; break;
             
             // Balistic long
             case "ID_MISSILE_BALISTIC_LONG" : $range=7500; break;
             
             // Balistic intercontinental
             case "ID_MISSILE_BALISTIC_INTERCONTINENTAL" : $range=10000; break;
         }
         
         return $range;
     }
     
     function isSea($seaID) 
     {
         $query="SELECT * 
		           FROM seas 
				  WHERE seaID=?";
		 
		 $result=$this->execute($query, "i", $seaID);
         
         // Has data ?
         if (mysqli_num_rows($result)==0)
             return false;
         else
             return true;
     }
     
     function getCouPos($cou)
     {
         // Is country ?
         if (!$this->isCountry($cou))
             throw new Exception ("Invalid country, db.php, line 3536");
         
         // Load country data
         $query="SELECT * 
		           FROM countries 
				  WHERE code=?";
         
         $result=$this->execute($query, 
									  "s", 
									  $cou);
		 
		 $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
         
         // Return
         return new CPoint($row['x'], $row['y']);
     }
     
     function getSeaPos($seaID)
     {
         // Is country ?
         if (!$this->isSea($seaID))
             throw new Exception ("Invalid sea, db.php, line 1959");
         
         // Load country data
         $query="SELECT * 
		           FROM seas 
				  WHERE seaID=?";
         
		 // Result
         $result=$this->execute($query, 
									  "i", 
									  $seaID);
		 
		 // Row data
		 $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
         
         // Return
         return new CPoint($row['posX'], $row['posY']);
     }
     
     function getWeaponPos($stocID)
     {
         // Stoc valid
         if (!$this->isStoc($stocID))
            throw new Exception ("Invalid ID, CUtils.java, line 1959"); 
         
         // Result
         $result=$this->execute("SELECT * 
		                           FROM stocuri 
								  WHERE stocID=?", 
							    "i", 
							    $stocID);
		 
		 // Row data
		 $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
         
         // Return
         return $this->getLocPos($row["war_loc_type"], $row["war_locID"]);
     }
     
     function getLocPos($loc_type, $locID)
     {
		if ($loc_type!="ID_LAND" && 
            $loc_type!="ID_SEA" && 
            $loc_type!="ID_NAVY_DESTROYER" &&
            $loc_type!="ID_AIRCRAFT_CARRIER")
        throw new Exception ("Invalid location type, CUtils.java, line 1959");
        
        // Land ?
        if ($loc_type=="ID_LAND")
            return $this->getCouPos($locID);
        
        // Sea ?
        if ($loc_type=="ID_SEA")
            return $this->getSeaPos($locID);
        
        // Navy destroyer or carier ?
        if ($loc_type=="ID_NAVY_DESTROYER" || 
            $loc_type=="ID_AIRCRAFT_CARRIER")
        return $this->getWeaponPos($locID);
        
        return new CPoint(0, 0);
     }
     
     function getPointDist($p1, $p2)
     {
		 $abs_x=abs($p1->x-$p2->x); 
         $abs_y=abs($p1->y-$p2->y);
         $powX=round($abs_x*$abs_x);
         $powY=round($abs_y*$abs_y);
         
         return round(sqrt($powX+$powY)*8);
     }
     
     function isStoc($stocID)
     {
        // Query
        $query="SELECT * 
		          FROM stocuri 
				 WHERE stocID=?";
		 
		 // Result
         $result=$this->execute($query, 
									  "i", 
									  $stocID);
        
        // Has data ?
        if (mysqli_num_rows($result))
            return true;
        else
            return false;
     }
     
     function getAdrAttack($adr)
     {
         // Attack
         $attack=0;
         
         // Address valid
         if (!$this->isAdr($adr))
            throw new Exception ("Invalid address, CUtils.java, line 1959");
         
         // Load inventory
         $query="SELECT * 
		           FROM stocuri 
				  WHERE adr=? 
				    AND in_use>? 
					AND qty>0";
		 
		 // Result
         $result=$this->execute($query, 
									  "si", 
									  $adr,
									  0);
         
         while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            if ($this->isAttackWeapon($row["tip"]))
                $attack=$attack+$this->getWeaponDamage($row['tip']);
         
         // Return
         return $attack;
     }
     
     function getAdrDefense($adr)
     {
         // Defense
         $defense=0;
         
         // Address valid
         if (!$this->isAdr($adr))
            throw new Exception ("Invalid address, CUtils.java, line 1959");
         
         // Load inventory
         $query="SELECT * 
		           FROM stocuri 
				  WHERE adr=? 
				    AND in_use>? 
					AND qty>0";
		 
		 // Result
         $result=$this->execute($query, 
									  "si", 
									  $adr,
									  0);
         
         while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
            if ($this->isDefenseWeapon($row["tip"]))
                $defense=$defense+$this->getWeaponDamage($row['tip']);
         
         // Return
         return $defense;
     }
	 
	 function formatCou($cou)
	 {
		 return ucfirst(strtolower($cou));
	 }
	 
	 function checkAccPass($pass)
	 {
		 $result=$this->getResult("SELECT * 
		                             FROM web_users 
				                    WHERE user=? 
				                      AND pass=?", 
								  "ss", 
								  $_REQUEST['ud']['user'], 
								  hash("sha256", $pass));
		 
		 // Has data ?
		 if (mysqli_num_rows($result)>0)
			 return true;
		 else
			 return false;
	 }
	 
	 function isTax($tax)
	 {
		 if ($tax=="ID_DIVIDENDS_TAX" || 
			 $tax=="ID_RENT_TAX" || 
			 $tax=="ID_REWARDS_TAX" || 
			 $tax=="ID_SALARY_TAX" || 
			 $tax=="ID_SALE_TAX")
		 return true;
		 else
		 return false;
	 }
	 
	 function isPrivate($cou)
	 {
		 // Check code
		 if (!$this->isCountry($cou))
		    throw new Exception ("Invalid address, CUtils.java, line 1959");
		 
		 // Load country data
		 $row=$this->getRows("SELECT * 
		                        FROM countries 
							   WHERE code=?", 
							 "s", 
							 $cou);
		 
		 // Private ?
		 if ($row['private']=="YES")
			 return true;
		 else
			 return false;
	 }
	 
	 function isCouForSale($cou)
	 {
		 // Check code
		 if (!$this->isCountry($cou))
		    throw new Exception ("Invalid address, CUtils.java, line 1959");
		 
		 // Load country data
		 $row=$this->getRows("SELECT * 
		                        FROM countries 
							   WHERE code=?", 
							 "s", 
							 $cou);
		 
		 // Private ?
		 if ($row['owner']!="")
			 return true;
		 else
			 return false;
	 }
	 
	 function getPvtCouPrice()
	 {
		 // Number of sold private countries
		 $row=$this->getRows("SELECT COUNT(*) AS total 
		                        FROM countries 
							   WHERE private=? 
							     AND owner<>?", 
							 "ss", 
							 "YES", 
							 "default");
		 
		 // Price
		 $price=$row['total']*50;
		 
		 // Minimum
		 if ($price<50)
			 $price=50;
		 
		 // Return
		 return $price;
	 }
	 
	 function isCouOwner($adr, $cou)
	 {
		 // Check adr
		 if (!$this->isAdr($adr))
		    throw new Exception ("Invalid address, CUtils.java, line 1959");
		 
		 // Check country
		 if (!$this->isCountry($cou))
		    throw new Exception ("Invalid country, CUtils.java, line 1959");
		 
		 // Load country data
		 $result=$this->getResult("SELECT * 
		                             FROM countries 
									WHERE private=? 
									  AND owner=? 
									  AND code=?", 
								  "sss", 
								  "YES", 
								  $adr, 
								  $cou);
		 
		 // Has data ?
		 if (mysqli_num_rows($result)>0)
			 return true;
		 else
			 return false;
	 }
	 
	 function hasRecords($adr, 
                         $table, 
                         $col)
    {
        // Load data
        $result=$this->getResult("SELECT * FROM ".$table." WHERE ".$col."='".$adr."'");
        
        // Has data
        if (mysqli_num_rows($result)>0)
            return true;
        
        // No rows
        return false;
    }
    
    function traceAdr($adr)
    {
        // Address valid
         if (!$this->isAdr($adr))
            throw new Exception ("Invalid address, CUtils.java, line 1959");
        
        // Adr
        if ($this->hasRecords($adr, "adr", "adr") || 
            $this->hasRecords($adr, "adr", "ref_adr") || 
            $this->hasRecords($adr, "adr", "node_adr"))
        return true;
        
        // Adr attr
        if ($this->hasRecords($adr, "adr_attr", "adr"))
            return true;
        
        // Ads
        if ($this->hasRecords($adr, "ads", "adr"))
            return true;
        
        // Assets
        if ($this->hasRecords($adr, "assets", "adr"))
            return true;
        
        // Assets
        if ($this->hasRecords($adr, "assets", "adr"))
            return true;
        
        // Assets mkts
        if ($this->hasRecords($adr, "assets_mkts", "adr"))
            return true;
        
        // Assets mkts pos
        if ($this->hasRecords($adr, "assets_mkts_pos", "adr"))
            return true;
        
        // Assets owners
        if ($this->hasRecords($adr, "assets_owners", "owner"))
            return true;
        
        // Blocks
        if ($this->hasRecords($adr, "blocks", "signer"))
            return true;
        
        // Comments
        if ($this->hasRecords($adr, "comments", "adr"))
            return true;
        
        // Companies
        if ($this->hasRecords($adr, "companies", "adr") || 
            $this->hasRecords($adr, "companies", "owner"))
            return true;
        
        // Countries
        if ($this->hasRecords($adr, "countries", "adr") || 
            $this->hasRecords($adr, "countries", "owner"))
            return true;
        
        // Del votes
        if ($this->hasRecords($adr, "del_votes", "delegate") || 
            $this->hasRecords($adr, "del_votes", "adr"))
            return true;
        
        // Delegates
        if ($this->hasRecords($adr, "delegates", "delegate"))
            return true;
        
        // Delegates log
        if ($this->hasRecords($adr, "delegates_log", "delegate"))
            return true;
        
        // Endorsers
        if ($this->hasRecords($adr, "endorsers", "endorser") || 
            $this->hasRecords($adr, "endorsers", "endorsed"))
            return true;
        
        // Escrowed
        if ($this->hasRecords($adr, "escrowed", "sender_adr") || 
            $this->hasRecords($adr, "escrowed", "rec_adr") || 
            $this->hasRecords($adr, "escrowed", "escrower"))
            return true;
        
        // Events
        if ($this->hasRecords($adr, "events", "adr"))
            return true;
        
        // Exchange
        if ($this->hasRecords($adr, "exchange", "adr"))
            return true;
        
        // Items consumed
        if ($this->hasRecords($adr, "items_consumed", "adr"))
            return true;
        
        // Laws
        if ($this->hasRecords($adr, "laws", "adr"))
            return true;
        
        // Laws votes
        if ($this->hasRecords($adr, "laws_votes", "adr"))
            return true;
        
        // Mes
        if ($this->hasRecords($adr, "mes", "from_adr") || 
            $this->hasRecords($adr, "mes", "to_adr"))
            return true;
        
        // My adr
        if ($this->hasRecords($adr, "my_adr", "adr"))
            return true;
        
        // My trans
        if ($this->hasRecords($adr, "my_trans", "adr") || 
            $this->hasRecords($adr, "my_trans", "adr_assoc"))
            return true;
        
        // Orgs
        if ($this->hasRecords($adr, "orgs", "adr"))
            return true;
        
        // Orgs props
        if ($this->hasRecords($adr, "orgs_props", "adr"))
            return true;
        
        // Orgs props votes
        if ($this->hasRecords($adr, "orgs_props_votes", "adr"))
            return true;
        
        // Rent contracts
        if ($this->hasRecords($adr, "rent_contracts", "from_adr") || 
            $this->hasRecords($adr, "rent_contracts", "to_adr"))
            return true;
        
        // Rewards
        if ($this->hasRecords($adr, "rewards", "adr"))
            return true;
        
        // Stocuri
        if ($this->hasRecords($adr, "stocuri", "adr"))
            return true;
        
        // Trans
        if ($this->hasRecords($adr, "trans", "src"))
            return true;
        
        // Trans pool
        if ($this->hasRecords($adr, "trans_pool", "src"))
            return true;
        
        // Tweets
        if ($this->hasRecords($adr, "tweets", "adr"))
            return true;
        
        // Tweets follow
        if ($this->hasRecords($adr, "tweets_follow", "adr") || 
            $this->hasRecords($adr, "tweets_follow", "follows"))
            return true;
       
        // Votes
        if ($this->hasRecords($adr, "votes", "adr"))
            return true;
        
        // War fighters
        if ($this->hasRecords($adr, "wars_fighters", "adr"))
            return true;
        
        // Web ops
        if ($this->hasRecords($adr, "web_ops", "fee_adr") || 
            $this->hasRecords($adr, "web_ops", "target_adr"))
            return true;
        
        // Web sys data
        if ($this->hasRecords($adr, "web_sys_data", "node_adr") || 
            $this->hasRecords($adr, "web_sys_data", "mining_adr"))
            return true;
        
        // Web users
        if ($this->hasRecords($adr, "web_users", "adr"))
            return true;
        
        // Work procs
        if ($this->hasRecords($adr, "work_procs", "adr"))
            return true;
        
        // Return
        return false;
    }
}
?>
