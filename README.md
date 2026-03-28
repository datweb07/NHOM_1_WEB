# E-Commerce Website - FPT Shop

<img width="960" height="313" alt="fpt-shop-banner" src="https://github.com/datweb07/NHOM_1_WEB/blob/main/images/fpt-shop-banner.png" />

## Project Description

The FPT Shop e-commerce website project is an online shopping platform specializing in technology products, mobile phones, laptops, tablets, and electronic accessories. The website provides a modern and convenient shopping experience with a user-friendly and easy-to-use interface.

### Key Features

- **Product Management**: Displays a diverse product catalog with detailed information, images, and pricing.
- **Shopping Cart**: Add, remove, and update the quantity of products in the cart.
- **Search & Filter**: Search for products by name and filter them by category, price, and brand.
- **User Management**: Registration, login, and personal information management.
- **Authentication & Authorization**: Separate login systems for customers and administrators with role-based access control.
- **Profile Management**: Update personal information (name, phone, birthdate, gender) and change password.
- **Checkout**: Order processing and payment methods.
- **Admin Panel**: Manage products, orders, and customers.

### Technologies Used

- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5
- **Backend**: PHP (OOP Architecture)
- **Database**: MySQL
- **Version Control**: Git, GitHub
- **Session Management**: PHP Sessions for authentication

## Installation & Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/datweb07/NHOM_1_WEB.git
   cd NHOM_1_WEB
   ```

2. **Configure Database**
   
   a. Import database into phpMyAdmin:
   ```bash
   database/db_web.sql
   ```
   
   b. Update database credentials in `config/config.php`:
   ```php
   define("HOST", "localhost");
   define("DB", "db_web");
   define("USER", "root");
   define("PASSWORD", "your_password");
   ```

3. **Run the Development Server**
   
   From the project root directory, run:
   ```bash
   php -S localhost:3000 router.php
   ```

4. **Access the Application**
   
   Open your browser and navigate to:
   - **Client**: `http://localhost:3000`
   - **Admin**: `http://localhost:3000/admin/auth/login`


## Development Team

| Member                                                                         | Role        |
| ------------------------------------------------------------------------------ | ----------- |
| Truong Thanh Dat ([datweb07](https://github.com/datweb07))                     | Team Leader |
| Phan Khac Anh Tuan ([KhacTuan1224](https://github.com/KhacTuan1224))           | Member      |
| Nguyen Phuong Chinh ([chinhngprit](https://github.com/chinhngprit))            | Member      |
| Nguyen Tan Khiem ([nguyentankhiem1610](https://github.com/nguyentankhiem1610)) | Member      |

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute to this project.

## Licenses

This project is released under the **MIT License**. See the [LICENSE.md](LICENSE.md) file for full details.

## Track the class project at this [link](https://docs.google.com/document/d/1SXeumwh1u8Yp0dC2vJMpMznbU5E-hHp4QlYRMehpj54/edit?fbclid=IwY2xjawP7fhlleHRuA2FlbQIxMQBzcnRjBmFwcF9pZAEwAAEedb2YK7uGIXycjsky8VB1DFG-L3-gWnW-waFfYHy-auBXTEFJHKVo2hiwIss_aem_jiqtsPn96N6dYubaf0h3ow&tab=t.n8hb9b8xnj2z)

## Track the team document at this [link](https://docs.google.com/document/d/1JKrh4aKDL6bRvAVQPyokfoLd3LKVeL6jLs0IW6hdVk4/edit?usp=sharing)