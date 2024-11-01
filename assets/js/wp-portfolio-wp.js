wp.PortfolioWP = 'undefined' === typeof( wp.PortfolioWP ) ? {} : wp.PortfolioWP;
wp.PortfolioWP.modalChildViews = 'undefined' === typeof( wp.PortfolioWP.modalChildViews ) ? [] : wp.PortfolioWP.modalChildViews;
wp.PortfolioWP.previewer = 'undefined' === typeof( wp.PortfolioWP.previewer ) ? {} : wp.PortfolioWP.previewer;
wp.PortfolioWP.modal = 'undefined' === typeof( wp.PortfolioWP.modal ) ? {} : wp.PortfolioWP.modal;
wp.PortfolioWP.items = 'undefined' === typeof( wp.PortfolioWP.items ) ? {} : wp.PortfolioWP.items;
wp.PortfolioWP.upload = 'undefined' === typeof( wp.PortfolioWP.upload ) ? {} : wp.PortfolioWP.upload;

jQuery( document ).ready( function( $ ){

	// Here we will have all gallery's items.
	wp.PortfolioWP.Items = new wp.PortfolioWP.items['collection']();
	
	// Settings related objects.
	wp.PortfolioWP.Settings = new wp.PortfolioWP.settings['model']( PortfolioWPHelper.settings );

	// PortfolioWP conditions
	wp.PortfolioWP.Conditions = new PortfolioWPGalleryConditions();

	// Initiate PortfolioWP Resizer
	if ( 'undefined' == typeof wp.PortfolioWP.Resizer ) {
		wp.PortfolioWP.Resizer = new wp.PortfolioWP.previewer['resizer']();
	}
	
	// Initiate Gallery View
	wp.PortfolioWP.GalleryView = new wp.PortfolioWP.previewer['view']({
		'el' : $( '#portfolio-wp-uploader-container' ),
	});

	// PortfolioWP edit item modal.
	wp.PortfolioWP.EditModal = new wp.PortfolioWP.modal['model']({
		'childViews' : wp.PortfolioWP.modalChildViews
	});


	// Here we will add items for the gallery to collection.
	if ( 'undefined' !== typeof PortfolioWPHelper.items ) {
		$.each( PortfolioWPHelper.items, function( index, image ){
			var imageModel = new wp.PortfolioWP.items['model']( image );
		});
	}

	// Initiate PortfolioWP Gallery Upload
	// new wp.PortfolioWP.upload['uploadHandler']();  // Comented By DEEPAK

});