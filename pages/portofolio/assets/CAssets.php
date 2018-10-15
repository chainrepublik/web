<?php
class CAssets
{
	function CAssets($db, $template, $acc)
	{
		$this->kern=$db;
		$this->template=$template;
		$this->acc=$acc;
	}
	
	function showAssets()
	{
		    $query="SELECT assets.adr, 
			               assets.title, 
						   assets.symbol, 
						   assets.pic,
						   assets.trans_fee,
						   assets.qty AS total_qty,
						   ao.qty 
		              FROM assets 
					  JOIN assets_owners AS ao ON ao.symbol=assets.symbol 
				     WHERE LENGTH(ao.symbol)=?
				       AND ao.owner=?
			         LIMIT 0,20"; 
			
			$result=$this->kern->execute($query, 
										 "is",
										 6,
										 $_REQUEST['ud']['adr']);	
		
		
		?>
                  
                  <br><br>
                  <table width="550px" border="0" cellspacing="0" cellpadding="0">
                      
                      <?php
					     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						 {
					  ?>
                      
                            <tr>
                            <td width="3%"><img src="<?php if ($row['pic']=="") print "../../template/GIF/asset.png"; else print $this->kern->crop($row['pic'], 50, 50); ?>"  class="img-circle" width="50"/></td>
                            <td width="2%">&nbsp;</td>
                            <td width="60%">
                            <span class="font_14"><a href="../../home/assets/asset.php?symbol=<?php print $row['symbol']; ?>">
								<?php print "<strong>".$this->kern->noescape(base64_decode($row['title']))."</strong> (".$row['symbol'].")"; ?></a></span><br>
                            <span class="font_10"><?php print "Trans Fee : <strong>".$row['trans_fee']."%</strong>, Issuer : ".$this->template->formatAdr($row['adr']); ?></span></td>
							
								<td width="25%" class="font_14" align="center">
									<strong><?php print $this->acc->getTransPoolBalance($_REQUEST['ud']['adr'], $row['symbol']); ?></strong><br><span class="font_10"><?php print round($row['qty']*100/$row['total_qty'], 2)."%"; ?></span></td>
							 
						      <td width="15%" class="font_14" align="center">
								  <a href="javascript:void(0)" class="btn btn-primary" onClick="
																								$('#send_coins_modal').modal(); $('#txt_cur').val('<?php print $row['symbol'] ?>'); $('#tab_assets').css('display', 'block'); $('#tab_CRC').css('display', 'none');">
									  <span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp;Send</a></td>
								
							
  </tr>
                            <tr>
                            <td colspan="5"><hr></td>
                            </tr>
                      
                      <?php
	                      }
					  ?>
                        
                  </table>
                  
                 
        
        <?php
	}
	
}
?>