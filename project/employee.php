<!-- employee.php -->
<?php
include("db_connect.php");

// پیام‌ها
$add_message = "";
$edit_message = "";
$delete_message = "";
$status = "";


// بررسی ارسال فرم برای افزودن کارمند
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == "add_employee") {
        $id = $_POST['id'];
        $Role = $_POST['Role'];
        $Salary = $_POST['Salary'];

        try {
            // اجرای پروسیجر افزودن کارمند
            $sql = "CALL AddEmployee(?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $id, $Role, $Salary);
            if ($stmt->execute()) {
                $add_message = "اطلاعات کارمند با موفقیت ذخیره شد.";
                $status = "success";
            } else {
                throw new Exception("خطا در ذخیره اطلاعات: " . $conn->error);
            }
        } catch (Exception $e) {
            $add_message = $e->getMessage();
            $status = "error";
        }
    } elseif ($_POST['action'] == "edit_employee") {
        $id = $_POST['id'];
        $Role = $_POST['Role'];
        $Salary = $_POST['Salary'];

        try {
            // اجرای پروسیجر ویرایش کارمند
            $sql = "CALL EditEmployee(?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $id, $Role, $Salary);
            if ($stmt->execute()) {
                $edit_message = "اطلاعات کارمند با موفقیت ویرایش شد.";
                $status = "success";
            } else {
                throw new Exception("خطا در ویرایش اطلاعات: " . $conn->error);
            }
        } catch (Exception $e) {
            $edit_message = $e->getMessage();
            $status = "error";
        }
    } elseif ($_POST['action'] == "delete_employee") {
        $id = intval($_POST['id']);

        try {
            // اجرای پروسیجر حذف کارمند
            $sql = "CALL DeleteEmployee(?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $delete_message = "اطلاعات کارمند با موفقیت حذف شد.";
                $status = "success";
            } else {
                throw new Exception("خطا در حذف اطلاعات: " . $conn->error);
            }
        } catch (Exception $e) {
            $delete_message = $e->getMessage();
            $status = "error";
        }
    }
}

// دریافت اطلاعات کارمندان از پروسیجر
$employees = [];
$sql = "CALL GetEmployees2()";
$result = $conn->query($sql);
if ($result) {
    // پردازش نتایج اول
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    // از next_result برای حرکت به کوئری بعدی استفاده کنید
    while ($conn->next_result()) {;}  // پردازش باقی‌مانده نتایج
}

// دریافت اطلاعات اشخاص از پروسیجر
$persons = [];
$sql = "CALL GetPersons2()";
$result = $conn->query($sql);
if ($result) {
    // پردازش نتایج اول
    while ($row = $result->fetch_assoc()) {
        $persons[] = $row;
    }
    // از next_result برای حرکت به کوئری بعدی استفاده کنید
    while ($conn->next_result()) {;}  // پردازش باقی‌مانده نتایج
}

?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت افراد</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=vazirmatn:400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

</head>
<body>

<!--برای لودینگ-->
<div id="loading" class="loading-overlay">
    <span class="loader"></span>
</div>

<div class="sidebar">
    <h3>فرم‌ها</h3>
    <ul>
        <li><a href="person.php"><i class="fas fa-user"></i> شخص</a></li>
        <li><a href="employee.php"><i class="fas fa-briefcase"></i> کارمند</a></li>
        <li><a href="customer.php"><i class="fas fa-user-tie"></i> مشتری</a></li>
        <li><a href="order.php"><i class="fas fa-shopping-cart"></i> سفارشات</a></li>
        <li><a href="curtain.php"><i class="fas fa-window-maximize"></i> پرده</a></li>
        <li><a href="peyment.php"><i class="fas fa-wallet"></i> پرداخت</a></li>
    </ul>
</div>


