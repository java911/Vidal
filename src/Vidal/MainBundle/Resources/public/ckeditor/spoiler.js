$(function() {
	$('div.spoiler-title').click(function() {
		$(this)
			.children()
			.first()
			.toggleClass('show-icon')
			.toggleClass('hide-icon');
		$(this)
			.parent().children().last().toggle();
	});
});

$(document).ready(function() {
	$('a').click(function() {
		if (window.location.hash) {
			$('.spoiler-toggle').removeClass('show-icon').addClass('hide-icon');
			$('.spoiler-content').show();
		}
	});
});