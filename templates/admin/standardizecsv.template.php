<form id="standardize-csv-form" method="post">
        <input type="hidden" name="action" value="standardize_csv">
        <?php wp_nonce_field('standardize_csv_nonce', 'standardize_csv_nonce_field'); ?>
        <p class="submit">
            <input type="submit" name="standardize_csv" id="standardize_csv" class="button button-primary" value="Standardize CSV">
        </p>
    </form>
