<?php
/*
Plugin Name: Novitell Recent Products
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Creating the widget
class wpb_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
// Base ID of your widget
            'novitell_recent_products',

// Widget name will appear in UI
            __('Novitell- Nya produkter / Kategori', 'novitell'),

// Widget description
            array( 'description' => __( 'Visa senaste produkterna från en utvald kategori', 'novitell' ), )
        );
    }

// Creating widget front-end
// This is where the action happens
    public function widget( $args, $instance ) {
        if (is_woocommerce_activated()) {

            $title = apply_filters( 'widget_title', $instance['title'] );

            $args = apply_filters('storefront_recent_products_args', array(
                'limit' => 4,
                'columns' => 4,
                'title' => $args['before_title'] . $title . $args['after_title'],
            ));

            echo $args['before_widget'];
            echo '<section class="storefront-product-section storefront-recent-products">';

            do_action('storefront_homepage_before_recent_products');

            echo '<h2 class="section-title">' . wp_kses_post($args['title']) . '</h2>';

            do_action('storefront_homepage_after_recent_products_title');

            echo storefront_do_shortcode('recent_products', array(
                'per_page' => intval($args['limit']),
                'columns' => intval($args['columns']),
                'orderby'   => 'date',
                'category' => $instance['cat_slug'],
            ));



            do_action('storefront_homepage_after_recent_products');

            echo '</section>';
            echo $args['after_widget'];
        }
    }

    // Widget Backend
    public function form( $instance ) {
        if (is_woocommerce_activated()) {
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = __('Rubrik för sektionen', 'novitell');
            }
            if (isset($instance['cat_slug'])) {
                $cat_slug = $instance['cat_slug'];
            } else {
                $cat_slug = '';
            }

            // Widget admin form
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Rubrik:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text"
                       value="<?php echo esc_attr($title); ?>"/>
            </p>
            <p>
                <select id="<?php echo $this->get_field_id('cat_slug'); ?>"
                        name="<?php echo $this->get_field_name('cat_slug'); ?>">
                    <option value="0">-- Ingen kategori vald</option>
                    <?php
                    $all_categories = $this->get_categories();

                    foreach ($all_categories as $cat) {
                        $title = $cat->name;
                        $slug = $cat->slug;

                        if ($cat->category_count > 0) {
                            ?>
                            <option value="<?php echo $slug; ?>" <?php selected($slug, $cat_slug); ?>>
                                <?php
                                echo $title . " (" . $cat->category_count . ")";
                                ?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </p>
            <?php
        }
        else {
            echo "You need to activate WooCommerce";
        }

    }

    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['cat_slug'] = $new_instance['cat_slug'];
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }

    /**
     * Fetches all product categories
     * @return array of product categories
     */
    private function get_categories() {
        $taxonomy     = 'product_cat';
        $orderby      = 'name';
        $show_count   = 0;      // 1 for yes, 0 for no
        $pad_counts   = 0;      // 1 for yes, 0 for no
        $hierarchical = 1;      // 1 for yes, 0 for no
        $title        = '';
        $empty        = 0;

        $args = array(
            'taxonomy'     => $taxonomy,
            'orderby'      => $orderby,
            'show_count'   => $show_count,
            'pad_counts'   => $pad_counts,
            'hierarchical' => $hierarchical,
            'title_li'     => $title,
            'hide_empty'   => $empty
        );
        return get_categories( $args );
    }

}

// Register and load the widget
function wpb_load_widget() {
    register_widget( 'wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );