jQuery(document).ready(function(){
	jQuery('.achievement-pic').hover(function () {
		var string = jQuery(this).attr('id').split('-');
		//var string = "test";
		for (var i = 0; i <= 4; i++) {
			if (i == string[2]) {
				jQuery('#achievement-title-id-'+i).css({'display':'block'});
			} else {
				jQuery('#achievement-title-id-'+i).css({'display':'none'});
			}
		}
		console.log(string[2]);
	},
	function () {
		for (var i = 0; i <= 4; i++) {
			jQuery('#achievement-title-id-'+i).css({'display':'none'});
		}
		jQuery('#achievement-title-id-1').css({'display':'block'});

	});
});