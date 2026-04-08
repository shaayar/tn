<?php
require_once __DIR__.'/includes/bootstrap.php';
if(loggedIn()){
    header('Location: '.BASE.(isAdmin()?'/admin/index.php':'/index.php')); exit;
}
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $user  = DB::one("SELECT * FROM users WHERE email=? AND is_active=1", [$email]);
    if($user){
        $ok = false;
        // Multi-method hash check — supports bcrypt, md5, sha1, plain (seed data)
        if(password_verify($pass, $user['password'])) $ok=true;
        elseif(md5($pass)===$user['password'])         $ok=true;
        elseif(sha1($pass)===$user['password'])        $ok=true;
        elseif($pass===$user['password'])              $ok=true;

        if($ok){
            // Auto-upgrade to bcrypt if needed
            if(strlen($user['password'])<60){
                DB::q("UPDATE users SET password=? WHERE id=?",[password_hash($pass,PASSWORD_BCRYPT),$user['id']]);
            }
            $_SESSION['uid']  = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            flash('ok','Welcome back, '.explode(' ',$user['name'])[0].'!');
            $dest = $user['role']==='admin' ? BASE.'/admin/index.php' : BASE.'/index.php';
            header('Location: '.$dest); exit;
        }
    }
    $err='Invalid email or password.';
}
$pageTitle='Login — TravelNest';
require_once __DIR__.'/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <div style="font-family:'Inter',sans-serif;font-size:30px;font-weight:700;color:var(--accent);text-align:center;margin-bottom:6px">TravelNest</div>
    <p class="sm tc mb20">Sign in to your account</p>

    <?php if($err): ?><div class="flash err">⚠️ <?= clean($err) ?></div><?php endif; ?>

    <form method="POST" action="">
      <div class="fg"><label>Email Address</label><input type="email" name="email" value="user@demo.com" required autocomplete="email"></div>
      <div class="fg"><label>Password</label><input type="password" name="password" value="demo123" required autocomplete="current-password"></div>
      <button type="submit" class="btn btn-primary w100 btn-lg mt8">Sign In →</button>
    </form>
    <p class="sm tc mt16">No account? <a href="<?= BASE ?>/register.php" style="color:var(--accent)">Sign Up Free</a></p>
    <div class="card2 mt16 p12">
      <div class="xs mb6" style="color:var(--accent)">Demo credentials:</div>
      <div class="xs">👤 <b>User:</b> user@demo.com / demo123</div>
      <div class="xs mt4">🔑 <b>Admin:</b> admin@travelnest.com / admin123</div>
    </div>
  </div>
</div>
<?php require_once __DIR__.'/includes/footer.php'; ?>
