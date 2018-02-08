<?
class CUserData
{
	function CUserData($db, $redirect=true)
	{
		$this->kern=$db;
		
		//if ($_SERVER['HTTP_CF_CONNECTING_IP']!="89.38.168.20") die ("Maintainance in progress...");
		
		if ($_SESSION['userID']>0 || $_REQUEST['key']!="")
		{
			if ($_SESSION['userID']>0)
			{
			   $query="SELECT * 
			             FROM web_users 
					    WHERE ID=?"; 
					 
			   $result=$this->kern->execute($query, "i", $_SESSION['userID']);	
	           $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			}
			
			
			 $_REQUEST['ud']['ID']=$row['ID'];
			 $_REQUEST['ud']['user']=$row['user']; 
			 $_REQUEST['ud']['pass']=$row['pass'];
			 $_REQUEST['ud']['email']=$row['email'];
			 $_REQUEST['ud']['IP']=$row['IP'];
			 $_REQUEST['ud']['status']=$row['status'];
			 $_REQUEST['ud']['api_key']=$row['api_key'];
			 $_REQUEST['ud']['ref_adr']=$row['ref_adr'];
			 $_REQUEST['ud']['tstamp']=$row['tstamp'];
			 $_REQUEST['ud']['unread_esc']=$row['unread_esc'];
			 $_REQUEST['ud']['unread_events']=$row['unread_events'];
			 $_REQUEST['ud']['unread_mes']=$row['unread_mes'];
			 $_REQUEST['ud']['unread_trans']=$row['unread_trans'];
			 
			if ($row['adr']=="")
			{
			     // Address info
				 $query="SELECT * 
			               FROM my_adr AS ma 
					      WHERE userID=?
					   ORDER BY ID ASC 
					      LIMIT 0,1";
						
			    // Load data
			    $result=$this->kern->execute($query, 
			                             "i",
										 $_REQUEST['ud']['ID']);	
										 
			    // Data
	            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			   
			    // Address
			    $_REQUEST['ud']['adr']=$row['adr']; 
				
				// Update
				$query="UPDATE web_users 
				           SET adr=? 
						 WHERE ID=?"; 
				
				// Execute
				$this->kern->execute($query, 
			                         "si",
									 $_REQUEST['ud']['adr'],
							    	 $_REQUEST['ud']['ID']);	
			}
			else $_REQUEST['ud']['adr']=$row['adr']; 
			
			
			 // Address info
			 $query="SELECT * 
			               FROM adr 
					      WHERE adr=?";
						
			// Load data
			$result=$this->kern->execute($query, 
			                             "s",
										 $_REQUEST['ud']['adr']);	
			
			// Unregistered
			if (mysqli_num_rows($result)>0)
			{							 
			    // Data
	            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				
				// Registered
				if ($row['cou']=="")
					if ($redirect)
						$this->kern->redirect("../../misc/unregistered/main.php");
			
			    // Load address details
			    $_REQUEST['ud']['cou']=$row['cou']; 
			    $_REQUEST['ud']['loc']=$row['loc'];  
			    $_REQUEST['ud']['pic']=$row['pic'];  
			    $_REQUEST['ud']['description']=$row['description'];  
			    $_REQUEST['ud']['block']=$row['block'];  
			    $_REQUEST['ud']['pol_inf']=$row['pol_inf'];  
			    $_REQUEST['ud']['energy']=$row['energy']+$this->getTransPoolBalance("ID_ENERGY"); 
			    $_REQUEST['ud']['attack']=$row['attack'];  
			    $_REQUEST['ud']['defense']=$row['defense'];  
			    $_REQUEST['ud']['master_adr']=$row['master_adr'];  
			    $_REQUEST['ud']['pol_endorsed']=$row['pol_endorsed'];  
				$_REQUEST['ud']['war_points']=$row['war_points'];  
			    $_REQUEST['ud']['created']=$row['created'];  
			    $_REQUEST['ud']['expires']=$row['expires'];  
			    $_REQUEST['ud']['energy_block']=$row['energy_block'];  
				$_REQUEST['ud']['travel']=$row['travel'];  
				$_REQUEST['ud']['travel_cou']=$row['travel_cou'];  
				$_REQUEST['ud']['work']=$row['work'];  
			
			    // Calculate balance
			    $_REQUEST['ud']['balance']=$row['balance']; 
			
			    // Balance
			    $_REQUEST['ud']['balance']=$_REQUEST['ud']['balance']+$this->getTransPoolBalance("CRC");
			
			    // Referers
			    $query="SELECT * 
			              FROM adr 
					     WHERE ref_adr=?";
					 
			    // Load data
			    $result=$this->kern->execute($query, 
			                             "s",
										 $_REQUEST['ud']['adr']);
										 
			    // Num rows
			    $_REQUEST['ud']['aff']=mysqli_num_rows($result);	
			 }
			 else 
			 {
				 // Balance
				 $_REQUEST['ud']['balance']=0;
				 
				 // Redirect
				 if ($redirect && 
					 $_REQUEST['ud']['user']!="root") 
					 $this->kern->redirect("../../misc/unregistered/main.php");
				 
				 // Defaults
				 $_REQUEST['ud']['aff']=0;
				 $_REQUEST['ud']['pol_inf']=0;
				 $_REQUEST['ud']['pol_endorsed']=0;
				 $_REQUEST['ud']['war_points']=0;
				 $_REQUEST['ud']['cou']="RO";
				 $_REQUEST['ud']['loc']="RO";
			 }
			
			 // Online
			 if (time()-$row['online']>60)
			 {
				 $query="UPDATE web_users 
				            SET online=? 
						   WHERE ID=?";
				 
				 $this->kern->execute($query, 
									  "ii", 
									  time(), 
									  $_REQUEST['ud']['ID']);			   
			 }
			 
			 if ($_REQUEST['ud']['banned']>0)
			 {
			   $_SESSION['userID']=0;
			   die ("This account was permanently suspended.");
			 }
			 
			 // Machine ID
			 if (!isset($_SESSION['mID'])) 
			     $_SESSION['mID']=rand(1000000, 9999999);
			 
	    }
	}
	
	function getTransPoolBalance($cur)
	{
		// Load trans pool trans
		$query="SELECT SUM(amount) AS total
			      FROM trans_pool 
	    	     WHERE src=? 
    		       AND cur=?";
			
	   // Load data
	   $result=$this->kern->execute($query, 
			                                 "ss",
										     $_REQUEST['ud']['adr'],
										     $cur);
			
	   // Data
	   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return
		return $row['total'];
	}
}
?>