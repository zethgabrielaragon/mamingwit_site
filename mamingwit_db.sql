-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2026 at 08:29 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mamingwit_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
  `id` int(11) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `reason` varchar(500) DEFAULT NULL,
  `severity` enum('medium','high','critical') DEFAULT 'high',
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blacklist`
--

INSERT INTO `blacklist` (`id`, `domain`, `reason`, `severity`, `added_at`) VALUES
(1, 'paypa1.com', 'PayPal phishing clone', 'critical', '2026-02-18 00:06:34'),
(2, 'arnazon.com', 'Amazon phishing clone', 'critical', '2026-02-18 00:06:34'),
(3, 'g00gle.com', 'Google phishing clone', 'critical', '2026-02-18 00:06:34'),
(4, 'microsooft.com', 'Microsoft phishing clone', 'critical', '2026-02-18 00:06:34'),
(5, 'faceb00k.com', 'Facebook phishing clone', 'critical', '2026-02-18 00:06:34'),
(6, 'netfl1x.com', 'Netflix phishing clone', 'high', '2026-02-18 00:06:34'),
(7, 'appleid-verify.com', 'Apple ID phishing', 'critical', '2026-02-18 00:06:34'),
(8, 'secure-bankofamerica.com', 'Bank of America phishing', 'critical', '2026-02-18 00:06:34'),
(9, 'login-paypal-secure.com', 'PayPal phishing', 'critical', '2026-02-18 00:06:34'),
(10, 'verify-account-google.net', 'Google account phishing', 'critical', '2026-02-18 00:06:34'),
(11, 'secure-chase-verify.com', 'Chase Bank phishing - fake login portal', 'critical', '2026-03-22 20:14:13'),
(12, 'wellsfargo-secure-login.net', 'Wells Fargo phishing clone', 'critical', '2026-03-22 20:14:13'),
(13, 'bankofamerica-alert.com', 'Bank of America credential harvesting', 'critical', '2026-03-22 20:14:13'),
(14, 'paypal-resolution-center.tk', 'PayPal phishing on free TLD', 'critical', '2026-03-22 20:14:13'),
(15, 'paypa1-secure.com', 'PayPal typosquatting (1 instead of l)', 'critical', '2026-03-22 20:14:13'),
(16, 'paypal-community.ml', 'PayPal phishing on suspicious TLD', 'critical', '2026-03-22 20:14:13'),
(17, 'venmo-payment-pending.com', 'Venmo fake payment notification', 'high', '2026-03-22 20:14:13'),
(18, 'coinbase-support-wallet.net', 'Coinbase cryptocurrency phishing', 'critical', '2026-03-22 20:14:13'),
(19, 'blockchain-wallet-recovery.com', 'Crypto wallet credential theft', 'critical', '2026-03-22 20:14:13'),
(20, 'accounts-google-verify.com', 'Google account phishing', 'critical', '2026-03-22 20:14:13'),
(21, 'g00gle-accounts.com', 'Google typosquatting (0 instead of o)', 'critical', '2026-03-22 20:14:13'),
(22, 'microsoft-account-team.net', 'Microsoft phishing campaign', 'critical', '2026-03-22 20:14:13'),
(23, 'appleid-unlock.com', 'Apple ID phishing', 'critical', '2026-03-22 20:14:13'),
(24, 'apple-support-icloud.net', 'iCloud credential harvesting', 'critical', '2026-03-22 20:14:13'),
(25, 'amazon-account-locked.com', 'Amazon fake security alert', 'critical', '2026-03-22 20:14:13'),
(26, 'arnazon-prime.com', 'Amazon typosquatting (rn instead of m)', 'critical', '2026-03-22 20:14:13'),
(27, 'netflix-billing-update.com', 'Netflix payment scam', 'high', '2026-03-22 20:14:13'),
(28, 'netflix-membership-suspended.net', 'Netflix phishing urgency tactic', 'high', '2026-03-22 20:14:13'),
(29, 'dropbox-file-share.tk', 'Dropbox phishing on free TLD', 'high', '2026-03-22 20:14:13'),
(30, 'facebook-security-team.com', 'Facebook credential theft', 'high', '2026-03-22 20:14:13'),
(31, 'instagram-verify-badge.net', 'Instagram verification scam', 'high', '2026-03-22 20:14:13'),
(32, 'twitter-support-appeal.com', 'Twitter/X phishing', 'high', '2026-03-22 20:14:13'),
(33, 'linkedin-job-offer.tk', 'LinkedIn malicious job posting', 'medium', '2026-03-22 20:14:13'),
(34, 'whatsapp-web-login.ml', 'WhatsApp Web phishing', 'high', '2026-03-22 20:14:13'),
(35, 'dhl-tracking-delivery.com', 'DHL fake tracking phishing', 'high', '2026-03-22 20:14:13'),
(36, 'fedex-package-redelivery.net', 'FedEx delivery scam', 'high', '2026-03-22 20:14:13'),
(37, 'usps-redelivery-schedule.com', 'USPS phishing', 'high', '2026-03-22 20:14:13'),
(38, 'ups-tracking-help.tk', 'UPS tracking scam', 'high', '2026-03-22 20:14:13'),
(39, 'ebay-seller-verification.com', 'eBay seller phishing', 'medium', '2026-03-22 20:14:13'),
(40, 'irs-refund-processing.com', 'IRS tax refund scam', 'critical', '2026-03-22 20:14:13'),
(41, 'irs-gov-refund.net', 'IRS impersonation', 'critical', '2026-03-22 20:14:13'),
(42, 'socialsecurity-benefits.com', 'SSA phishing', 'critical', '2026-03-22 20:14:13'),
(43, 'docusign-document-ready.com', 'DocuSign phishing', 'high', '2026-03-22 20:14:13'),
(44, 'office365-login-portal.net', 'Office 365 credential harvesting', 'critical', '2026-03-22 20:14:13'),
(45, 'sharepoint-file-shared.com', 'SharePoint phishing', 'high', '2026-03-22 20:14:13'),
(46, 'adobe-document-signature.tk', 'Adobe Sign phishing', 'high', '2026-03-22 20:14:13'),
(47, 'update-flash-player.com', 'Fake Flash Player update malware', 'critical', '2026-03-22 20:14:13'),
(48, 'download-chrome-update.net', 'Fake Chrome update', 'critical', '2026-03-22 20:14:13'),
(49, 'windows-defender-alert.com', 'Fake antivirus scam', 'critical', '2026-03-22 20:14:13'),
(50, 'java-runtime-update.tk', 'Fake Java update malware', 'critical', '2026-03-22 20:14:13'),
(51, 'verify-account-now.com', 'Generic verify urgency scam', 'high', '2026-03-22 20:14:13'),
(52, 'secure-login-portal.tk', 'Generic login phishing', 'high', '2026-03-22 20:14:13'),
(53, 'account-suspended-appeal.ml', 'Generic suspension scam', 'high', '2026-03-22 20:14:13'),
(54, 'claim-your-reward.com', 'Prize/reward scam', 'medium', '2026-03-22 20:14:13'),
(55, 'limited-time-offer-now.net', 'Urgency-based scam', 'medium', '2026-03-22 20:14:13'),
(56, 'customer-service-support.tk', 'Generic support impersonation', 'medium', '2026-03-22 20:14:13'),
(57, 'billing-information-update.com', 'Payment update scam', 'high', '2026-03-22 20:14:13'),
(58, 'password-reset-required.net', 'Fake password reset', 'high', '2026-03-22 20:14:13'),
(59, 'security-alert-action.com', 'Fake security notification', 'high', '2026-03-22 20:14:13');

-- --------------------------------------------------------

--
-- Table structure for table `community_reports`
--

CREATE TABLE `community_reports` (
  `id` int(11) NOT NULL,
  `url_hash` varchar(64) NOT NULL,
  `url` text NOT NULL,
  `reporter_ip` varchar(45) DEFAULT NULL,
  `report_reason` varchar(255) DEFAULT NULL,
  `reported_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `community_reports`
