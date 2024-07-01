<?php

namespace Includes\Base;

class StandardizeCSV {

    function handleCsvMapping($inputFile, $outputFile, $chunkSize = 100) {
        $outputCsv = fopen($outputFile, 'w'); // Create output file
        $inputCsv = fopen($inputFile, 'r'); // Read input file
    
        // Set headers for the output file
        $headers = [
            'ID',
            'SKU', 'Type', 'Name', 'Published', 'Is Featured?', 'Visibility in Catalog',
            'Short Description', 'Description', 'Date Sale Price Starts', 'Date Sale Price Ends',
            'Tax Status', 'Tax Class', 'In Stock?', 'Stock', 'Low stock amount', 'Backorders allowed?',
            'Sold individually?', 'Weight (kg)', 'Length (cm)', 'Width (cm)', 'Height (cm)',
            'Allow customer reviews?', 'Purchase note', 'Sale price', 'Regular price', 'Categories',
            'Tags', 'Shipping class', 'Images', 'Download limit', 'Download expiry days', 'Parent',
            'Grouped products', 'Upsells', 'Cross-sells', 'External URL', 'Button text', 'Position',
            'Attribute 1 name', 'Attribute 1 value(s)', 'Attribute 1 default', 'Attribute 1 visible', 'Attribute 1 global',
            'Attribute 2 name', 'Attribute 2 value(s)', 'Attribute 2 default', 'Attribute 2 visible', 'Attribute 2 global', 'STYLE#'
        ];
    
        fputcsv($outputCsv, $headers);
        $headers = fgetcsv($inputCsv); // Read the input file header
    
        $seenStyleIDs = [];
        $variableColors = [];
        $variableSizes = [];
        $variableIDs = [];
        $variableCategories = [];
    
        $allData = []; // Array to store all processed data
    
        // Process the input file in chunks
        while (!feof($inputCsv)) {
            $chunkData = [];
            for ($i = 0; $i < $chunkSize && !feof($inputCsv); $i++) {
                $row = fgetcsv($inputCsv);
                if ($row) {
                    $inputData = array_combine($headers, $row);
                    $chunkData[] = $inputData;
                }
            }
            $outputData = $this->processChunkData($chunkData, $seenStyleIDs, $variableColors, $variableSizes, $variableIDs, $variableCategories); // Process the chunk data
            $outputData = $this->cloneVariableToVariation($outputData); // Clone variable products to variations
    
            // Collect processed data from each chunk
            $allData = array_merge($allData, $outputData);
        }
    
        fclose($inputCsv);
    
        // Handle variable products after all data is processed
        $allData = $this->handleVariableProductRows($allData, $variableColors, $variableSizes, $variableCategories);
    
        // Write all processed data to the output CSV
        foreach ($allData as $row) {
            fputcsv($outputCsv, $row);
        }
    
        fclose($outputCsv);
    
        // echo "Transformation complete. Output saved to $outputFile.\n";
    }
    
    // Function to generate a unique ID
    function generateVariableId($row) {
        $uniqueKey = $row['UNIQUE_KEY'];
        $styleNumber = $row['STYLE#'];
    
        preg_match('/\d+/', $styleNumber, $matches);
        $styleNumberNumeric = isset($matches[0]) ? $matches[0] : '';
    
        return $uniqueKey . $styleNumberNumeric;
    }
    
    function setProductImages($inputData) {
        $urlsource =  preg_replace('/\/[^\/]*$/', '/', $inputData['FRONT_MODEL_IMAGE_URL']);
        $images = implode(', ', [
            !empty($inputData['FRONT_MODEL_IMAGE_URL']) ? $inputData['FRONT_MODEL_IMAGE_URL'] : '',
            !empty($inputData['BACK_MODEL_IMAGE']) ? $urlsource . $inputData['BACK_MODEL_IMAGE'] : '',
        ]);
    
        return $images;
    }
    
    function removeDuplicateRows($inputFile, $outputFile) {
        // Open input file for reading
        $inputHandle = fopen($inputFile, 'r');
        if (!$inputHandle) {
            die("Error: Unable to open input file '$inputFile'\n");
        }
    
        // Open output file for writing
        $outputHandle = fopen($outputFile, 'w');
        if (!$outputHandle) {
            fclose($inputHandle);
            die("Error: Unable to open output file '$outputFile'\n");
        }
    
        // Array to store seen IDs to detect duplicates
        $seenIds = [];
    
        // Read the header row
        $header = fgetcsv($inputHandle);
        if ($header === false) {
            fclose($inputHandle);
            fclose($outputHandle);
            die("Error: Input file '$inputFile' is empty or unable to read header.\n");
        }
    
        // Find the index of the 'SKU' column
        $SKUIndex = array_search('SKU', $header);
        if ($SKUIndex === false) {
            fclose($inputHandle);
            fclose($outputHandle);
            die("Error: 'SKU' column not found in the header of '$inputFile'.\n");
        }
    
        // Write the header row to the output file
        fputcsv($outputHandle, $header);
    
        // Array to store duplicate SKU's
        $duplicateIds = [];
    
        // Process the input file
        while (($row = fgetcsv($inputHandle)) !== false) {
            $SKU = $row[$SKUIndex]; // Access the 'ID' column by its index
    
            // Check if ID is not seen before
            if (!isset($seenIds[$SKU])) {
                $seenIds[$SKU] = true;
                fputcsv($outputHandle, $row);
            } else {
                // Store the duplicate ID
                $duplicateIds[] = $SKU;
            }
        }
    
        fclose($inputHandle);
        fclose($outputHandle);
    
        // Output the duplicate IDs
        // if (!empty($duplicateIds)) {
        //     echo "Duplicate IDs found:\n";
        //     foreach ($duplicateIds as $id) {
        //         echo "$id\n";
        //     }
        // } else {
        //     echo "No duplicate IDs found.\n";
        // }
    
        // echo "Duplicates removed based on ID column. Unique rows saved to '$outputFile'.\n";
    }
    
