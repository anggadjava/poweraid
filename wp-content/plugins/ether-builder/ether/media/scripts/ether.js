window.ether = window.ether || {};

ether.placeholder = ether.placeholder || {};
ether.$_GET = ether.$_GET || [];
ether.builder_lang = ether.builder_lang || {};

Array.isArray = Array.prototype.isArray || function (vArg) 
{
	return Object.prototype.toString.call(vArg) === "[object Array]";
};

Array.prototype.getRandom = Array.prototype.getRandom || function () {
    return this[Math.floor(this.length * Math.random())];
};

Array.prototype.forEach = Array.prototype.forEach || function(fun /*, thisArg */)
{
	"use strict";

	if (this === void 0 || this === null)

	throw new TypeError();

	var t = Object(this);
	var len = t.length >>> 0;

	if (typeof fun !== "function")
		throw new TypeError();

	var thisArg = arguments.length >= 2 ? arguments[1] : void 0;

	for (var i = 0; i < len; i++)
	{
		if (i in t)
			fun.call(thisArg, t[i], i, t);
	}
};

Array.prototype.unique = function() {
    var o = {}, i, l = this.length, r = [];
    for(i=0; i<l;i+=1) o[this[i]] = this[i];
    for(i in o) r.push(o[i]);
    return r;
};

