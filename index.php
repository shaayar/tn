<?php
require_once __DIR__.'/includes/bootstrap.php';
$featH=DB::all("SELECT * FROM hotels WHERE is_active=1 ORDER BY rating DESC LIMIT 4");
$featP=DB::all("SELECT * FROM packages WHERE is_active=1 ORDER BY id ASC LIMIT 3");
$featF=DB::all("SELECT * FROM flights WHERE is_active=1 AND price<5000 ORDER BY price ASC LIMIT 4");
$hotelImages=[
  'The Taj Mahal Palace'=>'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=600&h=400&fit=crop',
  'ITC Maurya'=>'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=600&h=400&fit=crop',
  'The Leela Palace'=>'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=600&h=400&fit=crop',
  'Grand Hyatt Goa'=>'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&h=400&fit=crop',
  'JW Marriott Kolkata'=>'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=600&h=400&fit=crop',
  'Four Seasons Mumbai'=>'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=600&h=400&fit=crop',
  'The Oberoi Udaivilas'=>'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=600&h=400&fit=crop',
  'Taj Exotica Maldives'=>'https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=600&h=400&fit=crop',
];
$defaultHotelImg='https://images.unsplash.com/photo-1455587734955-081b22074882?w=600&h=400&fit=crop';
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
$pageTitle='TravelNest — India\'s Travel Platform';
require_once __DIR__.'/includes/header.php';
?>
<!-- Det Modal -->
<div class="ov" id="det-modal"><div class="mod" style="max-width:560px"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>

