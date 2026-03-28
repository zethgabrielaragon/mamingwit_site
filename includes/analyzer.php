<?php
// ============================================================
// MAMINGWIT CHECKER - URL Analysis Engine
// Rule-Based Phishing & Malicious URL Detection
// ============================================================

require_once __DIR__ . '/db.php';

class URLAnalyzer {
    private $db;
    private $url;
    private $parsedUrl;
    private $domain;
    private $flags = [];
    private $riskScore = 0;
    private $details = [];

    // Known legitimate TLDs and patterns
    private $suspiciousTLDs = ['.tk', '.ml', '.ga', '.cf', '.gq', '.pw', '.top', '.click', '.download', '.loan', '.work', '.party'];
    
    // Brand names commonly targeted in phishing
    private $trustedBrands = [
        'paypal', 'amazon', 'google', 'microsoft', 'apple', 'netflix', 'facebook',
        'instagram', 'twitter', 'linkedin', 'yahoo', 'ebay', 'walmart', 'chase',
        'bankofamerica', 'citibank', 'wellsfargo', 'irs', 'dhl', 'fedex', 'ups'
    ];

    public function __construct($url) {
        $this->db = Database::getInstance();
        $this->url = trim($url);
        $this->parsedUrl = parse_url($this->url);
        $this->domain = isset($this->parsedUrl['host']) ? strtolower($this->parsedUrl['host']) : '';
    }

    public function analyze() {
        $result = [
            'url'           => $this->url,
            'domain'        => $this->domain,
            'protocol'      => isset($this->parsedUrl['scheme']) ? $this->parsedUrl['scheme'] : 'unknown',
            'flags'         => [],
            'risk_score'    => 0,
            'risk_level'    => 'low',
            'details'       => [],
            'is_https'      => false,
            'url_length'    => strlen($this->url),
            'param_count'   => 0,
            'uses_ip'       => false,
            'suspicious_domain' => false,
            'has_phishing_keywords' => false,
            'community_reports' => 0,
            'check_count'   => 1,
            'previously_flagged' => false,
            'technical_breakdown' => []
        ];

        // Run all detection rules
        $this->checkHTTPS($result);
        $this->checkIPAddress($result);
        $this->checkURLLength($result);
        $this->checkParameters($result);
        $this->checkSuspiciousDomain($result);
        $this->checkPhishingKeywords($result);
        $this->checkBlacklist($result);
        $this->checkSubdomainDepth($result);
        $this->checkSuspiciousChars($result);
        $this->checkTLD($result);
        $this->checkBrandSpoofing($result);
        $this->checkRedirectPatterns($result);
        $this->checkPreviousHistory($result);

        // Calculate final risk score and level
        $result['risk_score'] = min(100, $this->riskScore);
        $result['flags'] = $this->flags;
        $result['details'] = $this->details;

        if ($result['risk_score'] >= 60) {
            $result['risk_level'] = 'high';
        } elseif ($result['risk_score'] >= 30) {
            $result['risk_level'] = 'medium';
        } else {
            $result['risk_level'] = 'low';
        }

        // Save to database
        $this->saveToDatabase($result);

        // Get community report count
        $result['community_reports'] = $this->getCommunityReports();

        return $result;
    }

    private function checkHTTPS(&$result) {
        $scheme = isset($this->parsedUrl['scheme']) ? strtolower($this->parsedUrl['scheme']) : '';
        $result['is_https'] = ($scheme === 'https');
        
        if ($scheme !== 'https') {
            $penalty = 15;
            $this->addFlag('NO_HTTPS', 'Protocol is not HTTPS', $penalty, 'warning');
            $this->riskScore += $penalty;
            $result['technical_breakdown'][] = [
                'rule' => 'HTTPS Check',
                'status' => 'FAIL',
                'detail' => 'Uses ' . strtoupper($scheme ?: 'UNKNOWN') . ' instead of HTTPS',
                'score_impact' => "+{$penalty}"
            ];
        } else {
            $result['technical_breakdown'][] = [
                'rule' => 'HTTPS Check',
                'status' => 'PASS',
                'detail' => 'Encrypted HTTPS connection',
                'score_impact' => '+0'
            ];
        }
    }

