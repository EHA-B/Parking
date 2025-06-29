<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="<?php echo asset('app.css'); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل بيانات العميل</title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <style>
        .vehicles-section {
            margin-top: 30px;
            border-top: 2px solid #ddd;
            padding-top: 20px;
        }

        .vehicle-item {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }

        .vehicle-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .vehicle-title {
            font-weight: bold;
            color: #333;
        }

        .vehicle-actions {
            display: flex;
            gap: 10px;
        }

        .edit-vehicle-btn,
        .delete-vehicle-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .edit-vehicle-btn {
            background: #007bff;
            color: white;
        }

        .delete-vehicle-btn {
            background: #dc3545;
            color: white;
        }

        .vehicle-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .vehicle-detail {
            display: flex;
            flex-direction: column;
        }

        .vehicle-detail label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #666;
        }

        .vehicle-detail span {
            color: #333;
        }

        .add-vehicle-section {
            background: #e9ecef;
            border: 2px dashed #adb5bd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
        }

        .add-vehicle-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .vehicle-form {
            display: none;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
        }

        .vehicle-form.show {
            display: block;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .vehicle-form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .save-vehicle-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <section class="board">
        <div class="container1">
            <h2>تعديل بيانات العميل</h2>

            <!-- Customer Information Form -->
            <form action="<?php echo route('customers.update', $customer->id); ?>" method="POST">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="form-wrapper">
                    <div class="input-form">
                        <input type="text" name="name" class="inp-text" placeholder="الاسم الكامل" id="nameInput"
                            value="<?php echo old('name', $customer->name); ?>" required>
                        <label for="nameInput">: الاسم الكامل</label>
                        <?php if ($errors->has('name')): ?>
                            <div class="error-feedback">
                                <?php echo $errors->first('name'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="input-form">
                        <input type="text" name="phone" class="inp-text" placeholder="رقم الهاتف" id="phoneInput"
                            value="<?php echo old('phone', $customer->phone); ?>" required>
                        <label for="phoneInput">: رقم الهاتف</label>
                        <?php if ($errors->has('phone')): ?>
                            <div class="error-feedback">
                                <?php echo $errors->first('phone'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="input-form">
                        <input type="number" name="hours" class="inp-text" placeholder="عدد الساعات" id="hoursInput"
                            value="<?php echo old('hours', $customer->hours); ?>">
                        <label for="hoursInput">: عدد الساعات</label>
                        <?php if ($errors->has('hours')): ?>
                            <div class="error-feedback">
                                <?php echo $errors->first('hours'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <br>
                <div class="form-actions">
                    <button type="submit" class="serv-btn">تحديث بيانات العميل</button>
                    <a href="<?php echo route('customers.index'); ?>" class="remove ">إلغاء</a>
                </div>
            </form>

            <!-- Vehicles Section -->
            <div class="vehicles-section">
                <h3>مركبات العميل</h3>

                <?php if ($customer->vics->count() > 0): ?>
                    <?php foreach ($customer->vics as $vic): ?>
                        <div class="vehicle-item" id="vehicle-<?php echo $vic->id; ?>">
                            <div class="vehicle-header">
                                <div class="vehicle-title"><?php echo $vic->brand; ?> - <?php echo $vic->plate; ?></div>
                                <div class="vehicle-actions">
                                    <button class="edit-vehicle-btn"
                                        onclick="editVehicle(<?php echo $vic->id; ?>)">تعديل</button>
                                    <button class="delete-vehicle-btn"
                                        onclick="deleteVehicle(<?php echo $vic->id; ?>)">حذف</button>
                                </div>
                            </div>
                            <div class="vehicle-details">
                                <div class="vehicle-detail">
                                    <label>نوع المركبة:</label>
                                    <span><?php echo $vic->typ; ?></span>
                                </div>
                                <div class="vehicle-detail">
                                    <label>الماركة:</label>
                                    <span><?php echo $vic->brand; ?></span>
                                </div>
                                <div class="vehicle-detail">
                                    <label>رقم اللوحة:</label>
                                    <span><?php echo $vic->plate; ?></span>
                                </div>
                            </div>

                            <!-- Edit Vehicle Form (Hidden by default) -->
                            <div class="vehicle-form" id="edit-form-<?php echo $vic->id; ?>">
                                <form onsubmit="updateVehicle(event, <?php echo $vic->id; ?>)">
                                    <div class="form-row">
                                        <div class="input-form">
                                            <select name="typ" class="inp-text" required>
                                                <option value="مركبة كبيرة" <?php echo $vic->typ === 'مركبة كبيرة' ? 'selected' : ''; ?>>مركبة كبيرة</option>
                                                <option value="مركبة صغيرة" <?php echo $vic->typ === 'مركبة صغيرة' ? 'selected' : ''; ?>>مركبة صغيرة</option>
                                            </select>
                                            <label>نوع المركبة</label>
                                        </div>
                                        <div class="input-form">
                                            <input type="text" name="brand" class="inp-text" placeholder="الماركة"
                                                value="<?php echo $vic->brand; ?>" required>
                                            <label>الماركة</label>
                                        </div>
                                        <div class="input-form">
                                            <input type="text" name="plate" class="inp-text" placeholder="رقم اللوحة"
                                                value="<?php echo $vic->plate; ?>" required>
                                            <label>رقم اللوحة</label>
                                        </div>
                                    </div>
                                    <div class="vehicle-form-actions">
                                        <button type="button" class="cancel-btn"
                                            onclick="cancelEdit(<?php echo $vic->id; ?>)">إلغاء</button>
                                        <button type="submit" class="save-vehicle-btn">حفظ التغييرات</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>لا توجد مركبات مسجلة لهذا العميل</p>
                <?php endif; ?>

                <!-- Add New Vehicle Section -->
                <div class="add-vehicle-section">
                    <button class="add-vehicle-btn" onclick="showAddVehicleForm()">إضافة مركبة جديدة</button>
                </div>

                <!-- Add Vehicle Form (Hidden by default) -->
                <div class="vehicle-form" id="add-vehicle-form">
                    <form onsubmit="addVehicle(event)">
                        <div class="form-row">
                            <div class="input-form">
                                <select name="typ" class="inp-text" required>
                                    <option value="">اختر نوع المركبة</option>
                                    <option value="مركبة كبيرة">مركبة كبيرة</option>
                                    <option value="مركبة صغيرة">مركبة صغيرة</option>
                                </select>
                                <label>نوع المركبة</label>
                            </div>
                            <div class="input-form">
                                <input type="text" name="brand" class="inp-text" placeholder="الماركة" required>
                                <label>الماركة</label>
                            </div>
                            <div class="input-form">
                                <input type="text" name="plate" class="inp-text" placeholder="رقم اللوحة" required>
                                <label>رقم اللوحة</label>
                            </div>
                        </div>
                        <div class="vehicle-form-actions">
                            <button type="button" class="cancel-btn" onclick="hideAddVehicleForm()">إلغاء</button>
                            <button type="submit" class="save-vehicle-btn">إضافة المركبة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        function editVehicle(vicId) {
            // Hide all other edit forms
            document.querySelectorAll('.vehicle-form').forEach(form => {
                form.classList.remove('show');
            });

            // Show the specific edit form
            document.getElementById('edit-form-' + vicId).classList.add('show');
        }

        function cancelEdit(vicId) {
            document.getElementById('edit-form-' + vicId).classList.remove('show');
        }

        function showAddVehicleForm() {
            // Hide all edit forms
            document.querySelectorAll('.vehicle-form').forEach(form => {
                form.classList.remove('show');
            });

            // Show add form
            document.getElementById('add-vehicle-form').classList.add('show');
        }

        function hideAddVehicleForm() {
            document.getElementById('add-vehicle-form').classList.remove('show');
        }

        function updateVehicle(event, vicId) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch(`/customers/vehicles/${vicId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    typ: formData.get('typ'),
                    brand: formData.get('brand'),
                    plate: formData.get('plate')
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('تم تحديث المركبة بنجاح');
                        location.reload();
                    } else {
                        alert('حدث خطأ أثناء تحديث المركبة');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء تحديث المركبة');
                });
        }

        function addVehicle(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch(`/customers/<?php echo $customer->id; ?>/vehicles`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    typ: formData.get('typ'),
                    brand: formData.get('brand'),
                    plate: formData.get('plate')
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('تم إضافة المركبة بنجاح');
                        location.reload();
                    } else {
                        alert('حدث خطأ أثناء إضافة المركبة');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء إضافة المركبة');
                });
        }

        function deleteVehicle(vicId) {
            if (confirm('هل أنت متأكد من حذف هذه المركبة؟')) {
                fetch(`/customers/vehicles/${vicId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('تم حذف المركبة بنجاح');
                            location.reload();
                        } else {
                            alert('حدث خطأ أثناء حذف المركبة');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('حدث خطأ أثناء حذف المركبة');
                    });
            }
        }
    </script>
</body>

</html>