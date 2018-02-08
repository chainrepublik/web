<?
class CProdTypes
{
	function CProdTypes($db, $utils)
	{
		$this->kern=$db;
		$this->utils=$utils;
	}
	
	function getProdNetEnergy($prod, $qty)
	{
		$query="SELECT * FROM tipuri_produse WHERE prod=?";	
		$result=$this->kern->execute($query, "s", $prod);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		return round($row['net_energy']*$qty, 2);
	}
	
	function updateProdEnergy($prod)
	{
	    // Load product data
		$query="SELECT * FROM tipuri_produse WHERE prod=?";	
		$result=$this->kern->execute($query, "s", $prod);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Req Energy
		$req_energy=round($row['work_hours']*12, 2); 
		
		// Raw 1
		if ($row['prod_1']!="")
		  $req_energy=$req_energy+$this->getProdNetEnergy($row['prod_1'], $row['prod_1_qty']);
		  
		// Raw 2
		if ($row['prod_2']!="")
		  $req_energy=$req_energy+$this->getProdNetEnergy($row['prod_2'], $row['prod_2_qty']);
		  
		// Raw 3
		if ($row['prod_3']!="")
		  $req_energy=$req_energy+$this->getProdNetEnergy($row['prod_3'], $row['prod_3_qty']);
		   
		// Raw 4
		if ($row['prod_4']!="")
		  $req_energy=$req_energy+$this->getProdNetEnergy($row['prod_4'], $row['prod_4_qty']);
		  
		// Raw 5
		if ($row['prod_5']!="")
		  $req_energy=$req_energy+$this->getProdNetEnergy($row['prod_5'], $row['prod_5_qty']);
		  
		// Raw 6
		if ($row['prod_6']!="")
		  $req_energy=$req_energy+$this->getProdNetEnergy($row['prod_6'], $row['prod_6_qty']);
		  
		// Raw 7
		if ($row['prod_7']!="")
		  $req_energy=$req_energy+$this->getProdNetEnergy($row['prod_7'], $row['prod_7_qty']);
		  
		// Raw 8
		if ($row['prod_8']!="")
		  $req_energy=$req_energy+$this->getProdNetEnergy($row['prod_8'], $row['prod_8_qty']);
		  
		// Return
		$query="UPDATE tipuri_produse 
		           SET net_energy=? 
				 WHERE prod=?";
				 
		$this->kern->execute($query, 
		                     "ds", 
							 $req_energy, 
							 $prod);	
	}
	
	function showProds($search="")
	{
		$query="SELECT * FROM tipuri_produse";
		if ($search!="") $query=$query." WHERE name LIKE '%".$search."%' OR prod LIKE '%".$search."%'";
		$query=$query." ORDER BY name ASC"; 
		
		$result=$this->kern->execute($query);	
	   
		?>
        
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-hover table-striped" style="width:600px">
           
           <?
		      while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="498"><? print $row['name']; ?><br><span class='simple_gri_10'><? print $row['prod'].", <strong style='color:#000000'>".$row['net_energy']."</strong> energy"; ?></span></td>
                <td width="77" align="center"><a href="tipuri_produse.php?act=edit&ID=<? print $row['ID']; ?>" class="btn btn-success" style="width:60px">Edit</a></td>
                <td width="25" align="center"><a href="del.php?tab=tipuri_produse&ID=<? print $row['ID']; ?>&txt_search=<? print $_REQUEST['txt_search']; ?>" class="btn btn-danger" style="width:60px">Delete</a></td>
                </tr>
           
           <?
			  }
		   ?>
           
           </table>
        