    private function checkIPAddress(&$result) {
        // Check if host is an IP address
        if (filter_var($this->domain, FILTER_VALIDATE_IP)) {
            $result['uses_ip'] = true;
            $penalty = 25;
            $this->addFlag('IP_ADDRESS', 'Uses raw IP address instead of domain name', $penalty, 'danger');
            $this->riskScore += $penalty;
            $result['technical_breakdown'][] = [
                'rule' => 'IP Address Check',
                'status' => 'FAIL',
                'detail' => 'URL uses IP address ' . $this->domain . ' instead of a domain name',
                'score_impact' => "+{$penalty}"
            ];
        } else {
            $result['technical_breakdown'][] = [
                'rule' => 'IP Address Check',
                'status' => 'PASS',
                'detail' => 'Uses domain name, not raw IP',
                'score_impact' => '+0'
            ];
        }
    }

    private function checkURLLength(&$result) {
        $len = strlen($this->url);
        if ($len > 200) {
            $penalty = 20;
            $this->addFlag('EXCESSIVE_LENGTH', "URL is extremely long ({$len} chars)", $penalty, 'danger');
            $this->riskScore += $penalty;
            $status = 'FAIL';
        } elseif ($len > 100) {
            $penalty = 10;
            $this->addFlag('LONG_URL', "URL is unusually long ({$len} chars)", $penalty, 'warning');
            $this->riskScore += $penalty;
            $status = 'WARNING';
        } else {
            $penalty = 0;
            $status = 'PASS';
        }
        $result['technical_breakdown'][] = [
            'rule' => 'URL Length Check',
            'status' => $status,
            'detail' => "URL length: {$len} characters" . ($len > 75 ? ' (suspicious threshold: >75)' : ' (normal range)'),
            'score_impact' => "+{$penalty}"
        ];
    }

    private function checkParameters(&$result) {
        $query = isset($this->parsedUrl['query']) ? $this->parsedUrl['query'] : '';
        $params = [];
        if ($query) parse_str($query, $params);
        $paramCount = count($params);
        $result['param_count'] = $paramCount;

        if ($paramCount > 5) {
            $penalty = 15;
            $this->addFlag('EXCESSIVE_PARAMS', "URL contains {$paramCount} query parameters (excessive)", $penalty, 'warning');
            $this->riskScore += $penalty;
            $status = 'FAIL';
        } else {
            $penalty = 0;
            $status = 'PASS';
        }
        $result['technical_breakdown'][] = [
            'rule' => 'Parameter Analysis',
            'status' => $status,
            'detail' => "Query parameters found: {$paramCount}",
            'score_impact' => "+{$penalty}"
        ];
    }

    private function checkSuspiciousDomain(&$result) {
        if (empty($this->domain)) return;

        // Check for numeric subdomains or excessive hyphens
        $hyphenCount = substr_count($this->domain, '-');
        if ($hyphenCount >= 3) {
            $penalty = 10;
            $this->addFlag('EXCESSIVE_HYPHENS', "Domain contains {$hyphenCount} hyphens (suspicious)", $penalty, 'warning');
            $this->riskScore += $penalty;
            $result['suspicious_domain'] = true;
        }

        // Check for numbers mixed in domain name
        $domainParts = explode('.', $this->domain);
        $mainDomain = $domainParts[count($domainParts) - 2] ?? '';
        
        if (preg_match('/\d{2,}/', $mainDomain)) {
            $penalty = 10;
            $this->addFlag('NUMERIC_DOMAIN', 'Domain name contains suspicious numeric sequences', $penalty, 'warning');
            $this->riskScore += $penalty;
            $result['suspicious_domain'] = true;
        }

        $result['technical_breakdown'][] = [
            'rule' => 'Domain Structure Check',
            'status' => $result['suspicious_domain'] ? 'FAIL' : 'PASS',
            'detail' => "Domain: {$this->domain} | Hyphens: {$hyphenCount}",
            'score_impact' => $result['suspicious_domain'] ? '+10~20' : '+0'
        ];
    }

    private function checkPhishingKeywords(&$result) {
        $urlLower = strtolower($this->url);
        $foundKeywords = [];
        $totalWeight = 0;

        // Get keywords from database
        $stmt = $this->db->query("SELECT keyword, weight, category FROM phishing_keywords ORDER BY weight DESC");
        if ($stmt) {
            while ($row = $stmt->fetch_assoc()) {
                if (strpos($urlLower, strtolower($row['keyword'])) !== false) {
                    $foundKeywords[] = ['word' => $row['keyword'], 'weight' => $row['weight'], 'category' => $row['category']];
                    $totalWeight += $row['weight'];
                }
            }
        }

        if (!empty($foundKeywords)) {
            $penalty = min(30, $totalWeight);
            $kwList = array_column($foundKeywords, 'word');
            $this->addFlag('PHISHING_KEYWORDS', 'Contains phishing keywords: ' . implode(', ', $kwList), $penalty, 'danger');
            $this->riskScore += $penalty;
            $result['has_phishing_keywords'] = true;
            $result['detected_keywords'] = $foundKeywords;
            $status = 'FAIL';
        } else {
            $penalty = 0;
            $status = 'PASS';
        }

        $result['technical_breakdown'][] = [
            'rule' => 'Keyword Analysis',
            'status' => $status,
            'detail' => empty($foundKeywords) ? 'No phishing keywords detected' : 'Found: ' . implode(', ', array_column($foundKeywords, 'word')),
            'score_impact' => "+{$penalty}"
        ];
    }

