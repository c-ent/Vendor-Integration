<style>

 #brand-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px; /* Space between items */
}

.brand-button {
    flex: 1 0 calc(25% - 10px); /* 4 columns, considering gap */
    box-sizing: border-box; /* Ensures padding and border are included in the width */
    display: flex;
    align-items: center;
}

#style-buttons {
    display: grid;
    grid-template-columns: repeat(4, 1fr); /* Creates 4 equal columns */
    gap: 10px; /* Space between items */
}

.style-button {
    display: flex;
    align-items: center;
}

</style>

    <form id="create-file-form" method="post">
        <input type="hidden" name="action" value="create_file">
        <?php wp_nonce_field('create_file_nonce', 'create_file_nonce_field'); ?>
        <p class="submit">
            <input type="submit" name="create_file" id="create_file" class="button button-primary" value="Fetch CSV">
        </p>
    </form>

    <form id="filter_by_brand" method="post" action="">
        <h1>Select Brands</h1>
        <div id="brand-buttons">
            <?php foreach ($brands as $brand): ?>
                <div class="brand-button">
                    <label>
                        <input type="checkbox" name="brands[]" value="<?php echo htmlspecialchars($brand); ?>">
                        <?php echo htmlspecialchars($brand); ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Hidden field to indicate form submission -->
        <input type="hidden" name="filter_by_brand" value="1">
        <input type="submit" value="Submit">
    </form>

    <form id="filter_by_style" method="post" action="">
        <h1>Select Style</h1>
        <div id="style-buttons">
            <?php foreach ($styleTags as $styleTag): ?>
                <div class="style-button">
                    <label>
                        <input type="checkbox" name="styleTags[]" value="<?php echo htmlspecialchars($styleTag); ?>">
                        <?php echo htmlspecialchars($styleTag); ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Hidden field to indicate form submission -->
        <input type="hidden" name="filter_by_style" value="1">
        <input type="submit" value="Submit">
    </form>
    
        
    <form id="standardize-csv-form" method="post">
        <input type="hidden" name="action" value="standardize_csv">
        <?php wp_nonce_field('standardize_csv_nonce', 'standardize_csv_nonce_field'); ?>
        <p class="submit">
            <input type="submit" name="standardize_csv" id="standardize_csv" class="button button-primary" value="Standardize CSV">
        </p>
    </form>





<!-- <form id="filter-csv" method="post">

</form> -->

<div id="loading-animation" style="display: none;">
    <p>Loading... Please wait.</p>
    <!-- You can include a spinner or any other loading animation here -->
</div>

<!-- <div class="csv-fetch-status">
    <?php if (get_option('vendor_integration_csv_fetch_success')) : ?>
        <p>CSV found!</p>
    <?php else : ?>
        <p>CSV file not fetched yet.</p>
    <?php endif; ?>
</div> -->


<script type="text/javascript">
    document.getElementById('create-file-form').onsubmit = function() {
        document.getElementById('loading-animation').style.display = 'block';
        document.getElementById('progress-status').style.display = 'block';
    }

            const brandCheckboxes = document.querySelectorAll('input[name="brands[]"]');
            const styleCheckboxes = document.querySelectorAll('input[name="styles[]"]');
            const selectedBrandsList = document.getElementById('selected-brands');
            const selectedStylesList = document.getElementById('selected-styles');

            function updateSelectedItems() {
                const selectedBrands = [];
                const selectedStyles = [];

                brandCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        selectedBrands.push(checkbox.value);
                    }
                });

                styleCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        selectedStyles.push(checkbox.value);
                    }
                });

                // Update the display of selected brands
                selectedBrandsList.innerHTML = selectedBrands.map(brand => `<li>${brand}</li>`).join('');

                // Update the display of selected styles
                selectedStylesList.innerHTML = selectedStyles.map(style => `<li>${style}</li>`).join('');
            }

            // Initial update on page load
            updateSelectedItems();

            // Update the displayed items when checkboxes change
            brandCheckboxes.forEach(checkbox => checkbox.addEventListener('change', updateSelectedItems));
            styleCheckboxes.forEach(checkbox => checkbox.addEventListener('change', updateSelectedItems));
        });
</script>
