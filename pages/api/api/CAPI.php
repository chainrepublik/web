<?php
class CAPI
{
	function CAPI($db)
	{
		$this->kern=$db;
	}
	
	function showErr($mes)
	{
	   print "'status' : {'result' : 'ID_ERR', 'err_mes' : '$mes'}";	
	}
	
	function getLastTrans($adr_list)
	{
		try
		{
		      // No spaces
		      $adr_list=str_replace(" ", "", $adr_list);
		
		      // Explode
		     $v=explode(",", $adr_list);
		
		      // Parse address list
		      for ($a=0; $a<=sizeof($v)-1; $a++)
		      {
			     // Get address
			     $adr=$this->kern->adrFromName($v[$a]);
			
			     // Valid address ?
			     if (!$this->kern->isAdr($adr))
			     {
			   	   $this->showErr("Invalid address - ".$adr);
			   	   return false;
			     }
		     }
			
		     // Results
		     $res="{\"status\" : {\"result\" : \"ID_OK\", \"err_mes\" : \"none\"}, \"trans\" : [";
			  
		     // Show trans
		     for ($a=0; $a<=sizeof($v)-1; $a++)
		     {
			     // Address
			     $adr=$this->kern->adrFromName($v[$a]);
			  
			     // Load trans
			     $result=$this->kern->getResult("SELECT trans.*, 
				                                        blocks.confirmations 
			                                       FROM trans 
											  LEFT JOIN blocks ON blocks.hash=trans.block_hash 
										          WHERE trans.src IN (?) 
											        AND trans.tstamp>".(time-1440)." 
										       ORDER BY trans.ID DESC", 
											   "s", 
											   $adr);
				 
				 // Confirmations ?
				 if ($row['confirmations']=="")
					 $row['confirmations']=0;
			  
			     // Parse
			     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  	     $res=$res."{\"src\" : \"".$row['src']."\", \"amount\" : \"".round($row['amount'],8)."\", \"cur\" : \"".$row['cur']."\", \"escrower\" : \"".$row['escrower']."\", \"hash\" : \"".$row['hash']."\", \"block\" : \"".$row['block']."\", \"block_hash\" : \"".$row['block_hash']."\", \"status\" : \"".$row['status']."\", \"confirmations\" : \"".$row['confirmations']."\"},";
			  
		     }
			
			 // End
			 $res=$res."]}";
			
			 // Ending
			 $res=str_replace(",]", "]", $res);
			
		     // Output
		     print $res;
		}
		catch (Exception $ex)
		{
			$this->showErr("Unexpected error");
		}
	}
}
?>