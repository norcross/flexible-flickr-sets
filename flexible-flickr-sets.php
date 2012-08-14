<?php
/*
Plugin Name: Flexible Flickr Sets
Plugin URI: http://andrewnorcross.com/plugins/flexible-flickr-sets/
Description: Shortcode to display responsive Flickr slideshows
Version: 1.0
Author: norcross
Author URI: http://andrewnorcross.com/
License: GPL v2

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


*/


class FlexiFlickr
{

	/**
	 * This is our constructor
	 *
	 * @return FlexiFlickr
	 */
	public function __construct() {
		add_action					( 'admin_menu',				array( $this, 'settings'		) );
		add_action					( 'admin_init', 			array( $this, 'reg_settings'	) );
		add_action					( 'the_posts', 				array( $this, 'ffs_loader'		) );
		add_shortcode				( 'flexiflickr',			array( $this, 'shortcode'		) );
	}


	/**
	 * build out settings page
	 *
	 * @return FlexiFlickr
	 */


	public function settings() {
	    add_submenu_page('options-general.php', 'FlexiFlickr', 'FlexiFlickr', 'manage_options', 'flexi-flickr', array( $this, 'ffs_display' ));
	}

	/**
	 * Register settings
	 *
	 * @return FlexiFlickr
	 */


	public function reg_settings() {
		register_setting( 'ffs_options', 'ffs_options');		

	}

	/**
	 * Display main options page structure
	 *
	 * @return FlexiFlickr
	 */
	 
	public function ffs_display() { 
		
		if (!current_user_can('manage_options') )
			return;
		?>
	
		<div class="wrap">
    	<div class="icon32" id="icon-ffs"><br></div>
		<h2>FlexiFlickr Settings</h2>
        
	        <div class="ffs_options">
            	<div class="ffs_form_text">
            	<p>Enter your API key below</p>
                </div>
                
                <div class="ffs_form_options">
	            <form method="post" action="options.php">
			    <?php
                settings_fields( 'ffs_options' );
				$ffs_options	= get_option('ffs_options');

				$api		= (isset($ffs_options['api'])  ? $ffs_options['api']	: ''		);
				$time		= (isset($ffs_options['time']) ? $ffs_options['time']	: '7000'	);
				$paging		= (isset($ffs_options['paging']) && $ffs_options['paging'] == 'true' ? 'checked="checked"' : '');
				$direction	= (isset($ffs_options['direction']) && $ffs_options['direction'] == 'true' ? 'checked="checked"' : '');	
				$slide		= (isset($ffs_options['trans']) && $ffs_options['trans'] == 'slide' ? 'checked="checked"' : '');
				$fade		= (isset($ffs_options['trans']) && $ffs_options['trans'] == 'fade' ? 'checked="checked"' : '');
				$trans		= (isset($ffs_options['trans']) ? '' : 'checked="checked"');
				?>
				<table class="form-table ffs-table">
				<tbody>

				<tr valign="top" class="ffs_choice">
					<th scope="row">
						<label for="ffs_options[api]">API Key</label>
					</th>
					<td>
						<input type="text" id="ffs_api" name="ffs_options[api]" class="regular-text code" value="<?php echo esc_attr($api); ?>" />
						<p class="description">Enter the complete Flickr API key</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Transition</th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span>Transition</span></legend>
							<input type="radio" value="slide" name="ffs_options[trans]" id="ffs_slide" <?php echo $slide; ?> />
							<label for="ffs_slide">Slide</label><br>
				
							<input type="radio" value="fade" name="ffs_options[trans]" id="ffs_fade" <?php echo $fade; ?> <?php echo $trans; ?> />
							<label for="ffs_fade">Fade</label>
						</fieldset>
					</td>
				</tr>

				<tr valign="top" class="ffs_choice">
					<th scope="row">
						<label for="ffs_options[time]">Slideshow Time</label>
					</th>
					<td>
						<input type="text" id="ffs_time" name="ffs_options[time]" class="small-text" value="<?php echo esc_attr($time); ?>" />
						<p class="description">Enter the amount of time (in milliseconds) for each slide</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Directional Navigation</th>
					<td>
						<fieldset><legend class="screen-reader-text"><span>Directional Navigation</span></legend>
						<label for="ffs_options[direction]">
						<input type="checkbox" value="true" id="ffs_direction" name="ffs_options[direction]" <?php echo $direction; ?> />
						</label>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Paging Navigation</th>
					<td>
						<fieldset><legend class="screen-reader-text"><span>Paging Navigation</span></legend>
						<label for="ffs_options[paging]">
						<input type="checkbox" value="true" id="ffs_paging" name="ffs_options[paging]" <?php echo $paging; ?> />
						</label>
						</fieldset>
					</td>
				</tr>

				</tbody>
				</table>	              
    
	    		<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
				

				</form>
                </div>
    
            </div>

        </div>    

	
	<?php }
		

