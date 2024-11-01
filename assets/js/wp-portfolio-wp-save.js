wp.PortfolioWP = 'undefined' === typeof( wp.PortfolioWP ) ? {} : wp.PortfolioWP;

(function( $, PortfolioWP ){

    var PortfolioWPSaveImages = {
        updateInterval: false,

        checkSave: function() {
            var self = this;

            $('#publishing-action .spinner').addClass( 'is-active' );
            $('#publishing-action #publish').attr( 'disabled', 'disabled' );

            if ( ! self.updateInterval ) {
                self.updateInterval = setInterval( $.proxy( self.saveImages, self), 1000);
            }else{
                clearInterval( self.updateInterval );
                self.updateInterval = setInterval( $.proxy( self.saveImages, self), 1000);
            }
        },

    	saveImages: function( callback = false ) {
            var images = [],
                self = this,
                ajaxData;

            clearInterval( self.updateInterval );

            wp.PortfolioWP.Items.each( function( item ) {
                var attributes = item.getAttributes();
                images[ attributes['index'] ] = attributes;
            });

            ajaxData = { '_wpnonce' : PortfolioWPHelper['_wpnonce'], 'action' : 'portfolio_save_images', gallery : PortfolioWPHelper['id'] };
            ajaxData['images'] = JSON.stringify( images );

            $.ajax({
                method: 'POST',
                url: PortfolioWPHelper['ajax_url'],
                data: ajaxData,
                dataType: 'json',
            }).done(function( msg ) {
                $('#publishing-action .spinner').removeClass( 'is-active' );
                $('#publishing-action #publish').removeAttr( 'disabled' );

                if( typeof callback === "function" ) {
                    callback();
                }
            });
        },

        saveImage: function( id, callback = false ) {

            var image = wp.PortfolioWP.Items.get( id ),
            	json  = image.getAttributes();

            $('#publishing-action .spinner').addClass( 'is-active' );
            $('#publishing-action #publish').attr( 'disabled', 'disabled' );

            ajaxData = { '_wpnonce': PortfolioWPHelper['_wpnonce'], 'action': 'portfolio_save_image', 'gallery': PortfolioWPHelper['id'] };
            ajaxData['image'] = JSON.stringify( json );

            $.ajax({
                method: 'POST',
                url: PortfolioWPHelper['ajax_url'],
                data: ajaxData,
                dataType: 'json',
            }).done(function( msg ) {
                $('#publishing-action .spinner').removeClass( 'is-active' );
                $('#publishing-action #publish').removeAttr( 'disabled' );

                if( typeof callback === "function" ) {
                    callback();
                }
            });
        }
    }

    PortfolioWP.Save = PortfolioWPSaveImages;

}( jQuery, wp.PortfolioWP ))
