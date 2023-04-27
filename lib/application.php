<?php 

//ini_set("display_errors", 1);
//flush_rewrite_rules(true);

// Applications ----------------------------------------
// ------------------------------------------------
add_action( 'init', 'wp_att_socio_application_create_post_type' );
function wp_att_socio_application_create_post_type() {
	$labels = array(
		'name'               => __( 'Applications', 'wp-att-socio' ),
		'singular_name'      => __( 'Application', 'wp-att-socio' ),
		'add_new'            => __( 'Add new', 'wp-att-socio' ),
		'add_new_item'       => __( 'Add new application', 'wp-att-socio' ),
		'edit_item'          => __( 'Edit application', 'wp-att-socio' ),
		'new_item'           => __( 'New application', 'wp-att-socio' ),
		'all_items'          => __( 'All applications', 'wp-att-socio' ),
		'view_item'          => __( 'View application', 'wp-att-socio' ),
		'search_items'       => __( 'Search application', 'wp-att-socio' ),
		'not_found'          => __( 'Application not found', 'wp-att-socio' ),
		'not_found_in_trash' => __( 'Application not found in trash bin', 'wp-att-socio' ),
		'menu_name'          => __( 'Applications', 'wp-att-socio' ),
	);
	$args = array(
		'labels'        => $labels,
		'description'   => __( 'Add new application', 'wp-att-socio' ),
		'public'        => true,
		'menu_position' => 7,
		'taxonomies' 		=> array('type', 'status'),
		'supports'      => array( 'title', 'editor'/*, 'thumbnail', 'page-attributes'*/ ),
		'rewrite'	      => array( 'slug' => 'application', 'with_front' => false),
		'query_var'	    => true,
		'has_archive' 	=> false,
		'hierarchical'	=> true,
  	'exclude_from_search' => true,
  	'capabilities' => array(
        'publish_posts' => 'publish_applications',
        'edit_posts' => 'edit_applications',
        'edit_others_posts' => 'edit_others_applications',
        'delete_posts' => 'delete_applications',
        'delete_others_posts' => 'delete_others_applications',
        'read_private_posts' => 'read_private_applications',
        'edit_post' => 'edit_applications',
        'delete_post' => 'delete_applications',
        'read_post' => 'read_applications'
		)
	);
	register_post_type( 'application', $args );
}

//Type -------------------------
add_action( 'init', 'wp_att_socio_application_type_create_type' );
function wp_att_socio_application_type_create_type() {
	$labels = array(
		'name'              => __( 'Types', 'wp-att-socio' ),
		'singular_name'     => __( 'Type', 'wp-att-socio' ),
		'search_items'      => __( 'Search type', 'wp-att-socio' ),
		'all_items'         => __( 'All types', 'wp-att-socio' ),
		'parent_item'       => __( 'Parent type', 'wp-att-socio' ),
		'parent_item_colon' => __( 'Parent type', 'wp-att-socio' ).":",
		'edit_item'         => __( 'Edit type', 'wp-att-socio' ),
		'update_item'       => __( 'Update type', 'wp-att-socio' ),
		'add_new_item'      => __( 'Add type', 'wp-att-socio' ),
		'new_item_name'     => __( 'New type', 'wp-att-socio' ),
		'menu_name'         => __( 'Types', 'wp-att-socio' ),
	);
	$args = array(
		'labels' 		        => $labels,
		'hierarchical' 	    => true,
		'public'		        => true,
		'query_var'		      => true,
		'show_in_nav_menus' => false,
		'has_archive'       => false,
    'rewrite'           => array( 'slug' => 'type', 'with_front' => false),
    'publicly_queryable' => false,
    'publicly_queryable' => false,
				'capabilities' => array(
				'manage_terms' => 'manage_types',
				'edit_terms' => 'edit_types',
				'delete_terms' => 'delete_types',
				'assign_terms' => 'assign_types',
		)
	);
  register_taxonomy( 'type', array('application'), $args );
}

function wp_att_socio_application_type_fields($tag) {
	$term_meta = get_option( "taxonomy_".$tag->term_id);
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><?php _e('Image', 'wp-att-socio'); ?></th>
		<td>
			<input type="text" name="term_meta[image]" id="term_meta[image]" size="3" style="width: 100%;" value="<?php echo $term_meta['image'] ? $term_meta['image'] : ''; ?>">
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top"><?php _e('Contact emails (separated by commas)', 'wp-att-socio'); ?></th>
		<td>
			<input type="text" name="term_meta[contact_emails]" id="term_meta[contact_emails]" size="3" style="width: 100%;" value="<?php echo $term_meta['contact_emails'] ? $term_meta['contact_emails'] : ''; ?>">
		</td>
	</tr>
	<?php
}

add_action( 'type_edit_form_fields', 'wp_att_socio_application_type_fields', 10, 2);
 
