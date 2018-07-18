<?
class CInventory
{
	function CInventory($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showConsumeItems($type="ID_CIGARS", $adr)
	{
		switch ($type)
		{
			case "ID_CIGARS" : $prods="'ID_CIG_CHURCHILL', 'ID_CIG_PANATELA', 'ID_CIG_TORPEDO', 'ID_CIG_CORONA', 'ID_CIG_TORO'"; 
			                   $act="Smoke";
			                   break;
							   
			case "ID_DRINKS" : $prods="'ID_SAMPANIE', 'ID_MARTINI', 'ID_MARY', 'ID_SINGAPORE', 'ID_MOJITO', 'ID_PINA'"; 
			                   $act="Drink";
							   break;
							   
			case "ID_FOOD" : $prods="'ID_CROISANT', 'ID_HOT_DOG', 'ID_PASTA', 'ID_BURGER', 'ID_BIG_BURGER', 'ID_PIZZA'"; 
			                 $act="Eat";
							 break;
							 
			case "ID_WINE" : $prods="'ID_WINE'"; 
			                 $act="Drink";
							 break;
		}
		
		$query="SELECT st.*, 
		               tp.name 
		          FROM stocuri AS st 
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip 
				 WHERE st.tip IN (".$prods.") 
				   AND st.adr=?";
				   
		$result=$this->kern->execute($query, 
		                            "s", 
									$adr);	
		
		// No items
		if (mysqli_num_rows($result)==0) 
		   return false;
	  
		?>
          
          <br>
          <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td class="simple_blue_deschis_24">
                <?
				   switch ($type)
				   {
					   case "ID_CIGARS" : print "Cigars"; break; 
					   case "ID_DRINKS" : print "Drinks"; break; 
					   case "ID_FOOD" : print "Food"; break; 
					   case "ID_WINE" : print "Wine"; break; 
				   }
				?>
                </td>
              </tr>
            </tbody>
          </table>
<table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="52%" class="bold_shadow_white_14">Item</td>
                <td width="3%">&nbsp;</td>
                <td width="6%" align="center">&nbsp;</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center" class="bold_shadow_white_14">Energy</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
         
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          
          <?
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		  ?>
          
              <tr>
              <td width="52%" class="font_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
              <td width="18%" align="left">
              <img src="../../companies/overview/GIF/prods/big/<? print $row['tip']; ?>.png" width="55" height="55" class="img-circle"/></td>
              <td width="82%"><span class="font_14"><strong><? print $row['name']; ?></strong></span><br /><span class="simple_blue_10">
              <img src="../../template/GIF/stars_0.png" height="20" alt=""/></span></td>
              </tr>
              </tbody>
              </table></td>
              <td width="11%" align="center">&nbsp;</td>
              <td width="15%" align="center" class="font_14"><span class="simple_green_14"><strong>
			  <? 
			      print "+";
				  
				  if ($row['tip']!="ID_WINE")
				   print $this->kern->getProdEnergy($row['tip']); 
				  else
				    print round(5+$row['energy'], 4);
				?>
              
              </strong></span> <br />
              <span class="simple_green_10">points</span></td>
            </tr>
              <tr>
              <td colspan="3"><hr></td>
              </tr>
          
          <?
	         }
		  ?>
          
</table>
          <br>
        
        <?
	}
	
