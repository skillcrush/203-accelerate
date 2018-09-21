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
			?>
			<style type="text/css">
				#themeisle ul {
					margin-bottom: 0px;
				}

				#themeisle ul li.ti-dw-recommend-item {

					padding-left: 7px;
					border-top: 1px solid #eee;

					margin-bottom: 0px;
					padding-top: 6px;
				}

				#themeisle h2.hndle {
					background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB0AAAAdCAYAAABWk2cPAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAALfSURBVEhL7VW/ayJBFL7/5ZqziBzIcSCHxUE4hAiCgnAQsBASOAikEAIeCAaCiJAyIBICQURYwoJoEUmxa6MWioWxURu30mq77b57s86acX+oOQhp8sFjdmbfzrfve2/efMI74IP0VTB0gz/th/8iNbQxOt0eOmoN6WgAn78cwJeoYcLf78KrSPVneU3isEQZoz0D3pt03jhDrDTERJXxWM3A5yAOIPmgce/t2IvUGFzjkDa+7C5RTx0gWbxG0v8bBXUG3fLReigk4rh55gtbsAfpGDfhVTS+VA19bYbH7BkqU/56AzNU7nrYpbI7qfbEcxfBkSOHJGO27Vk0htqEYoXvARdS+tsEJyAJY1GRkOxnGTdq2zuaRZOkJ1Wi1+h7ODlJB/l1kYRKZVyKhMwoSmUw5M4uWMhIcl9f1l1qB2m/aMkZQIE2d5BeDTGiSD0VfC4jZPmSKiO+LMJGuoRE1bkiOIW00CAdW3NuxzLmxhD1hyHmug7dbBRD9KdLc4dR6Zfgn0ffXN2EjVSMLIcOrWxuwiyyPhYGI7X0m/agqPeIib5+yit/LWILKYuUlrTa5kbMwjko7J0AXc1RLZwifRF58Us1YXMzYSMV5WQ5ZWsG5VnYaG1BxM7zKBRzOAkHac460gxK9sUnVBqbu9rhKCS9lRaq1/poCeUq7tL6uPnjSLeoBRo9/KXjslqP4Na1gbiQssiULK9gP+VVqPnFQMblnwi+WmTfIji5amLES1mn/mz9yCFVuddZdiFlIJlTK+JtH2/AaPMogzipzrZ+4yQ1qCL1JSaNDL6bf81y1aOb5R6Vhow6G1tPNK9Borl0R2NLRiEaxNEF9Wa3yrHBSarTbeHot7ySqVmYR4AucDYuaJyzkfptZ0e/FeGa03m3jOQPizCAo0QG9W2ktL5HgGt45JRgaBh1x9R1WHY0KCTnLZOW5Kw0miuZq01ITGa6Z18Db9I3xAfpm+IdSIF/du91gSA2+I8AAAAASUVORK5CYII=');
					background-repeat: no-repeat;
					background-position: 90% 50%;
					background-size: 29px;
				}

				.ti-dw-feed-item {
					display: flex;
					align-items: center;
				}

				.ti-dw-feed-item a {
					float: left;
					width: 89.9%;
				}

				.ti-dw-feed-item .ti-dw-day-container {
					width: 100%;
					letter-spacing: 3px;
					display: block;
				}

				.ti-dw-feed-item .ti-dw-month-container {

					width: 100%;
					display: block;
					font-weight: 600;
					padding: 0px;
					margin-top: -6px;
					text-transform: uppercase;
					font-size: 10px;
					letter-spacing: 1px;
				}

				.ti-dw-feed-item .ti-dw-date-container {
					float: left;
					min-height: 30px;
					margin-right: 0.1%;
					width: 10%;
					text-align: center;
				}

				.ti-dw-recommend-item span {
					color: #72777c;
				}

				.ti-dw-powered-by {
					font-style: italic;
					text-align: right;
					margin-top:3px;
				}

			</style>
			<ul>
				<?php
				foreach ( $this->items as $item ) {
					?>
					<li class="ti-dw-feed-item"><span class="ti-dw-date-container"><span
									class="ti-dw-day-container"><?php echo date( 'd', $item['date'] ); ?></span> <span
									class="ti-dw-month-container"><?php echo substr( date( 'M', $item['date'] ), 0, 3 ); ?></span></span><a
								href="
								<?php
								echo add_query_arg(
									array(
										'utm_campaign' => 'feed',
										'utm_medium'   => 'dashboard_widget',
									), $item['link']
								);
								?>
								" target="_blank"><?php echo $item['title']; ?></a>
						<div class="clear"></div>
					</li>
					<?php
				}

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
						<li class="ti-dw-recommend-item ">
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
						</li>
						<li class="ti-dw-powered-by">
							Powered by <?php echo esc_attr( $this->product->get_friendly_name() ); ?>
						</li>

						<?php
					}
				}
				?>
			</ul>

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
