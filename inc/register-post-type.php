<?php 
	//* Get linktext
	$ejo_testimonials_settings = get_option( 'ejo_testimonials_settings', array() );

	//* Archive slug. Default = testimonials
	$archive = (isset($ejo_testimonials_settings['archive'])) ? $ejo_testimonials_settings['archive'] : 'testimonials';

	$labels = array(
		'name'                => 'Referenties',
		'singular_name'       => 'Referentie',
		'menu_name'           => 'Referenties',
		'parent_item_colon'   => 'Parent Referentie:',
		'all_items'           => 'Alle Referenties',
		'view_item'           => 'Bekijk Referentie',
		'add_new_item'        => 'Nieuwe Referentie Toevoegen',
		'add_new'             => 'Nieuwe Toevoegen',
		'edit_item'           => 'Wijzig Referentie',
		'update_item'         => 'Update Referentie',
		'search_items'        => 'Zoek Referenties',
		'not_found'           => 'Niet Gevonden',
		'not_found_in_trash'  => 'Niet Gevonden in Prullenbak',
	);
	$args = array(
		'labels'              => $labels,
		'description'         => 'Referenties met uitgebreide mogelijkheden',
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', ),
		'hierarchical'        => false,
		'menu_position'       => 26,
		'menu_icon'           => 'dashicons-format-quote',
		'public'              => true,
		'exclude_from_search' => true,

		'rewrite' => array(
			'slug'		=> $archive,
		),
		'has_archive'	=> $archive,
		
	);

	register_post_type( self::$post_type, $args );
