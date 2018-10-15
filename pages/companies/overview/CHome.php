<?php
class CHome
{
	function CHome($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	
    function showOverviewPanel()
	{
		// QR modal
		$this->template->showQRModal();
		
		// Load company data
		$query="SELECT com.*,
		               adr.pic AS adr_pic
		          FROM companies AS com 
				  JOIN adr ON adr.adr=com.adr
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
		$balance=$this->acc->getTransPoolBalance($com_row['adr'], "CRC");
		
		// Workplaces
		$query="SELECT * 
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
                    <td width="33%"><img src="<?php if ($com_row['adr_pic']=="") print "./GIF/blank_photo.png"; else print base64_decode($com_row['adr_pic']); ?>" width="150" height="150" <?php if ($com_row['adr_pic']!="") print "class=\"img img-rounded\""; ?> /></td>
                    <td width="67%" align="left" valign="top">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td height="30"><span class="inset_blue_inchis_18"><?php print base64_decode($com_row['name'])." (".$com_row['symbol'].")"; ?></span></td>
                        </tr>
                        <tr>
                          <td><span class="simple_gri_14"><?php print base64_decode($com_row['description']); ?></span></td>
                        </tr>
                        <tr>
                          <td height="20" valign="bottom"><span class="font_10">Address : <?php print $this->template->formatAdr($com_row['adr'], 10); ?></span></td>
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
                    <td height="40" align="center" valign="bottom" class="bold_shadow_white_32">
					
					<?php 
					   print $expire;
	  		        ?>
                    
                    </td>
                    <td height="40" align="center" valign="bottom">&nbsp;</td>
                    <td height="40" align="center" valign="bottom">
                    <span class="bold_shadow_white_32"><?php $v=explode(".", round($balance, 4)); print "".$v[0]; ?></span><span class="bold_shadow_white_18"><?php if (sizeof($v)==2) print ".".$v[1]; else print ".0000"; ?></span>
                    </td>
                    <td height="40" align="center" valign="bottom">&nbsp;</td>
                    <td height="40" align="center" valign="bottom"><span class="bold_shadow_white_32"><?php print $workplaces; ?></span></td>
                    <td height="40" align="center" valign="bottom">&nbsp;</td>
                    <td height="40" align="center" valign="bottom">
                    <span class="bold_shadow_white_32"><?php $v=explode(".", $price); print "".$v[0]; ?></span><span class="bold_shadow_white_18"><?php if (sizeof($v)>1) print ".".$v[1]; else print ".00"; ?></span>
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
        <br />
        
        <?php
	}
	
	function showTrustBut()
	{
		if (!$this->kern->isLoggedIn())
			return false;
		
		if (!$this->kern->trustAsset($_REQUEST['ud']['adr'], 
                                     $this->kern->getComSymbol($_REQUEST['ID'])))
		{
		?>

            <table width="90%">
            <tr><td align="right">
            <a href="javascript:void(0)" onClick="$('#trust_modal').modal()" class="btn btn-primary"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;&nbsp;Trust Asset</a></td></tr>
            </table>
            <br>

        <?php
		}
	}
}
?>