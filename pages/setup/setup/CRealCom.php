<?
class CRealCom
{
	function CRealCom($db, $utils)
	{
		$this->kern=$db;
		$this->utils=$utils;
	}
	
	function showCom($search="")
	{
		$query="SELECT * FROM real_com";
		if ($search!="") $query=$query." WHERE name LIKE '%".$search."%' OR symbol LIKE '%".$search."%'";
		$query=$query." ORDER BY name ASC LIMIT 0,25"; 
		
		$result=$this->kern->execute($query);	
	   
		?>
        
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-hover table-striped" style="width:600px">
           
           <?
		      while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="498"><? print $row['name']; ?><br><span class='simple_gri_10'>Symbol : <? print $row['symbol']; ?></span></td>
                <td width="77" align="center"><a href="edit_real_com.php?ID=<? print $row['ID']; ?>" class="btn btn-success" style="width:60px">Edit</a></td>
                </tr>
           
           <?
			  }
		   ?>
           
           </table>
        
        <?
	}
	
	function showSectors($sel)
	{
		?>
        
         <select id="dd_categ_1" name="dd_categ_1" class="form-control" onchange="dd_change()">
        <?
		   $query="SELECT * FROM sectors";
		   $result=$this->kern->execute($query);
		   	
	       while ($sec_row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
		     if ($sec_row['ID']==$sel) 
			    print "<option selected value='".$sec_row['ID']."'>".$sec_row['sector']."</option>";
		     else
			    print "<option value='".$sec_row['ID']."'>".$sec_row['sector']."</option>";
		   }
		?>
        </select>
        
        <script>
		function dd_change()
		{
		   $('#div_categ_2').load('get_page.php?act=get_sub_sectors&sectorID='+$('#dd_categ_1').val());
		}
        </script>
        
        <?
	}
	
	function showSubSectors($secID, $sel, $visible=false)
	{
		?>
         
         <div id="div_categ_2" name="div_categ_2">
         <select id="dd_categ_2" name="dd_categ_2" class="form-control" style="display:<? if ($visible==true) print "block"; else print "none"; ?>">
        <?
		   $query="SELECT * FROM sub_sectors WHERE sectorID='".$secID."'";
		   $result=$this->kern->execute($query);
		   if (mysql_num_rows($result)==0)
		   {
			   $query="SELECT * FROM sub_sectors WHERE sectorID='1'";
		       $result=$this->kern->execute($query);
		   }
		   
	       while ($sec_row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
		     if ($sec_row['ID']==$sel) 
			    print "<option selected value='".$sec_row['ID']."'>".$sec_row['sub_sector']."</option>";
		     else
			    print "<option value='".$sec_row['ID']."'>".$sec_row['sub_sector']."</option>";
		   }
		?>
        </select>
        </div>
        
        <?
	}
	
	function editProd($ID)
	{
		$query="SELECT * FROM real_com WHERE ID='".$ID."'";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
		?>
        
        <form id="form_update" name="form_update" action="edit_real_com.php?act=update&ID=<? print $ID; ?>" method="post">
        <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="145" height="55" align="right">Name&nbsp;&nbsp;</td>
        <td width="455" align="left">
        <input type="text" name="txt_name" id="txt_name" class="form-control" value="<? print $row['name']; ?>"/></td>
      </tr>
      <tr>
        <td height="55" align="right">Symbol&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_symbol" id="txt_symbol" class="form-control" style="width:100px" value="<? print $row['symbol']; ?>"/></td>
      </tr>
      <tr>
        <td height="55" align="right">Categ&nbsp;&nbsp;</td>
        <td align="left">
        
		<?
		   $this->showSectors($row['categ_1']); 
		?>
        
        </td>
      </tr>
      <tr>
        <td height="55" align="right">Subcateg&nbsp;&nbsp;</td>
        <td align="left">
		
        <?
		   $this->showSubSectors($row['categ_1'], $row['categ_2'], true); 
		?>
        
        </td>
      </tr>
      <tr>
        <td height="55" align="right" valign="middle">Shares&nbsp;&nbsp;</td>
        <td align="left"><input type="text" name="txt_shares_no" id="txt_shares_no" class="form-control" style="width:100px" value="<? print $row['shares_no']; ?>"/></td>
      </tr>
      <tr>
        <td height="55" align="right" valign="top">Description&nbsp;&nbsp;</td>
        <td align="left">
        <textarea class="form-control" id="txt_desc" name="txt_desc" rows="5"><? print base64_decode($row['description']); ?></textarea>
        </td>
      </tr>
      <tr>
        <td height="55" align="right">Link&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_link" id="txt_link" class="form-control" value="<? print $row['link']; ?>"/></td>
      </tr>
      <tr>
        <td height="10" colspan="2" align="right">&nbsp;</td>
      </tr>
      <tr>
        <td height="10" colspan="2" align="right" background="../../template/GIF/lp.png">&nbsp;</td>
        </tr>
      <tr>
        <td height="50" align="right">&nbsp;</td>
        <td align="right"><a href="#" onclick="javascript:$('#txt_desc').val(btoa($('#txt_desc').val())); $('#form_update').submit()" class="btn btn-success">Update</a></td>
      </tr>
    </table>
    </form>
    
   
        
        <?
	}
	
	
	function update($ID)
	{
		$query="UPDATE real_com 
		           SET name='".$_REQUEST['txt_name']."', 
				       symbol='".$_REQUEST['txt_symbol']."', 
					   categ_1='".$_REQUEST['dd_categ_1']."', 
					   categ_2='".$_REQUEST['dd_categ_2']."', 
					   description='".$_REQUEST['txt_desc']."',
					   shares_no='".$_REQUEST['txt_shares_no']."',
					   link='".$_REQUEST['txt_link']."'
			     WHERE ID='".$ID."'"; 
		$this->kern->execute($query);
		
		// Licence
		$query="SELECT * 
		          FROM tipuri_licente 
				 WHERE prod='".$_REQUEST['txt_symbol']."'";
		$result=$this->kern->execute($query);
		if (mysql_num_rows($result)==0)
		{
			$query="INSERT INTO tipuri_licente 
			                SET tip='ID_LIC_TRADE_STOCK', 
							    com_type='ID_COM_BROKER_STOCKS', 
								prod='".$_REQUEST['txt_symbol']."', 
								lic_type='ID_STOCK',
								lic_name='Licence to trade ".$_REQUEST['txt_name']." (".$_REQUEST['txt_symbol'].")',
								lev_1='1', 
								lev_2='5',
								lev_3='10',
								lev_4='15',
								lev_5='20',
								lev_1_proc='0',
								lev_2_proc='5',
								lev_3_proc='10',
								lev_4_proc='15',
								lev_5_proc='20'";
			$this->kern->execute($query);
		}
	}
}
?>