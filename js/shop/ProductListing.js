const ProductListing = {
	set_filter_url: '',
	state: {},

	selected_filter_group_id: '',

	init: function( set_filter_url, state )
	{
		ProductListing.set_filter_url = set_filter_url;
		ProductListing.state = state;
	},

	selectFilterGroup: function( group_id )
	{
		this.selected_filter_group_id = group_id;

		this._actualizeFilterGroup();
	},

	_actualizeFilterGroup: function() {
		let group_id = this.selected_filter_group_id;
		if(!group_id) {
			return;
		}

		$('.group-content.selected').removeClass('selected');
		$('#group-content-' + group_id).addClass('selected');
		$('.group-label.selected').removeClass('selected');
		$('#group-label-' + group_id).addClass('selected');

	},

	_actualize: function() {

		$.ajax({
			type: 'POST',
			url: ProductListing.set_filter_url,
			data: {
				filter: JSON.stringify( ProductListing.state )
			},
			dataType:'json',
			beforeSend: function(){
				//$('.content-for-filter #filter-loading').show();
			},
			success: function(data) {

				ProductListing._applyState( data );
				ProductListing._applyHistoryState( data );
			},
			complete: function(){
				//$('.content-for-filter #filter-loading').hide();
			}
		});
	},

	_applyState: function( data ) {

		$('#product_listing_filter_area').html( data['filter_snippet'] );
		$('#product_listing_list_area').html( data['list_snippet'] );

		ProductListing._actualizeFilterGroup();


		if(data['title']) {
			document.title = data['title'];
		}

		if(data['h1']) {
			$('.ith1').html(data['h1']);
			$('#listing_title').html(data['h1']);
		}

		if(data['description']) {
			$('meta[name=description]').remove();
			$('head').append( '<meta name="description" content="'+data['description']+'">' );
		}

	},

	_applyHistoryState: function( data ) {
		window.history.pushState(data,"", data.URL);

		window.onpopstate = function(e){
			if(e.state){
				ProductListing._applyState(e.state);
			}
		};
	},


	filter: {
		deactivate: function() {

			ProductListing.filter.properties._deactivate();
			ProductListing.filter.price._deactivate();
			ProductListing.filter.brands._deactivate();
			ProductListing.filter.flags._deactivate();

			ProductListing.pagination.setPage( 1 );
			ProductListing._actualize();
		},

		properties: {
			_deactivate: function() {
				if(ProductListing.state['properties']) {
					for( var property_id in  ProductListing.state.properties) {
						if(ProductListing.state.properties[property_id]['options']!==undefined) {
							ProductListing.state.properties[property_id] = {
								is_active: false,
								options: []
							};
						} else
						if(ProductListing.state.properties[property_id]['filter_min']!==undefined) {
							ProductListing.state.properties[property_id] = {
								is_active: false,
								filter_min: 0,
								filter_max: 0
							};
						} else {
							ProductListing.state.properties[property_id] = {
								is_active: false
							};

						}

					}
				}
			},

			option: {
				enable: function( property_id, option_id )
				{
					$('#filter_option_'+property_id+'_'+option_id).attr('checked', true);

					ProductListing.state.properties[property_id].options.push( option_id );
					ProductListing.state.properties[property_id].is_active = true;

					ProductListing.pagination.setPage( 1 );
					ProductListing._actualize();
				},

				disable: function( property_id, option_id )
				{
					$('#filter_option_'+property_id+'_'+option_id).attr('checked', false);

					var new_options = [];
					for(var c=0;c<ProductListing.state.properties[property_id].options.length;c++) {
						var _id = ProductListing.state.properties[property_id].options[c];
						if(_id!=option_id) {
							new_options.push(_id);
						}
					}

					ProductListing.state.properties[property_id].options = new_options;
					ProductListing.state.properties[property_id].is_active = new_options.length>0;

					ProductListing.pagination.setPage( 1 );
					ProductListing._actualize();
				},

				toggle: function( property_id, option_id )
				{
					var selected = ProductListing.state.properties[property_id].options.includes( option_id );

					if(selected) {
						ProductListing.filter.properties.option.disable( property_id, option_id );
					} else {
						ProductListing.filter.properties.option.enable( property_id, option_id );
					}

				},

				deactivate: function( property_id ) {
					ProductListing.state.properties[property_id].options = [];
					ProductListing.state.properties[property_id].is_active = false;

					ProductListing.pagination.setPage( 1 );
					ProductListing._actualize();

				}
			},

			number: {
				init: function( property_id, min_value, max_value, filter_min, filter_max  )
				{
					$('#filter_slider_range_'+property_id).slider({
						range: true,
						min: min_value,
						max: max_value,
						step: 1,
						values: [filter_min,filter_max],
						disabled: false,
						slide: function(event,ui){
							ProductListing.filter.properties.number.set(
								property_id,
								ui.values[0],
								ui.values[1]
							);
						},
						stop: function(){
							ProductListing.filter.properties.number.setDone();
						}
					});
				},

				set: function( property_id, filter_min, filter_max )
				{
					ProductListing.state.properties[property_id] = {
						is_active: true,
						filter_min: filter_min,
						filter_max: filter_max
					};

					$('#filter_value_from_'+property_id).text(filter_min);
					$('#filter_value_to_'+property_id).text(filter_max);
				},

				setDone: function()
				{
					ProductListing.pagination.setPage( 1 );
					ProductListing._actualize();
				},

				deactivate: function( property_id ) {
					ProductListing.state.properties[property_id] = {
						is_active: false
					};

					ProductListing.pagination.setPage( 1 );
					ProductListing._actualize();

				}

			},

			bool: {
				enable: function(property_id)
				{
					ProductListing.state.properties[property_id].is_active = true;
					ProductListing.pagination.setPage( 1 );
					ProductListing._actualize();
				},

				disable: function(property_id)
				{
					ProductListing.state.properties[property_id].is_active = false;
					ProductListing.pagination.setPage( 1 );
					ProductListing._actualize();
				},

				toggle: function( property_id )
				{
					if(ProductListing.state.properties[property_id].is_active) {
						ProductListing.filter.properties.bool.disable(property_id);
					} else {
						ProductListing.filter.properties.bool.enable(property_id);
					}
				},

				deactivate: function( property_id ) {
					ProductListing.state.properties[property_id].is_active = false;

					ProductListing.pagination.setPage( 1 );
					ProductListing._actualize();

				}

			}
		},

		brands: {
			_deactivate: function() {
				ProductListing.state.brands.active = [];
			},

			toggleBrand: function( brand_id )
			{

				var selected = ProductListing.state.brands.active.includes( brand_id );

				if(selected) {
					ProductListing.filter.brands.disable( brand_id );
				} else {
					ProductListing.filter.brands.enable( brand_id );
				}

			},

			disable: function( brand_id )
			{
				$( '#filter_brand_'+brand_id ).attr('checked', false);

				var new_active = [];
				for(var c=0;c<ProductListing.state.brands.active.length;c++) {
					var _id = ProductListing.state.brands.active[c];
					if(_id!=brand_id) {
						new_active.push(_id);
					}

				}

				ProductListing.state.brands.active = new_active;

				ProductListing.pagination.setPage( 1 );
				ProductListing._actualize();
			},

			enable: function( brand_id )
			{
				$( '#filter_brand_'+brand_id ).attr('checked', true);

				ProductListing.state.brands.active.push( brand_id );

				ProductListing.pagination.setPage( 1 );
				ProductListing._actualize();
			}
		},

		price: {
			_deactivate: function() {
				if(ProductListing.state['price']) {
					ProductListing.state.price = {
						is_active: false,
						from: 0,
						to: 0
					};
				}
			},

			init: function( min, max, value_min, value_max, disabled, step )
			{
				$('#slider-range').slider({
					range: true,
					min: min,
					max: max,
					step: step,
					values: [value_min,value_max],
					disabled: disabled,
					slide: function(event,ui){
						ProductListing.filter.price.set( ui.values[0], ui.values[1] );
					},
					stop: function(i){
						ProductListing.filter.price.setDone();
					}
				});
			},

			set: function( from, to )
			{
				$('#amount-from').text( from );
				$('#amount-to').text( to );

				ProductListing.state.price.is_active = true;
				ProductListing.state.price.from = from;
				ProductListing.state.price.to = to;

			},

			setDone: function()
			{
				ProductListing.pagination.setPage( 1 );
				ProductListing._actualize();
			},

			deactivate: function()
			{
				ProductListing.state.price.is_active = false;
				ProductListing.state.price.from = 0;
				ProductListing.state.price.to = 0;

				ProductListing.pagination.setPage( 1 );
				ProductListing._actualize();
			}
		},

		flags: {
			_deactivate: function () {
				ProductListing.state.flags.active = [];
			},

			enable: function( flag_id )
			{
				$('#filter_flag_'+flag_id ).attr('checked', true);

				ProductListing.state.flags.active.push( flag_id );

				ProductListing.pagination.setPage( 1 );
				ProductListing._actualize();
			},

			disable: function( flag_id )
			{
				$('#filter_flag_'+flag_id ).attr('checked', false);

				var new_active = [];
				for(var c=0;c<ProductListing.state.flags.active.length;c++) {
					var _id = ProductListing.state.flags.active[c];
					if(_id!=flag_id) {
						new_active.push(_id);
					}
				}

				ProductListing.state.flags.active = new_active;

				ProductListing.pagination.setPage( 1 );
				ProductListing._actualize();
			},

			toggle: function( flag_id )
			{
				var selected = ProductListing.state.flags.active.includes( flag_id );

				if(selected) {
					ProductListing.filter.flags.disable( flag_id );
				} else {
					ProductListing.filter.flags.enable( flag_id );
				}

			},

			deactivate: function() {
				ProductListing.state.flags.active = [];

				ProductListing.pagination.setPage( 1 );
				ProductListing._actualize();

			}
		}
	},

	setSort: function( option_id ) {
		ProductListing.state.selected_sort = option_id;
		ProductListing.pagination.setPage( 1 );
		ProductListing._actualize();
	},

	pagination: {
		setPage: function( page_no )
		{
			ProductListing.state.pagination.page_no = page_no;
		},
		
		go: function( page_no ) 
		{
			ProductListing.pagination.setPage( page_no );
			ProductListing._actualize();

			$([document.documentElement, document.body]).animate({
				scrollTop: $("#product_listing_filter_area").offset().top
			}, 1000);
		}
	}

};

