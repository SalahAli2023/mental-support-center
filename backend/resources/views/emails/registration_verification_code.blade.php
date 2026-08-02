<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>رمز التحقق</title>
    <style>
        body {
            /* font-family: 'Tahoma', Arial, sans-serif; */
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

            direction: rtl;
            text-align: right;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #9EBF3B, #D6A29A);
            color: #ffffff;
            padding: 24px;
        }
        .content {
            padding: 32px;
            color: #0f172a;
        }
        .code-box {
            text-align: center;
            margin: 32px 0;
            font-size: 32px;
            letter-spacing: 8px;
            font-weight: bold;
            color: #9EBF3B;
            background: #f1f5f9;
            padding: 16px;
            border-radius: 8px;
        }
        .footer {
            background: #f8fafc;
            padding: 20px;
            font-size: 13px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>مرحباً بك في منصة الدعم النفسي</h2>
    </div>
    <div class="content">
        <p>شكراً لثقتك بنا! لإكمال عملية إنشاء الحساب، يرجى استخدام رمز التحقق التالي:</p>
        <div class="code-box">
            {{ $code }}
        </div>
        <p>هذا الرمز صالح لمدة 10 دقائق. لا تشاركه مع أي شخص.</p>
        <p>إذا لم تقم بطلب إنشاء حساب، يمكنك تجاهل هذا البريد.</p>
    </div>
    <div class="footer">
        منصة الدعم النفسي — جميع الحقوق محفوظة © {{ date('Y') }}
    </div>
</div>
</body>
</html>




