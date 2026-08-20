<?php
/**
 * Plugin Name: NSP Compliance & Storefront Extras
 * Description: Hemp compliance extras for a WooCommerce Storefront store: age gate,
 *              farm-bill disclaimer bar, policy pages + footer menu, Visa/MasterCard
 *              "We Accept" badges and the FDA notice (mirrors deeprunshop.com).
 * Version:     0.1.0
 * Author:      NextStage Payments
 * License:     GPL-2.0-or-later
 */

namespace NSP\Compliance;

defined( 'ABSPATH' ) || exit;

define( 'NSP_COMPLIANCE_VERSION', '0.1.0' );

// ==== EDIT ME: store details used across compliance pages and the footer. =====
if ( ! defined( 'NSP_COMPANY_NAME' ) ) {
	define( 'NSP_COMPANY_NAME', 'My Hemp Store' );
}
if ( ! defined( 'NSP_COMPANY_LEGAL' ) ) {
	define( 'NSP_COMPANY_LEGAL', 'My Hemp Store LLC' );
}
if ( ! defined( 'NSP_COMPANY_ADDRESS' ) ) {
	define( 'NSP_COMPANY_ADDRESS', '123 Main Street, Anytown, USA 12345' );
}
if ( ! defined( 'NSP_PHONE' ) ) {
	define( 'NSP_PHONE', '' );
}
if ( ! defined( 'NSP_EMAIL' ) ) {
	define( 'NSP_EMAIL', 'hello@myhempstore.com' );
}

const PAGE_SLUGS = array(
	'coas'            => 'COA&#8217;s',
	'privacy-policy'  => 'Privacy Policy',
	'refund-policy'   => 'Refund Policy',
	'terms-conditions'=> 'Terms &amp; Conditions',
	'shipping-policy' => 'Shipping Policy',
	'contact'         => 'Contact Us',
);

function plugin_dir() {
	return plugin_dir_path( __FILE__ );
}

// ===== Content ================================================================

function contact_block_html() {
	$phone = defined( 'NSP_PHONE' ) && NSP_PHONE ? '<br>Phone: <a href="tel:' . esc_attr( preg_replace( '/[^0-9+]/', '', NSP_PHONE ) ) . '">' . esc_html( NSP_PHONE ) . '</a>' : '';
	return '<p><strong>' . esc_html( NSP_COMPANY_NAME ) . '</strong><br>'
		. esc_html( NSP_COMPANY_LEGAL ) . '<br>'
		. esc_html( NSP_COMPANY_ADDRESS ) . $phone
		. '<br>Email: <a href="mailto:' . esc_attr( NSP_EMAIL ) . '">' . esc_html( NSP_EMAIL ) . '</a></p>';
}

