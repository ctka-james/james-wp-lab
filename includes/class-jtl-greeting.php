<?php
/**
 * 問候 Shortcode 與前台資源載入類別。
 *
 * @package James_WP_Lab
 */

// 防止使用者直接以網址執行此 PHP 檔案。
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 管理互動問候 Shortcode 的輸出與前台資源。
 */
class JTL_Greeting {
	/**
	 * 外掛根目錄 URL。
	 *
	 * @var string
	 */
	private $plugin_url;

	/**
	 * 註冊問候 Shortcode 與前台資源載入 Hook。
	 *
	 * @param string $plugin_url 外掛根目錄 URL。
	 */
	public function __construct( $plugin_url ) {
		$this->plugin_url = $plugin_url;

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_greeting_assets' ) );
		add_shortcode( 'jtl_greeting', array( $this, 'render_greeting_shortcode' ) );
	}

	/**
	 * 僅在目前單篇內容使用 [jtl_greeting] 時載入前台資源。
	 *
	 * @return void
	 */
	public function enqueue_greeting_assets() {
		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();

		if ( ! $post instanceof WP_Post || ! has_shortcode( $post->post_content, 'jtl_greeting' ) ) {
			return;
		}

		wp_enqueue_style(
			'jtl-shortcode',
			$this->plugin_url . 'assets/css/jtl-shortcode.css',
			array(),
			'0.3.0'
		);

		wp_enqueue_script(
			'jtl-shortcode',
			$this->plugin_url . 'assets/js/jtl-shortcode.js',
			array(),
			'0.3.0',
			true
		);
	}

	/**
	 * 取得並清理後台設定的預設問候名稱。
	 *
	 * @return string 清理後的預設名稱。
	 */
	private function get_default_greeting_name() {
		$name = get_option( 'jtl_default_greeting_name', '' );

		if ( ! is_string( $name ) ) {
			return '';
		}

		return sanitize_text_field( $name );
	}

	/**
	 * 產生可插入文章或頁面的互動問候 Shortcode。
	 *
	 * 使用方式：
	 * [jtl_greeting]
	 * [jtl_greeting name="James"]
	 *
	 * @param array<string, mixed> $atts Shortcode 屬性。
	 * @return string 可安全輸出的 HTML。
	 */
	public function render_greeting_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'name' => '',
			),
			$atts,
			'jtl_greeting'
		);

		$name = is_string( $atts['name'] ) ? sanitize_text_field( $atts['name'] ) : '';

		if ( '' === $name ) {
			$name = $this->get_default_greeting_name();
		}

		if ( '' === $name ) {
			$name = __( '訪客', 'james-wp-lab' );
		}

		$title = sprintf(
			__( '你好，%s！', 'james-wp-lab' ),
			$name
		);

		$details_id = wp_unique_id( 'jtl-greeting-details-' );

		return sprintf(
			'<section class="jtl-greeting" data-jtl-greeting>
			<h2 class="jtl-greeting__title">%1$s</h2>
			<button class="jtl-greeting__toggle" type="button" data-jtl-toggle aria-expanded="false" aria-controls="%2$s">%3$s</button>
			<p id="%2$s" class="jtl-greeting__details" data-jtl-details hidden>%4$s</p>
		</section>',
			esc_html( $title ),
			esc_attr( $details_id ),
			esc_html__( '顯示說明', 'james-wp-lab' ),
			esc_html__( '這是一個由 WordPress Shortcode、CSS 與原生 JavaScript 組成的互動元件。', 'james-wp-lab' )
		);
	}
}