<?
class CAdmin
{
	function CAdmin($db, $acc, $template)
	{
	   	$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function showPanel()
	{
		// Query
		$query="SELECT * 
			      FROM companies 
			     WHERE comID=?";
		
		// Result 
	    $result=$this->kern->execute($query, 
		                             "i", 
									 $_REQUEST['ID']);	
			
		// Num rows
		if (mysqli_num_rows($result)>0)
	       $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		else
		   return false;
	
		
		?>
            
            <div id="div_basic" name="div_basic">
            <form action="admin.php?act=update_com&ID=<? print $_REQUEST['ID']; ?>" method="post" name="form_update" id="form_update">
            <table width="560" border="0" cellspacing="0" cellpadding="0">
            <tr><td valign="top">
            
            <?
			   if ($row['pic']=="")
			   {
			?>
                    
                    <table><tr><td>
                    <td width="212" height="207" align="center" valign="top" background="GIF/blank_pic.png" style="cursor:pointer" onclick="$('#modal_upload').modal()">
                    <table width="90%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                    <td height="175" align="center">&nbsp;</td>
                    </tr>
                    <tr>
                    <td align="center" class="font_12" style="color:#ffffff">Click to Upload</td>
                    </tr>
                    </table>
                    </td>
                    </td></tr></table>
            
            <?
			   }
			   else
			   {
			?>
            
                    <td width="212" height="207" align="center" valign="top">
                    <img src="../../../uploads/<? print $row['pic']; ?>" width="200" height="200" title="Click to View" data-toggle="tooltip" data-placement="top" style="cursor:pointer" onclick="$('#src_pic_modal').attr('src', '../../../uploads/<? print $row['pic']; ?>'); $('#txt_pic_id_1').val('<? print $row['pic']; ?>'); $('#pic_modal').modal()" />
                    </td>
            
            <?
			   }
			?>
            
            </td>
            <td width="348" align="center"><table width="97%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="205" align="center" valign="top" bgcolor="#fafafa">
                <table width="95%" border="0" cellspacing="0" cellpadding="0">
                  <tr class="font_14">
                    <td width="70%" height="35" align="left" valign="middle">Name</td>
                    <td width="30%" height="35" align="left" valign="middle">Symbol</td>
                  </tr>
                  <tr>
                    <td align="left">
                    <input type="text" name="txt_name" id="txt_name" style="width:200px" class="form-control" value="<? print base64_decode($row['name']); ?>"/></td>
                    <td align="left">
                    <input type="text" name="txt_symbol" id="txt_symbol" style="width:90px" class="form-control" disabled="disabled" value="<? print $row['symbol']; ?>" />
                    </td>
                  </tr>
                  <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="30" valign="top" class="font_14">Short Description</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td height="60" colspan="2" align="left" valign="top">
                    <textarea name="txt_desc" id="txt_desc" cols="45" rows="3" class="form-control" style="width:320px"><? print base64_decode($row['description']); ?></textarea></td>
                    </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table>
        <table width="540" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="50" align="right" valign="bottom"><a href="#" class="btn btn-primary" onClick="javascript:$('#form_update').submit()">Update</a></td>
          </tr>
        </table>
        </form>
        </div>
        
        <?
	}
	
	function showPicModal()
	{
		?>
            
             <div class="modal fade" id="pic_modal">
           <div class="modal-dialog">
           <div class="modal-content">
           <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
           <h4 class="modal-title">Delete Pic</h4>
           </div>
           <form method="post" action="admin.php?act=del_pic&ID=<? print $_REQUEST['ID']; ?>"  id="form_del_pic" name="form_del_pic">
           <input id="txt_pic_id_1" name="txt_pic_id_1" type="hidden" value=""/>
           </form>
           
           <form method="post" action="main.php?act=set_profile" id="form_set_profile" name="form_set_profile">
           <input id="txt_pic_id_2" name="txt_pic_id_2" type="hidden" value=""/>
           </form>
           <div class="modal-body">
           
           <table width="550" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><img src="../../template/GIF/camera.jpg" width="550" height="550" id="src_pic_modal" name="src_pic_modal"/></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          </table>
        
        </div>
             <div class="modal-footer">
             <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
             
             <a type="button" class="btn btn-primary btn-danger" id="but_del_pic" style="width:120px" onclick="$('#form_del_pic').submit();">
             <span class="glyphicon glyphicon-remove"></span>&nbsp;&nbsp;Delete Pic
             </a>
             
             </div>
            
             </div></div></div>
        
        <?
	}
	
	function processUpload($ID)
	{
		// Rights
		$query="SELECT * 
		          FROM companies 
				 WHERE ID='".$ID."' 
				   AND ownerID='".$_REQUEST['ud']['ID']."'";
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Only company's owner can execute this operation.");
		    return false;
		}
		
		$res=$this->template->processUpload($owner_type, $ownerID, 1);
		
		if ($res!=false)
		{
			$query="UPDATE companies 
			           SET pic='".$res."'
				     WHERE ID='".$ID."'";
			$this->kern->execute($query);	
		}
	}
	