<div class="container mt-5">
    <!-- نمایش پیام‌ها -->
    <?php if (!empty($add_message)): ?>
        <div class="alert <?php echo $status === 'success' ? 'alert-success' : 'alert-danger'; ?>">
            <span class="alert-close" onclick="this.parentElement.style.display='none';">&times;</span>
            <?php echo $add_message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($edit_message)): ?>
        <div class="alert <?php echo $status === 'success' ? 'alert-success' : 'alert-danger'; ?>">
            <span class="alert-close" onclick="this.parentElement.style.display='none';">&times;</span>
            <?php echo $edit_message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($delete_message)): ?>
        <div class="alert <?php echo $status === 'success' ? 'alert-success' : 'alert-danger'; ?>">
            <span class="alert-close" onclick="this.parentElement.style.display='none';">&times;</span>
            <?php echo $delete_message; ?>
        </div>
    <?php endif; ?>


    <h2 class="text-center mb-4">اطلاعات کارمند</h2>
    <button id="toggle-add-form" class="btn btn-primary mb-3">اضافه کردن کارمند</button>
    <div id="add-form" style="display: none;">
        <!-- فرم اطلاعات کارمند-->
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_employee">
            <div class="mb-3">
                <label for="id" class="form-label">کدملی شخص</label>
                <select name="id" class="form-select" required>
                    <option value="" disabled selected>انتخاب شخص</option>
                    <?php foreach ($persons as $person): ?>
                    <option value="<?php echo $person['PersonID']; ?>">
                        <?php echo "کدملی: ".$person['PersonID']. " - ".$person['FirstName'] . " - " . $person['LastName']." - ".$person['PhoneNumber'] . " - " . $person['Email']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="Role" class="form-label">نقش</label>
                    <input type="text" class="form-control" id="Role" name="Role" required pattern="^[آ-ی\s]+$"
                           oninvalid="this.setCustomValidity('نقش باید فقط حروف فارسی باشد')"
                           oninput="this.setCustomValidity('')">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="Salary" class="form-label">حقوق</label>
                    <input type="Number" class="form-control" id="Salary" name="Salary" required pattern="[0-9]+"
                           oninvalid="this.setCustomValidity('حقوق باید فقط عدد باشد')"
                           oninput="this.setCustomValidity('')">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">ثبت</button>
        </form>
    </div>


    <!-- جدول اطلاعات -->
    <h3 class="mt-4">داده‌های شخص</h3>
    <div class="search-container">
        <input type="text" id="search-input" class="search-input" placeholder="جستجو در جدول (شناسه، نام، نقش و ...)">
    </div>
    <?php if (count($persons) > 0): ?>
        <table class="table table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>کدملی</th>
                    <th>نام و نام خانوادگی</th>
                    <th>نقش</th>
                    <th>حقوق</th>
                    <th>ویرایش</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                    <tr>
                        <td><?php echo $employee['EmployeeID']; ?></td>
                        <td><?php echo $employee['FirstName'] . " " . $employee['LastName']; ?></td>
                        <td><?php echo htmlspecialchars($employee['Role']); ?></td>
                        <td><?php echo htmlspecialchars($employee['Salary']); ?></td>
                        <td>
                            <button class="icon-btn edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($employee)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="delete_employee">
                                <input type="hidden" name="id" value="<?php echo $employee['EmployeeID']; ?>">
                                <button class="icon-btn delete-btn" onclick="return confirm('آیا مطمئن هستید؟')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-danger">هیچ داده‌ای موجود نیست.</div>
    <?php endif; ?>
</div>

<!-- پاپ‌آپ ویرایش -->
<div class="modal-overlay" id="modal-overlay" onclick="closeEditModal()"></div>
<div class="modal" id="edit-modal">
    <h2>ویرایش اطلاعات</h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="edit_employee">
        
        <label >کدملی</label>
        <input type="text" name="id" id="edit-id" readonly>

        <label >نام</label>
        <input type="text" id="edit-FirstName" name="FirstName" placeholder="نام" readonly>
        
        <label >نام خانوادگی</label>
        <input type="text" id="edit-LastName" name="LastName" placeholder="نام خانوادگی" readonly>

        <label >نقش</label>
        <input type="text" id="edit-Role" name="Role" placeholder="نقش" required pattern="^[آ-ی\s]+$"
               oninvalid="this.setCustomValidity('نقش باید فقط حروف فارسی باشد')"
               oninput="this.setCustomValidity('')">
        
        <label >حقوق</label>
        <input type="Number" id="edit-Salary" name="Salary" placeholder="حقوق" required pattern="[0-9]+"
               oninvalid="this.setCustomValidity('حقوق باید فقط عدد باشد')"
               oninput="this.setCustomValidity('')">>

        <button type="submit">ذخیره تغییرات</button>
        <button type="button" class="cancel-btn" onclick="closeEditModal()">لغو</button>
    </form>
</div>

<script>
    function openEditModal(employee) {
            document.getElementById('edit-id').value = employee.EmployeeID;
            document.getElementById('edit-Role').value = employee.Role;
            document.getElementById('edit-Salary').value = employee.Salary;

            document.getElementById('edit-FirstName').value = employee.FirstName;
            document.getElementById('edit-LastName').value = employee.LastName;


            document.getElementById('edit-modal').classList.add('active');
            document.getElementById('modal-overlay').classList.add('active');
        }
        function closeEditModal() {
            document.getElementById('edit-modal').classList.remove('active');
            document.getElementById('modal-overlay').classList.remove('active');
        }


    // برای باز شدن فرم
    document.getElementById('toggle-add-form').addEventListener('click', function() {
        const formDiv = document.getElementById('add-form');
        if (formDiv.style.display === 'none') {
            formDiv.style.display = 'block';
            this.textContent = 'بستن فرم';
        } else {
            formDiv.style.display = 'none';
            this.textContent = 'اضافه کردن کارمند';
        }
    });
    document.querySelector('#add-form form').addEventListener('submit', function() {
        document.getElementById('add-form').style.display = 'none';
        document.getElementById('toggle-add-form').textContent = 'اضافه کردن کارمند';
    });

</script>
<script src="script.js"></script>
</body>
</html>
