<?php
require_once __DIR__.'/includes/bootstrap.php';
$q=clean($_GET['q']??'');$pg=max(1,(int)($_GET['pg']??1));
$w=['is_active=1'];$p=[];
if($q){$w[]='(vehicle_name LIKE ? OR cab_type LIKE ?)';$lk="%$q%";$p[]=$lk;$p[]=$lk;}
$res=DB::paginate("SELECT * FROM cabs WHERE ".implode(' AND ',$w)." ORDER BY id ASC",$p,$pg);
$cabImages=[
  'Wagon R / Alto'=>'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=400&h=300&fit=crop',
  'Swift Dzire / Etios'=>'https://images.unsplash.com/photo-1617469767053-d3b523a0b982?w=400&h=300&fit=crop',
  'Innova Crysta / XL6'=>'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=400&h=300&fit=crop',
  'Honda City / Ciaz'=>'https://images.unsplash.com/photo-1590362891991-f776e747a588?w=400&h=300&fit=crop',
  'Toyota Fortuner'=>'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=400&h=300&fit=crop',
  'Mercedes E-Class / BMW 5'=>'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=400&h=300&fit=crop',
  'Tata Nexon EV'=>'https://images.unsplash.com/photo-1593941707882-a5bba14938c7?w=400&h=300&fit=crop',
  'MG ZS EV'=>'https://images.unsplash.com/photo-1560958089-b8a1929cea89?w=400&h=300&fit=crop',
  'Hyundai Ioniq 5'=>'https://images.unsplash.com/photo-1606611013016-969c19ba27a5?w=400&h=300&fit=crop',
  'BYD e6'=>'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=400&h=300&fit=crop',
  'Tata Tiago EV'=>'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=400&h=300&fit=crop',
  'BMW iX'=>'https://images.unsplash.com/photo-1617531653332-bd46c24f2068?w=400&h=300&fit=crop',
];
$defaultCabImg='https://images.unsplash.com/photo-1502877338535-766e1452684a?w=400&h=300&fit=crop';
$pageTitle='Cab Rentals — TravelNest';
require_once __DIR__.'/includes/header.php';?>
<div class="ov" id="det-modal"><div class="mod" style="max-width:700px"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>
<meta name="csrf" content="<?=csrf()?>">
<div class="sec">
  <h2 class="stitle">Cab Rentals</h2><p class="ssub"><?=$res['total']?> available</p>
  <form method="GET" class="fbar mb16">
    <input name="q" value="<?=clean($q)?>" placeholder="Search...">
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
  </form>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
  <?php foreach($res['data'] as $item):
    $nm=$item['vehicle_name']??'';
    $pr=$item['base_fare']??0;
    $cabImg=$cabImages[$nm]??$defaultCabImg;
    $fn='showCab';
  ?>
  <div class="hc" style="cursor:pointer" onclick="<?=$fn?>(<?=$item['id']?>)">
    <div class="hc-img" style="background:url('<?=$cabImg?>') center/cover no-repeat;height:180px">
      <span class="rb" style="background:linear-gradient(135deg,var(--accent),#d4900a);color:#000"><?=$item['cab_type']?></span>
    </div>
    <div class="hc-body">
      <div class="fw5 mb4" style="font-size:15px"><?=clean($nm)?></div>
      <div class="sm mb6">👥 <?=$item['capacity']?> passengers · 📏 Min <?=$item['min_km']?> km</div>
      <?php if(isset($item['amenities'])):?><div class="flex wrap-x g4r mb8"><?php foreach(amenArr($item['amenities']) as $am):?><span class="chip"><?=clean($am)?></span><?php endforeach;?></div><?php endif;?>
      <div class="flex sb mt8">
        <div><span class="acc fw5" style="font-size:18px"><?=rupee($pr)?></span><span class="xs"> base + ₹<?=$item['price_per_km']?>/km</span></div>
        <div class="flex g4r">
          <button class="btn btn-ghost btn-xs" onclick="event.stopPropagation();<?=$fn?>(<?=$item['id']?>)">Details</button>
          <a href="<?=BASE?>/book.php?type=cab&id=<?=$item['id']?>" class="btn btn-primary btn-xs" onclick="event.stopPropagation()">Book</a>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach;?>
  </div>
  <?=pagLinks($res['page'],$res['last'],BASE."/cabs.php?q=".urlencode($q))?>
</div>
<?php require_once __DIR__.'/includes/footer.php';?>
