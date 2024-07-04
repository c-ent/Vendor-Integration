<form id="filter_by_style" method="post" action="">
    <h1>Select Style</h1>
    <div>
        <button type="button" id="select-all">Select All</button>
        <button type="button" id="deselect-all">Deselect All</button>
    </div>
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
    <input type="submit" class="button button-primary" value="Next">
</form>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllButton = document.getElementById('select-all');
        const deselectAllButton = document.getElementById('deselect-all');
        const checkboxes = document.querySelectorAll('#style-buttons input[type="checkbox"]');

        selectAllButton.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });

        deselectAllButton.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        });
    });
</script>
