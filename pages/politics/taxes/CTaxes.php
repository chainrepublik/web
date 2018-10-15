<?php
class CTaxes
{
	function CTaxes($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showTopPanel($cou, $target)
	{
		// Load country data
		$query="SELECT * 
				  FROM countries 
				 WHERE code=?";
				
		$result=$this->kern->execute($query, "s", $cou);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		?>

            <table width="550" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td width="11%"><img src="../../template/GIF/flags/56/<?php print $cou; ?>_56.gif" width="45"></td>
					<td width="30%" class="font_22" align="left" valign="top"><strong><?php print ucfirst(strtolower($row['country'])); ?></strong><br><span class="font_12" style="color: #999999"><?php if ($row['occupied']!=$row['code']) print "Under Ocupation"; else print "Free Country"; ?></span></td>
				    <td align="right">
					<?php 
		                 if ($target=="ID_CIT")
							 $sel=1;
		                 else
							 $sel=2;
		
		                 $this->template->showSmallMenu($sel, 
													    "Citizens", "main.php?cou=".$cou."&target=ID_CIT", 
													    "Companies", "main.php?cou=".$cou."&target=ID_COM"); 
					?>
					</td>
                </tr>
                <tr>
                  <td colspan="3"><hr></td>
                </tr>
              </tbody>
            </table>

        <?php
	}
	
	function showTaxes($cou, $type="ID_CIT")
	{
		// Citizens ?
		if ($type=="ID_CIT")
		$query="SELECT *
		          FROM taxes 
				 WHERE tax IN ('ID_SALARY_TAX', 
				               'ID_RENT_TAX', 
							   'ID_REWARDS_TAX', 
							   'ID_DIVIDENDS_TAX') 
				  AND cou=? 
			  ORDER BY value DESC";
		else
		$query="SELECT *
		          FROM taxes 
				  JOIN tipuri_produse AS tp ON tp.prod=taxes.prod
				 WHERE taxes.cou=?
			  ORDER BY value DESC";
		
		$result=$this->kern->execute($query, "s", $cou);	
		
		// Top bar
	    $this->template->showtopBar("Tax", "80%", "Amount", "20%"); 
	  
		?>
        
          <table width="540" border="0" cellspacing="0" cellpadding="0" align="center">
          <tbody>
			
			<?php
	 	        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			    {
		    ?>
			  
                   <tr>
					   <td width="80%" class="font_14"><?php print $this->getTaxName($row['tax'], $row['name']); ?><br><span class="font_10" style='color:#999999'><?php print $this->getTaxDesc($row['tax']); ?></span></td>
					   <td width="20%" class="font_14" style="color: #009900" align="center"><strong><?php print $row['value']."%"; ?></strong></td>
                   </tr>
                   <tr>
                   <td colspan="2">&nbsp;</td>
                   </tr>
			  
			<?php
				}
		    ?>
			  
          </tbody>
          </table>
          
        
        <?php
	}
	
	function getTaxName($tax, $prod_name)
	{
		if (strpos($tax, "SALE_TAX")>0)
		{
		  return $prod_name." Sale Tax";
		}
		else
		{
		  switch ($tax)
		  {
			  // Salary tax
			  case "ID_SALARY_TAX" : return "Salary Tax"; 
				                     break;
			  
			  // Rent tax
			  case "ID_RENT_TAX" : return "Rent Tax";
				                   break;
			  
			  // Rewardss tax
			  case "ID_REWARDS_TAX" : return "Rewards Tax"; 
				                      break;
			  
			  // Dividends tax
			  case "ID_DIVIDENDS_TAX" : return "Dividends Tax"; 
				                        break;
		  }
		}
	}
	
	function getTaxDesc($tax)
	{
		if (strpos($tax, "SALE_TAX")>0)
		{
		  return "Paid by companies when they sale products";
		}
		else
		{
		  switch ($tax)
		  {
			  // Salary tax
			  case "ID_SALARY_TAX" : return "Paid by all citizens when they receive their salary"; 
				                     break;
			  
			  // Rent tax
			  case "ID_RENT_TAX" : return "Paid by all citizens when renting items";
				                   break;
			  
			  // Rewardss tax
			  case "ID_REWARDS_TAX" : return "Paid by all citizens when they receive a network reward"; 
				                      break;
			  
			  // Dividends tax
			  case "ID_DIVIDENDS_TAX" : return "Paid by all citizens when they receive dividends"; 
				                        break;
		  }
		}
	}
}
?>