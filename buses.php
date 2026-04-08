<?php
require_once __DIR__.'/includes/bootstrap.php';
$q=clean($_GET['q']??'');$pg=max(1,(int)($_GET['pg']??1));
$w=['is_active=1'];$p=[];
if($q){$w[]='(name LIKE ? OR city LIKE ? OR destination LIKE ? OR from_city LIKE ? OR vehicle_name LIKE ? OR cruise_name LIKE ? OR train_name LIKE ? OR operator_name LIKE ?)';$lk="%$q%";for($i=0;$i<8;$i++)$p[]=$lk;}
$res=DB::paginate("SELECT * FROM buses WHERE ".implode(' AND ',$w)." ORDER BY id ASC",$p,$pg);
$busImages=[
  'Neeta Travels'=>'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=400&h=300&fit=crop',
  'VRL Travels'=>'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?w=400&h=300&fit=crop',
  'Sharma Transports'=>'https://images.unsplash.com/photo-1561361513-2d000a50f0dc?w=400&h=300&fit=crop',
  'Karnataka SRTC'=>'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=400&h=300&fit=crop',
  'Orange Travels'=>'https://images.unsplash.com/photo-1557223562-6c77ef16210f?w=400&h=300&fit=crop',
  'Paulo Travels'=>'https://images.unsplash.com/photo-1464219789935-c2d9d9aba644?w=400&h=300&fit=crop',
  'MSRTC Shivneri'=>'https://images.unsplash.com/photo-1597659840241-37e2b4b0add4?w=400&h=300&fit=crop',
  'Raj National Express'=>'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&h=300&fit=crop',
  'SRS Travels'=>'https://images.unsplash.com/photo-1494515843206-f3117d3f51b7?w=400&h=300&fit=crop',
  'Parveen Travels'=>'https://images.unsplash.com/photo-1586191582066-4f5be7e55867?w=400&h=300&fit=crop',
  'Green Line Travels'=>'https://images.unsplash.com/photo-1613467663874-fcd2bb3df1d2?w=400&h=300&fit=crop',
  'Kallada Travels'=>'https://images.unsplash.com/photo-1599835090765-931f8bcee7f0?w=400&h=300&fit=crop',
  'RSRTC Volvo'=>'https://images.unsplash.com/photo-1619735010143-c8e2d2e8c9ee?w=400&h=300&fit=crop',
  'Hans Travels'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=300&fit=crop',
];
$defaultBusImg='https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=400&h=300&fit=crop';
$pageTitle='Bus Tickets — TravelNest';
require_once __DIR__.'/includes/header.php';?>
<div class="ov" id="det-modal"><div class="mod" style="max-width:700px"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>
<meta name="csrf" content="<?=csrf()?>">
<div class="sec">
  <h2 class="stitle">Bus Tickets</h2><p class="ssub"><?=$res['total']?> available</p>
  <form method="GET" class="fbar mb16">
    <input name="q" value="<?=clean($q)?>" placeholder="Search...">
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
  </form>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px">
  <?php foreach($res['data'] as $item):
    $nm=$item['operator_name']??'';
    $pr=$item['price']??0;
    $busImg=$busImages[$nm]??$defaultBusImg;
  ?>
  <div class="hc" style="cursor:pointer" onclick="showBuse(<?=$item['id']?>)">
    <div class="hc-img" style="background:url('<?=$busImg?>') center/cover no-repeat;height:170px">
      <span class="rb" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:#fff;font-size:11px"><?=$item['bus_type']?></span>
    </div>
    <div class="hc-body">
      <div class="fw5 mb4" style="font-size:15px"><?=clean($nm)?></div>
      <?php if(isset($item['departure_time'])):?>
      <div class="flex g8 mt6 mb6" style="align-items:center">
        <div><div style="font-size:15px;font-weight:700"><?=$item['departure_time']?></div><div class="xs"><?=clean($item['from_city']??'')?></div></div>
        <div style="flex:1;border-top:1px dashed rgba(255,255,255,.15);margin:0 6px;position:relative"><span style="position:absolute;top:-9px;left:50%;transform:translateX(-50%);background:var(--card);padding:0 4px;font-size:10px;color:var(--text3)"><?=clean($item['duration']??'')?></span></div>
        <div class="tr"><div style="font-size:15px;font-weight:700"><?=$item['arrival_time']??''?></div><div class="xs"><?=clean($item['to_city']??'')?></div></div>
      </div>
      <?php endif;?>
      <?php if(isset($item['amenities'])):?><div class="flex wrap-x g4r mb6"><?php foreach(amenArr($item['amenities']) as $am):?><span class="chip"><?=clean($am)?></span><?php endforeach;?></div><?php endif;?>
      <div class="flex sb mt6">
        <div><span class="acc fw5" style="font-size:18px"><?=rupee($pr)?></span></div>
        <div class="flex g4r">
          <button class="btn btn-ghost btn-xs" onclick="event.stopPropagation();showBuse(<?=$item['id']?>)">Details</button>
          <a href="<?=BASE?>/book.php?type=buse&id=<?=$item['id']?>" class="btn btn-primary btn-xs" onclick="event.stopPropagation()">Book</a>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach;?>
  </div>
  <?=pagLinks($res['page'],$res['last'],BASE."/buses.php?q=".urlencode($q))?>
</div>
<?php require_once __DIR__.'/includes/footer.php';?>
