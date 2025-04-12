<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{ asset('app.css') }}">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DashBoard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    <tr class="data">
                        <td>{{$parking_slot->id}} </td>
                        <td>{{$parking_slot->vics->customer->name}}</td>
                        <td>
                            <img src="{{ $parking_slot->vics->typ == "مركبة صغيرة" ? asset('build/assets/motor.svg') : asset('build/assets/car.svg') }}"
                                alt={{$parking_slot->vics->typ == "مركبة صغيرة" ? "Motor" : "Car"}} width="40">
                        </td>
                        <td>{{$parking_slot->vics->brand}}</td>
                        <td>{{$parking_slot->vics->plate}}</td>
                        <td>{{$parking_slot->time_in}}</td>
                        <td>
                            @if($parking_slot->vics->services->count() > 0)

                                @foreach($parking_slot->vics->services as $service)
                                    @if($service->pivot->parking_slot_id == $parking_slot->id)
                                        <li>
                                            {{ $service->name }}
                                            ( التكلفة: {{ $service->cost }})
                                        </li>
                                    @endif
                                @endforeach

                            @else
                                لا يوجد خدمات!
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('dashboard.checkout', ['vic_id' => $parking_slot->vics->id, 'parking_slot_id' => $parking_slot->id]) }}"
                                class="btn ">خروج</a>
                            <a onclick="openServicePopup({{ $parking_slot->vics->id }}, {{ $parking_slot->id }})"
                                class="serv-btn" >إضافة خدمة</a>
                        </td>

                    </tr>
                @endforeach


            </table>
        </div>
    </section>
    <section class="side">
        <div class="top-menu">
            <a href="{{route('customers.index')}}" class="user"><img src="{{ asset('build/assets/users2.svg') }}" alt="customers" width="55px"></a>
            <a href="{{route('pricing.index')}}" class="settings"><img src="{{ asset('build/assets/settings.svg') }}" alt="settings"
                    width="50px"></a>
            <a href="{{route('history.index')}}" class="history"><img src="{{ asset('build/assets/history.svg') }}" alt="history"
                    width="50px"></a>

        </div>

        <section class="chick-in">
            <div class="oon">
                <button type="button" class="button1" onclick="showNewCustomer()">New</button>
                <button type="button" class="button1" onclick="showOldCustomer()">Old</button>
            </div>
            <form action="{{route('dashboard.new')}}" id="form1" method="POST" class="new-form"
                enctype="multipart/form-data">
                @csrf

                <h2>Enter a Customer</h2>
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

                <h2>Select a Customer</h2>
                <div class="form">
                    <div class="input-form">
                        <select name="customer_id" class="inp-text" id="customerSelect"
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
                            <option value="">Select Vehicle Type</option>
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

    <!-- Popup Form -->
    <div id="servicePopup" class="popup">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            <h2>إضافة خدمة</h2>
            <form id="serviceForm" method="POST">
                @csrf
                <div class="form">
                    <div class="input-form">
                        <input type="text" name="service_name" class="inp-text" placeholder="Service name...">
                        <label>: اسم الخدمة</label>
                    </div>
                    <div class="input-form">
                        <input type="number" name="service_price" class="inp-text" placeholder="Price...">
                        <label>: السعر</label>
                    </div>
                    <button type="submit" class="button2"><span class="button-content">إضافة</span></button>
                </div>
            </form>
        </div>
    </div>
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

        // Set the form's action URL with the correct parameters
        form.action = `/dashboard/add_service/${vicId}/${parkingSlotId}`;

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

    // Handle form submission
    document.getElementById('serviceForm').addEventListener('submit', function (e) {
        e.preventDefault();

        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('servicePopup').classList.remove('show');
                    location.reload(); // Or use a more elegant way to update the table
                }
            })
            .catch(error => console.error('Error:', error));
    });

    function add_vic()
    {
        const add_id = document.getElementById('add_if');
        const select = document.getElementById('vehicleTypeSelect');

        if(select.value === 'add_vic'){
            add_id.hidden = false;
        }
        else{
            add_id.hidden = true;
        }
    }

    // Initialize with New Customer form visible  
    window.onload = showNewCustomer;  
</script>

</html>