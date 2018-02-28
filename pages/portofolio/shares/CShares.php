<?
class CShares
{
	function CShares($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showShares()
	{
		    $query="SELECT assets.adr, 
			               com.name, 
						   assets.symbol, 
						   adr.pic,
						   assets.qty AS total_qty,
						   ao.qty,
						   com.comID
						FROM assets 
					  JOIN assets_owners AS ao ON ao.symbol=assets.symbol 
					  JOIN companies AS com ON com.symbol=ao.symbol
					  JOIN adr ON adr.adr=com.adr
				     WHERE LENGTH(ao.symbol)=?
				       AND ao.owner=?
			         LIMIT 0,20"; 
			
			$result=$this->kern->execute($query, 
										 "is",
										 5,
										 $_REQUEST['ud']['adr']);	
		
		
		?>
                  
                  <br><br>
                  <table width="550px" border="0" cellspacing="0" cellpadding="0">
                      
                      <?
					     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						 {
					  ?>
                      
                            <tr>
                            <td width="3%"><img src="<? if ($row['pic']=="") print "../../template/GIF/asset.png"; else print $this->kern->crop($row['pic'], 50, 50); ?>"  class="img-circle" width="50"/></td>
                            <td width="2%">&nbsp;</td>
                            <td width="60%">
                            <span class="font_14"><a href="../../companies/overview/main.php?ID=<? print $row['comID']; ?>">
								<? print "<strong>".$this->kern->noescape(base64_decode($row['name']))."</strong> (".$row['symbol'].")"; ?></a></span><br>
                            <span class="font_10"><? print "Trans Fee : <strong>0%</strong>, Issuer : ".$this->template->formatAdr($row['adr']); ?></span></td>
							
								<td width="25%" class="font_14" align="center">
									<strong><? print $this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], $row['symbol']); ?></strong><br><span class="font_10"><? print round($row['qty']*100/$row['total_qty'], 2)."%"; ?></span></td>
							 
						      <td width="15%" class="font_14" align="center">
								  <a href="javascript:void(0)" class="btn btn-primary" onClick="
																								$('#send_coins_modal').modal(); $('#txt_cur').val('<? print $row['symbol'] ?>'); $('#tab_assets').css('display', 'block'); $('#tab_CRC').css('display', 'none');">
									  <span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp;Send</a></td>
								
							
  </tr>
                            <tr>
                            <td colspan="5"><hr></td>
                            </tr>
                      
                      <?
	                      }
					  ?>
                        
                  </table>
                  
                 
        
        <?
	}
	
}
?>