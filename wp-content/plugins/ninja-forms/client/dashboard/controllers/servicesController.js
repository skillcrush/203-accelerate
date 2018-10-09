define([ 'models/serviceCollection' ], function( ServiceCollection ) {
	var controller = Marionette.Object.extend( {
		initialize: function() {
			this.services = new ServiceCollection();

			nfRadio.channel( 'dashboard' ).reply( 'install:service', this.installService, this );
      nfRadio.channel( 'dashboard' ).reply( 'get:services', this.getServices, this );
      this.fetchServices();
		},

		getServices: function() {
			return this.services;
		},

		/*
		 * Fetch services, with an optional callback function.
		 */
		fetchServices: function( callback ) {
			this.services.fetch({
				success: function( model ){
						if( callback ) callback( model );
						nfRadio.channel( 'dashboard' ).trigger( 'fetch:services' );
				}
			});
		},

		/*
		 * Request the remote install of the service's corresponding plugin.
		 */
		installService: function( serviceModel ) {
			var that = this;

			if ( ! ( serviceModel instanceof Backbone.Model ) ) {
				var serviceModel = this.services.find( function( model ) {
					return serviceModel == model.get( 'slug' );
				});
			}

			serviceModel.set( 'is_installing', true );

			var slug = serviceModel.get( 'slug' );
			var installPath = serviceModel.get( 'installPath' );

			// Request to Install the service plugin.
			jQuery.post( ajaxurl, { action: 'nf_services_install', plugin: slug, install_path: installPath }, function( response ){
				that.fetchServices(function(){
					nfRadio.channel( 'dashboard' ).request( 'install:service:' + slug );
				});
			} );
		}
	});

	return controller;
} );
