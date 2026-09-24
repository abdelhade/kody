<?php if(!empty($success_message)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof resetDeliveryCustomerAfterOrder === 'function') {
            resetDeliveryCustomerAfterOrder();
        } else if (typeof clearDeliveryFieldsFromForm === 'function') {
            clearDeliveryFieldsFromForm();
        } else if (typeof clearDeliveryForm === 'function') {
            clearDeliveryForm();
        }

        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح!',
            text: '<?= htmlspecialchars($success_message) ?>',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    });
</script>
<?php endif; ?>
