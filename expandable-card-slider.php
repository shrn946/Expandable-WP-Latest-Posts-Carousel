<?php
/*
Plugin Name: Latest Posts Carousel
Description: Displays the latest WordPress posts in a carousel.
Version: 1.0
Author: Hassan Naqvi
*/

// Enqueue necessary scripts and styles
function custom_plugin_enqueue_scripts() {
    // jQuery
    wp_enqueue_script('jquery');

    // Owl Carousel JavaScript
    wp_enqueue_script('owl-carousel-js', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array('jquery'), '2.3.4', true);

    // Custom JavaScript
    wp_enqueue_script('custom-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery', 'owl-carousel-js'), '1.0', true);

    // Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css', array(), '4.5.0');

    // Owl Carousel CSS
    wp_enqueue_style('owl-carousel-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css', array(), '2.3.4');

    // Owl Carousel Default Theme CSS
    wp_enqueue_style('owl-carousel-theme-default-css', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css', array(), '2.3.4');

    // Custom CSS
    wp_enqueue_style('custom-style', plugin_dir_url(__FILE__) . 'style.css', array(), '1.0');
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
            nav: false, // Enable navigation arrows
            dots: true // Enable navigation dots
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
    // Check if we are in the Elementor editor
    if (\Elementor\Plugin::instance()->editor->is_edit_mode()) {
        return ''; // Return an empty string to hide the shortcode in Elementor editor
    }

    // Shortcode attributes with defaults
    $atts = shortcode_atts(array(
        'exclude_category' => '', // Category slug(s) to exclude
        'fallback_image' => plugin_dir_url(__FILE__) . 'fallback-image.png', // Fallback image path
        'count' => -1 // Number of posts to display
    ), $atts);

    ob_start();

    // Retrieve latest posts
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => $atts['count'], // Number of posts to display
    );

    // Exclude posts by category slug if specified
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
    <section class="game-section">
        <div class="owl-carousel custom-carousel owl-theme">
            <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                <?php 
                // Check if the post belongs to the excluded category, and skip it
                if (in_category($atts['exclude_category'])) {
                    continue;
                }

                // Get the featured image URL, if available, else fallback image
                $featured_image_url = (has_post_thumbnail()) ? get_the_post_thumbnail_url(get_the_ID(), 'large') : $atts['fallback_image'];
                ?>
                <div class="item" style="background-image: url('<?php echo $featured_image_url; ?>');">
                    <div class="item-desc">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3> <!-- Add link to post permalink -->
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







// Function to display the settings page content
function custom_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Latest Posts Carousel Shortcodes</h1>
        <p>Welcome to the Latest Posts Carousel plugin settings page.</p>
        <h2>How to Use Shortcode</h2>
        <p>Here are a couple of shortcode examples for the latest_posts_carousel function:</p>

        <p>Display latest posts with the default settings:</p>
        <pre>[latest_posts_carousel]</pre>

        <p>Display 3 latest posts excluding the "electronics" category:</p>
        <pre>[latest_posts_carousel exclude_category="electronics" count="3"]</pre>

        <p>Display 8 latest posts with no specific exclusion:</p>
        <pre>[latest_posts_carousel count="8"]</pre>

        <p>Feel free to customize the shortcode attributes based on your specific needs. Adjust the exclude_category attribute to specify a particular category to exclude, and use the count attribute to control the number of posts displayed.</p>
    </div>
    <?php
}

// Function to add the settings page to the admin menu
function custom_plugin_add_menu() {
    add_options_page('Latest Posts Carousel Settings', 'Latest Posts Carousel', 'manage_options', 'latest-posts-carousel-settings', 'custom_plugin_settings_page');
}

// Hook to add the settings page
add_action('admin_menu', 'custom_plugin_add_menu');
