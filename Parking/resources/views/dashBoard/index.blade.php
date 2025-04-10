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
                </tr>
                <tbody>
                    <tr class="data">
                        <td>1</td>
                        <td>سيطان</td>
                        <td><img src="{{ asset('build/assets/motor.svg') }}" alt="Car" width="40"></td>
                        <td>bmw</td>
                        <td>1212</td>
                        <td>10:10</td>
                        <td>Add</td>
                    </tr>
           
                </tbody>
            </table>
        </div>
    </section>
    <section class="side">
        <div class="top-menu">
            <a href="#" class="user"><img src="{{ asset('build/assets/users2.svg') }}" alt="users" width="55px"></a>
            <a href="#" class="settings"><img src="{{ asset('build/assets/settings.svg') }}" alt="settings" width="50px"></a>
            <a href="#" class="history"><img src="{{ asset('build/assets/history.svg') }}" alt="history" width="50px"></a>
          
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
                        <input autofocus type="text" name="name" class="inp-text" placeholder="name...." id="nameInput" >
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
                        <select name="vehicle_type" class="inp-text" id="vehicleTypeSelect">
                            <option value="">Select Vehicle Type</option>
                        </select>
                        <label for="vehicleTypeSelect">: نوع المركبة</label>
                    </div>

                </div>
            <br>
                <button type="submit" class="button2"><span class="button-content">أدخل</span></button>
            </form>
        </section>

    </section>
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
            })
            .catch(error => {
                console.error('Error fetching vehicles:', error);
            });
    }


    // Initialize with New Customer form visible  
    window.onload = showNewCustomer;  
</script>

</html>