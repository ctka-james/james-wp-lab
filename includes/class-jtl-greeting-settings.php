<?php
/**
 * 問候 Shortcode 預設名稱的後台設定類別。
 *
 * @package James_WP_Lab
 */

// 防止使用者直接以網址執行此 PHP 檔案。
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 管理問候 Shortcode 的預設名稱設定。
 */
class JTL_Greeting_Settings {
	/**
	 * 註冊設定頁與 Settings API Hook。
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * 在「設定」選單下註冊外掛設定頁。
	 *
	 * @return void
	 */
	public function register_settings_page() {
		add_options_page(
			esc_html__( 'James WP Lab', 'james-wp-lab' ),
			esc_html__( 'James WP Lab', 'james-wp-lab' ),
			'manage_options',
			'james-wp-lab',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * 註冊問候預設名稱的 Settings API 設定。
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'jtl_greeting_settings',
			'jtl_default_greeting_name',
			array(
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => array( $this, 'sanitize_default_greeting_name' ),
			)
		);

		add_settings_section(
			'jtl_greeting_settings_section',
			esc_html__( '問候設定', 'james-wp-lab' ),
			array( $this, 'render_greeting_section' ),
			'james-wp-lab'
		);

		add_settings_field(
			'jtl_default_greeting_name_field',
			esc_html__( '預設問候名稱', 'james-wp-lab' ),
			array( $this, 'render_default_name_field' ),
			'james-wp-lab',
			'jtl_greeting_settings_section',
			array(
				'label_for' => 'jtl_default_greeting_name',
			)
		);
	}

	/**
	 * 清理預設問候名稱。
	 *
	 * @param mixed $value 待清理的設定值。
	 * @return string 清理後的名稱。
	 */
	public function sanitize_default_greeting_name( $value ) {
		if ( ! is_string( $value ) ) {
			return '';
		}

		return sanitize_text_field( $value );
	}

	/**
	 * 輸出設定頁面。
	 *
	 * @return void
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( '抱歉，您沒有權限存取此頁面。', 'james-wp-lab' ) );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'James WP Lab', 'james-wp-lab' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'jtl_greeting_settings' );
				do_settings_sections( 'james-wp-lab' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * 輸出問候設定 section 的說明。
	 *
	 * @return void
	 */
	public function render_greeting_section() {
		?>
		<p><?php echo esc_html__( '設定未指定 name 屬性的問候 Shortcode 預設名稱。', 'james-wp-lab' ); ?></p>
		<?php
	}

	/**
	 * 輸出預設名稱輸入欄位。
	 *
	 * @return void
	 */
	public function render_default_name_field() {
		$value = get_option( 'jtl_default_greeting_name', '' );

		if ( ! is_string( $value ) ) {
			$value = '';
		}
		?>
		<input id="jtl_default_greeting_name" name="jtl_default_greeting_name" type="text" class="regular-text" value="<?php echo esc_attr( $value ); ?>" />
		<?php
	}
}