<div class="hero">
  <div class="hero-overlay"></div>
  <h1>Discover the World<br><em>with TravelNest</em></h1>
  <p class="hero-sub">Flights · Hotels · Trains · Buses · Cabs · Cruises · Packages</p>
  <div style="display:flex;justify-content:center;margin-bottom:22px">
    <div class="stabs">
      <a class="stab on" href="#" onclick="sTab('fl',this);return false"><span class="material-symbols-outlined">flight</span> Flights</a>
      <a class="stab" href="#" onclick="sTab('ht',this);return false"><span class="material-symbols-outlined">apartment</span> Hotels</a>
      <a class="stab" href="#" onclick="sTab('pk',this);return false"><span class="material-symbols-outlined">luggage</span> Packages</a>
      <a class="stab" href="#" onclick="sTab('tr',this);return false"><span class="material-symbols-outlined">train</span> Trains</a>
      <a class="stab" href="#" onclick="sTab('bu',this);return false"><span class="material-symbols-outlined">directions_bus</span> Buses</a>
      <a class="stab" href="#" onclick="sTab('cb',this);return false"><span class="material-symbols-outlined">local_taxi</span> Cabs</a>
      <a class="stab" href="#" onclick="sTab('cr',this);return false"><span class="material-symbols-outlined">directions_boat</span> Cruises</a>
    </div>
  </div>
  <div class="sbox">
    <div id="s-fl" class="spane on"><form action="<?=BASE?>/flights.php" method="GET"><div class="sg c5">
      <div class="fg il"><label>From</label><input name="from" placeholder="Mumbai (BOM)"></div>
      <div class="fg il"><label>To</label><input name="to" placeholder="Delhi (DEL)"></div>
      <div class="fg il"><label>Depart</label><input type="text" name="dep" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" maxlength="10" title="Format: dd/mm/yyyy" oninput="formatDateInput(event)"></div>
      <div class="fg il"><label>Travellers</label><select name="pax"><option>1 Adult</option><option>2 Adults</option><option>Family (2+2)</option></select></div>
      <div class="fg il"><label>Class</label><select name="cls"><option>Economy</option><option>Business</option><option>First Class</option></select></div>
    </div><button type="submit" class="btn btn-primary w100">Search Flights →</button></form></div>
    <div id="s-ht" class="spane"><form action="<?=BASE?>/hotels.php" method="GET"><div class="sg c4">
      <div class="fg il"><label>City / Hotel</label><input name="q" placeholder="Mumbai, Goa..."></div>
      <div class="fg il"><label>Check-in</label><input type="text" name="ci" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" maxlength="10" title="Format: dd/mm/yyyy" oninput="formatDateInput(event)"></div>
      <div class="fg il"><label>Check-out</label><input type="text" name="co" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" maxlength="10" title="Format: dd/mm/yyyy" oninput="formatDateInput(event)"></div>
      <div class="fg il"><label>Guests</label><select name="guests"><option>1</option><option>2</option><option>3</option></select></div>
    </div><button type="submit" class="btn btn-primary w100">Search Hotels →</button></form></div>
    <div id="s-tr" class="spane"><form action="<?=BASE?>/trains.php" method="GET"><div class="sg c4">
      <div class="fg il"><label>From Station</label><input name="from" placeholder="CSTM — Mumbai CST"></div>
      <div class="fg il"><label>To Station</label><input name="to" placeholder="NDLS — New Delhi"></div>
      <div class="fg il"><label>Date</label><input type="text" name="dep" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" maxlength="10" title="Format: dd/mm/yyyy" oninput="formatDateInput(event)"></div>
      <div class="fg il"><label>Class</label><select name="cls"><option>All</option><option>1A</option><option>2A</option><option>3A</option><option>SL</option></select></div>
    </div><button type="submit" class="btn btn-primary w100">Search Trains →</button></form></div>
    <div id="s-bu" class="spane"><form action="<?=BASE?>/buses.php" method="GET"><div class="sg c4">
      <div class="fg il"><label>From</label><input name="from" placeholder="Pune"></div>
      <div class="fg il"><label>To</label><input name="to" placeholder="Mumbai"></div>
      <div class="fg il"><label>Date</label><input type="text" name="dep" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" maxlength="10" title="Format: dd/mm/yyyy" oninput="formatDateInput(event)"></div>
      <div class="fg il"><label>Type</label><select name="type"><option>All</option><option>AC Sleeper</option><option>Volvo</option><option>Semi-Sleeper</option></select></div>
    </div><button type="submit" class="btn btn-primary w100">Search Buses →</button></form></div>
    <div id="s-cb" class="spane"><form action="<?=BASE?>/cabs.php" method="GET"><div class="sg c4">
      <div class="fg il"><label>Pickup</label><input name="pickup" placeholder="Airport, Hotel..."></div>
      <div class="fg il"><label>Drop</label><input name="drop" placeholder="Destination..."></div>
      <div class="fg il"><label>Date &amp; Time</label><input type="datetime-local" name="dt"></div>
      <div class="fg il"><label>Type</label><select name="type"><option>Hatchback</option><option>Sedan</option><option>SUV</option><option>Luxury</option></select></div>
    </div><button type="submit" class="btn btn-primary w100">Book Cab →</button></form></div>
    <div id="s-pk" class="spane"><form action="<?=BASE?>/packages.php" method="GET"><div class="sg c3">
      <div class="fg il"><label>Destination</label><input name="q" placeholder="Goa, Kerala, Bali..."></div>
      <div class="fg il"><label>Duration</label><select name="dur"><option value="">Any</option><option>3-5 Nights</option><option>5-7 Nights</option><option>7+ Nights</option></select></div>
      <div class="fg il"><label>Category</label><select name="cat"><option value="">All</option><option>Domestic</option><option>International</option></select></div>
    </div><button type="submit" class="btn btn-primary w100">Search Packages →</button></form></div>
    <div id="s-cr" class="spane"><form action="<?=BASE?>/cruises.php" method="GET"><div class="sg c3">
      <div class="fg il"><label>Departure Port</label><input name="from" placeholder="Mumbai, Kochi..."></div>
      <div class="fg il"><label>Destination</label><input name="to" placeholder="Goa, Maldives..."></div>
      <div class="fg il"><label>Category</label><select name="cat"><option value="">All</option><option>Domestic</option><option>International</option></select></div>
    </div><button type="submit" class="btn btn-primary w100">Search Cruises →</button></form></div>
  </div>
</div>

