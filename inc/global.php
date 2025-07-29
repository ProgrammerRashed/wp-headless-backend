<?php
// DISABLE FRONTEND 
function disable_frontend_for_headless_forbidden()
{
    // Allow admin, login, ajax, and REST API (GraphQL) requests
    if (is_admin() || defined('REST_REQUEST') && REST_REQUEST) {
        return; // allow admin and API requests
    }

    $request_uri = $_SERVER['REQUEST_URI'];

    // Allow wp-login.php and admin-ajax.php
    if (strpos($request_uri, 'wp-login.php') !== false || strpos($request_uri, 'admin-ajax.php') !== false) {
        return;
    }

    // Allow GraphQL endpoint (adjust if different)
    if (strpos($request_uri, '/graphql') === 0) {
        return;
    }

    // For all other frontend requests, send 403 Forbidden
    status_header(403);
    exit;
}
add_action('template_redirect', 'disable_frontend_for_headless_forbidden');


// ADD POST TO GRAPH QL 
function add_graphql_support_to_posts($args, $post_type)
{
    if ('post' === $post_type) {
        $args['show_in_graphql'] = true;
        $args['graphql_single_name'] = 'post';
        $args['graphql_plural_name'] = 'posts';
    }
    return $args;
}
add_filter('register_post_type_args', 'add_graphql_support_to_posts', 10, 2);


// REDIRECT USER TO FRONTEND 
add_action('admin_menu', function () {
    add_menu_page(
        'Post Type URL Control',
        'Post Type URLs',
        'manage_options',
        'post-type-url-control',
        'pt_url_control_page_html'
    );
});

function pt_url_control_page_html()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save on POST
    if (isset($_POST['pt_url_control_nonce']) && wp_verify_nonce($_POST['pt_url_control_nonce'], 'pt_url_control_save')) {
        $data = $_POST['pt_url_control'] ?? [];
        update_option('pt_url_control_data', $data);
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    $saved = get_option('pt_url_control_data', []);
    $post_types = get_post_types(['public' => true], 'objects');
    ?>
    <div class="wrap">
        <h1>Post Type URL Control</h1>
        <form method="post">
            <?php wp_nonce_field('pt_url_control_save', 'pt_url_control_nonce'); ?>
            <table class="widefat fixed" style="max-width:800px;">
                <thead>
                    <tr>
                        <th>Post Type</th>
                        <th>Blank URL</th>
                        <th>Custom URL Base</th>
                        <th>Include in Sitemap</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($post_types as $pt_slug => $pt_obj):
                        $settings = $saved[$pt_slug] ?? [];
                        $blank = !empty($settings['blank']);
                        $custom_url = $settings['custom_url'] ?? '';
                        $include_in_sitemap = !empty($settings['sitemap']);
                        ?>
                        <tr>
                            <td><strong><?php echo esc_html($pt_obj->labels->singular_name); ?>
                                    (<?php echo esc_html($pt_slug); ?>)</strong></td>
                            <td style="text-align:center;">
                                <input type="checkbox" name="pt_url_control[<?php echo esc_attr($pt_slug); ?>][blank]" <?php checked($blank); ?> />
                            </td>
                            <td>
                                <input type="text" style="width:100%;"
                                    name="pt_url_control[<?php echo esc_attr($pt_slug); ?>][custom_url]"
                                    value="<?php echo esc_attr($custom_url); ?>"
                                    placeholder="https://example.com/custom-path" />
                            </td>
                            <td style="text-align:center;">
                                <input type="checkbox" name="pt_url_control[<?php echo esc_attr($pt_slug); ?>][sitemap]" <?php checked($include_in_sitemap); ?> />
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <input type="submit" class="button-primary" value="Save Settings" />
        </form>
    </div>
    <?php
}


// 1. Filter to customize frontend URLs per post type
function my_custom_frontend_view_url($url, $post)
{
    $post_type = get_post_type($post);
    $settings = get_option('pt_url_control_data', []);

    if (!empty($settings[$post_type])) {
        if (!empty($settings[$post_type]['blank'])) {
            return '';
        }
        if (!empty($settings[$post_type]['custom_url'])) {
            $custom_base_url = rtrim($settings[$post_type]['custom_url'], '/');
            $post_slug = $post->post_name ?? '';
            return $custom_base_url . '/' . $post_slug;
        }
    }

    // Default logic (rewrite to frontend URL)
    $frontend_url = carbon_get_theme_option('main_website_url') ?? 'http://localhost:3000';
    $wp_base_url = rtrim(get_site_url(), '/');

    if (strpos($url, $wp_base_url) === 0) {
        $path = substr($url, strlen($wp_base_url));
        $url = rtrim($frontend_url, '/') . $path;
    }

    return $url;
}
add_filter('post_type_link', 'my_custom_frontend_view_url', 10, 2);
add_filter('page_link', 'my_custom_frontend_view_url', 10, 2);
add_filter('preview_post_link', 'my_custom_frontend_view_url', 10, 2);

// 2. Customize admin "View" link to use custom URL and open in new tab, or remove if blank
add_filter('page_row_actions', 'customize_view_link', 10, 2);
add_filter('post_row_actions', 'customize_view_link', 10, 2);

