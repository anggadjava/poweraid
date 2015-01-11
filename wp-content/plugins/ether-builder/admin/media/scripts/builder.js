function builder_image_widget_change(image)
{
	if (ether.upload_caller != null)
	{
		jQuery(ether.upload_caller).closest('fieldset').find('img.upload_image').attr('src', image);
		jQuery(ether.upload_caller).closest('fieldset').find('input.upload_image').val(image);
	}
}

function builder_gallery_widget_change(image)
{
	if (ether.upload_caller != null)
	{
		jQuery(ether.upload_caller).closest('.group-item').find('img.upload_image').attr('src', image);
		jQuery(ether.upload_caller).closest('.group-item').find('input.upload_image').val(image);
	}
}

function builder_gallery_widget_insert(image)
{
	if (ether.upload_caller != null)
	{
		var $widget = jQuery(ether.upload_caller).closest('.builder-widget-wrapper');
		var widget_name = $widget.attr('class').match(/builder-widget-type-((?:\w|-)+)/)[1];

		var $container = $widget.find('.builder-widget-content').eq(0);
		var $gallery = $container.find('.group-content').children().eq(0);
		var $proto = $container.find('.group-prototype').children().eq(0);
		var $clone = $proto.clone();

		$clone.find('input, textarea, select').val('');
		$clone.find('textarea').text('');
		$clone.find('img').attr('src', image);
		$clone.find('input.upload_image').val(image);

		$gallery.append($clone.show().css('display',''))

		if ( ! $gallery.hasClass('ui-sortable') || ! $gallery.hasClass('ui-sortable-refreshed'))
		{
			$gallery
				.sortable({
					handle: '.group-item-title',
					appendTo: 'parent',
					tolerance: 'pointer',
					delay: 100,
					forceHelperSize: true,
					start: function (evt, ui)
					{
						ui.helper.css({width:ui.item.width() + 60});
						ui.placeholder.css({height: ui.item.children().eq(0).height() + 32});
					}
				})
				.addClass('ui-sortable-refreshed')
		} else
		{
			$gallery.sortable().sortable('refresh');
		}

		jQuery('body')
			.trigger('builder_widget_group_item_add', [widget_name, $widget, $clone, 'insertAfter'])
			.trigger('builder_' + widget_name + '_widget_group_item_add', [$widget, $clone, 'insertAfter']);
	}
}

