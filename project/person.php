<!-- person.php -->
<?php
include("db_connect.php");

// پیام‌ها
$add_message = "";
$edit_message = "";
$delete_message = "";
$status = "";

// بررسی ارسال فرم برای افزودن شخص
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == "add_person") {
        $id = $_POST['id'];
        $FirstName = $_POST['FirstName'];
        $LastName = $_POST['LastName'];
        $PhoneNumber = $_POST['PhoneNumber'];
        $Email = $_POST['Email'];

        try {
            $stmt = $conn->prepare("CALL add_person(?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $id, $FirstName, $LastName, $PhoneNumber, $Email);
            if ($stmt->execute()) {
                $add_message = "اطلاعات شخص با موفقیت ذخیره شد.";
                $status = "success";
            } else {
                throw new Exception("خطا در ذخیره اطلاعات: " . $conn->error);
            }
        } catch (Exception $e) {
            $add_message = $e->getMessage();
            $status = "error";
        }
    } elseif ($_POST['action'] == "edit_person") {
        $id = $_POST['id'];
        $FirstName = $_POST['FirstName'];
        $LastName = $_POST['LastName'];
        $PhoneNumber = $_POST['PhoneNumber'];
        $Email = $_POST['Email'];

        try {
            $stmt = $conn->prepare("CALL edit_person(?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $id, $FirstName, $LastName, $PhoneNumber, $Email);
            if ($stmt->execute()) {
                $edit_message = "اطلاعات شخص با موفقیت ویرایش شد.";
                $status = "success";
            } else {
                throw new Exception("خطا در ویرایش اطلاعات: " . $conn->error);
            }
        } catch (Exception $e) {
            $edit_message = $e->getMessage();
            $status = "error";
        }
    } elseif ($_POST['action'] == "delete_person") {
        $id = intval($_POST['id']);

        try {
            $stmt = $conn->prepare("CALL delete_person(?)");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $delete_message = "اطلاعات شخص با موفقیت حذف شد.";
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

// دریافت اطلاعات شخص برای نمایش از پروسیجر
$persons = [];
$sql = "CALL GetPersons()";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $persons[] = $row;
    }
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


    <h2 class="text-center mb-4">اطلاعات اشخاص</h2>
    <button id="toggle-add-form" class="btn btn-primary mb-3">اضافه کردن شخص</button>
    <div id="add-form" style="display: none;">
        <!-- فرم اطلاعات شخص-->
        <form method="POST" action="" id="personForm" >
            <input type="hidden" name="action" value="add_person">
            <div class="mb-3">
                <label for="id" class="form-label">کد ملی</label>
                <input type="text" class="form-control" id="id" name="id"
                       required minlength="10" maxlength="10"
                       pattern="^[0-9]{10}$"
                       oninvalid="this.setCustomValidity('کد ملی باید دقیقا 10 رقم باشد')"
                       oninput="this.setCustomValidity('')">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="FirstName" class="form-label">نام</label>
                    <input type="text" class="form-control" id="FirstName" name="FirstName"
                           required pattern="^[آ-ی\s]+$"
                           oninvalid="this.setCustomValidity('نام باید فقط حروف فارسی باشد')"
                           oninput="this.setCustomValidity('')">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="LastName" class="form-label">نام خانوادگی</label>
                    <input type="text" class="form-control" id="LastName" name="LastName"
                           required pattern="^[آ-ی\s]+$"
                           oninvalid="this.setCustomValidity('نام خانوادگی باید فقط حروف فارسی باشد')"
                           oninput="this.setCustomValidity('')">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="PhoneNumber" class="form-label">شماره تلفن</label>
                    <input type="text" class="form-control" id="PhoneNumber" name="PhoneNumber"
                           required maxlength="11" minlength="11"
                           pattern="^09[0-9]{9}$"
                           oninvalid="this.setCustomValidity('شماره موبایل باید به فرمت 09123456789 باشد')"
                           oninput="this.setCustomValidity('')">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="Email" class="form-label">ایمیل</label>
                    <input type="email" class="form-control" id="Email" name="Email"
                           required
                           oninvalid="this.setCustomValidity('ایمیل معتبر وارد کنید. مثال: example@gmail.com')"
                           oninput="this.setCustomValidity('')">
                </div>
            </div>
            <button type="submit" class="btn btn-primary ">ثبت</button>
        </form>
    </div>

    <!-- جدول اطلاعات -->
    <h3 class="mt-4">داده‌های شخص</h3>
    <div class="search-container">
        <input type="text" id="search-input" class="search-input" placeholder="جستجو در جدول (کدملی، نام و ...)">
    </div>
    <?php if (count($persons) > 0): ?>
        <table class="table table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>کدملی</th>
                    <th>نام</th>
                    <th>نام خانوادگی</th>
                    <th>شماره تلفن</th>
                    <th>ایمیل</th>
                    <th>ویرایش</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($persons as $person): ?>
                    <tr>
                        <td><?php echo $person['PersonID']; ?></td>
                        <td><?php echo htmlspecialchars($person['FirstName']); ?></td>
                        <td><?php echo htmlspecialchars($person['LastName']); ?></td>
                        <td><?php echo htmlspecialchars($person['PhoneNumber']); ?></td>
                        <td><?php echo htmlspecialchars($person['Email']); ?></td>
                        <td>
                            <button class="icon-btn edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($person)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="delete_person">
                                <input type="hidden" name="id" value="<?php echo $person['PersonID']; ?>">
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
        <input type="hidden" name="action" value="edit_person">
        <input type="hidden" name="id" id="edit-id">

        <input type="text" id="edit-FirstName" name="FirstName" placeholder="نام" required pattern="^[آ-ی\s]+$"
               oninvalid="this.setCustomValidity('نام باید فقط حروف فارسی باشد')"
               oninput="this.setCustomValidity('')">
        <input type="text" id="edit-LastName" name="LastName" placeholder="نام خانوادگی" required pattern="^[آ-ی\s]+$"
               oninvalid="this.setCustomValidity('نام خانوادگی باید فقط حروف فارسی باشد')"
               oninput="this.setCustomValidity('')">
        <input type="text" id="edit-PhoneNumber" name="PhoneNumber" placeholder="شماره تلفن" required maxlength="11" minlength="11"
               pattern="^09[0-9]{9}$"
               oninvalid="this.setCustomValidity('شماره موبایل باید به فرمت 09123456789 باشد')"
               oninput="this.setCustomValidity('')">
        <input type="email" id="edit-Email" name="Email" placeholder="ایمیل" required  oninvalid="this.setCustomValidity('ایمیل معتبر وارد کنید. مثال: example@gmail.com')"
               oninput="this.setCustomValidity('')">

        <button type="submit">ذخیره تغییرات</button>
        <button type="button" class="cancel-btn" onclick="closeEditModal()">لغو</button>
    </form>
</div>

<script>
    function openEditModal(person) {
            document.getElementById('edit-id').value = person.PersonID;
            document.getElementById('edit-FirstName').value = person.FirstName;
            document.getElementById('edit-LastName').value = person.LastName;
            document.getElementById('edit-PhoneNumber').value = person.PhoneNumber;
            document.getElementById('edit-Email').value = person.Email;

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
            this.textContent = 'اضافه کردن شخص';
        }
    });
    document.querySelector('#add-form form').addEventListener('submit', function() {
        document.getElementById('add-form').style.display = 'none';
        document.getElementById('toggle-add-form').textContent = 'اضافه کردن شخص';
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
