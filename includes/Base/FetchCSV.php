<?php

namespace Includes\Base;
ini_set('memory_limit', '2048M'); 
class FetchCSV {

    public function create_file() {
        // FTP server details
        $ftp_server = "ftp.sanmar.com";
        $ftp_user = "161085";
        $ftp_pass = "Sanmar85";

        $remote_file = "SanMarPDD/SanMar_EPDD.csv";
        $local_file = "SanMar_EPDD.csv";
        $max_retries = 3; // Adjust retries as needed
        $retry_delay = 5; // Seconds to wait between retries

        // Attempt FTP connection with retries
        $ftp_conn = $this->ftp_connect_retry($ftp_server, $ftp_user, $ftp_pass, $max_retries, $retry_delay);

        if (!$ftp_conn) {
            error_log("Failed to connect to FTP server.");
            return false; // Handle FTP connection error
        }

        // Enable passive mode
        ftp_pasv($ftp_conn, true);
        ftp_set_option($ftp_conn, FTP_TIMEOUT_SEC, 99999999999999);

        // Set FTP transfer mode to binary
        if (!ftp_set_option($ftp_conn, FTP_BINARY, true)) {
            error_log("Failed to set FTP transfer mode to binary.");
            ftp_close($ftp_conn);
            return false; // Handle FTP transfer mode setting error
        }

        // Determine resume point for the local file
        $resume_position = 0;
        if (file_exists($local_file)) {
            $resume_position = filesize($local_file);
            $resume_position = max(0, $resume_position); // Ensure position is not negative
        }

        // Download the file from the FTP server with resume option
        $handle = fopen($local_file, 'a'); // Open for writing (append mode) to resume
        $remote_file_size = ftp_size($ftp_conn, $remote_file);
        $local_file_size = filesize($local_file);
        
        if (ftp_fget($ftp_conn, $handle, $remote_file, FTP_BINARY, $resume_position)) {
            echo "Successfully downloaded file";
            
            // // Successfully downloaded file, proceed with upload to WordPress
            if ($this->upload_to_wordpress($local_file)) {
                return true; // Return success
                fclose($handle);
            ftp_close($ftp_conn);
            } else {
                return false; // Handle WordPress upload error
            }
        } else {
            fclose($handle);
            ftp_close($ftp_conn);
            $error_message = "Failed to download CSV file from FTP: " . ftp_last_error($ftp_conn);
            error_log($error_message);
            return false; // Handle FTP file fetch error
        }
    }

    private function ftp_connect_retry($ftp_server, $ftp_user, $ftp_pass, $max_retries, $retry_delay) {
        $attempts = 0;
        while ($attempts < $max_retries) {
            $ftp_conn = ftp_connect($ftp_server);
            if ($ftp_conn && ftp_login($ftp_conn, $ftp_user, $ftp_pass)) {
                return $ftp_conn;
            }
            error_log("FTP connection attempt $attempts failed.");
            $attempts++;
            sleep($retry_delay); // Wait before retrying
        }
        error_log("All FTP connection attempts failed.");
        return false; // Return false if all attempts fail
    }

    private function upload_to_wordpress($local_file) {
        // Upload directory in WordPress
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['path'] . '/' . basename($local_file);

        // Move the downloaded file to the uploads directory
        if (!rename($local_file, $upload_path)) {
            error_log("Failed to move downloaded file to uploads directory.");
            return false; // Handle file move error
        }

        // Prepare attachment data
        $filetype = wp_check_filetype(basename($upload_path), null);
        $attachment = array(
            'guid'           => $upload_dir['url'] . '/' . basename($upload_path),
            'post_mime_type' => $filetype['type'],
            'post_title'     => sanitize_file_name(basename($upload_path)),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Insert attachment into media library
        $attach_id = wp_insert_attachment($attachment, $upload_path);
        if (is_wp_error($attach_id)) {
            error_log("Failed to insert attachment into media library.");
            return false; // Handle attachment insertion error
        }

        // Generate attachment metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $upload_path);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return true; // Return success
    }
}
