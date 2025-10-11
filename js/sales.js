let counter = 1; // تعريف عداد الصفوف

        $(document).ready(function() {
            initializeSelect2();
            handleItemSelectionChange(); // حدث تغيير اختيار العنصر
            handleRowAddition(); // إضافة صف جديد
            handleInputChanges(); // التعامل مع تغييرات المدخلات
            handleRowDeletion(); // حذف الصفوف
            handleFormSubmission(); // تقديم النموذج
            handleKeyboardShortcuts(); // اختصارات لوحة المفاتيح

            // نقل التركيز عند الضغط على "Enter"
            $(document).on('keydown', 'input, select', function(event) {
                if (event.key === "Enter") {
                    event.preventDefault(); // منع السلوك الافتراضي
                    let nextElement = $(this).closest('td').next().find('input, select');
                    if (nextElement.length) {
                        nextElement.focus();
                    } else {
                        let nextRow = $(this).closest('tr').next();
                        if (nextRow.length) {
                            nextRow.find('input, select').first().focus();
                        }
                    }
                }
            });
        });

        function initializeSelect2() {
            $('#mySelectitm').select2({
                placeholder: "اختر صنف",
                ajax: {
                    url: 'js/ajax/sales_myitems.php',
                    dataType: 'json',
                    delay: 25,
                    data: (params) => ({ search: params.term }),
                    processResults: (data) => ({ results: data }),
                    cache: true
                }
            });
        }

        function handleItemSelectionChange() {
            $('select.mySelectitm').on('change', function() {
                const row = $(this).closest('tr');
                fetchItemInfo($(this).val(), row);
            });
        }

        function fetchItemInfo(itemId, row) {
            $.ajax({
                url: 'get/get_iteminfo.php?id=' + itemId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    const isSale = getParameterByName('q') === 'sale';
                    row.find("#itmprice").val(isSale ? data.last_price : data.price1);
                    row.find("#itmval").val(isSale ? data.last_price : data.price1);
                    row.find("#itmqty").val(1).dblclick();
                    $('#storeqty').html(data.itmqty.toFixed(2));
                    $('#price1').html(data.price1);
                    $('#market_price').html(data.market_price);
                    $('#storemdtime').html(data.mdtime);
                    $('#cost_price').html(data.cost_price);
                    $('#last_price').html(data.last_price);
                    
                    const unitSelect = row.find('select[name="u_val[]"]');
                    unitSelect.empty();
                    data.units.forEach(unit => {
                        unitSelect.append(new Option(unit.unit_name, unit.unit_value));
                    });
        
                    // Unit change event
                    unitSelect.on('change', function() {
                        const selectedUnitValue = $(this).val();
                        const selectedUnit = data.units.find(unit => unit.unit_value === selectedUnitValue);
                        if (selectedUnit) {
                            row.find("#itmprice").val(isSale ? data.last_price * selectedUnit.unit_value: selectedUnit.uprice1);
                            row.find("#itmqty").val(1).dblclick();
                            $('#storeqty').html((data.itmqty/selectedUnit.unit_value).toFixed(2) + " (" + selectedUnit.unit_value + ")");
                            $('#price1').html(selectedUnit.uprice1);
                            $('#market_price').html(selectedUnit.uprice3);
                            $('#cost_price').html(data.cost_price*selectedUnit.unit_value);
                            $('#last_price').html(data.last_price*selectedUnit.unit_value);
                            $('#price1').html(selectedUnit.uprice1);
                            calculateItemValue(row);
                            updateTotal();
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("خطأ في استدعاء البيانات:", error);
                }
            });
        }
        
        function handleRowAddition() {
            $("#addRow").on("click", function() {
                const itemId = $("#mySelectitm").val();
                if (itemId) {
                    addNewRow(itemId);
                } else {
                    alert("يرجى اختيار صنف.");
                }
            });
        }

        function addNewRow(itemId) {
            const newRow = $("#searchTable tbody tr").first().clone();
            newRow.find("td:first").text(counter++);
            newRow.find("td:nth-child(2)").text($("#mySelectitm option:selected").text());
            newRow.find("input[name='itmname[]']").val(itemId);
            const selectedUnit = $("select[name='u_val[]']").first().val();
            newRow.find("select[name='u_val[]']").val(selectedUnit);
            $('#searchTable tbody tr').last().find(".itmqty").val(1.00);    // تعيين الكمية إلى 1.00 في آخر صف
            $('#searchTable tbody tr').last().find(".itmprice").val(0.00);  // تعيين السعر إلى 0.00
            $('#searchTable tbody tr').last().find(".itmdisc").val(0.00);   // تعيين الخصم إلى 0.00
            $('#searchTable tbody tr').last().find(".itmval").val(0.00);    // تعيين القيمة إلى 0.00            
            newRow.appendTo("#itmrow");
            newRow.find("td:last").html('<button class="deleteRow btn btn-danger">X</button>');
            $('#itmTd').focus();
            updateTotal();
        }

        function handleInputChanges() {
            $(document).on('input', 'input', function() {
                const row = $(this).closest('tr');
                calculateItemValue(row);
                updateTotal();
            });
        }

        function calculateItemValue(row) {
            const itmQty = parseFloat(row.find('.itmqty').val()) || 0;
            const itmPrice = parseFloat(row.find('.itmprice').val()) || 0;
            const itmDisc = parseFloat(row.find('.itmdisc').val()) || 0;
            const itmVal = (itmQty * itmPrice) - itmDisc;
            row.find('.itmval').val(itmVal.toFixed(2) || '');
        }

        function handleRowDeletion() {
            $("#itmrow").on("click", ".deleteRow", function() {
                $(this).closest("tr").remove();
                updateTotal();
            });
        }

        function handleFormSubmission() {
            $('#addItemForm').on('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(this);
                $.ajax({
                    url: 'js/ajax/doadd_item.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#msgitem').html(response);
                        refreshSelect();
                    },
                    error: function(xhr) {
                        console.error('خطأ:', xhr.status);
                    }
                });
            });
        }

        function refreshSelect() {
            $.get('js/ajax/refresh_select.php', function(data) {
                $('#mySelectitm').html(data);
            });
        }

        function getParameterByName(name, url = window.location.href) {
            const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
            const results = regex.exec(url);
            return results ? (results[2] ? decodeURIComponent(results[2].replace(/\+/g, ' ')) : '') : null;
        }

        function updateTotal() {
            let total = 0;
            $('#itmrow .itmval').each(function() {
                const val = parseFloat($(this).val()) || 0;
                total += val;
            });
            $('#headtotal').val(total.toFixed(2));
            const headtotal = parseFloat($("#headtotal").val()) || 0;
            const headdisc = parseFloat($("#headdisc").val()) || 0;
            const headplus = parseFloat($("#headplus").val()) || 0;
            const headnet = headtotal - headdisc + headplus;
            $("#headnet").val(headnet);
            $("#change").val(headnet - parseFloat($("#paid").val()) || 0);
        }



        $(document).on('keydown', 'input, select', function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                let nextElement = $(this).closest('td').next().find('input, select');
                nextElement.length && nextElement.focus() && nextElement.attr('name') === 'u_val' && nextElement.select2('open');
            }
        });
        