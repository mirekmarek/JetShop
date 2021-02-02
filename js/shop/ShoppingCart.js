const ShoppingCart = {
	base_URL: '',

	buy: function( product_id, qty ) {

		$.ajax({
			type: "GET",
			url: ShoppingCart.base_URL+'?buy='+product_id+'&gty='+qty,
			dataType:'json',
			beforeSend: function(){
			},
			success: function( response ) {

				ShoppingCart._handleResponse( response );

				if(response['ok']) {
					$('#shopping_cart_popup').modal('show');
				}
			},
			complete: function(){
			}
		});
	},


	increment: function( product_id ) {
		let qty = $('#cart_quantity_'+product_id).val();
		qty++;

		ShoppingCart._setQty( product_id, qty );
	},

	decrement: function( product_id ) {
		let qty = $('#cart_quantity_'+product_id).val();
		qty--;

		ShoppingCart._setQty( product_id, qty );
	},

	_setQty: function( product_id, qty ) {
		$.ajax({
			type: "GET",
			url: ShoppingCart.base_URL+'?set_qty='+product_id+'&gty='+qty,
			dataType:'json',
			beforeSend: function(){
			},
			success: function( response ) {
				ShoppingCart._handleResponse( response );
			},
			complete: function(){
			}
		});
	},

	remove: function( product_id ) {
		$.ajax({
			type: "GET",
			url: ShoppingCart.base_URL+'?remove='+product_id,
			dataType:'json',
			beforeSend: function(){
			},
			success: function( response ) {
				ShoppingCart._handleResponse( response );
			},
			complete: function(){
			}
		});
	},


	_handleResponse: function (response) {
		if(response['ok']) {

			for( let id in response['snippets'] ) {
				$('#'+id).html(response['snippets'][id]);
			}
			//TODO: $('#shopping_cart_error_message').html( '' );

		} else {
			//TODO: $('#shopping_cart_error_message').html( response['error_message'] );
		}
	}

};