<?php
class CMarketsTypes
{
	function CMarketsTypes($db, $utils)
	{
		$this->kern=$db;
		$this->utils=$utils;
	}
	
	function showNewBut($link, $txt)
	{
		?>
        
           <table width="700" border="0" cellspacing="0" cellpadding="0">
           <tr>
           <td align="right"><a href="#" class="btn btn-success" onclick="$('#add_modal').modal()">New Market</a></td>
           </tr>
           </table>
           <br><br>
        
        <?php
	}
	
	function showMarkets($search="")
	{
		$query="SELECT * FROM v_mkts";
		if ($search!="") $query=$query." WHERE title LIKE '%".$search."%' OR symbol LIKE '%".$search."%'";
		$query=$query." ORDER BY title ASC"; 
		
		$result=$this->kern->execute($query);	
	   
		?>
        
<table width="600" border="0" align="center" cellpadding="0" cellspacing="0" class="table table-hover table-striped" style="width:600px">
           
           <?php
		      while ( $row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                <tr>
                <td width="498"><?php print $row['title']; ?><br><span class='simple_gri_10'><?php print $row['symbol']; ?></span></td>
                <td width="77" align="center">
                <a href="markets_types.php?act=edit&ID=<?php print $row['ID']; ?>" class="btn btn-success" style="width:60px">Edit</a></td>
                </tr>
           
           <?php
			  }
		   ?>
           
           </table>
        
        <?php
	}
	
	function editProd($ID)
	{
		$query="SELECT * FROM v_mkts WHERE ID='".$ID."'";
		$result=$this->kern->execute($query);	
	    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
		?>
        
        <form id="form_update" name="form_update" action="markets_types.php?act=update&ID=<?php print $ID; ?>" method="post">
        <table width="600" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="145" height="50" align="right">Title&nbsp;&nbsp;</td>
        <td width="455" align="left"><input type="text" name="txt_title" id="txt_title" class="form-control" value="<?php print $row['title']; ?>"/></td>
      </tr>
      <tr>
        <td height="50" align="right">Description&nbsp;&nbsp;</td>
        <td align="left">
        <textarea id="txt_desc" name="txt_desc" class="form-control"><?php print $row['description']; ?>
        </textarea>
        </td>
      </tr>
      <tr>
        <td height="50" align="right">Symbol&nbsp;&nbsp;</td>
        <td align="left">
		<input type="text" name="txt_symbol" id="txt_symbol" class="form-control" value="<?php print $row['symbol']; ?>"/>
        </td>
      </tr>
      <tr>
        <td height="50" align="right">Allow Buyers&nbsp;&nbsp;</td>
        <td align="left">
        <select class="form-control" id="dd_allow_buyers" name="dd_allow_buyers">
        <option value="Y" <?php if ($row['allow_buyers']=="Y") print "selected"; ?>>Yes</option>
        <option value="N" <?php if ($row['allow_buyers']=="N") print "selected"; ?>>No</option>
        </select>
        </td>
      </tr>
      <tr>
        <td height="50" align="right">Allow Sellers&nbsp;&nbsp;</td>
        <td align="left">
		 <select class="form-control" id="dd_allow_sellers" name="dd_allow_sellers">
         <option value="Y" <?php if ($row['allow_sellers']=="Y") print "selected"; ?>>Yes</option>
         <option value="N" <?php if ($row['allow_sellers']=="N") print "selected"; ?>>No</option>
         </select>
        </td>
      </tr>
      <tr>
        <td height="50" align="right">Min Qty&nbsp;&nbsp;</td>
        <td align="left">
        <input type="text" name="txt_min_qty" id="txt_min_qty" class="form-control" value="<?php print $row['min_qty']; ?>"/>
        </td>
      </tr>
      <tr>
        <td height="50" align="right">Decimals&nbsp;&nbsp;</td>
        <td align="left">
		<input type="text" name="txt_decimals" id="txt_decimals" class="form-control" value="<?php print $row['decimals']; ?>" style="width:100px"/>
        </td>
      </tr>
      <tr>
        <td height="50" align="right">Symbol Type&nbsp;&nbsp;</td>
        <td align="left">
        
        <select class="form-control" id="dd_symbol_type" name="dd_symbol_type">>
         <option value="ID_PROD" <?php if ($row['symbol_type']=="ID_PROD") print "selected"; ?>>Product</option>
         <option value="ID_SHARES" <?php if ($row['symbol_type']=="ID_SHARES") print "selected"; ?>>Shares</option>
         </select>
        </td>
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
        
        <?php
	}
	
	
	function showAddModal()
	{
		// Modal
		$this->utils->showModalHeader("add_modal", "New Market", "act", "new");
		
		?>
            
             
              <table width="550" border="0" cellspacing="0" cellpadding="5">
              <tr>
            <td width="31%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/add.png" width="126" height="123" /></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Add Licence</td>
              </tr>
            </table>
            <br /><br /></td>
            <td width="69%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td width="30%" height="45" align="right" valign="middle" class="bold_gri_14">Title&nbsp;&nbsp;</td>
                <td width="70%" height="40" align="left" valign="middle" id="td_prod">
				<input type="text" name="txt_title" id="txt_title" class="form-control" />
                </td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Description&nbsp;&nbsp;</span></td>
                <td>
                
                <textarea id="txt_desc" name="txt_desc" class="form-control"></textarea>
                
                </td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Symbol&nbsp;&nbsp;</span></td>
                <td><input class="form-control" id="txt_symbol" name="txt_symbol" placeholder="0"/></td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Allow Buyers&nbsp;</span></td>
                <td>
                 <select class="form-control" id="dd_allow_buyers" name="dd_allow_buyers" style="width:100px">
                 <option value="Y">Yes</option>
                 <option value="N">No</option>
                 </select>
                </td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Alloow Sellers&nbsp;&nbsp;</span></td>
                <td>
                <select class="form-control" id="dd_allow_sellers" name="dd_allow_sellers" style="width:100px">
                <option value="Y">Yes</option>
                <option value="N">No</option>
                </select>
                </td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Min Qty&nbsp;&nbsp;</span></td>
                <td> 
                <input type="text" name="txt_min_qty" id="txt_min_qty" class="form-control" style="width:100px">
                </td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Decimals&nbsp;&nbsp;</span></td>
                <td> 
                <input type="text" name="txt_decimals" id="txt_decimals" class="form-control" style="width:100px">
                </td>
              </tr>
              <tr>
                <td height="45" align="right"><span class="bold_gri_14">Symbol Type&nbsp;&nbsp;</span></td>
                <td>
                
                <select class="form-control" id="dd_symbol_type" name="dd_symbol_type">>
                <option value="ID_PROD">Product</option>
                <option value="ID_SHARES">Shares</option>
                </select>
                
                </td>
              </tr>
              <tr>
                <td height="0" align="right">&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              </table>
            
            </td>
          </tr>
        </table>
           
        <?php
		
		$this->utils->showModalFooter("Cancel", "Add");
	}
	
