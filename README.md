> Read in other languages: [Tiếng Việt](README.vi.md)

# E-Commerce Website - FPT Shop

<img width="960" height="313" alt="fpt-shop-banner" src="https://github.com/datweb07/NHOM_1_WEB/blob/main/images/fpt-shop-banner.png" />

## Project Description

The FPT Shop e-commerce website project is an online shopping platform specializing in technology products, mobile phones, laptops, tablets, and electronic accessories. The website provides a modern and convenient shopping experience with a user-friendly and easy-to-use interface.

## Key Features

### Client Features
- **Product Catalog** — Browse products with images, specs, pricing, and variants (color, storage, RAM)
- **Product Detail** — Full spec sheet, image gallery, variant selector, related products
- **Shopping Cart** — Add, update quantity, remove items, persistent across sessions
- **Search** — Real-time XML-based search with history tracking and popular keywords
- **Wishlist** — Save products for later, toggle from product cards
- **Checkout** — Multi-step checkout with address selection and payment method choice
- **Payment Methods** — VNPay (card), PayPal (international), VietQR (bank transfer QR), COD (cash on delivery)
- **Order Management** — View order history, track status, cancel pending orders
- **Product Reviews** — Star rating + comment system, one review per purchased product
- **Promotions** — Browse active promotions, apply discount/coupon codes at checkout
- **User Authentication** — Register, login, email verification, forgot/reset password
- **Google Login** — One-click sign-in via Google OAuth (Supabase Auth)
- **Profile Management** — Update name, phone, DOB, gender, avatar (Cloudinary upload)
- **Address Book** — Add/edit/delete multiple shipping addresses, set default
- **Responsive Design** — Mobile-first layout, works on all screen sizes

### Admin Features
- **Dashboard** — Revenue charts, order stats, top products, recent activity
- **Product Management** — Full CRUD for products, variants, images (Cloudinary), specifications
- **Category Management** — Hierarchical categories with featured/suggested flags
- **Order Management** — View all orders, update status, view order details
- **User Management** — View customer accounts, account details
- **Promotion Management** — Create/edit/delete promotional campaigns with product linking
- **Discount Code Management** — Generate and manage voucher/coupon codes with usage limits
- **Banner Management** — Hero banners and promotional banners with display toggle
- **Review Management** — View and moderate product reviews
- **Payment Verification** — Approve or reject manual payment confirmations (VietQR/COD)
- **Payment Gateway Health** — Monitor success/failure rates per gateway
- **Notification System** — Real-time admin notifications for new orders and payments
- **Refund Management** — Process refunds for VNPay (sandbox mock) and PayPal

## Technologies Used

| Layer | Technology |
|---|---|
| Backend | PHP 8.x, MVC architecture, custom file-based router |
| Frontend | HTML5, CSS3, JavaScript ES6+, Bootstrap 5, Font Awesome 6 |
| Database | MySQL 8.x (utf8mb4) |
| Image Storage | Cloudinary PHP SDK |
| Email | PHPMailer (bundled), Gmail SMTP |
| Auth | PHP Sessions + Supabase Auth (Google OAuth) |
| Payment | VNPay, PayPal REST API v2, VietQR, COD |
| Security | Google reCAPTCHA v2 |
| Caching | Redis (predis) |
| Env Config | vlucas/phpdotenv |

## Installation & Setup

### Prerequisites
- **PHP**: >= 8.0
- **MySQL**: >= 8.0
- **Composer**: Latest version
- **Web Server**: Apache/Nginx or PHP's built-in server

### Step 1: Clone the Repository
```bash
git clone [https://github.com/datweb07/NHOM_1_WEB.git](https://github.com/datweb07/NHOM_1_WEB.git)
cd NHOM_1_WEB
````

### Step 2: Install Dependencies

  - Install Composer at [this link](https://getcomposer.org/download/)

<!-- end list -->

```bash
composer install
```

### Step 3: Configure Environment Variables

1.  Copy the example environment file:

<!-- end list -->

```bash
cp .env.example .env
```

2.  Edit the `.env` file with your configurations:

<!-- end list -->

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:3000

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=db_fpt
DB_USERNAME=root
DB_PASSWORD=your_password

CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls

RECAPTCHA_SITE_KEY=your_recaptcha_site_key
RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key

SUPABASE_URL=https://your-project-id.supabase.co
SUPABASE_ANON_KEY=your_supabase_anon_key
SUPABASE_JWT_SECRET=your_supabase_jwt_secret

VNPAY_TMN_CODE=your_tmn_code
VNPAY_HASH_SECRET=your_hash_secret
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html

VIETQR_BANK_ID=VCB
VIETQR_ACCOUNT_NO=your_account_number
VIETQR_ACCOUNT_NAME=YOUR FULL NAME
VIETQR_TEMPLATE=compact2

PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_SECRET=your_paypal_secret
PAYPAL_MODE=sandbox
```