	function showRentItems($type, $visible=true)
	{
		$p="";
		
		switch ($type)
		{
			case "ID_CLOTHES" : $prods="'ID_SOSETE_Q1', 'ID_CAMASA_Q1', 'ID_GHETE_Q1', 'ID_PANTALONI_Q1', 'ID_PULOVER_Q1', 'ID_PALTON_Q1',
			                            'ID_SOSETE_Q2', 'ID_CAMASA_Q2', 'ID_GHETE_Q2', 'ID_PANTALONI_Q2', 'ID_PULOVER_Q2', 'ID_PALTON_Q2',
										'ID_SOSETE_Q3', 'ID_CAMASA_Q3', 'ID_GHETE_Q3', 'ID_PANTALONI_Q3', 'ID_PULOVER_Q3', 'ID_PALTON_Q3'"; 
			                    break;
							 
			case "ID_JEWELRY" : $prods="'ID_INEL_Q1', 'ID_CERCEL_Q1', 'ID_COLIER_Q1', 'ID_CEAS_Q1', 'ID_BRATARA_Q1'"; 
			                    break;
							 
			case "ID_CARS" : $prods="'ID_CAR_Q1', 'ID_CAR_Q2', 'ID_CAR_Q3'"; 
			                 break;
							 
			case "ID_HOUSES" : $prods="'ID_HOUSE_Q1', 'ID_HOUSE_Q2', 'ID_HOUSE_Q3'"; 
			                   break;
		}
		
		
		$query="SELECT st.*, 
		               tp.name, 
					   adr.name AS rented_to
			      FROM stocuri AS st
				  JOIN tipuri_produse AS tp ON tp.prod=st.tip
				  LEFT JOIN adr ON adr.adr=st.rented_to
			     WHERE (st.adr=? 
				    OR st.rented_to=?)
				   AND st.tip IN (".$prods.") 
			  ORDER BY st.ID DESC"; 
		
	    $result=$this->kern->execute($query, 
									 "ss", 
									 $_REQUEST['ud']['adr'], 
									 $_REQUEST['ud']['adr']);
		
		// No products	
		if (mysqli_num_rows($result)==0) 
			return false;	
		?>
            
            <br>
            <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td class="simple_blue_deschis_24">&nbsp;&nbsp;&nbsp;
                <?
				   switch ($type)
				   {
					   case "ID_CLOTHES" : print "Clothes"; $act="Wear"; break; 
					   case "ID_JEWELRY" : print "Jewelry"; $act="Wear"; break; 
					   case "ID_CARS" : print "Cars"; $act="Use"; break; 
					   case "ID_HOUSES" : print "Houses"; $act="Use"; break; 
				   }
				?>
                </td>
              </tr>
            </tbody>
          </table>
<table width="550" border="0" cellspacing="0" cellpadding="0" style="<? if ($visible==false) print "display:none"; ?>">
            <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="55%" class="bold_shadow_white_14">Product</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                
				<td width="10%" align="center" class="bold_shadow_white_14">Rent</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
				  
                <td width="10%" align="center" class="bold_shadow_white_14">Status</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0" cellpadding="0">
          
          <?
			 while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
				 $q=0;
				 
				 if (strpos($row['tip'], "Q1")>0) 
		            { 
		              $q=1; 
		              $title="Low Quality Product"; 
		            }
		
		            if (strpos($row['tip'], "Q2")>0) 
		            { 
		              $q=2; 
		              $title="Medium Quality Product"; 
		            }
		
		            if (strpos($row['tip'], "Q3")>0) 
		            {  
		              $q=3; 
		              $title="High Quality Product"; 
		            }
				 
				
				 if ($row['expire']>0)
				 {
				      $dif=$row['expire']-$row['tstamp'];
				      $remain=$row['expire']-time();
				      $d=100-round($remain*100/$dif);
				 }
				 else $d=0;
		  ?>
          
              
               <tr>
                 <td width="10%">
                 <img src="../../companies/overview/GIF/prods/big/<? print $this->kern->skipQuality($row['tip']); ?>.png" width="55" height="55" class="img-circle"/>
				 </td>
				   
                <td width="50%"><span class="font_14"><? print $row['name']; ?></span><br />
                
                <table width="120" border="0" cellspacing="0" cellpadding="0">
                <tr>
					<td><img src="../../template/GIF/stars_<? print $q; ?>.png" width="60" data-toggle="tooltip" data-placement="top" title="<? print $title; ?>" /></td>
                <td align="right">
				<span class="simple_green_10">
				<? print "+".$this->kern->getProdEnergy($row['tip'])." energy"; ?>
                </span>
                </td></tr>
                </table>
                
                </td>
                
				<td width="10%" align="center" class="font_14">
                <?
                        if ($row['rented_expires']==0) 
							print "<img src='GIF/rent_off.png' title='Not Rented' width='40px' data-toggle='tooltip' data-placement='top'>";
				        else
							print "<img src='GIF/rent_on.png' title='Rented to ".$row['rented_to']." for the next ".$this->kern->timeFromBlock($row['rented_expires'])."' width='40px' data-toggle='tooltip' data-placement='top'>";
					?>
				</td>
				   
                <td width="10%" align="center" class="font_14">
					<?
                        if ($row['in_use']==0) 
							print "<img src='GIF/use_off.png' title='Not Used' width='40px' data-toggle='tooltip' data-placement='top'>";
				        else
							print "<img src='GIF/use_on.png' title='In use' width='40px' data-toggle='tooltip' data-placement='top'>";
					?>
				</td>
                
              </tr>
				   <tr><td colspan="4"><hr></td></tr>
            
            
             
          
          <?
			 }
		  ?>
          
