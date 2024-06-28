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

        add_submenu_page(
            'vendor_integration', // Parent menu slug (from above)
            'Subpage Title',     // Page title
            'Subpage Title',     // Menu title
            'manage_options',    // Capability required to access
            'vendor_integration_subpage', // Menu slug
            array($this, 'admin_subpage_callback') // Callback function
        );
    }

    public function admin_subpage_callback() {
        // Callback function for your subpage content
        echo '<div class="wrap">';
        echo '<h2>Subpage Title</h2>';
        // Add your subpage content here
        echo '</div>';

        
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


        // Check if form is submitted
        if (isset($_POST['standardize_csv'])) {
            // Optionally, verify nonce for security
            if (!isset($_POST['standardize_csv_nonce_field']) || !wp_verify_nonce($_POST['standardize_csv_nonce_field'], 'standardize_csv_nonce')) {
                die('Security check'); // Handle unauthorized access here
            }

             // Assuming your class instance is $standardizeCsv
                $standardizeCsv = new \Includes\Base\StandardizeCSV();
                $standardizeCsv->run();

                // Add success message
                echo '<div class="updated"><p>CSV Standardization complete. The file has been processed successfully.</p></div>';

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