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

//CAMPOS personalizados
function wp_att_socio_get_survey_custom_fields() {
	global $post;
	$fields = array(
		'status' => array ('titulo' => __( 'Status', 'wp-att-socio' ), 'tipo' => 'select', 'valores' => [
      "1" => "Abierto",
      "2" => "Cerrado"
    ]),
	);
	return $fields;
}

function wp_att_socio_survey_add_custom_fields() {
  add_meta_box(
    'box_activities', // $id
    __('Survey Data', 'wp-att-socio'), // $title 
    'wp_att_socio_show_custom_fields', // $callback
    'survey', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'wp_att_socio_survey_add_custom_fields');
add_action('save_post', 'wp_att_socio_save_custom_fields' );


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


//CAMPOS personalizados
function wp_att_socio_get_question_custom_fields() {
	global $post;
	$fields = array(
    'options' => array ('titulo' => __( "Options", 'wp-att-socio' ), 'tipo' => 'repeater', "min" => 2, "max => 10", "fields" => array (
      'option' => array ('titulo' => __( "Text", 'bideurdin' ), 'tipo' => 'text')
    )),
	);
	return $fields;
}

function wp_att_socio_question_add_custom_fields() {
  add_meta_box(
    'box_activities', // $id
    __('Question Data', 'wp-att-socio'), // $title 
    'wp_att_socio_show_custom_fields', // $callback
    'question', // $page
    'normal', // $context
    'high'); // $priority
}
add_action('add_meta_boxes', 'wp_att_socio_question_add_custom_fields');
add_action('save_post', 'wp_att_socio_save_custom_fields' );
