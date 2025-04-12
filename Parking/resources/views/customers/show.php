<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="<?php echo asset('app.css'); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل العميل</title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
</head>

<body>
    <section class="board">
        <div class="container">
            <h2>تفاصيل العميل</h2>

            <div class="customer-details">
                <h3><?php echo htmlspecialchars($customer->name); ?></h3>
                <p><strong>رقم الهاتف:</strong> <?php echo htmlspecialchars($customer->phone); ?></p>
                <p><strong>عدد الساعات:</strong>
                    <?php echo htmlspecialchars($customer->hours ? number_format($customer->hours, 2) : 'غير محدد'); ?>
                </p>
            </div>

            <h3>المركبات</h3>
            <?php if (count($customer->vics) > 0): ?>
                <table class="table1">
                    <tr>
                        <th>النوع</th>
                        <th>الماركة</th>
                        <th>رقم اللوحة</th>
                    </tr>
                    <?php foreach ($customer->vics as $vic): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($vic->typ); ?></td>
                            <td><?php echo htmlspecialchars($vic->brand); ?></td>
                            <td><?php echo htmlspecialchars($vic->plate); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>لا توجد مركبات مسجلة لهذا العميل</p>
            <?php endif; ?>

            <div class="form-actions">
                <a href="<?php echo route('customers.edit', $customer->id); ?>" class="btn btn-warning">تعديل</a>
                <form action="<?php echo route('customers.destroy', $customer->id); ?>" method="POST"
                    style="display:inline-block;">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                </form>
                <a href="<?php echo route('customers.index'); ?>" class="btn">العودة للعملاء</a>
            </div>
        </div>
    </section>
</body>

</html>