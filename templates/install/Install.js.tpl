<script	language=JavaScript>

{literal}

function toggleNextButton() {
	button = document.getElementById('next_button');
	if ( button.disabled == true ) {
		button.disabled = false;
	} else {
		button.disabled = true;
	}

	return true;
}

function toggleLicenseAccept() {
	license_accept = document.getElementById('license_accept');
	if ( license_accept.checked == true ) {
		license_accept.checked = false;
	} else {
		license_accept.checked = true;
	}

	return true;
}

function clearSelect(src_obj) {
	for (i=0; i < src_obj.options.length; i++) {
		src_obj.options[i] = null;
		i=i - 1;
	}
}

function populateSelectBox( select_box_obj, options, selected, include_blank) {
	clearSelect(select_box_obj);

	if ( include_blank == true ) {
		select_box_obj.options[0] = new Option('--', 0);

		var i=1;
	} else {
		var i=0;
	}

	if ( options != null ) {
		for ( x in options ) {
			select_box_obj.options[i] = new Option(options[x], x);
			if ( selected == x ) {
				select_box_obj.options[i].selected = true;
			}

			var i = i + 1;
		}
	}

	return true;
}

function showHelpEntry(objectID) {
	return true;
}

var submitButtonPressed = false;
{/literal}
</script>