<?php

// Surveys ----------------------------------------
// ------------------------------------------------
add_action( 'init', 'wp_att_socio_survey_create_post_survey' );
function wp_att_socio_survey_create_post_survey() {
	$labels = array(
		'name'               => __( 'Surveys', 'wp-att-socio' ),
		'singular_name'      => __( 'Survey', 'wp-att-socio' ),
		'add_new'            => __( 'Add new', 'wp-att-socio' ),
		'add_new_item'       => __( 'Add new survey', 'wp-att-socio' ),
		'edit_item'          => __( 'Edit survey', 'wp-att-socio' ),
		'new_item'           => __( 'New survey', 'wp-att-socio' ),
		'all_items'          => __( 'All surveys', 'wp-att-socio' ),
		'view_item'          => __( 'View survey', 'wp-att-socio' ),
		'search_items'       => __( 'Search survey', 'wp-att-socio' ),
		'not_found'          => __( 'Survey not found', 'wp-att-socio' ),
		'not_found_in_trash' => __( 'Survey not found in trash bin', 'wp-att-socio' ),
		'menu_name'          => __( 'Surveys', 'wp-att-socio' ),
	);
	$args = array(
		'labels'        => $labels,
		'description'   => __( 'Add new survey', 'wp-att-socio' ),
		'public'        => true,
		'menu_position' => 7,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'rewrite'	      => array( 'slug' => 'survey', 'with_front' => false),
		'query_var'	    => true,
		'has_archive' 	=> false,
		'hierarchical'	=> true,
  	'exclude_from_search' => true,
  	/*'capabilities' => array(
        'publish_posts' => 'publish_surveys',
        'edit_posts' => 'edit_surveys',
        'edit_others_posts' => 'edit_others_surveys',
        'delete_posts' => 'delete_surveys',
        'delete_others_posts' => 'delete_others_surveys',
        'read_private_posts' => 'read_private_surveys',
        'edit_post' => 'edit_surveys',
        'delete_post' => 'delete_surveys',
        'read_post' => 'read_surveys'
		)*/
	);
	register_post_type( 'survey', $args );
}

//Columnas , filtros y ordenaciones ------------------------------------------------
function wp_att_socio_survey_set_custom_edit_columns($columns) {
  $columns['status'] = __( 'Status', 'wp-att-socio');
  $columns['questions'] = __( 'Questions', 'wp-att-socio');
	unset($columns['date']);
	$columns['date'] = __( 'Date');
	return $columns;
}

function wp_att_socio_survey_custom_column( $column ) {
  global $post;
	$temp = $post;
	if ($column == 'questions') {
		$args = array(
			'post_type' => 'question',
			'orderby' => 'menu_order',
			'meta_query' => array(
					array(
							'key' => '_question_survey',
							'value' => $post->ID,
							'compare' => '=',
					)
			)
		);
		//echo "<pre>"; print_r($args); echo "</pre>";
		$the_query = new WP_Query($args);
		if ($the_query->have_posts() ) {
			echo "<ol>";
			while ( $the_query->have_posts() ) { 
				$the_query->the_post();
				echo "<li><b>".get_the_title()."</b><br/><a href='".get_edit_post_link()."'>".__("Edit question", 'wp-att-socio')."</a></li>";
			}
			echo "</ol>";
		} else echo "<span style='color: red;'>".__("No questions associated", 'wp-att-socio')."</span>";
		wp_reset_query();
	} else if ($column == 'status') {
		//echo $post->ID."----".get_post_meta( $post->ID, '_survey_status', true );
		if(get_post_meta( $post->ID, '_survey_status', true ) == 1) echo "<span style='display: inline-block; padding: 10px; background-color: green; color: #fff;'>".__('Open', 'wp-att-socio' )."</span>"; 
		else echo "<span style='display: inline-block; padding: 10px; background-color: red; color: #fff;'>".__('Closed', 'wp-att-socio' )."</span>"; 
  }
	$psot = $temp;
}


//Los hooks si estamos en el admin 
if ( is_admin() && 'edit.php' == $pagenow && isset($_GET['post_type']) && 'survey' == $_GET['post_type'] ) {
  add_filter( 'manage_edit-survey_columns', 'wp_att_socio_survey_set_custom_edit_columns' ); //Metemos columnas
  add_action( 'manage_survey_posts_custom_column' , 'wp_att_socio_survey_custom_column', 'category' ); //Metemos columnas

  /*add_action( 'restrict_manage_posts', 'wp_att_socio_application_type_post_by_taxonomy' ); //Añadimos filtro tipo
  add_filter( 'parse_query', 'wp_att_socio_application_type_id_to_term_in_query' ); //Añadimos filtro tipo
  add_action( 'restrict_manage_posts', 'wp_att_socio_application_status_post_by_taxonomy' ); //Añadimos filtro marca
  add_filter( 'parse_query', 'wp_att_socio_application_status_id_to_term_in_query' ); //Añadimos filtro marca*/
  
	add_filter( 'months_dropdown_results', '__return_empty_array' ); //Quitamos el filtro de fechas en el admin
}

