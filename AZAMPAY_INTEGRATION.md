# AzamPay Integration Guide

I have set up the entire flow for clients to pay invoices using AzamPay (Mobile Money).

### 1. Setup Requirements
Before testing, you must add your AzamPay credentials to the `.env` file:

```env
AZAMPAY_CLIENT_ID=a5ed7aee-459e-4698-bc77-97d07c0e6a5e
AZAMPAY_CLIENT_SECRET=CxP6Qhg8lzIHGywba43cZ8mwD61OoiOM9M/7j+vf8g3xViapMkUs4aPUhiclQ1ugahhkP4beoLBy/feZFLIDRGdxqvucHwKrC3zmaDeViQKKcmLgroFICTyA9+hn5qBfdhN19byRnmkgqt0oqg8YLaTxTS5lRvYf52YVlSlk1VoDT9tgWFCmyDWvEyL0M3euhKzgtNF5Gq3MTwN6cmq1igkvwx9Ui/gJZVAVPOIYgiD5LNqCifbN8VFD+TyhyjEuQYlLFc25GWTunZich3vGb3/TVfccTUEl/B+ywbIWzQlaLiyJ6Jlnfde4TbiMc+5mBNoa9x7XF/yNZyuhOKbjhRqUE9/0WyKBkbpMCAp5QWMc1/smdVCT0QFl/7/49cjjr7HY4HMmkNDTqcvPTGx09konTm8Ym0AzgMgqKlF69mH2qX49sPInMpRZwqrSGuP3fd8gnfr2TzzO3xOEyKqV84H4ATM94sID5jrnfRS9kIpxOgtjZNB+2ZxCvsP4fkixIZPRJVvOOG0ObWU32PDqEbGTQN98zuDnc8cV/dMmbIMmg0QGY5A6az+DDYiCxNF7NAQt2rEOM1Dr+DJ3CH4iWpWPjJ6FfyN+lxtQbc1t3dvx7t39DVUo8sngO32lvkyeE12d2zzdzS87S3PtrRhV+ETf7vkBdbr9NARft0SUOCs=
AZAMPAY_API_KEY=32e98882-5cce-4f62-8f94-098b12e0929b
AZAMPAY_APP_NAME="AFIA ORBIT"
AZAMPAY_SANDBOX=true  # Set to false for production
```

### 2. Database Update
Run the migration to create the transactions table:
```bash
php artisan migrate
```

### 3. Features Implemented
- **Payment History**: All attempts are logged in `payment_transactions` table.
- **Mobile Checkout**: Supports Airtel, Tigo, Halotel, and AzamPesa.
- **Callback Handling**: Automatically marks invoices as paid when AzamPay confirms the transaction.
- **UI Integration**: "Pay Now" button added to the client's invoice list.

### 4. Files Created/Modified
- `database/migrations/...create_payment_transactions_table.php`
- `app/Services/AzamPayService.php`
- `app/Http/Controllers/PaymentController.php`
- `resources/views/payments/checkout.blade.php`
- `resources/views/client_portal/invoices.blade.php`
- `routes/web.php` & `routes/api.php`

### 5. Callback URL
Your callback URL for AzamPay configuration is:
`https://your-domain.com/api/payments/azampay/callback`

Ensure this URL is accessible publicly so AzamPay can reach it.
