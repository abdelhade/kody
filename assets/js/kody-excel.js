/**
 * Kody Excel Export Component (assets/js/kody-excel.js)
 * Comprehensive, reusable Excel export utility with full Arabic RTL support,
 * automatic action column exclusion, metadata headers, and styled column widths.
 *
 * Uses SheetJS (XLSX) already loaded in includes/footer.php.
 */

(function (window, document, $) {
  'use strict';

  var KodyExcel = {
    version: '1.0.0',

    // Default configuration
    defaults: {
      table: '#myTable',
      filename: 'تقرير_اكسل',
      title: '',
      company: '',
      subtitle: '',
      date: '',
      sheetName: 'بيانات',
      rtl: true,
      excludeSelector: '.no-export, .actions-col, .action-column, .k-table-actions, .k-actions-cell',
      excludeHeaderKeywords: [
        'العمليات', 'الإجراءات', 'الاجراءات', 'إجراء', 'عمليات', 'إجراءات',
        'actions', 'action', 'تحكم', 'خيارات', 'تعديل', 'حذف'
      ],
      includeFooter: true,
      includeHeaderMeta: true,
      autoWidth: true,
      notify: true
    },

    /**
     * Clean and parse cell value
     */
    cleanCellValue: function (cell) {
      if (!cell) return '';

      // If cell contains inputs or selects, get their values
      var select = cell.querySelector('select');
      if (select) {
        var opt = select.options[select.selectedIndex];
        return opt ? opt.text.trim() : '';
      }

      var input = cell.querySelector('input[type="text"], input[type="number"], input:not([type])');
      if (input) {
        return input.value.trim();
      }

      // Clone node to manipulate without modifying DOM
      var clone = cell.cloneNode(true);

      // Remove hidden elements, badges that are purely visual, and buttons
      var toRemove = clone.querySelectorAll('script, style, .no-export, .k-actions-cell, .dropdown-menu, button, a.btn');
      toRemove.forEach(function (el) {
        el.remove();
      });

      var text = clone.innerText || clone.textContent || '';
      text = text.replace(/\r\n|\r|\n/g, ' ').replace(/\s+/g, ' ').trim();

      // Check if text is a formatted number (e.g., "1,234.50" or " - 123.45 ")
      var cleanNum = text.replace(/,/g, '');
      if (/^-?\d+(\.\d+)?$/.test(cleanNum) && !/^0\d+/.test(cleanNum)) {
        var num = parseFloat(cleanNum);
        if (!isNaN(num)) {
          return num;
        }
      }

      return text;
    },

    /**
     * Check if a column should be excluded
     */
    isExcludedColumn: function (th, colIndex, table, options) {
      if (!th) return false;

      // Exclude by class
      if (th.matches && th.matches(options.excludeSelector)) return true;

      // Exclude by header keyword
      var thText = (th.innerText || th.textContent || '').trim().toLowerCase();
      for (var i = 0; i < options.excludeHeaderKeywords.length; i++) {
        if (thText === options.excludeHeaderKeywords[i].toLowerCase()) {
          return true;
        }
      }

      // Exclude if all body cells in this column contain only action buttons
      var sampleRows = table.querySelectorAll('tbody tr');
      if (sampleRows.length > 0) {
        var allAreActions = true;
        var checkedCount = 0;
        var maxCheck = Math.min(sampleRows.length, 5);

        for (var r = 0; r < maxCheck; r++) {
          var row = sampleRows[r];
          var cell = row.children[colIndex];
          if (!cell) continue;
          checkedCount++;

          if (cell.matches && cell.matches(options.excludeSelector)) {
            continue;
          }

          var buttons = cell.querySelectorAll('button, a.btn, .dropdown, i.fa-edit, i.fa-trash');
          var text = (cell.innerText || cell.textContent || '').trim();

          // If there is significant text and no buttons, it's not an action col
          if (buttons.length === 0 || text.length > 20) {
            allAreActions = false;
            break;
          }
        }

        if (checkedCount > 0 && allAreActions) {
          return true;
        }
      }

      return false;
    },

    /**
     * Export HTML table to XLSX
     */
    exportTable: function (tableTarget, customOptions) {
      if (typeof XLSX === 'undefined') {
        var errMsg = 'مكتبة SheetJS (XLSX) غير متوفرة. يرجى التأكد من تضمين js/sheetjs/excel.js';
        console.error(errMsg);
        if (window.KodyUI && window.KodyUI.toast) {
          window.KodyUI.toast('error', errMsg);
        } else {
          alert(errMsg);
        }
        return false;
      }

      var opts = Object.assign({}, this.defaults, customOptions || {});

      // Locate table
      var table = null;
      if (typeof tableTarget === 'string') {
        table = document.querySelector(tableTarget);
      } else if (tableTarget instanceof HTMLElement) {
        table = tableTarget;
      }

      if (!table) {
        console.error('KodyExcel: Table not found:', tableTarget);
        if (window.KodyUI && window.KodyUI.toast) {
          window.KodyUI.toast('warning', 'لم يتم العثور على الجدول المطلوب تصديره');
        }
        return false;
      }

      if (opts.notify && window.KodyUI && window.KodyUI.toast) {
        window.KodyUI.toast('info', 'جارِ تحضير وتصدير ملف الإكسل...');
      }

      // 1. Determine excluded columns
      var headerRow = table.querySelector('thead tr') || table.querySelector('tr');
      if (!headerRow) {
        console.error('KodyExcel: No rows found in table');
        return false;
      }

      var thCells = Array.from(headerRow.children);
      var excludedIndices = {};
      var colWidths = [];

      thCells.forEach(function (th, idx) {
        if (KodyExcel.isExcludedColumn(th, idx, table, opts)) {
          excludedIndices[idx] = true;
        }
      });

      // 2. Build rows data
      var aoaData = [];

      // Add Company & Title metadata if requested
      if (opts.includeHeaderMeta) {
        var companyName = opts.company;
        if (!companyName) {
          var compEl = document.querySelector('.k-brand-text, .company-name, .brand-link span, .navbar-brand');
          if (compEl) companyName = compEl.innerText.trim();
        }

        var reportTitle = opts.title;
        if (!reportTitle) {
          var titleEl = document.querySelector('.k-page-title, h1, .content-header h1, .card-title');
          if (titleEl) reportTitle = titleEl.innerText.trim();
        }

        var dateRange = opts.date;
        if (!dateRange) {
          var dateFrom = document.querySelector('input[name="from"], input[name="startdate"], input[name="date_from"]');
          var dateTo = document.querySelector('input[name="to"], input[name="enddate"], input[name="date_to"]');
          if (dateFrom && dateTo && dateFrom.value && dateTo.value) {
            dateRange = 'الفترة من: ' + dateFrom.value + ' إلى: ' + dateTo.value;
          } else {
            var now = new Date();
            dateRange = 'تاريخ التصدير: ' + now.getFullYear() + '-' +
              String(now.getMonth() + 1).padStart(2, '0') + '-' +
              String(now.getDate()).padStart(2, '0') + ' ' +
              String(now.getHours()).padStart(2, '0') + ':' +
              String(now.getMinutes()).padStart(2, '0');
          }
        }

        if (companyName) {
          aoaData.push([companyName]);
        }
        if (reportTitle) {
          aoaData.push([reportTitle]);
        }
        if (dateRange) {
          aoaData.push([dateRange]);
        }
        if (companyName || reportTitle || dateRange) {
          aoaData.push([]); // Empty spacing row
        }
      }

      // 3. Process Table Thead
      var headerRows = table.querySelectorAll('thead tr');
      if (headerRows.length === 0) {
        headerRows = [headerRow];
      }

      headerRows.forEach(function (hRow) {
        var hData = [];
        Array.from(hRow.children).forEach(function (cell, idx) {
          if (excludedIndices[idx]) return;
          var val = KodyExcel.cleanCellValue(cell);
          hData.push(val);
        });
        if (hData.length > 0) {
          aoaData.push(hData);
        }
      });

      // 4. Process Table Tbody
      var bodyRows = table.querySelectorAll('tbody tr');
      bodyRows.forEach(function (bRow) {
        // Skip hidden rows (like filtered out data in DataTables)
        if (bRow.style.display === 'none' && !bRow.classList.contains('dt-has-child')) return;

        var rData = [];
        Array.from(bRow.children).forEach(function (cell, idx) {
          if (excludedIndices[idx]) return;
          var val = KodyExcel.cleanCellValue(cell);
          rData.push(val);
        });

        if (rData.length > 0) {
          aoaData.push(rData);
        }
      });

      // 5. Process Table Tfoot
      if (opts.includeFooter) {
        var footRows = table.querySelectorAll('tfoot tr');
        footRows.forEach(function (fRow) {
          var fData = [];
          Array.from(fRow.children).forEach(function (cell, idx) {
            if (excludedIndices[idx]) return;
            var val = KodyExcel.cleanCellValue(cell);
            fData.push(val);
          });
          if (fData.length > 0) {
            aoaData.push(fData);
          }
        });
      }

      // 6. Create Sheet & Workbook
      var ws = XLSX.utils.aoa_to_sheet(aoaData);

      // Enable RTL (Right to Left) for Arabic
      if (opts.rtl) {
        ws['!views'] = [{ RTL: true }];
      }

      // Compute auto column widths
      if (opts.autoWidth) {
        var colMaxLengths = [];
        aoaData.forEach(function (row) {
          row.forEach(function (val, cIdx) {
            var strVal = (val !== null && val !== undefined) ? String(val) : '';
            // Arabic characters often need slightly wider display
            var len = strVal.length;
            if (/[\u0600-\u06FF]/.test(strVal)) {
              len = Math.ceil(len * 1.25);
            }
            colMaxLengths[cIdx] = Math.max(colMaxLengths[cIdx] || 10, len);
          });
        });

        ws['!cols'] = colMaxLengths.map(function (len) {
          return { wch: Math.min(Math.max(len + 3, 12), 45) };
        });
      }

      // Create workbook and append sheet
      var wb = XLSX.utils.book_new();
      var cleanSheetName = (opts.sheetName || 'بيانات').replace(/[\\/?*[\]:]/g, '_').substring(0, 31);
      XLSX.utils.book_append_sheet(wb, ws, cleanSheetName);

      // Set Workbook View to RTL
      if (opts.rtl) {
        if (!wb.Workbook) wb.Workbook = {};
        if (!wb.Workbook.Views) wb.Workbook.Views = [];
        wb.Workbook.Views[0] = { RTL: true };
      }

      // 7. Generate File & Trigger Download
      var finalFilename = opts.filename;
      if (!finalFilename.toLowerCase().endsWith('.xlsx')) {
        finalFilename += '.xlsx';
      }

      try {
        var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
        var blob = new Blob([wbout], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

        if (typeof window.navigator.msSaveBlob !== 'undefined') {
          window.navigator.msSaveBlob(blob, finalFilename);
        } else {
          var url = window.URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.style.display = 'none';
          a.href = url;
          a.download = finalFilename;
          document.body.appendChild(a);
          a.click();
          setTimeout(function () {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
          }, 200);
        }

        if (opts.notify && window.KodyUI && window.KodyUI.toast) {
          window.KodyUI.toast('success', 'تم تصدير ملف الإكسل بنجاح (' + finalFilename + ')');
        }

        return true;
      } catch (err) {
        console.error('KodyExcel: Export error:', err);
        if (window.KodyUI && window.KodyUI.toast) {
          window.KodyUI.toast('error', 'حدث خطأ أثناء تصدير ملف الإكسل: ' + err.message);
        }
        return false;
      }
    },

    /**
     * Export raw JavaScript array of objects or aoa
     */
    exportData: function (options) {
      if (typeof XLSX === 'undefined') {
        console.error('SheetJS is missing');
        return false;
      }

      var opts = Object.assign({}, this.defaults, options || {});
      var aoaData = [];

      // Add Company & Title metadata if requested
      if (opts.includeHeaderMeta) {
        if (opts.company) aoaData.push([opts.company]);
        if (opts.title) aoaData.push([opts.title]);
        if (opts.date) aoaData.push([opts.date]);
        if (opts.company || opts.title || opts.date) aoaData.push([]);
      }

      if (opts.headers && opts.headers.length > 0) {
        aoaData.push(opts.headers);
      }

      if (opts.rows && opts.rows.length > 0) {
        opts.rows.forEach(function (r) {
          aoaData.push(Array.isArray(r) ? r : Object.values(r));
        });
      }

      var ws = XLSX.utils.aoa_to_sheet(aoaData);
      if (opts.rtl) {
        ws['!views'] = [{ RTL: true }];
      }

      if (opts.autoWidth) {
        var colMaxLengths = [];
        aoaData.forEach(function (row) {
          row.forEach(function (val, cIdx) {
            var strVal = (val !== null && val !== undefined) ? String(val) : '';
            var len = strVal.length;
            if (/[\u0600-\u06FF]/.test(strVal)) len = Math.ceil(len * 1.25);
            colMaxLengths[cIdx] = Math.max(colMaxLengths[cIdx] || 10, len);
          });
        });
        ws['!cols'] = colMaxLengths.map(function (len) {
          return { wch: Math.min(Math.max(len + 3, 12), 45) };
        });
      }

      var wb = XLSX.utils.book_new();
      var cleanSheetName = (opts.sheetName || 'بيانات').replace(/[\\/?*[\]:]/g, '_').substring(0, 31);
      XLSX.utils.book_append_sheet(wb, ws, cleanSheetName);

      var finalFilename = opts.filename;
      if (!finalFilename.toLowerCase().endsWith('.xlsx')) finalFilename += '.xlsx';

      var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
      var blob = new Blob([wbout], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

      var url = window.URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.style.display = 'none';
      a.href = url;
      a.download = finalFilename;
      document.body.appendChild(a);
      a.click();
      setTimeout(function () {
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
      }, 200);

      if (opts.notify && window.KodyUI && window.KodyUI.toast) {
        window.KodyUI.toast('success', 'تم تصدير ملف الإكسل بنجاح');
      }

      return true;
    },

    /**
     * Automatic event delegation for any element with [data-kody-export]
     */
    init: function () {
      var self = this;

      $(document).on('click', '[data-kody-export]', function (e) {
        e.preventDefault();
        var $btn = $(this);

        // Disable button temporarily to prevent double click
        if ($btn.prop('disabled')) return;
        $btn.prop('disabled', true);
        var originalHtml = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> جارِ التصدير...');

        setTimeout(function () {
          var targetTable = $btn.data('table') || $btn.attr('data-table') || '#myTable';

          // If targetTable is a selector that doesn't exist, try nearest table
          var tableEl = document.querySelector(targetTable);
          if (!tableEl) {
            tableEl = $btn.closest('.card, .content-wrapper, body').find('table')[0] || document.querySelector('table');
          }

          var options = {
            filename: $btn.data('filename') || $btn.attr('data-filename') || (document.title ? document.title.replace(/[^\w\u0600-\u06FF-]/g, '_') : 'تقرير'),
            title: $btn.data('title') || $btn.attr('data-title') || '',
            company: $btn.data('company') || $btn.attr('data-company') || '',
            date: $btn.data('date') || $btn.attr('data-date') || '',
            sheetName: $btn.data('sheet') || $btn.attr('data-sheet') || 'البيانات',
            rtl: $btn.data('rtl') !== false && $btn.data('rtl') !== 'false'
          };

          self.exportTable(tableEl, options);

          // Restore button
          $btn.prop('disabled', false).html(originalHtml);
        }, 100);
      });
    }
  };

  // Expose to window
  window.KodyExcel = KodyExcel;

  // Initialize on DOM ready
  $(function () {
    KodyExcel.init();
  });

})(window, document, window.jQuery || window.$);
