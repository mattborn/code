<?php
/*
Plugin Name: Build SMO
Plugin URI: http://smovision.com
Description: This tool creates the initial content for the SMO theme.
Version: 3.2.1
Author: Paper Tower
Author URI: http://papertower.com

*/

if ( is_admin() ) :

	// Create menu item in Tools.
	function build_menu() {
		add_management_page( 'Build SMO', 'Build SMO', 'manage_options', 'build_smo', 'build_smo_options' );
	} add_action( 'admin_menu', 'build_menu' );
	
	// Build content and update options.
	function build_smo() {
		if ( isset($_POST['papertower']) ) {
			
			// Pages
			$p1 = minsert( 'Home', 'page', 0, '
	<div id="intro">
		<h1>Expert Eyecare.<br />Locally Focused.</h1>
		<p><strong>Welcome to South Michigan Ophthalmology.</strong> Our goal is to provide you with complete, comprehensive eyecare close to home. From routine eye exams and diagnostic testing to in-office laser refractive surgery, you will get <em>state-of-the-art medical treatment</em> for you and your family right here, close to home.</p>
		<ul class="group">
			<li><a href="#">View Our 3 Locations</a></li>
			<li><a href="#">Schedule an Appointment</a></li>
		</ul>
	</div><!-- #intro -->' );
			$p2 = minsert( 'About Us', 'page', 0, '<h2>Seeing is believing</h2>
At SMO, our focus is to bring you the most advanced eye care services available. With exam rooms equipped with progressive optical technology and our in-house operating room for laser refractive surgery, SMO physicians will provide you the care you need, right when you need it. SMO offers the most comprehensive eye care in South Michigan for both children and adults, with offices conveniently located in Marshall, Coldwater and Albion.

Our beautifully remodeled Marshall office features six adult exam rooms complete with the latest technology, and two specially designed pediatric rooms with built-in play areas.

We also have two testing rooms with state-of-the-art digital diagnostic equipment for early detection of glaucoma, macular degeneration, diabetes, medication toxicity and other ocular disease. In addition, minor eye and eyelid procedures can be done in our minor surgery room.

Be sure to stop by our full service expanded Optical Shop, located in the Marshall office. We have over 2,000 frames available, so you’re sure to find frames to fit your personality and your needs. We even have children’s frames, making SMO your one stop optical shop. SMO is proud to carry the latest designer frames including: Ray-Ban&reg;, Nike, Tommy Bahama&reg;, Bebe, Saks Fifth Avenue, Roxy, Quicksilver, True Religion, Wiley X&trade;, and many others.' );
			$p3 = minsert( 'Doctors', 'page', $p2 );
			$p4 = minsert( 'Staff', 'page', $p2 );
			$p5 = minsert( 'Facilities', 'page', $p2 );
			$p6 = minsert( 'Services', 'page', 0, '<h2>In-Office Procedures</h2>
SMO offers our patients the highest level of care, including the convenience of many in-office procedures. All the treatments done in our offices are completed using new, state-of-the-art equipment.

<ul>
	<li>Botox injections</li>
	<li>Immediate removal of foreign bodies</li>
	<li>Intravitreal injections for macular degeneration</li>
	<li>Lid treatments</li>
	<li>Minor eyelid surgeries</li>
	<li>Punctal plugs for dry eyes</li>
</ul>

We also complete many laser treatments in our local offices using the most advanced lasers available.

<ul>
	<li>LASIK/PRK (laser refractive surgery)</li>
	<li>Diabetic laser treatments</li>
	<li>Glaucoma laser</li>
	<li>YAG capsulotomy</li>
</ul>

<h2>Hospital-based Procedures</h2>
For more in-depth care, SMO physicians treat patients at Oaklawn Hospital. This offers the convenience of a location close to home and the comfort of knowing your SMO physician will be performing your procedure, giving you the utmost care.

<ul>
<li>Endocyclophotocoagulation (ECP) for operative treatment of glaucoma</li>
<li>Enucleation and Evisceration for treatment of blind, painful eyes</li>
<li>Eye muscle surgery (strabismus) for crossed eyes</li>
<li>Major eyelid surgeries</li>
<li>Blepharoplasty</li>
<li>Drooping lids</li>
<li>Drooping brows</li>
<li>Turned-in or turned-out eyelids</li>
<li>Lesion removals</li>
<li>No stitch, no shot cataract surgery</li>
<li>Ocular trauma</li>
<li>Refractive Cataract Surgery</li>
<li>Treatment for astigmatism and presbyopia</li>
</ul>' );
			$p7 = minsert( 'Eye Conditions', 'page', $p6 );
			$p8 = minsert( 'Cataract', 'page', $p8 );
			$p9 = minsert( 'Glaucoma', 'page', $p8 );
			$p10 = minsert( 'Myopia', 'page', $p8 );
			$p11 = minsert( 'Macular Degeneration', 'page', $p8 );
			$p12 = minsert( 'Dry Eye Syndrome', 'page', $p8 );
			$p13 = minsert( 'Diagnosis & Treatment', 'page', $p6 );
			$p14 = minsert( 'Adult Eye Care', 'page', $p6 );
			$p15 = minsert( 'Children&rsquo;s Eye Care', 'page', $p6 );
			$p16 = minsert( 'Eye Wear', 'page' );
			$p17 = minsert( 'Be Prepared', 'page' );
			$p18 = minsert( 'Resources', 'page' );
			$p19 = minsert( 'Specials', 'page', $p19 );
			$p20 = minsert( 'Events', 'page', $p19 );
			$p21 = minsert( 'FAQ', 'page', $p19 );
			$p22 = minsert( 'Additional Reading', 'page', $p19 );
			$p23 = minsert( 'Contact Us', 'page' );
			$p24 = minsert( 'Schedule Appointment', 'page' );
			
			$t1 = wp_insert_term( 'Location', 'category' ); unset($t1['term_taxonomy_id']);
			$t2 = wp_insert_term( 'Doctor', 'category' ); unset($t1['term_taxonomy_id']);
			$t3 = wp_insert_term( 'Staffer', 'category' ); unset($t1['term_taxonomy_id']);
			$t4 = wp_insert_term( 'Special', 'category' ); unset($t1['term_taxonomy_id']);
			$t5 = wp_insert_term( 'Event', 'category' ); unset($t1['term_taxonomy_id']);
			$t6 = wp_insert_term( 'Slider', 'category' ); unset($t1['term_taxonomy_id']);
			
			// Posts
			minsert( 'Marshall Office', 'post', $t1, '<address>830 West Michigan Avenue<br />
Marshall, MI 49068<br />
<a href="tel:2697819822">269 781 9822</a></address>' );
			minsert( 'Coldwater Office', 'post', $t1, '<address>350 Marshall Street<br />
Coldwater, MI 49036<br />
<a href="tel:8003233622">800 323 3622</a></address>' );
			minsert( 'Albion Office', 'post', $t1, '<address>114 West Erie Street<br />
Albion, MI 49224<br />
<a href="tel:5176293981">517 629 3981</a></address>' );
			minsert( 'Sample Doctor 1', 'post', $t2 );
			minsert( 'Sample Doctor 2', 'post', $t2 );
			minsert( 'Sample Doctor 3', 'post', $t2 );
			minsert( 'Sample Staffer 1', 'post', $t3 );
			minsert( 'Sample Staffer 2', 'post', $t3 );
			minsert( 'Sample Staffer 3', 'post', $t3 );
			minsert( 'Sample Special 1', 'post', $t4 );
			minsert( 'Sample Special 2', 'post', $t4 );
			minsert( 'Sample Special 3', 'post', $t4 );
			minsert( 'Sample Event 1', 'post', $t5 );
			minsert( 'Sample Event 2', 'post', $t5 );
			minsert( 'Sample Event 3', 'post', $t5 );
			minsert( 'Sample Slider 1', 'post', $t6 );
			minsert( 'Sample Slider 2', 'post', $t6 );
			minsert( 'Sample Slider 3', 'post', $t6 );
			
			$locations = get_theme_mod('nav_menu_locations');
			
			$m1 = wp_create_nav_menu( 'Navigation' );
			$n1 = wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p1, 'menu-item-object' => 'page' ) );
			$n2 = wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p2, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n2, 'menu-item-object-id' => $p3, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n2, 'menu-item-object-id' => $p4, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n2, 'menu-item-object-id' => $p5, 'menu-item-object' => 'page' ) );
			$n3 = wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p6, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n3, 'menu-item-object-id' => $p7, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n3, 'menu-item-object-id' => $p13, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n3, 'menu-item-object-id' => $p14, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n3, 'menu-item-object-id' => $p15, 'menu-item-object' => 'page' ) );
			$n4 = wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p16, 'menu-item-object' => 'page' ) );
			$n5 = wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p17, 'menu-item-object' => 'page' ) );
			$n6 = wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p18, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n6, 'menu-item-object-id' => $p19, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n6, 'menu-item-object-id' => $p20, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n6, 'menu-item-object-id' => $p21, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n6, 'menu-item-object-id' => $p22, 'menu-item-object' => 'page' ) );
			$n7 = wp_update_nav_menu_item( $m1, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p23, 'menu-item-object' => 'page' ) );
			$locations['navigation'] = $m1;
			
			$m2 = wp_create_nav_menu( 'Footer' );
			// Navigation
			$n8 = wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p1, 'menu-item-object' => 'page', 'menu-item-title' => 'Navigation' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n8, 'menu-item-object-id' => $p1, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n8, 'menu-item-object-id' => $p16, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n8, 'menu-item-object-id' => $p17, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n8, 'menu-item-object-id' => $p23, 'menu-item-object' => 'page' ) );
			// About Us
			$n9 = wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p2, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n9, 'menu-item-object-id' => $p3, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n9, 'menu-item-object-id' => $p4, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n9, 'menu-item-object-id' => $p5, 'menu-item-object' => 'page' ) );
			// Services
			$n10 = wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p6, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n10, 'menu-item-object-id' => $p7, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n10, 'menu-item-object-id' => $p13, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n10, 'menu-item-object-id' => $p14, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n10, 'menu-item-object-id' => $p15, 'menu-item-object' => 'page' ) );
			// Conditions
			$n11 = wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p7, 'menu-item-object' => 'page', 'menu-item-title' => 'Conditions' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n11, 'menu-item-object-id' => $p8, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n11, 'menu-item-object-id' => $p9, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n11, 'menu-item-object-id' => $p10, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n11, 'menu-item-object-id' => $p11, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n11, 'menu-item-object-id' => $p12, 'menu-item-object' => 'page' ) );
			// Resources
			$n12 = wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p18, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n12, 'menu-item-object-id' => $p19, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n12, 'menu-item-object-id' => $p20, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n12, 'menu-item-object-id' => $p21, 'menu-item-object' => 'page' ) );
			wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-parent-id' => $n12, 'menu-item-object-id' => $p22, 'menu-item-object' => 'page' ) );
			// Schedule Appointment
			$n13 = wp_update_nav_menu_item( $m2, 0, array( 'menu-item-type' => 'post_type', 'menu-item-object-id' => $p24, 'menu-item-object' => 'page' ) );
			$locations['footer'] = $m2;
			
			set_theme_mod('nav_menu_locations', $locations);
			
			update_option('permalink_structure', '/%category%/%postname%/');
			update_option('thumbnail_size_w', 200);
			update_option('thumbnail_crop', 1);
			update_option('medium_size_w', 300);
			update_option('large_size_w', 620);
			
			echo 'Build successful.<br />';
		}
	} add_action( 'init' , 'build_smo' );
	
	$i = 1;
	
	function minsert( $title, $type, $parent = null, $content = null, $date = null ) {
		global $i;
		
		$safe_date = ( $i < 60 ) ? '2011-10-04 00:'.sprintf("%02s",$i).':00' : '2011-10-04 01:'.sprintf("%02s",$i-60).':00';
		
		$parent_or_category = ( $type == 'post' ) ? 'post_category' : 'post_parent';
		
		$post_content = ( $content ) ? $content : 'Sorry, but this content is not yet available. Please check back as we are continually updating the site. Thank you!';
		
		$post_date = ( $date ) ? $date : $safe_date;
		
		$minsert = wp_insert_post( array(
			'post_title' => $title,
			'post_content' => $post_content,
			'post_author' => 1,
			'post_date' => $post_date,
			$parent_or_category => $parent,
			'post_status' => 'publish',
			'post_type' => $type
		) );
		
		$i++;
		
		return $minsert;
	}
	
	// Display plugin page.
	function build_smo_options() {
		if ( ! current_user_can('manage_options') ) wp_die( __('You do not have sufficient permissions to access this page.') ); ?>
	
	<div class="wrap">
		<h2>Build SMO</h2>
		<p><?php _e("This tool creates the initial content for the SMO theme."); ?></p>
		
		<form action="" id="build-form" method="post">
			<input type="hidden" name="papertower" />
			<input type="submit" class="button" name="submit" value="Build" />
		</form>
	</div><!-- .wrap -->

<?php } endif; ?>