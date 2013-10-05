<?php
 /**
 * remove head info By SingleX
 */
	foreach(array('rsd_link','index_rel_link','start_post_rel_link', 'wlwmanifest_link', 'parent_post_rel_link', 'adjacent_posts_rel_link_wp_head' ) as $xx)
	remove_action('wp_head',$xx); 
	
	include_once('inc/setting.php');

/**
 * Sets up the content width value based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 625;

/**
 * Sets up theme defaults and registers the various WordPress features that
 * Twenty Twelve supports.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To add a Visual Editor stylesheet.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links,
 * 	custom background, and post formats.
 * @uses register_nav_menu() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_setup() {
	/*
	 * Makes Twenty Twelve available for translation.
	 *
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Twenty Twelve, use a find and replace
	 * to change 'twentytwelve' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'twentytwelve', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// This theme supports a variety of post formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'link', 'quote', 'status' ) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'twentytwelve' ) );

	/*
	 * This theme supports custom background color and image, and here
	 * we also set up the default background color.
	 */
	add_theme_support( 'custom-background', array(
		'default-color' => 'e6e6e6',
	) );

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 624, 9999 ); // Unlimited height, soft crop
}
add_action( 'after_setup_theme', 'twentytwelve_setup' );

/**
 * Adds support for a custom header image.
 */
require( get_template_directory() . '/inc/custom-header.php' );

/**
 * Enqueues scripts and styles for front-end.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_scripts_styles() {
	global $wp_styles;

	/*
	 * Adds JavaScript to pages with the comment form to support
	 * sites with threaded comments (when in use).
	 */
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/*
	 * Adds JavaScript for handling the navigation menu hide-and-show behavior.
	 */
	wp_enqueue_script( 'twentytwelve-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0', true );

	/*
	 * Loads our special font CSS file.
	 *
	 * The use of Open Sans by default is localized. For languages that use
	 * characters not supported by the font, the font can be disabled.
	 *
	 * To disable in a child theme, use wp_dequeue_style()
	 * function mytheme_dequeue_fonts() {
	 *     wp_dequeue_style( 'twentytwelve-fonts' );
	 * }
	 * add_action( 'wp_enqueue_scripts', 'mytheme_dequeue_fonts', 11 );
	 */

	/* translators: If there are characters in your language that are not supported
	   by Open Sans, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'twentytwelve' ) ) {
		$subsets = 'latin,latin-ext';

		/* translators: To add an additional Open Sans character subset specific to your language, translate
		   this to 'greek', 'cyrillic' or 'vietnamese'. Do not translate into your own language. */
		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)', 'twentytwelve' );

		if ( 'cyrillic' == $subset )
			$subsets .= ',cyrillic,cyrillic-ext';
		elseif ( 'greek' == $subset )
			$subsets .= ',greek,greek-ext';
		elseif ( 'vietnamese' == $subset )
			$subsets .= ',vietnamese';

		$protocol = is_ssl() ? 'https' : 'http';
		$query_args = array(
			'family' => 'Open+Sans:400italic,700italic,400,700',
			'subset' => $subsets,
		);
		wp_enqueue_style( 'twentytwelve-fonts', add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );
	}

	/*
	 * Loads our main stylesheet.
	 */
	wp_enqueue_style( 'twentytwelve-style', get_stylesheet_uri() );

	/*
	 * Loads the Internet Explorer specific stylesheet.
	 */
	wp_enqueue_style( 'twentytwelve-ie', get_template_directory_uri() . '/css/ie.css', array( 'twentytwelve-style' ), '20121010' );
	$wp_styles->add_data( 'twentytwelve-ie', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'twentytwelve_scripts_styles' );

/**
 * Creates a nicely formatted and more specific title element text
 * for output in head of document, based on current view.
 *
 * @since Twenty Twelve 1.0
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string Filtered title.
 */
function twentytwelve_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'twentytwelve' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'twentytwelve_wp_title', 10, 2 );

