<?php
function sm_defultfunction(){
	
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('custom-background');

if(function_exists('register_nav_menu')){
	register_nav_menu('main-menu',__('main menu','zboom'));
	}



}

add_action('after_setup_theme','sm_defultfunction');


function read_more($limit){
		$post_content = explode(" ", get_the_content());
		
		$less_content = array_slice($post_content, 0, $limit);
		
		echo implode(" ", $less_content);
	}

register_post_type('smslider',array(
	'labels' => array(
		'name' => 'sliders',
		'add_new_item' =>'Add New Slider'
	),
	'public' => true,
	'supports' => array('title','editor','thumbnail'),
	));

register_post_type('zboomservices', array(
	'labels' => array(
	      'name' => 'blocks',
		  'add_new_item' => __('Add New Block','zboom')
	),
	'public' => true,
	'supports' => array('title','editor','thumbnail',)
	
	));

function create_product_tax() {
	register_taxonomy(
		'productcat',
		'abcproduct',
		array(
			'label' => __( 'category' ),
			'rewrite' => array( 'slug' => 'productcat' ),
			'hierarchical' => true,
		)
	);
}

add_action( 'init', 'create_product_tax' );


// Remove the toggle buttons filter
add_action( 'wp' , 'remove_2021_nav_button' );
function remove_2021_nav_button(){
    remove_filter( 'walker_nav_menu_start_el', 'twenty_twenty_one_add_sub_menu_toggle', 10, 4 );
}

/**
 * Disabled Plugin Update.
*/
function autopiacars_plugin_updates( $value ) {

	if( isset( $value->response['advanced-custom-fields-pro/acf.php'] ) ) {        

	   unset( $value->response['advanced-custom-fields-pro/acf.php'] );

	 }

	 return $value;

  }

add_filter( 'site_transient_update_plugins', 'autopiacars_plugin_updates' );



/**

 * Disabled Deactivate Pluin.

 */

add_filter( 'plugin_action_links', 'autopiacars_lock_plugins', 10, 4 );

function autopiacars_lock_plugins( $actions, $plugin_file, $plugin_data, $context ) {

    // Remove edit link for all

    if ( array_key_exists( 'edit', $actions ) )

        unset( $actions['edit'] );

    // Remove deactivate link for crucial plugins

    if ( array_key_exists( 'deactivate', $actions ) && in_array( $plugin_file, array(

        'advanced-custom-fields-pro/acf.php'

    )))
    unset( $actions['deactivate'] );

    return $actions;

}

// ---Create Custom Post type----

function banner_type() {

	$args = array(
      'label' => 'HomePage Banner',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'banner'),
        'query_var' => true,
        'menu_icon' => 'dashicons-album',
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'trackbacks',
            'custom-fields',
            'comments',
            'revisions',
            'thumbnail',
            'author',
            'page-attributes',)
        );

	register_post_type( 'banner', $args );
	flush_rewrite_rules();

}
add_action( 'init', 'banner_type' );


function testimonial_type() {

    $args = array(
      'label' => 'Testimonial',
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'testimonial'),
        'query_var' => true,
        'menu_icon' => 'dashicons-album',
        'supports' => array(
            'title',
            'editor',
            'excerpt',
            'trackbacks',
            'custom-fields',
            'comments',
            'revisions',
            'thumbnail',
            'author',
            'page-attributes',)
        );

    register_post_type( 'testimonial', $args );
    flush_rewrite_rules();

$args1 = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'testimonial_category' ),
    );

    register_taxonomy( 'testimonial_category', array( 'testimonial' ), $args1 );


}
add_action( 'init', 'testimonial_type' );

//--------------------------------------------------------------------

/**
 * Enqueue CSS & JS.
 */
require get_parent_theme_file_path( '/assets/enqueue.php' );

//--------------------------------------------------------------------------------
function abc_right_sidebar(){
	register_sidebar(array(
	     'name' => __('right sidebar','abc'),
		 'description' => __('Add your right sidebar widgets here','abc'),
		 'id' => 'right-sidebar',
		 'before_widget' => '<div class="box right-sidebar">',
		 'after_widget' => '</div></div>',
		 'before_title' => '<div class="heading">',
	     'after_title' => '</h2></div><div class="content">'
	));
}
add_action('widgets_init','abc_right_sidebar');