    private function checkBlacklist(&$result) {
        if (empty($this->domain)) return;

        // Strip www
        $checkDomain = preg_replace('/^www\./', '', $this->domain);

        $stmt = $this->db->prepare("SELECT domain, reason, severity FROM blacklist WHERE domain = ?");
        if ($stmt) {
            $stmt->bind_param('s', $checkDomain);
            $stmt->execute();
            $res = $stmt->get_result();
            $blacklisted = $res->fetch_assoc();

            if ($blacklisted) {
                $penalty = 50;
                $this->addFlag('BLACKLISTED', 'Domain is on known phishing blacklist: ' . $blacklisted['reason'], $penalty, 'critical');
                $this->riskScore += $penalty;
                $result['blacklisted'] = true;
                $result['technical_breakdown'][] = [
                    'rule' => 'Blacklist Check',
                    'status' => 'CRITICAL',
                    'detail' => 'Domain matches known phishing database: ' . $blacklisted['reason'],
                    'score_impact' => "+{$penalty}"
                ];
            } else {
                $result['blacklisted'] = false;
                $result['technical_breakdown'][] = [
                    'rule' => 'Blacklist Check',
                    'status' => 'PASS',
                    'detail' => 'Domain not found in phishing blacklist',
                    'score_impact' => '+0'
                ];
            }
            $stmt->close();
        }
    }

    private function checkSubdomainDepth(&$result) {
        if (empty($this->domain)) return;
        $parts = explode('.', $this->domain);
        $depth = max(0, count($parts) - 2);

        if ($depth >= 3) {
            $penalty = 15;
            $this->addFlag('DEEP_SUBDOMAIN', "Excessive subdomain depth ({$depth} levels)", $penalty, 'danger');
            $this->riskScore += $penalty;
            $status = 'FAIL';
        } elseif ($depth >= 2) {
            $penalty = 5;
            $this->addFlag('SUBDOMAIN_WARNING', "Multiple subdomain levels ({$depth})", $penalty, 'warning');
            $this->riskScore += $penalty;
            $status = 'WARNING';
        } else {
            $penalty = 0;
            $status = 'PASS';
        }

        $result['technical_breakdown'][] = [
            'rule' => 'Subdomain Depth',
            'status' => $status,
            'detail' => "Subdomain nesting: {$depth} levels",
            'score_impact' => "+{$penalty}"
        ];
    }

    private function checkSuspiciousChars(&$result) {
        // Check for @, %, encoded chars, double slashes
        $suspicious = [];
        if (strpos($this->url, '@') !== false) $suspicious[] = '@-symbol (can hide real destination)';
        if (substr_count($this->url, '//') > 1) $suspicious[] = 'Multiple double slashes';
        if (preg_match('/%[0-9a-fA-F]{2}/', $this->url) && substr_count($this->url, '%') > 3) {
            $suspicious[] = 'Heavy URL encoding (obfuscation)';
        }

        if (!empty($suspicious)) {
            $penalty = 15 * count($suspicious);
            $penalty = min(30, $penalty);
            $this->addFlag('SUSPICIOUS_CHARS', 'Suspicious characters: ' . implode('; ', $suspicious), $penalty, 'danger');
            $this->riskScore += $penalty;
            $status = 'FAIL';
        } else {
            $penalty = 0;
            $status = 'PASS';
        }

        $result['technical_breakdown'][] = [
            'rule' => 'Character Analysis',
            'status' => $status,
            'detail' => empty($suspicious) ? 'No suspicious characters found' : implode('; ', $suspicious),
            'score_impact' => "+{$penalty}"
        ];
    }

