<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


add_action( 'bp_loaded',            'bpmh_loaded',                  10 );
add_action( 'bp_init',              'bpmh_init',                    10 );
add_action( 'bp_enqueue_scripts',   'bpmh_enqueue_scripts',         10 );
add_action( 'bp_after_setup_theme', 'bpmh_after_setup_theme',       10 );
add_action( 'widgets_init',         'bpmh_widgets_init',           100 );
add_action( 'bp_loaded',            'bpmh_register_widgets',        11 );
add_action( 'bp_actions',           'bpmh_actions',                 10 );
add_action( 'admin_init',           'bpmh_admin_init',              10 );
add_action( 'bpmh_admin_init',      'bpmh_admin_register_settings', 11 );

function bpmh_loaded() {
	do_action( 'bpmh_loaded' );
}

function bpmh_init() {
	do_action( 'bpmh_init' );
}

function bpmh_after_setup_theme(){
	do_action( 'bpmh_after_setup_theme' );
}

function bpmh_widgets_init() {
	do_action( 'bpmh_widgets_init' );
}

function bpmh_register_widgets() {
	do_action( 'bpmh_register_widgets' );
}

function bpmh_enqueue_scripts() {
	do_action( 'bpmh_enqueue_scripts' );
}

function bpmh_actions() {
	do_action( 'bpmh_actions' );
}

function bpmh_admin_init() {
	do_action( 'bpmh_admin_init' );
}

function bpmh_admin_register_settings() {
	do_action( 'bpmh_admin_register_settings' );
}
