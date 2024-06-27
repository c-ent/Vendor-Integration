<form id="create-file-form" method="post">
    <input type="hidden" name="action" value="create_file">
    <?php wp_nonce_field('create_file_nonce', 'create_file_nonce_field'); ?>
    <p class="submit">
        <input type="submit" name="create_file" id="create_file" class="button button-primary" value="Create File">
    </p>
</form>

<div id="loading-animation" style="display: none;">
    <p>Loading... Please wait.</p>
    <!-- You can include a spinner or any other loading animation here -->
</div>

<div class="csv-fetch-status">
    <?php if (get_option('vendor_integration_csv_fetch_success')) : ?>
        <p>CSV found!</p>
    <?php else : ?>
        <p>CSV file not fetched yet.</p>
    <?php endif; ?>
</div>

<div id="progress-status" style="display: none;">
    <p>Download Progress: <span id="progress-percent">0</span>%</p>
</div>

<script type="text/javascript">
    document.getElementById('create-file-form').onsubmit = function() {
        document.getElementById('loading-animation').style.display = 'block';
        document.getElementById('progress-status').style.display = 'block';
    }
</script>
