<!-- order.php -->
<?php
include("db_connect.php");

// پیام‌ها
$add_message = "";
$edit_message = "";
$delete_message = "";
$status = "";

// دریافت اطلاعات کارمندان
$employees = [];
$sql = "CALL GetEmployees4()";
$result = $conn->query($sql);
if ($result) {
    // پردازش نتایج اول
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    // از next_result برای حرکت به کوئری بعدی استفاده کنید
    while ($conn->next_result()) {;}  // پردازش باقی‌مانده نتایج
}

// دریافت اطلاعات مشتریان
$customers = [];
$sql = "CALL GetCustomers4()";
$result = $conn->query($sql);
if ($result) {
    // پردازش نتایج اول
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    // از next_result برای حرکت به کوئری بعدی استفاده کنید
    while ($conn->next_result()) {;}  // پردازش باقی‌مانده نتایج
}

// دریافت اطلاعات پرداخت‌ها
$peyments = [];
$sql = "CALL GetPeyments4()";
$result = $conn->query($sql);
if ($result) {
    // پردازش نتایج اول
    while ($row = $result->fetch_assoc()) {
        $peyments[] = $row;
    }
    // از next_result برای حرکت به کوئری بعدی استفاده کنید
    while ($conn->next_result()) {;}  // پردازش باقی‌مانده نتایج
}

// دریافت اطلاعات پرده‌ها
$curtains = [];
$sql = "CALL GetCurtains4()";
$result = $conn->query($sql);
if ($result) {
    // پردازش نتایج اول
    while ($row = $result->fetch_assoc()) {
        $curtains[] = $row;
    }
    // از next_result برای حرکت به کوئری بعدی استفاده کنید
    while ($conn->next_result()) {;}  // پردازش باقی‌مانده نتایج
}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == "add_order") {
        $id = $_POST['id'];
        $CustomerID = $_POST['CustomerID'];
        $EmployeeID = $_POST['EmployeeID'];
        $PeymentID = $_POST['PeymentID'];
        $OrderDate = $_POST['OrderDate'];
        $Status = $_POST['Status'];
        $selectedCurtains = $_POST['curtains'] ?? [];


        // تبدیل آرایه پرده‌ها به فرمت JSON
        $curtainsJSON = json_encode($selectedCurtains);


        try {
            // فراخوانی پروسیجر افزودن سفارش
            $stmt = $conn->prepare("CALL AddOrder(?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiisss", $id, $CustomerID, $EmployeeID, $PeymentID, $OrderDate, $Status, $curtainsJSON);
            $stmt->execute();

            $add_message = "سفارش مرتبط با موفقیت ثبت شدند.";
            $status = "success";
        } catch (Exception $e) {
            $add_message = $e->getMessage();
            $status = "error";
        }
    } elseif ($_POST['action'] == "edit_order") {
        $id = $_POST['id'];
        $CustomerID = $_POST['CustomerID'];
        $EmployeeID = $_POST['EmployeeID'];
        $PeymentID = $_POST['PeymentID'];
        $OrderDate = $_POST['OrderDate'];
        $Status = $_POST['Status'];
        $selectedCurtains = $_POST['curtains'] ?? [];

        // تبدیل آرایه پرده‌ها به فرمت JSON
        $curtainsJSON = json_encode($selectedCurtains);

        try {
            // فراخوانی پروسیجر ویرایش سفارش
            $stmt = $conn->prepare("CALL EditOrder(?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiisss", $id, $CustomerID, $EmployeeID, $PeymentID, $OrderDate, $Status, $curtainsJSON);
            $stmt->execute();

            $edit_message = "اطلاعات سفارش با موفقیت ویرایش شد.";
            $status = "success";
        } catch (Exception $e) {
            $edit_message = $e->getMessage();
            $status = "error";
        }
    } elseif ($_POST['action'] == "delete_order") {
        $id = intval($_POST['id']);

        try {
            // فراخوانی پروسیجر حذف سفارش
            $stmt = $conn->prepare("CALL DeleteOrder(?)");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $delete_message = "اطلاعات سفارش با موفقیت حذف شد.";
            $status = "success";
        } catch (Exception $e) {
            $delete_message = $e->getMessage();
            $status = "error";
        }
    }
}