	function update()
	{
		$query="UPDATE v_mkts 
		           SET title='".$_REQUEST['txt_title']."', 
				       description='".$_REQUEST['txt_desc']."', 
					   symbol='".$_REQUEST['txt_symbol']."', 
					   allow_buyers='".$_REQUEST['dd_allow_buyers']."', 
					   allow_sellers='".$_REQUEST['dd_allow_sellers']."', 
					   min_qty='".$_REQUEST['txt_min_qty']."', 
					   decimals='".$_REQUEST['txt_decimals']."', 
					   symbol_type='".$_REQUEST['dd_symbol_type']."' 
				 WHERE ID='".$_REQUEST['ID']."'"; 
		$this->kern->execute($query);
		
		$this->editProd($_REQUEST['ID']);
	}
	
	function newMarket()
	{
		$query="INSERT INTO v_mkts 
		           SET title='".$_REQUEST['txt_title']."', 
				       description='".$_REQUEST['txt_desc']."', 
					   symbol='".$_REQUEST['txt_symbol']."', 
					   allow_buyers='".$_REQUEST['dd_allow_buyers']."', 
					   allow_sellers='".$_REQUEST['dd_allow_sellers']."', 
					   min_qty='".$_REQUEST['txt_min_qty']."', 
					   decimals='".$_REQUEST['txt_decimals']."', 
					   symbol_type='".$_REQUEST['dd_symbol_type']."'";
		$this->kern->execute($query);
		//print $query;
		$this->showMarkets();
	}
	
	
	
}
?>