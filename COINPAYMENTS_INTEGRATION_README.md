# CoinPayments Integration Guide

## Overview

This guide explains how to complete the CoinPayments integration for real production use. The system has been migrated from NowPayments to CoinPayments and is ready for configuration.

## What's Already Implemented

✅ **CoinPayments Service Class** - Complete API integration  
✅ **Payment Controller Methods** - Create transactions and handle IPNs  
✅ **Database Migration** - Added necessary columns for CoinPayments  
✅ **Admin Settings** - CoinPayments configuration panel  
✅ **Frontend Updates** - Payment forms and status pages  
✅ **Unit Tests** - Service validation and HMAC verification  
✅ **Feature Tests** - Complete payment flow testing  

## 🚀 REAL INTEGRATION STEPS

### Step 1: Get CoinPayments Account & API Keys

1. **Sign up at [CoinPayments.net](https://www.coinpayments.net)**
2. **Go to Account Settings → Merchant Settings**
3. **Create API Keys:**
   - Public Key (starts with `cp_`)
   - Private Key (starts with `cp_`)
   - Note your **Merchant ID**
4. **Set IPN Secret:**
   - Create a strong, unique secret (32+ characters)
   - This is used to verify webhook authenticity

### Step 2: Configure Environment Variables

Add these to your `.env` file:

```env
# CoinPayments Configuration
COINPAYMENTS_MERCHANT_ID="your_merchant_id_here"
COINPAYMENTS_PUBLIC_KEY="cp_your_public_key_here"
COINPAYMENTS_PRIVATE_KEY="cp_your_private_key_here"
COINPAYMENTS_IPN_SECRET="your_strong_ipn_secret_here"
COINPAYMENTS_CURRENCY2="USDT.TRC20"
COINPAYMENTS_IPN_URL="${APP_URL}/payments/coinpayments/ipn"
COINPAYMENTS_ENABLED=true
COINPAYMENTS_SANDBOX=false
```

### Step 3: Set IPN URL in CoinPayments

1. **In CoinPayments dashboard:**
   - Go to **Account Settings → Merchant Settings**
   - Set **IPN URL** to: `https://yourdomain.com/payments/coinpayments/ipn`
   - Replace `yourdomain.com` with your actual domain

### Step 4: Configure Admin Panel

1. **Login to admin panel**
2. **Go to Settings → Payment Settings**
3. **Set Payment Gateway to:** `CoinPayments (Crypto)`
4. **In CoinPayments Configuration:**
   - **Enable CoinPayments:** `Enabled`
   - **Default Crypto Currency:** `USDT.TRC20` (or your preference)

### Step 5: Test the Integration

#### Test Payment Flow:
1. **Create test user account**
2. **Go to payment checkout**
3. **Select crypto payment**
4. **Complete payment on CoinPayments**
5. **Verify IPN received and processed**

#### Test IPN Manually (for debugging):
```bash
# Use Postman or curl to test IPN
curl -X POST https://yourdomain.com/payments/coinpayments/ipn \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "HMAC: calculated_hmac_here" \
  -d "merchant=your_merchant_id&status=100&txn_id=TEST123&amount1=10.00&amount2=10.00&currency1=USD&currency2=USDT.TRC20"
```

## 🔧 Configuration Details

### Supported Cryptocurrencies

- **USDT.TRC20** (Tron Network) - Recommended for low fees
- **USDT.ERC20** (Ethereum Network) - Higher fees but more widely supported
- **BTC** (Bitcoin)
- **ETH** (Ethereum)

### IPN Status Mapping

| CoinPayments Status | Internal Status | Description |
|-------------------|----------------|-------------|
| 0                 | pending        | Payment pending |
| 1                 | processing     | Payment confirmed, waiting for confirmations |
| 2 or ≥100        | completed      | Payment fully confirmed |
| <0                | failed         | Payment failed/cancelled |

### Database Schema

New columns added to `transactions` table:

```sql
txn_id           VARCHAR(255) NULL INDEX    -- CoinPayments transaction ID
target_currency  VARCHAR(50) NULL INDEX    -- Crypto currency (e.g., USDT.TRC20)
received_amount  DECIMAL(18,8) DEFAULT 0   -- Actual crypto amount received
confirmations    UNSIGNED INT DEFAULT 0     -- Blockchain confirmations
processed_at     TIMESTAMP NULL             -- When payment was processed
```

## 🧪 Testing

### Run Tests
```bash
# Unit tests for service
php artisan test tests/Unit/CoinPaymentsServiceTest.php

# Feature tests for payment flow
php artisan test tests/Feature/CoinPaymentsTest.php

# All tests
php artisan test
```

### Test Environment
For testing, you can use:
- **Sandbox mode** (if available on your CoinPayments plan)
- **Testnet cryptocurrencies** (for BTC/ETH testing)
- **Small amounts** for real USDT testing

## 🚨 Security Considerations

### HMAC Verification
- **Never disable HMAC verification**
- **Use strong, unique IPN secrets**
- **Rotate secrets periodically**
- **Log all IPN attempts for monitoring**

### Rate Limiting
The IPN endpoint is public but protected by:
- HMAC signature verification
- Merchant ID validation
- Transaction ID validation

### Monitoring
Monitor these logs for issues:
```bash
# Laravel logs
tail -f storage/logs/laravel.log | grep "CoinPayments"

# Check for failed IPNs
grep "CoinPayments IPN failed" storage/logs/laravel.log
```

## 🔍 Troubleshooting

### Common Issues

#### IPN Not Received
1. **Check IPN URL** in CoinPayments dashboard
2. **Verify domain accessibility** from external sources
3. **Check server firewall** allows incoming POST requests
4. **Verify HMAC secret** matches between systems

#### Payment Not Processing
1. **Check transaction exists** in database
2. **Verify IPN verification** passed
3. **Check CoinPayments logs** for errors
4. **Verify merchant ID** matches

#### HMAC Verification Fails
1. **Check IPN secret** in both systems
2. **Verify raw body** is being sent correctly
3. **Check for middleware** that might modify request body
4. **Test with Postman** using exact same payload

### Debug Mode
Enable detailed logging in `config/logging.php`:
```php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['single', 'daily'],
        'ignore_exceptions' => false,
    ],
],
```

## 📱 Frontend Integration

### Payment Flow
1. **User selects crypto payment**
2. **System creates CoinPayments transaction**
3. **User redirected to CoinPayments checkout**
4. **User completes payment**
5. **CoinPayments sends IPN**
6. **System processes IPN and activates subscription**

### Status Updates
- **Pending:** User sees payment instructions
- **Processing:** Payment confirmed, waiting for confirmations
- **Completed:** Subscription activated automatically
- **Failed:** User can retry payment

## 🔄 Migration from NowPayments

### What Was Removed
- ❌ NowPayments API integration
- ❌ NowPayments webhook processing
- ❌ NowPayments environment variables
- ❌ NowPayments UI references

### What Was Added
- ✅ CoinPayments service class
- ✅ CoinPayments payment flow
- ✅ Enhanced transaction tracking
- ✅ Admin configuration panel
- ✅ Comprehensive testing

### Legacy Support
- **Existing NowPayments transactions** are still supported
- **UI shows legacy transactions** with appropriate labels
- **No data loss** during migration

## 📊 Monitoring & Analytics

### Key Metrics to Track
- **Payment success rate**
- **IPN delivery success rate**
- **Average confirmation time**
- **Failed payment reasons**
- **Gateway performance**

### Alerts to Set Up
- **Failed IPN attempts**
- **High failure rates**
- **Long confirmation times**
- **API errors**

## 🚀 Production Deployment

### Pre-deployment Checklist
- [ ] CoinPayments account verified
- [ ] API keys generated and tested
- [ ] IPN URL configured
- [ ] Environment variables set
- [ ] Admin panel configured
- [ ] Tests passing
- [ ] IPN endpoint accessible

### Post-deployment Verification
- [ ] Test payment flow end-to-end
- [ ] Verify IPN processing
- [ ] Check transaction status updates
- [ ] Monitor error logs
- [ ] Verify subscription activation

## 📞 Support

### CoinPayments Support
- **Documentation:** [CoinPayments API Docs](https://www.coinpayments.net/merchant-tools-api)
- **Support:** Available through CoinPayments dashboard
- **Community:** [CoinPayments Forum](https://www.coinpayments.net/forum)

### Application Support
- **Logs:** Check `storage/logs/laravel.log`
- **Database:** Verify transaction records
- **Tests:** Run test suite for validation
- **Admin Panel:** Check configuration settings

---

## 🎯 Quick Start Summary

1. **Get CoinPayments account & API keys**
2. **Add environment variables to `.env`**
3. **Set IPN URL in CoinPayments dashboard**
4. **Configure admin panel settings**
5. **Test payment flow**
6. **Monitor logs and transactions**
7. **Go live with real payments**

The system is production-ready once you complete these configuration steps!
