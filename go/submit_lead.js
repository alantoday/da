$('a[href="#submit-form"]').click(function(){
	var y = '';
	$(".elInputIEmail").each( function() {
		if ($(this).val() != '') {
			y = $(this).val();
		}
	});
    $.post("index.php?post=1", {email: y}, function(data, status){
		$(location).attr('href','http://aspir.link/vsl1?email='+y);
    });
});
