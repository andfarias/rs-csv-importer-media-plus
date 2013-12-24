<?php
/**
 * Post Media Helper
 *
 * Decorate wp_post_helper
 * 
 * @package WordPress
 * @subpackage Importer
 *
 *
 */
Class RS_CSV_Post_Media_Helper {

	public $medias;

	public function __construct($base) {
		$this->medias = array();
		$this->base = $base;
	}

	public function is_media( $value ) {
		if (parse_url($value, PHP_URL_SCHEME)) {
			$ext = array_pop(explode(".", $value));
			if(preg_match("/jpg|jpeg|gif|png/", $ext)) {
				return true;
			}
		}
		return false;
	}

	public function queue_media($key,$value) {
		$this->medias[$key] = $value;
	}

	public function migrate_medias() {
		foreach ($this->medias as $key => $url) {
			$path = remote_get_file($url);
			$id = $this->add_media($path, basename($path), "", "", false);
			$this->add_meta($key,$id,true);
		}
	}


	// Get PostID
	public function postid(){
		return $this->base->postid;
	}

	// Get Attachment ID
	public function attachment_id(){
		return $this->base->attachment_id;
	}

	// Init Post Data
	public function init($args = array()){
		return $this->base->init($args);
	}

	// Set Post Data
	public function set($args) {
		return $this->base->set($args);

	}

	// Add Post
	public function insert(){
		return $this->base->insert();
	}

	// Update Post
	public function update(){
		return $this->base->update();
	}

	private function add_related_meta($postid){
		return $this->base->add_related_meta($postid);
	}

	// Add Tag
	public function add_tags($tags = array()){
		return $this->base->add_tags($tags);

	}

	// add terms
	public function add_terms($taxonomy, $terms){
		return $this->base->add_terms($taxonomy, $terms);
	}

	// Add Media
	public function add_media($filename, $title = null, $content = null, $excerpt = null, $thumbnail = false){
		return $this->base->add_media($filename, $title, $content, $excerpt, $thumbnail);
	}

	// Add Custom Field
	public function add_meta($metakey, $val, $unique = true){
		return $this->base->add_meta($metakey, $val, $unique );
	}

	// Add Advanced Custom Field
	public function add_field($field_key, $val){
		return $this->base->add_field($field_key, $val);
	
	}
}
