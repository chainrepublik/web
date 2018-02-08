<?
class CTaxes
{
	function CTaxes($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function change($tax, $new_val, $expl)
	{
		 // Decode
		 $expl=base64_decode($expl);
		 $expl=str_replace("<", "", $expl);
		 $expl=str_replace(">", "", $expl);
		 
		 // Energy and equity
		 if ($_REQUEST['ud']['energy']<5 || 
		    $_REQUEST['ud']['equity']<5)
		 {
			 $this->template->showErr("Only players having the energy and equity over 5 can propose new laws.");
		     return false;
		 }
		  
		 // Already votin
		$query="SELECT * 
		          FROM laws 
				 WHERE tax='".$tax."' 
				   AND status='ID_VOTING'";
		$result=$this->kern->execute($query);	
	    
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("Another change of this tax is already voting");
		    return false;
		}
		
		
		 // Entry data
		if ($this->kern->isInt($new_val, "decimal")==false || $itemID<0)
		{
			$this->template->showErr("Invalid entry data.");
		    return false;
		}
		
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// Explanation
		if (strlen($expl)<25 || strlen($expl)>250)
		{
			$this->template->showErr("Invalid explanation length (25-250 characters).");
		    return false;
		}
		
		// Check tax
		$query="SELECT * 
		          FROM taxes 
				 WHERE tax='".$tax."'";
		$result=$this->kern->execute($query);	
	    if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		// Tax data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    
		// Last change
		if (time()-$row['last_change']<864000)
		{
			$this->template->showErr("Taxex / bonuses can be changed once every 10 days.");
		    return false;
		}
		
		 if ($row['value']>$new_val)
		 {
			 // Budget under 1000 or negative cashflow ?
		     $query="SELECT * 
		          FROM bank_acc 
				 WHERE owner_type='ID_BUG' 
				   AND ownerID=0 
				   AND moneda='GOLD'";
		     $result2=$this->kern->execute($query);
		     $row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC);
		 
		     if ($_REQUEST['sd']['budget_net_24h']<0 || 
		         $row2['balance']<1000)
		     {
			    $this->template->showErr("You can't propose tax reductions while the budget's balance is less than $100 or the 24 hours net result is negative.");
		        return false;
		      }
		 }
		 
