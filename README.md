> Read in other languages: [Tiếng Việt](README.vi.md)

# E-Commerce Website - FPT Shop

<img width="960" height="313" alt="fpt-shop-banner" src="https://github.com/datweb07/NHOM_1_WEB/blob/main/images/fpt-shop-banner.png" />

## Project Description

The FPT Shop e-commerce website project is an online shopping platform specializing in technology products, mobile phones, laptops, tablets, and electronic accessories. The website provides a modern and convenient shopping experience with a user-friendly and easy-to-use interface.

## Key Features

### Client Features
- **Product Catalog**: Browse a diverse range of products with detailed information, images, specifications, and pricing.
- **Product Variants**: Support for product variants (color, storage capacity, RAM) with different prices.
- **Shopping Cart**: Add, remove, update quantities, and manage items in the cart.
- **Advanced Search & Filter**: Search by name, filter by category, price range, and brand.
- **Wishlist**: Save favorite products for later purchase.
- **User Authentication**: Registration, login, email verification, and password reset.
- **Profile Management**: Update personal information (full name, phone number, date of birth, gender, avatar) and change password.
- **Order Management**: View order history, track order status, and order details.
- **Checkout Process**: Complete orders with various payment methods.
- **Product Reviews**: Rate and leave comments on purchased products.
- **Promotions & Discounts**: Apply discount codes and view promotional products.
- **Search History**: Track and manage product search history.
- **Responsive Design**: Mobile-friendly interface with optimized layouts.

### Admin Features
- **Dashboard**: Overview statistics of orders, revenue, and products.
- **Product Management**: Perform CRUD (Create, Read, Update, Delete) operations for products, variants, images, and specifications.
- **Category Management**: Hierarchical category structure along with featured and suggested categories.
- **Order Management**: View, update order status, and manage order details.
- **User Management**: View and manage customer accounts.
- **Promotion Management**: Create and manage promotional campaigns.
- **Discount Code Management**: Create and manage voucher/coupon codes.
- **Banner Management**: Manage hero banners and promotional banners.
- **Review Management**: View and manage product reviews.
- **Payment Verification**: Approve or reject payment confirmations.

## Technologies Used

### Frontend
- **HTML5, CSS3, JavaScript (ES6+)**
- **Bootstrap 5**: UI framework supporting responsive design.
- **Font Awesome**: Icon library.
- **Custom CSS**: Grid system, sliders, and animations.

### Backend
- **PHP 8.x**: Server-side programming.
- **OOP Architecture**: MVC pattern for clear separation of functional components.
- **Custom Router**: File-based routing system.
- **Middleware**: Middleware for handling authentication and authorization.
- **Session Management**: Use PHP Sessions for user authentication.

### Database
- **MySQL 8.x**: Relational database.
- **Charset**: utf8mb4.

### Third-Party Services & Libraries
- **Cloudinary**: Cloud-based image storage and optimization.
- **PHPMailer**: Send emails for verification and password reset.
- **PHPDotenv**: Environment variable management.
- **Redis** (Optional): Caching layer to improve performance.
- **Supabase**: Login via Google OAuth.

### Development Tools
- **Composer**: PHP dependency management.
- **Git & GitHub**: Version control.
- **VS Code**: Integrated Development Environment (IDE).

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
DB_DATABASE=db_web
DB_USERNAME=root
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls

SUPABASE_URL=[https://your-project-id.supabase.co](https://your-project-id.supabase.co)
SUPABASE_ANON_KEY=your-jwt-secret-from-api-settings
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

### Step 8: Run the Development Server

From the project root directory:

```bash
php -S localhost:3000 router.php
```

### Step 9: Access the Application

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