<?php
/**
 * Definisce i campi custom degli user come persone
 */

add_filter( 'gettext', 'dsi_change_user_to_person' );
add_filter( 'ngettext', 'dsi_change_user_to_person' );

/*
 * Cambio label per caratterizzare gli utenti potenziati
 */
function dsi_change_user_to_person( $translated )
{
	$translated = str_replace( 'Utenti', 'Utenti/Persone', $translated );
	return $translated;
}

add_action( 'admin_head-user-edit.php', 'dsi_remove_user_profile_fields_with_css' );
add_action( 'admin_head-profile.php',   'dsi_remove_user_profile_fields_with_css' );

/**
 * Nascondo i campi inutili\
 */
function dsi_remove_user_profile_fields_with_css() {
//Hide unwanted fields in the user profile
	$fieldsToHide = [
		'rich-editing',
		'admin-color',
		'comment-shortcuts',
		//'admin-bar-front',
		//'user-login',
		//'role',
		//'super-admin',
		//'first-name',
		//'last-name',
		//'nickname',
		'display-name',
		//'email',
		//'description',
		//'pass1',
		//'pass2',
		//'sessions',
		//'capabilities',
		//'syntax-highlighting',
		'url'

	];

	//add the CSS
	foreach ($fieldsToHide as $fieldToHide) {
		echo '<style>tr.user-'.$fieldToHide.'-wrap{ display: none; }</style>';
	}

	//fields that don't follow the wrapper naming convention
	echo '<style>tr.user-profile-picture{ display: none; }</style>';

	//all subheadings
	echo '<style>#your-profile h2{ display: none; }</style>';
}



/**
 * Sostituisco gravatar con la foto utente
 */

function remove_avatar_from_users_list( $avatar ) {
    if (is_admin()) {
	    global $current_screen;
	    //	    if ( $current_screen->base == 'users' ) {
	    // todo: recuperare la thumb del profilo per mostrarla nella pagina di lista
		    $avatar = '';
		//	    }
    }
    return $avatar;
}
add_filter( 'get_avatar', 'remove_avatar_from_users_list' );



/**
 * Crea i metabox dello user
 */
