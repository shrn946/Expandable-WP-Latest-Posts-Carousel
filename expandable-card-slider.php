<?php
/*
Plugin Name: Latest Posts Carousel
Description: Displays the latest WordPress posts in a carousel.
Version: 1.1
Author: Hassan Naqvi
*/

// Enqueue necessary scripts and styles
function custom_plugin_enqueue_scripts() {
    // jQuery
    wp_enqueue_script('jquery');

    // Owl Carousel JavaScript
    wp_enqueue_script(
        'owl-carousel-js',
        'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js',
        array('jquery'),
        '2.3.4',
        true
    );

    // Custom JavaScript
    wp_enqueue_script(
        'custom-script',
        plugin_dir_url(__FILE__) . 'script.js',
        array('jquery', 'owl-carousel-js'),
        '1.1',
        true
    );

    // Bootstrap Grid only to avoid conflicts
    wp_enqueue_style(
        'bootstrap-grid',
        'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap-grid.min.css',
        array(),
        '4.5.0'
    );

    // Owl Carousel CSS
    wp_enqueue_style(
        'owl-carousel-css',
        'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css',
        array(),
        '2.3.4'
    );

    // Owl Carousel Default Theme CSS
    wp_enqueue_style(
        'owl-carousel-theme-default-css',
        'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css',
        array(),
        '2.3.4'
    );

    // Custom CSS
    wp_enqueue_style(
        'custom-style',
        plugin_dir_url(__FILE__) . 'style.css',
        array(),
        '1.1'
    );
}
add_action('wp_enqueue_scripts', 'custom_plugin_enqueue_scripts');

// Add script for Owl Carousel initialization and item click
function custom_plugin_init_script() {
    ?>
    <script>
    jQuery(document).ready(function ($) {
        $(".custom-carousel").owlCarousel({
            autoWidth: true,
            loop: true,
            nav: false,
            dots: true
        });

        $(".custom-carousel").on('click', '.item', function () {
            $(".custom-carousel .item").not($(this)).removeClass("active");
            $(this).toggleClass("active");
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'custom_plugin_init_script');

// Function to generate latest posts carousel shortcode
function latest_posts_carousel_shortcode($atts) {
    // Check Elementor editor mode
    if (class_exists('\Elementor\Plugin') && \Elementor\Plugin::instance()->editor->is_edit_mode()) {
        return '';
    }

    $atts = shortcode_atts(array(
        'exclude_category' => '',
        'fallback_image'   => plugin_dir_url(__FILE__) . 'fallback-image.png',
        'count'            => -1
    ), $atts);

    ob_start();

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => $atts['count'],
    );

    if (!empty($atts['exclude_category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => explode(",", $atts['exclude_category']),
                'operator' => 'NOT IN',
            ),
        );
    }

    $posts_query = new WP_Query($args);

    if ($posts_query->have_posts()) :
    ?>
    <section class="game-section latest-posts-carousel-wrapper">
        <div class="owl-carousel custom-carousel owl-theme">
            <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                <?php 
                if (!empty($atts['exclude_category']) && in_category($atts['exclude_category'])) {
                    continue;
                }
                $featured_image_url = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'large') : $atts['fallback_image'];
                ?>
                <div class="item" style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
                    <div class="item-desc">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php
    else:
        echo '<p>No posts found</p>';
    endif;

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('latest_posts_carousel', 'latest_posts_carousel_shortcode');

// Admin settings page
function custom_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Latest Posts Carousel Shortcodes</h1>
        <p>Welcome to the Latest Posts Carousel plugin settings page.</p>
        <h2>How to Use Shortcode</h2>
        <p>Display latest posts with default settings:</p>
        <pre>[latest_posts_carousel]</pre>

        <p>Display 3 latest posts excluding "electronics":</p>
        <pre>[latest_posts_carousel exclude_category="electronics" count="3"]</pre>

        <p>Display 8 latest posts with no specific exclusion:</p>
        <pre>[latest_posts_carousel count="8"]</pre>
    </div>
    <?php
}
function custom_plugin_add_menu() {
    add_options_page('Latest Posts Carousel Settings', 'Latest Posts Carousel', 'manage_options', 'latest-posts-carousel-settings', 'custom_plugin_settings_page');
}
add_action('admin_menu', 'custom_plugin_add_menu');
