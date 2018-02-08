<?
class CAccounting
{
	function CAccounting($db, $acc, $template)
	{
		$this->kern=$db;
		$this->acc=$acc;
		$this->template=$template;
	}
	
	function viewed()
	{
		$query="UPDATE web_users 
		           SET unread_trans=0 
				 WHERE ID='".$_REQUEST['ud']['ID']."'";
		$this->kern->execute($query);	
	}
	
	
}
?>