function wp_att_socio_application_type_save_fields( $term_id ) {
	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id");
		$cat_keys = array_keys($_POST['term_meta']);
		foreach ($cat_keys as $key){
			if (isset($_POST['term_meta'][$key]) && $_POST['term_meta'][$key] != ''){
				$term_meta2[$key] = $_POST['term_meta'][$key];
			}
		}
		update_option( "taxonomy_$t_id", $term_meta2 );
	}
}
add_action( 'edited_type', 'wp_att_socio_application_type_save_fields', 10, 2);

//Status -------------------------
add_action( 'init', 'wp_att_socio_application_status_create_type' );
function wp_att_socio_application_status_create_type() {
	$labels = array(
		'name'              => __( 'Status', 'wp-att-socio' ),
		'singular_name'     => __( 'Status', 'wp-att-socio' ),
		'search_items'      => __( 'Search status', 'wp-att-socio' ),
		'all_items'         => __( 'All status', 'wp-att-socio' ),
		'parent_item'       => __( 'Parent status', 'wp-att-socio' ),
		'parent_item_colon' => __( 'Parent status', 'wp-att-socio' ).":",
		'edit_item'         => __( 'Edit status', 'wp-att-socio' ),
		'update_item'       => __( 'Update status', 'wp-att-socio' ),
		'add_new_item'      => __( 'Add status', 'wp-att-socio' ),
		'new_item_name'     => __( 'New status', 'wp-att-socio' ),
		'menu_name'         => __( 'Status', 'wp-att-socio' ),
	);
	$args = array(
		'labels' 		        => $labels,
		'hierarchical' 	    => true,
		'public'		        => true,
		'query_var'		      => true,
		'show_in_nav_menus' => false,
		'has_archive'       => false,
    'rewrite'           => array( 'slug' => 'status', 'with_front' => false),
    'publicly_queryable' => false,
				'capabilities' => array(
				'manage_terms' => 'manage_status',
				'edit_terms' => 'edit_status',
				'delete_terms' => 'delete_status',
				'assign_terms' => 'assign_status',
		)
	);
	register_taxonomy( 'status', array('application'), $args );
}

//CAMPOS personalizados ---------------------------
// ------------------------------------------------
function wp_att_socio_get_application_custom_fields() {
	global $post;
	$fields = array(
		'partner_number' => array ('titulo' => __( 'Partner number', 'wp-att-socio' ), 'tipo' => 'text'),
		'partner_name' => array ('titulo' => __( 'Name', 'wp-att-socio' ), 'tipo' => 'text'),
		'partner_phone' => array ('titulo' => __( 'Telephone', 'wp-att-socio' ), 'tipo' => 'text'),
		'partner_email' => array ('titulo' => __( 'Email', 'wp-att-socio' ), 'tipo' => 'email'),
		'notes' => array ('titulo' => __( 'Notes', 'wp-att-socio' ), 'tipo' => 'textarea'),
		'partner_file' => array ('titulo' => __( 'Attached File', 'wp-att-socio' ), 'tipo' => 'imageview'),
	);
	return $fields;
}

function wp_att_socio_application_add_custom_fields() {
  add_meta_box(
    'box_activities', // $id
    __('Application Data', 'wp-att-socio'), // $title 
    'wp_att_socio_show_custom_fields', // $callback
    'application', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'wp_att_socio_application_add_custom_fields');
add_action('save_post', 'wp_att_socio_save_custom_fields' );


//Respuesta ------------------
function wp_att_socio_application_response() {
  add_meta_box(
    'box_response', // $id
    __('Response', 'wp-att-socio'), // $title 
    'wp_att_socio_show_response', // $callback
    'application', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'wp_att_socio_application_response');

function wp_att_socio_show_response() { //Show box
  global $post;
  $terms = [WP_ATT_SOCIO_CLOSED_STATUS];
  foreach(get_term_children( WP_ATT_SOCIO_CLOSED_STATUS, 'status', 'status' ) as $term) $terms[] = $term->term_id;
  
  if (has_term($term, 'status', $post->ID )){ ?>
  	<div><?php echo get_post_meta($post->ID, '_application_response_text', true); ?></div>
		<div><b><?php echo apply_filters("the_content", get_post_meta($post->ID, '_application_response_date', true)); ?></b></div>
  <?php } else { ?>
		<a href="<?php echo get_admin_url(); ?>admin.php?page=wp-att-socio&application_id=<?php echo $post->ID ?>" class="button"><?php _e('Response', 'wp-att-socio'); ?></a>
  <?php }
}

add_action( 'admin_head', function () { ?>
	<style>
	#toplevel_page_wp-att-socio {
		display: none;
	}
	</style>
<?php } );

//Columnas , filtros y ordenaciones ------------------------------------------------
function wp_att_socio_application_set_custom_edit_columns($columns) {
  $columns['type-application'] = __( 'Type', 'wp-att-socio');
  $columns['status-application'] = __( 'Status', 'wp-att-socio');
	$columns['closed-date-application'] = __( 'Closed date', 'wp-att-socio');
	return $columns;
}

function wp_att_socio_application_custom_column( $column ) {
  global $post;
  if ($column == 'type-application') {
    $terms = get_the_terms( $post->ID, 'type'); 
    $string = array();
    if(is_array($terms) && count($terms) > 0) {
			$sorted_terms = sort_terms_hierarchically( $terms );
		  foreach($sorted_terms as $term) {
		    $string[] = $term->name;
		  }
    }
    if(count($string) > 0) echo implode (", ", $string);
  } else if ($column == 'status-application') {
    $terms = get_the_terms( $post->ID, 'status'); 
		$string = array();
    if(is_array($terms) && count($terms) > 0) {
			$sorted_terms = sort_terms_hierarchically( $terms );
		  foreach($sorted_terms as $term) {
		    $string[] = $term->name;
		  }
		}
    if(count($string) > 0) echo implode (", ", $string);
  } else if ($column == 'closed-date-application') {
		echo get_post_meta($post->ID, '_application_response_date', true);

	}
}

function wp_att_socio_application_type_post_by_taxonomy() {
	global $typenow;
	$post_type = 'application'; // change to your post type
	$taxonomy  = 'type'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'hierarchical' 		=> 1,
			'show_option_all' => __('Show all', 'wp-att-socio' ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => true,
		));
	};
}