    function processChunkData($chunkData, &$seenStyleIDs, &$variableColors, &$variableSizes, &$variableIDs, &$variableCategories) {
        $outputData = [];
    
        foreach ($chunkData as $inputData) {
            $styleID = $inputData['STYLE#'];
            $color = $inputData['COLOR_NAME'];
            $size = $inputData['SIZE'];
            $images = $this->setProductImages($inputData);
            $type = in_array($styleID, $seenStyleIDs) ? 'variation' : 'variable';
    
            $this->setAttributesAndCategory($styleID, $color, $size, $inputData, $seenStyleIDs, $variableColors, $variableSizes, $variableCategories);
    
            $row = [
                'ID' => $inputData['UNIQUE_KEY'],
                'SKU' => $inputData['UNIQUE_KEY'],
                'Type' => $type,
                'Name' => $inputData['PRODUCT_TITLE'],
                'Published' => ($inputData['PRODUCT_STATUS'] == 'Discontinued') ? -1 : 1,
                'Is Featured?' => 0,
                'Visibility in Catalog' => 'visible',
                'Short Description' => '',
                'Description' => $inputData['PRODUCT_DESCRIPTION'],
                'Date Sale Price Starts' => '',
                'Date Sale Price Ends' => '',
                'Tax Status' => 'taxable',
                'Tax Class' => '',
                'In Stock?' => 1,
                'Stock' => $inputData['QTY'],
                'Low stock amount' => '',
                'Backorders allowed?' => '',
                'Sold individually?' => '',
                'Weight (kg)' => '',
                'Length (cm)' => '',
                'Width (cm)' => '',
                'Height (cm)' => '',
                'Allow customer reviews?' => 0,
                'Purchase note' => '',
                'Sale price' => '',
                'Regular price' => $inputData['SUGGESTED_PRICE'],
                'Categories' => $inputData['CATEGORY_NAME'],
                'Tags' => '',
                'Shipping class' => '',
                'Images' => $images,
                'Download limit' => 0,
                'Download expiry days' => '',
                'Parent' => $type === 'variable' ? '' : $variableIDs[$styleID] . 'A',
                'Grouped products' => '',
                'Upsells' => '',
                'Cross-sells' => '',
                'External URL' => '',
                'Button text' => '',
                'Position' => 3,
                'Attribute 1 name' => 'Color',
                'Attribute 1 value(s)' => $color,
                'Attribute 1 default' => $type === 'variable' ? $inputData['COLOR_NAME'] : '',
                'Attribute 1 visible' => 1,
                'Attribute 1 global' => '',
                'Attribute 2 name' => 'Sizes',
                'Attribute 2 value(s)' => $size,
                'Attribute 2 default' => $type === 'variable' ? $inputData['SIZE'] : '',
                'Attribute 2 visible' => 1,
                'Attribute 2 global' => '',
                'STYLE#' => $styleID,
                'UNIQUE_KEY' => $inputData['UNIQUE_KEY'],
            ];
    
            if ($type === 'variable') {
                $variableIDs[$styleID] = $inputData['UNIQUE_KEY']; // Store the ID for this variable product
            }
    
            $outputData[] = $row;
        }
    
        return $outputData;
    }
    
