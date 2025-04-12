<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="<?php echo asset('app.css'); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers Management</title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
</head>
<body>
    <section class="board">
        <div class="list-container">
            <a href="<?php echo route('customers.create'); ?>" class="btn btn-primary mb-3">Add New Customer</a>
            
            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <?php echo session('success'); ?>
                </div>
            <?php endif; ?>

            <table class="table1">
                <tr>
                    <th>ID</th>
                    <th>الاسم</th>
                    <th>رقم الهاتف</th>
                    <th>عدد الساعات</th>
                    <th>الإجراءات</th>
                </tr>
                <?php if(count($customers) > 0): ?>
                    <?php foreach($customers as $customer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($customer->id); ?></td>
                        <td><?php echo htmlspecialchars($customer->name); ?></td>
                        <td><?php echo htmlspecialchars($customer->phone); ?></td>
                        <td><?php echo htmlspecialchars(number_format($customer->hours ?? 0, 2)); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?php echo route('customers.show', $customer->id); ?>" class="btn btn-info">عرض</a>
                                <a href="<?php echo route('customers.edit', $customer->id); ?>" class="btn btn-warning">تعديل</a>
                                <form action="<?php echo route('customers.destroy', $customer->id); ?>" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">لا يوجد عملاء</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </section>
</body>
</html>