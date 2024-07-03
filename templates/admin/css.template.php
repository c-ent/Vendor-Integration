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