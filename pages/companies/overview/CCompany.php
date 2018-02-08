<link rel="stylesheet" href="../../../style.css" type="text/css">
<?
class CCompany
{
	function CCompany($db, $acc, $template, $comID)
	{
		$this->kern=$db;
        $this->acc=$acc;
        $this->template=$template;
		$this->ID=$comID;
		
		// Valid ID
		if ($this->kern->isInt($comID)==false) 
		  die ("Invalid entry data");
		
		// Company exist
		$query="SELECT * 
		          FROM companies AS com 
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip
				  join web_users AS us ON us.ID=com.ownerID
				 WHERE com.ID='".$this->ID."'";
		$result=$this->kern->execute($query);	
		
		// If no exit
		if (mysqli_num_rows($result)==0) die("Inavlid entry data");
	    
		// Load data
		$this->com = mysqli_fetch_array($result, MYSQLI_ASSOC);
	}
	
	
	function doRentLic()
	{
		// Enough rights
		if ($this->com['ownerID']!=$_REQUEST['ud']['ID'])
		{
			$this->template->showErr("You don't have the rights to execute this operation.");
		    return false;
		}
		
		// Licence ID
		if ($this->kern->isInt($_REQUEST['licID'])==false || $_REQUEST['licID']<0) 
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		// Period
		if ($_REQUEST['period']!=3 && 
		    $_REQUEST['period']!=6 && 
			$_REQUEST['period']!=9 && 
			$_REQUEST['period']!=12 && 
			$_REQUEST['period']!=24)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Leverage
		if ($this->kern->isInt($_REQUEST['leverage'])==false || $_REQUEST['leverage']<0) 
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		// Load licence data
		$query="SELECT * 
		          FROM tipuri_licente 
				 WHERE ID='".$_REQUEST['licID']."'";
	    $result=$this->kern->execute($query);	
	    if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		$lic_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Check leverage
		$lev_price=0;
		if ($_REQUEST['leverage']>0)
		{
			if ($_REQUEST['leverage']!=$lic_row['lev_1'] && 
			    $_REQUEST['leverage']!=$lic_row['lev_2'] && 
				$_REQUEST['leverage']!=$lic_row['lev_3'] && 
				$_REQUEST['leverage']!=$lic_row['lev_4'] &&
				$_REQUEST['leverage']!=$lic_row['lev_5'])
				{
					$this->template->showErr("Invalid entry data");
		            return false;
				}
				else
				{
					switch ($_REQUEST['leverage'])
					{
						case $lic_row['lev_1'] : $lev_price=$lic_row['lev_1']; break;
						case $lic_row['lev_2'] : $lev_price=$lic_row['lev_2']; break;
						case $lic_row['lev_3'] : $lev_price=$lic_row['lev_3']; break;
						case $lic_row['lev_4'] : $lev_price=$lic_row['lev_4']; break;
						case $lic_row['lev_5'] : $lev_price=$lic_row['lev_5']; break;
					}
				}
		}
		
		// Price
		$price=$lic_row['price_'.$_REQUEST['period']."m"];
		if ($lev_price>0) $price=$price+$price*($lev_price/100);
		
		// Funds
		if ($this->acc->getFreeBalance("ID_CIT", $_REQUEST['ud']['ID'], "GOLD")<$price)
		{
			$this->template->showErr("Insufficient funds to perform this operation.");
		    return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Licence already exist 
		   if ($lic_row['tip']=="ID_LIC_TRADE_STOCK")
		   {
			   $query="SELECT * 
			             FROM stocuri 
						WHERE owner_type='ID_COM' 
						  AND ownerID='".$this->ID."' 
						  AND symbol='".$this->com['symbol']."'";
			   $result=$this->kern->execute($query);
			   $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				
			   // Already have the licence ?	
			   if (mysqli_num_rows($result)>0)
			   {
				   $query="UPDATE stocuri 
				              SET expire=expire+".($_REQUEST['period']*2600000).", 
							      leverage='".$_REQUEST['leverage']."' 
							WHERE ID='".$row['ID']."'";
				   $this->kern->execute($query);
			   }
			   else
			   {
				   $query="INSERT INTO stocuri 
				                   SET owner_type='ID_COM', 
								       ownerID='".$this->ID."', 
									   tip='".$lic_row['tip']."',  
									   qty='1', 
									   expire='".(time()+$_REQUEST['period']*2600000)."', 
									   categ='ID_LICENCE', 
									   symbol='".$lic_row['prod']."', 
									   leverage='".$_REQUEST['leverage']."', 
									   tstamp='".time()."', 
									   tID='".$tID."'";
				   $this->kern->execute($query);
			   }
		   }
		   
		   // Transfer money
		   $this->acc->finTransfer("ID_CIT", 
	                               $_REQUEST['ud']['ID'],
					               "ID_GAME", 
	                               0, 
					               $price, 
					               "GOLD", 
					               "You have rented / renewed a licence (".$lic_row['lic_name'].") for your company ".$this->com['name']." (".$this->com['symbol'].")", 
					               $_REQUEST['ud']['user']." rented / renewed a licence (".$lic_row['lic_name'].") for his / her company ".$this->com['name']." (".$this->com['symbol'].")",
					               $tID);
		   
		   // Commit
		   $this->kern->commit();
		   
		   // Ok
		   $this->template->showOK("Your request has beed successfully executed.");

		   return true;
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->kern->showerr("Unexpected error.");

		  return false;
	   }
		
	}
	

