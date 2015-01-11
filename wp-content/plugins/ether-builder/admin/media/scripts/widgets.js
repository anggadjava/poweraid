(function($){$(function()
{

var utils = ether.utils;
var get_field_val = utils.get_field_val;
var builder = ether.builder;
var builder_widget = builder.builder_widget;
var richtext = builder.richtext;
var set_icon = builder.set_icon;
var live_previews = builder.live_previews; //button widget uses live preview as location preview

var get_attr_val = utils.get_attr_val;
var scheduler = utils.scheduler;
var obj_foreach = utils.obj_foreach;
var capitalize = utils.capitalize;
var get_text = utils.get_text;
var batch_tooltip = utils.batch_tooltip;

var Widget = builder_widget.Widget;

var update_gallery_preview = function ($widget)
{
	var $preview = $widget.find('.builder-widget-gallery-preview');
	var $items = $widget.find('.group-item').slice(1);

	$preview.children().slice($items.length).remove();

	$items.each(function (id)
	{
		var $img = $(this).find('img');
		var url = $img.attr('src');
		var alt = $img.attr('alt');
		// var thumb_url = (url.indexOf(builder.thumb_size) === -1 ? url.replace(/(\.\w+)$/, builder.thumb_size + '$1') : url);
		var thumb_url = ( ! url.match(/\/placeholder\.\w+/) && url.indexOf(builder.thumb_size) === -1 ? url.replace(/(\.\w+)$/, builder.thumb_size + '$1') : url);

		if ( ! $preview.children().eq(id).length)
		{
			$preview.append('<img />');
		}

		$preview.children().eq(id)
			.on('error', function ()
			{
				$(this).attr('src', url).off('error');
			})
			.attr('src', thumb_url)
			.attr('alt', alt);
	});
}

var Generic_Container = function ()
{
	
}
Generic_Container.prototype = new Widget();

var Multi = function ()
{
	this.update_active_tab = function ($caller, toggle)
	{
		var $widget = $caller.hasClass('builder-widget-wrapper') ? $caller : $caller.closest('.builder-widget-wrapper');
		var cls = $widget.attr('class');

		cls = cls.match(/builder-widget-type-(tabs|accordion)/);

		if (cls)
		{
			var active_id = $widget.find('.group-item.builder-active-tab').parent().index() + 1;

			this.update_active_group_item($widget, active_id, true, toggle);
		}
	},

	this.update_active_group_item = function ($widget, id, update_field, toggle)
	{
		var $items = $widget.find('.group-item');
		var $field = $widget.find('.builder-tabs-widget-current-field').add($widget.find('.builder-accordion-widget-current-field')); //DIRT

		// console.log('set active group item ', $widget.length, id, update_field, toggle)

		if (id !== '' && id !== -1)
		{
			var $item = $items.eq(id);
			var is_active_item = $item.hasClass('builder-active-tab');
			var toggle_state = (toggle && is_active_item ? false : true);
			
			id = (toggle && is_active_item ? '' : id);

			$items.removeClass('builder-active-tab');
			$item.toggleClass('builder-active-tab', toggle_state);
			$field.val(id);	
		} else
		{
			$items.removeClass('builder-active-tab');
			update_field ? $field.val('') : '';
		}
	},

	this.multi_dashicon = function ($widget, type, icon_field, color_field, bg_color_field)
	{
		var $items = $widget.find('.group-item').slice(1);
		var $preview = $widget.find('.builder-multi-preview');

		$items.each(function (id)
		{
			var icon = $(this).find('.builder-' + type + '-widget-' + icon_field + '-field').val();
			var $icon = $preview.eq(id).find('.builder-multi-preview-icon');

			var color = (color_field ? $(this).find('.builder-' + type + '-widget-' + color_field + '-field').val() : '');
			var bg_color = (bg_color_field ? $(this).find('.builder-' + type + '-widget-' + bg_color_field + '-field').val() : '');

			if (icon.length)
			{
				set_icon($icon, icon);
				$icon
					.css('color', color)
					.css('background-color', bg_color)
					.show();
			} else
			{
				$icon.hide();
			}

			$icon.find('img').hide();
		});
	}

	this.multi_label_theme = function ($widget, type, subtype, color_field, bg_color_field)
	{
		var $items = $widget.find('.group-item').slice(1);
		var $preview = $widget.find('.builder-multi-preview');

		$items.each(function (id)
		{
			var $title = $preview.eq(id).find('.builder-multi-preview-title');
			var color = (color_field ? $(this).find('.builder-' + type + '-widget-' + subtype + '-' + color_field + '-field').val() : '');
			var bg_color = (bg_color_field ? $(this).find('.builder-' + type + '-widget-' + subtype + '-' + bg_color_field + '-field').val() : '');

			$title
				.css('color', color)
				.css('background-color', bg_color);
		});
	}

	this.multi = function ($widget, type, subtype, title_field, content_field)
	{
		var $items = $widget.find('.group-item').slice(1);
		var $wrap = $widget.find('.builder-multi-preview-wrap');
		var wrap_cls = $wrap.attr('class') || '';
		var current_tab = $widget.find('select.builder-' + type + '-widget-current-field').find('option:selected').text();
		var max_h = 0;

		title_field = title_field || 'title';
		content_field = content_field || 'content';

		$items.each(function (id)
		{
			var title = $(this).find('input.builder-' + type + '-widget-' + subtype + '-' + title_field + '-field').val();
			var content = $(this).find('.builder-' + type + '-widget-' + subtype + '-' + content_field + '-field').val();

			content = content.replace(/<\/?[^>]+>/gi, '');

			if ( ! $wrap.children().eq(id).length)
			{
				$wrap.append('<div class="builder-multi-preview"><div class="builder-multi-preview-title"><div class="builder-multi-preview-icon"><img src="" alt="preview icon" /></div><div class="text"></div></div><div class="builder-multi-preview-content"></div>');
			}

			$wrap.find('.builder-multi-preview').eq(id)
				.removeClass('builder-current')
				.children('.builder-multi-preview-title').children('.text').text(title.substring(0, 24)).end().end()
				.children('.builder-multi-preview-content').text(content.substring(0, 128));

			max_h = Math.max(max_h, $wrap.find('.builder-multi-preview').height());
		});

		$wrap.find('.builder-multi-preview').slice($items.length).remove();
		$wrap
			.attr('class', wrap_cls.replace(/\bc\d\b/, ''))
			.addClass('c' + Math.min(6, $items.length))
			.height(max_h);

		if (current_tab !== '')
		{
			$wrap.find('.builder-multi-preview').eq(current_tab - 1).addClass('builder-current');
		}
	}

	this.init = function ()
	{
		var self = this;

		this.init_multi = true

		$('.builder-location, #widgets-right')
			.on('click', '.group-item-active-marker', function ()
			{
				var $widget = $(this).closest('.builder-widget-wrapper');
				var item_id = $(this).parent().parent().index() + 1;

				self.update_active_group_item($widget, item_id, true, true);
			})
			.on('change', '.builder-widget-type-tabs select.builder-tabs-widget-current-field, .builder-widget-type-accordion select.builder-accordion-widget-current-field', function () 
			{
				self.update_active_group_item($(this).closest('.builder-widget-wrapper'), $(this).find('option:selected').val());
			})
			.on('change', 'input.builder-tabs-widget-tabs-icon-color-field, input.builder-accordion-widget-tabs-icon-color-field', function ()
			{
				var $self = $(this);
				scheduler.set('set-tab-icon-color', 150, function ()
				{
					var color = $self.val();
					color.length ? $self.closest('.group-item').find('.builder-widget-icon-field-preview').css('color', color) : '';
				});
			});

		//note
		//using wider scope here that applies to all widgets utilizing Multi,
		//however, at the moment only tabs/accordions make use of the active tab feature
		$('body')
			.on('builder_widget_group_item_remove', function (evt, widget_name, $widget, group_id)
			{
				self.update_active_tab($widget, false);
			})
			.on('builder_widget_group_item_add', function (evt, widget_name, $widget, $group, insert_method)
			{
				insert_method === 'prependTo' ? self.update_active_tab($widget, false) : '';
			})
			.on('builder_widget_group_item_duplicate', function (evt, widget_name, $widget, $group)
			{
				$group.children('.group-item.builder-active-tab').length ? self.update_active_tab($widget, false) : '';
			})
			.on('builder_widget_group_item_sortable_update', function (evt, widget_name, $widget, $caller)
			{
				self.update_active_tab($caller, false);
			});

		batch_tooltip.init({
			$scope: $('.builder-location, #widgets-right'),
			selector: '.group-item-active-marker', 
			content: 'toggle active state of this tab',
			par_y: -0.5,
			style: 'plain',
			size: 'small',
			theme: 'dark',
			tip: 'bottom',
			speed: 100,
			show_delay: 0
		});

		return this;
	}	
}
Multi.prototype = new Widget();

var Group_Item = function ()
{
	this.group_icon = function ($widget, type) //added when implementing image previews for services. unify it when dashicons and such are implemeneted
	{
		var $prototype_item = $widget.find('.group-item').eq(0);
		var $items = $widget.find('.group-item').slice(1);
		var $preview = $widget.find('.builder-multi-preview');
		var $img;
		var placeholder_url = $prototype_item.find('img').attr('src');

		$items.each(function (id)
		{
			var icon = $(this).find('.builder-' + type + '-widget-image-url-field').val();
			var thumb = ( ! icon.match(/\/placeholder\.\w+/) && icon.indexOf(builder.thumb_size) === -1 ? icon.replace(/(\.\w+)$/, builder.thumb_size + '$1') : icon);
			var $icon = $preview.eq(id).find('.builder-multi-preview-icon').eq(0);
			var $img = $icon.children('img');

			if (icon.length)
			{
				if (icon !== $img.src && thumb !== $img.src)
				{
					$img
						.on('load', function ()
						{
							$icon.show();
							$(this).show();
						})
						.on('error', function ()
						{
							$(this).attr('src', icon);
							$(this)
								.off('error')
								.on('error', function ()
								{
									$(this).attr('src', placeholder_url)
								});
						})
						.attr('src', thumb);
				}
			} else
			{
				$icon.hide();
				$img.hide();
			}
		});
	}
}
Group_Item.prototype = new Multi();

var Grid_Slider = function ()
{
	this.summary_get_grid_slider = function ($widget)
	{
		var fields = ['columns', 'rows', 'slider'];
		var as_text_val = ['columns', 'rows'];
	
		return this.parse_fields_summary(this.get_fields_summary($widget, fields, as_text_val));
	}
}
Grid_Slider.prototype = new Group_Item();

var Services = function ()
{
	this.summary_fields = 
	[
		'style',
		{ get_if: 'hide_image', not: true, get: ['type', 'content_align'] },
		{ get_if: 'hide_title', not: true, get: 'title_align'},
		{ get_if: 'hide_text', not: true, get: 'text_align'}
	];
	this.get_text_val = ['style', 'type', 'content_align', 'title_align', 'text_align'];
	this.summary_nodes = ['image_dimensions', 'grid_slider'];

	this.update_preview = function ($widget)
	{
		var $items = $widget.find('.group-item').slice(1);
		var $wrap = $widget.find('.builder-multi-preview-wrap');

		this.multi($widget, 'services', 'service');
		this.group_icon($widget, 'services');
	}
}
Services.prototype = new Grid_Slider();

var Testimonials = function ()
{
	this.summary_fields = ['style'];
	this.get_text_val = ['style'];
	this.summary_nodes = ['grid_slider'];

	this.update_preview = function ($widget)
	{
		var $items = $widget.find('.group-item').slice(1);
		var $wrap = $widget.find('.builder-multi-preview-wrap');

		this.multi($widget, 'testimonials', 'testimonial', 'author');
	}
}
Testimonials.prototype = new Grid_Slider();

var Divider = function ()
{
	this.update_preview = function ($widget)
	{
		var $preview = $widget.find('.builder-widget-divider-preview');
		var live_preview = live_previews.get_preview('divider');

		live_preview.init_widget($widget, function ()
		{
			var $live_preview = this.$preview.find('.ether-divider-preview');

			$preview.children().remove().end().append($live_preview.clone());
		});
	}
}
Divider.prototype = new Widget();

var Video = function ()
{
	this.update_title = function ($widget)
	{
		var title = $widget.find('input.builder-video-widget-url-field').val();

		this.set_title($widget, title);
	}
}
Video.prototype = new Widget();

var Heading = function ()
{
	this.summary_fields = ['type', 'text_align', 'id', 'font_family'];

	this.update_title = function ($widget)
	{
		var $title = this.get_title($widget);
		var $bar = $title.closest('.builder-widget-bar-info');
		var title = $widget.find('input.builder-heading-widget-title-field').val();
		var type = $widget.find('select.builder-heading-widget-type-field').find('option:selected').val();
		var text_align = $widget.find('select.builder-heading-widget-text-align-field').find('option:selected').val();

		var style_props = 
		[
			['color', 'font-style', 'font-weight', 'font-size', 'font-family'],
			['font_color', 'font_style', 'font_weight', 'font_size', 'font_family'],
		];
		var styles = get_attr_val($widget, style_props[1]);
		var style_attr = '';

		// console.log(styles);

		styles.forEach(function (style, id)
		{
			if (style !== '')
			{
				if (style_props[0][id] === 'font-family' && style !== '')
				{
					style_attr += (' ' + style_props[0][id] + ': ' + style + ', serif;');

					WebFont.load({
						google: {
							families: [style]
						}
					});
				} else
				{
					style_attr += (' ' + style_props[0][id] + ': ' + style + ';');
				}
			}

		});

		// console.log(style_attr);

		$title.children('span')
			.text(title.length ? title : 'Heading').attr('class', $title.children('span').attr('class').replace(/builder-h-widget-h\d/, '')).addClass('builder-h-widget-' + type)
			.attr('style', style_attr);

		$bar.attr('class', $bar.attr('class').replace(/\btext-align-\w+\b/, ''));

		if (text_align.length)
		{
			$bar.addClass('text-align-' + text_align);
		}
	}
}
Heading.prototype = new Widget();

var Code = function ()
{
	this.summary_fields = ['type', 'code'];
	this.get_text_val = ['type'];
}
Code.prototype = new Widget();

var Blockquote = function ()
{
	// this.summary_fields = ['style', 'author', 'url', 'text'];

	this.update_preview = function ($widget)
	{
		var $preview = $widget.find('.builder-blockquote-preview-wrap');
		var live_preview = live_previews.get_preview('blockquote');

		live_preview.init_widget($widget, function ()
		{
			var $live_preview = this.$preview.find('.ether-blockquote');

			$preview.children().remove().end().append($live_preview.clone());
		});
	}
}
Blockquote.prototype = new Widget();

var Button = function ()
{
	this.update_preview = function ($widget)
	{
		var $preview = $widget.find('.builder-button-preview-wrap');
		var live_preview = live_previews.get_preview('button');

		live_preview.init_widget($widget, function ()
		{
			var $live_preview = this.$preview.find('.ether-button-preview');

			$preview.children().remove().end().append($live_preview.clone());
		});
	}
}
Button.prototype = new Widget();

var Pricing_Table = function ()
{
	this.summary_fields = ['style', 'table_currency', 'aside'];
	this.get_text_val = ['style'];
	this.summary_nodes = ['grid_slider'];

	this.add_cell_all = function ($trigger)
	{
		var self = this;

		var $widget = $trigger.closest('.builder-widget-wrapper');
		var widget_name = builder_widget.get_name($widget, true);
		var $parent = $trigger.closest('.group-content-wrap');
		var $proto_cell = $parent.find('.group-prototype').find('.prc-table-cell').eq(0).clone();
		var $cells = $parent.find('.prc-table-cells');
		
		$cells.each(function (id)
		{
			var $proto_cell_clone = $proto_cell.clone();

			$(this).append($proto_cell_clone);
			
			$('body')
				.trigger('builder_widget_add_cell', [widget_name, $widget, $(this), $proto_cell_clone, id])
				.trigger('builder_' + widget_name + '_widget_add_cell', [$widget, $(this), $proto_cell_clone, id]);
		});

		$('body')
			.trigger('builder_widget_add_cell_all', [widget_name, $widget])
			.trigger('builder_' + widget_name + '_widget_add_cell', [$widget]);
	},

	this.remove_cell_all = function ($trigger)
	{
		var self = this;

		var $widget = $trigger.closest('.builder-widget-wrapper');
		var widget_name = builder_widget.get_name($widget, true);
		
		if (confirm('Are you sure?'))
		{
			var id = $trigger.closest('.prc-table-cell').index();

			$trigger.closest('.group-content-wrap').find('.prc-table-cells').each(function ()
			{
				$(this).children().eq(id).remove();

				$('body')
					.trigger('builder_widget_remove_cell', [widget_name, $widget, id])
					.trigger('builder_' + widget_name + '_widget_remove_cell', [$widget, id]);
			});
		}

		$('body')
			.trigger('builder_widget_remove_cell_all', [widget_name, $widget])
			.trigger('builder_' + widget_name + '_widget_remove_cell', [$widget]);
	},

	this.move_up_all = function ($trigger)
	{
		var $parent = $trigger.closest('.group-content');
		var $cell = $trigger.closest('.prc-table-cell');
		var id = $cell.index();

		$parent.find('.prc-table-cells').each(function ()
		{
			$cell = $(this).children().eq(id);
			$cell.insertBefore($cell.prev());
		});
	},

	this.move_down_all = function ($trigger)
	{
		var $parent = $trigger.closest('.group-content');
		var $cell = $trigger.closest('.prc-table-cell');
		var id = $cell.index();

		$parent.find('.prc-table-cells').each(function ()
		{
			$cell = $(this).children().eq(id);
			$cell.insertAfter($cell.next());
		});
	},


	this.pricing_table_rows = function ($widget)
	{
		var $items = $widget.find('.group-item').slice(1);
		var $wrap = $widget.find('.builder-multi-preview-wrap');
		var max_h = 0;

		$items.each(function (group_id)
		{
			var $rows_text = $(this).find('input.builder-pricing-table-widget-table-desc-field');
			var $rows_icon = $(this).find('input.builder-pricing-table-widget-table-icon-field');
			var $rows_preview = $wrap.children().eq(group_id).find('.builder-multi-preview-content');

			$rows_preview.text(''); //reset multi method result

			$rows_text.each(function (id)
			{
				var text = $(this).val().replace(/<\/?[^>]+>/gi, '').substring(0, 32);
				var icon = $rows_icon.eq(id).val();

				if ( ! $rows_preview.children().eq(id).length)
				{
					$rows_preview.append('<div class="builder-multi-preview-row ' + (id % 2 == 0 ? 'odd' : 'even') + '"><div class="builder-multi-preview-row-icon"></div><div class="builder-multi-preview-row-text"></div>');
				}

				icon = icon.length > 0 ? icon : 'builder-icon-preview-no-icon';

				$rows_preview.children().eq(id)
					.children('.builder-multi-preview-row-icon').attr('class', 'builder-multi-preview-row-icon ' + icon).end()
					.children('.builder-multi-preview-row-text').text(text).end();
			});

			$rows_preview.children().slice($rows_text.length).remove();

			max_h = Math.max(max_h, $rows_preview.closest('.builder-multi-preview').height());
		});

		$wrap.height(max_h);
	}

	this.update_preview = function ($widget)
	{
		var $items = $widget.find('.group-item').slice(1);
		var $wrap = $widget.find('.builder-multi-preview-wrap');

		this.multi($widget, 'pricing-table', 'table', 'title', 'desc');
		this.multi_dashicon($widget, 'pricing-table', 'table-title-icon', 'table-text-color', 'table-background-color');
		this.multi_label_theme($widget, 'pricing-table', 'table', 'text-color', 'background-color');
		this.pricing_table_rows($widget);
	}

	this.init = function ()
	{
		var self = this;

		$('.builder-location, #widgets-right')
			.on('click', '.builder-widget-type-pricing-table .prc-table-cell-add', function ()
			{
				self.add_cell_all($(this));
			})
			.on('click', '.builder-widget-type-pricing-table .prc-table-cell-remove', function ()
			{
				self.remove_cell_all($(this));
			})
			.on('click', '.builder-widget-type-pricing-table .prc-table-cell-move-up', function ()
			{
				self.move_up_all($(this));
			})
			.on('click', '.builder-widget-type-pricing-table .prc-table-cell-move-down', function ()
			{
				self.move_down_all($(this));
			});

		return this;
	}
}
Pricing_Table.prototype = new Grid_Slider();

var Pricing_Box = function ()
{
	this.summary_fields = ['style'];
	this.get_text_val = ['style'];
	this.summary_nodes = ['grid_slider'];

	this.update_preview = function ($widget)
	{
		var $items = $widget.find('.group-item').slice(1);
		var $wrap = $widget.find('.builder-multi-preview-wrap');

		this.multi($widget, 'pricing-box', 'box');
		this.multi_dashicon($widget, 'pricing-box', 'box-title-icon', 'box-text-color', 'box-background-color');
		this.multi_label_theme($widget, 'pricing-box', 'box', 'text-color', 'background-color');
	}
}
Pricing_Box.prototype = new Grid_Slider();

var Tabs = function ()
{
	this.summary_fields = ['style', 'current', 'user_id', 'tabs_align', 'type'];
	this.get_text_val = ['style', 'current', 'tabs_align', 'type'];

	this.update_title = function ($widget)
	{
		var title = $widget.find('select.builder-tabs-widget-type-field').find('option:selected').text();

		title !== '' ? title = (capitalize(title) + ' Tabs') : '';

		this.set_title($widget, title);
	}

	this.update_preview = function ($widget)
	{
		this.multi($widget, 'tabs', 'tabs');
		this.multi_dashicon($widget, 'tabs', 'tabs-icon', 'tabs-icon-color');
	}
}
Tabs.prototype = new Multi();

var Accordion = function ()
{
	this.summary_fields = ['style', {get_if: 'constrain', get: 'enable_current_toggle'}, 'current', 'user_id'];
	this.get_text_val = ['style', 'current'];

	this.update_preview = function ($widget)
	{
		this.multi($widget, 'accordion', 'tabs');
		this.multi_dashicon($widget, 'accordion', 'tabs-icon', 'icon-color');
	}
}
Accordion.prototype = new Multi();

var Table = function ()
{
	this.summary_fields = ['columns', 'rows', 'style', 'header_top', 'header_left'];
	this.get_text_val = ['columns', 'rows', 'style'];

	this.init = function ()
	{
		$('.builder-location, #widgets-right')
			.on('change', '.builder-widget-type-table input[name*="\[rows\]"], .builder-widget-type-table input[name*="\[columns\]"]', function(e)
			{
				var $parent = $(this).closest('fieldset');
				var $table = $parent.next('fieldset').find('table.table');
				var name = $table.find('input').eq(0).attr('name');

				var rows = parseInt($parent.find('input[name*="\[rows\]"]').val());
				var columns = parseInt($parent.find('input[name*="\[columns\]"]').val());

				if (rows <= 0 || isNaN(rows))
				{
					rows = 1;
				}

				if (rows > 60)
				{
					rows = 60;
				}

				if (columns <= 0 || isNaN(columns))
				{
					columns = 1;
				}

				if (columns > 30)
				{
					columns = 30;
				}

				$parent.find('input[name*="\[rows\]"]').val(rows);
				$parent.find('input[name*="\[columns\]"]').val(columns);

				var table_content = '';

				var _rows = $table.find('tr').length;
				var _columns = $table.find('tr').eq(0).children('td').length;

				if (rows < _rows)
				{
					$table.html($table.find('tr').slice(0, rows));
				} else if (rows > _rows)
				{
					var $clone = $table.find('tr').eq(0).children('td').eq(0).clone();
					$clone.find('input, select, textarea').val('');
					$clone.find('textarea').text('');

					var diff = rows - _rows;

					if (diff > 0)
					{
						for (var i = 0; i < diff; i++)
						{
							var $tr = $('<tr />');

							for (var j = 0; j < _columns; j++)
							{
								$tr.append($clone.clone());
							}

							$table.append($tr);
						}
					}
				}

				if (columns < _columns)
				{
					$table.find('tr').each( function()
					{
						$(this).html($(this).find('td').slice(0, columns));
					});
				} else if (columns > _columns)
				{
					var $clone = $table.find('tr').eq(0).children('td').eq(0).clone();
					$clone.find('input, select, textarea').val('');
					$clone.find('textarea').text('');

					$table.find('tr').each( function()
					{
						var diff = columns - _columns;

						if (diff > 0)
						{
							for (var i = 0; i < diff; i++)
							{
								$(this).append($clone.clone());
							}
						}
					});
				}

				e.preventDefault();

				return false;
			});

		return this;
	}
}
Table.prototype = new Widget();

var Message = function () 
{
	// this.summary_fields = ['title', 'style', 'text', {get_if: 'hide_icon', not: true, get: ['icon_align']}, 'close_button'];
	// this.get_text_val = ['style', 'icon_align'];

	// this.update_icon = function ($widget)
	// {
	// 	$widget.find('.builder-widget-icon').attr('class', 'builder-widget-icon builder-widget-icon-message ether-message ether-message-type-' + $widget.find('select.builder-message-widget-type-field').val());
	// }

	// this.update_title = function ($widget)
	// {
	// 	var title = $widget.find('select.builder-message-widget-type-field').find('option:selected').text();

	// 	this.set_title($widget, title);
	// }

	this.init = function ()
	{
		$('.builder-location, #widgets-right')
			.on('change', '.builder-widget-type-message select.builder-message-widget-type-field', function (evt)
			{
				var $widget = $(this).closest('.builder-widget-wrapper');
				var $preview = $widget.find('.builder-message-widget-type-preview span'); //edit form view
				$preview.attr('class', 'ether-message ether-message-type-' + $(this).val());
			});

		return this;
	}

	this.update_preview = function ($widget)
	{
		var $preview = $widget.find('.builder-message-preview-wrap');
		var live_preview = live_previews.get_preview('message');

		live_preview.init_widget($widget, function ()
		{
			var $live_preview = this.$preview.find('.ether-message-preview');

			$preview.children().remove().end().append($live_preview.clone());
		});
	}
}
Message.prototype = new Widget();

var Image = function () 
{
	this.summary_fields = ['align', 'frame', 'image', {get_if: 'description', get: ['show_img_title', 'img_title_alignment_y']}, 'url'];
	this.get_text_val = ['align', 'frame', 'show_img_title', 'img_title_alignment_y'];
	this.summary_nodes = ['image_dimensions'];

	this.update_preview = function ($widget)
	{
		var $preview = $widget.find('.builder-widget-image-preview');
		var url = $widget.find('input.builder-image-widget-image-field').val();
		url === '' ? url = $widget.find('.preview-img-wrap img').attr('src') : '';
		var thumb_url = ( ! url.match(/\/placeholder\.\w+/) && url.indexOf(builder.thumb_size) === -1 ? url.replace(/(\.\w+)$/, builder.thumb_size + '$1') : url);

		if ( ! $preview.children().length)
		{
			$preview.append('<img />');
		}

		$preview.children('img')
			.on('error', function ()
			{
				$(this).attr('src', url).off('error');
			})
			.attr('src', thumb_url)
	}

	this.init = function ()
	{
		//IMAGE WIDGET
		//has own custom fields for width and alignment; widget width / alignment don't apply here
		// $('.builder-location, #widgets-right')
		$('.builder-location')
			.on('change', 'select.builder-image-widget-align-field', function (evt)
			{
				builder_widget.update_alignment($(this).closest('.builder-widget-wrapper'), $(this).val());
			})
			.on('blur', 'input.builder-image-widget-image-width-field', function (evt)
			{
				builder_widget.update_width($(this).closest('.builder-widget-wrapper'), $(this).val());
			})
			.on('blur', 'input.builder-image-widget-image-crop-width-field', function (evt)
			{
				builder_widget.update_width($(this).closest('.builder-widget-wrapper'), $(this).val());
			});

		return this;
	}
}
Image.prototype = new Widget();

var List = function () 
{
	this.summary_fields = ['list_items_layout', 'text'];
	this.get_text_val = ['list_items_layout'];

	this.edit = function ($widget)
	{
		var icon = get_attr_val($widget, 'icon');
		var $preview = $widget.find('.builder-widget-icon-field-preview').children();

		set_icon($preview, icon);
	},

	this.update_icon = function ($widget)
	{
		var icon = $widget.find('input.builder-list-widget-icon-field').val();

		icon === '' ? icon = 'ether-list-icon ether-list-icon-default' : '';

		$widget.find('.builder-widget-icon').attr('class', 'builder-widget-icon ' + icon);
	}
}
List.prototype = new Widget();

var Gallery = function () 
{
	this.summary_fields = ['front_only', 'height', 'frame', 'disable_lightbox', 'image_mode', 'image_alignment_x_parent', 'image_alignment_y_parent', 'show_img_title'];
	this.get_text_val = ['height', 'frame', 'image_mode', 'image_alignment_x_parent', 'image_alignment_y_parent', 'show_img_title'];
	this.summary_nodes = ['grid_slider'];

	this.update_title = function ($widget)
	{
		var title = 'Items: ' + $widget.find('.group-item').length - 1;

		this.set_title($widget, title);
	},

	this.update_preview = update_gallery_preview;

	this.init = function ()
	{
		return this;
	}
}
Gallery.prototype = new Grid_Slider();

var Custom_Feed = function ()
{
	this.summary_fields = ['style', 'trim_words', 'content_hide', 'button_hide', 'preview_hide', 'post_type', 'taxonomy', 'term', 'orderby', 'numberposts', 'order', 'select'];
	this.get_text_val = ['style', 'trim_words', 'post_type', 'taxonomy', 'term', 'orderby', 'numberposts', 'order', 'select'];
	this.summary_nodes = ['grid_slider'];

	this.init = function ()
	{
		$('.builder-location, #widgets-right')
			.on('change', '.builder-widget-type-custom-feed select[name*="\[taxonomy\]"]', function()
			{
				var $self = $(this).parent();
				var $parent = $(this).closest('.cols');

				var $term = $parent.find('select[name*="\[term\]"]');

				$term.val('');

				var taxonomy = $(this).val();

				if (taxonomy == null)
				{
					taxonomy = '';
				}

				$term.children('option').hide();

				if (taxonomy.length > 0)
				{
					$term.children('option').each( function()
					{
						if ($(this).attr('value').substring(0, taxonomy.length) == taxonomy)
						{
							$(this).show();
						}
					});
				}

				$term.children('option').eq(0).show();
			})
			.on('change', '.builder-widget-type-custom-feed select[name*="\[post_type\]"]', function()
			{
				var $self = $(this).parent();
				var $parent = $(this).closest('.cols');

				var $taxonomy = $parent.find('select[name*="\[taxonomy\]"]');
				$taxonomy.val('');

				var $term = $parent.find('select[name*="\[term\]"]');

				$term.val('');

				var post_type = $(this).val();

				if (post_type == null)
				{
					post_type = '';
				}

				$taxonomy.children('option').hide();

				if (post_type.length > 0)
				{
					$taxonomy.children('option').each( function()
					{
						if ($(this).attr('value').substring(0, post_type.length) == post_type)
						{
							$(this).show();
						}
					});
				}

				$taxonomy.children('option').eq(0).show();

				$term.children('option').hide();

				$term.children('option').eq(0).show();
			});

		return this;
	},

	this.edit = function ($widget)
	{
		var $parent = $widget.find('.builder-widget').eq(0).children('.builder-widget-content');

		var $post_type = $parent.find('select[name*="\[post_type\]"]');
		var $taxonomy = $parent.find('select[name*="\[taxonomy\]"]');
		var $term = $parent.find('select[name*="\[term\]"]');

		var post_type = $post_type.val();
		var taxonomy = $taxonomy.val();
		var term = $term.val();

		$taxonomy.children('option').hide();

		$taxonomy.children('option').each( function()
		{
			if ($(this).attr('value') && $(this).attr('value').substring(0, post_type.length) == post_type)
			{
				$(this).show();
			}
		});

		$taxonomy.children('option').eq(0).show();

		$term.children('option').hide();

		$term.children('option').each( function()
		{
			if ($(this).attr('value').substring(0, taxonomy.length) == taxonomy)
			{
				$(this).show();
			}
		});

		$term.children('option').eq(0).show();
	}
}
Custom_Feed.prototype = new Grid_Slider();

var Rich_Text = function ()
{
	this.summary_fields = ['text'];

	this.edit = function ($widget)
	{
		var $textarea = $widget.find('textarea');

		richtext.init($textarea);
	},

	this.save = function ($widget)
	{
		var $textarea = $widget.find('textarea');
		var val;

		val = richtext.get_content($textarea);

		$textarea
			.text(val)
			.val(val);

		richtext.destroy($textarea, true);
	},

	this.cancel = function ($widget)
	{
		var $textarea = $widget.find('textarea');

		richtext.destroy($textarea, true);
	},

	this.update_data = function ($widget)
	{
		$textarea = $widget.find('textarea');
		$textarea.attr('id', $textarea.attr('name'));
	}
}
Rich_Text.prototype = new Widget();

var Post_Content = function () 
{
	
}
Post_Content.prototype = new Widget();

var Plain_Text = function () 
{
	this.summary_fields	= ['text_align', 'disable_formatting', 'text'];
}
Plain_Text.prototype = new Widget();

var Html = function () 
{
	this.summary_fields = ['html'];
}
Html.prototype = new Widget();

var Post_Feed = function () 
{
	this.summary_fields = ['style', 'trim_words', 'content_hide', 'button_hide', 'preview_hide', 'term', 'orderby', 'numberposts', 'order', 'select'];
	this.get_text_val = ['style', 'trim_words', 'term', 'orderby', 'numberposts', 'order', 'select'];
	this.summary_nodes = ['grid_slider'];
}
Post_Feed.prototype = new Grid_Slider();

var Page_Feed = function () 
{
	this.summary_fields = ['style', 'trim_words', 'content_hide', 'button_hide', 'preview_hide', 'orderby', 'numberposts', 'order', 'select'];
	this.get_text_val = ['style', 'trim_words', 'orderby', 'numberposts', 'order', 'select'];
	this.summary_nodes = ['grid_slider'];
}
Page_Feed.prototype = new Grid_Slider();

var Twitter_Feed = function () 
{
	this.summary_fields = ['twitter_id', 'style', 'max_tweets', 'show_user_name', 'show_user_avatar', 'show_time', 'show_retweet', 'show_interaction', 'show_interaction_mode'];
	this.get_text_val = ['show_interaction_mode'];
	this.summary_nodes = ['grid_slider'];
}
Twitter_Feed.prototype = new Grid_Slider();

var Flickr = function () 
{
	this.summary_fields = ['flickr_id', 'tags', 'count'];
	this.get_text_val = ['count'];
	this.summary_nodes = ['grid_slider'];
}
Flickr.prototype = new Grid_Slider();

var Nivo = function () 
{
	this.summary_fields = [
		'effect', 'anim_speed', 'pause_time', 'pause_on_hover',
		{get_if: 'random_start', not: true, get: ['start_slide']},
		{get_if: 'direction_nav', get: ['prev_text', 'next_text']},
		// {get_if: 'control_nav', get: ['control_nav_thumbs']},
		'control_nav',
		'slices', 'box_cols', 'box_rows'
	];
	this.get_text_val = ['effect', 'anim_speed', 'pause_time'];
	this.summary_node = ['image_dimensions'];

	this.update_preview = update_gallery_preview;
}
Nivo.prototype = new Widget();

var Roundabout = function () 
{
	this.summary_fields = [
		'duration', 'min_scale', 'max_scale', 'min_opacity', 'max_opacity', 
		{get_if: 'autoplay', get: ['autoplay_duration']},
		'image_crop_width', 'image_crop_height'
	];
	this.get_text_val = ['duration', 'min_scale', 'max_scale', 'min_opacity', 'max_opacity', 'autoplay_duration'];
	// this.summary_nodes = ['image_dimensions'];


	this.update_preview = update_gallery_preview;
}
Roundabout.prototype = new Widget();

var Googlemap = function () 
{
	this.summary_fields = ['address', 'show_address', 'view', 'zoom', 'height'];
	this.get_text_val = ['view', 'zoom'];
}
Googlemap.prototype = new Widget();

var Contact = function () 
{
	this.summary_fields = ['email', 'button_text', 'sent_message'];	
}
Contact.prototype = new Widget();

var Template = function () 
{
	this.update_summary = function ($widget)
	{
		var summary = this.auto_summary($widget);

		if ( ! summary || ! summary.length)
		{
			summary = 'No template selected';
			this.set_summary($widget, summary);
		}
	}	
}
Template.prototype = new Widget();

var Style = function () 
{
	this.summary_fields = ['css'];
}
Style.prototype = new Widget();

var Link = function () 
{
	this.summary_fields = ['title', 'url'];	
}
Link.prototype = new Widget();

var Heading_Menu = function () 
{
	this.summary_fields = ['style', 'scrollspy', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];	
	this.get_text_val = ['style'];
}
Heading_Menu.prototype = new Widget();

var Fb_Button = function () 
{
	this.summary_fields = ['appid', 'url', 'color', 'type', 'show_faces', 'width', 'height'];
	this.get_text_val = ['color', 'type'];
}
Fb_Button.prototype = new Widget();

var Fb_Comments = function () 
{
	this.summary_fields = ['appid', 'url', 'color', 'count'];
	this.get_text_val = ['color', 'count'];
}
Fb_Comments.prototype = new Widget();

var Fb_Likebox = function () 
{
	this.summary_fields = ['appid', 'url', 'color', 'show_faces', 'show_stream', 'show_header', 'height'];
	this.get_text_val = ['color'];	
}
Fb_Likebox.prototype = new Widget();

var Form = function () 
{

}
Form.prototype = new Widget();


var Row = function () 
{
	this.update_preview = function ($widget)
	{
		builder.row_widget_utils.update_columns_height($widget);
	}
}
Row.prototype = new Widget();


$('body').on('builder_init', function (evt, builder)
{
	builder.register_widgets({
		widget: Widget,
		multi: Multi,
		group_item: Group_Item,
		grid_slider: Grid_Slider,
		row: Row,
		generic_container: Generic_Container,

		post_content: Post_Content,
		divider: Divider,
		image: Image,
		plain_text: Plain_Text,
		code: Code,
		rich_text: Rich_Text,
		html: Html,
		heading: Heading,
		message: Message,
		blockquote: Blockquote,
		list: List,
		button: Button,
		video: Video,
		post_feed: Post_Feed,
		page_feed: Page_Feed,
		custom_feed: Custom_Feed,
		gallery: Gallery,
		services: Services,
		testimonials: Testimonials,
		table: Table,
		pricing_table: Pricing_Table,
		twitter_feed: Twitter_Feed,
		flickr: Flickr,
		tabs: Tabs,
		accordion: Accordion,
		pricing_box: Pricing_Box,
		nivo: Nivo,
		roundabout: Roundabout,
		googlemap: Googlemap,
		contact: Contact,
		template: Template,
		style: Style,
		link: Link,
		heading_menu: Heading_Menu,
		fb_button: Fb_Button,
		fb_comments: Fb_Comments,
		fb_likebox: Fb_Likebox,
		form: Form
	});
});

});})(jQuery);