function wp_att_socio_application_type_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'application'; // change to your post type
	$taxonomy  = 'type'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}

function wp_att_socio_application_status_post_by_taxonomy() {
	global $typenow;
	$post_type = 'application'; // change to your post type
	$taxonomy  = 'status'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'hierarchical' 		=> 1,
			'show_option_all' => __('Show all', 'wp-att-socio' ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => true,
			'depth'						=> 5,
		));
	};
}

function wp_att_socio_application_status_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'application'; // change to your post type
	$taxonomy  = 'status'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}

//Los hooks si estamos en el admin 
if ( is_admin() && 'edit.php' == $pagenow && isset($_GET['post_type']) && 'application' == $_GET['post_type'] ) {
  add_filter( 'manage_edit-application_columns', 'wp_att_socio_application_set_custom_edit_columns' ); //Metemos columnas
  add_action( 'manage_application_posts_custom_column' , 'wp_att_socio_application_custom_column', 'category' ); //Metemos columnas

  add_action( 'restrict_manage_posts', 'wp_att_socio_application_type_post_by_taxonomy' ); //Añadimos filtro tipo
  add_filter( 'parse_query', 'wp_att_socio_application_type_id_to_term_in_query' ); //Añadimos filtro tipo
  add_action( 'restrict_manage_posts', 'wp_att_socio_application_status_post_by_taxonomy' ); //Añadimos filtro marca
  add_filter( 'parse_query', 'wp_att_socio_application_status_id_to_term_in_query' ); //Añadimos filtro marca
  
	add_filter( 'months_dropdown_results', '__return_empty_array' ); //Quitamos el filtro de fechas en el admin
}


add_action('pre_get_posts', 'wp_att_socio_default_order', 99);

//Orden por defecto de las solicitudes
function wp_att_socio_default_order($query) {
	global $pagenow;
  if ( is_admin() && 'edit.php' == $pagenow && isset($_GET['post_type']) && 'application' == $_GET['post_type']) {
    if (!isset($_GET['orderby'])) {
        $query->set('orderby', 'date');
    }
    if (!isset($_GET['order'])) {
        $query->set('order', 'DESC');
    }
  }
}


