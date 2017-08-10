<?php
// create CPT
function NAMESPACE_register_post_type() {

	$args = array('public' => true, 'label' => 'Help Section', 'has_archive' => true, 'name'=> 'Help Section', 'query_var' => true);

	register_post_type('help_section', $args);
}
add_action('init', 'NAMESPACE_register_post_type');

// add the CPT to the search query
function NAMESPACE_add_custom_types( $query ) {
   if ( $query->is_search )
      $query->set( 'post_type', array( 'post', 'help_section') );
   return $query;
}
add_filter( 'pre_get_posts', 'NAMESPACE_add_custom_types' );

// ajax fetch from db
function NAMESPACE_ajax_fetch() {
?>
    <script type="text/javascript">
        function fetch() {
            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'post',
                data: {
                    action: 'NAMESPACE_data_fetch',
                    keyword: jQuery('#keyword').val()
                },
                success: function(data) {
                    jQuery('#datafetch').html(data);
                }
            });
        }
    </script>
<?php
}
add_action( 'wp_footer', 'NAMESPACE_ajax_fetch' );

// render the result
function NAMESPACE_data_fetch(){
    $the_query = new WP_Query( array( 'posts_per_page' => -1, 's' => esc_attr( $_POST['keyword'] ), 'post_type' => 'post' ) );
    if( $the_query->have_posts() ) :
        while( $the_query->have_posts() ): $the_query->the_post(); ?>
        <?php if (get_post_type( $post->ID ) == 'help_section') { //render only the CPT's results?>
            <h2>
                <a href="<?php echo esc_url( post_permalink() ); ?>">
                    <?php the_title();?>
                </a>
            </h2>
            <center><?php $post_type = get_post_type( $post->ID );?></center>
        <?php }
        endwhile;
        wp_reset_postdata();  
    endif;
    die();
}
add_action('wp_ajax_nopriv_NAMESPACE_data_fetch','NAMESPACE_data_fetch');
?>

<!-- Display the search bar -->
<input type="text" name="keyword" id="keyword" onkeyup="fetch()">
<div id="datafetch"></div>