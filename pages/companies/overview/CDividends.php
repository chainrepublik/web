
<?
class CDividends
{
	function CDividends($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function showDividends()
	{
		// Top bar
		$this->template->showTopBar("Date", "80%", "Amount", "20%");
		
		// Query
		$query="SELECT * 
		          FROM dividends 
				 WHERE comID=? 
			  ORDER BY ID DESC 
			     LIMIT 0,25";
		
		// Result
		$result=$this->kern->execute($query, 
								     "i", 
									  $_REQUEST['ID']);
		
		?>

           <table width="540" border="0" cellspacing="0" cellpadding="0">
           <tbody>
			
		   <?
		        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
				{
		   ?>
			   
                  <tr>
                  <td class="font_14" width="80%"><? print $this->kern->timeFromBlock($row['block'])." ago<br><span class='font_10'>Block ".$row['block']."</span>"; ?></td>
                  <td class="font_14" style="color: #009900" width="20%" align="center"><? print round($row['amount'], 4)." CRC"; ?></td>
                  </tr>
                  <tr>
                  <td colspan="2"><hr></td>
                  </tr>
           
		   <?
				}
		   ?>
			   
		   </tbody>
           </table>


        <?
	}
}
?>