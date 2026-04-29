<?php

require_once __DIR__ . '/PHPMailer.php';
require_once __DIR__ . '/SMTP.php';
require_once __DIR__ . '/Exception.php';
require_once dirname(__DIR__, 2) . '/core/EnvSetup.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService
{
    public static function getMailer()
    {
        $mail = new PHPMailer(true);

        $envConfig = EnvSetup::env(dirname(__DIR__, 3));
        
        try {
            $mail->isSMTP();
            $mail->Host       = $envConfig('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = $envConfig('MAIL_USERNAME');
            $mail->Password   = $envConfig('MAIL_PASSWORD');
            $mail->SMTPSecure = $envConfig('MAIL_ENCRYPTION'); 
            $mail->Port       = $envConfig('MAIL_PORT');
            $mail->CharSet    = 'UTF-8'; 

            return $mail;
        } catch (Exception $e) {
            error_log("Lỗi cấu hình Mailer: {$mail->ErrorInfo}");
            return null;
        }
    }

    public function sendOrderConfirmation(array $emailData): bool
    {
        try {
            $mail = self::getMailer();
            if (!$mail) {
                return false;
            }

            $envConfig = EnvSetup::env(dirname(__DIR__, 3)); 
            $mail->setFrom($envConfig('MAIL_FROM_ADDRESS'), $envConfig('MAIL_FROM_NAME'));
            $mail->addAddress($emailData['to']);
            $mail->Subject = $emailData['subject'];
            
            $data = $emailData['data'];
            $itemsHtml = $this->buildItemsTable($data['items']);
            
            $mail->isHTML(true);
            $mail->Body = "
                <h2>Xin chào {$data['customer_name']},</h2>
                <p>Cảm ơn bạn đã đặt hàng tại FPT Shop!</p>
                <p><strong>Mã đơn hàng:</strong> #{$data['order_id']}</p>
                <p><strong>Ngày đặt:</strong> {$data['order_date']}</p>
                <p><strong>Phương thức thanh toán:</strong> {$data['payment_method']}</p>
                
                <h3>Chi tiết đơn hàng:</h3>
                {$itemsHtml}
                
                <div style='margin-top: 20px; padding: 15px; background-color: #f9f9f9; border-radius: 5px;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 5px 0; color: #666;'>Tổng tiền hàng:</td>
                            <td style='text-align: right; padding: 5px 0;'>" . number_format($data['subtotal'] ?? $data['total_amount']) . "đ</td>
                        </tr>
                        <tr>
                            <td style='padding: 5px 0; color: #666;'>Phí vận chuyển:</td>
                            <td style='text-align: right; padding: 5px 0;'>" . number_format($data['shipping_fee'] ?? 0) . "đ</td>
                        </tr>";
                        
            if (($data['discount_amount'] ?? 0) > 0) {
                $mail->Body .= "
                        <tr>
                            <td style='padding: 5px 0; color: #666;'>Giảm giá" . (!empty($data['discount_code']) ? " ({$data['discount_code']})" : "") . ":</td>
                            <td style='text-align: right; padding: 5px 0; color: #e74c3c;'>-" . number_format($data['discount_amount']) . "đ</td>
                        </tr>";
            }
            
            $mail->Body .= "
                        <tr style='border-top: 2px solid #ddd;'>
                            <td style='padding: 10px 0; font-weight: bold; font-size: 16px;'>Tổng thanh toán:</td>
                            <td style='text-align: right; padding: 10px 0; font-weight: bold; font-size: 16px; color: #e74c3c;'>" . number_format($data['total_amount']) . "đ</td>
                        </tr>
                    </table>
                </div>
                
                <p style='margin-top: 20px;'>Chúng tôi sẽ xử lý đơn hàng của bạn trong thời gian sớm nhất.</p>
                <p>Trân trọng,<br>FPT Shop</p>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Send order confirmation failed: " . $e->getMessage());
            return false;
        }
    }

    public function sendPaymentSuccess(array $emailData): bool
    {
        try {
            $mail = self::getMailer();
            if (!$mail) {
                return false;
            }

            $envConfig = EnvSetup::env(dirname(__DIR__, 3)); 
            $mail->setFrom($envConfig('MAIL_FROM_ADDRESS'), $envConfig('MAIL_FROM_NAME'));
            $mail->addAddress($emailData['to']);
            $mail->Subject = $emailData['subject'];
            
            $data = $emailData['data'];
            
            $mail->isHTML(true);
            $mail->Body = "
                <h2>Thanh toán thành công!</h2>
                <p>Xin chào {$data['customer_name']},</p>
                <p>Giao dịch thanh toán của bạn đã được xử lý thành công.</p>
                
                <h3>Thông tin giao dịch:</h3>
                <ul>
                    <li><strong>Mã đơn hàng:</strong> #{$data['order_id']}</li>
                    <li><strong>Phương thức:</strong> {$data['payment_method']}</li>
                    <li><strong>Mã giao dịch:</strong> {$data['transaction_id']}</li>
                    <li><strong>Số tiền:</strong> " . number_format($data['amount']) . "đ</li>
                    <li><strong>Thời gian:</strong> {$data['payment_date']}</li>
                </ul>
                
                <p>Đơn hàng của bạn đang được xử lý và sẽ sớm được giao đến bạn.</p>
                <p>Trân trọng,<br>FPT Shop</p>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Send payment success failed: " . $e->getMessage());
            return false;
        }
    }

    public function sendPaymentReceived(array $emailData): bool
    {
        try {
            $mail = self::getMailer();
            if (!$mail) {
                return false;
            }

            $envConfig = EnvSetup::env(dirname(__DIR__, 3)); 
            $mail->setFrom($envConfig('MAIL_FROM_ADDRESS'), $envConfig('MAIL_FROM_NAME'));
            $mail->addAddress($emailData['to']);
            $mail->Subject = $emailData['subject'];
            
            $data = $emailData['data'];
            
            $mail->isHTML(true);
            $mail->Body = "
                <h2>Đã nhận thanh toán!</h2>
                <p>Xin chào {$data['customer_name']},</p>
                <p>Chúng tôi đã nhận được thanh toán cho đơn hàng #{$data['order_id']}.</p>
                
                <h3>Thông tin:</h3>
                <ul>
                    <li><strong>Số tiền:</strong> " . number_format($data['amount']) . "đ</li>
                    <li><strong>Phương thức:</strong> {$data['payment_method']}</li>
                    <li><strong>Dự kiến giao hàng:</strong> {$data['estimated_delivery']}</li>
                </ul>
                
                <p><strong>Đơn hàng của bạn đang được đóng gói và chuẩn bị giao.</strong></p>
                <p>Bạn sẽ nhận được thông báo khi đơn hàng được giao cho đơn vị vận chuyển.</p>
                
                <p>Trân trọng,<br>FPT Shop</p>
            ";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Send payment received failed: " . $e->getMessage());
            return false;
        }
    }
    private function buildItemsTable(array $items): string
    {
        if (empty($items)) {
            return '<p>Không có sản phẩm nào trong đơn hàng.</p>';
        }
        
        $html = '<table border="1" cellpadding="10" style="border-collapse: collapse; width: 100%;">';
        $html .= '<tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr>';
        
        foreach ($items as $item) {
            $tenSanPham = htmlspecialchars($item['ten_san_pham'] ?? 'Sản phẩm');
            $soLuong = (int)($item['so_luong'] ?? 0);
            $gia = (float)($item['gia'] ?? 0);
            $thanhTien = $soLuong * $gia;
            
            $html .= '<tr>';
            $html .= '<td>' . $tenSanPham . '</td>';
            $html .= '<td>' . $soLuong . '</td>';
            $html .= '<td>' . number_format($gia) . 'đ</td>';
            $html .= '<td>' . number_format($thanhTien) . 'đ</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        return $html;
    }
}