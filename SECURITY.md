# Security Policy

## Supported Versions

| Version | Supported |
| ------- | --------- |
| 1.x.x   | Yes       |
| < 1.0   | No        |


## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub Issues.**

Send a private email to: [datweb07@gmail.com](mailto:datweb07@gmail.com)

Include the following in your report:

- **Description**: A clear description of the vulnerability.
- **Steps to Reproduce**: Detailed, reproducible steps.
- **Impact**: What an attacker could achieve by exploiting this.
- **Affected Components**: Which files, routes, or features are affected.
- **Suggested Fix**: Optional, but appreciated.

### Response Timeline

| Stage | Timeframe |
|---|---|
| Initial acknowledgement | Within 48 hours |
| Validation & investigation | Within 7 days |
| Fix & coordinated disclosure | As soon as possible after validation |


## Security Architecture

### Credentials & Secrets

- All API keys, secrets, and passwords are stored in `.env` files only.
- `.env` is listed in `.gitignore` and is **never committed** to version control.
- `.env.example` contains only placeholder values and is safe to commit.
- The following secrets are managed via environment variables:

| Variable | Purpose |
|---|---|
| `DB_PASSWORD` | MySQL database password |
| `CLOUDINARY_API_KEY` / `CLOUDINARY_API_SECRET` | Image upload credentials |
| `MAIL_PASSWORD` | Gmail App Password for SMTP |
| `SUPABASE_ANON_KEY` / `SUPABASE_JWT_SECRET` | Supabase Auth credentials |
| `VNPAY_TMN_CODE` / `VNPAY_HASH_SECRET` | VNPay payment signing |
| `PAYPAL_CLIENT_ID` / `PAYPAL_SECRET` | PayPal API credentials |
| `RECAPTCHA_SITE_KEY` / `RECAPTCHA_SECRET_KEY` | Google reCAPTCHA keys |

### Authentication

- PHP sessions are used for user authentication with a 2-hour timeout.
- Passwords are hashed using `sha1()` (legacy — migration to `password_hash()` is recommended for production).
- Google OAuth is handled via Supabase Auth — no Google credentials are stored server-side.
- Admin routes are protected by `AdminMiddleware` which checks `loai_tai_khoan === 'ADMIN'`.
- Client-only routes are protected by `AuthMiddleware` which checks `loai_tai_khoan === 'MEMBER'`.

### Payment Security

- VNPay callbacks are verified using HMAC-SHA512 signature validation before processing.
- PayPal payments use OAuth2 access tokens fetched server-side — client ID and secret are never exposed to the browser.
- VietQR is a stateless QR generation service — no sensitive data is transmitted.
- All payment gateway credentials are sandbox/test credentials in development.

### Input Validation

- User inputs in SQL queries use `addslashes()` or integer casting.
- All output to HTML is escaped with `htmlspecialchars()`.
- File uploads are validated for MIME type and size before processing.
- reCAPTCHA v2 is used on login and registration forms to prevent automated attacks.

### HTTPS

- In production, all traffic should be served over HTTPS.
- Payment gateway callbacks (VNPay IPN) require a publicly accessible HTTPS URL.
- Supabase OAuth redirect URLs must use HTTPS in production.


## Security Best Practices for Contributors

- Never hardcode credentials, tokens, or secrets in source code.
- Never log sensitive data (passwords, full card numbers, tokens) — mask or omit them.
- Always validate and sanitize user input before using it in SQL, file paths, or HTML output.
- Keep dependencies up to date — run `composer update` periodically and review changelogs.
- Use `hash_equals()` for timing-safe string comparison when verifying signatures or tokens.
- Set `CURLOPT_SSL_VERIFYPEER` to `true` in production (it is set to `false` in some places for local development only).

## Acknowledgements

We appreciate responsible disclosure from the security community. Reporters of valid, confirmed vulnerabilities will be acknowledged in release notes (unless they prefer to remain anonymous).
