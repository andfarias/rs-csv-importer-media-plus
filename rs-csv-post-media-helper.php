<?php
/**
 * Post Media Helper
 *
 *
 * @package WordPress
 * @subpackage Importer
 *
 *
 */
Class RS_CSV_Post_Media_Helper extends wp_post_helper {

	public function is_media( $value ) {
		if (parse_url($value, PHP_URL_SCHEME)) {
			$ext = array_pop(explode(".", $value));
			if(preg_match("/jpg|jpeg|gif|png/", $ext)) {
				return true;
			}
		}
		return false;
	}

	public function migrate_media( $url ) {
		$path = remote_get_file($url);
		$this->add_media($path, basename($path), "", "", true);
		return $path;
	}




	// Add Media
	public function add_media($filename, $title = null, $content = null, $excerpt = null, $thumbnail = false){
		if (!$this->postid) {
			$this->medias[$filename] = array(
				$title,
				$content,
				$excerpt,
				$thumbnail,
				);
			return;
		}

		if ( $filename && file_exists($filename) ) {
			$mime_type = '';
			$wp_filetype = wp_check_filetype(basename($filename), null);
			if (isset($wp_filetype['type']) && $wp_filetype['type'])
				$mime_type = $wp_filetype['type'];
			unset($wp_filetype);

			$title = isset($title) ? $title : preg_replace('/\.[^.]+$/', '', basename($filename));
			$content = isset($content) ? $content : $title;
			$excerpt = isset($excerpt) ? $excerpt : $content;
			$attachment = array(
				'post_mime_type' => $mime_type ,
				'post_parent'    => $this->postid ,
				'post_author'    => $this->post->post_author ,
				'post_title'     => $title ,
				'post_content'   => $content ,
				'post_excerpt'   => $excerpt ,
				'post_status'    => 'inherit',
				'menu_order'     => $this->media_count + 1,
			);
			if (isset($this->post->post_name) && $this->post->post_name)
				$attachment['post_name'] = $this->post->post_name;
			$attachment_id = wp_insert_attachment($attachment, $filename, $this->postid);
			unset($attachment);

			if (!is_wp_error($attachment_id)) {
				$this->media_count++;
				$this->attachment_id[] = $attachment_id;
				$attachment_data = wp_generate_attachment_metadata($attachment_id, $filename);
				wp_update_attachment_metadata($attachment_id,  $attachment_data);
				unset($attachment_data);
				if ($thumbnail)
					set_post_thumbnail($this->postid, $attachment_id);
				return $attachment_id;
			} else {
				return $attachment_id;
			}
		} else {
			return false;
		}
	}


}
