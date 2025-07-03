<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="<?php echo asset('app.css'); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers Management</title>
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <style>
        .history-btn {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 5px;
        }

        .history-btn:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <section class="board">
        <div class="list-container1">
            <br>
            <div class="boton">
                <a href="<?php echo route('customers.create'); ?>" class="cta "><span>أضف عميلاً</span></a>
                <a href="<?php echo route('dashboard.index'); ?>" class="serv-btn back">العودة للصفحة الرئيسية</a>
            </div>

            <?php if (session('success')): ?>
                <div class="alert alert-success"> 
                    <?php echo session('success'); ?>
                </div>
            <?php endif; ?>

            <!-- Search Bar -->
            <div class="search-container" style="margin-bottom: 15px; display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: center;">
                    <input type="text" id="customerTableSearch" class="inp-text" placeholder="بحث عن عميل..."
                        style="flex: 1; padding: 4px; border-radius: 5px; margin-left:5px">
                    <label for="customerTableSearch" style="padding-left: 5px">:بحث عن عميل</label>
                </div>
            </div>

            <table class="table1">
                <tr>
                    <th>ID</th>
                    <th>الاسم</th>
                    <th>رقم الهاتف</th>
                    <th>عدد الساعات</th>
                    <th>الإجراءات</th>
                </tr>
                <tbody>
                    <?php if (count($customers) > 0): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr class="data">
                                <td><?php echo htmlspecialchars($customer->id); ?></td>
                                <td><?php echo htmlspecialchars($customer->name); ?></td>
                                <td><?php echo htmlspecialchars($customer->phone); ?></td>
                                <td><?php echo htmlspecialchars(number_format($customer->hours ?? 0, 2)); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($customer->hasMonthlySubscription()): ?>
                                            <a href="<?php echo route('dashboard.status-history', $customer->id); ?>"
                                                class="history-btn">التاريخ</a>
                                        <?php endif; ?>
                                        <a href="<?php echo route('customers.show', $customer->id); ?>" class="serv-btn">عرض</a>
                                        <a href="<?php echo route('customers.edit', $customer->id); ?>"
                                            class="serv-btn">تعديل</a>
                                        <form action="<?php echo route('customers.destroy', $customer->id); ?>" method="POST"
                                            style="display:inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                            <button type="submit" class="remove"
                                                onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</button>
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
    <script>
    // Debounce function to limit how often a function can be called
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Search functionality for customers table
    const debouncedCustomerSearch = debounce(function (searchText) {
        const searchTerms = searchText.toLowerCase().split(' ').filter(term => term.length > 0);
        const tableRows = document.querySelectorAll('.table1 tbody tr.data');

        tableRows.forEach(row => {
            const rowText = Array.from(row.querySelectorAll('td'))
                .map(cell => cell.textContent.trim().toLowerCase())
                .join(' ');
            const matchesAllTerms = searchTerms.every(term => rowText.includes(term));
            row.style.display = (matchesAllTerms || searchTerms.length === 0) ? '' : 'none';
        });
    }, 250);

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('customerTableSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => debouncedCustomerSearch(e.target.value));
            searchInput.addEventListener('search', (e) => debouncedCustomerSearch(''));
        }
    });
    </script>
</body>

</html>