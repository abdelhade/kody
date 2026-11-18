/**
 * عميل وكيل الطباعة المحلي (Tauri على 127.0.0.1:17890)
 *
 * التدفق (طباعة مباشرة):
 * 1) كودي يحفظ الأوردر ويفضل على شاشة الـ POS
 * 2) المتصفح يبعت مهام الطباعة للوكيل
 * 3) Toast: جاري الطباعة
 * 4) الوكيل يطبّع في الخلفية بمقاس حراري
 * 5) Toast: نجحت / فشلت
 */
(function (window) {
  'use strict';

  var AGENT_URL = 'http://127.0.0.1:17890';
  var HEALTH_MS = 1200;
  var POLL_MS = 500;
  var POLL_MAX_MS = 60000;

  function appRoot() {
    if (window.KODY_APP_ROOT) {
      return String(window.KODY_APP_ROOT).replace(/\/+$/, '');
    }
    var path = window.location.pathname || '/';
    path = path.replace(/\/+$/, '');
    path = path.replace(/\/(print|do|ajax|api|get)(\/.*)?$/i, '');
    path = path.replace(/\/[^/]+\.php$/i, '');
    return path || '';
  }

  function appUrl(rel) {
    rel = String(rel || '').replace(/^\//, '');
    return appRoot() + '/' + rel;
  }

  function toast(icon, title, timer) {
    if (window.Swal && typeof window.Swal.fire === 'function') {
      window.Swal.fire({
        toast: true,
        position: 'top',
        icon: icon || 'info',
        title: title || '',
        timer: typeof timer === 'number' ? timer : 1800,
        showConfirmButton: false,
      });
      return;
    }
    if (title) console.log('[print]', title);
  }

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
      appUrl('api/print_agent.php') +
      '?action=jobs&id=' +
      encodeURIComponent(invoiceId) +
      '&mode=' +
      encodeURIComponent(mode || 'both');
    return fetch(url, {
      credentials: 'same-origin',
      headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    }).then(function (r) {
      return r.text().then(function (text) {
        var trimmed = (text || '').trim();
        try {
          return JSON.parse(trimmed);
        } catch (e) {
          var hint = trimmed.slice(0, 80).replace(/\s+/g, ' ');
          throw new Error(
            'رد غير صالح من خادم الطباعة' +
              (hint ? ' (' + hint + ')' : '') +
              ' — ' +
              url
          );
        }
      });
    });
  }

  function sendJobs(jobs) {
    return fetch(AGENT_URL + '/print', {
      method: 'POST',
      mode: 'cors',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        jobs: jobs || [],
        // جلسة كودي من المتصفح — الوكيل بيبعتها مع فتح صفحة الطباعة
        cookies: typeof document !== 'undefined' ? document.cookie || '' : '',
      }),
    }).then(function (r) {
      return r.text().then(function (text) {
        var j = null;
        try {
          j = JSON.parse((text || '').trim());
        } catch (e) {
          throw new Error('رد غير صالح من وكيل الطباعة المحلي');
        }
        if (!r.ok || !j || !j.ok) {
          throw new Error((j && j.message) || 'فشل إرسال الطباعة للوكيل');
        }
        return j;
      });
    });
  }

  function pollJob(jobId) {
    var started = Date.now();
    function once() {
      return fetch(AGENT_URL + '/print/status?id=' + encodeURIComponent(jobId), {
        method: 'GET',
        mode: 'cors',
        cache: 'no-store',
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (j) {
          if (!j || !j.ok) {
            throw new Error((j && j.message) || 'تعذر متابعة الطباعة');
          }
          if (j.status === 'done') {
            return { ok: true, message: j.message || 'تمت الطباعة' };
          }
          if (j.status === 'failed') {
            return { ok: false, message: j.message || 'فشلت الطباعة' };
          }
          if (Date.now() - started > POLL_MAX_MS) {
            return { ok: false, message: 'انتهت مهلة انتظار الطباعة' };
          }
          return new Promise(function (resolve) {
            setTimeout(function () {
              resolve(once());
            }, POLL_MS);
          });
        });
    }
    return once();
  }

  /**
   * يرسل للطابور وينتظر النتيجة النهائية (نجاح/فشل).
   * @returns {Promise<boolean>}
   */
  function printViaAgent(invoiceId, mode) {
    return agentHealth().then(function (ok) {
      if (!ok) return false;
      return fetchJobs(invoiceId, mode).then(function (data) {
        if (!data || !data.ok || !data.jobs || !data.jobs.length) {
          throw new Error((data && data.message) || 'لا توجد مهام طباعة');
        }
        return sendJobs(data.jobs).then(function (queued) {
          var jobId = queued && queued.job_id;
          if (!jobId) return true;
          return pollJob(jobId).then(function (res) {
            if (!res.ok) {
              throw new Error(res.message || 'فشلت الطباعة');
            }
            return true;
          });
        });
      });
    });
  }

  function resolvePrintJobs(options) {
    options = options || {};
    var jobs = options.mode || options.printJobs || options.print_jobs;
    if (!jobs && typeof window.KODY_PRINT_JOBS !== 'undefined') {
      jobs = window.KODY_PRINT_JOBS;
    }
    jobs = String(jobs || 'both');
    if (['both', 'receipt', 'prep'].indexOf(jobs) === -1) {
      jobs = 'both';
    }
    return jobs;
  }

  function printJobsUrl(invoiceId, jobs) {
    if (jobs === 'receipt') {
      return appUrl('print/receipt.php?id=' + encodeURIComponent(invoiceId));
    }
    if (jobs === 'prep') {
      return appUrl('print/preparation.php?id=' + encodeURIComponent(invoiceId));
    }
    return appUrl('print/combo.php?id=' + encodeURIComponent(invoiceId));
  }

  function printOrFallback(invoiceId, mode, fallbackUrls) {
    var jobs = resolvePrintJobs({ mode: mode });
    var urls = fallbackUrls;
    if (!urls || !urls.length) {
      urls = [printJobsUrl(invoiceId, jobs)];
    }
    toast('info', 'جاري الطباعة...', 60000);
    return printViaAgent(invoiceId, jobs)
      .then(function (used) {
        if (used) {
          toast('success', 'تمت الطباعة بنجاح');
          return true;
        }
        (urls || []).forEach(function (u, i) {
          if (i === 0) window.open(u, '_blank');
          else
            setTimeout(function () {
              window.open(u, '_blank');
            }, 300 * i);
        });
        return false;
      })
      .catch(function (err) {
        console.warn('print agent:', err);
        toast('error', (err && err.message) || 'فشلت الطباعة');
        (urls || []).forEach(function (u, i) {
          if (i === 0) window.open(u, '_blank');
          else
            setTimeout(function () {
              window.open(u, '_blank');
            }, 300 * i);
        });
        return false;
      });
  }

  /**
   * طباعة مباشرة من الـ POS: تفضل على الشاشة + toast فقط.
   */
  function printInvoice(invoiceId, options) {
    options = options || {};
    var direct = !!options.direct;
    if (typeof options.direct === 'undefined' && typeof window.KODY_DIRECT_PRINT !== 'undefined') {
      direct = !!window.KODY_DIRECT_PRINT;
    }
    var jobs = resolvePrintJobs(options);
    var comboUrl = options.comboUrl || printJobsUrl(invoiceId, jobs);

    if (!direct) {
      window.open(comboUrl, '_blank');
      return Promise.resolve(false);
    }

    toast('info', 'جاري الطباعة...', 60000);
    return printViaAgent(invoiceId, jobs)
      .then(function (used) {
        if (used) {
          toast('success', 'تمت الطباعة بنجاح');
          return true;
        }
        toast('warning', 'وكيل الطباعة غير متصل');
        return false;
      })
      .catch(function (err) {
        console.warn('print agent:', err);
        toast('error', (err && err.message) || 'فشلت الطباعة');
        return false;
      });
  }

  window.KodyPrintAgent = {
    health: agentHealth,
    print: printViaAgent,
    printOrFallback: printOrFallback,
    printInvoice: printInvoice,
    appRoot: appRoot,
    appUrl: appUrl,
    AGENT_URL: AGENT_URL,
  };
})(window);
