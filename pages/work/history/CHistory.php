<?
class CHistory
{
	function CHistory($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	

	function showHistory()
	{
		$query="SELECT wp.*, 
		               wp.block AS time, 
					   com.*, 
					   tc.tip, tc.pic, tc.name AS com_type_name
		          FROM work_procs AS wp
				  JOIN companies AS com ON com.comID=wp.comID
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip
				 WHERE wp.adr=? 
			  ORDER BY wp.ID DESC 
			     LIMIT 0,20"; 
		
		$result=$this->kern->execute($query, 
									 "s", 
									 $_REQUEST['ud']['adr']);	
	   
	  
		?>
        
          <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="30" align="left" valign="top" class="bold_gri_18">Work History</td>
          </tr>
          </table>
        <table width="560" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="39%" class="bold_shadow_white_14">Employer</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="12%" align="center"><span class="bold_shadow_white_14">Time</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="11%" align="center" class="bold_shadow_white_14">Salary</td>
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
               <td width="40%">
               <table width="200" border="0" cellspacing="0" cellpadding="0">
               <tr>
               <td width="53"><img src="../../companies/overview/GIF/prods/big/<? print $row['pic']; ?>.png" width="50" height="50" class="img-circle"/></td>
               <td width="147">
			   <a href="#" class="font_14"><strong><? print base64_decode($row['name']); ?></strong></a><br><span class="font_10"><? print $row['com_type_name'].", ".$this->kern->timeFromBlock($row['block'])." ago"; ?></span>
               </td>
               </tr>
               </table></td>
				   <td width="15%" align="center" class="font_14"><? print $row['end']-$row['start']; ?><br><span class="font_10">minutes</span></td>
				   <td width="15%" align="center" class="font_14"><strong><? print "".round($row['salary'], 4); ?></strong><br><span class="font_10">CRC</span></td>
               </tr>
               <tr>
               <td colspan="5" ><hr></td>
               </tr>
          
          <?
			 }
		  ?>
          
          </table>
        
        <?
	}
}
?>