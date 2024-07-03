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
        <input type="submit" class="button button-primary" value="Next">
    </form>