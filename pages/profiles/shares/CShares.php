<?
class CShares
{
	function CShares($db, $acc, $template, $userID)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->userID=$userID;
	}
	
	function showShares()
	{
		$query="SELECT sh.qty, com.name, tc.tip_name, com.pic, tc.pic AS default_pic, com.ID
		          FROM shares AS sh 
				  JOIN companies AS com ON com.symbol=sh.symbol 
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip 
				 WHERE sh.owner_type='ID_CIT' 
				   AND com.ownerID='".$this->userID."'";
		 $result=$this->kern->execute($query);	
	     
	  
		?>
        
          <table width="95%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="86%" class="bold_shadow_white_14">Company</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center" class="bold_shadow_white_14">Qty</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
          </table>
          
          <table width="90%" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
          
               <tr>
               <td width="87%" class="font_14"><table width="90%" border="0" cellspacing="0" cellpadding="0">
               <tr>
                <td width="11%"><img src="<? if ($row['pic']=="") print "../../companies/overview/GIF/prods/big/".$row['default_pic'].".png"; else print "../../../uploads/".$row['pic'];  ?>" width="40" height="40" class="img-circle"/></td>
                <td width="89%" align="left"><a href="../../companies/overview/main.php?ID=<? print $row['ID']; ?>" class="font_16"><? print $row['name']; ?></a><br /><span class="font_10"><? print $row['tip_name']; ?></span></td>
               </tr>
               </table></td>
               <td width="13%" align="center" class="bold_verde_14"><? print $row['qty']; ?></td>
               </tr>
               <tr>
               <td colspan="2" ><hr></td>
               </tr>
          <?
			 }
		  ?>
          
          </table>
        
        <?
	}
}
?>