        <?
	}
	
	function editProd($ID)
	{
		$query="SELECT * 
		          FROM tipuri_produse 
				 WHERE ID=?";
				 
		$result=$this->kern->execute($query, "i", $ID);	
	    
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
		?>
        
        <form id="form_update" name="form_update" action="tipuri_produse.php?act=update&ID=<? print $ID; ?>" method="post">
        <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="145" height="50" align="right">Product&nbsp;&nbsp;</td>
        <td width="455" align="left"><input type="text" name="txt_prod" id="txt_prod" class="form-control" value="<? print $row['prod']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Work Hours&nbsp;&nbsp;</td>
        <td align="left"><input type="text" name="txt_hours" id="txt_hours" class="form-control" style="width:100px" value="<? print $row['work_hours']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 1&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_1", $row['prod_1']); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 1 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_raw_1_qty" id="txt_raw_1_qty" class="form-control" style="width:100px" value="<? print $row['prod_1_qty']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 2&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_2", $row['prod_2']); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 2 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_raw_2_qty" id="txt_raw_1_qty" class="form-control" style="width:100px" value="<? print $row['prod_2_qty']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 3&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_3", $row['prod_3']); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 3 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_raw_3_qty" id="txt_raw_3_qty" class="form-control" style="width:100px" value="<? print $row['prod_3_qty']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 4&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_4", $row['prod_4']); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 4 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_raw_4_qty" id="txt_raw_4_qty" class="form-control" style="width:100px" value="<? print $row['prod_4_qty']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 5&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_5", $row['prod_5']); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 5 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_raw_5_qty" id="txt_raw_5_qty" class="form-control" style="width:100px" value="<? print $row['prod_5_qty']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 6&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_6", $row['prod_6']); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 6 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_raw_6_qty" id="txt_raw_6_qty" class="form-control" style="width:100px" value="<? print $row['prod_6_qty']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 7&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_7", $row['prod_7']); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 7 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_raw_7_qty" id="txt_raw_7_qty" class="form-control" style="width:100px" value="<? print $row['prod_7_qty']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 8&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_8", $row['prod_8']); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 8 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_raw_8_qty" id="txt_raw_8_qty" class="form-control" style="width:100px" value="<? print $row['prod_8_qty']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Unit&nbsp;&nbsp;</td>
        <td align="left">
        <?
		   $this->utils->showUnitate($row['unitate']);
        ?>
        </td>
      </tr>
      <tr>
        <td height="50" align="right">Name&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_name" id="txt_name" class="form-control" value="<? print $row['name']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Production Capacity&nbsp;&nbsp;</td>
        <td align="left"><input type="text" name="txt_cap" id="txt_cap" class="form-control" value="<? print $row['prod_capacity']; ?>" style="width:100px"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Damage&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_damage" id="txt_damage" class="form-control" value="<? print $row['damage']; ?>" style="width:100px"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Produced By&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showComTypesDD("dd_produced", $row['produced_by']); ?></td>
      </tr>
	  <tr>
        <td height="50" align="right">Expires&nbsp;&nbsp;</td>
        <td align="left"><input type="text" name="txt_expires" id="txt_expires" class="form-control" value="<? print $row['expires']; ?>" style="width:100px"/></td>
      </tr>
      <tr>
        <td height="10" colspan="2" align="right">&nbsp;</td>
      </tr>
      <tr>
        <td height="10" colspan="2" align="right" background="../../template/GIF/lp.png">&nbsp;</td>
        </tr>
      <tr>
        <td height="50" align="right">&nbsp;</td>
        <td align="right"><a href="#" onclick="javascript:$('#form_update').submit()" class="btn btn-success">Update</a></td>
      </tr>
    </table>
    </form>
        
        <?
	}
	
	function newProdPanel()
	{
		?>
        
        <form id="form_insert" name="form_insert" action="tipuri_produse.php?act=insert" method="post">
        <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="145" height="50" align="right">Product&nbsp;&nbsp;</td>
        <td width="455" align="left"><input type="text" name="txt_prod" id="txt_prod" class="form-control" value=""/></td>
      </tr>
      <tr>
        <td height="50" align="right">Work Hours&nbsp;&nbsp;</td>
        <td align="left"><input type="text" name="txt_hours" id="txt_hours" class="form-control" style="width:100px"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 1&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_1"); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 1 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input name="txt_raw_1_qty" type="text" class="form-control" id="txt_raw_1_qty" style="width:100px" value="0" /></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 2&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_2"); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 2 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input name="txt_raw_2_qty" type="text" class="form-control" id="txt_raw_1_qty" style="width:100px" value="0" /></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 3&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_3"); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 3 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input name="txt_raw_3_qty" type="text" class="form-control" id="txt_raw_3_qty" style="width:100px" value="0" /></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 4&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_4"); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 4 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input name="txt_raw_4_qty" type="text" class="form-control" id="txt_raw_4_qty" style="width:100px" value="0" /></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 5&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_5"); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 5 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input name="txt_raw_5_qty" type="text" class="form-control" id="txt_raw_5_qty" style="width:100px" value="0" /></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 6&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_6"); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 6 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input name="txt_raw_6_qty" type="text" class="form-control" id="txt_raw_6_qty" style="width:100px" value="0" /></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 7&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_7"); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 7 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input name="txt_raw_7_qty" type="text" class="form-control" id="txt_raw_7_qty" style="width:100px" value="0" /></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 8&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showProdTypesDD("dd_raw_8"); ?></td>
      </tr>
      <tr>
        <td height="50" align="right">Raw 8 Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input name="txt_raw_8_qty" type="text" class="form-control" id="txt_raw_8_qty" style="width:100px" value="0" /></td>
      </tr>
      <tr>
        <td height="50" align="right">Unit&nbsp;&nbsp;</td>
        <td align="left">
        <?
		   $this->utils->showUnitate();
        ?>
        </td>
      </tr>
      <tr>
        <td height="50" align="right">Name&nbsp;&nbsp;</td>
        <td align="left">
        <input name="txt_name" type="text" class="form-control" id="txt_name" value="" /></td>
      </tr>
      <tr>
        <td height="50" align="right">Production Capacity&nbsp;&nbsp;</td>
        <td align="left"><input type="text" name="txt_cap" id="txt_cap" class="form-control" value="0" style="width:100px"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Damage&nbsp;&nbsp;</td>
        <td align="left"><input type="text" name="txt_damage" id="txt_damage" class="form-control" value="0" style="width:100px"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Produced By&nbsp;&nbsp;</td>
        <td align="left"><? $this->utils->showComTypesDD("dd_produced"); ?></td>
      </tr>
      <tr>
        <td height="10" colspan="2" align="right">&nbsp;</td>
      </tr>
      <tr>
        <td height="10" colspan="2" align="right" background="../../template/GIF/lp.png">&nbsp;</td>
        </tr>
      <tr>
        <td height="50" align="right">&nbsp;</td>
        <td align="right"><a href="#" onclick="javascript:$('#form_insert').submit()" class="btn btn-success">Insert</a></td>
      </tr>
    </table>
    </form>
        
        <?
	}
	
	function updateProdType()
	{
		
		$query="UPDATE tipuri_produse 
		           SET prod=?, 
				       work_hours=?, 
					   prod_1=?, 
					   prod_1_qty=?, 
					   prod_2=?,
					   prod_2_qty=?,
					   prod_3=?,
					   prod_3_qty=?,
					   prod_4=?,
					   prod_4_qty=?,
					   prod_5=?,
					   prod_5_qty=?,
					   prod_6=?,
					   prod_6_qty=?,
					   prod_7=?,
					   prod_7_qty=?,
					   prod_8=?,
					   prod_8_qty=?,
			           unitate=?,
					   name=?,
					   capacity=?,
					   produced_by=?,
					   damage=?,
					   expires=?
				 WHERE ID=?"; 
				
		$this->kern->execute($query, 
		                     "sdsdsdsdsdsdsdsdsdssisiii", 
							 $_REQUEST['txt_prod'], 
							 $_REQUEST['txt_hours'], 
							 $_REQUEST['dd_raw_1'], $_REQUEST['txt_raw_1_qty'],
							 $_REQUEST['dd_raw_2'], $_REQUEST['txt_raw_2_qty'],
							 $_REQUEST['dd_raw_3'], $_REQUEST['txt_raw_3_qty'],
							 $_REQUEST['dd_raw_4'], $_REQUEST['txt_raw_4_qty'],
							 $_REQUEST['dd_raw_5'], $_REQUEST['txt_raw_5_qty'],
							 $_REQUEST['dd_raw_6'], $_REQUEST['txt_raw_6_qty'],
							 $_REQUEST['dd_raw_7'], $_REQUEST['txt_raw_7_qty'],
							 $_REQUEST['dd_raw_8'], $_REQUEST['txt_raw_8_qty'],
							 $_REQUEST['dd_unitate'],
							 $_REQUEST['txt_name'],
							 $_REQUEST['txt_cap'],
							 $_REQUEST['dd_produced'],
							 $_REQUEST['txt_damage'],
							 $_REQUEST['txt_expires'],
							 $_REQUEST['ID']);
							 
	   // Update prod energy
	   $this->updateProdEnergy($_REQUEST['txt_prod']);
	   
	   // Show prods
	   $this->showProds("");
	}
	
	function insertProdType()
	{
		
		$query="INSERT INTO tipuri_produse 
		           SET prod=?, 
				       work_hours=?, 
					   prod_1=?, 
					   prod_1_qty=?, 
					   prod_2=?,
					   prod_2_qty=?,
					   prod_3=?,
					   prod_3_qty=?,
					   prod_4=?,
					   prod_4_qty=?,
					   prod_5=?,
					   prod_5_qty=?,
					   prod_6=?,
					   prod_6_qty=?,
					   prod_7=?,
					   prod_7_qty=?,
					   prod_8=?,
					   prod_8_qty=?,
			           unitate=?,
					   name=?,
					   capacity=?,
					   produced_by=?, 
					   damage=?";
					   
		$this->kern->execute($query, 
		                     "sdsdsdsdsdsdsdsdsdssisi", 
							 $_REQUEST['txt_prod'], 
							 $_REQUEST['txt_hours'], 
							 $_REQUEST['dd_raw_1'], $_REQUEST['txt_raw_1_qty'],
							 $_REQUEST['dd_raw_2'], $_REQUEST['txt_raw_2_qty'],
							 $_REQUEST['dd_raw_3'], $_REQUEST['txt_raw_3_qty'],
							 $_REQUEST['dd_raw_4'], $_REQUEST['txt_raw_4_qty'],
							 $_REQUEST['dd_raw_5'], $_REQUEST['txt_raw_5_qty'],
							 $_REQUEST['dd_raw_6'], $_REQUEST['txt_raw_6_qty'],
							 $_REQUEST['dd_raw_7'], $_REQUEST['txt_raw_7_qty'],
							 $_REQUEST['dd_raw_8'], $_REQUEST['txt_raw_8_qty'],
							 $_REQUEST['dd_unitate'],
							 $_REQUEST['txt_name'],
							 $_REQUEST['txt_cap'],
							 $_REQUEST['dd_produced'],
							 $_REQUEST['txt_damage']);
							 
		// Update prod energy
		$this->updateProdEnergy($_REQUEST['txt_prod']);
	    
		// Show prods				 
		$this->showProds("");
		
	}
}
?>