$(document).ready(function() {

    // ── تعطيل السكرول والكيبورد على جميع حقول الأرقام في جدول الوحدات ──────
    $(document).on('wheel', '#unitsContainer input[type="number"]', function(e) {
        e.preventDefault();
    });
    $(document).on('keydown', '#unitsContainer input[type="number"]', function(e) {
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            e.preventDefault();
        }
    });
    // ─────────────────────────────────────────────────────────────────────────

    // Add new row
    $('#addUnit').click(function() {
        // Clone the first row
        var clone = $('.urow').first().clone();

        // Reset specific fields in the cloned row
        clone.find('input[name="u_val[]"]').val('6').prop('readonly', false); // Reset u_val
        clone.find('input[name="unit_barcode[]"]').val(''); // Clear barcode
        clone.find('input[name="unit_barcode[]"]').removeClass('is-valid is-invalid'); // Clear validation classes
        clone.find('.unit-barcode-error').addClass('d-none'); // Hide error span

        // Get the value of the 'u_val' field in the main row (the first row)
        var u_val_main = parseFloat($('.urow').first().find('input[name="u_val[]"]').val()) || 1;

        // Multiply the values in the cloned row by u_val of the first row
        clone.find('input[name="cost_price[]"]').val(function() {
            return (parseFloat($('.urow').first().find('input[name="cost_price[]"]').val()) * u_val_main).toFixed(3);
        });

        clone.find('input[name="price1[]"]').val(function() {
            return (parseFloat($('.urow').first().find('input[name="price1[]"]').val()) * u_val_main).toFixed(3);
        });

        clone.find('input[name="price2[]"]').val(function() {
            return (parseFloat($('.urow').first().find('input[name="price2[]"]').val()) * u_val_main).toFixed(3);
        });

        clone.find('input[name="market_price[]"]').val(function() {
            return (parseFloat($('.urow').first().find('input[name="market_price[]"]').val()) * u_val_main).toFixed(3);
        });

        // Append the cloned row after the last row
        $('.urow').last().after(clone);

        // Attach delete functionality to the newly added row
        clone.find('.deleteRow').click(function() {
            if ($('.urow').length > 1) clone.remove();
            else alert('لا يمكن حذف الوحدة الاولي');
        });
    });

    $('.deleteRow').click(function() {
        if ($('.urow').length > 1) $(this).closest('.urow').remove();
        else alert('لا يمكن حذف الوحدة الاولي');
    });

    // Real-time validations
    let validationErrors = {
        barcode: false,
        iname: false
    };

    let typingTimer;
    const doneTypingInterval = 500;

    function validateField(fieldId, fieldName) {
        const input = $('#' + fieldId);
        const value = input.val().trim();
        const excludeId = input.data('id') || 0;
        const errorSpan = $('#' + fieldId + 'Error');

        if (!value) {
            input.removeClass('is-invalid');
            errorSpan.addClass('d-none');
            validationErrors[fieldId] = false;
            return;
        }

        $.ajax({
            url: 'ajax/validate_item.php',
            type: 'POST',
            data: {
                field: fieldName,
                value: value,
                exclude_id: excludeId
            },
            dataType: 'json',
            success: function(response) {
                if (response.valid === false) {
                    input.addClass('is-invalid');
                    errorSpan.text(response.message).removeClass('d-none');
                    validationErrors[fieldId] = true;
                } else {
                    input.removeClass('is-invalid').addClass('is-valid');
                    errorSpan.addClass('d-none');
                    validationErrors[fieldId] = false;
                }
            }
        });
    }

    $('#barcode').on('keyup', function() {
        clearTimeout(typingTimer);
        const input = $(this);
        input.removeClass('is-valid is-invalid');
        typingTimer = setTimeout(() => validateField('barcode', 'barcode'), doneTypingInterval);
    });

    $('#iname').on('keyup', function() {
        clearTimeout(typingTimer);
        const input = $(this);
        input.removeClass('is-valid is-invalid');
        typingTimer = setTimeout(() => validateField('iname', 'iname'), doneTypingInterval);
    });

    $('#barcode, #iname').on('blur', function() {
        clearTimeout(typingTimer);
        validateField($(this).attr('id'), $(this).attr('name'));
    });

    function validateUnitBarcode(input) {
        const value = input.val().trim();
        const excludeId = input.data('id') || 0;
        const errorSpan = input.siblings('.unit-barcode-error');

        if (!value) {
            input.removeClass('is-invalid');
            errorSpan.addClass('d-none');
            input.data('invalid', false);
            return;
        }

        // تحقق من التكرار في نفس الشاشة (مقارنة مع الباركودات الأخرى)
        let localDuplicate = false;
        const mainBarcode = $('#barcode').val().trim();
        if (value === mainBarcode && $('.unit-barcode-input').index(input) !== 0) {
            localDuplicate = true; // لا يمكن لوحدة غير الأولى أن تأخذ نفس الباركود الرئيسي
        }

        $('.unit-barcode-input').not(input).each(function() {
            if ($(this).val().trim() === value) {
                localDuplicate = true;
            }
        });

        if (localDuplicate) {
            input.removeClass('is-valid').addClass('is-invalid');
            errorSpan.text('الباركود مكرر في نفس الصنف').removeClass('d-none');
            input.data('invalid', true);
            return;
        }

        $.ajax({
            url: 'ajax/validate_item.php',
            type: 'POST',
            data: {
                field: 'barcode',
                value: value,
                exclude_id: excludeId
            },
            dataType: 'json',
            success: function(response) {
                if (response.valid === false) {
                    input.removeClass('is-valid').addClass('is-invalid');
                    errorSpan.text(response.message).removeClass('d-none');
                    input.data('invalid', true);
                } else {
                    input.removeClass('is-invalid').addClass('is-valid');
                    errorSpan.addClass('d-none');
                    input.data('invalid', false);
                }
            }
        });
    }

    $(document).on('keyup', '.unit-barcode-input', function() {
        clearTimeout(typingTimer);
        const input = $(this);
        input.removeClass('is-valid is-invalid');
        typingTimer = setTimeout(() => validateUnitBarcode(input), doneTypingInterval);
    });

    $(document).on('blur', '.unit-barcode-input', function() {
        clearTimeout(typingTimer);
        validateUnitBarcode($(this));
    });

    $('#item-main-form').on('submit', function(e) {
        if (validationErrors.barcode) {
            e.preventDefault();
            $('#barcode').focus();
            alert('يرجى تصحيح خطأ الباركود قبل الحفظ');
            return false;
        }
        if (validationErrors.iname) {
            e.preventDefault();
            $('#iname').focus();
            alert('يرجى تصحيح خطأ اسم الصنف قبل الحفظ');
            return false;
        }
        
        let hasUnitError = false;
        $('.unit-barcode-input').each(function() {
            if ($(this).data('invalid')) {
                hasUnitError = true;
                $(this).focus();
            }
        });

        if (hasUnitError) {
            e.preventDefault();
            alert('يرجى تصحيح أخطاء باركود الوحدات قبل الحفظ');
            return false;
        }
    });

});