/**
 * Extend Recent Posts Widget 
 *
 * Adds different formatting to the default WordPress Recent Posts Widget
 */

Class My_Recent_Posts_Widget extends WP_Widget_Recent_Posts {

	function widget($args, $instance) {
	
		extract( $args );
		
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts') : $instance['title'], $instance, $this->id_base);
				
		if( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )
			$number = 4;
					
		$r = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
		if( $r->have_posts() ) :
			
			echo $before_widget;
			if( $title ) echo $before_title . $title . $after_title; ?>
			<ul class="list-style circle-o pl-4 pb-0">
				<?php while( $r->have_posts() ) : $r->the_post(); ?>				
				<li><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="btn-link btn-primary pl-0"><?php the_title(); ?></a></li>
				<?php endwhile; ?>
			</ul>
			 
			<?php
			echo $after_widget;
		
		wp_reset_postdata();
		
		endif;
	}
}
function my_recent_widget_registration() {
  unregister_widget('WP_Widget_Recent_Posts');
  register_widget('My_Recent_Posts_Widget');
}
add_action('widgets_init', 'my_recent_widget_registration');

//==================================================================

/*
Theme Name: zBoom Music
Author: subham
Description: simple music wordpress theme
Virsion: 1.0
Tag: mosic, song, simple theme
Textdomain: zboom
*/
<?php wp_nav_menu( array('menu' => 'main_menu' , 'container' => '' , 'items_wrap' => '%3$s' )); ?>


<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3685.362867571015!2d88.34316971443329!3d22.528075840282018!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a02773831f240b3%3A0x664e32b394c0eca1!2sBest+Digital+Marketing+Companies+in+Kolkata%2C+India+%3A+Kreative+Machinez!5e0!3m2!1sen!2sin!4v1564109198876!5m2!1sen!2sin" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>

<?php while(have_posts()) { the_post();?>
<div class="merchandise_heading_sec col-lg-4 col-sm-12 mt-auto mb-auto">
<?php the_field('merchandise_box');?>
</div>
<?php } ?>


 <?php

                $args = array(

                'orderby'         => 'post_date',

                'posts_per_page'   => -1,

                'order'           => 'ASC',

                'post_type'       => 'banner',

                'post_status'     => 'publish' );

                $myposts = get_posts($args);               

                foreach($myposts as $post) 

                {               

        ?>

        <?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>

        <?php } wp_reset_query(); ?>




        <?php 
              $taxonomyName = "productcat";
//This gets top layer terms only.  This is done by setting parent to 0.  
$parent_terms = get_terms( $taxonomyName, array( 'parent' => 0, 'orderby' => 'slug', 'hide_empty' => false ) );   
echo '<ul>';
foreach ( $parent_terms as $pterm ) {
    //Get the Child terms
    $terms = get_terms( $taxonomyName, array( 'parent' => $pterm->term_id, 'child_of' => 5, 'orderby' => 'slug', 'hide_empty' => false ) );
    foreach ( $terms as $term ) {
        echo '<li><a href="' . get_term_link( $term ) . '">' . $term->name . '</a></li>';   
    }
}
echo '</ul>';

              ?>

============================================================================

<?php

    $args = array(

    'orderby'         => 'post_date',

    'posts_per_page'   => -1,

    'order'           => 'ASC',

    'post_type'       => 'post',

    'post_status'     => 'publish' );

    $myposts = get_posts($args);               

    foreach($myposts as $post) 

    {               

?>
<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>
<?php echo $post->post_title; ?>
<?php echo get_permalink( $post->ID ); ?>
<?php the_excerpt(); ?>

<?php } wp_reset_query(); ?>

<?php
	$thumb_id = get_post_thumbnail_id(get_the_ID());
	$alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
	$image_title = get_the_title($thumb_id);
?>
<?= $alt; ?>

<form method="get" action="<?php echo home_url('/'); ?>">
    <input type="text" name="s" placeholder="What you are looking for?" value="<?php the_search_query(); ?>">
    <input type="hidden" name="post_type" value="product">
</form>

=======================================================================

