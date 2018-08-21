<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  
  
  include "../CTerms.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  
  $terms=new CTerms($db, $acc, $template);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ChainRepublik</title>
<link rel="stylesheet" href="../../../style.css" type="text/css">
<script src="../../../flat/js/vendor/jquery.min.js"></script>
<script src="../../../flat/js/flat-ui.js"></script>
<link rel="stylesheet"./ href="../../../flat/css/vendor/bootstrap/css/bootstrap.min.css">
<link href="../../../flat/css/flat-ui.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
<link rel="shortcut icon" type="image/x-icon" href="../../template/GIF/favico.ico"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-557d86153ff482a3" async="async"></script>
</head>

<body background="../../template/GIF/back.png">

<?
   $template->showTop();
?>

<table width="1020" border="0" align="center" cellpadding="0" cellspacing="0">
  <tbody>
    <tr>
      <td align="center">
      <?
	     $template->showMainMenu(7);
	  ?>
      </td>
    </tr>
    <tr>
      <td><img src="../../template/GIF/bar.png" width="1020" height="20" alt=""/></td>
    </tr>
    <tr>
      <td height="500" align="center" valign="top" background="../../template/GIF/back_panel.png"><table width="1005" border="0" cellspacing="0" cellpadding="0">
        <tbody>
          <tr>
            <td width="204" align="right" valign="top">
            <?
			   $terms->showMenu(1);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center"><table width="500" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center" class="inset_maro_20">&nbsp;</td>
              </tr>
              <tr>
                <td align="center" class="bold_red_20">Terms and Conditions</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="bold_">ANNO1777 Labs, registered in Suite 15, 1st Floor Oliaji Trade Center,  Francis Rachesl Street, Victoria, Mahe, Seychelles as ADMINISTRATOR,  provides the online service chainrepublik, which can be accessed on webpage  WWW.chainrepublik.COM</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="bold_">1.General data</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">1.1 The terms and conditions are applicable for all service users,  including the users who contribute with video materials, information or  other material and services on the website. This website may contain  links to other websites, which are not under the ownership or the  control of the ADMINISTRATOR. The ADMINISTRATOR does not have control  and does not take any responsibility for the content, politics or  practices of any website. By using the chainrepublik service, you explicitly  absolve the ADMINISTRATOR of any responsibility which might result after  your use of another website. Therefore, we recommend that you read the  terms and conditions for each website that you might visit when leaving  our website.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">1.2 The ADMINISTRATOR reserves the right to change the Terms and  Conditions each time he considers necessary. All users will be notified  regarding the changes.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="bold_">2. The chainrepublik service</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">2.1 chainrepublik is provided only to the users who  have opened a customer account using the interface provided by the  service. Its use becomes available from the moment when the  ADMINISTARTOR has opened an account for the user.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">2.2 The game’s features can be modified at any  time by the ADMINISTRATOR and any time the ADMINISTRATOR thinks it’s  necessary, without prior notice and without providing a specific reason.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">2.3 Since the service is permanently modified  and the game rules change, the user has the right to use chainrepublik only  in the current version.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">2.4 The user does not have the right to ask the ADMINISTRATOR for changes of the chainrepublik service or technical support.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">2.5 The ADMINISTARTOR reserves the right to  temporary or final suspend the chainrepublik service without prior notice and  without providing a specific reason. In this case the user CANNOT claim  damages.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">2.6  The ADMINISTRATOR does not take any  responsibility for the general losses of bad functioning or interruption  of the chainrepublik service.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="bold_">3. The user’s obligations</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_red_14"><strong>3.1.1 A user CANNOT hold more accounts within  the service. Only one account per user is allowed. All accounts found to violate this rule will be PERMANENTLY CLOSED. All assets / virtual gold / virtual doollar owned by accounts that violate this rule will be deleted. The identification of multiple accounts of one user is made by the  ADMINISTRATOR through his own methods.</strong></span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.1.2 The user must ensure that the password  chosen to log on the personal account is known only by him/her. The  reveal of other persons’ password is forbidden. The user is the only one  responsible for damages made in case of loss of password.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.1.3  In chainrepublik service, the ADMINISTRATOR  places a communication way between users. The user is the only one  responsible for the content he uses within the communication. </span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.1.4 The user is authorized to use the  chainrepublik service only byway of a web-browser program. Any use of a  script or another IT program to access the chainrepublik service is strictly  forbidden. The violation of this condition will automatically result in  closing the use’s account. </span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.1.5 The use of errors inside the chainrepublik  service for personal advantages of the user is forbidden. The user must  notify the ADMINISTRATOR regarding any breakdown or error within the  service.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.1.6 The use of chainrepublik service for commercial use is forbidden, it is allowed only for personal use.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.1.7 The user does not have the right to copy  or distribute any part of the chainrepublik service without the written  consent of the ADMINISTRATOR.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.1.8 To access certain elements of the  chainrepublik service, the user must create an account. The user cannot use  the account of another person. To create the account, the user must  offer complete and factual information. The user is the sole responsible  for the activity which takes place within the account and for the  security of the password. You must immediately notify the ADMINISTRATOR  in case the security of your account has been compromised or the account  is accessed by a person who is unauthorized. Even if the ADMINISTRATOR  is not responsible for the damages caused by the authorized use of the  account, the user could be responsible for the damages caused to the  service or to other users by the unauthorized use.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.1.9 The user does not have the right to use  any automatic system which accesses the website in a way that it sends  more request messages to the chainrepublik service in a period of time.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.2 By talking to the ADMINISTRATOR, by  written messages or forum, the user has the obligation of using a decent  language, not meant to undermine the ADMINISTRATOR’S authority or to  use strong language with the team which administrates the chainrepublik  service.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.2.1The user admits that he does not consider  the pornographic content as being offending, and in the community where  he lives, the explicit sexual images or pornography are legal.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.2.2  The user declares under his own responsibility that he has legally turned the age of 18.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">3.2.3 The user has the obligation to login into his / her account at least once every 20 days or the account will become INACTIVE. All inactive accounts are deleted. All virtual goods/ virtual currencies owned by inactive accounts are also deleted from our records.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="bold_">4. The rights of the intellectual property</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">The content of the chainrepublik service is offered to the user only with an  informational purpose and for personal use, and cannot be used, copied,  reproduced, distributed, exposed, sold, licensed with other purpose and  without the prior written consent of the holder of the ownership rights.  The ADMINISTRATOR reserves all rights for this website and for its  content. The user agrees not to copy or distribute materials which are  posted to third party, for commercial interest. Is the user downloads  and prints a copy of the content for personal use, he must have all the  copyrights or the approval of the owners of the content. The user also  agrees not to affect or interfere in any way with the security elements  of the website, with the elements which prevent or restrict the use, the  copy of content or elements which attest the using limits of the  website or its content. </td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="bold_">5. Content posted by the user</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">5.1  The chainrepublik service allows the user to post video clips, photos or  other materials. It also allows webhosting, sharing and / or publishing  the posted materials. It is understood that, no matter if the posted  materials are published or not, the ADMINISTRATOR does not guarantee the  confidentiality of the materials.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">5.2 The user is the only one responsible for  the materials that he posts and the consequence of posting or their  publication. When the user posts materials, statements, he sustains and  guarantees that:</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">5.2.1 He owns or has obtained the license, the  rights, the consent or the approval to include, use and distribute  materials on the website and on any media channel; at the same time, he  authorizes the administrator to use all the patents, trademarks,  copyrights or any other ownership rights, all these being incorporated  or in relation to the posted materials, with the purpose of including,  using under any form, processing and distributing materials on the  website and on any media channels, according to the provisions of this  Terms and conditions of use.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">5.2.2 He has the written consent and / or the  approval of each person identifiable in the materials posted on the  website to appear on the website and on any media channels, in the  conditions and limits provisioned in these terms and conditions of use,  i.e. he has all the copyrights for the material that he posts.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">5.2.3 By posting the material on the website,  the user grants the ADMINISTRATOR  a license (that is assigning the  patrimonial copyrights, according to the applicable law) un-exclusively,  with free title, irrevocable, unlimited in time and territory by which  to send to the administrator all the copyrights for the posted material,  including the use, processing, the transformation, the distribution,  the rent, the lease, the public communication, the broadcasting by  cable, promoting or redistributing certain parts of the material under  any media format and byway of any media channel, as well as making the  derivate materials.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">5.3 The user agrees not to post materials  which are protected by the copyrights, or other ownership rights of a  third party, including the right to privacy and advertising, unless he  is the owner of these materials or has the owner’s consent or of the  full copyright law to post that material and to grant the license to the  administrator.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">5.4 The user agrees not to post elements which might damage the chainrepublik service or to a third party.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">5.5 The user agrees to mark as inadequate for  persons less than 18 years the files considered inadequate to be viewed  by underage persons.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">5.6 The user agrees not to post advertising  materials or commercial collaboration requests not requested or  unauthorized by the administrator. </span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">5.7 The user knows that during the use of the  website he exposes himself to the materials posted from a variety of  sources and that the administrator is not responsible for the accuracy,  utility, safety of the materials of the level in which they comply with  the ownership rights. Also, the user knows that he exposes himself to  materials which can be offensive, not decent, repulsive and agrees to  give up all the legal rights or compensations which he might claim from  the owners / operators, affiliates and / or license holders with full  immunity as allowed by the law regarding all legal aspects of the  website use.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="bold_">6.  Warranty statement</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">6.1  The user agrees that by using the  chainrepublik service to take ALL THE RISKS. THE ADMINISTARTOR DOES NOT offer  any warranties or representations regarding the accuracy or how  complete the content of the chainrepublik service is, or the content of other  websites related to chainrepublik and does not take any responsibility for:</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">6.1.1 Any errors, mistakes or lack of accuracy of content</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">6.1.2  Bodily harm or property damage, of any nature, resulted following the access of the user to chainrepublik service.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">6.1.3 Any unauthorized access or use of the  chainrepublik secured servers and / or any degradation / loss of financial /  personal data stored in them.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">6.1.4 Any interruption of stopping of the transmissions from or to the online service chainrepublik.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">6.1.5 Any errors, viruses, Trojan horses or  the types which can be sent to or through the chainrepublik service by a  third party and / or any errors or omissions in any content or for any  loss or damage of any kind as a result of the use of any posted content,  sent by email, or in any other way which is made available through the  online service chainrepublik.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">6.2 THE ADMINISTRATOR does not guarantee and  does not take any responsibility for any product or service which is  published or offered by a third party through the chainrepublik service or  any website with hyperlink or which appeared on any banner of other  means of advertising, and the ADMINISTRATOR will not be part of any or  in any other way responsible for monitoring the transactions between the  user and any third party which offers products or services. Regarding  the buying of any product or service, in any way, the user should use  his better judgment and to exercise it carefully when appropriate.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">7.The website is controlled and offered by the ADMINISTRATOR with the  Republic of Seychelles facilities. The administrator does not assert  that the chainrepublik service is suitable or available for use in other  locations. Those who access or use the chainrepublik service from other  jurisdictions, are responsible in terms of the local law. In case of  breach of the warranties  mentioned at point 6 above, the user will  integrally compensate the administrator for any and for all actions,  claims, requests, direct costs, fees, procedures and expenses, finally  established by an order / decision of the court which is the  responsibility of the administrator, which results in or relating to or  supported due to the breach or the supposedly breached rights if the  administrator uses any information or materials posted regarding these  terms and conditions of use.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="bold_">8. Virtual currency</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">8.1 The user has the right to use virtual  currencies of the service, &ldquo;VIRTUAL DOLLAR&rdquo; hereinafter  referred to as virtual currencies. The only owner of the virtual  currencies is the ADMINISTRATOR.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">8.2 The currency VIRTUAL DOLLAR is available for  buying and distribution and is available for the user. By buying  VIRTUAL DOLLAR or any other virtual goods, the user DOES NOT gain the  ownership right for them, he only has a temporary right to use it. The  owner of all virtual goods from the chainrepublik service is the  ADMINISTRATOR.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">8.3 The user can ask to transform the currency  VIRTUAL DOLLAR available at a certain moment in time in his personal  account in real GOLD. The request is analyzed by the administrator who  is the only one capable or approving or rejecting it.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">8.4  The administrator reserves the right to  reject any request coming from the user by which he wants to exchange  the currency VIRTUAL DOLLAR in real GOLD. The administrator is not obliged to  provide a specific reason for this decision.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table></td>
            <td width="206" align="center" valign="top">
            
			<?
			   $template->showRightPanel();
			   $template->showAds();
			?>
            
            </td>
          </tr>
        </tbody>
      </table>        </td></tr></tbody><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tbody>
            <tr>
              <td height="300" align="center" valign="top" bgcolor="#3b424b">
              <br />
              
			  <?
			     $template->showBottomMenu(false);
			  ?>
              
              <table width="1000" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b"><hr /></td>
                  </tr>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b">Copyright 2018, ANNO1777 Labs, All Rights Reserved</td>
                  </tr>
                  <tr>
                    <td height="0" align="center" class="font_12" style="color:#818d9b">&nbsp;</td>
                  </tr>
                </tbody>
              </table></td>
            </tr>
          </tbody>
    </table>

</body>
</html>