function page_content( $slug ) {
	$mail      = '<a href="mailto:' . esc_attr( NSP_EMAIL ) . '">' . esc_html( NSP_EMAIL ) . '</a>';
	$effective = '<p><em>Effective: ' . esc_html( date_i18n( 'F Y' ) ) . '</em></p>';
	$updated   = '<p><em>Last updated: ' . esc_html( date_i18n( 'F Y' ) ) . '</em></p>';
	$company   = esc_html( NSP_COMPANY_NAME );
	$legal     = esc_html( NSP_COMPANY_LEGAL );
	$address   = esc_html( NSP_COMPANY_ADDRESS );

	switch ( $slug ) {
		case 'coas':
			return <<<HTML
<p>Certificates of Analysis (COAs) for our products. Lab results verify cannabinoid content and quality. Certificates for the current production batches are available below as they are published.</p>
<h2>Available Certificates</h2>
<p>COA documents will be listed here as they become available for each batch. Please <a href="/contact/">contact us</a> if you need a certificate for a specific product or lot number.</p>
HTML;
		case 'privacy-policy':
			return $effective . <<<HTML
<h2>Information We Collect</h2>
<p>We collect information you provide directly to us, such as when you contact us, place an order, or subscribe to communications. This may include name, email address, phone number, shipping address, billing information, and order details.</p>
<p>We may also collect technical information automatically (browser type, IP address, pages visited) for analytics, security, and site improvement.</p>
<h2>How We Use Your Information</h2>
<ul>
<li>To process and fulfill orders</li>
<li>To respond to inquiries and provide customer service</li>
<li>To communicate about products, orders, and updates</li>
<li>To improve our website and services</li>
<li>To comply with legal obligations</li>
</ul>
<h2>Information Sharing</h2>
<p>We do not sell your personal information. We may share data with trusted service providers (such as payment processors or shipping carriers) solely to perform services on our behalf. We may also disclose information if required by law or to protect our rights.</p>
<h2>Data Security</h2>
<p>We implement reasonable security measures to protect your information. However, no method of transmission over the Internet is 100% secure.</p>
<h2>Your Choices</h2>
<p>You may opt out of marketing emails at any time using the unsubscribe link. You can request access, correction, or deletion of your personal information by contacting us.</p>
<h2>Children&#8217;s Privacy</h2>
<p>This site is intended for adults 21 years and older. We do not knowingly collect information from children under 21.</p>
<h2>Changes to This Policy</h2>
<p>We may update this Privacy Policy periodically. Changes will be posted on this page with an updated effective date.</p>
<h2>Contact Us</h2>
HTML . contact_block_html();
		case 'refund-policy':
			return <<<HTML
<p><strong>Our policy lasts 30 days.</strong> If 30 days have gone by since your purchase, unfortunately we can&#8217;t offer you a refund or exchange.</p>
<h2>Eligibility for Returns</h2>
<p>To be eligible for a return, your item must be unused and in the same condition that you received it. It must also be in the original packaging.</p>
<h2>How to Return</h2>
<p>Please contact us at {$mail} for return authorization.</p>
<h2>Refunds</h2>
<p>Once your return is received and inspected, we will notify you. If approved, your refund will be processed and applied to your original payment method within a certain number of days.</p>
<h2>Sale Items</h2>
<p>Only regular priced items may be refunded. Sale items cannot be refunded.</p>
<h2>Exchanges</h2>
<p>We only replace items if they are defective or damaged. Contact us to arrange an exchange for the same item.</p>
<h2>Shipping Returns</h2>
<p>You will be responsible for paying return shipping costs (non-refundable). If approved for a refund, return shipping costs may be deducted.</p>
<p><strong>Need to start a return?</strong> Please use our <a href="/contact/">contact form</a> or email {$mail}.</p>
HTML;
		case 'terms-conditions':
			return $updated . <<<HTML
<h2>Overview</h2>
<p>This website is operated by {$company}. Throughout the site, the terms "we", "us" and "our" refer to {$legal}.</p>
<p>By visiting our site and/or purchasing something from us, you agree to be bound by the following terms and conditions, including any additional policies referenced on this site.</p>
<h2>Online Store Terms</h2>
<p>By agreeing to these Terms, you represent that you are at least 21 years of age (required for hemp-derived products) and the age of majority in your state of residence.</p>
<p>You may not use our products for any illegal or unauthorized purpose. You must not transmit any worms, viruses, or destructive code.</p>
<h2>Products and Pricing</h2>
<p>Certain products may be available exclusively online. Prices are subject to change without notice. We reserve the right to modify or discontinue products or the Service at any time.</p>
<h2>Billing and Account Information</h2>
<p>We reserve the right to refuse or limit any order. You agree to provide current, complete and accurate purchase information.</p>
<h2>Prohibited Uses</h2>
<p>You are prohibited from using the site for unlawful purposes, to harass or discriminate, to submit false information, to upload malware, or to interfere with security features.</p>
<h2>Disclaimer of Warranties; Limitation of Liability</h2>
<p>The service and all products are provided "as is" and "as available" without warranties of any kind. In no case shall we be liable for any direct, indirect, incidental, or consequential damages arising from your use of the site or products.</p>
<h2>Governing Law</h2>
<p>These Terms shall be governed by the laws of your state of residence. Our address: {$address}.</p>
<h2>Contact Information</h2>
<p>Questions about the Terms of Service should be sent via our <a href="/contact/">contact page</a> or to {$mail}.</p>
HTML;
		case 'shipping-policy':
			return $effective . <<<HTML
<h2>Order Processing</h2>
<p>Orders are typically processed within 1&#8211;3 business days after payment confirmation (excluding weekends and holidays). You will receive a confirmation once your order has been placed, and tracking information when available after shipment.</p>
<h2>Shipping Rates &amp; Free Shipping</h2>
<p>Free standard shipping is available on qualifying orders over $85 within the contiguous United States, unless otherwise stated at checkout. Orders under $85 are charged a flat shipping rate of $8.00. Exact rates are shown before you complete payment.</p>
<h2>Delivery Timeframes</h2>
<p>Once shipped, standard delivery within the contiguous U.S. typically takes <strong>3&#8211;7 business days</strong>, depending on your location and the carrier. Delivery estimates are not guarantees.</p>
<h2>Shipping Destinations</h2>
<p>We currently ship within the United States. We do not ship to locations where hemp-derived products are restricted or prohibited by law. You are responsible for ensuring products are legal to receive in your jurisdiction.</p>
<p>Must be <strong>21 years of age or older</strong> to purchase. Signature or age verification may be required by the carrier for certain shipments.</p>
<h2>Order Tracking</h2>
<p>When a tracking number is available, we will share it by email. Please allow up to 24 hours after the shipped notice for tracking to update in the carrier system.</p>
<h2>Incorrect or Incomplete Addresses</h2>
<p>Please double-check your shipping address before submitting your order. We are not responsible for packages lost or delayed due to incorrect or incomplete addresses provided by the customer. Additional shipping charges may apply to reship orders returned for address errors.</p>
<h2>Lost, Stolen, or Damaged Packages</h2>
<p>If your package appears lost in transit or arrives damaged, contact us as soon as possible with your order number and photos (for damage). We will work with the carrier and, when appropriate, arrange a replacement or refund according to our <a href="/refund-policy/">Refund Policy</a>.</p>
<h2>Contact Us</h2>
HTML . contact_block_html();
		case 'contact':
			return <<<HTML
<p>We welcome questions about our products, wholesale opportunities, and orders.</p>
<h2>Get in Touch</h2>
HTML . contact_block_html() . '
<p>To reach us by email, use your mail client: <a href="mailto:' . esc_attr( NSP_EMAIL ) . '">' . esc_html( NSP_EMAIL ) . '</a> or call ' . esc_html( NSP_PHONE ? NSP_PHONE : 'the number above' ) . '.</p>';
	}
	return '';
}