<?php 
    /**
	 * Setup query to show the ‘services’ post type with ‘4’ posts.
	 * Output the title & Image.
	 */
	    $args = array(  
	        'post_type' => 'services',
	        'post_status' => 'publish',
	        'posts_per_page' => 4, 
	        'orderby' => 'post_date', 
	        'order' => 'ASC', 
	    );

	    $loop = new WP_Query( $args ); 
	        
	    while ( $loop->have_posts() ) : $loop->the_post();  ?>



	   <?php endwhile;

    	wp_reset_postdata(); ?>
=============================================================
<?php

// Remove Prefix of Archive Title

add_filter( 'get_the_archive_title', function ($title) {    
      if ( is_category() ) {    
              $title = single_cat_title( '', false );    
          } elseif ( is_tag() ) {    
              $title = single_tag_title( '', false );    
          } elseif ( is_author() ) {    
              $title = '<span class="vcard">' . get_the_author() . '</span>' ;    
          } elseif ( is_tax() ) { //for custom post types
              $title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
          }    
      return $title;    
  });
?>
==========================================================================
<?php $excerpt = get_the_excerpt();
$excerpt = substr( $excerpt, 0, 250 ); // Only display first 250 characters of excerpt
$result = substr( $excerpt, 0, strrpos( $excerpt, ' ' ) );
echo $result; ?>

====================================================
<?php 
//for post view 
if ( ! function_exists( 'function_name_given' ) ) :    
/**     * get the value of view.     */ 
function function_name_given($postID) {   
$count_key = 'wpb_post_views_count';    
$count = get_post_meta($postID, $count_key, true);    
if($count ==''){        
$count = 1;        
delete_post_meta($postID, $count_key);        
add_post_meta($postID, $count_key, '1');    
} else {        
$count++;        
update_post_meta($postID, $count_key, $count);    
}
}
endif;
//for single.php before endwhile put below.
function_name_given(get_the_ID()); ?>

<?php
// post view count display code.
                             
if ( get_post_meta( get_the_ID() , 'wpb_post_views_count', true) == '') { echo '0 view' ;                            
} else { 
echo get_post_meta( get_the_ID() , 'wpb_post_views_count', true) ." ". 'views'; }; ?>

<?php 
// For Popular post
$popularpost = new WP_Query( array( 'posts_per_page' => 5, 'meta_key' => 'wpb_post_views_count', 'orderby' => 'meta_value_num', 'order' => 'DESC'  ) );
                while ( $popularpost->have_posts() ) : $popularpost->the_post();
                	the_title();
endwhile; ?>


========================================================================
<?php
//---------- Remove auto <p> Tag From Contact Form 7 ------------
add_filter('wpcf7_autop_or_not', '__return_false');

function custom_short_excerpt($excerpt){
	return substr($excerpt, 0, 125);
}
add_filter('the_excerpt', 'custom_short_excerpt');

// short title
 echo wp_trim_words( get_the_title(), 5, ' ...' );

 
/**
 * Custom Customizer.
 */
