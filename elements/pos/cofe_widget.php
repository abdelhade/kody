<iframe
  id="cofe-pos-widget"
  src="https://withmoova.com/pos-widget"
  title="Cofe POS Widget"
  allowtransparency="true"
  style="
    position: fixed;
    top: 18px;
    right: 18px;
    width: 94px;
    height: 94px;
    border: 0;
    background: transparent;
    z-index: 999999;
    overflow: hidden;
  "
></iframe>

<script>
  (function () {
    const frame = document.getElementById('cofe-pos-widget');
    if (!frame) return;

    const COFE_ORIGIN  = 'https://withmoova.com';
    const DEVICE_TOKEN = '1f447185a42099b7acdc30c20ab7b1e0fba4e78c3ff8f950';
    const LOCALE       = 'ar';

    // -------------------------------------------------------
    // بيبعت الأوردر لـ PHP ويرجع النتيجة
    // -------------------------------------------------------
    async function createOrderInSupplierPos(payload) {
      console.log('[Cofe] ➡️ Sending order to POS:', payload);

      const response = await fetch('ajax/cofe_create_order.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Idempotency-Key': payload.idempotencyKey || ''
        },
        body: JSON.stringify(payload)
      });

      let result;
      try {
        result = await response.json();
      } catch (e) {
        throw new Error('استجابة غير صالحة من الخادم (not JSON)');
      }

      console.log('[Cofe] ✅ POS response:', result);

      if (!response.ok || !result.success) {
        const err = new Error(result.message || 'POS order creation failed');
        err.code = result.code || 'POS_CREATE_FAILED';
        throw err;
      }

      return result;
    }

    // -------------------------------------------------------
    // إرسال init للـ widget
    // -------------------------------------------------------
    function sendCofeInit() {
      if (!frame.contentWindow) return;
      console.log('[Cofe] 🔄 Sending cofe.init');
      frame.contentWindow.postMessage(
        { type: 'cofe.init', deviceToken: DEVICE_TOKEN, locale: LOCALE },
        COFE_ORIGIN
      );
    }

    frame.addEventListener('load', sendCofeInit);

    // -------------------------------------------------------
    // استقبال الرسائل من الـ widget
    // -------------------------------------------------------
    window.addEventListener('message', async function (event) {
      if (event.origin !== COFE_ORIGIN) return;
      if (event.source !== frame.contentWindow) return;

      const msgType = event.data?.type;
      console.log('[Cofe] 📩 Message received:', msgType, event.data);

      // طلب إعادة الـ init
      if (msgType === 'cofe.widget.request-init') {
        sendCofeInit();
        return;
      }

      // تغيير حجم الـ iframe
      if (msgType === 'cofe.widget.frame') {
        const requestedWidth  = Number(event.data.width)  || 94;
        const requestedHeight = Number(event.data.height) || 94;
        const needsPanelBuffer = requestedHeight > 94;

        const width  = Math.max(94, Math.min(window.innerWidth  - 36, requestedWidth));
        const height = Math.max(94, Math.min(window.innerHeight - 36, requestedHeight + (needsPanelBuffer ? 104 : 0)));

        frame.style.width  = width  + 'px';
        frame.style.height = height + 'px';

        if (height > 94) {
          frame.style.background = 'white';
        } else {
          frame.style.background = 'transparent';
        }
        return;
      }

      // تأكيد الأوردر — الحدث الرئيسي
      if (msgType !== 'cofe.order.confirmed') return;

      console.log('[Cofe] 🛒 Order confirmed — processing...');

      const data = event.data;

      const payload = {
        cofeOrderId:    data.cofeOrderId,
        idempotencyKey: data.idempotencyKey,
        branchId:       data.branchId,
        tableNumber:    data.tableNumber,
        items:          data.items
      };

      try {
        const supplierResult = await createOrderInSupplierPos(payload);

        console.log('[Cofe] 🎉 Order created successfully, notifying widget:', supplierResult);

        frame.contentWindow.postMessage(
          {
            type:                 'cofe.host.order-result',
            ok:                   true,
            draftId:              data.draftId,
            providerOrderId:      supplierResult?.providerOrderId      || supplierResult?.orderId   || null,
            providerReferenceId:  supplierResult?.providerReferenceId  || supplierResult?.referenceId || payload.idempotencyKey || null,
            providerStatus:       supplierResult?.providerStatus       || supplierResult?.status    || 'created',
            responsePayload:      supplierResult || null
          },
          COFE_ORIGIN
        );

      } catch (error) {
        console.error('[Cofe] ❌ Order creation failed:', error);

        frame.contentWindow.postMessage(
          {
            type:    'cofe.host.order-result',
            ok:      false,
            draftId: data.draftId,
            message: error?.message || 'POS order creation failed',
            retryable: true,
            errorPayload: {
              code:    error?.code || 'POS_CREATE_FAILED',
              payload: payload
            }
          },
          COFE_ORIGIN
        );
      }
    });

  })();
</script>
