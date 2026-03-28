// ============================================================
// MAMINGWIT CHECKER — Frontend Application Logic
// SIEM Dashboard Cybersecurity UI
// ============================================================

'use strict';

// === GLOBAL STATE ===
let currentURL = '';
let currentResult = null;
const loadingSteps = [
  'Resolving domain...', 'Checking HTTPS protocol...', 'Analyzing URL structure...',
  'Scanning keyword patterns...', 'Querying blacklist database...', 'Detecting brand spoofing...',
  'Checking community reports...', 'Calculating threat score...', 'Generating assessment...'
];

// === INIT ===
document.addEventListener('DOMContentLoaded', () => {
  initClock();
  loadStats();
  setupKeyboardShortcuts();
  document.getElementById('url-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') scanURL();
  });
});

// === CLOCK ===
function initClock() {
  const updateClock = () => {
    const now = new Date();
    const t = now.toUTCString().split(' ');
    document.getElementById('live-clock').textContent =
      now.toUTCString().match(/\d{2}:\d{2}:\d{2}/)?.[0] + ' UTC' || '--:--:-- UTC';
  };
  updateClock();
  setInterval(updateClock, 1000);
}

// === KEYBOARD SHORTCUTS ===
function setupKeyboardShortcuts() {
  document.addEventListener('keydown', e => {
    // ESC to close modal
    if (e.key === 'Escape') closeReportModal();
    // Ctrl+K to focus URL input
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
      e.preventDefault();
      document.getElementById('url-input').focus();
    }
  });
}

// === PANEL NAVIGATION ===
function showPanel(name) {
  ['scanner','history','intel'].forEach(p => {
    const el = document.getElementById('panel-' + p);
    if (el) el.style.display = (p === name) ? 'block' : 'none';
  });
  document.querySelectorAll('.nav-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.panel === name);
  });
  if (name === 'history') loadHistory();
}

// === SET URL FROM EXAMPLE ===
function setURL(url) {
  const input = document.getElementById('url-input');
  input.value = url;
  input.focus();
}

// === MAIN SCAN FUNCTION ===
async function scanURL() {
  const input = document.getElementById('url-input');
  const url = input.value.trim();

  if (!url) {
    showToast('⚠ Please enter a URL to analyze.', 'error');
    input.focus();
    return;
  }

  currentURL = url;
  showLoading(true);
  setThreatLevel('ANALYZING...');

  const btn = document.getElementById('scan-btn');
  btn.disabled = true;

  try {
    // Animate loading steps
    const stepEl = document.getElementById('loading-step');
    let stepIdx = 0;
    const stepInterval = setInterval(() => {
      if (stepEl && stepIdx < loadingSteps.length) {
        stepEl.textContent = loadingSteps[stepIdx++];
      }
    }, 300);

    const formData = new FormData();
    formData.append('action', 'analyze');
    formData.append('url', url);

    const response = await fetch('api.php', {
      method: 'POST',
      body: formData
    });

    const json = await response.json();
    clearInterval(stepInterval);

    if (json.error) {
      showToast('ERROR: ' + json.message, 'error');
      showLoading(false);
      btn.disabled = false;
      return;
    }

    currentResult = json.data;
    renderResults(currentResult);
    setThreatLevel(currentResult.risk_level.toUpperCase());
    loadStats(); // Refresh stats

  } catch (err) {
    showToast('Connection error. Ensure XAMPP is running.', 'error');
    console.error(err);
  }

  showLoading(false);
  btn.disabled = false;
}

// === SHOW/HIDE LOADING ===
function showLoading(show) {
  document.getElementById('scan-loading').style.display = show ? 'flex' : 'none';
  document.getElementById('result-container').style.display = show ? 'none' : 
    (currentResult ? 'block' : 'none');
}

