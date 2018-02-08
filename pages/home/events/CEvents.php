<?
class Cevents
{
	function CEvents($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showEvents()
	{
		// Load events
		$query="SELECT * 
		          FROM events 
				 WHERE adr=?
			  ORDER BY ID DESC 
				 LIMIT 0,25";
		
		// Load data
		$result=$this->kern->execute($query, 
									 "s", 
									 $_REQUEST['ud']['adr']);	
	   
	  
		?>
        
          <table width="95%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="81%" class="bold_shadow_white_14">Explanation</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="16%" align="center" class="bold_shadow_white_14">Time</td>
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
                <td width="82%" class="simple_gri_14">
                <?
				   if ($row['viewed']==0)
				     print "<strong>".$row['evt']."</strong>";
				   else
				     print $row['evt'];
				?>
                </td>
                <td width="18%" align="center" class="font_14">
                 <?
				   if ($row['viewed']==0)
				     print "<strong>".$this->kern->timeFromBlock($row['block'])."</strong>";
				   else
				     print $this->kern->timeFromBlock($row['tstamp']);
				?>
                </td>
                </tr>
                <tr>
                <td colspan="2" ><hr></td>
                </tr>
          
          <?
			 }
		  ?>
            
        </table>
        
        <?
		
		// Set unread events to zero
		$query="UPDATE web_users 
		           SET unread_events=0 
				 WHERE ID=?";
				 
		$this->kern->execute($query, 
							 "i", 
							 $_REQUEST['ud']['ID']);
		
		// Set events as read
		$query="UPDATE events 
		           SET viewed=? 
				 WHERE adr=?";
				   
		$this->kern->execute($query, 
							 "is", 
							 time(), 
							 $_REQUEST['ud']['adr']);
	}
}
?>