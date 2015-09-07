<?php

/**
 * Plugin Name:       Post Hierarchy Menu
 * Plugin URI:        http://iainjmccallum.com
 * Description:       This adds a simple widget which will spit out a ul li for nested CPT items.
 * Version:           0.0.1
 * Author:            Iain J McCallum
 * Author URI:        http://iainjmccallum.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cpt-hierarchy-menu
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

// Creating the widget 
class wpb_widget extends WP_Widget {

	//=====================================================================================================
	//installing the widget ?
	//=====================================================================================================
	function __construct() {
		parent::__construct(
			// Base ID of your widget
			'wpb_widget', 

			// Widget name will appear in UI
			__('CPT Hierarchy Menu', 'wpb_widget_domain'), 

			// Widget description
			array( 'description' => __( 'Shows a nested list of hierarchical Posts from the selected CPT', 'wpb_widget_domain' ), ) 
		);
	}


	//=====================================================================================================
	// Creating widget front-end
	// This is where the action happens
	//=====================================================================================================
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$cpt = apply_filters( 'widget_title', $instance['cpt'] );


		// Begin widget HTML
		echo $args['before_widget'];

			//Title
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			//loader / placeholder
			echo '<div class="post-hierarchy-menu" data-posttype="' . $cpt . '">';
			echo 'Loading ' . $cpt . ' menu...';
			echo '</div>';

		echo $args['after_widget'];
		//END widget HTML


		//Add widget script to the footer
		function your_function() {
			?>
				<script type="text/javascript" >

					jQuery(document).ready(function($) {
						
						//After page load - request the items
						//===================================================================
						$('.post-hierarchy-menu').each(function(){
							var unorderdPosts = [];
							var orderedPosts = [];
							var thisPHmenu = $(this);
							
							//params object could hold user setting in the future
							var params = {
								posttype: thisPHmenu.attr('data-posttype')
							};

							var ajaxURL =  "<?php echo get_site_url(); ?>/wp-admin/admin-ajax.php";
							
							$.ajax({
								url: ajaxURL,
								type: 'GET',
								data: {
									action: 'ph_menu',
									params: params
								},
								success: succesFunction,
								error: errorFunction
							});

							function errorFunction(jqXHR, textStatus, errorThrown){
								console.group("post hierarchy menu Ajax error:");
									console.log('jqXHR:', jqXHR);
									console.log('textStatus:', textStatus);
									console.log('errorThrown:', errorThrown);
								console.groupEnd();
							}

							// After request returns, sort & print the items
							//===================================================================
							function succesFunction(response){

								//0. Responce should be an array of objects
								unorderdPosts = JSON.parse(response);

								//1. move root items into orderedPosts array
								orderedPosts = setChildrenOf(0);
								
								//2. recurse through the ordered list building as we go!
								menuDomString = "<ul>";
								orderedPosts.forEach(function each(item) {
									menuDomString += "<li><a href='" + item.url + "'>" + item.name + '</a>';
									//if item has children
									if (hasMenuChildren(item.id)) {
										menuDomString += "<ul>";
										item.children = setChildrenOf(item.id);
										item.children.forEach(each);
										menuDomString += "</ul>";
									} else {
										//no children, we're at a leaf
									}
									menuDomString += "</li>";
								});
								menuDomString += "</ul>";

								//3. Display orderedPosts
								thisPHmenu.html(menuDomString);
							}

							/**
							 * loops through the unorderd post list looking for any items that list item_id as a parent
							 */
							function hasMenuChildren(item_id){
								for (var i = 0; i < unorderdPosts.length; i++) {
									if (unorderdPosts[i].parent == item_id) {
										return true;
									}
								}
								return false
							}
							

							/**
							 * takes the id of the item we're adding children too, 
							 * returns an array of those children
							 * removes the children from the list array
							 */
							function setChildrenOf(parentID){
								var childrenArray = [];
								for (var i = 0; i < unorderdPosts.length; i++) {
									if (unorderdPosts[i].parent == parentID) {
										//it's a root!
										childrenArray.push(unorderdPosts[i]);
										unorderdPosts.splice(i,1);
										i--;
									}
								}
								return childrenArray;
							}

						});
					});
				</script>
			<?php
		}
		add_action( 'wp_footer', 'your_function' );
	}
			

	//=====================================================================================================
	// Widget Backend 
	//=====================================================================================================
	public function form( $instance ) {
		$defaults = array(
			'title' => 'Title',
			'cpt' => 'post'
		);
		$title = $instance[ 'title' ];
		$cpt = $instance[ 'cpt' ];
		
		
		// Widget admin form
		?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'cpt' ); ?>">Select the post type to show in this nested list.</label>
				<select name="<?php echo $this->get_field_name( 'cpt' ); ?>" value="<?php echo esc_attr( $cpt ); ?>" id="<?php echo $this->get_field_id( 'cpt' ); ?>">
					<?php //get a list of the CPTs
						$post_types = get_post_types( '', 'names' ); 
						foreach ( $post_types as $post_type ) {
							if (esc_attr( $cpt ) == $post_type) {
								echo '<option value="' . $post_type . '" selected>' . $post_type . '</option>';
							} else {
								echo '<option value="' . $post_type . '">' . $post_type . '</option>';
							}
						}
					?>
				</select>
			</p>
			<!-- Future additions
			<hr />
			<p><i>Apperance</i></p>
			<p>
				<label>Text color</label>
			</p>
			<p>
				<label>Text hover color</label>
			</p>
			<p>
				<label>line color</label>
			</p>
			<p>
				<label>Style</label>
			</p>
			<p>
				<label>Custom CSS</label>
			</p>
			<p>
				<label>Enable collapsing</label>
			</p>
			<p>
				<label>Load as collapsed</label>
			</p>
			-->
		<?php 
	}

	
	//=====================================================================================================
	// Updating widget replacing old instances with new
	//=====================================================================================================
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		//$instance = $old_instance;
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['cpt'] = ( ! empty( $new_instance['cpt'] ) ) ? strip_tags( $new_instance['cpt'] ) : '';
		return $instance;
	}
} // Class wpb_widget ends here