--

INSERT INTO `community_reports` (`id`, `url_hash`, `url`, `reporter_ip`, `report_reason`, `reported_at`) VALUES
(6, '1c0a1b01048fc1b166bed38575c692dbc72e9ab85911caf165efd65b86fb6a5e', 'http://paypa1-verify-account.tk/login?redirect=account&update=now&confirm=password', '::1', 'Phishing - credential theft attempt', '2026-03-23 00:01:43');

-- --------------------------------------------------------

--
-- Table structure for table `phishing_keywords`
--

CREATE TABLE `phishing_keywords` (
  `id` int(11) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `weight` int(11) DEFAULT 10,
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phishing_keywords`
--

INSERT INTO `phishing_keywords` (`id`, `keyword`, `weight`, `category`) VALUES
(1, 'login', 8, 'auth'),
(2, 'verify', 10, 'auth'),
(3, 'update', 7, 'auth'),
(4, 'secure', 6, 'security'),
(5, 'account', 5, 'auth'),
(6, 'banking', 9, 'finance'),
(7, 'password', 10, 'auth'),
(8, 'confirm', 8, 'auth'),
(9, 'free', 5, 'lure'),
(10, 'winner', 7, 'lure'),
(11, 'prize', 7, 'lure'),
(12, 'urgent', 8, 'pressure'),
(13, 'suspended', 9, 'pressure'),
(14, 'expire', 8, 'pressure'),
(15, 'click', 4, 'action'),
(16, 'claim', 6, 'lure'),
(17, 'paypal', 8, 'brand'),
(18, 'amazon', 6, 'brand'),
(19, 'netflix', 6, 'brand'),
(20, 'apple', 5, 'brand'),
(21, 'google', 5, 'brand'),
(22, 'microsoft', 5, 'brand'),
(23, 'webscr', 10, 'tech'),
(24, 'signin', 8, 'auth'),
(25, 'credential', 9, 'auth'),
(127, 'locked', 9, 'pressure'),
(128, 'unusual', 9, 'pressure'),
(129, 'validate', 8, 'auth'),
(130, 'restore', 8, 'auth'),
(131, 'reactivate', 9, 'pressure'),
(132, 'expires', 8, 'pressure'),
(133, 'expired', 9, 'pressure'),
(134, 'immediately', 9, 'pressure'),
(135, 'action required', 10, 'pressure'),
(136, 'act now', 9, 'pressure'),
(137, 'last chance', 8, 'lure'),
(138, 'sign-in', 8, 'auth'),
(139, 'username', 8, 'auth'),
(140, 'authentication', 7, 'auth'),
(141, 'protected', 5, 'security'),
(142, 'invoice', 7, 'finance'),
(143, 'refund', 9, 'finance'),
(144, 'transaction', 7, 'finance'),
(145, 'wallet', 8, 'finance'),
(146, 'bank', 7, 'finance'),
(147, 'card', 7, 'finance'),
(148, 'congratulations', 7, 'lure'),
(149, 'gift', 6, 'lure'),
(150, 'bonus', 6, 'lure'),
(151, 'promotion', 5, 'lure'),
(152, 'discount', 4, 'lure'),
(153, 'coupon', 4, 'lure'),
(154, 'upgrade', 6, 'action'),
(155, 'download', 6, 'action'),
(156, 'install', 6, 'action'),
(157, 'activate', 7, 'action'),
(158, 'enable', 6, 'action'),
(159, 'chase', 7, 'brand'),
(160, 'wellsfargo', 7, 'brand'),
(161, 'bankofamerica', 7, 'brand'),
(162, 'cgi-bin', 8, 'tech'),
(163, 'account-recovery', 9, 'tech'),
(164, 'reset-password', 9, 'tech'),
(165, 'oauth', 7, 'tech'),
(166, 'sso', 6, 'tech'),
(167, 'portal', 5, 'tech'),
(168, 'submit', 6, 'action'),
(169, 'complete', 6, 'action'),
(170, 'respond', 7, 'pressure'),
(171, 'reply', 6, 'pressure'),
(172, 'notification', 5, 'alert'),
(173, 'alert', 7, 'alert'),
(174, 'warning', 8, 'alert'),
(175, 'important', 7, 'alert'),
(176, 'document', 5, 'lure'),
(177, 'file', 4, 'lure'),
(178, 'attachment', 6, 'lure'),
(179, 'shared', 5, 'lure'),
(180, 'receipt', 6, 'finance'),
(181, 'statement', 6, 'finance'),
(182, 'blocked', 9, 'pressure'),
(183, 'disabled', 9, 'pressure'),
(184, 'compromised', 9, 'pressure'),
(185, 'unauthorized', 8, 'pressure'),
(186, 'detected', 7, 'alert'),
(187, 'activity', 5, 'alert'),
(188, 'breach', 9, 'alert'),
(189, 'verify now', 10, 'pressure'),
(190, 'click here', 6, 'action'),
(191, 'sign in', 7, 'auth'),
(192, 'log in', 7, 'auth'),
(193, 'reset now', 8, 'action'),
(194, 'update now', 8, 'action'),
(195, 'confirm identity', 9, 'auth'),
(196, 'unlock account', 9, 'pressure');

-- --------------------------------------------------------

--
-- Table structure for table `url_checks`
--

CREATE TABLE `url_checks` (
  `id` int(11) NOT NULL,
  `url` text NOT NULL,
  `url_hash` varchar(64) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `protocol` varchar(10) DEFAULT NULL,
  `risk_score` int(11) DEFAULT 0,
  `risk_level` enum('low','medium','high') DEFAULT 'low',
  `flags_triggered` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`flags_triggered`)),
  `is_https` tinyint(1) DEFAULT 0,
  `url_length` int(11) DEFAULT 0,
  `param_count` int(11) DEFAULT 0,
  `uses_ip` tinyint(1) DEFAULT 0,
  `has_phishing_keywords` tinyint(1) DEFAULT 0,
  `suspicious_domain` tinyint(1) DEFAULT 0,
  `check_count` int(11) DEFAULT 1,
  `first_seen` datetime DEFAULT current_timestamp(),
  `last_checked` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `url_checks`
--

INSERT INTO `url_checks` (`id`, `url`, `url_hash`, `domain`, `protocol`, `risk_score`, `risk_level`, `flags_triggered`, `is_https`, `url_length`, `param_count`, `uses_ip`, `has_phishing_keywords`, `suspicious_domain`, `check_count`, `first_seen`, `last_checked`, `ip_address`) VALUES
(14, 'https://www.google.com', 'ac6bb669e40e44a8d9f8f0c94dfc63734049dcf6219aac77f02edf94b9162c09', 'www.google.com', 'https', 5, 'low', '[{\"code\":\"PHISHING_KEYWORDS\",\"description\":\"Contains phishing keywords: google\",\"weight\":5,\"severity\":\"danger\"}]', 1, 22, 0, 0, 1, 0, 1, '2026-03-22 23:58:18', '2026-03-22 23:58:18', '::1'),
(15, 'http://paypa1-verify-account.tk/login?redirect=account&update=now&confirm=password', '1c0a1b01048fc1b166bed38575c692dbc72e9ab85911caf165efd65b86fb6a5e', 'paypa1-verify-account.tk', 'http', 90, 'high', '[{\"code\":\"NO_HTTPS\",\"description\":\"Protocol is not HTTPS\",\"weight\":15,\"severity\":\"warning\"},{\"code\":\"PHISHING_KEYWORDS\",\"description\":\"Contains phishing keywords: verify, password, confirm, login, update, account\",\"weight\":30,\"severity\":\"danger\"},{\"code\":\"SUSPICIOUS_TLD\",\"description\":\"Uses high-risk TLD: .tk\",\"weight\":15,\"severity\":\"warning\"},{\"code\":\"REDIRECT_PATTERN\",\"description\":\"Contains open redirect indicators: redirect, redir\",\"weight\":10,\"severity\":\"warning\"},{\"code\":\"PREVIOUSLY_HIGH_RISK\",\"description\":\"URL was previously flagged as HIGH RISK\",\"weight\":20,\"severity\":\"critical\"}]', 0, 82, 3, 0, 1, 0, 2, '2026-03-23 00:00:12', '2026-03-23 00:00:54', '::1'),
(16, 'http://192.168.1.1/secure/banking/verify', 'ede2c37af3503723b49e889ae201cb7815bf4b7e053c21c363ead9d7cf28d789', '192.168.1.1', 'http', 75, 'high', '[{\"code\":\"NO_HTTPS\",\"description\":\"Protocol is not HTTPS\",\"weight\":15,\"severity\":\"warning\"},{\"code\":\"IP_ADDRESS\",\"description\":\"Uses raw IP address instead of domain name\",\"weight\":25,\"severity\":\"danger\"},{\"code\":\"PHISHING_KEYWORDS\",\"description\":\"Contains phishing keywords: verify, banking, bank, secure\",\"weight\":30,\"severity\":\"danger\"},{\"code\":\"SUBDOMAIN_WARNING\",\"description\":\"Multiple subdomain levels (2)\",\"weight\":5,\"severity\":\"warning\"}]', 0, 40, 0, 1, 1, 0, 1, '2026-03-23 00:15:22', '2026-03-23 00:15:22', '::1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `domain` (`domain`),
  ADD KEY `idx_domain` (`domain`);

--
-- Indexes for table `community_reports`
--
ALTER TABLE `community_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_url_hash` (`url_hash`);

--
-- Indexes for table `phishing_keywords`
--
ALTER TABLE `phishing_keywords`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `keyword` (`keyword`);

--
-- Indexes for table `url_checks`
--
ALTER TABLE `url_checks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_url_hash` (`url_hash`),
  ADD KEY `idx_risk_level` (`risk_level`),
  ADD KEY `idx_last_checked` (`last_checked`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blacklist`
--
ALTER TABLE `blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `community_reports`
--
ALTER TABLE `community_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `phishing_keywords`
--
ALTER TABLE `phishing_keywords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT for table `url_checks`
--
ALTER TABLE `url_checks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