// Códigos cortos --------------------------------------
function wp_att_socio_shortcode ($params = array(), $content = null) {
  global $post, $datos_sesion;
	//print_r($datos_sesion['user']);

  $html = "<form id='partner-attention' method='post' enctype='multipart/form-data'>";
  if (isset($_REQUEST['application_create']) && $_REQUEST['application_create'] != '') {
  
  	$subterm = get_term($_REQUEST['application_type'], "type");
  	$term = get_term($subterm->parent, "type");
  	$types = [
			$subterm->term_id,
			$term->term_id
		];
  	if($term->parent > 0) {
  		$grandterm = get_term($term->parent, "type");
  		$types[] = $grandterm->term_id;
  	}
  	$application = array(
			'post_title' => chop(sprintf (__("%s %s %s submitted by %s (%s)", "wp-att-socio"), (isset($grandterm) ? $grandterm->name : ""), $term->name, $subterm->name, $_REQUEST['application_partner_name'], $_REQUEST['application_partner_email'])), 
			'post_content' => $_REQUEST['application_text'],
			'post_status' => 'publish',
			'post_type' => 'application',
			/*'tax_input' => [
				"type" => $types,
				"status" => [WP_ATT_SOCIO_DEFAULT_STATUS]
			],*/
		);
		$post_id = wp_insert_post($application);
		wp_set_post_terms( $post_id, $types, 'type');
		wp_set_post_terms( $post_id, [WP_ATT_SOCIO_DEFAULT_STATUS], 'status');
		update_post_meta( $post_id, '_application_partner_number', $_REQUEST['application_partner_number']);
		update_post_meta( $post_id, '_application_partner_name', $_REQUEST['application_partner_name']);
		update_post_meta( $post_id, '_application_partner_phone', $_REQUEST['application_partner_phone']);
		update_post_meta( $post_id, '_application_partner_email', $_REQUEST['application_partner_email']);


		//Subimos el documento adjunto.
		if ($_FILES["application_partner_file"]["error"] == UPLOAD_ERR_OK) {
			$uploads_dir = wp_upload_dir()['basedir']."/wp-att-socio/".$post_id."/";
			if(!is_dir($uploads_dir)) wp_mkdir_p($uploads_dir);
			$uploads_url= wp_upload_dir()['baseurl']."/wp-att-socio/".$post_id."/";
			move_uploaded_file($_FILES["application_partner_file"]["tmp_name"], $uploads_dir.$_FILES["application_partner_file"]["name"]);
			update_post_meta( $post_id, '_application_partner_file', $uploads_url.$_FILES["application_partner_file"]["name"]);
			$message_attached_file = "<br/><br/><a href='".$uploads_url.$_FILES["application_partner_file"]["name"]."'>".__("Attached File", 'wp-att-socio')."</a>";
		}
		
		//Mandamos el email de aviso.
		$term_meta = get_option( "taxonomy_".$subterm->term_id);
		foreach(explode(",", $term_meta['contact_emails']) as $email) {
			$email_content = str_replace("\n", "<br/>", $_REQUEST['application_text'])."<br/><br/>---------------<br/><br/>".
				__("Name", 'wp-att-socio').": ".get_post_meta($post_id, '_application_partner_name', true)."<br/>".
				__("Partner number", 'wp-att-socio').": ".get_post_meta($post_id, '_application_partner_number', true)."<br/>".
				__("Telephone", 'wp-att-socio').": ".get_post_meta($post_id, '_application_partner_phone', true)."<br/>".
				__("Email", 'wp-att-socio').": ".get_post_meta($post_id, '_application_partner_email', true)."<br/><br/>".
				"<a href='".get_edit_post_link($post_id)."'>".__("View application", 'wp-att-socio')."</a>".
				(isset($message_attached_file) ? $message_attached_file : "");
			$message = file_get_contents(dirname(__FILE__)."/../emails/body.html");
			$message = str_replace("[MESSAGE]", $email_content, $message);
			wp_mail(trim($email), __("[Partner Attention Portal]", 'wp-att-socio')." ".get_the_title($post_id), $message, get_mail_headers());
		}
		
  	$html = "<center><h2>".__("Thanks you, your application has been received.", 'wp-att-socio')."</h2><h3>".__("As soon as possible we will send you a response.", 'wp-att-socio')."</h3></center>";
  	
  	//Mandamos un email al usuario
		$message = file_get_contents(dirname(__FILE__)."/../emails/body.html");
		$message = str_replace("[MESSAGE]", "<center><h1>".__("Thanks you, your application has been received.", 'wp-att-socio')."</h1><h2>".__("As soon as possible we will send you a response.", 'wp-att-socio')."<h2></center>", $message);
		wp_mail(get_post_meta($post_id, '_application_partner_email', true), __("[Partner Attention Portal]", 'wp-att-socio')." ".__("We have received your appliation", 'wp-att-socio'), $message, get_mail_headers());
  } else {
		$types = get_terms([
		  'taxonomy' => 'type',
		  'hide_empty' => false,
		  'parent' => 0,
			'orderby' => 'slug',
			'order' => 'ASC'
		]);
		$html .= "<ul>";
		foreach ($types as $type) {
			$term_meta = get_option( "taxonomy_".$type->term_id);
			$html .= "<li id='tab-".$type->term_id."' style='background-image: url(".$term_meta['image'].");'><h3>".$type->name."</h3><button>".$type->description."</button>";
			$subtypes = get_terms([
				'taxonomy' => 'type',
				'hide_empty' => false,
				'parent' => $type->term_id,
				'orderby' => 'slug',
				'order' => 'ASC'
			]);
			/*foreach ($subtypes as $subtype) {
			
				$subsubtypes= get_terms([
					'taxonomy' => 'type',
					'hide_empty' => false,
					'parent' => $subtype->term_id,
					'orderby' => 'slug',
					'order' => 'ASC'
				]);
				if(is_array($subsubtypes) && count($subsubtypes) > 0) { 
					$html .= "<li>".$subtype->name."<ul>";
					foreach ($subsubtypes as $subsubtype) {
						$html .= "<li><label><input type='radio' name='application_type' value='".$subsubtype->term_id."'> ".$subsubtype->name."</label></li>";
					}
					$html .= "</ul></li>";
				} else $html .= "<li><label><input type='radio' name='application_type' value='".$subtype->term_id."'> ".$subtype->name."</label></li>";
			}*/
			$html .= "<select name='application_type'>";
			$html .= "<option value='' selected='true' disabled='disabled'>".__("Select area", 'wp-att-socio')."</option>";
			foreach ($subtypes as $subtype) {
				
				$subsubtypes= get_terms([
					'taxonomy' => 'type',
					'hide_empty' => false,
					'parent' => $subtype->term_id,
					'orderby' => 'slug',
					'order' => 'ASC'
				]);
				if(is_array($subsubtypes) && count($subsubtypes) > 0) { 
					$html .= "<optgroup label='".$subtype->name."'>";
					foreach ($subsubtypes as $subsubtype) {
						$html .= "<option value='".$subsubtype->term_id."'>".$subsubtype->name."</option>";

					}
					$html .= "</optgroup>";
				} else $html .= "<option value='".$subtype->term_id."' style='font-weight: bold;'>".$subtype->name."</option>";
			}
			$html .= "</select>";

			$html .= "</li>";
		}
		$html .= "</ul>";
		$html .= "<div><div><label>".__("Application", 'wp-att-socio')."<br/><textarea name='application_text' rows='10'></textarea></label></div>";
		$html .= "<div><label>".__("Partner number", 'wp-att-socio')."<br/><input type='text' name='application_partner_number' value='".$datos_sesion['user']['login']."' required ></label></div>";
		$html .= "<div><label>".__("Name", 'wp-att-socio')."<br/><input type='text' name='application_partner_name' value='".$datos_sesion['user']['nomabo']." ".$datos_sesion['user']['apeabo']."' required ></label></div>";	
		$html .= "<div><label>".__("Telephone", 'wp-att-socio')."<br/><input type='text' name='application_partner_phone' required ></label></div>";	
		$html .= "<div><label>".__("Email", 'wp-att-socio')."<br/><input type='email' name='application_partner_email' required ></label></div>";	
		$html .= "<div><label>".__("Attach an image/document", 'wp-att-socio')."<br/><small>".__("Supported formats: gif,jpg,jpeg,png,doc,docx,pdf", 'wp-att-socio')."</small><input type='file' name='application_partner_file' accept='.gif,.jpg,.jpeg,.png,.doc,.docx,.pdf'></label></div>";				
		$html .= "<br/><input type='submit' name='application_create' value='".__("Send", 'wp-att-socio')."' /></div>";
		$html .= "</form>";
  }
  $html .= "<style>
  #partner-attention > ul {
		display: flex;
    align-content: flex-start;
    justify-content: space-between;
    align-items: flex-start;
		flex-wrap: wrap;
		gap: 20px;
    padding: 0;
    margin: 0 0 20px 0;
	}

	#partner-attention > ul > li {
    width: 100%;
    display: block;
    cursor: pointer;
    border: 1px solid #cecece;
    border-radius: 10px;
    padding: 20px;
    position: relative;
    padding-top: 150px;
    background: #ffffff none center 28px no-repeat;
    background-size: 90px auto;
		box-sizing: border-box;
	}

	@media (min-width: 600px) {
		#partner-attention > ul > li {
			width: calc(33% - 12px);
		}
	}

	#partner-attention > ul > li > h3 {
		text-align: center;
		color: #382884;
		font-weight: 400;
		text-transform: uppercase;
		font-size: 24px;
		line-height: 100%; 
	}

	#partner-attention > ul > li > p {
		text-align: center;
		font-size: 26px;
		line-height: 120%; 
	}
	
	#partner-attention > ul > li > button {
    margin: 20px auto 10px;
    display: block;
    background-color: #382884;
    color: #ffffff;
    font-weight: 700;
    font-size: 15px;
    line-height: 100%;
    border-radius: 30px;
    padding: 20px;
	}

	#partner-attention > ul > li > select {
		display: none;
		margin: 20px auto;

	}

	/*#partner-attention > ul > li.opened {
		background-color: #cecece;
	}*/

	#partner-attention > ul > li.opened > select {
		display: block;
	}
  
	#partner-attention > div {
		display: none;
		margin: auto;
		border: 1px solid #cecece;
		border-radius: 10px;
		padding: 20px;
	}

	@media (min-width: 600px) {
		#partner-attention > div {
			max-width: 50%;
		}
	}

	#partner-attention > div.opened {
		display: block;
	}

	#partner-attention > div input,
	#partner-attention > div textarea {
		width: 100%;
    margin-bottom: 15px !important;
	}
	
  </style>"; 
  $html .= "<script>
	var current = '';
  jQuery('#partner-attention > ul > li > button').click(function(e) {
		e.preventDefault();
		current = jQuery(this).parent().attr('id');
		jQuery('#partner-attention > ul > li').not('#partner-attention > ul > li#'+current).removeClass('opened');
		jQuery('#partner-attention > ul > li > select').not('#partner-attention > ul > li#'+current+' > select').prop('selectedIndex',0);
		jQuery(this).parent().addClass('opened');
		jQuery('#partner-attention > div').removeClass('opened');
	});

	jQuery('#partner-attention > ul > li > select').on('change', function (e) {
		console.log('select');
		jQuery('#partner-attention > div').addClass('opened');
		jQuery('html,body').animate({
				scrollTop: jQuery('#partner-attention > div').offset().top - 200
		},'slow');
	});

  </script>"; 
  return $html; 
}
add_shortcode('att-socio', 'wp_att_socio_shortcode');