(function($){$(function()
{
	var utils = ether.utils;
	var toggle_checkbox = utils.toggle_checkbox;
	var dynamic_label = utils.dynamic_label;
	var tooltip = utils.tooltip;
	var batch_tooltip = utils.batch_tooltip;
	var scheduler = utils.scheduler;
	var obj_foreach = utils.obj_foreach;
	var get_attr_val = utils.get_attr_val;
	var cond_fields = utils.cond_fields;
	var get_text = utils.get_text;
	var capitalize = utils.capitalize;
	var get_field_val = utils.get_field_val;
	var builder_lang = ether.builder_lang;

	var tooltip_presets = 
	{
		preset_1: 
		{
			par_y: -1.1,
			style: 'plain',
			size: 'small',
			theme: 'dark',
			tip: 'bottom',
			speed: 100,
			show_delay: 0
		}
	}

	$.expr[':'].icontains = function(obj, index, meta, stack){return (obj.textContent || obj.innerText || jQuery(obj).text() || '').toLowerCase().indexOf(meta[3].toLowerCase()) >= 0;};

	var richtext = 
	{
		prepare: function ()
		{
			if ( typeof tinyMCE == 'undefined')
				return;

			// wordpress 3.9
			if (tinyMCE && typeof tinyMCE.getInstanceById == 'undefined')
			{
				tinyMCE.getInstanceById = function(id)
				{
					var instance = this.get(id);

					if (instance == null)
					{
						return undefined;
					}

					return instance;
				};
			}
		},

		get_content: function ($textarea)
		{
			var id = $textarea.attr('id');
			var instance = tinyMCE.getInstanceById(id);

			return instance ? instance.getContent() : '';
		},

		init: function ($textarea, force)
		{
			var id = $textarea.attr('id');

			if (typeof tinyMCEPreInit != 'undefined' && ! window.builder_tinymce)
			{
				// workaround for wp-includes/js/tinymce/langs/en.js 404 error
				if (typeof tinyMCEPreInit.mceInit.etherbuildereditorfix != 'undefined')
				{
					tinyMCE.init(tinyMCEPreInit.mceInit.etherbuildereditorfix);
				} else if (typeof tinyMCEPreInit.mceInit.content != 'undefined')
				{
					tinyMCE.init(tinyMCEPreInit.mceInit.content);
				} else if (typeof tinyMCEPreInit.mceInit[''] != 'undefined')
				{
					tinyMCE.init(tinyMCEPreInit.mceInit['']);
				}

				window.builder_tinymce = true;
			}

			if (typeof tinyMCE != 'undefined' || force == true)
			{
				tinyMCE.execCommand('mceAddEditor', false, id);
			}
		},

		destroy: function ($textarea, force)
		{
			var id = $textarea.attr('id');

			if (typeof tinyMCE != 'undefined' && typeof tinyMCE.getInstanceById(id) != 'undefined')
			{
				tinyMCE.execCommand('mceFocus', false, id);
				tinyMCE.triggerSave();

				if (force == true)
				{
	    			tinyMCE.execCommand('mceRemoveEditor', false, id);
	    		}
	    	}
		}
	}
	richtext.prepare();

	//no icon: 
	// set_icon($elem);
	//dashicon: 
	// set_icon($elem, icon, 'dashicons');
	//ether-list-icon: 
	// set_icon($elem, icon, 'ether-list-icon');

	function set_icon ($elem, icon, prefix, callback)
	{
		icon = icon || '';
		prefix = prefix || '';

		var cls = $elem.attr('class') || '';

		//transform from
		//widget icon field format:
		//dashicons dashicons-some-icon
		//ether-list-icon ether-list-icon-some-icon
		var split = icon.split(' ');

		if (split.length === 2)
		{
			prefix = split[0];
			icon = split[1].replace(prefix + '-', '');
		}

		// console.log('set icon: "' + icon + '"/"' + prefix + '"; ' + $elem.length);
		
		if (prefix.length)
		{
			if ( ! icon.length)
			{
				var re = new RegExp(prefix + '-' + '(?:\\w|-)+', 'g');

				$elem
					.attr('class', cls.replace(re, ''))
					.removeClass(prefix)
					.addClass('builder-icon-preview-no-icon');
			} else
			{
				set_icon($elem, '', 'ether-list-icon');
				set_icon($elem, '', 'dashicons');

				$elem
					.addClass(prefix + ' ' + prefix + '-' + icon)
					.removeClass('builder-icon-preview-no-icon');
			}
		} else
		{
			if ( ! icon.length)
			{
				set_icon($elem, '', 'ether-list-icon');
				set_icon($elem, '', 'dashicons');
			}
		}

		callback ? callback($elem, icon, prefix) : '';
	}

	var icon_chooser = 
	{
		visible: null,
		$widget: null,
		$trigger: null,
		trigger_pos: null,
		set_icon: null,
		gui_initialized: null,

		set_trigger_pos: function (x, y)
		{
			this.trigger_pos = {x: x, y: y};
		},

		get_trigger_pos: function ()
		{
			return this.trigger_pos;
		},

		set_trigger: function ($trigger, x, y)
		{
			this.$trigger = $trigger;
			this.set_trigger_pos(x, y);
		},

		get_trigger: function ()
		{
			return this.$trigger;
		},

		set_widget: function ($widget)
		{
			this.$widget = $widget;
		},

		get_widget: function ()
		{
			return this.$widget;
		},

		set_gui_pos: function ()
		{
			var pos = this.get_trigger_pos();
			var $b = $('#builder-icon-chooser');
			var $w = $(window);

			pos.x + $b.width() > $w.width() ? pos.x = $w.width() - $b.width() - 24: '';
			pos.y + $b.height() > $w.height() ? pos.y = $w.height() - $b.height() - 24 : '';

			$('#builder-icon-chooser').css({
				top: pos.y,
				left: pos.x
			});
		},

		show_gui: function (force)
		{
			var self = this;

			if ( ! this.visible)
			{
				this.visible = true;
				this.set_gui_pos();
				force ? $('#builder-icon-chooser').stop(true).show().css('opacity', 1) : $('#builder-icon-chooser').fadeIn(500);
				
				$(document).on('click.builder-icon-chooser', function ()
				{
					self.hide_gui();
					$(document).off('click.builder-icon-chooser');
				})
			}
		},

		hide_gui: function ()
		{
			var self = this;

			self.visible = false;
			$('#builder-icon-chooser').stop(true).fadeOut(500);
		},

		set_icon: function (icon, prefix)
		{
			var $trig = this.get_trigger();
			var $parent = $trig.hasClass('builder-widget-icon-field-preview-wrap') ? $trig : $trig.closest('.builder-widget-icon-field-preview-wrap');
			var $icon = $parent.find('.builder-widget-icon-field-preview div').eq(0);
			var $field = $parent.find('.builder-widget-icon-field').eq(0);

			icon = icon || '';
			prefix = prefix || '';

			set_icon($icon, icon, prefix);

			var field_val = (prefix ? (prefix + ' ' + prefix + '-') : '') + icon;

			$field.val(field_val).blur();
		},

		init_gui: function ()
		{
			var self = this;

			if (this.gui_initialized)
				return;

			//general
			$('.builder-location, #widgets-right').on('click', '.builder-widget-icon-field-preview-wrap', function (e)
			{
				self.set_trigger($(this), e.pageX, e.pageY);
				self.set_widget($(this).closest('.builder-widget-wrapper').eq(0));
				self.show_gui(true);

				e.stopPropagation();
			});

			$('body').on('builder_widget_metabox_close', function ()
			{
				self.hide_gui();
			});

			//top bar
			$('#builder-icon-chooser-reset').on('click', function ()
			{
				self.set_icon();
				$('#builder-icon-chooser-close').click();
			});

			$('#builder-icon-chooser-close').on('click', function ()
			{
				self.hide_gui();
			});

			//tabs
			$('.builder-icon-chooser-tab-content').eq($('.builder-icon-chooser-tab.current').index()).show();
			$('.builder-icon-chooser-tab').on('click', function (e)
			{
				if ( ! $(this).hasClass('current'))
				{
					$(this)
						.addClass('current')
						.siblings().removeClass('current');
					
					$('.builder-icon-chooser-tab-content')
						.hide()
						.eq($(this).index()).show();

					self.set_gui_pos();
				}

				e.stopPropagation();
			});

			//icons ui
			$('.builder-icon-chooser-no-icon').on('click', function ()
			{
				self.set_icon();
				$('#builder-icon-chooser-close').click();
			});

			$('#builder-dashicons .dashicons').on('click', function (e)
			{
				var icon = $(this).attr('class').match(/dashicons-((?:\w|-)+)/)[1];

				self.set_icon(icon, 'dashicons');
			});

			$('#ether-list-icons .ether-list-icon').on('click', function (e)
			{
				var icon = $(this).attr('class').match(/ether-list-icon-((?:\w|-|\d)+)/)[1];

				self.set_icon(icon, 'ether-list-icon');
			});

			this.gui_initialized = true;
		}
	}
	icon_chooser.init_gui();

	var builder_widgets_metabox =
	{
		open: false,

		tabs: 
		{
			is_visible: function ($handle)
			{
				$handle.hasClass('.builder-widgets-group-content') ? $handle = $handle.prev() : '';

				return ! $handle.hasClass('closed');
			},

			is_enabled: function ($handle)
			{
				return ! $handle.hasClass('disabled');
			},

			toggle: function ($handle, state)
			{
				if (this.is_enabled($handle))
				{
					state ? this.show($handle) : this.hide($handle);

					$('body').trigger('builder_widgets_metabox_toggle_tab', [$handle, state]);
				}
			},

			show: function ($handle)
			{
				$handle
					.removeClass('closed')
					.children('.state-marker').text('[-]').end()
					.next().stop(true).slideDown(500);

				$('body').trigger('builder_widgets_metabox_show_tab', [$handle]);
			},

			hide: function ($handle)
			{
				$handle
					.addClass('closed')
					.children('.state-marker').text('[+]').end()
					.next().stop(true).slideUp(500);

				$('body').trigger('builder_widgets_metabox_hide_tab', [$handle]);
			},

			disable: function ($handle)
			{
				$handle.addClass('disabled');

				$('body').trigger('builder_widgets_metabox_disable_tab', [$handle]);
			},

			enable: function ($handle)
			{
				$handle.removeClass('disabled');

				$('body').trigger('builder_widgets_metabox_enable_tab', [$handle]);
			},

			init_gui: function ()
			{
				var self = this;

				$('#builder-widgets .builder-widgets-group-title')
					.append('<div class="state-marker dashicons dashicons-arrow-left">[-]</div>')
					.on('click', function ()
					{
						self.toggle($(this), ! self.is_visible($(this)));
					});

				$(window).on('resize', function ()
				{
					scheduler.set('builder-widgets-tabs-resize', 500, function ()
					{
						self.update_all(true);
					});
				})
			},

			update_all: function (forced)
			{
				var self = this;

				$('#builder-widgets .builder-widgets-group-content').each(function ()
				{
					$(this).height('');
					
					var $handle = $(this).prev();
					var visible_count = $(this).children().filter(function ()
					{
						return $(this).css('display') !== 'none';
					}).length;

					if ( ! visible_count && (self.is_visible($handle) || forced))
					{
						self.hide($handle);
						self.disable($handle);
					} else if (visible_count && ( ! self.is_visible($handle) || forced))
					{
						self.show($handle);
						self.enable($handle);
					}
				});
			}
		},

		filter_widgets: function(filter, callback, callback_this)
		{
			var $parent = $('#builder-widgets');
			
			filter = filter.toLowerCase();
			callback_this = callback_this || this;

			if (filter != '')
			{
				$parent.find('.builder-widget-wrapper').filter( function(index)
				{
					var data = $(this).find('.builder-widget-title').text().toLowerCase() + ' ' + $(this).find('.builder-widget-label').text().toLowerCase();

					return data.indexOf(filter) < 0;
				}).hide();

				$parent.find('.builder-widget-wrapper').filter( function(index)
				{
					var data = $(this).find('.builder-widget-title').text().toLowerCase() + ' ' + $(this).find('.builder-widget-label').text().toLowerCase();

					return data.indexOf(filter) >= 0;
				}).show();
			} else
			{
				$parent.find('.builder-widget-wrapper').show();
			}

			callback ? callback.call(callback_this) : '';
		},

		init_metabox: function ($metabox)
		{
			if ( ! this.open)
			{
				this.open = true;

				this.show_metabox($metabox);

				$('body').trigger('builder_widgets_metabox_init', [$metabox]);
			}
		},

		close_metabox: function ($metabox)
		{
			if (this.open)
			{
				this.open = false;

				this.hide_metabox($metabox);

				$('body').trigger('builder_widgets_metabox_close');
			}
		},

		show_metabox: function ($metabox)
		{
			$metabox.show();
			$('#builder-widget-overlay').stop(true, true).fadeTo('fast', 0.9, function()
			{
				$('input[name=builder-widget-filter]').focus();
			});

		},

		hide_metabox: function ($metabox)
		{
			$metabox.hide();
			$('#builder-widget-overlay').stop(true, true).fadeTo('fast', 0.0, function() { 
				$(this).hide(); 
			});
		},

		init_keyboard_ui: function ()
		{
			var self = this;
			
			$(document).keydown( function(e)
			{
				if (e.keyCode == 27)
				{
					self.close_metabox($('#builder-widgets'));
				}
			});
		},

		init_col_icons: function ()
		{
			var tooltip_options = $.extend({}, tooltip_presets.preset_1, {
				y_dir: 1, 
				tip: 'top'
			}) ;

			var cols = 
			{
				'2d3-1': 2,
				'2d3-2': 2,
				'3d4-1': 2,
				'3d4-2': 2,
				'2d4-1': 3,
				'2d4-2': 3,
				'2d4-3': 3
			};

			[1,2,3,4,5,6].forEach(function (id)
			{
				cols[id] = id;
			});

			obj_foreach(null, cols, function (id, col_count)
			{
				var $icon = $('<div>');
				var $widget = $('#builder-widgets').find('.builder-widget-type-row-' + id);
				var a;

				if ( ! $widget.length)
					return;

				$icon
					.addClass('builder-widget-col-icon')
					.addClass('not-responsive cols cols-' + id);

				for (a = 0; a < col_count; a += 1)
				{
					$icon.append('<div class="col"><div class="inner-col"></div></div>');
				}

				$icon.insertAfter($widget.find('.builder-widget-icon'));

				$widget.find('.builder-widget-icon').eq(0).remove();

				tooltip_options.$trigger = $widget;
				tooltip.init($widget.find('.builder-widget-bar-info .builder-widget-title'), $.extend({}, tooltip_options, { 
					par_y: 1,
					$container: $widget.closest('.builder-widgets-wrap'),
					$parent: $widget
				}));
			});
		},

		init_gui: function ()
		{
			var self = this;
			var tooltip_options = 
			{
				y_dir: 1, 
				tip: 'top', 
				theme: 'dark'
			};

			$('#builder-widgets')
				.find('.builder-widget-wrapper')
					.each(function ()
					{
						tooltip.init($(this).find('.ether-tooltip-content-raw'), $.extend({}, tooltip_options, {
							$container: $(this).closest('.builder-widgets-wrap')
						}));
					})
					.on('click', function()
					{
						var $clone = $(this).clone();

						$clone.addClass('builder-widget-before-first-save');
						builder.insert_widget($clone);
						self.close_metabox($('#builder-widgets'));
					}).end()
				.find('.builder-widgets-group-title')
					.each(function ()
					{
						tooltip.init($(this).find('.ether-tooltip-content-raw'), $.extend({}, tooltip_options, {
							$container: $(this).closest('.builder-widgets-wrap')
						}));
					}).end()
				.find('#builder-widgets-close').eq(0)
					.on('click', function ()
					{
						self.close_metabox($('#builder-widgets'));
					}).end().end()
				.find("input[name='builder-widget-filter']")
					.on('keyup', function ()
					{
						var filter = $(this).val();
						var $clear = $('#builder-widgets').find('.clear-filter');

						self.filter_widgets(filter, self.tabs.update_all, self.tabs);

						if (filter.length)
						{
							$clear.show();
						} else
						{
							$clear.hide();
						}
					}).end()
				.find('.clear-filter')
					.on('click', function ()
					{
						$('#builder-widgets')
							.find("input[name='builder-widget-filter']")
								.val('')
								.trigger('keyup');
					})

			this.init_col_icons();
			this.init_keyboard_ui();
			this.tabs.init_gui();
		}
	}
	builder_widgets_metabox.init_gui();

	var builder_widget_tabs = 
	{
		id: 0,

		$widget: null,
		$widget_title_bar: null,
		$widget_content: null,
		$tab_title: null,
		$tab_content: null,

		get_id: function ()
		{
			return this.id;
		},

		next_id: function ()
		{
			this.id += 1;
			return this.id;
		},

		show_tab: function (id, forced)
		{
			var window_pos_y = $(window).scrollTop();
			var $widget = this.$widget;
			var widget_name = builder_widget.get_name($widget);

			if (id === undefined)
				return;
			
			if ( ! this.$tab_title.eq(id).hasClass('ether-current') || forced)
			{
				this.$tab_title
					.removeClass('ether-current')
					.eq(id).addClass('ether-current');

				this.$tab_content
					.filter('.ether-current')
					// .stop(true).fadeOut(250).end()
					// .eq(id).addClass('ether-current').stop(true).fadeIn(250);
					.hide().end()
					.eq(id).addClass('ether-current').show();

				// console.log('show tab: ' + id, this.$tab_content.length);

				$(window).scrollTop(window_pos_y);

				$('body')
					.trigger('builder_widget_metabox_show_tab', [widget_name, $widget, this.$tab_title.eq(id), this.$tab_content.eq(id)])
					.trigger('builder_' + widget_name + '_widget_metabox_show_tab', [$widget, this.$tab_title.eq(id), this.$tab_content.eq(id)]);
			}
		},

		dom_initialized: function ()
		{
			return this.$widget.attr('data-ether-tabs') && this.$widget.attr('data-ether-tabs') === 'set';
		},

		gui_initialized: function ()
		{
			return this.$widget.attr('data-ether-tabs-gui') && this.$widget.attr('data-ether-tabs-gui') === 'set';	
		},

		init_dom: function ()
		{
			var self = this;

			if (this.dom_initialized())
				return false;

			this.$tab_content
				.addClass('ether-tab-content-' + self.get_id())
				.hide();

			this.$tab_title
				.addClass('ether-tab-title-' + self.get_id());

			this.$tab_title.wrapAll('<div class="ether-tab-title-wrap"></div>');
			this.$tab_content.wrapAll('<div class="ether-tab-content-wrap"></div>');

			this.$tab_title.parent().wrap('<div class="ether-tabs ether-tabs-orientation-x ether-tabs-align-left"></div>');
			this.$tab_title.parent().parent().appendTo(this.$widget_title_bar);
			this.$tab_content.parent().wrap('<div class="ether-tabs ether-tabs-orientation-x ether-tabs-align-left"></div>');

			this.$widget.attr('data-ether-tabs', 'set');
			this.next_id();
		},

		// init: function ($widget, visible_tab, show_tab_callback)
		init: function ($widget, visible_tab)
		{
			var self = this;

			// show_tab_callback ? this.show_tab_callback = show_tab_callback : '';
			$widget = $widget.eq(0);

			this.$widget = $widget;

			this.$widget_content = $widget.children('.builder-widget').children('.builder-widget-content').eq(0);
			this.$tab_title = this.$widget_content.find('.ether-tab-title');
			this.$tab_content = this.$widget_content.find('.ether-tab-content');
			this.$widget_title_bar = this.$widget_content.find('.builder-widget-content-bar').eq(0);

			this.init_dom();
			this.init_gui();
			this.show_tab(visible_tab, true);

			// $('body').trigger('builder_widget_tabs_init', [$widget, visible_tab, show_tab_callback]);
			$('body').trigger('builder_widget_tabs_init', [$widget, visible_tab]);

			//console.log('init widget tabs: ', 'widget:', this.$widget.length, 'widget content:', this.$widget_content.length, 'tab title:', this.$tab_title.length, 'tab content:', this.$tab_content.length, 'widget title bar:', this.$widget_title_bar.length)
		},

		// init_gui: function (show_tab_callback)
		init_gui: function ()
		{
			var self = this;

			if (this.gui_initialized())
				return false;

			this.$tab_title
				.on('click', function ()
				{
					self.show_tab($(this).index());
				})
				.on('mouseenter', function (e)
				{
					// self.init($(this).closest('.builder-widget-wrapper'), undefined, show_tab_callback);
					self.init($(this).closest('.builder-widget-wrapper'));
					e.stopPropagation();
				});

			this.$widget.attr('data-ether-tabs-gui', 'set');
		}
	}

	var builder_widget = function ()
	{
		var $widget = null;
		var $widget_clone = null;
		var $group_item_rich = null;
		var $group_item_textarea = null;
		var $group_item_rich_textarea = null;

		var metabox_open = false;
		var group_item_richtext_open = false;

		var Widget = function ()
		{
			this.$widget = null;
			this.summary_fields = null;
			this.get_text_val = null;
			this.summary_nodes = null;
		}

		Widget.prototype.init_widget = function ($widget)
		{
			this.$widget = $widget;
		}

		Widget.prototype.get_title = function ($widget)
		{
			return $widget.find('.builder-widget-bar-info .builder-widget-title').eq(0);
		}

		Widget.prototype.set_title = function ($widget, title)
		{
			title && title != '' ? this.get_title($widget).text(title) : '';
		}

		Widget.prototype.get_summary = function ($widget)
		{
			return $widget.find('.builder-widget-summary').eq(0);
		}

		Widget.prototype.set_summary = function ($widget, content)
		{
			var $summary = this.get_summary($widget);

			if (content && content.length)
			{
				$summary.text(content).show();
			} else
			{
				$summary.hide();
			}
		}

		Widget.prototype.update_icon = function ()
		{

		}
		
		Widget.prototype.update_title = function ()
		{

		}

		Widget.prototype.update_preview = function ()
		{

		}
		
		Widget.prototype.update_summary = function ($widget)
		{
			var summary = this.auto_summary($widget);
			
			if (this.summary_nodes)
			{
				this.summary_nodes.forEach(function (node)
				{
					summary += (', ' + this['summary_get_' + node]($widget));
				}, this);
			}

			this.set_summary($widget, summary);
		}

		Widget.prototype.is_not_empty_summary = function (s, match)
		{
			var result;

			if (Array.isArray(match))
			{
				match.forEach(function (m)
				{
					! result ? result = this.is_not_empty_summary(s, m) : '';
				}, this);

				return result;
				
			} else
			{
				if (
					match !== undefined && s == match ||
					typeof s === 'string' && s.length ||
					typeof s === 'boolean' && s ||
					s.val && this.is_not_empty_summary(s.val) ||
					s.length
					)
				{
					return true;
				}
			}
		},

		Widget.prototype.get_field_summary = function ($widget, name, by_text)
		{
			var field_val = get_attr_val($widget, name, by_text);
			var $label_title, label;
			var o = {};

			$label_title = builder_widget.field_get_label($widget, name);

			if ($label_title)
			{
				$label_title = $label_title.eq(0).children('.label-title').eq(0);
				label = $label_title.text();

				label = get_text($label_title, true, true);

				o.val = field_val;
				o.label = label;
			} else
			{
				o.val = '';
				o.label = '';
				console.warn('field missing! ' + name + ' in', $widget.attr('class').match(/(builder-widget-type-(?:(?:\w|-)+))/)[1]);
			}	

			return o;		
		}

		Widget.prototype.get_fields_summary = function ($widget, fields, get_text_val, constrained)
		{
			var summary = [];
			var fields = fields || this.summary_fields;
			var get_text_val = get_text_val || this.get_text_val || [];

			if ( ! fields)
				return;

			typeof fields === 'string' ? fields = [fields] : '';

			fields.forEach(function (name)
			{
				var s;

				if (constrained && summary.length)
					return;

				if ( typeof name === 'string')
				{
					s = this.get_field_summary($widget, name, $.inArray(name, get_text_val) !== -1);
					this.is_not_empty_summary(s.val) ? summary.push(s) : '';

				} else if (name.get_first)
				{
					s = this.get_fields_summary($widget, name.get_first, get_text_val, true);
					this.is_not_empty_summary(s) ? summary = summary.concat(s) : '';

				} else if (name.get_if && name.get) 
				{
					s = this.get_fields_summary($widget, name.get_if, get_text_val);
					
					if (name.not && ! this.is_not_empty_summary(s) || ! name.not && this.is_not_empty_summary(s))
					{
						Array.isArray(s) ? summary = summary.concat(s) : summary.push(s);
						s = this.get_fields_summary($widget, name.get, get_text_val);
						this.is_not_empty_summary(s) ? summary = summary.concat(s) : '';
					}
				}
			}, this);

			return summary;
		}

		Widget.prototype.parse_field_summary = function (data)
		{
			typeof data.val == 'boolean' ? data.val = (data.val ? 'Yes' : 'No') : '';
			return data.val && data.val !== '' ? capitalize(data.label) + ': ' + capitalize(data.val) : '';
		}

		Widget.prototype.parse_fields_summary = function (data)
		{
			var summary = [];

			if ( ! data)
				return '';

			data.forEach(function (field)
			{
				var s = this.parse_field_summary(field);
				s && s.length ? summary.push(s) : '';
			}, this);

			return summary.join(', ');
		}

		Widget.prototype.auto_summary = function ($widget)
		{
			// console.log(this.parse_fields_summary(this.get_fields_summary($widget)));

			return this.parse_fields_summary(this.get_fields_summary($widget));
		}

		Widget.prototype.summary_get_image_dimensions = function ($widget)
		{
			var fields = [{ get_first: ['image_width', 'image_crop_width']}, { get_first: ['image_height', 'image_crop_height'] }];

			return this.parse_fields_summary(this.get_fields_summary($widget, fields));
		}

		Widget.prototype.update = function ()
		{

		}

		Widget.prototype.init = function ()
		{
			return this;
		}

		Widget.prototype.edit = function ()
		{
			
		}

		return {
			Widget: Widget,

			get_widget: function ($elem)
			{
				return $elem.hasClass('builder-widget-wrapper') ? $elem : $elem.closest('.builder-widget-wrapper');
			},

			get_name: function ($widget, dashes)
			{
				var name = $widget.attr('class').match(/builder-widget-type-((?:\w+|-)+)/)[1];

				dashes ? name = name.replace('-','_') : '';

				return name;
			},

			get_group_items: function ($widget)
			{
				return $widget.find('.builder-widget-content').eq(0).find('.group-item');
			},

			has_group_items: function ($widget)
			{
				return this.get_group_items($widget).length > 0;
			},

			get_field: function ($widget, name)
			{
				return $widget.find('[name*="[' + name + ']"]');
			},

			field_get_label: function ($widget, name)
			{
				var field = this.get_field($widget, name);

				return field.length ? field.closest('label') : undefined;
			},

			is_top_level: function ($widget)
			{
				if ($widget.hasClass('builder-widget-core') || $widget.parent('.builder-location').length > 0)
				{
					return true;
				} else
				{
					return false;
				}
			},

			clone: function ($widget, update_widget_data, append_widget_to_dom)
			{
				var self = this;
				var $clone;

				if ( ! $widget)
					return;

				//clone does not get through sortable destroy properly so destroy it on $widget element before doing anything else
				$widget.find('.ui-sortable').sortable().sortable('destroy');
				$widget.find('.group-content').children().sortable().sortable('destroy');

				//check if a widget contains any other widgets and if so match all widgets
				if ($widget.find('.builder-widget-wrapper').length > 0)
				{
					$widget = $.merge($widget, $widget.find('.builder-widget-wrapper'))
				}

				$clone = $widget.eq(0).clone(true, true);
				$clone = $.merge($clone, $clone.find('.builder-widget-wrapper'));

				$.merge($widget.eq(0), $clone.eq(0)).find('.group-content').children().sortable({
					handle: '.group-item-title',
					appendTo: 'parent',
					tolerance: 'pointer',
					delay: 100,
					forceHelperSize: true,
					start: function (evt, ui)
					{
						ui.helper.css({width:ui.item.width() + 60});
						ui.placeholder.css({height: ui.item.children().eq(0).height() + 32});
					},
					update: function ()
					{
						builder_widgets.get_widget('multi').update_active_tab($(this), false);
					}
				});

				if (append_widget_to_dom === true)
				{
					$('body').trigger('builder_before_insert_widget_clone', [$widget, update_widget_data, append_widget_to_dom]);

					$clone.eq(0).insertAfter($widget.eq(0)).hide().slideDown(500).queue(function ()
					{
						$(this)
							.css('display', 'block')
							.dequeue();

						$('body').trigger('builder_after_insert_widget_clone_anim_end', [$widget, update_widget_data, append_widget_to_dom]);

						if ($widget.parent().hasClass('builder-widget-column'))
						{
							$('body').trigger('builder_after_col_capacity_change', ['add', $widget, $(this)]);
						}
					});

					$('body').trigger('builder_after_insert_widget_clone', [$widget, update_widget_data, append_widget_to_dom]);

					// if ($widget.parent().hasClass('builder-widget-column'))
					// {
					// 	$('body').trigger('builder_after_col_capacity_change', ['add', $widget, $clone]);
					// }
				} 

				if (update_widget_data === true)
				{
					$clone.each(function ()
					{
						widget_id_tracker.update_widget_data($(this));
					});
				}
				
				widget_debug.debug_id();

				$('body').trigger('builder_widget_clone', [self.get_name($widget, true), $widget]);

				return $clone.eq(0);
			},

			unwrap_row: function ($widget, forced)
			{
				if ( ! $widget.hasClass('builder-widget-core'))
					return;

				if ( ! forced && ! confirm(builder_lang.unwrap_row))
					return;

				var $widgets = $widget.find('.builder-widget-wrapper');

				$widgets.insertAfter($widget);
				$widget.remove();

				$widgets.each(function ()
				{
					widget_id_tracker.update_widget_data($(this));
				});
			},

			move_up: function ($widget)
			{
				var $prev = $widget.prev();
				var $core = $widget.parents('.builder-widget-core');

				if ($prev.length) //not first
				{
					$widget.insertBefore($prev);
					widget_id_tracker.update_widget_data($widget.add($prev));
				} else if ($core.length) //nested & first
				{
					$widget.insertBefore($core);
					widget_id_tracker.update_widget_data($widget.add($widget.nextAll()));
				}
			},

			move_down: function ($widget)
			{
				var $next = $widget.next();
				var $core = $widget.parents('.builder-widget-core');

				if ($next.length) //not last
				{
					$widget.insertAfter($next);
					widget_id_tracker.update_widget_data($widget.add($next));
				} else if ($core.length) //nested & last
				{
					$widget.insertAfter($core);
					widget_id_tracker.update_widget_data($core.add($core.nextAll()));
				}
			},

			//used on init_metabox
			//clone is used to restore original values whenever user drops the changes
			set_current_widget: function ($elem)
			{
				$widget = $elem;
				$widget_clone = this.clone($elem);
			},

			get_current_widget: function (get_clone)
			{
				return ! get_clone ? $widget : $widget_clone;
			},

			update_select_length: function ($widget, length)
			{
				var $select = $widget.find('.builder-widget-content').eq(0).find('select[name*="current"], select[name*="view_pos"], select[name*="start_slide"], select[name*="starting_child"]');

				if ( ! $select.length)
				{
					return false;
				}

				var shift = ($select.attr('name').match(/(current|start_slide|starting_child)/) ? 1 : 0);

				//shift hack #2 > count slider groups
				shift === 0 ? length = Math.ceil(length / (get_attr_val($select.parents('fieldset').eq(0), 'rows') * get_attr_val($select.parents('fieldset').eq(0), 'columns'))) : '';

				var $options = $select.children('option');
				var options_length = $options.length;
				var current_option_index = $select.children('option:selected').index();

				if ($options.length > length + shift)
				{
					$select.children().remove().end().append($options.slice(0, length + shift));

					if (current_option_index > length + shift)
					{
						$select.children('option').eq(0).attr('selected', 'selected');
					}
				} else if (options_length - shift < length)
				{
					for (var i = 0; i < length - (options_length - shift); i++)
					{
						$select.append('<option value="' + ((options_length) + i) + '">' + ((options_length - shift) + i + 1) + '</option>');
					}
				}
			},

			update_visibility: function ($widget, state, limit)
			{
				$widget.hasClass('builder-widget') ? $widget = $widget.parent() : '';
				limit = limit || 1;

				$widget.toggleClass('builder-hidden-widget', ! state);
				$widget.find('.builder-widget-visibility-field').each(function ()
				{
					if (limit <= 0)
						return;

					limit -= 1;
					$(this).attr('checked', ! state);
				});

				$('body').trigger('builder_widget_update_visibility', [$widget, state]);
			},

			update_alignment: function ($widget, alignment)
			{
				// console.log('update alignment', alignment, $widget.attr('class'));

				$widget.hasClass('builder-widget') ? $widget = $widget.parent() : '';
				$widget
					.attr('class', $widget.attr('class').replace(/\bbuilder-align\w+\b/, ''))
					.addClass('builder-align' + alignment)

				$('body').trigger('builder_widget_update_alignment', [$widget, alignment]);
			},

			update_width: function ($widget, width)
			{
				$widget.hasClass('builder-widget') ? $widget = $widget.parent() : '';
				width.match(/(\d*)(.*)/)[2] === '' ? width += 'px' : '';
				$widget.css('width', width);

				$('body').trigger('builder_widget_update_width', [$widget, width]);
			},

			update_clearfloat: function ($widget, state)
			{
				$widget.hasClass('builder-widget') ? $widget = $widget.parent() : '';
				$widget.toggleClass('builder-clearfloat-indicator', state);

				$('body').trigger('builder_widget_update_clearfloat', [$widget, state]);
			},

			init_metabox: function ($widget)
			{
				var $textarea = null;
				var $group_content_wrap = null;
				var group_content_wrap_length = null;
				var widget_name = this.get_name($widget, true);

				if ( ! this.metabox_open)
				{
					this.metabox_open = true;

					//$widget clone is created inside this.set_current_widget
					//it's used to revert data to its original state
					//in case user chooses to lose changes
					this.show_metabox($widget);
					this.set_current_widget($widget);

					builder_widget_tabs.init($widget, 0);

					$group_content_wrap = $widget.find('.group-content-wrap').eq(0);
					$group_content_wrap.each(function ()
					{
						if ($(this).find('.group-content').children('.cols').children().length > 0)
						{
							$(this).find('.buttonset-1').eq(1).show();
						} else
						{
							$(this).find('.buttonset-1').eq(1).hide();
						}
					});

					$('body').trigger('builder_widget_metabox_init', [widget_name, $widget]);
				}
			},

			close_metabox: function ($widget)
			{
				var widget_name = this.get_name($widget, true);

				if (this.metabox_open)
				{
					this.metabox_open = false;

					this.set_current_widget(null);
					this.hide_metabox($widget);

					$('body').trigger('builder_widget_metabox_close', [widget_name, $widget]);
				}
			},

			show_metabox: function ($widget)
			{
				$('#builder-widget-overlay').stop(true, true).fadeTo('fast', 0.9);

				$widget.find('.builder-widget').eq(0).children('.builder-widget-content')
					.removeClass('closed')
					.find('input[type!=hidden], select, textarea').eq(0).focus();
			},

			hide_metabox: function ($widget)
			{
				$('#builder-widget-overlay').stop(true, true).fadeTo('fast', 0.0, function() { $(this).hide(); });
				$widget.find('.builder-widget').eq(0).children('.builder-widget-content').addClass('closed');
			},

			cancel: function (evt)
			{
				var $widget = this.get_current_widget();
				var $widget_clone = this.get_current_widget(true);

				var insert_index;
				var $insert_location;

				if ( ! $widget || ! $widget.length)
					return false;

				if (confirm(builder_lang.quit))
				{
					//store clone insert location
					insert_index = $widget.index();
					$insert_location = $widget.parent();

					// console.log(insert_index, $insert_location.length, $insert_location)

					//remove modified widget early 
					//before doing anything else
					//so there's no ID conflicts
					//when manipulating cloned widget
					builder.remove_widget($widget, true);

					$('body').trigger('builder_widget_cancel', [this.get_name($widget, true), $widget]);

					if ( ! $widget_clone.hasClass('builder-widget-before-first-save'))
					{
						// console.log(insert_index, $insert_location.length, $insert_location, $widget_clone.length, $widget_clone)

						if ($insert_location.children().length && $insert_location.children().length > insert_index)
						{
							$widget_clone.insertBefore($insert_location.children().eq(insert_index));
						} else
						{
							$widget_clone.appendTo($insert_location);
						}
					} 
					
					this.close_metabox($widget_clone);
				}

				if (evt)
				{
					evt.preventDefault();
				}
			},

			save: function ($widget, close)
			{
				var $sidebar_inside;

				$widget = $widget || this.get_current_widget();

				$widget.removeClass('builder-widget-before-first-save');
				
				if (close)
				{
					$sidebar_inside = $(this).closest('.widget-inside');

					if ($sidebar_inside.length > 0)
					{
						$sidebar_inside.find('input[name=savewidget]').click();
					}

					this.close_metabox($widget);
				}

				$('body').trigger('builder_widget_save', [this.get_name($widget, true), $widget]);

				if ($widget.parent().hasClass('builder-widget-column'))
				{
					$('body').trigger('builder_after_col_capacity_change', [undefined, $widget.closest('.builder-widget-row')]);
				}
			},

			add_group_item: function ($trigger)
			{
				var $widget = $trigger.closest('.builder-widget-wrapper');
				var widget_name = this.get_name($widget, true);
				var $container = $trigger.closest('.group-content-wrap');
				var $group_items = $container.find('.group-content').children().eq(0);
				var $proto = $container.find('.group-prototype').children().eq(0);
				var $new_elem = $proto.clone();

				var cls = $trigger.attr('class') || '';
				var insert_method = cls.match(/builder-widget-insert-position-(\w+)/);

				insert_method ? insert_method = insert_method[1] : '';
				insert_method = (insert_method === 'before' ? 'prependTo' : 'appendTo');

				$new_elem
					.find('input, textarea, select').val('').end()
					.find('textarea').text('').end()
					[insert_method]($group_items)
					.hide()
					.slideDown(500, function () { $(this).css('display','');})

				$('body')
					.trigger('builder_widget_group_item_add', [widget_name, $widget, $new_elem, insert_method])
					.trigger('builder_' + widget_name + '_widget_group_item_add', [$widget, $new_elem, insert_method]);
			},

			duplicate_group_item: function ($trigger)
			{
				var $widget = $trigger.closest('.builder-widget-wrapper');
				var widget_name = this.get_name($widget, true);
				var $group_item = $trigger.closest('.group-item').parent();
				var $new_elem = $group_item.clone();
				var insert_method = 'insertAfter';

				$new_elem
					.insertAfter($group_item)
					.hide()
					.slideDown(500, function () { $(this).css('display','');})

				$('body')
					.trigger('builder_widget_group_item_add', [widget_name, $widget, $new_elem, insert_method])
					.trigger('builder_' + widget_name + '_widget_group_item_add', [$widget, $new_elem, insert_method])
					.trigger('builder_widget_group_item_duplicate', [widget_name, $widget, $new_elem, insert_method])
					.trigger('builder_' + widget_name + '_widget_group_item_duplicate', [$widget, $new_elem, insert_method]);
			},

			remove_group_item: function ($trigger)
			{
				if ( ! confirm(builder_lang.sure))
					return;

				var self = this;

				var $widget = $trigger.closest('.builder-widget-wrapper');
				var widget_name = this.get_name($widget, true);
				var $container = $trigger.closest('.group-content-wrap');
				var $group_items = $container.find('.group-content').children().eq(0);
				var group_id = $trigger.closest('col').index();

				$trigger.closest('.col')
					.hide(500)
					.queue(function ()
					{
						$(this).remove().dequeue();

						$('body')
							.trigger('builder_widget_group_item_remove', [widget_name, $widget, group_id])
							.trigger('builder_' + self.get_name($widget) + '_widget_group_item_remove', [$widget, group_id]);
					});
			},

			group_item_init_richtext: function ($trigger)
			{
				var self = this;

				var $widget = 		$trigger.closest('.builder-widget-wrapper');
				var $group_item = 	$trigger.closest('.group-item');
				var $form = 		$widget.find('.builder-widget-content-form').eq(0);
				var $textarea = 	$group_item.find('textarea').eq(0);
				var $rich_form;
				var $rich_textarea;
				var $mediabuttons = $('.wp-editor-tools').eq(0).clone();
				var $editor = $('<div class="builder-widget-content-form builder-widget-rich-form"><div class="wp-editor-wrap"><div class="wp-editor-container"><textarea name="builder-rich-content" id="builder-rich-content" cols="15" class="tinymce"></textarea></div></div></div>');

				$mediabuttons
					.find('.wp-switch-editor').remove().end()
					.children().eq(0).removeAttr('id').removeClass('hide').end().end()
					.find('.add_media').attr('name', 'builder-rich-content').end()
					.prependTo($editor);

				$form
					.after($editor)
					.hide();

				$rich_form = $widget.find('.builder-widget-rich-form').eq(0);
				$rich_textarea = $rich_form.find('#builder-rich-content');
				$rich_textarea
					.val($textarea.val())
					.text($textarea.val());

				richtext.init($rich_textarea, true);

				group_item_rich = $group_item;
				group_item_textarea = $textarea;
				group_item_rich_textarea = $rich_textarea;

				group_item_richtext_open = true;

				$('body').trigger('builder_widget_group_item_init_richtext', [self.get_name($widget), $widget]);
			},

			group_item_close_richtext: function ()
			{
				if (group_item_richtext_open)
				{
					group_item_richtext_open = false;
					
					richtext.destroy(group_item_rich_textarea, true);

					group_item_rich = null;
					group_item_textarea = null;
					group_item_rich_textarea = null;

					this.get_current_widget()
						.find('.builder-widget-content-form').eq(0).show().end().end()
						.find('.builder-widget-rich-form').eq(0).remove();
				}
			},

			group_item_save_richtext: function ()
			{
				var v = richtext.get_content(group_item_rich_textarea);

				group_item_textarea
					.text(v)
					.val(v);

				this.group_item_close_richtext();
			},

			group_item_cancel_richtext: function ()
			{
				this.group_item_close_richtext();
			},

			group_items_update: function ($widget, $group_item)
			{
				var $group_items = this.get_group_items($widget);
				var $group_items_wrap = $group_items.closest('.group-content').children().eq(0); //clunky selector

				if ( ! $group_items_wrap.hasClass('ui-sortable') || ! $group_items_wrap.hasClass('ui-sortable-refreshed'))
				{
					$group_items_wrap.sortable({
						handle: '.group-item-title',
						appendTo: 'parent',
						tolerance: 'pointer',
						delay: 100,
						forceHelperSize: true,
						start: function (evt, ui)
						{
							ui.helper.css({width:ui.item.width() + 60});
							ui.placeholder.css({height: ui.item.children().eq(0).height() + 32});
						},
					});
					$group_items_wrap.addClass('ui-sortable-refreshed');
				} else
				{
					$group_items_wrap.sortable().sortable('refresh');
				}

				this.update_select_length($widget, $group_items.length - 1);

				$widget.find('.buttonset-1').eq(1).fadeIn(500);
			},

			trigger_cancel: function (evt)
			{
				if (group_item_richtext_open)
				{
					this.group_item_cancel_richtext();
				} else
				{
					this.cancel(evt);
				}
			},

			trigger_save: function ($widget, close)
			{
				if (group_item_richtext_open)
				{
					this.group_item_save_richtext();
				} else
				{
					this.save(this.get_current_widget(), true);
				}
			},

			init_group_items: function ()
			{
				var self = this;

				var o = $.extend({}, tooltip_presets.preset_1, {
					$scope: $('.builder-location, #widgets-right')
				});

				batch_tooltip.init($.extend({}, o, { selector: '.builder-widget-group-item-duplicate', content: 'duplicate'}));
				batch_tooltip.init($.extend({}, o, { selector: '.builder-widget-group-item-rich', content: 'rich text'}));
				batch_tooltip.init($.extend({}, o, { selector: '.builder-widget-group-item-remove', content: 'remove'}));
				batch_tooltip.init($.extend({}, o, { selector: '.builder-widget-group-item-edit-image', content: 'edit image'}));

				$('.builder-location, #widgets-right')
					.on('click', '.builder-widget-group-item-add', function()
					{
						self.add_group_item($(this));
						return false;
					})
					.on('click', '.builder-widget-group-item-remove', function()
					{
						self.remove_group_item($(this));
						return false;
					})
					.on('click', '.builder-widget-group-item-duplicate', function()
					{
						self.duplicate_group_item($(this));
						return false;
					})
					.on('click', '.builder-widget-group-item-rich', function()
					{
						self.group_item_init_richtext($(this));
						return false;
					})
					.find('.group-content').children().sortable({
						handle: '.group-item-title',
						appendTo: 'parent',
						tolerance: 'pointer',
						delay: 100,
						forceHelperSize: true,
						start: function (evt, ui)
						{
							ui.helper.css({width:ui.item.width() + 60});
							ui.placeholder.css({height: ui.item.children().eq(0).height() + 32});
						},
						update: function ()
						{
							var $widget = self.get_widget($(this));
							var widget_name = self.get_name($widget);

							$('body')
								.trigger('builder_widget_group_item_sortable_update', [widget_name, $widget, $(this)])
								.trigger('builder_' + widget_name + '_widget_group_item_sortable_update', [$widget, $(this)]);
						}
					}).end();

				$('body')
					.on('builder_widget_group_item_add', function (evt, widget_name, $widget, $group_item)
					{
						toggle_checkbox.init($group_item);
						dynamic_label.set($group_item, true);
						self.group_items_update($widget, $group_item);
					})
					.on('builder_widget_group_item_remove', function (evt, widget_name, $widget, group_id)
					{
						// console.log('builder_widget_group_item_remove', arguments)
						var $select = $widget.find('.builder-widget-content').eq(0).find('select[name*="current"], select[name*="view_pos"]');
						var $group_items = self.get_group_items($widget);

						self.update_select_length($widget, $group_items.length - 1);

						if ($group_items.length === 1)
						{
							$widget.find('.buttonset-1').eq(1).fadeOut(250);
						}
					})
					.on('builder_widget_add_cell', function (evt, widget_name, $widget, $cells_wrap, $cell, group_id)
					{
						//group_id === 0: group proto; don't update dynamic label on it
						if (group_id > 0)
						{
							toggle_checkbox.init($cell); 
							dynamic_label.set($cell, true); 
						}
					})
			},

			init_form_fields: function ()
			{
				var self = this;

				$('.builder-location, #widgets-right')
					.on('change', '.builder-widget select', function ()
					{
						var val = $(this).val();

						$(this)
							.children('option[value="' + val + '"]').siblings().removeAttr('selected').end()
							.attr('selected', 'selected');
					})
					.on('keydown', '.builder-widget input', function (evt)
					{
						if (evt.keyCode === 13)
						{
							//$(this).blur();
							return false;
						}
					})
					.on('click', '.builder-widget input[type="checkbox"]', function ()
					{
						$(this).attr('checked', this.checked);
					})
					.on('blur', 'textarea', function ()
					{
						var val = $(this).val();
						$(this).val(val);
						$(this).text(val);
					})
					.on('change', '.builder-widget select[name*="\[term\]"]', function()
					{
						var $self = $(this).parent();
						var $target = $(this).closest('fieldset');

						if ($(this).val() != '')
						{
							$target.next('.sortable-content').hide();
							$self.next('.cols-3').show();
						} else
						{
							$target.next('.sortable-content').show();
							$self.next('.cols-3').hide();
						}
					})
			},

			init_main_menu: function ()
			{
				var self = this;
				var o;

				$('.builder-location')
					.on('click', '.builder-widget-actions .duplicate', function()
					{
						var $widget = $(this).closest('.builder-widget-wrapper').eq(0);

						self.clone($widget, true, true);

						return false;
					})
					.on('click', '.builder-widget-actions .toggle-visibility', function()
					{
						var $widget = $(this).closest('.builder-widget-wrapper').eq(0);
						var current_state = $widget.hasClass('builder-hidden-widget');

						self.update_visibility($widget, current_state);

						return false;
					})
					.on('click', '.builder-widget-actions .unwrap-row', function()
					{
						self.unwrap_row($(this).closest('.builder-widget-wrapper').eq(0));

						return false;
					})
					.on('click', '.builder-widget-actions .move-up', function()
					{
						self.move_up($(this).closest('.builder-widget-wrapper').eq(0));

						return false;
					})
					.on('click', '.builder-widget-actions .move-down', function()
					{
						self.move_down($(this).closest('.builder-widget-wrapper').eq(0));

						return false;
					})
					.on('click', '.builder-widget-actions .remove', function()
					{
						var $widget = $(this).closest('.builder-widget-wrapper').eq(0);

						builder.remove_widget($(this));

						return false;
					})
					.on('mouseenter click', '.builder-widget-actions', function ()
					{
						$(this).toggleClass('active');

						return false;
					})
					.on('mouseleave', '.builder-widget-wrapper', function ()
					{
						$(this).find('.builder-widget-actions').removeClass('active');
					})
					
				$('.builder-location, #widgets-right')
					.on('click', '.builder-widget-actions .edit', function()
					{
						var $widget = $(this).closest('.builder-widget-wrapper').eq(0);

						self.init_metabox($widget);

						return false;
					});

				o = $.extend({}, tooltip_presets.preset_1, {
					$scope: $('.builder-location')
				});

				batch_tooltip.init($.extend({}, o, { selector: '.builder-widget-actions .edit', content: 'edit'}));
				batch_tooltip.init($.extend({}, o, { selector: '.builder-widget-actions .remove', content: 'remove'}));
				batch_tooltip.init($.extend({}, o, { selector: '.builder-widget-actions .duplicate', content: 'duplicate'}));
				batch_tooltip.init($.extend({}, o, { selector: '.builder-widget-actions .unwrap-row', content: 'unwrap row'}));
				batch_tooltip.init($.extend({}, o, { selector: '.builder-widget-actions .move-up', content: 'move up'}));
				batch_tooltip.init($.extend({}, o, { selector: '.builder-widget-actions .move-down', content: 'move down'}));
				batch_tooltip.init($.extend({}, o, { selector: '.builder-widget-actions .toggle-visibility', content: 'toggle-visibility'}));
			},

			init_content_menu: function ()
			{
				var self = this;

				$('.builder-location, #widgets-right')
					.on('click', '.builder-widget-content-actions .save', function()
					{
						var $widget = $(this).closest('.builder-widget-wrapper');

						self.trigger_save($widget, true);

						return false;
					})
					.on('click', '.builder-widget-content-actions .cancel', function (evt)
					{
						self.trigger_cancel(evt);

						return false;
					});
			},

			init_keyboard_ui: function ()
			{
				var self = this;
				
				$(document).keydown( function(e)
				{
					if (e.keyCode == 27)
					{
						self.trigger_cancel();
					}
				});
			},

			init_gui: function ()
			{
				var self = this;

				this.init_main_menu();
				this.init_content_menu();
				this.init_form_fields();
				this.init_group_items();
				this.init_keyboard_ui();

				batch_tooltip.init($.extend({}, tooltip_presets.preset_1, {
					$scope: $('.builder-location'),
					selector: '.builder-column-widget-add',
					par_y: -0.9,
					content: 'add widget'					
				}));

				//WIDGET VISIBILITY / IS_HIDDEN / ALIGNMENT / WIDTH / CLEARFLOAT
				$('.builder-location')
					.on('click', 'input.builder-widget-visibility-field', function ()
					{
						self.update_visibility($(this).closest('.builder-widget-wrapper'), ! this.checked);
					})
					.on('change', 'select.builder-widget-align-field', function ()
					{
						self.update_alignment($(this).closest('.builder-widget-wrapper'), $(this).val());
					})
					.on('blur', 'input.builder-widget-width-field', function ()
					{
						self.update_width($(this).closest('.builder-widget-wrapper'), $(this).val());
					})
					.on('click', 'input.builder-widget-clearfloat-field', function ()
					{
						self.update_clearfloat($(this).closest('.builder-widget-wrapper'), this.checked);
					});

				$('body')
					.on('builder_widget_metabox_show_tab', function (evt, widget_name, $widget, $tab_title, $tab_content)
					{
						cond_fields.init($tab_content);
						toggle_checkbox.init($tab_content);
						dynamic_label.set($tab_content, true);
					})
					.on('builder_widget_metabox_close', function (evt, widget_name, $widget)
					{
						dynamic_label.unset($widget);
					})
					.on('builder_after_insert_widget', function (evt, $widget, insert_method)
					{
						widget_id_tracker.update_widget_data($widget);
						
						if (insert_method == 'after')
						{
							widget_id_tracker.update_widget_row_order($('.builder-location .builder-widget-wrapper'));
						}

						if ( ! $widget.hasClass('builder-widget-core'))
						{
							builder_widget.init_metabox($widget);
						} else
						{
							$('.builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
							{
								$(this).sortable().sortable('enable');
							});
						}

						builder.make_sortable($('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column'));
					})
					.on('builder_before_remove_widget', function (evt, $widget)
					{
						dynamic_label.unset($widget);
						// cond_fields.remove_cond_data($widget);	
					})
					.on('builder_after_remove_widget', function (evt, $next)
					{
						widget_id_tracker.update_widget_row_order($next);
					})
					.on('builder_before_insert_widget_clone', function (evt, $widget, update_widget_data, append_widget_to_dom)
					{
						if ($widget.parent().hasClass('builder-widget-column'))
						{
							$widget.closest('.builder-widget-row').css({
								'height': ''
							});
						}
					})
					.on('builder_after_insert_widget_clone', function (evt, $widget, update_widget_data, append_widget_to_dom)
					{
						widget_id_tracker.update_widget_row_order($widget.eq(0).nextAll());
					})
					.on('builder_after_insert_widget_clone_anim_end', function (evt, $widget, update_widget_data, append_widget_to_dom)
					{
						builder.make_sortable($('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column'));
					})
					.on('ether_cond_group_show', function (evt, $group)
					{
						toggle_checkbox.init($group);
						dynamic_label.set($group);
					})
					.on('builder_after_col_capacity_change', function (evt, mode, $elem)
					{
						// console.log('builder_after_col_capacity_change', evt, mode, $elem.length);

						switch (mode)
						{
							case 'add':
							{
								// builder.row_widget_utils.update_columns_height($elem.parents('.builder-widget-row'));
								break;
							}
							case 'remove':
							{
								builder.row_widget_utils.update_columns_height($elem.parents('.builder-widget-row'));
								break;
							}
							default:
							{
								builder.row_widget_utils.update_columns_height($elem);
							}
						}
					});

				return this;
			}
		}
	}().init_gui();

	var builder_widgets = function ()
	{
		var widgets = {};
		var update_branches = ['icon', 'title', 'summary', 'preview'];
		var common_events = ['save', 'cancel', 'update_data', 'clone']//, 'edit'] //see notes below;

		return {
			register_widget: function (name, constructor, container)
			{
				container = container || widgets;

				if ( ! container[name])
				{
					container[name] = new constructor();
					container[name].hasOwnProperty('init') ? container[name].init() : '';
				} 
			},

			register_widgets: function (data, container)
			{
				obj_foreach(this, data, function (name, constructor)
				{
					this.register_widget(name, constructor, container);
				});
			},		

			get_widget: function (name)
			{
				return widgets[name];
			},

			update_widget_branch: function (widget_name, $widget, branch, silent)
			{
				var widget = this.get_widget(widget_name);
				var widget_update_fn;

				if ( ! widget)
				{
					console.warn('not a valid widget name: ' + widget_name);
					return;
				}

				widget_update_fn = widget['update_' + branch];

				if ( ! widget_update_fn)
					return;

				// console.log('widget update branch', widget_name, $widget.length, branch)

				widget_update_fn.call(widget, $widget);

				! silent ? $('body').trigger('builder_widget_update_' + branch, [widget_name, $widget]) : '';
			},

			update_widget: function (widget_name, $widget, branch, silent)
			{
				widget_name = widget_name.replace(/-/g, '_');

				if (widget_name.substr(0, 3) === 'row')
				{
					widget_name = widget_name.substr(0, 3);
					branch = 'preview';
				}

				if (widget_name.substr(0, 5) === 'form_')
				{
					widget_name = widget_name.substr(5);
				}
				
				// console.log('update widget', widget_name, $widget.length, branch, silent);

				if (branch === 'all')
				{
					update_branches.forEach(function (branch)
					{
						this.update_widget_branch(widget_name, $widget, branch, true);
					}, this);

					! silent ? $('body').trigger('builder_widget_update_' + branch, [widget_name, $widget]) : '';
				} else
				{
					this.update_widget_branch(widget_name, $widget, branch, silent);
				}
			},

			init: function ()
			{
				var self = this;

				common_events.forEach(function (e)
				{
					$('body').on('builder_widget_' + e, function (evt, widget_name, $widget)
					{
						var widget = self.get_widget(widget_name);

						widget && widget[e] ? widget[e]($widget) : '';

					});
				});

				//note that at the moment builder_widget_metabox_init triggers widget.edit rather than init
				//this is on purpouse but maybe adjust the naming later on
				$('body').on('builder_widget_metabox_init', function (evt, widget_name, $widget)
				{
					var widget = self.get_widget(widget_name);

					widget && widget.edit ? widget.edit($widget) : '';
				});

				$('body').on('builder_widget_save', function (evt, widget_name, $widget)
				{
					self.update_widget(widget_name, $widget, 'all', true);
				});

				return this;
			}
		}
	}().init();

	var live_previews = function ()
	{
		var debug = false;
		var freeze_preview = false; //abandon preview hide and alignment if this is set, for debug

		var active_preview = null;
		var previews = {};
		var preview_pos;

		var Preview = function ()
		{
			this.PROPS = [];
			this.GROUP_ITEM_PROPS = [];
			this.DEFAULTS = {};
			this.SELECT_TEXT_VAL = {};

			this.$preview = null;
			this.$widget = null;
			this.$group_item = null;

			this.with_group_items = false;
		}

		Preview.prototype.show_gui = function (hide_after)
		{
			var self = this;

			// this.$preview.show();
			this.$preview.stop(true).fadeIn(500);
			live_previews.set_active_preview(this);

			if (hide_after)
			{
				scheduler.set('hide_' + this.name + '_gui', hide_after, function ()
				{
					self.hide_gui();
				});
			}

			// console.log('show ' + this.name)
		}

		Preview.prototype.hide_gui = function ()
		{
			if ( freeze_preview )
				return;

			// this.$preview.hide();
			
			this.$preview.stop(true).fadeOut(1000);
			live_previews.set_active_preview(null);

			// console.log('hide ' + this.name);
		}

		Preview.prototype.set_prop = function (key, val, id)
		{
			var defaults = this.DEFAULTS;

			(( ! val || val === '') && defaults[key]) ? val = defaults[key] : '';
			
			
			val === 'true' ? val = true : val === 'false' ? val = false : '';

			// console.log('set prop', key, '"' + val + '"', defaults[key]);
			// console.log('set prop: ', 'key: "' + key + '"', 'val: "' + val + '"')
			
			if (id)
			{
				! this.props[key] ? this.props[key] = [] : '';

				if (id === true)
				{
					this.props[key].push(val);
				} else if (typeof id === 'number')
				{
					this.props[key][id] = val;
				}
			} else
			{
				this.props[key] = val;
			}

			return val;
		}

		Preview.prototype.set_prop_by_field = function ($field, update)
		{
			var select_text_val = this.SELECT_TEXT_VAL;
			var prop = $field.attr('name').match(/((\[.*?\]){4})(\[(.*?)\])/);
			prop = prop ? prop[4] : $field.attr('name').match(/(?:\[(.*?)\]){2}/)[1];
			var val = get_field_val($field, select_text_val[prop]);

			// console.log('set prop by field: ', prop, val);
			
			val = this.set_prop(prop, val);
			update ? this.update(prop, val, update === true ? 6000 : update) : '';
		}

		Preview.prototype.set_props = function ($widget, props, vals)
		{
			var select_text_val = this.SELECT_TEXT_VAL;

			props.forEach(function (key)
			{
				var v = get_attr_val($widget, key, select_text_val[key]);
				v = this.set_prop(key, v);
				vals ? vals.push(v) : '';
			}, this);

			return vals;
		}

		Preview.prototype.get_prop = function (key)
		{
			return key ? this.props[key] : this.props;
		}

		Preview.prototype.get_props = function (keys)
		{
			var vals = [];

			keys.forEach(function (key)
			{
				vals.push(this.get_prop(key));
			}, this);

			return vals;
		}

		Preview.prototype.update_group_item = function ($group_item, delay)
		{
			var self = this;
			var vals;

			$group_item = $group_item || this.$group_item || this.$widget;
			delay = delay || 0;

			if ($group_item.hasClass('builder-widget-wrapper'))
			{
				$group_item = builder_widget.get_group_items($widget);
			}

			if ( ! $group_item.length)
				return;

			//reduce result set to single elem
			$group_item = $group_item.eq(Math.max(0, $group_item.length - 1));
			vals = this.set_props($group_item, this.GROUP_ITEM_PROPS, []);

			this.$group_item = $group_item;
			
			// console.log('update group item', vals)

			scheduler.set(this.name + '-group-item-preview', delay, function ()
			{
				self.update(self.GROUP_ITEM_PROPS, vals);
			});
		}

		Preview.prototype.init_widget_field = function ($parent, widget_name, prop_name, evts)
		{
			var self = this;
			var field_selector = '.builder-' + widget_name + '-widget-' + prop_name.replace(/_/g, '-') + '-field';
			var $field = $parent.find(field_selector);

			if ( ! $parent.length || ! $field.length)
				return;

			if ( ! evts || evts === '')
			{
				var tag_name = $field.get(0).tagName;
				
				// evts = 'mouseenter ';
				evts = '';
				evts += (tag_name === 'INPUT' ? 'change blur ' : tag_name = 'SELECT' ? 'change ' : tag_name === 'TEXTAREA' ? 'blur ' : '');
			}

			// console.log('init widget field', $parent.length, $field.length, widget_name, prop_name, evts);

			$('.builder-location, #widgets-right')
				.on(evts, field_selector, function ()
				{
					self.set_prop_by_field($(this), true);
				})
				.on('mouseenter', field_selector, function ()
				{
					var $self = $(this);

					scheduler.set('mouseenter-field-update-prop', 500, function ()
					{
						self.set_prop_by_field($self, true);
					});
				});
		}

		Preview.prototype.init_widget_fields = function ($parent, widget_name, fields)
		{
			var self = this;
			var select_text_val = this.SELECT_TEXT_VAL;

			if ( ! $parent.length)
				return;

			// console.log('init widget fields', $parent.length, widget_name, fields);

			if ( ! (fields instanceof Array))
			{
				obj_foreach(this, fields, function (prop, evts)
				{
					this.init_widget_field($parent, widget_name, prop, evts);
				});
			} else
			{
				fields.forEach(function (prop)
				{
					this.init_widget_field($parent, widget_name, prop);
				}, this);
			}
		}

		Preview.prototype.init_widget = function ($widget, callback)
		{
			this.$widget = $widget;
			this.$group_item = builder_widget.get_group_items($widget);

			var props = this.PROPS.concat(this.GROUP_ITEM_PROPS);
			var vals = [];
			var group_count = this.$group_item.length;
			var widget_name = this.name;

			vals = this.set_props($widget, this.PROPS, []);
			group_count ? vals = this.set_props(this.$group_item.eq(Math.max(0, group_count - 1)), this.GROUP_ITEM_PROPS, vals) : '';

			// console.log(this.$group_item.eq(Math.max(0, group_count - 1)));

			this.update(props, vals);
			this.update_group_item();

			callback ? callback.call(this) : '';

			$('body').trigger('builder_init_widget_preview', [this, $widget]);
		}

		Preview.prototype.deinit_widget = function ()
		{
			this.hide_gui();
			live_previews.set_active_preview(this.name, null);
		}

		Preview.prototype.init_gui = function ()
		{
			var self = this;

			var $parent = $('.builder-widget-type-' + this.name);
			var $group_item = builder_widget.get_group_items($parent);
			var widget_name = this.name;

			// console.log('live_previews: init gui:', this.name, $parent.length, $group_item.length)

			this.init_widget_fields($parent, this.name, this.PROPS);
			this.init_widget_fields($group_item, this.name, this.GROUP_ITEM_PROPS);

			if ($group_item.length)
			{
				$('.builder-location, #widgets-right')
					.on('mouseenter', '.builder-widget-type-' + widget_name + ' .group-item', function(id)
					{
						self.update_group_item($(this), 500);
					});
			}

			return this;
		}

		return {
			Preview: Preview,

			register_preview: function (name, constructor, $bind_to_dom, enable_group_items, container)
			{
				container = container || previews;

				! container[name] ? container[name] = new constructor($bind_to_dom).init_gui(enable_group_items) : '';
			},

			register_previews: function (previews, container)
			{
				obj_foreach(this, previews, function (name, data)
				{
					this.register_preview(name, data[0], data[1], data[2], container);
				});
			},		

			get_preview: function (name)
			{
				return name ? previews[name] : previews;
			},
			
			hide_active_preview: function ()
			{
				active_preview ? active_preview.hide_gui() : '';
			},

			set_active_preview: function (preview)
			{
				if (preview)
				{
					if(active_preview && active_preview.name !== preview.name)
					{
						this.hide_active_preview();
					}

					active_preview = preview;
				} else
				{
					if (active_preview)
					{
						active_preview = null;
					}
				}
			},

			get_active_preview: function ()
			{
				return active_preview;
			},

			get_preview_pos: function ()
			{
				return preview_pos;
			},

			set_preview_pos: function (pos)
			{
				preview_pos = pos;
			},

			update_preview_pos: function (evt, force)
			{
				var preview = this.get_active_preview();

				if ( freeze_preview )
					return;

				if ( ! preview)
					return;

				var $preview = preview.$preview;
				var preview_pos, mx, hw, pos;

				if (force)
				{
					$preview.toggleClass('builder-preview-left', ! $preview.hasClass('builder-preview-left'));
					this.set_preview_pos($preview.hasClass('.builder-preview-left') ? 'left' : 'right');
				} else
				{
					preview_pos = this.get_preview_pos();
					mx = evt.pageX;
					hw = $(window).width() / 2; 

					mx > hw + 50 ? pos = 'left' : mx < hw - 50 ? pos = 'right' : '';
					
					if (pos && pos !== preview_pos)
					{
						$preview.toggleClass('builder-preview-left', pos === 'right' ? false : true);
						this.set_preview_pos(pos);
					}
				}
			},

			reset_preview_pos: function ()
			{
				var preview = this.get_active_preview();

				if (preview)
				{
					this.get_active_preview().$preview.toggleClass('builder-preview-left', false);
					this.set_preview_pos('right');
				}
			},

			init: function ()
			{
				var self = this;

				$(document).on('mousemove', function (evt)
				{
					self.update_preview_pos(evt);
				});

				$('#builder-previews .builder-preview').on('mouseenter', function (evt)
				{
					self.update_preview_pos(evt, true);
				});

				$('body')
					.on('builder_widget_metabox_init', function (evt, widget_name, $widget)
					{
						var preview = self.get_preview(widget_name);

						if (preview)
						{
							preview.init_widget($widget, function ()
							{
								preview.show_gui(6000);
							});
						}
					})
					.on('builder_widget_metabox_close', function ()
					{
						self.hide_active_preview();
					})
					.on('builder_widget_group_item_init_richtext', function ()
					{
						self.hide_active_preview();
					})
					.on('builder_init_widget_preview', function (evt, preview, $widget)
					{
						self.reset_preview_pos();
					});

				return this;
			}
		}	
	}().init();

	var widget_debug = function ()
	{
		return {

			get_widget_fields_id: function ($widget)
			{
				var ID;
				var id_mismatch;
				var fields_data = [];

				var $fields_wrap = $widget.children('.builder-widget-content') //make explicit selection to avoid matching too many elements
				var $fields = $fields_wrap
					.find('input').add($fields_wrap.find('textarea')).add($fields_wrap.find('select'))
					.add($widget.children('input')).add($widget.children('textarea')).add($widget.children('select'))

				$fields.each(function ()
				{
					var name = $(this).attr('name');
					var id = name.match(/(?:\[.*?\]){3}\[(\d+)\]/)[1];

					// var id = name.match(/\[(\d+)\]\[[\w-]+\]$/); //doesn't work on testimonial_author due to an extra []
					// console.log(name, id, $(this));
					// id !== null ? id = id[1] : '';

					fields_data.push([id, name]);

					if ( ! ID)
					{
						ID = id;
					} else if (id !== id)
					{
						console.error('Incorrect field id: ' + id + '; should be: ' + ID);
						id_mismatch = true;
						return;
					}
				});

				! id_mismatch ? console.log('Field IDs ok: ' + $fields.length + 'x' + ID) : console.error('Field IDs mismatch', fields_data, $(this));

				return ID;
			},

			update_widget_debug_info: function ($widget, info, duplicate_id)
			{
				$widget.hasClass('.builder-widget-wrapper') ? $widget = $widget.children('.builder-widget').eq(0) : '';

				var $widget_top_bar = $widget.children('.builder-widget-bar').eq(0);
				var $debug_name_attr = $widget_top_bar.eq(0).children('.debug-name-attr').eq(0);

				$debug_name_attr.length > 0 ? $debug_name_attr.text(info) : $widget_top_bar.prepend('<div class="debug-name-attr">' + info + '</div>');

				if (duplicate_id)
				{
					$widget_top_bar.children('.debug-name-attr').eq(0).attr('style', 'background: red !important;');
				}
			},

			debug_id: function ()
			{
				var self = this;
				var ID = [];

				if ( ! builder.enable_widget_debug)
				{
					return false;
				}

				$('.builder-location .builder-widget').each(function ()
				{
					var duplicate_id;
					var id = self.get_widget_fields_id($(this));

					if ( $.inArray(id, ID) !== -1 )
					{
						duplicate_id = true;
					}

					self.update_widget_debug_info($(this), id, duplicate_id);
				});
			}
		}
	}()

	var widget_id_tracker = function ()
	{
		var ID_LIST = {};
		
		return {
			next_id: function (list)
			{
				list = list || ID_LIST;

				var id = new Date().getTime() + Math.floor(Math.random() * 999999999);
				if ( ! list[id])
				{
					list[id] = id;
					return id;
				} else
				{
					return this.next_id();
				}
			},

			register_id: function (id)
			{
				id ? ((typeof id === 'string' || typeof id === 'number') ? ID_LIST[id] = id : ID_LIST = id) : '';
			},

			get_registered_id: function ()
			{
				return ID_LIST;
			},

			update_widgets_id_list: function ()
			{
				var self = this;

				$data = this.get_widget_data($('#builder-location-main .builder-widget-wrapper'), true);

				$data.each(function ()
				{
					var id;
					$name = $(this).attr('name');

					id = $name.match(/((\[.*?\]){3})(\[(.*?)\])/)[4];

					self.register_id(id);
				});
			},

			update_widget_data: function ($widget)
			{
				var self = this;

				if ($widget.length > 1)
				{
					$widget.each(function ()
					{
						self.update_widget_data($(this));
					});

					return;
				}

				//console.log('update widget data')
				var $data = $widget.find('input[name^=ether_builder_widget], select[name^=ether_builder_widget], textarea[name^=ether_builder_widget], button[name^=ether_builder_widget]');

				var new_id = this.next_id();
				var attrs = ['name', 'id'];

				$data.each( function()
				{
					var $self = $(this);

					attrs.forEach(function (key)
					{
						if (typeof $self.attr(key) != 'undefined' && $self.attr(key) !== false)
						{
							var data = 'ether_builder_widget[__LOCATION__][__ROW__][__COLUMN__][__ID__][__SLUG__]';
							var current_data = /\[(.*?)\]\[(.*?)\]\[(.*?)\]\[(.*?)\]\[(.*?)\]/.exec($self.attr(key));

							if (current_data != null)
							{
								var is_array = false;

								if ($self.attr(key).length > 2)
								{
									is_array = ($self.attr(key).substring($self.attr(key).length - 2) == '[]');
								}

								if (current_data.length == 6)
								{
									data = data.replace(/__SLUG__/g, current_data[5]);
									data = data.replace(/__ID__/g, new_id);
								}

								// if (current_data.length == 6 && current_data[4] != '__ID__')
								// {
								// 	data = data.replace(/__ID__/g, current_data[4]);
								// } else
								// {
								// 	data = data.replace(/__ID__/g, new_id);
								// }

								//account for generic-container here as well
								var $row = $self.closest('div[class*=builder-widget-type-row]');
								//generic container actually has additional class of builder-widget-type-row-generic-container so this is no longer needed
								// if ( ! $row.length)
								// {
								// 	$row = $self.closest('.builder-widget-type-generic-container]');
								// }

								if ($row.length > 0)
								{
									data = data.replace(/__ROW__/g, $row.index());

									var $column = $self.closest('div.builder-widget-column');

									if ($column.length > 0)
									{
										data = data.replace(/__COLUMN__/g, $column.index());
									}
								} else
								{
									data = data.replace(/__ROW__/g, $self.closest('.builder-widget-wrapper').index());
								}

								var $location = $self.closest('.builder-location-wrapper:not(.read-only) .builder-location');

								if ($location.length > 0)
								{
									data = data.replace(/__LOCATION__/g, $location.attr('id').replace('builder-location-', ''));
								}

								$self.attr(key, data + (is_array ? '[]' : ''));
							}
						}
					});
				});

				$widget.addClass('initialized');

				$('body').trigger('builder_widget_update_data', [builder_widget.get_name($widget), $widget]);

				widget_debug.debug_id();
			},

			get_widget_data: function ($widget, deep)
			{
				var $settings;
				var $location;
				var $data;

				if (deep === true)
				{
					$settings = $widget.find('input[name^=ether_builder_widget], select[name^=ether_builder_widget], textarea[name^=ether_builder_widget], button[name^=ether_builder_widget]');
					$location = [];
				} else
				{
					$settings = $widget.find('.builder-widget-content-form').eq(0).find('input[name^=ether_builder_widget], select[name^=ether_builder_widget], textarea[name^=ether_builder_widget], button[name^=ether_builder_widget]');
					$location = $widget.find('.builder-widget').eq(0).find('input[name^=ether_builder_widget]');
				}
				var $data = $.merge($settings, $location);

				return $data;
			},

			update_widget_col_order: function ($widget)
			{
				var self = this;

				if ($widget.length > 1)
				{
					$widget.each(function ()
					{
						self.update_widget_col_order($(this));
					});

					return;
				}

				//console.log('update widget col order')
				var id = $widget.parent('.builder-widget-column').length > 0 ? $widget.parent('.builder-widget-column').index() : '__COLUMN__';

				$widget.each(function ()
				{
					var $data = self.get_widget_data($(this));

					$data.each(function ()
					{
						$name = $(this).attr('name');
						$(this).attr('name', $name.replace(/((\[.*?\]){2})(\[(.*?)\])/, '$1[' + id + ']'));
					});

					$('body').trigger('builder_widget_update_data', [builder_widget.get_name($(this)), $(this)]);
				});

				widget_debug.debug_id();
			},

			update_widget_row_order: function ($widget)
			{
				var self = this;

				if ($widget.length > 1)
				{
					$widget.each(function ()
					{
						self.update_widget_row_order($(this));
					});

					return;
				}

				//console.log('update widget row order')
				var row_id;

				//work only on this elem if it's not top level
				if ( ! builder_widget.is_top_level($widget.eq(0)))
				{
					$widget = $widget.eq(0);
					row_id = $widget.parents('.builder-widget-core').index();
				} else
				{
					row_id = $widget.eq(0).index();
				}

				$widget.each(function (elem_id)
				{
					var is_top_level = builder_widget.is_top_level($(this));
					var $data = self.get_widget_data($(this), true);

					if (is_top_level && elem_id !== 0)
					{
						row_id += 1;
					}

					$data.each(function ()
					{
						var name = $(this).attr('name');

						$(this).attr('name', name.replace(/((\[.*?\]){1})(\[(.*?)\])/, '$1[' + row_id + ']'));
					});

					$('body').trigger('builder_widget_update_data', [builder_widget.get_name($(this)), $(this)]);
				})

				widget_debug.debug_id();
			},

			init: function ()
			{
				this.update_widgets_id_list();

				return this;
			}
		}
	}().init();

	var builder = function () 
	{
		return {
			enable_widget_debug: false,

			$insert_target: null,
			insert_position: null,

			thumb_size: null,
			hide_visual_tab: false,
			hide_html_tab: false, 
			widget_mousedown: false,
			widget_drag: false,

			builder_widgets: null,
			live_previews: null,

			ctrl_down: false,

			user_control: function ()
			{
				return this.ctrl_down;
			},

			register_widget: function (name, constructor, container)
			{
				this.builder_widgets ? this.builder_widgets.register_widget(name, constructor, container) : '';
			},

			register_widgets: function (data)
			{
				this.builder_widgets ? this.builder_widgets.register_widgets(data) : '';
			},

			register_preview: function (name, constructor, container)
			{
				this.live_previews ? this.live_previews.register_preview(name, constructor, container) : '';
			},

			register_previews: function (data)
			{
				this.live_previews ? this.live_previews.register_previews(data) : '';
			},

			set_widget_insert_target: function ($trigger)
			{
				var cls;
				var origin;
				var position;
				var index;

				cls = $trigger.attr('class') || '';

				if (origin = cls.match(/builder-(\w+)-widget-add/))
				{
					origin = origin[1];
					position = cls.match(/builder-widget-insert-position-(\w+)/)[1];
					this.insert_position = position;
				}

				if (origin === 'location')
				{
					builder.$insert_target = $('.builder-location-wrapper:not(.read-only) #builder-location-main');
				} else if (origin === 'column')
				{
					index = $trigger.closest('.builder-widget-column-options').index();
					this.$insert_target = $trigger.closest('.builder-widget').find('.builder-widget-row').eq(0).find('.builder-widget-column').eq(index);
				}

				// console.log(this.insert_position, this.$insert_target.length);
			},

			update_builder_location_width: function ()
			{
				//need this or widgets with explicitly set width overflow and break layout; 
				//pure css max-width/width combination doesn't work in this case

				var width = $('#builder-location-wrapper').width() - 4; 

				$('.builder-location').each(function ()
				{
					$(this).width(width);
				});
			},

			row_widget_utils: 
			{
				debug: false,
				//this effectively sets .builder-widget-row height rather than the columns themselves
				//.builder-widget-column elements auto adjust due to css declaration of height: 100%
				update_columns_height: function ($scope, id, height)
				{
					var self = this;

					this.debug && id !== undefined ? console.log('update row widget', 'scope:',$scope.length, 'col id:', id, 'height:', height) : '';

					if ( ! $scope.hasClass('builder-widget-row'))
					{
						$scope = $scope.find('.builder-widget-row');
					}

					$scope.each(function ()
					{
						var h = height;

						//set row height unconditionally when no id is given
						if (id === undefined)
						{
							if (h === undefined)
							{
								$(this).height('auto');

								h = $(this).height();
							}

							$(this).height(h);

						//update height only if id is the tallest col
						//othherwise let all columns remain equal and max-height
						} else 
						{
							if (self.is_tallest_col($(this), id))
							{
								self.update_columns_height($(this));
								return;
							}
						}
					});
				},

				is_tallest_col: function ($row, id)
				{
					var self = this;

					var $cols = $row.find('.builder-widget-column');
					var col_height;
					var row_height;
					var result = true;

					this.debug ? console.group('is tallest col', id) : '';

					$cols.height('auto');
					col_height = $cols.eq(id).height();

					$cols.each(function (i)
					{
						self.debug ? console.log(i, $(this).height()) : '';

						if (i !== id)
						{
							if ($(this).height() > col_height)
							{
								result = false;
							}
						}
					});

					this.debug ? console.groupEnd() : '';

					$cols.css('height', '100%');

					return result;
				}
			},

			make_sortable: function ($elem)
			{
				var self = this;

				$elem.sortable().sortable('destroy');

				$elem.sortable
				({
					handle: '.builder-widget > .builder-widget-bar',
					connectWith: '.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column',
					appendTo: 'body',
					distance: 40,
					forceHelperSize: true,
					tolerance: 'pointer',
					helper: function(e, ui)
					{
						if (ui.hasClass('builder-widget-core'))
						{
							$('.builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
							{
								$(this).sortable().sortable('disable');
							});
						}

						$('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').sortable().sortable('refresh');

						return ui;
						//return make_clonable(e, ui);
					},

					start: function (e, ui)
					{
						self.widget_drag = true;
						ui.item.attr('data-top-level', builder_widget.is_top_level(ui.item));
						ui.item.attr('data-prev-id', ui.item.index());
					},

					beforeStop: function(e, ui)
					{
						if ($(ui.helper).hasClass('builder-widget-core'))
						{
							$('.builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
							{
								$(this).sortable().sortable('enable');
								$(this).sortable().sortable('refresh');
							});
						}
					},

					stop: function(e, ui)
					{
						var prev_id = parseInt(ui.item.attr('data-prev-id'), 10);
						var was_top = ui.item.attr('data-top-level') === 'true' ? true : false;
						var is_top = builder_widget.is_top_level(ui.item);
						var reorder_start_id = null;
						var reorder_self = null;
						var reorder_col = null;

						if (was_top === true && is_top === true)
						{
							reorder_start_id = Math.min(prev_id, ui.item.index());
						} else if (was_top === true && is_top === false)
						{
							reorder_start_id = prev_id;
							reorder_self = true;
							reorder_col = true;
						} else if (was_top === false && is_top === true)
						{
							reorder_start_id = ui.item.index();
							reorder_self = true;
							reorder_col = true;
						} else if (was_top === false && is_top === false)
						{
							reorder_self = true;
							reorder_col = true;
						}

						if (reorder_start_id !== null)
						{
							widget_id_tracker.update_widget_row_order($('.builder-location > .builder-widget-wrapper').slice(reorder_start_id));
						}

						if (reorder_self === true)
						{
							widget_id_tracker.update_widget_row_order(ui.item);
						}

						if (reorder_col === true)
						{
							widget_id_tracker.update_widget_col_order(ui.item);
						}

						ui.item.attr('data-prev-id', '');
						ui.item.attr('data-top-level', '')

						$('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
						{
							$(this).sortable().sortable('enable');
							$(this).sortable().sortable('refresh');
						});

						self.widget_drag = false;
					},

					out: function(e, ui)
					{
						//this is required only as a support when sortable element leaves original row wrapper
						//other more suitable events (remove & update) unfortunately don't have a ui.sender available at this point anymore
						//this one must be used instead
						self.row_widget_utils.update_columns_height(ui.sender.parents('.builder-widget-row')); 
						
						/*if ($(ui.placeholder).parent().hasClass('builder-widget-column'))
						{
							$('.builder-location-wrapper:not(.read-only) .builder-location').each( function()
							{
								$(this).sortable('enable');
								$(this).sortable('refresh');
							});
						}*/
					},

					over: function(e, ui)
					{
						self.row_widget_utils.update_columns_height(ui.placeholder.parents('.builder-widget-row'), ui.placeholder.parents('.builder-widget-column').index());

						ui.placeholder
							.css('width', '')
							.attr('style', 'width: ' + (ui.placeholder.width()) + 'px !important;');

						/*if ($(ui.placeholder).parent().hasClass('builder-widget-column'))
						{
							$('.builder-location-wrapper:not(.read-only) .builder-location').each( function()
							{
								$(this).sortable('disable');
							});
						} else
						{
							$(this).sortable('enable');
						}*/

						$('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
						{
							$(this).sortable().sortable('refresh');
						});
					},

					update: function (e, ui)
					{
						if (ui.item.parent().hasClass('builder-widget-column'))
						{
							$('body').trigger('builder_after_col_capacity_change', [undefined, ui.item.closest('.builder-widget-row')])
						}
						//if (ui.item.css('display', 'none'))
						//{
						//	ui.item.css('display', 'block');
						//}
					}
				});

				builder.row_widget_utils.update_columns_height($elem);
			},

			make_clonable: function (e, ui)
			{
				var copy = ui.clone().insertAfter(ui);

				this.make_sortable($('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column'));

				if (copy.hasClass('builder-widget-core'))
				{
					$('.builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column').each( function()
					{
						$(this).sortable().sortable('disable');
					});
				}

				$('#builder-widgets').sortable().sortable('refresh');

				return ui.clone();
			},

			insert_widget: function ($widget)
			{
				// console.log('insert widget: ', $widget.length, this.$insert_target.length, this.$insert_position);

				if ( ! this.$insert_target)
					return;

				var self = this;

				$('body').trigger('builder_before_insert_widget', [$widget, this.insert_position, this.$insert_target]);
				
				this.insert_position == 'before' ? this.$insert_target.prepend($widget) : this.$insert_target.append($widget);
				
				$('body').trigger('builder_after_insert_widget', [$widget, this.insert_position, this.$insert_target]);

				if ($widget.parent().hasClass('builder-widget-column'))
				{
					$('body').trigger('builder_after_col_capacity_change', ['add', $widget])
				}
			},

			remove_widget: function ($widget, force)
			{
				var $next, $parent;

				! $widget.hasClass('.builder-widget-wrapper') ? $widget = $widget.closest('.builder-widget-wrapper') : '';

				if (force === true || confirm(builder_lang.sure))
				{
					$parent = $widget.eq(0).parent();
					$next = $widget.eq(0).nextAll();

					$('body').trigger('builder_before_remove_widget', [$widget]);

					$widget.remove();

					$('body').trigger('builder_after_remove_widget', [$next, $parent]);

					if ($parent.hasClass('builder-widget-column'))
					{
						$('body').trigger('builder_after_col_capacity_change', ['remove', $parent])
					}
				}
			},

			wp_editor_init_tabs: function ()
			{
				if ($('.postarea').length > 0 && $('#editor-builder-tab').length > 0)
				{
					if ($('#postdivrich').length == 0 || $('#content-html').length == 0)
					{
						if ($('#postdivrich').length > 0 && $('#content-html').length == 0)
						{
							$('.wp-editor-tools').prepend('<a id="content-html" class="hide-if-no-js wp-switch-editor switch-html active">HTML</a>');
						} else
						{
							$('#editor-toolbar').prepend('<div class="zerosize"><input accesskey="e" type="button" /></div> <a id="edButtonHTML" class="hide-if-no-js active">HTML</a>');
						}
					}

					$('.mceIframeContainer iframe').each( function()
					{
						if ($(this).height() < 50)
						{
							$(this).height(300);
						}
					});

					var $tabs = null;

					if ($('.postarea #editor-toolbar').length > 0)
					{
						$tabs = $('.postarea #editor-toolbar');
					} else if ($('.postarea #wp-content-editor-tools').length > 0)
					{
						$tabs = $('.postarea #wp-content-editor-tools');
					}

					if ($tabs.children('.wp-editor-tabs').length > 0)
					{
						$tabs = $tabs.children('.wp-editor-tabs');
					}

					if ($tabs != null)
					{
						$tabs.children('a').eq(0).after($('<a />').attr('id', 'edButtonBuilder').addClass('hide-if-no-js wp-switch-editor').text('Builder').click( function()
						{
							$('.wp-editor-wrap').removeClass('tmce-active html-active active');
							$('.wp-switch-editor').removeClass('active');
							$(this).addClass('active');
							$('#editor-toolbar #edButtonHTML, #editor-toolbar #edButtonPreview, #wp-content-editor-tools #content-html, #wp-content-editor-tools #content-tmce').removeClass('active');
							$('#editorcontainer, #quicktags, #post-status-info, #media-buttons, #wp-content-media-buttons, #wp-content-editor-container').addClass('hide');
							$('#editor-builder-tab').removeClass('hide');

							$('input[name=' + ether.prefix + 'editor_tab]').val('builder');

							return false;
						}));

						$tabs.children('a:not(#edButtonBuilder)').click( function()
						{
							if ($(this).attr('id') != 'edButtonBuilder')
							{
								$('#wp-editor-wrap').removeClass('tmce-active html-active');
								$('.wp-editor-wrap').removeClass('tmce-active html-active active');
								$('.wp-switch-editor').removeClass('active');
								$('#edButtonBuilder').removeClass('active');

								if ($(this).attr('id') == 'content-html' || $(this).attr('id') == 'edButtonHTML')
								{
									$('#edButtonHTML, #content-html').addClass('active');
									$('.wp-editor-wrap').addClass('html-active');
								} else
								{
									$('#edButtonPreview, #content-tmce').addClass('active');
									$('.wp-editor-wrap').addClass('tmce-active');
								}

								//$(this).addClass('active');
								$('#editorcontainer, #quicktags, #post-status-info, #media-buttons, #wp-content-media-buttons, #wp-content-editor-container').removeClass('hide');
								$('#editor-builder-tab').addClass('hide');

								$('input[name=' + ether.prefix + 'editor_tab]').val('');
							}
						});

						if (ether.builder_tab || $('input[name=' + ether.prefix + 'editor_tab]').val() == 'builder')
						{
							$('#editor-toolbar #edButtonHTML, #editor-toolbar #edButtonPreview, #wp-content-editor-tools #content-html, #wp-content-editor-tools #content-tmce').removeClass('active');
							$('.wp-editor-wrap').removeClass('tmce-active html-active active');
							$('.wp-switch-editor').removeClass('active');
							$('#edButtonBuilder').addClass('active');
							$('#editorcontainer, #quicktags, #post-status-info, #media-buttons, #wp-content-media-buttons, #wp-content-editor-container').addClass('hide');
							$('#editor-builder-tab').removeClass('hide');

							$('input[name=' + ether.prefix + 'editor_tab]').val('builder');
						}
					}
				}

				if (ether.hide_visual_tab)
				{
					$('#edButtonPreview, .wp-switch-editor.switch-tmce').hide();
				}

				if (ether.hide_html_tab)
				{
					$('#edButtonHTML, .wp-switch-editor.switch-html').hide();
				}

				if ( ! ether.builder_tab && (ether.hide_visual_tab || ether.hide_html_tab))
				{
					$('.wp-editor-wrap').removeClass('tmce-active html-active active');
					$('.wp-switch-editor, #edButtonHTML, #edButtonPreview').removeClass('active');

					if (ether.hide_visual_tab)
					{
						/* some kind of bug, shows up tiny mce toolbar so show builder tab instead */

						/*$('#edButtonHTML, .wp-switch-editor.switch-html').addClass('active');
						$('.wp-editor-wrap').addClass('html-active');
						switchEditors.go('content', 'html');

						$('.mceEditor, #content_parent').addClass('hide');
						$('#content').show();*/
					} else
					{
						$('#edButtonPreview, .wp-switch-editor.switch-tmce').addClass('active');
						$('.wp-editor-wrap').addClass('tmce-active');

						if (typeof switchEditors != 'undefined' && $('#wp-content-wrap').length > 0)
						{
							switchEditors.go('content', 'tinymce');
						}
					}
				}
			},

			init_sortable_ui: function ()
			{
				this.make_sortable($('.builder-location-wrapper:not(.read-only)').find('.builder-location, .builder-location .builder-widget-row > div.builder-widget-column'));
				// this.make_sortable($('.builder-location-wrapper:not(.read-only) .builder-location, .builder-location-wrapper:not(.read-only) .builder-location .builder-widget-row > div.builder-widget-column'));
			},

			update_widgets: function ($widgets)
			{
				var self = this;

				$widgets.each(function ()
				{
					var widget_name = builder_widget.get_name($(this)).replace(/-/g, '_');

					builder_widgets.update_widget(widget_name, $(this), 'all', true);
				});
			},

			init_widget_row_options: function ()
			{
				$('.builder-location')
					.on('mouseenter', '.builder-widget-wrapper', function (evt)
					{
						if ( ! self.widget_mousedown || ! self.widget_drag)
						{
							$(this).find('.builder-widget-row-options').stop(true, true).delay(250)
								// .slideDown(250)
								.fadeIn(250)
								// .show()
						}
					})
					.on('mouseleave', '.builder-widget-wrapper', function (evt)
					{
						$(this).find('.builder-widget-row-options').stop(true, true)
							// .slideUp(250)
							// .fadeOut(250)
							// .hide()
					});
			},		

			init_gui: function ()
			{
				var self = this;

				this.wp_editor_init_tabs();
				this.init_sortable_ui();
				this.update_widgets($('.builder-location .builder-widget-wrapper'));
				this.init_widget_row_options();

				$('body').append($('<div />').attr('id', 'builder-widget-overlay'));
			
				$('.builder-location-wrapper')
					.on('click', 'button[name=builder-widget-add]', function()
					{
						builder.set_widget_insert_target($(this));
						builder_widgets_metabox.init_metabox($('#builder-widgets'));

						return false;
					})
					.on('mousedown', '.builder-widget-wrapper', function ()
					{
						self.widget_mousedown = true;
					})
					.on('mouseup', '.builder-widget-wrapper', function ()
					{
						self.widget_mousedown = false;
					});
					
				$('.builder-options > p > a').click( function()
				{
					var index = $(this).index();

					var $current = $(this).parent().nextAll('fieldset').eq(index);
					$(this).parent().parent().find('fieldset').not($current).slideUp();

					$current.stop(true, true).slideDown();

					return false;
				});

				$('#edButtonBuilder').click(function ()
				{
					self.update_builder_location_width();
				});

				$(window).resize(function ()
				{
					scheduler.set('set-builder-location-width', 250, function ()
					{
						self.update_builder_location_width();
						self.row_widget_utils.update_columns_height($('.builder-location'));
					});
				});

				$(document)
					.on('keydown', function (e)
					{
						if (e.keyCode === 17)
						{
							self.ctrl_down = true;
						}
					})
					.on('keyup', function (e)
					{
						if (e.keyCode === 17)
						{
							self.ctrl_down = false;
						}
					});

				this.update_builder_location_width();
				this.row_widget_utils.update_columns_height($('.builder-location'));

				widget_debug.debug_id();
			},

			init_modules: function (modules)
			{
				$.extend(this, modules);

				return this;
			},

			init: function (settings)
			{
				this.thumb_size = $('#builder-thumb-size').attr('class');

				var defaults = {
					hide_visual_tab: false,
					hide_html_tab: false,
					builder_tab: false,
				}

				settings = $.extend(defaults, settings);

				if (settings.hide_visual_tab || (settings.hide_visual_tab && settings.hide_html_tab))
				{
					settings.builder_tab = true;
				}

				$.extend(this, settings);

				$('body').trigger('builder_init', [this]);

				this.init_gui();
			}
		}
	}().init_modules({
		set_icon: set_icon,
		icon_chooser: icon_chooser,
		richtext: richtext,
		builder_widget: builder_widget,
		builder_widgets: builder_widgets,
		builder_widgets_metabox: builder_widgets_metabox,
		live_previews: live_previews,
		widget_debug: widget_debug,
		widget_id_tracker: widget_id_tracker
	});

	ether.builder = builder;

});})(jQuery);