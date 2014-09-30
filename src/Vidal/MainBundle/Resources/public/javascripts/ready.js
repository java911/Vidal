$(document).ready(function() {
	var types = {'product': 'препарат', 'molecule': 'вещество', 'atc': 'АТХ код', 'company': 'компания'};
	var type = 'all';
	var $selectType = $('#search_form .search-type');

	$('#search_form .search-query')
		.autocomplete({
			minLength: 2,
			source:    function(request, response) {
				type = $selectType.val();
				var query = '{' +
					' "query":{"query_string":{"query":"' + request.term + '*"}}' +
					', "fields":["name","type"]' +
					', "size":40' +
					', "highlight":{"fields":{"name":{}}}';
				if (type != 'all') {
					query += ', "filter":{"term" :{"type" : "' + type + '"}}';
				}
				query += ' }';
				$.ajax({
					url:      "http://www.vidal.ru:9200/website/autocomplete/_search",
					type:     "POST",
					dataType: "JSON",
					data:     query,
					success:  function(data) {
						var hits = data.hits.hits;
						var values = $.map(hits, function(item) {
							return {
								label: item.highlight && item.highlight.name ? item.highlight.name : '',
								value: item.fields.name,
								type:  item.fields.type
							}
						});
						values.sort(function(a, b) {
							return (a.type == b.type) ? 0 : ((a.type < b.type) ? 1 : -1);
						});
						response(values.slice(0, 15));
					}
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
		$("html, body").animate({ scrollTop: 0 }, 600);
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
		if (text.length > 70 && text.indexOf(' ') === -1) {
			var parts = text.split('/');
			text = parts.join('<span style="display:inline-block;width:0"></span>/');
			$link.html(text);
		}
	});
});