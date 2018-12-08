<?php
// Social Icons Installation (Demo Version) - 07-05-2013

// UNINSTALL
function iire_social_uninstall() {
	global $wpdb;
	global $blog_id;
		
	delete_option("iire_social_version");
	delete_option("iire_social_data");
	delete_option("iire_social_saveparms");	
	
	delete_site_option("iire_social_version");
	delete_site_option("iire_social_data");	
	delete_site_option("iire_social_saveparms");			
	
	$table_name = $wpdb->get_blog_prefix($blog_id)."iire_social";
	$SQL = "DROP TABLE IF EXISTS ".$table_name;		
	mysql_query($SQL) or die("An unexpected error occured.".mysql_error());	

 	$table_name = $wpdb->prefix."iire_social";	
	$wpdb->query("DROP TABLE IF EXISTS $table_name");
}


// DELETE TABLES ON DEACTIVATION
function iire_social_deactivate() {
	global $wpdb;
	global $blog_id;
	
	$save = get_option('iire_social_saveparms');
	if ($save == '' || $save == '0') {	
		delete_option("iire_social_version");
		delete_option("iire_social_data");	
		
		delete_site_option("iire_social_version");
		delete_site_option("iire_social_data");				

		$table_name = $wpdb->get_blog_prefix($blog_id)."iire_social";
		$SQL = "DROP TABLE IF EXISTS ".$table_name;		
		mysql_query($SQL) or die("An unexpected error occured.".mysql_error());	
	
 		$table_name = $wpdb->prefix."iire_social";	
		$wpdb->query("DROP TABLE IF EXISTS $table_name");
	}		
}

?>