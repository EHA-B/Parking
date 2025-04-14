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
                           
                                <a href="{{ route('dashboard.checkout', ['parcode' => $parking_slot->parcode]) }}" 
                                   class="btn checkout-btn" >خروج</a>
                           
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
        <div class="input-form1">
            <input type="text" class="parcode-input" placeholder="رمز الخروج" style="margin-right: 10px; padding: 5px;">

            <a href="{{ route('dashboard.checkout', ['parcode' => 'o']) }}" class="btn"
                onclick="updateCheckoutLink(this)">خروج</a>
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
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <strong>Customer Name:</strong> {{ $checkoutDetails['customer_name'] }}
                        </div>
                        <div>
                            <strong>Vehicle Type:</strong> {{ $checkoutDetails['vehicle_type'] }}
                        </div>
                        <div>
                            <strong>Vehicle Plate:</strong> {{ $checkoutDetails['vehicle_plate'] }}
                        </div>
                        <div>
                            <strong>Time In:</strong> {{ $checkoutDetails['time_in']->format('Y-m-d H:i:s') }}
                        </div>
                        <div>
                            <strong>Time Out:</strong> {{ $checkoutDetails['time_out']->format('Y-m-d H:i:s') }}
                        </div>
                        <div>
                            <strong>Duration:</strong> {{ number_format($checkoutDetails['duration_minutes'] ,2) }} minutes
                        </div>
                        <div class="col-span-2">
                            <strong>Parking Price:</strong> {{ number_format($checkoutDetails['base_parking_price'],2) }} 
                        </div>
                        <div class="col-span-2">
                            <strong>Services Price:</strong> {{ $checkoutDetails['services_price'] }} 
                        </div>
                        <div class="col-span-2">
                            <strong>Items Price:</strong> {{ $checkoutDetails['items_price'] }} 
                        </div>
                        <div class="col-span-2">
                            <strong>Total Price:</strong> {{ number_format($checkoutDetails['total_price'] ,2)}} 
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

        if (parcode) {
            linkElement.href = "{{ route('dashboard.checkout', ['parcode' => ':parcode']) }}".replace(':parcode', parcode);
        } else {
            // Optionally, you can show an alert or prevent navigation if no parcode is entered
            alert('Please enter a checkout code');
            return false;
        }
    }
</script>

</html>