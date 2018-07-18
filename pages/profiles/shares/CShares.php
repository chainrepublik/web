<?
class CShares
{
	function CShares($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
    
	function showAssets($adr, $type)
	{
		// Length
		if ($type=="ID_ASSETS")
			$length=6;
		else
			$length=5;
			
	    // Query
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
									 $length,
									 $adr);	
		
		
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
                            <span class="font_14"><a href="../../home/assets/asset.php?symbol=<? print $row['symbol']; ?>">
								<? print "<strong>".$this->kern->noescape(base64_decode($row['title']))."</strong> (".$row['symbol'].")"; ?></a></span><br>
                            <span class="font_10"><? print "Trans Fee : <strong>".$row['trans_fee']."%</strong>, Issuer : ".$this->template->formatAdr($row['adr']); ?></span></td>
							
								<td width="25%" class="font_14" align="center">
									<strong><? print $this->acc->getTransPoolBalance($adr, $row['symbol']); ?></strong><br><span class="font_10"><? print round($row['qty']*100/$row['total_qty'], 2)."%"; ?></span></td>
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