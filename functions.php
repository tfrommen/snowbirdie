<?php # -*- coding: utf-8 -*-

/**
 * Renders the footer widgets.
 *
 * @since 1.0.0
 *
 * @return void
 */
function snowbird_display_footer_widgets() {

	$columns = snowbird_maybe_display_footer();
	if ( $columns || has_action( 'snowbirdie.before_footer_widgets' ) || has_action( 'snowbirdie.after_footer_widgets' ) ) {
		?>
		<div class="widget-area">
			<?php
			/**
			 * Fires right before the footer widgets.
			 *
			 * @since 1.0.0
			 */
			do_action( 'snowbirdie.before_footer_widgets' );

			if ( $columns ) : ?>
				<div class="xf__container row clear">
					<?php $classes = snowbird_get_footer_widget_classes(); ?>
					<?php for ( $i = 1; $i <= $columns; $i++ ) : ?>
						<?php if ( is_active_sidebar( "footer-$i" ) ) : ?>
							<div class="<?php echo esc_attr( $classes . ( 1 === $i ? ' first' : '' ) . ( $columns === $i ? ' last' : '' ) ); ?>">
								<?php dynamic_sidebar( "footer-$i" ); ?>
							</div>
						<?php endif; ?>
					<?php endfor; ?>
				</div>
			<?php endif;

			/**
			 * Fires right after the footer widgets.
			 *
			 * @since 1.0.0
			 */
			do_action( 'snowbirdie.after_footer_widgets' );
			?>
		</div>
		<?php
	}
}

/**
 * Returns the author bio.
 *
 * @since 1.0.0
 *
 * @param bool $generate Optional. Generate bio if empty? Defaults to false.
 *
 * @return string Author bio.
 */
function snowbird_get_author_bio( $generate = false ) {
	$bio = get_the_author_meta( 'description', get_post()->post_author );

	if ( $generate && empty( $bio ) ) {
		$post_count = count_user_posts( get_post()->post_author );

		// Translators: 1: author 2: total posts count
		$bio = sprintf(
			esc_html__( '%1$s has been contributed to a whooping %2$s.', 'snowbird' ),
			get_the_author_meta( 'display_name', get_post()->post_author ),
			sprintf( _n( '%d article', '%d articles', number_format_i18n( $post_count ), 'snowbird' ), number_format_i18n( $post_count ) )
		);
	}

	return $bio ? wpautop( $bio ) : '';
}

add_filter( 'snowbird_content_width', function () {

	return 1280;
} );

add_filter( 'snowbird_get_color_schemes', function ( array $color_schemes ) {

	return array_merge( [
		'snowbirdie' => [
			'label'  => esc_html_x( 'snowbirdie', 'admin', 'snowbirdie' ),
			'colors' => [
				'header_text_color'        => '#0a0909',
				'header_background_color'  => '#fcfbfa',
				'content_title_color'      => '#401b0a',
				'content_text_color'       => '#424242',
				'content_alt_text_color'   => '#696969',
				'content_accent_color'     => '#ff5505',
				'content_background_color' => '#fbfbfb',
				'footer_title_color'       => '#fff6f2',
				'footer_text_color'        => '#faf6f5',
				'footer_alt_text_color'    => '#fff6f2',
				'footer_accent_color'      => '#ff5505',
				'footer_background_color'  => '#0d0907',
				'button_text_color'        => '#fcfbfa',
				'button_background_color'  => '#401b0a',
			],
		],
	], $color_schemes );
} );

add_filter( 'get_the_excerpt', function ( $output ) {

	if ( has_excerpt() && ! is_admin() && ! is_attachment() ) {
		return $output . snowbird_filter_excerpt_more();
	}
} );

add_filter( 'snowbird_filter_excerpt_more', function () {

	return '<p><a class="xf__more xf__button" href="' . esc_url( get_permalink() ) . '">' . esc_html__( 'Read More', 'snowbird' ) . '</a></p>';
} );

add_filter( 'snowbird_footer_text', function () {

	$site_name = get_bloginfo( 'name' );

	return sprintf(
		esc_html__( 'Copyright &copy; %1$s %2$s // %3$s', 'snowbirdie' ),
		date( 'Y' ),
		sprintf(
			'<a href="%2$s" title="%3$s" rel="home">%1$s</a>',
			esc_html( $site_name ),
			esc_url( home_url( '/' ) ),
			esc_attr( $site_name )
		),
		esc_html( get_bloginfo( 'description' ) )
	);
} );

add_action( 'init', function () {

	remove_action( 'after_setup_theme', 'snowbird_logo_data_update', 99 );
	remove_action( 'after_setup_theme', 'snowbird_jetpack_setup' );
	remove_action( 'tgmpa_register', 'snowbird_recommended_plugins' );
} );

add_action( 'after_setup_theme', function () {
	
	set_post_thumbnail_size( 1920, 720, true );

	add_image_size( 'snowbird-large', 1280, 480, true );
	add_image_size( 'snowbird-thumb', 400, 400, true );
	add_image_size( 'snowbird-small', 120, 120, true );
}, 20 );

add_action( 'widgets_init', function () {
	
	register_sidebar( [
		'name'          => esc_html_x( 'Author Bio', 'admin', 'snowbirdie' ),
		'id'            => 'author-bio',
		'description'   => 'Sidebar right below the author bio, displayed on single posts only.',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	] );
} );

add_action( 'wp_enqueue_scripts', function () {

	wp_enqueue_style(
		'snowbirdie-style',
		get_stylesheet_directory_uri() . '/style.css',
		[],
		wp_get_theme()->get( 'Version' )
	);
}, 20 );

add_action( 'snowbird_author_bio', function () {

	if ( is_single() ) {
		dynamic_sidebar( 'author-bio' );
	}
} );

add_action( 'snowbirdie.before_footer_widgets', function () {

	?>
	<div class="xf__container row clear">
		<div class="full first last">
			<?php the_widget( 'WP_Widget_Search' ); ?>
		</div>
	</div>
	<?php
} );
