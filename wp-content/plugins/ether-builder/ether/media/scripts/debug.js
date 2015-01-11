(function($){

$( function()
{
	var absolute = true;
	
	//$('pre.debug').css('display', 'block');

	if (absolute)
	{
		$('pre.debug').each( function()
		{
			var top = $(this).offset().top - parseInt($('html').css('margin-top'));
			$(this).data('top', top);
			
			$(this).css
			({
				top: top
			});
		});
	}

	$('pre.debug').hide();
	
	if (absolute)
	{
		$('pre.debug').css('position', 'absolute');
	}
	
	if (absolute)
	{
		$('pre.debug').each( function()
		{
			var top = $(this).data('top');
			var $box = $('<div />');
			$box.addClass('debug_toggle');
			
			$box.css
			({
				top: top,
			});
			
			$('body').append($box);
		});
	}
	
	$('.debug_toggle').click( function()
	{
		var index = $(this).index('.debug_toggle');

		$('pre.debug').eq(index).slideToggle();
	});
	
	$(window).keypress( function(e)
	{
		if (e.keyCode == 96)
		{
			$('pre.debug').slideToggle();

			return false;
		}
	});
});

})(jQuery);
