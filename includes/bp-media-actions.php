<?php
/**
 * Handles the uploads and creates respective posts for the upload
 * 
 * @since BP Media 2.0
 */
function bp_media_handle_uploads() {
	global $bp,$bp_media_options;
	$bp_media_options = get_option('bp_media_options',array(
		'videos_enabled'	=>	true,
		'audio_enabled'		=>	true,
		'images_enabled'	=>	true,
	));
	if (isset($_POST['action']) && $_POST['action'] == 'wp_handle_upload') {
		if (isset($_FILES) && is_array($_FILES) && array_key_exists('bp_media_file', $_FILES) && $_FILES['bp_media_file']['name'] != '') {
			if(preg_match('/image/',$_FILES['bp_media_file']['type'])){
				if($bp_media_options['images_enabled']==false){
					$bp->{BP_MEDIA_SLUG}->messages['error'][] = __('Image uploads are disabled');
					return;
				}
			}
			else if(preg_match('/video/',$_FILES['bp_media_file']['type'])){
				if($bp_media_options['videos_enabled']==false){
					$bp->{BP_MEDIA_SLUG}->messages['error'][] = __('Video uploads are disabled');
					return;
				}
			}
			else if(preg_match('/audio/',$_FILES['bp_media_file']['type'])){
				if($bp_media_options['audio_enabled']==false){
					$bp->{BP_MEDIA_SLUG}->messages['error'][] = __('Audio uploads are disabled');
					return;
				}
			}
			$class_name = apply_filters('bp_media_transcoder','BP_Media_Host_Wordpress');
			$bp_media_entry = new $class_name();
			try {
				$title = isset($_POST['bp_media_title']) ? ($_POST['bp_media_title'] != "") ? $_POST['bp_media_title'] : pathinfo($_FILES['bp_media_file']['name'], PATHINFO_FILENAME) : pathinfo($_FILES['bp_media_file']['name'], PATHINFO_FILENAME);
				$entry = $bp_media_entry->add_media($title, array_key_exists('bp_media_description',$_POST)?$_POST['bp_media_description']:'');
				if(!isset($bp->{BP_MEDIA_SLUG}->messages['updated'][0]))
					$bp->{BP_MEDIA_SLUG}->messages['updated'][0] = __('Upload Successful', 'bp-media');
			} catch (Exception $e) {
				$bp->{BP_MEDIA_SLUG}->messages['error'][] = $e->getMessage();
			}
		} else {
			$bp->{BP_MEDIA_SLUG}->messages['error'][] = __('You did not specified a file to upload', 'bp-media');
		}
	}
}
//add_action('bp_init', 'bp_media_handle_uploads');

/**
 * Displays the messages that other functions/methods creates according to the BuddyPress' formating
 * 
 * @since BP Media 2.0
 */
function bp_media_show_messages() {
	global $bp;
	if (is_array($bp->{BP_MEDIA_SLUG}->messages)) {
		$types = array('error', 'updated', 'info');
		foreach ($types as $type) {
			if (count($bp->{BP_MEDIA_SLUG}->messages[$type]) > 0) {
				bp_media_show_formatted_error_message($bp->{BP_MEDIA_SLUG}->messages[$type], $type);
			}
		}
	}
}
add_action('bp_media_before_content', 'bp_media_show_messages');

/**
 * Enqueues all the required scripts and stylesheets for the proper working of BuddyPress Media.
 * 
 * @since BP Media 2.0
 */
function bp_media_enqueue_scripts_styles() {
    
	wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('bp-media-mejs', plugins_url('includes/media-element/mediaelement-and-player.min.js', dirname(__FILE__)));
	wp_enqueue_script('bp-media-default', plugins_url('includes/js/bp-media.js', dirname(__FILE__)));
	global $bp;
	
	$bp_media_vars = array( 
		'ajaxurl' => admin_url( 'admin-ajax.php'),
		'page'	=> 1,
		'current_action' => isset($bp->current_action)?$bp->current_action:false,
		'action_variables' =>	isset($bp->action_variables)?$bp->action_variables:false,
		'displayed_user' => bp_displayed_user_id(),
		'loggedin_user'	=> bp_loggedin_user_id()
	);
	wp_localize_script( 'bp-media-default', 'bp_media_vars', $bp_media_vars );
    wp_enqueue_style('bp-media-mecss', plugins_url('includes/media-element/mediaelementplayer.min.css', dirname(__FILE__)));
	wp_enqueue_style('bp-media-default', plugins_url('includes/css/bp-media-style.css', dirname(__FILE__)));
	
}

add_action('wp_enqueue_scripts', 'bp_media_enqueue_scripts_styles', 11);

