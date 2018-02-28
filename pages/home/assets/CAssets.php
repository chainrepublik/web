<?
class CAssets
{
	function CAssets($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	
	function newAsset($name, 
					  $desc, 
					  $how_buy, 
					  $how_sell, 
					  $website, 
					  $pic, 
					  $symbol, 
					  $initial_qty, 
					  $trans_fee, 
					  $days)
	{
		 // Trans fee
		 $trans_fee=round($trans_fee, 2);
		
		 // Net fee
		 $fee=round(($initial_qty*0.0001)+(0.0001*$days), 4);
		
		// Trans fee
		if ($trans_fee>1) 
			$fee=round($fee*$trans_fee, 4); 
		
		 // Basic check
		 if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
	                                 $_REQUEST['ud']['adr'],
						             $fee, 
						             $this->template,
					      	         $this->acc)==false)
		 return false;	 
			 
		 // Name
		 if (strlen($name)<3 || strlen($name)>50)
		 {
			 $this->template->showErr("Invalid name length (5-50 characters)");
			 return false;
		 }
		 
		 // Description
		 if (strlen($desc)>1000)
		 {
			 $this->template->showErr("Invalid description length (5-1000 characters)");
			 return false;
		 }
		 
		 // How to buy
		 if (strlen($how_buy)>1000)
		 {
			 $this->template->showErr("Invalid how to buy length (5-1000 characters)");
			 return false;
		 }
		 
		 // How to sell
		 if (strlen($how_sell)>1000)
		 {
			 $this->template->showErr("Invalid how to sell length (5-1000 characters)");
			 return false;
		 }
		 
		 // Website
		 if ($website!="")
		 {
			if (strpos($website, "http")===false) $website="http://".$website;
			
		    if (filter_var($website, FILTER_VALIDATE_URL)==false)
		   {
			   $this->template->showErr("Invalid website link");
			   return false;
		   }
		 }
		 
		 // Pic
		 if ($pic!="")
		 {
			 if (strpos($pic, "http")===false) $pic="http://".$pic;
			 
		     if (filter_var($pic, FILTER_VALIDATE_URL) ==false)
		     {
			    $this->template->showErr("Invalid pic");
			    return false;
		     }
		 }
		 
		 // Symbol
		 $symbol=strtoupper($symbol);
		
		 if ($this->kern->isSymbol($symbol, 6)==false)
		 {
			 $this->template->showErr("Invalid symbol");
			 return false;
		 }
		 
		 // Symbol already exist ?
		 $query="SELECT * 
		           FROM assets 
				  WHERE symbol=?";
		
		$result=$this->kern->execute($query, 
									 "s", 
									 $symbol);
		
	     if (mysqli_num_rows($result)>0)
		 {
			 $this->template->showErr("Symbol already exist");
			 return false;
		 }
		 
		 // Initial qty
		 if ($initial_qty<1000)
		 {
			 $this->template->showErr("Minimum initial qty is 1000 units");
			 return false;
		 }
		 
		 if ($trans_fee>0)
		 {
		    // Transaction fee
		    if ($trans_fee>10)
			{
				$this->template->showErr("Maximum transaction fee is 10");
			    return false;
			}
		 }
		 
		 // Market days
		 if ($days<100)
		 {
			  $this->template->showErr("Minimum market days is 100");
			  return false;
		 }
		 
