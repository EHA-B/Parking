<!DOCTYPE html>
<html lang="ar">

<head>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الأسعار</title>
</head>

<body>
    <section class="board">
        <div class="container">
            <h2>إدارة أسعار الوقوف</h2>
            <a href="<?php echo route('dashboard.index'); ?>" class="serv-btn back">العودة للصفحة الرئيسية</a>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('pricing.update') }}" method="POST">
                @csrf
                <div class="form">
                    <h3>أسعار السيارات</h3>
                    <div class="input-form">
                        <input type="number" step="0.01" name="car_hourly_rate" class="inp-text"
                            value="{{ old('car_hourly_rate', $pricing->car_hourly_rate ?? 0) }}" required>
                        <label>سعر الساعة للسيارات</label>
                    </div>

                    <div class="input-form">
                        <input type="number" step="0.01" name="car_daily_rate" class="inp-text"
                            value="{{ old('car_daily_rate', $pricing->car_daily_rate ?? 0) }}" required>
                        <label>سعر اليوم للسيارات</label>
                    </div>

                    <div class="input-form">
                        <input type="number" step="0.01" name="car_monthly_rate" class="inp-text"
                            value="{{ old('car_monthly_rate', $pricing->car_monthly_rate ?? 0) }}" required>
                        <label>سعر الشهر للسيارات</label>
                    </div>

                    <h3>أسعار الدراجات النارية</h3>
                    <div class="input-form">
                        <input type="number" step="0.01" name="moto_hourly_rate" class="inp-text"
                            value="{{ old('moto_hourly_rate', $pricing->moto_hourly_rate ?? 0) }}" required>
                        <label>سعر الساعة للدراجات النارية</label>
                    </div>

                    <div class="input-form">
                        <input type="number" step="0.01" name="moto_daily_rate" class="inp-text"
                            value="{{ old('moto_daily_rate', $pricing->moto_daily_rate ?? 0) }}" required>
                        <label>سعر اليوم للدراجات النارية</label>
                    </div>

                    <div class="input-form">
                        <input type="number" step="0.01" name="moto_monthly_rate" class="inp-text"
                            value="{{ old('moto_monthly_rate', $pricing->moto_monthly_rate ?? 0) }}" required>
                        <label>سعر الشهر للدراجات النارية</label>
                    </div>

                    <button type="submit" class="button2">
                        <span class="button-content">تحديث الأسعار</span>
                    </button>
                </div>
            </form>
        </div>
    </section>
</body>

</html>