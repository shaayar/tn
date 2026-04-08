<?php
require_once __DIR__.'/includes/bootstrap.php';
mustLogin();
$me=me();
$pg=max(1,(int)($_GET['pg']??1));
$res=DB::paginate("SELECT * FROM bookings WHERE user_id=? ORDER BY created_at DESC",[$me['id']],$pg,10);
$icons=['Flight'=>'✈','Hotel'=>'🏨','Package'=>'📦','Train'=>'🚆','Bus'=>'🚌','Cab'=>'🚕','Cruise'=>'🚢'];
$pageTitle='My Bookings — TravelNest';
require_once __DIR__.'/includes/header.php';
?>
<div class="ov" id="det-modal"><div class="mod"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>
<div class="sec">
  <h2 class="stitle">My Bookings</h2>
  <p class="ssub"><?= $res['total'] ?> total booking<?= $res['total']!=1?'s':'' ?></p>

  <?php if(empty($res['data'])): ?>
  <div class="empty-state">
    <span class="empty-emoji">📋</span>
    <div class="empty-title">No bookings yet</div>
    <div class="empty-desc">Start exploring and book your first trip!</div>
    <a href="<?= BASE ?>/index.php" class="btn btn-primary">Explore Deals</a>
  </div>
  <?php else: ?>

  <div class="bk-timeline">
  <?php foreach($res['data'] as $b):
    $travelDate = $b['travel_date'] ? fmtDate($b['travel_date']) : fmtDate($b['created_at']);
    $statusCls  = ['Confirmed'=>'t-green','Pending'=>'t-amber','Cancelled'=>'t-red','Completed'=>'t-blue'];
    $sCls       = $statusCls[$b['booking_status']] ?? 't-gray';
    $bkCls      = 'bk-card status-'.strtolower($b['booking_status']);
  ?>
  <div class="<?=$bkCls?>">
    <div class="card mb10">
      <div class="flex g16">
        <div style="font-size:30px;flex-shrink:0;width:44px;text-align:center"><?= $icons[$b['booking_type']] ?? '📋' ?></div>
        <div style="flex:1;min-width:0">
          <div class="fw5" style="font-size:15px"><?= clean($b['item_name']) ?></div>
          <div class="flex g12 mt6 wrap-x">
            <span class="xs">🗓 <?= $travelDate ?></span>
            <span class="xs">ID: <span class="acc fw5"><?= clean($b['booking_ref']) ?></span></span>
            <span class="xs">PNR: <?= clean($b['pnr_number'] ?? 'N/A') ?></span>
            <span class="xs"><?= (int)$b['passengers'] ?> pax · <?= clean($b['payment_method'] ?? '') ?></span>
          </div>
        </div>
        <div class="tr" style="flex-shrink:0">
          <div class="acc fw6" style="font-size:18px"><?= rupee($b['total_amount']) ?></div>
          <span class="tag <?= $sCls ?> mt4"><?= $b['booking_status'] ?></span>
          <div class="flex g6 mt8" style="justify-content:flex-end">
            <a href="<?= BASE ?>/invoice.php?ref=<?= clean($b['booking_ref']) ?>"
               class="btn btn-blue btn-xs">📄 Invoice</a>
            <?php if($b['booking_status'] === 'Confirmed'): ?>
            <button class="btn btn-danger btn-xs"
                    onclick="cancelBk('<?= clean($b['booking_ref']) ?>')">Cancel</button>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  </div>
  <?= pagLinks($res['page'], $res['last'], BASE.'/bookings.php?') ?>
  <?php endif; ?>
</div>

<?php require_once __DIR__.'/includes/footer.php'; ?>