<div class="sec">
  <!-- Deal Marquee -->
  <div class="marquee-wrap reveal">
    <div class="marquee">
      <?php
      $deals=[
        '✈️ Mumbai → Delhi from <span class="deal-price">₹1,899</span>',
        '🏨 Goa Beach Resort from <span class="deal-price">₹2,499/night</span>',
        '🚆 Rajdhani Express from <span class="deal-price">₹1,245</span>',
        '📦 Kerala 5N/6D from <span class="deal-price">₹15,999</span>',
        '🚢 Mumbai-Goa Cruise from <span class="deal-price">₹8,999</span>',
        '🚕 Airport Transfer from <span class="deal-price">₹499</span>',
        '✈️ BLR → GOI from <span class="deal-price">₹2,199</span>',
        '🏨 Jaipur Heritage Stay from <span class="deal-price">₹3,299/night</span>',
      ];
      // Duplicate for seamless loop
      for($i=0;$i<2;$i++){
        foreach($deals as $d){
          echo '<span class="deal-item"><span class="deal-dot"></span> '.$d.'</span>';
        }
      }
      ?>
    </div>
  </div>

  <!-- Promo banner -->
  <div class="card mb24 reveal" style="background:linear-gradient(135deg,rgba(240,165,0,.08),rgba(56,189,248,.04));border-color:rgba(240,165,0,.3)">
    <div class="flex sb wrap-x g12"><div><h3 style="font-size:20px;color:var(--accent);margin-bottom:6px">🎉 Holi Special — 40% Off!</h3><p class="sm">Use code <strong class="acc">HOLI2026</strong> on all bookings. Limited time!</p></div><a href="<?=BASE?>/flights.php" class="btn btn-primary">Book Now →</a></div>
  </div>

  <!-- Trending Destinations -->
  <h2 class="stitle reveal">Trending Destinations</h2><p class="ssub reveal">Where Indians are flying this season</p>
  <style>
    .dest-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:24px}
    .dest-img-card{border-radius:14px;overflow:hidden;position:relative;height:240px;cursor:pointer;transition:all .35s cubic-bezier(.22,1,.36,1);box-shadow:0 2px 12px rgba(0,0,0,.08)}
    .dest-img-card:hover{transform:translateY(-6px);box-shadow:0 12px 36px rgba(0,0,0,.15)}
    .dest-img-card img{width:100%;height:100%;object-fit:cover;transition:transform .5s cubic-bezier(.22,1,.36,1)}
    .dest-img-card:hover img{transform:scale(1.08)}
    .dest-img-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.7) 0%,rgba(0,0,0,.15) 40%,transparent 70%);display:flex;flex-direction:column;justify-content:flex-end;padding:18px 16px;text-decoration:none;color:#fff}
    .dest-img-overlay .dest-name{font-size:17px;font-weight:700;margin-bottom:2px;letter-spacing:-.3px}
    .dest-img-overlay .dest-region{font-size:12px;opacity:.85;margin-bottom:8px}
    .dest-img-overlay .dest-price-tag{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;background:rgba(0,140,255,.85);border-radius:20px;font-size:12px;font-weight:700;width:fit-content;backdrop-filter:blur(6px)}
    .dest-img-overlay .dest-badge{position:absolute;top:12px;right:12px;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;backdrop-filter:blur(8px)}
    @media(max-width:960px){.dest-grid{grid-template-columns:repeat(3,1fr)}}
    @media(max-width:600px){.dest-grid{grid-template-columns:1fr 1fr}.dest-img-card{height:200px}}
  </style>
  <div class="dest-grid reveal">
    <?php
    $destinations=[
      ['Goa','India','from ₹2,499','Hot Deal','flights','https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=400&h=500&fit=crop','t-amber'],
      ['Jaipur','Rajasthan','from ₹1,899','Cultural','flights','https://images.unsplash.com/photo-1477587458883-47145ed94245?w=400&h=500&fit=crop','t-blue'],
      ['Tokyo','Japan','from ₹32,000','International','flights','https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=400&h=500&fit=crop','t-purple'],
      ['Manali','Himachal','from ₹3,299','Adventure','hotels','https://images.unsplash.com/photo-1544735716-392fe2489ffa?w=400&h=500&fit=crop','t-green'],
      ['Maldives','Indian Ocean','from ₹65,000','Luxury','packages','https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=400&h=500&fit=crop','t-teal'],
    ];
    foreach($destinations as $d):?>
    <a href="<?=BASE?>/<?=$d[4]?>.php" class="dest-img-card">
      <img src="<?=$d[5]?>" alt="<?=$d[0]?>" loading="lazy">
      <div class="dest-img-overlay">
        <span class="dest-badge tag <?=$d[6]?>"><?=$d[3]?></span>
        <div class="dest-name"><?=$d[0]?></div>
        <div class="dest-region"><?=$d[1]?></div>
        <span class="dest-price-tag"><?=$d[2]?></span>
      </div>
    </a>
    <?php endforeach;?>
  </div>

  <!-- Cheap Flights -->
  <h2 class="stitle reveal">Best Flight Deals</h2><p class="ssub reveal">Top routes under ₹5,000 today</p>
  <style>
    .deal-flights-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:24px}
    .deal-fc{background:#fff;border:1px solid var(--border);border-radius:var(--rl);padding:20px 22px;transition:all var(--transition);cursor:pointer;position:relative;overflow:hidden;box-shadow:var(--shadow)}
    .deal-fc::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:4px 0 0 4px;background:var(--accent)}
    .deal-fc:hover{border-color:rgba(0,140,255,.3);box-shadow:var(--shadow-md);transform:translateY(-3px)}
    .deal-fc-inner{display:flex;align-items:center;gap:14px}
    .deal-fc-airline{display:flex;align-items:center;gap:10px;min-width:120px;flex-shrink:0}
    .deal-fc-route{flex:1;display:flex;align-items:center;gap:10px;min-width:0}
    .deal-fc-price{text-align:right;min-width:100px;flex-shrink:0}
    @media(max-width:600px){.deal-flights-grid{grid-template-columns:1fr}.deal-fc-inner{flex-wrap:wrap}}
  </style>
  <div class="deal-flights-grid reveal">
    <?php foreach($featF as $f):?>
    <div class="deal-fc" onclick="showFlight(<?=$f['id']?>)" data-airline="<?=clean($f['airline'])?>">
      <div class="deal-fc-inner">
        <div class="deal-fc-airline">
          <div class="al-logo"><?=$f['emoji']?></div>
          <div>
            <div class="fw5" style="font-size:13px"><?=clean($f['airline'])?></div>
            <div class="xs"><?=clean($f['flight_code'])?></div>
            <span class="tag t-green" style="font-size:10px;margin-top:3px;display:inline-block"><?=$f['stops']?></span>
          </div>
        </div>
        <div class="deal-fc-route">
          <div class="tc"><div class="ftime"><?=$f['departure_time']?></div><div class="fcode"><?=clean($f['from_code'])?></div></div>
          <div style="flex:1;display:flex;align-items:center;gap:6px;min-width:60px">
            <div class="fline"></div><div class="fdur"><?=clean($f['duration'])?></div><div class="fline"></div>
          </div>
          <div class="tc"><div class="ftime"><?=$f['arrival_time']?></div><div class="fcode"><?=clean($f['to_code'])?></div></div>
        </div>
        <div class="deal-fc-price">
          <div class="fprice"><?=rupee($f['price'])?></div>
          <div class="xs"><?=clean($f['class'])?></div>
          <?php if($f['seats_available']<15):?><div class="urgency-badge mt4">🔥 <?=$f['seats_available']?> seats left</div><?php endif;?>
          <div class="flex g6 mt8" style="justify-content:flex-end">
            <button class="btn btn-ghost btn-xs" onclick="event.stopPropagation();showFlight(<?=$f['id']?>)">Details</button>
            <a href="<?=BASE?>/book.php?type=flight&id=<?=$f['id']?>" class="btn btn-primary btn-xs" onclick="event.stopPropagation()">Book</a>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach;?>
  </div>

  <!-- Hotels -->
  <h2 class="stitle reveal">Top-Rated Hotels</h2><p class="ssub reveal">Exclusive TravelNest rates</p>
  <div class="g4 mb24 reveal">
    <?php foreach($featH as $h):
      $hImg=$hotelImages[$h['name']]??$defaultHotelImg;
    ?>
    <div class="hc">
      <div class="hc-img" onclick="showHotel(<?=$h['id']?>)" style="background:url('<?=$hImg?>') center/cover no-repeat">
        <span class="rb"><?=$h['rating']?></span>
      </div>
      <div class="hc-body">
        <div class="fw5 mb4" style="font-size:14px;cursor:pointer" onclick="showHotel(<?=$h['id']?>)"><?=clean($h['name'])?></div>
        <div class="xs mb6">📍 <?=clean($h['city'])?></div>
        <div class="stars mb6"><?=str_repeat('★',(int)$h['stars'])?></div>
        <?php if($h['free_cancellation']):?><span class="tag t-green" style="font-size:10px;display:block;margin-bottom:6px">Free Cancellation</span><?php endif;?>
        <div class="flex sb mt8"><div><span class="acc fw5" style="font-size:16px"><?=rupee($h['price_per_night'])?></span><span class="xs">/night</span></div>
          <div class="flex g4r"><button class="btn btn-ghost btn-xs" onclick="showHotel(<?=$h['id']?>)">Details</button><a href="<?=BASE?>/book.php?type=hotel&id=<?=$h['id']?>" class="btn btn-primary btn-xs">Book</a></div>
        </div>
      </div>
    </div>
    <?php endforeach;?>
  </div>

  <!-- Packages -->
  <h2 class="stitle reveal">Holiday Packages</h2><p class="ssub reveal">All-inclusive — flights + hotels + sightseeing</p>
  <div class="pkg-grid mb24 reveal">
    <?php foreach($featP as $p):
      $dest=$p['destination']??'';
      $pImg=$packageImages[$dest]??$defaultPkgImg;
      $tagColors=['Best Seller'=>'t-amber','Nature'=>'t-green','Heritage'=>'t-purple','Honeymoon'=>'t-red','International'=>'t-blue','Adventure'=>'t-teal','Island Paradise'=>'t-teal','Family'=>'t-blue','Spiritual'=>'t-purple','Extreme'=>'t-red','Popular'=>'t-amber','Weekend'=>'t-green','Wildlife'=>'t-green','Ultra Luxury'=>'t-gold','Bucket List'=>'t-red','Scenic'=>'t-blue','Premium'=>'t-gold','Cultural'=>'t-purple','Off-beat'=>'t-teal'];
      $tc=$tagColors[$p['tag']]??'t-amber';
    ?>
    <div class="pkg-card" onclick="showPackage(<?=$p['id']?>)">
      <div class="pkg-card-img" style="background-image:url('<?=$pImg?>')">
        <span class="pkg-nights">🌙 <?=$p['nights']?> Nights</span>
        <span class="pkg-tag tag <?=$tc?>"><?=clean($p['tag'])?></span>
      </div>
      <div class="pkg-card-body">
        <div class="pkg-name"><?=clean($p['name'])?></div>
        <div class="pkg-dest">📍 <?=clean($dest)?></div>
        <?php if(isset($p['inclusions'])):?><div class="pkg-inclusions"><?php foreach(array_slice(incArr($p['inclusions']),0,3) as $inc):?><span class="chip"><?=clean($inc)?></span><?php endforeach;?></div><?php endif;?>
        <div class="pkg-card-footer">
          <div><span class="pkg-price"><?=rupee($p['price'])?></span><div class="pkg-per">per person</div></div>
          <a href="<?=BASE?>/book.php?type=package&id=<?=$p['id']?>" class="btn btn-primary btn-sm" onclick="event.stopPropagation()">Book →</a>
        </div>
      </div>
    </div>
    <?php endforeach;?>
  </div>

  <!-- Why TravelNest -->
  <h2 class="stitle reveal tc">Why TravelNest?</h2><p class="ssub reveal tc">Trusted by millions of travellers across India</p>
  <div class="g4 reveal">
    <?php foreach([['🛡️','Secure Payments','RBI-compliant 256-bit SSL encryption on all transactions'],['🔄','Free Cancellation','500+ hotels & select flights with hassle-free cancellation'],['📞','24/7 Support','1800-103-8747 Toll-Free — we\'re always here for you'],['💎','Loyalty Rewards','Earn TravelCoins on every booking, redeem for discounts']] as $u):?>
    <div class="card why-card">
      <span class="why-icon"><?=$u[0]?></span>
      <h3 style="font-size:15px;margin-bottom:8px"><?=$u[1]?></h3>
      <p class="sm" style="line-height:1.6"><?=$u[2]?></p>
    </div>
    <?php endforeach;?>
  </div>

</div>
<?php require_once __DIR__.'/includes/footer.php';?>