//CAMPOS personalizados
function wp_att_socio_get_survey_custom_fields() {
	global $post;
	$fields = array(
		'status' => array ('titulo' => __( 'Status', 'wp-att-socio' ), 'tipo' => 'select', 'valores' => [
      "1" => __('Open', 'wp-att-socio' ),
      "2" => __('Closed', 'wp-att-socio' )
    ]),
	);
	return $fields;
}

function wp_att_socio_survey_add_custom_fields() {
  add_meta_box(
    'box_surveys', // $id
    __('Survey Config', 'wp-att-socio'), // $title 
    'wp_att_socio_show_custom_fields', // $callback
    'survey', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'wp_att_socio_survey_add_custom_fields');
add_action('save_post', 'wp_att_socio_save_custom_fields' );

function wp_att_socio_survey_add_data() {
  add_meta_box(
    'box_surveys_data', // $id
    __('Survey Data', 'wp-att-socio'), // $title 
    'wp_att_socio_survey_show_data', // $callback
    'survey', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'wp_att_socio_survey_add_data');

function wp_att_socio_survey_show_data() {
	global $post;
	echo wp_att_socio_generate_survey_data($post->ID);
}


//Questions -------------------------
// ------------------------------------------------
add_action( 'init', 'wp_att_socio_survey_create_post_question' );
function wp_att_socio_survey_create_post_question() {
	$labels = array(
		'name'               => __( 'Questions', 'wp-att-socio' ),
		'singular_name'      => __( 'Question', 'wp-att-socio' ),
		'add_new'            => __( 'Add new', 'wp-att-socio' ),
		'add_new_item'       => __( 'Add new question', 'wp-att-socio' ),
		'edit_item'          => __( 'Edit question', 'wp-att-socio' ),
		'new_item'           => __( 'New question', 'wp-att-socio' ),
		'all_items'          => __( 'All questions', 'wp-att-socio' ),
		'view_item'          => __( 'View question', 'wp-att-socio' ),
		'search_items'       => __( 'Search question', 'wp-att-socio' ),
		'not_found'          => __( 'Question not found', 'wp-att-socio' ),
		'not_found_in_trash' => __( 'Question not found in trash bin', 'wp-att-socio' ),
		'menu_name'          => __( 'Questions', 'wp-att-socio' ),
	);
	$args = array(
		'labels'        => $labels,
		'description'   => __( 'Add new question', 'wp-att-socio' ),
		'public'        => true,
		'menu_position' => 7,
		'supports'      => array( 'title'/*, 'editor', 'thumbnail'*/, 'page-attributes' ),
		'rewrite'	      => array( 'slug' => 'question', 'with_front' => false),
		'query_var'	    => true,
		'has_archive' 	=> false,
		'hierarchical'	=> true,
  	'exclude_from_search' => true,
  	/*'capabilities' => array(
        'publish_posts' => 'publish_questions',
        'edit_posts' => 'edit_questions',
        'edit_others_posts' => 'edit_others_questions',
        'delete_posts' => 'delete_questions',
        'delete_others_posts' => 'delete_others_questions',
        'read_private_posts' => 'read_private_questions',
        'edit_post' => 'edit_questions',
        'delete_post' => 'delete_questions',
        'read_post' => 'read_questions'
		)*/
	);
	register_post_type( 'question', $args );
}

//Columnas , filtros y ordenaciones ------------------------------------------------
function wp_att_socio_question_set_custom_edit_columns($columns) {
  $columns['survey'] = __( 'Survey', 'wp-att-socio');
	unset($columns['date']);
	return $columns;
}

function wp_att_socio_question_custom_column( $column ) {
  global $post;
  if ($column == 'survey') {
    $survey_id = get_post_meta( $post->ID, '_question_survey', true );
		if($survey_id > 0) echo "<b>".get_the_title($survey_id)."</b><br/><a href='".get_edit_post_link($survey_id)."'>".__("Edit survey", 'wp-att-socio')."</a>";
		else echo "<span style='color: red;'>".__("No survey associated", 'wp-att-socio')."</span>";
  }
}

//Los hooks si estamos en el admin 
if ( is_admin() && 'edit.php' == $pagenow && isset($_GET['post_type']) && 'question' == $_GET['post_type'] ) {
  add_filter( 'manage_edit-question_columns', 'wp_att_socio_question_set_custom_edit_columns' ); //Metemos columnas
  add_action( 'manage_question_posts_custom_column' , 'wp_att_socio_question_custom_column', 'category' ); //Metemos columnas

  /*add_action( 'restrict_manage_posts', 'wp_att_socio_application_type_post_by_taxonomy' ); //Añadimos filtro tipo
  add_filter( 'parse_query', 'wp_att_socio_application_type_id_to_term_in_query' ); //Añadimos filtro tipo
  add_action( 'restrict_manage_posts', 'wp_att_socio_application_status_post_by_taxonomy' ); //Añadimos filtro marca
  add_filter( 'parse_query', 'wp_att_socio_application_status_id_to_term_in_query' ); //Añadimos filtro marca*/
  
	add_filter( 'months_dropdown_results', '__return_empty_array' ); //Quitamos el filtro de fechas en el admin
}


//CAMPOS personalizados
function wp_att_socio_get_question_custom_fields() {
	global $post;
	$fields = array(
    'options' => array ('titulo' => __( "Options", 'wp-att-socio' ), 'tipo' => 'repeater', "min" => 2, "max => 10", "fields" => array (
      'option' => array ('titulo' => __( "Text", 'wp-att-socio' ), 'tipo' => 'text'),
			'votes' => array ('titulo' => __( "Votes", 'wp-att-socio' ), 'tipo' => 'info', 'default' => 0),
			'opentext' => array ('titulo' => __( "Open text", 'wp-att-socio' ), 'tipo' => 'text'),
    )),
		'survey' => array ('titulo' => __( 'Survey', 'wp-att-socio' ), 'tipo' => 'select', "valores" => wp_att_socio_question_custom_fields_relatedsurveys()),


	);
	return $fields;
}

function wp_att_socio_question_custom_fields_relatedsurveys () {
	$surveys = array();
	$surveys[0] = __("Select survey", 'wp-att-socio');
  $args = array(
		'post_type' => 'survey',
		'posts_per_page' => -1,
		//'post_status' => 'publish,draft',
		'order' => 'ASC',
		//'post__not_in' => array(get_the_id()),
		'orderby' => 'meta_value_num post_title',  
	);
	foreach ( get_posts($args) as $post ) { 
		$id = $post->ID;
		$surveys[$id] = get_the_title($id);
	}
  return $surveys;
}


function wp_att_socio_question_add_custom_fields() {
  add_meta_box(
    'box_questions', // $id
    __('Question Data', 'wp-att-socio'), // $title 
    'wp_att_socio_show_custom_fields', // $callback
    'question', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'wp_att_socio_question_add_custom_fields');
add_action('save_post', 'wp_att_socio_save_custom_fields' );


function wp_att_socio_can_fill_survey($survey_id, $user_id) {
	$users = get_post_meta( $survey_id, '_survey_users', true );
	if(!is_array($users)) $users = [];
	if(get_post_meta( $survey_id, '_survey_status', true ) == 1 && !in_array($user_id, $users)) return true;
		else return false;
}


function wp_att_socio_generate_survey_data($survey_id) { //Mostramos los datos actuales de la encuesta.
		
	$html = "<h3 style='margin-top: 40px; font-weight: 700;'>".get_the_title($survey_id)." </h3>";
	$html .= apply_filters("the_content", get_post_field('post_content', $survey_id));

	$args = array(
		'post_type' => 'question',
		'orderby' => 'menu_order',
		'meta_query' => array(
				array(
						'key' => '_question_survey',
						'value' => $survey_id,
						'compare' => '=',
				)
		)
	);

	$the_query = new WP_Query($args);
	if ($the_query->have_posts() ) {
		$html .= "<ol style='margin-top: 20px; padding: 0px;'>";
		while ( $the_query->have_posts() ) { 
			$the_query->the_post();
			$question_id = get_the_id();
			$html .= "<li style='margin-bottom: 30px; padding: 0px;'><b>".get_the_title()."</b>";
			$options = get_post_meta($question_id , '_question_options', true );
			$total = 0;
			foreach ($options as $option) {
					$total = $option['votes'] + $total;
			}
			$html .= "<ul style='padding: 10px; margin-bottom: 5px;'>";
			foreach ($options as $option) {
				$html .= "<li>".$option['option']." (".($option['votes'] > 0 ? $option['votes'] : "0")." votos / ".($option['votes'] > 0 ? round(($option['votes']*100 / $total), 2) : "0")."% )</li>";
			}
			$html .= "</ul>";
			$html .= sprintf(__("<b>Total:</b> %d votes", 'wp-att-socio'), $total);
			$html .= "</li>";
		}
		$html .= "</ol>";
	}
	wp_reset_query();
	return $html;
}