function sud_itsupport_Customizer($wp_customize){
	$wp_customize->add_panel( 'Sud_Page_Panel', array(  
		'priority'     => 30,
	  	'capability'     => 'edit_theme_options',
	  	'theme_supports' => '',
	  	'title'          =>'Additional Theme Option',
	  	'description'    => 'This Page Is for Customizer Page Setting'
	));

	$wp_customize->add_section('bia_logo_section', array(
    'panel' => 'Sud_Page_Panel',
    'title' => 'Inerpage Logo'
  ));
  $wp_customize->add_setting( 'bia_logo', array(
        'sanitize_callback' => 'esc_url_raw',
    ) );

    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'bia_logo', array(
        'label'    => __( 'Site Logo', 'bia' ),
        'section'  => 'bia_logo_section',
        'settings' => 'bia_logo',
    ) ) );
    

	$wp_customize->add_section('Top_leftbar_id', array(
		'panel' => 'Sud_Page_Panel',
		'title' => 'Topbar'
	));  
	$wp_customize->add_setting("top_leftbar_text", array('default' => ''));	
	$wp_customize->add_control("top_leftbar_text", array(	  
	  'label'   => "Leftbar Text:",
	  'settings'=> "top_leftbar_text",
	  'section' => 'Top_leftbar_id',
	  'type'    => 'textarea'		
	));

	$wp_customize->add_section('Contact_section_id', array(
		'panel' => 'Sud_Page_Panel',
		'title' => 'Contact Info'
	));  
	$wp_customize->add_setting("com_add", array('default' => ''));	
	$wp_customize->add_control("com_add", array(	  
	  'label'   => "Address:",
	  'settings'=> "com_add",
	  'section' => 'Contact_section_id',
	  'type'    => 'textarea'		
	));
	$wp_customize->add_setting("google_link", array('default' => ''));	
	$wp_customize->add_control("google_link", array(	  
	  'label'   => "Google Map Link of Address:",
	  'settings'=> "google_link",
	  'section' => 'Contact_section_id',
	  'type'    => 'text'		
	));
	$wp_customize->add_setting("com_phone", array('default' => ''));	
	$wp_customize->add_control("com_phone", array(	  
	  'label'   => "Phone:",
	  'settings'=> "com_phone",
	  'section' => 'Contact_section_id',
	  'type'    => 'text'		
	));
	$wp_customize->add_setting("com_email", array('default' => ''));	
	$wp_customize->add_control("com_email", array(	  
	  'label'   => "Email:",
	  'settings'=> "com_email",
	  'section' => 'Contact_section_id',
	  'type'    => 'text'		
	));

	$wp_customize->add_section('Social_section_id', array(
		'panel' => 'Sud_Page_Panel',
		'title' => 'Social Links'
	));  
	$wp_customize->add_setting("fb_link", array('default' => ''));	
	$wp_customize->add_control("fb_link", array(	  
	  'label'   => "Facebook Link:",
	  'settings'=> "fb_link",
	  'section' => 'Social_section_id',
	  'type'    => 'text'		
	));
	$wp_customize->add_setting("instagram_link", array('default' => ''));	
	$wp_customize->add_control("instagram_link", array(	  
	  'label'   => "Instagram Link:",
	  'settings'=> "instagram_link",
	  'section' => 'Social_section_id',
	  'type'    => 'text'		
	));
	$wp_customize->add_setting("twitter_link", array('default' => ''));	
	$wp_customize->add_control("twitter_link", array(	  
	  'label'   => "Twitter Link:",
	  'settings'=> "twitter_link",
	  'section' => 'Social_section_id',
	  'type'    => 'text'		
	));
	$wp_customize->add_setting("linkedin_link", array('default' => ''));	
	$wp_customize->add_control("linkedin_link", array(	  
	  'label'   => "LinkedIn Link:",
	  'settings'=> "linkedin_link",
	  'section' => 'Social_section_id',
	  'type'    => 'text'		
	));	

	$wp_customize->add_section('Copyright_text_id', array(
		'panel' => 'Sud_Page_Panel',
		'title' => 'Copyright Text'
	));  
	$wp_customize->add_setting("copyright_text", array('default' => ''));	
	$wp_customize->add_control("copyright_text", array(	  
	  'label'   => "Copyright Text:",
	  'settings'=> "copyright_text",
	  'section' => 'Copyright_text_id',
	  'type'    => 'textarea'		
	));
}
add_action('customize_register', 'sud_itsupport_Customizer');

<?php echo get_theme_mod('phone_no'); ?>
<?php the_custom_logo(); ?>
<!-------------------------------------------------------------------->

<?php
// query for the new-builds page
$outdoor_query = new WP_Query( 'pagename=new-builds' );
// "loop" through query (even though it's just one page) 
while ( $outdoor_query->have_posts() ) : $outdoor_query->the_post(); ?>
<?php endwhile;
    // reset post data (important!)
    wp_reset_postdata(); ?>

    
<!-- comment form modify---------------------------------------- -->

<?php

add_filter('comment_form_defaults', 'set_my_comment_title', 20);
function set_my_comment_title( $defaults ){
 $defaults['title_reply'] = __('Leave a comment', 'customizr-child');
 return $defaults;
}

