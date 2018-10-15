<?php
class CRewards
{
	function CRewards($db, $acc, $template)
	{
		$this->kern=$db;
		$this->template=$template;
		$this->acc=$acc;
		
		if ($_REQUEST['ud']['user']!="root") 
			die ("Invalid credentials");
	}
    
	function showRewardPanel()
	{
		// Title
		$title="Nodes reward";
		
		// Img
		$img="";
		
		// Img width
		$img_width="";
		
		// P1 val
		$query="SELECT SUM(energy) AS total 
		          FROM adr 
				 WHERE node_adr<>''";
		$result=$this->kern->execute($query);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Total
		$total=$row['total'];
		if ($total=="") $total=0;
		$p1_val=round($total);
		
		// P2 title
		$p2_title="User's Energy";
		
		/// P1 val
		$query="SELECT SUM(energy) AS total 
		          FROM adr 
				 WHERE node_adr<>?";
		$result=$this->kern->execute($query, "s", $_REQUEST['sd']['node_adr']);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$p2_val=$row['total']; 
		if ($p2_val=="") $p2_val=0;
		
		// Reward pool
		$pool=$this->kern->getRewardPool("ID_NODES");
		
		// Per point
		$p=round($p2_val*100/$p1_val, 4);
		
		// Reward
		$p3_val=round($p*$pool/100, 2);
		
		
		// P2 sub title
		$p2_sub_title="CRC / energy point";
		
		// Expl
		$expl="Node operators are rewarded by the network every 24 hours. Rewards are calculated based on the total energy of the users of a node. Node operators reward pool is 10% of total daily reward pool, or <strong>".$this->kern->getRewardPool("ID_NODES")." CRC / day </strong>. Rewards are not automatically paid by the network. You need to <strong>claim</strong> your reward every 24 hours. Below are listed. Below are listed the last rewards received by this node.";
		
		// Panel
		$this->template->showRewardPanel($title, 
							             "./GIF/food.png", 80, 
							             "Total Energy", $p1_val, "total users energy", 
							             "Node USers Energy", $p2_val, "total energy",
							             $p3_val, 
							             $expl);
	}
}