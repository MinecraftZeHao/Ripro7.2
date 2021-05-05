<?php

global $wpdb, $order_table_name, $paylog_table_name, $coupon_table_name, $balance_log_table_name,$down_log_table_name;
$order_table_name       = isset($table_prefix) ? ($table_prefix . 'cao_order') : ($wpdb->prefix . 'cao_order');
$paylog_table_name      = isset($table_prefix) ? ($table_prefix . 'cao_paylog') : ($wpdb->prefix . 'cao_paylog');
$coupon_table_name      = isset($table_prefix) ? ($table_prefix . 'cao_coupon') : ($wpdb->prefix . 'cao_coupon');
$balance_log_table_name = isset($table_prefix) ? ($table_prefix . 'cao_balance_log') : ($wpdb->prefix . 'cao_balance_log');
$ref_log_table_name = isset($table_prefix) ? ($table_prefix . 'cao_ref_log') : ($wpdb->prefix . 'cao_ref_log');
$down_log_table_name = isset($table_prefix) ? ($table_prefix . 'cao_down_log') : ($wpdb->prefix . 'cao_down_log');
$mpwx_log_table_name = isset($table_prefix) ? ($table_prefix . 'cao_mpwx_log') : ($wpdb->prefix . 'cao_mpwx_log');

if (!function_exists('caozhuti_setup')):
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function caozhuti_setup()
    {
        $setupDb = new setupDb();
        $setupDb->install();

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');
        // add_image_size( 'cao_full_400', 400 );
        // add_image_size( 'cao_full_800', 800 );
        // add_image_size( 'cao_full_1160', 1160 );
        // add_image_size( 'cao_rect_300', 300, 200, true );

        register_nav_menus(array(
            'menu-1' => '主菜单',
            // 'menu-2' => '分类筛选菜单',
        ));
        
        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        // Load regular editor styles into the new block-based editor.
        add_theme_support('editor-styles');

        // Load default block styles.
        add_theme_support('wp-block-styles');

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');

        // add link manager // 开启友情链接功能
        add_filter('pre_option_link_manager_enabled', '__return_true');

        //添加自定义文章类型代码(弃用)
        // new Ws_Post_Type('shop','shop_cat','商品');
        // add_theme_support( 'post-formats', array( 'video','image') );
        // CREATE PAGES
        $init_pages = array(
            'pages/user.php' => array('用户中心', 'user'),
            'pages/zhuanti.php' => array('专题', 'zhuanti'),
            'pages/archives.php' => array('存档', 'archives'),
            'pages/tags.php' => array('标签云', 'tags'),
        );

        foreach ($init_pages as $template => $item) {

            $one_page = array(
                'post_title'  => $item[0],
                'post_name'   => $item[1],
                'post_status' => 'publish',
                'post_type'   => 'page',
                'post_author' => 1,
            );

            
                $one_page_check =  get_page_by_title($item[0]);
           
            
            if (!isset($one_page_check->ID)) {
                $one_page_id = wp_insert_post($one_page);
                update_post_meta($one_page_id, '_wp_page_template', $template);
            }
        }
        //更新伪静态规则
        // flush_rewrite_rules();

    }
add_action('after_setup_theme', 'caozhuti_setup');
endif;


/**
 * [Init_theme 激活主题跳转设置页面]
 * @Author   Dadong2g
 * @DateTime 2019-05-28T11:16:53+0800
 * @param    [type]                   $oldthemename [description]
 */
function Init_to_theme($oldthemename){
  global $pagenow;
  if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
    wp_redirect( admin_url( 'admin.php?page=csf-caozhuti#tab=27' ) );
    exit;
  }
}

add_action('after_switch_theme', 'Init_to_theme');




/**
 * [caozhuti_widgets_init Register widget area.]
 * @Author   Dadong2g
 * @DateTime 2019-05-28T23:47:36+0800
 * @return   [type]                   [description]
 */