    function showRentLic()
	{
		// Check lic ID
		if ($this->kern->isInt($_REQUEST['licID'])==false || $_REQUEST['licID']<0) die ("Invalid entry data");
		
		// Load licence data
		$query="SELECT * 
		          FROM tipuri_licente 
				 WHERE ID='".$_REQUEST['licID']."'";
	    $result=$this->kern->execute($query);	
	    if (mysqli_num_rows($result)==0) die ("Invalid entry data");
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		?>
           <br />
           
           <form method="post" action="rent_licence.php?act=rent&ID=<? print $_REQUEST['ID']?>&licID=<? print $_REQUEST['licID']; ?>" id="form_lic" name="form_lic">
           <table width="550" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="50" align="center" background="../../template/GIF/panel_top_red.png" class="bold_shadow_white_16"><? print $row['lic_name']; ?></td>
  </tr>
  <tr>
    <td height="200" align="center" valign="top" background="../../template/GIF/panel_middle.png"><table width="90%" border="0" cellspacing="2" cellpadding="0">
      <tr>
        <td height="35" colspan="3" align="left" class="bold_red_18">For how long you want to rent the licence ?</td>
      </tr>
      <tr>
        <td width="7%" align="center" bgcolor="#fff9ef"><input name="period" type="radio" id="period" value="3" checked="checked" /></td>
        <td width="78%" align="left" bgcolor="#fff9ef" class="inset_maro_inchis_16_bold">&nbsp;&nbsp;3 months</td>
        <td width="15%" align="center" bgcolor="#fff9ef" class="font_16"><? print "".$row['price_3m']; ?></td>
      </tr>
      <tr>
        <td align="center" bgcolor="#fff9ef"><input type="radio" name="period" id="period" value="6" /></td>
        <td align="left" bgcolor="#fff9ef"><span class="inset_maro_inchis_16_bold">&nbsp;&nbsp;6 months </span><span class="bold_verde_12">(save <? print "".$row['price_6m_save']; ?>)</span></td>
        <td align="center" bgcolor="#fff9ef"><span class="font_16"><? print "".$row['price_6m']; ?></span></td>
      </tr>
      <tr>
        <td align="center" bgcolor="#fff9ef"><input type="radio" name="period" id="period" value="9" /></td>
        <td align="left" bgcolor="#fff9ef"><span class="inset_maro_inchis_16_bold">&nbsp;&nbsp;9 months <span class="bold_verde_12">(save <? print "".$row['price_9m_save']; ?>)</span></span></td>
        <td align="center" bgcolor="#fff9ef"><span class="font_16"><? print "".$row['price_9m']; ?></span></td>
      </tr>
      <tr>
        <td align="center" bgcolor="#fff9ef"><input type="radio" name="period" id="period" value="12" /></td>
        <td align="left" bgcolor="#fff9ef"><span class="inset_maro_inchis_16_bold">&nbsp;&nbsp;12 months <span class="bold_verde_12">(save <? print "".$row['price_12m_save']; ?>)</span></span></td>
        <td align="center" bgcolor="#fff9ef"><span class="font_16"><? print "".$row['price_12m']; ?></span></td>
      </tr>
      <tr>
        <td align="center" bgcolor="#fff9ef"><input type="radio" name="period" id="period" value="24" /></td>
        <td bgcolor="#fff9ef"><span class="inset_maro_inchis_16_bold">&nbsp;&nbsp;24 months <span class="bold_verde_12">(save <? print "".$row['price_24m_save']; ?>)</span></span></td>
        <td align="center" bgcolor="#fff9ef"><span class="font_16"><? print "".$row['price_24m']; ?></span></td>
      </tr>
    </table>
      <br />
      <br />
      <table width="90%" border="0" cellspacing="2" cellpadding="0">
        <tr>
          <td height="85" colspan="3" align="left" valign="top"><span class="bold_red_18">Leverage</span><br />
            <span class="font_12">If you buy this features, customers will be able to buy / sell this stock using only a fraction of the price. For example, if Apple is trading at $100 and you use x20 leverage,  one Apple share can be bought using only &amp;#3647; 0.05. For x10 leverage, you will need $10.</span></td>
        </tr>
        <tr>
          <td width="7%" align="center" bgcolor="#fff9ef"><input name="leverage" type="radio" id="leverage" value="<? print $row['lev_1_proc']; ?>" checked="checked" /></td>
          <td width="78%" align="left" bgcolor="#fff9ef" class="inset_maro_inchis_16_bold">&nbsp;&nbsp;x<? print $row['lev_1']; ?></td>
          <td width="15%" align="center" bgcolor="#fff9ef" class="font_16"><? if ($row['lev_1_proc']==0) print "free"; else print "+".$row['lev_1_proc']."%"; ?></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#fff9ef"><input type="radio" name="leverage" id="leverage" value="<? print $row['lev_2_proc']; ?>" /></td>
          <td align="left" bgcolor="#fff9ef"><span class="inset_maro_inchis_16_bold">&nbsp;&nbsp;x<? print $row['lev_2']; ?></span></td>
          <td align="center" bgcolor="#fff9ef"><span class="font_16"><? if ($row['lev_2_proc']==0) print "free"; else print "+".$row['lev_2_proc']."%"; ?></span></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#fff9ef"><input type="radio" name="leverage" id="leverage" value="<? print $row['lev_3_proc']; ?>" /></td>
          <td align="left" bgcolor="#fff9ef"><span class="inset_maro_inchis_16_bold">&nbsp;&nbsp;x<? print $row['lev_3']; ?></span></td>
          <td align="center" bgcolor="#fff9ef"><span class="font_16"><? if ($row['lev_3_proc']==0) print "free"; else print "+".$row['lev_3_proc']."%"; ?></span></td>
        </tr>
        <tr>
          <td align="center" bgcolor="#fff9ef"><input type="radio" name="leverage" id="leverage" value="<? print $row['lev_4_proc']; ?>" /></td>
          <td align="left" bgcolor="#fff9ef"><span class="inset_maro_inchis_16_bold">&nbsp;&nbsp;x<? print $row['lev_4']; ?></span></td>
          <td align="center" bgcolor="#fff9ef"><span class="font_16"><? if ($row['lev_4_proc']==0) print "free"; else print "+".$row['lev_4_proc']."%"; ?></span></td>
        </tr>
      </table>
      <br /><br />
      </td>
  </tr>
  <tr>
    <td height="50" align="center" valign="bottom" background="../../template/GIF/panel_middle_dark.png"><table width="90%" border="0" cellspacing="0" cellpadding="5">
      <tr>
        <td align="right"><a href="#" onclick="javascript:" onclick="$('#form_lic').submit();" class="btn btn-primary">Rent</a></td>
      </tr>
    </table>
     
    </td>
  </tr>
  <tr>
    <td><img src="../../template/GIF/panel_bottom.png" width="560" /></td>
  </tr>
</table>
</form>
        
        <?
	}
	
	
}
?>