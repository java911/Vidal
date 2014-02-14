$(document).ready(function() {
	$('.products-table-indication').click(function(e) {
		e.stopPropagation();
		var $exclude = $(this).children('div');
		$('.products-table-indication > div').not($exclude).hide();
		var $indication = $(this).children('div');
		$indication.css('display') == 'block'
			? $indication.hide()
			: $indication.show();
	});

	$('body').click(function() {
		$('.products-table-indication > div').hide();
	});
});