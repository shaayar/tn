<?php
function loggedIn():bool{return isset($_SESSION['uid']);}
function isAdmin():bool{return($_SESSION['role']??'')==='admin';}
function mustLogin(string $back=''):void{
    if(!loggedIn()){header('Location: '.BASE.'/login.php'.($back?"?back=".urlencode($back):''));exit;}
}
function mustAdmin():void{
    if(!isAdmin()){header('Location: '.BASE.'/admin/login.php');exit;}
}
function me():array|false{
    if(!loggedIn())return false;
    return DB::one("SELECT * FROM users WHERE id=?",[$_SESSION['uid']]);
}

function clean(string $v):string{return htmlspecialchars(trim($v),ENT_QUOTES,'UTF-8');}

// FIXED: date formatting helper
function fmtDate(string|null $d, string $fmt='d M Y'):string{
    if(!$d||$d==='0000-00-00')return '—';
    try{ return date($fmt, strtotime($d)); }
    catch(Throwable){ return $d; }
}

function rupee(float $n):string{return '₹'.number_format($n,0,'.',',');}
function genRef():string{return 'TN'.strtoupper(substr(uniqid(),-5)).rand(10,99);}
function genPNR():string{return 'PNR'.rand(1000000000,9999999999);}

function flash(string $t,string $m):void{$_SESSION['flash']=['t'=>$t,'m'=>$m];}
function getFlash():string{
    if(empty($_SESSION['flash']))return '';
    $f=$_SESSION['flash'];unset($_SESSION['flash']);
    $bg=$f['t']==='ok'?'#052e1c':'#3b0012';$col=$f['t']==='ok'?'#4ade80':'#fb7185';
    return "<div class='flash ".($f['t']==='ok'?'ok':'err')."'>".clean($f['m'])."</div>";
}
function csrf():string{if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(24));return $_SESSION['csrf'];}
function checkCsrf():bool{return isset($_POST['csrf'])&&hash_equals($_SESSION['csrf']??'',$_POST['csrf']);}

function tierBadge(string $t):string{
    $m=['Platinum'=>'#c4b5fd','Gold'=>'#fbbf24','Silver'=>'#94a3b8','Bronze'=>'#d97706'];
    $c=$m[$t]??'#fff';
    return "<span style='background:rgba(0,0,0,.3);color:$c;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600'>$t</span>";
}
function statusBadge(string $s):string{
    $m=['Confirmed'=>'#4ade80','Pending'=>'#fbbf24','Cancelled'=>'#fb7185','Completed'=>'#38bdf8',
        'Active'=>'#4ade80','Suspended'=>'#fb7185','Expiring'=>'#fbbf24','Scheduled'=>'#a78bfa',
        'Available'=>'#4ade80','WL 4'=>'#fbbf24','WL 8'=>'#fbbf24','WL 12'=>'#fbbf24','RAC 3'=>'#fbbf24','RAC 5'=>'#fbbf24'];
    $col=$m[$s]??'#94a3b8';
    return "<span style='background:rgba(0,0,0,.3);color:$col;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600'>$s</span>";
}
function pagLinks(int $cur,int $last,string $base):string{
    if($last<=1)return '';
    $o='<div class="pag">';
    if($cur>1)$o.="<a class='pgb' href='{$base}&pg=".($cur-1)."'>‹ Prev</a>";
    for($i=max(1,$cur-2);$i<=min($last,$cur+2);$i++)
        $o.="<a class='pgb".($i===$cur?' on':'')."' href='{$base}&pg=$i'>$i</a>";
    if($cur<$last)$o.="<a class='pgb' href='{$base}&pg=".($cur+1)."'>Next ›</a>";
    return $o.'</div>';
}
function amenArr(string $s):array{return array_filter(array_map('trim',explode(',',$s)));}
function incArr(string $s):array{return array_filter(array_map('trim',explode('|',$s)));}
function jsonOut(mixed $d,int $c=200):never{
    http_response_code($c);header('Content-Type: application/json');echo json_encode($d);exit;
}
