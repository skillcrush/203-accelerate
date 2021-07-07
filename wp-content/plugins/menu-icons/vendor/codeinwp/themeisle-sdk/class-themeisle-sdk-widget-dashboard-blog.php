<?php
/**
 * The blog dashboard model class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Widgets
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'ThemeIsle_SDK_Widget_Dashboard_Blog' ) ) :
	/**
	 * Blog dashboard widget model for ThemeIsle SDK.
	 */
	class ThemeIsle_SDK_Widget_Dashboard_Blog extends ThemeIsle_SDK_Widget {

		/**
		 * @var array instance The instances.
		 */
		protected $product;
		/**
		 * @var array Feed items.
		 */
		private $items = array();

		/**
		 * ThemeIsle_SDK_Widget_Dashboard_Blog constructor.
		 *
		 * @param ThemeIsle_SDK_Product $product_object The product object.
		 */
		public function __construct( $product_object ) {
			$this->product = $product_object;
			parent::__construct( $product_object );
		}

		/**
		 * Registers the hooks
		 */
		public function setup_hooks_child() {
			$this->setup_vars();
			add_action( 'wp_dashboard_setup', array( &$this, 'add_widget' ) );
			add_action( 'wp_network_dashboard_setup', array( &$this, 'add_widget' ) );
			add_filter( 'themeisle_sdk_recommend_plugin_or_theme', array( &$this, 'recommend_plugin_or_theme' ) );
		}

		/**
		 * Setup class variables
		 */
		function setup_vars() {
			$this->dashboard_name = apply_filters( 'themeisle_sdk_dashboard_widget_name', 'WordPress Guides/Tutorials' );
			$this->feeds          = apply_filters(
				'themeisle_sdk_dashboard_widget_feeds', array(
					'https://themeisle.com/blog/feed',
				)
			);
		}

		/**
		 * Add widget to the dashboard
		 *
		 * @return string|void
		 */
		function add_widget() {
			global $wp_meta_boxes;
			if ( isset( $wp_meta_boxes['dashboard']['normal']['core']['themeisle'] ) ) {
				return;
			}
			wp_add_dashboard_widget(
				'themeisle', $this->dashboard_name, array(
					&$this,
					'render_dashboard_widget',
				)
			);
		}

		/**
		 * Setup feed items.
		 */
		private function setup_feeds() {
			$items_normalized = array();
			if ( false === ( $items_normalized = get_transient( 'themeisle_sdk_feed_items' ) ) ) {
				// Load SimplePie Instance
				$feed = fetch_feed( $this->feeds );
				// TODO report error when is an error loading the feed
				if ( is_wp_error( $feed ) ) {
					return;
				}

				$items = $feed->get_items( 0, 5 );
				foreach ( (array) $items as $item ) {
					$items_normalized[] = array(
						'title' => $item->get_title(),
						'date'  => $item->get_date( 'U' ),
						'link'  => $item->get_permalink(),
					);
				}
				set_transient( 'themeisle_sdk_feed_items', $items_normalized, 48 * HOUR_IN_SECONDS );
			}
			$this->items = $items_normalized;
		}

		/**
		 * Render widget content
		 */
		function render_dashboard_widget() {
			$this->setup_feeds();
			if ( empty( $this->items ) || ! is_array( $this->items ) ) {
				return;
			}
			?>
			<style type="text/css">
				#themeisle ul li.ti-dw-recommend-item {
					padding-left: 7px;
					border-top: 1px solid #eee;
					margin-bottom: 0px;
					padding-top: 6px;
				}

				#themeisle h2.hndle {
					background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA3Ny4xMiA3Ny4xMiI+PHRpdGxlPkFzc2V0IDM8L3RpdGxlPjxwYXRoIGQ9Ik03NS43Niw0Ny42cS0xLjUsNi40Ni00LjA3LDkuMjZBMzYuMjIsMzYuMjIsMCwwLDEsNjMuMjYsNjNhNTEsNTEsMCwwLDEtOS43OSw0LjZoLS4xNXY0LjgyQTQuNjYsNC42NiwwLDAsMSw1Miw3NS43Niw0LjA3LDQuMDcsMCwwLDEsNDksNzcuMTJhNCw0LDAsMCwxLTIuMTgtLjYxQTQuOTQsNC45NCwwLDAsMSw0NS4xOSw3NWE0LjQyLDQuNDIsMCwwLDEtNy41NCwwLDQuOTQsNC45NCwwLDAsMS0xLjU4LDEuNSwzLjc1LDMuNzUsMCwwLDEtMiwuNjFBNC4yNyw0LjI3LDAsMCwxLDMwLjI3LDc1YTQuOTQsNC45NCwwLDAsMS0xLjU4LDEuNSw0LDQsMCwwLDEtMi4xOC42MSwzLjkzLDMuOTMsMCwwLDEtMy4xNi0xLjQzLDUuMyw1LjMsMCwwLDEtMS4yMS0zLjU0LDEzLjgxLDEzLjgxLDAsMCwxLTguNTgsMywxMy4yMywxMy4yMywwLDAsMS05LTMuNDdBMTguMjEsMTguMjEsMCwwLDAsOSw3MC43OWExNC4yNiwxNC4yNiwwLDAsMS03LjgzLTUuNDIsMTYuMTIsMTYuMTIsMCwwLDAsNS4xOS4wNywxMi44OSwxMi44OSwwLDAsMCw0LjktMS43M1EzLjE1LDU1LjQzLDEuMDUsNTAuNDZhMTgsMTgsMCwwLDEtMS02LjkzQTQwLDQwLDAsMCwxLDEuMjEsMzQsMjYuNTksMjYuNTksMCwwLDEsNS4yNywyNC4xUTguNDQsMTkuNDMsMTMsMTkuNDNhMy40MSwzLjQxLDAsMCwxLDMuNDcsMi4yNmMuNi0uOCwxLjItMS41NiwxLjgxLTIuMjZsLjQ1LS40NS43NS0uNzUsMS4wNS0xLjgxYTM1LjIsMzUuMiwwLDAsMSwyLjExLTMuMzIsMjguNTcsMjguNTcsMCwwLDEsOS4xMi03LjY4QTIzLjQsMjMuNCwwLDAsMSw0Mi45MywyLjU2YTIyLjIyLDIyLjIyLDAsMCwxLDIuNzEuMTVBOC4xNiw4LjE2LDAsMCwxLDUxLjgxLDAsOC42OSw4LjY5LDAsMCwxLDU4LDIuNDFhMS41MSwxLjUxLDAsMCwxLC4xNS42OGMwLC4yNS0uNTUuOTMtMS42NiwybC0uMy4zYTkuNjksOS42OSwwLDAsMSwxLjU4LjgzYy41NS4zNS44My42NS44My45YTEuNjIsMS42MiwwLDAsMS0uNiwxbC0uMTUuMTVxNi43OCw2LDguNTgsMTYuMjdBMy4zLDMuMywwLDAsMSw2OSwyMy4zNWE2LjQ4LDYuNDgsMCwwLDEsNC4yMiwyLjFxMy45MiwzLjMyLDMuOTIsMTEuOUE0Mi4wNSw0Mi4wNSwwLDAsMSw3NS43Niw0Ny42Wk01My42Miw1NC4wN2EyNCwyNCwwLDAsMS0xNCw0LjUyQTIxLjQxLDIxLjQxLDAsMCwxLDI3LDU0LjY3LDI0LjI4LDI0LjI4LDAsMCwxLDE4LjUzLDQ1YTI5LDI5LDAsMCwxLTMtMTMsMzEuNzgsMzEuNzgsMCwwLDEsLjkxLTcuNTNBMzIuMTcsMzIuMTcsMCwwLDAsMTEuOSw0MS4yN2EzMy41LDMzLjUsMCwwLDAsMi43OSwxMy40LDI5LjEsMjkuMSwwLDAsMCw3LjksMTAuODUsNC42OCw0LjY4LDAsMCwxLDEuNjYtMS44OCw0LjE3LDQuMTcsMCwwLDEsNC40NC0uMDgsNC41LDQuNSwwLDAsMSwxLjU4LDEuNjZBNC4yLDQuMiwwLDAsMSwzNCw2M2EzLjgzLDMuODMsMCwwLDEsMiwuNiw0LjUsNC41LDAsMCwxLDEuNTgsMS42Niw0LjI3LDQuMjcsMCwwLDEsNy41NCwwLDQuNSw0LjUsMCwwLDEsMS41OC0xLjY2LDQuMTcsNC4xNywwLDAsMSw0LjQ0LjA4LDMuODYsMy44NiwwLDAsMSwxLjUxLDEuNzMsMzAuMTcsMzAuMTcsMCwwLDAsNy42OC05Ljk0LDMxLjE4LDMxLjE4LDAsMCwwLDMuMTYtMTIuMzVBMjguMzksMjguMzksMCwwLDEsNTMuNjIsNTQuMDdaTTI4Ljc3LDY1LjM3YTMuMSwzLjEsMCwwLDAtNC4zNywwLDMuMDYsMy4wNiwwLDAsMC0uOSwyLjI2djQuODJhMy4zMiwzLjMyLDAsMCwwLC45LDIuMzMsMi45MywyLjkzLDAsMCwwLDQuMzcsMCwzLjMyLDMuMzIsMCwwLDAsLjktMi4zM1Y2Ny42M0EzLjA2LDMuMDYsMCwwLDAsMjguNzcsNjUuMzdabS45LTQ4YTQuMjQsNC4yNCwwLDAsMCwzLjQ3LDEuNzMsNC40NSw0LjQ1LDAsMCwwLDMuNTQtMS43Myw2LDYsMCwwLDAsMS40My0zLjkyLDUuNyw1LjcsMCwwLDAtMS40My0zLjg0QTQuNTMsNC41MywwLDAsMCwzMy4xNCw4YTQuMzEsNC4zMSwwLDAsMC0zLjQ3LDEuNjYsNS43OSw1Ljc5LDAsMCwwLTEuMzUsMy43N0E2LjMzLDYuMzMsMCwwLDAsMjkuNjcsMTcuNFptNi40OCw0OGEzLjEsMy4xLDAsMCwwLTQuMzcsMCwzLjA2LDMuMDYsMCwwLDAtLjksMi4yNnY0LjgyYTMuMzIsMy4zMiwwLDAsMCwuOSwyLjMzLDIuOTMsMi45MywwLDAsMCw0LjM3LDAsMy4zMiwzLjMyLDAsMCwwLC45LTIuMzNWNjcuNjNBMy4wNiwzLjA2LDAsMCwwLDM2LjE1LDY1LjM3Wm0tNC4wNy01NC4zYTIuMzcsMi4zNywwLDAsMSwyLTEsMi4xNywyLjE3LDAsMCwxLDEuODgsMSw0LjEsNC4xLDAsMCwxLDAsNC41MiwyLjE3LDIuMTcsMCwwLDEtMS44OCwxLDIuMzcsMi4zNywwLDAsMS0yLTEsMy43MiwzLjcyLDAsMCwxLS43NS0yLjM0QTMuNDksMy40OSwwLDAsMSwzMi4wOCwxMS4wN1ptNSw5LjQxYTIwLjYxLDIwLjYxLDAsMCwwLTMuNTQsMTIuMjgsMTcuMTUsMTcuMTUsMCwwLDAsNC4wNywxMSwxMi40MywxMi40MywwLDAsMCw5Ljg3LDQuNDUsMTUuMywxNS4zLDAsMCwwLDktMywxMywxMywwLDAsMCwuNi0zLjMycTAtMy4zMi0zLjE2LTMuMzFhOC41OCw4LjU4LDAsMCwwLTIsLjNxLTcuODMsMS41LTExLjU5LS4zLTQuNTMtMi4xMi00LjUyLTkuMzRBMzcuOTIsMzcuOTIsMCwwLDEsMzcuMDUsMjAuNDhaTTYxLjQ1LDE3QTEyLjg0LDEyLjg0LDAsMCwwLDUxLjIxLDEyLjJhMTUuMTEsMTUuMTEsMCwwLDAtNi40LDEuNDMsMTcuODMsMTcuODMsMCwwLDAtNS41LDRxLTYuMTgsMjAuNjQsNi4xOCwyMC42NEEyNC43NSwyNC43NSwwLDAsMCw0OSwzNy44MWEzOC45MiwzOC45MiwwLDAsMSw0LjgyLS40NmMxLjgxLDAsMy4wNi40NiwzLjc3LDEuMzZxMS4zNSwxLjUuNzUsNS4xMmEyMS43MiwyMS43MiwwLDAsMCw1LTcuMTVBMjEuMzgsMjEuMzgsMCwwLDAsNjUuMDcsMjgsMTcuNDQsMTcuNDQsMCwwLDAsNjEuNDUsMTdaTTQzLjYsNjUuMzdhMi43MiwyLjcyLDAsMCwwLTIuMS0uOSwzLDMsMCwwLDAtMi4xOS45LDMuMDYsMy4wNiwwLDAsMC0uOSwyLjI2djQuODJhMy4zMiwzLjMyLDAsMCwwLC45LDIuMzMsMi44NCwyLjg0LDAsMCwwLDIuMTksMSwyLjYxLDIuNjEsMCwwLDAsMi4xLTEsMy40NSwzLjQ1LDAsMCwwLC44My0yLjMzVjY3LjYzQTMuMTcsMy4xNywwLDAsMCw0My42LDY1LjM3Wm03LjQ2LDBhMywzLDAsMCwwLTIuMTgtLjksMi43MywyLjczLDAsMCwwLTIuMTEuOSwzLjE3LDMuMTcsMCwwLDAtLjgzLDIuMjZ2NC44MmEzLjQ1LDMuNDUsMCwwLDAsLjgzLDIuMzMsMi42MSwyLjYxLDAsMCwwLDIuMTEsMSwyLjgzLDIuODMsMCwwLDAsMi4xOC0xLDMuMzIsMy4zMiwwLDAsMCwuOS0yLjMzVjY3LjYzQTMuMDYsMy4wNiwwLDAsMCw1MS4wNiw2NS4zN1oiLz48L3N2Zz4=');
					background-repeat: no-repeat;
					background-position: 92% 50%;
					background-size: 30px;
				}

				#themeisle .inside {
					padding: 0;
				}

				.ti-feed-list {
					padding: 0 12px 5px;
					margin-bottom: 10px;
					border-bottom: 1px solid #eee;
				}

				.ti-dw-feed-item a {
					display: flex;
					align-items: center;
					margin-bottom: 5px;
					padding: 5px;
					transition: .2s ease;
					border-radius: 3px;
				}

				.ti-dw-feed-item a:hover {
					background-color: #f8f8f8;
				}

				.ti-dw-feed-item a:hover .ti-dw-date-container {
					opacity: .9;
				}

				.ti-dw-feed-item .ti-dw-month-container {
					margin-top: -5px;
					text-transform: uppercase;
					font-size: 10px;
					letter-spacing: 1px;
					font-weight: 700;
				}

				.ti-dw-feed-item .ti-dw-date-container {
					border-radius: 3px;
					transition: .2s ease;
					min-height: 35px;
					margin-right: 5px;
					min-width: 35px;
					text-align: center;
					border: 1px solid #2a6f97;
					color: #fff;
					background: #2ea2cc;
					display: flex;
					flex-direction: column;
					justify-content: center;
				}

				.ti-dw-footer {
					padding: 0 12px 5px;
					text-align: center;
				}

				.ti-dw-recommend-item {
					display: block;
				}

				.ti-dw-recommend-item span {
					color: #72777c;
				}

				.ti-dw-powered-by {
					font-size: 11px;
					margin-top: 3px;
					display: block;
					color: #72777c;
				}

				.ti-dw-powered-by span {
					font-weight: 600;
				}

			</style>
			<ul class="ti-feed-list">
				<?php
				foreach ( $this->items as $item ) {
					?>
					<li class="ti-dw-feed-item">
						<a href="
						<?php
						echo add_query_arg(
							array(
								'utm_campaign' => 'feed',
								'utm_medium'   => 'dashboard_widget',
							), $item['link']
						);
						?>
						" target="_blank">
							<span class="ti-dw-date-container"><span
										class="ti-dw-day-container"><?php echo date( 'd', $item['date'] ); ?></span> <span
										class="ti-dw-month-container"><?php echo substr( date( 'M', $item['date'] ), 0, 3 ); ?></span></span><?php echo $item['title']; ?>
						</a>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
			$recommend = apply_filters( 'themeisle_sdk_recommend_plugin_or_theme', array() );
			if ( is_array( $recommend ) && ! empty( $recommend ) ) {

				$type = $recommend['type'];
				if ( ( $type == 'theme' && current_user_can( 'install_themes' ) ) || ( $type == 'plugin' && current_user_can( 'install_plugins' ) ) ) {
					add_thickbox();
					$url = add_query_arg(
						array(
							'theme' => $recommend['slug'],
						), network_admin_url( 'theme-install.php' )
					);

					if ( 'plugin' === $type ) {

						$url = add_query_arg(
							array(
								'tab'    => 'plugin-information',
								'plugin' => $recommend['slug'],
							), network_admin_url( 'plugin-install.php' )
						);
					}
					?>
					<div class="ti-dw-footer">
					<span class="ti-dw-recommend-item ">
							<span class="ti-dw-recommend"><?php echo apply_filters( 'themeisle_sdk_dashboard_popular_label', sprintf( 'Popular %s', ucwords( $type ) ) ); ?>
								: </span>
						<?php
						echo trim(
							str_replace(
								array(
									'lite',
									'Lite',
								), '', $recommend['name']
							)
						);
						?>
						(<a class="thickbox open-plugin-details-modal"
							href="<?php echo $url . '&TB_iframe=true&width=600&height=500'; ?>"><?php echo apply_filters( 'themeisle_sdk_dashboard_install_label', 'Install' ); ?></a>)
					</span>
						<span class="ti-dw-powered-by">
						Powered by <span><?php echo esc_attr( $this->product->get_friendly_name() ); ?></span>
					</span>
					</div>

					<?php
				}
			}
			?>

			<?php

		}

		/**
		 * Either the current product is installed or not.
		 *
		 * @param array $val The current recommended product.
		 *
		 * @return bool Either we should exclude the plugin or not.
		 */
		public function remove_current_products( $val ) {
			if ( $val['type'] === 'theme' ) {
				$exist = wp_get_theme( $val['slug'] );

				return ! $exist->exists();
			} else {
				$all_plugins = array_keys( get_plugins() );
				foreach ( $all_plugins as $slug ) {
					if ( strpos( $slug, $val['slug'] ) !== false ) {
						return false;
					}
				}

				return true;
			}
		}

		/**
		 * Fetch themes from wporg api.
		 *
		 * @param string $author The author name.
		 *
		 * @return array The list of themes.
		 */
		function get_themes_from_wporg( $author ) {
			$products = wp_remote_get(
				'https://api.wordpress.org/themes/info/1.1/?action=query_themes&request[author]=' . $author . '&request[per_page]=30&request[fields][active_installs]=true'
			);
			$products = json_decode( wp_remote_retrieve_body( $products ) );
			if ( is_object( $products ) ) {
				$products = isset( $products->themes ) ? $products->themes : array();
			} else {
				$products = array();
			}

			return $products;
		}

		/**
		 * Fetch plugin from wporg api.
		 *
		 * @param string $author The author slug.
		 *
		 * @return array The list of plugins for the selected author.
		 */
		function get_plugins_from_wporg( $author ) {
			$products = wp_remote_get(
				'https://api.wordpress.org/plugins/info/1.1/?action=query_plugins&request[author]=' . $author . '&request[author]=codeinwp&request[per_page]=20&request[fields][active_installs]=true'
			);
			$products = json_decode( wp_remote_retrieve_body( $products ) );
			if ( is_object( $products ) ) {
				$products = isset( $products->plugins ) ? $products->plugins : array();
			} else {
				$products = array();
			}

			return $products;
		}

		/**
		 * Fetch products from the recomended section.
		 *
		 * @return array|mixed The list of products to use in recomended section.
		 */
		function get_product_from_api() {
			if ( false === ( $products = get_transient( 'themeisle_sdk_products' ) ) ) {
				$products         = array();
				$themeisle_themes = $this->get_themes_from_wporg( 'themeisle' );
				$codeinwp_themes  = $this->get_themes_from_wporg( 'codeinwp' );

				$themeisle_plugins = $this->get_plugins_from_wporg( 'themeisle' );
				$codeinwp_plugins  = $this->get_plugins_from_wporg( 'codeinwp' );

				$all_themes = array_merge( $themeisle_themes, $codeinwp_themes );
				foreach ( $all_themes as $theme ) {
					if ( $theme->active_installs < 4999 ) {
						continue;
					}
					$products[] = array(
						'name'     => $theme->name,
						'type'     => 'theme',
						'slug'     => $theme->slug,
						'installs' => $theme->active_installs,
					);
				}
				$all_plugins = array_merge( $themeisle_plugins, $codeinwp_plugins );
				foreach ( $all_plugins as $plugin ) {
					if ( $plugin->active_installs < 5999 ) {
						continue;
					}
					$products[] = array(
						'name'     => $plugin->name,
						'type'     => 'plugin',
						'slug'     => $plugin->slug,
						'installs' => $plugin->active_installs,
					);
				}
				set_transient( 'themeisle_sdk_products', $products, 6 * HOUR_IN_SECONDS );
			}

			return $products;
		}

		/**
		 * Contact the API and fetch the recommended plugins/themes
		 */
		function recommend_plugin_or_theme() {
			$products = $this->get_product_from_api();
			if ( ! is_array( $products ) ) {
				$products = array();
			}
			$products = array_filter( $products, array( $this, 'remove_current_products' ) );
			$products = array_merge( $products );
			if ( count( $products ) > 1 ) {
				shuffle( $products );
				$products = array_slice( $products, 0, 1 );
			}
			$to_recommend = isset( $products[0] ) ? $products[0] : $products;

			return $to_recommend;
		}
	}
endif;
