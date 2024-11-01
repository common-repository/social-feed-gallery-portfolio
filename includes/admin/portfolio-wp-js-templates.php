<script type="text/html" id="tmpl-portfolio-image">
    <div class="portfolio-wp-single-image-content {{data.orientation}}" <# if ( data.full != '' ) { #> style="background-image:url({{ data.thumbnail }})" <# } #> >
        <?php do_action( 'portfolio_admin_gallery_image_start' ) ?>
        <# if ( data.thumbnail != '' ) { #>
            <img src="{{ data.thumbnail }}">
        <# } #>
        <div class="actions">
            <?php do_action( 'portfolio_admin_gallery_image_before_actions' ) ?>
            <a href="#" class="portfolio-wp-edit-image" title="<?php esc_attr_e( 'Edit Image', igp_wp ) ?>"><span class="dashicons dashicons-edit"></span></a>
            <?php do_action( 'portfolio_admin_gallery_image_after_actions' ) ?>
            <a href="#" class="portfolio-wp-delete-image" title="<?php esc_attr_e( 'Delete Image', igp_wp ) ?>"><span class="dashicons dashicons-trash"></span></a>
        </div>
        <div class="segrip ui-resizable-handle ui-resizable-se"></div>
        <?php do_action( 'portfolio_admin_gallery_image_end' ) ?>
    </div>
</script>

<script type="text/html" id="tmpl-portfolio-wp-image-editor">
    <div class="edit-media-header">
        <button class="left dashicons"><span class="screen-reader-text"><?php esc_html_e( 'Edit previous media item', igp_wp ); ?></span></button>
        <button class="right dashicons"><span class="screen-reader-text"><?php esc_html_e( 'Edit next media item', igp_wp ); ?></span></button>
    </div>
    <div class="media-frame-title">
        <h1><?php esc_html_e( 'Edit Metadata', igp_wp ); ?></h1>
    </div>
    <div class="media-frame-content">
        <div class="attachment-details save-ready">
            <!-- Left -->
            <div class="attachment-media-view portrait">
                <div class="thumbnail thumbnail-image">
                    <img class="details-image" src="{{ data.full }}" draggable="false" />
                </div>
            </div>
            
            <!-- Right -->
            <div class="attachment-info">
                <!-- Settings -->
                <div class="settings">
                    <!-- Attachment ID -->
                    <input type="hidden" name="id" value="{{ data.id }}" />
                    
                    <!-- Image Title -->
                    <label class="setting">
                        <span class="name"><?php esc_html_e( 'Title', igp_wp ); ?></span>
                        <input type="text" name="title" value="{{ data.title }}" />
                        <div class="description">
                            <?php esc_html_e( 'Image titles can take any type of HTML.', igp_wp ); ?>
                        </div>
                    </label>
                  
                    
                    <!-- Caption Text -->
                    <label class="setting">
                        <span class="name"><?php esc_html_e( 'Caption Text', igp_wp ); ?></span>
                        <textarea name="description">{{ data.description }}</textarea>
                        <div class="description">
                        </div>
                    </label>

                                        
                    <!-- Link -->
                    <div class="setting">
                        <label class="">
                            <span class="name"><?php esc_html_e( 'URL', igp_wp ); ?></span>
                            <input type="text" name="link" value="{{ data.link }}" />
                            <span class="description">
                                <?php esc_html_e( 'Enter a hyperlink if you wish to link this image to somewhere other than its attachment page. In order to use it you will need to select attachment page on Lightbox & Links setting under General.', igp_wp ); ?>
                            </span>
                        </label>
                        <label>
                        <span class="description">
                            <input type="checkbox" name="target" value="1"<# if ( data.target == '1' ) { #> checked <# } #> />
                            <span><?php esc_html_e( 'Opens your image links in a new browser window / tab.', igp_wp ); ?></span>
                        </span>
                        </label>
                    </div>
                    

                    <!-- Addons can populate the UI here -->
                    <div class="portfolio-wp-addons"></div>
                </div>
                <!-- /.settings -->     
               
                <!-- Actions -->
                <div class="actions">
                    <a href="#" class="portfolio-wp-gallery-meta-submit button media-button button-large button-primary media-button-insert" title="<?php esc_attr_e( 'Save Metadata', igp_wp ); ?>">
                        <?php esc_html_e( 'Save', igp_wp ); ?>
                    </a>
                    <a href="#" class="portfolio-wp-gallery-meta-submit-close button media-button button-large media-button-insert" title="<?php esc_attr_e( 'Save & Close', igp_wp ); ?>">
                        <?php esc_html_e( 'Save & Close', igp_wp ); ?>
                    </a>

                    <!-- Save Spinner -->
                    <span class="settings-save-status">
                        <span class="spinner"></span>
                        <span class="saved"><?php esc_html_e( 'Saved.', igp_wp ); ?></span>
                    </span>
                </div>
                <!-- /.actions -->
            </div>
        </div>
    </div>
</script>