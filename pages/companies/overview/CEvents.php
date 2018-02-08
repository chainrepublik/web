<?
class CEvents
{
	function CEvents($db, $comID)
	{
		$this->kern=$db;
		$this->comID=$comID;
		
		// 0 unread events
		$query="UPDATE companies 
		           SET events=0 
				 WHERE ID='".$comID."'";
		$this->kern->execute($query);
	}
	
	function showPage()
	{
		$query="SELECT * 
		          FROM events 
				 WHERE receiver_type='ID_COM' 
				   AND receiverID='".$this->comID."'
			  ORDER BY ID DESC LIMIT 0,20";
		$result=$this->kern->execute($query);
		
		?>
        
           <table width="90%" border="0" cellspacing="0" cellpadding="5">
           
           <?
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="13%" valign="top"><img src="../../template/GIF/warning.png" /></td>
                <td width="87%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
               
                <tr>
                <td class="font_12"><? print $row['evt']; ?></td>
                </tr>
                <tr>
                <td class="simple_mov_10"><? print $this->kern->getAbsTime($row['tstamp']); ?></td>
                </tr>
                </table></td>
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