<?php
/**
 * @package Create Test Blog
 * @version 1.0.0
 */

/*
Plugin Name: Create Test Blog
Plugin URI:
Description: Creates a Test Blog.
Author: Mark Pemburn
Version: 1.0.0
Author URI:
*/

class CreateBlog
{
    private static $instance = null;

    private function __construct()
    {
        $this->addActions();
    }

    public static function boot()
    {
        if (!self::$instance) {
            self::$instance = new CreateBlog();
        }

        return self::$instance;
    }

    protected function addActions(): void
    {
        add_action('network_admin_menu', [$this, 'addMenuPage']);
    }

    public function addMenuPage(): void
    {
        $hook = add_menu_page(
            __('Create Test Blog', 'uri'),
            'Create Test Blog',
            'switch_themes',
            'create-test-blog',
            [$this, 'showCreatePage'],
            'dashicons-admin-tools',
            90
        );

        add_action('load-' . $hook, [$this, 'addAdminAddOptions']);
    }

    public function addAdminAddOptions(): void
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Create',
        );

        add_screen_option($option, $args);
    }

    public function showCreatePage(): void
    {
        echo '<div style="margin: 5rem;">';
        if (! $_REQUEST['create']) {
            echo '<form method="POST" action="/wp-admin/network/admin.php?page=create-test-blog&create=true">';
            echo '<label for="title" style="font-weight: bolder;">Title:<br/>';
            echo '<input type="text" name="title" value="A Fabulous New Blog!"></input>';
            echo '</label><br/>';
            echo '<label for="title" style="font-weight: bolder;">Url:<br/>';
            echo '<input type="text" name="url" value="fabulous"></input>';
            echo '</label><br/><br/>';
            echo '<input type="submit" value="Create Blog" style="font-weight: bolder;"></input>';
            echo '</form>';
        } else {
            $this->create();
        }
        echo '</div>';
    }

    public function create()
    {
        echo "<h2>Creating a new subsite...</h2>";

        $blogId = wpmu_create_blog(
            $_SERVER['SERVER_NAME'],
            '/' . $_REQUEST['url'],
            $_REQUEST['title'],
            get_current_user_id(),
            ['public' => true]
        );

        if ($blogId) {
            switch_to_blog($blogId);
            $title = get_bloginfo('name');
            echo "<h2>Creating a new post on \"{$title}\"...</h2>";
            $this->createPost($title);
            echo '<h3><a href="/' . $_REQUEST['url'] . '">' . "Visit \"{$title}\"</a></h3>";
        }
    }

    protected function createPost(string $title)
    {
        $newPost = [
            'post_title' => 'A New Blog Post',
            'post_content' => "Welcome to \"{$title}\". This is my very first blog post.",
            'post_status' => 'publish',
            'post_author' => 1,
            'post_category' => [8, 39]
        ];

        wp_insert_post($newPost);
    }
}

CreateBlog::boot();