// ===== Page + menu installation (idempotent) ==================================

function ensure_pages_and_menu() {
	$installed = get_option( 'nsp_compliance_installed' );
	if ( $installed === NSP_COMPLIANCE_VERSION ) {
		return;
	}

	if ( empty( $GLOBALS['wp_rewrite'] ) ) {
		$GLOBALS['wp_rewrite'] = new WP_Rewrite();
	}

	$page_ids = array();
	foreach ( PAGE_SLUGS as $slug => $title ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );
		if ( $existing ) {
			$page_ids[ $slug ] = $existing->ID;
			continue;
		}
		$id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_name'    => $slug,
			'post_title'   => $title,
			'post_content' => page_content( $slug ),
		) );
		if ( ! is_wp_error( $id ) ) {
			$page_ids[ $slug ] = $id;
		}
	}

	$menu = wp_get_nav_menu_object( 'Policies' );
	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( 'Policies' );
	} else {
		$menu_id = (int) $menu->term_id;
	}
	if ( is_wp_error( $menu_id ) || ! $menu_id ) {
		return;
	}

	foreach ( $page_ids as $slug => $page_id ) {
		$items = wp_get_nav_menu_items( $menu_id );
		$found = false;
		$items = is_array( $items ) ? $items : array();
		foreach ( $items as $item ) {
			if ( (int) $item->object_id === (int) $page_id ) {
				$found = true;
				break;
			}
		}
		if ( ! $found ) {
			wp_update_nav_menu_item( $menu_id, 0, array(
				'menu-item-object-id' => $page_id,
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			) );
		}
	}

	$locations              = get_theme_mod( 'nav_menu_locations', array() );
	$locations['footer']    = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );

	update_option( 'nsp_compliance_installed', NSP_COMPLIANCE_VERSION );
}

// ===== Frontend output ========================================================

function disclaimer_bar() {
	echo '<div class="nsp-disclaimer">All products are less than .3% Delta 9 THC as required by the 2018 Farm Bill.</div>';
}
add_action( 'wp_body_open', __NAMESPACE__ . '\\disclaimer_bar', 0 );

