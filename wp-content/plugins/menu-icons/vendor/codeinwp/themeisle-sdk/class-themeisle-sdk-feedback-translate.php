<?php
/**
 * The Translate feedback model class for ThemeIsle SDK
 *
 * @package     ThemeIsleSDK
 * @subpackage  Feedback
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'ThemeIsle_SDK_Feedback_Translate' ) ) :
	/**
	 * Translate feedback model for ThemeIsle SDK.
	 */
	class ThemeIsle_SDK_Feedback_Translate extends ThemeIsle_SDK_Feedback {

		/**
		 * @var string $heading The heading of the modal
		 */
		private $heading = 'Improve {product}';
		/**
		 * @var string The message.
		 */
		private $msg = 'Translating <b>{product}</b> into as many languages as possible is a huge project. We still need help with a lot of them, so if you are good at translating into <b>{language}</b>, it would be greatly appreciated.
The process is easy, and you can join by following the link below!';
		/**
		 * @var string $button_cancel The text of the cancel button
		 */
		private $button_cancel = 'No, thanks.';
		/**
		 * @var string $button_already The text of the already did it button
		 */
		private $button_do = 'Ok, I will gladly help.';
		/**
		 * @var array Array of available locals.
		 */
		private $locales = array(
			'af'             => array(
				'slug' => 'af',
				'name' => 'Afrikaans',
			),
			'ak'             => array(
				'slug' => 'ak',
				'name' => 'Akan',
			),
			'am'             => array(
				'slug' => 'am',
				'name' => 'Amharic',
			),
			'ar'             => array(
				'slug' => 'ar',
				'name' => 'Arabic',
			),
			'arq'            => array(
				'slug' => 'arq',
				'name' => 'Algerian Arabic',
			),
			'ary'            => array(
				'slug' => 'ary',
				'name' => 'Moroccan Arabic',
			),
			'as'             => array(
				'slug' => 'as',
				'name' => 'Assamese',
			),
			'ast'            => array(
				'slug' => 'ast',
				'name' => 'Asturian',
			),
			'az'             => array(
				'slug' => 'az',
				'name' => 'Azerbaijani',
			),
			'azb'            => array(
				'slug' => 'azb',
				'name' => 'South Azerbaijani',
			),
			'az_TR'          => array(
				'slug' => 'az-tr',
				'name' => 'Azerbaijani (Turkey)',
			),
			'ba'             => array(
				'slug' => 'ba',
				'name' => 'Bashkir',
			),
			'bal'            => array(
				'slug' => 'bal',
				'name' => 'Catalan (Balear)',
			),
			'bcc'            => array(
				'slug' => 'bcc',
				'name' => 'Balochi Southern',
			),
			'bel'            => array(
				'slug' => 'bel',
				'name' => 'Belarusian',
			),
			'bg_BG'          => array(
				'slug' => 'bg',
				'name' => 'Bulgarian',
			),
			'bn_BD'          => array(
				'slug' => 'bn',
				'name' => 'Bengali',
			),
			'bo'             => array(
				'slug' => 'bo',
				'name' => 'Tibetan',
			),
			'bre'            => array(
				'slug' => 'br',
				'name' => 'Breton',
			),
			'bs_BA'          => array(
				'slug' => 'bs',
				'name' => 'Bosnian',
			),
			'ca'             => array(
				'slug' => 'ca',
				'name' => 'Catalan',
			),
			'ceb'            => array(
				'slug' => 'ceb',
				'name' => 'Cebuano',
			),
			'ckb'            => array(
				'slug' => 'ckb',
				'name' => 'Kurdish (Sorani)',
			),
			'co'             => array(
				'slug' => 'co',
				'name' => 'Corsican',
			),
			'cs_CZ'          => array(
				'slug' => 'cs',
				'name' => 'Czech',
			),
			'cy'             => array(
				'slug' => 'cy',
				'name' => 'Welsh',
			),
			'da_DK'          => array(
				'slug' => 'da',
				'name' => 'Danish',
			),
			'de_DE'          => array(
				'slug' => 'de',
				'name' => 'German',
			),
			'de_CH'          => array(
				'slug' => 'de-ch',
				'name' => 'German (Switzerland)',
			),
			'dv'             => array(
				'slug' => 'dv',
				'name' => 'Dhivehi',
			),
			'dzo'            => array(
				'slug' => 'dzo',
				'name' => 'Dzongkha',
			),
			'el'             => array(
				'slug' => 'el',
				'name' => 'Greek',
			),
			'art_xemoji'     => array(
				'slug' => 'art-xemoji',
				'name' => 'Emoji',
			),
			'en_US'          => array(
				'slug' => 'en',
				'name' => 'English',
			),
			'en_AU'          => array(
				'slug' => 'en-au',
				'name' => 'English (Australia)',
			),
			'en_CA'          => array(
				'slug' => 'en-ca',
				'name' => 'English (Canada)',
			),
			'en_GB'          => array(
				'slug' => 'en-gb',
				'name' => 'English (UK)',
			),
			'en_NZ'          => array(
				'slug' => 'en-nz',
				'name' => 'English (New Zealand)',
			),
			'en_ZA'          => array(
				'slug' => 'en-za',
				'name' => 'English (South Africa)',
			),
			'eo'             => array(
				'slug' => 'eo',
				'name' => 'Esperanto',
			),
			'es_ES'          => array(
				'slug' => 'es',
				'name' => 'Spanish (Spain)',
			),
			'es_AR'          => array(
				'slug' => 'es-ar',
				'name' => 'Spanish (Argentina)',
			),
			'es_CL'          => array(
				'slug' => 'es-cl',
				'name' => 'Spanish (Chile)',
			),
			'es_CO'          => array(
				'slug' => 'es-co',
				'name' => 'Spanish (Colombia)',
			),
			'es_CR'          => array(
				'slug' => 'es-cr',
				'name' => 'Spanish (Costa Rica)',
			),
			'es_GT'          => array(
				'slug' => 'es-gt',
				'name' => 'Spanish (Guatemala)',
			),
			'es_MX'          => array(
				'slug' => 'es-mx',
				'name' => 'Spanish (Mexico)',
			),
			'es_PE'          => array(
				'slug' => 'es-pe',
				'name' => 'Spanish (Peru)',
			),
			'es_PR'          => array(
				'slug' => 'es-pr',
				'name' => 'Spanish (Puerto Rico)',
			),
			'es_VE'          => array(
				'slug' => 'es-ve',
				'name' => 'Spanish (Venezuela)',
			),
			'et'             => array(
				'slug' => 'et',
				'name' => 'Estonian',
			),
			'eu'             => array(
				'slug' => 'eu',
				'name' => 'Basque',
			),
			'fa_IR'          => array(
				'slug' => 'fa',
				'name' => 'Persian',
			),
			'fa_AF'          => array(
				'slug' => 'fa-af',
				'name' => 'Persian (Afghanistan)',
			),
			'fuc'            => array(
				'slug' => 'fuc',
				'name' => 'Fulah',
			),
			'fi'             => array(
				'slug' => 'fi',
				'name' => 'Finnish',
			),
			'fo'             => array(
				'slug' => 'fo',
				'name' => 'Faroese',
			),
			'fr_FR'          => array(
				'slug' => 'fr',
				'name' => 'French (France)',
			),
			'fr_BE'          => array(
				'slug' => 'fr-be',
				'name' => 'French (Belgium)',
			),
			'fr_CA'          => array(
				'slug' => 'fr-ca',
				'name' => 'French (Canada)',
			),
			'frp'            => array(
				'slug' => 'frp',
				'name' => 'Arpitan',
			),
			'fur'            => array(
				'slug' => 'fur',
				'name' => 'Friulian',
			),
			'fy'             => array(
				'slug' => 'fy',
				'name' => 'Frisian',
			),
			'ga'             => array(
				'slug' => 'ga',
				'name' => 'Irish',
			),
			'gd'             => array(
				'slug' => 'gd',
				'name' => 'Scottish Gaelic',
			),
			'gl_ES'          => array(
				'slug' => 'gl',
				'name' => 'Galician',
			),
			'gn'             => array(
				'slug' => 'gn',
				'name' => 'Guaraní',
			),
			'gsw'            => array(
				'slug' => 'gsw',
				'name' => 'Swiss German',
			),
			'gu'             => array(
				'slug' => 'gu',
				'name' => 'Gujarati',
			),
			'hat'            => array(
				'slug' => 'hat',
				'name' => 'Haitian Creole',
			),
			'hau'            => array(
				'slug' => 'hau',
				'name' => 'Hausa',
			),
			'haw_US'         => array(
				'slug' => 'haw',
				'name' => 'Hawaiian',
			),
			'haz'            => array(
				'slug' => 'haz',
				'name' => 'Hazaragi',
			),
			'he_IL'          => array(
				'slug' => 'he',
				'name' => 'Hebrew',
			),
			'hi_IN'          => array(
				'slug' => 'hi',
				'name' => 'Hindi',
			),
			'hr'             => array(
				'slug' => 'hr',
				'name' => 'Croatian',
			),
			'hu_HU'          => array(
				'slug' => 'hu',
				'name' => 'Hungarian',
			),
			'hy'             => array(
				'slug' => 'hy',
				'name' => 'Armenian',
			),
			'id_ID'          => array(
				'slug' => 'id',
				'name' => 'Indonesian',
			),
			'ido'            => array(
				'slug' => 'ido',
				'name' => 'Ido',
			),
			'is_IS'          => array(
				'slug' => 'is',
				'name' => 'Icelandic',
			),
			'it_IT'          => array(
				'slug' => 'it',
				'name' => 'Italian',
			),
			'ja'             => array(
				'slug' => 'ja',
				'name' => 'Japanese',
			),
			'jv_ID'          => array(
				'slug' => 'jv',
				'name' => 'Javanese',
			),
			'ka_GE'          => array(
				'slug' => 'ka',
				'name' => 'Georgian',
			),
			'kab'            => array(
				'slug' => 'kab',
				'name' => 'Kabyle',
			),
			'kal'            => array(
				'slug' => 'kal',
				'name' => 'Greenlandic',
			),
			'kin'            => array(
				'slug' => 'kin',
				'name' => 'Kinyarwanda',
			),
			'kk'             => array(
				'slug' => 'kk',
				'name' => 'Kazakh',
			),
			'km'             => array(
				'slug' => 'km',
				'name' => 'Khmer',
			),
			'kn'             => array(
				'slug' => 'kn',
				'name' => 'Kannada',
			),
			'ko_KR'          => array(
				'slug' => 'ko',
				'name' => 'Korean',
			),
			'kir'            => array(
				'slug' => 'kir',
				'name' => 'Kyrgyz',
			),
			'lb_LU'          => array(
				'slug' => 'lb',
				'name' => 'Luxembourgish',
			),
			'li'             => array(
				'slug' => 'li',
				'name' => 'Limburgish',
			),
			'lin'            => array(
				'slug' => 'lin',
				'name' => 'Lingala',
			),
			'lo'             => array(
				'slug' => 'lo',
				'name' => 'Lao',
			),
			'lt_LT'          => array(
				'slug' => 'lt',
				'name' => 'Lithuanian',
			),
			'lv'             => array(
				'slug' => 'lv',
				'name' => 'Latvian',
			),
			'me_ME'          => array(
				'slug' => 'me',
				'name' => 'Montenegrin',
			),
			'mg_MG'          => array(
				'slug' => 'mg',
				'name' => 'Malagasy',
			),
			'mk_MK'          => array(
				'slug' => 'mk',
				'name' => 'Macedonian',
			),
			'ml_IN'          => array(
				'slug' => 'ml',
				'name' => 'Malayalam',
			),
			'mlt'            => array(
				'slug' => 'mlt',
				'name' => 'Maltese',
			),
			'mn'             => array(
				'slug' => 'mn',
				'name' => 'Mongolian',
			),
			'mr'             => array(
				'slug' => 'mr',
				'name' => 'Marathi',
			),
			'mri'            => array(
				'slug' => 'mri',
				'name' => 'Maori',
			),
			'ms_MY'          => array(
				'slug' => 'ms',
				'name' => 'Malay',
			),
			'my_MM'          => array(
				'slug' => 'mya',
				'name' => 'Myanmar (Burmese)',
			),
			'ne_NP'          => array(
				'slug' => 'ne',
				'name' => 'Nepali',
			),
			'nb_NO'          => array(
				'slug' => 'nb',
				'name' => 'Norwegian (Bokmål)',
			),
			'nl_NL'          => array(
				'slug' => 'nl',
				'name' => 'Dutch',
			),
			'nl_BE'          => array(
				'slug' => 'nl-be',
				'name' => 'Dutch (Belgium)',
			),
			'nn_NO'          => array(
				'slug' => 'nn',
				'name' => 'Norwegian (Nynorsk)',
			),
			'oci'            => array(
				'slug' => 'oci',
				'name' => 'Occitan',
			),
			'ory'            => array(
				'slug' => 'ory',
				'name' => 'Oriya',
			),
			'os'             => array(
				'slug' => 'os',
				'name' => 'Ossetic',
			),
			'pa_IN'          => array(
				'slug' => 'pa',
				'name' => 'Punjabi',
			),
			'pl_PL'          => array(
				'slug' => 'pl',
				'name' => 'Polish',
			),
			'pt_BR'          => array(
				'slug' => 'pt-br',
				'name' => 'Portuguese (Brazil)',
			),
			'pt_PT'          => array(
				'slug' => 'pt',
				'name' => 'Portuguese (Portugal)',
			),
			'ps'             => array(
				'slug' => 'ps',
				'name' => 'Pashto',
			),
			'rhg'            => array(
				'slug' => 'rhg',
				'name' => 'Rohingya',
			),
			'ro_RO'          => array(
				'slug' => 'ro',
				'name' => 'Romanian',
			),
			'roh'            => array(
				'slug' => 'roh',
				'name' => 'Romansh',
			),
			'ru_RU'          => array(
				'slug' => 'ru',
				'name' => 'Russian',
			),
			'rue'            => array(
				'slug' => 'rue',
				'name' => 'Rusyn',
			),
			'rup_MK'         => array(
				'slug' => 'rup',
				'name' => 'Aromanian',
			),
			'sah'            => array(
				'slug' => 'sah',
				'name' => 'Sakha',
			),
			'sa_IN'          => array(
				'slug' => 'sa-in',
				'name' => 'Sanskrit',
			),
			'scn'            => array(
				'slug' => 'scn',
				'name' => 'Sicilian',
			),
			'si_LK'          => array(
				'slug' => 'si',
				'name' => 'Sinhala',
			),
			'sk_SK'          => array(
				'slug' => 'sk',
				'name' => 'Slovak',
			),
			'sl_SI'          => array(
				'slug' => 'sl',
				'name' => 'Slovenian',
			),
			'sna'            => array(
				'slug' => 'sna',
				'name' => 'Shona',
			),
			'snd'            => array(
				'slug' => 'snd',
				'name' => 'Sindhi',
			),
			'so_SO'          => array(
				'slug' => 'so',
				'name' => 'Somali',
			),
			'sq'             => array(
				'slug' => 'sq',
				'name' => 'Albanian',
			),
			'sq_XK'          => array(
				'slug' => 'sq-xk',
				'name' => 'Shqip (Kosovo)',
			),
			'sr_RS'          => array(
				'slug' => 'sr',
				'name' => 'Serbian',
			),
			'srd'            => array(
				'slug' => 'srd',
				'name' => 'Sardinian',
			),
			'su_ID'          => array(
				'slug' => 'su',
				'name' => 'Sundanese',
			),
			'sv_SE'          => array(
				'slug' => 'sv',
				'name' => 'Swedish',
			),
			'sw'             => array(
				'slug' => 'sw',
				'name' => 'Swahili',
			),
			'syr'            => array(
				'slug' => 'syr',
				'name' => 'Syriac',
			),
			'szl'            => array(
				'slug' => 'szl',
				'name' => 'Silesian',
			),
			'ta_IN'          => array(
				'slug' => 'ta',
				'name' => 'Tamil',
			),
			'ta_LK'          => array(
				'slug' => 'ta-lk',
				'name' => 'Tamil (Sri Lanka)',
			),
			'tah'            => array(
				'slug' => 'tah',
				'name' => 'Tahitian',
			),
			'te'             => array(
				'slug' => 'te',
				'name' => 'Telugu',
			),
			'tg'             => array(
				'slug' => 'tg',
				'name' => 'Tajik',
			),
			'th'             => array(
				'slug' => 'th',
				'name' => 'Thai',
			),
			'tir'            => array(
				'slug' => 'tir',
				'name' => 'Tigrinya',
			),
			'tl'             => array(
				'slug' => 'tl',
				'name' => 'Tagalog',
			),
			'tr_TR'          => array(
				'slug' => 'tr',
				'name' => 'Turkish',
			),
			'tt_RU'          => array(
				'slug' => 'tt',
				'name' => 'Tatar',
			),
			'tuk'            => array(
				'slug' => 'tuk',
				'name' => 'Turkmen',
			),
			'twd'            => array(
				'slug' => 'twd',
				'name' => 'Tweants',
			),
			'tzm'            => array(
				'slug' => 'tzm',
				'name' => 'Tamazight (Central Atlas)',
			),
			'ug_CN'          => array(
				'slug' => 'ug',
				'name' => 'Uighur',
			),
			'uk'             => array(
				'slug' => 'uk',
				'name' => 'Ukrainian',
			),
			'ur'             => array(
				'slug' => 'ur',
				'name' => 'Urdu',
			),
			'uz_UZ'          => array(
				'slug' => 'uz',
				'name' => 'Uzbek',
			),
			'vi'             => array(
				'slug' => 'vi',
				'name' => 'Vietnamese',
			),
			'wa'             => array(
				'slug' => 'wa',
				'name' => 'Walloon',
			),
			'xho'            => array(
				'slug' => 'xho',
				'name' => 'Xhosa',
			),
			'xmf'            => array(
				'slug' => 'xmf',
				'name' => 'Mingrelian',
			),
			'yor'            => array(
				'slug' => 'yor',
				'name' => 'Yoruba',
			),
			'zh_CN'          => array(
				'slug' => 'zh-cn',
				'name' => 'Chinese (China)',
			),
			'zh_HK'          => array(
				'slug' => 'zh-hk',
				'name' => 'Chinese (Hong Kong)',
			),
			'zh_TW'          => array(
				'slug' => 'zh-tw',
				'name' => 'Chinese (Taiwan)',
			),
			'de_DE_formal'   => array(
				'slug' => 'de/formal',
				'name' => 'German (Formal)',
			),
			'nl_NL_formal'   => array(
				'slug' => 'nl/formal',
				'name' => 'Dutch (Formal)',
			),
			'de_CH_informal' => array(
				'slug' => 'de-ch/informal',
				'name' => 'Chinese (Taiwan)',
			),
			'pt_PT_ao90'     => array(
				'slug' => 'pt/ao90',
				'name' => 'Portuguese (Portugal, AO90)',
			),
		);

		/**
		 * ThemeIsle_SDK_Feedback_Translate constructor.
		 *
		 * @param ThemeIsle_SDK_Product $product_object The product object.
		 */
		public function __construct( $product_object ) {
			parent::__construct( $product_object );
		}

		/**
		 * Return the locale path.
		 *
		 * @param string $locale Locale code.
		 *
		 * @return string Locale path.
		 */
		private function get_locale_paths( $locale ) {
			if ( empty( $locale ) ) {
				return '';
			}

			$slug = isset( $this->locales[ $locale ] ) ? $this->locales[ $locale ]['slug'] : '';
			if ( empty( $slug ) ) {
				return '';
			}
			if ( strpos( $slug, '/' ) === false ) {
				$slug .= '/default';
			}
			$url = 'https://translate.wordpress.org/projects/wp-' . $this->product->get_type() . 's/' . $this->product->get_slug() . '/' . ( $this->product->get_type() === 'plugin' ? 'dev/' : '' ) . $slug . '?filters%5Bstatus%5D=untranslated&sort%5Bby%5D=random';

			return $url;
		}

		/**
		 * Registers the hooks
		 */
		public function setup_hooks_child() {
			add_action( 'wp_ajax_' . $this->product->get_key() . __CLASS__, array( $this, 'dismiss' ) );
		}

		/**
		 * Either we should show the notification or not.
		 *
		 * @return bool Valid notification.
		 */
		function can_notify() {
			if ( ! $this->product->is_wordpress_available() ) {
				$this->disable();
				return false;
			}
			$show = get_option( $this->product->get_key() . '_translate_flag', 'yes' );
			if ( 'no' === $show ) {
				return false;
			}
			$lang = $this->get_user_locale();
			if ( 'en_US' === $lang ) {
				return false;
			}
			$languages = $this->get_translations();
			if ( ! is_array( $languages ) ) {
				return false;
			}
			if ( ! isset( $languages['translations'] ) ) {
				return false;
			}

			$languages = $languages['translations'];
			$available = wp_list_pluck( $languages, 'language' );
			if ( in_array( $lang, $available ) ) {
				return false;
			}
			if ( ! isset( $this->locales[ $lang ] ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Get the user's locale.
		 */
		private function get_user_locale() {
			global $wp_version;
			if ( version_compare( $wp_version, '4.7.0', '>=' ) ) {
				return get_user_locale();
			}
			$user = wp_get_current_user();
			if ( $user ) {
				$locale = $user->locale;
			}
			return $locale ? $locale : get_locale();
		}

		/**
		 * Shows the notification
		 */
		function show_notification() {
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}

		/**
		 * Shows the admin notice
		 */
		function admin_notices() {
			$id = $this->product->get_key() . '_translate';

			$this->add_css( $this->product->get_key() );
			$this->add_js( $this->product->get_key() );
			$html = $this->get_html( $this->product->get_key() );

			if ( $html ) {
				echo '<div class="notice notice-success is-dismissible" id="' . $id . '" ><div class="themeisle-translate-box">' . $html . '</div></div>';
			}
		}

		/**
		 * Loads the css
		 *
		 * @param string $key The product key.
		 */
		function add_css( $key ) {
			?>
			<style type="text/css" id="<?php echo $key; ?>ti-translate-css">
			</style>
			<?php
		}

		/**
		 * Loads the js
		 *
		 * @param string $key The product key.
		 */
		function add_js( $key ) {
			?>
			<script type="text/javascript" id="<?php echo $key; ?>ti-translate-js">
				(function ($) {
					$(document).ready(function () {
						$('#<?php echo $key; ?>_translate').on('click', '.translate-dismiss', function (e) {

							$.ajax({
								url: ajaxurl,
								method: "post",
								data: {
									'nonce': '<?php echo wp_create_nonce( (string) __CLASS__ ); ?>',
									'action': '<?php echo $this->product->get_key() . __CLASS__; ?>'
								},
								success: function () {
									$('#<?php echo $key; ?>_translate').html('<p><b>Thanks for your answer.</b></p>');
								}
							});
						});
					});
				})(jQuery);
			</script>
			<?php
		}

		/**
		 * Fetch translations from api.
		 *
		 * @return mixed Translation array.
		 */
		private function get_translations() {
			$cache_key    = $this->product->get_key() . '_all_languages';
			$translations = get_transient( $cache_key );

			if ( $translations === false ) {
				require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
				$translations = translations_api(
					$this->product->get_type() . 's',
					array(
						'slug'    => $this->product->get_slug(),
						'version' => $this->product->get_version(),
					)
				);
				set_transient( $cache_key, $translations, WEEK_IN_SECONDS );
			}

			return $translations;

		}

		/**
		 * Generates the HTML
		 *
		 * @param   string $key The product key.
		 *
		 * @return  void|string Html code of the notification.
		 */
		function get_html( $key ) {
			$lang    = $this->get_user_locale();
			$link    = $this->get_locale_paths( $lang );
			$heading = apply_filters( $this->product->get_key() . '_feedback_translate_heading', $this->heading );
			$product = $this->product->get_friendly_name();
			$heading = str_replace(
				array( '{product}' ),
				$product, $heading
			);

			$message       = apply_filters( $this->product->get_key() . '_feedback_translation', $this->msg );
			$language_meta = $this->locales[ $lang ];
			$message       = str_replace( '{language}', $language_meta['name'], $message );
			$message       = str_replace( '{product}', $product, $message );
			$button_cancel = apply_filters( $this->product->get_key() . '_feedback_translate_button_cancel', $this->button_cancel );
			$button_do     = apply_filters( $this->product->get_key() . '_feedback_translate_button_do', $this->button_do );

			return '<div id="' . $this->product->get_key() . '-translate-notification" class="themeisle-sdk-translate-box">'
				   . '<h2>' . $heading . '</h2>'
				   . '<p>' . $message . '</p>'
				   . '<div class="actions">'
				   . '<a href="' . $link . '" target="_blank" class="button button-primary translate-dismiss"> ' . $button_do . '</a>&nbsp;'
				   . get_submit_button( $button_cancel, 'translate-dismiss ' . $this->product->get_key() . '-ti-translate', $this->product->get_key() . 'ti-translate-no', false )
				   . '</div></br></div>';
		}

		/**
		 * Called when the either button is clicked
		 */
		function dismiss() {
			check_ajax_referer( (string) __CLASS__, 'nonce' );

			$this->disable();
		}

		/**
		 * Disables the notification
		 */
		protected function disable() {

			update_option( $this->product->get_key() . '_translate_flag', 'no' );
		}

		/**
		 * Enables the notification
		 */
		protected function enable() {
			update_option( $this->product->get_key() . '_translate_flag', 'yes' );
		}
	}
endif;
