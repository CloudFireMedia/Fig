$(document).ready(function() {
	if (isMobile.any) {
		$('.layer').addClass('is-mobile');

		$('#main.screen .write-us.mobile').addClass('active');
		$('#main.screen .write-us.mobile').addClass('current');
	} else {
		$('#main.screen .write-us.desktop').addClass('active');
		$('#main.screen .write-us.desktop').addClass('current');
	}

	$('#menu-btn').on('click', function(e) {
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

	$('#filter-btn').on('click', function(e) {
		e.preventDefault();

		/**/
	});

	$('#star-btn').on('click', function(e) {
		e.preventDefault();

		var btnEl = $(this),
			id = $('#info.screen').data('eventId'),
			followBtnEl = $('#info.screen #follow'),
			eventEl = $('#main.screen .events > li > a[data-event-id="'+id+'"]'),
			starredCountEl = $('#menu .list > li > a[data-status="starred"] .count'),
			starredCount = parseInt(starredCountEl.html()),
			status = 'None';

		if (followBtnEl.hasClass('active')) {
			followBtnEl.trigger('click');
		}

		btnEl.toggleClass('active');

		if (btnEl.hasClass('active')) {
			status = 'starred';
			starredCountEl.html(starredCount + 1);
		} else {
			starredCountEl.html(starredCount - 1);
		}

		eventEl.data('status', status);

		$.ajax({
			dataType: 'json',
			url: '/event.php',
			data: {
				'action': 'set',
				'id': id,
				'fields': {
					'status': status
				}
			},
			success: function(results) {
				console.log(results);
			}
		});
	});

	$('#info.screen #follow').on('click', function(e) {
		e.preventDefault();

		if (!$('#info.screen').hasClass('past')) {
			var btnEl = $(this),
				id = $('#info.screen').data('eventId'),
				starBtnEl = $('#star-btn'),
				eventEl = $('#main.screen .events > li > a[data-event-id="'+id+'"]'),
				scheduledCountEl = $('#menu .list > li > a[data-status="scheduled"] .count'),
				scheduledCount = parseInt(scheduledCountEl.html()),
				followsCountEl = $('#info.screen .count'),
				followsCount = parseInt(followsCountEl.html()),
				status = 'None';

			if (starBtnEl.hasClass('active')) {
				starBtnEl.trigger('click');
			}

			btnEl.toggleClass('active');

			if (btnEl.hasClass('active')) {
				status = 'scheduled';
				followsCount++;
				scheduledCountEl.html(scheduledCount + 1);
				btnEl.html(btnEl.data('active'));
			} else {
				followsCount--;
				scheduledCountEl.html(scheduledCount - 1);
				btnEl.html(btnEl.data('inactive'));
			}

			eventEl.data('status', status);

			$.ajax({
				dataType: 'json',
				url: '/event.php',
				data: {
					'action': 'set',
					'id': $('#info.screen').data('eventId'),
					'fields': {
						'status': status,
						'count': followsCount
					}
				},
				success: function(results) {
					console.log(results);
				}
			});

			followsCountEl.html(followsCount);
		}
	});

	$('#blind').on('click', function(e) {
		toggleMenu();
	});

	$('#menu .list > li > a').on('click', function(e) {
		e.preventDefault();

		var btnEl = $(this),
			status = btnEl.data('status');

		$.ajax({
			dataType: 'json',
			url: '/event.php',
			data: {
				'action': 'list',
				'status': status
			},
			success: showEvents
		});

		$('#menu .list .categories a').removeClass('active');
		$('#menu .list > li > a').removeClass('active');

		btnEl.addClass('active');

		$('#main.screen .write-us.current').removeClass('active');
		if (status == 'upcoming') {
			$('#main.screen .write-us.current').addClass('active');
		}

		toggleMenu();

		$('#screens .screen').hide();
		$('#main.screen').show();
	});

	$('#menu .list .categories a').on('click', function(e) {
		e.preventDefault();

		var btnEl = $(this),
			category = btnEl.data('category');

		$.ajax({
			dataType: 'json',
			url: '/event.php',
			data: {
				'action': 'list',
				'category': category
			},
			success: showEvents
		});

		$('#menu .list .categories a').removeClass('active');
		$('#menu .list > li > a').removeClass('active');

		btnEl.addClass('active');

		$('#main.screen .write-us.current').removeClass('active');

		toggleMenu();

		$('#screens .screen').hide();
		$('#main.screen').show();
	});

	$('#menu .list .underconstruction').on('click', function(e) {
		e.preventDefault();

		toggleMenu();

		$('#screens .screen').hide();
		$('#underconstruction.screen').show();
	});

	$('#menu .footer .user').on('click', function(e) {
		e.preventDefault();

		toggleMenu();

		$('#screens .screen').hide();
		$('#user.screen').show();
	});

	$('#menu .footer .settings-btn').on('click', function(e) {
		e.preventDefault();

		toggleMenu();

		$('#screens .screen').hide();
		$('#underconstruction.screen').show();
	});

	$('#info.screen #guests').on('click', function(e) {
		e.preventDefault();

		/*var btnEl = $(this);

		btnEl.toggleClass('active');*/
	});

	$('#main.screen .events').on('click', '.event', function(e) {
		e.preventDefault();

		var id = $(this).data('eventId');

		showEvent(id);
	});

	$('#user.screen .interests a').on('click', function(e) {
		e.preventDefault();

		$(this).toggleClass('active');
	});

	function toggleMenu() {
		$('#menu').toggleClass('open');
		$('#blind').toggleClass('open');
		$('#screens').toggleClass('close');
	}

	function showEvent(id) {
		$.ajax({
			dataType: 'json',
			url: '/event.php',
			data: {
				'action': 'get',
				'id': id
			},
			success: function(event) {
				showMap(event.address);

				var starBtnEl = $('#star-btn'),
					followBtnEl = $('#info.screen #follow');

				if (event.status == 'starred') {
					starBtnEl.addClass('active');
				} else {
					starBtnEl.removeClass('active');
				}

				if (event.status == 'scheduled') {
					followBtnEl.addClass('active');
					followBtnEl.html(followBtnEl.data('active'));
				} else {
					followBtnEl.removeClass('active');
					followBtnEl.html(followBtnEl.data('inactive'));
				}

				if (event.is_past) {
					$('#info.screen').addClass('past');
				} else {
					$('#info.screen').removeClass('past');
				}

				$('#info.screen').data('eventId', event.id);
				$('#info.screen .brief .title').html(event.title);
				$('#info.screen .count').html(event.follows);
				$('#info.screen .date').html(event.date);
				$('#info.screen .time').html(event.time);
				$('#info.screen .category').html(event.category);

				$('#screens .screen').hide();
				$('#info.screen').show();
			}
		});
	}

	function showEvents(events) {
		$('#main.screen .events').empty();

		$.each(events, function() {
			var liEl = $('<li></li>').appendTo('#main.screen .events'),
				linkEl = $('<a class="event '+ this.status +'" href="#" data-event-id="'+ this.id +'"></a>').appendTo(liEl);

			$('<div class="time"><span class="icon clock"></span>'+ this.date +', '+ this.time +'</div>').appendTo(linkEl);
			$('<div class="title">'+ this.title +'</div>').appendTo(linkEl);

			var infoEl = $('<ul class="info"></ul>').appendTo(linkEl);
			$('<li><span class="icon tags"></span>'+ this.category +'</li>').appendTo(infoEl);
			$('<li><span class="icon users"></span>'+ this.follows +'</li>').appendTo(infoEl);
		});

		if (events.length > 0) {
			$('#main.screen .empty').removeClass('active');
		} else {
			$('#main.screen .empty').addClass('active');
		}
	}

	function showMap(address) {
		var geocoder = new google.maps.Geocoder(),
			map = new google.maps.Map($('#map').get(0), {
				zoom: 17,
				disableDefaultUI: true//,
				//styles: GMAP_STYLE
			}),
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
});