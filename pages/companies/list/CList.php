<?php
class CList
{
	function CList($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showSelector()
	{
		?>
        
        <br>
        <form method="post" name="form_sel" id="form_sel">
        <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="90" align="center" bgcolor="#eef5f9">
             <table width="540" border="0" cellspacing="0" cellpadding="0">
              <tr class="font_14">
                <td width="50%" height="30" align="left" valign="top">Company Type</td>
                <td width="50%" height="30" align="left" valign="top">Order By</td>
              </tr>
              <tr>
                <td>
                
                <select name="dd_com_type" id="dd_com_type" class="form-control" style="width:240px" onChange="change()">
                <option selected value='ID_ALL'>All</option>
                
				<?php
				    $query="SELECT * 
					          FROM tipuri_companii
							 WHERE ID>?
						  ORDER BY name ASC";
		
					$result=$this->kern->execute($query, "i", 0);	
	                
					while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					  print "<option value='".$row['tip']."'>".$row['name']."</option>";
					
                ?>
                </select>
                
                </td>
                <td>
                
                <select name="dd_order_by" id="dd_order_by" class="form-control" style="width:240px" onChange="change()">
                <option value="ID_BALANCE">Balance</option>
                <option value="ID_WORKPLACES">Workplaces</option>
                <option value="ID_LICENCES">Licences Number</option>
                <option value="ID_SHARE_PRICE">Share Price</option>
                </select>
                
                <script>
				  function change()
				  {
					  fadeOut("div_list", "get_page.php?act=show_list", "form_sel");
				  }
				</script>
                
                </td>
              </tr>
            </table></td>
          </tr>
        </table>
        </form>
        
        <?php
	}
	
	function showCompanies($tip="ID_ALL", $order_by="ID_BALANCE")
	{
		if ($order_by=="ID_BALANCE")
		{
			 $col="Balance";
			
			if ($tip=="ID_ALL") 
			{
			      $query="SELECT com.*, 
						         adr.balance,
								 adr.pic AS adr_pic,
								 tc.pic
			                FROM companies AS com 
							JOIN tipuri_companii AS tc ON tc.tip=com.tip
							JOIN adr AS adr ON adr.adr=com.adr
						ORDER BY adr.balance DESC
						    LIMIT 0,30";
				
				  $result=$this->kern->execute($query);
			}
			else
			{
								 
								      $query="SELECT com.*, 
									                 tc.tip_name, 
													 tc.pic, 
													 com.pic AS com_pic, 
													 ba.balance
			                                   FROM companies AS com 
											   JOIN tipuri_companii AS tc ON tc.tip=com.tip
										       JOIN bank_acc AS ba ON ba.ownerID=com.ID 
										      WHERE ba.owner_type='ID_COM'
											    AND com.tip='".$tip."' 
									       ORDER BY ba.balance DESC
										      LIMIT 0,30";
											  
			}
		}
		
		
		
		
		?>
           
           <div id="div_list" name="div_list">
           <br />
           <table width="560" border="0" cellspacing="0" cellpadding="0">
           <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="76%" class="bold_shadow_white_14">Company</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="21%" align="center" class="bold_shadow_white_14"><?php print $col; ?></td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          <?php
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		  ?>
          
                <tr>
                <td width="77%" align="left" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td width="16%"><img src="
				<?php 
				     if ($row['adr_pic']=="") 
					    print "../overview/GIF/prods/big/".$row['pic'].".png";
					 else
					    print base64_decode($row['adr_pic']); 
				 ?>
                
                " width="50" height="50" class="img-rounded" /></td>
                <td width="86%" align="left">
                <a href="../overview/main.php?ID=<?php print $row['comID']; ?>" class="font_14"><strong><?php print base64_decode($row['name']); ?></strong></a>
                <br />
                <span class="font_10">Symbol : <?php print $row['symbol']; ?></span>
                </td>
                </tr>
                </table></td>
                <td width="23%" align="center">
				<span class="bold_verde_14">
				<?php 
				    switch ($order_by)
					{
					   case "ID_BALANCE" : print "".round($row['balance'], 4); 
					                       print " </span><br><span class='simple_green_10'>$".$this->kern->getUSD($row['balance'])."</span>";
										   break;
					}
				?>
               
                </td>
                </tr><tr>
                <td colspan="2" ><hr></td>
                </tr>
          
          <?php
	           }
		  ?>
          
         </table>
         </div>
         <br><br><br>
        
        <?php
	}
}
?>