function wcs_change_submit_button_class( $args ) {
    $args = array( 
    	'label_submit' => 'submit comment',
    	'class_submit' => 'btn btn-primary btn-round btn-long mt-5'
    	);
    return $args;
}
add_filter( 'comment_form_defaults', 'wcs_change_submit_button_class' );


// change comment form fields order
add_filter( 'comment_form_fields', 'mo_comment_fields_custom_order' );
function mo_comment_fields_custom_order( $fields ) {
	$comment_field = $fields['comment'];
	$author_field = $fields['author'];
	$email_field = $fields['email'];
	$url_field = $fields['url'];
	unset( $fields['comment'] );
	unset( $fields['author'] );
	unset( $fields['email'] );
	unset( $fields['url'] );
	// the order of fields is the order below, change it as needed:
	
	$fields['author'] = $author_field;
	$fields['email'] = $email_field;
	$fields['url'] = $url_field;
	$fields['comment'] = $comment_field;
	// done ordering, now return the fields:
	return $fields;
}

// comment form customization

add_filter( 'comment_form_default_fields', 'mo_comment_fields_custom_html' );
function mo_comment_fields_custom_html( $fields ) {
	// first unset the existing fields:
	unset( $fields['comment'] );
	unset( $fields['author'] );
	unset( $fields['email'] );
	unset( $fields['url'] );
	// then re-define them as needed:
	$fields = [
		'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x( 'A CUSTOM COMMENT LABEL', 'noun', 'textdomain' ) . '</label> ' .
			'<textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" aria-required="true" required="required"></textarea></p>',
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'A CUSTOM NAME LABEL', 'textdomain'  ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
			'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" maxlength="245"' . $aria_req . $html_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'A CUSTOM EMAIL LABEL', 'textdomain'  ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
			'<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" maxlength="100" aria-describedby="email-notes"' . $aria_req . $html_req  . ' /></p>',
		'url'    => '<p class="comment-form-url"><label for="url">' . __( 'A CUSTOM WEBSITE LABEL', 'textdomain'  ) . '</label> ' .
			'<input id="url" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" maxlength="200" /></p>',
	];
	// done customizing, now return the fields:
	return $fields;
}
// remove default comment form so it won't appear twice
add_filter( 'comment_form_defaults', 'mo_remove_default_comment_field', 10, 1 ); 
function mo_remove_default_comment_field( $defaults ) { if ( isset( $defaults[ 'comment_field' ] ) ) { $defaults[ 'comment_field' ] = ''; } return $defaults; }

//end comment form customization---------

function alpha_comments_defaults( $defaults ) {
    $defaults['id_form'] = '';
    $defaults['id_submit'] = '';
    $defaults['comment_field'] = '<div class="subscribe-box-1 mt-4">' .
                                                                '<textarea name="comment" class="for-textarea form-control" placeholder="Comment *"></textarea></div><div class="clear"></div><div class="clear"></div>';
    return $defaults;
}
add_filter('comment_form_defaults', 'alpha_comments_defaults');


function alpha_comments_fields( $fields ) {
    $commenter= wp_get_current_commenter();
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : ' ' );

    $fields = array(
                        'author' =>
                                '<div class="clear"></div>
                  <div class="subscribe-box-1 mt-3">' .
                                '<input name="author" placeholder="Your Name *" type="text" class="form-control" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" ' . $aria_req . ' /></div>
                  <div class="clear"></div>',

                        'email' =>
                                '<div class="subscribe-box-1 mt-4">' .
                                '<input name="email" placeholder="Email *" class="form-control" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" ' . $aria_req . ' /></div>
                  <div class="clear"></div>',

                        'url' => '<div class="subscribe-box-1 mt-4">' .
                                '<input name="url" placeholder="Website *" class="form-control" type="text" value="' . esc_attr(  $commenter['comment_author_url'] ) . '" size="30" ' . $aria_req . ' /></div>
                  <div class="clear"></div>',
    );

    return $fields;
}
add_filter('comment_form_default_fields', 'alpha_comments_fields');


// Reorder comment fields.
// http://wordpress.stackexchange.com/a/218324/2807
function alpha_move_comment_field( $fields ) {
    $comment_field = $fields['comment'];
    unset( $fields['comment'] );
    $fields['comment'] = $comment_field;

    return $fields;
}
add_filter( 'comment_form_fields', 'alpha_move_comment_field' );

