<?php
require_once(dirname(__FILE__).'/../select/'.'field_select.php'); 
class Kleo_Options_cats_multi_select extends Kleo_Options_select {

    /**
     * Field Constructor.
     *
     * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
     *
     * @since Kleo_Options 1.0.0
    */
    function __construct($field = array(), $value ='', $parent) {
        $this->field = $field;
		$this->value = $value;
		$this->args = $parent->args;
		
        $wp_args = wp_parse_args($this->field['args'], array());
		$cats = get_categories($wp_args);
		foreach ($cats as $cat) {
			$this->field['options'][$cat->term_id] = $cat->name;
		}
		$this->field['multiselect'] = true;
    }
}
