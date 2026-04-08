<?php
require_once __DIR__.'/includes/bootstrap.php';
mustLogin();
$me=me();
$type=clean($_GET['type']??'');$id=(int)($_GET['id']??0);
$tbl=['flight'=>'flights','hotel'=>'hotels','package'=>'packages','train'=>'trains','bus'=>'buses','cab'=>'cabs','cruise'=>'cruises'];
if(!isset($tbl[$type])||!$id){header('Location: '.BASE.'/index.php');exit;}
$item=DB::one("SELECT * FROM {$tbl[$type]} WHERE id=? AND is_active=1",[$id]);
if(!$item){header('Location: '.BASE."/$type".'s.php');exit;}
$price=(float)($item['price']??$item['price_per_night']??$item['base_fare']??$item['price_2a']??0);
$label=$item['name']??($item['cruise_name']??($item['train_name']??($item['operator_name']??($item['vehicle_name']??''))));
if($type==='flight')$label=$item['airline'].' '.$item['flight_code'].' '.$item['from_city'].'→'.$item['to_city'];
if($type==='hotel')$label=$item['name'].', '.$item['city'];
$tax=round($price*TAX_RATE);$total=$price+$tax;
$err='';$bookingSuccess=false;$successData=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!checkCsrf()){$err='Invalid request. Refresh and try again.';}
    else{
        $pax=max(1,(int)($_POST['pax']??1));
        $promo=strtoupper(clean($_POST['promo']??''));
        if($type==='train'&&isset($_POST['tcls'])){
            $cm=['1A'=>'price_1a','2A'=>'price_2a','3A'=>'price_3a','SL'=>'price_sl'];
            $ck=clean($_POST['tcls']??'');
            if(isset($cm[$ck])&&$item[$cm[$ck]]>0)$price=(float)$item[$cm[$ck]];
        }
        $base=$price*$pax;$tax2=round($base*TAX_RATE);$disc=0;
        if($promo){
            $pc=DB::one("SELECT * FROM promo_codes WHERE code=? AND status IN('Active','Expiring') AND valid_until>=CURDATE() AND used_count<usage_limit",[$promo]);
            if($pc&&$base>=$pc['min_booking']){
                $disc=$pc['discount_type']==='percentage'?min(round($base*$pc['discount_value']/100),(float)$pc['max_discount']):(float)$pc['discount_value'];
            }
        }
        $ttl=max(0,$base+$tax2-$disc);
        $ref=genRef();$pnr=genPNR();
        $payMethod=clean($_POST['pay']??'UPI');
        DB::insert('bookings',['booking_ref'=>$ref,'user_id'=>$me['id'],'booking_type'=>ucfirst($type),'item_id'=>$id,'item_name'=>$label,'travel_date'=>clean($_POST['tdate']??''),'passengers'=>$pax,'base_amount'=>$base,'tax_amount'=>$tax2,'discount_amount'=>$disc,'total_amount'=>$ttl,'promo_code'=>$promo?:null,'payment_method'=>$payMethod,'booking_status'=>'Confirmed','payment_status'=>'Paid','passenger_name'=>clean($_POST['pname']??$me['name']),'passenger_email'=>clean($_POST['pemail']??$me['email']),'passenger_phone'=>clean($_POST['pphone']??$me['phone']??''),'pnr_number'=>$pnr]);
        if($promo&&$disc>0)DB::q("UPDATE promo_codes SET used_count=used_count+1 WHERE code=?",[$promo]);
        DB::q("UPDATE users SET total_bookings=total_bookings+1,total_spent=total_spent+? WHERE id=?",[$ttl,$me['id']]);
        $bookingSuccess=true;
        $successData=['ref'=>$ref,'pnr'=>$pnr,'amount'=>$ttl,'pay'=>$payMethod,'label'=>$label,'type'=>ucfirst($type),'pname'=>clean($_POST['pname']??$me['name'])];
    }
}
// If booking was successful, show confirmation page
if($bookingSuccess){
    $pageTitle='Payment Confirmed — TravelNest';require_once __DIR__.'/includes/header.php';
?>
<div class="sec" style="max-width:600px;text-align:center">
  <div class="pay-confirm-card">
    <div class="pay-confirm-anim">
      <div class="pay-confirm-circle">
        <svg class="pay-confirm-check" viewBox="0 0 52 52"><path class="pay-confirm-check-path" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>
      </div>
    </div>
    <h2 style="font-size:28px;margin-bottom:8px;color:var(--green)">Payment Successful!</h2>
    <p class="sm mb20">Your booking has been confirmed. A confirmation email has been sent.</p>

    <div class="card2 mb16" style="text-align:left">
      <div class="info-row"><span class="sm">Booking Ref</span><span class="fw6 acc"><?=$successData['ref']?></span></div>
      <div class="info-row"><span class="sm">PNR Number</span><span class="fw5"><?=$successData['pnr']?></span></div>
      <div class="info-row"><span class="sm">Booking</span><span class="fw5"><?=clean($successData['label'])?></span></div>
      <div class="info-row"><span class="sm">Type</span><span class="tag t-blue"><?=$successData['type']?></span></div>
      <div class="info-row"><span class="sm">Passenger</span><span class="fw5"><?=$successData['pname']?></span></div>
      <div class="info-row"><span class="sm">Payment Method</span><span class="tag t-amber"><?=strtoupper($successData['pay'])?></span></div>
      <div class="info-row" style="border-bottom:none"><span class="sm">Amount Paid</span><span class="fw6 acc" style="font-size:22px;font-family:'Inter',sans-serif"><?=rupee($successData['amount'])?></span></div>
    </div>

    <div class="flex g8 cc mb16">
      <a href="<?=BASE?>/invoice.php?ref=<?=$successData['ref']?>" class="btn btn-primary btn-lg">📄 View Invoice</a>
      <a href="<?=BASE?>/bookings.php" class="btn btn-ghost">My Bookings</a>
    </div>

    <p class="xs" style="color:var(--text3)">Redirecting to invoice in <span id="countdown">5</span> seconds...</p>
  </div>
</div>
<style>
.pay-confirm-card{animation:confirmFadeIn .6s ease}
@keyframes confirmFadeIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.pay-confirm-anim{width:100px;height:100px;margin:0 auto 24px;position:relative}
.pay-confirm-circle{width:100px;height:100px;border-radius:50%;background:rgba(52,211,153,.1);border:3px solid var(--green);display:flex;align-items:center;justify-content:center;animation:confirmCirclePop .6s cubic-bezier(.4,0,.2,1)}
@keyframes confirmCirclePop{0%{transform:scale(0);opacity:0}60%{transform:scale(1.2)}100%{transform:scale(1);opacity:1}}
.pay-confirm-check{width:40px;height:40px}
.pay-confirm-check-path{stroke:var(--green);stroke-width:4;stroke-linecap:round;stroke-linejoin:round;stroke-dasharray:48;stroke-dashoffset:48;animation:confirmCheckDraw .5s .4s ease forwards}
@keyframes confirmCheckDraw{to{stroke-dashoffset:0}}
</style>
<script>
let cd=5;const cdEl=document.getElementById('countdown');
const cdTimer=setInterval(()=>{cd--;if(cdEl)cdEl.textContent=cd;if(cd<=0){clearInterval(cdTimer);window.location='<?=BASE?>/invoice.php?ref=<?=$successData['ref']?>';}},1000);
</script>
<?php require_once __DIR__.'/includes/footer.php';exit;}
$pageTitle='Book — TravelNest';require_once __DIR__.'/includes/header.php';?>
<div class="sec" style="max-width:860px">
  <h2 class="stitle tc">Complete Your Booking</h2>
  <p class="ssub tc">Secure checkout — your data is encrypted</p>
  <?php if($err):?><div class="flash err"><?=clean($err)?></div><?php endif;?>

  <!-- Wizard Progress -->
  <div class="wizard-progress">
    <div class="wizard-step active" onclick="wizardGo(1)"><span class="step-num">1</span> Traveller</div>
    <div class="wizard-connector"></div>
    <div class="wizard-step" onclick="wizardGo(2)"><span class="step-num">2</span> Add-ons</div>
    <div class="wizard-connector"></div>
    <div class="wizard-step" onclick="wizardGo(3)"><span class="step-num">3</span> Payment</div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start">
    <form method="POST" id="book-form">
      <input type="hidden" name="csrf" value="<?=csrf()?>">
      <input type="hidden" id="base-amt" name="_base" value="<?=$price?>">
      <input type="hidden" id="disc-amt" name="discount_applied" value="0">

      <!-- Step 1: Traveller Details -->
      <div class="step-panel active">
        <div class="card mb16">
          <div class="flex g12 mb16">
            <div style="font-size:32px;flex-shrink:0"><?=$item['emoji']??'📋'?></div>
            <div>
              <div class="fw6" style="font-size:15px"><?=clean($label)?></div>
              <?php if($type==='flight'):?><div class="sm mt4"><?=$item['departure_time']?> → <?=$item['arrival_time']?> · <?=clean($item['duration'])?> · <?=$item['stops']?></div><?php endif;?>
              <?php if($type==='hotel'):?><div class="sm mt4">📍 <?=clean($item['city'])?> · <?=str_repeat('★',(int)$item['stars'])?> · <?=clean($item['meal_plan']??'')?></div><?php endif;?>
              <?php if($type==='train'):?><div class="sm mt4"><?=clean($item['from_station'])?> → <?=clean($item['to_station'])?> · <?=$item['departure_time']?> → <?=$item['arrival_time']?></div><?php endif;?>
              <?php if($type==='package'):?><div class="sm mt4">📍 <?=clean($item['destination'])?> · 🌙 <?=$item['nights']?> Nights</div><?php endif;?>
              <?php if($type==='bus'):?><div class="sm mt4"><?=clean($item['from_city'])?> → <?=clean($item['to_city'])?> · <?=$item['departure_time']?> · <?=clean($item['bus_type']??'')?></div><?php endif;?>
            </div>
          </div>
          <h3 style="font-size:15px;margin-bottom:16px">👤 Passenger Details</h3>
          <div class="g2 mb12"><div class="fg"><label>Full Name</label><input name="pname" value="<?=clean($me['name'])?>" required></div><div class="fg"><label>Email</label><input type="email" name="pemail" value="<?=clean($me['email'])?>" required></div></div>
          <div class="g2 mb12"><div class="fg"><label>Phone</label><input name="pphone" value="<?=clean($me['phone']??'')?>" required></div><div class="fg"><label>Travel Date</label><input type="text" name="tdate" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" maxlength="10" title="Format: dd/mm/yyyy" oninput="formatDateInput(event)" required></div></div>
          <?php if($type==='train'):?>
          <div class="g2 mb12"><div class="fg"><label>Class</label><select name="tcls" onchange="updateTrain(this)">
            <?php foreach(['1A'=>'price_1a','2A'=>'price_2a','3A'=>'price_3a','SL'=>'price_sl'] as $lbl=>$col):if($item[$col]>0):?><option value="<?=$lbl?>" data-price="<?=$item[$col]?>"><?=$lbl?> — <?=rupee($item[$col])?></option><?php endif;endforeach;?>
          </select></div><div class="fg"><label>Quota</label><select name="quota"><option>General</option><option>Tatkal</option><option>Ladies</option><option>Senior Citizen</option></select></div></div>
          <?php endif;?>
          <div class="fg"><label>Passengers / Guests</label><select name="pax" id="pax" onchange="recalcTotal()"><option value="1">1</option><?php for($i=2;$i<=6;$i++):?><option value="<?=$i?>"><?=$i?></option><?php endfor;?></select></div>
        </div>
        <button type="button" class="btn btn-primary w100" onclick="wizardGo(2)">Continue to Add-ons →</button>
      </div>

      <!-- Step 2: Add-ons -->
      <div class="step-panel">
        <div class="card mb16">
          <h3 style="font-size:15px;margin-bottom:16px">🎁 Enhance Your Trip</h3>
          <div style="display:grid;gap:10px">
            <div class="addon-item" data-price="199" onclick="toggleAddon(this)">
              <input type="checkbox" name="addons[]" value="travel_insurance" style="display:none">
              <div class="addon-icon">🛡️</div>
              <div style="flex:1">
                <div class="fw5" style="font-size:14px">Travel Insurance</div>
                <div class="xs">Trip cancellation, medical emergency & baggage loss cover up to ₹5,00,000</div>
              </div>
              <div class="tr"><div class="fw6 acc">₹199</div><div class="xs">per person</div></div>
              <div class="addon-check">✓</div>
            </div>
            <div class="addon-item" data-price="349" onclick="toggleAddon(this)">
              <input type="checkbox" name="addons[]" value="meal" style="display:none">
              <div class="addon-icon">🍽️</div>
              <div style="flex:1">
                <div class="fw5" style="font-size:14px">Meal Preference</div>
                <div class="xs">Pre-book your meal — Veg / Non-veg / Jain options available</div>
              </div>
              <div class="tr"><div class="fw6 acc">₹349</div><div class="xs">per person</div></div>
              <div class="addon-check">✓</div>
            </div>
            <div class="addon-item" data-price="499" onclick="toggleAddon(this)">
              <input type="checkbox" name="addons[]" value="extra_baggage" style="display:none">
              <div class="addon-icon">🧳</div>
              <div style="flex:1">
                <div class="fw5" style="font-size:14px">Extra Baggage (15 kg)</div>
                <div class="xs">Additional 15 kg checked baggage allowance</div>
              </div>
              <div class="tr"><div class="fw6 acc">₹499</div><div class="xs">one-time</div></div>
              <div class="addon-check">✓</div>
            </div>
            <div class="addon-item" data-price="299" onclick="toggleAddon(this)">
              <input type="checkbox" name="addons[]" value="priority_seat" style="display:none">
              <div class="addon-icon">💺</div>
              <div style="flex:1">
                <div class="fw5" style="font-size:14px">Priority Seating</div>
                <div class="xs">Choose your preferred seat — window, aisle, extra legroom</div>
              </div>
              <div class="tr"><div class="fw6 acc">₹299</div><div class="xs">per person</div></div>
              <div class="addon-check">✓</div>
            </div>
            <div class="addon-item" data-price="599" onclick="toggleAddon(this)">
              <input type="checkbox" name="addons[]" value="airport_transfer" style="display:none">
              <div class="addon-icon">🚐</div>
              <div style="flex:1">
                <div class="fw5" style="font-size:14px">Airport / Station Transfer</div>
                <div class="xs">AC cab pickup & drop from airport or station to hotel</div>
              </div>
              <div class="tr"><div class="fw6 acc">₹599</div><div class="xs">one-way</div></div>
              <div class="addon-check">✓</div>
            </div>
          </div>
          <div class="flex sb mt12 card2 p12" id="addon-total-row" style="display:none">
            <span class="sm fw5">Add-ons Total</span>
            <span class="fw6 acc" id="addon-total-val">₹0</span>
          </div>
        </div>
        <div class="card mb16"><h3 style="font-size:15px;margin-bottom:14px">🎟️ Promo Code</h3>
          <div class="flex g8"><input id="promo-in" name="promo" placeholder="FIRST50 · SUMMER25 · HOLI2026..." style="text-transform:uppercase"><button type="button" class="btn btn-ghost" onclick="checkPromo()">Apply</button></div>
          <div id="promo-msg" class="mt8"></div>
        </div>
        <div class="flex g8">
          <button type="button" class="btn btn-ghost w100" onclick="wizardGo(1)">← Back</button>
          <button type="button" class="btn btn-primary w100" onclick="wizardGo(3)">Continue to Payment →</button>
        </div>
      </div>

      <!-- Step 3: Payment -->
      <div class="step-panel">
        <div class="card mb20"><h3 style="font-size:15px;margin-bottom:14px">💳 Payment Method</h3>
          <div class="pay-methods">
            <?php
            $payMethods=[
              ['upi','UPI','📱','Pay via any UPI app'],
              ['card','Credit/Debit Card','💳','Visa, MasterCard, RuPay'],
              ['nb','Net Banking','🏦','All major banks supported'],
              ['emi','EMI','📊','0% interest available'],
              ['wallet','TravelNest Wallet','👛','Use wallet balance'],
              ['cod','Pay at Counter','🏪','Pay at the venue'],
            ];
            foreach($payMethods as $i=>[$v,$l,$icon,$desc]):?>
            <label class="pay-method-item<?=$i===0?' selected':''?>" data-method="<?=$v?>" onclick="selectPayMethod(this,'<?=$v?>')">
              <input type="radio" name="pay" value="<?=$v?>" <?=$i===0?'checked':''?> style="display:none">
              <div class="pay-method-header">
                <span class="pay-icon"><?=$icon?></span>
                <div><div class="fw5" style="font-size:14px"><?=$l?></div><div class="xs"><?=$desc?></div></div>
                <span class="pay-check">✓</span>
              </div>
            </label>
            <?php endforeach;?>
          </div>

          <!-- UPI Sub-options -->
          <div class="pay-sub-panel" id="pay-sub-upi" style="display:block">
            <div class="fw5 mb8" style="font-size:13px">Choose UPI App</div>
            <div class="pay-apps-grid">
              <button type="button" class="pay-app-btn selected" onclick="selectUpiApp(this,'gpay')">
                <span style="font-size:24px">🟢</span>
                <span class="fw5">GPay</span>
              </button>
              <button type="button" class="pay-app-btn" onclick="selectUpiApp(this,'phonepe')">
                <span style="font-size:24px">🟣</span>
                <span class="fw5">PhonePe</span>
              </button>
              <button type="button" class="pay-app-btn" onclick="selectUpiApp(this,'paytm')">
                <span style="font-size:24px">🔵</span>
                <span class="fw5">Paytm</span>
              </button>
              <button type="button" class="pay-app-btn" onclick="selectUpiApp(this,'bhim')">
                <span style="font-size:24px">🟠</span>
                <span class="fw5">BHIM</span>
              </button>
            </div>
            <input type="hidden" name="upi_app" id="upi-app-val" value="gpay">
            <div class="fg mt12">
              <label>Or enter UPI ID</label>
              <input type="text" name="upi_id" id="upi-id-input" placeholder="yourname@upi" style="font-size:14px" oninput="syncUpiId(this.value)">
            </div>
            <div class="card2 p12 mt8" style="border-color:rgba(52,211,153,.2)">
              <div class="flex g8"><span>🔒</span><span class="xs" style="color:var(--green)">UPI payments are protected by RBI guidelines. Your money is safe.</span></div>
            </div>
          </div>

          <!-- Card Sub-options -->
          <div class="pay-sub-panel" id="pay-sub-card" style="display:none">
            <div class="fw5 mb8" style="font-size:13px">Card Details</div>
            <div class="fg"><label>Card Number</label><input type="text" name="card_number" id="card-number" placeholder="1234 5678 9012 3456" maxlength="19" oninput="fmtCard(this)"></div>
            <div class="g2">
              <div class="fg"><label>Expiry</label><input type="text" name="card_expiry" id="card-expiry" placeholder="MM/YY" maxlength="5" oninput="fmtExpiry(this)"></div>
              <div class="fg"><label>CVV</label><input type="password" name="card_cvv" placeholder="•••" maxlength="4"></div>
            </div>
            <div class="fg"><label>Name on Card</label><input type="text" name="card_name" placeholder="As printed on card"></div>
            <div class="flex g8 mt4">
              <span class="tag t-blue">Visa</span><span class="tag t-amber">MasterCard</span><span class="tag t-green">RuPay</span><span class="tag t-purple">Amex</span>
            </div>
          </div>

          <!-- Net Banking Sub-options -->
          <div class="pay-sub-panel" id="pay-sub-nb" style="display:none">
            <div class="fw5 mb8" style="font-size:13px">Select Bank</div>
            <input type="hidden" name="nb_bank" id="nb-bank-val" value="">
            <div class="pay-apps-grid">
              <?php foreach([['🏦','SBI'],['🏛','HDFC'],['🏗','ICICI'],['🏢','Axis'],['🏤','Kotak'],['🏣','PNB']] as [$bIcon,$bName]):?>
              <button type="button" class="pay-app-btn" onclick="selectBank(this,'<?=$bName?>')">
                <span style="font-size:20px"><?=$bIcon?></span>
                <span class="fw5" style="font-size:12px"><?=$bName?></span>
              </button>
              <?php endforeach;?>
            </div>
          </div>

          <!-- EMI Sub-options -->
          <div class="pay-sub-panel" id="pay-sub-emi" style="display:none">
            <div class="fw5 mb8" style="font-size:13px">Select EMI Tenure</div>
            <div class="emi-options">
              <?php foreach([3,6,9,12] as $m):?>
              <label class="emi-option" onclick="selectEmi(this)">
                <div class="fw5"><?=$m?> Months</div>
                <div class="xs">₹<?=number_format(round($total/$m))?>/mo</div>
                <span class="tag t-green" style="font-size:9px"><?=$m<=6?'No Cost EMI':'Low Interest'?></span>
              </label>
              <?php endforeach;?>
            </div>
          </div>

          <!-- Wallet Sub-options -->
          <div class="pay-sub-panel" id="pay-sub-wallet" style="display:none">
            <div class="card2 p16 tc">
              <div style="font-size:32px;margin-bottom:8px">👛</div>
              <div class="fw5 mb4">TravelNest Wallet</div>
              <div class="xs mb8">Current Balance</div>
              <div class="acc fw6" style="font-size:24px;font-family:'Inter',sans-serif">₹2,500</div>
              <div class="xs mt8" style="color:var(--green)">✓ Sufficient for this booking</div>
            </div>
          </div>

          <!-- Pay at Counter -->
          <div class="pay-sub-panel" id="pay-sub-cod" style="display:none">
            <div class="card2 p16">
              <div class="flex g8 mb8"><span style="font-size:20px">🏪</span><span class="fw5">Pay at Counter</span></div>
              <div class="sm" style="line-height:1.7">Pay at the venue/counter when you arrive. A booking confirmation will be sent to your email. Please carry a valid ID.</div>
            </div>
          </div>
        </div>
        <div class="flex g8 mb16">
          <button type="button" class="btn btn-ghost w100" onclick="wizardGo(2)">← Back</button>
          <button type="submit" id="pay-btn" class="btn btn-primary btn-lg w100" onclick="return validateAndPay()">🔒 Confirm & Pay <?=rupee($total)?></button>
        </div>
        <!-- Pay processing overlay -->
        <div id="pay-processing" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;display:none;align-items:center;justify-content:center;flex-direction:column;gap:16px">
          <div style="width:64px;height:64px;border:5px solid rgba(255,255,255,.2);border-top-color:#008cff;border-radius:50%;animation:spinPay 1s linear infinite"></div>
          <div style="color:#fff;font-size:16px;font-weight:600">Processing Payment…</div>
          <div style="color:rgba(255,255,255,.6);font-size:13px">Please do not close this window</div>
        </div>
        <style>@keyframes spinPay{to{transform:rotate(360deg)}}</style>
      </div>
    </form>

    <!-- Sticky Fare Sidebar -->
    <div style="position:sticky;top:80px">
      <div class="card" style="background:var(--bg3)"><h3 style="font-size:15px;margin-bottom:14px">💰 Fare Summary</h3>
        <div class="flex sb mb8"><span class="sm">Base fare</span><span class="sm" id="base-show"><?=rupee($price)?></span></div>
        <div class="flex sb mb8"><span class="sm">GST & Fees (12%)</span><span class="sm"><?=rupee($tax)?></span></div>
        <div class="flex sb mb8 hidden" id="disc-row"><span class="sm grn">Promo Discount</span><span class="sm grn" id="disc-show">-₹0</span></div>
        <div class="flex sb mb8 hidden" id="addon-row"><span class="sm" style="color:var(--accent)">Add-ons</span><span class="sm" style="color:var(--accent)" id="addon-show">+₹0</span></div>
        <div class="flex sb" style="padding-top:10px;border-top:1px solid var(--border)"><span class="fw5">Total</span><span class="fw6 acc" id="total-show" style="font-size:22px"><?=rupee($total)?></span></div>
      </div>
    </div>
  </div>
