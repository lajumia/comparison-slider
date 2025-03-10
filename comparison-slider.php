<?php
/**
 * Plugin Name:       Comparison Slider
 * Plugin URI:        https://github.com/lajumia/comparison-slider
 * Description:       A simple before/after comparison slider plugin.
 * Version:           1.0
 * Author:            Md Laju Miah
 * Author URI:        https://www.upwork.com/freelancers/~0149190c8d83bae2e2
 * License:           GPL-2.0+
 * Text Domain:       comparison-slider
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Main plugin class
class Comparison_Slider {

    // Constructor
    public function __construct() {
        // Hook into WordPress actions and filters
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_shortcode('comparison_slider', array($this, 'slider_shortcode'));
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', [$this, 'save_settings']);
    }

    // Enqueue scripts and styles
    public function enqueue_scripts() {
        wp_enqueue_style('cs-style', plugin_dir_url(__FILE__) . 'assets/css/comparison-slider.css');
        wp_enqueue_script('cs-script', plugin_dir_url(__FILE__) . 'assets/js/comparison-slider.js', array('jquery'), '1.0.0', true);
    }

    // Enqueue scripts and styles for the admin area
    public function enqueue_admin_scripts() {
        wp_enqueue_style('cs-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-comparison-slider.css');
        wp_enqueue_media();
    }

    // Shortcode to display the slider
    public function slider_shortcode($atts) {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/comparison-slider-template.php';
        return ob_get_clean();
    }

    // Hook into the 'admin_menu' action to create a menu item
    public function add_settings_page() {
        add_menu_page(
            'Comparison Slider Settings', 
            'Comparison Slider',         
            'manage_options',             
            'comparison_slider',          
            '',           
            'dashicons-image-filter',     
            80                            
        );
        
        // Add the "Slides" submenu under the main "Comparison Slider" menu
        add_submenu_page(
            'comparison_slider',          
            'Slides',                     
            'Slides',                     
            'manage_options',             
            'comparison_slider_slides',   
            array($this, 'slides_page')   
        );

        // Remove the submenu page by its parent slug and submenu slug
        remove_submenu_page('comparison_slider', 'comparison_slider'); 
    }

    private $option_name = 'comparison_slider_settings';

    // Save form settings
    public function save_settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comparison_slider_settings'])) {
            $data = [
                'before_image' => esc_url($_POST['before_image']),
                'after_image'  => esc_url($_POST['after_image']),
                'width'        => intval($_POST['width'])
            ];
            update_option($this->option_name, $data);
            add_action('admin_notices', function() {
                echo '<div class="updated"><p>Slide saved successfully!</p></div>';
            });
        }
    }

    // Callback function to display the slides page
    public function slides_page() {
        $settings = get_option($this->option_name, [
            'before_image' => '',
            'after_image'  => '',
            'width'        => '500'
        ]);
        ?>

        <div class="comparison-wrap">

            <div class="wrap-left">
                <h1 class="com-heading">Slide </h1>
                <div class="wrap">
                    <form method="post">
                        <input type="hidden" name="comparison_slider_settings" value="1">
                        <table class="form-table">
                            <tr>
                                <th><label for="before_image">Choose before image</label></th>
                                <td>
                                    <input type="hidden" name="before_image" id="before_image" value="<?php echo esc_url($settings['before_image']); ?>" />
                                    
                                    <button type="button" class="button before_image_upload">Choose Image</button>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="after_image">Choose after image</label></th>
                                <td>
                                    <input type="hidden" name="after_image" id="after_image" value="<?php echo esc_url($settings['after_image']); ?>" />
                                    
                                    <button type="button" class="button after_image_upload">Choose Image</button>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="width">Choose slider width</label></th>
                                <td>
                                    <select name="width" id="width">
                                        <?php 
                                        for ($i = 100; $i <= 1000; $i += 100) {
                                            echo '<option value="' . $i . '" ' . selected($settings['width'], $i, false) . '>' . $i . 'px</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="shortcode">Shortcode</label></th>
                                <td>
                                   <input type="text" disabled name="" id="" value="[comparison_slider]">
                                </td>
                            </tr>
                        </table>
                        <p><input type="submit" value="Save Slide" class="button-primary" /></p>
                    </form>
                </div>

            </div>
            <div class="wrap-right">
                <h1 class="com-heading"> Preview</h1>
                <div class="before-img">
                    <h4>Before Image</h4>
                    <img id="before_image_preview" src="<?php echo esc_url($settings['before_image']); ?>" style="width: 300px; display: <?php echo $settings['before_image'] ? 'block' : 'none'; ?>;" />
                </div>
                <div class="after-img">
                    <h4>After Image</h4>
                    <img id="after_image_preview" src="<?php echo esc_url($settings['after_image']); ?>" style="width: 300px; display: <?php echo $settings['after_image'] ? 'block' : 'none'; ?>;" />
                </div>
                
            </div>

        </div>

        <script>
        jQuery(document).ready(function($) {
            function mediaUploader(buttonClass, inputField, previewImage) {
                $(buttonClass).click(function(e) {
                    e.preventDefault();
                    var mediaUploader = wp.media({
                        title: 'Select an Image',
                        button: { text: 'Use this Image' },
                        multiple: false
                    }).on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $(inputField).val(attachment.url);
                        $(previewImage).attr('src', attachment.url).show();
                    }).open();
                });
            }

            mediaUploader('.before_image_upload', '#before_image', '#before_image_preview');
            mediaUploader('.after_image_upload', '#after_image', '#after_image_preview');
        });
        </script>
    


    <?php
    }















}
// Initialize the plugin
new Comparison_Slider();
