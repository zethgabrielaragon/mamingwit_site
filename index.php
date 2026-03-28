<?php
// ============================================================
// MAMINGWIT CHECKER - Main Interface
// ============================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MAMINGWIT CHECKER — Threat Intelligence Platform</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@300;400;500;600;700&family=Exo+2:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Animated Grid Background -->
<div class="grid-bg"></div>
<div class="scanline"></div>

<!-- Top Status Bar -->
<div class="statusbar">
  <div class="statusbar-inner">
    <div class="status-left">
      <span class="status-dot pulse-green"></span>
      <span class="mono">SYSTEM ONLINE</span>
      <span class="sep">|</span>
      <span class="mono" id="live-clock">--:--:-- UTC</span>
      <span class="sep">|</span>
      <span class="mono">ENGINE v2.4.1</span>
    </div>
    <div class="status-right">
      <span class="mono">MAMINGWIT THREAT INTELLIGENCE PLATFORM</span>
      <span class="sep">|</span>
      <span class="mono badge-alert" id="threat-level-bar">THREAT LEVEL: MONITORING</span>
    </div>
  </div>
</div>

<!-- Main Container -->
<div class="app-container">

  <!-- ======= HEADER ======= -->
  <header class="main-header">
    <div class="header-logo">
      <div class="logo-icon">
        <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
          <polygon points="20,2 38,12 38,28 20,38 2,28 2,12" fill="none" stroke="#00f5ff" stroke-width="1.5"/>
          <polygon points="20,8 32,15 32,25 20,32 8,25 8,15" fill="none" stroke="#00f5ff" stroke-width="1" opacity="0.5"/>
          <circle cx="20" cy="20" r="5" fill="#00f5ff" opacity="0.8"/>
          <line x1="20" y1="2" x2="20" y2="8" stroke="#00f5ff" stroke-width="1.5"/>
          <line x1="20" y1="32" x2="20" y2="38" stroke="#00f5ff" stroke-width="1.5"/>
          <line x1="2" y1="12" x2="8" y2="15" stroke="#00f5ff" stroke-width="1.5"/>
          <line x1="32" y1="25" x2="38" y2="28" stroke="#00f5ff" stroke-width="1.5"/>
          <line x1="38" y1="12" x2="32" y2="15" stroke="#00f5ff" stroke-width="1.5"/>
          <line x1="8" y1="25" x2="2" y2="28" stroke="#00f5ff" stroke-width="1.5"/>
        </svg>
      </div>
      <div class="logo-text">
        <div class="logo-name">MAMINGWIT</div>
        <div class="logo-sub">URL THREAT INTELLIGENCE</div>
      </div>
    </div>
    <nav class="header-nav">
      <button class="nav-btn active" data-panel="scanner" onclick="showPanel('scanner')">
        <svg viewBox="0 0 18 18"><circle cx="8" cy="8" r="5" fill="none" stroke="currentColor" stroke-width="1.5"/><line x1="12" y1="12" x2="17" y2="17" stroke="currentColor" stroke-width="1.5"/></svg>
        SCANNER
      </button>
      <button class="nav-btn" data-panel="history" onclick="showPanel('history')">
        <svg viewBox="0 0 18 18"><circle cx="9" cy="9" r="7" fill="none" stroke="currentColor" stroke-width="1.5"/><polyline points="9,5 9,9 12,12" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>
        HISTORY
      </button>
      <button class="nav-btn" data-panel="intel" onclick="showPanel('intel')">
        <svg viewBox="0 0 18 18"><rect x="2" y="2" width="14" height="14" rx="1" fill="none" stroke="currentColor" stroke-width="1.5"/><line x1="5" y1="6" x2="13" y2="6" stroke="currentColor" stroke-width="1.2"/><line x1="5" y1="9" x2="13" y2="9" stroke="currentColor" stroke-width="1.2"/><line x1="5" y1="12" x2="9" y2="12" stroke="currentColor" stroke-width="1.2"/></svg>
        INTEL
      </button>
    </nav>
  </header>

  <!-- ======= STATS ROW ======= -->
  <div class="stats-row" id="stats-row">
    <div class="stat-card">
      <div class="stat-icon stat-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
      <div class="stat-data">
        <div class="stat-num" id="stat-total">--</div>
        <div class="stat-label">URLS SCANNED</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon stat-red"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
      <div class="stat-data">
        <div class="stat-num" id="stat-high">--</div>
        <div class="stat-label">HIGH RISK DETECTED</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon stat-yellow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></div>
      <div class="stat-data">
        <div class="stat-num" id="stat-reports">--</div>
        <div class="stat-label">COMMUNITY REPORTS</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon stat-cyan"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 014-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg></div>
      <div class="stat-data">
        <div class="stat-num" id="stat-repeated">--</div>
        <div class="stat-label">REPEATED URLS</div>
      </div>
    </div>
  </div>

  <!-- ======= SCANNER PANEL ======= -->
  <div class="panel" id="panel-scanner">
    
    <!-- URL Input Section -->
    <div class="scan-section">
      <div class="section-header">
        <div class="section-title">
          <span class="section-tag">SCAN TARGET</span>
          <h2>URL THREAT ANALYZER</h2>
          <p>Submit a URL for deep threat intelligence analysis using rule-based detection engine</p>
        </div>
        <div class="section-badge">
          <div class="badge-icon">🔍</div>
          <div class="badge-text">READY TO SCAN</div>
        </div>
      </div>

      <div class="url-input-zone">
        <div class="input-prefix">
          <svg viewBox="0 0 20 20" fill="none" stroke="#00f5ff" stroke-width="1.5"><circle cx="8" cy="8" r="5.5"/><line x1="12.5" y1="12.5" x2="18" y2="18"/></svg>
        </div>
        <input type="text" id="url-input" class="url-field" 
               placeholder="Enter URL to analyze (e.g., http://suspicious-site.com/login)" 
               autocomplete="off" spellcheck="false">
        <button class="scan-btn" id="scan-btn" onclick="scanURL()">
          <span class="scan-btn-text">INITIATE SCAN</span>
          <div class="scan-btn-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
          </div>
        </button>
      </div>

      <div class="example-urls">
        <span class="example-label">QUICK TEST:</span>
        <button class="example-tag safe" onclick="setURL('https://www.google.com')">SAFE URL</button>
        <button class="example-tag phish" onclick="setURL('http://paypa1-verify-account.tk/login?redirect=account&update=now&confirm=password')">PHISHING SAMPLE</button>
        <button class="example-tag warn" onclick="setURL('http://192.168.1.1/secure/banking/verify')">IP ADDRESS</button>
      </div>
    </div>

    <!-- Loading State -->
    <div class="scan-loading" id="scan-loading" style="display:none">
      <div class="loading-inner">
        <div class="loading-radar">
          <div class="radar-sweep"></div>
          <div class="radar-rings">
            <div class="ring r1"></div>
            <div class="ring r2"></div>
            <div class="ring r3"></div>
          </div>
        </div>
        <div class="loading-text">
          <div class="loading-main">ANALYZING THREAT VECTOR...</div>
          <div class="loading-sub" id="loading-step">Initializing engine...</div>
        </div>
      </div>
    </div>

    <!-- ======= RESULTS PANEL ======= -->
    <div id="result-container" style="display:none">

      <!-- Risk Overview -->
      <div class="result-overview">
        
        <!-- Risk Meter -->
        <div class="risk-meter-card">
          <div class="card-label">THREAT ASSESSMENT</div>
          <div class="risk-meter-wrap">
            <div class="risk-arc-container">
              <svg class="risk-arc" viewBox="0 0 200 120">
                <!-- Background arc -->
                <path d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="#1a2744" stroke-width="18" stroke-linecap="round"/>
                <!-- Colored arc segments -->
                <path d="M 20 100 A 80 80 0 0 1 80 22" fill="none" stroke="#00e676" stroke-width="18" stroke-linecap="round" opacity="0.3"/>
                <path d="M 80 22 A 80 80 0 0 1 140 22" fill="none" stroke="#ffd600" stroke-width="18" stroke-linecap="round" opacity="0.3"/>
                <path d="M 140 22 A 80 80 0 0 1 180 100" fill="none" stroke="#ff1744" stroke-width="18" stroke-linecap="round" opacity="0.3"/>
                <!-- Active progress arc -->
                <path id="meter-progress" d="M 20 100 A 80 80 0 0 1 20 100" fill="none" stroke="#00e676" stroke-width="18" stroke-linecap="round" stroke-dasharray="251.2" stroke-dashoffset="251.2" class="meter-path"/>
                <!-- Needle -->
                <line id="meter-needle" x1="100" y1="100" x2="100" y2="30" stroke="#ffffff" stroke-width="2" stroke-linecap="round" style="transform-origin:100px 100px; transform:rotate(0deg);" class="meter-needle"/>
                <circle cx="100" cy="100" r="5" fill="#ffffff"/>
              </svg>
              <div class="risk-score-display">
                <div class="risk-number" id="risk-number">0</div>
                <div class="risk-label-text">RISK SCORE</div>
              </div>
            </div>
            <div class="risk-level-badge" id="risk-level-badge">
              <span class="risk-level-text" id="risk-level-text">SCANNING...</span>
            </div>
          </div>
          <div class="meter-legend">
            <span class="legend-item low">● LOW (0-29)</span>
            <span class="legend-item med">● MEDIUM (30-59)</span>
            <span class="legend-item high">● HIGH (60+)</span>
          </div>
        </div>

        <!-- URL Metadata -->
        <div class="url-meta-card">
          <div class="card-label">TARGET ANALYSIS</div>
          <div class="meta-grid">
            <div class="meta-item">
              <div class="meta-key">URL</div>
              <div class="meta-val url-val" id="meta-url">—</div>
            </div>
            <div class="meta-item">
              <div class="meta-key">DOMAIN</div>
              <div class="meta-val" id="meta-domain">—</div>
            </div>
            <div class="meta-item">
              <div class="meta-key">PROTOCOL</div>
              <div class="meta-val" id="meta-protocol">—</div>
            </div>
            <div class="meta-item">
              <div class="meta-key">URL LENGTH</div>
              <div class="meta-val" id="meta-length">—</div>
            </div>
            <div class="meta-item">
              <div class="meta-key">PARAMETERS</div>
              <div class="meta-val" id="meta-params">—</div>
            </div>
            <div class="meta-item">
              <div class="meta-key">SCAN COUNT</div>
              <div class="meta-val" id="meta-scan-count">—</div>
            </div>
          </div>
          <div class="quick-indicators">
            <div class="indicator" id="ind-https"><span class="ind-dot"></span>HTTPS</div>
            <div class="indicator" id="ind-ip"><span class="ind-dot"></span>IP ADDR</div>
            <div class="indicator" id="ind-keywords"><span class="ind-dot"></span>KEYWORDS</div>
            <div class="indicator" id="ind-blacklist"><span class="ind-dot"></span>BLACKLIST</div>
            <div class="indicator" id="ind-previous"><span class="ind-dot"></span>FLAGGED</div>
          </div>
          <!-- Community Report -->
          <div class="community-section">
            <div class="community-count">
              <svg viewBox="0 0 16 16" fill="none" stroke="#ff6b6b" stroke-width="1.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
              <span>COMMUNITY REPORTS:</span>
              <strong id="community-count-val">0</strong>
            </div>
            <button class="report-btn" id="report-btn" onclick="submitReport()">
              <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 2h8l2 4-2 4H4L2 6l2-4z"/><line x1="10" y1="6" x2="10" y2="6"/></svg>
              REPORT AS PHISHING
            </button>
          </div>
        </div>
      </div>

      <!-- Flags / Triggered Rules -->
      <div class="flags-section">
        <div class="section-header-sm">
          <div class="sh-label">TRIGGERED RULES</div>
          <div class="sh-count" id="flags-count">0 ALERTS</div>
        </div>
        <div class="flags-grid" id="flags-grid">
          <!-- Populated by JS -->
        </div>
      </div>

      <!-- Technical Breakdown -->
      <div class="breakdown-section">
        <div class="section-header-sm">
          <div class="sh-label">RULE ENGINE BREAKDOWN</div>
          <div class="sh-count">DETAILED SCAN RESULTS</div>
        </div>
        <div class="breakdown-table-wrap">
          <table class="breakdown-table">
            <thead>
              <tr>
                <th>DETECTION RULE</th>
                <th>STATUS</th>
                <th>FINDING</th>
                <th>SCORE IMPACT</th>
              </tr>
            </thead>
            <tbody id="breakdown-tbody">
            </tbody>
          </table>
        </div>
      </div>

      <!-- Phishing Keywords -->
      <div class="keywords-section" id="keywords-section" style="display:none">
        <div class="section-header-sm">
          <div class="sh-label">⚠ DETECTED PHISHING KEYWORDS</div>
        </div>
        <div class="keywords-grid" id="keywords-grid"></div>
      </div>

      <!-- Legitimate vs Phishing Comparison -->
      <div class="comparison-section">
        <div class="section-header-sm">
          <div class="sh-label">THREAT INTELLIGENCE REFERENCE</div>
          <div class="sh-count">LEGITIMATE VS PHISHING COMPARISON</div>
        </div>
        <div class="comparison-grid">
          <div class="comp-col comp-legit">
            <div class="comp-header">
              <span class="comp-icon">✓</span>
              LEGITIMATE URL PATTERN
            </div>
            <ul class="comp-list">
              <li><span class="check">●</span> Uses HTTPS with valid SSL certificate</li>
              <li><span class="check">●</span> Simple, recognizable domain name</li>
              <li><span class="check">●</span> No excessive subdomains or hyphens</li>
              <li><span class="check">●</span> Short, clean URL structure</li>
              <li><span class="check">●</span> No suspicious query parameters</li>
              <li><span class="check">●</span> Matches the displayed brand name</li>
              <li><span class="check">●</span> Standard TLD (.com, .org, .gov)</li>
            </ul>
            <div class="comp-example">
              <code>https://www.paypal.com/signin</code>
            </div>
          </div>
          <div class="comp-col comp-phish">
            <div class="comp-header">
              <span class="comp-icon">✗</span>
              PHISHING URL PATTERN
            </div>
            <ul class="comp-list">
              <li><span class="cross">●</span> Uses HTTP or fake HTTPS indicator</li>
              <li><span class="cross">●</span> Misspelled or substituted characters</li>
              <li><span class="cross">●</span> Multiple subdomains to deceive</li>
              <li><span class="cross">●</span> Excessively long with parameters</li>
              <li><span class="cross">●</span> Contains social engineering words</li>
              <li><span class="cross">●</span> Raw IP address instead of domain</li>
              <li><span class="cross">●</span> Suspicious free TLD (.tk, .ml)</li>
            </ul>
            <div class="comp-example phish">
              <code>http://paypa1-secure.verify-login.tk/update?account=confirm&redirect=webscr</code>
            </div>
          </div>
        </div>
      </div>

    </div><!-- end result-container -->
  </div><!-- end panel-scanner -->

  <!-- ======= HISTORY PANEL ======= -->
  <div class="panel" id="panel-history" style="display:none">
    <div class="scan-section">
      <div class="section-header">
        <div class="section-title">
          <span class="section-tag">URL HISTORY</span>
          <h2>SCAN LOG DATABASE</h2>
          <p>All previously analyzed URLs with risk assessments and community data</p>
        </div>
        <button class="refresh-btn" onclick="loadHistory()">↺ REFRESH</button>
      </div>
    </div>
    <div class="history-table-wrap">
      <table class="history-table">
        <thead>
          <tr>
            <th>URL / DOMAIN</th>
            <th>RISK LEVEL</th>
            <th>SCORE</th>
            <th>SCANS</th>
            <th>REPORTS</th>
            <th>LAST CHECKED</th>
            <th>ACTION</th>
          </tr>
        </thead>
        <tbody id="history-tbody">
          <tr><td colspan="7" class="loading-row">Loading history...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ======= INTEL PANEL ======= -->
  <div class="panel" id="panel-intel" style="display:none">
    <div class="scan-section">
      <div class="section-header">
        <div class="section-title">
          <span class="section-tag">THREAT INTEL</span>
          <h2>SECURITY KNOWLEDGE BASE</h2>
          <p>Detection rules, phishing techniques, and social engineering tactics</p>
        </div>
      </div>
    </div>

    <div class="intel-grid">
      <div class="intel-card">
        <div class="intel-icon">🎣</div>
        <div class="intel-title">PHISHING DETECTION RULES</div>
        <div class="intel-body">
          <p>The engine applies <strong>15+ detection rules</strong> across multiple categories:</p>
          <ul>
            <li>Protocol Analysis (HTTPS enforcement)</li>
            <li>IP Address Detection</li>
            <li>Domain Spoofing & Brand Impersonation</li>
            <li>Keyword Frequency Analysis</li>
            <li>TLD Risk Classification</li>
            <li>URL Structure Anomalies</li>
            <li>Character Obfuscation Detection</li>
            <li>Redirect Pattern Recognition</li>
            <li>Subdomain Depth Analysis</li>
            <li>Community Blacklist Matching</li>
          </ul>
        </div>
      </div>

      <div class="intel-card">
        <div class="intel-icon">🧠</div>
        <div class="intel-title">SOCIAL ENGINEERING TACTICS</div>
        <div class="intel-body">
          <p>Attackers commonly use these psychological manipulation techniques:</p>
          <ul>
            <li><strong>Urgency</strong> — "Your account will be suspended!"</li>
            <li><strong>Fear</strong> — "Unauthorized access detected"</li>
            <li><strong>Greed</strong> — "You won a free prize!"</li>
            <li><strong>Authority</strong> — Impersonating banks or government</li>
            <li><strong>Trust</strong> — Mimicking trusted brand logos and URLs</li>
            <li><strong>Curiosity</strong> — "Click to see who viewed your profile"</li>
          </ul>
        </div>
      </div>

      <div class="intel-card">
        <div class="intel-icon">⚖️</div>
        <div class="intel-title">RISK SCORING SYSTEM</div>
        <div class="intel-body">
          <p>Risk score is computed by aggregating rule violations:</p>
          <div class="score-table">
            <div class="score-row"><span>No HTTPS</span><span class="score-val warn">+15</span></div>
            <div class="score-row"><span>IP Address Used</span><span class="score-val danger">+25</span></div>
            <div class="score-row"><span>Blacklisted Domain</span><span class="score-val critical">+50</span></div>
            <div class="score-row"><span>Brand Spoofing</span><span class="score-val critical">+25</span></div>
            <div class="score-row"><span>Phishing Keywords</span><span class="score-val danger">+up to 30</span></div>
            <div class="score-row"><span>Excessive Length</span><span class="score-val warn">+10~20</span></div>
            <div class="score-row"><span>Deep Subdomains</span><span class="score-val warn">+5~15</span></div>
            <div class="score-row"><span>Suspicious TLD</span><span class="score-val warn">+15</span></div>
          </div>
        </div>
      </div>

      <div class="intel-card">
        <div class="intel-icon">🛡️</div>
        <div class="intel-title">HOW TO PROTECT YOURSELF</div>
        <div class="intel-body">
          <ul>
            <li>Always verify HTTPS before entering credentials</li>
            <li>Check the domain matches the expected organization</li>
            <li>Hover over links before clicking to preview the URL</li>
            <li>Be suspicious of urgent messages asking for action</li>
            <li>Use a password manager — it won't autofill on fake sites</li>
            <li>Enable two-factor authentication on all accounts</li>
            <li>Report suspicious URLs to protect the community</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

