<?php
class CBuy
{
	function CBuy($db, $template)
	{
		// Kern
		$this->kern=$db;
		
		// Template
		$this->template=$template;
	}
	
	function confirm($cur, $amount, $txid)
	{
		$this->template->showOk("Your transaction has been recorded.");
	}
	
	function showAdr()
	{
		?>

             <table width="540" border="0" cellspacing="0" cellpadding="0">
			    <tbody>
			      <tr>
			        <td height="30" align="left" class="font_14">Bitcoin Address</td>
		          </tr>
			      <tr>
			        <td height="30" align="left"  class="font_14" bgcolor="#fafafa"><strong>1CcPVXJA5BEEnjtj8vgcSKGPZwdRrgsF1B</strong></td>
		          </tr>
			      <tr>
			        <td align="left">&nbsp;</td>
		          </tr>
			      <tr>
			        <td height="30" align="left"  class="font_14">Ethereum Address</td>
		          </tr>
			      <tr>
			        <td height="30" align="left" class="font_14" bgcolor="#fafafa"><strong>0xf7eEDc7d2EE8078fAcaD699737f00fDE57931936</strong></td>
		          </tr>
			      <tr>
			        <td align="left">&nbsp;</td>
		          </tr>
			      <tr>
			        <td height="30" align="left" class="font_14">Bitcoin Cash</td>
		          </tr>
			      <tr>
			        <td height="30" align="left" class="font_14" bgcolor="#fafafa"><strong>qrzfhe9l0rpwpdvkung0ce9qlhzdrxqugud2k3stah</strong></td>
		          </tr>
		        </tbody>
			    </table>

        <?php
	}
	
	function showConfirmModal()
	{
		// Modal
		$this->template->showModalHeader("buy_confirm_modal", "Confirm Payment", "act", "confirm");
		
		?>
              
         <table width="550" border="0" cellspacing="0" cellpadding="5">
          <tr>
            <td width="39%" align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td align="center"><img src="../../template/GIF/ico_renew.png" width="150"></td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">&nbsp;</td>
              </tr>
              <tr>
                <td align="center" class="bold_gri_18">&nbsp;</td>
              </tr>
            </table></td>
            <td width="61%" align="left" valign="top">
            
            
            <table width="100%" border="0" cellspacing="0" cellpadding="5">
              <tr>
                <td height="30" valign="top" class="font_14"><strong>Cryptocurrency</strong></td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">
				
					<select id="dd_confirm_cur" name="dd_confirm_cur" class="form-control" style="width: 300px">
						<option value="BTC">Bitcoin</option>
						<option value="ETH">Ethereum</option>
						<option value="BCC">Bitcoin Cash</option>
					</select>
		        
				</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_14">&nbsp;</td>
              </tr>
              <tr>
				  <td height="30" valign="top" class="font_14"><strong>Amount Sent (in cryptocurrency)</strong></td>
              </tr>
              <tr>
                <td><input class="form-control" value="0" name="txt_confirm_amount" id="txt_confirm_amount" type="number" step="0.0001" style="width:100px"/></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" valign="top" class="font_12"><span class="font_14"><strong>Transaction ID (optional)</strong></span></td>
              </tr>
              <tr>
                <td><input class="form-control" value="" name="txt_confirm_txid" id="txt_confirm_txid" maxlength="250" style="width:300px"/></td>
              </tr>
            </table>
            
            </td>
          </tr>
          </table>

    
           
        <?php
		$this->template->showModalFooter("Renew");
	}
	
	function showConfirmBut()
	{
		// Modal
		$this->showConfirmModal();
		?>
            
            <br>
            <table width="550" border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
				<td align="right"><a href="javascript:void()" onClick="$('#buy_confirm_modal').modal()" class="btn btn-primary">Confirm</a></td>
            </tr>
            </tbody>
            </table>

        <?php
	}
	
	function showPrice()
	{
		?>

             <div class="panel panel-default" style="width: 550px">
				 <div class="panel-body font_14" align="left">Test Coin Price - <strong style="color: #009900">$0.25</strong></div>
             </div><br>

        <?php
	}
}
?>
        
