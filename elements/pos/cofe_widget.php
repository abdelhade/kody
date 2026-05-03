<iframe
  id="cofe-pos-widget"
  src="https://withmoova.com/pos-widget"
  title="Cofe POS Widget"
  style="
    position: fixed;
    top: 0;
    right: 10px;
    width: 94px;
    height: 94px;
    border: 0;
    background: transparent;
    z-index: 999999;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  "
></iframe>

<script>
  (function () {
    const frame = document.getElementById('cofe-pos-widget');
    if (!frame) return;

    function sendCofeInit() {
      if (!frame.contentWindow) return;

      frame.contentWindow.postMessage(
        {
          type: 'cofe.init',
          deviceToken: '1f447185a42099b7acdc30c20ab7b1e0fba4e78c3ff8f950',
          locale: 'ar'
        },
        'https://withmoova.com'
      );
    }

    frame.addEventListener('load', sendCofeInit);

    window.addEventListener('message', function (event) {
      if (event.origin !== 'https://withmoova.com') return;
      if (event.source !== frame.contentWindow) return;

      if (event.data?.type === 'cofe.widget.request-init') {
        sendCofeInit();
        return;
      }

      if (event.data?.type === 'cofe.widget.frame') {
        const requestedWidth = Number(event.data.width) || 94;
        const requestedHeight = Number(event.data.height) || 94;
        const needsPanelBuffer = requestedHeight > 94;

        const width = Math.max(
          94,
          Math.min(window.innerWidth - 36, requestedWidth)
        );
        const height = Math.max(
          94,
          Math.min(window.innerHeight - 36, requestedHeight + (needsPanelBuffer ? 104 : 0))
        );

        frame.style.width = width + 'px';
        frame.style.height = height + 'px';
        
        // Change background to white when expanded (when height > 94)
        if (height > 94) {
          frame.style.background = 'white';
        } else {
          frame.style.background = 'transparent';
        }
      }
    });
  })();
</script>