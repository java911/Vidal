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
					window.location = Routing.generate('search', {'q': ui.item.value[0]});
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

	$('.text a').click(function() {
		var id = this.getAttribute('href').substring(2);
		$('#' + id).closest('.spoiler-content').show();
	});

	$('.block table, .text table').not('.products-table').each(function() {
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