/**
 * Deletes associated media entry and its files upon deletion of an activity.
 * 
 * @since BP Media 2.0
 */
function bp_media_delete_activity_handler($activity_id, $user) {
	global $bp_media_count;
	bp_media_init_count(bp_loggedin_user_id());
	$post_id = bp_activity_get_meta($activity_id, 'bp_media_parent_post');
	$type = get_post_meta($post_id, 'bp_media_type', true);
	switch ($type) {
		case 'image':
			$bp_media_count['images'] = intval($bp_media_count['images']) - 1;
			break;
		case 'video':
			$bp_media_count['videos'] = intval($bp_media_count['videos']) - 1;
			break;
		case 'audio':
			$bp_media_count['audio'] = intval($bp_media_count['audio']) - 1;
			break;
	}
	$attachment_id = get_post_meta($post_id, 'bp_media_child_attachment', true);
	wp_delete_attachment($attachment_id, true);
	wp_delete_post($post_id, true);
	bp_update_user_meta(bp_loggedin_user_id(), 'bp_media_count', $bp_media_count);
}

/* Adds bp_media_delete_activity_handler() function to be called on bp_activity_before_action_delete_activity hook */
//add_action('bp_before_activity_delete', 'bp_media_delete_activity_handler', 1, 2);
//The above hook isn't at right place yet, so skipped it till its corrected
//add_action('bp_activity_before_action_delete_activity', 'bp_media_delete_activity_handler', 10, 2);

function bp_media_delete_activity_handler_old($args){
	global $bp_media_count,$wpdb;
	
	if(!array_key_exists('id', $args))
		return;
	
	$activity_id=$args['id'];
	$query="SELECT post_id from $wpdb->postmeta WHERE meta_key='bp_media_child_activity' AND meta_value=$activity_id";
	$result=$wpdb->get_results($query);
	if(!(is_array($result)&& count($result)==1 ))
		return;
	$post_id=$result[0]->post_id;
	try{
		$media = new BP_Media_Host_Wordpress($post_id);
		$media->delete_media();
	}
	catch(Exception $e){
		error_log('Media tried to delete was already deleted');
	}
}
add_action('bp_before_activity_delete', 'bp_media_delete_activity_handler_old');

/**
 * Called on bp_init by screen functions
 * 
 * @uses global $bp, $bp_media_query
 * 
 * @since BP Media 2.0
 */
function bp_media_set_query() {
	global $bp, $bp_media_query;
	switch ($bp->current_action) {
		case BP_MEDIA_IMAGES_SLUG:
			$type = 'image';
			break;
		case BP_MEDIA_AUDIO_SLUG:
			$type = 'audio';
			break;
		case BP_MEDIA_VIDEOS_SLUG:
			$type = 'video';
			break;
		default :
			$type = null;
	}
	if (isset($bp->action_variables) && is_array($bp->action_variables) && isset($bp->action_variables[0]) && $bp->action_variables[0] == 'page' && isset($bp->action_variables[1]) && is_numeric($bp->action_variables[1])) {
		$paged = $bp->action_variables[1];
	} else {
		$paged = 1;
	}
	if ($type) {
		$args = array(
			'post_type' => 'attachment',
			'post_status'	=>	'any',
			'post_mime_type' =>	$type,
			'author' => $bp->displayed_user->id,
			'meta_key' => 'bp-media-key',
			'meta_value' => $bp->displayed_user->id,
			'meta_compare' => 'LIKE',
			'paged' => $paged
		);
		$bp_media_query = new WP_Query($args);
	}
}

/**
 * Adds a download button and edit button on single entry pages of media files.
 * 
 * @since BP Media 2.0
 */
function bp_media_action_buttons() {
	if(!in_array('bp_media_current_entry', $GLOBALS))
		return false;
	global $bp_media_current_entry;
	if($bp_media_current_entry!=NULL){
		if(bp_displayed_user_id()==  bp_loggedin_user_id())	echo '<a href="'.$bp_media_current_entry->get_edit_url().'" class="button item-button bp-secondary-action bp-media-edit" title="Edit Media">Edit</a>';
		echo '<a href="'.$bp_media_current_entry->get_attachment_url().'" class="button item-button bp-secondary-action bp-media-download" title="Download">Download</a>';
		
	}
}
add_action('bp_activity_entry_meta', 'bp_media_action_buttons');

/* Should be used with Content Disposition Type for media files set to attachment */

/**
 * Shows the media count of a user in the tabs
 * 
 * @since BP Media 2.0
 */
