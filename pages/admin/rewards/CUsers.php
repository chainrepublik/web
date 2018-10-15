<?php
class CRewards
{
	function CRewards($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
		
		if ($_REQUEST['ud']['user']!="root") 
			die ("Invalid credentials");
	}
    
	function showRewardPanel()
	{
		?>

            <table width="550" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td height="333" align="center" valign="top" background="../../template/GIF/bonus_back.png"><table width="90%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                      <tr>
                        <td height="55" align="center" valign="bottom" class="font_18" style="color: #999999">Nodes Reward</td>
                        </tr>
                      <tr>
                        <td>&nbsp;</td>
                        </tr>
                      <tr>
                        <td height="120" align="center" valign="top"><table width="95%" border="0" cellspacing="0" cellpadding="0">
                          <tbody>
                            <tr>
                              <td width="24%" rowspan="3" align="left">&nbsp;</td>
                              <td width="26%" align="center" class="font_12" style="color: #999999">Total Energy</td>
                              <td width="25%" align="center" class="font_12" style="color: #999999">Reward / point</td>
								<td width="25%" align="center" class="font_12" style="color: #555555"><strong>Your Reward</strong></td>
                            </tr>
                            <tr>
                              <td width="26%" height="70" align="center" class="font_20" style="color: #999999">1242</td>
                              <td width="25%" align="center" class="font_20" style="color: #999999">0.0013</td>
								<td width="25%" align="center" class="font_20" style="color: #009900"><strong>0.3212</strong></td>
                            </tr>
                            <tr>
                              <td width="26%" align="center" class="font_12" style="color: #999999">total users energy</td>
                              <td width="25%" align="center" class="font_12" style="color: #999999">CRC / energy point</td>
								<td width="25%" align="center" class="font_12" style="color: #555555"><strong>CRC</strong></td>
                            </tr>
                          </tbody>
                        </table></td>
                        </tr>
                      <tr>
						  <td height="100" valign="middle" class="font_10" style="color: #999999">Node operators are rewarded by the network every 24 hours. Rewards are calculated based on the total energy of the users of a node. Node operators reward pool is 10% of total daily reward pool, or <strong><?php print round($db->getRewardPool("ID_NODES"))." CRC / day"; ?></strong>. Rewards are not automatically paid by the network. You need to <strong>claim</strong> your reward every 24 hours. Below are listed. Below are listed the last rewards received by this node.</td>
                      </tr>
                      </tbody>
                  </table></td>
                </tr>
              </tbody>
            </table>

        <?php
	}
}