// Libs ----------------------------------------
function sort_terms_hierarchically($terms) {
	usort($terms, "cmp");
	return $terms;
}

function cmp($a, $b) {
	return strcmp($a->parent, $b->parent);
}

//Export to CSV ---------------------
function wp_att_socio_export_to_csv() {
  if (isset($_GET['post_type']) && $_GET['post_type'] == 'application' && isset($_GET['csv']) && $_GET['csv'] == 'true') {

		$query = new WP_Query(array(
				'post_type' => 'application',
				'post_status' => 'publish',
				'orderby' => 'date',
				'order' => 'DESC'
		));

		$csv = "Fecha,Tipo 1,Tipo2,Tipo 3,Estado 1,Estado 2,Estado 3,Nº de socio,Nombre,Teléfono,Email,Asunto,Fecha respuesta,Respuesta\n";

		while ($query->have_posts()) {
			$query->the_post();
			$post_id = get_the_id();
			
		  $terms = get_the_terms($post_id, 'type');
		  $string = array();
		  if(is_array($terms) && count($terms) > 0) {
		  	$sorted_terms = sort_terms_hierarchically( $terms );
		  	foreach($sorted_terms as $term) {
		  	  $string[] = $term->name;
		  	}
		  }
			while(count($string) < 3) $string[] = "";
		  if(count($string) > 0) $type = implode (",", $string);

		  $terms = get_the_terms($post_id, 'status');
		  $string = array();
		  if(is_array($terms) && count($terms) > 0) { 
				$sorted_terms = sort_terms_hierarchically( $terms );
				foreach($sorted_terms as $term) {
				  $string[] = $term->name;
				}
			}
			while(count($string) < 3) $string[] = "";
		  if(count($string) > 0) $status = implode (",", $string);
			
			
			$csv .= get_the_date("Y-m-d H:i:s").",".$type.",".$status.",".
				get_post_meta($post_id, '_application_partner_number', true).",".
				get_post_meta($post_id, '_application_partner_name', true).",".
				get_post_meta($post_id, '_application_partner_phone', true).",".
				get_post_meta($post_id, '_application_partner_email', true).",\"".
				addslashes(str_replace("<br/>", "\n", get_the_content($post_id)))."\",".
				get_post_meta($post_id, '_application_response_date', true).",\"".
				addslashes(str_replace("<br/>", "\n", get_post_meta($post_id, '_application_response_text', true)))."\"\n";

		}
		wp_reset_query();
		
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		// force download
		header("Content-Description: File Transfer");
		header("Content-Encoding: UTF-8");
		header("Content-Type: text/csv; charset=UTF-8");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");

		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename=atencion-socio.csv");
		header("Content-Transfer-Encoding: binary");
		echo $csv;
		die;
  }
}
add_action( 'admin_init', 'wp_att_socio_export_to_csv', 1 );