(function($){

$.fn.cattr = function(key, value, attribute)
{
	if (typeof attribute == 'undefined')
	{
		attribute = 'className';
	}

	var $object = $(this).eq(0);
	var class_name = '';

	if (key != null)
	{
		var classes = $object[0][attribute].split(' ');

		for (i = 0; i < classes.length; i++)
		{
			if (classes[i].substr(0, key.length) == key)
			{
				class_name = classes[i];
			}
		}
	}

	if (typeof value == 'undefined' || value == null)
	{
		return class_name.substr(key.length+1);
	} else
	{
		if (class_name != '')
		{
			//$object.attr(attribute, $object.attr(attribute).replace(class_name, key + '-' + value));
			$object[0][attribute] = $object[0][attribute].replace(class_name, key + '-' + value);
		} else
		{
			$object[0][attribute] = $object[0][attribute] + ' ' + key + '-' + value;
			//$object.attr(attribute, $object.attr(attribute) + ' ' + key + '-' + value);
		}
	}

	return this;
};

$( function()
{
	var Collection = function ()
	{
		this.id = 0;
		this.elements = {};
	}

	Collection.prototype.get_id = function ()
	{
		return this.id;
	},

	Collection.prototype.next_id = function ()
	{
		this.id += 1;

		return this.get_id();
	},

	Collection.prototype.get = function (id)
	{
		return id !== undefined ? this.elements[id] : this.elements;
	}

	Collection.prototype.add = function (elem, name)
	{
		if ( ! name)
		{
			name = this.get_id();
			this.next_id();
		}

		elem.set_id(name);
		this.elements[name] = elem;

		// console.log('add elem', name, elem, this, elements, elem instanceof Instance, elem instanceof Group, elem instanceof Field)
	}

	Collection.prototype.count = function ()
	{
		var count = 0;

		obj_foreach(this, this.elements, function (key)
		{
			count += 1;
		});

		return count;
	}

	Collection.prototype.remove = function (id)
	{
		delete this.elements[id];
	}

	var Collection_Elem = function ()
	{
		this.id;
	}

	Collection_Elem.prototype.set_id = function (v)
	{
		this.id = v;
	}

	Collection_Elem.prototype.get_id = function ()
	{
		return this.id;
	}

	Collection_Elem.prototype.get_data = function (cache)
	{
		var result = {};

		obj_foreach(this, this, function (key, val)
		{
			var d;

			if (key.substr(0, 4) === 'get_')
			{
				d = this[key]();

				if (d instanceof Collection_Elem)
				{
					cache.push(d);

					d = d.get_data();
				}

				result[key.replace('get_', '')] = d;
			}
		});

		// console.log('generic get data', result);

		return result;
	}

	var obj_foreach = function (thisArg, obj, callback, depth) 
	{
		var key;

		for (key in obj) 
		{
		    if (obj.hasOwnProperty(key)) 
		    {
		        callback.apply(thisArg, [key, obj[key]]);
		        
		        if (depth === true && (typeof obj[key] === 'object')) 
		        {
		            obj_foreach(thisArg, obj[key], callback, depth);
		        }
		    }
		}
	}

	var capitalize = function (s)
	{
		return s && s.length ? s.charAt(0).toUpperCase() + s.slice(1) : s;
	}

	var scheduler = function ()
	{
		var timeouts = {};

		return {
			clear: function (name)
			{
				clearTimeout(timeouts[name]);
			},

			set: function (name, timeout, fn, this_arg, args)
			{
				clearTimeout(timeouts[name]);

				if (timeout)
				{
					timeouts[name] = setTimeout(function ()
					{
						fn.apply(this_arg, args);
					}, timeout);
				} else
				{
					fn.apply(this_arg, args);
				}
			},

			unset: function (name)
			{
				delete timeouts[name];
			}
		}
	}();

	var get_field_val = function ($field, by_text, as_obj)
	{
		var v;
		var tag;
		var type;

		if ( ! $field.length)
		{
			return;
		} 

		tag = $field[0].nodeName.toUpperCase();

		if ($field.length > 1)
		{
			v = as_obj ? {} : [];

			$field.each(function (id)
			{	
				var r = get_field_val($(this), Array.isArray(by_text) ? by_text[id] : by_text);
				
				as_obj ? v[$(this).attr('name')] = r : v.push(r);
			});

			return v;
		}

		switch (tag)
		{
			case 'SELECT':
			{
				v = by_text ? $field.find('option:selected').text() : $field.find('option:selected').val();
				break;
			}
			case 'TEXTAREA':
			{
				v = $field.val();
				break;
			}
			// case 'INPUT':
			default:
			{
				// console.log(tag, $field)

				type = $field.attr('type').toUpperCase();

				v = (type === 'CHECKBOX' ? $field[0].checked : type === 'RADIO' ? $field.is(':selected') : $field.val());
				by_text && type === 'CHECKBOX' ? v = (v == true ? 'Yes' : 'No') : '';
				break;
			}
		}

		// console.log('get field val: tag: ' + tag + ' type: ' + type + ' val: ' + v);

		return v;
	}

	var get_attr_val = function ($scope, attr, by_text, as_obj)
	{
		// console.log('get attr val: attr:', attr, ' by text: ', by_text, $scope.length);

		var $field;
		var v;

		if (Array.isArray(attr))
		{
			v = as_obj ? {} : [];

			attr.forEach(function (a, id)
			{
				var r = get_attr_val($scope, a, Array.isArray(by_text) ? by_text[id] : by_text);

				as_obj ? v[a] = r : v.push(r);
			});

			// console.log(v);

			return v;
		} else 
		{
			$field = (attr === true ? $scope : $scope.find('select, input, textarea').filter('[name*="[' + attr + ']"]'));

			// console.log('get attr val: attr:', attr, ' by text: ', by_text, $scope.length, $field.length);

			if ( ! $field.length)
			{
				// console.warn('get attr val: no field selected: attr:', attr, 'by text: ', by_text);
			}

			return get_field_val($field, by_text);
		}
	}

	var get_text = function ($elem, parent_only, trim)
	{
		var text = $elem.text();

		if (parent_only)
		{
			$elem.children().each(function ()
			{
				text = text.replace($(this).text(), '');
			});

			trim ? text = text.trim() : '';
		}

		return text;
	}

	//for use with unique tooltips tied specifically to single elements
	var tooltip = function ()
	{
		var tooltip_defaults = 
		{
			show_delay: 250,
			hide_delay: 250,
			show_speed: 250,
			hide_speed: 250,
			par_x: 0.5,
			par_y: 1,
			align_x: -0.5,
			align_y: 0,
			x_dir: 0,
			y_dir: 0,
			keep_visible_on_tooltip_hover: false,
			style: 'default', //plain
			theme: 'light', //dark
			size: 'medium', //small/big
			type: 'default', //info/warning/error
			show_icon: false,
			pad_icon: false,
			color: null,
			'background-color': null,
			classes: null,
			tip: null,
			$trigger: null,
			$tooltip: null,
			$container: null,
			$parent: null,
			content: null
		}

		var Tooltip = function (options)
		{
			this.scheduler_id = null; //used for unique namespacing when showing/hiding tooltip
			this.options = options;
		}
		Tooltip.prototype = new Collection_Elem();

		Tooltip.prototype.get_options = function (prop)
		{
			return prop ? (this.options[prop] !== undefined && this.options[prop] !== null ? this.options[prop] : tooltip_defaults[prop]) : jQuery.extend({}, tooltip_defaults, this.options);
		}

		Tooltip.prototype.set_options = function (prop, val)
		{
			this.options[prop] = val;
		}

		Tooltip.prototype.show = function ()
		{
			tooltip.show(this);
		}

		Tooltip.prototype.hide = function ()
		{
			tooltip.hide(this);
		}

		Tooltip.prototype.init = function ()
		{
			var self = this;



			this.get_options('$trigger')
				.on('mouseenter', function ()
				{
					self.show();
				})
				.on('mouseleave', function ()
				{
					self.hide();
				})

			this.get_options('$tooltip')
				.on('mouseenter', function (e)
				{
					e.stopPropagation();

					if (self.get_options('keep_visible_on_tooltip_hover'))
					{
						self.show();
					}
				})
				.on('mouseleave', function ()
				{
					if (self.get_options('keep_visible_on_tooltip_hover'))
					{
						self.hide();
					}
				});

			return this;
		}

		return {
			Tooltip: Tooltip,

			get_defaults: function (prop)
			{
				return prop ? tooltip_defaults[prop] : tooltip_defaults;
			},

			show: function (tooltip)
			{
				var self = this;
				var o = tooltip.get_options();
				var scheduler_id = tooltip.scheduler_id || Math.round(Math.random() * 999999);
				! tooltip.scheduler_id ? tooltip.scheduler_id = scheduler_id : '';

				scheduler.clear('ether-tooltip-hide-' + scheduler_id);
				scheduler.set('ether-tooltip-show-' + scheduler_id, o.show_delay, function ()
				{
					var $container = o.$container || $(document);
					var $parent = o.$parent;
					var $tooltip = o.$tooltip;
					
					var to_pos = get_pos($parent, true);
					var from_pos;
					var bounds = get_bounds($container, true, true, true);
					
					var show_speed = o.show_speed;
					var par_x = o.par_x;
					var par_y = o.par_y;
					var align_x = o.align_x;
					var align_y = o.align_y;
					var x_dir = o.x_dir;
					var y_dir = o.y_dir;

					$tooltip
						.addClass('active')
						.appendTo($('body'))
						.css({
							top: 0,
							left: 0,
							display: 'block'
						});

					to_pos = rel_shift_pos(to_pos, $parent, par_x, par_y); //align on parent
					to_pos = rel_shift_pos(to_pos, $tooltip, align_x, align_y); //align tooltip relative to itself
					to_pos = pos_to_bounds(to_pos, $tooltip);
					from_pos = fit_bounds(rel_shift_pos(to_pos, $tooltip, x_dir, y_dir), bounds); //validate bounds
					to_pos = rel_shift_pos(from_pos, $tooltip, -x_dir, -y_dir); //get adjusted to_pos //ignore bounds at this point

					$tooltip
						.css({
							opacity: 0, 
							top: from_pos[0],
							left: from_pos[1]
						})
						.stop(true)
						.animate({
							'top': to_pos[0],
							'left': to_pos[1],
							'opacity': 1
						}, show_speed);

					$parent.addClass('active');
				});
			},

			hide: function (tooltip)
			{
				var self = this;
				var o = tooltip.get_options();
				var scheduler_id = tooltip.scheduler_id || Math.round(Math.random() * 999999);
				! tooltip.scheduler_id ? tooltip.scheduler_id = scheduler_id : '';

				scheduler.clear('ether-tooltip-show-' + scheduler_id);
				scheduler.set('ether-tooltip-hide-' + scheduler_id, o.hide_delay, function ()
				{
					o.$tooltip.filter('.active').each(function (id)
					{
						var $self = $(this);

						var pos = get_bounds($(this), true);
						var x_dir = o.x_dir;
						var y_dir = o.y_dir;
						var to_pos = rel_shift_pos(pos, $(this), x_dir, y_dir);
						var $parent = o.$parent;
						var hide_speed = o.hide_speed;

						// console.log('hide', $tooltip.text());

						$(this)
							.stop(true)
							.animate({
								'top': to_pos[0],
								'left': to_pos[1],
								'opacity': 0
							}, hide_speed, function ()
							{
								$(this)
									.css({'display': 'none'})
									.removeClass('active');
								$parent
									.removeClass('active')
									.append($self);
							});
					});
				});
			},

			construct_dom: function (options, $tooltip)
			{
				var class_names = [];
				var style_attr = '';

				var o = $.extend({}, this.get_defaults(), options);

				var style = o.style;
				var theme = o.theme;
				var type = o.type;
				var size = o.size;
				var show_icon = o.show_icon;
				var pad_icon = o.pad_icon;
				var color = o.color;
				var bg_color = o['background-color'];
				var classes = o.classes;
				var tip = o.tip;
				var content = o.content;

				color 	 ?	style_attr += 'color: ' + color + '; ' : '';
				bg_color ?	style_attr += 'background-color: ' + bg_color + '; ' : '';

				class_names = class_names.concat([
					'ether-tooltip', 
					'style-' + style,
					'theme-' + theme,
					'size-' + size,
					'type-' + type
				]);

				classes ?				class_names.push(classes) : '';
				tip ?					class_names.push('show-tip show-tip-' + tip) : '';
				show_icon ? 			class_names.push(' show-icon') : '';
				show_icon && pad_icon ? class_names.push(' pad-icon') : '';

				class_names = class_names.join(' ');

				if ( ! $tooltip || $tooltip.length)
				{
					$tooltip = $('<div>');
				}

				$tooltip
					.attr('class', class_names)
					// .attr('style', style_attr)
					.append('<div class="ether-tooltip-content" style="' + style_attr + '">' + (show_icon ? '<div class="icon"></div>' : '') + content + '</div>')
					.append('<div class="tip-wrap"><div class="tip" style="' + style_attr + '"></div></div>');

				return $tooltip;
			},

			init: function ($elem, options)
			{
				var self = this;

				$elem.each(function ()
				{
					var $tooltip_wrap = $('<div>');

					! options.content ? options.content = $(this).html() : '';

					$tooltip_wrap
						.addClass('ether-tooltip-wrap')
						.append(self.construct_dom(options))
						.insertAfter($(this));

					if ( ! options.$trigger )
					{
						options.$trigger = $tooltip_wrap;
					} else
					{
						$tooltip_wrap.hide();
					}

					! options.$parent ? options.$parent = options.$trigger : '';
					! options.$tooltip ? options.$tooltip = $tooltip_wrap.find('.ether-tooltip') : '';

					if ($(this).find('a').length)
					{
						options.keep_visible_on_tooltip_hover = true;
					}

					new Tooltip(options).init();		

					$(this).remove();
				});
			}
		}
	}();

	//for use for tooltips of the same text ocurring on multiple elements
	var batch_tooltip = function ()
	{
		var tooltip_util = tooltip;
		var tooltip_defaults = $.extend({}, tooltip_util.get_defaults(), {
			$scope: null,
			selector: null,
		});

		var Batch_Tooltip = function (options)
		{
			this.triggers = null;
			this.options = options;
		}
		Batch_Tooltip.prototype = new tooltip_util.Tooltip();

		Batch_Tooltip.prototype.update_options = function ($trigger)
		{
			this.set_options('$trigger', $trigger);
			this.set_options('$parent', $trigger);
		}
		
		Batch_Tooltip.prototype.add_trigger = function ($scope, selector)
		{
			var self = this;

			if ( ! $scope || ! $scope.length)
				return;

			this.triggers.push({
				$scope: $scope, 
				selector: selector
			});

			if (selector)
			{
				$scope
					.addClass('ether-batch-tooltip-scope')
					.on('mouseenter.batch_tooltip', selector, function (evt)
					{
						self.update_options($(this))
						tooltip_util.show(self);
					})
					.on('click.batch_tooltip', selector, function (evt)
					{
						self.update_options($(this))
						tooltip_util.show(self);
					})
					.on('mouseleave.batch_tooltip', selector, function ()
					{
						self.update_options($(this))
						tooltip_util.hide(self);
					});
			} else 
			{
				this.update_options($scope);
				
				$scope
					.addClass('ether-batch-tooltip-scope')
					.on('mouseenter.batch_tooltip', function (evt)
					{
						tooltip_util.show(self);
					})
					.on('click.batch_tooltip', function (evt)
					{
						tooltip_util.show(self);
					})
					.on('mouseleave.batch_tooltip', function ()
					{
						tooltip_util.hide(self);
					});
			}
		}

		Batch_Tooltip.prototype.init_dom = function (o)
		{
			var $tooltip_wrap = $('<div class="ether-batch-tooltip">');
			var $tooltip;

			o = o || this.get_options();
			
			$tooltip = tooltip_util.construct_dom(o);
			$tooltip_wrap
				.append($tooltip)
				.appendTo($('body'));
			
			this.set_options('$tooltip', $tooltip);

			return $tooltip_wrap;
		}

		Batch_Tooltip.prototype.init = function ()
		{
			this.triggers = [];

			var o = this.get_options();

			this.init_dom();
			this.add_trigger(o.$scope, o.selector);

			return this;
		}

		var tooltips = new Collection();

		return {
			init: function (options)
			{
				var self = this;
				var tooltip = this.has_tooltip(options.content);

				var $scope = options.$scope;
				var selector = options.selector;

				if ( ! $scope || ! $scope.length || ! options.content || ! options.content.length)
					return;

				if ( ! tooltip)
				{
					tooltip = new Batch_Tooltip(options).init();

					this.add_tooltip(tooltip);
				} else
				{
					tooltip.add_trigger($scope, selector);
				}
			},

			get_tooltips: function ()
			{
				return tooltips;
			},

			get_tooltip: function (content)
			{
				return this.has_tooltip(content);
			},

			add_tooltip: function (tooltip, id)
			{
				tooltips.add(tooltip, id);
			},

			has_tooltip: function (content)
			{
				var tooltips = this.get_tooltips().get();
				var result;

				obj_foreach(this, tooltips, function (id, tooltip)
				{
					if (result)
						return;

					tooltip.get_options('content') === content ? result = tooltip : '';
				});

				return result;
			}
		}
	}();

	var dynamic_label = function ()
	{
		var id = {
			label: 0,
			scope: 0
		};
		var options = null; //custom options are temporarily stored when calling set - hotfix - come up with something more stable later

		return {
			get_id: function (type)
			{
				return id[type];
			},

			set_id: function (type)
			{
				id[type] += 1;
				return this.get_id(type) - 1;	
			},

			next_id: function (type)
			{
				id[type] += 1;
				return this.get_id(type);
			},

			wrap_check_radio: function ($label)
			{
				var $label_title = $label.find('.label-title');
				var $fields = $label.children('input[type="checkbox"], input[type="radio"]')

				if ($fields.length)
				{
					$label_title
						.prepend($fields)
						.addClass('expanded-title');
				}
			},

			init_tooltip: function ($label, $tooltip, $container)
			{
				tooltip.init($tooltip, {
					$container: $container, 
					y_dir: 1, 
					theme: 'dark', 
					tip: 'top'
				});

				$label.find('.ether-tooltip-wrap').appendTo($label.children('.label-title'));
			},		

			label_get_field: function ($label)
			{
				return $label.children('input:not(input[type="checkbox"]):not(input[type="radio"]), select');
			},

			label_get_title: function ($label)
			{
				return $label.children('.label-title');
			},

			label_field_unset: function ($label)
			{
				this.label_get_field($label).css('width', '');
			},

			label_field_set: function ($label)
			{
				// console.log('set label field: ', this.label_get_id($label), this.label_get_field($label).length);
				this.label_get_field($label).css('width', ($label.width() - this.label_get_title($label).outerWidth()));
			},

			label_get_id: function ($label)
			{
				var cls = $label.attr('class') || '';

				cls = cls.match(/label-dynamic-id-(\d+)/);
				cls ? cls = cls[1] : '';

				return cls;
			},

			label_unset: function ($label)
			{
				var label_id = this.label_get_id($label);

				if (label_id !== undefined)
				{
					this.label_field_unset($label);
					$label
						.removeClass('label-dynamic-id-' + label_id)
						.removeClass('label-dynamic');
				}
			},

			label_set: function ($label, forced, options)
			{
				var $container, find_parent_method;

				if (options && options.tooltip_container_selector) //custom options are temporarily stored when calling set - hotfix - come up with something more stable later
				{
					find_parent_method = options.tooltip_container_search_method || 'closest'; //starts at: $label
					$container = $label[find_parent_method](options.tooltip_container_selector); 
				}

				$label
					.addClass('label-dynamic')
					.addClass('label-dynamic-id-' + this.set_id('label'));

				this.wrap_check_radio($label);
				this.init_tooltip($label, $label.find('small'), $container);
			},

			scope_get_id: function ($scope)
			{
				var cls = $scope.attr('class') || '';

				cls = cls.match(/label-dynamic-scope-id-(\d+)/);
				cls ? cls = cls[1] : '';

				return cls;
			},

			scope_get_labels: function ($scope)
			{
				var $label = $scope.find('label').filter(function ()
				{
					//skip checkboxes because they don't need the extra classes overhead due to their slightly different way of displaying structure.
					return ! $(this).find('input[type="checkbox"]').length; 
				});

				return $label;
			},

			scope_update: function ($scope, $label)
			{
				var self = this;

				if ( ! $label)
				{
					$label = this.scope_get_labels($scope);
				}

				// console.log('scope update:', this.scope_get_id($scope), 'labels: ', $label.length, 'sc width: ', $scope.width())

				//first unset everything: this is more liberal towards the styles of the $scope
				$label
					.each(function ()
					{
						if ($(this).hasClass('label-dynamic') && $(this).is(':visible'))
						{
							self.label_field_unset($(this));
						}
					})
					.each(function ()
					{
						self.label_field_set($(this));
					});
			},

			scope_unset: function ($scope)
			{
				var self = this;
				var scope_id = this.scope_get_id($scope);
				var $label = $scope.find('label.label-dynamic');

				$scope
					.removeClass('label-dynamic-scope')
					.removeClass('label-dynamic-scope-id-' + scope_id);

				$(window).off('resize.label-dynamic-scope-id-' + scope_id);

				$label.each(function ()
				{
					self.label_unset($(this));
				});
			},

			scope_set: function ($scope, forced, options)
			{
				var self = this;
				var scope_id, update_event_name, $label;

				options = options; //custom options are temporarily stored when calling init - hotfix - come up with something more stable later

				if ( ! $scope.hasClass('label-dynamic-scope') || forced )
				{
					$label = this.scope_get_labels($scope);

					if (forced)
					{
						self.scope_unset($scope);
					}

					scope_id = this.set_id('scope');
					update_event_name = 'resize.label-dynamic-scope-id-' + scope_id;

					$scope
						.addClass('label-dynamic-scope')
						.addClass('label-dynamic-scope-id-' + scope_id)
								
					$(window).on(update_event_name, function ()
					{
						scheduler.set(update_event_name, 250, function ()
						{
							self.scope_update($scope, $label);
						});
					});

					//console.log('scope: ', scope_id, 'labels:', $label.length);

					$label.each(function ()
					{
						self.label_set($(this), forced, options);
					});

				}

				this.scope_update($scope, $label);
			},

			set: function ($scope, forced, options)
			{
				this.scope_set($scope, forced, options);
			},

			unset: function ($scope)
			{
				this.scope_unset($scope);
			},

			init: function (callback, options)
			{
				var self = this;

				callback ? callback(this) : '';

				return this;
			}
		}
	}().init();

	var toggle_checkbox = function ()
	{
		return {
			show: function ($elem, set_field)
			{
				! $elem.hasClass('ether-toggle-checkbox') ? $elem = $elem.closest('.ether-toggle-checkbox') : '';

				$elem.length ? $elem.removeClass('ether-toggle-checkbox-state-off') : '';

				set_field ? $elem.find('input').prop('checked', true) : '';
			},

			hide: function ($elem, set_field)
			{
				! $elem.hasClass('ether-toggle-checkbox') ? $elem = $elem.closest('.ether-toggle-checkbox') : '';

				$elem.length ? $elem.addClass('ether-toggle-checkbox-state-off') : '';

				set_field ? $elem.find('input').prop('checked', false) : '';
			},

			toggle: function ($elem, state, set_field)
			{
				state ? this.show($elem, set_field) : this.hide($elem, set_field);
			},

			toggle_scope: function ($scope, state, set_field)
			{
				var self = this;

				! $scope.hasClass('ether-toggle-checkbox') ? $scope = $scope.find('.ether-toggle-checkbox') : '';
				
				$scope.each(function ()
				{
					self.toggle($(this), state, set_field);
				});
			},

			toggle_button_state: function ($elem, state)
			{
				$elem
					.text(state ? 'Hide All' : 'Show All')
					.toggleClass('ether-toggle-checkbox-action-off', state)
					.toggleClass('ether-toggle-checkbox-action-on', ! state);
			},

			update_all: function ($scope)
			{
				var self = this;

				$scope = $scope ? ($scope.hasClass('ether-toggle-checkbox-group') ? $scope : $scope.find('.ether-toggle-checkbox-group')) : $('.ether-toggle-checkbox-group');

				$scope.each(function ()
				{
					var name = $(this).attr('class').match(/ether-toggle-checkbox-group-name-((?:\w|-)+)/)[1];
					var $button = $('.ether-toggle-checkbox-button-name-' + name);

					// console.log('update: ', name, $button.length, $(this).find('input:checked').length, $(this).find('input').length);

					! $(this).find('input:checked').length ? self.toggle_button_state($button, false) : ($(this).find('input:checked').length === $(this).find('input').length ? self.toggle_button_state($button, true) : '');
				});
			},

			update_label_state: function ($elem)
			{
				var tag = $elem[0].tagName.toLowerCase();
				var $l = tag === 'label' ? $elem : $elem.parent('label');
				var $f = tag === 'label' ? $elem.find('input') : $elem;
				var v = $f[0].checked;

				// console.log(tag, $l.length, $f.length, '"' + v + '"');

				v ? $l.removeClass('ether-toggle-checkbox-state-off') : $l.addClass('ether-toggle-checkbox-state-off');			
			},

			init: function ($scope, forced)
			{
				var self = this;
				var $field = $scope.find('input[type="checkbox"]');

				$field.each(function ()
				{
					var $l = $(this).parent('label');

					if ( ! $l.hasClass('ether-toggle-checkbox') || forced)
					{
						$l.addClass('ether-toggle-checkbox');
						$(this).insertBefore($l.children().eq(0));
						self.update_label_state($(this));
						tooltip.init($l.find('small'), {
							y_dir: 1,
							theme: 'dark',
							tip: 'top'
						});
					}
				});
			},

			init_gui: function ()
			{
				var self = this;
				
				$('body')
					.on('click', '.ether-toggle-checkbox input', function ()
					{
						self.toggle($(this), this.checked);
					})
					.on('click', '.ether-toggle-checkbox-button', function ()
					{
						var name = $(this).attr('class').match(/ether-toggle-checkbox-button-name-((?:\w|-)+)/)[1];
						var state = ! $(this).hasClass('ether-toggle-checkbox-action-off');
						var $scope = $('.ether-toggle-checkbox-group-name-' + name);

						// console.log(name, state, $scope.length);

						self.toggle_scope($scope, state, true);
						self.toggle_button_state($(this), state);

						if ($scope.find('.ether-toggle-checkbox-group').length)
						{
							self.update_all($scope.find('.ether-toggle-checkbox-group'));
						}
					});

				$('.ether-toggle-checkbox').each(function ()
				{
					$(this).prepend($(this).find('input').eq(0));
				});

				$('.ether-toggle-checkbox-auto').each(function ()
				{
					self.update_label_state($(this));
				});

				this.update_all();

				return this;
			}
		}
	}().init_gui();
	
	//field naming convention (drop the [])
	//ether-cond-field ether-cond-[field-unique-name]
	//SIMPLE: //ether-cond-group ether-action-[show/hide]-ether-cond-[value]-ether-field-[field-unique-name]
	//ADVANCED: //ether-cond-group ether-action-[show/hide]-ether-filter-[is/isnot]-ether-cond-[value]-ether-field-column-count-[or/and]-ether-filter-[is/isnot]-ether-cond-[value]-ether-field-[field-unique-name]

	//samples of encoded conditions
	//[
	//ether-action-show-ether-cond-1-ether-field-1-or-ether-cond-2-ether-field-2,
	//ether-action-show-ether-cond-3-ether-cond-4-ether-field-3-and-ether-cond-5-ether-field-4-or-ether-cond-6-ether-field-5
	//]

	var cond_fields = function ()
	{
		var Instance = function ($scope)
		{
			this.$scope = $scope;
			this.fields = new Collection();
			this.groups = new Collection();
			this.id;
		}
		Instance.prototype = new Collection_Elem();

		Instance.prototype.groups_next_id = function ()
		{
			return this.groups.get_id();
		}

		Instance.prototype.groups_next_id = function ()
		{
			this.groups.next_id();

			return this.groups_next_id();
		}
		
		Instance.prototype.get_data = function (collection)
		{
			var collection = this['get_' + collection]();
			var result = {};

			obj_foreach(this, collection, function (id, elem)
			{
				result[id] = elem.get_data();
			});

			return result;
		}

		Instance.prototype.link_fields_with_groups = function (fields, groups)
		{
			obj_foreach(this, groups, function (id, group)
			{
				var rel_fields = group.get_related_fields();

				rel_fields.forEach(function (field_name)
				{
					// console.log('get field', field_name, this.get_field())
					var field = this.get_field(field_name);

					if (field)
					{
						field.add_group(id);						
					}

				}, this);
			});

			obj_foreach(this, fields, function (id, field)
			{
				field.update_counter(false);
			});
		}			

		Instance.prototype.get_scope = function ()
		{
			return this.$scope;
		}

		Instance.prototype.get_field = function (id)
		{
			return this.fields.get(id);
		}

		Instance.prototype.add_field = function (field, id)
		{
			this.fields.add(field, id);
		}

		Instance.prototype.remove_field = function (id)
		{
			this.fields.remove(id);
		}

		Instance.prototype.init_field = function ($field)
		{
			var field = new Field($field, this).init();

			if (field)
			{
				this.add_field(field, field.get_name());
			}
		}

		Instance.prototype.field_get_name = function ($field)
		{
			return $field.cattr('ether-field');
		}

		Instance.prototype.update_field = function ($field, delay)
		{
			var self = this;

			if (delay !== undefined)
			{
				scheduler.set('update-cond-field-' + this.field_get_name($field), 250, function ()
				{
					self.update_field($field);
				});

				return;
			}

			var field_name = this.field_get_name($field);
			var field = this.get_field(field_name);

			field.update();
		}

		Instance.prototype.get_group = function (id)
		{
			return this.groups.get(id);
		}

		Instance.prototype.add_group = function (group)
		{
			this.groups.add(group);
		}

		Instance.prototype.remove_group = function (id)
		{
			this.groups.remove(id);
		}

		Instance.prototype.init_group = function ($group)
		{
			var group = new Group($group, this).init();

			if (group)
			{
				this.add_group(group);
			}
		}

		Instance.prototype.update_groups = function (forced)
		{
			var groups = this.get_group();

			// console.log('update groups', groups);

			obj_foreach(this, groups, function (id, group)
			{
				group.update(forced);
			});
		}

		Instance.prototype.register = function ()
		{
			var self = this;
			var $scope = this.get_scope();

			if ($scope.hasClass('ether-conditional-fields'))
			{
				return false;
			} else
			{
				$scope.addClass('ether-conditional-fields');
			}

			$scope
				.find('.ether-cond-group').each(function ()
				{
					self.init_group($(this), $scope);
				}).end()
				.find('.ether-cond-field').each(function ()
				{
					self.init_field($(this));
				}).end()
				//
				.on('change', '.ether-cond-field', function ()
				{
					self.update_field($(this), 500);
				})
				.on('keyup', 'input.ether-cond-field:text, textarea.ether-cond-field', function ()
				{
					self.update_field($(this), 500);
				});

			this.link_fields_with_groups(this.get_field(), this.get_group());

			this.update_groups(true);

			// console.log('groups data', this.get_data('group'), 'fields data', this.get_data('field'));

			return this;
		}

		var Group = function ($group, parent)
		{
			this.$group = $group;
			this.parent = parent;
			this.data;
		}
		Group.prototype = new Collection_Elem();

		Group.prototype.initialized = function ($group)
		{
			return $group[0].className.indexOf('ether-cond-group-id-') !== -1
		}

		Group.prototype.get_related_fields = function ()
		{
			var data = this.get_data();
			var result = [];

			obj_foreach(this, data, function (action, d)
			{
				d.forEach(function (rulesets)
				{
					rulesets.forEach(function (ruleset)
					{
						result.push(ruleset.field_name);
					});
				});
			});

			return result;
		}

		Group.prototype.get_data = function ()
		{
			return this.data;
		}

		Group.prototype.set_data = function (d)
		{
			this.data = d;
		}

		Group.prototype.get_group = function ()
		{
			return this.$group;
		}

		Group.prototype.get_parent = function ()
		{
			return this.parent;
		}

		Group.prototype.get_scope = function ()
		{
			return this.get_parent().get_scope();
		}

		Group.prototype.get_field = function (id)
		{
			return this.get_parent().get_field(id);
		}

		Group.prototype.count_fields = function (state)
		{
			var $fields = this.get_group().find('input, textarea, select');

			// console.log('count fields: ', this.get_id() + '', $fields.length, $fields.filter(':visible').length, $fields.filter(':hidden').length, state);

			return (state === undefined ? $fields.length : ($fields.filter(state ? ':visible' : ':hidden').length));
		}

		Group.prototype.ruleset_get_frag = function (ruleset, frag)
		{
			if ( ! expr[frag])
				return;

			var result = expr[frag].exec(ruleset);

			result ? result = result[1] : '';

			// console.log('ruleset_get_frag:', frag, result);

			if ( ! result)
				return;

			if (frag === 'values')
			{
				result = '____' + result + '____';
				result = result.split('ether-cond');
				result = result.slice(1);
				result = result.map(function (e) 
				{ 
					e = e.replace('--', '').replace('____', '').replace(/-/g, '');

					return e; 
				});
			}

			return result;
		}

		//rule: action + condition(s)
		//rulesets: rule connected with -or-
		//ruleset: rule connected with -and-
		Group.prototype.decode = function (string)
		{
			var data = {};
			var matches = [];

			string = string.split(' ');
			string.forEach(function (name)
			{
				if (name.indexOf('ether-action-') !== -1)
				{
					matches.push(name);
				}
			});

			matches.forEach(function (match, id)
			{
				var action = this.ruleset_get_frag(match, 'action');
				var rulesets = match.replace(expr.action, '').split('-or-');

				rulesets.forEach(function (ruleset)
				{
					var ruleset = ruleset.split('-and-');
					var conditions = [];
					
					ruleset.forEach(function (frag)
					{
						var condition;
						var field_name = this.ruleset_get_frag(frag, 'field_name');
						var values = this.ruleset_get_frag(frag, 'values');
						var filter = this.ruleset_get_frag(frag, 'filter');

						if ( ! field_name || ! values)
							return;

						condition =
						{
							field_name: field_name,
							values: values,
							is: true
						};
						
						if (filter)
						{
							if (filter === 'isnot')
							{
								condition.is = false;
							}
						}
						
						conditions.push(condition);

					}, this);

					! data[action] ? data[action] = [] : '';

					if (conditions.length)
					{
						data[action].push(conditions);
					}

				}, this);

			}, this);

			// console.log('decode:', string, data);

			return data;
		}

		Group.prototype.show = function (forced)
		{
			var self = this;
			var $group = this.get_group();

			if (forced)
			{
				$group.show();
				$('body').trigger('ether_cond_group_show', [$group, self]);
			} else
			{
				$group.slideDown(500, function ()
				{
					$('body').trigger('ether_cond_group_show', [$group, self]);
				});
			}

		}

		Group.prototype.hide = function (forced)
		{
			var self = this;
			var $group = this.get_group();

			if (forced)
			{
				$group.hide();
				$('body').trigger('ether_cond_group_hide', [$group, self]);
			} else
			{
				$group.slideUp(500, function ()
				{
					$(this).css({display: 'none'});
					$(this).dequeue();
					$('body').trigger('ether_cond_group_hide', [$group, self]);
				});
			}

		}

		Group.prototype.trigger_action = function (action, forced)
		{
			// console.log('trigger action: ' + action + '; group id: ' + this.get_id() + '; forced: ' + forced)

			switch (action)
			{
				case 'show':
				{
					this.show(forced);
					break;
				}
				case 'hide':
				{
					this.hide(forced);
					break;
				}
			}
		}

		Group.prototype.validate_condition = function (condition)
		{
			var field = this.get_field(condition.field_name);

			if ( ! field)
				return undefined;

			var match = $.inArray(field.get_val(), condition.values) !== -1;
			var result = condition.is === match;

			// console.log('validate condition:', 'field:', condition.field_name, 'requirement:', (condition.is ? 'is' : 'is not') + ':', condition.values, '; match:', match, ' validation result:', result);

			return result;
		}

		Group.prototype.validate_all = function (forced, no_action)
		{
			var data = this.get_data();
			var or_result = [];
			var at_least_one_or_is_valid = false;

			// console.group(this.get_group().attr('class'));

			obj_foreach(this, data, function (action, rulesets)
			{
				rulesets.forEach(function (ruleset)
				{
					var and_result = [];

					ruleset.forEach(function (condition)
					{
						and_result.push(this.validate_condition(condition));
					}, this);

					or_result.push(and_result);

				}, this);

				// console.log(or_result, data);

				if ( ! no_action)
				{
					or_result.forEach(function (and_result)
					{
						if (and_result.indexOf(false) === -1)
						{
							at_least_one_or_is_valid = true;
						}
					});

					if (at_least_one_or_is_valid)
					{
						this.trigger_action(action, forced)
					} else
					{
						this.trigger_action(action === 'show' ? 'hide' : 'show', forced);
					}
				}
			});

			console.groupEnd();

			return or_result;
		}
		
		Group.prototype.update = function (forced)
		{
			//see init notes
			var $group = this.get_group();

			if ( ! this.initialized($group))
			{
				$group.addClass('ether-cond-group-id-' + this.get_id()); 
			}

			this.validate_all(forced);
		}

		Group.prototype.init = function ()
		{
			var $group = this.get_group();
			var data;
			
			if (this.initialized($group))
				return false;

			data = this.decode($group[0].className);
			this.set_data(data);

			// at this point group is not yet added to the collection and doesn't have an id
			// apply it on first update instead
			// $group.addClass('ether-cond-group-id-' + this.get_id()); 

			return this;
		}

		var Field = function ($field, parent)
		{
			this.$field = $field;
			this.parent = parent;
			this.groups = [];
		}
		Field.prototype = new Collection_Elem();

		Field.prototype.get_name = function ()
		{
			return this.$field.cattr('ether-field');
		}

		Field.prototype.get_field = function ()
		{
			return this.$field;
		}

		Field.prototype.get_label = function ()
		{
			return this.get_field().closest('label');
		}

		Field.prototype.get_val = function ()
		{
			var val;
			var is_checkbox;
			var $field = this.$field;

			is_checkbox = $field.is('input') && $field.attr('type') == 'checkbox';
			val = get_field_val($field);

			if (is_checkbox)
			{
				if ($field.attr('checked'))
				{
					val = 'on';
				} else
				{
					val = 'off';
				}
			}

			return val;
		}

		Field.prototype.get_groups = function (get_data)
		{
			var data = {};
			var groups = this.groups;
			var parent = this.parent;

			if ( ! get_data)
			{
				return groups;
			} else
			{
				groups.forEach(function (id)
				{
					data[id] = parent.get_group(id);
				});
			}

			return data;
		}

		Field.prototype.get_data = function ()
		{
			var result = {};
			var props = ['name', 'val', 'groups', 'field'];

			props.forEach(function (prop)
			{
				result[prop] = this['get_' + prop]();
			}, this);

			return result;
		}

		Field.prototype.add_group = function (group_id)
		{
			var groups = this.groups;

			$.inArray(group_id, groups) === -1 ? groups.push(group_id) : '';
			// console.log('field: ', this.get_name(), 'add group:', group_id, groups)
		}

		Field.prototype.get_field_count = function (state)
		{
			var count = 0;

			// console.log('update count: ' + this.get_id());

			obj_foreach(this, this.get_groups(true), function (id, group)
			{
				count += group.count_fields(state);
			});

			return count;
		}

		Field.prototype.update_counter = function (state)
		{
			// this.get_label().find('.ether-cond-field-counter').text(' [' + this.get_field_count() + ']');
			this.get_label().find('.ether-cond-field-counter').text(this.get_field_count(state));
		}

		Field.prototype.update = function ()
		{
			var groups = this.get_groups(true);

			obj_foreach(this, groups, function (id, group)
			{
				group.update();
			});
		}

		Field.prototype.init = function ()
		{
			var $label = this.get_label();

			if ( ! $label.children('.ether-cond-field-counter').length)
			{
				$label.children('.label-title').length ? $label = $label.children('.label-title') : '';
				$label.append('<div class="ether-cond-field-counter">');

				batch_tooltip.init({
					$scope: $('body'),
					selector: '.ether-cond-field-counter',
					content: 'related hidden fields count',
					y_dir: 0,
					style: 'plain',
					size: 'small',
					theme: 'dark',
					tip: 'bottom',
					par_y: -1.1
				});
			}

			return this;
		}

		var expr = 
		{
			action: new RegExp('ether-action-(\\w+)-'),
			field_name: new RegExp('-ether-field-((?:\\w|\\d|-)+)$'),
			filter: new RegExp('ether-filter-(\\w+)'),
			values: new RegExp('(ether-cond-.*?)-ether-field-')
		}

		var instances = new Collection();

		return {
			get_instance: function (id)
			{
				return instances.get(id);
			},

			init: function ($scope)
			{
				var instance = new Instance($scope).register();

				if (instance)
				{
					instances.add(instance);
				} 

				// console.log(instances.get());

				return this;
			},

			init_gui: function ()
			{
				$('body')
					.on('ether_cond_group_show', function (evt, $group, group)
					{
						obj_foreach(group, group.get_field(), function (id, field)
						{
							field.update_counter(false);
						});
					})
					.on('ether_cond_group_hide', function (evt, $group, group)
					{
						obj_foreach(group, group.get_field(), function (id, field)
						{
							field.update_counter(false);
						});
					});

				return this;
			}
		}
	}().init_gui();

	var farbtastic =
	{
		farbtastic_no_update: false,

		prepare: function ()
		{
			if ( ! $.farbtastic)
				return;
			
			var color = $(this).children('input').eq(0).val();

			if ($(this).children('.ether-farbtastic').length == 0)
			{
				$(this).children('input').before('<span class="ether-farbtastic-trigger" style="background-color: ' + (color == '' ? '#ffffff' : color) + ';"></span>');
				$(this).children('input').before('<div class="ether-farbtastic" style="display: none; position: absolute; z-index: 50;"></div>');
			}
		},

		init: function ()
		{
			if ( ! $.farbtastic)
				return;

			var $trigger = $(this).prev('.ether-farbtastic-trigger');
			var $input = $(this).next('input');

			if (typeof $(this).get(0).farbtastic == 'undefined')
			{
				$(this).farbtastic($trigger);
			}

			$(this).get(0).farbtastic.setColor($input.val());
			$input.change().blur();
		},

		init_gui: function ()
		{
			if ( ! $.farbtastic)
				return;

			var self = this;

			$('label.ether-color').each(self.prepare);

			$('.ether-farbtastic').each(self.init);

			$('.ether-farbtastic-trigger').live('click', function()
			{
				var picker = $(this).next('.ether-farbtastic').get(0).farbtastic;

				if (typeof picker == 'undefined')
				{
					$(this).next('.ether-farbtastic').farbtastic($(this));
					picker = $(this).next('.ether-farbtastic').get(0).farbtastic;
					picker.setColor($(this).siblings('input').val());
				}

				$(this).next('.ether-farbtastic').fadeIn();

				return false;
			});
			
			$('.ether-color input').change( function()
			{
				if ( ! self.farbtastic_no_update)
				{
					var picker = $(this).prevAll('.ether-farbtastic').get(0).farbtastic;

					if (typeof picker == 'undefined')
					{
						$(this).prev('.ether-farbtastic').farbtastic($(this).prevAll('.ether-farbtastic-trigger'));
						picker = $(this).prev('.ether-farbtastic').get(0).farbtastic;
						//picker.setColor($(this).siblings('input').val());
					}
					picker.setColor($(this).val());

					var color = $(this).val();

					if (color.substring(0, 3) == 'rgb')
					{
						color = to_hex(color.replace('rgb(', '').replace(')', '').split(', '));
					}
				}

				self.farbtastic_no_update = false;
			});

			$('.ether-farbtastic').live('mousemove', function()
			{
				var $trigger = $(this).prev('.ether-farbtastic-trigger');
				var $input = $(this).next('input');
				var picker = $(this).get(0).farbtastic;

				var val = $input.val();
				var color = picker.color;

				if (color != val)
				{
					$input.val(color);
					self.farbtastic_no_update = true;
					$input.change();
				}
			});

			$(document).mousedown( function()
			{
				$('.ether-farbtastic:visible').each( function()
				{
					var picker = $(this).get(0).farbtastic;

					$(this).next('input').val(picker.color);
					$(this).fadeOut();
				});
			});

			//move this out of here into builder.js
			$('.widget-inside .builder-widget .builder-widget-actions .edit').live('click', function()
			{
				var $parent = $(this).closest('.builder-widget');

				var $color_fields = $parent.find('.ether-color');

				if ($color_fields.length > 0)
				{
					$color_fields.each(self.prepare);
					$color_fields.children('.ether-farbtastic').each(self.init);
				}
			});
		}
	}
	farbtastic.init_gui();

	var simple_tabs = function ()
	{
		return {

			id: 0,

			get_id: function ($elem)
			{
				return this.id;
			},

			next_id: function ()
			{
				this.id += 1;
				return this.id;
			},

			show_tab: function ($parent, tab_id, show_tab_callback)
			{
				if (tab_id === undefined)
					return;

				var window_pos_y = $(window).scrollTop();
				var parent_id = $parent.attr('class').match(/tabs-parent-id-(\d+)/)[1];
				var $tab_title = $parent.find('.ether-tab-title-id-' + parent_id);
				var $tab_content = $parent.find('.ether-tab-content-id-' + parent_id);

				// console.log('show tab', parent_id, tab_id, $tab_title.length, $tab_content.length);
				
				if ( ! $tab_title.eq(tab_id).hasClass('ether-current'))
				{
					$tab_title
						.removeClass('ether-current')
						.eq(tab_id).addClass('ether-current');

					$tab_content
						.filter('.ether-current')
						// .stop(true).fadeOut(250).end()
						// .eq(tab_id).addClass('ether-current').stop(true).fadeIn(250);
						.hide().end()
						.eq(tab_id).addClass('ether-current').show();

					show_tab_callback ? show_tab_callback($parent, $tab_content.eq(tab_id)) : '';

					// console.log('show tab: ' + tab_id, $tab_content.length);

					$(window).scrollTop(window_pos_y);
				}
			},

			dom_initialized: function ($parent)
			{
				return $parent.attr('data-ether-tabs') && $parent.attr('data-ether-tabs') === 'set';
			},

			gui_initialized: function ($parent)
			{
				return $parent.attr('data-ether-tabs-gui') && $parent.attr('data-ether-tabs-gui') === 'set';	
			},

			init_dom: function ($parent, $tab_title, $tab_content, tab_type, tab_align, show_tab_callback)
			{
				var self = this;

				if (this.dom_initialized($parent))
					return false;

				$tab_content
					.addClass('ether-tab-content ether-tab-content-id-' + self.get_id())
					.hide();

				$tab_title
					.addClass('ether-tab-title ether-tab-title-id-' + self.get_id());

				$tab_title.wrapAll('<div class="ether-tab-title-wrap"></div>');
				$tab_content.wrapAll('<div class="ether-tab-content-wrap"></div>');

				$.merge($tab_title.parent(), $tab_content.parent()).wrapAll('<div class="ether-tabs ether-tabs-orientation-' + tab_type + ' ether-tabs-orientation-' + tab_align + ' ether-tabs-id-' + self.get_id() + '"></div>');

				$parent
					.attr('data-ether-tabs', 'set')
					.addClass('ether-tabs-parent ether-tabs-parent-id-' + self.get_id());

				if ( show_tab_callback )
				{
					show_tab_callback($tab_title, $tab_content);
				}

				this.next_id();
			},

			init_gui: function ($parent, $tab_title, show_tab_callback)
			{
				var self = this;

				if (this.gui_initialized($parent))
					return false;

				$tab_title
					.on('click', function ()
					{
						self.show_tab($parent, $(this).index(), show_tab_callback);
					})

				$parent.attr('data-ether-tabs-gui', 'set');
			},

			init: function ($parent, title_selector, content_selector, visible_tab, tab_type, tab_align, show_tab_callback)
			{
				if ( ! $parent.length)
					return;

				show_tab_callback ? show_tab_callback = show_tab_callback : '';
				visible_tab = visible_tab || 0;
				tab_type = tab_type || 'x';
				tab_align = tab_align || 'left';

				var $tab_title = $parent.find(title_selector);
				var $tab_content = $parent.find(content_selector);

				this.init_dom($parent, $tab_title, $tab_content, tab_type, tab_align, show_tab_callback);
				this.init_gui($parent, $tab_title, show_tab_callback);
				this.show_tab($parent, visible_tab, show_tab_callback);

				// console.log($parent.length, $tab_title.length, $tab_content.length,)
			}
		}
	}();

	var get_window_bounds = function ()
	{
		return [0, 0, $(window).height(), $(window).width()];
	}

	var fit_bounds = function (ch, p, crop)
	{
		var adj_y;
		var adj_x;

		// console.log('fit bounds: ', ch, p);

		ch = ch.slice();

		if (crop)
		{
			ch[0] = Math.max(p[0], ch[0]);
			ch[1] = Math.max(p[1], ch[1]);
			ch[2] = Math.min(p[2], ch[2]);
			ch[3] = Math.min(p[3], ch[3]);
		} else
		{
			adj_y = Math.max(0, - (ch[0] - p[0]));
			adj_x = Math.max(0, - (ch[1] - p[1]));

			ch[0] += adj_y;
			ch[1] += adj_x;
			ch[2] += adj_y;
			ch[3] += adj_x;
			
			adj_y = Math.min(0, p[2] - ch[2]);
			adj_x = Math.min(0, p[3] - ch[3]);
			
			ch[0] += adj_y;
			ch[1] += adj_x;
			ch[2] += adj_y;
			ch[3] += adj_x;
		}

		// console.log('fit bounds: ', ch, '(<-result)', p);

		return ch;
	}

	var get_pos = function ($el, $rel)
	{
		$rel === true ? $rel = $(window) : '';

		var el = [];
		var rel;
		var a;

		if ($el[0] instanceof Window || $el[0] instanceof Document)
		{
			el[0] = $el.scrollTop();
			el[1] = $el.scrollLeft();
		} else
		{
			el[0] = $el.offset().top;
			el[1] = $el.offset().left;
		}

		if ( ! $rel)
			return el;

		// console.log('get_pos: ', $el.attr('class'));
		// console.log('raw: ', el)

		if ($rel)
		{
			rel = get_bounds($rel)

			el[0] -= rel[0];
			el[1] -= rel[1];

			// console.log('rel: ', el);
		}

		return el;
	}

	var get_bounds = function ($el, $rel, crop, crop_to_window)
	{
		$rel === true ? $rel = $(window) : '';

		var el = [];
		var rel;
		var a;

		if ($el[0] instanceof Window || $el[0] instanceof Document)
		{
			el[0] = $el.scrollTop();
			el[1] = $el.scrollLeft();
			el[2] = el[0] + $(window).height();
			el[3] = el[1] + $(window).width();
		} else
		{
			el[0] = $el.offset().top;
			el[1] = $el.offset().left;
			el[2] = el[0] + $el.height();
			el[3] = el[1] + $el.width();
		}

		if ( ! $rel && ! crop)
			return el;

		// console.log('get_bounds: ', $el.attr('class'))//, $el.text());
		// console.log('raw: ', el);

		if ($rel)
		{
			rel = get_bounds($rel)

			el[0] -= rel[0];
			el[1] -= rel[1];
			el[2] -= rel[0];
			el[3] -= rel[1];

			// console.log('rel: ', el);

			if (crop)
			{
				el[0] = Math.min(Math.max(0, el[0]), rel[2]);
				el[1] = Math.min(Math.max(0, el[1]), rel[3]);
				el[2] = Math.max(Math.min(el[2], rel[2]), rel[0]);
				el[3] = Math.max(Math.min(el[3], rel[3]), rel[1]);

				// console.log('crop: ', el);

				if (crop_to_window)
				{
					a = get_window_bounds();
					el = fit_bounds(el, a);
					
					// console.log('absolute: ', el);
				}
			}
		}

		return el;
	}

	var fit_in_bounds = function ($child, parent)
	{
		var child = get_bounds($child, true);
		var fit = fit_bounds(child, parent);

		// console.log('fit in bounds: ','c', child, 'p', parent, 'result: ', fit);

		$child.css({
			top: child[0],
			left: child[1]
		});

		return child;
	}


	var rel_shift_pos = function (pos, $el, x_dir, y_dir)
	{
		var shift_y, shift_x;

		pos = pos.slice();

		y_dir = y_dir || 0;
		x_dir = x_dir || 0;

		shift_y = y_dir * ($el.height() + 8);
		shift_x = x_dir * ($el.width() + 8);

		pos[0] += shift_y;
		pos[1] += shift_x;
		pos[2] ? pos[2] += shift_y : '';
		pos[3] ? pos[3] += shift_x : '';

		return pos;
	}

	var pos_to_bounds = function (pos, $el)
	{
		pos = pos.slice();

		// console.log('pos to bounds: ', pos);
		
		pos[2] = pos[0] + $el.height();
		pos[3] = pos[1] + $el.width();

		// console.log('pos to bounds: ', pos);

		return pos;
	}

	function img_placeholder_src(src, force)
	{
		if (typeof force == 'undefined')
		{
			force = false;
		}

		if (src != null && typeof src.length != 'undefined')
		{
			if (src.length > 0 && (src.match(/^(?:.*?)\.?(youtube|vimeo)\.com\/(watch\?[^#]*v=(\w+)|(\d+)).+$/) || src.match ((/^(?:.*?)\.?(ted)\.com\//))))
			{
				return ether.placeholder.video;
			} else if (force || src.length == '')
			{
				return ether.placeholder.img;
			}
		}

		return src;
	}

	window.wp_send_to_editor = window.send_to_editor;

	window.send_to_editor = function(html)
	{
		/*if (typeof o == "object" || typeof o == "array")
		{
			if (ether.upload_dst == null)
			{
				for (var i = 0; i < o.length; i++)
				{
					add_image(o[i]);
				}
			} else
			{
				if (o.length > 0)
				{
					var src = o[0];

					$dst = $(ether.upload_dst);

					$dst.each( function()
					{
						if ($(this).is("img"))
						{
							$(this).attr("src", src).show();
						} else if ($(this).is("a"))
						{
							$(this).attr("href", src);
						} else if ($(this).is("input"))
						{
							$(this).val(src);
						}
					});
				}
			}

			tb_remove();
			ether.upload_dst = null;
		}*/

		if ((typeof html == "object" || typeof html == "array") && ether.editor == null)
		{
			if (typeof ether.upload_callback == 'string' && ether.upload_callback != '')
			{
				for (var i = 0; i < html.length; i++)
				{
					eval(ether.upload_callback + '(\'' + html[i] + '\');');
				}
			} else if (typeof ether.upload_callback == 'function')
			{
				for (var i = 0; i < html.length; i++)
				{
					ether.upload_callback(html[i]);
				}
			}

			if (ether.upload_dst != null)
			{
				if (html.length > 0)
				{
					var src = html[0];

					var $dst = $(ether.upload_dst);

					$dst.each( function()
					{
						if ($(this).is("img"))
						{
							$(this).attr("src", src).show();
						} else if ($(this).is("a"))
						{
							$(this).attr("href", src);
						} else if ($(this).is("input"))
						{
							$(this).val(src).change();
						}
					});
				}
			}

			tb_remove();
			ether.upload_dst = null;
			ether.upload_callback = '';
			ether.upload_caller = null;
		} else
		{
			if (ether.upload_dst != null)
			{
				var url = '';
				var alt = '';
				var title = '';

				if ($('img', html).length > 0)
				{
					url = $('img', html).attr('src');
					alt = $('img', html).attr('alt');
					title = $('img', html).attr('title');
				} else
				{
					url = html;
				}

				$dst = $(ether.upload_dst);
				$alt = $(ether.upload_dst + '_alt');
				$title = $(ether.upload_dst + '_title');

				$dst.each( function()
				{
					if ($(this).is('img'))
					{
						$(this).attr('src', url).show();
					} else if ($(this).is('a'))
					{
						$(this).attr('href', url);
					} else if ($(this).is('input'))
					{
						$(this).val(url).change();
					}
				});

				$alt.each( function()
				{
					if ($(this).is('img'))
					{
						$(this).attr('alt', alt).show();
					} else if ($(this).is('a'))
					{
						$(this).text(alt);
					} else if ($(this).is('input'))
					{
						$(this).val(alt);
					}
				});

				$title.each( function()
				{
					if ($(this).is('img'))
					{
						$(this).attr('title', title).show();
					} else if ($(this).is('a'))
					{
						$(this).attr('title', title);
					} else if ($(this).is('input'))
					{
						$(this).val(title);
					}
				});

				tb_remove();
				ether.upload_dst = null;
			} else
			{
				if (typeof tinyMCE != 'undefined' && tinyMCE.activeEditor != null)
				{
					if (typeof html == "object" || typeof html == "array")
					{
						for (var i = 0; i < html.length; i++)
						{
							if (window.tinyMCE.majorVersion >= 4)
							{
								tinyMCE.execCommand("mceInsertContent", false, '<a href="' + html[i] + '" class="alignleft"><img src="' + html[i] + '" alt="" width="300" /></a>');
							} else
							{
								tinyMCE.execInstanceCommand(tinyMCE.activeEditor.id, "mceInsertContent", false, '<a href="' + html[i] + '" class="alignleft"><img src="' + html[i] + '" alt="" width="300" /></a>');
							}
						}
					} else
					{
						if (window.tinyMCE.majorVersion >= 4)
						{
							tinyMCE.execCommand("mceInsertContent", false, html);
						} else
						{
							tinyMCE.execInstanceCommand(tinyMCE.activeEditor.id, "mceInsertContent", false, html);
						}
					}

					tb_remove();
				} else
				{
					window.wp_send_to_editor(html);

					if (html.indexOf('src=') != 1)
					{
						if ($(html).is('img'))
						{
							html = $(html).attr('src');
						} else
						{
							html = $(html).find('img').attr('src');
						}
					}

					if (typeof ether.upload_callback == 'string' && ether.upload_callback != '')
					{
						eval(ether.upload_callback + '(\'' + html + '\');');
					} else if (typeof ether.upload_callback == 'function')
					{
						ether.upload_callback(html);
					}

					ether.upload_callback = '';

					tb_remove();
				}
			}
		}

		ether.editor = null;
	};

	var unit = function (value, default_unit)
	{
		var v,u;

		if (value === '')
			return value;

		value += '';
		v = value.match(/(\d+)/);
		v !== undefined ? v = v[1] : '';
		u = value.replace(v, '');
		! u.length ? u = default_unit : '';

		value = parseInt(value, 10) + u;

		return value;
	}

	var utils = function ()
	{
		return {
			init_utils: function (data)
			{
				obj_foreach(this, data, function (name, fn)
				{
					! this[name] ? this[name] = fn : '';
				});
			}
		}
	}();

	utils.init_utils({
		obj_foreach: obj_foreach,
		unit: unit,
		capitalize: capitalize,
		scheduler: scheduler,
		get_attr_val: get_attr_val,
		get_field_val: get_field_val,
		get_text: get_text,
		fit_bounds: fit_bounds,
		get_bounds: get_bounds,
		fit_in_bounds: fit_in_bounds,
		rel_shift_pos: rel_shift_pos,
		tooltip: tooltip,
		batch_tooltip: batch_tooltip,
		dynamic_label: dynamic_label,
		toggle_checkbox: toggle_checkbox,
		cond_fields: cond_fields,
		farbtastic: farbtastic,
		simple_tabs: simple_tabs
	});

	if (ether.utils)
	{
		utils.obj_foreach(null, utils, function (k, v)
		{
			if ( ! ether.utils[k])
				ether.utils[k] = v;
		});
	} else
	{
		ether.utils = utils;
	}

	// OMG FIX FOR ITHEMES BUILDER

	if (typeof(tb_showIframe) != 'undefined')
	{
		var ether_tb_showIframe = tb_showIframe;

		tb_showIframe = function()
		{
			ether_tb_showIframe();
			tb_position();

			setTimeout( function()
			{
				tb_position();
			}, 10);
		};

		// var ether_tb_showIframe = tb_showIframe;
	}

	// quick fix
	$('.inside > .ether-form, #form .inside .builder-location-wrapper').each( function()
	{
		$(this).parent().addClass('ether-inside');
	});

	$('input[name=reset]').click( function()
	{
		if ( ! confirm('Are you sure you want to reset settings on this page? \'Cancel\' to stop, \'OK\' to reset.'))
		{
			return false;
		}
	});

	$('img.upload_image')
		.each( function()
		{
			var src = img_placeholder_src($(this).attr('src'));

			$(this).attr('src', src);
		})

	$('.ether img').load( function()
	{
		$(this).show();
	});

	$('.ether .hidden').hide();

	$('.remove_image').click( function()
	{
		var name = $(this).attr('name').replace('#', '').replace(ether.prefix + 'remove_', '');

		$('input.upload_' + name).val('').change();
		$('input.upload_' + name + '_alt').val('');
		$('input.upload_' + name + '_title').val('');

		$('img.upload_' + name).attr('src', ether.placeholder.img);

		return false;
	});

	$(document)
		.on('click', '.confirm', function()
		{
			if ( ! confirm('Are you sure?'))
			{
				return false;
			}
		})
		.on('click', 'img.upload_image', function()
		{
			var src = img_placeholder_src($(this).attr('src'), true);

			$(this).attr('src', src);
		})
		.on('change', 'img.upload_image', function()
		{
			var src = img_placeholder_src($(this).val());

			$(this).closest('.group-item-content').find('img.upload_image').attr('src', src);
		})
		.on('click', '.wp-editor-tools .add_media', function()
		{
			ether.editor = tinyMCE.activeEditor;
		})
		.on('click', '.upload_image', function()
		{
			if ($(this).is('button'))
			{
				var name = $(this).attr('name');
				var width = $(this).cattr('width');
				var height = $(this).cattr('height');
				var single = $(this).hasClass('single');
				var tab = $(this).cattr('tab');
				var callback = $(this).cattr('callback');

				if (tab == '')
				{
					tab = 'images';
				}

				if ( ! callback)
				{
					ether.upload_dst = '.' + name.replace(ether.prefix, '').replace('[', '\[').replace(']', '\]');

					ether.upload_callback = '';
				} else
				{
					ether.upload_dst = null;
					ether.upload_callback = callback;
				}

				ether.upload_caller = $(this);

				tb_show('', 'media-upload.php?&type=image&post_id=0&ether=true&output=html&width=' + width + '&height=' + height + '&tab=' + tab + '&single=' + single + '&TB_iframe=true');

				return false;
			}
		})
		.on('click', '.upload_media', function()
		{
			if ($(this).is('button'))
			{
				var name = $(this).attr('name');
				ether.upload_dst = '.' + name.replace(ether.prefix, '');

				tb_show('', 'media-upload.php?&post_id=0&ether=true&TB_iframe=true');

				return false;
			}
		});
});

})(jQuery);