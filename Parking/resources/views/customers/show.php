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
                <div class="det">
                    <h3 style="text-align: center;"><?php echo htmlspecialchars($customer->name); ?></h3>
                    <h4>:الاسم الكامل</h4>
                </div>
                <div class="det">
                    <h3 style="text-align: center;"><?php echo htmlspecialchars($customer->phone); ?></h3>
                    <h4>:رقم الهاتف</h4>
                </div>
                <div class="det">
                    <h3 style="text-align: center;">
                        <?php echo htmlspecialchars($customer->hours ? number_format($customer->hours, 2) : 'غير محدد'); ?>
                    </h3>
                    <h4>:عدد الساعات</h4>
                </div>


            </div>

            <h3>المركبات</h3>
            <?php if (count($customer->vics) > 0): ?>
                <table class="table1">
                    <tr>
                        <th>النوع</th>
                        <th>الماركة</th>
                        <th>رقم اللوحة</th>
                    </tr>
                    <tbody>
                        <?php foreach ($customer->vics as $vic): ?>
                            <tr class="data">
                                <td><?php echo htmlspecialchars($vic->typ); ?></td>
                                <td><?php echo htmlspecialchars($vic->brand); ?></td>
                                <td><?php echo htmlspecialchars($vic->plate); ?></td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
            <?php else: ?>
                <p>لا توجد مركبات مسجلة لهذا العميل</p>
            <?php endif; ?>

            <div class="form-actions">
                <a href="<?php echo route('customers.edit', $customer->id); ?>" class="serv-btn">تعديل</a>
                <form action="<?php echo route('customers.destroy', $customer->id); ?>" method="POST"
                    style="display:inline-block;">
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <button type="submit" class="remove" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
                </form>
                <a href="<?php echo route('customers.index'); ?>" class="serv-btn back">العودة للعملاء</a>
            </div>
        </div>
    </section>
</body>

</html>