<?php 
/**
 * Featured Post Markup
 */
// $isPrinted = false;
if( $post_args['eael_post_list_featured_area'] == 'yes' ) : 
    if( isset( $post_args['featured_posts'] ) && ! empty( $post_args['featured_posts'] ) && $feature_counter == 0 ) : 
        $feature_counter++;
        $feature_post = get_post( intval( $post_args['featured_posts'] ) );
        setup_postdata( $feature_post );
?>
<div class="eael-post-list-featured-wrap">
    <div class="eael-post-list-featured-inner" style="background-image: url('<?php echo esc_url(wp_get_attachment_image_url(get_post_thumbnail_id(), 'full')); ?>')">
        <div class="featured-content">
            <?php if( $post_args['eael_post_list_featured_meta'] === 'yes' ) : ?>
            <div class="meta">
                <span>
                    <i class="fas fa-user"></i>
                    <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta ( 'user_nicename' ) ); ?>">
                    <?php the_author(); ?></a>
                </span>
                <span><i class="fas fa-calendar"></i> <?php echo get_the_date(); ?></span>
            </div>
            <?php endif; ?>
            <?php if( $post_args['eael_post_list_featured_title'] === 'yes' ) : ?>
                <h2 class="eael-post-list-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <?php endif; ?>
            <?php if( $post_args['eael_post_list_featured_excerpt'] === 'yes' ) : ?>
            <p><?php echo $this->eael_get_excerpt_by_id( get_the_ID(), $post_args['eael_post_list_featured_excerpt_length'] ); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
    $isPrinted = true;
    endif; // in_array( get_the_ID(), $post_args['featured_posts'] ) && $feature_counter == 0
endif; // $post_args['eael_post_list_featured_area'] == 'yes'

/**
 * Normal Post Markup
 */
if( ! $isPrinted ) :

    echo $iterator == 0 ? '<div class="eael-post-list-posts-wrap">' : ''; 
?>
<div class="eael-post-list-post<?php if( empty( wp_get_attachment_image_url(get_post_thumbnail_id() ) ) ) : ?> eael-empty-thumbnail<?php endif; ?>">
    <?php echo isset($post_args['eael_post_list_layout_type']) && $post_args['eael_post_list_layout_type'] == 'advanced' ? '<div class="eael-post-list-post-inner">' : ''; ?>
        <?php if( isset( $post_args['eael_post_list_post_feature_image'] ) && $post_args['eael_post_list_post_feature_image'] === 'yes' ) : ?>
        <div class="eael-post-list-thumbnail<?php if( empty( wp_get_attachment_image_url(get_post_thumbnail_id() ) ) ) : ?> eael-empty-thumbnail<?php endif; ?>"><?php if( !empty( wp_get_attachment_image_url(get_post_thumbnail_id() ) ) ) : ?><img src="<?php echo esc_url(wp_get_attachment_image_url(get_post_thumbnail_id(), 'full')); ?>" alt="<?php echo esc_attr(get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true)); ?>">
        <?php endif; ?><a href="<?php the_permalink(); ?>"></a></div><?php endif; ?>
        <div class="eael-post-list-content">

            <?php if( isset($post_args['eael_post_list_layout_type']) && $post_args['eael_post_list_layout_type'] == 'advanced' && ($iterator == 8) && $post_args['eael_post_list_post_cat'] != '' ) : ?>
                <div class="boxed-meta">
                    <div class="meta-categories">
                        <?php $this->eael_get_cats_list_custom(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if( $post_args['eael_post_list_post_title'] === 'yes' ) : ?>
            <h2 class="eael-post-list-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <?php endif; ?>
            <?php if( isset( $post_args['eael_post_list_post_meta'] ) && $post_args['eael_post_list_post_meta'] === 'yes' ) : ?>
            <div class="meta">
                <span><?php echo get_the_date(); ?></span>
            </div>
            <?php endif; ?>

            <?php if( isset( $post_args['eael_post_list_post_excerpt'] ) && $post_args['eael_post_list_post_excerpt'] === 'yes' ) : ?>
                <?php if($post_args['eael_post_list_layout_type'] == 'default') : ?>
                    <p><?php echo $this->eael_get_excerpt_by_id( get_the_ID(), $post_args['eael_post_list_post_excerpt_length'] ); ?></p>
                <?php endif; ?>
                <?php if($post_args['eael_post_list_layout_type'] == 'advanced' && $iterator !== 8) : ?>
                    <p><?php echo $this->eael_get_excerpt_by_id( get_the_ID(), $post_args['eael_post_list_post_excerpt_length'] ); ?></p>
                <?php endif; ?>
            <?php endif; ?>
            

            <?php if( $iterator == 8 && $post_args['eael_post_list_layout_type'] == 'advanced' ) : ?>
            <p><?php echo $this->eael_get_excerpt_by_id( get_the_ID(), 36); ?></p>
            <?php endif; ?>

            <?php if( isset($post_args['eael_post_list_layout_type']) && $post_args['eael_post_list_layout_type'] == 'advanced' ) : ?>
            <div class="boxed-meta">
                <?php if($post_args['eael_post_list_author_meta'] != '') : ?>
                <div class="author-meta">
                    <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta( 'ID' ))); ?>" class="author-photo">
                        <?php echo get_avatar(get_the_author_meta( 'ID' ), 100, false, get_the_title() . '-author' ); ?>
                    </a>
                    <div class="author-info">
                        <h5><?php the_author_posts_link(); ?></h5>
                        <a href="<?php echo get_day_link(get_post_time('Y'), get_post_time('m'), get_post_time('j'));  ?>"><p><?php echo get_the_date('d.m.y'); ?></p></a>
                    </div>
                </div>
                <?php endif; ?>

                <?php if( $iterator != 8) : ?>
                    <?php if($post_args['eael_post_list_post_cat'] != '') : ?>
                    <div class="meta-categories">
                        <?php $this->eael_get_cats_list_custom(); ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                
            </div>
            <?php endif; ?>

        </div>
        <?php echo isset($post_args['eael_post_list_layout_type']) && $post_args['eael_post_list_layout_type'] == 'advanced' ? '</div>' : ''; ?>
</div>
<?php   
    echo ( $iterator == ( $posts->found_posts - 1 ) ) == true ? '</div>' : '';
    $iterator++;
endif; //  ! $isPrinted 