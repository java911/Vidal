$(document).ready(function() {
	$('#search_form .search-query')
		.autocomplete({
			minLength: 2,
			source:    function(request, response) {
				$.ajax({
					url:      "http://vidal.evrika.ru:9200/website/autocomplete/_search",
					type:     "POST",
					dataType: "JSON",
					data:     '{ "query":{"query_string":{"query":"' + request.term + '*"}}, "fields":["name"], "size":15, "highlight":{"fields":{"name":{}}} }',
					success:  function(data) {
						response($.map(data.hits.hits, function(item) {
							return {
								label: item.highlight.name,
								value: item.fields.name
							}
						}));
					}
				});
			},
			select: function(event, ui) {
				if(ui.item){
					$(this).val(ui.item.value);
				}
			}
		}).data("ui-autocomplete")._renderItem = function (ul, item) {
		return $("<li></li>")
			.data("item.autocomplete", item)
			.append("<a>" + item.label + "</a>")
			.appendTo(ul);
	};

	$('#search_form .search-type').chosen({disable_search:true});
});