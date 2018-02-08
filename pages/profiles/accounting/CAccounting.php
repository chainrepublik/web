<?
class CAccounting
{
	function CAccounting($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showBankAcc($owner_type, $ownerID, $visible=false)
	{
		$query="SELECT * 
		          FROM bank_acc AS ba 
			      LEFT JOIN companies AS bank ON bank.ID=ba.bankID
			     WHERE ba.owner_type='".$owner_type."' 
			       AND ba.ownerID='".$ownerID."'";
		
		$result=$this->kern->execute($query);	
	  
	  
		?>
            
            <br>
            <div style="display:<? if ($visible==false) print "none"; else print "block"; ?>" id="tab_accounts">
            <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="79%" class="bold_shadow_white_14">Bank</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="18%" align="center" class="bold_shadow_white_14">Balance</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="550" border="0" cellspacing="0">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
             <tr>
             <td width="9%" class="font_14"><img src="../../companies/overview/GIF/prods/big/ID_BANK.png" width="40" height="40" class="img-circle"/></td>
            <td width="70%" align="left" class="font_16"><a href="#" class="font_16"><? print $row['acc']; ?></a><span class="font_12"> ( <? print $row['moneda']; ?> )</span><span class="font_14"><br /><a href="main.php?ID=<? print $row['bankID']; ?>" target="_blank" class="maro_12"><? print $row['name']; ?></a></span></td>
            <td width="21%" align="center"><span class="font_16"><? if ($row['moneda']=="GOLD") print "";  print round($row['balance'], 4); if ($row['moneda']!="GOLD") print "</span><br><span class=\"bold_verde_10\">".$row['moneda']."</span>"; ?></td>
            </tr>
          <tr>
            <td colspan="4" ><hr></td>
          </tr>
          
          <?
			 }
		  ?>
          
        </table>
        </div>
        
        <script>
		  $('#but_deposit').tooltip();
		  $('#but_withdraw').tooltip();
		  $('#but_move').tooltip();
		</script>
        
        <?
	}
	
	function showMenu()
	{
		?>
        
            <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td align="right"><? $this->template->showSmallMenu("History", "", "", "", "Accounts", 2); ?></td>
            </tr>
            </table>
            <br />
        
        <?
	}
}
?>