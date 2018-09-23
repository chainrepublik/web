<?
class CBudget
{
	function CBudget($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showTrans($cou)
	{
		// Address
		$adr=$this->kern->getCouAdr($cou);
		
		// Cou name
		$country=$this->kern->countryFromCode($cou);
		
		// Query
		$query="SELECT *
		          FROM trans 
			 LEFT JOIN blocks ON blocks.hash=trans.block_hash
		    	 WHERE trans.src=?
				ORDER BY trans.ID DESC 
			     LIMIT 0,20"; 
		
		$result=$this->kern->execute($query, "s", $adr);
		
		?>
            
            <br>
            <div id="div_trans" name="div_trans">
            <table width="90%" border="0" cellspacing="0" cellpadding="0" class="table-responsive">
              <tbody>
                <?
					   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					   {
					?>
                     
                          <tr>
                          <td width="55%" align="left">
							  <a href="../../explorer/packets/packet.php?hash=<? print $row['hash']; ?>" class="font_14"><strong><? print $country." State Budget"; ?></strong></a><p class="font_10" style="color: #999999"><? print $this->kern->getAbsTime($row['tstamp'])."ago, ".base64_decode($row['expl']); if ($row['escrower']!="") print "&nbsp;&nbsp;<span class='label label-warning'>escrowed</span>"; ?></p></td>
                          <td width="5%" align="center" class="font_14" style="color:#999999">
                          <?
						      if ($row['mes']!="") 
							  print "<span id='gly_msg_".rand(100, 10000)."' data-placement='top' class='glyphicon glyphicon-envelope' data-toggle='popover' data-trigger='hover' title='Message' data-content='".base64_decode($row['mes'])."'></span>&nbsp;&nbsp;";
							
						  ?>
                          </td>
                          <td width="15%" align="center" class="font_16">
                          <?
						      $confirms=$row['confirmations'];
							  
							  if ($confirms=="")
					             $confirms=0;
								 
						      if ($confirms==0)
					             print "<span class='label label-danger' data-toggle='tooltip' data-placement='top' title='Confirmations'>".$confirms."</span>";
							  
						      else if ($confirms<=10)
					             print "<span class='label label-info' data-toggle='tooltip' data-placement='top' title='Confirmations'>".$confirms."</span>";
						      
						      else if ($confirms>10 && $confirms<25)
					             print "<span class='label label-warning' data-toggle='tooltip' data-placement='top' title='Confirmations'>".$confirms."</span>";
						      
						      else
							     print "<span class='label label-success' data-toggle='tooltip' data-placement='top' title='Confirmed'>Confirmed</span>";
								 
						 ?>
                         
                          </td>
                          <td width="25%" align="center" class="font_14" style=" 
						  <? 
						      if ($row['amount']<0) 
							     print "color:#990000"; 
							  else 
							     print "color:#009900"; 
						  ?>"><strong>
						  <? 
						     print round($row['amount'], 8)." "; 
							 
							 // CRC
							 if ($row['cur']=="CRC") 
							   print "CRC"; 
							 
							 // Symbol
							 else if (strpos($row['cur'], "_")==-1) 
							   print strtoupper($row['symbol']);
							   
							 // Product
							 else  
							   print "<br><span class='font_10'>".$row['name']."</span>";
						  ?>
                          </strong>
                          <p class="font_12">
						  <? 
						      if ($row['cur']=="CRC")
							  {
								  if ($row['amount']<0)
								    print "-$".abs(round($row['amount']*$_REQUEST['sd']['coin_price'], 4));
								  else
								     print "+$".round($row['amount']*$_REQUEST['sd']['coin_price'], 4);
							  }
							  else print base64_decode($row['title']);
					      ?>
                          </p>
                          </td>
                          </tr>
                          <tr>
                          <td colspan="4"><hr></td>
                          </tr>
                    
                    <?
					   }
					?>
                    
                    </tbody>
                  </table>
                  <br><br><br>
                  </div>
                  
            
            <script>
			$("span[id^='gly_']").popover();
			</script>
        <?
	}
	
	function showPanel($cou)
	{
		// Budget address
		$adr=$this->kern->getCouAdr($cou); 
		
		// Query
		$query="SELECT SUM(amount) AS total
		          FROM trans
				 WHERE src=? 
				   AND amount>? 
				   AND block>?";
	    
		// Result
		$result=$this->kern->execute($query, 
									 "sii", 
									 $adr, 
									 0, 
									 $_REQUEST['sd']['last_block']-1440);	
		
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$income24=$row['total'];
		
		// Query
		$query="SELECT SUM(amount) AS total
		          FROM trans
				 WHERE src=? 
				   AND amount<? 
				   AND block>?";
	    
		// Result
		$result=$this->kern->execute($query, 
									 "sii", 
									 $adr, 
									 0, 
									 $_REQUEST['sd']['last_block']-1440);	
		
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$spent24=$row['total'];
		
		// Query
		$query="SELECT COUNT(*) AS total
		          FROM trans
				 WHERE src=? 
				   AND block>?";
	    
		// Result
		$result=$this->kern->execute($query, 
									 "si", 
									 $adr, 
									 $_REQUEST['sd']['last_block']-1440);	
		
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$trans=$row['total'];
		
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="465" align="center" valign="top" background="GIF/panel.png">
                
                <table width="560" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="100" align="center" style="font-size:40px; color:#242b32; font-family:'Times New Roman', Times, serif; text-shadow: 1px 1px 0px #777777;">State Budget</td>
                  </tr>
                  <tr>
                    <td height="220" align="center">
                    
                    <table width="400" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td align="center" style="font-size:80px; color:#242b32; font-family:'Times New Roman', Times, serif; ">
							<? print "".round($this->acc->getTransPoolBalance($adr, "CRC"), 2); ?><span class="font_16">&nbsp;&nbsp;CRC</span>
                        </td>
                      </tr>
                     
                    </table>
                    
                    </td>
                  </tr>
                  <tr>
                    <td height="130" align="center" valign="top">
                    <table width="550" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="17">&nbsp;</td>
                        <td width="102" height="50" align="center" valign="bottom" style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;">Income 24H</td>
                        <td width="40" align="center" valign="bottom">&nbsp;</td>
                        <td width="99" align="center" valign="bottom"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;">Spend 24H</span></td>
                        <td width="40" align="center" valign="bottom">&nbsp;</td>
                        <td width="97" align="center" valign="bottom"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;">Net Result 24H</span></td>
                        <td width="39" align="center" valign="bottom">&nbsp;</td>
                        <td width="100" align="center" valign="bottom"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;">Transactions</span></td>
                        <td width="16">&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td height="60" align="center" valign="bottom" class="bold_shadow_green_32"><? print "+".round($income24, 2); ?></td>
                        <td align="center" valign="bottom">&nbsp;</td>
                        <td align="center" valign="bottom" class="bold_shadow_red_32">
						<? print "".round(abs($spent24)); ?></td>
                        <td align="center" valign="bottom">&nbsp;</td>
                        <td align="center" valign="bottom"><span class="<? if ($income24+$spent24<0) print "bold_shadow_red_32"; else print "bold_shadow_green_32"; ?>">
						
						<? 
						
						    if (($income24+$spent24)<0) 
							   print "-".round(abs($income24+$spent24), 2); 
							else 
							   print "+".round(abs($income24+$spent24), 2); 
						?>
                        
                        </span></td>
                        <td align="center" valign="bottom">&nbsp;</td>
                        <td align="center" valign="bottom" class="bold_shadow_green_32">
						<? 
						    print $trans;
						?>
                        </td>
                        <td>&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td align="center" valign="bottom" class="bold_shadow_white_10">&nbsp;</td>
                        <td align="center" valign="bottom">&nbsp;</td>
                        <td align="center" valign="bottom" class="bold_shadow_white_10">&nbsp;</td>
                        <td align="center" valign="bottom">&nbsp;</td>
                        <td align="center" valign="bottom" class="bold_shadow_white_10">&nbsp;</td>
                        <td align="center" valign="bottom">&nbsp;</td>
                        <td align="center" valign="bottom" class="bold_shadow_white_10">&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr>
            </table>
        
        <?
	}
	
	function showBonuses($cou)
	{
		$query="SELECT *
		          FROM bonuses AS bon 
				  JOIN tipuri_produse AS tp on tp.prod=bon.prod
				 WHERE cou=? 
			  ORDER BY amount DESC";
		
		$result=$this->kern->execute($query, "s", $cou);	
		
		// Top bar
	    $this->template->showtopBar("Bonus", "80%", "Amount", "20%"); 
	  
		?>
        
          <table width="540" border="0" cellspacing="0" cellpadding="0" align="center">
          <tbody>
			
			<?
	 	        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			    {
		    ?>
			  
                   <tr>
                   <td width="80%" class="font_14"><? print $row['name']." Aquisition Bonus"; ?></td>
					   <td width="20%" class="font_14" style="color: #009900" align="center"><strong><? print $row['amount']." CRC"; ?></strong></td>
                   </tr>
                   <tr>
                   <td colspan="2"><hr></td>
                   </tr>
			  
			<?
				}
		    ?>
			  
          </tbody>
          </table>
          
        
        <?
	}
	
	function showTaxes($cou)
	{
		// Citizens ?
		$query="SELECT *
		          FROM taxes 
			 LEFT JOIN tipuri_produse AS tp ON tp.prod=taxes.prod
				 WHERE taxes.cou=?
			  ORDER BY value DESC";
		
		$result=$this->kern->execute($query, "s", $cou);	
		
		// Top bar
	    $this->template->showtopBar("Tax", "80%", "Amount", "20%"); 
	  
		?>
        
          <table width="540" border="0" cellspacing="0" cellpadding="0" align="center">
          <tbody>
			
			<?
	 	        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			    {
		    ?>
			  
                   <tr>
					   <td width="80%" class="font_14"><? print $this->getTaxName($row['tax'], $row['name']); ?><br><span class="font_10" style='color:#999999'><? print $this->getTaxDesc($row['tax']); ?></span></td>
					   <td width="20%" class="font_14" style="color: #009900" align="center"><strong><? print $row['value']."%"; ?></strong></td>
                   </tr>
                   <tr>
                   <td colspan="2"><hr></td>
                   </tr>
			  
			<?
				}
		    ?>
			  
          </tbody>
          </table>
          
        
        <?
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