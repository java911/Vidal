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
		if (data == 'DoubleClick') {

		}
		else if (data == 'FAIL') {
			$('.share-error').show();
		}
		else {
			$('#share-email form, .share-error').hide();
			$('.share-message').text('Ваше приглашение было успешно отправлено на e-mail: ' + data).show();
			shareClick();
		}
	});

	$.getJSON(Routing.generate('share_counter', {'class': '{{ class }}', 'target': '{{ id }}'}), function(data) {
		$('.counter span').text(data);
		if (parseInt(data) > 0) {
			$('.counter').css('display', 'inline-block');
		}
	});

	$('#share-buttons a').not('#share-email').click(function() {
		shareClick();
	});

	function shareClick() {
		$.getJSON(Routing.generate('share_click', {'class': '{{ class }}', 'target': '{{ id }}'}), function(data) {
			$('.counter span').text(data);
			if (parseInt(data) > 0) {
				$('.counter').css('display', 'inline-block');
			}
		});
	}
});