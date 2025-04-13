<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DashBoard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    </style>
</head>

<body>
    <section class="board">
        <div class="list-container">
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
                        <td>{{$parking_slot->id}} </td>
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
                            <a href="{{ route('dashboard.checkout', ['vic_id' => $parking_slot->vics->id, 'parking_slot_id' => $parking_slot->id]) }}"
                                class="btn "
                                onclick="return showCheckoutConfirmation(event, {{ $parking_slot->vics->id }}, {{ $parking_slot->id }}, '{{ $parking_slot->vics->customer->name }}', '{{ $parking_slot->vics->plate }}', '{{ $parking_slot->vics->typ }}', '{{ \Carbon\Carbon::parse($parking_slot->time_in)->format('Y-m-d H:i:s') }}')">خروج</a>
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

        </div>

        <section class="chick-in">
            <div class="oon">
                <button type="button" class="button1" onclick="showNewCustomer()">New</button>
                <button type="button" class="button1" onclick="showOldCustomer()">Old</button>
            </div>
            <form action="{{route('dashboard.new')}}" id="form1" method="POST" class="new-form"
                enctype="multipart/form-data">
                @csrf

                <h2>ادخل عميل</h2>
                <div class="form">
                    <div class="input-form">
                        <input autofocus type="text" name="name" class="inp-text" placeholder="name...." id="nameInput">
                        <label for="nameInput">: الاسم الكامل</label>
                    </div>
                    <div class="input-form" id="newCustomerPhone">
                        <input type="text" name="phone" class="inp-text" placeholder="phone...." id="phoneInput">
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
                        <input type="text" name="brand" class="inp-text" placeholder="vehicle...." id="brandInput">
                        <label for="brandInput">: نوع المركبة</label>
                    </div>
                    <div class="input-form">
                        <input type="text" name="plate" class="inp-text" placeholder="plate...." id="plateInput">
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
                        <select name="customer_id" class="inp-text select2-search" id="customerSelect"
                            onchange="populateVehicles(this)">
                            <option value="">Select an Old Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{$customer->id}}">
                                    {{$customer->name}}
                                </option>
                            @endforeach
                        </select>
                        <label for="customerSelect">: اختيار عميل قديم</label>
                    </div>
                    <div class="input-form">
                        <select name="vehicle_choose" class="inp-text" id="vehicleTypeSelect" onchange="add_vic()">
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
            <span class="close-popup">&times;</span>
            <h2>إضافة خدمة</h2>
            <form id="serviceForm" method="POST">
                @csrf
                <div class="form">
                    <select name="service_select" class="inp-text" id="service_select">
                        <option value="choose">اختر خدمة</option>
                        @foreach ($services as $service)
                            <option value="{{$service->id}}">
                                {{$service->name}} : التكلفة {{$service->cost}}
                            </option>
                        @endforeach
                    </select>
                    <label for="service_select">: اختيار خدمة</label>

                    <select name="item_select" class="inp-text" id="item_select">
                        <option value="choose">اختر مادة</option>
                        @foreach ($items as $item)
                            <option value="{{$item->id}}">
                                {{$item->item}} : السعر {{$item->price}} باقي : {{$item->quantity}}
                            </option>
                        @endforeach
                    </select>
                    <label for="item_select">: اختيار مواد</label>

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

    <!-- Checkout Confirmation Popup -->
    <div id="checkoutPopup" class="popup">
        <div class="popup-content">
            <span class="close-popup" onclick="closeCheckoutPopup()">&times;</span>
            <h2>تأكيد الخروج</h2>
            <div class="checkout-details">
                <div class="detail-row">
                    <span class="detail-label">اسم العميل:</span>
                    <span id="checkout-customer-name" class="detail-value"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">رقم اللوحة:</span>
                    <span id="checkout-plate" class="detail-value"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">نوع المركبة:</span>
                    <span id="checkout-vehicle-type" class="detail-value"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">رقم مكان الوقوف:</span>
                    <span id="checkout-parking-slot" class="detail-value"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">وقت الدخول:</span>
                    <span id="checkout-time-in" class="detail-value"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">وقت الخروج:</span>
                    <span id="checkout-time-out" class="detail-value"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">المدة (بالدقائق):</span>
                    <span id="checkout-duration" class="detail-value"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">السعر لكل دقيقة:</span>
                    <span id="checkout-price-per-minute" class="detail-value"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">السعر الإجمالي:</span>
                    <span id="checkout-total-price" class="detail-value"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">الخدمات:</span>
                    <div id="checkout-services" class="detail-value"></div>
                </div>
            </div>
            <div class="checkout-actions">
                <a id="confirm-checkout-btn" href="#" class="button2">تأكيد الخروج</a>
                <button onclick="closeCheckoutPopup()" class="button2" style="background-color: #ccc;">إلغاء</button>
            </div>
        </div>
    </div>
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

    // // Handle form submission
    // document.getElementById('serviceForm').addEventListener('submit', function (e) {
    //     e.preventDefault();

    //     fetch(this.action, {
    //         method: 'POST',
    //         body: new FormData(this),
    //         headers: {
    //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    //         }
    //     })
    //         .then(response => response.json())
    //         .then(data => {
    //             if (data.success) {
    //                 document.getElementById('servicePopup').classList.remove('show');
    //                 location.reload(); // Or use a more elegant way to update the table
    //             }
    //         })
    //         .catch(error => console.error('Error:', error));
    // });

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

    // Checkout confirmation functions
    function showCheckoutConfirmation(event, vicId, parkingSlotId, customerName, plate, vehicleType, timeIn) {
        event.preventDefault();

        // Get current time for checkout
        const now = new Date();
        const timeOut = now.toLocaleString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        // Parse the time_in string to a Date object
        // The timeIn string might be in a format that JavaScript can't parse directly
        // Let's try to parse it correctly
        let timeInDate;
        try {
            // First try direct parsing
            timeInDate = new Date(timeIn);

            // Check if the date is valid
            if (isNaN(timeInDate.getTime())) {
                // If not valid, try to parse it manually
                // Assuming the format is something like "2023-05-15 14:30:00"
                const parts = timeIn.split(/[- :]/);
                if (parts.length >= 6) {
                    timeInDate = new Date(
                        parseInt(parts[0]), // year
                        parseInt(parts[1]) - 1, // month (0-based)
                        parseInt(parts[2]), // day
                        parseInt(parts[3]), // hour
                        parseInt(parts[4]), // minute
                        parseInt(parts[5])  // second
                    );
                } else {
                    // If we can't parse it, use the current time
                    timeInDate = now;
                }
            }
        } catch (e) {
            console.error("Error parsing date:", e);
            timeInDate = now;
        }

        // Format time_in to Gregorian calendar
        const formattedTimeIn = timeInDate.toLocaleString('en-US', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        // Calculate duration in minutes with decimal precision
        const durationMs = now - timeInDate;
        const durationMinutes = (durationMs / (1000 * 60)).toFixed(2);

        // Get price per minute based on vehicle type
        let pricePerMinute = 0;
        if (vehicleType === 'مركبة صغيرة') {
            pricePerMinute = {{ $pricing->moto_price ?? 0 }};
        } else {
            pricePerMinute = {{ $pricing->car_price ?? 0 }};
        }

        // Calculate total price
        const totalPrice = (durationMinutes * pricePerMinute).toFixed(2);

        // Populate the checkout details
        document.getElementById('checkout-customer-name').textContent = customerName;
        document.getElementById('checkout-plate').textContent = plate;
        document.getElementById('checkout-vehicle-type').textContent = vehicleType;
        document.getElementById('checkout-parking-slot').textContent = parkingSlotId;
        document.getElementById('checkout-time-in').textContent = formattedTimeIn;
        document.getElementById('checkout-time-out').textContent = timeOut;
        document.getElementById('checkout-duration').textContent = durationMinutes;
        document.getElementById('checkout-price-per-minute').textContent = pricePerMinute;
        document.getElementById('checkout-total-price').textContent = totalPrice;

        // Get services for this vehicle and parking slot
        const servicesContainer = document.getElementById('checkout-services');
        servicesContainer.innerHTML = '';

        // Find the parking slot in the table
        const parkingSlot = document.querySelector(`tr[data-parking-slot-id="${parkingSlotId}"]`);
        if (parkingSlot) {
            const servicesCell = parkingSlot.querySelector('td:nth-child(7)');
            if (servicesCell) {
                servicesContainer.innerHTML = servicesCell.innerHTML;
            } else {
                servicesContainer.innerHTML = 'لا يوجد خدمات';
            }
        } else {
            servicesContainer.innerHTML = 'لا يوجد خدمات';
        }

        // Set the confirm button href
        const confirmBtn = document.getElementById('confirm-checkout-btn');
        confirmBtn.href = "{{ route('dashboard.checkout', ['vic_id' => ':vicId', 'parking_slot_id' => ':parkingSlotId']) }}"
            .replace(':vicId', vicId)
            .replace(':parkingSlotId', parkingSlotId);

        // Show the popup
        document.getElementById('checkoutPopup').classList.add('show');

        return false;
    }

    function closeCheckoutPopup() {
        document.getElementById('checkoutPopup').classList.remove('show');
    }

    // Close checkout popup when clicking outside
    window.addEventListener('click', function (event) {
        const popup = document.getElementById('checkoutPopup');
        if (event.target === popup) {
            popup.classList.remove('show');
        }
    });
</script>

</html>