class WooGogetAutocomplete  {
	constructor(config) {
	    this.setOptions(config);
		this.initAutocomplete();
	}
	setOptions(config) {
		this.options = {
			reference: null,
			component_form: {},
			formFieldsValue: {},
			autocompleteField: null,
			countryField: null,
			geoFields: {
				lat: null,
				lng: null,
			},
			geoCodeDefault: true,
			autocompleteOptions: {},
		};
		this.autocomplete = null;
		this.place = null;
		this.location = null;

		this.options = {...this.options, ...config}
	}
	initAutocomplete() {
		var self = this;

		self.autocomplete = new google.maps.places.Autocomplete(self.options.autocompleteField, {
			types: ["geocode"],
			...self.options.autocompleteOptions
		});

		if(self.options.autocompleteOptions.country !== undefined) {
			self.autocomplete.setComponentRestrictions({
				country: self.options.autocompleteOptions.country
			})
		}

		google.maps.event.addListener(self.autocomplete, "place_changed", function() {
			//self.autocomplete.setFields(["address_components"]);
			self.place = self.autocomplete.getPlace();

			console.log(self.place, 'place');

			self.fillInAddress();

			setTimeout(function(){
				if ("createEvent" in document) {
				    var evt = document.createEvent("HTMLEvents");
				    evt.initEvent("change", false, true);
				    self.options.autocompleteField.dispatchEvent(evt);
				}
				else
				    self.options.autocompleteField.fireEvent("onchange");
			}, 500);
		});

		self.options.autocompleteField.addEventListener("focus", function() {
			self.setCurrentCountry()
		});

		var s = document.getElementById(this.options.countryField);
		null != s && s.addEventListener("change", function() {
			self.setCurrentCountry()
		});

		if(self.options.geoCodeDefault) {
			var address = self.options.autocompleteField.value;
			self.geoCodeAddress(address);
		}
	}
	geoCodeAddress(address) {
		var self = this;
		console.log(address, 'address');
		if(!address) return;

		var geocoder = new google.maps.Geocoder();
		geocoder.geocode( { 'address': address}, function(results, status) {
		  if (status == google.maps.GeocoderStatus.OK)
		  {
		      if(results[0].geometry.location) {
		      	self.location = results[0].geometry.location;
		      	self.fillLatLng();
		      }
		  } else {
		  	  alert("Geocode was not successful for the following reason: " + status);
		  }
		});
	}
	fillInAddress() {
		var self = this;

		this.clearFormValues();
		let address_components = self.place.address_components;
		for (var s in address_components){
			for (var t in address_components[s].types){
				for (var n in this.options.component_form) {
					if (n == address_components[s].types[t] && this.options.component_form[n] !== undefined) {
						var i = this.options.component_form[n][1];
						address_components[s].hasOwnProperty(i) && (this.options.formFieldsValue[this.options.component_form[n][0]] = address_components[s][i])
					}
				}
			}
		}

		this.fillForm();
	}
	clearFormValues() {
		for (var e in this.options.formFieldsValue) this.options.formFieldsValue[e] = ""
	}
	fillForm() {
		for (var e in this.options.formFieldsValue){
			if (this.options.selectFields[e] !== undefined) {
				this.selectField(e, this.options.formFieldsValue[e]);	
			} 
			else {
				if (null === document.getElementById(e)) continue;
				document.getElementById(e).value = this.options.formFieldsValue[e]
			}
		}

		if(this.place.geometry !== undefined) {
			this.location = this.place.geometry.location;
		}

		if(this.options.addressCallback !== undefined) {
			this.options['addressCallback'](this);
		}

		this.fillLatLng();
	}
	fillLatLng() {
		if(!this.location) {
			return;
		}

		if(this.options.geoFields.lat !== null) {
			document.getElementById(this.options.geoFields.lat).value = this.location.lat();
		}
		if(this.options.geoFields.lng !== null) {
			document.getElementById(this.options.geoFields.lng).value = this.location.lng();
		}

		if(this.options.locationCallback !== undefined) {
			this.options['locationCallback'](this);
		}
	}
	selectField(e, s) {
		var t = document.getElementById(e);

		if(t== null || t.options === undefined)
			return !1;

		t.value = "";
		for (var n = 0; n < t.options.length; n++) {
			if (t.options[n].text == s) {
				t.selectedIndex = n;
				break
			}
		}

		if ("createEvent" in document) {
		    var evt = document.createEvent("HTMLEvents");
		    evt.initEvent("change", false, true);
		    t.dispatchEvent(evt);
		}
		else
		    t.fireEvent("onchange");
	}
	setCurrentCountry() {
		if (null === document.getElementById(this.options.countryField)) e = "";
		else var e = document.getElementById(this.options.countryField).value;

		if(e) {
			this.autocomplete.setComponentRestrictions({
				country: e
			})	
		}
		
	}
};