function bp_media_init_count($user = null) {
	global $bp_media_count;
	if (!$user)
		$user = bp_displayed_user_id();
	if ($user < 1) {
		$bp_media_count = null;
		return false;
	}
	$count = bp_get_user_meta($user, 'bp_media_count', true);
	if (!$count) {
		$bp_media_count = array('images' => 0, 'videos' => 0, 'audio' => 0);
		bp_update_user_meta($user, 'bp_media_count', $bp_media_count);
	} else {
		$bp_media_count = $count;
	}
	add_filter('bp_get_displayed_user_nav_' . BP_MEDIA_SLUG, 'bp_media_items_count_filter', 10, 2);

	if (bp_current_component() == BP_MEDIA_SLUG) {
		add_filter('bp_get_options_nav_' . BP_MEDIA_IMAGES_SLUG, 'bp_media_items_count_filter', 10, 2);
		add_filter('bp_get_options_nav_' . BP_MEDIA_VIDEOS_SLUG, 'bp_media_items_count_filter', 10, 2);
		add_filter('bp_get_options_nav_' . BP_MEDIA_AUDIO_SLUG, 'bp_media_items_count_filter', 10, 2);
		add_filter('bp_get_options_nav_' . BP_MEDIA_ALBUMS_SLUG, 'bp_media_items_count_filter', 10, 2);
	}
	return true;
}
add_action('init', 'bp_media_init_count');

/**
 * Displays the footer of the BP Media Plugin if enabled through the dashboard options page
 * 
 * @since BP Media 2.0
 */
function bp_media_footer() { ?>
	<div id="bp-media-footer"><p>Using <a title="BuddyPress Media adds photos, video and audio upload/management feature" href="http://rtcamp.com/buddypress-media/">BuddyPress Media</a>.</p></div>
	<?php
}
if(get_option('bp_media_remove_linkback')!='1')
	add_action('bp_footer','bp_media_footer');

function bp_media_upload_enqueue(){
	$params=array(
		'url'=>plugins_url('bp-media-upload-handler.php',__FILE__),
		'runtimes'	=>	'gears,html5,flash,silverlight,browserplus',
		'browse_button'	=>	'bp-media-upload-browse-button',
		'container'	=>	'bp-media-upload-ui',
		'drop_element' =>	'drag-drop-area',
		'filters'	=>	array(array('title' => "Media Files",'extensions'=> "mp4,jpg,png,jpeg,gif,mp3")),
		'max_file_size'	=>	'100mb',
		'multipart'           => true,
		'urlstream_upload'    => true,
		'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
		'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
		'file_data_name'      => 'bp_media_file', // key passed to $_FILE.
		'multi_selection'		=> true,
		'multipart_params'	=> array('action'=>'wp_handle_upload')
	);
	wp_enqueue_script('bp-media-uploader',plugins_url('js/bp-media-uploader.js',__FILE__),array('plupload', 'plupload-html5', 'plupload-flash', 'plupload-silverlight', 'plupload-html4','plupload-handlers','jquery-ui-core','jquery-ui-widget','jquery-ui-position','jquery-ui-dialog'));
	wp_localize_script('bp-media-uploader','bp_media_uploader_params',$params);
	wp_enqueue_style('bp-media-default',plugins_url('css/bp-media-style.css',__FILE__));
	wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css'); 
}
//add_action('wp_enqueue_scripts','bp_media_upload_enqueue');
//This is used only on the uploads page so its added as action in the screens function of upload page.


/**
 * Called on bp_init by screen functions
 * 
 * @uses global $bp, $bp_media_albums_query
 * 
 * @since BP Media 2.2
 */
function bp_media_albums_set_query() {
	global $bp, $bp_media_albums_query;
	if (isset($bp->action_variables) && is_array($bp->action_variables) && isset($bp->action_variables[0]) && $bp->action_variables[0] == 'page' && isset($bp->action_variables[1]) && is_numeric($bp->action_variables[1])) {
		$paged = $bp->action_variables[1];
	} else {
		$paged = 1;
	}
	if ($bp->current_action == BP_MEDIA_ALBUMS_SLUG) {
		$args = array(
			'post_type' => 'bp_media_album',
			'author' => $bp->displayed_user->id,
			'paged' => $paged,
		);
		$bp_media_albums_query = new WP_Query($args);
	}
}

/**
 * Called on bp_init by screen functions
 * 
 * @uses global $bp, $bp_media_query
 * 
 * @since BP Media 2.2
 */
function bp_media_albums_set_inner_query($album_id=0) {
	global $bp, $bp_media_query;
	$paged = 0;
	$action_variables = $bp->canonical_stack['action_variables'];
	if (isset($action_variables) && is_array($action_variables) && isset($action_variables[0])) {
		if($action_variables[0] == 'page' && isset($action_variables[1]) && is_numeric($action_variables[1]))
			$paged = $action_variables[1];
		else if($action_variables[1] == 'page' && isset($action_variables[2]) && is_numeric($action_variables[2]))
			$paged = $action_variables[2];
	} 
	if(!$paged)
		$paged = 1;
	$args = array(
		'post_type' => 'attachment',
		'post_status'	=>	'any',
		'author' => $bp->displayed_user->id,
		'post_parent'=>$album_id,
		'paged' => $paged
	);
	$bp_media_query = new WP_Query($args);
}

