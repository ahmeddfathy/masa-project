<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رسالة جديدة من نموذج الاتصال</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            padding: 20px;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .content {
            margin-bottom: 30px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>رسالة جديدة من نموذج الاتصال</h1>
        </div>

        <div class="content">
            <div class="field">
                <div class="label">الاسم:</div>
                <div class="value">{{ $name }}</div>
            </div>

            <div class="field">
                <div class="label">البريد الإلكتروني:</div>
                <div class="value">{{ $email }}</div>
        </div>

            <div class="field">
                <div class="label">رقم الهاتف:</div>
                <div class="value">{{ $phone }}</div>
        </div>

            <div class="field">
                <div class="label">الرسالة:</div>
                <div class="value">{{ $userMessage }}</div>
            </div>
        </div>

        <div class="footer">
            <p>تم إرسال هذه الرسالة من نموذج الاتصال في موقع عدسة سوما</p>
        </div>
    </div>
</body>
</html>
