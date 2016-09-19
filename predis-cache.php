<?php
/*
Plugin Name: Predis-Cache
Description: Simple plugin for control predis cache
Version: 1.0
Author: Manh Tu
*/

$redis_host = "tgtn-001.mm9ndj.0001.apse1.cache.amazonaws.com:6379";

/**
* Edit post 
* Clear cache action
*/
function predis_update_cache( $post_id ) {
    if ( wp_is_post_revision( $post_id ) )
        return;

    $post_url = get_permalink( $post_id );
    delete_predis_cache($post_url);
}

/**
* Remove post
* Clear cache action
*/
function predis_delete_cache($post_id) {
    $post_url = get_permalink( $post_id );
    delete_predis_cache($post_url);
}

function includePredis()
{
    if (!class_exists('Predis\Client')) {
        include( plugin_dir_path( __FILE__ ) . 'predis.php');
    }
}

/**
* Clear cache of one post
* Also clear cache of home page
*/
function delete_predis_cache($post_url) {
    includePredis();
    global $redis_host;
    $redis = new Predis\Client('redis://'. $redis_host);
    $url_domain = home_url();
    $pkey = md5($post_url);
    // First, remove this page
    if ($redis->exists($pkey)) {
        $redis->del($pkey);
    }
    // Second, remove main page

    $pkey_1 = md5($url_domain);
    $pkey_2 = md5($url_domain. "/");
    $pkey_3 = md5($url_domain. "/index.php");
    if ($redis->exists($pkey_1)) {
        $redis->del($pkey_1);
    }
    if ($redis->exists($pkey_2)) {
        $redis->del($pkey_2);
    }
    if ($redis->exists($pkey_3)) {
        $redis->del($pkey_3);
    }
    echo "<!-- predis removed link : $post_url -->";
}

function clean_single_cache_success() {
    ?>
    <div class="updated notice">
        <p><?php _e( 'Cache on this site was cleaned!' ); ?></p>
    </div>
    <?php
}

function clean_single_cache()
{
    includePredis();
    global $redis_host;
    $redis = new Predis\Client('redis://'. $redis_host);    
    if ($redis->flushall()) {        
        add_action( 'admin_notices', 'clean_single_cache_success' );
    }
}

/**
* Add top menu admin
*/
function toolbar_predis( $wp_admin_bar ) {
    $args = array(
        'id'    => 'predis_cache',
        'title' => 'Predis Cache',
        'href'  => '#',
        'parent' => false,
    );
    $wp_admin_bar->add_node( $args );
    $args = array();
    array_push($args,array(
        'id'        =>  'empty_cache',
        'title'     =>  'Empty all cache on this site',
        'href'      =>  '#',
        'parent'    =>  'predis_cache',
        'meta'      =>  array('onclick' =>  'alert_single_cache();')
    ));
    for($a=0; $a<sizeOf($args); $a++)
    {
        $wp_admin_bar->add_node($args[$a]);
    }

    // if user select clear this site
    if (substr($_SERVER['REQUEST_URI'], -4) == '?c=y') {
        clean_single_cache();
    }
}

function confirm_clean_cache(){
    ?>
    <script type="text/javascript">
        function alert_single_cache() {
            var ans = confirm("Are you sure you want to clean cache on this sites ?");
            if (ans) {
                window.location="?c=y";
            }
        }
    </script>
    <?php
}

add_action('admin_footer', 'confirm_clean_cache');
add_action( 'admin_bar_menu', 'toolbar_predis', 999 );
add_action( 'save_post', 'predis_update_cache' );
add_action( 'before_delete_post', 'predis_delete_cache', 10 );

