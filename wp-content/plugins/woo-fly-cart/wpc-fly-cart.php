<?php
/*
Plugin Name: WPC Fly Cart for WooCommerce
Plugin URI: https://wpclever.net/
Description: WooCommerce interaction mini cart with many styles and effects.
Version: 5.1.3
Author: WPClever
Author URI: https://wpclever.net
Text Domain: woo-fly-cart
Domain Path: /languages/
Requires at least: 4.0
Tested up to: 6.0
WC requires at least: 3.0
WC tested up to: 6.9
*/

defined( 'ABSPATH' ) || exit;

! defined( 'WOOFC_VERSION' ) && define( 'WOOFC_VERSION', '5.1.3' );
! defined( 'WOOFC_FILE' ) && define( 'WOOFC_FILE', __FILE__ );
! defined( 'WOOFC_URI' ) && define( 'WOOFC_URI', plugin_dir_url( __FILE__ ) );
! defined( 'WOOFC_DIR' ) && define( 'WOOFC_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'WOOFC_REVIEWS' ) && define( 'WOOFC_REVIEWS', 'https://wordpress.org/support/plugin/woo-fly-cart/reviews/?filter=5' );
! defined( 'WOOFC_CHANGELOG' ) && define( 'WOOFC_CHANGELOG', 'https://wordpress.org/plugins/woo-fly-cart/#developers' );
! defined( 'WOOFC_DISCUSSION' ) && define( 'WOOFC_DISCUSSION', 'https://wordpress.org/support/plugin/woo-fly-cart' );
! defined( 'WPC_URI' ) && define( 'WPC_URI', WOOFC_URI );

include 'includes/wpc-dashboard.php';
include 'includes/wpc-menu.php';
include 'includes/wpc-kit.php';

if ( ! function_exists( 'woofc_init' ) ) {
	add_action( 'plugins_loaded', 'woofc_init', 11 );

	function woofc_init() {
		// load text-domain
		load_plugin_textdomain( 'woo-fly-cart', false, basename( __DIR__ ) . '/languages/' );

		if ( ! function_exists( 'WC' ) || ! version_compare( WC()->version, '3.0', '>=' ) ) {
			add_action( 'admin_notices', 'woofc_notice_wc' );

			return;
		}

		if ( ! class_exists( 'WPCleverWoofc' ) && class_exists( 'WC_Product' ) ) {
			class WPCleverWoofc {
				protected static $localization = array();
				protected static $instance = null;

				public static function instance() {
					if ( is_null( self::$instance ) ) {
						self::$instance = new self();
					}

					return self::$instance;
				}

				function __construct() {
					add_action( 'init', [ $this, 'init' ] );
					add_action( 'wp_footer', [ $this, 'footer' ] );
					add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
					add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
					add_action( 'admin_init', [ $this, 'register_settings' ] );
					add_action( 'admin_menu', [ $this, 'admin_menu' ] );
					add_filter( 'plugin_action_links', [ $this, 'action_links' ], 10, 2 );
					add_filter( 'plugin_row_meta', [ $this, 'row_meta' ], 10, 2 );
					add_filter( 'wp_nav_menu_items', [ $this, 'nav_menu_items' ], 99, 2 );
					add_filter( 'woocommerce_add_to_cart_fragments', [ $this, 'cart_fragment' ] );
					add_filter( 'woocommerce_update_order_review_fragments', [ $this, 'cart_fragment' ] );

					// ajax
					add_action( 'wp_ajax_woofc_update_qty', [ $this, 'update_qty' ] );
					add_action( 'wp_ajax_nopriv_woofc_update_qty', [ $this, 'update_qty' ] );
					add_action( 'wp_ajax_woofc_remove_item', [ $this, 'remove_item' ] );
					add_action( 'wp_ajax_nopriv_woofc_remove_item', [ $this, 'remove_item' ] );
					add_action( 'wp_ajax_woofc_undo_remove', [ $this, 'undo_remove' ] );
					add_action( 'wp_ajax_nopriv_woofc_undo_remove', [ $this, 'undo_remove' ] );
					add_action( 'wp_ajax_woofc_empty_cart', [ $this, 'empty_cart' ] );
					add_action( 'wp_ajax_nopriv_woofc_empty_cart', [ $this, 'empty_cart' ] );
				}

				function init() {
					self::$localization = (array) get_option( '_woofc_localization' );
				}

				function localization( $key = '', $default = '' ) {
					$str = '';

					if ( ! empty( $key ) && ! empty( self::$localization[ $key ] ) ) {
						$str = self::$localization[ $key ];
					} elseif ( ! empty( $default ) ) {
						$str = $default;
					}

					return apply_filters( 'woofc_localization_' . $key, $str );
				}

				function enqueue_scripts() {
					// disable on some pages
					if ( apply_filters( 'woofc_disable', false ) ) {
						return;
					}

					// hint css
					wp_enqueue_style( 'hint', WOOFC_URI . 'assets/hint/hint.min.css' );

					// perfect srollbar
					if ( apply_filters( 'woofc_perfect_scrollbar', get_option( '_woofc_perfect_scrollbar', 'yes' ) ) === 'yes' ) {
						wp_enqueue_style( 'perfect-scrollbar', WOOFC_URI . 'assets/perfect-scrollbar/css/perfect-scrollbar.min.css' );
						wp_enqueue_style( 'perfect-scrollbar-wpc', WOOFC_URI . 'assets/perfect-scrollbar/css/custom-theme.css' );
						wp_enqueue_script( 'perfect-scrollbar', WOOFC_URI . 'assets/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js', array( 'jquery' ), WOOFC_VERSION, true );
					}

					// slick
					if ( ( apply_filters( 'woofc_slick', 'yes' ) === 'yes' ) && ( get_option( '_woofc_suggested', 'cross_sells' ) !== 'none' ) ) {
						wp_enqueue_style( 'slick', WOOFC_URI . 'assets/slick/slick.css' );
						wp_enqueue_script( 'slick', WOOFC_URI . 'assets/slick/slick.min.js', array( 'jquery' ), WOOFC_VERSION, true );
					}

					// main
					if ( ! apply_filters( 'woofc_disable_font_icon', false ) ) {
						wp_enqueue_style( 'woofc-fonts', WOOFC_URI . 'assets/css/fonts.css' );
					}

					// css
					wp_enqueue_style( 'woofc-frontend', WOOFC_URI . 'assets/css/frontend.css', array(), WOOFC_VERSION );
					$color      = get_option( '_woofc_color', '#cc6055' );
					$bg_image   = get_option( '_woofc_bg_image', '' ) !== '' ? wp_get_attachment_url( get_option( '_woofc_bg_image', '' ) ) : '';
					$inline_css = ".woofc-area.woofc-style-01 .woofc-inner, .woofc-area.woofc-style-03 .woofc-inner, .woofc-area.woofc-style-02 .woofc-area-bot .woofc-action .woofc-action-inner > div a:hover, .woofc-area.woofc-style-04 .woofc-area-bot .woofc-action .woofc-action-inner > div a:hover {
                            background-color: {$color};
                        }

                        .woofc-area.woofc-style-01 .woofc-area-bot .woofc-action .woofc-action-inner > div a, .woofc-area.woofc-style-02 .woofc-area-bot .woofc-action .woofc-action-inner > div a, .woofc-area.woofc-style-03 .woofc-area-bot .woofc-action .woofc-action-inner > div a, .woofc-area.woofc-style-04 .woofc-area-bot .woofc-action .woofc-action-inner > div a {
                            outline: none;
                            color: {$color};
                        }

                        .woofc-area.woofc-style-02 .woofc-area-bot .woofc-action .woofc-action-inner > div a, .woofc-area.woofc-style-04 .woofc-area-bot .woofc-action .woofc-action-inner > div a {
                            border-color: {$color};
                        }

                        .woofc-area.woofc-style-05 .woofc-inner{
                            background-color: {$color};
                            background-image: url('{$bg_image}');
                            background-size: cover;
                            background-position: center;
                            background-repeat: no-repeat;
                        }
                        
                        .woofc-count span {
                            background-color: {$color};
                        }";
					wp_add_inline_style( 'woofc-frontend', $inline_css );

					// js
					wp_enqueue_script( 'woofc-frontend', WOOFC_URI . 'assets/js/frontend.js', array( 'jquery', ), WOOFC_VERSION, true );
					wp_localize_script( 'woofc-frontend', 'woofc_vars', array(
							'ajax_url'            => admin_url( 'admin-ajax.php' ),
							'nonce'               => wp_create_nonce( 'woofc-security' ),
							'scrollbar'           => get_option( '_woofc_perfect_scrollbar', 'yes' ),
							'auto_show'           => get_option( '_woofc_auto_show_ajax', 'yes' ),
							'undo_remove'         => get_option( '_woofc_undo_remove', 'yes' ),
							'confirm_remove'      => get_option( '_woofc_confirm_remove', 'no' ),
							'instant_checkout'    => get_option( '_woofc_instant_checkout', 'no' ),
							'confirm_empty'       => get_option( '_woofc_confirm_empty', 'no' ),
							'confirm_empty_text'  => self::localization( 'empty_confirm', esc_html__( 'Do you want to empty the cart?', 'woo-fly-cart' ) ),
							'confirm_remove_text' => self::localization( 'remove_confirm', esc_html__( 'Do you want to remove this item?', 'woo-fly-cart' ) ),
							'undo_remove_text'    => self::localization( 'remove_undo', esc_html__( 'Undo?', 'woo-fly-cart' ) ),
							'removed_text'        => self::localization( 'removed', esc_html__( '%s was removed.', 'woo-fly-cart' ) ),
							'manual_show'         => get_option( '_woofc_manual_show', '' ),
							'reload'              => get_option( '_woofc_reload', 'no' ),
							'slick'               => apply_filters( 'woofc_slick', 'yes' ),
							'slick_params'        => apply_filters( 'woofc_slick_params', json_encode( apply_filters( 'woofc_slick_params_arr', array(
								'slidesToShow'   => 1,
								'slidesToScroll' => 1,
								'dots'           => true,
								'arrows'         => false,
								'autoplay'       => false,
								'autoplaySpeed'  => 3000,
								'rtl'            => is_rtl()
							) ) ) ),
							'is_cart'             => is_cart(),
							'is_checkout'         => is_checkout(),
							'cart_url'            => ( ( get_option( '_woofc_hide_cart_checkout', 'no' ) === 'yes' ) && ( is_cart() || is_checkout() ) ) ? wc_get_cart_url() : '',
							'hide_count_empty'    => get_option( '_woofc_count_hide_empty', 'no' ),
							'wc_checkout_js'      => defined( 'WC_PLUGIN_FILE' ) ? plugins_url( 'assets/js/frontend/checkout.js', WC_PLUGIN_FILE ) : '',
						)
					);
				}

				function admin_enqueue_scripts( $hook ) {
					wp_enqueue_style( 'woofc-backend', WOOFC_URI . 'assets/css/backend.css', array(), WOOFC_VERSION );

					if ( strpos( $hook, 'woofc' ) ) {
						wp_enqueue_media();
						add_thickbox();
						wp_enqueue_style( 'wp-color-picker' );
						wp_enqueue_style( 'fonticonpicker', WOOFC_URI . 'assets/fonticonpicker/css/jquery.fonticonpicker.css' );
						wp_enqueue_script( 'fonticonpicker', WOOFC_URI . 'assets/fonticonpicker/js/jquery.fonticonpicker.min.js', array( 'jquery' ) );
						wp_enqueue_style( 'woofc-fonts', WOOFC_URI . 'assets/css/fonts.css' );
						wp_enqueue_script( 'woofc-backend', WOOFC_URI . 'assets/js/backend.js', array(
							'jquery',
							'wp-color-picker'
						) );
					}
				}

				function action_links( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin === $file ) {
						$settings             = '<a href="' . admin_url( 'admin.php?page=wpclever-woofc&tab=settings' ) . '">' . esc_html__( 'Settings', 'woo-fly-cart' ) . '</a>';
						$links['wpc-premium'] = '<a href="' . admin_url( 'admin.php?page=wpclever-woofc&tab=premium' ) . '">' . esc_html__( 'Premium Version', 'woo-fly-cart' ) . '</a>';
						array_unshift( $links, $settings );
					}

					return (array) $links;
				}

				function row_meta( $links, $file ) {
					static $plugin;

					if ( ! isset( $plugin ) ) {
						$plugin = plugin_basename( __FILE__ );
					}

					if ( $plugin === $file ) {
						$row_meta = array(
							'support' => '<a href="' . esc_url( WOOFC_DISCUSSION ) . '" target="_blank">' . esc_html__( 'Community support', 'woo-fly-cart' ) . '</a>',
						);

						return array_merge( $links, $row_meta );
					}

					return (array) $links;
				}

				function register_settings() {
					// settings
					register_setting( 'woofc_settings', '_woofc_auto_show_ajax' );
					register_setting( 'woofc_settings', '_woofc_auto_show_normal' );
					register_setting( 'woofc_settings', '_woofc_manual_show' );
					register_setting( 'woofc_settings', '_woofc_reverse_items' );
					register_setting( 'woofc_settings', '_woofc_overlay_layer' );
					register_setting( 'woofc_settings', '_woofc_perfect_scrollbar' );
					register_setting( 'woofc_settings', '_woofc_position' );
					register_setting( 'woofc_settings', '_woofc_effect' );
					register_setting( 'woofc_settings', '_woofc_style' );
					register_setting( 'woofc_settings', '_woofc_color' );
					register_setting( 'woofc_settings', '_woofc_bg_image' );
					register_setting( 'woofc_settings', '_woofc_close' );
					register_setting( 'woofc_settings', '_woofc_link' );
					register_setting( 'woofc_settings', '_woofc_price' );
					register_setting( 'woofc_settings', '_woofc_data' );
					register_setting( 'woofc_settings', '_woofc_save_for_later' );
					register_setting( 'woofc_settings', '_woofc_subtotal' );
					register_setting( 'woofc_settings', '_woofc_coupon' );
					register_setting( 'woofc_settings', '_woofc_coupon_listing' );
					register_setting( 'woofc_settings', '_woofc_shipping_cost' );
					register_setting( 'woofc_settings', '_woofc_shipping_calculator' );
					register_setting( 'woofc_settings', '_woofc_free_shipping_bar' );
					register_setting( 'woofc_settings', '_woofc_total' );
					register_setting( 'woofc_settings', '_woofc_buttons' );
					register_setting( 'woofc_settings', '_woofc_instant_checkout' );
					register_setting( 'woofc_settings', '_woofc_suggested' );
					register_setting( 'woofc_settings', '_woofc_suggested_limit' );
					register_setting( 'woofc_settings', '_woofc_empty' );
					register_setting( 'woofc_settings', '_woofc_confirm_empty' );
					register_setting( 'woofc_settings', '_woofc_share' );
					register_setting( 'woofc_settings', '_woofc_continue' );
					register_setting( 'woofc_settings', '_woofc_continue_url' );
					register_setting( 'woofc_settings', '_woofc_confirm_remove' );
					register_setting( 'woofc_settings', '_woofc_undo_remove' );
					register_setting( 'woofc_settings', '_woofc_reload' );
					register_setting( 'woofc_settings', '_woofc_hide_cart_checkout' );
					register_setting( 'woofc_settings', '_woofc_menus' );
					register_setting( 'woofc_settings', '_woofc_count' );
					register_setting( 'woofc_settings', '_woofc_count_position' );
					register_setting( 'woofc_settings', '_woofc_count_icon' );
					register_setting( 'woofc_settings', '_woofc_count_hide_empty' );

					// localization
					register_setting( 'woofc_localization', '_woofc_localization' );
				}

				function admin_menu() {
					add_submenu_page( 'wpclever', esc_html__( 'WPC Fly Cart', 'woo-fly-cart' ), esc_html__( 'Fly Cart', 'woo-fly-cart' ), 'manage_options', 'wpclever-woofc', array(
						$this,
						'admin_menu_content'
					) );
				}

				function admin_menu_content() {
					$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'settings';
					?>
                    <div class="wpclever_settings_page wrap">
                        <h1 class="wpclever_settings_page_title"><?php echo esc_html__( 'WPC Fly Cart', 'woo-fly-cart' ) . ' ' . WOOFC_VERSION; ?></h1>
                        <div class="wpclever_settings_page_desc about-text">
                            <p>
								<?php printf( esc_html__( 'Thank you for using our plugin! If you are satisfied, please reward it a full five-star %s rating.', 'woo-fly-cart' ), '<span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span>' ); ?>
                                <br/>
                                <a href="<?php echo esc_url( WOOFC_REVIEWS ); ?>"
                                   target="_blank"><?php esc_html_e( 'Reviews', 'woo-fly-cart' ); ?></a> | <a
                                        href="<?php echo esc_url( WOOFC_CHANGELOG ); ?>"
                                        target="_blank"><?php esc_html_e( 'Changelog', 'woo-fly-cart' ); ?></a>
                                | <a href="<?php echo esc_url( WOOFC_DISCUSSION ); ?>"
                                     target="_blank"><?php esc_html_e( 'Discussion', 'woo-fly-cart' ); ?></a>
                            </p>
                        </div>
						<?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) { ?>
                            <div class="notice notice-success is-dismissible">
                                <p><?php esc_html_e( 'Settings updated.', 'woo-fly-cart' ); ?></p>
                            </div>
						<?php } ?>
                        <div class="wpclever_settings_page_nav">
                            <h2 class="nav-tab-wrapper">
                                <a href="<?php echo admin_url( 'admin.php?page=wpclever-woofc&tab=settings' ); ?>"
                                   class="<?php echo esc_attr( $active_tab === 'settings' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'Settings', 'woo-fly-cart' ); ?>
                                </a>
                                <a href="<?php echo admin_url( 'admin.php?page=wpclever-woofc&tab=localization' ); ?>"
                                   class="<?php echo esc_attr( $active_tab === 'localization' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>">
									<?php esc_html_e( 'Localization', 'woo-fly-cart' ); ?>
                                </a>
                                <a href="<?php echo admin_url( 'admin.php?page=wpclever-woofc&tab=premium' ); ?>"
                                   class="<?php echo esc_attr( $active_tab === 'premium' ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>"
                                   style="color: #c9356e">
									<?php esc_html_e( 'Premium Version', 'woo-fly-cart' ); ?>
                                </a>
                                <a href="<?php echo admin_url( 'admin.php?page=wpclever-kit' ); ?>" class="nav-tab">
									<?php esc_html_e( 'Essential Kit', 'woo-fly-cart' ); ?>
                                </a>
                            </h2>
                        </div>
                        <div class="wpclever_settings_page_content">
							<?php if ( $active_tab === 'settings' ) { ?>
                                <form method="post" action="options.php">
                                    <table class="form-table">
                                        <tr class="heading">
                                            <th><?php esc_html_e( 'General', 'woo-fly-cart' ); ?></th>
                                            <td><?php esc_html_e( 'General settings for the fly cart.', 'woo-fly-cart' ); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Open on AJAX add to cart', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_auto_show_ajax">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_auto_show_ajax', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_auto_show_ajax', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php printf( esc_html__( 'The fly cart will be opened immediately after whenever click to AJAX Add to cart buttons? See %s "Add to cart behaviour" setting %s', 'woo-fly-cart' ), '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products&section=display' ) . '" target="_blank">', '</a>.' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Open on normal add to cart', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_auto_show_normal">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_auto_show_normal', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_auto_show_normal', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'The fly cart will be opened immediately after whenever click to normal Add to cart buttons (AJAX is not enable) or Add to cart button in single product page?', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Reverse items', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_reverse_items">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_reverse_items', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_reverse_items', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Overlay layer', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_overlay_layer">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_overlay_layer', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_overlay_layer', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'If you hide the overlay layer, the buyer still can work on your site when the fly cart is opening.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php esc_html_e( 'Use perfect-scrollbar', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_perfect_scrollbar">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_perfect_scrollbar', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_perfect_scrollbar', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php printf( esc_html__( 'Read more about %s', 'woo-fly-cart' ), '<a href="https://github.com/mdbootstrap/perfect-scrollbar" target="_blank">perfect-scrollbar</a>' ); ?>.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Position', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_position">
                                                    <option value="01" <?php echo esc_attr( get_option( '_woofc_position', '05' ) === '01' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Right', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="02" <?php echo esc_attr( get_option( '_woofc_position', '05' ) === '02' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Left', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="03" <?php echo esc_attr( get_option( '_woofc_position', '05' ) === '03' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Top', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="04" <?php echo esc_attr( get_option( '_woofc_position', '05' ) === '04' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Bottom', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="05" <?php echo esc_attr( get_option( '_woofc_position', '05' ) === '05' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Center', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Effect', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_effect">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_effect', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_effect', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Enable/disable slide effect.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Style', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_style">
                                                    <option value="01" <?php echo esc_attr( get_option( '_woofc_style', '01' ) === '01' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Color background', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="02" <?php echo esc_attr( get_option( '_woofc_style', '01' ) === '02' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'White background', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="03" <?php echo esc_attr( get_option( '_woofc_style', '01' ) === '03' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Color background, no thumbnail', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="04" <?php echo esc_attr( get_option( '_woofc_style', '01' ) === '04' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'White background, no thumbnail', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="05" <?php echo esc_attr( get_option( '_woofc_style', '01' ) === '05' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Background image', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Color', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" name="_woofc_color" id="_woofc_color"
                                                       value="<?php echo get_option( '_woofc_color', '#cc6055' ); ?>"
                                                       class="woofc_color_picker"/>
                                                <span class="description"><?php printf( esc_html__( 'Background & text color, default %s', 'woo-fly-cart' ), '<code>#cc6055</code>' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Background image', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <div class="woofc_image_preview" id="woofc_image_preview">
													<?php if ( get_option( '_woofc_bg_image', '' ) !== '' ) {
														echo '<img src="' . wp_get_attachment_url( get_option( '_woofc_bg_image', '' ) ) . '"/>';
													} ?>
                                                </div>
                                                <input id="woofc_upload_image_button" type="button" class="button"
                                                       value="<?php _e( 'Upload image' ); ?>"/>
                                                <input type="hidden" name="_woofc_bg_image"
                                                       id="woofc_image_attachment_url"
                                                       value="<?php echo get_option( '_woofc_bg_image', '' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Close button', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_close">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_close', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_close', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Show/hide the close button.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Link to individual product', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_link">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_link', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes, open in the same tab', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="yes_blank" <?php echo esc_attr( get_option( '_woofc_link', 'yes' ) === 'yes_blank' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes, open in the new tab', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="yes_popup" <?php echo esc_attr( get_option( '_woofc_link', 'yes' ) === 'yes_popup' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes, open quick view popup', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_link', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description">If you choose "Open quick view popup", please install <a
                                                            href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=woo-smart-quick-view&TB_iframe=true&width=800&height=550' ) ); ?>"
                                                            class="thickbox" title="Install WPC Smart Quick View">WPC Smart Quick View</a> to make it work.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Item price', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_price">
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_price', 'price' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="price" <?php echo esc_attr( get_option( '_woofc_price', 'price' ) === 'price' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Price', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="subtotal" <?php echo esc_attr( get_option( '_woofc_price', 'price' ) === 'subtotal' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Subtotal', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Show/hide the item price or subtotal under title.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Item data', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_data">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_data', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_data', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Show/hide the item data under title.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Save for later', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_save_for_later">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_save_for_later', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_save_for_later', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description">Show/hide the save for later button for each product. If you enable this option, please install and activate <a
                                                            href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wc-save-for-later&TB_iframe=true&width=800&height=550' ) ); ?>"
                                                            class="thickbox" title="Install WPC Save For Later">WPC Save For Later</a> to make it work.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Subtotal', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_subtotal">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_subtotal', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_subtotal', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Coupon', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_coupon">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_coupon', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_coupon', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span style="color: #c9356e">This feature is available for Premium Version only.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Coupon listing', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_coupon_listing">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_coupon_listing', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_coupon_listing', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description">If you enable this option, please install and activate <a
                                                            href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wpc-coupon-listing&TB_iframe=true&width=800&height=550' ) ); ?>"
                                                            class="thickbox" title="Install WPC Coupon Listing">WPC Coupon Listing</a> to make it work.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Shipping cost', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_shipping_cost">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_shipping_cost', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_shipping_cost', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span style="color: #c9356e">This feature is available for Premium Version only.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Shipping calculator', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_shipping_calculator">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_shipping_calculator', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_shipping_calculator', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span style="color: #c9356e">This feature is available for Premium Version only.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Free shipping bar', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_free_shipping_bar">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_free_shipping_bar', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_free_shipping_bar', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description">If you enable this option, please install and activate <a
                                                            href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wpc-free-shipping-bar&TB_iframe=true&width=800&height=550' ) ); ?>"
                                                            class="thickbox" title="Install WPC Free Shipping Bar">WPC Free Shipping Bar</a> to make it work.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Total', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_total">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_total', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_total', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Action buttons', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_buttons">
                                                    <option value="01" <?php echo esc_attr( get_option( '_woofc_buttons', '01' ) === '01' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Cart & Checkout', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="02" <?php echo esc_attr( get_option( '_woofc_buttons', '01' ) === '02' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Cart only', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="03" <?php echo esc_attr( get_option( '_woofc_buttons', '01' ) === '03' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Checkout only', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="hide" <?php echo esc_attr( get_option( '_woofc_buttons', '01' ) === 'hide' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Instant checkout', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_instant_checkout">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_instant_checkout', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_instant_checkout', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'If enable this option, buyer can checkout directly on the fly cart.', 'woo-fly-cart' ); ?></span>
                                                <span style="color: #c9356e">This feature is available for Premium Version only.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Suggested products', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_suggested">
                                                    <option value="cross_sells" <?php echo esc_attr( get_option( '_woofc_suggested', 'cross_sells' ) === 'cross_sells' || get_option( '_woofc_cross_sells', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Cross sells products', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="related" <?php echo esc_attr( get_option( '_woofc_suggested', 'cross_sells' ) === 'related' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Related products', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="both" <?php echo esc_attr( get_option( '_woofc_suggested', 'cross_sells' ) === 'both' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Cross sells & Related products', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="none" <?php echo esc_attr( get_option( '_woofc_suggested', 'cross_sells' ) === 'none' || get_option( '_woofc_cross_sells', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'None', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Suggested products limit', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="number" min="1" step="1" name="_woofc_suggested_limit"
                                                       value="<?php echo get_option( '_woofc_suggested_limit', 10 ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Empty cart', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_empty">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_empty', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_empty', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Show/hide the empty cart button under the product list.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Confirm empty', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_confirm_empty">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_confirm_empty', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_confirm_empty', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Enable/disable confirm before emptying the cart.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Share cart', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_share">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_share', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_share', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description">If you enable this option, please install and activate <a
                                                            href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=plugin-information&plugin=wpc-share-cart&TB_iframe=true&width=800&height=550' ) ); ?>"
                                                            class="thickbox" title="Install WPC Share Cart">WPC Share Cart</a> to make it work.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Continue shopping', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_continue">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_continue', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Show', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_continue', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Hide', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Show/hide the continue shopping button at the end of fly cart.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Continue shopping URL', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="url" name="_woofc_continue_url" class="regular-text code"
                                                       value="<?php echo get_option( '_woofc_continue_url', '' ); ?>"/>
                                                <span class="description"><?php esc_html_e( 'Custom URL for "continue shopping" button. By default, only close the fly cart when clicking on this button.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Confirm remove', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_confirm_remove">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_confirm_remove', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_confirm_remove', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Enable/disable confirm before removing a product.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Undo remove', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_undo_remove">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_undo_remove', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_undo_remove', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Enable/disable undo after removing a product.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Reload the cart', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_reload">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_reload', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_reload', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'The cart will be reloaded when opening the page? If you use the cache for your site, please turn on this option.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Hide on Cart & Checkout', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_hide_cart_checkout">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_hide_cart_checkout', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_hide_cart_checkout', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Hide the fly cart on the Cart and Checkout page.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr class="heading">
                                            <th><?php esc_html_e( 'Bubble', 'woo-fly-cart' ); ?></th>
                                            <td><?php esc_html_e( 'Settings for the bubble.', 'woo-fly-cart' ); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Enable', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_count">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_count', 'yes' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_count', 'yes' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Position', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_count_position">
                                                    <option value="top-left" <?php echo esc_attr( get_option( '_woofc_count_position', 'bottom-left' ) === 'top-left' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Top Left', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="top-right" <?php echo esc_attr( get_option( '_woofc_count_position', 'bottom-left' ) === 'top-right' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Top Right', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="bottom-left" <?php echo esc_attr( get_option( '_woofc_count_position', 'bottom-left' ) === 'bottom-left' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Bottom Left', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="bottom-right" <?php echo esc_attr( get_option( '_woofc_count_position', 'bottom-left' ) === 'bottom-right' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Bottom Right', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Icon', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select id="woofc_count_icon" name="_woofc_count_icon">
													<?php
													for ( $i = 1; $i <= 16; $i ++ ) {
														if ( get_option( '_woofc_count_icon', 'woofc-icon-cart7' ) === 'woofc-icon-cart' . $i ) {
															echo '<option value="woofc-icon-cart' . $i . '" selected>woofc-icon-cart' . $i . '</option>';
														} else {
															echo '<option value="woofc-icon-cart' . $i . '">woofc-icon-cart' . $i . '</option>';
														}
													}
													?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Hide if empty', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <select name="_woofc_count_hide_empty">
                                                    <option value="yes" <?php echo esc_attr( get_option( '_woofc_count_hide_empty', 'no' ) === 'yes' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'Yes', 'woo-fly-cart' ); ?>
                                                    </option>
                                                    <option value="no" <?php echo esc_attr( get_option( '_woofc_count_hide_empty', 'no' ) === 'no' ? 'selected' : '' ); ?>>
														<?php esc_html_e( 'No', 'woo-fly-cart' ); ?>
                                                    </option>
                                                </select>
                                                <span class="description"><?php esc_html_e( 'Hide the bubble if the cart is empty?', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr class="heading">
                                            <th><?php esc_html_e( 'Menu', 'woo-fly-cart' ); ?></th>
                                            <td><?php esc_html_e( 'Settings for cart menu item.', 'woo-fly-cart' ); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Menu', 'woo-fly-cart' ); ?></th>
                                            <td>
												<?php
												$nav_args    = array(
													'hide_empty' => false,
													'fields'     => 'id=>name',
												);
												$nav_menus   = get_terms( 'nav_menu', $nav_args );
												$saved_menus = get_option( '_woofc_menus', array() );

												foreach ( $nav_menus as $nav_id => $nav_name ) {
													echo '<input type="checkbox" name="_woofc_menus[]" value="' . esc_attr( $nav_id ) . '" ' . ( is_array( $saved_menus ) && in_array( $nav_id, $saved_menus, false ) ? 'checked' : '' ) . '/><label>' . $nav_name . '</label><br/>';
												}
												?>
                                                <span class="description"><?php esc_html_e( 'Choose the menu(s) you want to add the cart at the end.', 'woo-fly-cart' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Custom menu', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" name="_woofc_manual_show" class="regular-text"
                                                       value="<?php echo get_option( '_woofc_manual_show', '' ); ?>"
                                                       placeholder="<?php esc_attr_e( 'button class or id', 'woo-fly-cart' ); ?>"/>
                                                <span class="description"><?php printf( esc_html__( 'The class or id of the custom menu. When clicking on it, the fly cart will show up. Example %s or %s', 'woo-fly-cart' ), '<code>.fly-cart-btn</code>', '<code>#fly-cart-btn</code>' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr class="submit">
                                            <th colspan="2">
												<?php settings_fields( 'woofc_settings' ); ?>
												<?php submit_button(); ?>
                                            </th>
                                        </tr>
                                    </table>
                                </form>
							<?php } elseif ( $active_tab === 'localization' ) { ?>
                                <form method="post" action="options.php">
                                    <table class="form-table">
                                        <tr class="heading">
                                            <th scope="row"><?php esc_html_e( 'Localization', 'woo-fly-cart' ); ?></th>
                                            <td>
												<?php esc_html_e( 'Leave blank to use the default text and its equivalent translation in multiple languages.', 'woo-fly-cart' ); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Cart heading', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[heading]"
                                                       value="<?php echo esc_attr( self::localization( 'heading' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Shopping cart', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Close', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[close]"
                                                       value="<?php echo esc_attr( self::localization( 'close' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Close', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Remove', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[remove]"
                                                       value="<?php echo esc_attr( self::localization( 'remove' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Remove', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Confirm remove', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[remove_confirm]"
                                                       value="<?php echo esc_attr( self::localization( 'remove_confirm' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Do you want to remove this item?', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Undo remove', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[remove_undo]"
                                                       value="<?php echo esc_attr( self::localization( 'remove_undo' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Undo?', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Removed', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[removed]"
                                                       value="<?php echo esc_attr( self::localization( 'removed' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( '%s was removed.', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Empty cart', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[empty]"
                                                       value="<?php echo esc_attr( self::localization( 'empty' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Empty cart', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Confirm empty', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[empty_confirm]"
                                                       value="<?php echo esc_attr( self::localization( 'empty_confirm' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Do you want to empty the cart?', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Share cart', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[share]"
                                                       value="<?php echo esc_attr( self::localization( 'share' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Share cart', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Subtotal', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[subtotal]"
                                                       value="<?php echo esc_attr( self::localization( 'subtotal' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Subtotal', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Coupon code', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[coupon_code]"
                                                       value="<?php echo esc_attr( self::localization( 'coupon_code' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Coupon code', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Coupon apply', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[coupon_apply]"
                                                       value="<?php echo esc_attr( self::localization( 'coupon_apply' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Apply', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Shipping', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[shipping]"
                                                       value="<?php echo esc_attr( self::localization( 'shipping' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Shipping', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Total', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[total]"
                                                       value="<?php echo esc_attr( self::localization( 'total' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Total', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Cart', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text" name="_woofc_localization[cart]"
                                                       value="<?php echo esc_attr( self::localization( 'cart' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Cart', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Checkout', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[checkout]"
                                                       value="<?php echo esc_attr( self::localization( 'checkout' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Checkout', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Continue shopping', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[continue]"
                                                       value="<?php echo esc_attr( self::localization( 'continue' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'Continue shopping', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'Cross sells', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[cross_sells]"
                                                       value="<?php echo esc_attr( self::localization( 'cross_sells' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'You may be interested in&hellip;', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><?php esc_html_e( 'There are no products', 'woo-fly-cart' ); ?></th>
                                            <td>
                                                <input type="text" class="regular-text"
                                                       name="_woofc_localization[no_products]"
                                                       value="<?php echo esc_attr( self::localization( 'no_products' ) ); ?>"
                                                       placeholder="<?php esc_attr_e( 'There are no products in the cart!', 'woo-fly-cart' ); ?>"/>
                                            </td>
                                        </tr>
                                        <tr class="submit">
                                            <th colspan="2">
												<?php settings_fields( 'woofc_localization' ); ?>
												<?php submit_button(); ?>
                                            </th>
                                        </tr>
                                    </table>
                                </form>
							<?php } elseif ( $active_tab === 'premium' ) { ?>
                                <div class="wpclever_settings_page_content_text">
                                    <p>Get the Premium Version just $29! <a
                                                href="https://wpclever.net/downloads/fly-cart?utm_source=pro&utm_medium=woofc&utm_campaign=wporg"
                                                target="_blank">https://wpclever.net/downloads/fly-cart</a>
                                    </p>
                                    <p><strong>Extra features for Premium Version:</strong></p>
                                    <ul style="margin-bottom: 0">
                                        <li>- Enable coupon form.</li>
                                        <li>- Enable shipping cost and shipping calculator.</li>
                                        <li>- Enable suggested products.</li>
                                        <li>- Get lifetime update & premium support.</li>
                                    </ul>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php
				}

				function update_qty() {
					if ( isset( $_POST['cart_item_key'], $_POST['cart_item_qty'] ) && ! empty( $_POST['cart_item_key'] ) ) {
						if ( WC()->cart->get_cart_item( sanitize_text_field( $_POST['cart_item_key'] ) ) ) {
							if ( (float) sanitize_text_field( $_POST['cart_item_qty'] ) > 0 ) {
								WC()->cart->set_quantity( sanitize_text_field( $_POST['cart_item_key'] ), (float) sanitize_text_field( $_POST['cart_item_qty'] ) );
							} else {
								WC()->cart->remove_cart_item( sanitize_text_field( $_POST['cart_item_key'] ) );
							}
						}

						echo json_encode( array( 'action' => 'update_qty' ) );

						die();
					}
				}

				function remove_item() {
					if ( isset( $_POST['cart_item_key'] ) ) {
						WC()->cart->remove_cart_item( sanitize_text_field( $_POST['cart_item_key'] ) );
						WC_AJAX::get_refreshed_fragments();

						die();
					}
				}

				function undo_remove() {
					if ( isset( $_POST['item_key'] ) ) {
						if ( WC()->cart->restore_cart_item( sanitize_text_field( $_POST['item_key'] ) ) ) {
							echo 'true';
						} else {
							echo 'false';
						}

						die();
					}
				}

				function empty_cart() {
					WC()->cart->empty_cart();
					WC_AJAX::get_refreshed_fragments();

					die();
				}

				function get_cart_area() {
					if ( ! isset( WC()->cart ) ) {
						return '';
					}

					$cart_html = '<div class="woofc-inner woofc-cart-area">';
					$cart_html .= apply_filters( 'woofc_above_area_content', '' );
					$cart_html .= '<div class="woofc-area-top"><span>' . self::localization( 'heading', esc_html__( 'Shopping cart', 'woo-fly-cart' ) ) . '</span>';

					if ( get_option( '_woofc_close', 'yes' ) === 'yes' ) {
						$cart_html .= '<div class="woofc-close hint--left" aria-label="' . esc_attr( self::localization( 'close', esc_html__( 'Close', 'woo-fly-cart' ) ) ) . '"><i class="woofc-icon-icon10"></i></div>';
					}

					$cart_html .= '</div><!-- woofc-area-top -->';

					$cart_html .= '<div class="woofc-area-mid woofc-items">';
					$cart_html .= apply_filters( 'woofc_above_items_content', '' );

					// notices
					if ( apply_filters( 'woofc_show_notices', true ) ) {
						$notices = wc_print_notices( true );

						if ( ! empty( $notices ) ) {
							$cart_html .= '<div class="woofc-notices">' . $notices . '</div>';
						}
					}

					$items     = WC()->cart->get_cart();
					$related   = $suggested_products = array();
					$suggested = get_option( '_woofc_suggested', 'cross_sells' );

					if ( is_array( $items ) && ( count( $items ) > 0 ) ) {
						if ( apply_filters( 'woofc_cart_items_reverse', get_option( '_woofc_reverse_items', 'yes' ) === 'yes' ) ) {
							$items = array_reverse( $items );
						}

						foreach ( $items as $cart_item_key => $cart_item ) {
							if ( ! isset( $cart_item['bundled_by'] ) && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
								$_link         = get_option( '_woofc_link', 'yes' );
								$_product      = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
								$_product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
								$_product_link = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

								$cart_html .= '<div class="' . esc_attr( apply_filters( 'woocommerce_cart_item_class', 'woofc-item', $cart_item, $cart_item_key ) ) . '" data-key="' . esc_attr( $cart_item_key ) . '" data-name="' . esc_attr( $_product->get_name() ) . '">';
								$cart_html .= apply_filters( 'woofc_above_item_inner', '', $cart_item );
								$cart_html .= '<div class="woofc-item-inner">';
								$cart_html .= '<div class="woofc-item-thumb">';

								if ( ( $_link !== 'no' ) && ! empty( $_product_link ) ) {
									$cart_item_thumbnail = sprintf( '<a ' . ( $_link === 'yes_popup' ? 'class="woosq-link" data-id="' . esc_attr( $_product_id ) . '" data-context="woofc"' : '' ) . ' href="%s" ' . ( $_link === 'yes_blank' ? 'target="_blank"' : '' ) . '>%s</a>', esc_url( $_product_link ), $_product->get_image() );
								} else {
									$cart_item_thumbnail = $_product->get_image();
								}

								// add suggested products
								if ( $suggested === 'related' || $suggested === 'both' ) {
									$related = array_merge( $related, wc_get_related_products( $_product->get_id(), 3 ) );
								}

								$cart_html .= apply_filters( 'woocommerce_cart_item_thumbnail', $cart_item_thumbnail, $cart_item, $cart_item_key );
								$cart_html .= '</div><!-- /.woofc-item-thumb -->';

								$cart_html .= '<div class="woofc-item-info">';
								$cart_html .= apply_filters( 'woofc_above_item_info', '', $_product );

								$cart_html .= '<span class="woofc-item-title">';

								if ( ( $_link !== 'no' ) && ! empty( $_product_link ) ) {
									$cart_item_name = sprintf( '<a ' . ( $_link === 'yes_popup' ? 'class="woosq-link" data-id="' . esc_attr( $_product_id ) . '" data-context="woofc"' : '' ) . ' href="%s" ' . ( $_link === 'yes_blank' ? 'target="_blank"' : '' ) . '>%s</a>', esc_url( $_product_link ), $_product->get_name() );
								} else {
									$cart_item_name = $_product->get_name();
								}

								$cart_html .= apply_filters( 'woocommerce_cart_item_name', $cart_item_name, $cart_item, $cart_item_key );
								$cart_html .= '</span><!-- /.woofc-item-title -->';

								if ( get_option( '_woofc_data', 'no' ) === 'yes' ) {
									$cart_html .= apply_filters( 'woofc_cart_item_data', '<span class="woofc-item-data">' . wc_get_formatted_cart_item_data( $cart_item, apply_filters( 'woofc_cart_item_data_flat', true ) ) . '</span>', $cart_item );
								}

								if ( get_option( '_woofc_price', 'price' ) === 'price' ) {
									$cart_html .= '<span class="woofc-item-price">' . apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ) . '</span>';
								} elseif ( get_option( '_woofc_price', 'price' ) === 'subtotal' ) {
									$cart_html .= '<span class="woofc-item-price">' . apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ) . '</span>';
								}

								if ( ( get_option( '_woofc_save_for_later', 'yes' ) === 'yes' ) && class_exists( 'WPCleverWoosl' ) ) {
									$cart_html .= '<span class="woofc-item-save">' . do_shortcode( '[woosl product_id="' . ( $cart_item['variation_id'] ?: $_product_id ) . '" cart_item_key="' . $cart_item_key . '" context="woofc"]' ) . '</span>';
								}

								$cart_html .= apply_filters( 'woofc_below_item_info', '', $_product );
								$cart_html .= '</div><!-- /.woofc-item-info -->';

								$min_value = apply_filters( 'woocommerce_quantity_input_min', $_product->get_min_purchase_quantity(), $_product );
								$max_value = apply_filters( 'woocommerce_quantity_input_max', $_product->get_max_purchase_quantity(), $_product );

								if ( $_product->is_sold_individually() || ( $max_value && $min_value === $max_value ) || ! empty( $cart_item['woosb_parent_id'] ) || ! empty( $cart_item['wooco_parent_id'] ) ) {
									$cart_item_quantity = $cart_item['quantity'];
								} else {
									$cart_item_qty            = isset( $cart_item['quantity'] ) ? wc_stock_amount( wp_unslash( $cart_item['quantity'] ) ) : $_product->get_min_purchase_quantity();
									$cart_item_quantity_input = woocommerce_quantity_input( array(
										'classes'     => array( 'input-text', 'woofc-qty', 'qty', 'text' ),
										'input_name'  => 'woofc_qty_' . $cart_item_key,
										'input_value' => $cart_item_qty,
										'min_value'   => $min_value,
										'max_value'   => $max_value,
										'woofc_qty'   => array(
											'input_value' => $cart_item_qty,
											'min_value'   => $min_value,
											'max_value'   => $max_value
										)
									), $_product, false );
									$cart_item_quantity       = '<span class="woofc-item-qty-minus">-</span>' . $cart_item_quantity_input . '<span class="woofc-item-qty-plus">+</span>';
								}

								$cart_html .= '<div class="woofc-item-qty"><div class="woofc-item-qty-inner">' . apply_filters( 'woocommerce_cart_item_quantity', $cart_item_quantity, $cart_item_key, $cart_item ) . '</div></div><!-- /.woofc-item-qty -->';
								$cart_html .= apply_filters( 'woocommerce_cart_item_remove_link', '<span class="woofc-item-remove"><span class="hint--left" aria-label="' . esc_attr( self::localization( 'remove', esc_html__( 'Remove', 'woo-fly-cart' ) ) ) . '"><i class="woofc-icon-icon10"></i></span></span>', $cart_item_key );
								$cart_html .= '</div><!-- /.woofc-item-inner -->';
								$cart_html .= apply_filters( 'woofc_below_item_inner', '', $cart_item );
								$cart_html .= '</div><!-- /.woofc-item -->';
							}
						}
					} else {
						$cart_html .= '<div class="woofc-no-item">' . self::localization( 'no_products', esc_html__( 'There are no products in the cart!', 'woo-fly-cart' ) ) . '</div>';

						if ( ( get_option( '_woofc_save_for_later', 'yes' ) === 'yes' ) && class_exists( 'WPCleverWoosl' ) ) {
							$cart_html .= '<div class="woofc-save-for-later">' . do_shortcode( '[woosl_list context="woofc"]' ) . '</div>';
						}
					}

					$cart_html .= apply_filters( 'woofc_below_items_content', '' );
					$cart_html .= '</div><!-- woofc-area-mid -->';

					$cart_html .= '<div class="woofc-area-bot">';
					$cart_html .= apply_filters( 'woofc_above_bottom_content', '' );

					if ( ! empty( $items ) ) {
						if ( get_option( '_woofc_empty', 'no' ) === 'yes' || get_option( '_woofc_share', 'yes' ) === 'yes' ) {
							// enable empty or share
							$cart_html .= '<div class="woofc-link">';

							if ( get_option( '_woofc_empty', 'no' ) === 'yes' ) {
								$cart_html .= '<div class="woofc-empty"><span class="woofc-empty-cart">' . self::localization( 'empty', esc_html__( 'Empty cart', 'woo-fly-cart' ) ) . '</span></div>';
							}

							if ( get_option( '_woofc_share', 'yes' ) === 'yes' ) {
								$cart_html .= '<div class="woofc-share"><span class="woofc-share-cart wpcss-btn" data-hash="' . esc_attr( WC()->cart->get_cart_hash() ) . '">' . self::localization( 'share', esc_html__( 'Share cart', 'woo-fly-cart' ) ) . '</span></div>';
							}

							$cart_html .= '</div>';
						}

						if ( get_option( '_woofc_subtotal', 'yes' ) === 'yes' ) {
							$cart_html .= apply_filters( 'woofc_above_subtotal_content', '' );
							$cart_html .= '<div class="woofc-data"><div class="woofc-data-left">' . self::localization( 'subtotal', esc_html__( 'Subtotal', 'woo-fly-cart' ) ) . '</div><div id="woofc-subtotal" class="woofc-data-right">' . apply_filters( 'woofc_get_subtotal', WC()->cart->get_cart_subtotal() ) . '</div></div>';
							$cart_html .= apply_filters( 'woofc_below_subtotal_content', '' );
						}

						if ( class_exists( 'WPCleverWpcfb' ) && ( get_option( '_woofc_free_shipping_bar', 'yes' ) === 'yes' ) ) {
							$cart_html .= '<div class="woofc-data">' . do_shortcode( '[wpcfb]' ) . '</div>';
						}

						if ( get_option( '_woofc_total', 'yes' ) === 'yes' ) {
							$cart_html .= apply_filters( 'woofc_above_total_content', '' );
							$cart_html .= '<div class="woofc-data"><div class="woofc-data-left">' . self::localization( 'total', esc_html__( 'Total', 'woo-fly-cart' ) ) . '</div><div id="woofc-total" class="woofc-data-right">' . apply_filters( 'woofc_get_total', WC()->cart->get_total() ) . '</div></div>';
							$cart_html .= apply_filters( 'woofc_below_total_content', '' );
						}

						if ( get_option( '_woofc_buttons', '01' ) === '01' ) {
							// both buttons
							$cart_html .= '<div class="woofc-action"><div class="woofc-action-inner"><div class="woofc-action-left"><a class="woofc-action-cart" href="' . wc_get_cart_url() . '">' . self::localization( 'cart', esc_html__( 'Cart', 'woo-fly-cart' ) ) . '</a></div><div class="woofc-action-right"><a class="woofc-action-checkout" href="' . wc_get_checkout_url() . '">' . self::localization( 'checkout', esc_html__( 'Checkout', 'woo-fly-cart' ) ) . '</a></div></div></div>';
						} else {
							if ( get_option( '_woofc_buttons', '01' ) === '02' ) {
								// cart
								$cart_html .= '<div class="woofc-action"><div class="woofc-action-inner"><div class="woofc-action-full"><a class="woofc-action-cart" href="' . wc_get_cart_url() . '">' . self::localization( 'cart', esc_html__( 'Cart', 'woo-fly-cart' ) ) . '</a></div></div></div>';
							}

							if ( get_option( '_woofc_buttons', '01' ) === '03' ) {
								// checkout
								$cart_html .= '<div class="woofc-action"><div class="woofc-action-inner"><div class="woofc-action-full"><a class="woofc-action-checkout" href="' . wc_get_checkout_url() . '">' . self::localization( 'checkout', esc_html__( 'Checkout', 'woo-fly-cart' ) ) . '</a></div></div></div>';
							}
						}

						if ( ( get_option( '_woofc_save_for_later', 'yes' ) === 'yes' ) && class_exists( 'WPCleverWoosl' ) ) {
							$cart_html .= '<div class="woofc-save-for-later">' . do_shortcode( '[woosl_list context="woofc"]' ) . '</div>';
						}

						if ( $suggested !== 'none' ) {
							if ( $suggested === 'cross_sells' || $suggested === 'both' || get_option( '_woofc_cross_sells', 'yes' ) === 'yes' ) {
								$suggested_products = WC()->cart->get_cross_sells();
							}

							$suggested_limit    = get_option( '_woofc_suggested_limit', 10 );
							$suggested_products = array_unique( array_merge( $suggested_products, $related ) );
							$suggested_products = $suggested_limit > 0 ? array_slice( $suggested_products, 0, $suggested_limit ) : $suggested_products;
							$suggested_products = apply_filters( 'woofc_suggested_products', $suggested_products );

							if ( ! empty( $suggested_products ) ) {
								$cart_html .= apply_filters( 'woofc_above_cross_sells_content', '' );
								$cart_html .= '<div class="woofc-cross-sells">';
								$cart_html .= '<div class="woofc-cross-sells-heading"><span>' . self::localization( 'cross_sells', esc_html__( 'You may be interested in&hellip;', 'woo-fly-cart' ) ) . '</span></div>';
								$cart_html .= '<div class="woofc-cross-sells-products ' . ( count( $suggested_products ) > 1 ? 'woofc-cross-sells-products-slick' : '' ) . '">';

								foreach ( $suggested_products as $suggested_product_id ) {
									$suggested_product = wc_get_product( $suggested_product_id );

									if ( $suggested_product ) {
										$cart_html .= '<div class="woofc-cross-sells-product">';
										$cart_html .= '<div class="woofc-cross-sells-product-image">' . $suggested_product->get_image() . '</div>';
										$cart_html .= '<div class="woofc-cross-sells-product-info">';
										$cart_html .= '<div class="woofc-cross-sells-product-name"><a href="' . esc_url( $suggested_product->get_permalink() ) . '">' . $suggested_product->get_name() . '</a></div>';
										$cart_html .= '<div class="woofc-cross-sells-product-price">' . $suggested_product->get_price_html() . '</div>';
										$cart_html .= '<div class="woofc-cross-sells-product-atc">' . do_shortcode( '[add_to_cart style="" show_price="false" id="' . esc_attr( $suggested_product->get_id() ) . '"]' ) . '</div>';
										$cart_html .= '</div>';
										$cart_html .= '</div>';
									}
								}

								$cart_html .= '</div></div>';
								$cart_html .= apply_filters( 'woofc_below_cross_sells_content', '' );
							}
						}
					}

					if ( get_option( '_woofc_continue', 'yes' ) === 'yes' ) {
						$cart_html .= '<div class="woofc-continue"><span class="woofc-continue-url" data-url="' . esc_url( get_option( '_woofc_continue_url', '' ) ) . '">' . self::localization( 'continue', esc_html__( 'Continue shopping', 'woo-fly-cart' ) ) . '</span></div>';
					}

					$cart_html .= apply_filters( 'woofc_below_bottom_content', '' );
					$cart_html .= '</div><!-- woofc-area-bot -->';
					$cart_html .= apply_filters( 'woofc_below_area_content', '' );
					$cart_html .= '</div>';

					return $cart_html;
				}

				function get_cart_count() {
					if ( ! isset( WC()->cart ) ) {
						return '';
					}

					$cart_count  = '';
					$count       = WC()->cart->get_cart_contents_count();
					$icon        = get_option( '_woofc_count_icon', 'woofc-icon-cart7' );
					$count_class = 'woofc-count woofc-count-' . get_option( '_woofc_count_position', 'bottom-left' );

					if ( ( get_option( '_woofc_hide_cart_checkout', 'no' ) === 'yes' ) ) {
						$count_class .= ' woofc-count-hide-cart-checkout';
					}

					if ( ( get_option( '_woofc_count_hide_empty', 'no' ) === 'yes' ) && ( $count <= 0 ) ) {
						$count_class .= ' woofc-count-hide-empty';
					}

					$cart_count .= '<div id="woofc-count" class="' . esc_attr( $count_class ) . '">';
					$cart_count .= '<i class="' . esc_attr( $icon ) . '"></i>';
					$cart_count .= '<span id="woofc-count-number" class="woofc-count-number">' . esc_attr( $count ) . '</span>';
					$cart_count .= '</div>';

					return apply_filters( 'woofc_cart_count', $cart_count, $count, $icon );
				}

				function get_cart_menu() {
					if ( ! isset( WC()->cart ) ) {
						return '';
					}

					$count     = WC()->cart->get_cart_contents_count();
					$subtotal  = WC()->cart->get_cart_subtotal();
					$icon      = get_option( '_woofc_count_icon', 'woofc-icon-cart7' );
					$cart_menu = '<li class="' . apply_filters( 'woofc_cart_menu_class', 'menu-item woofc-menu-item menu-item-type-woofc' ) . '"><a href="' . wc_get_cart_url() . '"><span class="woofc-menu-item-inner" data-count="' . esc_attr( $count ) . '"><i class="' . esc_attr( $icon ) . '"></i> <span class="woofc-menu-item-inner-subtotal">' . $subtotal . '</span></span></a></li>';

					return apply_filters( 'woofc_cart_menu', $cart_menu, $count, $subtotal, $icon );
				}

				function nav_menu_items( $items, $args ) {
					$selected    = false;
					$saved_menus = get_option( '_woofc_menus', array() );

					if ( ! is_array( $saved_menus ) || empty( $saved_menus ) || ! property_exists( $args, 'menu' ) ) {
						return $items;
					}

					if ( $args->menu instanceof WP_Term ) {
						// menu object
						if ( in_array( $args->menu->term_id, $saved_menus, false ) ) {
							$selected = true;
						}
					} elseif ( is_numeric( $args->menu ) ) {
						// menu id
						if ( in_array( $args->menu, $saved_menus, false ) ) {
							$selected = true;
						}
					} elseif ( is_string( $args->menu ) ) {
						// menu slug or name
						$menu = get_term_by( 'name', $args->menu, 'nav_menu' );

						if ( ! $menu ) {
							$menu = get_term_by( 'slug', $args->menu, 'nav_menu' );
						}

						if ( $menu && in_array( $menu->term_id, $saved_menus, false ) ) {
							$selected = true;
						}
					}

					if ( $selected ) {
						$items .= self::get_cart_menu();
					}

					return $items;
				}

				function footer() {
					if ( apply_filters( 'woofc_disable', false ) ) {
						return;
					}

					if ( ( get_option( '_woofc_hide_cart_checkout', 'no' ) === 'yes' ) && ( is_cart() || is_checkout() ) ) {
						return;
					}

					echo '<div id="woofc-area" class="woofc-area woofc-effect-' . esc_attr( get_option( '_woofc_position', '05' ) ) . ' woofc-slide-' . esc_attr( get_option( '_woofc_effect', 'yes' ) ) . ' woofc-style-' . esc_attr( get_option( '_woofc_style', '01' ) ) . '">';
					echo self::get_cart_area();
					echo '</div>';

					if ( get_option( '_woofc_count', 'yes' ) === 'yes' ) {
						echo self::get_cart_count();
					}

					if ( get_option( '_woofc_overlay_layer', 'yes' ) === 'yes' ) {
						echo '<div class="woofc-overlay"></div>';
					}

					if ( ( isset( $_POST['add-to-cart'] ) || isset( $_GET['add-to-cart'] ) ) && ( get_option( '_woofc_auto_show_normal', 'yes' ) === 'yes' ) ) {
						?>
                        <script>
                          jQuery(document).ready(function() {
                            setTimeout(function() {
                              woofc_show_cart();
                            }, 1000);
                          });
                        </script>
						<?php
					}
				}

				function cart_fragment( $fragments ) {
					ob_start();
					echo self::get_cart_count();
					$fragments['.woofc-count'] = ob_get_clean();

					ob_start();
					echo self::get_cart_menu();
					$fragments['.woofc-menu-item'] = ob_get_clean();

					ob_start();
					echo self::get_cart_link();
					$fragments['.woofc-cart-link'] = ob_get_clean();

					ob_start();
					echo self::get_cart_area();
					$fragments['.woofc-cart-area'] = ob_get_clean();

					return $fragments;
				}

				public static function get_cart_link( $echo = false ) {
					if ( ! isset( WC()->cart ) ) {
						return '';
					}

					$count     = WC()->cart->get_cart_contents_count();
					$subtotal  = WC()->cart->get_cart_subtotal();
					$icon      = get_option( '_woofc_count_icon', 'woofc-icon-cart7' );
					$cart_link = '<span class="woofc-cart-link"><a href="' . wc_get_cart_url() . '"><span class="woofc-cart-link-inner" data-count="' . esc_attr( $count ) . '"><i class="' . esc_attr( $icon ) . '"></i> <span class="woofc-cart-link-inner-subtotal">' . $subtotal . '</span></span></a></span>';
					$cart_link = apply_filters( 'woofc_cart_link', $cart_link, $count, $subtotal, $icon );

					if ( $echo ) {
						echo $cart_link;
					} else {
						return $cart_link;
					}
				}

				public static function woofc_get_cart_link( $echo = false ) {
					self::get_cart_link( $echo );
				}
			}

			return WPCleverWoofc::instance();
		}
	}
}

if ( ! function_exists( 'woofc_notice_wc' ) ) {
	function woofc_notice_wc() {
		?>
        <div class="error">
            <p><strong>WPC Fly Cart</strong> requires WooCommerce version 3.0 or greater.</p>
        </div>
		<?php
	}
}
