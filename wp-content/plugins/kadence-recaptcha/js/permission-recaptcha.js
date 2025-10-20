function kt_createCookie( name, value, days ) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}
function kt_makeid() {
  var text = "";
  var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

  for (var i = 0; i < 5; i++)
    text += possible.charAt(Math.floor(Math.random() * possible.length));

  return text;
}
(function ($) { 
	var grc = jQuery('form').find( '#kt-permission-consent' );
	if ( grc.length > 0 ) {
		grc.closest('form').css('position', 'relative')
	}
	jQuery('#kt-permission-consent').click(function (e) {
		e.preventDefault();
		kt_createCookie( ktpercap.permission_cookie, true, 60 );
		var theurl = window.location.href;
		if ( theurl.match(/\?./) ) {
			window.location.href = window.location.href+"&"+$.param({'v':kt_makeid()})
		} else {
			window.location.href = window.location.href+"?"+$.param({'v':kt_makeid()})
		}
	});

})(jQuery);


