(function($){$(function()
{

var builder = ether.builder;
var builder_widget = builder.builder_widget;
var live_previews = builder.live_previews;
var set_icon = builder.set_icon;

var utils = ether.utils;
var scheduler = utils.scheduler;
var obj_foreach = utils.obj_foreach;
var get_attr_val = utils.get_attr_val;
var get_field_val = utils.get_field_val;
var unit = utils.unit;

var Preview = live_previews.Preview;

var Button = function ($preview)
{
	this.PROPS = ['width', 'label', 'url', 'icon', 'icon_align', 'style', 'style_type', 'background', 'color', 'border_radius', 'border_color_top_left', 'border_color_bottom_right'];
	this.SELECT_TEXT_VAL = { align: true, style: true};
	this.DEFAULTS = {
		label: 'Button'
	}
	
	this.$preview = $preview;
	this.props = {};
	this.name = 'button';

	this.update = function (prop, value, show_time)
	{
		var self = this;

		var $preview = this.$preview.find('.ether-button-preview');
		var $icon = $preview.find('.ether-button-preview-icon');
		var $label = $preview.find('.ether-button-preview-label');

		if ( ! $preview.length)
			return;

		typeof prop !== 'object' ? prop = [prop] : '';
		typeof value !== 'object' ? value = [value] : '';

		// console.log('prop: ', prop, '; val: ', value, $preview.length, $icon.length, $label.length)

		prop.forEach(function (p, p_id)
		{
			var v = value[p_id];

			switch (p)
			{
				case 'align':
				{
					$preview.attr('class', $preview.attr('class').replace(/builder-align\w+/, ''))
					$preview.addClass('builder-align' + v.toLowerCase());
					break;
				}
				case 'width':
				{
					$preview.css('width', v == '' ? 'auto' : v);
					break;
				}
				case 'label':
				{
					$label.text(v);
					break;
				}
				case 'icon':
				{
					set_icon($icon, v);
					// v.length ? $icon.show() : $icon.hide();
					break;
				}
				case 'icon_align':
				{
					$preview.toggleClass('ether-button-preview-icon-alignright', v === 'right');
					break;
				}
				case 'style_type': //update names
				{
					$preview.attr('class', $preview.attr('class').replace(/ether-button-preview-style-(\w|-)+/, ''))
					$preview.addClass('ether-button-preview-style-' + v);
					break;
				}
				case 'style': //update names
				{
					// console.log($preview.attr('class').match(/ether-button-style-\w+/, ''), v)
					$preview.attr('class', $preview.attr('class').replace(/ether-button-preview-size-\w+/, ''))
					$preview.addClass('ether-button-preview-size-' + v.toLowerCase());
					break;
				}
				case 'background':
				{
					$preview.css('background-color', v);
					break;
				}
				case 'color':
				{
					$preview.css('color', v);
					break;
				}
				case 'border_radius':
				{
					$preview.css('border-radius', v + 'px');
					break;
				}
				case 'border_color_top_left':
				{
					$preview.css({
						'border-top-color': v,
						'border-left-color': v
					});
					break;
				}
				case 'border_color_bottom_right':
				{
					$preview.css({
						'border-bottom-color': v,
						'border-right-color': v
					});
					break;
				}
			}
		});

		show_time ? this.show_gui(show_time) : '';
	}
}
Button.prototype = new Preview();

var Divider = function ($preview)
{
	this.PROPS = ['width', 'back_to_top', 'back_to_top_alignment', 'back_to_top_title', 'back_to_top_custom_title', 'border_style', 'border_width', 'border_color', 'text_color', 'spacing_top', 'spacing_bottom'];
	this.SELECT_TEXT_VAL = { back_to_top_title: true };
	this.DEFAULTS = {
		back_to_top_title: 'Back to top',
		back_to_top_custom_title: 'Custom title',
		back_to_top_alignment: 'left'
	}
	
	this.$preview = $preview;
	this.props = {};
	this.name = 'divider';

	this.update = function (prop, value, show_time)
	{
		var self = this;

		var $preview = this.$preview.find('.ether-divider-preview');

		var $div = $preview.children();
		var $title = $div.children('span');
		var $hr = $div.children('hr');

		if ( ! $preview.length)
			return;

		typeof prop !== 'object' ? prop = [prop] : '';
		typeof value !== 'object' ? value = [value] : '';

		// console.log('prop: ', prop, '; val: ', value, $preview.length, $icon.length, $label.length)

		prop.forEach(function (p, p_id)
		{
			var v = value[p_id];
			v === undefined ? v = this.get_prop(prop) : '';

			switch (p)
			{
				case 'width':
				{
					$div.css('width', v == '' ? '300px' : v);
					break;
				}
				case 'back_to_top':
				{
					this.update('back_to_top_alignment', undefined, show_time);
					this.update('back_to_top_title', undefined, show_time)

					if ( v )
					{
						$title.show();
					} else
					{
						$title.hide();
					}

					break;
				}
				case 'back_to_top_alignment':
				{
					$div.attr('class', $div.attr('class') ? $div.attr('class').replace(/ether-title-align\w+\b/, '') : '').addClass('ether-title-align' + v);
					break;
				}
				case 'back_to_top_title':
				{
					if (v.toLowerCase() == 'custom')
					{
						this.update('back_to_top_custom_title', undefined, show_time);
						break;					
					}
					$title.html(v);

					break;
				}
				case 'back_to_top_custom_title':
				{
					$title.html(v);

					break;
				}
				case 'border_style':
				{
					$hr.css('border-style', v);
					break;
				}
				case 'border_width':
				{
					$hr.css('border-width', v);
					break;
				}
				case 'border_color':
				{
					$hr.css('border-color', v);
					break;
				}
				case 'text_color':
				{
					$title.css('color', v);
					break;
				}
				case 'spacing_top':
				{
					$div.css('margin-top', unit(v, 'px'));
					break;
				}
				case 'spacing_bottom':
				{
					$div.css('margin-bottom', unit(v, 'px'));
					break;
				}
			}
		}, this);

		show_time ? this.show_gui(show_time) : '';
	}
}
Divider.prototype = new Preview();

var Image =	function ($preview)
{
	this.$preview = $preview;
	this.placeholder_url = this.$preview.find('img').attr('src');

	this.PROPS = ['frame', 'image'];
	this.DEFAULTS = {
		image: this.placeholder_url
	}

	this.props = {};
	this.name = 'image';

	this.update = function (prop, value, show_time)
	{
		var self = this;
		var $preview;

		typeof prop !== 'object' ? prop = [prop] : '';
		typeof value !== 'object' ? value = [value] : '';

		var $preview = this.$preview;

		if ( ! $preview.length)
			return;

		// console.log(prop, value, $preview, $widget);

		prop.forEach(function (p, p_id)
		{
			var v = value[p_id];

			switch (p)
			{
				case 'frame':
				{
					var $frame = $preview.find('.builder-image-preview-frame');
					var cls = $frame.attr('class') || '';

					$frame.attr('class', cls.replace(/ether-preview-frame-(\d|\w)+/, ''))
					v.length || v == true ? $frame.addClass('ether-preview-frame-' + v) : '';

					break;
				}
				case 'image': 
				{
					$preview.find('img').attr('src', v);
					break;
				}
			}
		}, this);

		show_time ? this.show_gui(show_time) : '';;
	}
}
Image.prototype = new Preview();

var Blockquote = function ($preview)
{
	this.PROPS = ['style', 'author', 'url', 'text'];
	this.GROUP_ITEM_PROPS = ['testimonial_author', 'testimonial_url', 'testimonial_content'];
	this.DEFAULTS = {
		testimonial_content: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.',
		text: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.'
	}

	this.props = {};
	this.$preview = $preview;
	this.name = 'blockquote';

	this.update = function (prop, value, show_time)
	{
		var self = this;
		var $preview;

		typeof prop !== 'object' ? prop = [prop] : '';
		typeof value !== 'object' ? value = [value] : '';

		var $preview = this.$preview;

		if ( ! $preview.length)
			return;

		// console.log(prop, value, $preview, $widget);

		$preview = $preview.find('.ether-blockquote');

		prop.forEach(function (p, p_id)
		{
			var v = value[p_id];
			var cls = $preview.attr('class') || '';

			switch (p)
			{
				case 'style':
				{
					$preview.attr('class', cls.replace(/ether-blockquote-style-(\d|\w)+/, ''))
					v.length || v == true ? $preview.addClass('ether-blockquote-style-' + v) : '';

					break;
				}
				case 'author':
				case 'url':
				case 'testimonial_author':
				case 'testimonial_url':
				{
					var url_key = p === 'author' || p === 'url' ? 'url' : 'testimonial_url';
					var author_key = p === 'author' || p === 'url' ? 'author' : 'testimonial_author';
					var url = this.get_prop(url_key) || '';
					var author = this.get_prop(author_key) || '';
					var $quote_meta = $preview.find('.ether-quote-meta');
					
					author.length ? $quote_meta.show().html(url.length ? '<a href="' + url + '">' + author + '</a>' : '<span>' + author + '</span>') : $quote_meta.hide();

					break;
				}
				case 'testimonial_content':
				case 'text':
				{
					$preview.children('.ether-quote-wrap').eq(0).children('p').eq(0).text(v);
					break;
				}
			}
		}, this);

		show_time ? this.show_gui(show_time) : '';
	}
}
Blockquote.prototype = new Preview();

var Testimonials = function ($preview)
{
	this.$preview = $preview;
	this.name = 'testimonials';
}
Testimonials.prototype = new Blockquote();

var Message = function ($preview)
{
	this.PROPS = ['type', 'style', 'hide_icon', 'icon_align', 'title', 'text'];
	this.DEFAULTS = {
		text: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.'
	}

	this.props = {};
	this.$preview = $preview;
	this.name = 'message';

	this.update = function (prop, value, show_time)
	{
		var self = this;
		var $preview;

		typeof prop !== 'object' ? prop = [prop] : '';
		typeof value !== 'object' ? value = [value] : '';

		var $preview = this.$preview;

		if ( ! $preview.length)
			return;

		// console.log(this.get_prop());
		// console.log(prop, value, $preview, $widget);

		$preview = $preview.find('.ether-message-preview');

		prop.forEach(function (p, p_id)
		{
			var v = value[p_id];
			var cls = $preview.attr('class') || '';

			switch (p)
			{
				case 'style':
				{
					$preview.attr('class', cls.replace(/ether-message-style-(\d|\w)+/, ''))
					v.length || v == true ? $preview.addClass('ether-message-style-' + v) : '';

					break;
				}
				case 'type':
				{
					$preview.attr('class', cls.replace(/ether-message-type-(\d|\w)+/, ''))
					v.length || v == true ? $preview.addClass('ether-message-type-' + v) : '';
					break;
				}
				case 'hide_icon':
				{
					// console.log('hide icon:' + v)
					$preview.toggleClass('ether-message-hide-icon', v.length > 0 || v == true);
					break;
				}
				case 'icon_align':
				{
					$preview.attr('class', cls.replace(/ether-message-icon-align-(\d|\w)+/, ''))
					v.length || v == true ? $preview.addClass('ether-message-icon-align-' + v) : '';
					break;
				}
				case 'title':
				{
					$preview.find('h3').toggle(v.length > 0 || v == true);
					break;
				}
				case 'text':
				{
					$preview.find('p').eq(0).text(v);
					break;
				}
			}
		}, this);

		show_time ? this.show_gui(show_time) : '';
	}
}
Message.prototype = new Preview();

var Services = function ($preview)
{
	this.$preview = $preview;
	this.placeholder_url = this.$preview.find('img').attr('src');

	this.PROPS = ['style', 'type', 'hide_title', 'title_on_top', 'title_align', 'content_align', 'text_align', 'hide_text', 'hide_image', 'icon_size'];
	this.GROUP_ITEM_PROPS = ['service_title', 'service_content', 'image_url'];
	this.DEFAULTS = {
		service_title: 'Service Title',
		service_content: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
		image_url: this.placeholder_url
	}

	this.props = {};
	this.name = 'services';

	this.update = function (prop, value, show_time)
	{
		var self = this;
		var $preview;

		typeof prop !== 'object' ? prop = [prop] : '';
		typeof value !== 'object' ? value = [value] : '';

		var $preview = this.$preview;

		if ( ! $preview.length)
			return;

		// console.log(prop, value, $preview, $widget);

		$preview = $preview.find('.ether-services-preview');

		prop.forEach(function (p, p_id)
		{
			var v = value[p_id];
			var cls = $preview.attr('class') || '';

			switch (p)
			{
				case 'style':
				{
					$preview.attr('class', cls.replace(/ether-style-(\d|\w)+/, ''))
					v.length || v == true ? $preview.addClass('ether-style-' + v) : '';

					break;
				}
				case 'type':
				{
					$preview.attr('class', cls.replace(/ether-type-(\d|\w)+/, ''))
					$preview.attr('class', cls.replace(/ether-icon-size-(\d|\w)+/, ''))
					$preview.addClass('ether-type-' + v);
					
					if (v === 'icon')
					{
						$preview.addClass('ether-icon-size-' + this.get_prop('icon_size'));
					}

					break;
				}
				case 'icon_size':
				{
					$preview.attr('class', cls.replace(/ether-icon-size-(\d|\w)+/, ''))
					$preview.addClass('ether-icon-size-' + v);
					break;
				}
				case 'hide_title':
				case 'hide_text':
				case 'hide_image':
				{
					p = p.replace(/_/g, '-');
					$preview.toggleClass('ether-' + p, v);
					break;
				}
				case 'title_align':
				case 'text_align':
				case 'content_align':
				{
					p == 'content_align' ? this.update('title_on_top', this.get_prop('title_on_top'), 6000) : '';

					p = p.replace(/_/g, '-');
					var re = new RegExp('ether-' + p + '-(\\d|\\w)+');
					$preview.attr('class', cls.replace(re, ''))
					$preview.addClass('ether-' + p + '-' + v);
					break;
				}
				case 'title_on_top':
				{
					p = p.replace(/_/g, '-');
					this.get_prop('content_align') !== 'center' ? v = false : '';
					v == true || v == 'true' ? $preview.find('.ether-title').insertBefore($preview.find('.ether-icon')) : $preview.find('.ether-title').insertAfter($preview.find('.ether-icon'));
					$preview.toggleClass('ether-' + p, v);
					break;
				}
				case 'service_title':
				{
					$preview.find('.ether-title').text(v);
					break;
				}
				case 'service_content':
				{
					$preview.find('.ether-content p').text(v);
					break;
				}
				case 'image_url':
				{
					$preview.find('img').attr('src', v);
					break;
				}
			}
		}, this);

		show_time ? this.show_gui(6000) : '';
	}
}
Services.prototype = new Preview();

//NOTE PRCBOX is the first to use same styles as front end (non -preview- interfixed)
//however it utilizes button widget styles that still is interfixed 
var Pricing_Box = function ($preview)
{
	this.PROPS = ['style', 'currency']; 
	this.GROUP_ITEM_PROPS = ['box_title', 'box_title_icon', 'box_title_icon_align', 'box_price_main', 'box_price_tail', 'box_content', 'box_text_color', 'box_background_color', 'box_button_label', 'box_button_size', 'box_button_style', 'box_button_icon', 'box_button_icon_align'];
	this.SELECT_TEXT_VAL = { 'box_button_size': true };
	this.DEFAULTS = {
		box_title: 'Box Title',
		box_content: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam',
		box_price_main: 99,
		box_price_tail: 99,
		box_button_label: 'Button',
		box_text_color: '#000000',
		box_background_color: '#ffffff',
		box_button_size: 'medium',
		box_button_style: 'flat-color'
	}

	this.props = {};
	this.$preview = $preview;
	this.name = 'pricing-box';

	this.update = function (prop, value, show_time)
	{
		var self = this;
		var $preview;

		typeof prop !== 'object' ? prop = [prop] : '';
		typeof value !== 'object' ? value = [value] : '';

		var $preview = this.$preview;

		if ( ! $preview.length)
			return;
		
		var $title = $preview.find('.ether-prcbox-title');
		var $content = $preview.find('.ether-prcbox-desc p');
		var $button = $preview.find('.ether-prcbox-button');

		// console.log('update: ', prop, value, $preview);

		$preview = $preview.find('.ether-prcbox-preview');

		prop.forEach(function (p, p_id)
		{
			var v = value[p_id];
			var cls = $preview.attr('class') || '';
			var button_class = $button.attr('class');

			switch (p)
			{
				case 'style':
				{
					$preview.attr('class', cls.replace(/ether-prcbox-style-(\d|\w)+/, ''))
						.addClass('ether-prcbox-style-' + v);
					break;
				}
				case 'currency':
				{
					$preview.find('.ether-prcbox-currency').text(v);
					break;
				}
				case 'box_title':
				{
					$preview.find('.ether-prcbox-title .text').text(v);
					break;
				}
				case 'box_price_main':
				{
					$preview.find('.ether-prcbox-val-main').text(v);
					break;
				}
				case 'box_price_tail':
				{
					$preview.find('.ether-prcbox-val-tail').text(v);
					break;
				}
				case 'box_title_icon':
				{
					set_icon($preview.find('.ether-prcbox-title .ether-icon'), v, '', function ($icon)
					{
						$icon.closest('.ether-prcbox-title').toggleClass('ether-prcbox-title-no-icon', v.length === 0);
					});
					this.update('box_title_icon_align', this.get_prop('box_title_icon_align'), 6000);
					break;
				}
				case 'box_title_icon_align':
				{
					v === 'left' ? $title.find('.text').insertAfter($title.find('.ether-button-preview-icon')) : $title.find('.text').insertBefore($title.find('.ether-button-preview-icon')); 
					break;
				} 
				case 'box_text_color':
				{
					$title.css('color', v);
					$button.css('color', v);
					break;
				} 
				case 'box_background_color':
				{
					$title.css('background-color', v);
					$button.css('background-color', v);
					break;
				}
				case 'box_button_label':
				{
					$button.find('.text').text(v);
					break;
				} 
				case 'box_button_size':
				{
					! v.length ? v = 'medium' : '';
					v == 'default' ? v = 'medium' : ''; //kind of hax
					v = v.toLowerCase();

					$button
						.attr('class', button_class.replace(/ether-button-preview-size-\w+/, ''))
						.addClass('ether-button-preview-size-' + v);
					break;
				}
				case 'box_button_style':
				{
					! v.length ? v = 'flat-color' : '';

					$button
						.attr('class', button_class.replace(/ether-button-preview-style-(\w|-)+/, ''))
						.addClass('ether-button-preview-style-' + v);
					break;
				}
				case 'box_button_icon':
				{
					set_icon($preview.find('.ether-prcbox-button .ether-button-preview-icon'), v);
					this.update('box_button_icon_align', this.get_prop('box_button_icon_align'), 6000);
					break;
				}
				case 'box_button_icon_align':
				{
					$button
						.attr('class', button_class.replace(/ether-button-preview-icon-align(\w|-)+/, ''))
						.addClass('ether-button-preview-icon-align' + v);
					break;
				}
				case 'box_content':
				{
					$content.text(v.replace(/<\/?[^>]+>/gi, ''));
					break;
				}
			}
		}, this);

		show_time ? this.show_gui(6000) : '';
	}
}
Pricing_Box.prototype = new Preview();

var Pricing_Table = function ($preview)
{
	this.PROPS = ['style', 'currency']; 
	this.GROUP_ITEM_PROPS = ['table_title', 'table_title_icon', 'table_title_icon_align', 'table_price_main', 'table_price_tail', 'table_text_color', 'table_background_color', 'table_button_label', 'table_button_size', 'table_button_style', 'table_button_icon', 'table_button_icon_align', 'table_desc', 'table_icon'];
	this.SELECT_TEXT_VAL = { 'table_button_size': true };
	this.DEFAULTS = {
		table_title: 'Column Title',
		table_title_icon: '',
		table_desc: 'Lorem ipsum dolor sit amet',
		table_icon: '',
		table_price_main: 99,
		table_price_tail: 99,
		table_button_label: 'Button',
		table_text_color: '#000000',
		table_background_color: '#f9f9f9',
		table_button_size: 'medium',
		table_button_style: 'flat-color'
	}

	this.props = {};
	this.$preview = $preview;
	this.name = 'pricing-table';
	
	this.update_table_fields = function (id)
	{
		this.set_props(this.$group_item, ['table_desc', 'table_icon']);

		var fields = this.get_prop('table_desc');
		var icons = this.get_prop('table_icon');
		var $rows_wrap = $preview.find('.ether-prc-fields-wrap');

		! (fields instanceof Array) ? fields = [fields] : '';
		! (icons instanceof Array) ? icons = [icons] : '';

		id ? fields = fields.slice(id, 1) : '';

		// console.log('update table fields: ', fields, icons, id, $rows_wrap.length, $rows_wrap)

		fields.forEach(function (field, id)
		{
			var icon = icons[id];

			if ( ! $rows_wrap.children().eq(id).length)
			{
				$rows_wrap.append('<div class="ether-prc-field ether-prc-row ether-prc-row-' + (id % 2 == 0 ? 'odd' : 'even') + '"><div class="ether-icon"></div><div class="ether-text"></div>');
			}

			icon = icon && icon.length > 0 ? icon : 'builder-icon-preview-no-icon';

			$rows_wrap
				.find('.ether-icon').eq(id).attr('class', 'ether-icon ' + icon).end().end()
				.find('.ether-text').eq(id).text(field);
		});

		$rows_wrap.children().slice(fields.length).remove();
	}

	this.update = function (prop, value, show_time)
	{
		var self = this;
		var $preview;

		typeof prop !== 'object' ? prop = [prop] : '';
		typeof value !== 'object' ? value = [value] : '';

		var $preview = this.$preview;

		if ( ! $preview.length)
			return;
		
		var $title = $preview.find('.ether-prc-title');
		var $button = $preview.find('.ether-prc-button');

		// console.log('update: ', 'prop: ', prop, 'value: ', value, $preview.length);

		$preview = $preview.find('.ether-prc-preview');

		prop.forEach(function (p, p_id)
		{
			var v = value[p_id];
			var cls = $preview.attr('class') || '';
			var button_class = $button.attr('class');

			switch (p)
			{
				case 'style':
				{
					$preview.attr('class', cls.replace(/ether-prc-style-(\d|\w)+/, ''))
						.addClass('ether-prc-style-' + v);
					break;
				}
				case 'currency':
				{
					$preview.find('.ether-prc-currency').text(v);
					break;
				}
				case 'table_title':
				{
					$preview.find('.ether-prc-title .text').text(v);
					break;
				}
				case 'table_price_main':
				{
					$preview.find('.ether-prc-val-main').text(v);
					break;
				}
				case 'table_price_tail':
				{
					$preview.find('.ether-prc-val-tail').text(v);
					break;
				}
				case 'table_title_icon':
				{
					// console.log(p + ' :' + v + ';');
					set_icon($preview.find('.ether-prc-title .ether-icon'), v, '', function ($icon)
					{
						$icon.closest('.ether-prc-title').toggleClass('ether-prc-title-no-icon', v.length === 0);
					});
					this.update('table_title_icon_align', this.get_prop('table_title_icon_align'), 6000);
					break;
				}
				case 'table_title_icon_align':
				{
					v === 'left' ? $title.find('.text').insertAfter($title.find('.ether-button-preview-icon')) : $title.find('.text').insertBefore($title.find('.ether-button-preview-icon')); 
					break;
				} 
				case 'table_text_color':
				{
					$title.css('color', v);
					$button.css('color', v);
					break;
				} 
				case 'table_background_color':
				{
					$title.css('background-color', v);
					$button.css('background-color', v);
					break;
				}
				case 'table_button_label':
				{
					$button.find('.text').text(v);
					break;
				} 
				case 'table_button_size':
				{
					! v.length ? v = 'medium' : '';
					v == 'default' ? v = 'medium' : ''; //kind of hax

					$button
						.attr('class', button_class.replace(/ether-button-preview-size-\w+/, ''))
						.addClass('ether-button-preview-size-' + v);
					break;
				}
				case 'table_button_style':
				{
					! v.length ? v = 'flat-color' : '';

					$button
						.attr('class', button_class.replace(/ether-button-preview-style-(\w|-)+/, ''))
						.addClass('ether-button-preview-style-' + v);
					break;
				}
				case 'table_button_icon':
				{
					set_icon($preview.find('.ether-prc-button .ether-button-preview-icon'), v);
					this.update('table_button_icon_align', this.get_prop('table_button_icon_align'), 6000);
					break;
				}
				case 'table_button_icon_align':
				{
					$button
						.attr('class', button_class.replace(/ether-button-preview-icon-align(\w|-)+/, ''))
						.addClass('ether-button-preview-icon-align' + v);
					break;
				}
				case 'table_desc':
				case 'table_icon':
				{
					this.update_table_fields();
					break;
				}
			}
		}, this);

		show_time ? this.show_gui(6000) : '';
	}
}
Pricing_Table.prototype = new Preview();

var Slider_Nav = function ($preview)
{
	this.PROPS = ['slider', 'navigation', 'theme', 'ctrl_style', 'ctrl_padding', 'ctrl_arrows_pos_x', 'ctrl_arrows_pos_y', 'ctrl_arrows_pos_shift_x', 'ctrl_arrows_pos_shift_y', 'ctrl_arrows_full_width', 'ctrl_arrows_spacing', 'ctrl_pag_pos_x', 'ctrl_pag_pos_y', 'ctrl_pag_pos_shift_x', 'ctrl_pag_pos_shift_y', 'ctrl_pag_spacing'];
	this.DEFAULTS = {
		'ctrl_arrows_spacing': 2,
		'ctrl_pag_spacing': 2
	}

	this.$preview = $preview;
	this.props = {};
	this.name = 'slider_nav';

	this.update = function (prop, value, show_time)
	{
		var self = this;
		var $preview;

		typeof prop !== 'object' ? prop = [prop] : '';
		typeof value !== 'object' ? value = [value] : '';

		var $preview = this.$preview;

		if ( ! $preview.length)
			return;

		var $arrows = $('#builder-slider-preview-arrows');
		var $pag = $('#builder-slider-preview-pag');

		// if (this.get_prop('navigation') > 0 && this.get_prop('slider') == true)
		{
			// show_time ? this.show_gui(show_time) : '';

			this.set_nav_style();

			if (this.set_nav_visibility($arrows, 'arrows'))
			{
				this.set_wide_arrows();
				this.set_nav_spacing($arrows, 'arrows');
				this.set_nav_pos($arrows, 'arrows');
			}

			if (this.set_nav_visibility($pag, 'pag'))
			{
				this.set_nav_spacing($pag, 'pag');
				this.set_nav_pos($pag, 'pag');
			}
		}
		// } else
		// {
			// this.hide_gui();
		// }

		show_time ? this.show_gui(show_time) : '';
	}

	this.set_nav_style = function ()
	{
		var s = this.get_prop('ctrl_style');
		var theme = this.get_prop('theme');

		this.$preview.attr('class', 'builder-preview ether-ctrl-style-' + s + (theme === 'dark' ? '-light' : '') + ' ' + ('builder-slider-preview-theme-' + theme));
	}

	this.set_nav_visibility = function ($elem, type)
	{
		var slider = this.get_prop('slider');
		var nav = this.get_prop('navigation');
		v = ((type === 'arrows' && (nav == 1 || nav == 3)) || type === 'pag' && nav > 1)
		! slider ? v = false : '';
		// console.log('set visibility to:' + v + ';', $elem)
		$elem.toggle(v);
		return v;
	}

	this.set_wide_arrows = function ()
	{
		var v = this.get_prop('ctrl_arrows_full_width');
		var $arrows = $('#builder-slider-preview-arrows');
		$arrows.toggleClass('builder-slider-preview-arrows-wide', v);
	}

	this.set_nav_spacing = function ($elem, type)
	{
		$elem.children().css('margin', parseInt(this.get_prop('ctrl_' + type + '_spacing')));
	}
	
	this.set_nav_pos = function ($elem, type)
	{
		var hpw = $elem.parent().width() / 2;
		var hph = $elem.parent().height() / 2;
		var hw = $elem.width() / 2;
		var hh = $elem.height() / 2;
		var h_align = this.get_prop('ctrl_' + type + '_pos_x');
		var v_align = this.get_prop('ctrl_' + type + '_pos_y');
		var h_shift = parseInt(this.get_prop('ctrl_' + type + '_pos_shift_x'));
		var v_shift = parseInt(this.get_prop('ctrl_' + type + '_pos_shift_y'));
		var pad = parseInt(this.get_prop('ctrl_padding'));
		var wide_arrows = this.get_prop('ctrl_arrows_full_width') && type === 'arrows';

		isNaN(h_shift) ? h_shift = 0 : '';
		isNaN(v_shift) ? v_shift = 0 : '';
		isNaN(pad) ? pad = 0 : '';
		
		/*
		console.log('set nav pos', type, $elem.length, hpw, hph, hw, hh, h_align, v_align, h_shift, v_shift, pad, wide_arrows, {
			left: wide_arrows ? (h_align !== 'center' ? pad + h_shift : 0) : (h_align === 'left' ? pad + h_shift : (h_align === 'right' ? 'auto' : hpw - hw + h_shift)),
			right: wide_arrows ? (h_align !== 'center' ? pad - h_shift : 0) : (h_align === 'right' ? pad + h_shift: 'auto'),
			top: (v_align === 'top' ? pad + v_shift : (v_align === 'bottom' ? 'auto' : hph - hh + v_shift)),
			bottom: (v_align === 'bottom' ? pad + v_shift : 'auto')
		});
		*/
		$elem.css({
			left: wide_arrows ? (h_align !== 'center' ? pad + h_shift : 0) : (h_align === 'left' ? pad + h_shift : (h_align === 'right' ? 'auto' : hpw - hw + h_shift)),
			right: wide_arrows ? (h_align !== 'center' ? pad - h_shift : 0) : (h_align === 'right' ? pad + h_shift: 'auto'),
			top: (v_align === 'top' ? pad + v_shift : (v_align === 'bottom' ? 'auto' : hph - hh + v_shift)),
			bottom: (v_align === 'bottom' ? pad + v_shift : 'auto')
		});

		//hackor
		type === 'arrows' ? this.set_wide_arrows() : '';
	}

	this.init_gui = function ()
	{
		//use simpler model here until unified field slider classes are introduced

		var self = this;

		$('.builder-slider-settings')
			.find('select').live('change', function ()
			{
				self.set_prop_by_field($(this), true);
			}).end()
			.find("input[type='text']").live('blur', function ()
			{
				self.set_prop_by_field($(this), true);
			}).end()
			.find("input[type='checkbox']").live('change', function ()
			{
				self.set_prop_by_field($(this), true);
			}).end()

		//assign to Slider Settings tab atm. Replace when unified slider classes are introduced
		$('.builder-location, #widgets-right').on('click', '.builder-slider-preview-init', function ()
		{
			var $widget = $(this).closest('.builder-widget-wrapper');

			self.init_widget($widget);
		});

		return this;
	}
}
Slider_Nav.prototype = new Preview();

$('body').on('builder_init', function (evt, builder)
{
	builder.register_previews({
		slider_nav: [Slider_Nav, $('#builder-slider-preview')],
		button: [Button, $('#builder-button-preview')],
		image: [Image, $('#builder-image-preview')],
		divider: [Divider, $('#builder-divider-preview')],
		blockquote: [Blockquote, $('#builder-blockquote-preview')],
		message: [Message, $('#builder-message-preview')],
		testimonials: [Testimonials, $('#builder-blockquote-preview')],
		services: [Services, $('#builder-services-preview')],
		pricing_box: [Pricing_Box, $('#builder-prcbox-preview')],
		pricing_table: [Pricing_Table, $('#builder-prc-preview')]
	});
});

});})(jQuery);