function caozhuti_widgets_init()
{
    $sidebars = array(
        'sidebar' => '文章页侧栏',
        'off_canvas'   => '全站侧栏菜单',
        // 'footer'   => '网站屁股小工具'
    );
    if (is_cao_site_list_blog() || true) {
        $sidebars['blog'] = '博客模式侧边栏';
    }
    foreach ($sidebars as $key => $value) {
        register_sidebar(array(
            'name'          => $value,
            'id'            => $key,
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h5 class="widget-title">',
            'after_title' => '</h5>',
        ));
    }

}
add_action('widgets_init', 'caozhuti_widgets_init');

/**
 * [caozhuti_scripts 加载主题JS和CSS资源]
 * @Author   Dadong2g
 * @DateTime 2019-05-28T23:46:28+0800
 * @return   [type]                   [description]
 */
if (!function_exists('caozhuti_scripts')):
function caozhuti_scripts()
{
    $theme_assets = get_template_directory_uri() . '/assets';
    $theme_assets_v = _the_theme_version();
    if (!is_admin()) {

        // 禁用jquery和110n翻译
        wp_deregister_script('jquery');
        wp_deregister_script('l10n');
        //注册CSS引入CSS
        wp_register_style('external', $theme_assets . '/css/external.css', array(), $theme_assets_v, 'all');
        wp_register_style('sweetalert2', $theme_assets . '/css/sweetalert2.min.css', array(), $theme_assets_v, 'all');
        wp_register_style('app', $theme_assets . '/css/app.css', array(), $theme_assets_v, 'all');
        wp_register_style('diy', $theme_assets . '/css/diy.css', array(), $theme_assets_v, 'all');
        wp_register_style('fancybox', $theme_assets . '/css/jquery.fancybox.min.css', array(), $theme_assets_v, 'all');
        if (true) {
            wp_enqueue_style('external');
            wp_enqueue_style('sweetalert2');
            wp_enqueue_style('app');
            wp_enqueue_style('diy');
            wp_enqueue_style('fancybox');
        }
        // 引入JS
        wp_register_script('jquery', $theme_assets . '/js/jquery-2.2.4.min.js', '', '2.2.4', false);
        wp_register_script('sweetalert2', $theme_assets . '/js/plugins/sweetalert2.min.js', array(), $theme_assets_v, false);
        wp_register_script('plugins', $theme_assets . '/js/plugins.js', array(), $theme_assets_v, true);
        wp_register_script('app', $theme_assets . '/js/app.js', array('jquery'), $theme_assets_v, true);
        wp_register_script('fancybox', $theme_assets . '/js/plugins/jquery.fancybox.min.js', array('jquery'), $theme_assets_v, true);
        wp_register_script('llqrcode', $theme_assets . '/js/plugins/llqrcode.js', array('jquery'), '2.0.1', true);
        wp_register_script('captcha','https://ssl.captcha.qq.com/TCaptcha.js', array(), '', true);

        if (true) {
            wp_enqueue_script('jquery');
            wp_enqueue_script('sweetalert2');
            wp_enqueue_script('plugins');
            wp_enqueue_script('app');
        }
        if (_cao('is_captcha_qq')) {
            wp_enqueue_script('captcha');
        }
        // llqrcode
        if (is_page_template('pages/user.php')) {
            wp_enqueue_script('llqrcode');
        }
        //jquery.fancybox.min.js
        if (is_singular() && _cao('is_fancybox_img',true)) {
            wp_enqueue_style('fancybox');
            wp_enqueue_script('fancybox');
        }
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
        //脚本本地化
        wp_localize_script('app', 'caozhuti',
            array(
                'site_name' => get_bloginfo('name'),
                'home_url' => esc_url( home_url() ),
                'ajaxurl'    => esc_url(admin_url('admin-ajax.php')),
                'is_singular'    => is_singular() ? 1 : 0,
                'tencent_captcha'    => array('is' => _cao('is_captcha_qq','0'),'appid' => _cao('captcha_qq_appid','')),
                'infinite_load' => '加载更多',
                'infinite_loading' => '<i class="fa fa-spinner fa-spin"></i> 加载中...',
                'site_notice' => array('is' => _cao('is_site_notify','0'),'color' => _cao('site_notify_color','rgb(33, 150, 243)'), 'html' => '<div class="notify-content"><h3>'._cao('site_notify_title','').'</h3><div>'._cao('site_notify_desc','').'</div></div>'),
                'pay_type_html' => _cao_get_pay_type_html(),
            )
        );

    }
}
add_action('wp_enqueue_scripts', 'caozhuti_scripts');
endif;
// 管理页面CSS
function caoAdminScripts() {   
    wp_enqueue_style('caoadmin', get_template_directory_uri() . '/assets/css/admin.css', array(), '', 'all');
}
add_action( 'admin_enqueue_scripts', 'caoAdminScripts' );


