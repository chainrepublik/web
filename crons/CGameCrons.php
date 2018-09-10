<?
class CGameCrons
{
	function CGameCrons($db)
	{
		$this->kern=$db;
	}
    
	function getTargetVotes($target_type, $targetID, $vote="ID_UP", $type="ID_NO")
	{
		// Query
		if ($type=="ID_NO")
			$query="SELECT COUNT(*) AS total 
			          FROM votes 
					 WHERE target_type=?
					   AND targetID=? 
					   AND type=?";
		else
			$query="SELECT SUM(power) AS total 
			          FROM votes 
					 WHERE target_type=? 
					   AND targetID=? 
					   AND type=?";
		
	   // Result
	   $result=$this->kern->execute($query, 
									"sis", 
									$target_type,
								    $targetID,
								    $vote);
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Result
		$res=round($row['total'], 2);
		
		// Return
		if ($res=="")
			return 0;
		else
			return $res;
	}
	
	function getTotalVotes($target_type="ID_TWEET")
	{
		$query="SELECT SUM(power) AS total
		          FROM votes 
				 WHERE target_type=?";
		
		// Result
	   $result=$this->kern->execute($query, 
									"s", 
									$target_type);
		
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Result
		$res=round($row['total'], 2);
		
		// Return
		if ($res=="")
			return 0;
		else
			return $res;
	}
	
	function updateVoteStats($target_type)
	{
	   // Load data
	   $query="SELECT DISTINCT(targetID)
	             FROM votes 
				WHERE target_type=?";	
	   
	   // Result
	   $result=$this->kern->execute($query, 
									"s", 
									$target_type);	
		
		// Articles pool
		if ($target_type=="ID_TWEET")
		   $pool=$this->kern->getRewardPool("ID_PRESS");
		else
		   $pool=$this->kern->getRewardPool("ID_COM");
		
		// Articles total votes
		$total=$this->getTotalVotes($target_type);
	   
	    // Parse rows
	    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	    {
		   // Target ID
		   $targetID=$row['targetID'];
			
		   // Coments
		   $comments=0;
			
		   // Comments ?
		   if ($target_type=="ID_TWEET")
		   {
			   // Query
			   $query="SELECT COUNT(*) AS total 
			             FROM comments 
						WHERE parent_type=? 
						  AND parentID=?";
			   
			   // Result
	           $res_com=$this->kern->execute($query, 
									         "si", 
								        	 $target_type,
										     $targetID);	
			   
			   // Coments
			   $row = mysqli_fetch_array($res_com, MYSQLI_ASSOC);
			   
			   // Comments
			   $comments=$row['total'];
		   }
			
		   // Upvotes number
		   $upvotes_no=$this->getTargetVotes($target_type, 
											 $targetID, 
											 "ID_UP", 
											 "ID_NO");
			
		   // Upvotes power
		   $upvotes_power=$this->getTargetVotes($target_type, 
												$targetID, 
												"ID_UP", 
												"ID_POWER");
			
		   // Downvotes number
		   $downvotes_no=$this->getTargetVotes($target_type, 
											   $targetID, 
											   "ID_DOWN", 
											   "ID_NO");
			
		   // Downvotes power
		   $downvotes_power=$this->getTargetVotes($target_type, 
												  $targetID, 
												  "ID_DOWN", 
												  "ID_POWER");
			
		   // Net power
		   $power=$upvotes_power-$downvotes_power;

		   // Percent of total
		   $p=$power*100/$total;
			   
		   // Payment
		   $pay=round($p*$pool/100, 4);
			
		   // Min pay 
		   if ($pay<0) $pay=0;
			
		   // Update
		   $query="INSERT INTO votes_stats 
		                   SET target_type=?, 
					           targetID=?, 
				               upvotes_24=?, 
						       upvotes_power_24=?, 
						       downvotes_24=?, 
						       downvotes_power_24=?, 
						       pay=?, 
							   comments=?,
						       tstamp=?";
			
			// Execute
			$this->kern->execute($query, 
								 "siididdii", 
						         $target_type,
						    	 $targetID,
				    		     $upvotes_no,
							     $upvotes_power,
							     $downvotes_no,
							     $downvotes_power,
							     $pay,
								 $comments,
								 time());
		   
	    }
	}
	
	
	function updateVotes()
	{
		// Delete votes stats
	   $query="DELETE FROM votes_stats";
	   
		// Execute
	   $result=$this->kern->execute($query);
		
		// Articles
		$this->updateVoteStats("ID_TWEET");
			
		// Comments
		$this->updateVoteStats("ID_COM");
	}
	
