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

const Cookies = {
	set: function (name, value, ttl, path) {

		let expires = '';
		if(ttl!==undefined) {
			const d = new Date();

			d.setTime(d.getTime() + (ttl*24*60*60*1000));

			expires = ";expires="+ d.toUTCString();
		}

		let _path = '';
		if(path!==undefined) {
			_path = ';path='+path;
		}


		document.cookie = name + "=" + value + expires + _path;
	},

	get: function ( name ) {
		name += "=";

		let cookies = decodeURIComponent(document.cookie);

		cookies = cookies.split(';');

		for(let i = 0; i <cookies.length; i++) {
			let cookie = cookies[i];

			while (cookie.charAt(0) == ' ') {
				cookie = cookie.substring(1);
			}
			if (cookie.indexOf(name) == 0) {
				return cookie.substring(name.length, cookie.length);
			}
		}

		return '';
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

