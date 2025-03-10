<?php

function comparison_slider_shortcode_preview() {
    // Get stored images and width from wp_options
    $settings = get_option('comparison_slider_settings', [
        'before_image' => '',
        'after_image'  => '',
        'width'        => '600'
    ]);

    // Default images if not set
    $before_image = !empty($settings['before_image']) ? esc_url($settings['before_image']) : 'https://github.com/lajumia/comparison-slider/blob/main/Before.jpeg';
    $after_image  = !empty($settings['after_image']) ? esc_url($settings['after_image']) : 'https://github.com/lajumia/comparison-slider/blob/main/After.jpeg';
    $width        = isset($settings['width']) ? intval($settings['width']) : 600;

    ob_start(); ?>


<div class="container">
        <div class="inner">                        
            <div class="comparison-slider-wrapper">
                <div class="comparison-slider">
                    <div class="overlay">And I am the <strong>after</strong> image.</div>
                    <img src="<?php echo $before_image;?>" alt="Before Image"/>
                
                    <div class="resize">
                        <div class="overlay">I am the <strong>before</strong> image.</div>
                        <img className="second-img" src="<?php echo $after_image;?>" alt="After Image"/>
                    </div>
                
                    <div class="divider"></div>
                </div>
            </div>
            
        </div>

    </div>
<?php
};
comparison_slider_shortcode_preview();