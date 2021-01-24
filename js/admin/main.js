var UI = {
	mainToolbarPin: function() {
		var main_toolbar = document.getElementById("main-toolbar");
		if(!main_toolbar) {
			return;
		}
		var main_col = document.getElementById("main-col");
		var main_toolbar_initial_offset = main_toolbar.offsetTop;
		var main_toolbar_height = main_toolbar.offsetHeight;
		var main_col_initial_padding_top = main_col.style.paddingTop;

		window.onscroll = function() {
			if (window.pageYOffset > main_toolbar_initial_offset) {
				main_toolbar.classList.add("main-toolbar-sticky");
				main_col.style.paddingTop = main_toolbar_height+'px';
			} else {
				main_toolbar.classList.remove("main-toolbar-sticky");
				main_col.style.paddingTop = main_col_initial_padding_top;
			}
		};
	}
};

JetAjaxForm.defaultHandlers = {
	showProgressIndicator: function( form ) {
		$('#__progress__').show();
		$('#__progress_prc__').html('');
	},

	hideProgressIndicator: function( form ) {
		$('#__progress__').hide();
	},

	onProgress: function( form, percent ) {
		$('#__progress_prc__').html(percent+'%');
	},

	onSuccess: function( form, response_data ) {
	},

	onFormError: function( form, response_data ) {
	},

	onAccessDenied: function( form ) {
		alert('Access denied!');
	},

	onError: function( form ) {
		alert('Unknown error ...');
	}
};


var urlPathPart = {
	afterNameChanged: function( name, url_path_part_input_id, shop_id ) {

		$.ajax({
			url: "?action=generate_url_path_part&name="+encodeURIComponent(name)+'&shop_id='+shop_id,
			dataType: 'json',
			success: function( data ){
				$('#'+url_path_part_input_id).val(data.url_path_part);
			}
		});
	},

	afterPathPartChanged: function( input, shop_id ) {
		$.ajax({
			url: "?action=generate_url_path_part&name="+encodeURIComponent(input.value)+'&shop_id='+shop_id,
			dataType: 'json',
			success: function( data ){
				input.value = data.url_path_part;
			}
		});
	}
};


