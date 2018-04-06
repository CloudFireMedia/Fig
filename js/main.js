$(document).ready(function() {
	window.onpopstate = function() {
		pageChange();
	};
	pageChange();

	$('#menu-btn').click(function(e) {
		e.preventDefault();

		toggleMenu();

		if (!$('#screens').hasClass('close')) {
			$('#screens .screen').hide();
			$('#screens .screen').each(function() {
				if ($(this).css('display') == 'none') {
					$('#main.screen').show();
				}
			});
		}
	});

	$('#blind').click(function(e) {
		toggleMenu();
	});

	$('#menu .list .underconstruction').click(function(e) {
		e.preventDefault();

		toggleMenu();

		$('#screens .screen').hide();
		$('#underconstruction.screen').show();
	});

	$('#menu .list .categories a').click(function(e) {
		e.preventDefault();

		var category = $(this).data('category');

		$('#main.screen .buttons > li > a').each(function() {
			var btnEl = $(this);

			btnEl.parent('li').hide();

			if (btnEl.data('category') == category) {
				btnEl.parent('li').show();
			}
		});

		toggleMenu();

		$('#screens .screen').hide();
		$('#main.screen').show();
	});

	$('#menu .list > li > a').click(function(e) {
		e.preventDefault();

		var status = $(this).data('status');

		if (status == 'all') {
			$('#main.screen .buttons > li > a').parent('li').show();
		} else {
			$('#main.screen .buttons > li > a').each(function() {
				var btnEl = $(this);

				btnEl.parent('li').hide();

				if (btnEl.data('status') == status) {
					btnEl.parent('li').show();
				}
			});
		}

		toggleMenu();

		$('#screens .screen').hide();
		$('#main.screen').show();
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

		showMap(btn.place, btn.location);

		$('#info.screen .count').html(btn.count);
		$('#info.screen .time').html(btn.time);

		$('#screens .screen').hide();
		$('#info.screen').show();
	}

	function showMap(place, location) {
		var geocoder = new google.maps.Geocoder(),
			map = new google.maps.Map($('#map').get(0), {
				zoom: 17,
				disableDefaultUI: true//,
				//styles: GMAP_STYLE
			}),
			address = place+' '+location,
			url = 'https://www.google.com/maps/?q='+address;

		geocoder.geocode({'address': address}, function(results, status) {
			if (status === 'OK') {
				var pos = results[0].geometry.location,
					marker = new google.maps.Marker({
						position: pos,
						map: map
					});
				
				map.setCenter(pos);
			}
		});

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