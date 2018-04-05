$(document).ready(function() {
	window.onpopstate = function() {
		pageChange();
	};
	pageChange();

	$('#menu-btn').click(function(e) {
		e.preventDefault();

		toggleMenu();

		$('#screens .screen').hide();
		$('#main.screen').show();
	});

	$('#menu .list .underconstruction').click(function(e) {
		e.preventDefault();

		toggleMenu();

		$('#screens .screen').hide();
		$('#underconstruction.screen').show();
	});

	$('#menu .list #events').click(function(e) {
		e.preventDefault();

		toggleMenu();
	});

	$('#menu .footer .settings-btn').click(function(e) {
		e.preventDefault();

		toggleMenu();

		$('#screens .screen').hide();
		$('#underconstruction.screen').show();
	});

	$('#info.screen .buttons #going').click(function(e) {
		e.preventDefault();

		var btnEl = $(this),
			countEl = $('#info.screen .count'),
			count = parseInt(countEl.html());

		btnEl.toggleClass('active');

		if (btnEl.hasClass('active')) {
			count++;
			btnEl.html(btnEl.data('active'));
		} else {
			count--;
			btnEl.html(btnEl.data('inactive'));
		}

		countEl.html(count);
	});

	$('#info.screen .buttons #guests').click(function(e) {
		e.preventDefault();

		$('#screens .screen').hide();
		$('#underconstruction.screen').show();
	});

	$('#main.screen .buttons > li > a').click(function(e) {
		var href = $(this).attr('href');

		if (href.startsWith('#')) {
			showEvent(href.substr(1));
		}
	});

	function toggleMenu() {
		$('#menu').toggleClass('open');
		$('#blind').toggleClass('open');
		$('#screens').toggleClass('close');
	}

	function showEvent(index) {
		var btn = BUTTONS[index];

		showMap(btn.place, btn.lat, btn.lng);

		$('#info.screen .count').html(btn.count);
		$('#info.screen .time').html(btn.time);

		$('#screens .screen').hide();
		$('#info.screen').show();
	}

	function showMap(place, lat, lng) {
		var pos = {
				lat: lat,
				lng: lng
			},
			map = new google.maps.Map($('#map').get(0), {
				zoom: 17,
				center: pos,
				disableDefaultUI: true/*,
				styles: GMAP_STYLE*/
			}),
			marker = new google.maps.Marker({
				position: pos,
				title: place,
				//icon: '/img/balloon.png',
				map: map
			}),
			url = 'https://www.google.com/maps/place/'+place+'/@'+lat+','+lng+',17z';

		$('#info.screen #map-link').attr('href', url);
	}

	function pageChange() {
		if (location.hash != '') {
			showEvent(location.hash.substr(1));
		} else {
			$('#screens .screen').hide();
			$('#main.screen').show();
		}
	}
});