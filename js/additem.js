$(document).ready(function() {

    // ── Select2 للمجموعات ───────────────────────────────────────────────────
    if ($.fn.select2) {
        $('.select2-item').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: '— اختر —',
            allowClear: true
        });
    }

    // ── Custom file labels + معاينة الصورة ───────────────────────────────────
    $('#imgs').on('change', function() {
        if (!this.files || !this.files[0]) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            var $img = $('#itemPreviewImg');
            var $placeholder = $('#itemImagePlaceholder');
            $img.attr('src', e.target.result).removeClass('d-none').show();
            $placeholder.addClass('d-none').hide();
            $('#itemImagePanel').addClass('has-image');
        };
        reader.readAsDataURL(this.files[0]);
    });

    $('.custom-file-input').on('change', function() {
        if (this.id === 'imgs') return;
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName || 'اختر ملفاً');
    });

    $('#importItemsModal').on('hidden.bs.modal', function() {
        var $form = $('#import-items-form');
        if ($form.length) {
            $form[0].reset();
            $form.find('.custom-file-label').removeClass('selected').html('اختر ملف');
        }
    });

    // ── حساب الأسعار حسب المعامل ─────────────────────────────────────────────
    var priceFields = ['cost_price', 'price1', 'price2', 'market_price'];

    function itemBaseRow() {
        var $base = $('.urow-base');
        return $base.length ? $base.first() : $('.urow').first();
    }

    function nextUnitFactor() {
        var used = {};
        $('input[name="u_val[]"]').each(function() {
            var coeff = parseFloat($(this).val());
            if (isFinite(coeff)) used[coeff.toFixed(3)] = true;
        });
        var factor = 6;
        while (used[factor.toFixed(3)]) factor += 1;
        return factor;
    }

    priceFields.forEach(function(fieldName) {
        $(document).on('input', '.urow-base input[name="' + fieldName + '[]"]', function() {
            var firstRowValue = parseFloat($(this).val()) || 0;
            $('.urow').not('.urow-base').each(function() {
                var u_val = parseFloat($(this).find('input[name="u_val[]"]').val()) || 1;
                $(this).find('input[name="' + fieldName + '[]"]').val((firstRowValue * u_val).toFixed(3));
            });
        });
    });

    $(document).on('input', 'input[name="u_val[]"]', function() {
        var currentRow = $(this).closest('.urow');
        if (currentRow.hasClass('urow-base')) return;
        var u_val = parseFloat($(this).val()) || 1;
        priceFields.forEach(function(fieldName) {
            var firstRowValue = parseFloat(itemBaseRow().find('input[name="' + fieldName + '[]"]').val()) || 0;
            currentRow.find('input[name="' + fieldName + '[]"]').val((firstRowValue * u_val).toFixed(3));
        });
    });

    // ── التحقق من الوحدات عند الحفظ ─────────────────────────────────────────
    $('#item-main-form').on('submit', function(e) {
        var selectedValues = [];
        var duplicateFound = false;
        var validUnitFound = false;
        $('select[name="unit_id[]"]').each(function() {
            var val = $(this).val();
            if (val) {
                validUnitFound = true;
                if (selectedValues.indexOf(val) !== -1) duplicateFound = true;
                selectedValues.push(val);
            }
        });
        if ($('.urow').length === 0 || !validUnitFound) {
            e.preventDefault();
            alert('لا يمكن حفظ صنف بدون وحدات');
            return;
        }
        if (duplicateFound) {
            e.preventDefault();
            alert('غير مسموح بتكرار الوحدات');
            return;
        }
        var coeffValues = [];
        var duplicateCoeff = false;
        $('input[name="u_val[]"]').each(function() {
            var coeff = parseFloat($(this).val());
            if (!isFinite(coeff) || coeff <= 0) {
                duplicateCoeff = true;
                return;
            }
            var key = coeff.toFixed(3);
            if (coeffValues.indexOf(key) !== -1) duplicateCoeff = true;
            coeffValues.push(key);
        });
        if (duplicateCoeff) {
            e.preventDefault();
            alert('غير مسموح بتكرار معامل الوحدة، ويجب أن يكون أكبر من صفر');
            return;
        }
        var $base = itemBaseRow();
        if (!$base.length || $('.urow-base').length !== 1) {
            e.preventDefault();
            alert('يجب أن يحتوي الصنف على وحدة أساسية واحدة بمعامل 1');
            return;
        }
        $base.find('input[name="u_val[]"]').val('1');
        var $baseBarcode = $base.find('.unit-barcode-input');
        if (!$baseBarcode.val().trim()) {
            $baseBarcode.val($('#barcode').val().trim());
        }
    });

    // ── منع Enter من إرسال النموذج ───────────────────────────────────────────
    $('#item-main-form').on('keydown', function(e) {
        if (e.key !== 'Enter') return;
        var $target = $(e.target);
        if ($target.is('textarea, button, [type="submit"]')) return;
        e.preventDefault();
    });

    // ── اختصار F2 للحفظ ─────────────────────────────────────────────────────
    $(document).on('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            $('#item-main-form').trigger('submit');
        }
    });

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
        var factor = nextUnitFactor();
        var usedUnits = {};
        $('select[name="unit_id[]"]').each(function() {
            usedUnits[$(this).val()] = true;
        });

        var clone = itemBaseRow().clone();
        clone.removeClass('urow-base');
        clone.find('input[name="iu_id[]"]').val('0');
        clone.find('input[name="u_val[]"]').val(String(factor)).prop('readonly', false);
        clone.find('input[name="unit_barcode[]"]').val('').removeClass('is-valid is-invalid').removeData('invalid');
        clone.find('.unit-barcode-error').addClass('d-none');

        var $unitSelect = clone.find('select[name="unit_id[]"]');
        $unitSelect.find('option').each(function() {
            if (!usedUnits[$(this).val()]) {
                $unitSelect.val($(this).val());
                return false;
            }
        });

        priceFields.forEach(function(fieldName) {
            var base = parseFloat(itemBaseRow().find('input[name="' + fieldName + '[]"]').val()) || 0;
            clone.find('input[name="' + fieldName + '[]"]').val((base * factor).toFixed(3));
        });

        $('.urow').last().after(clone);
    });

    $(document).on('click', '.deleteRow', function() {
        var $row = $(this).closest('.urow');
        if ($row.hasClass('urow-base') || $('.urow').length <= 1) {
            alert('لا يمكن حذف الوحدة الأساسية');
            return;
        }
        $row.remove();
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

    itemBaseRow().find('.unit-barcode-input').data('linked', ($('#barcode').val() || '').trim());

    function followItemBarcode(next) {
        var $input = itemBaseRow().find('.unit-barcode-input');
        var linked = $input.data('linked');
        var current = ($input.val() || '').trim();
        if (current === '' || current === linked) {
            $input.val(next);
        }
        $input.data('linked', next);
    }

    $('#barcode').on('input', function() {
        followItemBarcode(($(this).val() || '').trim());
    });

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
        const isBase = input.closest('.urow').hasClass('urow-base');
        if (value === mainBarcode && !isBase) {
            localDuplicate = true;
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
        var seenBarcodes = {};
        var mainBarcode = ($('#barcode').val() || '').trim();
        $('.unit-barcode-input').each(function() {
            var value = ($(this).val() || '').trim();
            var isBase = $(this).closest('.urow').hasClass('urow-base');
            if (value && ((!isBase && value === mainBarcode) || seenBarcodes[value])) {
                $(this).addClass('is-invalid').data('invalid', true);
                $(this).siblings('.unit-barcode-error').text('الباركود مكرر في نفس الصنف').removeClass('d-none');
            }
            if (value) seenBarcodes[value] = true;
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
