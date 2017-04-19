var FxTracker = FxTracker || {};

(function (ns, $, undefined) {

	var trackerEnabled;
	var trackerRoute;
	var _mappings;

	ns.track = function(mappings) {
		map(mappings);
		_mappings = mappings;
	};

	ns.initialize = function(enable, route) {
		trackerEnabled = (enable === 'true' || enable === 'True' || enable === true);
		trackerRoute = route;
	};

	ns.clickEvent = function (eventId, selector, parentSelector) {
		return {
			eventId: eventId,
			selector: selector,
			event: "click",
			handle: log,
			parentSelector: parentSelector
		};
	};

	ns.pageEvent = function (eventId, selector, isPostBack) {
		return {
			eventId: eventId,
			selector: selector,
			event: "load",
			handle: log,
			postback: isPostBack || false
		};
	};

	ns.customEvent = function (eventId, selector, event) {
		return {
			eventId: eventId,
			selector: selector,
			event: event,
			handle: log
		};
	};

	ns.keypressEvent = function (eventId, selector, keyId, parentSelector) {
		return {
			eventId: eventId,
			selector: selector,
			event: "keypress",
			handle: log,
			keyId: keyId,
			parentSelector: parentSelector
		};
	};

	ns.saveEvent = function (eventId, callback) {
		if (!trackerEnabled) {
			if (callback) callback();
			return;
		}

		var order = null;
		if (typeof orderController !== "undefined") {
			order = orderController.order;
		}

		order = !order ? $("#_challengeTokenHiddenField").data("order") : order;
		order = !order ? null : order;

		var eventParam = null;
		if (order && order.beneficiary && order.correspondent) {
			eventParam = {
				BankCountryId: order.beneficiary.bank.bankCountryId,
				CountryTo: order.correspondent.location.countryAbbrev,
				CityTo: order.correspondent.location.city,
				StateTo: order.correspondent.location.state,
				CorrespondentId: order.correspondent.correspondentIdEncrypted,
				CorrespondentLocationId: order.correspondent.location.locationIdEncrypted
			};
		}

		var data = JSON.stringify({ eventId: eventId, eventParam: eventParam });

		return $.fxAjax({
			url: trackerRoute,
			contentType: "application/json; charset=utf-8",
			dataType: "json",
			data: data,
			type: "POST",
			cache: false,
			timeout: 2000,
			success: function(response) {
				trackerEnabled = response;
			},
			error: function(response) {
				// possible time out error
			},
			complete: callback
		});
	};


	ns.attachTracking = function (eventId, func) {
		return function () {
			func.apply(this, arguments);
			setTimeout('FxTracker.Controls.saveEvent(' + eventId + ');', 50);
		};
	};

	var map = function (mappings) {
		for (var index in mappings) {
			bind(mappings[index]);
		}
	};

	var bind = function (mapping) {
		if (mapping.event == 'load' && mapping.postback) {
			return false;
		}

		var $this = $(mapping.selector);

		if (mapping.event == 'keypress') {
			$this.attr('keyId', mapping.keyId);
		}

		$this.attr('eventId', mapping.eventId);

		if (mapping.parentSelector) {
			$(mapping.parentSelector).on(mapping.event, mapping.selector, mapping.handle);
		} else {
			$this.on(mapping.event, mapping.handle);
		}
	};

	var log = function (e) {
		var $this = $(this);
		var eventId = $this.attr('eventId');
		var doAjaxCall = true;

		if ($this.attr('keyId')) {
			if (e.which != $this.attr('keyId')) {
				doAjaxCall = false;
			}
		}

		if (trackerEnabled && doAjaxCall) {
			FxTracker.Controls.saveEvent(eventId, null);
		}
	};

	var logAndRedirect = function (eventId, link) {
		var redirectTo = link.attr("href");
		link.attr("href", "#");
		link.on("click", function () {
			ns.saveEvent(eventId, function () { document.location = redirectTo; });
		});
	};

	ns.logAndRedirect = logAndRedirect;

})(FxTracker.Controls = FxTracker.Controls || {}, jQuery);
