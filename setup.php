<?php
/**
 * TravelNest — Setup / Password Reset Script
 * Run this ONCE if admin login fails.
 * Visit: http://localhost/TN/setup.php
 * DELETE this file after running!
 */
require_once __DIR__.'/includes/bootstrap.php';

$done = [];
$errors = [];

try {
    // Reset admin password
    $adminHash = password_hash('admin123', PASSWORD_BCRYPT);
    $r1 = DB::q("UPDATE users SET password=? WHERE email='admin@travelnest.com' AND role='admin'", [$adminHash]);
    $done[] = "✅ Admin password reset to: <strong>admin123</strong>";

    // Reset demo user password
    $userHash = password_hash('demo123', PASSWORD_BCRYPT);
    $r2 = DB::q("UPDATE users SET password=? WHERE email='user@demo.com'", [$userHash]);
    $done[] = "✅ Demo user password reset to: <strong>demo123</strong>";

    // Reset all other demo users
    $allHash = password_hash('demo123', PASSWORD_BCRYPT);
    DB::q("UPDATE users SET password=? WHERE role='user' AND email LIKE '%@example.com'", [$allHash]);
    $done[] = "✅ All @example.com users password reset to: <strong>demo123</strong>";

    // Verify admin exists
    $admin = DB::one("SELECT id, name, email, role FROM users WHERE email='admin@travelnest.com'");
    if($admin){
        $done[] = "✅ Admin account found: <strong>{$admin['name']}</strong> (ID: {$admin['id']})";
    } else {
        // Create admin if missing
        $id = DB::insert('users', [
            'name'=>'Admin TravelNest','email'=>'admin@travelnest.com',
            'password'=>$adminHash,'phone'=>'+91 98765 00001','city'=>'Mumbai',
            'role'=>'admin','tier'=>'Platinum','total_spent'=>0,'total_bookings'=>0,'is_active'=>1
        ]);
        $done[] = "✅ Admin account CREATED (ID: $id)";
    }
} catch(Throwable $e){
    $errors[] = "❌ Error: ".$e->getMessage();
}
?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>TravelNest Setup</title>
<style>
body{font-family:'Inter','DM Sans',sans-serif;background:#ffffff;color:#0f172a;padding:40px;max-width:600px;margin:0 auto}
h1{font-family:'Inter',sans-serif;color:#008cff;font-size:32px;margin-bottom:8px}
.box{background:#0c1422;border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:24px;margin:20px 0}
.ok{color:#4ade80}.err{color:#fb7185}
.btn{display:inline-block;padding:12px 24px;background:#f0a500;color:#000;border-radius:8px;font-weight:600;text-decoration:none;margin-top:16px}
code{background:#1a2640;padding:3px 8px;border-radius:4px;color:#38bdf8;font-size:13px}
.warn{background:#3b1c00;border:1px solid #f0a500;border-radius:8px;padding:14px;margin-top:20px;color:#fbbf24;font-size:13px}
</style>
</head>
<body>
<h1>TravelNest Setup</h1>
<p style="color:#8899b4">One-time password reset utility</p>

<div class="box">
<?php foreach($done as $d): ?><p class="ok" style="margin-bottom:8px"><?= $d ?></p><?php endforeach; ?>
<?php foreach($errors as $e): ?><p class="err" style="margin-bottom:8px"><?= clean($e) ?></p><?php endforeach; ?>
</div>

<?php if(empty($errors)): ?>
<div style="background:#052e1c;border:1px solid #065f46;border-radius:10px;padding:20px;margin:20px 0">
  <h3 style="color:#4ade80;margin-bottom:12px">✅ Setup Complete!</h3>
  <p style="color:#4ade80;margin-bottom:8px">You can now login with:</p>
  <p style="color:#edf2f7;margin-bottom:4px">👤 <strong>User:</strong> user@demo.com / <code>demo123</code></p>
  <p style="color:#edf2f7">🔑 <strong>Admin:</strong> admin@travelnest.com / <code>admin123</code></p>
</div>
<a href="<?= BASE ?>/admin/login.php" class="btn">→ Go to Admin Login</a>
<a href="<?= BASE ?>/login.php" class="btn" style="background:#131c2b;color:#edf2f7;margin-left:10px">→ User Login</a>
<?php endif; ?>

<div class="warn">⚠️ <strong>Security:</strong> Delete this file after use! <code>rm setup.php</code></div>
</body>
</html>
