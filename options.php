<?php
/*
Plugin Name: Mailchimp Newsletter Ajax
Plugin URI: www.thomaskuhnert.com
Description: subscribe to Mailchimp list with api_key and list_id
Version: 1
Author: Thomas Kuhnert
Author URI: www.thomaskuhnert.com
*/


require 'shortcode.php';
require 'mc-config.php';

if(isset($_POST["subscribers"]) && $_POST["subscribers"] == 'delete'){
    update_option( 'tma_subscribers', array() );
}


add_action('plugins_loaded', 'tma_translation');
function tma_translation() {
    load_plugin_textdomain( 'tommy-mailchimp-ajax', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}


add_action( 'wp_enqueue_scripts', 'load_plugin_css', 15 );
function load_plugin_css() {
    $plugin_url = dirname( plugin_basename(__FILE__) );

    wp_enqueue_style( 'tommy-mailchimp-ajax', plugin_dir_url(__FILE__) . '/tommy-mailchimp-ajax.css');
}

add_action('admin_menu', 'test_plugin_setup_menu');
function test_plugin_setup_menu(){
    add_options_page(
            'Mailchimp-Ajax',
            'Mailchimp-Ajax',
            'manage_options',
            __FILE__,
            'form_for_mailchimp_settings'
        );
}

function form_for_mailchimp_settings(){
    ?>
	    <div class="wrap">
        <h1><?php _e("Mailchimp settings", 'tommy-mailchimp-ajax') ?></h1>
        <p><?php _e("To display the form on the website somewhere else than in the about us section, put this shortcode somewhere you want to display it. It could be a widget or a simply a page.", 'tommy-mailchimp-ajax') ?></p>
        <code>[newsletter-form]</code>
	    <form method="post" action="options.php">
            <?php
                settings_fields("section");
                do_settings_sections("theme-options");
                submit_button();
                ?>

        </form>


        <?php display_subscribers() ?>

        <form action="<?php echo $_SERVER['REQUEST_URI']?>" method="post">
            <p class="submit">
            <button type="submit" name="subscribers" id="subscribers" class="button" value="delete" onclick="return confirm('<?php echo htmlspecialchars("Sicher? Dies löscht die Liste in der Wordpress Datenbank. Hoffentlich sind alle diese Adressen bei Mailchimp importiert."); ?>')"/>Liste löschen</button>
            </p>
        </form>

		</div>
	<?php
}

function display_api_key_input(){
	?>
    	<input type="text" name="api_key" id="api_key" value="<?php echo get_option('api_key'); ?>" />
    <?php
}

function display_list_id(){
    ?>
        <input type="text" name="list_id" id="list_id" value="<?php echo get_option('list_id'); ?>" />
    <?php
}

function display_opt_in_box(){
    ?>
        <input type="checkbox" id="opt_in" name="opt_in" value="1" <?php checked(checked( get_option('opt_in'), 1 )) ?>/>
    <?php
}

function display_subscribers()
{

    if($subscribers = get_option('tma_subscribers')){

        $html = '
        <table class="widefat striped">
            <thead>
                <tr>
                <th>Kürzliche Registrierungen
                </th>
                <th>Zeitpunkt
                </th>
                </tr>
            </thead>

            <tbody>
            ';
            foreach ($subscribers as $key => $value) {
                $html .=
                '<tr>
                    <td>'.$value['email'].'</td>

                    <td>' . get_date_from_gmt( date( 'Y-m-d H:i:s', $value['date'] ), ' j M Y - H:i:s' ).'</td>
                </tr>';
            }

            $html .= '
            <tr class="list-details list-014f2b7f68-details">
            </tr>
            </tbody>
        </table>
        ';
        echo $html;
    }
}

function display_theme_panel_fields()
{
	add_settings_section("section", "Einstellugen", null, "theme-options");

	add_settings_field("api_key", "Mailchimp Api-Key", "display_api_key_input", "theme-options", "section");
    add_settings_field("list_id", "Mailchimp List-ID", "display_list_id", "theme-options", "section");

    add_settings_field("opt_in", "Wenn sich jemand einträgt, Bestätigungslink oder sofort eintragen?", "display_opt_in_box", "theme-options", "section");

    register_setting("section", "api_key");
    register_setting("section", "list_id");
    register_setting("section", "opt_in");
    register_setting("section", "subscribers");
}

add_action("admin_init", "display_theme_panel_fields");




add_filter('plugin_action_links', 'myplugin_plugin_action_links', 10, 2);

function myplugin_plugin_action_links($links, $file) {
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=wp-plugin-mailchimp-ajax-subscribe%2Foptions.php">Settings</a>';
        array_unshift($links, $settings_link);
    }

    return $links;
}
