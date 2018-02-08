<?
class CEmail
{
	function CEmail()
	{
		$this->addHeader();
	}
	
	function addHeader()
	{
		$this->mes="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
                 <tr>
                 <td align=\"left\"><img src=\"http://www.ChainRepublik/pages/template/GIF/email_logo.png\" width=\"360\" height=\"79\" /></td>
                 </tr>
                 <tr>
                 <td>&nbsp;</td>
                 </tr>
                 <tr>
                 <td height=\"60\" background=\"http://www.ChainRepublik/pages/template/GIF/email_bar.png\">&nbsp;</td>
                 </tr>
                 <tr>
                 <td height=\"100\" align=\"center\" valign=\"top\">";
	}
	
	function addSpace($no=1)
	{
		for ($a=1; $a<=$no; $a++)
		  $this->mes=$this->mes."<br>";
	}
	
	function addFooter()
	{
		$this->mes=$this->mes."</td></tr><tr>
                               <td height=\"60\" background=\"http://www.ChainRepublik/pages/template/GIF/email_bar.png\">&nbsp;</td>
                               </tr><tr>
                               <td height=\"60\" align=\"center\">
                               <br /><br />
                               <table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">
                               <tr>
                               <td height=\"70\" align=\"center\" valign=\"top\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:10px; color:#777777\">Please do not reply to this email. This mailbox is not monitored, and we are unable to respond to inquiries sent to this address. This email is not spam. You have received this email because you have joined ChainRepublik, the first real cash social trading game. If you want to stop those notifications, please login to your chainrepublik account or get in touch with our support staff at support@ChainRepublik</td>
                                </tr>
                                </table></td>
                                </tr>
                                </table>";
		
	}
	
	function addPanel($text, $color="#555555", $size=14, $bold="bold", $bgcolor="#f0f0f0")
	{
		$this->mes=$this->mes."<table width=\"600\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">
                               <tr>
                               <td height=\"70\" align=\"center\" valign=\"top\" bgcolor=\"".$bgcolor."\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:".$size."; font-weight:".$bold."; color : ".$color."\">".$text."</td>
                               </tr>
                               </table>";
	}
	
	function addTable($title_1, $val_1, 
	                  $title_2="", $val_2="", 
					  $title_3="", $val_3="", 
					  $title_4="", $val_4="", 
					  $title_5="", $val_5="",
					  $title_6="", $val_6="")
	{
		$this->mes=$this->mes."<table width=\"600\" border=\"0\" cellspacing=\"2\" cellpadding=\"5\">";
		
		// Row 1
		if ($title_1!="") $this->mes=$this->mes."<tr>
           <td width=\"31%\" height=\"30\" align=\"right\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#333333\">".$title_1."&nbsp;&nbsp;</td>
           <td width=\"69%\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#681c88; font-weight:bold\">".$val_1."</td>
           </tr>";
		
		// Row 2
		if ($title_2!="") $this->mes=$this->mes."<tr>
           <td width=\"31%\" height=\"30\" align=\"right\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#333333\">".$title_2."&nbsp;&nbsp;</td>
           <td width=\"69%\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#681c88; font-weight:bold\">".$val_2."</td>
           </tr>";
		   
		   // Row 3
		if ($title_3!="") $this->mes=$this->mes."<tr>
           <td width=\"31%\" height=\"30\" align=\"right\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#333333\">".$title_3."&nbsp;&nbsp;</td>
           <td width=\"69%\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#681c88; font-weight:bold\">".$val_3."</td>
           </tr>";
		   
		   // Row 4
		if ($title_4!="") $this->mes=$this->mes."<tr>
           <td width=\"31%\" height=\"30\" align=\"right\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#333333\">".$title_4."&nbsp;&nbsp;</td>
           <td width=\"69%\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#681c88; font-weight:bold\">".$val_4."</td>
           </tr>";
		   
		   // Row 5
		if ($title_5!="") $this->mes=$this->mes."<tr>
           <td width=\"31%\" height=\"30\" align=\"right\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#333333\">".$title_5."&nbsp;&nbsp;</td>
           <td width=\"69%\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#681c88; font-weight:bold\">".$val_5."</td>
           </tr>";
		   
		   // Row 6
		if ($title_2!="") $this->mes=$this->mes."<tr>
           <td width=\"31%\" height=\"30\" align=\"right\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#333333\">".$title_6."&nbsp;&nbsp;</td>
           <td width=\"69%\" bgcolor=\"#f0f0f0\" style=\"font-family:Verdana, Geneva, sans-serif; font-size:14px; color:#681c88; font-weight:bold\">".$val_6."</td>
           </tr>";
		   
		// End
		$this->mes=$this->mes."</table>";
	}
	
	
	function send($email, $subject)
	{
		 $this->addFooter();
		 
         $headers = "From: noreply@ChainRepublik \r\n";
         $headers .= "Reply-To: noreply@ChainRepublik \r\n";
         $headers .= "MIME-Version: 1.0\r\n";
         $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		 return mail($email, $subject, $this->mes, $headers);
	}
}
?>