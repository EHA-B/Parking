<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status History</title>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <style>
        .history-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .history-table th,
        .history-table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #eee;
        }

        .history-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .status-in {
            color: #28a745;
        }

        .status-out {
            color: #dc3545;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .edit-button {
            padding: 5px 10px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-left: 5px;
        }

        .edit-button:hover {
            background-color: #5a6268;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .input-form {
            margin: 15px 0;
        }

        .input-form input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .input-form label {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="history-container">
        <a href="{{ route('dashboard.index') }}" class="back-button">العودة للوحة التحكم</a>

        <h2>سجل حالة الوقوف</h2>

        <table class="history-table">
            <thead>
                <tr>
                    <th>التاريخ والوقت</th>
                    <th>رقم اللوحة</th>
                    <th>نوع المركبة</th>
                    <th>الحالة</th>
                    <th>تحرير</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $record)
                    <tr>
                        <td>{{ $record->changed_at->setTimezone('Asia/Amman')->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $record->parkingSlot->vics->plate }}</td>
                        <td>{{ $record->parkingSlot->vics->brand }}</td>
                        <td class="status-{{ $record->status }}">
                            {{ $record->status === 'in' ? 'داخل' : 'خارج' }}
                        </td>
                        <td>
                            <button class="edit-button" onclick="openEditModal({{ $record->id }}, '{{ $record->changed_at->setTimezone('Asia/Amman')->format('Y-m-d\TH:i:s') }}')">
                                تعديل الوقت
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Edit Time Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>تعديل الوقت</h2>
            <form id="editTimeForm">
                <input type="hidden" id="recordId" name="record_id">
                <div class="input-form">
                    <label for="newTime">الوقت الجديد:</label>
                    <input type="datetime-local" id="newTime" name="new_time" required>
                </div>
                <button type="submit" class="button2">
                    <span class="button-content">حفظ التغييرات</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(recordId, currentTime) {
            const modal = document.getElementById('editModal');
            const recordIdInput = document.getElementById('recordId');
            const newTimeInput = document.getElementById('newTime');
            
            recordIdInput.value = recordId;
            newTimeInput.value = currentTime;
            modal.style.display = "block";
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            modal.style.display = "none";
        }

        document.getElementById('editTimeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                record_id: document.getElementById('recordId').value,
                new_time: document.getElementById('newTime').value
            };
            
            fetch('/dashboard/update-status-time', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('تم تحديث الوقت بنجاح');
                    location.reload();
                } else {
                    alert(data.message || 'حدث خطأ أثناء تحديث الوقت');
                }
            })
            .catch(error => {
                alert('حدث خطأ أثناء تحديث الوقت');
            });
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }
    </script>
</body>

</html>