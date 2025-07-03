<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الصندوق</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body>
    <div class="all">
        <a href="{{ route('dashboard.index') }}" class="button2" style="width:100px; margin:10px;">العودة للرئيسية</a>

        <h1 class="title">إدارة الصندوق</h1>
        <div class="income-outcome-container">
            <div class="income-box">
                <h2>إضافة دخل</h2>
                <form action="{{ route('box.income.store') }}" method="POST" class="form">
                    @csrf
                    <div class="input-form">
                        <label>المبلغ</label>
                        <input type="number" name="amount" class="inp-text" required>
                    </div>
                    <div class="input-form">
                        <label>العميل (اختياري)</label>
                        <select name="customer_id" class="inp-text" id="customer-select">
                            <option value="">بدون عميل</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-form">
                        <label>ملاحظات</label>
                        <input type="text" name="notes" class="inp-text">
                    </div>
                    <button type="submit" class="button2">إضافة دخل</button>
                </form>
            </div>
            <div class="outcome-box">
                <h2>إضافة مصروف</h2>
                <form action="{{ route('box.outcome.store') }}" method="POST" class="form">
                    @csrf
                    <div class="input-form">
                        <label>المبلغ</label>
                        <input type="number" name="amount" class="inp-text" required>
                    </div>
                    <div class="input-form">
                        <label>ملاحظات</label>
                        <input type="text" name="notes" class="inp-text">
                    </div>
                    <button type="submit" class="button2" style="background:#dc3545;">إضافة مصروف</button>
                </form>
            </div>
        </div>
        <!-- عرض رصيد الصندوق الحالي -->
        <div style="display:flex; justify-content:center; margin-bottom:20px;">
            <div
                style="background:#fffbe8; border:1px solid #ffc107; border-radius:8px; padding:20px 40px; min-width:220px; text-align:center; font-size:1.5em; font-weight:bold; color:#856404;">
                رصيد الصندوق الحالي: {{ number_format($currentBoxBalance, 2) }}
            </div>
        </div>
        <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 20px;">
            <button id="toggleMonthlyProfits" class="button2" type="button">عرض أرباح الأشهر</button>
            <form action="{{ route('box.calculate-month-profit') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="button2" style="background:#007bff;">حساب ربح الشهر الحالي</button>
            </form>
            <button id="toggleTransactions" class="button2" type="button">إظهار/إخفاء سجل الحركات</button>
        </div>
        <!-- قائمة أرباح الأشهر -->
        <div id="monthlyProfitsList" style="display:none; margin-bottom:20px;">
            <h2 style="text-align:center;">أرباح الأشهر</h2>
            <table class="table1">
                <thead>
                    <tr>
                        <th>الشهر</th>
                        <th>السنة</th>
                        <th>الربح</th>
                        <th>تاريخ الحساب</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyProfits as $profit)
                        <tr>
                            <td>{{ $profit->month }}</td>
                            <td>{{ $profit->year }}</td>
                            <td>{{ number_format($profit->profit, 2) }}</td>
                            <td>{{ $profit->calculated_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <h2 style="text-align:center;">سجل الحركات</h2>
        <!-- فلتر شهر/سنة وعرض الرصيد النهائي -->
        <form id="filterForm" method="GET" style="display: flex; justify-content: center; gap: 10px; margin-bottom: 10px; align-items: center;">
            <select name="month" id="filterMonth" class="inp-text" style="width: 120px;">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $m == $selectedMonth ? 'selected' : '' }}>شهر {{ $m }}</option>
                @endfor
            </select>
            <select name="year" id="filterYear" class="inp-text" style="width: 120px;">
                @for ($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ $y == $selectedYear ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <span style="font-weight:bold; color:#007bff; font-size:1.1em;">
                الرصيد النهائي لهذا الشهر:
                @if(!is_null($closingBalance))
                    {{ number_format($closingBalance, 2) }}
                @else
                    ---
                @endif
            </span>
        </form>
        <div id="transactionsLog">
        <table class="table1" id="transactionsTable">
            <thead>
                <tr>
                    <th>النوع</th>
                    <th>المبلغ</th>
                    <th>العميل</th>
                    <th>ملاحظات</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr data-month="{{ \Carbon\Carbon::parse($transaction->created_at)->month }}" data-year="{{ \Carbon\Carbon::parse($transaction->created_at)->year }}">
                        <td>{{ $transaction->type == 'income' ? 'دخل' : 'مصروف' }}</td>
                        <td>{{ $transaction->amount }}</td>
                        <td>{{ $transaction->customer ? $transaction->customer->name : '-' }}</td>
                        <td>{{ $transaction->notes }}</td>
                        <td>{{ $transaction->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#customer-select').select2({
                placeholder: "ابحث عن عميل...",
                allowClear: true,
                width: '100%'
            });
            // Toggle monthly profits list
            $('#toggleMonthlyProfits').on('click', function() {
                $('#monthlyProfitsList').toggle();
            });
            // Toggle transactions log
            $('#toggleTransactions').on('click', function() {
                $('#transactionsLog').toggle();
            });
            // عند تغيير الفلتر أرسل النموذج لجلب الرصيد الصحيح
            $('#filterMonth, #filterYear').on('change', function() {
                $('#filterForm').submit();
            });
            // فلترة سجل الحركات حسب الشهر والسنة (بعد تحميل الصفحة)
            function filterTransactions() {
                var selectedMonth = $('#filterMonth').val();
                var selectedYear = $('#filterYear').val();
                $('#transactionsTable tbody tr').each(function() {
                    var rowMonth = $(this).data('month').toString();
                    var rowYear = $(this).data('year').toString();
                    if (rowMonth === selectedMonth && rowYear === selectedYear) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
            // فلترة تلقائية عند التحميل
            filterTransactions();
        });
    </script>
</body>

</html>