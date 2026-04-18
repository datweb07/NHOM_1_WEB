# Third-Party Notices

This project uses third-party libraries, services, and assets. Below is a complete list of all dependencies, their licenses, copyright information, and setup instructions where applicable.

---

## PHP Dependencies (via Composer)

### 1. PHPDotenv
- **Package**: `vlucas/phpdotenv`
- **Version**: ^5.6
- **License**: BSD-3-Clause
- **Copyright**: Copyright (c) 2013, Vance Lucas
- **Repository**: https://github.com/vlucas/phpdotenv
- **Purpose**: Loads environment variables from `.env` file into `getenv()`, `$_ENV`, and `$_SERVER`
- **License Text**: https://github.com/vlucas/phpdotenv/blob/master/LICENSE

### 2. Cloudinary PHP SDK
- **Package**: `cloudinary/cloudinary_php`
- **Version**: ^3.1
- **License**: MIT License
- **Copyright**: Copyright (c) 2012-2024 Cloudinary
- **Repository**: https://github.com/cloudinary/cloudinary_php
- **Purpose**: Cloud-based image upload, storage, transformation, and delivery
- **License Text**: https://github.com/cloudinary/cloudinary_php/blob/master/LICENSE

### 3. PHPMailer (bundled)
- **Source**: Bundled directly in `app/services/mailer/`
- **Version**: 6.x
- **License**: LGPL-2.1
- **Copyright**: Copyright (c) 2012-2024 PHPMailer Contributors
- **Repository**: https://github.com/PHPMailer/PHPMailer
- **Purpose**: Sending transactional emails via SMTP (verification, password reset, order notifications)
- **License Text**: https://github.com/PHPMailer/PHPMailer/blob/master/LICENSE

---

## Frontend Libraries (via CDN)

### 4. Bootstrap
- **Version**: 5.3.x
- **License**: MIT License
- **Copyright**: Copyright (c) 2011-2024 The Bootstrap Authors
- **CDN**: https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/
- **Website**: https://getbootstrap.com/
- **Purpose**: Responsive CSS/JS framework for building modern web interfaces
- **License Text**: https://github.com/twbs/bootstrap/blob/main/LICENSE

### 5. Bootstrap Icons
- **Version**: 1.11.x
- **License**: MIT License
- **Copyright**: Copyright (c) 2019-2024 The Bootstrap Authors
- **CDN**: https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/
- **Website**: https://icons.getbootstrap.com/
- **Purpose**: SVG icon library used throughout the admin and client interfaces
- **License Text**: https://github.com/twbs/icons/blob/main/LICENSE

### 6. Font Awesome
- **Version**: 6.x
- **License**:
  - Icons: CC BY 4.0 License
  - Fonts: SIL OFL 1.1 License
  - Code: MIT License
- **Copyright**: Copyright (c) Fonticons, Inc.
- **CDN**: https://cdnjs.cloudflare.com/ajax/libs/font-awesome/
- **Website**: https://fontawesome.com/
- **Purpose**: Icon library used in navigation, buttons, and UI elements
- **License Text**: https://fontawesome.com/license/free

