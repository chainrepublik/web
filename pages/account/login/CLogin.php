<?php
class CLogin
{
	function CLogin($db, $template, $acc)
	{
		$this->kern=$db;
		$this->template=$template;
		$this->acc=$acc;
	}
	
	function showForm()
	{
		?>
            
            <div class="panel panel-default" style="width:600px">
            <div class="panel-body">
     
            <form action="main.php?act=login" method="post" name="form_login" id="form_login">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr><td>
            <?php
		       if ($_REQUEST['act']=="login")
		       $this->doLogin($_REQUEST['txt_user'], 
		               $_REQUEST['txt_pass']);
		    ?>
           </td></tr>
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
             <td align="left" class=""><strong>Password</strong></td>
           </tr>
           <tr>
             <td align="left"><input name="txt_pass" id="txt_pass" class="form-control" type="password"/></td>
           </tr>
           <tr>
             <td align="left">&nbsp;</td>
           </tr>
           <tr>
             <td align="right">
             <a href="javascript:void(0)" onclick="$('#form_login').submit()" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Login</a>
             </td>
           </tr>
         </tbody>
       </table>
       </form>
       
     </div>
     </div>
        
        <?php
	}
	
	
	function doLogin($user, $pass)
	{
		
	   // Check pass
	   if (strlen($pass)<4 || strlen($pass)>30)
	   {
		   $this->template->showErr("Invalid password (5-12 characters)", 390);
		   return false;
	   }
	 
	   // Load user data 
	   $query="SELECT * 
	               FROM web_users 
			      WHERE (user=? OR email=?) 
			        AND pass=?"; 
					
	   $result=$this->kern->execute($query, 
		                              "sss", 
									  $user, 
									  $user, 
									  hash("sha256", $pass));	
	     
	  if (mysqli_num_rows($result)==0)
	  {
		     $this->template->showErr("Invalid username or password", 500);
		     return false;
	  }
		
	  // Row
	  $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	 
	  // Account creation block
      if ($row['block']==$_REQUEST['sd']['last_block'] 
		  && $user!="root")
	  {
		  $this->template->showErr("Your account has not been yet registered with the network. Wait for 5 minutes and try again.", 500);
		  return false;
	  }
	  
      // Session
	  $_SESSION['userID']=$row['ID'];
	  $userID=$row['ID'];
	  
	  // Logs activity
	  $query="INSERT INTO actions
                       SET userID=?,
			               act='Logs in account',
						   country=?,
                           tstamp=?,
                           IP=?,
				           URL=?";
	   
	   $this->kern->execute($query, 
	                        "isiss", 
							$row['ID'], 
							$_SERVER["HTTP_CF_IPCOUNTRY"], 
							time(),
							$IP,
							$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	   
	   // Online
	   $query="UPDATE web_users 
	              SET online=? 
			    WHERE ID=?";
				
	   $this->kern->execute($query, 
	                        "ii", 
							time(), 
							$row['ID']);
	   
	   // Redirect
	   if ($user=="root")
		   die ("<script>window.location='../../../pages/admin/users/main.php'</script>");
	   else
	       die ("<script>window.location='../../../pages/home/press/main.php'</script>");
		   
	   return false;
      }
	
}
?>
