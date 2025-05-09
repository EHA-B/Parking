<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="<?php echo asset('app.css'); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة عميل جديد</title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
</head>

<body>
    <section class="board">
        <div class="container1">
            <h2>إضافة عميل جديد</h2>
            <form action="<?php echo route('customers.store'); ?>" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <div class="form-wrapper">
                    <div class="input-form">
                        <input type="text" name="name" class="inp-text" placeholder="الاسم الكامل" id="nameInput"
                            value="<?php echo old('name'); ?>" required>
                        <label for="nameInput">: الاسم الكامل</label>
                        <?php if ($errors->has('name')): ?>
                            <div class="error-feedback">
                                <?php echo $errors->first('name'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="input-form">
                        <input type="text" name="phone" class="inp-text" placeholder="رقم الهاتف" id="phoneInput"
                            value="<?php echo old('phone'); ?>" required>
                        <label for="phoneInput">: رقم الهاتف</label>
                        <?php if ($errors->has('phone')): ?>
                            <div class="error-feedback">
                                <?php echo $errors->first('phone'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="input-form">
                        <input type="number" name="hours" class="inp-text" placeholder="عدد الساعات" id="hoursInput"
                            value="<?php echo old('hours'); ?>">
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
                    <button type="submit" class="serv-btn">إضافة العميل</button>
                    <a href="<?php echo route('customers.index'); ?>" class="remove">إلغاء</a>
                </div>
            </form>
        </div>
    </section>
</body>

</html>