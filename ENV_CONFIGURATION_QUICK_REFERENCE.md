# 🔧 .env Configuration Quick Reference

## 📋 **What You Need to Replace in .env File**

### 🌐 **Domain Configuration**
```bash
# Replace YOUR_DOMAIN.com with your actual domain
APP_URL=https://yourdomain.com
MAIL_HOST=yourdomain.com
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
COINPAYMENTS_IPN_URL="https://yourdomain.com/payments/coinpayments/ipn"
```

### 🗄️ **Database Configuration (cPanel)**
```bash
# Get these from cPanel → MySQL Databases
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_database_password
```

### 📧 **Email Configuration (3 Options)**

#### Option 1: cPanel Email (Recommended)
```bash
MAIL_MAILER=smtp
MAIL_HOST=yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
```

#### Option 2: Gmail SMTP
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

#### Option 3: Mailgun (Professional)
```bash
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-mailgun-domain.com
MAILGUN_SECRET=your-mailgun-secret
```

### 💰 **CoinPayments Configuration (Already Set)**
```bash
# These are already configured with your credentials
COINPAYMENTS_ENABLED=true
COINPAYMENTS_MERCHANT_ID="82fb593d8bc444d7fd126342665a3068"
COINPAYMENTS_PUBLIC_KEY="5a3e09f1e0aa0059e826cc064ed786f25c3f1e6450543314e88ecd552eeb4ddb"
COINPAYMENTS_PRIVATE_KEY="179f143D3be0d7064791f2A30ec32538fc68eee9B29745B084455D3E531e1265"
COINPAYMENTS_IPN_SECRET="529209"
COINPAYMENTS_CURRENCY2="USDT.TRC20"
COINPAYMENTS_IPN_URL="https://yourdomain.com/payments/coinpayments/ipn"
COINPAYMENTS_SANDBOX=false
SUBSCRIPTION_PRICE="39.90"
```

---

## 🚀 **Quick Setup Steps**

### 1. **Copy the Template**
```bash
# Copy the production template
cp production.env .env
```

### 2. **Edit .env File**
Replace these placeholders:
- `YOUR_DOMAIN.com` → Your actual domain
- `your_cpanel_database_name` → Your cPanel database name
- `your_cpanel_database_user` → Your cPanel database user
- `your_database_password` → Your database password
- `your_email_password` → Your email password

### 3. **Test Configuration**
```bash
# Test the configuration
php test_coinpayments_integration.php
```

---

## 📧 **Email Setup Options**

### **cPanel Email Setup**
1. Go to cPanel → Email Accounts
2. Create: `noreply@yourdomain.com`
3. Use the password in .env file

### **Gmail Setup**
1. Enable 2-Factor Authentication
2. Generate App Password
3. Use App Password in .env file

### **Mailgun Setup**
1. Sign up at Mailgun.com
2. Add your domain
3. Get API credentials
4. Use in .env file

---

## 🔍 **Verification Checklist**

### Before Deployment
- [ ] Domain name updated in all places
- [ ] Database credentials correct
- [ ] Email credentials working
- [ ] CoinPayments IPN URL updated
- [ ] All placeholders replaced

### After Deployment
- [ ] Website loads correctly
- [ ] Database connection works
- [ ] Email sending works
- [ ] CoinPayments integration works
- [ ] Payment processing works

---

## 🛠️ **Common Issues & Solutions**

### Database Connection Error
```bash
# Check database credentials
php artisan tinker
DB::connection()->getPdo();
exit
```

### Email Not Sending
```bash
# Test email configuration
php artisan tinker
Mail::raw('Test', function($m) { 
    $m->to('test@example.com')->subject('Test'); 
});
exit
```

### CoinPayments Not Working
```bash
# Test CoinPayments integration
php test_coinpayments_integration.php
```

---

## 📞 **Need Help?**

1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Test configuration**: Run the test script
3. **Verify credentials**: Check cPanel settings
4. **Monitor IPN**: Check CoinPayments dashboard

---

## 🎯 **Final .env Template**

Here's your complete .env file ready for production:

```bash
APP_NAME="Net On You"
APP_ENV=production
APP_KEY=base64:PUP/QOGO9myuUXwzAxAXanWITK3pjHJufcK6nork6qc=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Net On You"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="Net On You"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

COMMISSION_RATE=0.10
REFERRAL_BONUS=5.00
MIN_PAYOUT_AMOUNT=50.00

COINPAYMENTS_ENABLED=true
COINPAYMENTS_MERCHANT_ID="82fb593d8bc444d7fd126342665a3068"
COINPAYMENTS_PUBLIC_KEY="5a3e09f1e0aa0059e826cc064ed786f25c3f1e6450543314e88ecd552eeb4ddb"
COINPAYMENTS_PRIVATE_KEY="179f143D3be0d7064791f2A30ec32538fc68eee9B29745B084455D3E531e1265"
COINPAYMENTS_IPN_SECRET="529209"
COINPAYMENTS_CURRENCY2="USDT.TRC20"
COINPAYMENTS_IPN_URL="https://yourdomain.com/payments/coinpayments/ipn"
COINPAYMENTS_SANDBOX=false
SUBSCRIPTION_PRICE="39.90"
```

**Just replace the placeholders and you're ready to go!** 🚀

