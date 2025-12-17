<?php
// login.php
// صفحة تسجيل الدخول (مصححة ومحسنة)
// ملاحظات: انقل إعدادات الداتابيس لملف منفصل في مرحلة الإنتاج.

session_start();

// -------------------- إعدادات الداتابيس --------------------
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'focus';

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// -------------------- دوال مساعدة --------------------
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}

// لو المستخدم مسجل بالفعل => اذهب للداشبورد
if (isset($_SESSION['login']) && isset($_SESSION['userid'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = null;

// -------------------- معالجة POST (تسجيل الدخول) --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // تحقق من CSRF token (لو نموذج مرسل)
    $posted_csrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $posted_csrf)) {
        $error_message = "طلب غير صالح (CSRF). حاول مرة أخرى.";
    } else {
        $user = trim($_POST['uname'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($user === '' || $password === '') {
            $error_message = "يرجى إدخال اسم المستخدم وكلمة المرور";
        } else {
            // استعلام مستخدم بواسطة prepared statement
            $stmt = $conn->prepare("SELECT id, uname, password, userrole, usertype FROM users WHERE uname = ? AND isdeleted != 1 LIMIT 1");
            if ($stmt === false) {
                $error_message = "خطأ في إعداد الاستعلام";
            } else {
                $stmt->bind_param("s", $user);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $storedHash = $row['password'];
                    $userId = (int)$row['id'];

                    $password_ok = false;

                    // حالة 1: كلمة المرور مخزنة باستخدام password_hash (bcrypt/argon2...)
                    if (strlen($storedHash) >= 60 || str_starts_with($storedHash, '$2y$') || str_starts_with($storedHash, '$2a$') || str_starts_with($storedHash, '$argon')) {
                        if (password_verify($password, $storedHash)) {
                            $password_ok = true;
                            // اذا كانت تحتاج إعادة هاش (خافت/تحسين الخوارزمية) -> اعيد هاش
                            if (password_needs_rehash($storedHash, PASSWORD_DEFAULT)) {
                                $newHash = password_hash($password, PASSWORD_DEFAULT);
                                $u = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                                if ($u) {
                                    $u->bind_param("si", $newHash, $userId);
                                    $u->execute();
                                    $u->close();
                                }
                            }
                        }
                    }
                    // حالة 2: كلمة السر مخزنة بـ MD5 (طول 32) — دعم قديم، ننصح بالهجرة
                    elseif (strlen($storedHash) === 32) {
                        if (md5($password) === $storedHash) {
                            $password_ok = true;
                            // نعمل rehash إلى password_hash
                            $newHash = password_hash($password, PASSWORD_DEFAULT);
                            $u = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                            if ($u) {
                                $u->bind_param("si", $newHash, $userId);
                                $u->execute();
                                $u->close();
                            }
                        }
                    } else {
                        // محاولة password_verify كخيار عام
                        if (password_verify($password, $storedHash)) {
                            $password_ok = true;
                        }
                    }

                    if ($password_ok) {
                        // تسجيل جلسة آمن
                        session_regenerate_id(true);
                        $_SESSION['userid'] = $row['id'];
                        $_SESSION['usrole'] = $row['userrole'];
                        $_SESSION['usty'] = $row['usertype'];
                        $_SESSION['login'] = $row['uname'];

                        // تسجيل وقت الجلسة (prepared)
                        $session_stmt = $conn->prepare("INSERT INTO session_time(user) VALUES (?)");
                        if ($session_stmt) {
                            $session_stmt->bind_param("i", $userId);
                            $session_stmt->execute();
                            $session_stmt->close();
                        }

                        // هنا ممكن تستدعي logger لو معرف
                        // if (isset($logger)) { $logger->logLogin($row['uname'], true); }

                        header("Location: dashboard.php");
                        exit();
                    } else {
                        // رسالة عامة لا تكشف إن اليوزر غير موجود أو الباسورد خاطئ
                        $error_message = "اسم المستخدم أو كلمة المرور غير صحيحة";
                        // if (isset($logger)) { $logger->logLogin($user, false, "Invalid credentials"); }
                    }
                } else {
                    // مستخدم غير موجود
                    $error_message = "اسم المستخدم أو كلمة المرور غير صحيحة";
                    // if (isset($logger)) { $logger->logLogin($user, false, "User not found"); }
                }

                $stmt->close();
            }
        }
    }
}

// -------------------- جلب قائمة المستخدمين للـ <select> --------------------
$users = [];
$resuser = $conn->query("SELECT id, uname FROM users WHERE isdeleted != '1' ORDER BY id ASC");
if ($resuser) {
    while ($r = $resuser->fetch_assoc()) {
        $users[] = $r;
    }
    $resuser->close();
}

// إغلاق الاتصال بعد العرض (اتركه مفتوحًا للعمليات إذا لزم)
?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>focus.COM | Log in</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ملفات CSS (احرص أن المسارات صحيحة) -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/css3.css">
  <link rel="icon" href="assets/favicon/favicon.png" type="image/ico">
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link href="assets/libs/source-sans-pro-local.css" rel="stylesheet">
  <style>
    body { font-family: "Source Sans Pro", sans-serif; }
    .login-card-body { padding: 1.5rem; }
    .card { margin-top: 40px; }
  </style>
</head>
<body style="background-image: url('assets/wallpaper/background.jpg');">

<center>
  <div class="card card-dark col-lg-3 text-center p-9 m-0 shadow-lg" style="padding: 10px; outline:100px;">
    <div class="card-header" style="background-color: #534292;">
      <h2 style="color:#fff;margin:0;padding:8px;">تسجيل الدخول</h2>
    </div>

    <div style="padding:10px;">
      <img src="assets/favicon/favicon.png" alt="" style="width:200px;height:auto;">
    </div>

    <div class="card-body login-card-body">
      <p class="login-box-msg">سجل الدخول بالاسم و الباسورد</p>

      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
          <?= e($error_message) ?>
        </div>
      <?php endif; ?>

      <form action="<?= e($_SERVER['PHP_SELF']) ?>" method="post" autocomplete="off" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

        <div class="form-group">
          <label for="uname">المستخدم</label>
          <select name="uname" id="uname" class="form-control" required>
            <option value="">اختر المستخدم...</option>
            <?php foreach ($users as $u): ?>
              <option value="<?= e($u['uname']) ?>"
                <?= (isset($_POST['uname']) && $_POST['uname'] === $u['uname']) ? 'selected' : '' ?>>
                <?= e($u['uname']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="input-group mb-3">
          <input autofocus id="password" name="password" type="password" class="form-control" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>

        <div class="row" style="margin-top:8px;">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember">
              <label for="remember">تذكرني</label>
            </div>
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block btn-flat">تسجيل الدخول</button>
          </div>
        </div>
      </form>

    </div>
  </div>
</center>

<!-- js -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