add_action('manage_posts_extra_tablenav', function($which) {
    if (get_current_screen()->post_type !== 'application'){
        return;
    }
    echo '<a href="'.get_admin_url().'edit.php?post_type=application&csv=true" class="button">'.__("Export to CSV", 'wp-att-socio').'</a>';
});






//Administrador  -> response application 

add_filter('page_row_actions', 'wp_att_socio_response_link', 10, 2);
function wp_att_socio_response_link($actions, $post) {
	if ($post->post_type =="application" && !has_term( WP_ATT_SOCIO_CLOSED_STATUS, 'status', $post->ID )){
		$actions['application_response'] = '<a href="'.get_admin_url().'admin.php?page=wp-att-socio&application_id='.$post->ID.'" class="google_link">' . __('Response', 'wp-att-socio') . '</a>';
	}
	return $actions;
}


add_action( 'admin_menu', 'wp_att_socio_plugin_menu' );
function wp_att_socio_plugin_menu() {
	add_menu_page( __('Applications', 'wp-att-socio'), __('Applications', 'wp-att-socio'), 'edit_applications', 'wp-att-socio', 'wp_att_socio_page_settings');
}

function wp_att_socio_page_settings() { 
	$post = get_post($_REQUEST['application_id']);
	if ($post->post_type =="application" && !has_term( WP_ATT_SOCIO_CLOSED_STATUS, 'statuspadding: 20px;', $post->ID )){
		?><h1><?php _e("Response", 'wp-att-socio'); ?></h1><?php 
		if(isset($_REQUEST['send']) && $_REQUEST['send'] != '') { 

			$message = file_get_contents(dirname(__FILE__)."/../emails/body.html");
			$message = str_replace("[MESSAGE]", str_replace("\n", "<br/>", $_REQUEST['_application_response_text']), $message);
			if(wp_mail(get_post_meta($post->ID, '_application_partner_email', true), __("[Partner Attention Portal]", 'wp-att-socio')." ".sprintf(__("Response to your application created at %s", 'wp-att-socio'), get_the_date("Y-m-d H:i:s", $post->ID)), $message, get_mail_headers())) {
				update_post_meta($post->ID, '_application_response_text',  str_replace("\n", "<br/>", $_REQUEST['_application_response_text']));
				update_post_meta($post->ID, '_application_response_date', date("Y-m-d H:i:s"));
				wp_delete_object_term_relationships($post->ID, 'status');
				wp_set_object_terms($post->ID, WP_ATT_SOCIO_CLOSED_STATUS, 'status', true);
				?>
					<h1 style="border: 1px solid green; color: green; text-align: center; padding: 20px;"><?php _e("Response sent correctly", 'wp-att-socio'); ?></h1>
					<a href="<?php echo get_edit_post_link($post->ID); ?>" class="button"><?php _e("View application", 'wp-att-socio'); ?></a>
				<?php		
			} else {
				?>
					<h1 style="border: 1px solid green; color: green; text-align: center; padding: 20px;"><?php _e("An error occurred while trying to send the email. Please try again later.", 'wp-att-socio'); ?></h1>
					<a href="<?php echo get_edit_post_link($post->ID); ?>" class="button"><?php _e("View application", 'wp-att-socio'); ?></a>
				<?php		
			}
		} else { ?>
			<h2><?php echo $post->post_title; ?></h2>
			<pre style="padding: 10px; border: 1px solid #cecece; background-color: #fff;"><?php echo $post->post_content; ?></pre>
			<ul>
				<li><b><?php _e("Date", 'wp-att-socio'); ?>:</b> <?php echo get_the_date("Y-m-d H:i:s", $post->ID); ?></li>
				<li><b><?php _e("Partner number", 'wp-att-socio'); ?>:</b> <?php echo get_post_meta($post->ID, '_application_partner_number', true); ?></li>
				<li><b><?php _e("Name", 'wp-att-socio'); ?>:</b> <?php echo get_post_meta($post->ID, '_application_partner_name', true); ?></li>
				<li><b><?php _e("Telephone", 'wp-att-socio'); ?>:</b> <?php echo get_post_meta($post->ID, '_application_partner_phone', true); ?></li>
				<li><b><?php _e("Email", 'wp-att-socio'); ?>:</b> <?php echo get_post_meta($post->ID, '_application_partner_email', true); ?></li>
			</ul>
			
			
			<form method="post">
				<input type="hidden" name="application_id" value="<?php echo $post->ID; ?>" />
				<textarea name="_application_response_text" style="width: 100%;" rows="20" required>Estimado/a <?php echo get_post_meta($post->ID, '_application_partner_name', true); ?>,&#13;&#10;&#13;&#10;En relación a:&#13;&#10;&#13;&#10;-----&#13;&#10;<?php echo $post->post_content; ?>&#13;&#10;-----&#13;&#10;&#13;&#10;Le podemos decir que:&#13;&#10;&#13;&#10;&#13;&#10;&#13;&#10;Atentamente,&#13;&#10;La dirección del Real Club Jolaseta</textarea>
				<input type="submit" name="send" class="button button-primary" value="<?php _e("Send response", 'wp-att-socio'); ?>" />
			</form>
		<?php }
	}
}








