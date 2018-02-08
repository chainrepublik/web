<?
class CSignup
{
	function CSignup($db, $template, $acc)
	{
		$this->kern=$db;
		$this->template=$template;
		$this->acc=$acc;
	}
	
	
	function signup($user, $pass, $re_pass, $email)
	{
		// User
		if (strlen($user)<5 || strlen($user)>15)
		{
			$this->template->showErr("Invalid username length (5-15 characters)", 510);
			return false;
		}
		
		// User exist on network ?
		$query="SELECT * 
		          FROM adr 
				 WHERE name=?";
		
		// Load
		$result=$this->kern->execute($query, "s", $user);
		
		// Has data ?
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("User already exist", 510);
			return false;
		}
		
		// User exist ?
		$query="SELECT * 
		          FROM web_users 
				 WHERE user=?";
		
		// Load
		$result=$this->kern->execute($query, "s", $user);
		
		// User exist
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("User already exist", 510);
			return false;
		}
		
		// Password
		if (strlen($pass)<5 || strlen($pass)>25)
		{
			$this->template->showErr("Invalid password length", 510);
			return false;
		}
		
		// Passwwords match
		if ($pass!=$re_pass)
		{
			$this->template->showErr("Passwords don't match", 510);
			return false;
		}
		
		// Check email
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
			$this->template->showErr("Invalid email", 510);
			return false;
		}

		// Email used ?
		$query="SELECT * 
		          FROM web_users 
				 WHERE email=?";
				 
		// Load data
		$result=$this->kern->execute($query, "s", $email);
		
		// Email already used
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("Email is already used", 510);
			return false;
		}
		
		// Country
		$cou=$_SERVER["HTTP_CF_IPCOUNTRY"];
		 
		// Invalid country
		if ($this->kern->isCountry($cou)==false)
		   $cou="RO"; 
		   
		// Referer
		if (!isset($_SESSION['rID']))
		   $ref_adr=$_REQUEST['sd']['node_adr'];
		   
		if ($_SERVER['HTTP_CF_CONNECTING_IP']!="") 
	      $IP=$_SERVER['HTTP_CF_CONNECTING_IP'];
	   else
	      $IP=$_SERVER['REMOTE_ADDR'];
	     
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Creates account
		   $query="INSERT INTO web_users 
		                   SET user=?, 
						       pass=?, 
							   IP=?, 
							   cou=?, 
							   email=?,
							   block=?,
							   tstamp=?";
							   
		   $result=$this->kern->execute($query, 
		                                "sssssii", 
										$user, 
										hash("sha256", $pass), 
										$IP, 
										$cou, 
										$email, 
										$_REQUEST['sd']['last_block'],
										time());
		   
		   // UserID
		   $userID=mysqli_insert_id($this->kern->con); 
		   
		  
			// set session
			$_SESSION['userID']=$userID;
		    
			// Creates adress
		    $query="INSERT INTO web_ops 
			                SET fee_adr=?,
							    target_adr=?,
							    userID=?, 
							    op=?, 
								par_1=?, 
								par_2=?, 
								par_3=?,
								status=?,
								days=?, 
								tstamp=?"; 
								
			// Execute
	        $this->kern->execute($query, 
			                     "ssisssssii", 
								 $_REQUEST['sd']['node_adr'],
								 $_REQUEST['sd']['node_adr'],
								 $userID, 
								 "ID_NEW_ACCOUNT", 
								 $user, 
								 $cou, 
								 $ref_adr, 
								 "ID_PENDING", 
								 365,
								 time()); 
			
			// Commit
	 	    $this->kern->commit();
	        
			// Confirm
			$this->template->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error - ".$ex->getMessage(), 510);

		  return false;
	   }
	}
	
	function showForm()
	{
	   $query="SELECT * from web_users WHERE ID='".$_SESSION['refID']."'";  
	   $result=$this->kern->execute($query);	
	   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			  
	   
		?>
            
      
         <div class="panel panel-default" style="width:600px">
     <div class="panel-body">
       
		 <?
		     // Signup ?
		    if ($_REQUEST['act']=="signup")
		        $signup->signup($_REQUEST['txt_user'], 
		                        $_REQUEST['txt_pass'], 
						        $_REQUEST['txt_re_pass'], 
						        $_REQUEST['txt_email'], 
						        $_SESSION['refID']);  
		 ?>
		 
       <form action="main.php?act=signup" method="post" name="form_signup" id="form_signup">
       <table width="90%" border="0" cellspacing="0" cellpadding="0">
         <tbody>
           <tr>
             <td height="30" align="left" valign="top" class=""><strong>Username</strong></td>
           </tr>
           <tr>
             <td align="left"><input name="txt_user" id="txt_user" class="form-control"/></td>
           </tr>
           <tr>
             <td align="left">&nbsp;</td>
           </tr>
           <tr>
             <td align="left" class=""><strong>Email</strong></td>
           </tr>
           <tr>
             <td align="left"><input class="form-control" name="txt_email" id="txt_email" /></td>
           </tr>
           <tr>
             <td align="left">&nbsp;</td>
           </tr>
           <tr>
             <td align="left" class=""><strong>Password</strong></td>
           </tr>
           <tr>
             <td align="left"><input class="form-control" type="password" name="txt_pass" id="txt_pass"/></td>
           </tr>
           <tr>
             <td align="left">&nbsp;</td>
           </tr>
           <tr>
             <td align="left" class=""><strong>Retype Password</strong></td>
           </tr>
           <tr>
             <td align="left"><input class="form-control" type="password" name="txt_re_pass" id="txt_re_pass" /></td>
           </tr>
           <tr>
             <td align="left">&nbsp;</td>
           </tr>
           <tr>
             <td align="left"><input type="checkbox" name="terms" id="terms" />
               <label for="checkbox" class="font_12">&nbsp;I agree with the <a href="../../terms/terms/main.php">Terms of Service</a></label></td>
           </tr>
           <tr>
             <td align="left">&nbsp;</td>
           </tr>
          
           <tr>
             <td align="right">
             <a href="javascript:void(0)" onclick="$('#form_signup').submit()" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Signup</a>
             </td>
           </tr>
         </tbody>
       </table>
       </form>
       
     </div>
     </div>
      <div align="center" class="font_12" style="width:500px; color:#999999">You were refered to chainrepublik by <a href="#"><? print $row['user']; ?></a></div>
        
        <?
	}
	
	
   
 
}
?>
