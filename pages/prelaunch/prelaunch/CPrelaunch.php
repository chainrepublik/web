<?
class CPrelaunch
{
	function CPrelaunch($db, $template)
	{
		$this->kern=$db;
		$this->template=$template;
	}
	
	function notify($email)
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
	    {
           $this->template->showErr("This email address is considered invalid.", 1000);
		   return false;
        }
		
		$query="SELECT * FROM notify WHERE email='".$_REQUEST['email']."'";
		$result=$this->kern->execute($query);	
	    if (mysqli_num_rows($result)>0)
		{
			 $this->template->showErr("We already notify this email address.", 1000);
		     return false;
		}
		
		$query="SELECT * FROM notify WHERE IP='".$_SERVER['HTTP_CF_CONNECTING_IP']."'";
		$result=$this->kern->execute($query);	
		if (mysqli_num_rows($result)>0)
		{
			 $this->template->showErr("We already notify this email address.", 1000);
		     return false;
		}
		
		$query="INSERT INTO notify SET email='".$_REQUEST['txt_email']."', IP='".$_SERVER['HTTP_CF_CONNECTING_IP']."', tstamp='".time()."'";
		$this->kern->execute($query);	
		
		$this->template->showOk("We will keep you updated.", 1000);
	}
}
?>