// Shortcode para formulario login oficina virtual.
add_shortcode('wp_att_socio_login', function ($atts, $content, $tag) {
  global $cKplugin, $cKjolaseta, $conf_global, $datos_sesion;
	ob_start(); 
	// Recepción de prg
	$prg = $cKplugin->cKodea->prg_data_get('_prg', get_current_user_id());
	$msg = (isset($prg['msg']))? $prg['msg'] : false;

	/*if ($datos_sesion['noticia_msg']) {
		$datos_sesion['noticia_msg'] = false;
		$msg = msg_prg('login-noticia');
	}*/ ?>
<script>
  cKodea.jQuery(function ($) {
    // Muestra notificaciones
    $('._notify').fadeIn();

    // Oculta notificaciones
    $(document).on('click', '._notify-cerrar', function (e) {
      e.preventDefault();
      $(e.target.closest('._notify')).fadeOut();
    });
  });
</script>
<div><?= notify($msg); ?></div>
<?php // nuevo nivel de buffer 
	if($datos_sesion['login'] == 1) { ?>
		<div>
			<h4 style="text-align: center; margin-bottom: 30px;"><?php echo sprintf (__("Welcome to our partner attention zone <strong>%s</strong>", 'wp-att-socio'), $datos_sesion['user']['nomabo']." ".$datos_sesion['user']['apeabo']); ?></h4>
			<?php echo do_shortcode("[att-socio]"); ?>
		</div>
	<?php } else { ?>
	<form action="<?= \Ilunabar\PLUGIN_AJAX; ?>" method="post" class="formulario">
		<?= wp_nonce_field('ckodea_form_prg', '_wpnonce', true, false); ?>
		<input type="hidden" name="action" value="application_login">
		<fieldset>
			<legend><?php _e("Login to access to Partner attention zone.", 'wp-att-socio'); ?></legend>
			<div class="row _input">
				<span class="label"><?php _e("Parnert number (6 digits)", 'wp-att-socio'); ?> <span class="asterisco">*</span></span>
				<span class="formw" style="max-width: 69%;"><input type="text" name="usuario" value="" placeholder="<?php _e("Parnert number (6 digits)", 'wp-att-socio'); ?>*"></span>
			</div>
			<div class="row _input">
				<span class="label paddingnizq"><?php _e("Password", 'wp-att-socio'); ?> <span class="asterisco">*</span></span>
				<span class="formw" style="max-width: 69%;"><input type="text" name="contraseña" value="" placeholder="<?php _e("Password", 'wp-att-socio'); ?>*"></span>
			</div>
			<input type="submit" value="<?php _e("Login", 'wp-att-socio'); ?>">
			<a href="https://ovirtual.jolaseta.com/lostpass.php" id="boton-recuperar" target="_blank"><?php _e("Remenber password", 'wp-att-socio'); ?></a>
		</fieldset>
	</form>
	<?php }
  return ob_get_clean();
});

