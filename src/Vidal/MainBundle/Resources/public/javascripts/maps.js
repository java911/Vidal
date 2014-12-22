var map;
//localStorage.clear('coordsData');

ymaps.ready(function() {
	var regionId = $('.select').val();

	if (supports_html5_storage()) {
		var coordsData = localStorage.getItem("coordsData") || false;
		if (coordsData) {
			init(JSON.parse(coordsData));
		}
		else {
			$.getJSON(Routing.generate('pharmacies_objects', {'regionId': regionId}), function(data) {
				localStorage.setItem('coordsData', JSON.stringify(data));
				init(data);
			});
		}
	}
	else {
		$.getJSON(Routing.generate('pharmacies_objects', {'regionId': regionId}), init);
	}
});

function init(data) {
	map = new ymaps.Map('map', {
		center: [data.region.latitude, data.region.longitude],
		zoom:   data.region.zoom
	});

	var objectManager = new ymaps.ObjectManager({
		// Чтобы метки начали кластеризоваться, выставляем опцию.
		clusterize: true,
		// ObjectManager принимает те же опции, что и кластеризатор.
		gridSize:   64
	});

	// Чтобы задать опции одиночным объектам и кластерам, обратимся к дочерним коллекциям ObjectManager.
	objectManager.objects.options.set('preset', 'islands#greenDotIcon');
	objectManager.clusters.options.set('preset', 'islands#greenClusterIcons');

	// обработчик открытия метки
	objectManager.objects.events.add('click', function(e) {
		var objectId = e.get('objectId');
		var obj = objectManager.objects.getById(objectId);

		obj.properties.balloonContent = 'Идет загрузка данных...';
		objectManager.objects.balloon.open(objectId);

		$.getJSON(Routing.generate('getMapBalloonContent', {'id': objectId}), function(balloonHtml) {
			obj.properties.balloonContent = balloonHtml;
			objectManager.objects.balloon.open(objectId);
		});
	});

	map.geoObjects.add(objectManager);
	objectManager.add(data.coords);
}

function supports_html5_storage() {
	try {
		return 'localStorage' in window && window['localStorage'] !== null;
	} catch (e) {
		return false;
	}
}

$(document).ready(function() {
	$('.select')
		.chosen({
			disable_search:  true,
			no_results_text: "не найдено"
		})
		.change(function() {
			var regionId = $('.select').val();
			$.getJSON(Routing.generate('pharmacies_region', {'regionId': regionId}), function(region) {
				var coords = [parseFloat(region.latitude), parseFloat(region.longitude)];
				map.panTo(coords, {flying: false});
				map.setCenter(coords, region.zoom);
			});
		});
});