function customize_view_link($actions, $post)
{
    $original_url = get_permalink($post);
    $custom_url = my_custom_frontend_view_url($original_url, $post);

    if (empty($custom_url)) {
        // Remove "View" if URL is blank
        unset($actions['view']);
    } else {
        if (isset($actions['view'])) {
            // Replace href and add target="_blank"
            $actions['view'] = preg_replace(
                '#href="[^"]+"#',
                'href="' . esc_url($custom_url) . '" target="_blank"',
                $actions['view']
            );
        }
    }

    return $actions;
}




// REMOVE YOAST SEO 
add_filter('wpseo_metabox_prio', function ($prio) {
    global $post;
    if ($post && $post->post_type === 'members') {
        return 'low';
    }
    return $prio;
});

// REMOVE WP CATEGORY AND SOME OTHER FIELDS FROM THESE CUSTOM POST TYPE 
add_filter('wpseo_accessible_post_types', function ($post_types) {
    unset($post_types['members']);
    unset($post_types['achievements']);
    unset($post_types['testimonials']);
    unset($post_types['clients']);
    unset($post_types['custom_navigations']);
    return $post_types;
});

// REMOVE CATEGORY AND TAG FROM SELECTED CUSTOM POSTS 
add_action('init', function () {
    unregister_taxonomy_for_object_type('category', 'members');
    unregister_taxonomy_for_object_type('post_tag', 'members');
}, 100); // Priority 100 ensures it runs after all CPTs are registered

add_action('init', function () {
    unregister_taxonomy_for_object_type('category', 'services');
    unregister_taxonomy_for_object_type('post_tag', 'services');
}, 100); // Priority 100 ensures it runs after all CPTs are registered
add_action('init', function () {
    unregister_taxonomy_for_object_type('category', 'blogs');
    unregister_taxonomy_for_object_type('post_tag', 'blogs');
}, 100); // Priority 100 ensures it runs after all CPTs are registered
add_action('init', function () {
    unregister_taxonomy_for_object_type('category', 'custom_navigations');
    unregister_taxonomy_for_object_type('post_tag', 'custom_navigations');
}, 100); // Priority 100 ensures it runs after all CPTs are registered

// REMOVE DEFAULT WP POST
function remove_default_post_type_menu()
{
    remove_menu_page('edit.php'); // Hides the "Posts" menu
}
add_action('admin_menu', 'remove_default_post_type_menu');

function block_post_type_access()
{
    global $pagenow;
    if ($pagenow === 'edit.php' && $_GET['post_type'] === 'post') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'block_post_type_access');



// DISABLES COMMENT SUPPORT 
function disable_comments_support_everywhere()
{
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'disable_comments_support_everywhere');


function disable_comments_frontend()
{
    return false;
}
add_filter('comments_open', 'disable_comments_frontend', 20, 2);
add_filter('pings_open', 'disable_comments_frontend', 20, 2);


// REMOVE COMMENT FROM UI 
function remove_comments_admin_ui()
{
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'remove_comments_admin_ui');

function remove_comments_from_admin_bar()
{
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'remove_comments_from_admin_bar');

// REMOVE COMMENT METABOX 
function remove_comments_meta_boxes()
{
    foreach (get_post_types() as $post_type) {
        remove_meta_box('commentsdiv', $post_type, 'normal');
        remove_meta_box('commentstatusdiv', $post_type, 'normal');
    }
}
add_action('add_meta_boxes', 'remove_comments_meta_boxes');




// SITEMAPS 
// Register GraphQL-only sitemap entries with metadata
add_action('graphql_register_types', function () {
    register_graphql_object_type('SitemapEntry', [
        'description' => 'A sitemap entry with metadata',
        'fields' => [
            'url' => ['type' => 'String', 'description' => 'Frontend URL'],
            'lastModified' => ['type' => 'String', 'description' => 'Last modified date (ISO8601)'],
            'changeFrequency' => ['type' => 'String', 'description' => 'Update frequency'],
            'priority' => ['type' => 'Float', 'description' => 'Priority in sitemap'],
        ],
    ]);

    register_graphql_field('RootQuery', 'customSitemapUrls', [
        'type' => ['list_of' => 'SitemapEntry'],
        'description' => 'Custom sitemap entries with metadata',
        'resolve' => function () {
            $entries = [];
            $saved = get_option('pt_url_control_data', []);

            // Dynamically collect only enabled post types
            $post_types = array_keys(array_filter($saved, function ($settings) {
                return !empty($settings['sitemap']);
            }));

            if (empty($post_types))
                return [];

            $query = new WP_Query([
                'post_type' => $post_types,
                'post_status' => 'publish',
                'posts_per_page' => -1,
            ]);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $post = get_post();
                    $url = my_custom_frontend_view_url(get_permalink(), $post);

                    if (!empty($url)) {
                        $entries[] = [
                            'url' => esc_url($url),
                            'lastModified' => get_the_modified_date('c', $post),
                            'changeFrequency' => 'weekly',
                            'priority' => 0.8,
                        ];
                    }
                }
            }
            wp_reset_postdata();

            return $entries;
        }
    ]);

});
