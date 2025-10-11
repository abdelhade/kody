$(document).ready(function() {
    // Initialize Select2 for item selection
    $('#mySelectitm').select2({
        placeholder: "اختر صنف",
        ajax: {
            url: 'js/ajax/sales_myitems.php',
            dataType: 'json',
            delay: 25,
            data: function (params) {
                return { search: params.term }; // Search term
            },
            processResults: function (data) {
                return { results: data };
            },
            cache: true
        }
    });
 
    // Form submission with AJAX
    var form = document.getElementById('addItemForm');
    form.addEventListener('submit', function(event) {
        event.preventDefault(); 
        $('#addItemBtn').hide(); // Hide the button
        
        setTimeout(function() {
            $('#addItemBtn').show(); // Show the button after 2.5 seconds
        }, 2500);

        var formData = new FormData(form);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'js/ajax/doadd_item.php'); 

        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('msgitem').innerHTML = xhr.responseText; // Display response
                var $codenew = parseInt($('#code').val(), 10) + 1; 
                $('#code, #barcode, #unitCode').val($codenew);
                refreshSelect();
            } else {
                console.error('Error: ' + xhr.status);
            }
        };
        xhr.send(formData);
    });

    // Refresh the Select2 element
    function refreshSelect() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById('mySelectitm').innerHTML = xhr.responseText;
            }
        };
        xhr.open('GET', 'js/ajax/refresh_select.php', true); 
        xhr.send();
    }

    // Add new unit row
    $('#addUnit').click(function(){
        var clone = $('.urow').first().clone();
        clone.find('input[type="number"]').prop('readonly', false).val('6'); 
        $('.urow').last().after(clone); 
        clone.find('input[type="text"]').val('');
        
        clone.find('.deleteRow').click(function(){
            if ($('.urow').length > 1) {
                $(this).closest('.urow').remove(); 
            } else {
                alert('لا يمكن حذف الوحدة الأولي');
            }
        });
    });

    // Form validation for 'submit2' and 'submit' buttons
    $('#submit, #submit2').click(function(event) {
        var headtotalValue = parseFloat($('#headtotal').val());
        if (isNaN(headtotalValue) || headtotalValue == "0.00") {
            event.preventDefault(); 
            alert("يجب أن تكون قيمة الفاتورة أكبر من صفر ولا تكون فارغة أو غير صالحة");
        } else {
            $('#myForm').submit(); 
        }
    });

    function handleKeyboardShortcuts() {
        $(document).keydown(event => {
            if (event.key === 'F11') {
                event.preventDefault();
                $('#submit2').click();
            } else if (event.key === 'F12') {
                event.preventDefault();
                $('#submit').click();
            }
        });
    }
    
    handleKeyboardShortcuts();
    



    // Disable all submit buttons on click
    $('.submit').click(function() {
        $('.submit').attr('disabled', true);
    });
});



$(document).on('keydown', '.itmval', function(event) {
    if (event.key === "Enter") {
        event.preventDefault();  // منع السلوك الافتراضي
        $('#addRow').click();    // يحاكي النقر على زر "إضافة صف"
        
        // تأخير التركيز قليلاً للسماح بإضافة الصف قبل التركيز على حقل البحث
        setTimeout(function() {
            $('#mySelectitm').select2('open'); // فتح القائمة المنسدلة
            $('.select2-search__field').focus(); // ينقل التركيز إلى حقل البحث داخل القائمة
        }, 0); // التأخير هنا يمكن أن يكون 0 أو أي قيمة صغيرة
    }
});
row.find('select[name="u_val[]"]').on('change', function() {
    const selectedUnitValue = $(this).html();
    const selectedUnit = data.units.find(unit => unit.unit_name === selectedUnitValue);
    if (selectedUnit) {
        $('#price1').html(selectedUnit.uprice1);
    }
});


$(document).ready(function() {
    $('#showOps').click(function() {
        $('#operations').toggle(); // Toggle visibility
    });
});