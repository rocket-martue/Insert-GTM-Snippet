<?php
/**
 * Plugin Name: Insert GTM Snippet
 * Description: This plugin inserts GTM snippets into the site.
 * Version: 1.0.0
 * Author: Osamu Takahashi
 * Author URI: https://profiles.wordpress.org/osamunize/
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: insert-gtm-snippet
 * Domain Path: /languages
 */
defined( 'ABSPATH' ) || exit;

// メニューを追加
    add_action( 'admin_menu', 'register_insert_gtm_snippet_menu_page' );
    function register_insert_gtm_snippet_menu_page(){
        add_menu_page( 'Insert GTM Snippet', 'Insert GTM Snippet','manage_options', 'insert_gtm_snippet', 'mt_insert_gtm_snippet_settings_page', ''); 
    }
    function mt_insert_gtm_snippet_settings_page() {

// ユーザーが必要な権限を持つか確認
    if (!current_user_can('manage_options'))
    {
        wp_die( __('You do not have sufficient permissions to access this page.') );
    }

// フィールドとオプション名の変数
    $opt_name = 'gtm_snippet';
    $hidden_field_name = 'mt_submit_hidden';
    $data_field_name = 'gtm_snippet';

// DBから既存のオプション値を取得
    $opt_val = get_option( $opt_name );

// ユーザーが何か情報を POST したかどうかを確認
// POST していれば、隠しフィールドに 'Y' が設定されている
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // POST されたデータを取得
        $opt_val = $_POST[ $data_field_name ];
        // POST された値をDBに保存
        update_option( $opt_name, $opt_val );
        // 画面に「Setting Saves」メッセージを表示
        ?>
        <div class="updated"><p><strong><?php _e('Settings Saved.', 'gtm_snippet_menu' ); ?></strong></p></div>
        <?php
    }

    echo '<div class="wrap">';
    echo "<h2>" . __( 'Insert GTM Snippet', 'gtm_snippet_menu' ) . "</h2>";
    ?>
    <form name="form1" method="post" action="">
    <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
    <p><?php _e("GTM Container ID (GTM-XXXXXXX):", 'gtm_snippet_menu' ); ?> 
    <input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
    <p>If the value is empty or does not exist, no snippet is output.</p>
    <p class="submit">
    <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
    </p>
    </form>
    </div>
    <?php
    }

function gtm_inserter_head(){
    $opt_val = get_option( 'gtm_snippet' );
    if ( !null == $opt_val ){
        echo "
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','".$opt_val."');</script>
        <!-- End Google Tag Manager -->
        "."\n";}
    }
    add_action('wp_head', 'gtm_inserter_head' , 1);

function gtm_inserter_body(){
    $opt_val = get_option( 'gtm_snippet' );
    if ( !null == $opt_val ){
        echo '
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id='.$opt_val.'"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->	
        '."\n";}
    }
    add_action('wp_body_open', 'gtm_inserter_body');
