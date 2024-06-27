<?php 
namespace Includes\Pages;

use \Includes\Base\BaseController;
// use GuzzleHttp\Client;
// use Psr\Http\Message\ResponseInterface;
// use GuzzleHttp\Exception\RequestException;
// use GuzzleHttp\Exception\ClientException;
// use GuzzleHttp\Psr7\Request;

class Admin extends BaseController {

    public function register() {
        add_action('admin_menu', array($this, 'add_admin_pages'));
        add_action('init', array($this, 'save_setting'));
    }

    public function add_admin_pages() {
        add_menu_page(
            'Vendor Integration', 
            'Vendor Integration', 
            'manage_options', 
            'vendor_integration', 
            array($this, 'admin_index'), 
            'dashicons-store', 
            110
        );
    }

    public function admin_index() {
        global $getThisTemplates;
    
        // Check if form is submitted
        if (isset($_POST['create_file'])) {
            // Optionally, verify nonce for security
            if (!isset($_POST['create_file_nonce_field']) || !wp_verify_nonce($_POST['create_file_nonce_field'], 'create_file_nonce')) {
                die('Security check'); // Handle unauthorized access here
            }
    
            // Call the file creation logic
            $create_file_instance = new \Includes\Base\FetchCSV();
            $attachment_id = $create_file_instance->create_file();
    
            if ($attachment_id) {
                echo '<div class="updated"><p>File created successfully! Attachment ID: ' . $attachment_id . '</p></div>';
            } else {
                echo '<div class="error"><p>Error creating file.</p></div>';
            }
        }
    
        // Display your admin page content
        ob_start();
        include($getThisTemplates['admin/admin.template']);
        $output = ob_get_clean();
    
        echo $output; 
    }
    
    
    
    public function save_setting() {
        
    }
    

}