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
        <div class="list-container1">
            <br>
            <div class="boton">
                <a href="<?php echo route('customers.create'); ?>" class="cta "><span>أضف عميلاً</span></a>
                <a href="<?php echo route('dashboard.index'); ?>" class="serv-btn back">العودة للصفحة الرئيسية</a>
            </div>
            
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
                <tbody>
                <?php if(count($customers) > 0): ?>
                    <?php foreach($customers as $customer): ?>
                    <tr class="data">
                        <td><?php echo htmlspecialchars($customer->id); ?></td>
                        <td><?php echo htmlspecialchars($customer->name); ?></td>
                        <td><?php echo htmlspecialchars($customer->phone); ?></td>
                        <td><?php echo htmlspecialchars(number_format($customer->hours ?? 0, 2)); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?php echo route('customers.show', $customer->id); ?>" class="serv-btn">عرض</a>
                                <a href="<?php echo route('customers.edit', $customer->id); ?>" class="serv-btn">تعديل</a>
                                <form action="<?php echo route('customers.destroy', $customer->id); ?>" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <button type="submit" class="remove" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
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
                </tbody>
            </table>
        </div>
        
    </section>
</body>
</html>