### Step 4: Setup Database

1.  Create a new database in MySQL:

<!-- end list -->

```sql
CREATE DATABASE db_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2.  Import the database schema:

<!-- end list -->

```bash
mysql -u root -p db_web < database/db_web.sql
```

Or use phpMyAdmin to import the `database/db_web.sql` file.

### Step 5: Configure Cloudinary (Image Storage)

1.  Sign up for a free account at [Cloudinary](https://cloudinary.com/).
2.  Get your credentials from your dashboard.
3.  Update the `.env` file with your Cloudinary credentials.

### Step 6: Configure Email Service

For Gmail SMTP:

1.  Enable 2-step verification (2FA) on your Google account.
2.  Generate an App Password: [Google App Passwords](https://myaccount.google.com/apppasswords).
3.  Enter the generated password into the `MAIL_PASSWORD` field in the `.env` file.

### Step 7: Configure Google Login Service

Configure Supabase and Google Cloud Console (Supabase & Google Cloud)

  - Supabase Setup:

<!-- end list -->

1.  Go to the [Supabase](https://supabase.com/) homepage, create a free account, and create a new project.
2.  Copy the URL of the newly created project.
3.  On the left navigation bar, go to **Authentication** → **Sign In/Providers**.
4.  Under **Auth Providers**, select Google, and set up the Google Cloud Console to fill in the required information.

<!-- end list -->

  - Google Cloud Console Setup:

<!-- end list -->

1.  Go to the [Google Cloud Console](https://console.cloud.google.com/?hl=vi) homepage.
2.  In the top-left corner next to the Google Cloud logo, click the project dropdown and select **New project**.
3.  Under **Project name**, enter a memorable name, e.g., `FPT-SHOP`, then click **Create**.
4.  Next, click the hamburger menu icon, select **APIs & Services** → **OAuth consent screen**.
5.  Under **Overview** → **Google Auth Platform not configured yet**, select **Get started**.
6.  Under **App Information**, enter a memorable **App name** (you can use `FPT-SHOP`), choose your currently logged-in email for the **User support email**, and click Next.
7.  In the **Audience** section, select **External**, then click Next.
8.  Under **Contact Information**, enter your currently logged-in email, click Next, and then click **Create**.

<!-- end list -->

  - Create Client ID:

<!-- end list -->

1.  On the left menu, select **APIs & Services** → **Credentials**.
2.  At the top, select **Create credentials** → **OAuth client ID**.
3.  For **Application type**, select **Web application**. Under Name, give it a memorable name (e.g., `Supabase Auth Client`).
4.  Under **Authorized redirect URIs**, click **Add URI**. Then go back to *Supabase Setup* at step 4, copy the **Callback URL (for OAuth)**, return here to paste it into URIs 1, and click **Create**.
5.  Right after clicking create, Google will display a popup containing two strings: **Client ID** and **Client Secret**. Copy these two strings, return to Supabase **(Authentication → Providers → Google)**, enable "Sign in with Google", paste the two strings into the corresponding fields, and click Save.

<!-- end list -->

  - Declare URL for Web Application:

<!-- end list -->

1.  Return to Supabase, go to **Authentication → URL Configuration**.
2.  **Site URL**: Enter `http://localhost:3000` (development) or `https://yourdomain.com` (production).
3.  **Redirect URLs**: Add the exact path to the callback handler file on your PHP system.
    Example: `http://localhost:3000/app/views/client/auth/callback.php`
4.  Update the `.env` file with your Cloudinary credentials.

### Step 8: Google reCAPTCHA v2 

1. Go to https://www.google.com/recaptcha/admin/create.
2. Under **Label**, enter a name (e.g., `FPT Shop`).
3. Under **reCAPTCHA type**, select **reCAPTCHA v2** → "I'm not a robot" Checkbox.
4. Under **Domains**, add `localhost` (for development) and your production domain.
5. Click **Submit** — you will receive a **Site Key** and **Secret Key**.
6. Copy them into your `.env`:
   ```env
   RECAPTCHA_SITE_KEY=6Lc...your_site_key
   RECAPTCHA_SECRET_KEY=6Lc...your_secret_key
   ```
