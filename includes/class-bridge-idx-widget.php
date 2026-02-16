<?php
/**
 * Bridge IDX Widget Class.
 *
 * @package Your_Package_Name
 */

class Bridge_IDX_Widget extends WP_Widget {

    /**
     * Construct the widget.
     */
    public function __construct() {
        parent::__construct(
            'bridge_idx_widget',
            __('Bridge IDX Listings', 'text_domain'),
            array('description' => __('Displays MLS listings from the Bridge Interactive API', 'text_domain'))
        );
    }

    /**
     * The widget form (for backend).
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        // Here you can add any widget options if needed.
        // Example: text field for title
        $title = !empty($instance['title']) ? $instance['title'] : __('New title', 'text_domain');
        echo '<p>';
        echo '<label for="' . $this->get_field_id('title') . '">' . __('Title:', 'text_domain') . '</label>';  
        echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" />';
        echo '</p>';
    }

    /**
     * The widget to be displayed on the frontend.
     *
     * @param array $args     Display arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        // Fetch MLS listings from Bridge Interactive API.
        // Example API request (this should be modified according to actual API structure and authentication tokens):
        $api_url = 'https://api.bridgeinteractive.com/listings'; // Replace with actual API URL
        $response = wp_remote_get($api_url);

        if (is_array($response) && !is_wp_error($response)) {
            $listings = json_decode($response['body'], true);
            if (!empty($listings)) {
                echo '<h3>' . apply_filters('widget_title', $instance['title']) . '</h3>';
                echo '<ul>';
                foreach ($listings as $listing) {
                    echo '<li>' . esc_html($listing['title']) . '</li>'; // Modify to output desired listing details.
                }
                echo '</ul>';
            } else {
                echo '<p>No listings found.</p>';
            }
        } else {
            echo '<p>Error fetching listings.</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Updating widget replacing old instances with new.
     *
     * @param array $new_instance New values.
     * @param array $old_instance Old values.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
}

// Register the widget.
function register_bridge_idx_widget() {
    register_widget('Bridge_IDX_Widget');
}
add_action('widgets_init', 'register_bridge_idx_widget');
