// JavaScript Document
function isVisible(el) 
 {
	
     var viewportWidth = jQuery(window).width(),
        viewportHeight = jQuery(window).height(),
        documentScrollTop = jQuery(document).scrollTop(),
        documentScrollLeft = jQuery(document).scrollLeft(),

        $myElement = jQuery(el),

        elementOffset = $myElement.offset(),
        elementHeight = $myElement.height(),
        elementWidth = $myElement.width(),

        minTop = documentScrollTop,
        maxTop = documentScrollTop + viewportHeight,
        minLeft = documentScrollLeft,
        maxLeft = documentScrollLeft + viewportWidth;

        if (
        (elementOffset.top > minTop && elementOffset.top + elementHeight < maxTop) &&
        (elementOffset.left > minLeft && elementOffset.left + elementWidth < maxLeft)
       ) 
	   return true; 
	    else 
	   return false;
 }