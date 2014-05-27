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
					', "size":15' +
					', "highlight":{"fields":{"name":{}}}' +
					', "sort":[{"type":{"order":"asc"},"name":{"order":"asc"}}]';
				if (type != 'all') {
					query += ', "filter":{"term" :{"type" : "' + type + '"}}';
				}
				query += ' }';
				$.ajax({
					url:      "http://twiga.vidal.ru:9200/website/autocomplete/_search",
					type:     "POST",
					dataType: "JSON",
					data:     query,
					success:  function(data) {
						response($.map(data.hits.hits, function(item) {
							return {
								label: item.highlight.name,
								value: item.fields.name,
								type:  item.fields.type
							}
						}));
					}
				});
			},
			select:    function(event, ui) {
				if (ui.item) {
					$(this).val(ui.item.value);
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
});