function age_gate() {
	$html = '<div id="nsp-age-gate" role="dialog" aria-modal="true" aria-labelledby="nsp-age-title" hidden>
	<div class="nsp-age-box">
		<h2 id="nsp-age-title">Are you 21 or older?</h2>
		<p>This website offers hemp-derived products containing cannabinoids. You must be <strong>21 years of age or older</strong> to enter.</p>
		<div class="nsp-age-actions">
			<button type="button" class="nsp-age-yes">Yes, I am 21+</button>
			<button type="button" class="nsp-age-no">No, I am under 21</button>
		</div>
		<small>By entering you confirm you are of legal age to view this site.</small>
	</div>
	</div>';
	echo $html;
}
add_action( 'wp_footer', __NAMESPACE__ . '\\age_gate', 0 );

function payment_badges_html() {
	$visa = 'data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2072%2048%22%20role%3D%22img%22%20aria-label%3D%22Visa%22%3E%3Crect%20width%3D%2272%22%20height%3D%2248%22%20rx%3D%226%22%20fill%3D%22%231A1F71%22%2F%3E%3Ctext%20x%3D%2236%22%20y%3D%2231%22%20text-anchor%3D%22middle%22%20font-family%3D%22Arial%2CHelvetica%2Csans-serif%22%20font-size%3D%2218%22%20font-weight%3D%22700%22%20font-style%3D%22italic%22%20fill%3D%22%23FFFFFF%22%20letter-spacing%3D%221%22%3EVISA%3C%2Ftext%3E%3C%2Fsvg%3E';
	$mc   = 'data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2072%2048%22%20role%3D%22img%22%20aria-label%3D%22MasterCard%22%3E%3Crect%20width%3D%2272%22%20height%3D%2248%22%20rx%3D%226%22%20fill%3D%22%23252525%22%2F%3E%3Ccircle%20cx%3D%2230%22%20cy%3D%2224%22%20r%3D%2212%22%20fill%3D%22%23EB001B%22%2F%3E%3Ccircle%20cx%3D%2242%22%20cy%3D%2224%22%20r%3D%2212%22%20fill%3D%22%23F79E1B%22%2F%3E%3Cpath%20d%3D%22M36%2015.2a12%2012%200%200%201%200%2017.6%2012%2012%200%200%201%200-17.6z%22%20fill%3D%22%23FF5F00%22%2F%3E%3C%2Fsvg%3E';
	return '<span class="nsp-badge"><img src="' . esc_attr( $visa ) . '" alt="Visa" width="48" height="32" loading="lazy"></span>'
		. '<span class="nsp-badge"><img src="' . esc_attr( $mc ) . '" alt="MasterCard" width="48" height="32" loading="lazy"></span>';
}

function storefront_footer_extras() {
	echo '<div class="nsp-footer-legal">
		<div class="nsp-footer-col nsp-footer-contact">'
		. apply_filters( 'nsp_compliance_contact_html', '<p><strong>' . esc_html( NSP_COMPANY_NAME ) . '</strong><br>' . esc_html( NSP_COMPANY_ADDRESS ) . '</p>' ) .
		'</div>
		<div class="nsp-footer-col nsp-footer-accept">
			<p class="nsp-accept-title">We Accept</p>
			' . payment_badges_html() . '
			<p class="nsp-age-note">Must be 21 or older to purchase.</p>
		</div>
		<div class="nsp-footer-col nsp-footer-fda">
			<p>The statements made regarding these products have not been evaluated by the Food and Drug Administration. The efficacy of these products has not been confirmed by FDA-approved research. These products are not intended to diagnose, treat, cure or prevent any disease. All information presented here is not meant as a substitute for or alternative to information from health care practitioners. Please consult your health care professional about potential interactions or other possible complications before using any product. The Federal Food, Drug, and Cosmetic Act require this notice.</p>
		</div>
	</div>';
}
add_action( 'storefront_after_footer', __NAMESPACE__ . '\\storefront_footer_extras', 20 );

function enqueue_assets() {
	wp_enqueue_style( 'nsp-compliance', plugin_dir_url( __FILE__ ) . 'assets/nsp-compliance.css', array(), NSP_COMPLIANCE_VERSION );
	wp_enqueue_script( 'nsp-compliance', plugin_dir_url( __FILE__ ) . 'assets/nsp-compliance.js', array(), NSP_COMPLIANCE_VERSION, true );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_assets' );

add_action( 'init', __NAMESPACE__ . '\\ensure_pages_and_menu', 20 );