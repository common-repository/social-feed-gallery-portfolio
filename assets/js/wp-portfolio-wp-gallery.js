wp.PortfolioWP = 'undefined' === typeof( wp.PortfolioWP ) ? {} : wp.PortfolioWP;

(function( $, PortfolioWP ){

    var PortfolioWPGalleryResizer = Backbone.Model.extend({
    	defaults: {
            'columns': 12,
            'gutter': 10,
            'containerSize': false,
            'size': false,
        },

        initialize: function( args ){
            var resizer = this;

            this.set( 'containerSize', jQuery( '#portfolio-wp-uploader-container .portfolio-wp-uploader-inline-content' ).width() );

            // Get options
            this.set( 'gutter', parseInt( wp.PortfolioWP.Settings.get('gutter') ) );

        	// calculate block size.
        	this.generateSize();

            // Listen to column and gutter change
            // this.listenTo( wp.PortfolioWP.Settings, 'change:columns', this.changeColumns );
            this.listenTo( wp.PortfolioWP.Settings, 'change:gutter', this.changeGutter );

            // Listen to window resize
            jQuery( window ).on( 'resize', $.proxy( this.windowResize, this ) );
            new ResizeSensor( jQuery( '#portfolio-wp-uploader-container .portfolio-wp-uploader-inline-content' ), function() {
                resizer.windowResize();
            });
        },
        
        generateSize: function(){
        	var columns = this.get( 'columns' ),
        		gutter = this.get( 'gutter' ),
        		containerWidth = this.get( 'containerSize' ),
        		size;

        	/* 
        	   We will calculate the size ( width and height, because every item is a square ) of an item.
    		   The formula is : from the container size we will subtract gutter * number of columns and then we will dived by number of columns
        	 */
        	size = Math.floor( ( containerWidth - ( gutter * ( columns - 1 ) ) ) / columns );
        	this.set( 'size', size );
        },
        /* 
           Here we will calculate the new size of the item.
           This will be called after resize event, that means the item is resized and we need to check it.
           currentSize is the new size of the item after we resized it.
         */
        calculateSize: function( currentSize ){
        	var size = this.get( 'size' ),
        		columns = Math.round( currentSize / size ),
        		gutter = this.get( 'gutter' ),
                containerColumns = this.get( 'columns' ),
        		correctSize;

            if ( columns > containerColumns ) {
                columns = containerColumns;
            }

        	correctSize = size * columns + ( gutter * ( columns - 1 ) );
        	return correctSize;
        },

        // Get columns from width/height
        getSizeColumns: function( currentSize ){
            var size = this.get( 'size' );
            return Math.round( currentSize / size );
        },

        windowResize: function() {
            var currentSize = this.get( 'containerSize' ),
                newSize = jQuery( '#portfolio-wp-uploader-container .portfolio-wp-uploader-inline-content' ).width();

            // Check if container size have been changed.
            if ( currentSize == newSize ) {
                return;
            }

            // Change Container Width
            this.set( 'containerSize', newSize );

            // Resize Items
            this.resizeItems();

        },

        resizeItems: function(){

            // Generate new sizes.
            this.generateSize();

            if ( 'undefined' != typeof wp.PortfolioWP.Items && wp.PortfolioWP.Items.length > 0 ) {

                // Resize all items when gutter or columns have changed.
                wp.PortfolioWP.Items.each( function( item ){
                    item.resize();
                });

            }

            if ( 'custom-grid' == PortfolioWP.Settings.get( 'type' ) ) {
                // Change packary columnWidth & columnHeight
                wp.PortfolioWP.GalleryView.setPackaryOption( 'columnWidth', this.get( 'size' ) );
                wp.PortfolioWP.GalleryView.setPackaryOption( 'rowHeight', this.get( 'size' ) );

                // Update Grid
                wp.PortfolioWP.GalleryView.setPackaryOption( 'gutter', parseInt( this.get( 'gutter' ) ) );

                // Reset Packary
                wp.PortfolioWP.GalleryView.resetPackary();
            }

        },

        changeColumns: function( model, value ){
            this.set( 'columns', value );

            // Resize all gallery items
            this.resizeItems();

        },

        changeGutter: function( model, value ){
            this.set( 'gutter', parseInt( value ) );

            // Resize all gallery items
            this.resizeItems();

        }

    });

    var PortfolioWPGalleryView = Backbone.View.extend({

    	isSortable : false,
    	isResizeble: false,
        refreshTimeout: false,
        updateIndexTimeout: false,

    	initialize: function( args ) {

    		// This is the container where the gallery items are.
    		this.container = this.$el.find( '.portfolio-wp-uploader-inline-content' );

            // Helper Grid container
            this.helperGridContainer = this.$el.parent().find( '.portfolio-wp-helper-guidelines-container' );
            this.helperGrid = this.$el.find( '#portfolio-wp-grid' );

            // Listen to grid toggle
            this.helperGridContainer.on( 'change', 'input', $.proxy( this.updateSettings, this ) );

    		// Listent when gallery type is changing.
        	this.listenTo( wp.PortfolioWP.Settings, 'change:type', this.checkSettingsType );

        	// Enable current gallery type
        	this.checkGalleryType( wp.PortfolioWP.Settings.get( 'type' ) );

            // Grid
           //this.gridView = new wp.PortfolioWP.previewer['helpergrid']({ 'el' : this.$el.find( '#portfolio-wp-grid' ), 'galleryView' : this });

        },

        updateSettings: function( event ) {
            var value,
                setting = event.target.dataset.setting;

            value = event.target.checked ? 1 : 0;

            wp.PortfolioWP.Settings.set( 'helpergrid', value );

            if ( value ) {
                this.helperGrid.hide();
            }else{
                this.helperGrid.show();
            }

        },

        checkSettingsType: function( model, value ) {
        	this.checkGalleryType( value );
        },

        checkGalleryType: function( type ) {

            if ( 'creative-gallery' == type ) {

            	// If resizeble is enable we will destroy it
            	if ( this.isResizeble ) {
            		this.disableResizeble();
            	}

            	// If sortable is not enabled, we will initialize it.
            	if ( ! this.isSortable ) {
            		this.enableSortable();
            	}

                this.helperGridContainer.find( '.portfolio-wp-helper-guidelines-wrapper' ).hide();
                this.container.removeClass( 'portfolio-wp-custom-grid' ).addClass( 'portfolio-wp-creative-gallery' );

            }else if ( 'custom-grid' == type ) {

            	// If sortable is enable we will destroy it
            	if ( this.isSortable ) {
            		this.disableSortable();
            	}

            	// If resizeble is not enabled, we will initialize it.
            	if ( ! this.isResizeble ) {
            		this.enableResizeble();
            	}

               /* this.helperGridContainer.find( '.portfolio-wp-helper-guidelines-wrapper' ).show();
                if ( ! wp.PortfolioWP.Settings.get( 'helpergrid' ) ) {
                    this.helperGrid.show();
                }*/

                this.container.removeClass( 'portfolio-wp-creative-gallery' ).addClass( 'portfolio-wp-custom-grid' );

            }
        },

        enableSortable: function() {
            var galleryView = this;

        	this.isSortable = true;
        	this.container.sortable( {
    	        items: '.portfolio-wp-single-image',
    	        cursor: 'move',
    	        forcePlaceholderSize: true,
    	        placeholder: 'portfolio-wp-single-image-placeholder',
                stop: function( event, ui ) {
                    var itemsIDs = galleryView.container.sortable( 'toArray' );
                    itemsIDs.forEach( function( itemID, i ) {
                        var id = "#" + itemID;
                        $( id ).trigger( 'PortfolioWP:updateIndex', { 'index': i } );
                    });
                }
    	    } );
        },

        disableSortable: function() {
        	this.isSortable = false;
        	this.container.sortable( 'destroy' );
        },

        enableResizeble: function() {

        	this.isResizeble = true;
            this.$el.addClass( 'portfolio-wp-resizer-enabled' );

            if ( 'undefined' == typeof wp.PortfolioWP.Resizer ) {
                wp.PortfolioWP.Resizer = new wp.PortfolioWP.previewer['resizer']({ 'galleryView': this });
            }

        	this.container.packery({
        		itemSelector: '.portfolio-wp-single-image',
                gutter: parseInt( wp.PortfolioWP.Resizer.get( 'gutter' ) ),
                columnWidth: wp.PortfolioWP.Resizer.get( 'size' ),
                rowHeight: wp.PortfolioWP.Resizer.get( 'size' ),
    		});

            this.container.on( 'layoutComplete', this.updateItemsIndex );
            this.container.on( 'dragItemPositioned', this.updateItemsIndex );
        },

        disableResizeble: function() {
    		this.isResizeble = false;
            this.$el.removeClass( 'portfolio-wp-resizer-enabled' );
            this.container.packery( 'destroy' );
        },

        bindDraggabillyEvents: function( item ){
        	if ( this.isResizeble ) {
        		this.container.packery( 'bindUIDraggableEvents', item );
        	}
        },

        resetPackary: function() {
            var view = this;

            if ( this.refreshTimeout ) {
                clearTimeout( this.refreshTimeout );
            }

            this.refreshTimeout = setTimeout(function () {        
                view.container.packery();
            }, 200);

        },

        updateItemsIndex: function(){

            var container = this;

            if ( this.updateIndexTimeout ) {
                clearTimeout( this.updateIndexTimeout );
            }
            
            this.updateIndexTimeout = setTimeout( function() {
                var items = $(container).packery('getItemElements');
                $( items ).each( function( i, itemElem ) {
                    $( itemElem ).trigger( 'PortfolioWP:updateIndex', { 'index': i } );
                });
            }, 200);

        },

        setPackaryOption: function( option, value ){

            var packaryOptions = this.container.data('packery');
            if ( 'undefined' != typeof packaryOptions ) {
                packaryOptions.options[ option ] = value;
            }

        },

    });

    var PortfolioWPGalleryGrid = Backbone.View.extend({

        containerHeight: 0,
        currentRows: 0,
        updateGridTimeout: false,

        initialize: function( args ) {
            var view = this;

            this.galleryView = args.galleryView;
            if ( 'undefined' == typeof wp.PortfolioWP.Resizer ) {
                wp.PortfolioWP.Resizer = new wp.PortfolioWP.previewer['resizer']({ 'galleryView': this.galleryView });
            }
            
            this.containerHeight = this.galleryView.container.height();

            // Listent when gallery type is changing.
            this.listenTo( wp.PortfolioWP.Settings, 'change:type', this.checkSettingsType );

            // Listent when gallery gutter is changing.
            this.listenTo( wp.PortfolioWP.Settings, 'change:gutter', this.changeGutter );

            // Listen when column width is changing
            this.listenTo( wp.PortfolioWP.Resizer, 'change:size', this.updateGrid );

            // On layout complete
            this.galleryView.container.on( 'layoutComplete', function( event ){
                view.updateGrid();
            });

            // Enable current gallery type
            this.checkGalleryType( wp.PortfolioWP.Settings.get( 'type' ) );

        },

        checkSettingsType: function( model, value ) {
            this.checkGalleryType( value );
        },

        checkGalleryType: function( type ) {
            if ( 'creative-gallery' == type ) {
                this.$el.hide();
            }else if ( 'custom-grid' == type ) {
                if ( ! wp.PortfolioWP.Settings.get( 'helpergrid' ) ) {
                    this.$el.show();
                }

                // Generate grid
                this.generateGrid();
            }
        },

        generateGrid: function() {
            var view = this,
                neededRows = 0,
                columnWidth = wp.PortfolioWP.Resizer.get( 'size' ),
                gutter = wp.PortfolioWP.Resizer.get( 'gutter' ),
                neededItems = 0,
                neededContainerHeight = 0,
                containerHeight = 0,
                minContainerHeight = 0,
                parentHeight = this.$el.parent().height();

            containerHeight = view.$el.height();
            minContainerHeight = ( columnWidth + gutter ) * 3 - gutter;

            if ( containerHeight < minContainerHeight ) {
                containerHeight = minContainerHeight;
            }

            neededRows = Math.round( ( containerHeight + gutter ) / ( columnWidth + gutter ) ) + 1;
            neededContainerHeight = ( neededRows ) * ( columnWidth + gutter ) - gutter;

            while( containerHeight < neededContainerHeight ) {
                neededContainerHeight = neededContainerHeight - ( columnWidth + gutter );
            }

            this.$el.height( neededContainerHeight );
            $( '#portfolio-wp-uploader-container' ).css( 'min-height', minContainerHeight + 'px' );
            if ( neededContainerHeight > parentHeight ) {
                this.$el.parent().height( neededContainerHeight );
            }

            if ( neededRows > this.currentRows ) {

                neededItems = ( neededRows - this.currentRows ) * 12;
                this.currentRows = neededRows;

                for ( var i = 1; i <= neededItems; i++ ) {
                    this.$el.append( '<div class="portfolio-wp-grid-item"></div>' );
                }

                this.$el.find( '.portfolio-wp-grid-item' ).css( { 'width': columnWidth, 'height' : columnWidth, 'margin-right' : gutter, 'margin-bottom' : gutter } );

            }

        },

        updateGrid: function() {
            var view = this,
                neededRows = 0,
                columnWidth = wp.PortfolioWP.Resizer.get( 'size' ),
                gutter = wp.PortfolioWP.Resizer.get( 'gutter' ),
                neededItems = 0,
                neededContainerHeight = 0,
                containerHeight = 0,
                packery = view.galleryView.container.data('packery'),
                parentHeight = this.$el.parent().height();

            if ( 'undefined' == typeof packery ) {
                return;
            }

            containerHeight = packery.maxY - packery.gutter;
            minContainerHeight = ( columnWidth + gutter ) * 3 - gutter;

            if ( containerHeight < minContainerHeight ) {
                containerHeight = minContainerHeight;
            }

            neededRows = Math.round( ( containerHeight + gutter ) / ( columnWidth + gutter ) ) + 1;
            neededContainerHeight = ( neededRows ) * ( columnWidth + gutter ) - gutter;

            while( containerHeight < neededContainerHeight ) {
                neededContainerHeight = neededContainerHeight - ( columnWidth + gutter );
            }

            this.$el.height( neededContainerHeight );
            $( '#portfolio-wp-uploader-container' ).css( 'min-height', minContainerHeight + 'px' );
            if ( neededContainerHeight > parentHeight ) {
                this.$el.parent().height( neededContainerHeight );
            }

            neededItems = ( neededRows - this.currentRows ) * 12;
            this.currentRows = neededRows;

            for ( var i = 1; i <= neededItems; i++ ) {
                this.$el.append( '<div class="portfolio-wp-grid-item"></div>' );
            }

            this.$el.find( '.portfolio-wp-grid-item' ).css( { 'width': columnWidth, 'height' : columnWidth, 'margin-right' : gutter, 'margin-bottom' : gutter } );

        },

        changeGutter: function() {
            var view = this,
                neededRows = 0,
                columnWidth = wp.PortfolioWP.Resizer.get( 'size' ),
                gutter = wp.PortfolioWP.Resizer.get( 'gutter' ),
                neededItems = 0,
                neededContainerHeight = 0,
                containerHeight = 0,
                packery = view.galleryView.container.data('packery');

            if ( 'undefined' == typeof packery ) {
                return;
            }

            containerHeight = packery.maxY - packery.gutter;

            if ( containerHeight < 300 ) {
                containerHeight = 300;
            }

            neededRows = Math.round( ( containerHeight + gutter ) / ( columnWidth + gutter ) ) + 1;
            neededContainerHeight = ( neededRows ) * ( columnWidth + gutter ) - gutter;

            while( containerHeight < neededContainerHeight ) {
                neededContainerHeight = neededContainerHeight - ( columnWidth + gutter );
            }

            this.$el.height( neededContainerHeight );

            this.$el.find( '.portfolio-wp-grid-item' ).css( { 'width': columnWidth, 'height' : columnWidth, 'margin-right' : gutter, 'margin-bottom' : gutter } );

        }

    });

    PortfolioWP.previewer = {
        'resizer' : PortfolioWPGalleryResizer,
        'helpergrid' : PortfolioWPGalleryGrid,
        'view' : PortfolioWPGalleryView
    }

}( jQuery, wp.PortfolioWP ))

