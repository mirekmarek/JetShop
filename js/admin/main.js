const UI = {
	mainToolbarPin: function () {
		const main_toolbar = document.getElementById("main-toolbar");
		if (!main_toolbar) {
			return;
		}
		const main_col = document.getElementById("main-col");
		if(!main_col) {
			return;
		}

		const main_toolbar_initial_offset = main_toolbar.offsetTop;
		const main_toolbar_height = main_toolbar.offsetHeight;
		const main_col_initial_padding_top = main_col.style['paddingTop'];

		window.onscroll = function () {
			if (window.pageYOffset > main_toolbar_initial_offset) {
				main_toolbar.classList.add("main-toolbar-sticky");
				main_col.style.paddingTop = main_toolbar_height + 'px';
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