### Step 9: VNPay (Card Payment — Sandbox)

1. Go to https://sandbox.vnpayment.vn/devreg/ and register a sandbox merchant account.
2. After registration, log in to the sandbox portal.
3. Go to **Thông tin tích hợp** (Integration Info) to find your:
   - **TMN Code** (`vnp_TmnCode`) — your merchant terminal code
   - **Hash Secret** (`vnp_HashSecret`) — your signing secret
4. Copy them into your `.env`:
   ```env
   VNPAY_TMN_CODE=XXXXXXXX
   VNPAY_HASH_SECRET=your_hash_secret_here
   VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
   ```
5. For testing, use VNPay's test card numbers from: https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html

> **Note**: VNPay sandbox does not support real refunds. This project uses a mock refund simulation that logs the operation without calling the VNPay API.

### Step 10: PayPal (International Payment — Sandbox)

1. Go to https://developer.paypal.com/ and log in with your PayPal account.
2. Go to **Apps & Credentials** → make sure you are in **Sandbox** mode.
3. Click **Create App** → name it `FPT Shop` → **Create App**.
4. You will see your **Client ID** and **Secret key 1** — copy them:
   ```env
   PAYPAL_CLIENT_ID=AXxx...your_client_id
   PAYPAL_SECRET=EXxx...your_secret
   PAYPAL_MODE=sandbox
   ```
5. For testing, use PayPal sandbox buyer accounts from **Testing Tools** → **Sandbox Accounts** → Choose an account with the `Type` Personal, which will contain the `Email` and `Password` used to test payments.
6. Currency conversion: VND amounts are divided by 25,000 to get USD (e.g., 500,000 VND → $20.00 USD).

> For production, change `PAYPAL_MODE=live` and replace with live credentials.

### Step 11: VietQR (Bank Transfer QR)

1. Set your bank details in `.env`:
   ```env
   VIETQR_BANK_ID=VCB          # Bank code (VCB = Vietcombank, TCB = Techcombank, etc.)
   VIETQR_ACCOUNT_NO=1234567890
   VIETQR_ACCOUNT_NAME=NGUYEN VAN A
   VIETQR_TEMPLATE=compact2
   ```
2. Find your bank's code at: https://www.vietqr.io/danh-sach-ngan-hang
3. Payment confirmation is **manual** — admin must verify and approve the transfer.

### Step 12: Run the Development Server

From the project root directory:

```bash
php -S localhost:3000 router.php
```

### Step 13: Access the Application

  - **Client**: http://localhost:3000
  - **Admin Panel**: http://localhost:3000/admin/auth/login

### Default Admin Credentials

After importing the database, you can log in with:

  - **Email**: admin@fptshop.com
  - **Password**: admin

## Development Team

| Member                                                                         | Role        |
| ------------------------------------------------------------------------------ | ----------- |
| Truong Thanh Dat ([datweb07](https://github.com/datweb07))                     | Team Leader |
| Phan Khac Anh Tuan ([KhacTuan1224](https://github.com/KhacTuan1224))           | Member      |
| Nguyen Phuong Chinh ([chinhngprit](https://github.com/chinhngprit))            | Member      |
| Nguyen Tan Khiem ([nguyentankhiem1610](https://github.com/nguyentankhiem1610)) | Member      |

## Contributing

Please read [CONTRIBUTING.md](https://www.google.com/search?q=CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is released under the **MIT License**. See the [LICENSE.md](LICENSE.md) file for full details.

### Third-Party Library Licenses

This project uses various third-party libraries and services. For detailed information on all dependencies, their licenses, and compliance requirements, please see [THIRD-PARTY-NOTICES.md](https://www.google.com/search?q=THIRD-PARTY-NOTICES.md).

## Track the class project at [this link](https://docs.google.com/document/d/1SXeumwh1u8Yp0dC2vJMpMznbU5E-hHp4QlYRMehpj54/edit?fbclid=IwY2xjawP7fhlleHRuA2FlbQIxMQBzcnRjBmFwcF9pZAEwAAEedb2YK7uGIXycjsky8VB1DFG-L3-gWnW-waFfYHy-auBXTEFJHKVo2hiwIss_aem_jiqtsPn96N6dYubaf0h3ow&tab=t.n8hb9b8xnj2z)

## Track the team document at [this link](https://docs.google.com/document/d/1JKrh4aKDL6bRvAVQPyokfoLd3LKVeL6jLs0IW6hdVk4/edit?usp=sharing)