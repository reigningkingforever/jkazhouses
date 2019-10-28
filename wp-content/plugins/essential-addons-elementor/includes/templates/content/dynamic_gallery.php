<?php
    $classes = [];
    /**
     * Collect all category of a post
     * @var array
     */
    $categories = get_the_category( get_the_ID() );
    if( is_array( $categories ) ) {
        foreach ($categories as $category) {
            $classes[] = $category->slug;
        }
    }
    /**
     * Collect all tags of a post
     * @var array
     */
    $posttags = get_the_tags();
    if ($posttags) {
        foreach($posttags as $tag) {
            $classes[] = $tag->slug;
        }
    }

    $product_cats = get_the_terms( get_the_ID(), 'product_cat' );
    if ($product_cats) {
        foreach($product_cats as $cat) {
            $classes[] = $cat->slug;
        }
    }
    

    /* ----------------------------------- */
    /* 'eael-hoverer'
    /* ----------------------------------- */
    if( $post_args['eael_fg_grid_style'] == 'eael-hoverer' ) :
        $gallery_image = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
        if( ! empty($gallery_image) ) :
        ?>
        <div class="dynamic-gallery-item <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
            <div class="dynamic-gallery-item-inner">
                <div class="dynamic-gallery-thumbnail">
                    <img src="<?php echo esc_url($gallery_image); ?>" alt="<?php echo esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)); ?>">

                    <?php if( 'eael-none' !== $post_args['eael_fg_grid_hover_style'] ) : ?>
                    <div class="caption <?php echo esc_attr( $post_args['eael_fg_grid_hover_style'] ); ?> ">
                        <?php if( 'true' == $post_args['eael_fg_show_popup'] && ! empty($post_args['eael_section_fg_zoom_icon']) ) : ?>
                            <?php if( 'media' == $post_args['eael_fg_show_popup_styles'] ) : ?>
                                <a href="<?php echo esc_url(get_the_post_thumbnail_url( get_the_ID(), 'full' )); ?>" class="popup-media eael-magnific-link">
                                </a>
                            <?php elseif('buttons' == $post_args['eael_fg_show_popup_styles']) : ?>
                                <div class="buttons">
                                    <?php if( ! empty($post_args['eael_section_fg_zoom_icon']) ) : ?>
                                        <a href="<?php echo esc_url(get_the_post_thumbnail_url( get_the_ID(), 'full' )); ?>" class="eael-magnific-link"><i class="<?php echo esc_attr( $post_args['eael_section_fg_zoom_icon'] ); ?>"></i></a>
                                    <?php endif; ?>
                                    <?php if( ! empty($post_args['eael_section_fg_link_icon']) ) : ?>
                                        <a href="<?php echo esc_url(get_permalink(get_the_ID())); ?>"><i class="<?php echo esc_attr( $post_args['eael_section_fg_link_icon'] ); ?>"></i></a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <?php
        endif;
    endif;


    /* ----------------------------------- */
    /* 'eael-cards'
    /* ----------------------------------- */
    if( $post_args['eael_fg_grid_style'] == 'eael-cards' ) :
        $gallery_image = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
        if( ! empty($gallery_image) ) :
        ?>
        <div class="dynamic-gallery-item <?php echo esc_attr( implode( ' ', $classes ) ) ?>">
            <div class="dynamic-gallery-item-inner">
                <div class="dynamic-gallery-thumbnail">
                    <img src="<?php echo esc_url(get_the_post_thumbnail_url( get_the_ID(), 'medium' )); ?>" alt="<?php echo esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)); ?>">
                    <?php if( 'media' == $post_args['eael_fg_show_popup_styles'] && 'eael-none' == $post_args['eael_fg_grid_hover_style']) { ?>
                        <a href="<?php echo esc_url(get_the_post_thumbnail_url( get_the_ID(), 'full' )); ?>" class="popup-only-media eael-magnific-link">
                        </a>
                    <?php } ?>

                    <?php if( 'eael-none' !== $post_args['eael_fg_grid_hover_style'] ) : ?>
                    <?php if('media' == $post_args['eael_fg_show_popup_styles']) : ?>
                        <div class="caption media-only-caption"><?php else : ?><div class="caption <?php echo esc_attr( $post_args['eael_fg_grid_hover_style'] ); ?> "><?php endif; ?>
                        <?php if( 'true' == $post_args['eael_fg_show_popup'] ) : ?>
                            <?php if( 'media' == $post_args['eael_fg_show_popup_styles'] && ! empty($post_args['eael_section_fg_zoom_icon']) ) : ?>
                                <a href="<?php echo esc_url(get_the_post_thumbnail_url( get_the_ID(), 'full' )); ?>" class="popup-media eael-magnific-link">
                                </a>
                            <?php elseif('buttons' == $post_args['eael_fg_show_popup_styles']) : ?>
                                <div class="buttons">
                                    <?php if( ! empty($post_args['eael_section_fg_zoom_icon']) ) : ?>
                                        <a href="<?php echo esc_url(get_the_post_thumbnail_url( get_the_ID(), 'full' )); ?>" class="eael-magnific-link"><i class="<?php echo esc_attr( $post_args['eael_section_fg_zoom_icon'] ); ?>"></i></a>
                                    <?php endif; ?>
                                    <?php if( ! empty($post_args['eael_section_fg_link_icon']) ) : ?>
                                        <a href="<?php echo esc_url(get_permalink(get_the_ID())); ?>"><i class="<?php echo esc_attr( $post_args['eael_section_fg_link_icon'] ); ?>"></i></a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="item-content">
                    <?php echo the_title('<h2 class="title"><a href="'.get_permalink().'">', '</a></h2>'); ?>
                    <?php if( ! empty($this->eael_get_excerpt_by_id( get_the_ID(), $post_args['eael_post_excerpt'] )) ) : ?>
                        <p><?php echo $this->eael_get_excerpt_by_id( get_the_ID(), $post_args['eael_post_excerpt'] );?></p>
                    <?php endif; ?>

                    <?php if( ('buttons' == $post_args['eael_fg_show_popup_styles']) && ('eael-none' == $post_args['eael_fg_grid_hover_style']) ) : ?>
                        <div class="card-buttons">
                            <?php if( ! empty($post_args['eael_section_fg_zoom_icon']) ) : ?>
                                <a href="<?php echo esc_url(get_the_post_thumbnail_url( get_the_ID(), 'full' )); ?>" class="eael-magnific-link"><i class="<?php echo esc_attr( $post_args['eael_section_fg_zoom_icon'] ); ?>"></i></a>
                            <?php endif; ?>
                            <?php if( ! empty($post_args['eael_section_fg_link_icon']) ) : ?>
                                <a href="<?php echo esc_url(get_permalink(get_the_ID())); ?>"><i class="<?php echo esc_attr( $post_args['eael_section_fg_link_icon'] ); ?>"></i></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        endif;
    endif;