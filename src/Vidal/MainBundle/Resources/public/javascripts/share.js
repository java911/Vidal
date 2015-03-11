$(document).ready(function() {
	$('.share-btn').fancybox({
		helpers: {
			title: null
		},
		beforeShow: function() {
			$('#share-email input[type="text"], #share-email textarea').val('');
			$('.share-message, .share-error').hide();
			$('#share-email form').show();
		}
	});

	$('#share-email input[type="text"], #share-email textarea').placeholder();

	$('#share-email form').ajaxForm(function(data) {
		if (data == 'FAIL') {
			$('.share-error').show();
		}
		else {
			$('#share-email form, .share-error').hide();
			$('.share-message').text('Ваше приглашение было успешно отправлено на e-mail: ' + data).show();
		}
	});
});