/**
 * Function to return the media for the ajax requests
 */
function bp_media_load_more() {
	global $bp,$bp_media_query;
	$page = isset($_POST['page'])?$_POST['page']:die();
	$current_action = isset($_POST['current_action'])?$_POST['current_action']:null;
	$action_variables = isset($_POST['action_variables'])?$_POST['action_variables']:null;
	$displayed_user = isset($_POST['displayed_user'])?$_POST['displayed_user']:null;
	$loggedin_user = isset($_POST['loggedin_user'])?$_POST['loggedin_user']:null;
	if(!$displayed_user||intval($displayed_user)==0){
		die();
	}
	$posts_per_page = 10;
	switch($current_action){
		case BP_MEDIA_IMAGES_SLUG:
			$args = array(
				'post_type' => 'attachment',
				'post_status'	=>	'any',
				'post_mime_type' =>	'image',
				'author' => $bp->displayed_user->id,
				'meta_key' => 'bp-media-key',
				'meta_value' => $bp->displayed_user->id,
				'meta_compare' => 'LIKE',
				'paged' => $page,
				'posts_per_page' => $posts_per_page
			);
			break;			
		case BP_MEDIA_AUDIO_SLUG:
			$args = array(
				'post_type' => 'attachment',
				'post_status'	=>	'any',
				'post_mime_type' =>	'audio',
				'author' => $bp->displayed_user->id,
				'meta_key' => 'bp-media-key',
				'meta_value' => $bp->displayed_user->id,
				'meta_compare' => 'LIKE',
				'paged' => $page,
				'posts_per_page' => $posts_per_page
			);
			break;
		case BP_MEDIA_VIDEOS_SLUG:
			$args = array(
				'post_type' => 'attachment',
				'post_status'	=>	'any',
				'post_mime_type' =>	'video',
				'author' => $bp->displayed_user->id,
				'meta_key' => 'bp-media-key',
				'meta_value' => $bp->displayed_user->id,
				'meta_compare' => 'LIKE',
				'paged' => $page,
				'posts_per_page' => $posts_per_page
			);
			break;
		case BP_MEDIA_ALBUMS_SLUG:
			if(isset($action_variables)&&  is_array($action_variables)&&isset($action_variables[0])&&isset($action_variables[1])){
				$args = array(
					'post_type' => 'attachment',
					'post_status'	=>	'any',
					'author' => $displayed_user,
					'post_parent'=>$action_variables[1],
					'paged' => $page
				);
			}
			else{
				$args = array(
					'post_type' => 'bp_media_album',
					'author' => $displayed_user,
					'paged' => $page,
				);
			}
			break;
		default:
			die();
	}
	$bp_media_query = new WP_Query($args);
	if(isset($bp_media_query->posts)&&is_array($bp_media_query->posts)&&count($bp_media_query->posts)){
		foreach($bp_media_query->posts as $attachment){
			try{
				$media = new BP_Media_Host_Wordpress($attachment->ID);
				echo $media->get_media_gallery_content();
			}
			catch(exception $e){
				die();
			}
		}
	}
	die();
}
add_action('wp_ajax_bp_media_load_more', 'bp_media_load_more');
add_action( 'wp_ajax_nopriv_bp_media_load_more', 'bp_media_load_more' );


function bp_media_delete_attachment_handler($attachment_id){
	if(get_post_meta($attachment_id,'bp-media-key')){
		global $bp_media_count;
		$attachment = get_post($attachment_id);
		preg_match_all('/audio|video|image/i', $attachment->post_mime_type, $result);
		if(isset($result[0][0]))
			$type = $result[0][0];
		else
			return false;
		bp_media_init_count($attachment->post_author);
		switch($type){
			case 'image':
				$images = intval($bp_media_count['images'])?intval($bp_media_count['images']):0;
				$bp_media_count['images'] = $images - 1;
				break;
			case 'audio':
				$bp_media_count['audio'] = intval($bp_media_count['audio']) - 1;
				break;
			case 'video':
				$bp_media_count['videos'] = intval($bp_media_count['videos']) - 1;
				break;
			default:
				return false;
		}
		bp_update_user_meta($attachment->post_author, 'bp_media_count', $bp_media_count);
		return true;
	}
}
add_action('delete_attachment','bp_media_delete_attachment_handler');
?>