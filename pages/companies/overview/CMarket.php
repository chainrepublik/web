<?php
class CMarket
{
	function CMarket($db, $acc, $template, $comID)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function getFirstProd()
	{
		// Query
		$query="SELECT * 
		          FROM companies 
				 WHERE comID=?";
		
		// Result
		$result=$this->kern->execute($query, 
		                             "i", 
									 $_REQUEST['ID']);
		
		// Load rows							 	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Type
		$tip=$row['tip'];
		
		// Query
		$query="SELECT * 
		          FROM com_prods AS cp
				  JOIN tipuri_produse AS tp ON tp.prod=cp.prod
				 WHERE com_type=?";
				 
		// Load
		$result=$this->kern->execute($query, 
		                             "s", 
									 $tip);
		
		// Data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return
		return $row['prod'];
	}
	
	function getMktID($prod)
	{
		// Query
		$query="SELECT * 
		          FROM assets_mkts 
				 WHERE asset=? 
				   AND cur=?";
				   
		// Result
		$result=$this->kern->execute($query, 
		                             "ss", 
									 $prod,
									 "CRC");	
									 
		// Data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Return
		return $row['mktID'];
	}
	
	function showSelector()
	{
		// First mktID
		if (!isset($_REQUEST['mktID']))
		   $_REQUEST['mktID']=$this->getMktID($this->getFirstProd());
		   
		
		// Query
		$query="SELECT * 
		          FROM companies 
				 WHERE comID=?";
		
		// Result
		$result=$this->kern->execute($query, 
		                             "i", 
									 $_REQUEST['ID']);	
	    
		// Row
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Tip
		$tip=$row['tip'];
		
		// Query
		$query="SELECT * 
		          FROM com_prods AS cp
				  JOIN tipuri_produse AS tp ON tp.prod=cp.prod
				 WHERE com_type=?";
				 
		// Result
		$result=$this->kern->execute($query, 
		                             "s", 
									 $tip);	
		
		?>
          
            
        <input type="hidden" id="prod" name="txt_prod" value=""/>
        <table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td align="left">
        
        <div class="dropdown">
        <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
         Choose Market&nbsp;&nbsp;
         <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
        
        <?php
		   $i=0;
		
		   // Loop
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
			  // Index
			  $i++;
			   
			  // First product
			  if ($i==1) 
				  $prod=$row['prod'];
			   
		      print "<li role='presentation'><a role='menuitem' tabindex='-1' href='".$_SERVER['PHP_SELF']."?ID=".$_REQUEST['ID']."&mktID=".$this->getMktID($row['prod'])."'>".$row['name']."</a></li>";
		   }
		?>
        
       
        </ul>
        </div>
        
        </td>
          </tr>
        </table><br>
        
        <?php
		
		return $prod;
	}
}
?>