/**
 * Makes our wp_nav_menu() fallback -- wp_page_menu() -- show a home link.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentytwelve_page_menu_args' );

/**
 * Registers our main widget area and the front page widget areas.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_widgets_init() {
	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'twentytwelve' ),
		'id' => 'sidebar-1',
		'description' => __( 'Appears on posts and pages except the optional Front Page template, which has its own widgets', 'twentytwelve' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'First Front Page Widget Area', 'twentytwelve' ),
		'id' => 'sidebar-2',
		'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', 'twentytwelve' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Second Front Page Widget Area', 'twentytwelve' ),
		'id' => 'sidebar-3',
		'description' => __( 'Appears when using the optional Front Page template with a page set as Static Front Page', 'twentytwelve' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'twentytwelve_widgets_init' );

if ( ! function_exists( 'twentytwelve_content_nav' ) ) :
/**
 * Displays navigation to next/previous pages when applicable.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_content_nav( $html_id ) {
	global $wp_query;

	$html_id = esc_attr( $html_id );

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
			<div class="nav-previous alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentytwelve' ) ); ?></div>
			<div class="nav-next alignright"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) ); ?></div>
		</nav><!-- #<?php echo $html_id; ?> .navigation -->
	<?php endif;
}
endif;

if ( ! function_exists( 'twentytwelve_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentytwelve_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
		// Display trackbacks differently than normal comments.
	?>
	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
		<p><?php _e( 'Pingback:', 'twentytwelve' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
		// Proceed with normal comments.
		global $post;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<div class="comment-gravatar">
				<?php echo get_avatar( $comment, 44 );?>
			</div>
			<div class="comment-text">
				<div class="comment-info">
					<span class="fn"><?php printf( '%1$s %2$s', get_comment_author_link(), ( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author', 'twentytwelve' ) . '</span>' : '');?> : </span>
					<?php comment_text();?>
				</div>
				<div class="entry-meta">
					<span class="comment-date"><?php comment_date('Y/m/d') ?> <?php comment_time('H:i'); ?> <?php edit_comment_link( __( 'Edit', 'twentytwelve' ) ); ?></span>
					<span class="comment-reply"><?php comment_reply_link( array_merge( $args, array('reply_text' => __( 'Reply', 'twentytwelve' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) );?></span>
					<?php if ( '0' == $comment->comment_approved ) : ?>
					<div class="clear"></div>
					<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentytwelve' ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</article><!-- #comment-## -->
	<?php
		break;
	endswitch; // end comment_type check
}
endif;

if ( ! function_exists( 'twentytwelve_entry_meta' ) ) :
/**
 * Prints HTML with meta information for current post: categories, tags, permalink, author, and date.
 *
 * Create your own twentytwelve_entry_meta() to override in a child theme.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_entry_meta() {
	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'twentytwelve' ) );

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ' , ', 'twentytwelve' ) );
	
	// Translators: 1 is category, 2 is tag, 3 is the date and 4 is the author's name.
	/*修改文章信息显示（1：分类；2：标签；3：日期；4：作者）*/
	$utility_text = __( '<span class="info-category"><i class="icon-folder-open"></i> %1$s</span><span class="info-tags"><i class="icon-tags"></i> %2$s</span>', 'twentytwelve' );
	printf(
		$utility_text,
		$categories_list,
		$tag_list
	);
}
endif;

/**
 * Extends the default WordPress body class to denote:
 * 1. Using a full-width layout, when no active widgets in the sidebar
 *    or full-width template.
 * 2. Front Page template: thumbnail in use and number of sidebars for
 *    widget areas.
 * 3. White or empty background color to change the layout and spacing.
 * 4. Custom fonts enabled.
 * 5. Single or multiple authors.
 *
 * @since Twenty Twelve 1.0
 *
 * @param array Existing class values.
 * @return array Filtered class values.
 */
function twentytwelve_body_class( $classes ) {
	$background_color = get_background_color();

	if ( ! is_active_sidebar( 'sidebar-1' ) || is_page_template( 'page-templates/full-width.php' ) )
		$classes[] = 'full-width';

	if ( is_page_template( 'page-templates/front-page.php' ) ) {
		$classes[] = 'template-front-page';
		if ( has_post_thumbnail() )
			$classes[] = 'has-post-thumbnail';
		if ( is_active_sidebar( 'sidebar-2' ) && is_active_sidebar( 'sidebar-3' ) )
			$classes[] = 'two-sidebars';
	}

	if ( empty( $background_color ) )
		$classes[] = 'custom-background-empty';
	elseif ( in_array( $background_color, array( 'fff', 'ffffff' ) ) )
		$classes[] = 'custom-background-white';

	// Enable custom font class only if the font CSS is queued to load.
	if ( wp_style_is( 'twentytwelve-fonts', 'queue' ) )
		$classes[] = 'custom-font-enabled';

	if ( ! is_multi_author() )
		$classes[] = 'single-author';

	return $classes;
}
add_filter( 'body_class', 'twentytwelve_body_class' );

