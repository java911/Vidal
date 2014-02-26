$(document).ready(function() {
	// клик смены флажка
	$('.btn-swap').click(function(e) {
		e.preventDefault();
		var $link = $(this);

		$.getJSON(this.href, null, function(data) {
			data
				? $link.removeClass('btn-negative').addClass('btn-positive')
				: $link.removeClass('btn-positive').addClass('btn-negative');
		});
	});

	// клик разворачивания фильтров
	$('legend.filter_legend').click(function(e) {
		e.stopPropagation();
		$('.filter_container').slideToggle();
	});
});