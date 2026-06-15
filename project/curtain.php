<!-- curtain.php -->
<?php
include("db_connect.php");

// پیام‌ها
$add_message = "";
$edit_message = "";
$delete_message = "";
$status = "";


// بررسی ارسال فرم برای افزودن پرده
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == "add_curtain") {
        $id = $_POST['id'];
        $Type = $_POST['Type'];
        $Color = $_POST['Color'];
        $Material = $_POST['Material'];
        $Dimensions = $_POST['Dimensions'];
        $Price = $_POST['Price'];

        try {
            $stmt = $conn->prepare("CALL AddCurtain(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $id, $Type, $Color, $Material, $Dimensions, $Price);
            if ($stmt->execute()) {
                $add_message = "اطلاعات پرده با موفقیت ذخیره شد.";
                $status = "success";
            } else {
                throw new Exception("خطا در ذخیره اطلاعات: " . $conn->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            $add_message = $e->getMessage();
            $status = "error";
        }
    } elseif ($_POST['action'] == "edit_curtain") {
        $id = $_POST['id'];
        $Type = $_POST['Type'];
        $Color = $_POST['Color'];
        $Material = $_POST['Material'];
        $Dimensions = $_POST['Dimensions'];
        $Price = $_POST['Price'];
    
        try {
            $stmt = $conn->prepare("CALL EditCurtain(?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $id, $Type, $Color, $Material, $Dimensions, $Price);
            if ($stmt->execute()) {
                $edit_message = "اطلاعات پرده با موفقیت ویرایش شد.";
                $status = "success";
            } else {
                throw new Exception("خطا در ویرایش اطلاعات: " . $conn->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            $edit_message = $e->getMessage();
            $status = "error";
        }
    } elseif ($_POST['action'] == "delete_curtain") {
        $id = intval($_POST['id']);
    
        try {
            $stmt = $conn->prepare("CALL DeleteCurtain(?)");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $delete_message = "اطلاعات پرده با موفقیت حذف شد.";
                $status = "success";
            } else {
                throw new Exception("خطا در حذف اطلاعات: " . $conn->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            $delete_message = $e->getMessage();
            $status = "error";
        }
    }
}

// دریافت اطلاعات پرده برای نمایش
$curtains = [];
$sql = "CALL GetCurtains()";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $curtains[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت پرده فروشی</title>
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


    <h2 class="text-center mb-4">اطلاعات پرده</h2>
    <button id="toggle-add-form" class="btn btn-primary mb-3">اضافه کردن پرده</button>
    <div id="add-form" style="display: none;">
        <!-- فرم اطلاعات پرده-->
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_curtain">

            <div class="mb-3">
                <label for="id" class="form-label">شناسه</label>
                <input type="Number" class="form-control" id="id" name="id" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="Type" class="form-label">نوع پرده</label>
                    <select name="Type" id="Type" class="form-select" required>
                        <option value="" disabled selected>---</option>
                        <option value="کرکره ای">کرکره ای</option>
                        <option value="زبرا">زبرا</option>
                        <option value="پارچه ای">پارچه ای</option>
                        <option value="غیره">غیره</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="Color" class="form-label">رنگ</label>
                    <input type="text" class="form-control" id="Color" name="Color" required>
                </div>
            </div>



            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="Material" class="form-label">جنس</label>
                    <input type="text" class="form-control" id="Material" name="Material" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="Dimensions" class="form-label">ابعاد</label>
                    <input type="text" class="form-control" id="Dimensions" name="Dimensions" required>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="Price" class="form-label">قیمت هر متر</label>
                    <input type="Number" class="form-control" id="Price" name="Price" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">ثبت</button>
        </form>
    </div>

    <!-- جدول اطلاعات -->
    <h3 class="mt-4">داده‌های پرده</h3>
    <div class="search-container">
        <input type="text" id="search-input" class="search-input" placeholder="جستجو در جدول (شناسه، رنگ، نوع و ...)">
    </div>
    <?php if (count($curtains) > 0): ?>
        <table class="table table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>شناسه</th>
                    <th>نوع</th>
                    <th>رنگ</th>
                    <th>جنس</th>
                    <th>ابعاد</th>
                    <th>قیمت</th>
                    <th>ویرایش</th>
                    <th>حذف</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($curtains as $curtain): ?>
                    <tr>
                        <td><?php echo $curtain['CurtainID']; ?></td>
                        <td><?php echo htmlspecialchars($curtain['Type']); ?></td>
                        <td><?php echo htmlspecialchars($curtain['Color']); ?></td>
                        <td><?php echo htmlspecialchars($curtain['Material']); ?></td>
                        <td><?php echo htmlspecialchars($curtain['Dimensions']); ?></td>
                        <td><?php echo htmlspecialchars($curtain['Price']); ?></td>
                        <td>
                            <button class="icon-btn edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($curtain)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="delete_curtain">
                                <input type="hidden" name="id" value="<?php echo $curtain['CurtainID']; ?>">
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
        <input type="hidden" name="action" value="edit_curtain">
        
        <label >َشناسه</label>
        <input type="Number" name="id" id="edit-id" readonly>

        <label >نوع</label>
        <select name="Type" id="edit-Type" required>
                <option value="" disabled selected>---</option>
                <option value="کرکره ای">کرکره ای</option>
                <option value="زبرا">زبرا</option>
                <option value="پارچه ای">پارچه ای</option>
                <option value="غیره">غیره</option>
        </select>

        <label>رنگ</label>
        <input type="text" class="form-control" id="edit-Color" name="Color" required>

        <label>جنس</label>
        <input type="text" class="form-control" id="edit-Material" name="Material" required>

        <label>ابعاد</label>
        <input type="text" class="form-control" id="edit-Dimensions" name="Dimensions" required>

        <label>قیمت</label>
        <input type="Number" class="form-control" id="edit-Price" name="Price" required>
        
        <button type="submit">ذخیره تغییرات</button>
        <button type="button" class="cancel-btn" onclick="closeEditModal()">لغو</button>
    </form>
</div>

<script>
    function openEditModal(curtain) {
            document.getElementById('edit-id').value = curtain.CurtainID;
            document.getElementById('edit-Type').value = curtain.Type;
            document.getElementById('edit-Color').value = curtain.Color;
            document.getElementById('edit-Material').value = curtain.Material;
            document.getElementById('edit-Dimensions').value = curtain.Dimensions;
            document.getElementById('edit-Price').value = curtain.Price;


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
            this.textContent = 'اضافه کردن پرده';
        }
    });
    document.querySelector('#add-form form').addEventListener('submit', function() {
        document.getElementById('add-form').style.display = 'none';
        document.getElementById('toggle-add-form').textContent = 'اضافه کردن پرده';
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