/**
 * Adjusts content_width value for full-width and single image attachment
 * templates, and when there are no active widgets in the sidebar.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_content_width() {
	if ( is_page_template( 'page-templates/full-width.php' ) || is_attachment() || ! is_active_sidebar( 'sidebar-1' ) ) {
		global $content_width;
		$content_width = 960;
	}
}
add_action( 'template_redirect', 'twentytwelve_content_width' );

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @since Twenty Twelve 1.0
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 * @return void
 */
function twentytwelve_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
}
add_action( 'customize_register', 'twentytwelve_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since Twenty Twelve 1.0
 */
function twentytwelve_customize_preview_js() {
	wp_enqueue_script( 'twentytwelve-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), '20120827', true );
}
add_action( 'customize_preview_init', 'twentytwelve_customize_preview_js' );

function cut_str($sourcestr,$cutlength)
{
$returnstr="";
$i=0;
$n=0;
$str_length=strlen($sourcestr);
while (($n<$cutlength) and ($i<=$str_length))
{
$temp_str=substr($sourcestr,$i,1);
$ascnum=Ord($temp_str);
if ($ascnum>=224)
{
$returnstr=$returnstr.substr($sourcestr,$i,3); 
$i=$i+3;
$n++;
}
elseif ($ascnum>=192) 
{
$returnstr=$returnstr.substr($sourcestr,$i,2); 
$i=$i+2;
$n++;
}
elseif ($ascnum>=65 && $ascnum<=90)
{
$returnstr=$returnstr.substr($sourcestr,$i,1);
$i=$i+1;
$n++;
}
else
{
$returnstr=$returnstr.substr($sourcestr,$i,1);
$i=$i+1;
$n=$n+0.5;
}
}
if ($str_length>$cutlength){
$returnstr = $returnstr . "...";
}
return $returnstr;
}
/**
 * 站点信息统计，替代post-views插件
 * 
 * @author SingleX
 */
function lo_all_view(){ //本站总计浏览次数
    global $wpdb;
    $count=0;
    $views= $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key='views'"); 
    foreach($views as $key=>$value)
    {
        $meta_value=$value->meta_value;
        if($meta_value!='')
        {
            $count+=(int)$meta_value;
        }
    }
    return $count;
}
function get_post_views($post_id, $views=' Views') {//文章浏览次数
	$count_key = 'views';
	$count = get_post_meta($post_id, $count_key, true);
	if ($count == '') {
		delete_post_meta($post_id, $count_key);
		add_post_meta($post_id, $count_key, '0');
		$count = '0';
	}
	echo number_format_i18n($count) . $views;
}
function set_post_views() {
	global $post;
	$post_id = $post->ID;
	$count_key = 'views';
	$count = get_post_meta($post_id, $count_key, true);
	if (is_single() || is_page()) {
		if ($count == '') {
			delete_post_meta($post_id, $count_key);
			add_post_meta($post_id, $count_key, '0');
		} else {
			update_post_meta($post_id, $count_key, $count + 1);
		}
	}
}
add_action('get_header', 'set_post_views');

function theme2012plus_customize_fields($fields) { //评论框昵称，邮件，网址属性修改
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$fields =  array(
		'author' => '<p class="comment-form-author"><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' />' .
					'<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label></p>',
		'email'  => '<p class="comment-form-email"><input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' />' .
					'<label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label></p>',
		'url'    => '<p class="comment-form-url"><input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />' .
					'<label for="url">' . __( 'Website' ) . '</label></p>',
	);
	return $fields;
}
add_filter('comment_form_default_fields', 'theme2012plus_customize_fields');

add_filter('smilies_src','theme2012plus_smilies_src',1,10);function theme2012plus_smilies_src ($img_src, $img, $siteurl){return get_bloginfo('template_directory').'/images/smilies/'.$img;}
function wp_smilies() { //添加自定义表情
	global $wpsmiliestrans;
	if ( !get_option('use_smilies') or (empty($wpsmiliestrans))) return;
	$smilies = array_unique($wpsmiliestrans);
	$link='';
	foreach ($smilies as $key => $smile) {
		$file = get_bloginfo('template_directory').'/images/smilies/'.$smile;
		$value = " ".$key." ";
		$img = "<img src=\"{$file}\" alt=\"{$smile}\"/>";
		$imglink = htmlspecialchars($img);
		$link .= "<a style=\"cursor:pointer\" onclick=\"grin('{$key}')\">{$img}</a>&nbsp;";
	}
    return $link;
}

// archives.php 存档页代码
function theme2012plus_archives_list() {
 global $wpdb,$month;
 $lastpost = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_date <'" . current_time('mysql') . "' AND post_status='publish' AND post_type='post' AND post_password='' ORDER BY post_date DESC LIMIT 1");
 $output = get_option('SHe_archives_'.$lastpost);
 if(empty($output)){
	 $output = '';
	 $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'SHe_archives_%'");
	 $q = "SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month, count(ID) as posts FROM $wpdb->posts p WHERE post_date <'" . current_time('mysql') . "' AND post_status='publish' AND post_type='post' AND post_password='' GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC";
	 $monthresults = $wpdb->get_results($q);
	 if ($monthresults) {
		 foreach ($monthresults as $monthresult) {
		 $thismonth    = zeroise($monthresult->month, 2);
		 $thisyear    = $monthresult->year;
		 $q = "SELECT ID, post_date, post_title, comment_count FROM $wpdb->posts p WHERE post_date LIKE '$thisyear-$thismonth-%' AND post_date AND post_status='publish' AND post_type='post' AND post_password='' ORDER BY post_date DESC";
		 $postresults = $wpdb->get_results($q);
		 if ($postresults) {
			 $text = sprintf('%s %d', $month[zeroise($monthresult->month,2)], $monthresult->year);
			 $postcount = count($postresults);
			 $output .= '<ul class="archives-list"><li><span class="archives-yearmonth">' . $text . ' &nbsp;(' . count($postresults) . '&nbsp;篇文章)</span><ul class="archives-monthlisting">' . "\n";
			 foreach ($postresults as $postresult) {
				 if ($postresult->post_date != '0000-00-00 00:00:00') {
				 $url = get_permalink($postresult->ID);
				 $arc_title    = $postresult->post_title;
				 if ($arc_title)
					 $text = wptexturize(strip_tags($arc_title));
				 else
					 $text = $postresult->ID;
					 $title_text = 'View this post, &quot;' . wp_specialchars($text, 1) . '&quot;';
					 $output .= '<li>' . mysql2date('d日', $postresult->post_date) . '&nbsp;:&nbsp;' . "<a href='$url' title='$title_text'>$text</a>";
					 $output .= '&nbsp;(' . $postresult->comment_count . ')';
					 $output .= '</li>' . "\n";
				 }
			 }
		 }
		 $output .= '</ul></li></ul>' . "\n";
		 }
	 update_option('SHe_archives_'.$lastpost,$output);
	 }else{
		 $output = '<div class="errorbox">Sorry, no posts matched your criteria.</div>' . "\n";
	 }
 }
 echo $output;
}

/* 小工具们~~~~ */

if( function_exists( 'register_sidebar_widget' ) ) {   
	register_sidebar_widget('Theme2012Plus·分类目录','widget_category'); //显示双栏分类目录
}
function widget_category() { include(TEMPLATEPATH . '/inc/widget_category.php'); }

//热点文章
class singlex_widget extends WP_Widget {
     function singlex_widget() {
         $widget_ops = array('description' => '按阅读量显示全站范围的文章');
         $this->WP_Widget('singlex_widget', 'Theme2012Plus·热点文章', $widget_ops);
     }
     function widget($args, $instance) {
         extract($args);
         $title = apply_filters('widget_title',esc_attr($instance['title']));
         $limit = strip_tags($instance['limit']);
         echo $before_widget.$before_title.$title.$after_title;
?> 
         <ul>
            <?php
			global $wpdb, $post;
			$mode = '';
			$term_id = 0;
			$aftercount = 'Views';
			$output = '';
			$mode = ($mode == '') ? 'post' : $mode;
			$type_sql = ($mode != 'both') ? "AND post_type='$mode'" : '';
			$term_sql = (is_array($term_id)) ? "AND $wpdb->term_taxonomy.term_id IN (" . join(',', $term_id) . ')' : ($term_id != 0 ? "AND $wpdb->term_taxonomy.term_id = $term_id" : '');
			$term_sql.= $term_id ? " AND $wpdb->term_taxonomy.taxonomy != 'link_category'" : '';
			$inr_join = $term_id ? "INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)" : '';
			// database query
			$most_viewed = $wpdb->get_results("SELECT ID, post_date, post_title, (meta_value+0) AS views FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) $inr_join WHERE post_status = 'publish' AND post_password = '' $term_sql $type_sql AND meta_key = 'views' GROUP BY ID ORDER BY views DESC LIMIT $limit");
			if ($most_viewed) {
				foreach ($most_viewed as $viewed) {
					$post_ID    = $viewed->ID;
					$post_views = number_format($viewed->views);
					$post_title = esc_attr($viewed->post_title);
					$get_permalink = esc_attr(get_permalink($post_ID));
					$output .= "<li><a href=\"$get_permalink\" title=\"$post_title  $post_views $aftercount\">$post_title</a></li>";
				}
			} else {
				$output = "<li>N/A</li>";
			}
			echo $output;
            ?>
        </ul>
<?php         
         echo $after_widget;
     }
     function update($new_instance, $old_instance) {
         if (!isset($new_instance['submit'])) {
             return false;
         }
         $instance = $old_instance;
         $instance['title'] = strip_tags($new_instance['title']);
         $instance['limit'] = strip_tags($new_instance['limit']);
         return $instance;
     }
     function form($instance) {
         global $wpdb;
         $instance = wp_parse_args((array) $instance, array('title'=> '','limit' => ''));
         $title = esc_attr($instance['title']);
         $limit = strip_tags($instance['limit']);
 ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>">标题：<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p> 
        <p><label for="<?php echo $this->get_field_id('limit'); ?>">数量：<input id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>" /></label></p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
 <?php
     }
 }
 add_action('widgets_init', 'singlex_widget_init');
 function singlex_widget_init() {
     register_widget('singlex_widget');
 }
 
//最近评论
class doofer_widget extends WP_Widget {
     function doofer_widget() {
         $widget_ops = array('description' => '显示最近的读者评论（带头像）');
         $this->WP_Widget('doofer_widget', 'Theme2012Plus·最近评论', $widget_ops);
     }
     function widget($args, $instance) {
         extract($args);
         $title = apply_filters('widget_title',esc_attr($instance['title']));
         $limit = strip_tags($instance['limit']);
		 $email = strip_tags($instance['email']);
         echo $before_widget.$before_title.$title.$after_title;
?> 
         <ul>
            <?php
                global $wpdb;
                $limit_num = $limit;
                //$my_email = "'" . get_bloginfo ('admin_email') . "'";
				$my_email ="'" . $email . "'";
                $rc_comms = $wpdb->get_results("SELECT ID, post_title, comment_ID, comment_author,comment_author_email,comment_date,comment_content FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID  = $wpdb->posts.ID) WHERE comment_approved = '1' AND comment_type = '' AND post_password = '' AND comment_author_email != $my_email ORDER BY comment_date_gmt DESC LIMIT $limit_num ");
                $rc_comments = '';
                foreach ($rc_comms as $rc_comm) { $rc_comments .= "<li class=\"recentcommli\">
				<div class='clearfix'>
					<span class='recentcommentsavatar'>" . get_avatar($rc_comm,$size='32') ."</span>
					<a href='". get_comment_link($rc_comm->comment_ID) . "' title='在《" . $rc_comm->post_title .  "》发表的评论'>". $rc_comm->comment_content."</a>
					<br/>
					<span class=\"recentcomments-date\">".mysql2date('Y-m-d', $rc_comm->comment_date)."</span>
					<span class=\"recentcomments-author\"> by ".$rc_comm->comment_author."</span>
				</div></li>\n";}
				$rc_comments = convert_smilies($rc_comments);
                echo $rc_comments;
            ?>
        </ul>
<?php         
         echo $after_widget;
     }
     function update($new_instance, $old_instance) {
         if (!isset($new_instance['submit'])) {
             return false;
         }
         $instance = $old_instance;
         $instance['title'] = strip_tags($new_instance['title']);
         $instance['limit'] = strip_tags($new_instance['limit']);
		 $instance['email'] = strip_tags($new_instance['email']);
         return $instance;
     }
     function form($instance) {
         global $wpdb;
         $instance = wp_parse_args((array) $instance, array('title'=> '','limit' => '', 'email' => ''));
         $title = esc_attr($instance['title']);
         $limit = strip_tags($instance['limit']);
		 $email = strip_tags($instance['email']);
 ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>">标题：<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p> 
        <p><label for="<?php echo $this->get_field_id('limit'); ?>">数量：<input id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id('email'); ?>">排除以下邮箱的回复（留空则不排除）: <br><input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="text" value="<?php echo $email; ?>" /></label></p>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
 <?php
     }
 }
 add_action('widgets_init', 'doofer_widget_init');
 function doofer_widget_init() {
     register_widget('doofer_widget');
 }
 
 