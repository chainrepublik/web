<?
class CInvCodes
{
	function CInvCodes($db, $utils)
	{
		$this->kern=$db;
		$this->utils=$utils;
	}
	
	function showCodes($search="")
	{
		$query="SELECT * FROM inv_codes";
		if ($search!="") $query=$query." WHERE code LIKE '%".$search."%' OR user LIKE '".$search."'";
		$query=$query." ORDER BY tstamp DESC LIMIT 0,25"; 
		
		$result=$this->kern->execute($query);	
	   
		?>
        
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-hover table-striped" style="width:600px">
           
           <?
		      while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="498"><? print $row['code']; ?><br><span class='simple_gri_10'>User : <? print $row['user']; ?></span></td>
                <td width="100"><? if ($row['tstamp']>0) print $this->kern->getAbsTime(); else print "unused"; ?></td>
               </tr>
           
           <?
			  }
		   ?>
           
           </table>
        
        <?
	}
	
	function generate()
	{
		$query="SELECT * FROM inv_codes";
		$result=$this->kern->execute($query);	
	  
		for ($a=1; $a<25; $a++)
		{
		   $code="PIPS-".rand(100, 999)."-".rand(100, 999)."-".rand(100, 999);
		   $codes=$codes.$code.", ";
		   
		   $query="INSERT INTO inv_codes SET code='".$code."'";
		   $this->kern->execute($query);	
		}
		
		?>
        
             <table width="700" border="0" cellspacing="0" cellpadding="0">
             <tr>
             <td height="30" align="center" bgcolor="#f0f0f0">Copy Paste the following codes</td>
             </tr>
             <tr>
             <td height="100" align="center" bgcolor="#fafafa"><? print $codes; ?></td>
             </tr>
             </table>
        
        <?
	}
}
?>