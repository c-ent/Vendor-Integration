
<form id="create-file-form" method="post">
        <input type="hidden" name="action" value="create_file">
        <?php wp_nonce_field('create_file_nonce', 'create_file_nonce_field'); ?>
        <p class="submit">
            <input type="submit" name="create_file" id="create_file" class="button button-primary" value="Fetch CSV">
        </p>
    </form>

    
<div id="loading-animation" style="display: none;">
    <p>Loading... Please wait.</p>
    <!-- You can include a spinner or any other loading animation here -->
</div>


<script type="text/javascript">
    document.getElementById('create-file-form').onsubmit = function() {
        document.getElementById('loading-animation').style.display = 'block';
        document.getElementById('progress-status').style.display = 'block';
    }
</script>