//comments count
<?php comments_popup_link( 'No comments', '1 comment', '% comments', 'comments-link', 'Comments are off'); ?>

?>
<!-- start on click loadmore -->
<style type="text/css">
    .col-lg-4.col-md-6.text-center.killer {
    display: none;
    }
	   
	a#load {
    width: 135px;
    border: 1px solid #c40101;
    display: table;
    margin: 0 auto;
    border-radius: 50px;
    color: #c40101;
    font-size: 14px;
    letter-spacing: 1.12px;
    padding: 6px 0;
	}
	p.no_more_post {
    padding: 15px;
    background: #fff1f3;
    color: #c51230;
    border: 1px solid #f5d8dc;
	}
   </style>
<script>
jQuery(function(){
    	jQuery(".killer").slice(0, 6).show(); // select the first ten
	    jQuery("#load").click(function(e){ // click event for load more
	        e.preventDefault();
	        var total_post = jQuery('.post_count').val();
	        var post_count = jQuery('.post_count_next').val();
	        jQuery('.post_count_next').val('');
	       	var next_count = parseInt(post_count) + parseInt(3);
	       	
	       	if( next_count != '' ){
	       		jQuery(".killer:hidden").slice(0, 3).show();
	        	jQuery('.post_count_next').val(next_count);
	        	console.log(next_count);
	        	if( total_post <= next_count ){
	        		jQuery("#load").hide();
	        		jQuery('.no_more_post').slideDown();
	        	}
	       	}
	        
	    });
	});
</script>

<div class="col-lg-9 col-md-8">
            <div class="row showproduct">
               <?php 
              $terms = wp_get_post_terms( $post->ID, array('productcat') );
              $term_slugs = wp_list_pluck( $terms, 'slug' ); 

              $args = array(

              'posts_per_page' => -1,

              'tax_query' => array(

                  'relation' => 'AND',

                  array(

                      'taxonomy' => 'productcat',

                      'field' => 'slug',

                       'terms' => $term_slugs

                      //'terms' => $category->slug

                  )

              ),

              'post_type' => 'product',

              'orderby' => 'post_date,',
              'order'   => 'ASC',

              );

              $testimonial = new WP_Query($args);
              $post_count  = $testimonial->found_posts;

              //print_r($products);

              if ( $testimonial->have_posts() ) {

               while ( $testimonial->have_posts() ) {
                $testimonial->the_post();

               ?>
               <div class="col-lg-4 col-md-6 text-center killer">
                  <div class="wrap">
                     <a href="<?php echo get_permalink( $post->ID ); ?>">
                     <img src="<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>" alt="">
                     </a>
                     <div class="decp">
                        <h4><?php the_title(); ?></h4>
                        <p><?php echo $post->post_excerpt; ?></p>
                        <a href="<?php the_permalink(); ?>">Read More</a>
                     </div>
                  </div>
               </div>
              <?php  } wp_reset_query(); } ?>
            </div>
            <div class="col-lg-12 text-center">
              <a href="#" class="view-link" id="load">Load More...</a>
              <p class="no_more_post" style="display: none"> No More Post</p>
            <?php //the_posts_pagination( array('screen_reader_text'=>' ') ); ?>
        	</div>
         </div>
        <input type="hidden" class="post_count" value="<?php //echo $post_count;?>">
		<input type="hidden" class="post_count_next" value="3">

		<!-- //End on click loadmore -->
		
<!-- woocommerce email attachment send when order placed -->

<?php 
add_filter( 'woocommerce_email_attachments', 'add_woocommerce_attachments_for_low_carbohidrate_meal', 10, 3 );

function add_woocommerce_attachments_for_low_carbohidrate_meal ( $attachments, $email_id, $email_order ){
  $product_id = 1232;
  $attachment_id = 1493;

  

  if( $email_id === 'customer_processing_order' ){
    $order = wc_get_order( $email_order );
    $items = $order->get_items();
  
    foreach ( $items as $item ) {
      if ( $product_id === $item->get_product_id() ) {
        $attachments[] = get_attached_file( $attachment_id );		
      }
    }	 
	
  }
  return $attachments;
}

 ?>

