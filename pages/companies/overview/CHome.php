<?
class CHome
{
	function CHome($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function renewCompany($comID, $months)
	{
		// Company ID
		$query="SELECT * 
		          FROM companies 
				 WHERE ID=?
				   AND ownerID=?";
		
		// Execute
		$result=$this->kern->execute($query, 
		                             "ii", 
									 $comID, 
									 $_REQUEST['ud']['ID']);	
		
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data");
			return false;
		}
		
		// Company data
		$row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Months
		if ($months!=3 && 
		    $months!=6 && 
			$months!=9 && 
			$months!=12 && 
			$months!=24)
	    {
			$this->template->showErr("Invalid entry data");
			return false;
		}
		
		// Load company type data
		$query="SELECT * 
		          FROM tipuri_companii 
				 WHERE tip=?";
		
		// Result
		$result=$this->kern->execute($query, 
		                             "s", 
									 $row['tip']);
	    
		// Row					 	
		$row_com_type=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Price
		$price=$this->getGoldPrice($row_com_type['price_'.$months.'m']);
		
		// Funds
		if ($this->acc->getBalance("ID_COM", $comID)<$price)
		{
		   	$this->template->showErr("Insufficient funds to execute this operation");
			return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Open a company
		   $this->kern->newAct("Renews a company licence");
		   
		  
			  
		   // Commit
		   $this->kern->commit();
		   
		   return true;
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showerr("Unexpected error (".$ex->getMessage().")");

		  return false;
	   }
	}
	
	function showOverviewPanel()
	{
		// Load company data
		$query="SELECT * 
		          FROM companies 
				 WHERE comID=?";
				 
		// Result
		$result=$this->kern->execute($query, 
		                             "i", 
									 $_REQUEST['ID']);
	    
		// Row					 	
		$com_row=mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Expire
		$expire=round(($com_row['expires']-$_REQUEST['sd']['last_block'])/1440);
		
		// CRC balance
		$balance=$this->acc->getNetBalance($com_row['adr'], "CRC");
		
		// Workplaces
		$query="SELECT COUNT(*) AS total 
		          FROM workplaces 
				 WHERE comID=?";
		
		// Result
		$result=$this->kern->execute($query, 
		                             "i", 
									 $_REQUEST['ID']);
									 
		// Result
		$workplaces=mysqli_num_rows($result);
		
		// Share price
		$price=0;
		?>
            
            <br>
            <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="460" align="center" valign="top" background="GIF/overview.png"><table width="560" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="100" align="center" style="font-size:40px; color:#242b32; font-family:'Times New Roman', Times, serif; text-shadow: 1px 1px 0px #777777;">Company Overview</td>
              </tr>
              <tr>
                <td height="220" align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="33%"><img src="<? if ($this->com['com_pic']=="") print "./GIF/blank_photo.png"; else print "../../../uploads/".$this->com['com_pic']; ?>" width="150" height="150" <? if ($this->com['com_pic']!="") print "class=\"img-circle\""; ?> /></td>
                    <td width="67%" align="left" valign="top">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td height="30"><span class="inset_blue_inchis_18"><? print base64_decode($com_row['name'])." (".$com_row['symbol'].")"; ?></span></td>
                        </tr>
                        <tr>
                          <td><span class="simple_gri_14"><? print base64_decode($com_row['description']); ?></span></td>
                        </tr>
                        <tr>
                          <td height="20" valign="bottom"><span class="font_10">Address : <? print $this->template->formatAdr($com_row['adr'], 10); ?></span></td>
                        </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td height="130" align="center" valign="top"><table width="550" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="17">&nbsp;</td>
                    <td width="102" height="50" align="center" valign="bottom" style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;">Expire</td>
                    <td width="40" align="center" valign="bottom">&nbsp;</td>
                    <td width="99" align="center" valign="bottom"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;">Balance</span></td>
                    <td width="40" align="center" valign="bottom">&nbsp;</td>
                    <td width="97" align="center" valign="bottom"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;">Workplaces</span></td>
                    <td width="39" align="center" valign="bottom">&nbsp;</td>
                    <td width="100" align="center" valign="bottom"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;">Share Price</span></td>
                    <td width="16">&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td height="50" align="center" valign="bottom" class="bold_shadow_white_32">
					
					<? 
					   print $expire;
	  		        ?>
                    
                    </td>
                    <td align="center" valign="bottom">&nbsp;</td>
                    <td align="center" valign="bottom">
                    <span class="bold_shadow_white_32"><? $v=explode(".", round($balance, 4)); print "".$v[0]; ?></span><span class="bold_shadow_white_18"><? if (sizeof($v)==2) print ".".$v[1]; else print ".0000"; ?></span>
                    </td>
                    <td align="center" valign="bottom">&nbsp;</td>
                    <td align="center" valign="bottom"><span class="bold_shadow_white_32"><? print $workplaces; ?></span></td>
                    <td align="center" valign="bottom">&nbsp;</td>
                    <td align="center" valign="bottom">
                    <span class="bold_shadow_white_32"><? $v=explode(".", $price); print "".$v[0]; ?></span><span class="bold_shadow_white_18"><? if (sizeof($v)>1) print ".".$v[1]; else print ".00"; ?></span>
                    </td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td align="center" valign="bottom" class="bold_shadow_white_10">days</td>
                    <td align="center" valign="bottom">&nbsp;</td>
                    <td align="center" valign="bottom" class="bold_shadow_white_10">CRC</td>
                    <td align="center" valign="bottom">&nbsp;</td>
                    <td align="center" valign="bottom" class="bold_shadow_white_10">workplaces</td>
                    <td align="center" valign="bottom">&nbsp;</td>
                    <td align="center" valign="bottom" class="bold_shadow_white_10">per share</td>
                    <td>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table>
        <br /><br />
        
        <?
	}
	
