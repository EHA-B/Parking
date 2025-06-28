<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DashBoard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Preload critical assets -->
    <link rel="preload" href="{{ asset('app.css') }}" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" as="style">

    <!-- Load stylesheets -->
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Load other scripts after jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js" defer></script>

    <!-- Add resource hints -->
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Add cache control headers -->
    <meta http-equiv="Cache-Control" content="public, max-age=31536000">
    <style>
        .select2-container {
            width: 250px !important;
        }

        .select2-selection {
            height: 30px !important;
            border-radius: 8px !important;
            background-color: var(--gray) !important;
            border: transparent !important;
        }

        .select2-selection:focus {
            outline: none !important;
            box-shadow: 0 0 5px var(--secondary-color) !important;
        }

        .select2-container--default .select2-selection--single {
            border: none !important;
        }

        .select2-search__field {
            direction: rtl;
        }

        /* Table body overflow styles */
        .table1 {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .body-overflow {
            display: block;
            max-height: 500px;
            overflow-y: auto;
        }

        .table1 thead {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .table1 tbody {
            display: block;
            width: 100%;
        }

        .table1 tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        /* Checkout confirmation styles */
        .checkout-details {
            margin: 20px 0;
            text-align: right;
        }

        .detail-row {
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }

        .detail-label {
            font-weight: bold;
            margin-left: 10px;
        }

        .detail-value {
            color: var(--secondary-color);
        }

        .checkout-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        /* Barcode printing styles */
        #barcode-container {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            text-align: center;
        }

        .print-button {
            background-color: var(--secondary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            border: none;
            font-family: 'Cairo', sans-serif;
        }

        .close-barcode {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 20px;
            cursor: pointer;
            color: var(--gray);
        }

        .close-barcode:hover {
            color: var(--primary-color);
        }

        #barcode-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Print button styling */
        .print-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            font-family: 'Cairo', sans-serif;
            margin: 0 10px;
        }

        .print-btn:hover {
            background-color: var(--secondary-color);
        }

        /* Add modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            height: 70%;
            max-width: 500px;
            border-radius: 8px;
            position: relative;
            overflow-y: scroll;
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            width: 25px;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Add these styles to your existing styles section */
        .notification-container {
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }

        .notification-panel {
            position: absolute;
            top: 100%;
            right: 0;
            width: 300px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .notification-header {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-content {
            flex: 1;
            cursor: pointer;
        }

        .delete-notification {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            margin-right: 10px;
            opacity: 0.6;
            transition: opacity 0.2s;
        }

        .delete-notification:hover {
            opacity: 1;
        }

        .notification-item:hover {
            background-color: #f5f5f5;
        }

        .notification-item.unread {
            background-color: #f0f7ff;
        }

        .mark-read-btn {
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 12px;
        }

        .mark-read-btn:hover {
            text-decoration: underline;
        }

        /* Add this to your existing styles */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        #paymentInfo {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        #paymentInfo p {
            margin: 10px 0;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <!-- Add modal HTML structure -->
    <div id="carModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeCarModal()">&times;</span>
            <h2>سيارات</h2>
            <div id="carModalContent" class="carModalContent">
                <form action="{{route('dashboard.new')}}" id="form1" method="POST" class="new-form"
                    enctype="multipart/form-data">
                    @csrf

                    <h2>ادخل عميل</h2>
                    <div class="form">
                        <div class="input-form">
                            <input type="text" name="name" class="inp-text" placeholder="name...." id="nameInput"
                                required>
                            <label for="nameInput">: الاسم الكامل</label>
                        </div>
                        <div class="input-form" id="newCustomerPhone">
                            <input type="text" name="phone" class="inp-text" placeholder="phone...." id="phoneInput"
                                required>
                            <label for="phoneInput">: رقم الهاتف</label>
                        </div>

                        <input type="hidden" name="vehicle_type" value="مركبة كبيرة">

                        <div class="input-form">
                            <input type="text" name="brand" class="inp-text" placeholder="vehicle...." id="brandInput"
                                required>
                            <label for="brandInput">: نوع المركبة</label>
                        </div>
                        <div class="input-form">
                            <input type="text" name="plate" class="inp-text" placeholder="plate...." id="plateInput"
                                required>
                            <label for="plateInput">: رقم اللوحة</label>
                        </div>
                        <div class="input-form">
                            <select name="parking_type" id="parkingType" onchange="toggleManualPricing()"
                                class="inp-text">
                                <option value="hourly">ساعي</option>
                                <option value="daily">يومي</option>
                                <option value="monthly">شهري</option>
                            </select>
                            <label>نوع الوقوف</label>
                        </div>
                        <div id="manualPricing" style="display: none;">

                            <div class="input-form">
                                <input type="number" name="manual_rate" class="inp-text" placeholder="أدخل السعر">
                                <label>السعر </label>
                            </div>
                        </div>
                        <div class="input-form">
                            <input type="text" name="notes" class="inp-text" placeholder="notes...." id="notes">
                            <label for="notes">: ملاحظات</label>
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="button2"><span class="button-content">أدخل</span></button>
                </form>
            </div>
        </div>
    </div>
    <!-- garage modal -->
    <div id="garageModal" class="modal">
        <div class="list-container">
            <span class="close-modal" onclick="closeGarageModal()">&times;</span>
            <br>
            <br>
            @if(session('new_parcode'))
                <div class="success-message"
                    style="background-color: var(--secondary-color); color: white; padding: 10px; margin-bottom: 10px; border-radius: 5px; text-align: center;">
                    تم إنشاء رمز وقوف السيارات بنجاح: {{ session('new_parcode') }}
                </div>
            @endif

            @if(session('success'))
                <div class="success-message"
                    style="background-color: var(--secondary-color); color: white; padding: 10px; margin-bottom: 10px; border-radius: 5px; text-align: center;">
                    {{ session('success') }}
                </div>
            @endif

            <div class="search-container"
                style="margin-bottom: 15px; display: flex; flex-direction: column; gap: 10px;">
                <!-- Regular Search -->
                <div style="display: flex; align-items: center;">
                    <input type="text" id="tableSearch" class="inp-text" placeholder="بحث عام..."
                        style="flex: 1; padding: 4px; border-radius: 5px; margin-left:5px">
                    <label for="tableSearch" style="padding-left: 5px">:بحث عام</label>
                </div>

                <!-- Filtered Search -->
                <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <select id="parkingTypeFilter" class="select2-search" >
                        <option value="all">جميع الانواع</option>
                        <option value="hourly">ساعي</option>
                        <option value="daily">يومي</option>
                        <option value="monthly">شهري</option>
                    </select>
                    <label style="padding-left: 5px">:نوع الوقوف</label>
                </div>
            </div>

            <table class="table1">
                <tr>
                    <th>ID</th>
                    <th>الزبون</th>
                    <th>فئة المركبة</th>
                    <th>نوع المركبة</th>
                    <th>رقم اللوحة</th>
                    <th>وقت الدخول</th>
                    <th>نوع الوقوف</th>
                    <th>الحالة</th>
                    <th>تحرير</th>
                </tr>
                <tbody class="body-overflow">
                    @foreach($parking_slots as $parking_slot)
                                    <tr class="data" data-parking-slot-id="{{ $parking_slot->id }}">
                                        <td onclick="showParcodePopup('{{ $parking_slot->parcode }}')" style="cursor:pointer;">
                                            {{$parking_slot->id}}
                                        </td>
                                        <td>{{$parking_slot->vics->customer->name}}</td>
                                        <td>
                                            <img src="{{ $parking_slot->vics->typ == "مركبة صغيرة" ? asset('build/assets/motor.svg') : asset('build/assets/car.svg') }}"
                                                alt={{$parking_slot->vics->typ == "مركبة صغيرة" ? "Motor" : "Car"}} width="40"
                                                loading="lazy">
                                        </td>
                                        <td>{{$parking_slot->vics->brand}}</td>
                                        <td>{{$parking_slot->vics->plate}}</td>
                                        <td>{{ \Carbon\Carbon::parse($parking_slot->time_in)->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            {{
                        $parking_slot->parking_type === 'hourly' ? 'ساعي' :
                        ($parking_slot->parking_type === 'daily' ? 'يومي' : 'شهري')
                                        }}
                                        </td>
                                        <td>
                                            <div style="display: flex; flex-direction: row; gap: 10px; align-items: center;">
                                                @if($parking_slot->parking_type === 'monthly')
                                                    <p>خارج</p>
                                                    <div>
                                                        <label class="switch">
                                                            <input type="checkbox" class="status-toggle"
                                                                data-parking-slot-id="{{ $parking_slot->id }}" {{ $parking_slot->status === 'in' ? 'checked' : '' }}>
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </div>
                                                    <p>داخل</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="action-td">
                                            <button onclick="openServicePopup({{ $parking_slot->vics->id }}, {{ $parking_slot->id }})"
                                                class="serv-btn">
                                                <i class="fas fa-plus"></i> خدمة
                                            </button>
                                            <a href="{{ route('dashboard.checkout', ['parcode' => $parking_slot->parcode]) }}"
                                                class="btn checkout-btn">خروج</a>
                                            @if($parking_slot->parking_type === 'monthly')
                                                <a href="{{ route('dashboard.status-history', $parking_slot->vics->customer->id) }}"
                                                    class="serv-btn">
                                                    حركة المركبة
                                                </a>
                                            @endif
                                            @if($parking_slot->parking_type === 'monthly')
                                                <button onclick="openPaymentModal({{ $parking_slot->id }})" class="serv-btn">
                                                    <i class="fas fa-money-bill"></i> دفع
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                    @endforeach
                </tbody>


            </table>
        </div>
    </div>
    <!-- Add motor modal HTML structure -->
    <div id="motorModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeMotorModal()">&times;</span>
            <h2>دراجات نارية</h2>
            <div id="motorModalContent" class="carModalContent">
                <form action="{{route('dashboard.new')}}" id="motorForm" method="POST" class="new-form"
                    enctype="multipart/form-data">
                    @csrf

                    <h2>ادخل عميل</h2>
                    <div class="form">
                        <div class="input-form">
                            <input type="text" name="name" class="inp-text" placeholder="name...." id="motorNameInput"
                                required>
                            <label for="motorNameInput">: الاسم الكامل</label>
                        </div>
                        <div class="input-form" id="motorCustomerPhone">
                            <input type="text" name="phone" class="inp-text" placeholder="phone...."
                                id="motorPhoneInput" required>
                            <label for="motorPhoneInput">: رقم الهاتف</label>
                        </div>

                        <input type="hidden" name="vehicle_type" value="مركبة صغيرة">

                        <div class="input-form">
                            <input type="text" name="brand" class="inp-text" placeholder="vehicle...."
                                id="motorBrandInput" required>
                            <label for="motorBrandInput">: نوع المركبة</label>
                        </div>
                        <div class="input-form">
                            <input type="text" name="plate" class="inp-text" placeholder="plate...."
                                id="motorPlateInput" required>
                            <label for="motorPlateInput">: رقم اللوحة</label>
                        </div>
                        <div class="input-form">
                            <select name="parking_type" id="motorParkingType" onchange="toggleMotorManualPricing()"
                                class="inp-text">
                                <option value="hourly">ساعي</option>
                                <option value="daily">يومي</option>
                                <option value="monthly">شهري</option>
                            </select>
                            <label>نوع الوقوف</label>
                        </div>
                        <div id="motorManualPricing" style="display: none;">
                            <div class="input-form">
                                <input type="number" name="manual_rate" class="inp-text" placeholder="أدخل السعر">
                                <label>السعر </label>
                            </div>
                        </div>
                        <div class="input-form">
                            <input type="text" name="notes" class="inp-text" placeholder="notes...." id="motorNotes">
                            <label for="motorNotes">: ملاحظات</label>
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="button2"><span class="button-content">أدخل</span></button>
                </form>
            </div>
        </div>
    </div>

    <!-- Barcode overlay -->
    <div id="barcode-overlay"></div>

    <!-- Hidden barcode container -->
    <div id="barcode-container">
        <span class="close-barcode" onclick="closeBarcode()">&times;</span>
        <h3>رمز وقوف السيارات</h3>
        <svg id="barcode"></svg>
        <div id="barcode-text"></div>
        <button class="print-button" onclick="printBarcode()">طباعة الباركود</button>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePaymentModal()">&times;</span>
            <h2>دفع شهري</h2>
            <div id="paymentInfo">
                <p>المبلغ الكلي: <span id="totalAmount">0</span></p>
                <p>المبلغ المدفوع: <span id="paidAmount">0</span></p>
                <p>المبلغ المتبقي: <span id="remainingAmount">0</span></p>
            </div>
            <form id="paymentForm">
                <div class="input-form">
                    <input type="number" name="amount" id="paymentAmount" class="inp-text" required>
                    <label>المبلغ</label>
                </div>
                <div class="input-form">
                    <select hidden name="payment_method" id="paymentMethod" class="inp-text" required>
                        <option value="cash">نقدي</option>

                    </select>

                </div>
                <div class="input-form">
                    <input type="text" name="notes" id="paymentNotes" class="inp-text">
                    <label>ملاحظات</label>
                </div>
                <button type="submit" class="button2">
                    <span class="button-content">تأكيد الدفع</span>
                </button>
            </form>
        </div>
    </div>

    <section class="left-side">

        <div class="cards-contianer">
            <div class="top-menu">
                <div class="input-form1">
                    <input autofocus type="text" class="parcode-input" placeholder="رمز الخروج"
                        style="margin-right: 10px; padding: 5px; background-color: gray;">

                    <a class="btn" onclick="updateCheckoutLink(this)">خروج</a>
                </div>
                <div class="notification-container" style="position: relative; margin-right: 10px; width=50px">
                    <img src="{{ asset('build/assets/notification.svg') }}" alt="notifications" width="40"
                        style="cursor: pointer;" onclick="toggleNotifications()" loading="lazy">
                    <div id="notificationBadge" class="notification-badge" style="display: none;">0</div>
                    <div id="notificationPanel" class="notification-panel" style="display: none;">
                        <div class="notification-header">
                            <h3>الإشعارات</h3>
                            <button onclick="markAllAsRead()" class="mark-read-btn">تحديد الكل كمقروء</button>
                        </div>
                        <div id="notificationList" class="notification-list">
                            <!-- Notifications will be populated here -->
                        </div>
                    </div>
                </div>
                <a href="{{route('customers.index')}}" class="user" id="pass"><img
                        src="{{ asset('build/assets/users2.svg') }}" alt="customers" width="45px" loading="lazy"></a>
                <a href="{{ route('pricing.index') }}" class="settings" id="pass1"><img
                        src="{{ asset('build/assets/price.svg') }}" alt="settings" width="40px" loading="lazy"></a>
                <a href="{{route('history.index')}}" class="history" id="pass2"><img
                        src="{{ asset('build/assets/history.svg') }}" alt="history" width="40px" loading="lazy"></a>
                <a href="{{route('items-services.index')}}" class="history" id="pass3"><img
                        src="{{ asset('build/assets/serv.svg') }}" alt="items_services" width="40px" loading="lazy"></a>
                <script>
                    document.getElementById('pass').addEventListener('click', function (event) {
                        var password = prompt("ادخل كلمة المرور:");
                        if (password !== "123456") {
                            event.preventDefault(); // Block navigation if password is wrong
                            alert("كلمة المرور غير صحيحة");
                        }
                        // If correct, navigation proceeds
                    });
                    document.getElementById('pass1').addEventListener('click', function (event) {
                        var password = prompt("ادخل كلمة المرور:");
                        if (password !== "123456") {
                            event.preventDefault(); // Block navigation if password is wrong
                            alert("كلمة المرور غير صحيحة");
                        }
                        // If correct, navigation proceeds
                    });
                    document.getElementById('pass2').addEventListener('click', function (event) {
                        var password = prompt("ادخل كلمة المرور:");
                        if (password !== "123456") {
                            event.preventDefault(); // Block navigation if password is wrong
                            alert("كلمة المرور غير صحيحة");
                        }
                        // If correct, navigation proceeds
                    });
                    document.getElementById('pass3').addEventListener('click', function (event) {
                        var password = prompt("ادخل كلمة المرور:");
                        if (password !== "123456") {
                            event.preventDefault(); // Block navigation if password is wrong
                            alert("كلمة المرور غير صحيحة");
                        }
                        // If correct, navigation proceeds
                    });
                </script>

            </div>
            <div class="cards">
                <div class="card-body" onclick="carFormPopup()">
                    <h1>سيارات</h1>
                    <div class="card-img">
                        <img src="{{ asset('build/assets/car.jpg') }}" loading="lazy" />
                    </div>
                </div>
                <div class="card-body" onclick="motorFormPopup()">
                    <h1>دراجات نارية</h1>
                    <div class="card-img">
                        <img src="{{ asset('build/assets/motor.jpg') }}" loading="lazy" />
                    </div>
                </div>
                <div class="card-body" onclick="garageFormPopup()">
                    <h1>الكراج</h1>
                    <div class="card-img">
                        <img src="{{ asset('build/assets/parking.jpg') }}" loading="lazy" />
                    </div>
                </div>

            </div>

        </div>

    </section>



    <!-- Pricing Popup -->
    <div id="pricingPopup" class="popup">
        <div class="popup-content">
            <span class="close-popup" onclick="closePricingPopup()">&times;</span>
            <h2>ادارة الاسعار</h2>
            <div id="pricingSuccessMessage" class="success-message" style="display: none;">
                تم تحديث الأسعار بنجاح
            </div>
            <form id="pricingForm" action="{{ route('pricing.update') }}" method="POST">
                @csrf
                <div class="form">
                    <div class="input-form">
                        <input type="number" step="0.01" name="car_price" class="inp-text"
                            value="{{ old('car_price', $pricing->car_price ?? 0) }}" required>
                        <label>سعر الدقيقة للسيارات</label>
                    </div>

                    <div class="input-form">
                        <input type="number" step="0.01" name="moto_price" class="inp-text"
                            value="{{ old('moto_price', $pricing->moto_price ?? 0) }}" required>
                        <label>سعر دقيقة للمتورات</label>
                    </div>

                    <button type="submit" class="button2">
                        <span class="button-content">عدل الاسعار</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Popup Form -->
    <div id="servicePopup" class="popup">
        <div class="popup-content">
            <span class="close-popup" onclick="closeServicesPopup()">&times;</span>
            <h2>إضافة خدمة</h2>
            <form id="serviceForm" method="POST">
                @csrf
                <div class="form">
                    <div class="input-form">
                        <select name="service_select" class="inp-text" id="service_select">
                            <option value="choose">اختر خدمة</option>
                            @foreach ($services as $service)
                                <option value="{{$service->id}}">
                                    {{$service->name}} : التكلفة {{$service->cost}}
                                </option>
                            @endforeach
                        </select>
                        <label for="service_select">: اختيار خدمة</label>
                    </div>
                    <div class="input-form">
                        <select name="item_select" class="inp-text" id="item_select">
                            <option value="choose">اختر مادة</option>
                            @foreach ($items as $item)
                                <option value="{{$item->id}}">
                                    {{$item->item}} : السعر {{$item->price}} باقي : {{$item->quantity}}
                                </option>
                            @endforeach
                        </select>
                        <label for="item_select">: اختيار مواد</label>
                    </div>

                    <div class="input-form">
                        <input type="number" name="item_quantity" class="inp-text" placeholder="item_quantity...."
                            id="item_quantity">
                        <label for="item_quantity">: العدد</label>
                    </div>

                    {{-- <div class="input-form">

                        <input type="text" name="service_name" class="inp-text" placeholder="Service name...">
                        <label>: اسم الخدمة</label>
                    </div> --}}

                    <button type="submit" class="button2"><span class="button-content">إضافة</span></button>
                </div>
            </form>
        </div>
    </div>


    @if(isset($checkoutDetails))
        <div id="checkoutModal" class="chick-out-con">
            <div class="chick-out-header">
                <h2>تأكيد الخروج</h2>

            </div>
            <div class="chick-out-items">
                <div>
                    <strong>اسم العميل:</strong> {{ $checkoutDetails['customer_name'] }}
                </div>
                <div class="relative flex-auto p-6">
                    <div class="details-con">

                        <div class="detail">
                            <p> {{ $checkoutDetails['vehicle_type'] }}</p>
                            <strong>:فئة المركبة</strong>
                        </div>
                        <div class="detail">
                            <p>{{ $checkoutDetails['vehicle_plate'] }}</p>
                            <strong>:رقم اللوحة</strong>
                        </div>
                        <div class="detail">
                            <p>{{ $checkoutDetails['time_in']->format('Y-m-d H:i:s') }}</p>
                            <strong>:وقت الدخول</strong>
                        </div>
                        <div class="detail">
                            <p>{{ $checkoutDetails['time_out']->format('Y-m-d H:i:s') }}</p>
                            <strong>:وقت الخروج</strong>
                        </div>
                        <div class="detail">
                            @if($checkoutDetails['parking_type'] === 'hourly')
                                <p>{{ ceil($checkoutDetails['duration_minutes'] / 60) }} ساعة</p>
                                <strong>:المدة</strong>
                            @else
                                <p>{{ number_format($checkoutDetails['duration_minutes'], 2) }} دقيقة</p>
                                <strong>:المدة</strong>
                            @endif
                        </div>
                        <div class="detail">
                            <p>{{ number_format($checkoutDetails['base_parking_price'], 2) }}</p>
                            <strong>: تكلفة الوقوف التلقائية</strong>
                        </div>
                        <div class="detail">
                            <p>{{ number_format($checkoutDetails['manual_rate'], 2) }}</p>
                            <strong>: تكلفة الوقوف الحقيقية</strong>
                        </div>
                        <div class="detail">
                            <p>{{ $checkoutDetails['services_price'] }}</p>
                            <strong>:تكلفة الخدمات</strong>
                        </div>
                        <div class="detail">
                            <p>{{ $checkoutDetails['items_price'] }}</p>
                            <strong>:تكلفة المواد</strong>
                        </div>
                        @php
                            $total = ($checkoutDetails['manual_rate'] !== null
                                ? $checkoutDetails['manual_rate']
                                : $checkoutDetails['base_parking_price']
                            ) + $checkoutDetails['items_price'] + $checkoutDetails['services_price'];
                            $roundedTotal = ceil($total / 100) * 100;
                        @endphp
                        <div class="detail">
                            <p>{{ number_format($roundedTotal, 2) }}</p>
                            <strong>:المجموع</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="chick-out-actions">
                <form action="{{ route('dashboard.confirm-checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="vic_id" value="{{ $checkoutDetails['vic_id'] }}">
                    <input type="hidden" name="parking_slot_id" value="{{ $checkoutDetails['parking_slot_id'] }}">
                    <div class="action-buttons">
                        <a href="{{route('dashboard.index')}}" onclick="closeModal()" class="cancel-btn">
                            إلغاء
                        </a>
                        <button type="button" class="print-btn" onclick="printCheckoutDetails()">
                            طباعة التفاصيل
                        </button>
                        <button type="submit" class="confirm-btn">
                            تأكيد الخروج
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalOverlay" class="fixed inset-0 z-40 bg-black opacity-25"></div>

        <script>
            function carFormPopup() {

            }



            // ============================
            function closeModal() {
                document.getElementById('checkoutModal').style.display = 'none';
                document.getElementById('modalOverlay').style.display = 'none';
            }

            // Function to print checkout details
            function printCheckoutDetails() {
                // Create a hidden iframe
                const printFrame = document.createElement('iframe');
                printFrame.style.display = 'none';
                document.body.appendChild(printFrame);

                // Clone the details container
                const detailsContainer = document.querySelector('.details-con').cloneNode(true);
                const customerName = document.querySelector('.chick-out-items > div:first-child').cloneNode(true);

                // Get current date and time
                const now = new Date();
                const dateTimeString = now.toLocaleString('ar-uk', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                // Create date/time element
                const dateTimeElement = document.createElement('div');
                dateTimeElement.id = 'datetime-text';
                dateTimeElement.textContent = dateTimeString;

                // Create title element
                const titleElement = document.createElement('h2');
                titleElement.textContent = 'تفاصيل الخروج';
                titleElement.style.textAlign = 'center';
                titleElement.style.marginBottom = '20px';

                printFrame.contentWindow.document.write(`
                                            <html>
                                                <head>
                                                    <style>
                                                        body { 
                                                            display: flex; 
                                                            flex-direction: column;
                                                            justify-content: center; 
                                                            align-items: center; 
                                                            height: 95vh; 
                                                            margin: 0; 
                                                            font-family: 'Cairo', sans-serif;
                                                            padding: 10px;
                                                        }
                                                        .details-con {
                                                            width: 100%;
                                                            max-width: 600px;
                                                            margin: 0 auto;
                                                        }
                                                        .detail {
                                                            display: flex;
                                                            justify-content: space-between;
                                                            margin-bottom: 5px;
                                                            padding: 5px 0;
                                                            border-bottom: 1px solid #eee;
                                                        }
                                                        .detail strong {
                                                            font-weight: bold;
                                                        }
                                                        #datetime-text {
                                                            margin-bottom: 15px;
                                                            font-size: 10px;
                                                            font-weight: bold;
                                                        }
                                                        h2 {
                                                            color: #333;
                                                        }
                                                    </style>
                                                </head>

                                                    ${dateTimeElement.outerHTML}
                                                    ${customerName.outerHTML}
                                                    ${detailsContainer.outerHTML}
                                                </body>
                                            </html>
                                        `);

                printFrame.contentWindow.document.close();

                // Wait for content to load before printing
                printFrame.onload = function () {
                    printFrame.contentWindow.print();

                    // Remove the iframe after printing
                    setTimeout(() => {
                        document.body.removeChild(printFrame);
                    }, 1000);
                };
            }
        </script>
    @endif
</body>
<script>
    function showNewCustomer() {
        document.getElementById('form1').style.display = 'block';
        document.getElementById('form2').style.display = 'none';

    }

    function showOldCustomer() {
        document.getElementById('form2').style.display = 'block';
        document.getElementById('form1').style.display = 'none';

    }

    function populateVehicles(customerSelect) {
        // Get the selected customer ID
        const customerId = customerSelect.value;

        // Get the vehicle type select element
        const vehicleTypeSelect = document.getElementById('vehicleTypeSelect');

        // Clear existing options
        vehicleTypeSelect.innerHTML = '<option value="">Select Vehicle Type</option>';

        // If no customer is selected, return
        if (!customerId) return;

        // Fetch vehicles for the selected customer via AJAX
        fetch(`/get-customer-vehicles/${customerId}`)
            .then(response => response.json())
            .then(vehicles => {
                // Populate the vehicle type select
                vehicles.forEach(vehicle => {
                    const option = document.createElement('option');
                    option.value = vehicle.id;
                    option.text = vehicle.brand;
                    vehicleTypeSelect.appendChild(option);
                });
                const option = document.createElement('option');
                option.value = 'add_vic';
                option.text = "...اضافة مركبة";
                vehicleTypeSelect.appendChild(option);
            })
            .catch(error => {
                console.error('Error fetching vehicles:', error);
            });
    }

    function openServicePopup(vicId, parkingSlotId) {
        const popup = document.getElementById('servicePopup');
        const form = document.getElementById('serviceForm');

        // Use the Laravel route helper with the correct route name
        form.action = "{{ route('dashboard.add_service', ['vic_id' => ':vicId', 'parking_slot_id' => ':parkingSlotId']) }}"
            .replace(':vicId', vicId)
            .replace(':parkingSlotId', parkingSlotId);

        popup.classList.add('show');
    }

    // Close popup when clicking the close button
    document.querySelector('.close-popup').addEventListener('click', function () {
        document.getElementById('servicePopup').classList.remove('show');
    });

    // Close popup when clicking outside
    window.addEventListener('click', function (event) {
        const popup = document.getElementById('servicePopup');
        if (event.target === popup) {
            popup.classList.remove('show');
        }
    });
    function add_vic() {
        const add_id = document.getElementById('add_if');
        const select = document.getElementById('vehicleTypeSelect');

        if (select.value === 'add_vic') {
            add_id.hidden = false;
        }
        else {
            add_id.hidden = true;
        }
    }

    function openPricingPopup() {
        const popup = document.getElementById('pricingPopup');
        popup.classList.add('show');
    }

    function closePricingPopup() {
        const popup = document.getElementById('pricingPopup');
        popup.classList.remove('show');
    }
    function closeServicesPopup() {
        const popup = document.getElementById('servicePopup');
        popup.classList.remove('show');
    }

    // Close pricing popup when clicking outside
    window.addEventListener('click', function (event) {
        const popup = document.getElementById('pricingPopup');
        if (event.target === popup) {
            popup.classList.remove('show');
        }
    });

    // Handle pricing form submission
    document.getElementById('pricingForm').addEventListener('submit', function (e) {
        e.preventDefault();

        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const successMessage = document.getElementById('pricingSuccessMessage');
                    successMessage.style.display = 'block';

                    // Hide the form
                    this.style.display = 'none';

                    // Close popup after 2 seconds
                    setTimeout(() => {
                        closePricingPopup();
                        // Reset the form and success message for next time
                        this.style.display = 'block';
                        successMessage.style.display = 'none';
                    }, 2000);
                } else {
                    alert(data.message || 'حدث خطأ أثناء تحديث الأسعار');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء تحديث الأسعار');
            });
    });

    // Initialize with New Customer form visible  
    window.onload = showNewCustomer;

    // Initialize Select2
  

    function updateCheckoutLink(linkElement) {
        const parcodeInput = document.querySelector('.parcode-input');
        const parcode = parcodeInput.value.trim();

        // Check if parcode is a non-empty string of digits
        if (/^\d+$/.test(parcode)) {
            // Make an AJAX request to check if the parcode exists
            fetch(`{{ route('dashboard.check-parcode') }}?parcode=${parcode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        // If parcode exists, update the link and navigate
                        linkElement.href = "{{ route('dashboard.checkout', ['parcode' => ':parcode']) }}".replace(':parcode', parcode);
                        window.location.href = linkElement.href;
                    } else {
                        // Show error message if parcode doesn't exist
                        alert('رمز الوقوف غير صحيح. يرجى التحقق من الرمز والمحاولة مرة أخرى.');
                        parcodeInput.focus();
                    }
                })
                .catch(error => {
                    console.error('Error checking parcode:', error);
                    alert('حدث خطأ أثناء التحقق من رمز الوقوف. يرجى المحاولة مرة أخرى.');
                });
        } else {
            // Prevent navigation and show a message
            alert('يرجى إدخال رمز خروج صالح (أرقام فقط)');
            parcodeInput.focus();
            return false;
        }
    }

    // Function to generate barcode
    function generateBarcode(parcode) {
        const barcodeElement = document.getElementById('barcode');
        const barcodeText = document.getElementById('barcode-text');
        const barcodeContainer = document.getElementById('barcode-container');
        const barcodeOverlay = document.getElementById('barcode-overlay');

        // Clear any previous barcode
        barcodeElement.innerHTML = '';

        // Generate the barcode
        JsBarcode(barcodeElement, parcode, {
            format: "CODE128",
            lineColor: "#000",
            width: 2,
            height: 100,
            displayValue: true
        });

        // Set the text below the barcode
        barcodeText.textContent = parcode;

        // Show the barcode container and overlay
        barcodeContainer.style.display = 'block';
        barcodeOverlay.style.display = 'block';
    }

    // Function to print barcode
    function printBarcode() {
        // Create a hidden iframe instead of opening a new window
        const printFrame = document.createElement('iframe');
        printFrame.style.display = 'none';
        document.body.appendChild(printFrame);

        const barcodeElement = document.getElementById('barcode').cloneNode(true);

        printFrame.contentWindow.document.write(`
            <html>
                <head>
                    <style>
                        @page {
                            size: auto;
                            margin: 0;
                        }
                        body { 
                            display: flex; 
                            flex-direction: column;
                            justify-content: space-between; 
                            align-items: center; 
                            height: 6cm; 
                            margin: 0; 
                            padding: 0.5cm;
                            font-family: 'Cairo', sans-serif;
                            box-sizing: border-box;
                        }
                        .notes-box {
                            width: 100%;
                            height: 2.5cm;
                            border: 1px solid #000;
                            margin-bottom: 0.5cm;
                        }
                        svg { 
                            max-width: 100%; 
                            height: 2.5cm !important;
                        }
                        @media print {
                            body {
                                margin: 0;
                                padding: 0.5cm;
                                height: 6cm;
                            }
                            .notes-box {
                                page-break-inside: avoid;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="notes-box"></div>
                    ${barcodeElement.outerHTML}
                </body>
            </html>
        `);

        printFrame.contentWindow.document.close();

        // Wait for content to load before printing
        printFrame.onload = function () {
            printFrame.contentWindow.print();

            // Remove the iframe after printing
            setTimeout(() => {
                document.body.removeChild(printFrame);
                closeBarcode();
            }, 1000);
        };
    }

    // Check for new parcode in session and generate barcode if present
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('new_parcode'))
            generateBarcode('{{ session('new_parcode') }}');
        @endif

        // Add event listener to close barcode when clicking on overlay
        document.getElementById('barcode-overlay').addEventListener('click', function () {
            closeBarcode();
        });
    });

    function closeBarcode() {
        document.getElementById('barcode-container').style.display = 'none';
        document.getElementById('barcode-overlay').style.display = 'none';
    }

    // Debounce function to limit how often a function can be called
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Regular search functionality
    const debouncedSearch = debounce(function (searchText) {
        const searchTerms = searchText.toLowerCase().split(' ').filter(term => term.length > 0);
        const tableRows = document.querySelectorAll('.table1 tbody tr.data');
        const parkingTypeFilter = document.getElementById('parkingTypeFilter').value;

        tableRows.forEach(row => {
            const rowText = Array.from(row.querySelectorAll('td'))
                .map(cell => cell.textContent.trim().toLowerCase())
                .join(' ');

            // Get the parking type from the 7th column (index 6)
            const parkingType = row.querySelectorAll('td')[6].textContent.trim().toLowerCase();

            // Check if row matches both search terms and parking type filter
            const matchesAllTerms = searchTerms.every(term => rowText.includes(term));
            const matchesParkingType = parkingTypeFilter === 'all' ||
                (parkingTypeFilter === 'hourly' && parkingType.includes('ساعي')) ||
                (parkingTypeFilter === 'daily' && parkingType.includes('يومي')) ||
                (parkingTypeFilter === 'monthly' && parkingType.includes('شهري'));

            row.style.display = (matchesAllTerms || searchTerms.length === 0) && matchesParkingType ? '' : 'none';
        });
    }, 250);

    // Event listeners
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('tableSearch');
        const parkingTypeFilter = document.getElementById('parkingTypeFilter');

        if (searchInput) {
            searchInput.addEventListener('input', (e) => debouncedSearch(e.target.value));
            searchInput.addEventListener('search', (e) => debouncedSearch(''));
        }

        if (parkingTypeFilter) {
            parkingTypeFilter.addEventListener('change', () => {
                debouncedSearch(searchInput.value);
            });
        }

        // Event delegation for notification panel
        document.addEventListener('click', function (event) {
            const panel = document.getElementById('notificationPanel');
            const container = document.querySelector('.notification-container');

            if (!container.contains(event.target)) {
                panel.style.display = 'none';
            }
        });

        // Event delegation for status toggles
        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('status-toggle')) {
                const parkingSlotId = event.target.dataset.parkingSlotId;
                
                fetch(`/dashboard/toggle-status/${parkingSlotId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                    } else {
                        event.target.checked = !event.target.checked;
                        alert(data.message);
                    }
                })
                .catch(error => {
                    event.target.checked = !event.target.checked;
                    alert('An error occurred while updating the status');
                });
            }
        });
    });

    function showParcodePopup(parcode) {
        // Generate the barcode
        generateBarcode(parcode);

        // Update the checkout input field with the parcode
        const parcodeInput = document.querySelector('.parcode-input');
        if (parcodeInput) {
            parcodeInput.value = parcode;
        }
    }

    function toggleManualPricing() {
        var parkingType = document.getElementById('parkingType').value;
        var manualPricingDiv = document.getElementById('manualPricing');

        // Show the manual pricing fields if daily or monthly is selected
        if (parkingType === 'daily' || parkingType === 'monthly') {
            manualPricingDiv.style.display = 'block';
        } else {
            manualPricingDiv.style.display = 'none';
        }
    }

    function toggleOldManualPricing() {
        var parkingType = document.getElementById('oldParkingType').value;
        var manualPricingDiv = document.getElementById('oldManualPricing');
        if (parkingType === 'daily' || parkingType === 'monthly') {
            manualPricingDiv.style.display = 'block';
        } else {
            manualPricingDiv.style.display = 'none';
        }
    }

    function carFormPopup() {
        const modal = document.getElementById('carModal');
        modal.style.display = "block";
        
        // Automatically select "مركبة كبيرة" in the vehicle type dropdown
        const vehicleTypeSelect = modal.querySelector('#typInput');
        if (vehicleTypeSelect) {
            vehicleTypeSelect.value = "مركبة كبيرة";
        }
    }
    function garageFormPopup(){
        const modal = document.getElementById('garageModal');
        modal.style.display="block"
    }
    

    function closeGarageModal() {
        const modal = document.getElementById('garageModal');
        modal.style.display = "none";
    }
    function closeCarModal() {
        const modal = document.getElementById('carModal');
        modal.style.display = "none";
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const carModal = document.getElementById('carModal');
        const motorModal = document.getElementById('motorModal');
        if (event.target == carModal) {
            carModal.style.display = "none";
        }
        if (event.target == motorModal) {
            motorModal.style.display = "none";
        }
    }

    function motorFormPopup() {
        const modal = document.getElementById('motorModal');
        modal.style.display = "block";
    }

    function closeMotorModal() {
        const modal = document.getElementById('motorModal');
        modal.style.display = "none";
    }

    function toggleMotorManualPricing() {
        var parkingType = document.getElementById('motorParkingType').value;
        var manualPricingDiv = document.getElementById('motorManualPricing');
        if (parkingType === 'daily' || parkingType === 'monthly') {
            manualPricingDiv.style.display = 'block';
        } else {
            manualPricingDiv.style.display = 'none';
        }
    }

    // Add these functions to your existing script section
    function checkExpiredSubscriptions() {
        const parkingSlots = @json($parking_slots);
        const now = new Date();
        const notifications = JSON.parse(localStorage.getItem('notifications') || '[]');
        const deletedNotifications = JSON.parse(localStorage.getItem('deletedNotifications') || '[]');
        
        console.log('Checking expired subscriptions...');
        console.log('Current time:', now);
        
        parkingSlots.forEach(slot => {
            if (slot.parking_type === 'monthly') {
                const timeIn = new Date(slot.time_in);
                console.log('Checking slot:', {
                    customer: slot.vics.customer.name,
                    plate: slot.vics.plate,
                    timeIn: timeIn,
                    parkingType: slot.parking_type
                });
                
                // Calculate the difference in days
                const diffTime = Math.abs(now - timeIn);
                const daysDiff = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                console.log('Days difference:', daysDiff);
                
                if (daysDiff >= 30) {
                    console.log('Subscription expired for:', slot.vics.customer.name);
                    const notificationExists = notifications.some(n => 
                        n.parkingSlotId === slot.id && n.type === 'expired_subscription'
                    );
                    
                    // Check if this notification was previously deleted
                    const wasDeleted = deletedNotifications.includes(slot.id);
                    
                    if (!notificationExists && !wasDeleted) {
                        console.log('Creating new notification');
                        notifications.push({
                            id: Date.now(),
                            parkingSlotId: slot.id,
                            type: 'expired_subscription',
                            message: `قضى ${slot.vics.customer.name} (${slot.vics.plate}) مدة شهر في الكراج`,
                            isRead: false,
                            createdAt: new Date().toISOString()
                        });
                    } else {
                        console.log('Notification already exists or was deleted');
                    }
                }
            }
        });
        
        localStorage.setItem('notifications', JSON.stringify(notifications));
        updateNotificationBadge();
        renderNotifications();
    }

    function updateNotificationBadge() {
        const notifications = JSON.parse(localStorage.getItem('notifications') || '[]');
        const unreadCount = notifications.filter(n => !n.isRead).length;
        const badge = document.getElementById('notificationBadge');
        
        if (unreadCount > 0) {
            badge.style.display = 'block';
            badge.textContent = unreadCount;
        } else {
            badge.style.display = 'none';
        }
    }

    function renderNotifications() {
        const notifications = JSON.parse(localStorage.getItem('notifications') || '[]');
        const notificationList = document.getElementById('notificationList');
        
        // Sort notifications by date, newest first
        notifications.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
        
        notificationList.innerHTML = notifications.map(notification => {
            const date = new Date(notification.createdAt);
            const formattedDate = date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            return `
                <div class="notification-item ${notification.isRead ? '' : 'unread'}">
                    <div class="notification-content" onclick="markAsRead(${notification.id})">
                        <div>${notification.message}</div>
                        <small>${formattedDate}</small>
                    </div>
                    <button class="delete-notification" onclick="deleteNotification(${notification.id})">
                        <p style="font-size:15px; color:red ;">Delete</p>
                    </button>
                </div>
            `;
        }).join('');
    }

    function deleteNotification(notificationId) {
        const notifications = JSON.parse(localStorage.getItem('notifications') || '[]');
        const deletedNotifications = JSON.parse(localStorage.getItem('deletedNotifications') || '[]');
        
        // Find the notification to get its parkingSlotId
        const notificationToDelete = notifications.find(n => n.id === notificationId);
        if (notificationToDelete) {
            // Add the parkingSlotId to deletedNotifications
            deletedNotifications.push(notificationToDelete.parkingSlotId);
            localStorage.setItem('deletedNotifications', JSON.stringify(deletedNotifications));
        }
        
        // Remove the notification
        const updatedNotifications = notifications.filter(n => n.id !== notificationId);
        localStorage.setItem('notifications', JSON.stringify(updatedNotifications));
        updateNotificationBadge();
        renderNotifications();
    }

    function toggleNotifications() {
        const panel = document.getElementById('notificationPanel');
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    }

    function markAsRead(notificationId) {
        const notifications = JSON.parse(localStorage.getItem('notifications') || '[]');
        const index = notifications.findIndex(n => n.id === notificationId);
        
        if (index !== -1) {
            notifications[index].isRead = true;
            localStorage.setItem('notifications', JSON.stringify(notifications));
            updateNotificationBadge();
            renderNotifications();
        }
    }

    function markAllAsRead() {
        const notifications = JSON.parse(localStorage.getItem('notifications') || '[]');
        notifications.forEach(n => n.isRead = true);
        localStorage.setItem('notifications', JSON.stringify(notifications));
        updateNotificationBadge();
        renderNotifications();
    }

    // Check for expired subscriptions when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded, checking subscriptions...');
        checkExpiredSubscriptions();
        
        // Check every minute for testing (you can change this back to 3600000 for production)
        setInterval(checkExpiredSubscriptions, 60000);
    });

    // Close notification panel when clicking outside
    document.addEventListener('click', function(event) {
        const panel = document.getElementById('notificationPanel');
        const container = document.querySelector('.notification-container');
        
        if (!container.contains(event.target)) {
            panel.style.display = 'none';
        }
    });

    // Add this to your existing JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const statusToggles = document.querySelectorAll('.status-toggle');
        
        statusToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const parkingSlotId = this.dataset.parkingSlotId;
                
                fetch(`/dashboard/toggle-status/${parkingSlotId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert(data.message);
                    } else {
                        // Revert the toggle if there was an error
                        this.checked = !this.checked;
                        alert(data.message);
                    }
                })
                .catch(error => {
                    // Revert the toggle if there was an error
                    this.checked = !this.checked;
                    alert('An error occurred while updating the status');
                });
            });
        });
    });

    // Declare variables at the top
    let currentParkingSlotId = null;
    
    function openPaymentModal(id) {
        currentParkingSlotId = id;
        const modal = document.getElementById('paymentModal');
        modal.style.display = "block";
        // Fetch payment history
        fetch(`/monthly-payments/${id}/history`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('totalAmount').textContent = data.total_amount;
                document.getElementById('paidAmount').textContent = data.paid_amount;
                document.getElementById('remainingAmount').textContent = data.remaining_amount;
                document.getElementById('paymentAmount').max = data.remaining_amount;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء جلب معلومات الدفع');
            });
        // Reset form
        document.getElementById('paymentForm').reset();
    }

    function closePaymentModal() {
        const modal = document.getElementById('paymentModal');
        modal.style.display = "none";
        currentParkingSlotId = null;
        // Reset form
        document.getElementById('paymentForm').reset();
    }

    // Add event listeners when the DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Handle form submission
        const paymentForm = document.getElementById('paymentForm');
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    amount: document.getElementById('paymentAmount').value,
                    payment_method: document.getElementById('paymentMethod').value,
                    notes: document.getElementById('paymentNotes').value
                };
                
                fetch(`/monthly-payments/${currentParkingSlotId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('تم تسجيل الدفع بنجاح');
                        closePaymentModal();
                        location.reload();
                    } else {
                        alert(data.message || 'حدث خطأ أثناء تسجيل الدفع');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء تسجيل الدفع');
                });
            });
        }

        // Handle modal close button
        const closeButton = document.querySelector('#paymentModal .close');
        if (closeButton) {
            closeButton.addEventListener('click', closePaymentModal);
        }

        // Handle clicking outside modal
        const modal = document.getElementById('paymentModal');
        if (modal) {
            modal.addEventListener('click', function(event) {
                if (event.target === this) {
                    closePaymentModal();
                }
            });
        }
    });
</script>

</html> 