	function updateSysStats()
	{
		// Last block
		$query="SELECT * FROM net_stat";
		$result=$this->kern->execute($query);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$last_block=$row['last_block']; 
		
		$query="SELECT * FROM sys_stats";
	    $result=$this->kern->execute($query);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			// Init
			$total_users=0;
			$total_com=0;
			$total_24H=0;
			$total_work=0;
		    $total_energy=0;
			$avg_energy=0;
			$total_pol_inf=0;
			$avg_pol_inf=0;
			$total_war_points=0;
			$avg_war_points=0;
			
		    // Total addressess
			$query="SELECT COUNT(*) AS total 
			          FROM adr 
					 WHERE cou=? 
					   AND LENGTH(name)>5";
			$cou_res=$this->kern->execute($query, "s", $row['cou']);
			$cou_row = mysqli_fetch_array($cou_res, MYSQLI_ASSOC);
			$total_users=$cou_row['total'];
			
			// Companies
			$query="SELECT COUNT(*) AS total 
			          FROM companies AS com 
					  JOIN adr ON adr.adr=com.adr 
					 WHERE adr.cou=?";
			
			$cou_res=$this->kern->execute($query, "s", $row['cou']);
			$cou_row = mysqli_fetch_array($cou_res, MYSQLI_ASSOC);
			$total_com=$cou_row['total'];
			
			// Workplaces
			$query="SELECT COUNT(*) AS total 
			          FROM workplaces AS work
					  JOIN companies AS com ON com.comID=work.comID
					  JOIN adr ON adr.adr=com.adr 
					 WHERE adr.cou=?";
			
			$cou_res=$this->kern->execute($query, "s", $row['cou']);
			$cou_row = mysqli_fetch_array($cou_res, MYSQLI_ASSOC);
			$total_work=$cou_row['total'];
			
			// Any users ?
			if ($total_users>0)
			{
			   // New users 24H
			   $query="SELECT COUNT(*) AS total 
			          FROM adr 
					 WHERE cou=? 
					   AND LENGTH(name)>5 
					   AND created>".($last_block-1440); 
			   $cou_res=$this->kern->execute($query, "s", $row['cou']);
			   $cou_row = mysqli_fetch_array($cou_res, MYSQLI_ASSOC);
			   $total_24H=$cou_row['total'];
			
			   // Total energy
			   $query="SELECT SUM(energy) AS total 
			             FROM adr 
				  	    WHERE cou=? 
					      AND LENGTH(name)>5";
			   $cou_res=$this->kern->execute($query, "s", $row['cou']);
			   $cou_row = mysqli_fetch_array($cou_res, MYSQLI_ASSOC);
			   $total_energy=round($cou_row['total']);
			   $avg_energy=round($total_energy/$total_users, 2);
			
			   // Total pol inf
			   $query="SELECT SUM(pol_inf) AS total 
			             FROM adr 
					    WHERE cou=? 
					      AND LENGTH(name)>5";
			   $cou_res=$this->kern->execute($query, "s", $row['cou']);
			   $cou_row = mysqli_fetch_array($cou_res, MYSQLI_ASSOC);
			   $total_pol_inf=round($cou_row['total']);
			   $avg_pol_inf=round($total_pol_inf/$total_users, 2);
				
			   // Total pol end
			   $query="SELECT SUM(pol_endorsed) AS total 
			             FROM adr 
					    WHERE cou=? 
					      AND LENGTH(name)>5";
			   $cou_res=$this->kern->execute($query, "s", $row['cou']);
			   $cou_row = mysqli_fetch_array($cou_res, MYSQLI_ASSOC);
			   $total_pol_end=round($cou_row['total']);
			   $avg_pol_end=round($total_pol_inf/$total_users, 2);
			
			   // Total war points
			   $query="SELECT SUM(war_points) AS total 
			             FROM adr 
					    WHERE cou=? 
					      AND LENGTH(name)>5";
			   $cou_res=$this->kern->execute($query, "s", $row['cou']);
			   $cou_row = mysqli_fetch_array($cou_res, MYSQLI_ASSOC);
			   $total_war_points=round($cou_row['total']);
			   $avg_war_points=round($total_war_points/$total_users, 2);
			}
			
			 $query="UPDATE sys_stats 
			            SET users=?, 
						    signups_24h=?, 
							companies=?, 
							workplaces=?, 
							total_energy=?, 
							avg_energy=?, 
							total_war_points=?, 
							avg_war_points=?, 
							total_pol_inf=?, 
							avg_pol_inf=?,
							total_pol_end=?, 
							avg_pol_end=?
					  WHERE cou=?";
			
			$this->kern->execute($query, 
								 "iiiiidididids", 
								 $total_users, 
								 $total_24H, 
								 $total_com, 
								 $total_work, 
								 $total_energy, 
								 $avg_energy, 
								 $total_war_points, 
								 $avg_war_points, 
								 $total_pol_inf, 
								 $avg_pol_inf, 
								 $total_pol_end,
								 $avg_pol_end,
								 $row['cou']);
		}
		
	}
	
	
	function checkSystem()
	{
		// Load last block
		$query="SELECT * FROM net_stat";
		$result=$this->kern->execute($query);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$last_block=$row['last_block'];
		
		// Load block data
		$query="SELECT * FROM blocks WHERE block=?";
		$result=$this->kern->execute($query, "i", $last_block);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$tstamp=$row['tstamp'];
	
		
		// More than 5 min ?
		if (time()-$tstamp>600 && 
			time()-$tstamp<720)
		{
            $query="INSERT INTO err_log 
			           SET type=?, 
					       mes=?, 
						   tstamp=?";
			
			$result=$this->kern->execute($query, 
										 "ssi", 
										 "SMS",
										 "SMS sent",
										 time());
			
			$this->kern->sendSMS("40754386386", $_SERVER['HTTP_HOST']." is down");
		}
	}
	
	// 1 minute
	function run_cron_1M($cronID)
	{
		$ID=$this->cronStart($cronID);
		
		try
		{
		  // Stats
		  $this->updateSysStats();
			
		  // Votes stats
	      $this->updateVotes();	
			
		  // Check system
		  $this->checkSystem();
			
		  // Mesaj
		  $this->cronOK($ID);
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();
		  
		  print "Error";

		  // Mesaj
		  $this->cronError($ID, $ex->getMessage());

		  return false;
	   }	
		
		// Incheie cron-ul
		$this->cronEnd($ID);
	}
	
	// 5 minutes
	function run_cron_5M($cronID)
	{
		$ID=$this->cronStart($cronID);
		
		try
		{
		   
		  
		  // Mesaj
		  $this->cronOK($ID);
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->cronError($ID, $ex->getMessage());

		  return false;
	   }	
		
		// Incheie cron-ul
		$this->cronEnd($ID);
	}
	
	// 10 minutes
	function run_cron_10M($cronID)
	{
		$ID=$this->cronStart($cronID);
		
		try
		{
		  

		  // Mesaj
		  $this->cronOK($ID);
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->cronError($ID, $ex->getMessage());

		  return false;
	   }	
		
		// Incheie cron-ul
		$this->cronEnd($ID);
	}
	
	// 1 hour
	function run_cron_1H($cronID)
	{
		$ID=$this->cronStart($cronID);
		
		try
		{
		 

		   // Mesaj
		   $this->cronOK($ID);
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->cronError($ID, $ex->getMessage());

		  return false;
	   }	
		
		// Incheie cron-ul
		$this->cronEnd($ID);
	}
	
	// 1 day
	function run_cron_1D($cronID)
	{
		$ID=$this->cronStart($cronID);
		
		try
		{
		 
		  
		  // Commit
		  $this->kern->commit();

		  // Mesaj
		  $this->cronOK($ID);
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->cronError($ID, $ex->getMessage());

		  return false;
	   }	
		
		// Incheie cron-ul
		$this->cronEnd($ID);
	}
	
	// Cron-ul porneste
	function cronStart($cronID)
	{
		$query="INSERT INTO cron_runs 
		                SET cronID=?, 
						    start=?, 
							status=?";
		
		$this->kern->execute($query, 
							 "iis", 
							 $cronID, 
							 time(), 
							 "ID_RUNNING");
		
		$ID=mysqli_insert_id($this->kern->con);
		
		$query="UPDATE crons 
		           SET last_run=?
				 WHERE ID=?";
		
		$this->kern->execute($query, 
							 "ii", 
							 time(), 
							 $cronID);			 
		
		$query="UPDATE crons 
		           SET next_run=last_run+inter
				 WHERE ID=?";
		$this->kern->execute($query, "i", $cronID);
		
		// ID
		return $ID;
	}
	
	// Cron-ul se incheie
	function cronEnd($ID)
	{
		$query="UPDATE cron_runs 
		           SET end=?,
				       duration=".time()."-start
				 WHERE ID=?";
		
		$this->kern->execute($query, 
							 "ii",
							 time(),
							 $ID);
	}
	
	// Eroare
	function cronError($ID, $err)
	{
		$query="UPDATE cron_runs 
		           SET status=?
				 WHERE ID=?";
		
		$this->kern->execute($query, 
							 "si", 
							 "ERROR - ".$err, 
							 $ID);
	}
	
	// Ok
	function cronOK($ID)
	{
		$query="UPDATE cron_runs 
		           SET status=?
				 WHERE ID=?";
		
		$this->kern->execute($query, 
							 "si", 
							 "OK", 
							 $ID);
	}
	
	// Ruleaza cron-urile
	function run()
	{
		$query="SELECT * 
		          FROM crons 
				  WHERE next_run < ?"; 
		
		$result=$this->kern->execute($query, 
									 "i", 
									 time());
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			switch ($row['cron'])
			{
				
				// General 1 minut
				case "ID_CRON_1M" : $this->run_cron_1M($row['ID']); break;
				
				// General 5 minut
				case "ID_CRON_5M" : $this->run_cron_5M($row['ID']); break;
				
				// General 10 minut
				case "ID_CRON_10M" : $this->run_cron_10M($row['ID']); break;
				
				// General 1 hour
				case "ID_CRON_1H" : $this->run_cron_1H($row['ID']); break;
				
				// General 1 day
				case "ID_CRON_1D" : $this->run_cron_1D($row['ID']); break;
			}
		}
	}
}
?>