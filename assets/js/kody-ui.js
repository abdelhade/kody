/**
 * Kody Unified Interactive UI Handler (kody-ui.js)
 * Provides SweetAlert2 confirmations, toasts, and standardized interactive behaviors.
 */

window.KodyUI = (function ($) {
  'use strict';

  // SweetAlert2 Toast configuration
  var Toast = (typeof Swal !== 'undefined') ? Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3500,
    timerProgressBar: true,
    onOpen: function (toast) {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    },
    didOpen: function (toast) {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
  }) : null;

  function toast(type, message) {
    if (Toast) {
      Toast.fire({
        type: type || 'success',
        icon: type || 'success',
        title: message
      });
    } else {
      console.log('[' + (type || 'info').toUpperCase() + '] ' + message);
    }
  }

  function confirmAction(options) {
    var defaults = {
      title: 'هل أنت متأكد؟',
      text: 'لا يمكن التراجع عن هذا الإجراء بعد تنفيذه.',
      icon: 'warning',
      type: 'warning',
      confirmButtonText: 'نعم، استمرار',
      cancelButtonText: 'إلغاء',
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#64748b',
      isDelete: true
    };

    var opts = $.extend({}, defaults, options);
    var alertType = opts.type || opts.icon || 'warning';

    if (typeof Swal === 'undefined') {
      if (window.confirm(opts.title + '\n' + opts.text)) {
        if (typeof opts.onConfirm === 'function') opts.onConfirm();
      }
      return;
    }

    Swal.fire({
      title: opts.title,
      text: opts.text,
      type: alertType,
      icon: alertType,
      showCancelButton: true,
      confirmButtonColor: opts.confirmButtonColor,
      cancelButtonColor: opts.cancelButtonColor,
      confirmButtonText: opts.confirmButtonText,
      cancelButtonText: opts.cancelButtonText,
      reverseButtons: true,
      customClass: {
        popup: 'k-swal-popup',
        confirmButton: 'k-btn k-btn-danger',
        cancelButton: 'k-btn k-btn-secondary'
      }
    }).then(function (result) {
      if (result.isConfirmed || result.value) {
        if (typeof opts.onConfirm === 'function') {
          opts.onConfirm();
        } else if (opts.url) {
          window.location.href = opts.url;
        }
      }
    });
  }

  // Initialize event delegations
  function init() {
    // 1. Unified Delete Confirmation delegation
    $(document).on('click', '[data-kody-delete]', function (e) {
      e.preventDefault();
      var $btn = $(this);
      var url = $btn.attr('href') || $btn.data('url');
      var itemName = $btn.data('item-name') || '';
      var title = itemName ? ('حذف ' + itemName + '؟') : 'تأكيد الحذف';
      var text = $btn.data('message') || 'هل أنت متأكد من رغبتك في حذف هذا العنصر نهائياً؟';

      confirmAction({
        title: title,
        text: text,
        confirmButtonText: 'نعم، حذف',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#ef4444',
        url: url
      });
    });

    // 2. Client-side Live Table Filter for inputs with data-kody-table-search
    $(document).on('input keyup', '[data-kody-table-search]', function () {
      var query = $(this).val().toLowerCase().trim();
      var targetTableSelector = $(this).data('kody-table-search');
      var $table = $(targetTableSelector);
      if (!$table.length) return;

      var $rows = $table.find('tbody tr:not(.k-empty-row)');
      if (!query) {
        $rows.show();
        return;
      }

      $rows.each(function () {
        var $row = $(this);
        var text = $row.text().toLowerCase();
        $row.toggle(text.indexOf(query) > -1);
      });
    });

    // 3. Auto flash messages from query parameters
    checkUrlFlash();
  }

  function checkUrlFlash() {
    var params = new URLSearchParams(window.location.search);
    if (params.get('msg') === 'saved' || params.get('saved') === '1' || params.get('add') === '1') {
      toast('success', 'تم حفظ البيانات بنجاح');
    } else if (params.get('msg') === 'deleted' || params.get('del') === '1') {
      toast('success', 'تم الحذف بنجاح');
    } else if (params.get('msg') === 'updated' || params.get('edit') === '1') {
      toast('success', 'تم تحديث البيانات بنجاح');
    } else if (params.get('w') === 'del') {
      toast('error', 'لا يمكن الحذف لوجود بيانات وسجلات مرتبطة بهذا العنصر');
    } else if (params.get('error')) {
      toast('error', params.get('error'));
    }
  }

  $(document).ready(init);

  return {
    toast: toast,
    confirm: confirmAction
  };
})(window.jQuery || window.$);
