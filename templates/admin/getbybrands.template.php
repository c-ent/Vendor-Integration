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
    <input type="hidden" name="filter_by_brand" value="1">

    <p class="select-buttons">
        <button type="button" id="select-all">Select All</button>
        <button type="button" id="deselect-all">Deselect All</button>
    </p>

    
    <p class="submit">
        <input type="submit" class="button button-primary" value="Next">
    </p>
   
</form>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const selectAllButton = document.getElementById('select-all');
        const deselectAllButton = document.getElementById('deselect-all');
        const checkboxes = document.querySelectorAll('#brand-buttons input[type="checkbox"]');

        selectAllButton.addEventListener('click', () => {
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });

        deselectAllButton.addEventListener('click', () => {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        });
    });
</script>