// دریافت سفارش‌ها
$orders = [];
$sql = "CALL GetOrders()";
$result = $conn->query($sql);
if ($result) {
    // پردازش نتایج اول
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
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


    <h2 class="text-center mb-4">اطلاعات سفارش</h2>

    <button id="toggle-add-form" class="btn btn-primary mb-3">اضافه کردن سفارش</button>
    <div id="add-form" style="display: none;">
        <!-- فرم اطلاعات سفارش-->
        <form method="POST" action="">
            <input type="hidden" name="action" value="add_order">

            <div class="mb-3">
                <label for="id" class="form-label">شناسه</label>
                <input type="Number" class="form-control" id="id" name="id" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="CustomerID" class="form-label">انتخاب مشتری</label>
                    <select name="CustomerID" id="CustomerID" class="form-control" required>
                        <option value="" disabled selected>---</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['CustomerID']; ?>">
                                <?php echo "کدملی: ".$customer['CustomerID']. " - ".$customer['Address'] . " - " . $customer['JoinDate']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="EmployeeID" class="form-label">انتخاب کارمند</label>
                    <select name="EmployeeID" id="EmployeeID" class="form-control" required>
                        <option value="" disabled selected>---</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?php echo $employee['EmployeeID']; ?>">
                                <?php echo "کدملی: ".$employee['EmployeeID']. " - ".$employee['Role'] . " - " . $employee['Salary']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>


            <div class="row">

            </div>



            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="PeymentID" class="form-label">انتخاب پرداخت</label>
                    <select name="PeymentID" id="PeymentID" class="form-control" required>
                        <option value="" disabled selected>---</option>
                        <?php foreach ($peyments as $peyment): ?>
                            <option value="<?php echo $peyment['PeymentID']; ?>">
                                <?php echo "شناسه: ".$peyment['PeymentID']. " - ".$peyment['Type'] . " - " . $peyment['Amount']. " - " . $peyment['Description']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="mb-3">
                    <label for="curtains" class="form-label">انتخاب پرده‌ها</label><br>
                    <?php foreach ($curtains as $curtain): ?>
                        <label>
                            <input type="checkbox"  name="curtains[]" class="form-checkbox" value="<?php echo $curtain['CurtainID']; ?>">
                            <?php echo $curtain['Type'] . " - " . $curtain['Color'] . " (قیمت هر متر: " . $curtain['Price'] . ")"; ?>
                        </label><br>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="OrderDate" class="form-label">تاریخ سفارش</label><br>
                    <input type="date" id="OrderDate" name="OrderDate" class="form-control" required></input>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="Status" class="form-label">وضعیت</label>
                    <select name="Status" id="Status" class="form-select" required>
                        <option value="" disabled selected>---</option>
                        <option value="موفق">موفق</option>
                        <option value="درحال پردازش">درحال پردازش</option>
                        <option value="ناموفق">ناموفق</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">ثبت</button>
        </form>
    </div>



    <!-- جدول اطلاعات -->
    <h3 class="mt-4">داده‌های سفارش</h3>
    <div class="search-container">
        <input type="text" id="search-input" class="search-input" placeholder="جستجو در جدول (شناسه، کد مشتری، وضعیت و ...)">
    </div>
    <?php if (count($orders) > 0): ?>
        <table class="table table-bordered mt-4">
            <thead class="table-dark">
            <tr>
                <th>شناسه</th>
                <th>کدملی مشتری</th>
                <th>کدملی کارمند</th>
                <th>پرداخت</th>
                <th>تاریخ سفارش</th>
                <th>وضعیت</th>
                <th>پرده‌ها</th>
                <th>ویرایش</th>
                <th>حذف</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['OrderID']; ?></td>
                    <td><?php echo $order['CustomerID']; ?></td>
                    <td><?php echo htmlspecialchars($order['EmployeeID']); ?></td>
                    <td><?php echo htmlspecialchars($order['PeymentID']); ?></td>
                    <td><?php echo htmlspecialchars($order['OrderDate']); ?></td>
                    <td><?php echo htmlspecialchars($order['Status']); ?></td>
                    <td><?php echo htmlspecialchars($order['Curtains']); ?></td>
                    <td>
                        <button class="icon-btn edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($order)); ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="delete_order">
                            <input type="hidden" name="id" value="<?php echo $order['OrderID']; ?>">
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
    <form method="POST" action=""  id="edit-form">
        <input type="hidden" name="action" value="edit_order">

        <label >َشناسه</label>
        <input type="Number" name="id" id="edit-id" readonly>

        <label >انتخاب مشتری</label>
        <select name="CustomerID" id="edit-CustomerID" class="form-control"  required>
            <option value="" disabled selected>---</option>
            <?php foreach ($customers as $customer): ?>
                <option value="<?php echo $customer['CustomerID']; ?>">
                    <?php echo "کدملی: ".$customer['CustomerID']. " - ".$customer['Address'] . " - " . $customer['JoinDate']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>انتخاب کارمند</label>
        <select name="EmployeeID" id="edit-EmployeeID" class="form-control" required>
            <option value="" disabled selected>---</option>
            <?php foreach ($employees as $employee): ?>
                <option value="<?php echo $employee['EmployeeID']; ?>">
                    <?php echo "کدملی: ".$employee['EmployeeID']. " - ".$employee['Role'] . " - " . $employee['Salary']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>انتخاب پرداخت</label>
        <select name="PeymentID" id="edit-PeymentID" class="form-control" required>
            <option value="" disabled selected>---</option>
            <?php foreach ($peyments as $peyment): ?>
                <option value="<?php echo $peyment['PeymentID']; ?>">
                    <?php echo "شناسه: ".$peyment['PeymentID']. " - ".$peyment['Type'] . " - " . $peyment['Amount']. " - " . $peyment['Description']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>پرده</label>
        <?php foreach ($curtains as $curtain): ?>
            <label>
                <input type="checkbox"  name="curtains[]" id="edit-curtains" value="<?php echo $curtain['CurtainID']; ?>">
                <?php echo $curtain['Type'] . " - " . $curtain['Color'] . " (قیمت هر متر: " . $curtain['Price'] . ")"; ?>
            </label><br>
        <?php endforeach; ?>

        <label>تاریخ سفارش</label>
        <input type="date" id="edit-OrderDate" name="OrderDate" class="form-control"  required></input>

        <label>وضعیت</label>
        <select name="Status" id="edit-Status" class="form-select" required>
            <option value="" disabled selected>---</option>
            <option value="موفق">موفق</option>
            <option value="درحال پردازش">درحال پردازش</option>
            <option value="ناموفق">ناموفق</option>
        </select>

        <button type="submit">ذخیره تغییرات</button>
        <button type="button" class="cancel-btn" onclick="closeEditModal()">لغو</button>
    </form>
</div>

<script>
    function openEditModal(order) {
        document.getElementById('edit-id').value = order.OrderID;
        document.getElementById('edit-CustomerID').value = order.CustumerID;
        document.getElementById('edit-EmployeeID').value = order.EmployeeID;
        document.getElementById('edit-PeymentID').value = order.PeymentID;
        document.getElementById('edit-OrderDate').value = order.OrderDate;
        document.getElementById('edit-Status').value = order.Status;


        // انتخاب پرده مربوطه
        const selectedcurtains = order.Curtains.split(', ');
        document.querySelectorAll('.edit-curtains').forEach(input => {
            input.checked = selectedcurtains.includes(input.nextSibling.nodeValue.trim());
        });


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
            this.textContent = 'اضافه کردن سفارش';
        }
    });
    document.querySelector('#add-form form').addEventListener('submit', function() {
        document.getElementById('add-form').style.display = 'none';
        document.getElementById('toggle-add-form').textContent = 'اضافه کردن سفارش';
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
