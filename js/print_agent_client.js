/**
 * عميل وكيل الطباعة المحلي (Tauri على 127.0.0.1:17890)
 */
(function (window) {
  'use strict';

  var AGENT_URL = 'http://127.0.0.1:17890';
  var HEALTH_MS = 1200;

  function agentHealth() {
    return fetch(AGENT_URL + '/health', {
      method: 'GET',
      mode: 'cors',
      cache: 'no-store',
      signal: AbortSignal.timeout ? AbortSignal.timeout(HEALTH_MS) : undefined,
    })
      .then(function (r) {
        return r.ok ? r.json() : Promise.reject();
      })
      .then(function (j) {
        return !!(j && j.ok);
      })
      .catch(function () {
        return false;
      });
  }

  function fetchJobs(invoiceId, mode) {
    var url =
      'api/print_agent.php?action=jobs&id=' +
      encodeURIComponent(invoiceId) +
      '&mode=' +
      encodeURIComponent(mode || 'both');
    return fetch(url, {
      credentials: 'same-origin',
      headers: { Accept: 'application/json' },
    }).then(function (r) {
      return r.json();
    });
  }

  function sendJobs(jobs) {
    return fetch(AGENT_URL + '/print', {
      method: 'POST',
      mode: 'cors',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ jobs: jobs || [] }),
    }).then(function (r) {
      return r.json().then(function (j) {
        if (!r.ok || !j || !j.ok) {
          throw new Error((j && j.message) || 'فشل إرسال الطباعة للوكيل');
        }
        return j;
      });
    });
  }

  /**
   * يحاول الطباعة عبر الوكيل. لو الوكيل مش شغال يرجع false.
   * @returns {Promise<boolean>}
   */
  function printViaAgent(invoiceId, mode) {
    return agentHealth().then(function (ok) {
      if (!ok) return false;
      return fetchJobs(invoiceId, mode).then(function (data) {
        if (!data || !data.ok || !data.jobs || !data.jobs.length) {
          throw new Error((data && data.message) || 'لا توجد مهام طباعة');
        }
        // لو مفيش طابعة إضافية الوكيل بيتجاهل — لكن ما نبعتش مهام فاضية
        return sendJobs(data.jobs).then(function () {
          return true;
        });
      });
    });
  }

  /**
   * طباعة مع سقوط للمتصفح لو الوكيل مش متاح.
   * fallbackUrls: مصفوفة روابط نسبية تتفتح في تاب لو فشل الوكيل
   */
  function printOrFallback(invoiceId, mode, fallbackUrls) {
    return printViaAgent(invoiceId, mode)
      .then(function (used) {
        if (used) return true;
        (fallbackUrls || []).forEach(function (u, i) {
          if (i === 0) {
            window.open(u, '_blank');
          } else {
            setTimeout(function () {
              window.open(u, '_blank');
            }, 300 * i);
          }
        });
        return false;
      })
      .catch(function (err) {
        console.warn('print agent:', err);
        (fallbackUrls || []).forEach(function (u, i) {
          if (i === 0) window.open(u, '_blank');
          else
            setTimeout(function () {
              window.open(u, '_blank');
            }, 300 * i);
        });
        return false;
      });
  }

  window.KodyPrintAgent = {
    health: agentHealth,
    print: printViaAgent,
    printOrFallback: printOrFallback,
    AGENT_URL: AGENT_URL,
  };
})(window);