    function setAttributesAndCategory($styleID, $color, $size, $inputData, &$seenStyleIDs, &$variableColors, &$variableSizes, &$variableCategories) {
        if (!in_array($styleID, $seenStyleIDs)) {
            $seenStyleIDs[] = $styleID;
            $variableColors[$styleID] = [];
            $variableSizes[$styleID] = [];
            $variableCategories[$styleID] = [
                'CATEGORY_NAME' => $inputData['CATEGORY_NAME'],
                'SUBCATEGORY_NAME' => $inputData['SUBCATEGORY_NAME']
            ];
        }
    
        if (!empty($color) && !in_array($color, $variableColors[$styleID])) {
            $variableColors[$styleID][] = $color;
        }
    
        if (!empty($size) && !in_array($size, $variableSizes[$styleID])) {
            $variableSizes[$styleID][] = $size;
        }
    
        if (!empty($inputData['CATEGORY_NAME'])) {
            $categ = trim($inputData['CATEGORY_NAME']);
            $categ = preg_replace('/\s+/', ' ', $categ); // Normalize spaces
    
            if (!isset($variableCategories[$styleID]['CATEGORY_NAME'])) {
                $variableCategories[$styleID]['CATEGORY_NAME'] = $categ;
            } else {
                $existingSubcategories = explode(', ', $variableCategories[$styleID]['CATEGORY_NAME']);
                
                if (!in_array($categ, $existingSubcategories)) {
                    $variableCategories[$styleID]['CATEGORY_NAME'] .= ', ' . $categ;
                }
            }
        }
    
        if (!empty($inputData['SUBCATEGORY_NAME'])) {
            $subcategory = trim($inputData['SUBCATEGORY_NAME']);
            $subcategory = preg_replace('/\s+/', ' ', $subcategory); // Normalize spaces
            
            if (!isset($variableCategories[$styleID]['SUBCATEGORY_NAME'])) {
                $variableCategories[$styleID]['SUBCATEGORY_NAME'] = $subcategory;
            } else {
                $existingSubcategories = explode(', ', $variableCategories[$styleID]['SUBCATEGORY_NAME']);
                
                if (!in_array($subcategory, $existingSubcategories)) {
                    $variableCategories[$styleID]['SUBCATEGORY_NAME'] .= ', ' . $subcategory;
                }
            }
        }
    }
    
    function cloneVariableToVariation($outputData) {
        $newOutputData = [];
        foreach ($outputData as $row) {
            $newOutputData[] = $row; // Always add the original row
    
            if ($row['Type'] === 'variable') {
                $variationRow = $row;
                $variationRow['ID'] = $row['UNIQUE_KEY'];
                $variationRow['SKU'] = $row['SKU'];
                $variationRow['Type'] = 'variation';
                $variationRow['Parent'] = $row['SKU'] . 'A';
                $variationRow['Attribute 1 value(s)'] = $row['Attribute 1 value(s)'];
                $variationRow['Attribute 2 value(s)'] = $row['Attribute 2 value(s)'];
                $variationRow['Attribute 1 default'] = $row['Attribute 1 default'];
                $variationRow['Attribute 2 default'] = $row['Attribute 2 default'];
                $newOutputData[] = $variationRow;
            }
        }
    
        return $newOutputData;
    }
    
    function handleVariableProductRows($outputData, $variableColors, $variableSizes, $variableCategories) {
        foreach ($outputData as &$row) {
            if ($row['Type'] === 'variable') {
                $row['ID'] = $this->generateVariableId($row);
                $styleID = $row['STYLE#'];
                $row['SKU'] = $row['SKU'] . 'A';
                
                $category = isset($variableCategories[$styleID]) ? $variableCategories[$styleID]['CATEGORY_NAME'] : '';
                $subcategory = isset($variableCategories[$styleID]) ? $variableCategories[$styleID]['SUBCATEGORY_NAME'] : '';
    
                $categories = implode(', ', array_filter([$category, $subcategory]));
                $row['Categories'] = $categories;
                
                if (!empty($variableColors[$styleID])) {
                    $row['Attribute 1 value(s)'] = implode(', ', array_unique($variableColors[$styleID]));
                }
                if (!empty($variableSizes[$styleID])) {
                    $row['Attribute 2 value(s)'] = implode(', ', array_unique($variableSizes[$styleID]));
                }
            }
        }
    
        return $outputData;
    }
    
    function moveFileToWordPressUploads($filePath) {
        $wp_upload_dir = wp_upload_dir();
        $target_dir = $wp_upload_dir['path'];
        $target_file = $target_dir . '/' . basename($filePath);
    
        if (!rename($filePath, $target_file)) {
            echo "Failed to move $filePath to $target_file.\n";
        } else {
            // echo "File successfully moved to $target_file.\n";
        }
    }
    
    // Usage
    function run() {
        $inputFile = ABSPATH . 'wp-content/uploads/VendorIntegration/FilteredbyStyle.csv'; 
        $outputFile = 'FinalStandardize.csv';
    
        $this->handleCsvMapping($inputFile, 'temp.csv');
        $this->removeDuplicateRows('temp.csv', $outputFile);
    
        // Move file to WordPress uploads directory
        $this->moveFileToWordPressUploads($outputFile);
    
        // Register the file in the WordPress media library
        $uploads = wp_upload_dir();
        $outputFilePath = $uploads['path'] . '/' . $outputFile;
    
        $attachment = array(
            'guid'           => $uploads['url'] . '/' . $outputFile,
            'post_mime_type' => 'text/csv',
            'post_title'     => sanitize_file_name($outputFile),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );
    
        $attachmentId = wp_insert_attachment($attachment, $outputFilePath);
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachmentData = wp_generate_attachment_metadata($attachmentId, $outputFilePath);
        wp_update_attachment_metadata($attachmentId, $attachmentData);
    
        unlink('temp.csv');
    }
    
    
}


?>