	function delPic($pic)
	{
	  // Rights
		$query="SELECT * 
		          FROM companies 
				 WHERE ID='".$_REQUEST['ID']."' 
				   AND ownerID='".$_REQUEST['ud']['ID']."'";
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Only company's owner can execute this operation.");
		    return false;
		}
		
	  $query="SELECT * FROM companies WHERE ID='".$_REQUEST['ID']."'";
	  $result=$this->kern->execute($query);	
	  $row = mysqli_fetch_array($result, MYSQLI_ASSOC); 
	   
	  // Delete file
	  unlink("../../../uploads/".$row['pic']);
			  
	  // Update db
	  $query="UPDATE companies 
			     SET pic='' 
			   WHERE ID='".$_REQUEST['ID']."'"; 
	  $this->kern->execute($query);	
	}
	
	function updateProfile($ID, $name, $desc)
	{
		// Logged in
		if ($this->kern->isLoggedIn()==false)
		{
			$this->template->showErr("You need to login to execute this operation.");
		    return false;
		}
		
		// ID
		if ($this->kern->isInt($ID)==false)
		{
			$this->template->showErr("Invalid entry data");
		    return false;
		}
		
		// Rights
		$query="SELECT * 
		          FROM companies 
				 WHERE ID='".$ID."' 
				   AND ownerID='".$_REQUEST['ud']['ID']."'";
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)==0)
		{
			$this->template->showErr("Only company's owner can execute this operation.");
		    return false;
		}
		
		// Name
		if (strlen($name)<5 || strlen($name)>30)
		{
			$this->template->showErr("Company name is a string 5-30 characters long");
		    return false;
		}
		
		if (preg_match('/[^A-Za-z0-9\s.-]/', $name)) 
		{
			$this->template->showErr("Company name is a string containing only letters and numbers");
		    return false;
		}
		
		$query="SELECT * 
		          FROM companies 
				 WHERE name='".$name."'
				   AND ID<>'".$ID."'";
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)>0)
		{
			$this->template->showErr("Name (".$name.") already exist.");
		    return false;
		}
		
		// Description
		if (strlen($name)>250)
		{
			$this->template->showErr("Company description is a string 5-30 characters long");
		    return false;
		}
		
		if (preg_match('/[^A-Za-z0-9\s.-]/', $name)) 
		{
			$this->template->showErr("Company description is a string containing only letters and numbers");
		    return false;
		}
		
		try
	    {
		   // Begin
		   $this->kern->begin();
		   
		   // Track ID
		   $tID=$this->kern->getTrackID();

           // Action
           $this->kern->newAct($act, $tID);
		   
		   // Update
		   $query="UPDATE companies 
		              SET name='".$name."', 
					      description='".$desc."' 
					WHERE ID='".$ID."'";
		   $this->kern->execute($query);	
		   
		   // Commit
		   $this->kern->commit();

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
	
	function showCasinoSelector($casinoID)
	{
		?>
             
             <div id="div_selector" name="div_selector">
             <table width="550" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="50" align="center" bgcolor="#e9f0f8">
                  <select id="dd_period" name="dd_period" class="form-control" style="width:530px"/>
                  <option value="24">Last 24 Hours</option>
                  <option value="48">Last 48 Hours</option>
                  <option value="72">Last 72 Hours</option>
                  <option value="168">Last Week</option>
                  <option value="720">Last Month</option>
                  </select>
                  </td>
                </tr>
                <tr>
                  <td height="20" align="center" valign="top"><img src="GIF/red_arrow.png" width="20" height="15" alt=""/></td>
                </tr>
              </tbody>
            </table>
            <br />
            </div>
        
        <?
	}
	
	function showCasinoReports($casinoID, $hours=24)
	{
		// Total bets
		$query="SELECT COUNT(*) AS bets
		          FROM table_bets 
				 WHERE casinoID='".$casinoID."' 
				   AND tstamp>".(time()-($hours*3600)); 
	     $result=$this->kern->execute($query);	
	     $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 $total_bets=$row['bets'];
		 
		 // Winnings
		 $query="SELECT SUM(won) AS total
		           FROM table_bets 
				  WHERE casinoID=".$casinoID." 
				    AND won>0 
				    AND tstamp>".(time()-($hours*3600)); 
	     $result=$this->kern->execute($query);	
	     $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 $win=round($row['total'], 4);
		 
		 // Losses
		 $query="SELECT SUM(won) AS total
		           FROM table_bets 
				  WHERE casinoID=".$casinoID." 
				    AND won<0 
				    AND tstamp>".(time()-($hours*3600)); 
	     $result=$this->kern->execute($query);	
	     $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		 $loss=abs(round($row['total'], 4));
		 
		 // Net
		 $net=abs($loss-$win);
		 
		?>
            
            <div id="div_reports" name="div_reports">
            <table width="550" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td><table width="130" border="0" cellspacing="1" cellpadding="0">
                    <tbody>
                      <tr>
                        <td height="30" align="center" bgcolor="#e9f0f8" class="inset_blue_inchis_12">Total Bets</td>
                      </tr>
                      <tr>
                        <td height="70" align="center" bgcolor="#f1f8ff" class="inset_blue_inchis_30"><? print $total_bets; ?></td>
                      </tr>
                    </tbody>
                  </table></td>
                  <td align="center"><table width="130" border="0" cellspacing="1" cellpadding="0">
                    <tbody>
                      <tr>
                        <td height="30" align="center" bgcolor="#e9f0f8" class="inset_blue_inchis_12">Players Winnings</td>
                      </tr>
                      <tr>
                        <td height="70" align="center" bgcolor="#f1f8ff">
                        <strong>
                        <span class="simple_red_30"><? print $this->kern->splitNumber($win, 0); ?></span><span class="simple_red_14"><? print ".".$this->kern->splitNumber($win, 1); ?></span>
                        </strong>
                        </td>
                      </tr>
                    </tbody>
                  </table></td>
                  <td align="center"><table width="130" border="0" cellspacing="1" cellpadding="0">
                    <tbody>
                      <tr>
                        <td height="30" align="center" bgcolor="#e9f0f8" class="inset_blue_inchis_12">Players Losses</td>
                      </tr>
                      <tr>
                        <td height="70" align="center" bgcolor="#f1f8ff">
                         <strong>
                           <span class="simple_green_30"><? print $this->kern->splitNumber($loss, 0); ?></span><span class="simple_green_14"><? print ".".$this->kern->splitNumber($loss, 1); ?></span>
                         </strong>
                        </td>
                      </tr>
                    </tbody>
                  </table></td>
                  <td align="right"><table width="130" border="0" cellspacing="1" cellpadding="0">
                    <tbody>
                      <tr>
                        <td height="30" align="center" bgcolor="#e9f0f8" class="inset_blue_inchis_12">Net Profit</td>
                      </tr>
                      <tr>
                        <td height="70" align="center" bgcolor="#f1f8ff">
                        <strong>
                        <span class="<? if ($net<0) print "simple_green_30"; else print "simple_red_30"; ?>"><? if ($net>0) print "-"; ?><? print $this->kern->splitNumber($net, 0); ?></span><span class="<? if ($net<0) print "simple_green_14"; else print "simple_red_14"; ?>"><?  print ".".$this->kern->splitNumber($net, 1); ?></span>
                        </strong>
                        </td>
                      </tr>
                    </tbody>
                  </table></td>
                </tr>
              </tbody>
          </table>
          </div>
        
        <?
	}
	
	function showCasinoTables($casinoID)
	{
		?>
            
<div id="div_tables" name="div_table">    
            <br><br>
  <table width="560" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="2%"><img src="../../template/GIF/menu_bar_left.png" width="14" height="48" /></td>
      <td width="95%" align="center" background="../../template/GIF/menu_bar_middle.png"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="63%" class="bold_shadow_white_14">Table</td>
            <td width="3%"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
            <td width="17%" align="center" class="bold_shadow_white_14">Time</td>
            <td width="3%" align="center"><img src="../../template/GIF/menu_bar_sep.png" width="15" height="48" /></td>
            <td width="14%" align="center" class="bold_shadow_white_14">Amount</td>
          </tr>
      </table></td>
      <td width="3%"><img src="../../template/GIF/menu_bar_right.png" width="14" height="48" /></td>
    </tr>
  </table>
  <table width="540" border="0" cellspacing="0" cellpadding="5">
    <tr>
            <td width="64%" class="font_14">tester1 paid the fee to open a company</td>
            <td width="21%" align="center" class="font_14">5 minutes</td>
            <td width="15%" align="center" class="bold_verde_14">+$3.21</td>
          </tr>
          <tr>
            <td colspan="3" ><hr></td>
            </tr>
          </table> 
          </div>
        
        <?
	}
	
	function showTablesPage($ID)
	{
		 print "<div id='div_casino_page' name='div_casino_page' style='display:none'><br>";
		 $this->showCasinoSelector($ID);
		 $this->showCasinoReports($ID);
		 $this->showCasinoTables($ID);
		 print "</div>";
	}
	
}
?>