	    // Max value
		if ($new_val<$row['min'])
		{
			$this->template->showErr("Minimum allowed value is ".$row['min']);
		    return false;
		}
		
		
		// Only one law / day
		$query="SELECT * 
		          FROM laws 
				 WHERE userID='".$_REQUEST['ud']['ID']."' 
				   AND tstamp>".(time()-86400);
		$result=$this->kern->execute($query);	
	    if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("You can propose one law every 24 hours");
		    return false;
		}
		
		// Rejected law in the last 5 days
		$query="SELECT * 
		          FROM laws 
				 WHERE tstamp>".(time()-432000)." 
				   AND userID='".$_REQUEST['ud']['ID']."' 
				   AND status='ID_REJECTED'";
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)>0)
		{
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			$this->template->showErr("One of your proposed laws was rejected in the last 5 days. You can propose new laws in ".round((time()-$row['tstamp'])/86400));
		    return false;
		}
		
		// Changed in the last 10 days
		$query="SELECT * 
		          FROM laws 
				 WHERE tax='".$tax."' 
				   AND last_change>".(time()-432000);
		$result=$this->kern->execute($query);	
	    
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("A tax can be changed once every 5 days");
		    return false;
		}
	
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();
		   
		   // Action 
		   $this->kern->newAct("Propose a tax change ");
		   
		   // Insert tax
		   $query="INSERT INTO laws 
		                   SET userID='".$_REQUEST['ud']['ID']."', 
						       tax='".$tax."',
							   new_val='".$new_val."',
							   expl='".base64_encode($expl)."', 
							   type='ID_TAX_CHANGE', 
							   status='ID_VOTING', 
							   tstamp='".time()."', 
							   tID='".$tID."'"; 
		   $this->kern->execute($query);	
		   
		     // Last change
		   $query="UPDATE taxes 
		              SET last_change='".time()."' 
					WHERE tax='".$tax."'";
		   $this->kern->execute($query);	
		   
		   // Unread laws
		   $query="UPDATE web_users 
		              SET unread_laws=unread_laws+1 
					WHERE energy>1 
					  AND equity>1";
		   $this->kern->execute($query);	
			
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->showOk("You have succesfully proposed a new law");

		   return true;
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error.");

		  return false;
	   }
	}
	
	function showChangeTaxModal()
	{
		// Modal
		$this->template->showModalHeader("change_modal", "Change Tax", "act", "change", "tax", "");
		?>
            
          <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="GIF/bonus.png" width="180" height="160" alt=""/></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">Change Tax</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14">New Value</td>
              </tr>
              <tr>
                <td><input class="form-control" placeholder="Subject (5-50 characters)" id="txt_val" name="txt_val" value="" style="width:60px"/></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">Explain your proposal</td>
              </tr>
              <tr>
                <td><textarea class="form-control" rows="5" id="txt_mes" name="txt_mes" placeholder="Explain your proposal in english (20-250 characters)"><? print $mes; ?></textarea></td>
              </tr>
            </table>
            
            </td>
          </tr>
        </table>
        
<script>
		   function format()
		   {
			   $('#txt_mes').val(window.btoa($('#txt_mes').val()));
		   }
         </script>
           
        <?
		$this->template->showModalFooter("Cancel", "Send");
	}
	
	function showTaxes($categ="ID_TRANSFER", $prod_section="ID_ENERGY")
	{
		$query="SELECT * 
		          FROM taxes 
				 WHERE categ='".$categ."' 
				   AND prod_section='".$prod_section."'"; 
		 $result=$this->kern->execute($query);	
	  
	  
		?>
        
           <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tbody>
              <tr>
                <td height="40" align="left" class="simple_blue_18"><strong>
                <?
				   switch ($categ)
				   {
					   case "ID_WORK" : print "Work Taxes"; break;
					   case "ID_TRANSFER" : print "Transfer Taxes"; break;
				   }
				   
				   switch ($prod_section)
				   {
					   case "ID_WORK" : print "Salary"; break;
					   case "ID_ENERGY" : print " - Energy products"; break;
				   }
				?>
                </strong></td>
              </tr>
            </tbody>
          </table>
<table width="560" border="0" cellspacing="0" cellpadding="0">
  <tr>
            <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
            <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="63%" class="bold_shadow_white_14">Tax</td>
                <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="14%" align="center" class="bold_shadow_white_14">Amount</td>
                <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
                <td width="17%" align="center" class="bold_shadow_white_14">Change</td>
              </tr>
            </table></td>
            <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
          </tr>
        </table>
       
        <table width="540" border="0" cellspacing="0" cellpadding="5">
          
          <?
		     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			 {
		  ?>
            
              <tr>
              <td width="64%" class="font_14"><? print $row['title']; ?>
              <br /><span class="simple_blue_10"><? print $row['description']; ?></span>
              </td>
              <td width="17%" align="center" class="bold_verde_14"><? if ($row['val_type']=="ID_FIXED") print "".$row['value']; else print $row['value']."%"; ?></td>
              <td width="19%" align="center" class="bold_verde_14">
              <a class="btn btn-primary" style="width:100px" href="#" onclick="$('#change_modal').modal(); 
                                                                         $('#txt_val').val('<? print $row['value']; ?>');
                                                                         $('#tax').val('<? print $row['tax']; ?>');">Change</a>
              </td>
              </tr>
              <tr>
              <td colspan="3" ><hr></td>
              
           <?
			 }
		   ?>
           
          </tr>
       
        </table>
        <br><br>
        
        <?
	}
}
?>