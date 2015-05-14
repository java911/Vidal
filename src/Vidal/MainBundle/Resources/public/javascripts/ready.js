$(document).ready(function() {
	var types = {'product': 'препарат', 'molecule': 'вещество', 'atc': 'АТХ код', 'company': 'компания'};
	var type = 'all';
	var $selectType = $('#search_form .search-type');

	$('#search_form .search-query')
		.autocomplete({
			minLength: 2,
			source:    function(request, response) {
				var url = Routing.generate('elastic_autocomplete', {
					'type': $selectType.val(),
					'term': request.term.trim()
				});
				$.getJSON(url, function(data) {
					response($.map(data.hits.hits, function(item) {
						return {
							label: item.highlight.name,
							value: item._source.name,
							type:  item._source.type
						}
					}));
				});
			},
			select:    function(event, ui) {
				if (ui.item) {
					$('#bad').prop('checked')
						? window.location = Routing.generate('search', {'q': ui.item.value, 'bad': 'on'})
						: window.location = Routing.generate('search', {'q': ui.item.value});
				}
			}
		}).data("ui-autocomplete")._renderItem = function(ul, item) {
		return $('<li class="aut"></li>')
			.data("item.autocomplete", item)
			.append("<a>" + "<i>" + types[item.type] + "</i>" + item.label + "</a>")
			.appendTo(ul);
	};

	$('.admin-edit').click(function(e) {
		e.stopPropagation();
	});

	$('#search_form .search-type').chosen({disable_search: true});

	$(window).scroll(function() {
		if ($(this).scrollTop() > 100) {
			$('#top-link').fadeIn();
		} else {
			$('#top-link').fadeOut();
		}
	});

	$('#top-link').click(function() {
		$("html, body").animate({scrollTop: 0}, 600);
		return false;
	});

	$('a[href^="http"], a[href^="ftp"]').not('a[href^="http://vidal"]').click(function() {
		window.open(this.href, "");
		return false;
	});

	$('.tags > span').click(function(e) {
		e.stopPropagation();
		var $ul = $(this).find('> ul');
		$('.tags ul').not($ul).hide();
		$ul.toggle();
	});

	$('.tags ul').mouseover(function(e) {
		e.stopPropagation();
	}).click(function(e) {
		e.stopPropagation();
	});

	$('body').click(function() {
		$('.tags ul').hide();
	});

	$('.anons-footer').click(function(e) {
		var $announcement = $(this).toggleClass('expanded').closest('.announcement');
		$announcement.find('ul').slideToggle('fast');
		$announcement.find('.products').slideToggle('fast');
	});

	$('.text a').each(function() {
		var $link = $(this);
		var text = $link.text();
		var href = this.href;

		if (text.length > 70 && text.indexOf(' ') === -1) {
			var parts = text.split('/');
			text = parts.join('<span style="display:inline-block;width:0"></span>/');
			$link.html(text);
		}

		var index = href.indexOf('#_');
		if (index !== -1) {
			href = href.substring(index);
			this.href = href;
		}
	});

	$('.pharm-article .text a').each(function() {
		var href = $(this).attr('href');

		if (href.substr(0, 2) == '#_') {
			var data = $(this).closest('.pharm-article').attr('data');
			$(this).attr('data', data);
		}
	});

	$('.text a').click(function() {
		var id = $(this).attr('href').substring(2);
		var data = $(this).closest('.pharm-article').attr('data');
		if (data) {
			$(this).closest('.pharm-article').find('.spoiler-content').show()
				.closest('.spoiler').find('.spoiler-toggle').removeClass('show-icon').addClass('hide-icon');
		}
		else {
			$('#' + id).closest('.spoiler-content').show()
				.closest('.spoiler').find('.spoiler-toggle').removeClass('show-icon').addClass('hide-icon');
		}
	});

	$('.spoiler').click(function() {
		var $content = $(this).find('.spoiler-content');
		if ($content.css('display') == 'none') {
			$(this).find('.spoiler-toggle').removeClass('show-icon').addClass('hide-icon');
			$content.slideDown();
		}
		else {
			$(this).find('.spoiler-toggle').removeClass('hide-icon').addClass('show-icon');
			$content.slideUp();
		}
	});

	$('.block table, .text table, .text img, .block img').not('.products-table').each(function() {
		var $this = $(this);

		if ($this.width() > 520) {
			$this.wrap('<div class="table-wrap"><div>');
		}
	});

	$('.products-table-name .m').click(function(e) {
		e.stopPropagation();
		var $exclude = $(this).children('div');
		$('.products-table-name > div').not($exclude).hide();
		var $indication = $(this).siblings('div');
		$indication.css('display') == 'block'
			? $indication.hide()
			: $indication.show();
	});

	$('body').click(function() {
		$('.products-table-name > div').hide();
	});
});