// JavaScript Document
function fadeOut(layer, load_page, form)
{
	$('#'+layer).fadeOut(100, 0, 
	function() 
	{
		$('#'+layer).load(load_page, $('#'+form).serialize(), 
		function() 
		{  
		   $('#'+layer).fadeIn(1000);
		});
	});
}

function slide(layer, load_page, form)
{
	$('#'+layer).slideToggle(250, 0, 
	function() 
	{
		$('#'+layer).load(load_page, $('#'+form).serialize(), 
		function() 
		{  
		   $('#'+layer).slideToggle(250);
		});
	});
}