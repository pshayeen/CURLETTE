document.addEventListener('DOMContentLoaded', function () {

    // delete confirmation modal, filled in per row when opened
    var deleteModalEl = document.getElementById('deleteProductModal');
    if (deleteModalEl) {
        deleteModalEl.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) return;

            var idField = document.getElementById('deleteProductIdField');
            var labelEl = document.getElementById('deleteProductLabel');

            if (idField) idField.value = trigger.getAttribute('data-id') || '';
            if (labelEl) labelEl.textContent = trigger.getAttribute('data-label') || "This can't be undone.";
        });
    }

});