    private function checkTLD(&$result) {
        $tldFound = '';
        foreach ($this->suspiciousTLDs as $tld) {
            if (str_ends_with($this->domain, $tld)) {
                $tldFound = $tld;
                break;
            }
        }

        if ($tldFound) {
            $penalty = 15;
            $this->addFlag('SUSPICIOUS_TLD', "Uses high-risk TLD: {$tldFound}", $penalty, 'warning');
            $this->riskScore += $penalty;
            $status = 'FAIL';
        } else {
            $penalty = 0;
            $status = 'PASS';
        }

        $result['technical_breakdown'][] = [
            'rule' => 'TLD Analysis',
            'status' => $status,
            'detail' => $tldFound ? "High-risk TLD detected: {$tldFound}" : 'TLD appears legitimate',
            'score_impact' => "+{$penalty}"
        ];
    }

    private function checkBrandSpoofing(&$result) {
        if (empty($this->domain)) return;

        $domainLower = strtolower($this->domain);
        $spoofedBrand = '';

        foreach ($this->trustedBrands as $brand) {
            // Legitimate: brand.com, brand.net etc
            $parts = explode('.', $domainLower);
            $mainPart = implode('.', array_slice($parts, -2));
            
            // Check if brand is in subdomain (not main domain) - possible spoofing
            if (strpos($domainLower, $brand) !== false) {
                // Check if it's NOT the actual brand domain
                $brandPattern = '/^(www\.)?' . preg_quote($brand, '/') . '\.(com|net|org|co|io|gov)$/i';
                if (!preg_match($brandPattern, $domainLower)) {
                    $spoofedBrand = $brand;
                    break;
                }
            }
        }

        if ($spoofedBrand) {
            $penalty = 25;
            $this->addFlag('BRAND_SPOOFING', "Possible brand impersonation detected: '{$spoofedBrand}'", $penalty, 'critical');
            $this->riskScore += $penalty;
            $status = 'CRITICAL';
        } else {
            $penalty = 0;
            $status = 'PASS';
        }

        $result['technical_breakdown'][] = [
            'rule' => 'Brand Spoofing Detection',
            'status' => $status,
            'detail' => $spoofedBrand ? "Brand '{$spoofedBrand}' found in non-official domain" : 'No brand impersonation detected',
            'score_impact' => "+{$penalty}"
        ];
    }

    private function checkRedirectPatterns(&$result) {
        $patterns = ['redirect', 'redir', 'goto', 'url=', 'link=', 'return=', 'returnurl', 'forward='];
        $found = [];
        $urlLower = strtolower($this->url);

        foreach ($patterns as $p) {
            if (strpos($urlLower, $p) !== false) {
                $found[] = $p;
            }
        }

        if (!empty($found)) {
            $penalty = 10;
            $this->addFlag('REDIRECT_PATTERN', 'Contains open redirect indicators: ' . implode(', ', $found), $penalty, 'warning');
            $this->riskScore += $penalty;
            $status = 'WARNING';
        } else {
            $penalty = 0;
            $status = 'PASS';
        }

        $result['technical_breakdown'][] = [
            'rule' => 'Redirect Pattern Check',
            'status' => $status,
            'detail' => empty($found) ? 'No redirect patterns found' : 'Redirect indicators: ' . implode(', ', $found),
            'score_impact' => "+{$penalty}"
        ];
    }

    private function checkPreviousHistory(&$result) {
        $hash = hash('sha256', $this->url);
        
        $stmt = $this->db->prepare("SELECT id, risk_level, risk_score, check_count FROM url_checks WHERE url_hash = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('s', $hash);
            $stmt->execute();
            $res = $stmt->get_result();
            $prev = $res->fetch_assoc();

            if ($prev) {
                $result['previously_flagged'] = true;
                $result['previous_risk_level'] = $prev['risk_level'];
                $result['check_count'] = $prev['check_count'] + 1;

                if ($prev['risk_level'] === 'high') {
                    $penalty = 20;
                    $this->addFlag('PREVIOUSLY_HIGH_RISK', "URL was previously flagged as HIGH RISK", $penalty, 'critical');
                    $this->riskScore += $penalty;
                } elseif ($prev['risk_level'] === 'medium') {
                    $penalty = 10;
                    $this->addFlag('PREVIOUSLY_FLAGGED', "URL was previously flagged as MEDIUM RISK", $penalty, 'warning');
                    $this->riskScore += $penalty;
                }
            }
            $stmt->close();
        }
    }

    private function getCommunityReports() {
        $hash = hash('sha256', $this->url);
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM community_reports WHERE url_hash = ?");
        if ($stmt) {
            $stmt->bind_param('s', $hash);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();
            return (int)$row['total'];
        }
        return 0;
    }

    private function addFlag($code, $description, $weight, $severity) {
        $this->flags[] = [
            'code' => $code,
            'description' => $description,
            'weight' => $weight,
            'severity' => $severity
        ];
    }