        </table>
        
        <?
	}
	
	function showGift($expires)
	{
	    ?>

            <table width="100" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
            <td height="150" align="center"><img src="GIF/gift.png" width="100" height="150"  title="Welcome gift. Expires in <? print $this->kern->timeFromBlock($expires); ?>" data-toggle="tooltip" data-placement="top"/></td>
            </tr>
</tbody></table>

        <?
	}
	
	function showTicket($prod, $qty)
	{
		$q=$this->kern->getQuality($prod);
	   ?>

            <table width="100"><tr><td height="150" background="GIF/ticket.png"  title="Travel ticket <? print $q; ?> stars" data-toggle="tooltip" data-placement="top">
	        <table width="90" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
            <td height="90" align="center">&nbsp;</td>
            </tr>
            <tr>
            <td align="center" valign="bottom"><img src="../../template/GIF/stars_1.png" width="90" height="20" alt=""/></td>
            </tr>
            <tr>
			<td height="35" align="center" valign="bottom" class="font_18" style="color: #9C742B"><strong><? print $qty; ?></strong></td>
            </tr></tbody></table></td></tr></table>

       <?
	}
	
	function showMisc()
	{
		$n=0;
		
		$query="SELECT * 
		          FROM stocuri 
				 WHERE (tip=?
				       OR tip=?
					   OR tip=?
					   OR tip=?
					   OR tip=?
					   OR tip=?) 
					   AND adr=?";
		
		$result=$this->kern->execute($query, 
									 "sssssss", 
									 "ID_TRAVEL_TICKET_Q1", 
									 "ID_TRAVEL_TICKET_Q2", 
									 "ID_TRAVEL_TICKET_Q3", 
									 "ID_TRAVEL_TICKET_Q4", 
									 "ID_TRAVEL_TICKET_Q5", 
									 "ID_GIFT",
									 $_REQUEST['ud']['adr']);
		
		
		?>
          
          <br>
          <table width="550" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  
				  <?
		             while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			         {
						 $n++;
		          ?>
					
		               <td width="100" align="center" valign="top">
						
						   <?
						    if (strpos($row['tip'], "TICKET")>0)
								$this->showTicket($row['tip'], $row['qty']);
						    else
								$this->showGift($row['expires']);
						?>
						   
		          </td>
				        <td align="center">&nbsp;</td>
                  
				  <?
	                 }
		
		             for ($a=1; $a<=5-$n; $a++)
					 {
						 ?>
					
					         <td width="100" align="center" valign="top">&nbsp;</td>
				             <td align="center">&nbsp;</td>
					
					     <?
					 }
		
		          ?>
					
                </tr>
              </tbody>
</table>

        <?
	}
	
	function showGuns()
	{
		
	}
}
?>