// === RENDER RESULTS ===
function renderResults(data) {
  document.getElementById('result-container').style.display = 'block';

  // === Risk Meter ===
  animateRiskMeter(data.risk_score, data.risk_level);

  // === URL Metadata ===
  const urlDisplay = data.url.length > 60 ? data.url.substring(0, 60) + '...' : data.url;
  document.getElementById('meta-url').textContent = urlDisplay;
  document.getElementById('meta-url').title = data.url;
  document.getElementById('meta-domain').textContent = data.domain || '—';
  document.getElementById('meta-protocol').textContent = (data.protocol || 'unknown').toUpperCase();
  document.getElementById('meta-length').textContent = data.url_length + ' chars';
  document.getElementById('meta-params').textContent = data.param_count + ' params';
  document.getElementById('meta-scan-count').textContent = '#' + (data.check_count || 1) + 
    (data.check_count > 1 ? ' (repeated)' : ' (first scan)');

  // === Quick Indicators ===
  setIndicator('ind-https', data.is_https ? 'pass' : 'fail', 'HTTPS', data.is_https ? '✓ Secure' : '✗ Insecure');
  setIndicator('ind-ip', data.uses_ip ? 'fail' : 'pass', 'IP ADDR', data.uses_ip ? '✗ Raw IP' : '✓ Domain');
  setIndicator('ind-keywords', data.has_phishing_keywords ? 'fail' : 'pass', 'KEYWORDS', data.has_phishing_keywords ? '✗ Found' : '✓ Clean');
  setIndicator('ind-blacklist', data.blacklisted ? 'fail' : 'pass', 'BLACKLIST', data.blacklisted ? '✗ Listed' : '✓ Clear');
  setIndicator('ind-previous', data.previously_flagged ? 'warn' : 'pass', 'FLAGGED', data.previously_flagged ? '⚠ Repeat' : '✓ New');

  // === Community Reports ===
  document.getElementById('community-count-val').textContent = data.community_reports || 0;

  // === Flags ===
  renderFlags(data.flags || []);

  // === Technical Breakdown ===
  renderBreakdown(data.technical_breakdown || []);

  // === Keywords ===
  if (data.detected_keywords && data.detected_keywords.length > 0) {
    renderKeywords(data.detected_keywords);
    document.getElementById('keywords-section').style.display = 'block';
  } else {
    document.getElementById('keywords-section').style.display = 'none';
  }

  // === Previously Flagged Warning ===
  if (data.previously_flagged && data.previous_risk_level === 'high') {
    showToast('⚠ WARNING: This URL was previously flagged as HIGH RISK!', 'error');
  } else if (data.community_reports > 0) {
    showToast(`⚠ ${data.community_reports} community member(s) reported this URL`, 'info');
  }

  // Scroll to results smoothly
  document.getElementById('result-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// === ANIMATE RISK METER ===
function animateRiskMeter(score, level) {
  const numEl = document.getElementById('risk-number');
  const badgeEl = document.getElementById('risk-level-badge');
  const levelText = document.getElementById('risk-level-text');
  const progressPath = document.getElementById('meter-progress');
  const needle = document.getElementById('meter-needle');

  // Animate number
  let current = 0;
  const target = Math.min(100, parseInt(score));
  const duration = 1200;
  const start = performance.now();

  const animateNum = (ts) => {
    const progress = Math.min((ts - start) / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    current = Math.round(eased * target);
    numEl.textContent = current;
    if (progress < 1) requestAnimationFrame(animateNum);
  };
  requestAnimationFrame(animateNum);

  // Animate SVG arc
  // Arc from ~20,100 to ~180,100 is 180 degrees, total arc path = 251.2 (half circle perimeter = π*80)
  const totalDash = 251.2;
  const filledDash = totalDash * (target / 100);
  const offset = totalDash - filledDash;

  // Color based on level
  const colors = { low: '#00e676', medium: '#ffd600', high: '#ff1744' };
  const color = colors[level] || '#00e676';

  setTimeout(() => {
    if (progressPath) {
      progressPath.style.strokeDashoffset = offset;
      progressPath.style.stroke = color;
      progressPath.style.strokeDasharray = totalDash;
    }

    // Needle rotation: -90deg = 0 score, 90deg = 100 score
    if (needle) {
      const rotation = -90 + (180 * (target / 100));
      needle.style.transform = `rotate(${rotation}deg)`;
    }
  }, 100);

  // Update badge
  badgeEl.className = 'risk-level-badge level-' + level;
  const labels = { low: '✓ LOW RISK', medium: '⚠ MEDIUM RISK', high: '✗ HIGH RISK' };
  levelText.textContent = labels[level] || level.toUpperCase();

  // Update number color
  numEl.style.color = color;
  numEl.style.textShadow = `0 0 20px ${color}60`;
}

// === SET INDICATOR STATE ===
function setIndicator(id, state, label, tooltip) {
  const el = document.getElementById(id);
  if (!el) return;
  el.className = 'indicator ' + state;
  el.title = tooltip || '';
  const dot = el.querySelector('.ind-dot');
  const textNode = el.childNodes[el.childNodes.length - 1];
}

// === RENDER FLAGS ===
function renderFlags(flags) {
  const grid = document.getElementById('flags-grid');
  const countEl = document.getElementById('flags-count');
  
  countEl.textContent = flags.length + ' ALERT' + (flags.length !== 1 ? 'S' : '');
  
  if (flags.length === 0) {
    grid.innerHTML = '<div class="flag-empty">✓ NO THREAT INDICATORS DETECTED — URL APPEARS CLEAN</div>';
    return;
  }

  const icons = { warning: '⚠', danger: '🔶', critical: '🔴' };
  
  grid.innerHTML = flags.map(flag => `
    <div class="flag-item sev-${flag.severity}">
      <div class="flag-icon">${icons[flag.severity] || '⚠'}</div>
      <div class="flag-content">
        <div class="flag-code">${escHtml(flag.code)}</div>
        <div class="flag-desc">${escHtml(flag.description)}</div>
        <div class="flag-weight">RISK CONTRIBUTION: +${flag.weight} pts</div>
      </div>
    </div>
  `).join('');
}

// === RENDER BREAKDOWN TABLE ===
function renderBreakdown(breakdown) {
  const tbody = document.getElementById('breakdown-tbody');
  if (!breakdown || breakdown.length === 0) {
    tbody.innerHTML = '<tr><td colspan="4" class="loading-row">No breakdown data.</td></tr>';
    return;
  }

  tbody.innerHTML = breakdown.map(row => {
    const isZero = row.score_impact === '+0';
    return `
      <tr>
        <td style="color:var(--text-bright); font-weight:600">${escHtml(row.rule)}</td>
        <td><span class="status-badge status-${row.status}">${escHtml(row.status)}</span></td>
        <td>${escHtml(row.detail)}</td>
        <td class="score-impact${isZero ? ' zero' : ''}">${escHtml(row.score_impact)}</td>
      </tr>
    `;
  }).join('');
}

// === RENDER KEYWORDS ===
function renderKeywords(keywords) {
  const grid = document.getElementById('keywords-grid');
  grid.innerHTML = keywords.map(kw => `
    <div class="keyword-chip">
      <span class="kw-word">${escHtml(kw.word)}</span>
      <span class="kw-cat">[${escHtml(kw.category)}]</span>
      <span class="kw-weight">+${kw.weight}pts</span>
    </div>
  `).join('');
}

// === LOAD STATS ===
async function loadStats() {
  try {
    const response = await fetch('api.php?action=stats');
    const json = await response.json();
    if (!json.error && json.data) {
      const d = json.data;
      animateCounter('stat-total', parseInt(d.total_checked) || 0);
      animateCounter('stat-high', parseInt(d.high_risk) || 0);
      animateCounter('stat-reports', parseInt(d.total_reports) || 0);
      animateCounter('stat-repeated', parseInt(d.repeated_urls) || 0);
    }
  } catch(e) {
    // Stats not critical, fail silently
  }
}

// === ANIMATE COUNTER ===
function animateCounter(id, target) {
  const el = document.getElementById(id);
  if (!el) return;
  const start = parseInt(el.textContent) || 0;
  const dur = 800;
  const t0 = performance.now();
  const anim = (ts) => {
    const p = Math.min((ts - t0) / dur, 1);
    const v = Math.round(start + (target - start) * (1 - Math.pow(1 - p, 2)));
    el.textContent = v;
    if (p < 1) requestAnimationFrame(anim);
  };
  requestAnimationFrame(anim);
}

// === LOAD HISTORY ===
async function loadHistory() {
  const tbody = document.getElementById('history-tbody');
  tbody.innerHTML = '<tr><td colspan="7" class="loading-row">Loading scan history...</td></tr>';

  try {
    const response = await fetch('api.php?action=history');
    const json = await response.json();

    if (json.error || !json.data || json.data.length === 0) {
      tbody.innerHTML = '<tr><td colspan="7" class="loading-row">No scan history found.</td></tr>';
      return;
    }

    tbody.innerHTML = json.data.map(row => {
      const urlShort = (row.url || '').length > 60 ? row.url.substring(0, 60) + '...' : (row.url || '—');
      const date = new Date(row.last_checked);
      const dateStr = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
      const riskClass = 'risk-' + (row.risk_level || 'low');
      const riskLabel = (row.risk_level || 'low').toUpperCase();

      return `
        <tr>
          <td>
            <span class="url-cell" title="${escHtml(row.url || '')}">${escHtml(urlShort)}</span>
            <span class="domain-cell">${escHtml(row.domain || '—')}</span>
          </td>
          <td><span class="risk-badge-sm ${riskClass}">${riskLabel}</span></td>
          <td style="color:var(--text-bright); font-family:var(--font-mono)">${row.risk_score || 0}</td>
          <td style="font-family:var(--font-mono)">${row.check_count || 1}×</td>
          <td style="color:${(row.report_count > 0) ? 'var(--red)' : 'var(--text-dim)'}; font-family:var(--font-mono)">
            ${row.report_count > 0 ? '⚠ ' : ''}${row.report_count || 0}
          </td>
          <td style="font-family:var(--font-mono); font-size:11px; color:var(--text-dim)">${escHtml(dateStr)}</td>
          <td>
            <button class="rescan-btn" onclick="rescanURL(${JSON.stringify(escHtml(row.url || ''))})">
              RE-SCAN
            </button>
          </td>
        </tr>
      `;
    }).join('');
  } catch(e) {
    tbody.innerHTML = '<tr><td colspan="7" class="loading-row">Failed to load history. Ensure server is running.</td></tr>';
  }
}

// === RESCAN FROM HISTORY ===
function rescanURL(url) {
  document.getElementById('url-input').value = url;
  showPanel('scanner');
  scanURL();
}

// === COMMUNITY REPORT ===
function submitReport() {
  if (!currentURL) {
    showToast('No URL to report. Please scan a URL first.', 'error');
    return;
  }
  document.getElementById('report-url-display').textContent = currentURL;
  document.getElementById('report-modal').style.display = 'flex';
}

async function confirmReport() {
  const reason = document.getElementById('report-reason').value;
  closeReportModal();

  const btn = document.getElementById('report-btn');
  btn.disabled = true;
  btn.textContent = 'SUBMITTING...';

  try {
    const formData = new FormData();
    formData.append('action', 'report');
    formData.append('url', currentURL);
    formData.append('reason', reason);

    const response = await fetch('api.php', { method: 'POST', body: formData });
    const json = await response.json();

    if (json.success) {
      showToast('✓ Report submitted. Thank you for protecting the community!', 'success');
      // Update count
      const countEl = document.getElementById('community-count-val');
      countEl.textContent = parseInt(countEl.textContent || 0) + 1;
      loadStats();
    } else {
      showToast('⚠ ' + (json.message || 'Could not submit report.'), 'error');
    }
  } catch(e) {
    showToast('Connection error.', 'error');
  }

  btn.disabled = false;
  btn.innerHTML = `<svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 2h8l2 4-2 4H4L2 6l2-4z"/></svg> REPORT AS PHISHING`;
}

function closeReportModal() {
  document.getElementById('report-modal').style.display = 'none';
}

function closeModal(e) {
  if (e.target.classList.contains('modal-overlay')) closeReportModal();
}

// === THREAT LEVEL BAR ===
function setThreatLevel(level) {
  const bar = document.getElementById('threat-level-bar');
  if (!bar) return;
  const colors = {
    'LOW': 'var(--green)',
    'MEDIUM': 'var(--yellow)',
    'HIGH': 'var(--red)',
    'ANALYZING...': 'var(--cyan)',
    'MONITORING': 'var(--text-dim)'
  };
  bar.textContent = 'THREAT LEVEL: ' + level;
  bar.style.color = colors[level] || 'var(--text-dim)';
}

// === TOAST NOTIFICATION ===
let toastTimeout;
function showToast(msg, type = 'info') {
  const toast = document.getElementById('toast');
  toast.textContent = msg;
  toast.className = 'toast show toast-' + type;
  clearTimeout(toastTimeout);
  toastTimeout = setTimeout(() => {
    toast.classList.remove('show');
  }, 4000);
}

// === UTILITY: HTML Escape ===
function escHtml(str) {
  if (typeof str !== 'string') return String(str || '');
  return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
}