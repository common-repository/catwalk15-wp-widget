<?php
/**
* Plugin Name: Catwalk15 Widget
* Plugin URI: http://www.catwalkfifteen.com/catwalk15-wordress-widget/
* Description: Showcase your recent Catwalk15 photos.
* Version: 1.0
* Author: Catwalk15
* Author URI: http://catwalkfifteen.com/
* License: A "Slug" license name e.g. GPL12
*/

define("THEME_NS", "catwalk15");

add_action( 'wp_enqueue_scripts', 'catwalk15_scripts' );

/**
 * Add stylesheet to the page
 */
function catwalk15_scripts() {
		wp_enqueue_style( 'prefix-style', plugins_url('catwalk15.css', __FILE__) );
		 // Register the script like this for a plugin:
    wp_register_script( 'catwalk15', plugins_url( '/catwalk15.js', __FILE__ ), array( 'jquery' ) );
    // or
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'catwalk15' );
}

class catwalk15_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'catwalk15_latest_widget', 

		// Widget name will appear in UI
		__('Catwalk15 Widget', 'catwalk15_latest_widget'), 

		// Widget description
		array( 'description' => __( 'Displays recent Catwalk15 posts.', THEME_NS ), ) 
		);
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		global $post, $token;
		$title = apply_filters( 'widget_title', $instance['title'] );
		$count = $instance['count'];
		$username = $instance['username'];
		$achievements = $instance['achievements'];
		$logo = $instance['logo'];
		// before and after widget arguments are defined by themes
		
		if (trim($username) == "") {
			$user = 0;
		} else {
			$feed = file_get_contents("http://catwalk15.com/api/username/".$username);
			//var_dump($feed);
			$utilizator = json_decode($feed);
			$user = $utilizator->id;
		}
		
		$out = $args['before_widget'];
		if ( ! empty( $title ) ) {
			$out .= $args['before_title'] . $title . $args['after_title'];
		}
		$out .= '<div class="catwalk15">';
		$feed = file_get_contents("http://catwalk15.com/api/person/".$user."/posts/0/500/0/".$count);
		$feed = json_decode($feed);
				//var_dump($feed);
		echo $out;
		if ($user) {
		?>
    <div class="row demo-row">
      <ul class="thumbnails" id="example1">
				<?php foreach ($feed as $item) : ?>
					<?php if (!$item->deleted) : ?>
					<li><a href="http://www.catwalkfifteen.com/profile/<? echo $username; ?>/<?php echo ($utilizator->id * $item->id * strtotime($utilizator->created)); ?>" target="_blank"><img src="<?php echo $item->url ?>" /></a><?php
						/*if (intval($item->votes) === 0) {
							$procent = 50;
						} else {
							$procent = round((intval($item->votplus)/intval($item->votes))*100);
						}
						if ($procent >= 50) {
							echo '<div class="votes plus">'.$procent.'%</div>';
						} else {
							echo '<div class="votes minus">'.$procent.'%</div>';
						}
						*/
					?></li>
				<?php endif; ?>
			 <?php endforeach; ?>
			</ul>
			 <div class="clear"></div>
			 <?php 
				if ($achievements == "on") {
					echo '<div class="achievements">';
					echo '<h3 class="subtitle">Achievements<h3>';
					$count = 0;
					$user_achievements = file_get_contents("http://catwalk15.com/api/person/".$user."/achievements");
					$user_achievements = json_decode($user_achievements);
				?>
				<ul class="achievement-pictures">
				<?php for ($i = count($user_achievements); $i > count($user_achievements) - 5; $i--) { 
					$pic = explode("/", $user_achievements[$i-1]->pic);
					$pic = $pic[5];
					?>
					<?php if (!$user_achievements[$i-1]->locked == 0) : ?>
						<li>
							<img title="<?php echo $user_achievements[$i-1]->title ?>" class="achievement-pic" id="achievement-pic-<?php echo $count+1; ?>" src="<?php echo plugins_url("/images/achievements/".$pic, __FILE__); ?>" />
						</li>
						<?php $count++; ?>
					<?php endif; ?>
				<?php } ?>
					<div class="clear"></div>
					<div class="linie"></div>
					<li><div class="arrow"></div></li>
					</ul>
			 <div class="clear"></div>
				<?php 
					$count = 0;
					for ($i = count($user_achievements); $i > count($user_achievements) - 5; $i--) { 
					?>
					<?php if (!$user_achievements[$i-1]->locked == 0) : ?>
						<span <?php if ($count == 0) echo 'style="display: block"'?> class="achievement-title" id="achievement-title-id-<?php echo $count+1; ?>"><?php echo $user_achievements[$i-1]->title?></span>
						<?php $count++; ?>
					<?php endif; ?>
				<?php } ?>
			 <?php } ?>	
			 <?php 
				if ($logo == "on") {
				?>
				<img src="<?php echo plugins_url( '/images/catwalk15_logo.png', __FILE__ ); ?>" />
			 <?php } ?>
			</div>	
    </div> <!-- /row -->
<?php
		} else {
			echo '<span style="color: #d00">The username provided is invalid!</span>';
		}
		$out = '</div>';
		// This is where you run the code and display the output
		echo __( $out, 'catwalk15_latest_widget' );
		echo '<div style="clear:both"></div>';
		echo $args['after_widget'];
	}
		
	// Widget Backend 
	public function form( $instance ) {
		global $token;
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'catwalk15_latest_widget' );
		}
		if ( isset( $instance[ 'count' ] ) ) {
			$count = $instance[ 'count' ];
		}
		else {
			$count = 6;
		}
		if ( isset( $instance[ 'username' ] ) ) {
			$username = $instance[ 'username' ];
		}
		else {
			$username = "";
		}
		if ( isset( $instance[ 'achievements' ] ) ) {
			$achievements = $instance[ 'achievements' ];
		}
		else {
			$achievements = 0;
		}
		//var_dump($instance);
		if ( isset( $instance[ 'logo' ] ) and ($instance[ 'logo' ] == "on")  ) {
			$logo = "catwalk15_logo.png";
		}
		else {
			$logo = 0;
		}
		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', THEME_NS ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		<p>
		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'Username:', THEME_NS ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" />
			<?php
				$feed = file_get_contents("http://catwalk15.com/api/username/".$username);
				$utilizator = json_decode($feed);
				$user = $utilizator->id;
				//var_dump($username);
				if (!($user) and ($utilizator != NULL)) {
					echo '<span style="color: #d00">The username you specified is not valid! Please enter another username.</span>';
				}
				//echo $user;
				$utilizator = json_decode($feed);
			?>
		<p>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'No. of posts to display: ', THEME_NS ); ?></label>
			<select id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>">
				<option value="2" <?php if ($count == 2) { echo "selected"; } ?>>2</option>
				<option value="4" <?php if ($count == 4) { echo "selected"; } ?>>4</option>
				<option value="6" <?php if ($count == 6) { echo "selected"; } ?>>6</option>
				<option value="8" <?php if ($count == 8) { echo "selected"; } ?>>8</option>
				<option value="10" <?php if ($count == 10) { echo "selected"; } ?>>10</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'achievements' ); ?>"><?php _e( 'Get achievements:', THEME_NS ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'achievements' ); ?>" name="<?php echo $this->get_field_name( 'achievements' ); ?>" type="checkbox" <?php if ($achievements) { echo "checked";} else { echo ""; } ?> />
		</p>
		<p>
			<?php //var_dump($logo); ?>
			<label for="<?php echo $this->get_field_id( 'logo' ); ?>"><?php _e( 'Display logo:', THEME_NS ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'logo' ); ?>" name="<?php echo $this->get_field_name( 'logo' ); ?>" type="checkbox" <?php if ($logo) { echo "checked";} ?> />
		</p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';
		$instance['username'] = ( ! empty( $new_instance['username'] ) ) ? strip_tags( $new_instance['username'] ) : '';
		$instance['achievements'] = ( ! empty( $new_instance['achievements'] ) ) ? strip_tags( $new_instance['achievements'] ) : '';
		$instance['logo'] = ( ! empty( $new_instance['logo'] ) ) ? strip_tags( $new_instance['logo'] ) : '';
		return $instance;
	}
} // Class wpb_widget ends here

// register Foo_Widget widget
function register_catwalk15_widget() {
    register_widget( 'catwalk15_widget' );
}
add_action( 'widgets_init', 'register_catwalk15_widget' );