    private function saveToDatabase(&$result) {
        $hash = hash('sha256', $this->url);
        $url = substr($this->url, 0, 2000);
        $domain = substr($this->domain, 0, 255);
        $protocol = substr($result['protocol'], 0, 10);
        $flagsJson = json_encode($result['flags']);
        $riskScore = (int)$result['risk_score'];
        $riskLevel = $result['risk_level'];
        $isHttps = $result['is_https'] ? 1 : 0;
        $urlLength = $result['url_length'];
        $paramCount = $result['param_count'];
        $usesIp = $result['uses_ip'] ? 1 : 0;
        $hasPkw = $result['has_phishing_keywords'] ? 1 : 0;
        $suspDomain = $result['suspicious_domain'] ? 1 : 0;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Check if exists
        $checkStmt = $this->db->prepare("SELECT id FROM url_checks WHERE url_hash = ?");
        if ($checkStmt) {
            $checkStmt->bind_param('s', $hash);
            $checkStmt->execute();
            $checkRes = $checkStmt->get_result();
            $exists = $checkRes->fetch_assoc();
            $checkStmt->close();

            if ($exists) {
                $upd = $this->db->prepare(
                    "UPDATE url_checks SET risk_score=?, risk_level=?, flags_triggered=?, check_count=check_count+1, last_checked=NOW() WHERE url_hash=?"
                );
                if ($upd) {
                    $upd->bind_param('isss', $riskScore, $riskLevel, $flagsJson, $hash);
                    $upd->execute();
                    $upd->close();
                }
            } else {
                $ins = $this->db->prepare(
                    "INSERT INTO url_checks (url, url_hash, domain, protocol, risk_score, risk_level, flags_triggered, is_https, url_length, param_count, uses_ip, has_phishing_keywords, suspicious_domain, ip_address) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                if ($ins) {
                    $ins->bind_param('ssssissiiiiiis',
                        $url, $hash, $domain, $protocol, $riskScore, $riskLevel,
                        $flagsJson, $isHttps, $urlLength, $paramCount, $usesIp,
                        $hasPkw, $suspDomain, $ip
                    );
                    $ins->execute();
                    $ins->close();
                }
            }
        }
    }
}

// Community Report Handler
function submitCommunityReport($url, $reason = '') {
    $db = Database::getInstance();
    $hash = hash('sha256', $url);
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $url = substr($url, 0, 2000);
    $reason = substr($reason, 0, 255);

    // Prevent duplicate reports from same IP
    $check = $db->prepare("SELECT id FROM community_reports WHERE url_hash = ? AND reporter_ip = ? AND reported_at > NOW() - INTERVAL 24 HOUR");
    if ($check) {
        $check->bind_param('ss', $hash, $ip);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => 'You have already reported this URL in the last 24 hours.'];
        }
        $check->close();
    }

    $stmt = $db->prepare("INSERT INTO community_reports (url_hash, url, reporter_ip, report_reason) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param('ssss', $hash, $url, $ip, $reason);
        $stmt->execute();
        $stmt->close();
        return ['success' => true, 'message' => 'Report submitted. Thank you for helping the community.'];
    }
    return ['success' => false, 'message' => 'Failed to submit report.'];
}

// Get URL history
function getURLHistory($limit = 20) {
    $db = Database::getInstance();
    $result = $db->query("
        SELECT uc.url, uc.domain, uc.risk_level, uc.risk_score, uc.check_count, uc.last_checked,
               COUNT(cr.id) as report_count
        FROM url_checks uc
        LEFT JOIN community_reports cr ON uc.url_hash = cr.url_hash
        GROUP BY uc.id
        ORDER BY uc.last_checked DESC
        LIMIT " . (int)$limit
    );
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

// Get stats
function getDashboardStats() {
    $db = Database::getInstance();
    $stats = [];

    $r = $db->query("SELECT COUNT(*) as total FROM url_checks");
    $stats['total_checked'] = $r ? $r->fetch_assoc()['total'] : 0;

    $r = $db->query("SELECT COUNT(*) as total FROM url_checks WHERE risk_level = 'high'");
    $stats['high_risk'] = $r ? $r->fetch_assoc()['total'] : 0;

    $r = $db->query("SELECT COUNT(*) as total FROM community_reports");
    $stats['total_reports'] = $r ? $r->fetch_assoc()['total'] : 0;

    $r = $db->query("SELECT COUNT(*) as total FROM url_checks WHERE check_count > 1");
    $stats['repeated_urls'] = $r ? $r->fetch_assoc()['total'] : 0;

    return $stats;
}
?>