//=====================================================================================================
// Register and load the widget
//=====================================================================================================
function wpb_load_widget() {
	register_widget( 'wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );


//=====================================================================================================
//Add widget CSS to the header
//=====================================================================================================
function phm_styling() {
	?>
		<style>
			/* Thanks to: http://codepen.io/khoama/pen/hpljA */
			/* Future addition
			.post-hierarchy-menu ul {
				margin: 0px 0px 0px 12px !important;
				list-style: none;
			}
			.post-hierarchy-menu ul li {
				font-size: 14px;
				line-height: 22px;
				position: relative;
			}
			
			.post-hierarchy-menu ul li.root {
				margin: 0px 0px 0px -20px;
			}
			.post-hierarchy-menu ul li.root:before {
				display: none;
			}
			.post-hierarchy-menu ul li.root:after {
				display: none;
			}
			.post-hierarchy-menu ul li:last-child:after {
				display: none;
			}
			.post-hierarchy-menu ul li ul li:before {
				position: absolute;
				left: -9px;
				top: -3px;
				content: '';
				display: block;
				border-left: 1px solid #666;
				height: 1em;
				border-bottom: 1px solid #666;
				width: 8px;
				border-bottom-left-radius: 3px;
			}
			.post-hierarchy-menu ul li ul li:after {
				position: absolute;
				left: -9px;
				bottom: -7px;
				content: '';
				display: block;
				border-left: 1px solid #666;
				height: 100%;
			}*/

		</style>
	<?php
}
add_action( 'wp_head', 'phm_styling' );


//=====================================================================================================
// back end answering the request
//=====================================================================================================
function ph_menu() {	
	$params = $_GET['params'];
	$cpt = $params['posttype'];

	//1. Fetch all the posts in the CPT
	$cpth_args_all = array(
		'post_type' => $cpt,
		'post_status' => 'publish',
		'orderby' => 'ID',
		'order'   => 'ASC',
		'posts_per_page' => -1
	);
	$cpth_array_all = get_posts($cpth_args_all);
	$cpth_array_all_conditioned = array();

	//2. refine the menu
	for ($x = 0; $x < sizeof($cpth_array_all); $x++) {
		$cpth_item = array(
			'id' => $cpth_array_all[$x]->ID,
			'parent' => $cpth_array_all[$x]->post_parent,
			'name' => $cpth_array_all[$x]->post_title,
			'url' => $cpth_array_all[$x]->guid
		);
		//$cpth_array_all_conditioned[$x] = $cpth_item;
		array_push($cpth_array_all_conditioned,$cpth_item);
	} 

	//print_r($cpth_array_all_conditioned);
	echo json_encode($cpth_array_all_conditioned);

	die();
}
add_action( 'wp_ajax_ph_menu', 'ph_menu' );
add_action( 'wp_ajax_nopriv_ph_menu', 'ph_menu' );