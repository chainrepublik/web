<?
  session_start(); 
  
  include "../../../kernel/db.php";
  include "../../../kernel/CUserData.php";
  include "../../../kernel/CGameData.php";
  include "../../../kernel/CAccountant.php";
  include "../../template/CTemplate.php";
  include "../../../kernel/CVMarket.php";
  include "../../../kernel/CAds.php";
  include "../CTerms.php";
  
  $db=new db();
  $gd=new CGameData($db);
  $ud=new CUserData($db);
  $template=new CTemplate();
  $acc=new CAccountant($db, $template);
  $mkt=new CVMarket($db, $acc, $template);
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
<link rel="shortcut icon" type="image/png" href="../../template/GIF/favico.png"/>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script>$(document).ready(function() { $("body").tooltip({ selector: '[data-toggle=tooltip]' }); });</script>
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
			   $terms->showMenu(2);
			   $template->showLeftAds();
			?>
            </td>
            <td width="594" align="center"><table width="500" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center" class="inset_maro_20">&nbsp;</td>
              </tr>
              <tr>
                <td align="center" class="bold_red_20">Privacy Policy</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">This privacy policy sets out how ANNO1777 Labs uses and protects any  information that you give [business name] when you use this website.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">ANNO1777 Labs  is committed to ensuring that your privacy is protected.  Should we ask you to provide certain information by which you can be  identified when using this website, then you can be assured that it will  only be used in accordance with this privacy statement.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="simple_">What we collect</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_">We may collect the following information:</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30" class="simple_gri_14">-name and job title </td>
              </tr>
              <tr>
                <td height="30" class="simple_gri_14">-proof of identity (photo ID, passport)</td>
              </tr>
              <tr>
                <td height="30" class="simple_gri_14">-proof of adress (utility bills)</td>
              </tr>
              <tr>
                <td height="30" class="simple_gri_14">-demographic information such as postcode, preferences and interests </td>
              </tr>
              <tr>
                <td height="30" class="simple_gri_14">-other information relevant to customer surveys and/or offers</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_"><strong>What we do with the information we gather</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">We require this information to understand your needs and provide you  with a better service, and in particular for the following reasons:</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="30"><span class="simple_gri_14">Internal record keeping </span></td>
              </tr>
              <tr>
                <td height="30"><span class="simple_gri_14">Service security and integrity</span></td>
              </tr>
              <tr>
                <td height="30"><span class="simple_gri_14">We may use the information to improve our products and services. </span></td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">We may periodically send promotional emails about new products, special  offers or other information which we think you may find interesting  using the email address which you have provided.</span></td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">From time to time, we may also use your information to contact you for  market research purposes. We may contact you by email, phone, fax or  mail. We may use the information to customise the website according to  your interests.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="simple_">Security</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">We are committed to ensuring that your information is secure. In order  to prevent unauthorised access or disclosure, we have put in place  suitable physical, electronic and managerial procedures to safeguard and  secure the information we collect online.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="simple_">How we use cookies</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">A cookie is a small file which asks permission to be placed on your  computer's hard drive. Once you agree, the file is added and the cookie  helps analyse web traffic or lets you know when you visit a particular  site. Cookies allow web applications to respond to you as an individual.  The web application can tailor its operations to your needs, likes and  dislikes by gathering and remembering information about your  preferences.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">We use traffic log cookies to identify which pages are being used. This  helps us analyse data about webpage traffic and improve our website in  order to tailor it to customer needs. We only use this information for  statistical analysis purposes and then the data is removed from the  system.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">Overall, cookies help us provide you with a better website by enabling  us to monitor which pages you find useful and which you do not. A cookie  in no way gives us access to your computer or any information about  you, other than the data you choose to share with us.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">You can choose to accept or decline cookies. Most web browsers  automatically accept cookies, but you can usually modify your browser  setting to decline cookies if you prefer. This may prevent you from  taking full advantage of the website.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="simple_">Links to other websites</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td class="simple_gri_14">Our website may contain links to other websites of interest. However,  once you have used these links to leave our site, you should note that  we do not have any control over that other website. Therefore, we cannot  be responsible for the protection and privacy of any information which  you provide whilst visiting such sites and such sites are not governed  by this privacy statement. You should exercise caution and look at the  privacy statement applicable to the website in question.</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong class="simple_">Controlling your personal information</strong></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">You may choose to restrict the collection or use of your personal information in the following ways:.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">Whenever you are asked to fill in a form on the website, look for the  box that you can click to indicate that you do not want the information  to be used by anybody for direct marketing purposes</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">if you have previously agreed to us using your personal information for  direct marketing purposes, you may change your mind at any time by  writing to ANNO1777 Labs, 95 Wilton Road, Suite 3, London, UK</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">We will not sell, distribute or lease your personal information to third  parties unless we have your permission or are required by law to do so.  We may use your personal information to send you promotional  information about third parties which we think you may find interesting  if you tell us that you wish this to happen.</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">You may request details of personal information which we hold about you  under the Data Protection Act 1998. A small fee will be payable. If you  would like a copy of the information held on you please write to   ANNO1777 Labs, 95 Wilton Road, Suite 3, London, UK</span></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><span class="simple_gri_14">If you believe that any information we are holding on you is incorrect  or incomplete, please write to or email us as soon as possible at the  above address. We will promptly correct any information found to be  incorrect.</span></td>
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
                    <td height="0" align="center" class="font_12" style="color:#818d9b">Copyright 2016, ANNO1777 Labs, All Rights Reserved</td>
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