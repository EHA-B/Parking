<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items and Services Management</title>
    <style>
        .container {
            display: flex;
            justify-content: space-between;
        }

        .items-section,
        .services-section {
            width: 48%;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            height: 80vh;
        }

        .form-section {
            margin-bottom: 20px;
        }

        .table-section {
            max-height: 400px;
            overflow-y: auto;
        }

        .form3 {
            display: flex;
            flex-direction: column;
            gap: 20px;
            height: 25vh;
        }
    </style>
</head>

<body>
    <section class="board">
        
    <div class="container3">
            
            <!-- Items Section -->
            <div class="items-section">
                <h2>ادارة المواد</h2>

                <!-- Create Item Form -->
                <div class="form-section">
                    <form action="{{ route('items.store') }}" method="POST">
                        @csrf
                        <div class="form3">
                            <div class="input-form">
                                <input type="text" name="item" class="inp-text" placeholder="Item Name" required>
                                <label>: اسم المادة</label>
                            </div>
                            <div class="input-form">
                                <input type="number" name="quantity" class="inp-text" placeholder="Quantity" required>
                                <label>: الكمية</label>
                            </div>
                            <div class="input-form">
                                <input type="number" step="0.01" name="price" class="inp-text" placeholder="Price"
                                    required>
                                <label>: السعر</label>
                            </div>
                            <button type="submit" class="button2">
                                <span class="button-content">إضافة مادة</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Items Table -->
                <div class="table-section">
                    <table class="table2">
                        <thead>
                            <tr>
                                <th>المادة</th>
                                <th>الكمية</th>
                                <th>السعر</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->item }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    <td>
                                        <!-- Edit Item Modal Trigger -->
                                        <button onclick="openItemEditModal({{ $item->id }}, 
                                            '{{ $item->item }}', 
                                            {{ $item->quantity }}, 
                                            {{ $item->price }})" class="serv-btn">تعديل</button>

                                        <!-- Delete Item Form -->
                                        <form action="{{ route('items.destroy', $item->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="remove"
                                                onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Services Section -->
            <div class="services-section">
                <h2>ادارة الخدمات</h2>

                <!-- Create Service Form -->
                <div class="form-section">
                    <form action="{{ route('services.store') }}" method="POST">
                        @csrf
                        <div class="form3">
                            <div class="input-form">
                                <input type="text" name="name" class="inp-text" placeholder="Service Name" required>
                                <label>: اسم الخدمة</label>
                            </div>
                            <div class="input-form">
                                <input type="number" step="0.01" name="cost" class="inp-text" placeholder="Service Cost"
                                    required>
                                <label>: تكلفة الخدمة</label>
                            </div>
                            <button type="submit" class="button2">
                                <span class="button-content">إضافة خدمة</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Services Table -->
                <div class="table-section">
                    <table class="table2">
                        <thead>
                            <tr>
                                <th>الخدمة</th>
                                <th>التكلفة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ number_format($service->cost, 2) }}</td>
                                    <td>
                                        <!-- Edit Service Modal Trigger -->
                                        <button onclick="openServiceEditModal({{ $service->id }}, 
                                            '{{ $service->name }}', 
                                            {{ $service->cost }})" class="serv-btn">تعديل</button>

                                        <!-- Delete Service Form -->
                                        <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                                            style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="remove"
                                                onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <a href="<?php echo route('dashboard.index'); ?>" class="serv-btn back">العودة للصفحة الرئيسية</a>
    </section>

    <!-- Item Edit Modal -->
    <div id="itemEditModal" class="popup">
        <div class="popup-content">
            <span class="close-popup" onclick="closeItemEditModal()">&times;</span>
            <h2>تعديل المادة</h2>
            <form id="itemEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form">
                    <div class="input-form">
                        <input type="text" name="item" id="editItemName" class="inp-text" required>
                        <label>: اسم المادة</label>
                    </div>
                    <div class="input-form">
                        <input type="number" name="quantity" id="editItemQuantity" class="inp-text" required>
                        <label>: الكمية</label>
                    </div>
                    <div class="input-form">
                        <input type="number" step="0.01" name="price" id="editItemPrice" class="inp-text" required>
                        <label>: السعر</label>
                    </div>
                    <button type="submit" class="button2">
                        <span class="button-content">تحديث</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Service Edit Modal -->
    <div id="serviceEditModal" class="popup">
        <div class="popup-content">
            <span class="close-popup" onclick="closeServiceEditModal()">&times;</span>
            <h2>تعديل الخدمة</h2>
            <form id="serviceEditForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form">
                    <div class="input-form">
                        <input type="text" name="name" id="editServiceName" class="inp-text" required>
                        <label>: اسم الخدمة</label>
                    </div>
                    <div class="input-form">
                        <input type="number" step="0.01" name="cost" id="editServiceCost" class="inp-text" required>
                        <label>: التكلفة</label>
                    </div>
                    <button type="submit" class="button2">
                        <span class="button-content">تحديث</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Item Edit Modal Functions
        function openItemEditModal(id, name, quantity, price) {
            const modal = document.getElementById('itemEditModal');
            const form = document.getElementById('itemEditForm');

            // Set form action
            form.action = `/items/${id}`;

            // Populate form fields
            document.getElementById('editItemName').value = name;
            document.getElementById('editItemQuantity').value = quantity;
            document.getElementById('editItemPrice').value = price;

            modal.classList.add('show');
        }

        function closeItemEditModal() {
            const modal = document.getElementById('itemEditModal');
            modal.classList.remove('show');
        }

        // Service Edit Modal Functions
        function openServiceEditModal(id, name, cost) {
            const modal = document.getElementById('serviceEditModal');
            const form = document.getElementById('serviceEditForm');

            // Set form action
            form.action = `/services/${id}`;

            // Populate form fields
            document.getElementById('editServiceName').value = name;
            document.getElementById('editServiceCost').value = cost;

            modal.classList.add('show');
        }

        function closeServiceEditModal() {
            const modal = document.getElementById('serviceEditModal');
            modal.classList.remove('show');
        }
    </script>
</body>

</html>