// Formulario de login
add_action('ckodea_ajax_application_login', function () {
  global $cKplugin, $cKjolaseta, $datos_sesion, $conf_global;


  // Comprobación de seguridad nonce, llegan datos del formulario
  if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'ckodea_form_prg')) {
    // NOTE: no se puede sanear con sanitize_text_field y tampoco hace falta
    $usuario    = (isset($_POST['usuario']))?    (string) sanitize_text_field($_POST['usuario'])    : '';
    $contraseña = (isset($_POST['contraseña']))? (string) sanitize_text_field($_POST['contraseña']) : '';
    $login_data = null;
    $options = [
      'trace'    => true,
      'location' => $cKjolaseta->conf_global['url']['wsdl'],
      'uri'      => $cKjolaseta->conf_global['url']['sweb']
    ];

    try {
    	$soap       = new \SoapClient(null, $options);
      $login_data = $soap->login($usuario, $contraseña);
    } catch(Exception $e) {
      $datos_sesion['login_error'] = $e->getMessage();
    } catch (\Throwable $e) {
      $datos_sesion['login_error'] = $e->getMessage();
    }

    if ($login_data && $login_data->valido) {
      $datos_sesion['login']   = true;
      $datos_sesion['user_id'] = $login_data->login;
      $datos_sesion['user']    = $login_data;
      $datos_sesion['numabo']  = $usuario;
      $datos_sesion['sem']     = md5($usuario.'jolaris');
			// Preparamos prg
			/*$prg = $cKplugin->cKodea->prg_data_save([
				'msg' => msg_prg('login-ok')
			], get_current_user_id());*/
			$cKplugin->cKodea->do_exit(303, get_the_permalink(WP_ATT_SOCIO_PAGE_ID).'?_prg='.$prg);


		} else {
			// Preparamos prg
      $prg = $cKplugin->cKodea->prg_data_save([
        'msg' => msg_prg('login-error')
      ], get_current_user_id());
      $cKplugin->cKodea->do_exit(303, get_the_permalink(WP_ATT_SOCIO_PAGE_ID).'?_prg='.$prg);
    }
  } else {
    // Preparamos prg
    $prg = $cKplugin->cKodea->prg_data_save([
      'msg' => msg_prg('sesion-caducada')
    ], get_current_user_id());
    $cKplugin->cKodea->do_exit(303, get_the_permalink(WP_ATT_SOCIO_PAGE_ID).'?_prg='.$prg);
  }
});


// Mensajes de respuesta prg
if(!function_exists("msg_prg")) {
	function msg_prg($msg = '') {
		$v = '';

		switch ($msg) {
			default:
			case 'sesion-caducada':
				$v = [
					'html' => _x('<p>La sesión ha caducado.</p>', 'login-registro ajax', 'jolaseta'),
					'tipo' => 'alerta'
				];
			break;

			case 'login-ok':
				$v = [
					'html' => _x('<p>El usuario y la contraseña son correctos.</p>', 'login-registro ajax', 'jolaseta'),
					'tipo' => 'ok'
				];
			break;

			case 'login-error':
				$v = [
					'html' => _x('<p>El usuario y/o la contraseña no son correctos.</p>', 'login-registro ajax', 'jolaseta'),
					'tipo' => 'ko'
				];
			break;

			case 'login-noticia':
				$v = [
					'html' => _x('<p>La noticia es privada, debe iniciar sesión para poder verla.</p>', 'login-registro ajax', 'jolaseta'),
					'tipo' => 'ko'
				];
			break;
		}

		return $v;
	}
}

//Notificador básico. Tipos: ok, ko, neutral, alerta
if(!function_exists("notify")) {
	function notify($msg = '') {
		$v = '';
		if (is_array($msg)) {
			return '<div class="notificacion-'.$msg['tipo'].' _notify"><div class="notificacion-parrafos">'.$msg['html'].'</div><i class="fas fa-times-circle _notify-cerrar"></i></div>';
		}
	}
}
