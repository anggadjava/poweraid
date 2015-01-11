(function($){$(function()
{
	var builder = ether && ether.builder ? ether.builder : undefined;
	
	if ( ! ether || ! builder)
		return;

	var utils = ether.utils;
	var obj_foreach = utils.obj_foreach;
	var tooltip = utils.tooltip;
	var dynamic_label = utils.dynamic_label;

	// console.log(ether, builder);

	//custom routines specific to some pages
	var builder_admin_pages =
	{
		builder_settings:
		{
			init_gui: function ()
			{
				var options = { theme: 'dark', tip: 'top' };

				$('#builder-widgets-visibility')
					.find('label').each(function ()
					{
						tooltip.init($(this).find('small'), $.extend({}, options, { 
							$container: $(this).closest('.ether-box')
						}));
					}).end()
					.find('h4').each(function ()
					{
						tooltip.init($(this).find('small'), $.extend({}, options, { 
							$container: $(this).closest('.ether-box')
						}))
					});

				dynamic_label.set($('.ether-box'), false, {
					tooltip_container_selector: '.ether-box',
					tooltip_container_search_method: 'closest' //starts at: $label
				});
			}
		},

		init: function ()
		{
			obj_foreach(null, this, function (key, obj)
			{
				key !== 'init' && obj.init_gui ? obj.init_gui() : '';
			});
		}
	}
	builder_admin_pages.init();

	builder.init({
		hide_visual_tab: ether.hide_visual_tab,
		hide_html_tab: ether.hide_html_tab,
		builder_tab: ether.builder_tab,
	});
});})(jQuery);