# Admin User Guide: Refund Processing

## Overview

This guide explains how to process refunds for customer payments through the admin panel. The refund feature allows you to return money to customers through payment gateways (VNPay) when needed.

## When to Use Refunds

You should process a refund when:
- Customer requests refund due to product defect
- Order was cancelled after payment
- Wrong amount was charged
- Product is out of stock and cannot be fulfilled
- Customer is not satisfied with the product (within return policy)

## Refund Eligibility

A payment can be refunded if:
- ✅ Payment status is "Đã duyệt" (THANH_CONG)
- ✅ Payment method is VNPay or Momo
- ✅ Payment has not been refunded before
- ❌ COD payments cannot be refunded through the system
- ❌ ZaloPay payments are not supported for refunds yet

## How to Process a Refund

### Step 1: Navigate to Payment Details

1. Login to admin panel at `/admin/auth/login`
2. Click "Thanh toán" in the sidebar menu
3. Find the payment you want to refund
4. Click "Chi tiết" button to view payment details

![Payment List](../assets/payment-list.png)

### Step 2: Check Refund Eligibility

On the payment detail page, check:
- Payment status shows "Đã duyệt" (green badge)
- Payment method is VNPay or Momo
- "Hoàn tiền" button is visible and enabled

If the refund button is:
- **Visible and yellow**: You can process the refund
- **Grayed out**: Payment is not approved yet
- **Hidden**: Payment is COD, already refunded, or not eligible

![Payment Detail](../assets/payment-detail.png)

### Step 3: Initiate Refund

1. Click the "Hoàn tiền" button
2. A confirmation modal will appear

![Refund Modal](../assets/refund-modal.png)

### Step 4: Enter Refund Details

In the refund modal:
1. **Refund Amount**: Pre-filled with payment amount (cannot be changed)
2. **Refund Reason**: Enter a clear reason for the refund (required)

Example reasons:
- "Khách hàng yêu cầu hoàn tiền do sản phẩm lỗi"
- "Đơn hàng bị hủy sau khi thanh toán"
- "Sản phẩm hết hàng, không thể giao"
- "Khách hàng không hài lòng với sản phẩm"

### Step 5: Confirm Refund

1. Review the warning message about the refund action
2. Click "Xác nhận hoàn tiền" button
3. Wait for the system to process (may take 5-10 seconds)

### Step 6: Verify Refund Success

After processing:
- ✅ Success message appears: "Hoàn tiền thành công. Mã giao dịch: [REFUND_ID]"
- ✅ Refund button disappears
- ✅ Refund history section shows the completed refund

![Refund Success](../assets/refund-success.png)

## Understanding Refund Status

### Status Badges

- **Hoàn thành** (Green): Refund successfully processed by payment gateway
- **Đang xử lý** (Yellow): Refund is being processed, please wait
- **Thất bại** (Red): Refund failed, contact technical support

### Refund History

The refund history table shows:
- **Số tiền**: Refund amount
- **Trạng thái**: Current status (Hoàn thành/Đang xử lý/Thất bại)
- **Lý do**: Reason you provided for the refund
- **Mã GD Gateway**: Gateway transaction ID (for tracking)
- **Ngày tạo**: When refund was initiated
- **Ngày hoàn thành**: When refund was completed

## Troubleshooting

### Issue: Refund button is grayed out

**Reason:** Payment is not approved yet  
**Solution:** Wait for payment to be approved, or approve it first

### Issue: Refund button is hidden

**Possible reasons:**
- Payment method is COD (cannot refund through system)
- Payment has already been refunded
- Payment method is ZaloPay (not supported yet)

**Solution:** For COD refunds, handle manually with accounting team

### Issue: Refund failed with error message

**Reason:** Payment gateway returned an error  
**Solution:** 
1. Check the error message for details
2. Verify payment gateway is configured correctly
3. Contact technical support if issue persists
4. You can retry the refund after fixing the issue

### Issue: Refund is stuck in "Đang xử lý" status

**Reason:** Gateway is processing the refund  
**Solution:** 
1. Wait 5-10 minutes and refresh the page
2. If still pending after 30 minutes, contact technical support
3. Check with payment gateway support for transaction status

## Important Notes

### Refund Processing Time

- **VNPay**: Refunds are processed immediately, but may take 3-5 business days to appear in customer's bank account
- **Momo**: Refunds are processed immediately, usually appear in customer's wallet within 24 hours

### Refund Limitations

- ❌ Cannot refund partial amounts (full payment only)
- ❌ Cannot refund the same payment twice
- ❌ Cannot cancel a refund once processed
- ❌ COD payments must be refunded manually

### Best Practices

1. **Always provide a clear reason**: This helps with accounting and customer service
2. **Verify payment details**: Double-check you're refunding the correct payment
3. **Inform the customer**: Let the customer know the refund has been processed
4. **Document the refund**: Keep records of why the refund was issued
5. **Check refund status**: Verify the refund completed successfully

### Customer Communication

After processing a refund, inform the customer:
- Refund has been initiated
- Expected time for money to appear in their account
- Gateway transaction ID for their reference
- Contact information if they have questions

Example message:
```
Kính gửi quý khách,

Chúng tôi đã xử lý hoàn tiền cho đơn hàng #[ORDER_ID] của quý khách.

Số tiền: [AMOUNT] VND
Mã giao dịch: [REFUND_ID]
Thời gian xử lý: 3-5 ngày làm việc

Số tiền sẽ được hoàn về tài khoản/ví của quý khách trong vòng 3-5 ngày làm việc.

Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi.

Trân trọng,
[Your Company Name]
```

## Revenue Impact

### How Refunds Affect Revenue

When you process a refund:
- ✅ The refunded amount is automatically excluded from revenue calculations
- ✅ Dashboard revenue updates immediately after refund completion
- ✅ Monthly and total revenue reports reflect the refund

### Checking Revenue After Refund

1. Navigate to Dashboard
2. Check "Doanh thu" section
3. Verify revenue has decreased by refund amount

## Frequently Asked Questions

**Q: Can I refund a payment that was made 6 months ago?**  
A: Yes, as long as the payment is approved and hasn't been refunded before. However, check with your payment gateway for their refund time limits.

**Q: What happens to the order after refund?**  
A: The order status is not automatically changed. You should manually update the order status to "Đã hủy" or "Trả hàng" as appropriate.

**Q: Can I refund only part of the payment?**  
A: No, currently the system only supports full refunds. For partial refunds, contact technical support.

**Q: How do I refund a COD payment?**  
A: COD payments must be refunded manually through your accounting process. The system does not support automatic COD refunds.

**Q: What if the customer's bank account is closed?**  
A: The refund will fail and the money will be returned to your account. Contact the customer for alternative refund method.

**Q: Can I cancel a refund after clicking confirm?**  
A: No, refunds cannot be cancelled once processed. Always double-check before confirming.

**Q: How do I know if the customer received the refund?**  
A: Check the refund status. If it shows "Hoàn thành", the gateway has processed it. The customer should receive it within the gateway's processing time. You can also provide the gateway transaction ID to the customer for tracking.

## Support

If you encounter any issues or have questions:
- **Technical Support**: [support@yourcompany.com]
- **Phone**: [Support Phone Number]
- **Working Hours**: Monday-Friday, 9:00-18:00

## Changelog

**Version 1.0** (2024-01-XX)
- Initial release of refund feature
- Support for VNPay refunds
- Automatic revenue calculation updates
