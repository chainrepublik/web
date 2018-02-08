<?
class CUsedProd
{
	function CUsedProd($db, $utils)
	{
		$this->kern=$db;
		$this->utils=$utils;
	}
	
	function addProd($com_type, $prod, $type, $split)
	{
		$query="INSERT INTO com_prods 
		                SET com_type=?, 
						    prod=?, 
							type=?, 
							buy_split=?";
		
		$this->kern->execute($query, 
							 "ssss", 
							 $com_type, 
							 $prod, 
							 $type, 
							 $split);	
	}
	
	function showProdTypesDD($id, $sel="")
	{
		$query="SELECT * 
		          FROM tipuri_companii 
				 WHERE ID>? 
			  ORDER BY name ASC";
		
		$result=$this->kern->execute($query, "i", 0);	
	  
		print "<select id='".$id."' name='".$id."' class='form-control' style='width:700px' onChange=\"window.location='com_prods.php?com='+$(this).val()\">";
		
		print "<option value=''>None</option>";
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
		   if ($row['tip']==$sel)
		     print "<option value='".$row['tip']."' selected>".$row['name']."</option>";
		   else
		     print "<option value='".$row['tip']."'>".$row['name']."</option>";
		}
		
		print "</select>";
	}
	
	function showProds($com_type)
	{
		if ($com_type=="") return false;
		
		$query="SELECT * 
		          FROM tipuri_companii 
				 WHERE tip=?";
		
		$result=$this->kern->execute($query, "s", $com_type);	
		
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$com_name=$row['name'];
		
		$query="SELECT *, 
		               cp.ID AS pID, 
					   tp.prod AS tip_prod,
					   tp.name AS prod_name 
		          FROM com_prods AS cp 
				  JOIN tipuri_produse AS tp ON tp.prod=cp.prod 
				  JOIN tipuri_companii AS tc ON tc.tip=cp.com_type 
				 WHERE cp.com_type=?";
		
		$result=$this->kern->execute($query, 
									 "s", 
									 $com_type);	
		
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
              <td width="375" align="left"><? print $row['prod_name']; ?><br /><span style="font-family:Verdana, Geneva, sans-serif; font-size:10px; color:#999999"><? print $row['tip_prod']; ?></span></td>
              <td width="145" align="center">
			  <? 
				 switch ($row['type'])
				 {
					 case "ID_RAW" : print "Raw Material"; break;
					 case "ID_FINITE" : print "Finite Material"; break;
					 case "ID_TOOLS" : print "Production Tools"; break;
					 case "ID_BUILDING" : print "Building"; break;
					 case "ID_OTHER" : print "Other"; break;
				 }
		      ?>
              </td>
              <td width="80" align="center">
			  <? 
			     switch ($row['buy_split'])
				 {
					 case "ID_YES" : print "Yes"; break;
					 case "ID_NO" : print "No"; break;
				 }
			  ?>
              </td>
              
              <td width="80" align="center">
              <a href="del.php?tab=com_prods&ID=<? print $row['pID']; ?>&com=<? print $_REQUEST['com']; ?>" class="btn btn-danger">Delete</a>
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
		$this->utils->showModalHeader("add_modal", "Add Product", "act", "add_prod", "com", "");
		
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
                <td align="center" class="bold_gri_18">Add Product</td>
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
                <td height="45" align="right" valign="middle" class="bold_gri_14">Prod&nbsp;&nbsp;</td>
                <td height="40" align="left" valign="middle">
                <? $this->utils->showProdTypesDD("dd_prod_type"); ?>
                </td>
              </tr>
              <tr>
                <td height="45" align="right" valign="middle" class="bold_gri_14">Type&nbsp;&nbsp;</td>
                <td height="40" align="left" valign="middle">
                <select id="dd_type" name="dd_type" class="form-control">
                <option value="ID_RAW">Raw Material</option>
                <option value="ID_FINITE">Finite Material</option>
                <option value="ID_TOOLS">Production Tools</option>
                <option value="ID_BUILDING">Building</option>
                <option value="ID_OTHER">Other</option>
                </select>
                </td>
              </tr>
              <tr>
                <td height="45" align="right" valign="middle" class="bold_gri_14">Buy Split&nbsp;&nbsp; </td>
                <td height="40" align="left" valign="middle">
                <select id="dd_split" name="dd_split" class="form-control">
                <option value="ID_YES">Yes</option>
                <option selected value="ID_NO">No</option>
                </select>
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