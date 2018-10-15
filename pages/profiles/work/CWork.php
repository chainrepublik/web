<?php
class CWork
{
	function CWork($db, $acc, $template, $userID)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
		$this->userID=$userID;
	}
	
	
	
	function showHistory($day, $month, $year)
	{
		$query="SELECT wp.*, wp.tstamp AS time, com.*, tc.* 
		          FROM work_procs AS wp
				  JOIN companies AS com ON com.ID=wp.comID
				  JOIN tipuri_companii AS tc ON tc.tip=com.tip
				 WHERE wp.userID='".$this->userID."' 
			  ORDER BY wp.ID DESC 
			     LIMIT 0,20"; 
		$result=$this->kern->execute($query);	
	   
	  
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
                <td width="49%" class="bold_shadow_white_14">Employer</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="15%" align="center"><span class="bold_shadow_white_14">Time</span></td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="13%" align="center"><span class="bold_shadow_white_14">Prod</span></td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Salary</td>
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
               <td width="52%">
               <table width="200" border="0" cellspacing="0" cellpadding="0">
               <tr>
               <td width="53"><img src="../../companies/overview/GIF/prods/big/<?php print $row['pic']; ?>.png" width="50" height="50" class="img-circle"/></td>
               <td width="147">
               <a href="#" class="font_14"><strong><?php print $row['name']; ?></strong></a>
               <br />
               <span class="font_10"><?php print $row['tip_name']; ?></span></td>
               </tr>
               </table></td>
               <td width="18%" align="center" class="font_14"><?php print $this->kern->getAbsTime($row['time']); ?></td>
               <td width="15%" align="center" class="bold_gri_14"><?php print $row['productivity']."%"; ?></td>
               <td width="15%" align="center" class="bold_verde_14"><?php print "".$row['salary']; ?></td>
               </tr>
               <tr>
               <td colspan="4" ><hr></td>
               </tr>
          
          <?php
			 }
		  ?>
          
          </table>
        
        <?php
	}
}
?>