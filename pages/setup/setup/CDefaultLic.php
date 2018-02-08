<?
class CDefaultLic
{
	function CDefaultLic($db, $utils)
	{
		$this->kern=$db;
		$this->utils=$utils;
	}
	
	function addLic($com_type, $lic)
	{
		$query="INSERT INTO default_lic 
		                SET com_type='".$com_type."', 
						    lic='".$lic."'"; 
		$this->kern->execute($query);	
	}
	
	function showComTypesDD($id, $sel="")
	{
		$query="SELECT * FROM tipuri_companii ORDER BY tip_name ASC";
		$result=$this->kern->execute($query);	
	  
		print "<select id='".$id."' name='".$id."' class='form-control' style='width:700px' onChange=\"window.location='default_lic.php?com='+$(this).val()\">";
		
		print "<option value=''>None</option>";
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
		   if ($row['tip']==$sel)
		     print "<option value='".$row['tip']."' selected>".$row['tip_name']."</option>";
		   else
		     print "<option value='".$row['tip']."'>".$row['tip_name']."</option>";
		}
		
		print "</select>";
	}
	
	function showLic($com_type)
	{
		if ($com_type=="") return false;
		
		$query="SELECT * FROM tipuri_companii WHERE tip='".$com_type."'";
		$result=$this->kern->execute($query);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$com_name=$row['tip_name'];
		
		$query="SELECT *, dl.ID AS lID
		          FROM default_lic AS dl
				  JOIN tipuri_licente AS tl ON tl.tip=dl.lic 
				 WHERE dl.com_type='".$com_type."'";
		$result=$this->kern->execute($query);	
		
		?>
           
           <br><br>
           <table width="700" border="0" cellspacing="0" cellpadding="0">
           <tr>
           <td align="right"><a href="#" onclick="$('#add_modal').modal(); $('#com').val('<? print $_REQUEST['com']; ?>'); $('#td_com_name').text('<? print $com_name; ?>')" class="btn btn-success">New Product<a/></td>
           </tr>
           </table>
           <br>
           <table border="0" cellspacing="0" cellpadding="0" class="table table-striped table-hover" style="width:700px">
           
           <?
		   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		   {
		   ?>
           
              <tr>
              <td width="375" align="left"><? print $row['lic_name']; ?></td>
              <td width="80" align="center">
              <a href="del.php?tab=default_lic&ID=<? print $row['lID']; ?>&com=<? print $_REQUEST['com']; ?>" class="btn btn-danger">Delete</a>
              </td>
              
              </tr>
           
           <?
	        }
		   ?>
           
           </table>
        
        <?
	}
	
	function showAddModal()
	{
		// Modal
		$this->utils->showModalHeader("add_modal", "Add Default Licence", "act", "add_lic", "com", "");
		
		?>
            
            <input type="hidden" name="valoare" id="valoare" value="2.43" />
            <input type="hidden" name="order_id" id="order_id" value="" />
            
              <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/add.png" width="126" height="123"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Add Licence</td>
              </tr>
            </table>
            <br /><br /></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="37%" height="45" align="right" valign="middle" class="bold_gri_14">Company&nbsp;&nbsp;</td>
                <td width="63%" height="40" align="left" valign="middle" class="bold_mov_14" id="td_com_name">&nbsp;</td>
              </tr>
              <tr>
                <td height="45" align="right" valign="middle" class="bold_gri_14">Licence&nbsp;&nbsp;</td>
                <td height="40" align="left" valign="middle">
                <? $this->utils->showLicTypesDD("dd_lic"); ?>
                </td>
              </tr>
              <tr>
                <td height="45">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
           
        <?
		
		$this->utils->showModalFooter("Cancel", "Add");
	}
}
?>