</div>
<script>
const BASE_PRICE=<?=$price?>;let disc=0;let addonTotal=0;
function recalcTotal(){
  const pax=parseInt(document.getElementById('pax')?.value||1);
  const base=BASE_PRICE*pax;const tax=Math.round(base*.12);const tot=Math.max(0,base+tax-disc+addonTotal);
  document.getElementById('base-show').textContent='₹'+base.toLocaleString('en-IN');
  document.getElementById('total-show').textContent='₹'+tot.toLocaleString('en-IN');
  document.getElementById('pay-btn').textContent='Confirm & Pay ₹'+tot.toLocaleString('en-IN');
  document.getElementById('base-amt').value=base;
}
function updateTrain(sel){
  const pr=parseFloat(sel.options[sel.selectedIndex].dataset.price||0);
  if(pr){window._baseOverride=pr;document.getElementById('base-amt').value=pr;recalcTotal();}
}
const _origCheckPromo=window.checkPromo;
window.checkPromo=function(){
  const code=document.getElementById('promo-in')?.value?.trim().toUpperCase();
  const base=parseFloat(document.getElementById('base-amt')?.value||BASE_PRICE);
  if(!code)return;
  fetch(_BASE+'/api.php?a=promo&code='+encodeURIComponent(code)+'&amount='+base).then(r=>r.json()).then(d=>{
    const msg=document.getElementById('promo-msg');
    if(d.ok){disc=d.disc;document.getElementById('disc-amt').value=disc;document.getElementById('disc-row').classList.remove('hidden');document.getElementById('disc-show').textContent='-₹'+disc.toLocaleString('en-IN');if(msg)msg.innerHTML='<span class="tag t-green">'+d.label+' — saving ₹'+disc.toLocaleString('en-IN')+'</span>';recalcTotal();}
    else{disc=0;document.getElementById('disc-amt').value=0;document.getElementById('disc-row').classList.add('hidden');if(msg)msg.innerHTML='<span class="tag t-red">'+(d.msg||'Invalid code')+'</span>';recalcTotal();}
  });
};

