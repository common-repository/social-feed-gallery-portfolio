wp.PortfolioWP = 'undefined' === typeof( wp.PortfolioWP ) ? {} : wp.PortfolioWP;

var PortfolioWPGalleryConditions = Backbone.Model.extend({

	initialize: function( args ){

		var rows = jQuery('.portfolio-wp-settings-container tr[data-container]');
		var tabs = jQuery('.portfolio-wp-tabs .portfolio-wp-tab');
		this.set( 'rows', rows );
		this.set( 'tabs', tabs );

		this.initEvents();
		this.initValues();

	},

	initEvents: function(){

		this.listenTo( wp.PortfolioWP.Settings, 'change:type', this.changedType );
		this.listenTo( wp.PortfolioWP.Settings, 'change:effect', this.changedEffect );
		this.listenTo( wp.PortfolioWP.Settings, 'change:lightbox', this.changedLightbox );

	},

	initValues: function(){

		this.changedType( false, wp.PortfolioWP.Settings.get( 'type' ) );
		this.changedEffect( false, wp.PortfolioWP.Settings.get( 'effect' ) );
		this.changedLightbox( false, wp.PortfolioWP.Settings.get( 'lightbox' ) );

	},

	changedType: function( settings, value ){
		var rows = this.get( 'rows' ),
			tabs = this.get( 'tabs' );

		if ( 'custom-grid' == value ) {

			// Show Responsive tab
			tabs.filter( '[data-tab="portfolio-wp-responsive"]' ).show();
			
			rows.filter( '[data-container="columns"], [data-container="gutter"]' ).show();
			rows.filter( '[data-container="width"], [data-container="height"], [data-container="margin"], [data-container="randomFactor"], [data-container="shuffle"]' ).hide();

		}else if ( 'creative-gallery' ) {

			// Hide Responsive tab
			tabs.filter( '[data-tab="portfolio-wp-responsive"]' ).hide();

			rows.filter( '[data-container="columns"], [data-container="gutter"]' ).hide();
			rows.filter( '[data-container="width"], [data-container="height"], [data-container="margin"], [data-container="randomFactor"], [data-container="shuffle"]' ).show();

		}

	},

	changedLightbox: function( settings, value ){
		var rows = this.get( 'rows' ),
			tabs = this.get( 'tabs' );

		if ( 'lightbox2' == value ) {
			
			rows.filter( '[data-container="show_navigation"], [data-container="show_navigation_on_mobile"]' ).show();

		}else{

			rows.filter( '[data-container="show_navigation"], [data-container="show_navigation_on_mobile"]' ).hide();

		}

	},

	changedEffect: function( settings, value ){
		var hoverBoxes = jQuery( '.portfolio-wp-effects-preview > div' );

		hoverBoxes.hide();
		hoverBoxes.filter( '.panel-' + value ).show();
	}

});