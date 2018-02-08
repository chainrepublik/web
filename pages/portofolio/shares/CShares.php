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
		$query="SELECT sh.qty, 
		               com.name, 
					   tc.tip_name, 
					   com.pic, 
					   tc.pic AS default_pic, 
					   com.ID,
					   mkt.ask
		          FROM shares AS sh 
				  JOIN companies AS com ON com.symbol=sh.symbol 
				  JOIN v_mkts AS mkt ON mkt.symbol=com.symbol 
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip 
				 WHERE sh.owner_type='ID_CIT' 
				   AND sh.ownerID='".$_REQUEST['ud']['ID']."'";
		 $result=$this->kern->execute($query);	
	     
	  
		?>
        
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="53%" class="bold_shadow_white_14">Company</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center" class="bold_shadow_white_14">Qty</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="10%" align="center" class="bold_shadow_white_14">Price</td>
                <td width="3%" align="center" class="bold_shadow_white_14"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="16%" align="center" class="bold_shadow_white_14">Trade</td>
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
               <td width="55%" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
               <tr>
                <td width="18%"><img src="<? if ($row['pic']=="") print "../../companies/overview/GIF/prods/big/".$row['default_pic'].".png"; else print "../../../uploads/".$row['pic'];  ?>" width="40" height="40" class="img-circle"/></td>
                <td width="82%" align="left"><a href="../../companies/overview/main.php?ID=<? print $row['ID']; ?>" class="font_16"><? print $row['name']; ?></a><br /><span class="font_10"><? print $row['tip_name']; ?></span></td>
               </tr>
               </table></td>
               <td width="15%" align="center" class="bold_verde_14"><? print $row['qty']; ?></td>
               <td width="12%" align="center" class="bold_verde_14"><? print "".$row['ask']; ?></td>
               <td width="18%" align="center" class="bold_verde_14"><a href="../../companies/overview/shares.php?ID=<? print $row['ID']; ?>" class="btn btn-primary" style="width:80px">Trade</a></td>
               </tr>
               <tr>
               <td colspan="4" ><hr></td>
               </tr>
          <?
			 }
		  ?>
          
          </table>
        
        <?
	}
}
?>