	function showAllowedBuyers($posID, $rec, $recID, $qty)
	{
		// Load poition data
		$query="SELECT * 
		          FROM a_mkts_orders AS vmo 
				  JOIN tipuri_produse AS tp ON tp.prod=vmo.symbol 
				 WHERE vmo.ID='".$posID."'"; 
		 $result=$this->kern->execute($query);	
	     $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 
		 if ($rec!="") 
		 {
			if ($_REQUEST['rec']=="ID_CIT")
			{
			   // Food ?
			   if ($row['symbol']=="ID_FOOD_Q1" || 
			       $row['symbol']=="ID_FOOD_Q2" || 
				   $row['symbol']=="ID_FOOD_Q3")
			     $this->food->eat($posID);
			   else
			     $this->user_market->buy($posID, $qty);
			}
			else
			{
			  $this->com_market->trade("ID_COM", $recID, $posID, $qty);
			}
			
		 }
		
		 
		 // Check possible buyers
		 $query="SELECT * 
		           FROM allow_trans 
				  WHERE receiver_type='ID_CIT' 
				    AND prod='".$row['symbol']."'";
		 $result=$this->kern->execute($query);	
		 if (mysqli_num_rows($result)>0)
		    $user_can_buy=true;
	     else 
		    $user_can_buy=false;
			
	     // Companies 
		 $query="SELECT com.tip 
		           FROM companies AS com 
				   JOIN allow_trans AS at ON at.receiver_type=com.tip 
				  WHERE at.can_buy='Y' 
				    AND com.ownerID='".$_REQUEST['ud']['ID']."' 
					AND at.prod='".$row['symbol']."'";
	     $result=$this->kern->execute($query);	
	     
		 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		    $tipuri=$tipuri.", '".$row['tip']."'";
		 
		 $tipuri=substr($tipuri, 1, 10000);
		?>
        
         <br><br>
         <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td width="18%" align="left" valign="top"><img src="GIF/lanes.png" width="99" height="115" alt=""/></td>
              <td width="82%" align="center" valign="top" class="simple_gri_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td>You can buy this product for multiple companies or for yourself. You need to specify who is the beneficiary of this product. You can sepcify only one recipient.</td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                </tbody>
              </table>
              </td>
            </tr>
            <tr>
              <td colspan="2" align="left" valign="top" background="../../template/GIF/lc.png">&nbsp;</td>
            </tr>
            <tr>
              <td colspan="2" align="left" valign="top">&nbsp;</td>
            </tr>
            
  </tbody>
        </table>
        
        <?
			     if ($user_can_buy==true)
				 {
			  ?>
              
                <table width="90%" border="0" cellspacing="0" cellpadding="0">
                  <tbody>
                    <tr>
                      <td width="24%" align="left" class="font_14">Myself</td>
                      <td width="59%">&nbsp;</td>
                      <td width="17%"><a href="main.php?ID=<? print $_REQUEST['ID']; ?>&act=buy&posID=<? print $posID; ?>&rec=ID_CIT&txt_qty_<? print $posID; ?>=<? print $qty; ?>" class="btn btn-primary" style="width:80px" >Choose</a></td>
                    </tr>
                  </tbody>
</table>
              
              <?
				 }
				 
				 $this->showMyCompanies($tipuri, $posID, $qty);
			  ?>
              
              
        
        <?
	}
	
