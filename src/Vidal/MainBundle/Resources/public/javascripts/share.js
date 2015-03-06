$(document).ready(function() {
	$('.share-btn').fancybox();

	$('#share-email input[type="text"], #share-email textarea').placeholder();

	$('#share-email form').ajaxForm(function(data) {
		if (data == 'FAIL') {
			alert('Пожалуйста, заполните все поля и убедитесь в правильности указанных e-mail адресов');
		}
		else {
			alert('Ваше приглашение было успешно отправлено на e-mail: ' + data);
		}
	});
});