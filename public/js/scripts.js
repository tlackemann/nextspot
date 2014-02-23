/**
 * Nextspot JS
 * @author Thomas Lackemann
 * @copywrite 2014
 */
(function() {
	var nextspot = function() {

		/**
		 * Reference to self for anonymous functions
		 */
		var self = this;

		/**
		 * URL
		 */
		this.url = null,

		/**
		 * Base path
		 */
		this.basePath = null,

		/**
		 * Current latitude
		 */
		this.latitude = null,

		/**
		 * Current longitude
		 */
		this.longitude = null,

		/**
		 * Current address
		 */
		this.address = null,

		/**
		 * Cache keys for various storage
		 */
		this._cacheKey = {
			'address': 'ns_user_address',
			'latlng': 'ns_latlng',
			'expire': 'ns_global_expire',
		},

		/**
		 * Start the application
		 */
		this.init = function() {
			this.setUrl();
			this.getLocation();
			this.initClickListener();
			if (document.getElementById('splash-bg') !== undefined) {
				// this.loadSplashBackground(false);
			}
		},

		/**
		 * Parses the current URL and sets it for the application
		 * @return void
		 */
		this.setUrl = function(basePath) {
			var pathArray = window.location.href.split( '/' );
			var protocol = pathArray[0];
			var host = pathArray[2];
			this.url = protocol + '//' + host;
			if (this.basePath) {
				this.url = this.url + this.basePath;
			}
			
		},

		this.setBasePath = function(basePath) {
			if (basePath !== undefined) {
				this.basePath = basePath;
				this.url = this.url + this.basePath;
			}
		},

		/**
		 * Start the click listener
		 * @return void
		 */
		this.initClickListener = function() {
			$('#like').on('click', function() {
				$.ajax(self.url + '/likes/index?id=' + $(this).attr('data-id'))
					.success(function(data) {
						var json = JSON.parse(data);
						if (json.success) {
							// flash success
							self.flashMessage(json.message);
							// hide the like/dislike button
							$('#like').text('Thanks!').attr('disabled', 'disabled');
							$('#dislike').hide();

							setTimeout(function() {
								window.location = self.url;
							}, 5000);
						} else {
							// flash error
							self.flashMessage(json.message, 'error');
							
						}
					})
					.error(function(data) {

					});
			});
			$('#dislike').on('click', function() {
				$.ajax(self.url + '/likes/dislike?id=' + $(this).attr('data-id'))
					.success(function(data) {
						var json = JSON.parse(data);
						if (json.success) {
							// hide the like/dislike button
							$('#like').hide();
							$('#dislike').text('Searching...').attr('disabled', 'disabled');

							setTimeout(function() {
								location.reload();
							}, 1000);
						} else {
							// flash error
							self.flashMessage(json.message, 'error');
						}
					})
					.error(function(data) {

					});
				
			})
		},

		/**
		 * Flashes a message
		 * @param string message
		 * @param string type
		 * @param int timeout
		 */
		this.flashMessage = function(message, type, timeout) {
			if (type == undefined) {
				type = 'success';
			}
			if (timeout == undefined) {
				timeout = 5000;
			}
			var messageClass = null;
			switch (type) {
				case 'success' :
					messageClass = 'alert-success';
					break;
				case 'error' :
					messageClass = 'alert-error';
					break;
			}
			$('#alert')
				.hide()
				.removeClass('hide')
				.addClass(messageClass)
				.text(message)
				.fadeIn('fast');

			if (timeout) {
				setTimeout(function() {
					$('#alert').fadeOut();
				}, timeout);
			}

		},

		/**
		 * Get the current location of the user
		 * @return void
		 */
		this.getLocation = function() {
			if (!this.address && !this.getCache(this._cacheKey['address'])) {
				navigator.geolocation.getCurrentPosition(function(location) {
					self.latitude = location.coords.latitude;
				    self.longitude = location.coords.longitude;

				    // Now that we have a lon/lat, hit google to tell us the city
				    $.ajax('http://maps.googleapis.com/maps/api/geocode/json'
				    	+ '?latlng=' + self.latitude + ',' + self.longitude
				    	+ '&sensor=false')
				    .success(function(data) {
				    	for (var i in data.results) {
				    		for (var j in data.results[i].address_components) {
				    			if (data.results[i].address_components[j].types[0] !== undefined && data.results[i].address_components[j].types[0] == 'locality') {
				    				self.address = data.results[i].formatted_address;
				    			}
				    		}
				    	}

				    	self.setCache(self._cacheKey['address'], self.address);
				    	self.setCache(self._cacheKey['latlng'], JSON.stringify({'lat': self.latitude, 'lng': self.longitude}));

				    	if (self.address !== null) {
				    		$("#location").text(self.address);
				    	}
				    	if (self.latitude && self.longitude) {
				    		$('a.search').attr('data-latlng', self.latitude + ',' + self.longitude);
				    		// Append criteria to search string
				    		$('a.search').each(function() {
				    			$(this).attr('href', $(this).attr('href')
				    			+ '&lat=' + self.latitude
				    			+ '&lng=' + self.longitude
				    			+ '&address=' + self.address);
				    		});
				    	}

				    }).error(function(data) {
				    	console.log(data);
				    });
				});
			} else {
				this.address = this.getCache(this._cacheKey['address']);
				var latlng = JSON.parse(this.getCache(this._cacheKey['latlng']));
				this.latitude = latlng.lat;
				this.longitude = latlng.lng;
			}

			if (this.address !== null) {
	    		$("#location").text(this.address);
	    	}
	    	if (this.latitude && this.longitude) {
	    		$('a.search').attr('data-latlng', this.latitude + ',' + this.longitude);
	    		// Append criteria to search string
	    		$('a.search').each(function() {
	    			$(this).attr('href', $(this).attr('href')
	    			+ '&lat=' + self.latitude
	    			+ '&lng=' + self.longitude
	    			+ '&address=' + self.address);
	    		});
	    	}

		},

		/**
		 * Set a cache object in localStorage
		 * @param string key
		 * @param string value
		 * @param string expire
		 * @return boolean
		 */
		this.setCache = function(key, value, expire) {
			expire = (expire) ? Date.now() + expire : Date.now() + 900000; // default 15 minutes

			var expireDates = (localStorage.getItem(this._cacheKey['expire'])) ? JSON.parse(localStorage.getItem(this._cacheKey['expire'])) : {};
			localStorage.setItem(key, value);
			expireDates[key] = expire;
			localStorage.setItem(this._cacheKey['expire'], JSON.stringify(expireDates));
			
			return true;
		},

		/**
		 * Gets a cache object from localStorage based on expire time
		 * @param string key
		 * @return string|array|object
		 */
		this.getCache = function(key) {
			var expireDates = (localStorage.getItem(this._cacheKey['expire'])) ? JSON.parse(localStorage.getItem(this._cacheKey['expire'])) : {};
			console.log(expireDates);
			if (localStorage.getItem(key) !== undefined && (expireDates[key] > Date.now() || expireDates[key] == undefined)) {
				return localStorage.getItem(key);
			}
			return null;
		},

		/**
		 * Get the background image from cache or fetch a random if necessary
		 * @return boolean
		 */
		this.loadSplashBackground = function(useCache) {
			if (useCache == undefined) {
				useCache = true;
			}
			if (useCache && this.getCache('background')) {
				var image = this.getCache('background');
				console.log(image);
				$('#splash-bg')
					.hide()
					.attr('src', image)
					.fadeIn('slow');
			} else {
				$.ajax(this.url + '/index/background')
					.success(function(data) {
						var json = JSON.parse(data);
						$('#splash-bg')
							.hide()
							.attr('src', $('#splash-bg').attr('data-url') + '/' + json.image)
							.fadeIn('slow');

						self.setCache('background', $('#splash-bg').attr('data-url') + '/' + json.image);
					})
					.error(function() {

					});
			}
		}
	}

	// Initialize the object
	if (!window.nextspot) {
		window.nextspot = new nextspot();
	}
})();

