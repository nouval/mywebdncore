angular.module("FxOnline.Directives", []);
angular.module("FxOnline.Services", []);
angular.module('CustomerEnrollmentApp', ['FxOnline.Directives', 'FxOnline.Services', 'ce.Factories', 'ce.Services', "ngTouch", "angucomplete-alt"])
     .controller('CustomerEnrollmentCtrl', [
        '$scope', '$http', '$templateCache', '$compile', 'ceFactory', 'beneficiaryService', 'ceService', 'identificationService', '$timeout', '$filter', 'ThinBox',
        function ($scope, $http, $templateCache, $compile, $ceFactory, $beneficiaryService, $ceService, $identificationService, $timeout, $filter, AngularThinBox) {
        	/*variables*/
        	$scope.lockIDSelection = false;
        	$scope.viewId = 0;
        	$scope.fields = null;
        	$scope.visitedIdTab = false;
        	$scope.countries = null;
        	$scope.dot = null;
        	$scope.customerId = 0;
        	$scope.validationRequestParams = "";
        	$scope.selectedDuplicateCustomer = null;
        	$scope.logger = new AddressLogger("/Classic/CustomerEnrollment/api/addressvalidation/saveValidationResults");
        	$scope.addressService = {
        		requestTime: "",
        		responseTime: "",
        		confidence: "",
        		providerId: 0
        	};

        	$scope.complianceValidForm = true;


        	/*Events*/
        	$scope.init = function (model) {
        		$scope.Model = JSON.parse(model);
        		$scope.userCountryCode = $scope.Model.CountryCode;
        		$scope.registeredTo = $("#registeredTo").val();
        		$scope.domainName = $("#domainName").val();
        		$scope.registrationCode = $("#registrationCode").val();
        		$scope.Scheme = $scope.Model.Scheme;
        		$scope.useAcuant = $scope.Model.UseAcuant;
        		$scope.dateWaterMark = $scope.Model.DateFormat;
        		$scope.Authroity = $scope.Model.Authroity;
        		$ceFactory.initJqueryDateEvents($scope.Model);
        		$scope.scanStarted = false;
        		$scope.allowIDdelete = $scope.Model.AllowIDdelete;
        		$scope.hideIDimage = $scope.Model.HideIDimage;
        		$scope.defaultImageExists = true;
        		$scope.savingId = false;
        		$scope.savingCustomer = false;
        		$scope.IdInvalid = false;
        		$scope.complianceValidForm = true;
        		$scope.initialBeneficiary = null;
        		$scope.savingId = false;
        		$scope.serviceId = $scope.Model.NceReq.ServiceId;
        	};

        	$scope.showOverlay = function () {
        		$ceFactory.showOverlay();
        	};

        	$scope.hideOverlay = function () {
        		$ceFactory.hideOverlay();
        	};

        	$scope.setPreferredLangauge = function () {
        		if ($scope.c.CustomerLocale == undefined)
        			return;
        		angular.forEach($scope.locales, function (obj, i) {

        			if (obj.code.toLowerCase() == $scope.c.CustomerLocale.toLowerCase()) {
        				$scope.c.CustomerPrefLang = obj.label;
        			}
        		});
        	};

        	$scope.clearAutoDiv = function () {
        		$scope.locations = [];
        		$scope.autoCompleteLocations = [];
        		$scope.f1 = [];
        		$scope.f2 = [];
        		$scope.f3 = [];
        		$scope.f4 = [];
        		$scope.line2 = 0;
        		$scope.line1 = 0;
        		$scope.line3 = 0;
        		$scope.line4 = 0;
        	};

        	$scope.closeWin = function () {

        		if (angular.isDefined($scope.c) && $scope.c !== null && !angular.equals($scope.initialCustomer, $scope.c)) {
        			$scope.unsavedViewId = 1;
        			return;
        		} else if (angular.isDefined($scope.b) && $scope.b !== null && !angular.equals($scope.initialBeneficiary, $scope.b)) {
        			$scope.unsavedViewId = 1;
        			return;
        		} else if (angular.isDefined($scope.questions) && $scope.questions !== null && !angular.equals($scope.initialQuestions, $scope.questions)) {
        			$scope.unsavedViewId = 1;
        			return;
        		}

        		//if ($scope.c !== $scope.oldCustomerObj) {
        		//	$scope.unsavedViewId = 1;
        		//	return;
        		//}


        		//if (window.unsaved) {
        		//	$scope.unsavedViewId = 1;
        		//	return;
        		// }

        		$scope.closeModal();
        	};

        	$scope.closeModal = function () {
        		$scope.close();
        		setTimeout(function () {
        			if ($scope.nceobj.IsAngularThinBox) {
        				AngularThinBox.cancel();
        			} else {
        				parent.CES.CustomerController.modalBoxClose();
        			}
        		}, 0);
        	};

        	$scope.close = function () {
        		$scope.viewId = 0;
        		$scope.states = null;
        		$scope.tel1 = null;
        		$scope.tel2 = null;
        		//reset identification tab settings
        		$("#imgList img").remove();
        		$("#divZoom img").remove();
        		$("#original img").remove();
        		$("#cropImgWrapper img").remove();
        		$scope.identificationImages = null;
        		$scope.customerIds = null;
        		$("#divCarousel").children().remove();
        		$('#dvIdentificationList').children().remove();
        		$scope.IdTabRequired = false;
        		$scope.imageFrontSideKey = null;
        		$scope.imageBackSideKey = null;
        		$scope.i = null;
        		//reset beneficiary tab settings
        		$scope.b = null;
        		$scope.otherBeneficiary = false;
        		$scope.tel3 = null;
        		$scope.tel4 = null;
        		$scope.clearAutoDiv();
        		$scope.hideOverlay();
        		$scope.idTypes2 = null;
        		$scope.iDcountry = '';
        		$scope.c = null;
        		$('#customerBlockable .blockOverlay').hide();
        		$scope.complianceValidForm = true;
        	};

        	$scope.autoCompleteCityAndState = function () {
        		$http({
        			method: 'POST',
        			data: {
        				countryCode: $scope.c.Country,
        				postalCode: $scope.c.PostalCode
        			},
        			url: '/Classic/CustomerEnrollment2/GetCityAndStateForAutoComplete'
        		}).success(function (data) {
        			if (data != null && data.length > 0) {
        				$scope.c.City = data[0];
        				$scope.c.State = data[1];
        			}
        		});
        	};

        	$scope.open = function (id, tabId) {
        		$scope.savingId = false;
        		window.unsaved = false;
        		$scope.selectedDuplicateCustomer = null;
        		$scope.IdTabRequired = false;
        		$scope.addressValidationView = false;
        		$scope.addressConfirmed = false;
        		$scope.viewId = 1;
        		$scope.tabId = tabId;
        		$scope.lockIDSelection = false;
        		$scope.HasBeneficiary = $ceFactory.enableBeneficiary($scope.queryString) > 0 ? false : true;
        		$scope.identficationViewId = 0;
        		$scope.allowBene = $scope.nceobj.ShowBeneficiariesTab;
        		$scope.dataQualityView = false;
        		$scope.addressSelectedFromAutocomplete = false;
        		$scope.customertype = $ceFactory.getCustomerType($scope.queryString);
        		$scope.initialBenLoad = true;

        		if (id == -1) {
        			$scope.scanId = true;
        			$scope.wait = false;
        		} else {
        			$scope.scanId = false;
        			$scope.startScan = false;
        		}

        		$scope.c = { Id: 0 };
        		var sync = false;
        		if (id > 0) {
        			sync = true;
        			$scope.getCustomer(id, true);
        		}
        		else {
        			$scope.getRequiredFields(sync, id);

        		}
        	};

        	$scope.getRequiredFields = function (sync, id) {
        		$http({
        			method: 'POST',
        			data: {
        				param: $scope.bsaParam,
        				serviceId: $scope.serviceId,
        				validationRequestParams: $scope.queryString,
        				sar: $ceFactory.getSar($scope.nceobj),
        				custCountryResidence: $scope.c ? $scope.c.Residence : ""
        			},
        			url: '/Classic/CustomerEnrollment2/FieldRuleWithList'
        		}).success(function (data) {
        			$scope.fields = data.result.CustomerFields;
        			$scope.businessSectors = data.result.BussinessSectors;
        			$scope.occupations = data.result.Occupations;
        			$scope.sourceOfFunds = data.result.SourceOfFunds;
        			$scope.HasCompliance = data.result.HasCompliance;
        			if (!sync)
        				$scope.getCustomer(id);


        			if (id > 0) {
        				var validationData = $("#unifyingDiv").data("orderValidationData");
        				if (validationData != null && validationData != undefined && validationData.identificationInValid && !validationData.customerInValid) {
        					$scope.getCustomerIds();
        				}
        			}

        			if ($scope.tabId === 1) {
        				$scope.c.Id = id;
        				$scope.getCustomerIds();
        			}

        			if ($scope.tabId === 2) {
        				$scope.c.Id = id;
        				$scope.getQuestions();
        			}
        			else if ($scope.tabId === 3) {
        				$scope.c.Id = id;
        				$scope.getBeneficiaries();
        			}


        			// to maintain data change status
        			$scope.initialCustomer = angular.copy($scope.c);
        		});
        	};


        	$scope.getCountry = function (code) {
        		var found = false;
        		angular.forEach($scope.countries, function (c) {
        			if (c.code == code) {
        				$scope.countryOfResidence = c.name;
        				$scope.toggleResidence = true;
        				found = true;
        			}
        		});
        		if (!found) {
        			$scope.toggleResidence = false;
        			$scope.c.Residence = '';
        		}
        	};

        	$scope.loadScanTemplate = function () {
        		$scope.scanStarted = false;
        		$scope.scanId = true;
        		$scope.wait = false;
        		$scope.zoomClicked = false;
        		$scope.lockIDSelection = false;
        		$scope.i = $ceFactory.getIdentificationModel();
        		$scope.iDcountry = '';
        		$scope.getCustomerEnrollmentList();
        		$scope.scanCompleted = false;
        		$scope.scanInProgress = false;
        		$("#dvNewCustomer").empty();
        		var html = $templateCache.get("scanUS");
        		$("#dvNewCustomer").append($compile(html)($scope));
        		$("#idIssuedCountry").focus();
        	};

        	$scope.getCustomer = function (id, getReqFields) {
        		$ceFactory.clearErrors();
        		$scope.scanStarted = false;
        		$scope.toggleLocale = true;
        		$scope.toggleResidence = true;
        		$scope.toggleAddPhone = true;
        		$scope.scanId = false;

        		if (id === -1) {
        			$scope.loadScanTemplate();
        		} else if (id === 0) {
        			$scope.createNewCustomer();
        		} else if (id === 2) {
        			var behalfId = $ceFactory.getSendOnBehalfOfCustomerId($scope.queryString);
        			if (behalfId == null || behalfId == undefined)
        				behalfId = 0;
        			$scope.customertype = $ceFactory.getCustomerType($scope.queryString);
        			$http({
        				method: 'POST',
        				data: { id: behalfId },
        				url: '/Classic/CustomerEnrollment2/Customer'
        			}).success(function (data) {
        				$scope.c = data;
        				$scope.validationStatus = data.AddressValidationStatusId;
        				$scope.setOriginalAddress(data);
        				$scope.c.DisabledTaxId = $scope.c.EditTaxId;
        				$scope.toggleAddPhone = !$scope.c.ShowLine2;
        				$scope.disableTaxId = $scope.c.TaxId && $scope.c.TaxId.length > 0;
        				$scope.getStates($scope.c.CountryOfBirth, 'states');
        				$scope.getCustomerEnrollmentList();

        				// to maintain data change status
        				$scope.initialCustomer = angular.copy($scope.c);

        				$('#CustomerEnrollmentApp #FirstName').focus();

        				//$scope.telePhoneModel = {
        				//	countryCode: $scope.Model.Country,
        				//	number: $scope.c.TelLine1AreaCode + $scope.c.TelLine1
        				//};
        				//$scope.telePhoneModel2 = {
        				//	countryCode: $scope.Model.Country,
        				//	number: $scope.c.TelLine2AreaCode + $scope.c.TelLine2
        				//};
        				if (getReqFields) {
        					$scope.getRequiredFields(true, behalfId);
        				}
        			});
        		} else {
        			id = $scope.c.Id;
        			if ($scope.c.Id <= 0) {
        				id = $scope.nceobj.CustomerIdEncrypted;
        			}

        			$http({
        				method: 'POST',
        				data: { id: id },
        				url: '/Classic/CustomerEnrollment2/Customer',
        				async: false,
        			}).success(function (data) {
        				$scope.ComplianceTabSaveModalAndContinue = data.ComplianceTabSaveModalAndContinue;
        				$scope.c = data;
        				$scope.validationStatus = data.AddressValidationStatusId;
        				$scope.setOriginalAddress(data);
        				$scope.c.DisabledTaxId = $scope.c.EditTaxId;
        				$scope.toggleAddPhone = !$scope.c.ShowLine2;
        				$scope.disableTaxId = $scope.c.TaxId && $scope.c.TaxId.length > 0;
        				$scope.getStates($scope.c.CountryOfBirth, 'states');
        				$scope.getCustomerEnrollmentList();
        				$('#CustomerEnrollmentApp #FirstName').focus();
        				//$scope.telePhoneModel = {
        				//	countryCode: $scope.Model.Country,
        				//	number: $scope.c.TelLine1AreaCode + $scope.c.TelLine1
        				//};
        				//$scope.telePhoneModel2 = {
        				//	countryCode: $scope.Model.Country,
        				//	number: $scope.c.TelLine2AreaCode + $scope.c.TelLine2
        				//};
        				if (getReqFields) {
        					$scope.getRequiredFields(true, id);
        				}

        				// to maintain data change status
        				$scope.initialCustomer = angular.copy($scope.c);
        			});
        		}
        	};

        	$scope.setOriginalAddress = function (customer) {
        		$scope.originalAddress = {};
        		$scope.originalAddress.Address = customer.Address;
        		$scope.originalAddress.Unit = customer.Unit;
        		$scope.originalAddress.PostalCode = customer.PostalCode;
        		$scope.originalAddress.City = customer.City;
        		$scope.originalAddress.State = customer.State;
        		$scope.originalAddress.Country = customer.Country;
        	};

        	$scope.addressChanged = function () {
        		if (!$scope.originalAddress) return true;

        		if ($scope.originalAddress.Address !== $scope.c.Address) return true;
        		if ($scope.originalAddress.Unit !== $scope.c.Unit) return true;
        		if ($scope.originalAddress.PostalCode !== $scope.c.PostalCode) return true;
        		if ($scope.originalAddress.City !== $scope.c.City) return true;
        		if ($scope.originalAddress.State !== $scope.c.State) return true;
        		if ($scope.originalAddress.Country !== $scope.c.Country) return true;

        		return false;
        	};


        	$scope.selectedCustomer = function (customer) {
        		$scope.selectedDuplicateCustomer = customer;
        	};

        	$scope.loadSelectedCustomer = function (id) {
        		$scope.c.Id = id;
        		$scope.getCustomer(id);
        		$scope.customerViewId = 0;
        		$scope.selectBeneficiary(0);
        	};

        	$scope.saveSelectedCustomer = function (id) {
        		$scope.customerViewId = 0; //toggle - displays the main section
        		$scope.c.AcceptDataDuplicate = true;
        		$scope.save(2);
        	};
        	var hasDigit = function (str) {
        		return str.match(/\d+/g);
        	};

        	var createAddressRequest = function () {
        		var postalCode = $scope.c.PostalCode == undefined ? '' : $scope.c.PostalCode;
        		var stateCode = $scope.c.State == undefined ? '' : $scope.c.State;
        		var city = $scope.c.City == undefined ? '' : $scope.c.City;

        		var requestParams = {
        			addressLine: $scope.c.Address,
        			adminDistrict: stateCode,
        			locality: city,
        			countryRegion: $scope.c.Country,
        			postalCode: postalCode,
        			unitNumber: ""
        		};
        		return requestParams;
        	};

        	var getUpdatedAddress = function (location) {

        		var requestParams = {
        			addressLine: location.address.addressLine,
        			adminDistrict: location.address.adminDistrict,
        			locality: location.address.locality,
        			countryRegion: location.address.countryRegion,
        			postalCode: location.address.postalCode,
        			unitNumber: ""
        		};

        		return requestParams;
        	};

        	$scope.resultToAddressRequest = function (result) {
        		var requestParams = {
        			addressLine: result.addressLine,
        			adminDistrict: result.adminDistrict,
        			locality: result.locality,
        			countryRegion: result.countryRegionIso2,
        			postalCode: result.postalCode,
        			unitNumber: ""
        		};

        		return requestParams;
        	};

        	$scope.createValidationLogEntry = function (correction) {
        		var updatedAddress, validationCode, result;
        		var self = this;
        		var originalAddress = createAddressRequest();
        		var results = $scope.addList;

        		if (correction) {
        			var validCode = 1;
        			result = correction;
        			updatedAddress = getUpdatedAddress(correction);
        			validationCode = validCode;
        		} else if (results && results.length > 0) {
        			var overrideCode = 3;
        			result = results[0];
        			updatedAddress = $scope.resultToAddressRequest(result.address);
        			validationCode = overrideCode;
        		} else {
        			var invalidCode = 0;
        			updatedAddress = null;
        			result = null;
        			validationCode = invalidCode;
        		}

        		return {
        			request: originalAddress,
        			response: updatedAddress,
        			result: result,
        			validationStatus: validationCode
        		};
        	};

        	$scope.cacheLogEntry = function (entry) {
        		var request = entry.request;
        		var response = entry.response;
        		var result = entry.result;
        		var validationStatus = entry.validationStatus;

        		response = response || {
        			addressLine: "",
        			unitNumber: "",
        			locality: "",
        			postalCode: "",
        			countryRegion: "",
        			adminDistrict: ""
        		};

        		result = result || {
        			confidence: "",
        			minConfidence: "Low",
        			point: { coordinates: [0.0, 0.0] }
        		};

        		var data = {
        			requestTime: $scope.addressService.requestTime,
        			responseTime: $scope.addressService.responseTime,
        			providerId: $scope.addressService.providerId,
        			requestToId: 1,
        			confidence: result.confidence,
        			minConfidence: result.confidence,
        			latitude: result.point.coordinates[0],
        			longitude: result.point.coordinates[1],
        			validationStatus: validationStatus,
        			customerId: 0,
        			customerTypeId: 0,
        			statusCode: "Success"
        		};

        		$scope.logger.cacheLogEntry($scope.logger.createLogEntry(request, response, data));
        	};


        	$scope.addressValidation = function (max, callBack) {
        		if (callBack != undefined && !$scope.Model.UseAddressValidation) {
        			$scope.compareAddress();
        			return;
        		}

        		var address = $scope.c.Address == undefined ? '' : $scope.c.Address;
        		var postalCode = $scope.c.PostalCode == undefined ? '' : $scope.c.PostalCode;
        		var stateCode = $scope.c.State == undefined ? '' : $scope.c.State;
        		var city = $scope.c.City == undefined ? '' : $scope.c.City;

        		max = (max == undefined ? 20 : max);

        		if ($scope.Model.UseLatitudeLongitude) {
        			$scope.resetLatitudeAndLongitude();
        		}

        		if (address.length >= 3) {
        			var addressLocationService = new LocationService($scope.Model.AddressLocationKey);
        			var serviceRequest = {
        				addressLine: address,
        				adminDistrict: stateCode,
        				locality: city,
        				countryRegion: $scope.c.Country,
        				postalCode: postalCode
        			};
        			$scope.addressService.requestTime = new Date().toUTCString().replace(" UTC", "Z");

        			addressLocationService.requestLocation(serviceRequest, function (locations) {
        				$scope.addressService.responseTime = addressLocationService.responseTime;
        				$scope.addressService.confidence = addressLocationService.confidence;
        				$scope.addressService.providerId = addressLocationService.providerid;

        				if (locations == undefined || locations.length <= 0) {
        					if (callBack != undefined) {
        						$scope.$apply($scope.compareAddress());
        					}
        					return;
        				} else if (locations.length > 0) {

        					if (locations[0].address.postalCode == undefined) {
        						locations = [];
        					}
        					setTimeout(function () {

        						var autocompleteCorrections = [];

        						for (var i = 0; i < locations.length && autocompleteCorrections.length < max; i++) {
        							var location = locations[i];
        							var address = location.address;
        							if (address.addressLine && hasDigit(address.addressLine)) {
        								//add name attribute since melissa data does not return it
        								if (!location.name) {
        									location.name = address.addressLine + ', ' + address.locality + ', ' + address.adminDistrict + ', ' + address.postalCode;
        								}
        								autocompleteCorrections.push(location);
        							}
        						}

        						$scope.autoCompleteLocations = $scope.Model.UseAddressAutocomplete ? autocompleteCorrections : [];
        						$scope.$apply($scope.locations = autocompleteCorrections);
        						if (callBack != undefined) {
        							callBack();
        						}
        					});
        				}


        			});

        		} else {
        			$scope.locations = [];
        			$scope.autoCompleteLocations = [];
        		}
        	};

        	$scope.setAddress = function (location) {


        		var logEntry = $scope.createValidationLogEntry(location);
        		$scope.cacheLogEntry(logEntry);

        		$scope.locations = [];
        		$scope.autoCompleteLocations = [];
        		$scope.c.Address = location.address.addressLine;
        		$scope.c.PostalCode = location.address.postalCode;
        		$scope.c.City = location.address.locality;

        		$scope.c.State = getStateAbbreviation(location.address.adminDistrict);
        		$scope.c.Latitude = location.point.coordinates[0];
        		$scope.c.Longitude = location.point.coordinates[1];
        		$scope.c.Unit = "";
        		$scope.addressSelectedFromAutocomplete = true;
        		$scope.setOriginalAddress($scope.c);
        	};


        	$scope.showDataQualityView = function () {

        		var max = 10;

        		if ($scope.Model.AddressCorrectionMaximum)
        			max = $scope.Model.AddressCorrectionMaximum;

        		$scope.addressValidation(max, function () {
        			$scope.address = {};
        			$scope.$apply($scope.setAddresssDataValidation());
        		});
        	};

        	var getStateAbbreviation = function (stateName) {

        		var stateObj = ($filter('filter')($scope.customerStates, { abbrev: stateName }));

        		if (stateObj.length > 0) {
        			return stateObj[0].abbrev;
        		} else {
        			stateObj = ($filter('filter')($scope.customerStates, { state: stateName }));
        			if (stateObj.length > 0) {
        				return stateObj[0].abbrev;
        			}
        		}

        		return "";
        	};

        	$scope.compareAddress = function () {
        		$scope.selectedLoc = 1;
        		$scope.fullAddress = '';
        		var first = true;
        		var adds = ['Address', 'City', 'State', 'PostalCode'];
        		angular.forEach(adds, function (l) {
        			if ($scope.c[l] != undefined && $scope.c[l] != '') {
        				if ($scope.c[l].length > 0) {
        					if (!first) {
        						$scope.fullAddress += ", ";
        					}
        					$scope.fullAddress += $scope.c[l];
        					first = false;
        				}
        			}
        		});
        		$scope.addressValidationView = !first;
        	};

        	$scope.selectedAddress = function (add) {
        		$scope.selectedLoc = 2;
        		$scope.address = add;
        	};
        	$scope.acceptCorrection = function () {

        		$scope.addressValidationView = false;
        		$scope.addressConfirmed = true;

        		var correction = $scope.selectedLoc == 0 ? false : $scope.address;
        		var logEntry = $scope.createValidationLogEntry(correction);
        		$scope.cacheLogEntry(logEntry);

        		$scope.validationStatus = logEntry.validationStatus;

        		if ($scope.selectedLoc == 0) {
        			$scope.save(4);
        			return;
        		}

        		$scope.locations = [];
        		$scope.autoCompleteLocations = [];
        		$scope.c.Address = $scope.address.address.addressLine;
        		$scope.c.PostalCode = $scope.address.address.postalCode;
        		$scope.c.City = $scope.address.address.locality;
        		if ($scope.address.address.adminDistrict) {
        			$scope.c.State = getStateAbbreviation($scope.address.address.adminDistrict);
        		}

        		$scope.c.Latitude = $scope.address.point.coordinates[0];
        		$scope.c.Longitude = $scope.address.point.coordinates[1];
        		$scope.save(4);
        	};
        	$scope.resetLatitudeAndLongitude = function () {
        		$scope.c.Latitude = 0;
        		$scope.c.Longitude = 0;
        		$scope.addressConfirmed = false;
        	};
        	$scope.setAddresssDataValidation = function () {
        		$scope.addList = angular.copy($scope.locations);
        		var temp = [];
        		angular.forEach($scope.addList, function (l) {
        			if (l.address.addressLine != undefined) {
        				temp.push(l);
        			}
        		});
        		$scope.addList = temp;
        		$scope.compareAddress();
        	};

        	$scope.saveCachedLogEntries = function (customerId) {
        		var self = this;
        		this.logger.getCachedEntries().forEach(function (entry) {
        			entry.customerId = customerId;
        			entry.customerTypeId = 0; //what is this
        		});

        		this.logger.saveCachedLogEntries();
        	};


        	$scope.save = function (actionId) {
        		if (actionId === 3)
        			$scope.savingId = true;

        		if (actionId == 1)
        			$scope.savingCustomer = true;


        		$scope.IdInvalid = false;
        		if (actionId == 1 && $scope.serviceId === 111) {
        			var orderValidationData = $("#unifyingDiv").data("orderValidationData");
        			$scope.IdTabRequired = $scope.IdTabRequired || (orderValidationData && orderValidationData.identificationInValid);
        			$scope.IdInvalid = orderValidationData && orderValidationData.identificationInValid;
        		}

        		$ceFactory.clearErrors();
        		$scope.c.DobText = $("#Dob").val();
        		$scope.c.ConfirmDobText = $("#ConfirmDob").val();

        		$scope.c.AddressValidationStatusId = $scope.validationStatus || ($scope.addressSelectedFromAutocomplete ? 1 : 0);

        		if ($scope.i != undefined) {
        			if ($scope.i.IdNumber != undefined) {
        				$scope.c.IdNumber = $scope.i.IdNumber;
        			}
        			if ($scope.i.IdNumber === null) {
        				$scope.c.IdNumber = '';
        			}

        			if ($scope.i.IdTypeSpecific != undefined) {
        				$scope.c.SpecificIdType = $scope.i.IdTypeSpecific;
        			}
        			$scope.c.IdCountryIssued = $scope.iDcountry;
        			if ($scope.i.IdType != undefined) {
        				$scope.c.BothIdType = $scope.i.IdType;
        			}
        			if ($scope.iDcountry != undefined) {
        				$scope.c.IssuedByCountry = $scope.iDcountry;
        			}
        		}

        		var setting = $filter('filter')($scope.fields, { Name: 'fImageID' });
        		var imageRequired = setting.length > 0 && setting[0].Required;


        		if (!$scope.addressConfirmed)
        			$scope.addressConfirmed = ($scope.c.IsAddressValidated || $scope.c.AddressValidationStatusId === 1) && !$scope.addressChanged();

        		$http({
        			method: 'POST',
        			data: {
        				customer: $scope.c,
        				validationRequestParams: $scope.queryString,
        				serviceId: $scope.serviceId,
        				param: $scope.bsaParam,
        				sar: $ceFactory.getSar($scope.nceobj),
        				identficationViewId: $scope.identficationViewId,
        				imageRequired: imageRequired
        			},
        			url: '/Classic/CustomerEnrollment2/SaveCustomer'
        		}).success(function (data) {
        			if (data.result.Id) {
        				$scope.c.Id = data.result.Id;
        				$scope.c.CustomerIdNumber = "C" + data.result.Id;

        				// to maintain data change status
        				$scope.initialCustomer = angular.copy($scope.c);

        			}

        			if (!data.result.Errors && data.isAddressValidationEnabled && !$scope.addressConfirmed && actionId === 1) {

        				if ($scope.Model.UseLatitudeLongitude) {
        					if (($scope.c.Address != undefined && $scope.c.Address.length > 0) && ($scope.c.Latitude == 0 || $scope.c.Longitude == 0)) {
        						$scope.showDataQualityView();
        						return;
        					}

        				} else {
        					if ($scope.c.Address != undefined && $scope.c.Address.length > 0) {
        						$scope.showDataQualityView();
        						return;
        					}
        				}
        			}
        			if (data.result.HasDuplicate) {
        				$scope.rejectCreateNew = data.result.RejectCreateNew;
        				$scope.duplicateCustomerList = data.result.Result;
        				$scope.customerViewId = 12;
        				$scope.isByDob = data.isDuplicateSarchByDob;
        				$scope.tabId = 0;
        			} else if (data.result.IsValid) {
        				$scope.saveCachedLogEntries(data.result.Id);
        				$scope.c.Id = data.result.Id;
        				$scope.c.CustomerIdNumber = "C" + data.result.Id;
        				$scope.dataQualityView = false;
        				window.unsaved = false;
        				$scope.scanMessage = null;

        				if ($scope.IdTabRequired == true && ($scope.imageFrontSideKey != null || $scope.imageBackSideKey != null)) {
        					$scope.getCustomerIds();
        					if (actionId == 3 && $scope.identficationViewId == 21) {
        						$scope.saveIndentificationDetails();
        					}
        				}
        				else if ($scope.IdTabRequired && $scope.IdInvalid) {
        					$scope.getCustomerIds();
        				}
        				else if ($scope.IdTabRequired === true && $scope.i != undefined) {
        					if ($scope.i.IdNumber != undefined && $scope.identficationViewId == 21) {
        						$scope.saveIndentificationDetails();
        					} else {
        						$scope.validateDefaultImage(data, actionId);

        						if (actionId == 1 && $scope.iDcountry && $scope.identficationViewId == 21) {
        							$scope.tabId = 1;
        						}
        						else if ($scope.defaultImageExists) {
        							$scope.selectBeneficiary($ceFactory.getBeneId($scope.sourceId, $scope.nceobj.CustomerIdTo));
        						}
        					}
        				} else {
        					setTimeout(function () {
        						$scope.validateDefaultImage(data, actionId);
        						if (actionId == 1 && $scope.iDcountry && $scope.identficationViewId == 21) {
        							$scope.tabId = 1;
        						}
        						else if ($scope.defaultImageExists) {
        							$scope.selectBeneficiary($ceFactory.getBeneId($scope.sourceId, $scope.nceobj.CustomerIdTo));
        						}
        					}, actionId == 3 ? 3000 : 0);
        				}

        			} else {
        				$scope.IdTabRequired = $scope.IdTabRequired || data.result.Error === 80;
        				if (data.result.Error === 80) {
        					$scope.getCustomerIds();
        				}

        				$scope.dataQualityView = data.result.Error === 12;
        				$scope.fields = data.fields;
        				if (data.result.Error !== 80) {
        					$scope.validateDefaultImage(data, actionId);
        				}

        				setTimeout(function () {
        					$scope.setMessage(data);
        				}, 0);
        			}
        			setTimeout(function () {
        				$scope.setMessage(data);
        			}, 0);

        			//$scope.savingId = false;
        			$scope.savingCustomer = false;
        		});
        	};


        	$scope.validateDefaultImage = function (data, actionId) {
        		if (actionId === 1 || actionId === 3) {
        			if (data.result.Errors && data.result.Errors.length === 1 && (data.result.Errors[0].Key === "IDimage" || data.result.Errors[0].Key === "c.fImageID")) {
        				if (actionId === 1) {
        					$scope.getCustomerIds();
        				}
        				$scope.tabId = 1;
        				$scope.defaultImageExists = false;
        				$scope.savingId = false;

        				setTimeout(function () {
        					$scope.defaultImageExists = true;
        					$scope.$apply();
        				}, 5000);
        			}
        			else if (data.result.Errors) {
        				$scope.defaultImageExists = true;
        				$scope.tabId = 0;
        			}
        			else {
        				$scope.defaultImageExists = true;
        			}
        		}
        	};

        	$scope.getCustomerEnrollmentList = function () {

        		if ($scope.countries == null) {
        			$http({
        				method: 'POST',
        				url: '/Classic/CustomerEnrollment2/EnrollmentList'
        			}).success(function (data) {
        				$scope.countries = data.countries;
        				$scope.customerStates = data.states;
        				$scope.countryName = data.countryName;
        				$scope.countryCodes = data.countryCodes;
        				$scope.locales = data.locales;
        				$scope.locale = data.locale;
        				/**/
        				$scope.agentCountry = data.agentCountry;
        				$scope.c.Locale = data.locale;
        				$scope.setDefaultValue();
        			});
        		} else {
        			$scope.setDefaultValue();
        		}
        	};

        	$scope.createNewCustomer = function () {
        		$scope.c = $ceFactory.getCustomer($scope.requestData, $scope.callerId, $scope.Model.StateCode, $scope.Model.Country, $scope.nceobj.CustomerIdEncrypted);
        		$scope.c.Locale = $scope.locale;
        		$scope.countryOfResidence = $scope.countryName;
        		$scope.getCustomerEnrollmentList();
        		$scope.scanId = false;
        		$scope.startScan = false;
        		//$scope.telePhoneModel = {
        		//	countryCode: $scope.Model.Country,
        		//	number: ""
        		//};
        		//$scope.telePhoneModel2 = {
        		//	countryCode: $scope.Model.Country,
        		//	number: ""
        		//};
        		$('#CustomerEnrollmentApp #FirstName').focus();
        	};


        	$scope.filterTelCode = function (value, index) {
        		var l = 'line' + index;
        		var f = 'f' + index;
        		$scope[f] = null;
        		$scope[f] = [];
        		var temp = [];
        		$scope[l] = 1;
        		angular.forEach($scope.countryCodes, function (item) {

        			if (item.name.toLowerCase().indexOf(value.toLowerCase()) === 0) {
        				temp.push(item);
        			} else if (item.abbrev.toLowerCase().indexOf(value.toLowerCase()) === 0) {
        				temp.push(item);
        			} else if (item.code == value) {
        				temp.push(item);
        			}
        		});
        		if (temp.length < 1) {
        			$scope[l] = 0;
        		} else {
        			$scope[f] = temp;
        		}

        	};
        	$scope.setDefaultValue = function () {
        		if ($scope.c.Id < 1 || $scope.c.Id == undefined) {
        			$scope.c.Country = $scope.agentCountry;
        			$scope.c.Locale = $scope.locale;
        		}
        		var telLine1Code = $scope.c.Country;
        		var telLine2Code = $scope.c.Country;
        		if ($scope.c.Id > 1) {
        			telLine1Code = $scope.c.TelLine1Code.replace(/\s+/g, '') != "" ? $scope.c.TelLine1Code : $scope.c.Country;
        			telLine2Code = $scope.c.TelLine2Code.replace(/\s+/g, '') != "" ? $scope.c.TelLine2Code : $scope.c.Country;
        		}

        		$scope.findCountryName(telLine1Code, 'TelLine1Code', 1, $scope.c);
        		$scope.findCountryName(telLine2Code, 'TelLine2Code', 2, $scope.c);

        		$scope.setPreferredLangauge();

        		if ($scope.c.Id < 2) {
        			$scope.c.Residence = $scope.agentCountry;
        			$scope.countryOfResidence = $scope.countryName;
        			$scope.c.CustomerLocale = $scope.locale;
        			$scope.setPreferredLangauge();
        		} else {
        			$scope.getCountry($scope.c.Residence);
        		}

        		// to maintain data change status
        		$scope.initialCustomer = angular.copy($scope.c);


        	};

        	$scope.setBenPhoneCodes = function () {
        		$scope.findCountryName($scope.b.Country, 'TelLine1Code', 3, $scope.b);
        		$scope.findCountryName($scope.b.Country, 'TelLine2Code', 4, $scope.b);
        	};
        	$scope.findCountryName = function (code, key, index, model) {
        		var c = $scope.countryCodes;
        		var countryCodeFound = false;

        		window.angular.forEach(c, function (item) {
        			if (item.abbrev == code && item.abbrev != undefined) {
        				countryCodeFound = true;
        				$scope.setCountryCode(key, item, index, false, model);
        			}
        		});
        		if (!countryCodeFound) {
        			var item = {
        				code: 0,
        				showArea: false,
        				abbrev: code,
        				areaMaxLen: 10,
        				maxLen: 0
        			};
        			$scope.setCountryCode(key, item, index, false, model);
        		}
        	};

        	$scope.setCountryCode = function (key, value, index, reset, model) {
        		model[key] = value.abbrev;
        		$scope['line' + index] = 0;
        		$scope['tel' + index] = {
        			code: value.code,
        			showArea: value.showArea,
        			areaLen: value.areaMaxLen,
        			numLen: value.maxLen
        		};
        		if (reset) {
        			$scope.c['TelLine' + index + 'AreaCode'] = null;
        			$scope.c['TelLine' + index] = null;
        		}
        	};

        	$scope.verifyAreaLen = function ($event, obj, len, index) {
        		if (obj != undefined && (obj.length) == len) {
        			$(index).focus();
        		}
        	};

        	$scope.getQuestions = function () {
        		$scope.tabId = 2;
        		$http({
        			method: 'POST',
        			data: {
        				id: $scope.c.Id,
        				validationRequestParams: $scope.queryString
        			},
        			url: '/Classic/CustomerEnrollment2/GetComplianceQuestions'
        		}).success(function (data) {
        			$scope.questions = data;
        			$scope.initialQuestions = angular.copy($scope.questions);
        		});
        	};

        	$scope.saveCompliance = function () {

        		$scope.complianceAnswer = "";
        		$scope.complianceValidForm = true;


        		angular.forEach($scope.questions, function (obj, i) {
        			obj.QuestionAsked = obj.Question;
        			if (obj.AnswerObject) {
        				obj.Answer = obj.AnswerObject.Key;
        			}
        			if (obj.IsRequired && obj.Answer.toString().trim().length <= 0) {
        				$scope.complianceValidForm = false;
        			}
        		});
        		if ($scope.complianceValidForm) {
        			$http({
        				method: 'POST',
        				data: { questions: $scope.questions, id: $scope.c.Id },
        				url: '/Classic/CustomerEnrollment2/SaveComplianceAnswers'
        			}).success(function (data) {
				        $scope.complianceAnswer = data.complianceAnswers.toString().replace(/\"/g, '');
        				data.result = data;
        				$scope.setMessage(data);
        				$scope.selectBeneficiary(0);

        				$scope.initialQuestions = angular.copy($scope.questions);

        			});
        		}
        	};

        	$scope.getStates = function (ctry, list) {
        		if (ctry == undefined || ctry == null || ctry == '')
        			return;
        		if (ctry.trim().length < 2)
        			return;

        		$scope[list] = null;
        		if (ctry == null) {
        			return;
        		}

        		var stateMatch = true;
        		if (list === 'states') {
        			stateMatch = false;
        		}

        		$http({
        			method: 'GET',
        			url: '/Classic/CustomerEnrollment/api/Countries/' + ctry + '/States'
        		}).success(function (data) {
        			$scope[list] = data;
        			angular.forEach(data, function (obj, i) {
        				if (obj.abbrev === $scope.c.StateOfBirth)
        					stateMatch = true;
        			});
        			if (!stateMatch) {
        				$scope.c.StateOfBirth = "";
        			}
        		});
        	};

        	$scope.deleteImageFromCache = function (key) {

        		if (key === $scope.imageFrontSideKey)
        			$scope.imageFrontSideKey = null;
        		if (key === $scope.imageBackSideKey)
        			$scope.imageBackSideKey = null;
        		$http({
        			method: 'POST',
        			data: { key: key },
        			url: '/Classic/CustomerEnrollment/DeleteImageFromCache'
        		}).success(function (data) {
        		});

        		if ($scope.imageFrontSideKey == null && $scope.imageBackSideKey == null) {
        			$scope.loadScanTemplate();
        		}
        	};

        	$scope.restartScan = function () {
        		$scope.restartScanView();
        		$scope.close();
        	};
        	$scope.restartScanView = function () {
        		$scope.deleteOcrImages();
        		$scope.scanCompleted = false;
        		$scope.scanInProgress = false;
        		$scope.scanStarted = false;
        	};

        	$scope.deleteOcrImages = function () {
        		$("#divNewCarousel").empty();
        		$http({
        			method: 'POST',
        			data: {
        				front: $scope.imageFrontSideKey,
        				back: $scope.imageBackSideKey
        			},
        			url: '/Classic/CustomerEnrollment2/DeleteImages'
        		}).success(function (data) {
        			$("#divFrontSideImage").empty();
        			$("#divBackSideImage").empty();
        			$("#divNewCarousel").empty();
        			$scope.imageFrontSideKey = null;
        			$scope.imageBackSideKey = null;
        		});
        	};

        	$scope.setAsDefault = function (id) {
        		$http({
        			method: 'POST',
        			data: {
        				id: id,
        				customerId: $scope.c.Id
        			},
        			url: '/Classic/CustomerEnrollment2/MakeItDefault'
        		}).success(function (data) {
        			$scope.getCustomerIds(id);
        		});
        	};

        	//Begin Identification
        	$scope.getCustomerIds = function (identificationId) {
        		if ($scope.addressValidationView)
        			return;
        		$scope.tabId = 1;
        		if ($scope.IdTabRequired == true && ($scope.imageFrontSideKey != null || $scope.imageBackSideKey != null)) {

        			$scope.identificationImages = null;
        			$("#divCarousel").empty();

        			$scope.identficationViewId = 21;
        			if (($scope.imageFrontSideKey == undefined || $scope.imageFrontSideKey == null) && ($scope.imageBackSideKey == undefined || $scope.imageBackSideKey == null)) {
        				return;
        			}

        			$scope.newIdImages = [];
        			var imageFrontSideUrl = $scope.Scheme + "://" + $scope.Authroity + "/Classic/CustomerEnrollment/GetImage?imageKey=" + $scope.imageFrontSideKey + "&dt=" + new Date();

        			$scope.newIdImages.push({ 'Url': imageFrontSideUrl });

        			if ($scope.imageBackSideKey != undefined && $scope.imageBackSideKey != null) {
        				var imageBackSideUrl = $scope.Scheme + "://" + $scope.Authroity + "/Classic/CustomerEnrollment/GetImage?imageKey=" + $scope.imageBackSideKey + "&dt=" + new Date();
        				$scope.newIdImages.push({ 'Url': imageBackSideUrl });
        			}

        			$scope.$parent.newimgListCalled = false;
        			$scope.newCarouselDisplayed = false;

        			$("#divNewCarousel").empty();
        			var html = $templateCache.get("new-scanned-Image-template");
        			$("#divNewCarousel").append("<ul class='jcarousel-skin-image'></ul>");
        			$("#divNewCarousel ul").append($compile(html)($scope));

        			$timeout(function () {
        				if ($scope.newCarouselDisplayed == false) {
        					$scope.formatNewCustomerCarousel();
        				}
        			});
        			return;
        		}

        		if ($scope.c && $scope.c.Id <= 0) {
        			$scope.c.Id = $scope.nceobj.CustomerIdEncrypted;
        		}

        		if ($scope.c && $scope.c.Id > 0) {

        			$scope.savingId = true;

        			$http({
        				method: 'POST',
        				data: {
        					customerId: $scope.c.Id,
        					locale: $scope.locale
        				},
        				url: '/Classic/CustomerEnrollment2/CustomerIds'
        			}).success(function (data) {
        				$scope.idListCalled = false;
        				$scope.$parent.newimgListCalled = false;
        				$scope.newCarouselDisplayed = false;
        				$scope.customerIds = data.items;
        				$scope.postUrl = data.postUrl;
        				$scope.i = $ceFactory.getIdentificationModel();
        				$("#dvIdentificationList").empty();
        				var html = $templateCache.get("identification-template");
        				$("#dvIdentificationList").append("<ul id='id-list' class='jcarousel-skin-image'></ul>");
        				$("#dvIdentificationList ul").append($compile(html)($scope));

        				$scope.idListDisplayed = false;
        				if ($scope.customerIds.length == 0) {
        					$scope.addNewIndetification();
        				} else {

        					angular.forEach($scope.customerIds, function (obj, i) {
        						if (obj.IdTypeName == "")
        							obj.IdTypeName = $ceFactory.getIdTypeName(obj.IdType, $scope.idTypes2);

        						if (obj.IsDefault) {
        							$scope.iDcountry = obj.IssuedByCoutry;
        						}
        					});

        					var response = $ceFactory.getSelectedCustomerId($scope.customerIds, identificationId);
        					$scope.displayIdentificationDetail(response.selectedObj);
        					$scope.currentSelectedId = response.currentSelectedId;
        				}

        				$timeout(function () {
        					$scope.formatCarousel1();
        				});

        				$scope.savingId = false;

        			});
        		} else {

        			$scope.savingId = false;

        			if ($scope.identficationViewId != 21) {
        				$scope.addNewIndetification();
        			}

        		}
        	};

        	$scope.getStateId = function (code) {
        		var stateId = 0;
        		angular.forEach($scope.issuedStates, function (obj, key) {
        			if (stateId > 0)
        				return;
        			if (obj.Code == code)
        				stateId = obj.Id;
        		});
        		return stateId;
        	};

        	$scope.getIdTypes = function (ctry) {
        		if (ctry == null)
        			return;
        		$http({
        			method: 'GET',
        			params: {
        				country: ctry
        			},
        			url: '/Classic/CustomerEnrollment2/GetIdTypes'
        		}).success(function (data) {
        			$scope.idTypes = data.result;

        		}).error(function (data) {
        			$scope.isValid = false;
        			$scope.closeMessage();
        		});
        	};

        	$scope.updateIdFields = function (country, idType) {
        		$scope.clearIdChanges();
        		$scope.i = $ceFactory.getIdentificationModel();
        		$scope.getIdentificationIdTypes(country);

        	};
        	$scope.clearIdChanges = function () {
        		$scope.issuedStates = null;
        		$scope.issuedOrganizations = null;
        		$scope.idTypeSpecifics = null;
        		$scope.idTypes2 = null;
        		$("#Issued").val("");
        		$("#IdExpired").val("");
        	};
        	$scope.getIdentificationIdTypes = function (country) {

        		$http({
        			method: 'POST',
        			url: '/Classic/CustomerEnrollment2/IdentificationTypes',
        			data: {
        				country: country
        			}
        		}).success(function (data) {
        			//var selectLabel = String.format("-- {0} --", Resources.Client.get("selectLabel"));
        			var allIdTypes = data.result;
        			$scope.i.IdType = "";
        			$scope.idTypes2 = allIdTypes;

        			setTimeout(function () {
        				$("#iType option:contains('──────────────')").attr('disabled', 'disabled');
        			}, 100);
        		});
        	};

        	$scope.addNewIndetification = function () {
        		$scope.discardIdChanges();
        		$scope.initAddNewId();
        		$scope.iDcountry = "";
        		//$scope.getIdentificationIdTypes($scope.agentCountry);
        	};
        	$scope.discardId = function () {
        		$scope.clearIdChanges();
        		$scope.initAddNewId();
        		$scope.lockIDSelection = false;
        		$scope.i = $ceFactory.getIdentificationModel();
        		$scope.iDcountry = "";
        		$scope.i = $ceFactory.getIdentificationModel();
        		if ($scope.customerIds != undefined && $scope.customerIds.length > 0) {
        			$scope.identficationViewId = 20;
        			$scope.getCustomerIds();
        		}
        		$scope.deleteOcrImages();
        	};
        	$scope.discardIdChanges = function () {

        		$scope.i = $ceFactory.getIdentificationModel();
        		//$scope.iDcountry = $scope.agentCountry;
        		$scope.clearIdChanges();
        	};
        	$scope.initAddNewId = function () {
        		$scope.identificationImages = null;
        		$("#divCarousel").empty();
        		$scope.identficationViewId = 21;
        	};

        	$scope.resetIdNumber = function () {

        		if ($scope.IdTabRequired !== true) {
        			$("#Issued").val("");
        			$("#IdExpired").val("");
        			$scope.i.IdNumber = null;
        			$scope.i.IssuedText = null;
        			$scope.i.ExpireText = null;
        		}
        	};
        	$scope.getNextFieldByType = function () {
        		$scope.i.State = -1,
                    $scope.i.OrganizationId = 0,
                    $scope.i.IdTypeSpecific = 0;
        		$scope.issuedOrganizations = null;
        		$scope.idTypeSpecifics = null;
        		$ceFactory.clearErrors();
        		$scope.getIdNextFields(1);
        		$scope.resetIdNumber();
        	};
        	$scope.getNextFieldByState = function () {
        		$scope.i.IdTypeSpecific = 0;
        		$scope.i.OrganizationId = 0,
                    $scope.resetIdNumber();
        		$scope.issuedOrganizations = null;
        		$scope.idTypeSpecifics = null;
        		$scope.getOrganizations($scope.i.State);
        	};

        	$scope.getNextFieldByIssuedBy = function () {
        		$scope.i.IdTypeSpecific = 0;
        		$scope.resetIdNumber();
        		$scope.idTypeSpecifics = null;
        		$scope.getSpecificIDs($scope.i.State, $scope.i.OrganizationId);

        	};

        	$scope.specificIds = null;
        	$scope.organizations = null;
        	$scope.getOrganizations = function (stateId) {

        		if ($scope.organizations == null) {
        			$scope.issuedOrganizations = null;
        			return;
        		}
        		$scope.issuedOrganizations = [];
        		angular.forEach($scope.organizations, function (obj) {

        			if (obj.StateId == stateId) {
        				$scope.issuedOrganizations.push(obj);

        			}
        		});
        		if ($scope.issuedOrganizations.length === 1) {
        			$scope.i.OrganizationId = $scope.issuedOrganizations[0].Id;
        			$scope.getSpecificIDs($scope.i.State, $scope.i.OrganizationId);
        		} else {
        			$scope.getSpecificIDs($scope.i.State, 0);
        		}
        	};
        	$scope.getSpecificIDs = function (stateId, orgId) {

        		if ($scope.specificIds == null) {
        			$scope.idTypeSpecifics = null;
        			return;
        		}
        		$scope.idTypeSpecifics = [];
        		angular.forEach($scope.specificIds, function (obj) {
        			if ((stateId == -1 || obj.StateId == stateId) && (orgId == 0 || obj.OrganizationId == orgId)) {
        				$scope.idTypeSpecifics.push(obj);
        			}
        		});
        		if ($scope.idTypeSpecifics.length === 1) {
        			$scope.i.IdTypeSpecific = $scope.idTypeSpecifics[0].Id;
        		}
        	};

        	$scope.getIdNextFields = function (level) {
        		var id_key = $ceFactory.getIdKeyPairs($scope.i.IdType);
        		if (id_key.key !== '0') {
        			$scope.i.IdTypeSpecific = id_key.key;
        			$scope.issuedStates = {};
        			return;
        		}

        		$http({
        			method: 'POST',
        			data: {
        				customerId: $scope.c.Id ? $scope.c.Id : 0,
        				idType: id_key.id,
        				countryCode: $scope.iDcountry,
        				stateId: $scope.stateId,
        				orgnizationId: $scope.i.OrganizationId
        			},
        			url: "/Classic/CustomerEnrollment/GetNextFields",
        		}).success(function (response) {

        			if (level == 1) {
        				$scope.issuedStates = response.IssuedStates;
        				$scope.organizations = response.IssuedOrganizations;
        				$scope.specificIds = response.IdTypeSpecifics;
        				if (response.IssuedStates == undefined || response.IssuedStates == null || response.IssuedStates.length < 1) {
        					$scope.getOrganizations(-1);
        					$scope.getSpecificIDs(-1, 0);
        				}
        			}
        		});
        	};

        	$scope.saveIndentificationDetails = function () {
        		$ceFactory.clearErrors();
        		var iD = {
        			CustomerId: $scope.c.Id,
        			StateId: $scope.i.State,
        			IssuedByCoutry: $scope.iDcountry,
        			IssuedText: $("#Issued").val(),
        			ExpireText: $("#IdExpired").val(),
        			OrganizationId: $scope.i.OrganizationId,
        			IdTypeSpecific: $scope.i.IdTypeSpecific,
        			IdNumber: $scope.i.IdNumber,
        			IdType: $scope.i.IdType
        		};
        		$http({
        			method: 'POST',
        			url: '/Classic/CustomerEnrollment2/SaveIndentification',
        			data: {
        				identification: iD,
        				locale: $scope.locale,
        				customerId: $scope.c.Id,
        				imageFrontSideKey: $scope.imageFrontSideKey,
        				imageBackSideKey: $scope.imageBackSideKey,
        				fileName: $scope.newImageFilePath,
        				idTypeName: $('#iType :selected').text()
        			}
        		}).success(function (data) {
        			$scope.savingId = false;

        			if (data != null && !data.result.IsValid) {
        				$scope.tabId = 1;
        			} else {
        				$scope.tabId = 0;
        			}
        			setTimeout(function () {
        				$scope.setMessage(data);
        			}, 0);
        			if (data.result.Error == 14)
        				alert(data.result.Message);
        			if (data != null && data.result.IsValid) {
        				$scope.lockIDSelection = false;
        				$scope.IdTabRequired = false;
        				$scope.clearIdChanges();
        				$scope.getCustomerIds(data.result.Id); //explicitly calling this method, so that customerlist would be repainted on jcarousel
        			}

        		});
        	};

        	$scope.displayIdentificationDetail = function (detail, viewId) {

        		$scope.i = $.extend(true, {}, detail);
        		$scope.i.State = detail.IssuedByState;
        		$scope.identficationViewId = 20;
        		$scope.identificationImages = null;
        		$("#divCarousel").empty();
        		if (detail.ImageId > 0) {
        			$scope.displayCarouselImages(detail.Id, detail.ImageId);
        		}
        		$scope.originalImageUrl = "";
        	};

        	$scope.deleteIdentification = function () {

        		$http({
        			method: 'POST',
        			url: "/Classic/CustomerEnrollment/DeleteIdentification",
        			params: {
        				customerId: $scope.c.Id,
        				id: $scope.i.Id
        			}
        		}).success(function (response) {
        			$scope.closeMessage();
        			$scope.getCustomerIds();
        		});
        	};

        	$scope.wait = false;
        	$scope.showCustomerDetails = function () {
        		window.unsaved = true;
        		$scope.wait = true;
        		$http({
        			method: 'POST',
        			data: {
        				imageKey: $scope.imageFrontSideKey,
        				imageBackSideKey: $scope.imageBackSideKey,
        				documentTypeId: $scope.i.IdType,
        				country: $scope.iDcountry
        			},
        			url: '/Classic/CustomerEnrollment2/ProcessImage'
        		}).success(function (data) {

        			$scope.scanId = false;
        			$scope.wait = false;
        			$scope.IdTabRequired = true;
        			$scope.scanMessage = data.message;
        			$scope.c = data.customer;
        			$scope.c.Country = $scope.agentCountry;
        			$scope.c.Locale = $scope.locale;
        			$scope.c.Residence = $scope.agentCountry;
        			$scope.c.CustomerLocale = $scope.locale;
        			$scope.c.TelLine1Code = $scope.c.Country;
        			$scope.c.TelLine2Code = $scope.c.Country;
        			$scope.c.IsLine1Mobile = true;
        			$scope.c.IsLine2Mobile = false;
        			$scope.setDefaultValue();
        			if (data.isSuccess === true) {
        				$scope.lockIDSelection = data.lockId;
        				if ($scope.i != null) {
        					$scope.i.State = $scope.getStateId($scope.c.State);
        					if ($scope.i.State > 0) {
        						$scope.getNextFieldByState();
        					}
        					$scope.i.IdNumber = data.idNumber;
        					$scope.i.IssuedText = data.issuedDate;
        					$scope.i.ExpireText = data.expiredDate;
        				}
        			}

        		});
        	};

            $scope.scanImageManual = function (source) {

                var scanner = {
                    StopDevice: function() {
                        console.log("StopDevice... Do nothing!");
                    },
                    GetPathToTempDir: function() {
                        return "C:\\Users\\nhussein\\AppData\\Local\\Temp\\";
                    },
                    SaveImage: function(a, b) {
                        return -1;
                    }
                };

                switch (source) {
                    case "frontSide":
                        $scope.saveFrontSide(scanner);
                        break;
                    case "backSide":
                        $scope.saveBackSide(scanner);
                        break;
                    default:
                        $scope.saveImage();
                        break;
                }
            };

        	$scope.scanImage = function (source) {

        	    console.log('booyah!', frontSide);

        		var scanner = document.getElementById("vsTwain1");
        		if (scanner.IsTwainAvailable == null) {
        			alert("Web application is not configured correctly.");
        			return;
        		}
        		try {
        			scanner.Register($scope.registeredTo, $scope.domainName, $scope.registrationCode);
        			scanner.StartDevice();
        			scanner.MaxImages = 1;
        			scanner.AutoCleanBuffer = 1;
        			scanner.DisableAfterAcquire = 1;

        			setTimeout(function () {
        				if (scanner.SelectSource() == 1) {
        					$scope.$apply(function () {
        						$scope.scanStarted = true;
        						$scope.scanInProgress = true;
        						$scope.scanCompleted = false;

        						angular.forEach($scope.countries, function (obj, i) {
        							if (obj.code == $scope.iDcountry)
        								$scope.issuedByCountrySelectedName = obj.name;
        						});

        						angular.forEach($scope.idTypes2, function (obj, i) {
        							if (obj.Key == $scope.i.IdType)
        								$scope.idTypeSelectedName = obj.Name;
        						});
        					});

        					scanner.ShowUI = 0;
        					scanner.OpenDataSource();
        					scanner.unitOfMeasure = 0;

        					scanner.PixelType = $scope.Model.UploadInColor ? 2 : 1;

        					scanner.resolution = 200;
        					while (scanner.AcquireModal()) {
        					}
        					//based on the source invoke approproirae method
        					switch (source) {
        						case "frontSide":
        							$scope.saveFrontSide(scanner);
        							break;
        						case "backSide":
        							$scope.saveBackSide(scanner);
        							break;
        						default:
        							$scope.saveImage();
        							break;
        					}
        				}
        			});
        		} catch (ex) {
        			scanner.StopDevice();
        		}
        	};

        	$scope.saveBackSide = function (scanner) {

        		var uploadControl = document.getElementById("uploadControl1");
        		uploadControl.addFields('cid', 0);
        		$scope.postImgUrl = $scope.Scheme + "://" + $scope.Authroity + "/Classic/CustomerEnrollment/SaveImage";
        		var response = $scope.postFile(scanner, uploadControl);
        		response = JSON.parse(response);
        		if (response.imageSaved == true) {
        			$scope.$apply(function () {
        				$scope.imageBackSideKey = response.imageKey;
        			});
        		}

        		$scope.$apply(function () {
        			$scope.scanCompleted = true;
        			$scope.scanInProgress = false;
        			$scope.scannedSuccessfully = true;
        		});

        		$scope.timestamp2 = $ceFactory.getTimestamp();
        		scanner.StopDevice();
        	};

        	$scope.saveFrontSide = function (scanner) {

        		var uploadControl = document.getElementById("uploadControl1");
        		uploadControl.Clear();
        		uploadControl.addFields('cid', 0);
        		uploadControl.addFields('iid', 0);
        		uploadControl.addFields('iiid', 0);
        		uploadControl.addFields('iipd', 0);
        		$scope.postImgUrl = $scope.Scheme + "://" + $scope.Authroity + "/Classic/CustomerEnrollment/SaveImage";
        		var response = $scope.postFile(scanner, uploadControl);
        		if (response == '') {

        			$scope.scanInProgress = false;
        			$scope.scanCompleted = false;
        			$scope.restartScanView();
        			scanner.StopDevice();
        			return;
        		}
        		response = JSON.parse(response);
        		if (response.imageSaved == true) {
        			// $scope.$apply(function () {
        				$scope.imageFrontSideKey = response.imageKey;
        				$scope.imageProcessed = response.imageSaved;
        				$scope.scanCompleted = true;
        				$scope.scanInProgress = false;
        				$scope.scannedSuccessfully = true;
        			// });
                };                    
        		$scope.timestamp1 = $ceFactory.getTimestamp();
        		scanner.StopDevice();
        	};

        	$scope.saveImage = function () {
        		try {

        			var uploadControl = document.getElementById("uploadControl1");
        			var scanner = document.getElementById("vsTwain1");
        			var imgPathSet = scanner.GetPathToTempDir() + "temp.jpg";

        			if (scanner.SaveImage(0, imgPathSet) == 0) {
        				alert(scanner.errorString);
        			} else {
        				var previewImageObject = document.getElementById("cropbox");
        				uploadControl.Clear();
        				uploadControl.URL = $scope.postUrl;
        				uploadControl.File = imgPathSet;
        				uploadControl.FieldName = "fImage";
        				uploadControl.addFields('cid', $scope.c.Id);
        				uploadControl.addFields('iid', $scope.i.Id);
        				uploadControl.addFields('iiid', $scope.i.ImageId == null || $scope.i.ImageId == undefined ? 0 : $scope.i.ImageId);
        				uploadControl.addFields('iipd', '');
        				var response = uploadControl.Upload_Start();
        				uploadControl.Clear();
        				//console.log(response);


        				if ($scope.c.Id > 0) {
        					$scope.scanText = "Start Scan";
        					$scope.i.ImageId = response;


        					if ($scope.i.ImageId <= 0 || $scope.i.ImageId == undefined) {
        						//console.log("GetID");

        						$scope.getCustomerIds(21, $scope.i.Id);
        					} else {
        						$scope.displayIdentificationDetail($scope.i);
        					}
        				} else {
        					$scope.processImage(response);
        				}
        			}
        			scanner.StopDevice();

        		} catch (e) {

        		}
        	};

        	$scope.postFile = function (scanner, uploadControl) {
        		var response = "";
        		try {

        			if (uploadControl == undefined)
        				var uploadControl = document.getElementById("uploadControl1");

        			// var scanner = document.getElementById("vsTwain1");
        			var imgPathSet = scanner.GetPathToTempDir() + "temp.jpg";

        			if (scanner.SaveImage(0, imgPathSet) == 0) {
        				alert(scanner.errorString);
        			} else {
        				var previewImageObject = document.getElementById("cropbox");
        				uploadControl.URL = $scope.postImgUrl;
        				uploadControl.File = imgPathSet;
        				uploadControl.FieldName = "fImage";
        				response = uploadControl.Upload_Start();
        				uploadControl.Clear();
        			}
        			scanner.StopDevice();
        		} catch (e) {
        			scanner.StopDevice();
        			alert("Image is not saved: " + e.message);
        		}
        		return response;
        	};

        	$scope.displayCarouselImages = function (identificationId, imageId) {
        		var html = $templateCache.get("carousel-template");
        		$identificationService.displayCarouselImages(identificationId, imageId, $scope.c.Id)
                    .then(function (response) {
                    	$scope.imgListCalled = false;
                    	$scope.identificationImages = null;
                    	$scope.identificationImages = response;
                    	$("#divCarousel ul").append($compile(html)($scope));
                    	$scope.carouselDisplayed = false;
                    	$timeout(function () {
                    		$scope.formatCarousel();
                    	});
                    },
                        function (e) {
                        });
        	};

        	$scope.deleteImage = function (customerId, identificationId, imageId1, imageId2) {
        		if (confirm(Resources.Client.get("photoConfirmDelete"))) {
        			$http({
        				url: "/Classic/CustomerEnrollment/DeleteIdentificationImage/",
        				method: 'GET',
        				params: {
        					customerId: customerId,
        					identificationId: identificationId,
        					imageId1: imageId1,
        					imageId2: imageId2
        				}
        			}).success(function (response) {
        				$scope.displayCarouselImages(identificationId, imageId1);
        			});
        		}
        	};

        	$scope.formatCarousel = function () {
        		if ($scope.identificationImages != undefined) {
        			$scope.carouselDisplayed = true;
        			$('#divCarousel ul').jcarousel({
        				start: 0,
        				scroll: 1,
        				visible: 1,
        				itemFallbackDimension: 300,
        				size: $scope.identificationImages.length,
        				initCallback: function (carousel) {
        					$.delayFor(100).then(function () {
        						self.$('.jcarousel-prev').click();
        					});
        				}
        			});
        			if ($scope.identificationImages.length < 2) {
        				$('#divCarousel .jcarousel-prev').hide();
        				$('#divCarousel .jcarousel-next').hide();
        			}
        		}
        	};

        	$scope.formatCarousel1 = function () {
        		if ($scope.customerIds != undefined) {
        			$scope.idListDisplayed = true;
        			$('#dvIdentificationList ul').jcarousel({
        				start: 0,
        				scroll: 1,
        				visible: 4,
        				itemFallbackDimension: 300,
        				size: $scope.customerIds.length,
        				initCallback: function (carousel) {
        					$.delayFor(100).then(function () {
        						self.$('.jcarousel-prev').click();
        					});
        				}
        			});
        			if ($scope.customerIds.length < 5) {
        				$('#dvIdentificationList .jcarousel-prev').hide();
        				$('#dvIdentificationList .jcarousel-next').hide();
        			}
        		}
        	};

        	$scope.formatNewCustomerCarousel = function () {
        		$scope.newCarouselDisplayed = true;
        		$timeout(function () {
        			$('#divNewCarousel ul').jcarousel({
        				start: 0,
        				scroll: 1,
        				visible: 1,
        				itemFallbackDimension: 300,
        				size: $scope.newIdImages.length,
        				initCallback: function (carousel) {
        					$.delayFor(100).then(function () {
        						self.$('.jcarousel-prev').click();
        					});
        				}
        			});

        		});
        	};

        	$scope.displayImageForCrop = function (url, imageId2, identificationId, imageListId) {

        		$scope.identficationViewId = 22;
        		$scope.originalImageUrl = url + "&date = " + new Date();
        		$scope.originalImageId = imageId2;
        		$scope.originalIdentificationId = identificationId;
        		$scope.originalImageListId = imageListId;
        		$ceFactory.displayImageForCrop($scope.originalImageUrl);
        	};

        	$scope.saveCroppedImage = function () {
        		if ($("#croppedImage").attr("src") != '') {
        			var cropService = $ceService.get("/Classic/CustomerEnrollment/AcceptCrop",
        			{
        				'customerId': $scope.c.Id,
        				'identificationId': $scope.originalIdentificationId,
        				'imagePageId': $scope.originalImageId
        			});
        			cropService.then(function (response) {
        				$scope.identficationViewId = 20;
        				$scope.displayCarouselImages($scope.originalIdentificationId, $scope.originalImageListId);
        			},
                        function (e) {
                        });
        		}
        	};

        	$scope.zoomImage = function (imgUrl) {
        		$("#divZoom2 img").remove();
        		$("#divZoom2").append("<img class='idZoomImg' src='" + imgUrl + "'/>");
        		$scope.identficationViewId = 24;

        	};

        	$scope.zoomNewCustomerImage = function (key, ts) {
        		$scope.zoomUrl = key;
        		$scope.timestamp0 = ts;
        		$scope.zoomClicked = true;

        	};

        	$scope.closeNewCustomerImageZoom = function () {
        		$scope.zoomClicked = false;
        	};

        	$scope.cropImage = function () {
        		$ceFactory.cropImage($scope.originalImageId, false, 'idcrop');
        		$("#IdAcceptbutton").prop('disabled', false);
        	};

        	$scope.closeZoom = function () {
        		if ($scope.i.Id > 0)
        			$scope.identficationViewId = 20;
        		else
        			$scope.identficationViewId = 21;
        	};

        	$scope.updateCustBen = function (benCustomerId) {
	        	$scope.b.CustomerId = $scope.c.Id;
	        	$scope.b.CustomerIdTo = benCustomerId;

	        	$scope.b.Relationship = "";
	        	$http.post("/Classic/CustomerEnrollment2/AddBeneficiaryCustomerRelationship", $scope.b).success(function (response) {
	        		$scope.getBeneficiaries();
	        	});
	        };

        	$scope.openDuplicateBenModal = function (inputs) {
        		// modal options
        		var options = {
        			width: "450px",
        			height: "260px",
        			showXIcon: false,
        			cancelOnEsc: true,
        			inputs: inputs
        		};

        		AngularThinBox.open("/Classic/Features/CustomerEnrollment/Client/ExistingBeneficiary.html?v=" + Math.random(), options).then(function (response) {

        			if (response) {
        				$scope.updateCustBen(inputs.benCustomerId);
			        }

        		}, function (reason) {
        			// we'll get here if modal called ThinBox.cancel()
        			// or the user clicked the x-icon.
        			$scope.dot = null;
        		});
        	};

        	$scope.saveBeneficiary = function () {

        		//HACK: since we can't use ng-model inside angucomplete
        		$scope.b.City = $('[name="fCity"]').val();

        		if (!$scope.CityConfirmed) {
        			var error = [{ Key: "fCity", Message: Resources.Client.get("AutocompleteInvalid") }];
        			$ceFactory.setErrors(error);
        			return;
        		}

        		if ($scope.c.Id < 1)
        			return;
        		$scope.dot = "...";
        		$ceFactory.clearErrors();

        		if ($scope.b.Country === 'CU') {
        			$scope.benIdNoMatch = false;
			        $scope.b.CustomerId = $scope.c.Id;
        			$http.post("/Classic/CustomerEnrollment2/GetBenefiaryByIdNumber", {
        				beneficiary: $scope.b
			        }).then(function (response) {
        				var isExistingBeneficiary = response.data.CustomerIdTo > 0;
        				if (isExistingBeneficiary) {
					        if (response.data.MatchType === 1) {
					        	$scope.updateCustBen(response.data.CustomerIdTo);
					        }
							else if (response.data.MatchType === 2) {
						        var options = {
							        duplicateBeneficiary: Resources.CustomerEnrollment.get("duplicateBeneficiary"),
							        existingBeneficiary: Resources.CustomerEnrollment.get("existingBeneficiary"),
							        existingBeneficiaryInstructionYes: Resources.CustomerEnrollment.get("existingBeneficiaryInstructionYes"),
							        existingBeneficiaryInstructionNo: Resources.CustomerEnrollment.get("existingBeneficiaryInstructionNo"),
							        yes: Resources.CustomerEnrollment.get("yes"),
							        no: Resources.CustomerEnrollment.get("no"),
							        ok: Resources.Client.get("OCROKButton"),
							        name: response.data.Name,
							        fullAddress: response.data.FullAddress,
									idNumber: response.data.IdNumber,
							        benCustomerId: response.data.CustomerIdTo
						        }
						        $scope.openDuplicateBenModal(options);
						        $scope.savedBen = response.data;
							}
							else if (response.data.MatchType === 3) {
								var options2 = {
									duplicateBeneficiary: Resources.CustomerEnrollment.get("duplicateBeneficiary"),
									existingBeneficiary: Resources.CustomerEnrollment.get("existingBeneficiary"),
									existingBeneficiaryInstructionYes: Resources.CustomerEnrollment.get("existingBeneficiaryInstructionYes"),
									existingBeneficiaryInstructionNo: Resources.CustomerEnrollment.get("existingBeneficiaryInstructionNo"),
									yes: Resources.CustomerEnrollment.get("yes"),
									no: Resources.CustomerEnrollment.get("no"),
									ok: Resources.Client.get("OCROKButton"),
									name: response.data.Name,
									phone: response.data.Phone,
									fullAddress: response.data.FullAddress,
									benCustomerId: response.data.CustomerIdTo,
									existingBeneficiaryRejected: Resources.CustomerEnrollment.get("existingBeneficiaryRejected")
								}
								$scope.openDuplicateBenModal(options2);
								$scope.savedBen = response.data;
					        }
        				}
        				else {
					        $scope.benIdNoMatch = true;
        					$scope.saveBeneficiaryAtLast();
        				}
        			});
        		}
        		else {
        			$scope.saveBeneficiaryAtLast();
        		}
        	};

        	$scope.saveBeneficiaryAtLast = function () {

        		$beneficiaryService.saveBeneficiary($scope.b, $scope.c.Id, $scope.queryString + "&countryTo=" + $scope.b.Country).then(function (data) {
        			$scope.dot = null;
        			if (data != null) {
        				$scope.setMessage(data, true);
        				if (!data.result.IsValid && data.result.IsFailed) {
        					var lowestTab = { index: 100, key: "" };
        					$.each(data.result.Errors, function (i, obj) {
        						var currentTabIndex = $('[ng-model="' + this.Key + '"]:visible').attr("tabindex");
        						if (lowestTab.index > currentTabIndex) {
        							lowestTab.index = currentTabIndex;
        							lowestTab.key = this.Key;
        						}
        					});
        					$('[ng-model="' + lowestTab.key + '"]').focus();
        				}

        				if (data.result.IsValid) {
        					$scope.savedBen = angular.copy($scope.b);
        					$scope.savedBen.BeneId = data.result.Id;

        					$scope.b = null;
        					window.unsaved = false;
        					$scope.getBeneficiaries();
        				}


        				$scope.initialBeneficiary = angular.copy($scope.b);



        			}
        		}, function (e) {
        			$scope.dot = null;
        		});
        	};

        	$scope.getBeneficiaries = function () {

        		if ($scope.addressValidationView)
        			return;

        		$scope.tabId = 3;
        		if (!$scope.b || $scope.b == null || $scope.initialBenLoad) {
        			$scope.b = { City: "" };
        			var qsList = {};
        			var list = $scope.queryString.split('&amp;');
        			for (var i = 0; i < list.length; i++) {
        				var item = list[i].split('=');
        				if (item[0])
        					qsList[item[0]] = item[1];
        			}

        			if ($scope.nceobj.FromAddBeneficiaryLink && qsList["benCountry"] === "CU") {
        				$scope.b.FirstName = decodeURIComponent(qsList["benFirstname"] || "");
        				$scope.b.LastName = decodeURIComponent(qsList["benLastname"] || "");
        				$scope.b.LastName2 = decodeURIComponent(qsList["benLastname2"] || "");
        				$scope.b.Address = decodeURIComponent(qsList["benAddress"] || "");
        				$scope.b.PostalCode = decodeURIComponent(qsList["benPostal"] || "");
        				$scope.b.Country = decodeURIComponent(qsList["benCountry"] || "");
        				setTimeout(function () { $scope.benCountryChanged(); }, 0);
        				$scope.b.State = decodeURIComponent(qsList["benState"] || "");
        				$scope.b.City = decodeURIComponent(qsList["benCity"] || "");
        				$scope.b.TelLine1Code = decodeURIComponent(qsList["benTelCountryCode"] || "");
        				$scope.b.TelLine1AreaCode = decodeURIComponent(qsList["benTelAreaCode"] || "");
        				$scope.b.TelLine1 = decodeURIComponent(qsList["benTelPhoneBody"] || "");
        				$scope.b.TelLine2Code = decodeURIComponent(qsList["benCellCountryCode"] || "");
        				$scope.b.TelLine2AreaCode = decodeURIComponent(qsList["benCellAreaCode"] || "");
        				$scope.b.TelLine2 = decodeURIComponent(qsList["benCellPhoneBody"] || "");
        			}
			        $scope.initialBenLoad = false;
        			$scope.initialBeneficiary = angular.copy($scope.b);
        		}
        		//else
        		//	return;

        		$scope.getBeneficiaryRelationshipList();
        		$ceService.post("/Classic/CustomerEnrollment2/GetBeneficiarySetting", { validationRequestParams: $scope.queryString + "&countryTo=" + $scope.b.Country })
                    .then(function (data) {
                    	$scope.beneficiaryFields = data.result.CustomerFields;
                    	$scope.benCellNoRequired = false;
                    	$scope.benPhoneNoRequired = false;
                    	angular.forEach($scope.beneficiaryFields, function (obj, i) {
                    		if (obj.Name == "fCellNo") {
                    			$scope.benCellNoRequired = obj.Required;
                    		}
                    		if (obj.Name == "fTelNo") {
                    			$scope.benPhoneNoRequired = obj.Required;
                    		}
                    	});

                    	$scope.getCityStateLabel();

                    },
                        function (e) {
                        });

        		$beneficiaryService.getBeneficiaries($scope.c.Id).then(function (data) {
        			$scope.beneficiaries = data;
        			if ($scope.savedBen) {
        				//if beneid matches load that record
        				var benObj = ($filter('filter')($scope.beneficiaries, { BeneId: $scope.savedBen.BeneId }));
        				if ($scope.savedBen.BeneId > 0 && benObj) {
        					$scope.selectBeneficiary(benObj[0]); s
        				}
        				else if ($scope.savedBen.Country === "CU") {
        					//automatically select the last one in the list since that's last one added
        					$scope.selectBeneficiary($scope.beneficiaries[0]);
        				}
        				$scope.savedBen = {};
        			}
        		}, function (e) {

        		});
        	};

        	$scope.getBeneficiaryRelationshipList = function () {
        		if ($scope.relationships != undefined && $scope.relationships.length > 0)
        			return;
        		$http({
        			method: 'GET',
        			url: '/Classic/CustomerEnrollment/GetBenRelationships'
        		}).success(function (data) {
        			$scope.relationships = data;
        		});
        	};

        	$scope.displayOtherBeneficiary = function () {
        		angular.forEach($scope.relationships, function (obj, i) {
        			if (obj.Id == $scope.b.RelationshipTypeId) {
        				$scope.otherBeneficiary = obj.Name == "Other" ? true : false;
        			}
        		});
        	};

        	$scope.selectBeneficiary = function (beneficiary) {
        		var customerId = 0;

        		if ($scope.c !== null && $scope.c.Id !== null)
        			customerId = $scope.c.Id;

        		$scope.feedOrderPage(customerId, beneficiary);
        	};

        	var getValueFromQueryString = function (array, value) {
        		for (var i = 0; i < array.length; i++) {
        			if (array[i].indexOf(value + "=", 0) >= 0) {
        				return array[i].split("=")[1];
        			}
        		}
        		return "";
        	};

        	$scope.feedOrderPage = function (customerId, beneficiary) {

        		if (customerId === 0)
        			return;

        		var qArray = $scope.queryString.split('&amp;');

        		$beneficiaryService.selectBeneficiary(customerId, beneficiary ? beneficiary.BeneId : 0,
					$ceFactory.enableBeneficiary($scope.queryString),
					getValueFromQueryString(qArray, "benCountry"), $scope.Model.NceReq.HasLocation,
					$scope.Model.NceReq.DefaultCorrespondenceCountry).then(function (data) {
						$('#ceLoading').hide();
						var errors = data.validationResult.Errors;
						var close = true;
						if (errors.IsIdentificationInvalid) {
							if ($scope.customerIds == undefined || $scope.customerIds == null || $scope.customerIds.length <= 0) {
								$scope.getCustomerIds();
								close = false;
							}
						}
						if (close) {
							var openerVal = "customer";

							var qlength = qArray.length;
							if (qlength > 0) {
								for (var i = 0; i < qlength; i++) {
									if (qArray[i] === "openerLink=onbehalf") {
										openerVal = "onbehalf";
										break;
									}
									else if (qArray[i] === "openerLink=new-onbehalf") {
										openerVal = "new-onbehalf";
										break;
									}
									else if (qArray[i] === "openerLink=customer") {
										openerVal = "customer";
										break;
									}
								}
							}

							$('#ceLoading').show();
							$scope.customertype = openerVal;
							var currentOrder = JSON.parse(data.CurrentOrder);
							currentOrder.orderData.beneficiary.relationship = beneficiary.Relationship;
							currentOrder.orderData.beneficiary.relationshipTypeId = beneficiary.RelationshipTypeId;
							var errors = data.validationResult.Errors;
							currentOrder.orderValidationData.customerInValid = errors.IsCustomerInvalid;
							currentOrder.orderValidationData.identificationInValid = errors.IsIdentificationInvalid;
							currentOrder.orderValidationData.errorMessage = errors.ErrorMessage;
							currentOrder.showCustomerNotifications = false; // data.validationResult.PromptForNotification;
							currentOrder.orderData.sar.complianceAnswer = $scope.complianceAnswer;
							if ($scope.reqFromTab === 2) // if compliance tab was started first
								currentOrder.saveAndContinue = $scope.ComplianceTabSaveModalAndContinue;


							setTimeout(function () {
								if ($scope.nceobj.IsAngularThinBox) {
									AngularThinBox.close(currentOrder);
								} else {
									if (parent.CES.CustomerController) {
										parent.CES.CustomerController.modalBoxCallBack(currentOrder, openerVal);
									}
								}
							}, 0);

							$('#ceLoading').hide();
							$scope.savingId = false;
						}

						$ceFactory.closeTooltip();
					}, function (e) {
						$('#ceLoading').hide();
					});
        	};
        	//End beneficiay
        	$scope.setMessage = function (data, nofocus) {
        		$ceFactory.clearErrors();
        		$scope.isValid = data.result.IsValid;
        		$scope.closeMessage();
        		if (!data.result.IsValid) {
        			if (data.result.IsFailed) {
        				$ceFactory.setErrors(data.result.Errors, nofocus);
        			}
        		}
        	};
        	$scope.closeMessage = function () {
        		if ($scope.message != null) {
        			setTimeout(function () {
        				$scope.$apply($scope.message = null);
        			}, 4000);
        		}
        	};

        	$scope.changeProxy = function (val, $event) {
        		return $ceFactory.changeProxy(val, $event, $scope.Model.DateFormat);
        	};

        	$scope.appendSeperator = function (val, $event) {

        		if (isNaN(parseInt($event.char)))
        			return val;

        		if (val.length > 2 && val.substr(2, 1) != "/" && val.substr(1, 1) != "/") {
        			val = val.substr(0, 2) + "/" + val.substr(2, val.length);
        		}

        		if (val.length > 5 && val.substr(5, 1) != "/" && val.substr(4, 1) != "/" && val.substr(3, 1) != "/") {
        			val = val.substr(0, 5) + "/" + val.substr(5, val.length);
        		}
        		if (val.length == 2 || val.length == 5)
        			return val + "/";
        		return val;
        	};

        	$scope.togglePhoneType = function (which) {
        		if (which == 1) {
        			$scope.c.IsLine2Mobile = $scope.c.IsLine1Mobile;
        		} else {
        			$scope.c.IsLine1Mobile = $scope.c.IsLine2Mobile;
        		}
        	};

        	$scope.toggleNoTaxIdLabel = function () {
        		$scope.c.TaxId = !$scope.c.NoTaxId ? $scope.Model.NoTaxIdLabel : "";
        	};

        	$scope.sourceOfFundTypeChanged = function () {
        		$scope.c.SourceOfFund = $scope.c.SourceOfFundId == 999 ? "" : $("#SourceOfFunds option:selected").text();
        	};


        	$scope.occupationTypeChanged = function () {
        		$scope.c.Occupation = $scope.c.OccupationId == 67 ? "" : $("#Occupations option:selected").text();
        	};

        	$scope.addPhone = function () {
        		$scope.toggleAddPhone = false;
        		$scope.c.IsLine2Mobile = !$scope.c.IsLine1Mobile;
        	}

        	$scope.showTaxIdField = function () {
        		$scope.c.AllowTaxIdChange = false;
        		$scope.disableTaxId = false;
        		//$scope.c.TaxId = "";
        		$scope.c.EditTaxId = false;
        	}

        	$scope.benCountryChanged = function () {
        		$scope.getBeneficiaries();
        		$scope.getIdTypes($scope.b.Country);
        		$scope.getStates($scope.b.Country, 'beneStates');
        		$scope.setBenPhoneCodes();
        		$scope.CityConfirmed = $scope.b.Country !== 'CU';

        		//HACK: can't figure out how to apply style to an input inside angucomplete directive 
        		if ($scope.beneficiaryFields)
        			$('#City input').toggleClass("i-req", ($filter('filter')($scope.beneficiaryFields, { Name: 'fCity', Required: true })).length > 0);

		        $('#City input').attr('tabindex', '10');
	        }

        	$scope.benStateChanged = function () {
        		//if ($scope.b.Country === 'CU') {
        		//	$http.post("/Classic/CustomerEnrollment2/GetCityByStateCountryAbbrev", { country: $scope.b.Country, state: $scope.b.State }).then(function (response) {
        		//		$scope.b.BenCities = response;
        		//	});
        		//}
        	}

        	$scope.getCityStateLabel = function () {
        		var cityStateService = $ceService.get("/Classic/CustomerEnrollment2/StateCityLabels",
				{
					'benCountry': $scope.b.Country
				});
        		cityStateService.then(function (response) {
        			$scope.newStateLabel = response.stateLabel;
        			$scope.newCityLabel = response.cityLabel;
        		},
				function (e) {
				});
        	}

        	$scope.cityInputSelected = function (selectedCityObject) {
        		if (selectedCityObject) {
        			$scope.b.City = selectedCityObject.originalObject.cityName;
        			$scope.CityConfirmed = true;
        		}
        	}

        	$scope.cityInputChanged = function (cityInputValue) {
        		if (!$scope.b) {
        			$scope.b = { City: '' };
        		}
        		$scope.b.City = cityInputValue;

        		if ($scope.b.Country === 'CU')
        			$scope.CityConfirmed = false;
        	}

        	$scope.cityAutoCompleteRequestFormatter = function () {
        		return {
        			country: $scope.b.Country || '',
        			state: $scope.b.State || '',
        			citytext: $scope.b.City || ''
        		};
        	};
        }
     ]);
