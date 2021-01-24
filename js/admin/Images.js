var Images = {
	delete_form_id: null,

	uploadImage: function( form_id, entity, image_class, shop_id ) {
		JetAjaxForm.submit(form_id );
	},

	deleteImage: function(form_id, entity, image_class, shop_id) {
		Images.delete_form_id = form_id;

		var img_url = $('#image_'+entity+'_'+image_class+'_'+shop_id+'_image').data('url');

		$('#image_delete_confirm_thb').css('background-image', 'url(' + img_url + ')');

		$('#image_delete_confirm').modal('show');

	},

	deleteImageConfirm: function () {
		JetAjaxForm.submit( Images.delete_form_id );
		$('#image_delete_confirm').modal('hide');
	},

	deleteImageReject: function () {
		$('#image_delete_confirm').modal('hide');
	}
};
