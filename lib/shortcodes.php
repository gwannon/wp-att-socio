<?php

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




	} else if(isset($_REQUEST['send_survey']) && $_REQUEST['send_survey'] != '') { 
	
		/*echo "<pre>";
		print_r($_REQUEST);	
		print_r($datos_sesion);
		echo "</pre>";

		die;*/

		


		foreach ($_REQUEST['currentsurvey'] as $survey_id => $currentsurvey) {



			//Metemos al usuario como votante.
			if(wp_att_socio_can_fill_survey($survey_id, $datos_sesion['user_id'])) {
				$users = get_post_meta( $survey_id, '_survey_users', true );
				if(is_array($users)) $users[] = $datos_sesion['user_id'];
				else {
					$users = [];
					$users[] = $datos_sesion['user_id'];
				}
				update_post_meta( $survey_id, '_survey_users', $users);
				//Metemos sus votos
				foreach ($currentsurvey as $question_id => $option_id) {

					//Chequemaos que la pregunta pertenezca a la encuesta
					if(get_post_meta($question_id, '_question_survey', true ) == $survey_id) {
						$options = get_post_meta( $question_id , '_question_options', true );
						//Chequeamos que exista la opción
						if(isset($options[$option_id])) { 
							if(isset($options[$option_id]['votes'])) $options[$option_id]['votes'] ++;
							else $options[$option_id]['votes'] = 1;
							update_post_meta($question_id, '_question_options', $options);


							//Guardamos textos si existe 
							if(isset($_REQUEST['currentsurveyopentext'][$survey_id][$question_id])) {
								foreach($_REQUEST['currentsurveyopentext'][$survey_id][$question_id] as $option_id => $opentext) {
									if($opentext != '') {
										$opentexts = get_post_meta( $question_id , '_question_option_'.$option_id.'_opentexts', true );
										if(is_array($opentexts)) $opentexts[$option_id] = $opentext; 
										else {
											$opentexts = [];
											$opentexts[] = $opentext;
										}
										update_post_meta($question_id, '_question_option_'.$option_id.'_opentexts', $opentexts);
									}
								}
							}



						}
					}
				}



				$html = "<center><h2>".__("Thanks you, your answers has been received.", 'wp-att-socio')."</h2></center>";
			} else {
				$html = "<center><h2>".__("Sorry, you have already participated in this survey or the survey is closed.", 'wp-att-socio')."</h2></center>";
			}
			break;
		}
		
		//Mostramos los datos actuales de la encuesta.
		$html .= wp_att_socio_generate_survey_data($survey_id);
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
		
		$html .= "<li id='tab-survey' style='background-image: url(/wp-content/plugins/wp-att-socio/assets/images/encuesta.svg);'><h3>".__("The partner responds", 'wp-att-socio')."</h3><button>".__("Answer surveys", 'wp-att-socio')."</button>";
		$html .= "<select name='survey_type'>";
		$html .= "<option value='' selected='true' disabled='disabled'>".__("Select survey", 'wp-att-socio')."</option>";
		$surveys = get_posts(["post_type" => 'survey', "order_by" => "menu-order", "order" => "ASC", "post_status" => "publish"]);
		foreach ($surveys as $survey) {
			$html .= "<option value='survey-".$survey->ID."'>".get_the_title($survey->ID)." (".(get_post_meta($survey->ID, '_survey_status', true ) == 1 ? __("Open" , 'wp-att-socio') : __("Closed", 'wp-att-socio')).")</option>";
		}
		$html .= "</select>";
		$html .= "</li>";

		$html .= "</ul>";
		$html .= "<div><div><label>".__("Application", 'wp-att-socio')."<br/><textarea name='application_text' rows='10'></textarea></label></div>";
		$html .= "<div><label>".__("Partner number", 'wp-att-socio')."<br/><input type='text' name='application_partner_number' value='".$datos_sesion['user']['login']."' required ></label></div>";
		$html .= "<div><label>".__("Name", 'wp-att-socio')."<br/><input type='text' name='application_partner_name' value='".$datos_sesion['user']['nomabo']." ".$datos_sesion['user']['apeabo']."' required ></label></div>";	
		$html .= "<div><label>".__("Telephone", 'wp-att-socio')."<br/><input type='text' name='application_partner_phone' required ></label></div>";	
		$html .= "<div><label>".__("Email", 'wp-att-socio')."<br/><input type='email' name='application_partner_email' required ></label></div>";	
		$html .= "<div><label>".__("Attach an image/document", 'wp-att-socio')."<br/><small>".__("Supported formats: gif,jpg,jpeg,png,doc,docx,pdf", 'wp-att-socio')."</small><input type='file' name='application_partner_file' accept='.gif,.jpg,.jpeg,.png,.doc,.docx,.pdf'></label></div>";				
		$html .= "<br/><input type='submit' name='application_create' value='".__("Send", 'wp-att-socio')."' /></div>";
		$html .= "</form>";


		
		$surveys = get_posts(["post_type" => 'survey', "order_by" => "menu-order", "order" => "ASC", "post_status" => "publish"]);
		foreach ($surveys as $survey) {
			$html .= "<div id='survey-".$survey->ID."' class='survey'>";
			if(!wp_att_socio_can_fill_survey($survey->ID, $datos_sesion['user_id'])) { 
				$html .= wp_att_socio_generate_survey_data($survey->ID);
			} else {
				$html .= "<form id='survey-".$survey->ID."-form' method='post'><h2>".get_the_title($survey->ID)." </h2>";
				$html .= apply_filters("the_content", $survey->post_content);
				//$temp = $post;
				//Si no está cerrada y no ha participado
					$args = array(
						'post_type' => 'question',
						'orderby' => 'menu_order',
						'order' => 'ASC',
						'meta_query' => array(
								array(
										'key' => '_question_survey',
										'value' => $survey->ID,
										'compare' => '=',
								)
						)
					);
					$the_query = new WP_Query($args);
					if ($the_query->have_posts() ) {
						$html .= "<ol style='margin-top: 20px; padding: 0px;'>";
						while ( $the_query->have_posts() ) { 
							$the_query->the_post();
							$html .= "<li style='margin-bottom: 30px; padding: 0px;'><b>".get_the_title()."</b>";


							$options = get_post_meta( $post->ID, '_question_options', true );
							$total = 0;
							foreach ($options as $option) {
									$total = $option['votes'] + $total;
							}
							$html .= "<ul>";
							foreach ($options as $key => $option) {
								
								if(isset($option['opentext']) && $option['opentext'] != '') {
									$html .= "<li><label><input type='radio' data-show-opentext='#opentext-".$survey->ID."-".$post->ID."-".$key."' name=\"currentsurvey[".$survey->ID."][".$post->ID."]\" value='".$key."' required='required'> ".$option['option']."</label>";
								
									$html .="<div id='opentext-".$survey->ID."-".$post->ID."-".$key."' style='display: none;'>
										<b>".addslashes($option['opentext'])."</b><br/>
										<textarea rows='10' cols='50' name=\"currentsurveyopentext[".$survey->ID."][".$post->ID."][".$key."]\" maxlength='500' disabled='disabled'></textarea>
									</div>";
								} else {
									$html .= "<li><label><input type='radio' name=\"currentsurvey[".$survey->ID."][".$post->ID."]\" value='".$key."' required='required'> ".$option['option']."</label>";
								}
								
								
								$html .= "</li>";
							}
							$html .= "</ul>";
							$html .= "</li>";
						}
						$html .= "</ol>";
					}
					wp_reset_query();
					$html .= "<input type='submit' name='send_survey' value='".__("Send survey", 'wp-att-socio')."' /></form>";
				}
				$html .= "</div>";
		}
		$html .= "<script>
			jQuery('input[type=radio]').click(function(){
				jQuery('input[type=radio]').each(function(){
					if(typeof jQuery(this).data('show-opentext') == 'string' && jQuery(this).is(':checked')) {
						jQuery(jQuery(this).data('show-opentext')).fadeIn();
						jQuery(jQuery(this).data('show-opentext')+' > textarea').attr('disabled', false);
					} else if(typeof jQuery(this).data('show-opentext') == 'string') {
						jQuery(jQuery(this).data('show-opentext')).fadeOut();
						jQuery(jQuery(this).data('show-opentext')+' > textarea').val('');
						jQuery(jQuery(this).data('show-opentext')+' > textarea').attr('disabled', true);
					} 
				});
			});
		
		
		</script>";
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
	
	$html .= "<style>
		@media (min-width: 600px) {
			#partner-attention > ul > li {
				width: calc(50% - 12px);
			}
		}

		.survey {
			display: none;
			margin: auto;
			border: 1px solid #cecece;
			border-radius: 10px;
			padding: 20px;
		}

		.survey.opened {
			display: block;
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
		jQuery('.survey').removeClass('opened');
	});

	jQuery('#partner-attention > ul > li > select').not('#partner-attention > ul > li#tab-survey > select').on('change', function (e) {
		console.log('select');
		jQuery('#partner-attention > div').addClass('opened');
		jQuery('html,body').animate({
				scrollTop: jQuery('#partner-attention > div').offset().top - 200
		},'slow');
	});

	jQuery('#partner-attention > ul > li#tab-survey > select').on('change', function (e) {
		console.log('select2 -> '+jQuery(this).val());
		jQuery('.survey').not('#'+jQuery(this).val()).removeClass('opened');
		jQuery('#'+jQuery(this).val()).addClass('opened');
		jQuery('html,body').animate({
				scrollTop: jQuery('#'+jQuery(this).val()).offset().top - 200
		},'slow');
	});

  </script>"; 
  return $html; 
}
add_shortcode('att-socio', 'wp_att_socio_shortcode');

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