$(document).ready(function() {
	$('#search_query').autocomplete({
		minLength: 2,
		source:    function(request, response) {
			$.ajax({
				url:      "http://localhost:9200/website/autocomplete/_search",
				type:     "POST",
				dataType: "JSON",
				data:     '{"query":{"query_string":{"query":"' + request.term + '*"}}, "fields": ["name"], "size":15}',
				success:  function(data) {
					response($.map(data.hits.hits, function(item) {
						return {
							label: item.fields.name,
							value: item.fields._id
						}
					}));
				}
			});
		}
	})
});