/**
 *
 * Codestar Framework
 * A Simple and Lightweight WordPress Option Framework for Themes and Plugins
 *
 */
require_once get_template_directory() . '/inc/codestar-framework/codestar-framework.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require_once get_template_directory() . '/inc/core-functions.php';

/**
 * Custom template tags for this theme.
 */
require_once get_template_directory() . '/inc/theme-functions.php';
require_once get_template_directory() . '/inc/core-ajax.php';

/**
 * core Class. 对php版本做了兼容处理
 */


        require_once get_template_directory() . '/inc/class/core.class.7.3.php';
  

/**
 * walker.class.php Class.
 */
require_once get_template_directory() . '/inc/class/walker.class.php';

/**
 * composer autoload.
 */
require_once get_template_directory() . '/vendor/autoload.php';

/**
 * admin
 */
require_once get_template_directory() . '/inc/admin/init.php';
//禁止加载谷歌字体开始！
function wp_style_del_web( $src, $handle ) {
	if( strpos(strtolower($src),'fonts.googleapis.com') ){
		$src=''; 
	}	
	return $src;
}
add_filter( 'style_loader_src', 'wp_style_del_web', 2, 2 );
//js处理
function wp_script_del_web( $src, $handle ) {
	$src_low = strtolower($src);
	if( strpos($src_low,'maps.googleapis.com') ){
		return  str_replace('maps.googleapis.com','ditu.google.cn',$src_low);  //google地图
	}	
	if( strpos($src_low,'ajax.googleapis.com') ){
		return  str_replace('ajax.googleapis.com','ajax.useso.com',$src_low);  //google库用360替代
	}
	if( strpos($src_low,'twitter.com') || strpos($src_low,'facebook.com')  || strpos($src_low,'youtube.com') ){
		return '';        //无法访问直接去除
	}	
	return $src;
}
add_filter( 'script_loader_src', 'wp_script_del_web', 2, 2 );
// 禁用WP编辑器加载Google字体css
function xintheme_remove_gutenberg_styles($translation, $text, $context, $domain)
{
    if($context != 'Google Font Name and Variants' || $text != 'Noto Serif:400,400i,700,700i') {
        return $translation;
    }
    return 'off';
}
add_filter( 'gettext_with_context', 'xintheme_remove_gutenberg_styles',10, 4);
//禁止加载谷歌字体结束！
 //去除头部多余加载信息
remove_action( 'wp_head', 'wp_generator' );//移除WordPress版本
remove_action( 'wp_head', 'rsd_link' );//移除离线编辑器开放接口
remove_action( 'wp_head', 'wlwmanifest_link' );//移除离线编辑器开放接口
remove_action( 'wp_head', 'index_rel_link' );//去除本页唯一链接信息
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); //清除前后文信息
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );//清除前后文信息
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );//清除前后文信息
remove_action( 'wp_head', 'feed_links', 2 );//移除文章和评论feed
remove_action( 'wp_head', 'feed_links_extra', 3 ); //移除分类等feed
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 ); //移除wp-json
remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); //头部的JS代码
add_filter( 'show_admin_bar', '__return_false' );//移除wp-json链接
remove_action( 'wp_head', 'rel_canonical' ); //rel=canonical
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 ); //rel=shortlink
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );//移除emoji载入js
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );//emoji载入js
remove_action( 'wp_print_styles', 'print_emoji_styles' );//移除emoji载入css
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action('wp_head','wp_resource_hints',2);//移除dns-prefetch