// var Profile = {
//     check: function (id) {
//         if ($.trim($("#" + id)[0].value) == '') {
//             $("#" + id)[0].focus();
//             $("#" + id + "_alert").show();

//             return false;
//         };

//         return true;
//     },
//     validate: function () {
//         if (SignUp.check("name") == false) {
//             return false;
//         }
//         if (SignUp.check("email") == false) {
//             return false;
//         }
//         $("#profileForm")[0].submit();
//     }
// };

// var SignUp = {
//     check: function (id) {
//         if ($.trim($("#" + id)[0].value) == '') {
//             $("#" + id)[0].focus();
//             $("#" + id + "_alert").show();

//             return false;
//         };

//         return true;
//     },
//     validate: function () {
//         if (SignUp.check("name") == false) {
//             return false;
//         }
//         if (SignUp.check("username") == false) {
//             return false;
//         }
//         if (SignUp.check("email") == false) {
//             return false;
//         }
//         if (SignUp.check("password") == false) {
//             return false;
//         }
//         if ($("#password")[0].value != $("#repeatPassword")[0].value) {
//             $("#repeatPassword")[0].focus();
//             $("#repeatPassword_alert").show();

//             return false;
//         }
//         $("#registerForm")[0].submit();
//     }
// }

$(document).ready(function () {
	window.nextspot.init();
});