### 7. dvhcvn (Đơn vị hành chính Việt Nam)
- **Source**: Raw JSON via GitHub CDN
- **Author**: Dao Hoang Son ([daohoangson](https://github.com/daohoangson))
- **Repository**: https://github.com/daohoangson/dvhcvn
- **CDN**: `https://raw.githubusercontent.com/daohoangson/dvhcvn/master/data/dvhcvn.json`
- **License**: MIT License
- **Purpose**: Vietnamese administrative divisions data (Province/District/Ward) used in address forms
- **License Text**: https://github.com/daohoangson/dvhcvn/blob/master/LICENSE

---

## Third-Party Payment Services

### 8. VNPay
- **Service**: Vietnamese online payment gateway
- **Website**: https://vnpay.vn/
- **Sandbox**: https://sandbox.vnpayment.vn/
- **Purpose**: Online card payment processing (domestic ATM cards, Visa, Mastercard)
- **Integration**: HMAC-SHA512 signed redirect-based payment flow
- **Sandbox Note**: VNPay does not support real refunds in sandbox mode. This project implements a **mock refund simulation** that logs the refund operation without calling the actual API.
- **Terms of Service**: https://vnpay.vn/dieu-khoan-su-dung.html
- **Developer Docs**: https://sandbox.vnpayment.vn/apis/docs/huong-dan-tich-hop/

### 9. PayPal
- **Service**: International online payment gateway
- **Website**: https://www.paypal.com/
- **Sandbox**: https://www.sandbox.paypal.com/
- **API Base (Sandbox)**: `https://api-m.sandbox.paypal.com`
- **API Base (Production)**: `https://api-m.paypal.com`
- **Purpose**: International payment processing in USD (converted from VND at ~25,000 VND/USD)
- **Integration**: OAuth2 REST API v2 — Create Order → Capture Payment flow
- **Terms of Service**: https://www.paypal.com/us/legalhub/useragreement-full
- **Developer Docs**: https://developer.paypal.com/docs/api/overview/

### 10. VietQR
- **Service**: Vietnamese bank QR code generation standard
- **Website**: https://www.vietqr.io/
- **API**: `https://img.vietqr.io/image/`
- **Purpose**: Generate QR codes for bank transfers (manual payment confirmation by admin)
- **Integration**: Stateless URL-based QR image generation — no API key required
- **Supported Banks**: All Vietnamese banks supporting NAPAS QR standard
- **Developer Docs**: https://www.vietqr.io/danh-sach-api

---

## Authentication & Backend Services

### 11. Supabase
- **Service**: Open-source Firebase alternative (Auth + Database + Storage)
- **Website**: https://supabase.com/
- **License**: Apache License 2.0
- **Purpose**: Google OAuth 2.0 authentication — token verification and user identity
- **Integration**: Supabase Auth → Google OAuth provider → JWT token verification via REST API
- **Repository**: https://github.com/supabase/supabase
- **Terms of Service**: https://supabase.com/terms
- **Privacy Policy**: https://supabase.com/privacy
- **Developer Docs**: https://supabase.com/docs/guides/auth/social-login/auth-google

### 12. Google OAuth 2.0 (via Supabase)
- **Service**: Google Identity Platform
- **Website**: https://developers.google.com/identity
- **Purpose**: "Sign in with Google" functionality — delegates to Supabase Auth
- **Terms of Service**: https://policies.google.com/terms
- **Privacy Policy**: https://policies.google.com/privacy
- **Developer Console**: https://console.cloud.google.com/

### 13. Cloudinary (Service)
- **Service**: Cloud-based media management platform
- **Website**: https://cloudinary.com/
- **Purpose**: Product image and user avatar upload, storage, optimization, and CDN delivery
- **Free Tier**: 25 credits/month (approx. 25,000 transformations)
- **Terms of Service**: https://cloudinary.com/tos
- **Privacy Policy**: https://cloudinary.com/privacy
- **Developer Docs**: https://cloudinary.com/documentation/php_integration

---

## Email Service

### 14. Gmail SMTP
- **Service**: Google Gmail SMTP relay
- **Website**: https://mail.google.com/
- **Purpose**: Sending transactional emails — account verification, password reset, order confirmations
- **Protocol**: SMTP over TLS, port 587
- **Authentication**: Google App Password (requires 2FA enabled)
- **Terms of Service**: https://policies.google.com/terms
- **Setup Guide**: https://support.google.com/accounts/answer/185833

---

## Security

### 15. Google reCAPTCHA v2
- **Service**: Google reCAPTCHA
- **Website**: https://www.google.com/recaptcha/
- **Purpose**: Bot protection on login and registration forms
- **Type**: reCAPTCHA v2 ("I'm not a robot" checkbox)
- **Terms of Service**: https://policies.google.com/terms
- **Developer Docs**: https://developers.google.com/recaptcha/docs/display
- **Admin Console**: https://www.google.com/recaptcha/admin/

---

## Optional Infrastructure

### 16. Redis
- **Service**: In-memory data structure store
- **License**: BSD-3-Clause (Redis < 7.4) / RSALv2 + SSPLv1 (Redis >= 7.4)
- **Website**: https://redis.io/
- **Purpose**: Optional caching layer for sessions, search history, and frequently accessed data
- **License Text**: https://redis.io/docs/about/license/

---

## Database

### 17. MySQL
- **Version**: 8.x
- **License**: GPL v2 (with FOSS License Exception for open-source use)
- **Copyright**: Copyright (c) 2000, 2024, Oracle Corporation and/or its affiliates
- **Website**: https://www.mysql.com/
- **Purpose**: Primary relational database for all application data
- **License Text**: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

---

## Development Tools

### 18. Composer
- **Tool**: PHP Dependency Manager
- **License**: MIT License
- **Website**: https://getcomposer.org/
- **Purpose**: Managing PHP package dependencies
- **License Text**: https://github.com/composer/composer/blob/main/LICENSE

---

## Compliance Notes

- All CDN-loaded libraries are loaded from public, reputable CDNs (jsDelivr, cdnjs, Google Fonts CDN).
- No GPL-licensed code is bundled or distributed with this project's source in a way that would trigger copyleft obligations beyond what is noted above.
- Third-party service credentials (API keys, secrets) are stored exclusively in `.env` files and are never committed to version control.
- The `.env.example` file contains only placeholder values and is safe to commit.
