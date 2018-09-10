<?
class COrders
{
	function COrders($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showOrders($type)
	{
		$result=$this->kern->getResult("SELECT ord.*, us.user  
			                              FROM orders AS ord 
									      JOIN users AS us ON us.ID=ord.userID 
									     WHERE status=? 
									       AND type=? 
								      ORDER BY price DESC 
								         LIMIT 0,25", 
								      "ss", 
								      "ID_LIVE", 
								      "ID_SELL");
		?>
             
             <br>
             <table class="table table-striped table-hover">
				 <thead>
					 <tr>
						 <th class="font_14" width="20%">Trader</th>
						 <th class="font_14" width="20%">Type</th>
						 <th class="font_14" width="20%">Qty</th>
						 <th class="font_14" width="20%">Price</th>
						 <th class="font_14" width="20%">Posted</th>
					 </tr>
				 </thead>
				 
				 <?
	          	    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				    {
		         ?>
				 
				        <tr>
					    <td class="font_14"><? print $row['user']; ?></td>
					    <td class="font_14" style="color: <? if ($row['type']=="ID_BUY") print "#009900"; else print "#990000"; ?>"><strong><? if ($row['type']=="ID_BUY") print "BUY"; else print "SELL"; ?></strong></td>
					    <td class="font_14"><? print $row['amount']; ?></td>
							<td class="font_14" style="color: <? if ($row['type']=="ID_BUY") print "#009900"; else print "#990000"; ?>"><strong><? print "$".$row['price']; ?></strong></td>
					    <td class="font_14" height="50"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
				        </tr>
				 
				 <?
					}
		         ?>
				 
             </table>

        <?
	}
}
?>