</div><!-- end app-container -->

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<!-- Report Modal -->
<div class="modal-overlay" id="report-modal" style="display:none" onclick="closeModal(event)">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title">⚠ SUBMIT COMMUNITY REPORT</div>
      <button class="modal-close" onclick="closeReportModal()">✕</button>
    </div>
    <div class="modal-body">
      <p>Help the community by flagging this URL as a phishing or malicious link.</p>
      <div class="report-url-display" id="report-url-display"></div>
      <label class="modal-label">REASON FOR REPORT</label>
      <select class="modal-select" id="report-reason">
        <option value="Phishing - credential theft attempt">Phishing (credential theft)</option>
        <option value="Malware distribution site">Malware distribution</option>
        <option value="Brand impersonation or spoofing">Brand impersonation</option>
        <option value="Scam or fraud">Scam / Fraud</option>
        <option value="Spam or unwanted content">Spam</option>
        <option value="Other suspicious activity">Other suspicious activity</option>
      </select>
    </div>
    <div class="modal-footer">
      <button class="modal-btn cancel" onclick="closeReportModal()">CANCEL</button>
      <button class="modal-btn submit" onclick="confirmReport()">SUBMIT REPORT</button>
    </div>
  </div>
</div>

<script src="js/app.js"></script>
</body>
</html>