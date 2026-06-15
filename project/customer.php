<!-- customer.php -->
<?php
include("db_connect.php");

// پیام‌ها
$add_message = "";
$edit_message = "";
$delete_message = "";
$status = "";


// بررسی ارسال فرم برای افزودن مشتری
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == "add_customer") {
        $id = $_POST['id'];
        $Address = $_POST['Address'];
        $JoinDate = $_POST['JoinDate'];
    
        try {
            $stmt = $conn->prepare("CALL add_customer(?, ?, ?)");
            $stmt->bind_param("iss", $id, $Address, $JoinDate);
            $stmt->execute();
    
            $add_message = "اطلاعات مشتری با موفقیت ذخیره شد.";
            $status = "success";
        } catch (Exception $e) {
            $add_message = $e->getMessage();
            $status = "error";
        }
    } elseif ($_POST['action'] == "edit_customer") {
        $id = $_POST['id'];
        $Address = $_POST['Address'];
        $JoinDate = $_POST['JoinDate'];
    
        try {
            $stmt = $conn->prepare("CALL edit_customer(?, ?, ?)");
            $stmt->bind_param("iss", $id, $Address, $JoinDate);
            $stmt->execute();
    
            $edit_message = "اطلاعات مشتری با موفقیت ویرایش شد.";
            $status = "success";
        } catch (Exception $e) {
            $edit_message = $e->getMessage();
            $status = "error";
        }
    } elseif ($_POST['action'] == "delete_customer") {
        $id = intval($_POST['id']);
    
        try {
            $stmt = $conn->prepare("CALL delete_customer(?)");
            $stmt->bind_param("i", $id);
            $stmt->execute();
    
            $delete_message = "اطلاعات مشتری با موفقیت حذف شد.";
            $status = "success";
        } catch (Exception $e) {
            $delete_message = $e->getMessage();
            $status = "error";
        }
    }
}

// دریافت اطلاعات مشتری
$customers = [];
$sql = "CALL GetCustomerInfo()";  // فراخوانی پروسیجر
$result = $conn->query($sql);
if ($result) {
    // پردازش نتایج اول
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    // از next_result برای حرکت به کوئری بعدی استفاده کنید
    while ($conn->next_result()) {;}  // پردازش باقی‌مانده نتایج
}

// دریافت اطلاعات شخص
$persons = [];
$sql = "CALL GetPersonInfo()";  // فراخوانی پروسیجر
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

<!-- Home Button -->
<a href="http://localhost/jahed/index.html" class="home-btn" id="homeBtn">
    <i class="fas fa-home"></i>
    <span>صفحه اصلی</span>
</a>

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


    <h2 class="text-center mb-4">اطلاعات مشتری</h2>
    <button id="toggle-add-form" class="btn btn-primary mb-3">اضافه کردن مشتری</button>
    <div id="add-form" style="display: none;">
        <!-- فرم اطلاعات شخص-->
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_customer">
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
                    <label for="Address" class="form-label">آدرس</label>
                    <input type="text" class="form-control" id="Address" name="Address" required  oninvalid="this.setCustomValidity('آدرس مورد نیاز است')"
                           oninput="this.setCustomValidity('')">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="JoinDate" class="form-label">تاریخ عضویت</label>
                    <input type="Date" class="form-control" id="JoinDate" name="JoinDate" required  oninvalid="this.setCustomValidity('تاریخ مورد نیاز است')"
                           oninput="this.setCustomValidity('')">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">ثبت</button>
        </form>
    </div>


    <!-- جدول اطلاعات -->
    <h3 class="mt-4">داده‌های مشتری</h3>
    <div class="search-container">
        <input type="text" id="search-input" class="search-input" placeholder="جستجو در جدول (کدملی، نام، آدرس و ...)">
    </div>
    <?php if (count($persons) > 0): ?>
        <table class="table table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>کدملی</th>
                    <th>نام و نام خانوادگی</th>
                    <th>آدرس</th>
                    <th>تاریخ عضویت</th>
                    <th>ویرایش</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?php echo $customer['CustomerID']; ?></td>
                        <td><?php echo $customer['FirstName'] . " " . $customer['LastName']; ?></td>
                        <td><?php echo htmlspecialchars($customer['Address']); ?></td>
                        <td><?php echo htmlspecialchars($customer['JoinDate']); ?></td>
                        <td>
                            <button class="icon-btn edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($customer)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="delete_customer">
                                <input type="hidden" name="id" value="<?php echo $customer['CustomerID']; ?>">
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
        <input type="hidden" name="action" value="edit_customer">
        
        <label >کدملی</label>
        <input type="text" name="id" id="edit-id">

        <label >نام</label>
        <input type="text" id="edit-FirstName" name="FirstName" placeholder="نام" readonly>
        
        <label >نام خانوادگی</label>
        <input type="text" id="edit-LastName" name="LastName" placeholder="نام خانوادگی" readonly>

        <label >آدرس</label>
        <input type="text" id="edit-Address" name="Address" placeholder="آدرس" required  oninvalid="this.setCustomValidity('آدرس مورد نیاز است')"
               oninput="this.setCustomValidity('')">
        
        <label >تاریخ عضویت</label>
        <input type="date" id="edit-JoinDate" name="JoinDate" placeholder="تاریخ" required  oninvalid="this.setCustomValidity('تاریخ مورد نیاز است')"
               oninput="this.setCustomValidity('')">

        <button type="submit">ذخیره تغییرات</button>
        <button type="button" class="cancel-btn" onclick="closeEditModal()">لغو</button>
    </form>
</div>

<script>
    function openEditModal(customer) {
            document.getElementById('edit-id').value = customer.CustomerID;
            document.getElementById('edit-Address').value = customer.Address;
            document.getElementById('edit-JoinDate').value = customer.JoinDate;

            document.getElementById('edit-FirstName').value = customer.FirstName;
            document.getElementById('edit-LastName').value = customer.LastName;


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
            this.textContent = 'اضافه کردن مشتری';
        }
    });
    document.querySelector('#add-form form').addEventListener('submit', function() {
        document.getElementById('add-form').style.display = 'none';
        document.getElementById('toggle-add-form').textContent = 'اضافه کردن مشتری';
    });


    // دکمه خونه
    document.getElementById("homeBtn").addEventListener("click", function(e){
        let ripple = document.createElement("span");
        ripple.classList.add("ripple");

        let rect = this.getBoundingClientRect();
        ripple.style.left = (e.clientX - rect.left) + "px";
        ripple.style.top = (e.clientY - rect.top) + "px";

        this.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
</script>
<script src="script.js"></script>
</body>
</html>
