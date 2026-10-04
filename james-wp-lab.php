<?php
/**
 * Plugin Name:       James WP Lab
 * Plugin URI:        https://m6-wp.webspots.org
 * Description:       James 的 WordPress 外掛開發學習練習。
 * Version:           0.4.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            James Tsai
 * Text Domain:       james-wp-lab
 *
 * @package James_WP_Lab
 */

// 防止使用者直接以網址執行此 PHP 檔案。
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-jtl-greeting.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-jtl-greeting-settings.php';

new JTL_Greeting( plugin_dir_url( __FILE__ ) );
new JTL_Greeting_Settings();

/**
 * 在管理後台顯示 James WP Lab 的第一則練習通知。
 *
 * 只有具備管理網站設定權限的使用者才會看到這則訊息。
 *
 * @return void
 */
function jtl_display_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="notice notice-info">
		<p>
			<?php echo esc_html__( 'James WP Lab 已載入：這是第一個 admin_notices Action Hook 練習。', 'james-wp-lab' ); ?>
		</p>
	</div>
	<?php
}

add_action( 'admin_notices', 'jtl_display_admin_notice' );