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
        
        include($getThisTemplates['admin/css.template']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $brands = $this->get_brands();
            include($getThisTemplates['admin/fetchcsv.template']);
            include($getThisTemplates['admin/getbybrands.template']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['create_file'])) {
                $this->handle_create_file();
            }
        
            if (isset($_POST['standardize_csv'])) {
                $this->handle_standardize_csv();
            }
    
            if (isset($_POST['filter_by_brand'])) {
                $this->process_brand_filter();
                $styleTags = $this->get_style_tags();
                include($getThisTemplates['admin/getbystyle#.template']);
            }
    
            if (isset($_POST['filter_by_style'])) {
                $this->process_style_filter();
                include($getThisTemplates['admin/standardizecsv.template']);
            }
        }
    
        ob_start();
        $output = ob_get_clean();
        echo $output;
    }
    

    private function handle_create_file() {
        if (!isset($_POST['create_file_nonce_field']) || !wp_verify_nonce($_POST['create_file_nonce_field'], 'create_file_nonce')) {
            die('Security check');
        }
    
        $create_file_instance = new \Includes\Base\FetchCSV();
        $attachment_id = $create_file_instance->create_file();
    
        if ($attachment_id) {
            echo '<div class="updated"><p>File created successfully! Attachment ID: ' . $attachment_id . '</p></div>';
        } else {
            echo '<div class="error"><p>Error creating file.</p></div>';
        }
    }
    
    private function handle_standardize_csv() {
        if (!isset($_POST['standardize_csv_nonce_field']) || !wp_verify_nonce($_POST['standardize_csv_nonce_field'], 'standardize_csv_nonce')) {
            die('Security check');
        }
    
        $standardizeCsv = new \Includes\Base\StandardizeCSV();
        $standardizeCsv->run();
    
        echo '<div class="updated"><p>CSV Standardization complete. The file has been processed successfully.</p></div>';
    }

    private function process_brand_filter() {
        $_SESSION['filter_by_brand_submitted'] = true;
    
        if (isset($_POST['brands']) && is_array($_POST['brands'])) {
            $selected_brands = array_map('trim', $_POST['brands']);
            print_r($selected_brands); // Debugging output
    
            $filtered_csv_data = $this->filter_csv_by_brands($selected_brands);
            $this->save_filtered_csv($filtered_csv_data, 'FilteredbyBrand.csv');
            echo 'Filtered CSV file created successfully: <a href="' . content_url('uploads/VendorIntegration/FilteredbyBrand.csv') . '">' . 'FilteredbyBrand.csv' . '</a>';
        } else {
            echo 'No brands selected.';
        }
    }
    
    private function filter_csv_by_brands($selected_brands) {
        $csv_file_path = ABSPATH . 'wp-content/uploads/2024/06/SanMar_EPDD.csv';
        $filtered_csv_data = [];
    
        if (($handle = fopen($csv_file_path, 'r')) !== FALSE) {
            $header = fgetcsv($handle);
            $brand_logo_index = array_search('BRAND_LOGO_IMAGE', $header);
    
            if ($brand_logo_index === FALSE) {
                die('Required BRAND_LOGO_IMAGE column is missing in the CSV file.');
            }
    
            $filtered_csv_data[] = $header;
    
            while (($row = fgetcsv($handle)) !== FALSE) {
                $brand_logo_image = $row[$brand_logo_index];
                if (!empty($brand_logo_image) && in_array($brand_logo_image, $selected_brands, true)) {
                    $filtered_csv_data[] = $row;
                }
            }
            fclose($handle);
        } else {
            die('Failed to open the CSV file.');
        }
    
        return $filtered_csv_data;
    }

    private function process_style_filter() {
        $_SESSION['filter_by_style_submitted'] = true;
    
        if (isset($_POST['styleTags']) && is_array($_POST['styleTags'])) {
            $selected_style_tags = array_map('trim', $_POST['styleTags']);
            print_r($selected_style_tags); // Debugging output
    
            $filtered_csv_data = $this->filter_csv_by_styles($selected_style_tags);
            $this->save_filtered_csv($filtered_csv_data, 'FilteredbyStyle.csv');
            echo 'Filtered CSV file created successfully: <a href="' . content_url('uploads/VendorIntegration/FilteredbyStyle.csv') . '">' . 'FilteredbyStyle.csv' . '</a>';
        } else {
            echo 'No style tags selected.';
        }
    }
    
    private function filter_csv_by_styles($selected_style_tags) {
        $csv_file_path = ABSPATH . 'wp-content/uploads/VendorIntegration/FilteredbyBrand.csv';
        $filtered_csv_data = [];
    
        if (($handle = fopen($csv_file_path, 'r')) !== FALSE) {
            $header = fgetcsv($handle);
            $style_tag_index = array_search('STYLE#', $header);
    
            if ($style_tag_index === FALSE) {
                die('Required STYLE# column is missing in the CSV file.');
            }
    
            $filtered_csv_data[] = $header;
    
            while (($row = fgetcsv($handle)) !== FALSE) {
                $style_tag = $row[$style_tag_index];
                if (!empty($style_tag) && in_array($style_tag, $selected_style_tags, true)) {
                    $filtered_csv_data[] = $row;
                }
            }
            fclose($handle);
        } else {
            die('Failed to open the CSV file.');
        }
    
        return $filtered_csv_data;
    }

    private function save_filtered_csv($filtered_csv_data, $filename) {
        $filtered_csv_file_path = ABSPATH . 'wp-content/uploads/VendorIntegration/' . $filename;
    
        $filtered_csv_dir_path = dirname($filtered_csv_file_path);
        if (!file_exists($filtered_csv_dir_path)) {
            mkdir($filtered_csv_dir_path, 0755, true);
        }
    
        if (($handle = fopen($filtered_csv_file_path, 'w')) !== FALSE) {
            foreach ($filtered_csv_data as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        } else {
            echo 'Failed to create the filtered CSV file.';
        }
    }

    private function get_brands() {
        $csvFile = ABSPATH . 'wp-content/uploads/2024/06/SanMar_EPDD.csv';
        $brands = [];
    
        if (($handle = fopen($csvFile, 'r')) !== false) {
            $header = fgetcsv($handle);
            $brandLogoImageIndex = array_search('BRAND_LOGO_IMAGE', $header);
    
            if ($brandLogoImageIndex === false) {
                die('BRAND_LOGO_IMAGE column not found.');
            }
    
            while (($data = fgetcsv($handle)) !== false) {
                $brandLogoImage = $data[$brandLogoImageIndex];
                if (!empty($brandLogoImage)) {
                    $brands[] = $brandLogoImage;
                }
            }
    
            fclose($handle);
            $brands = array_unique($brands);
        } else {
            die('Failed to open CSV file.');
        }
    
        return $brands;
    }
    
    private function get_style_tags() {
        $csvFile = ABSPATH . 'wp-content/uploads/VendorIntegration/FilteredbyBrand.csv';
        $styleTags = [];
    
        if (($handle = fopen($csvFile, 'r')) !== false) {
            $header = fgetcsv($handle);
            $styleTagIndex = array_search('STYLE#', $header);
    
            if ($styleTagIndex === false) {
                die('STYLE# column not found.');
            }
    
            while (($data = fgetcsv($handle)) !== false) {
                $styleTagData = $data[$styleTagIndex];
                if (!empty($styleTagData)) {
                    $styleTags[] = $styleTagData;
                }
            }
    
            fclose($handle);
            $styleTags = array_unique($styleTags);
        } else {
            die('Failed to open CSV file.');
        }
    
        return $styleTags;
    }
    
    public function save_setting() {
        
    }
    

}