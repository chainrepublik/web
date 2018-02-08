<?
class CBudget
{
	function CBudget($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showPanel()
	{
		// Income 24h
		$income=$_REQUEST['sd']['budget_24h'];
		
		// Spend 24h
		$spend=$_REQUEST['sd']['budget_spend_24h'];
		
		// Net 7D
		$net_7d=$_REQUEST['sd']['budget_net_7d'];
		
		$query="SELECT * 
		          FROM bank_acc 
				 WHERE owner_type='ID_BUG' 
				   AND moneda='GOLD'";
	    $result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$balance=$row['balance'];
		
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="465" align="center" valign="top" background="GIF/panel.png">
                
                <table width="560" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td height="100" align="center" style="font-size:40px; color:#242b32; font-family:'Times New Roman', Times, serif; text-shadow: 1px 1px 0px #777777;">Game Budget</td>
                  </tr>
                  <tr>
                    <td height="220" align="center">
                    
                    <table width="400" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td align="center" style="font-size:80px; color:#242b32; font-family:'Times New Roman', Times, serif; ">
						<? print "".round($balance, 4); ?>
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
                        <td width="100" align="center" valign="bottom"><span style="font-size:12px; color:#6c757e; font-family:Verdana, Geneva, sans-serif; text-shadow: 1px 1px 0px #333333;">Net Result 7D</span></td>
                        <td width="16">&nbsp;</td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td height="60" align="center" valign="bottom" class="bold_shadow_green_32"><? print "+".round($income, 4); ?></td>
                        <td align="center" valign="bottom">&nbsp;</td>
                        <td align="center" valign="bottom" class="bold_shadow_red_32">
						<? print "".round(abs($spend)); ?></td>
                        <td align="center" valign="bottom">&nbsp;</td>
                        <td align="center" valign="bottom"><span class="<? if ($income+$spend<0) print "bold_shadow_red_32"; else print "bold_shadow_green_32"; ?>">
						
						<? 
						
						    if (($income+$spend)<0) 
							   print "-".round(abs($income+$spend), 4); 
							else 
							   print "+".round(abs($income+$spend), 4); 
						?>
                        
                        </span></td>
                        <td align="center" valign="bottom">&nbsp;</td>
                        <td align="center" valign="bottom" class="bold_shadow_green_32">
						<? 
						    if ($net_7d<0) 
							   print "-".abs(round($net_7d, 4)); 
							else 
							   print "+".round($net_7d, 4); 
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
}
?>