add_action( 'cmb2_init', 'dsi_add_persone_metaboxes' );
function dsi_add_persone_metaboxes() {

	$prefix = '_dsi_persona_';

	$cmb_user = new_cmb2_box( array(
		'id'               => $prefix . 'persona_box',
		'title'            => __( 'Persona', 'design_scuole_italia' ),
		// Doesn't output for user boxes
		'object_types'     => array( 'user' ),
		// Tells CMB2 to use user_meta vs post_meta
		'show_names'       => true,
		//'new_user_section' => 'add-new-user',
		// where form will show on new user page. 'add-existing-user' is only other valid option.
		'priority'     => 'hight',
	) );

	$cmb_user->add_field( array(
		'name'     => __( 'La Persona', 'design_scuole_italia' ),
		'desc'     => __( 'Attributi che estendono le caratteristiche dell\'utente' , 'design_scuole_italia' ),
		'id'       => $prefix . 'extra_info',
		'type'     => 'title',
		'on_front' => false,
	) );

	$cmb_user->add_field( array(
		'name'    => __( 'Foto della Persona', 'design_scuole_italia' ),
		'desc'    => __( 'Inserire una fotografia che ritrae il soggetto descritto nella scheda', 'design_scuole_italia' ),
		'id'      => $prefix . 'foto',
		'type'    => 'file',
	) );



	$cmb_user->add_field( array(
		'name'    => __( 'Ruolo nell\'organizzazione *', 'design_scuole_italia' ),
		'desc'    => __( 'Personale Docente / Personale Tecnico Amministrativo', 'design_scuole_italia' ),
		'id'      => $prefix . 'ruolo_scuola',
		'type'             => 'select',
		'show_option_none' => true,
		'options'          => array(
			'docente' => __( 'Personale Docente', 'design_scuole_italia' ),
			'amministrativo'   => __( 'Personale Tecnico Amministrativo', 'design_scuole_italia' )
		),
	) );

	$cmb_user->add_field( array(
		'name'    => __( 'Ruolo Docente', 'design_scuole_italia' ),
		'desc'    => __( 'Seleziona la tipologia di ruolo docente', 'design_scuole_italia' ),
		'id'      => $prefix . 'ruolo_docente',
		'type'             => 'select',
		'show_option_none' => true,
		'options'          => array(
			'infanzia' => __( 'Scuola Infanzia', 'design_scuole_italia' ),
			'primaria' => __( 'Scuola Primaria', 'design_scuole_italia' ),
			'secondaria1' => __( 'Scuola Secondaria I grado', 'design_scuole_italia' ),
			'secondaria2' => __( 'Scuola Secondaria II grado', 'design_scuole_italia' ),
			'formazione' => __( 'Percorsi di Istruzione e Formazione Professionale', 'design_scuole_italia' ),
		),
		'attributes'    => array(
			'data-conditional-id'     => $prefix . 'ruolo_scuola',
			'data-conditional-value'  => 'docente',
		),
	) );
	$cmb_user->add_field( array(
		'name'    => __( 'Incarico', 'design_scuole_italia' ),
		'desc'    => __( 'Se docente: con incarico a tempo determinato/indeterminato', 'design_scuole_italia' ),
		'id'      => $prefix . 'incarico_docente',
		'type'             => 'select',
		'show_option_none' => true,
		'options'          => array(
			'determinato' => __( 'Incarico a Tempo Determinato', 'design_scuole_italia' ),
			'indeterminato' => __( 'Incarico a Tempo Indeterminato', 'design_scuole_italia' ),
		),
		'attributes'    => array(
			'data-conditional-id'     => $prefix . 'ruolo_scuola',
			'data-conditional-value'  => 'docente',
		),
	) );

	$cmb_user->add_field( array(
		'name'    => __( 'Durata Incarico', 'design_scuole_italia' ),
		'id'      => $prefix . 'durata_incarico_docente',
		'desc'    => __( 'Se docente a tempo determinato, prevedere data scadenza incarico', 'design_scuole_italia' ),
		'type' => 'text_date',
		'date_format' => 'd-m-Y',
		'attributes'    => array(
			'data-conditional-id'     => $prefix . 'incarico_docente',
			'data-conditional-value'  => 'determinato',
		),
	) );

	$cmb_user->add_field( array(
		'name'    => __( 'Tipo posto', 'design_scuole_italia' ),
		'desc'    => __( 'Nomale / Sostegno', 'design_scuole_italia' ),
		'id'      => $prefix . 'tipo_posto',
		'type'             => 'select',
		'show_option_none' => true,
		'options'          => array(
			'normale' => __( 'Normale', 'design_scuole_italia' ),
			'sostegno' => __( 'Sostegno', 'design_scuole_italia' ),
		),
		'attributes'    => array(
			'data-conditional-id'     => $prefix . 'ruolo_scuola',
			'data-conditional-value'  => 'docente',
		),
	) );



	$cmb_user->add_field( array(
		'name'    => __( 'Tipo supplenza', 'design_scuole_italia' ),
		'desc'    => __( 'Se supplente - Tipologia supplenza. Assume valori: ANNUALE per le supplenze di durata fino al 31/08 e FINO AL TERMINE per le supplenze di durata fino al 30/06', 'design_scuole_italia' ),
		'id'      => $prefix . 'tipo_supplenza',
		'type'             => 'select',
		'show_option_none' => true,
		'options'          => array(
			'annuale' => __( 'Annuale', 'design_scuole_italia' ),
			'termine' => __( 'Fino al termine', 'design_scuole_italia' ),
		),
		'attributes'    => array(
			'data-conditional-id'     => $prefix . 'tipo_posto',
			'data-conditional-value'  => 'sostegno',
		),
	) );


	$cmb_user->add_field( array(
		'name'    => __( 'Ruolo Tecnico / Amministrativo', 'design_scuole_italia' ),
		'desc'    => __( 'Seleziona la tipologia di ruolo docente', 'design_scuole_italia' ),
		'id'      => $prefix . 'ruolo_amministrativo',
		'type'             => 'select',
		'show_option_none' => true,
		'options'          => array(
			'tecnico' => __( 'Personale Tecnico', 'design_scuole_italia' ),
			'amministrativo' => __( 'Personale Amministrativo', 'design_scuole_italia' ),
			'collaboratore' => __( 'Collaboratore Scolastico', 'design_scuole_italia' ),
			),
		'attributes'    => array(
			'data-conditional-id'     => $prefix . 'ruolo_scuola',
			'data-conditional-value'  => 'amministrativo',
		),
	) );

	$cmb_user->add_field( array(
		'id' => $prefix . 'altri_ruoli_struttura',
		'name'    => __( 'Altri ruoli - strutture organizzative ', 'design_scuole_italia' ),
		'desc' => __( 'Altre strutture organizzative di cui fa parte (Es consiglio di istituto). Seleziona una struttura organizzativa. Se non la trovi inseriscila <a href="post-new.php?post_type=struttura">cliccando qui</a> ' , 'design_scuole_italia' ),
		'type'    => 'custom_attached_posts',
		'column'  => true, // Output in the admin post-listing as a custom column. https://github.com/CMB2/CMB2/wiki/Field-Parameters#column
		'options' => array(
			'show_thumbnails' => false, // Show thumbnails on the left
			'filter_boxes'    => true, // Show a text box for filtering the results
			'query_args'      => array(
				'posts_per_page' => 10,
				'post_type'      => 'struttura',
			), // override the get_posts args
		),
	) );

	$cmb_user->add_field( array(
		'name'    => __( 'Altri ruoli - funzioni strumentali ', 'design_scuole_italia' ),
		'desc'    => __( 'Definisci qui altre funzioni strumentali attribuite ', 'design_scuole_italia' ),
		'id'      => $prefix . 'altri_ruoli',
		'type'    => 'textarea',
	) );


	$cmb_user->add_field( array(
		'name'    => __( 'Genere *', 'design_scuole_italia' ),
		'id'      => $prefix . 'genere',
		'type'    => 'radio_inline',
		'options'          => array(
			'm' => __( 'M', 'design_scuole_italia' ),
			'f'     => __( 'F', 'design_scuole_italia' ),
		),
		'attributes'    => array(
			'required'    => 'required'
		),
	) );

	$cmb_user->add_field( array(
		'name'    => __( 'Data di nascita', 'design_scuole_italia' ),
		'id'      => $prefix . 'data_nascita',
		'type'    => 'text_date',
		'attributes'    => array(
			'required'    => 'required',
			'data-datepicker' => json_encode( array(
				'yearRange' => '-100:+0',
			) ),
		),
	) );


	$cmb_user->add_field( array(
		'name'    => __( 'Numero di telefono pubblico ', 'design_scuole_italia' ),
		'id'      => $prefix . 'telefono_pubblico',
		'type'    => 'text'
	) );

	$cmb_user->add_field( array(
		'name'    => __( 'Indirizzo email pubblico ', 'design_scuole_italia' ),
		'id'      => $prefix . 'email_pubblico',
		'type'    => 'text_email'
	) );

	$cmb_user->add_field( array(
		'name'    => __( 'Ulteriori informazioni', 'design_scuole_italia' ),
		'desc'    => __( 'Ulteriori informazioni relative alla persona', 'design_scuole_italia' ),
		'id'      => $prefix . 'altre_info',
		'type'    => 'textarea',
		//'attributes'    => array(
		//	'required'    => 'required'
		//),
	) );


}

/**
 * Funzione per recuperare gli user/persone da mostrare su cmb2
 * @param $query_args
 *
 * @return array
 */
function dsi_get_cmb2_user( $query_args ) {

	$args = wp_parse_args( $query_args, array(
		'fields' => array( 'user_login' ),

	) );

	$users = get_users(  );

	$user_options = array();
	if ( $users ) {
		foreach ( $users as $user ) {
			$user_options[ $user->ID ] = $user->user_login;
		}
	}

	return $user_options;
}