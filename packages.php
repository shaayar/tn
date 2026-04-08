<?php
require_once __DIR__.'/includes/bootstrap.php';
$q=clean($_GET['q']??'');$pg=max(1,(int)($_GET['pg']??1));
$w=['is_active=1'];$p=[];
if($q){$w[]='(name LIKE ? OR destination LIKE ?)';$lk="%$q%";$p[]=$lk;$p[]=$lk;}
$res=DB::paginate("SELECT * FROM packages WHERE ".implode(' AND ',$w)." ORDER BY id ASC",$p,$pg);
$packageImages=[
  'Goa'=>'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=600&h=400&fit=crop',
  'Kerala'=>'https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?w=600&h=400&fit=crop',
  'Rajasthan'=>'https://images.unsplash.com/photo-1477587458883-47145ed94245?w=600&h=400&fit=crop',
  'Bali'=>'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&h=400&fit=crop',
  'Dubai'=>'https://images.unsplash.com/photo-1518684079-3c830dcef090?w=600&h=400&fit=crop',
  'Manali'=>'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?w=600&h=400&fit=crop',
  'Andaman'=>'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&h=400&fit=crop',
  'Singapore'=>'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=600&h=400&fit=crop',
  'Rishikesh'=>'https://images.unsplash.com/photo-1600100397608-ef5765e06437?w=600&h=400&fit=crop',
  'Ladakh'=>'https://images.unsplash.com/photo-1626015449493-dbfb0ae3d965?w=600&h=400&fit=crop',
  'Bangkok & Phuket'=>'https://images.unsplash.com/photo-1528181304800-259b08848526?w=600&h=400&fit=crop',
  'Coorg'=>'https://images.unsplash.com/photo-1597652161088-47d041acf7a5?w=600&h=400&fit=crop',
  'Jim Corbett'=>'https://images.unsplash.com/photo-1561731216-c3a4d99437d5?w=600&h=400&fit=crop',
  'Maldives'=>'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=600&h=400&fit=crop',
  'Nepal'=>'https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=600&h=400&fit=crop',
  'Srinagar'=>'https://images.unsplash.com/photo-1594484208280-efa00f96fc21?w=600&h=400&fit=crop',
  'Switzerland'=>'https://images.unsplash.com/photo-1530122037265-a5f1f91d3b99?w=600&h=400&fit=crop',
  'Vietnam'=>'https://images.unsplash.com/photo-1528127269322-539801943592?w=600&h=400&fit=crop',
  'Meghalaya'=>'https://images.unsplash.com/photo-1600198560498-1f2b57029201?w=600&h=400&fit=crop',
  'Bhutan'=>'https://images.unsplash.com/photo-1553856622-d1b352e24a00?w=600&h=400&fit=crop',
];
$defaultPkgImg='https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=600&h=400&fit=crop';
$tagColors=['Best Seller'=>'t-amber','Nature'=>'t-green','Heritage'=>'t-purple','Honeymoon'=>'t-red','International'=>'t-blue','Adventure'=>'t-teal','Island Paradise'=>'t-teal','Family'=>'t-blue','Spiritual'=>'t-purple','Extreme'=>'t-red','Popular'=>'t-amber','Weekend'=>'t-green','Wildlife'=>'t-green','Ultra Luxury'=>'t-gold','Bucket List'=>'t-red','Scenic'=>'t-blue','Premium'=>'t-gold','Cultural'=>'t-purple','Off-beat'=>'t-teal'];
$pageTitle='Holiday Packages — TravelNest';
require_once __DIR__.'/includes/header.php';?>
<div class="ov" id="det-modal"><div class="mod" style="max-width:700px"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>
<meta name="csrf" content="<?=csrf()?>">
<div class="sec">
  <h2 class="stitle">Holiday Packages</h2><p class="ssub"><?=$res['total']?> available</p>
  <form method="GET" class="fbar mb16">
    <input name="q" value="<?=clean($q)?>" placeholder="Search packages...">
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
  </form>
  <div class="pkg-grid">
  <?php foreach($res['data'] as $item):
    $nm=$item['name']??'';
    $dest=$item['destination']??'';
    $pr=$item['price']??0;
    $pImg=$packageImages[$dest]??$defaultPkgImg;
    $tc=$tagColors[$item['tag']??'']??'t-amber';
  ?>
  <div class="pkg-card" onclick="showPackage(<?=$item['id']?>)">
    <div class="pkg-card-img" style="background-image:url('<?=$pImg?>')">
      <span class="pkg-nights">🌙 <?=$item['nights']?> Nights</span>
      <?php if(isset($item['tag'])):?><span class="pkg-tag tag <?=$tc?>"><?=clean($item['tag'])?></span><?php endif;?>
    </div>
    <div class="pkg-card-body">
      <div class="pkg-name"><?=clean($nm)?></div>
      <div class="pkg-dest">📍 <?=clean($dest)?><?php if(isset($item['category'])):?> · <?=clean($item['category'])?><?php endif;?></div>
      <?php if(isset($item['inclusions'])):?><div class="pkg-inclusions"><?php foreach(array_slice(incArr($item['inclusions']),0,4) as $inc):?><span class="chip"><?=clean($inc)?></span><?php endforeach;?></div><?php endif;?>
      <div class="pkg-card-footer">
        <div><span class="pkg-price"><?=$pr>0?rupee($pr):'—'?></span><div class="pkg-per">per person</div></div>
        <div class="flex g4r">
          <button class="btn btn-ghost btn-xs" onclick="event.stopPropagation();showPackage(<?=$item['id']?>)">Details</button>
          <a href="<?=BASE?>/book.php?type=package&id=<?=$item['id']?>" class="btn btn-primary btn-xs" onclick="event.stopPropagation()">Book</a>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach;?>
  </div>
  <?=pagLinks($res['page'],$res['last'],BASE."/packages.php?q=".urlencode($q))?>
</div>
<?php require_once __DIR__.'/includes/footer.php';?>