/* ─── Payment Gateway Interactions ─── */
function selectPayMethod(el,method){
  document.querySelectorAll('.pay-method-item').forEach(m=>m.classList.remove('selected'));
  el.classList.add('selected');
  el.querySelector('input[type=radio]').checked=true;
  // Hide all sub-panels, show selected
  document.querySelectorAll('.pay-sub-panel').forEach(p=>p.style.display='none');
  const panel=document.getElementById('pay-sub-'+method);
  if(panel){panel.style.display='block';panel.style.animation='none';panel.offsetHeight;panel.style.animation='paySubIn .3s ease';}
}
function selectUpiApp(btn,app){
  document.querySelectorAll('#pay-sub-upi .pay-app-btn').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');
  const v=document.getElementById('upi-app-val');if(v)v.value=app;
  // Clear manual UPI ID if app selected
  const uid=document.getElementById('upi-id-input');if(uid)uid.value='';
}
function syncUpiId(val){
  // If user types UPI ID manually, clear app selection
  if(val.trim()){document.querySelectorAll('#pay-sub-upi .pay-app-btn').forEach(b=>b.classList.remove('selected'));const v=document.getElementById('upi-app-val');if(v)v.value='manual';}
}
function selectBank(btn,bank){
  document.querySelectorAll('#pay-sub-nb .pay-app-btn').forEach(b=>b.classList.remove('selected'));
  btn.classList.add('selected');
  const v=document.getElementById('nb-bank-val');if(v)v.value=bank;
}
function selectEmi(el){
  document.querySelectorAll('.emi-option').forEach(o=>o.classList.remove('selected'));
  el.classList.add('selected');
}
function fmtCard(inp){
  let v=inp.value.replace(/\D/g,'').slice(0,16);
  inp.value=v.replace(/(\d{4})/g,'$1 ').trim();
}
function fmtExpiry(inp){
  let v=inp.value.replace(/\D/g,'');
  if(v.length>2)v=v.slice(0,2)+'/'+v.slice(2,4);
  inp.value=v;
}
function validateAndPay(){
  const method=document.querySelector('input[name=pay]:checked')?.value||'upi';
  if(method==='card'){
    const cn=(document.getElementById('card-number')?.value||'').replace(/\s/g,'');
    const ex=document.querySelector('input[name=card_expiry]')?.value||'';
    const cv=document.querySelector('input[name=card_cvv]')?.value||'';
    const nm=document.querySelector('input[name=card_name]')?.value||'';
    if(cn.length<15){alert('Please enter a valid card number.');return false;}
    if(!/^\d{2}\/\d{2}$/.test(ex)){alert('Please enter a valid expiry date (MM/YY).');return false;}
    if(cv.length<3){alert('Please enter a valid CVV.');return false;}
    if(!nm.trim()){alert('Please enter the name on card.');return false;}
  }
  if(method==='nb'){
    const bk=document.getElementById('nb-bank-val')?.value||'';
    if(!bk){alert('Please select a bank for Net Banking.');return false;}
  }
  if(method==='upi'){
    const app=document.getElementById('upi-app-val')?.value||'';
    const uid=document.getElementById('upi-id-input')?.value||'';
    if(!app&&!uid.trim()){alert('Please select a UPI app or enter your UPI ID.');return false;}
  }
  // Show processing overlay
  const ov=document.getElementById('pay-processing');
  if(ov){ov.style.display='flex';}
  const btn=document.getElementById('pay-btn');
  if(btn){btn.disabled=true;btn.textContent='Processing…';}
  return true;
}
/* ─── Add-on Toggle ─── */
function toggleAddon(el){
  el.classList.toggle('selected');
  const cb=el.querySelector('input[type=checkbox]');
  if(cb) cb.checked=el.classList.contains('selected');
  addonTotal=0;
  document.querySelectorAll('.addon-item.selected').forEach(item=>{
    addonTotal+=parseInt(item.dataset.price||0);
  });
  const addonRow=document.getElementById('addon-row');
  const addonShow=document.getElementById('addon-show');
  const addonTotalRow=document.getElementById('addon-total-row');
  const addonTotalVal=document.getElementById('addon-total-val');
  if(addonTotal>0){
    if(addonRow) addonRow.classList.remove('hidden');
    if(addonShow) addonShow.textContent='+₹'+addonTotal.toLocaleString('en-IN');
    if(addonTotalRow) addonTotalRow.style.display='flex';
    if(addonTotalVal) addonTotalVal.textContent='₹'+addonTotal.toLocaleString('en-IN');
  } else {
    if(addonRow) addonRow.classList.add('hidden');
    if(addonTotalRow) addonTotalRow.style.display='none';
  }
  recalcTotal();
}
</script>
<?php require_once __DIR__.'/includes/footer.php';?>
