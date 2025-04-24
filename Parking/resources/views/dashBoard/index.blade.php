<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DashBoard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Add JsBarcode library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
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
    </style>
</head>

<body>
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

    <section class="board">
        <div class="list-container">
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

            <div class="search-container" style="margin-bottom: 15px; display: flex; align-items: center;">
                <input type="text" id="tableSearch" class="inp-text" placeholder="search..."
                    style="flex: 1; padding: 4px; border-radius: 5px; margin-left:5px">
                <label for="tableSearch" style="padding-left: 5px">:ابحث</label>
            </div>

            <table class="table1">
                <tr>
                    <th>ID</th>
                    <th>الزبون</th>
                    <th>فئة المركبة</th>
                    <th>نوع المركبة</th>
                    <th>رقم اللوحة</th>
                    <th>وقت الدخول</th>
                    <th>خدمات</th>
                    <th>تحرير</th>
                </tr>
                @foreach($parking_slots as $parking_slot)
                    <tr class="data" data-parking-slot-id="{{ $parking_slot->id }}">
                        <td onclick="showParcodePopup('{{ $parking_slot->parcode }}')" style="cursor:pointer;">
                            {{$parking_slot->id}}
                        </td>
                        <td>{{$parking_slot->vics->customer->name}}</td>
                        <td>
                            <img src="{{ $parking_slot->vics->typ == "مركبة صغيرة" ? asset('build/assets/motor.svg') : asset('build/assets/car.svg') }}"
                                alt={{$parking_slot->vics->typ == "مركبة صغيرة" ? "Motor" : "Car"}} width="40">
                        </td>
                        <td>{{$parking_slot->vics->brand}}</td>
                        <td>{{$parking_slot->vics->plate}}</td>
                        <td>{{ \Carbon\Carbon::parse($parking_slot->time_in)->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($parking_slot->vics->services->count() > 0 || $parking_slot->vics->items->count() > 0)
                                @foreach($parking_slot->vics->services as $service)
                                    @if($service->pivot->parking_slot_id == $parking_slot->id)
                                        <li>
                                            {{ $service->name }}
                                            ( التكلفة: {{ $service->cost }})
                                        </li>
                                    @endif
                                @endforeach

                                @foreach($parking_slot->vics->items as $item)
                                    <li>
                                        {{$item->item}}
                                        العدد : {{$item->pivot->item_quantity}}
                                    </li>
                                @endforeach
                            @else
                                لا يوجد خدمات!
                            @endif
                        </td>

                        <td>

                            <a href="{{ route('dashboard.checkout', ['parcode' => $parking_slot->parcode]) }}"
                                class="btn checkout-btn">خروج</a>

                            <a onclick="openServicePopup({{ $parking_slot->vics->id }}, {{ $parking_slot->id }})"
                                class="serv-btn">إضافة خدمة</a>
                        </td>

                    </tr>
                @endforeach


            </table>
        </div>
    </section>
    <section class="side">
        <div class="top-menu">
            <a href="{{route('customers.index')}}" class="user"><img src="{{ asset('build/assets/users2.svg') }}"
                    alt="customers" width="55px"></a>
            <a href="#" class="settings" onclick="openPricingPopup()"><img src="{{ asset('build/assets/price.svg') }}"
                    alt="settings" width="50px"></a>
            <a href="{{route('history.index')}}" class="history"><img src="{{ asset('build/assets/history.svg') }}"
                    alt="history" width="50px"></a>
            <a href="{{route('items-services.index')}}" class="history"><img src="{{ asset('build/assets/serv.svg') }}"
                    alt="items_services" width="50px"></a>


        </div>

        <section class="chick-in">
            <div class="oon">
                <button type="button" class="button1" onclick="showNewCustomer()">جديد</button>
                <button type="button" class="button1" onclick="showOldCustomer()">قديم</button>
            </div>
            <form action="{{route('dashboard.new')}}" id="form1" method="POST" class="new-form"
                enctype="multipart/form-data">
                @csrf

                <h2>ادخل عميل</h2>
                <div class="form">
                    <div class="input-form">
                        <input type="text" name="name" class="inp-text" placeholder="name...." id="nameInput" required>
                        <label for="nameInput">: الاسم الكامل</label>
                    </div>
                    <div class="input-form" id="newCustomerPhone">
                        <input type="text" name="phone" class="inp-text" placeholder="phone...." id="phoneInput"
                            required>
                        <label for="phoneInput">: رقم الهاتف</label>
                    </div>

                    <div class="input-form">
                        <select name="vehicle_type" class="inp-text" id="typInput">
                            <option value="مركبة صغيرة">مركبة صغيرة</option>
                            <option value="مركبة كبيرة">مركبة كبيرة</option>
                        </select>
                        <label for="typInput">: المركبة</label>
                    </div>

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
                        <input type="text" name="notes" class="inp-text" placeholder="notes...." id="notes">
                        <label for="notes">: ملاحظات</label>
                    </div>
                </div>
                <br>
                <button type="submit" class="button2"><span class="button-content">أدخل</span></button>
            </form>

            {{-- ---------------------------- --}}


            <form action="{{route('dashboard.old')}}" id="form2" class="old-form" method="POST"
                enctype="multipart/form-data" style="display:none;">
                @csrf

                <h2>اختر عميل</h2>
                <div class="form">
                    <div class="input-form">
                        <select name="customer_id" class="inp-text select2-search" id="customerSelect" required
                            onchange="populateVehicles(this)">
                            <option value="">اختر عميل قديم</option>
                            @foreach ($customers as $customer)
                                <option value="{{$customer->id}}">
                                    {{$customer->name}}
                                </option>
                            @endforeach
                        </select>
                        <label for="customerSelect">: اختيار عميل قديم</label>
                    </div>
                    <div class="input-form">
                        <select name="vehicle_choose" class="inp-text" id="vehicleTypeSelect" onchange="add_vic()"
                            required>
                            <option value="">اختر نوع المركبة</option>
                        </select>
                        <label for="vehicleTypeSelect">: نوع المركبة</label>
                    </div>

                    <div id="add_if" hidden="true">
                        <div class="input-form">
                            <select name="vehicle_type" class="inp-text" id="typInput">
                                <option value="مركبة صغيرة">مركبة صغيرة</option>
                                <option value="مركبة كبيرة">مركبة كبيرة</option>
                            </select>
                            <label for="typInput">: المركبة</label>
                        </div>

                        <div class="input-form">
                            <input type="text" name="brand" class="inp-text" placeholder="vehicle...." id="brandInput">
                            <label for="brandInput">: نوع المركبة</label>
                        </div>
                        <div class="input-form">
                            <input type="text" name="plate" class="inp-text" placeholder="plate...." id="plateInput">
                            <label for="plateInput">: رقم اللوحة</label>
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
        </section>
        <div class="input-form1">
            <input autofocus type="text" class="parcode-input" placeholder="رمز الخروج"
                style="margin-right: 10px; padding: 5px;">

            <a class="btn" onclick="updateCheckoutLink(this)">خروج</a>
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
                <button onclick="closeModal()" class="close-btn">&times;</button>
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
                            <p>{{ number_format($checkoutDetails['duration_minutes'], 2) }} minutes</p>
                            <strong>:المدة</strong>
                        </div>
                        <div class="detail">
                            <p>{{ number_format($checkoutDetails['base_parking_price'], 2) }}</p>
                            <strong>:تكلفة الوقوف</strong>
                        </div>
                        <div class="detail">
                            <p>{{ $checkoutDetails['services_price'] }}</p>
                            <strong>:تكلفة الخدمات</strong>
                        </div>
                        <div class="detail">
                            <p>{{ $checkoutDetails['items_price'] }}</p>
                            <strong>:تكلفة المواد</strong>
                        </div>
                        <div class="detail">
                            <p>{{ number_format($checkoutDetails['total_price'], 2) }}</p>
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
                                                height: 100vh; 
                                                margin: 0; 
                                                font-family: 'Cairo', sans-serif;
                                                padding: 15px;
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
                                    <body>

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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
    $(document).ready(function () {
        $('.select2-search').select2({
            placeholder: 'ابحث عن عميل...',
            dir: "rtl",
            language: {
                noResults: function () {
                    return "لا يوجد نتائج";
                }
            }
        });
    });

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
        const barcodeText = document.getElementById('barcode-text').cloneNode(true);

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

        printFrame.contentWindow.document.write(`
            <html>
                <head>
                    <style>
                        body { 
                            display: flex; 
                            flex-direction: column;
                            justify-content: center; 
                            align-items: center; 
                            height: 50vh; 
                            margin: 0; 
                            font-family: 'Cairo', sans-serif;
                            transform: translateY(5cm);
                        }
                        svg { 
                            max-width: 100%; 
                            height: auto; 
                        }
                        #datetime-text {
                            margin-bottom: 15px;
                            font-size: 16px;
                            font-weight: bold;
                        }
                        #barcode-text {
                            margin-top: 10px;
                            font-size: 18px;
                            font-weight: bold;
                        }
                    </style>
                </head>
                <body>
                  
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

    // Table search functionality
    document.getElementById('tableSearch').addEventListener('input', function () {
        const searchText = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('.table1 tr.data');

        tableRows.forEach(row => {
            let text = '';
            row.querySelectorAll('td').forEach(cell => {
                text += cell.textContent.toLowerCase() + ' ';
            });

            if (text.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    function clearSearch() {
        const searchInput = document.getElementById('tableSearch');
        searchInput.value = '';
        // Trigger the input event to show all rows
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
    }

    // Function to show parking code popup
    function showParcodePopup(parcode) {
        // Generate the barcode
        generateBarcode(parcode);

        // Update the checkout input field with the parcode
        const parcodeInput = document.querySelector('.parcode-input');
        if (parcodeInput) {
            parcodeInput.value = parcode;
        }
    }
</script>

</html>