	function showMyCompanies($tipuri, $posID, $qty)
	{
		if ($tipuri=="") return false;
		
		$query="SELECT com.*, ba.balance, tc.tip_name, tc.pic, com.pic AS com_pic
		          FROM companies AS com
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip
				  JOIN bank_acc AS ba ON ba.ownerID=com.ID
				 WHERE com.owner_type='ID_CIT' 
				   AND ba.owner_type='ID_COM'
				   ANd ba.fundID='0'
				   AND ba.moneda='GOLD'
				   AND com.ownerID='".$_REQUEST['ud']['ID']."'
				   AND com.tip IN (".$tipuri.")"; 
	    $result=$this->kern->execute($query);	
	    
		?>
        <table width="560" border="0" cellspacing="0" cellpadding="5">
           
          <?
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>

               <tr>
                 <td width="11%" class="font_14">
                 <img src="
				 <? 
				     if ($row['com_pic']=="") 
					    print "../overview/GIF/prods/big/".$row['pic'].".png";
					 else
					    print "../../../uploads/".$row['com_pic']; 
				 ?>" 
                 width="50" height="50" class="img-circle" /></td>
                 <td width="56%" class="font_14"><a href="#" class="maro_16"><? print $row['name']; ?> </a><br />
                 <span class="font_10"><? print $row['tip_name']; ?></span></td>
                 <td width="16%" align="left" class="bold_green_14"><? print "".round($row['balance'], 4); ?></td>
                 <td width="17%" align="center" class="bold_verde_14">
                 <a href="main.php?ID=<? print $_REQUEST['ID']; ?>&act=buy&posID=<? print $posID; ?>&rec=<? print $row['tip']; ?>&recID=<? print $row['ID']; ?>&txt_qty_<? print $posID; ?>=<? print $qty; ?>" class="btn btn-primary" style="width:80px" >Choose</a></td>
          </tr>
                 <tr>
                 <td colspan="4" ><hr></td>
                 </tr>
      
           <?
			  }
		   ?>
           
</table>
        
        <?
	}
	
	
	
	function showRenewPanel($comID)
	{
		// Load company data
		$query="SELECT * 
		          FROM companies 
				 WHERE comID=?";
		
		// Result
		$result=$this->kern->execute($query, 
		                             "i", 
									 $comID);	
									 
		// Load data
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Expires in the next 10 days ?
		if ($row['expires']-$_REQUEST['sd']['last_block']>14400) 
		   return false;
		
		// Days
		$days=floor(($row['expires']-$_REQUEST['sd']['last_block'])/1440);
		
		// No negatives
		if ($days<0) $days=0;
	  
		$this->showRenewCompanyModal($_REQUEST['ID']);
		
		?>
        
             <table width="550" border="0" cellspacing="0" cellpadding="0">
             <tbody>
             <tr>
              <td width="433" height="80" align="center" bgcolor="#ffe8ed"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td class="bold_red_12">Your company expires in <? print $days; ?> days. You need to renew the company licence or your company will be deleted. If a company is deleted, all workplaces, shares or other items will be also </td>
                  </tr>
                </tbody>
              </table></td>
              <td width="117" align="center" bgcolor="#ffe8ed"><a href="javascript:void(0)" onclick="$('#renew_com_modal').modal()" class="btn btn-danger">Renew</a></td>
            </tr>
            </tbody>
            </table>
            <br>
        
        <?
	}
	
	function getGoldPrice($usd)
    {
	   return round($usd/$_REQUEST['sd']['gold_price'], 2);
    }
   
	function showRenewCompanyModal($comID)
	{
		// Load company data
		$query="SELECT tc.* 
		          FROM companies AS com
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip
				 WHERE com.ID='".$comID."'"; 
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	  
		
		$this->template->showModalHeader("renew_com_modal", "Renew Company Modal", "act", "renew", "", "");
		?>
        
              <table width="600" border="0" cellspacing="0" cellpadding="5">
              <tr>
              <td width="223"><img src="GIF/new_workplace.jpg" width="200" height="204" /></td>
              <td width="357" align="left" valign="top"><table width="85%" border="0" cellspacing="2" cellpadding="0">
              <tr>
                <td width="8%" align="center" bgcolor="#FFFFFF"><input name="period" type="radio" id="period" value="3" checked="checked" /></td>
                <td width="60%" height="40" align="left" bgcolor="#FFFFFF" class="inset_maro_inchis_16_bold">&nbsp;&nbsp;3 months</td>
                <td width="32%" align="center" bgcolor="#FFFFFF" class="font_14"><? print $this->getGoldPrice(30); ?> gold</td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="6" /></td>
                <td height="40" align="left" bgcolor="#FFFFFF"><span class="inset_maro_inchis_16_bold">&nbsp;&nbsp;6 months </span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14"><? print $this->getGoldPrice(50); ?> gold</span></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="9" /></td>
                <td height="40" align="left" bgcolor="#FFFFFF"><span class="inset_maro_inchis_16_bold">&nbsp;&nbsp;9 months</span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14"><? print $this->getGoldPrice(70); ?> gold</span></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="12" /></td>
                <td height="40" align="left" bgcolor="#FFFFFF"><span class="inset_maro_inchis_16_bold">&nbsp;&nbsp;12 months</span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14"><? print $this->getGoldPrice(90); ?> gold</span></td>
              </tr>
              <tr>
                <td align="center" bgcolor="#FFFFFF"><input type="radio" name="period" id="period" value="24" /></td>
                <td height="40" bgcolor="#FFFFFF"><span class="inset_maro_inchis_16_bold">&nbsp;&nbsp;24 months</span></td>
                <td align="center" bgcolor="#FFFFFF"><span class="font_14"><? print $this->getGoldPrice(200); ?> gold</span></td>
              </tr>
            </table></td>
          </tr>
          </table>
        
         
        <?
			$this->template->showModalFooter("Cancel", "Rent");
	}
}
?>