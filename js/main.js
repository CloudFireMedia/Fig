$(document).ready(function() {
	window.onpopstate = function() {
		pageChange();
	};
	pageChange();

	$('#menu.screen .menu .btn').click(function(e) {
		showEvent($(this).attr('href').substr(1));
	});

	function showEvent(index) {
		var event = EVENTS[index];

		showMap(event.title, event.lat, event.lng);

		$('#info.screen .count').html(event.count);
		$('#info.screen .time').html(event.time);

		$('#menu.screen').hide();
		$('#info.screen').show();
	}

	function showMap(title, lat, lng) {
		var pos = {
				lat: lat,
				lng: lng
			},
			map = new google.maps.Map($('#map').get(0), {
				zoom: 17,
				center: pos,
				disableDefaultUI: true/*,
				styles: GMAP_STYLE*/
			});

		var marker = new google.maps.Marker({
			position: pos,
			title: title,
			//icon: '/img/balloon.png',
			map: map
		});
	}

	function pageChange() {
		if (location.hash != '') {
			showEvent(location.hash.substr(1));
		} else {
			$('#menu.screen').show();
			$('#info.screen').hide();
		}
	}
});