if(!window['Whisperers']) {

	window['Whisperers'] = {};

	window['Whisperer'] = function (
		input_id,
		whisperer_items_area_id,
		data_request_url
	) {
		const KEY_ARROW_UP = 38;
		const KEY_ARROW_DOWN = 40;
		const KEY_ENTER = 13;
		const KEY_ESCAPE = 23;

		this.input_field = $('#' + input_id);

		this.whisperer_items_area_id = whisperer_items_area_id;
		this.data_request_url = data_request_url;

		this.item_class = 'search-whisperer-item';
		this.selected_item_class = 'search-whisperer-item-selected';
		this.message_class = 'search-whisperer-message';
		this.container_height_multiple = 6;
		this.selected_row_positioning = 2;
		this.min_str_len = 2;

		this.enabled = true;

		this.urlGenerator = null;

		this._xhr = null;
		this._cancel_timeout_handler = null;
		this._whisperer_timeout_handler = null;
		this._original_blur_handlers = null;


		this.onItemFocus = function (item_node, whisperer) {
		};

		this.onItemSelect = function (item_node, whisperer) {
		};

		this.onCancel = function (whisperer) {
		};

		this.onHide = function (whisperer) {
		};
		this.onShow = function (whisperer) {
		};


		const whisperer = this;

		this.init = function () {
			whisperer.input_field.attr('autocomplete', 'off');

			whisperer.whisperer_items_area = $('#' + whisperer.whisperer_items_area_id);

			whisperer.input_field.keyup(function (event) {
				if (!whisperer.enabled) {
					return true;
				}

				if (
					event.keyCode === KEY_ARROW_DOWN ||
					event.keyCode === KEY_ARROW_UP ||
					event.keyCode === KEY_ENTER ||
					event.keyCode === KEY_ESCAPE
				) {
					return false;
				}

				whisperer.whisper();

			});


			whisperer._original_blur_handlers = [];

			const orig_blur_handlers = jQuery._data(whisperer.input_field[0], "events")['blur'];

			if (orig_blur_handlers) {
				for (let c = 0; c < orig_blur_handlers.length; c++) {
					whisperer._original_blur_handlers.push(orig_blur_handlers[c].handler)
				}
				whisperer.input_field.unbind('blur');
			}

			whisperer.input_field.blur(function (e) {
				if (whisperer._whisperer_timeout_handler) {
					clearTimeout(whisperer._whisperer_timeout_handler);
					whisperer._whisperer_timeout_handler = null;
				}

				if (whisperer._cancel_timeout_handler) {
					clearTimeout(whisperer._cancel_timeout_handler);
				}

				whisperer._cancel_timeout_handler = setTimeout(function () {
					whisperer._handleCancel(e);
					whisperer._cancel_timeout_handler = null;
				}, 200);
			});

			whisperer.input_field.focus(function (e) {
				if (whisperer._cancel_timeout_handler) {
					clearTimeout(whisperer._cancel_timeout_handler);
				}
			});


			$(document).keydown(function (event) {
				if (event.target !== whisperer.input_field[0]) {
					return true;
				}

				const selected_row = whisperer.whisperer_items_area.find('.' + whisperer.selected_item_class);

				switch (event.keyCode) {
					case KEY_ENTER:
						if (selected_row.length) {
							whisperer._handleItemSelect(selected_row);
						}
						return false;
					case KEY_ARROW_DOWN:
						if (selected_row.length) {
							const next_row = selected_row.next('.' + whisperer.item_class);

							if (next_row.length) {
								whisperer._handleItemFetch(next_row);
							}
						}
						return false;
					case KEY_ARROW_UP:
						if (selected_row.length) {
							const prev_row = selected_row.prev('.' + whisperer.item_class);
							if (prev_row.length) {
								whisperer._handleItemFetch(prev_row);
							}
						}
						return false;
					case KEY_ESCAPE:
						whisperer._handleCancel();
						return false;
				}


				return true;
			});

		};

		this.whisper = function () {
			if (whisperer.input_field.attr('readonly')) {
				return;
			}

			if (whisperer._whisperer_timeout_handler) {
				clearTimeout(whisperer._whisperer_timeout_handler);
				whisperer._whisperer_timeout_handler = null;
			}

			whisperer._whisperer_timeout_handler = setTimeout(function () {
				whisperer._whisperer_timeout_handler = null;

				if (whisperer._xhr) {
					whisperer._xhr.abort();
				}

				const value = whisperer.input_field.val();

				if (value.length >= whisperer.min_str_len) {
					let url = whisperer.data_request_url + encodeURIComponent(value);

					if (whisperer.urlGenerator) {
						url = whisperer.urlGenerator(value);
					}

					whisperer._xhr = $.ajax({
						url: url,
						success: function (data) {
							whisperer._onLoad(data);
						}
					});

					whisperer._show();
				} else {
					whisperer.hide();
				}
			}, 200);

		};

		this.hide = function () {
			whisperer.whisperer_items_area.hide();
			whisperer.onHide(whisperer);
		};

		this._show = function () {
			if (!whisperer.whisperer_items_area.is(":visible")) {
				whisperer.whisperer_items_area.show();
				whisperer.onShow(whisperer);
			}
		};

		this._onLoad = function (snippet) {
			whisperer.whisperer_items_area.html(snippet);

			whisperer.whisperer_items_area.scrollTop(0);

			const selected_row = whisperer.whisperer_items_area.find('.' + whisperer.selected_item_class);
			if (selected_row.length) {
				const row_height = selected_row.innerHeight();
				whisperer.whisperer_items_area.height(row_height * whisperer.container_height_multiple);

				whisperer.whisperer_items_area.find('.' + whisperer.item_class).click(function (e) {
					const row = $(e.currentTarget);
					whisperer._handleItemSelect(row);
					window['event']['stopPropagation']();
				});
			} else {
				const message = whisperer.whisperer_items_area.find('.' + whisperer.message_class);
				if (message.length) {
					whisperer.whisperer_items_area.height(message.innerHeight() + 2);
				}
			}
		};

		this._handleItemSelect = function (selected_row) {
			setTimeout(function () {
				whisperer.onItemSelect(selected_row, whisperer);
			}, 10);
		};

		this._handleCancel = function (e) {
			whisperer.hide();
			whisperer.onCancel(whisperer);

			if (e) {
				whisperer._handleCancel_callOriginalBlurHandlers(e);
			}
		};

		this._handleCancel_callOriginalBlurHandlers = function (e) {
			for (let c = 0; c < whisperer._original_blur_handlers.length; c++) {
				whisperer._original_blur_handlers[c](e);
			}
		};

		this._handleItemFetch = function (row) {

			const selected_row = whisperer.whisperer_items_area.find('.' + whisperer.selected_item_class);

			selected_row.removeClass(whisperer.selected_item_class);
			row.addClass(whisperer.selected_item_class);

			const row_height = row.innerHeight();
			whisperer.whisperer_items_area.height(row_height * whisperer.container_height_multiple);

			const row_top_offset = row.offset().top - whisperer.whisperer_items_area.offset().top;
			const ideal_row_position = row_height * whisperer.selected_row_positioning;
			const ideal_row_position_diff = row_top_offset - ideal_row_position;

			let scroll_to = whisperer.whisperer_items_area.scrollTop() + ideal_row_position_diff;

			if (scroll_to < row_height) {
				scroll_to = 0;
			}

			whisperer.whisperer_items_area.scrollTop(scroll_to);

			whisperer.onItemFocus(row, whisperer);

		};

	};
}