	/**
	 * load scripts for front end
	 *
	 * @return FlexiFlickr
	 */


	public function front_scripts() {

		wp_enqueue_style( 'flexslider', plugins_url('/lib/css/flexslider.css', __FILE__), array(), null, 'all' );
		wp_enqueue_script( 'flexslider', plugins_url('/lib/js/jquery.flexslider.min.js', __FILE__) , array('jquery'), null, true );
		wp_enqueue_script( 'ffs-init', plugins_url('/lib/js/ffs.init.js', __FILE__) , array('jquery'), null, true );

	}

	/**
	 * load front-end CSS if shortcode is present
	 *
	 * @return FlexiFlickr
	 */


	public function ffs_loader($posts) {

		// no posts present. nothing more to do here
		if ( empty($posts) )
			return $posts;		

		// they said they didn't want the CSS. their loss.
		$ffs_options = get_option('ffs_options');

		if(isset($ffs_options['css']) && $ffs_options['css'] == 'true' )
			return $posts;		

		
		// false because we have to search through the posts first
		$found = false;
		 
		// search through each post
		foreach ($posts as $post) {
			// check the post content for the short code
			$content	= $post->post_content;
			if ( preg_match('/flexiflickr(.*)/', $content) ) 
				$found = true; // we have found a post with the short code

			break; // stop the search
		}
		 
		if ($found == true )
			$this->front_scripts();


		return $posts;
	}


	/**
	 * Build out shortcode with variable array of options
	 *
	 * @return FlexiFlickr
	 */

	public function shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'setid' => '',
		
		), $atts ) );

		// first, check for API key and 
		$ffs_opt = get_option('ffs_options');
		$api_key = $ffs_opt['api'];
		
		if(!isset($api_key))
			return;

		global $post;
		$p_id = $post->ID;
		// make API call
		if( false == get_transient( 'ffs_api_'.$p_id ) ) {	

			$args = array (
				'sslverify' => false
				);

			$format		= 'php_serial';
			$media		= 'photos';
			$extras		= 'url_o';

			$request	= new WP_Http;
			$url		= 'http://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key='.$api_key.'&photoset_id='.$setid.'&media='.$media.'&extras='.$extras.'&format='.$format;
			$response	= wp_remote_get ( $url, $args );

				// check for bad response from Flickr
				if( is_wp_error( $response ) ) {
					$rawdata = 'unable to connect';
					delete_transient( 'ffs_api_'.$p_id );
				} else {
					$rawdata = $response['body'];
				}
		
			// Save a transient to the database
			set_transient( 'ffs_api_'.$p_id , $rawdata, 60*60*4 );
		
		} // end transient check 		

		// check for transient cache'd result and unserialize it
		$flickr = get_transient( 'ffs_api_'.$p_id );
		$data	= unserialize($flickr);

		// other settings
		$speed		= (isset($ffs_opt['time']) ? $ffs_opt['time'] : '7000' );

		$paging		= (isset($ffs_opt['paging']) 	&& $ffs_opt['paging'] == 'true' 	? 'true'  : 'false' );
		$direction	= (isset($ffs_opt['direction']) && $ffs_opt['direction'] == 'true' 	? 'true'  : 'false' );
		$trans		= (isset($ffs_opt['trans']) 	&& $ffs_opt['trans'] == 'slide' 	? 'slide' : 'fade'  );

		// opening slideshow markup
		$flexiflickr = '<div class="flexslider carousel flexiflickr" data-trans="'.$trans.'" data-speed="'.$speed.'" data-paging="'.$paging.'" data-direction="'.$direction.'">';
		$flexiflickr .= '<ul class="slides">';

			// get our data
			$photos = $data['photoset']['photo'];

			foreach ($photos as $photo) {
				$image_url = $photo['url_o'];

				$flexiflickr .= '<li><img src="'.$image_url.'"></li>';
			}
		


		
		// closing slideshow markup
		$flexiflickr .= '</ul></div>';

	// return entire build array
	return $flexiflickr;
	
	}

/// end class
}


// Instantiate our class

function flexiflickr_init() {
	$flexiflickr = new FlexiFlickr();
}

if(!function_exists('flexiflickr_init')) {
	flexiflickr_init();
}

add_action('init', 'flexiflickr_init', 1);