		 try
	     {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Issue a new asset");
		  	  
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								par_2=?,
								par_3=?,
								par_4=?,
								par_5=?,
								par_6=?,
								par_7=?,
								par_8=?,
								par_9=?,
								par_10=?,
								days=?,
								status=?, 
								tstamp=?"; 
			 
	       $this->kern->execute($query, 
								"issssssssssidsisi", 
								$_REQUEST['ud']['ID'], 
								"ID_ISSUE_ASSET", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$name, 
								$desc, 
								$how_buy, 
								$how_sell, 
								$website, 
								$pic, 
								$symbol, 
								$initial_qty, 
								$trans_fee, 
								$_REQUEST['ud']['adr'],
								$days, 
								"ID_PENDING", 
								time());
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->confirm();
	   }
	   catch (Exception $ex)
	   {
	      // Rollback
		  $this->kern->rollback();

		  // Mesaj
		  $this->template->showErr("Unexpected error - ".$ex->getMessage());

		  return false;
	   }
	}
	
	function showAssets($type="ID_USER")
	{
		if ($type=="ID_USER")
		{
		    $query="SELECT * 
		              FROM assets 
				     WHERE CHAR_LENGTH(symbol)=6
				  ORDER BY ID ASC
			         LIMIT 0,20"; 
			
			$result=$this->kern->execute($query);	
		}
		
		else if ($type=="ID_SHARES")
		{
		    $query="SELECT assets.*, 
		                   am.ask, 
					       am.bid 
		              FROM assets 
				      JOIN assets_mkts AS am ON assets.symbol=am.asset
				     WHERE CHAR_LENGTH(symbol)=5 
				       AND am.cur=?
				  ORDER BY am.bid DESC
			         LIMIT 0,20";
		   
			$result=$this->kern->execute($query, 
								    	 "s", 
									     "CRC");	
	    }
		
		else if ($type=="ID_ISSUED") 
		{
		    $query="SELECT assets.*, 
		                   am.ask, 
					       am.bid 
		              FROM assets 
				      JOIN assets_mkts AS am ON assets.symbol=am.asset
				     WHERE CHAR_LENGTH(symbol)=6 
				       AND assets.adr=?
				  ORDER BY am.bid DESC
			         LIMIT 0,20";
		
		     $result=$this->kern->execute($query, 
									      "s", 
									      $_REQUEST['ud']['adr']);	
		}
		
		 if ($type=="ID_SHARES")
		 $this->template->showTopBar("Name", "70%", 
									 "Ask", "10%", 
									 "Bid", "10%");
         ?>
                  
                  <br><br>
<table width="550px" border="0" cellspacing="0" cellpadding="0">
                      
                      <?
					     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						 {
					  ?>
                      
                            <tr>
                            <td width="3%"><img src="<? if ($row['pic']=="") print "../../template/GIF/asset.png"; else print $this->kern->crop($row['pic'], 50, 50); ?>"  class="img-circle"/></td>
                            <td width="2%">&nbsp;</td>
                            <td width="70%">
                            <span class="font_14"><a href="asset.php?symbol=<? print $row['symbol']; ?>">
								<? print "<strong>".$this->kern->noescape(base64_decode($row['title']))."</strong> (".$row['symbol'].")"; ?></a></span><br>
                            <span class="font_10"><? print "Issuer : ".$this->template->formatAdr($row['adr']); ?></span></td>
							
							 <?
							     if ($type=="ID_SHARES")
								 {
							 ?>
								
						      <td width="15%" class="font_14" align="center">
							  <? print $row['ask']."<br><span class='font_10'>CRC</span>"; ?></td>
					          <td width="15%" class="font_14" align="center">
							  <? print $row['bid']."<br><span class='font_10'>CRC</span>"; ?></td>
								
							<?
								 }
							?>
								
  </tr>
                            <tr>
                            <td colspan="4"><hr></td>
                            </tr>
                      
                      <?
	                      }
					  ?>
                        
                  </table>
                  
                 
        
        <?
	}
	
	function showIssuedAssets()
	{
		$query="SELECT * 
		          FROM assets 
				 WHERE linked_mktID=0 
				   AND adr=?
			  ORDER BY ID ASC
			     LIMIT 0,20";
		
		 $result=$this->kern->execute($query, 
									  "s", 
									  $_REQUEST['ud']['adr']);	
		 
		?>
        
          <table width="95%" border="0" cellspacing="0" cellpadding="0">
                      
                      <?
					     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						 {
					  ?>
                      
                            <tr>
                            <td width="4%"><img src="<? if ($row['pic']=="") print "../../template/template/GIF/empty_pic.png"; else print $this->kern->crop($row['pic'], 100, 100); ?>"  class="img-circle img-responsive"/></td>
                            <td width="1%">&nbsp;</td>
                            <td width="95%">
                            <span class="font_16"><a href="asset.php?symbol=<? print $row['symbol']; ?>">
							<? print $this->kern->noescape(base64_decode($row['title']))." (".$row['symbol'].")"; ?></a>
                            <p class="font_12"><? print $this->kern->noescape(substr(base64_decode($row['description']), 0, 250))."..."; ?></p></td>
                            </tr>
                            <tr>
                            <td colspan="4" background="../../template/template/GIF/lp.png">&nbsp;</td>
                            </tr>
                      
                      <?
	                      }
					  ?>
                        
</table>
                  
                 
        
        <?
	}
	
	
	
	
	
	function showIssueMoreModal()
	{
		$this->template->showModalHeader("issue_more_modal", "Issue More Assets", "act", "issue_more", "assetID", 0);
		?>
        
           <table width="700" border="0" cellspacing="0" cellpadding="0">
          <tr>
           <td width="130" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
             <tr>
               <td align="center"><img src="./GIF/issue_more.png" width="200" /></td>
             </tr>
             <tr><td>&nbsp;</td></tr>
             <tr>
               <td align="center">
				   <? 
		              $this->template->showReq(0.1, 0.01, "issue_more"); 
				   ?>
			   </td>
             </tr>
             <tr>
               <td align="center">&nbsp;</td>
             </tr>
            
           </table></td>
           <td width="400" align="center" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="0">
             <tbody>
               <tr>
			     <td height="30" align="left" class="font_14"><strong>Amount</strong></td>
               </tr>
               <tr>
                 <td align="left"><input id="txt_issue_more_amount" name="txt_issue_more_amount" class="form-control" value="100" style="width: 100px"></td>
               </tr>
             </tbody>
           </table></td>
         </tr>
     </table>
     
           <script>
		   $('#txt_issue_more_amount').keyup(function() { $('#req_issue_more_coins').text(parseFloat($('#txt_issue_more_amount').val()*0.0001).toFixed(4)); });
		   </script>
       
        <?
		$this->template->showModalFooter("Send");
		
	}
	
	function showTrustBut()
	{
		if (!$this->kern->trustAsset($_REQUEST['ud']['adr'], $_REQUEST['symbol']))
		{
		?>

<br>
            <table width="90%">
            <tr><td align="right">
            <a href="javascript:void(0)" onClick="$('#trust_modal').modal()" class="btn btn-primary"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;&nbsp;Trust Asset</a></td></tr>
            </table>
            

        <?
		}
	}
	
	function showPanel($symbol)
	{
		// QR modal
		$this->template->showQRModal();
		
		// Owners
		$query="SELECT COUNT(*) AS total
		          FROM assets_owners 
				 WHERE symbol=?";
		
		// Result
		$result=$this->kern->execute($query, 
									 "s", 
									 $symbol);	
	    
		// Load data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Owners
		$owners=$row['total'];
		
		// Trusted by
		$query="SELECT COUNT(*) AS total 
		          FROM adr_attr 
				 WHERE attr=? 
				   AND s1=?";
		
		// Result
	    $result=$this->kern->execute($query, 
									 "ss", 
									 "ID_TRUST_ASSET",
									 $symbol);	
		
		// Data
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Trusted
		$trusted=$row['total']; 
		if ($trusted=="") $trusted=0;
		
		// Asset Data
		$query="SELECT *
		          FROM assets 
				 WHERE symbol='".$symbol."'";
		
		// Result
	    $result=$this->kern->execute($query, 
									 "s", 
									 $symbol);	
	    
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		?>
        
            
            <br>
<div class="panel panel-default" style="width:90%">
            <div class="panel-body">
            <table width="100%">
            <tr>
            <td width="23%" valign="top"><img src="<? if ($row['pic']=="") print "../../template/template/GIF/empty_pic.png"; else print $this->kern->crop($row['pic'], 150, 150); ?>"  class="img-circle img-responsive"/></td>
            <td width="1%">&nbsp;</td>
            <td width="76%" valign="top"><span class="font_16"><strong><? print $this->kern->noescape(base64_decode($row['title'])); ?></strong></span>
            <p class="font_14"><? print $this->kern->noescape(base64_decode($row['description'])); ?></p></td>
            </tr>
            <tr><td colspan="3"><hr></td></tr>
            <tr><td colspan="3">
    
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-responsive">
             <tr>
            <td width="30%" align="center"><span class="font_12">Symbol&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print $row['symbol']; ?></strong></span></td>
            <td width="40%" class="font_12" align="center">Available&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print $row['qty']; ?> units</strong></td>
            <td width="30%" class="font_12" align="center">Transaction Fee&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print $row['trans_fee']."%"; ?></strong></td>
            </tr>
            <tr><td colspan="5"><hr></td></tr>
            <tr>
            <td width="30%" align="center"><span class="font_12">Address</span>&nbsp;&nbsp;&nbsp;&nbsp;<strong><a class="font_12" href="#"><? print $this->template->formatAdr($row['adr'], 12); ?></a></strong></td>
            <td width="40%" class="font_12" align="center">Trusted by&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print $trusted. " players"; ?></strong></td>
            <td width="30%" class="font_12" align="center">Expire&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print "~ ".$this->kern->timeFromBlock($row['expires']); ?></strong></td>
            </tr>
            <tr><td colspan="5"><hr></td></tr>
            <tr>
            <td width="30%" align="center"><span class="font_12">Fee</span>&nbsp;&nbsp;&nbsp;&nbsp;<strong><a class="font_12" href="#"><? print $this->template->formatAdr($row['trans_fee_adr'], 12); ?></a></strong></td>
            <td width="40%" class="font_12" align="center">Can Issue More&nbsp;&nbsp;&nbsp;YES<strong></strong></td>
            <td width="30%" class="font_12" align="center">Owners&nbsp;&nbsp;&nbsp;&nbsp;<strong><? print $owners; ?></strong></td>
            </tr>
            <tr><td colspan="5"><hr></td></tr>
            </table>
            
            <table>
            </table>
            
            </td></tr>
            </table>
            </div>
            </div>
            <br>
            
            <table width="90%">
            <tr>
            
            <td width="50%">
            <div class="panel panel-default">
            <div class="panel-heading">
            <h3 class="panel-title" class="font_14">How to buy <? print $row['symbol']; ?></h3>
            </div>
            <div class="panel-body font_12">
            
            <table width="90%">
            <tr>
            <td width="30%"><img src="GIF/buy.png" class="img img-responsive"></td>
            <td width="70%"><? print base64_decode($row['how_buy']); ?></td>
            </tr>
            </table>
            
            </div>
            </div>
            </td>
            
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            
            <td width="50%">
            <div class="panel panel-default">
            <div class="panel-heading">
            <h2 class="panel-title">How to sell / redeem <? print $row['symbol']; ?></h2>
            </div>
            <div class="panel-body font_14">
            
            <table width="90%">
            <tr>
            <td width="30%"><img src="GIF/redeem.png" class="img img-responsive"></td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td width="70%" class="font_12"><? print base64_decode($row['how_sell']); ?></td>
            </tr>
            </table>
            
            </div>
            </div>
            </td>
            
            </tr>
            </table>
            
        
        <?
	}
	
	
	function showOwners($symbol)
	{
		// Load asset data
		$query="SELECT * 
		          FROM assets 
				 WHERE symbol=?";
		
		// Execute
		$result=$this->kern->execute($query, 
									 "s", 
									 $symbol);	
		
		// Load data ?
	     $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		// Issued
		$qty=$row['qty'];
		
		$query="SELECT ao.*, cou.country 
		          FROM assets_owners AS ao 
				  JOIN adr ON adr.adr=ao.owner 
				  JOIN countries AS cou ON cou.code=adr.cou 
				 WHERE symbol=?
			  ORDER BY ao.qty DESC";
		
	    $result=$this->kern->execute($query, 
									 "s", 
									 $symbol);	
	    
		?>
                   <br>
                   <table width="90%" class="table-responsive">
                    
                    <?
					   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					   {
					?>
                    
                        <tr>
                        <td width="8%" align="left" class="font_14">
                        <img src="../../template/GIF/empty_pic.png"  class="img-circle" width="50" /></td>
                        <td width="2%">&nbsp;</td>
                        <td width="90%" align="left" class="font_14">
                        <a href="#" class="font_14">
                        <strong><? print $this->template->formatAdr($row['owner']); ?></strong>
                        </a>
                        <p class="font_10"><? print "Citizenship ".ucfirst(strtolower($row['country'])); ?></p>
                        </td>
                        
                        <td width="11%" align="center" class="font_16">
                        <span<strong><? print round($row['qty'], 8)."<br><span class='font_10'>".round($row['qty']*100/$qty, 2)."%</span>"; ?></strong></span></td>
                        </tr>
                        <tr>
                        <td colspan="4"><hr></td>
                        </tr>
                        
                      <?
					   }
					  ?>
</table>
                        <br><br>
                       
           
        
        <?
	}
	
	function showTrans($symbol)
	{
		$query="SELECT * 
		          FROM trans 
				 WHERE cur=?
			  ORDER BY ID DESC
			     LIMIT 0,20";
		
	    $result=$this->kern->execute($query, 
									 "s", 
									 $symbol);	
		
		if (mysqli_num_rows($result)==0)
		{
			print "<br><p class='font_14' style='color:#990000'>No transactions found</p><br><br>";
			return false;
		}
	    
		?>
           
          <br>
                   <table width="90%" class="table-responsive">
                    
                    <?
					   while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
					   {
					?>
                    
                        <tr>
                        <td width="8%" align="left" class="font_14">
                        <img src="../../template/template/GIF/empty_pic.png"  class="img-circle img-responsive"/></td>
                        <td width="2%">&nbsp;</td>
                        <td width="70%" align="left" class="font_14">
                        <a href="#" class="font_14">
                        <strong><? print $this->template->formatAdr($row['src']); ?></strong>
                        </a>
                        <p class="font_10"><? print "Received ~". $this->kern->timeFromBlock($row['block'])." ago"; ?></p>
                        </td>
                        
                        <td width="25%" align="center" class="font_14" style="color : <? if ($row['amount']<0) print "#990000"; else print "#009900"; ?>">
                        <span<strong><? print round($row['amount'], 8)." ".$symbol; ?></strong></span></td>
                        </tr>
                        <tr>
                        <td colspan="4"><hr></td>
                        </tr>
                        
                      <?
					   }
					  ?>
</table>
                        <br><br>
           
        
        <?
	}
	
	function showMyAssets()
	 {
	    // Issue More Modal
		$this->showIssueMoreModal();
		
		// Query
		$query="SELECT * 
		          FROM assets 
				 WHERE adr=?
			  ORDER BY ID ASC
			     LIMIT 0,20"; 
		
		 $result=$this->kern->execute($query, 
									  "s", 
									  $_REQUEST['ud']['adr']);	
		 
		?>
          
          <br>
          <table width="95%" border="0" cellspacing="0" cellpadding="0" class="table-responsive">
                      
                      <?
					     while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
						 {
					  ?>
                      
                            <tr>
                            <td width="5%"><img src="<? if ($row['pic']=="") print "../../template/GIF/empty_pic.png"; else $this->kern->crop($row['pic'], 50, 50); ?>"  class="img-circle" width="50"/></td>
                            <td width="0%">&nbsp;</td>
                            <td width="74%"><span class="font_14"><a href="asset.php?symbol=<? print $row['symbol']; ?>"><? print base64_decode($row['title'])." (".$row['symbol'].")"; ?></a></span>
                              <p class="font_12"><? print substr(base64_decode($row['description']), 0, 250)."..."; ?></p></td>
                            <td width="12%" align="center">
								
							 <div class="btn-group">
                             <button type="button" class="btn btn-warning dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                             Action <span class="caret"></span>
                             </button>
                             <ul class="dropdown-menu">
                             <li><a href="javascript:void(0)" onClick="$('#renew_modal').modal(); $('#txt_renew_target_type').val('ID_ASSET'); $('#txt_renew_targetID').val('<? print $row['assetID']; ?>');">Renew</a></li>
                             <li><a href="javascript:void(0)" onClick="$('#issue_more_modal').modal()">Issue More</a></li>
                             </ul>
                             </div>
								
							</td>
                            </tr>
			               
                            <tr>
                            <td colspan="4"><hr></td>
                            </tr>
                      
                      <?
	                      }
					  ?>
                        
</table>
                  
                 
        
        <?
	 }
	
	 
	
	 function showIssueAssetModal($symbol="")
	 {
		 ?>
            
            <br><br>
            <form id="form_modal_issue" name="form_modal_issue" method="post" action="main.php?target=<? print $_REQUEST['target']; ?>&sub_target=<? print $_REQUEST['sub_target']; ?>&act=issue">
            <table width="90%" border="0" cellspacing="0" cellpadding="0">
            <tr>
            <td width="90%" align="center" valign="top">
				<table width="90%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td height="30" align="left" valign="top" class="font_14"><strong>Name</strong></td>
                </tr>
              <tr>
                <td align="left">
                <input class="form-control" id="txt_issue_name" name="txt_issue_name" placeholder="Asset Name (5-30 characters)" value=""/></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="font_14"><strong>Short Description</strong></td>
              </tr>
              <tr>
                <td align="left">
                <textarea rows="3fd" id="txt_issue_desc" name="txt_issue_desc" class="form-control" placeholder="Short Description (10-250 characters)"></textarea>
                </td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tbody>
                    <tr>
                      <td width="45%" height="30" valign="top"><span class="font_14"><strong>How to buy this asset ? (optional)</strong></span></td>
                      <td width="5%" valign="top">&nbsp;</td>
                      <td width="50%" height="30" valign="top"><span class="font_14"><strong>How to sell  this asset ? (optional)</strong></span></td>
                    </tr>
                    <tr>
                      <td width="45%"><textarea rows="3fd" id="txt_issue_buy" name="txt_issue_buy" class="form-control" placeholder="Explain how regular users can buy this asset (10-500 characters)"></textarea></td>
                      <td width="5%">&nbsp;</td>
                      <td width="50%"><textarea rows="3fd" id="txt_issue_sell" name="txt_issue_sell" class="form-control"  placeholder="Explain how regular users can sellor redeem this asset (10-500 characters)"></textarea></td>
                    </tr>
                  </tbody>
                </table></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left" valign="top" class="font_14"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="45%" height="30" align="left" valign="top" class="font_14"><strong>Website (optional)</strong></td>
                    <td width="5%">&nbsp;</td>
                    <td width="50%" align="left" valign="top"><strong>Pic (optional)</strong></td>
                  </tr>
                  <tr>
                    <td><input class="form-control" id="txt_issue_website" name="txt_issue_website"  placeholder="Wesite Link" value=""/></td>
                    <td width="5%">&nbsp;</td>
                    <td><input class="form-control" id="txt_issue_pic" name="txt_issue_pic" placeholder="Link to Image" value=""/></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="23%" height="30" align="left" valign="top" class="font_14"><strong>Symbol</strong></td>
                    <td width="3%">&nbsp;</td>
                    <td width="23%" align="left" valign="top" class="font_14"><strong>Initial Qty</strong></td>
                    <td width="3%">&nbsp;</td>
                    <td width="23%" align="left" valign="top" class="font_14"><strong> Fee (%)</strong></td>
                    <td width="3%">&nbsp;</td>
                    <td width="23%" align="left" valign="top" class="font_14"><strong>Expire (days)</strong></td>
                  </tr>
                  <tr>
                    <td><input class="form-control" id="txt_issue_symbol" name="txt_issue_symbol" placeholder="XXXXXX" value="" maxlength="6"/></td>
                    <td width="3%">&nbsp;</td>
                    <td><input class="form-control" id="txt_issue_init_qty" name="txt_issue_init_qty" placeholder="10000" value="" onKeyUp="onClick()" type="number"/></td>
                    <td width="3%">&nbsp;</td>
                    <td><input class="form-control" id="txt_issue_trans_fee" name="txt_issue_trans_fee" placeholder="1%" value="1" type="number" min="0.01" max="10"  onKeyUp="onClick()"/></td>
                    <td width="3%">&nbsp;</td>
                    <td><input class="form-control" id="txt_issue_days" name="txt_issue_days" placeholder="1000" style="width:100px" value="" type="number" onKeyUp="onClick()"/></td>
                  </tr>
                  </table></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              </table></td>
          </tr>
            <tr>
              <td align="center" valign="top"><hr></td>
            </tr>
            <tr>
              <td align="right" valign="top">
              <a href="javascript:void(0)" onClick="$('#form_modal_issue').submit()" class="btn btn-primary">Issue Asset</a></td>
              </tr>
            </table>
            <br><br><br>
</form>
        
<script>
		function onClick()
		{
			var qty=parseFloat($('#txt_issue_init_qty').val()*0.0001);
			var days=parseFloat($('#txt_issue_days').val()*0.0001);
			var trans_fee=parseInt($('#txt_issue_trans_fee').val());
			$('#ss_net_fee_panel_val').text(parseFloat((qty+days)*trans_fee).toFixed(4));
		}
		
		</script>
        
        <?
	}
	
	function newMarket($asset_symbol, 
					   $cur_symbol, 
					   $decimals,
					   $name, 
					   $desc, 
					   $days)
	{
		 // Basic check
		 if ($this->kern->basicCheck($_REQUEST['ud']['adr'], 
	                                 $_REQUEST['ud']['adr'],
						             0.0001*$days, 
						             $this->template,
					      	         $this->acc)==false)
		 return false;	 
		 
		 // Name
		 if ($this->kern->isTitle($name)==false)
		 {
			 $this->template->showErr("Invalid name");
			 return false;
		 }
		 
		 // Description
		 if ($this->kern->isDesc($desc)==false)
		 {
			 $this->template->showErr("Invalid description");
			 return false;
		 }
		 
		 // Asset symbol length
		 if (strlen($asset_symbol)!=6)
		 {
			 $this->template->showErr("Invalid asset");
			 return false; 
		 }
		
		 // Asset symbol valid
		 if ($this->kern->isAsset($asset_symbol)==false)
		 {
			 $this->template->showErr("Invalid asset");
			 return false;
		 }
		 
		 // Currency valid ?
		 if ($cur_symbol!="CRC")
		 {
			 // Currency symbol length
		     if (strlen($asset_symbol)!=6)
		     {
			     $this->template->showErr("Invalid currency");
			     return false; 
		     }
			 
		    if ($this->kern->isAsset($cur_symbol)==false)
		    {
				$this->template->showErr("Invalid currency");
			    return false;
		    }
		 }
		
		
		// Days
		if ($days<30)
		{
			 $this->template->showErr("Minimum period is 30 days");
			 return false;
		}
		
		try
	     {
		   // Begin
		   $this->kern->begin();

           // Action
           $this->kern->newAct("Launch a new regular asset market");
					   
		   // Insert to stack
		   $query="INSERT INTO web_ops 
			                SET userID=?, 
							    op=?, 
								fee_adr=?, 
								target_adr=?,
								par_1=?,
								par_2=?,
								par_3=?,
								par_4=?,
								par_5=?,
								days=?,
								status=?, 
								tstamp=?"; 
			
	       $this->kern->execute($query, 
								"isssssssiisi", 
								$_REQUEST['ud']['ID'], 
								"ID_NEW_REGULAR_ASSET_MARKET", 
								$_REQUEST['ud']['adr'], 
								$_REQUEST['ud']['adr'], 
								$asset_symbol, 
								$cur_symbol, 
								$name, 
								$desc, 
								$decimals, 
								$days, 
								"ID_PENDING", 
								time());
		
		   // Commit
		   $this->kern->commit();
		   
		   // Confirm
		   $this->template->confirm();
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
	
	
	function showMarkets($asset="")
	{
		if ($asset=="")
		$query="SELECT * 
		          FROM assets_mkts 
				 WHERE adr<>? 
				   AND CHAR_LENGTH(asset)=6
			  ORDER BY bid DESC
				 LIMIT 0,25";
		else
		$query="SELECT * 
		          FROM assets_mkts 
				 WHERE adr<>?
				   AND asset='".$asset."'
			  ORDER BY bid DESC
				 LIMIT 0,25";
		
		$result=$this->kern->execute($query, 
									 "s", 
									 "default");	
	 
	    $this->template->showTopBar("Asset", "50%", "Ask", "15%", "Bid", "15%", "Trade", "15%");
		?>
           
          
           <table class="table-responsive" width="90%">
           
           <?
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                 <tr>
                 <td width="10%"><img class="img img-circle" src="../../template/GIF/empty_pic.png" width="50"></td>
                 <td width="0%">&nbsp;</td>
                 <td width="47%" align="left">
                 <a href="asset.php?symbol=<? print $row['symbol']; ?>" class="font_14"><? print base64_decode($row['name'])."<br>"; ?></a>
                 <p class="font_10"><? print substr(base64_decode($row['description']), 0, 40)."..."; ?></p>
                 </td>
                 <td class="font_14" width="14%" align="center">
				 <? 
				      print "<strong>".round($row['ask'], 8)."</strong> <br><span class='font_10'>".$row['cur']."</span>"; 
			     ?>
                 </td>
                 <td width="19%" align="center" class="font_14">
				 <? 
				      print "<strong>".round($row['bid'], 8)."</strong> <br><span class='font_10'>".$row['cur']."</span>";
			     ?>
                 </td>
                 <td class="font_16" width="10%">
                 <a href="market.php?ID=<? print $row['mktID']; ?>" class='btn btn-warning btn-sm' style="color:#000000">Trade</a>
                 </td>
                
                 
                 </tr>
                 <tr><td colspan="6"><hr></td></tr>
           
           <?
			  }
		   ?>
           
           </table>
           
        <?
	}
	
	function showMyMarkets()
	{
		$query="SELECT * 
		          FROM assets_mkts 
				 WHERE adr=? 
			ORDER BY bid DESC
				 LIMIT 0,25";
		
		$result=$this->kern->execute($query, 
									 "s", 
									 $_REQUEST['ud']['adr']);	
	 
	    $this->template->showTopBar("Asset", "50%", 
									"Ask", "15%", 
									"Bid", "15%", 
									"Trade", "15%");
		?>
           
          
           <table class="table-responsive" width="90%">
           
           <?
		      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			  {
		   ?>
           
                 <tr>
                 <td width="10%"><img class="img img-circle" src="../../template/GIF/empty_pic.png" width="50"></td>
                 <td width="0%">&nbsp;</td>
                 <td width="47%" align="left">
                 <a href="asset.php?symbol=<? print $row['symbol']; ?>" class="font_14"><? print base64_decode($row['name'])."<br>"; ?></a>
                 <p class="font_10"><? print substr(base64_decode($row['description']), 0, 40)."..."; ?></p>
                 </td>
                 <td class="font_14" width="14%" align="center">
				 <? 
				      print "<strong>".round($row['ask'], 8)."</strong> <br><span class='font_10'>".$row['cur']."</span>"; 
			     ?>
                 </td>
                 <td width="19%" align="center" class="font_14">
				 <? 
				      print "<strong>".round($row['bid'], 8)."</strong> <br><span class='font_10'>".$row['cur']."</span>";
			     ?>
                 </td>
                 
				 <td class="font_16" width="10%">
                 <a href="market.php?ID=<? print $row['mktID']; ?>" class='btn btn-warning btn-sm' style="color:#000000">Renew</a>
                 </td>
                
                 
                 </tr>
                 <tr><td colspan="6"><hr></td></tr>
           
           <?
			  }
		   ?>
           
           </table>
           
        <?
	}
	
	function showNewMarketModal()
	{
		$this->template->showModalHeader("modal_new_market", "New Assets Market", "act", "new_market", "edit_symbol", "");
		?>
        
            <table width="610" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="172" align="center" valign="top"><table width="180" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center"><img src="./GIF/new_mkt.png" class="img-responsive"/></td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
              <tr>
                <td align="center">&nbsp;</td>
              </tr>
            </table></td>
            <td width="438" align="right" valign="top"><table width="400" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="30" align="left" valign="top" class="font_14"><strong>Title</strong></td>
              </tr>
              <tr>
                <td align="left" valign="top" class="font_14">
                <input class="form-control" id="txt_new_name" name="txt_new_name" placeholder="Title (5-50 characters)" style="width:350px"/></td>
              </tr>
              <tr>
                <td align="left" valign="top" class="font_14">&nbsp;</td>
              </tr>
              <tr>
                <td height="30" align="left" valign="top" class="font_14"><strong>Short Description</strong></td>
              </tr>
              <tr>
                <td align="left">
                <textarea rows="3" id="txt_new_desc" name="txt_new_desc" class="form-control" style="width:350px" placeholder="Short Description (optional, 0-250 characters)"></textarea>
                </td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left"><table width="85%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="33%" height="30" align="left" valign="top" class="font_14"><strong>Asset Symbol</strong></td>
                    <td width="33%" height="30" align="left" valign="top" class="font_14"><strong>Currency</strong></td>
                    <td width="33%" align="left" valign="top" class="font_14"><strong>Decimals</strong></td>
                  </tr>
                  <tr>
                    <td><input name="txt_new_asset_symbol" class="form-control" id="txt_new_asset_symbol" placeholder="XXXXXX" style="width:90%" maxlength="6"/></td>
                    <td><input name="txt_new_cur" class="form-control" id="txt_new_cur" placeholder="XXXXXX" style="width:90%" maxlength="6"/></td>
                    <td align="left"><select id="dd_decimals" name="dd_decimals" class="form-control" style="width:90%">
                      <option value="1" selected>1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                    </select></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left"><table width="85%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="33%" height="30" align="left" valign="top"><span class="font_14"><strong>Days</strong></span></td>
                  </tr>
                  <tr>
                    <td align="left"><input class="form-control" id="txt_new_days" name="txt_new_days" placeholder="100" style="width:80px"/></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              <tr>
                <td align="left">&nbsp;</td>
              </tr>
              </table></td>
              </tr>
             </table>
              
              
        <?
		$this->template->showModalFooter("New");
	}
	
	
	
}
?>