<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/helpers.php';
$__addr    = stAddress();
$pageTitle = stSiteName() . ' — IT Solutions & Software Services' . ($__addr !== '' ? ' | ' . $__addr : '');
$pageDesc  = 'IT Solutions & Software Services. Reliable, locally supported technology for your business.' . ($__addr !== '' ? ' | ' . $__addr : '');

$testimonials = [];
try { $testimonials = query("SELECT * FROM testimonials WHERE active=1 ORDER BY position LIMIT 6"); } catch(\Throwable $e) {}
$__logoClients = [];
try { $__logoClients = query("SELECT org_name, logo_url FROM clients WHERE status='active' AND logo_url IS NOT NULL AND logo_url!='' ORDER BY " . sqlRand() . " LIMIT 20"); } catch(\Throwable $e) {}
// Fallback to the partners directory (client logos / names) when no CRM client logos exist
if (empty($__logoClients)) {
  try { $__logoClients = query("SELECT name AS org_name, logo_url FROM partners WHERE active=1 AND type='client' ORDER BY position ASC, id ASC LIMIT 24"); } catch(\Throwable $e) {}
}
// Show section if we have any clients/partners (even text-only, no logo required)

$newsItems = [];
try { $newsItems = query("SELECT id, title, slug, excerpt, category, author_name, published_at, COALESCE(cover_url, image_url) AS cover FROM news WHERE published=1 AND active=1 ORDER BY published_at DESC LIMIT 4"); } catch(\Throwable $e) {}

$homeProducts = [];
try {
    $homeProducts = query(
        "SELECT id,name,slug,tagline,summary,icon,lucide_icon,icon_color,badge,features,highlights," .
        "home_card_dark,home_card_wide,home_bg_css,demo_screenshot_url,tab_label,home_position,position " .
        "FROM products WHERE active=1 AND show_on_home=1 " .
        "ORDER BY COALESCE(NULLIF(home_position,0),position), id LIMIT 12"
    );
} catch(\Throwable $e) {
    // products table may not have new columns yet — run database_migrate_v2.sql
    try { $homeProducts = query("SELECT id,name,slug,tagline,summary,icon,badge,features,highlights,position FROM products WHERE active=1 ORDER BY position,id LIMIT 12"); }
    catch(\Throwable $e2) {}
}

// ── Site settings (CMS-driven homepage content) ──────────────────
$__s = siteSettings();

// Stats bar — admin-editable, fallback to defaults
$_def = [
  ['10+',  'Years of Experience',   'calendar'],
  ['650+', 'Happy Clients', 'users'],
  ['7+',   'Major Products',        'layers'],
  ['100%', 'Client Retention Rate', 'shield-check'],
];
$stats = [];
for ($__i=1;$__i<=4;$__i++) {
  $v = trim($__s["stat_{$__i}_value"] ?? '');
  $l = cms($__s, "stat_{$__i}_label");
  $stats[] = [$v?:$_def[$__i-1][0], $l?:$_def[$__i-1][1], $_def[$__i-1][2]];
}
unset($__i,$v,$l,$_def);

// ── Hero CMS variables — bilingual: admin sets EN + NP, cms() picks right one ──
$_heroTitle        = cms($__s, 'homepage_hero_title');
$_heroSub          = cms($__s, 'homepage_hero_subtitle');
$_heroBadge1       = cms($__s, 'hero_badge1_text');
$_heroBadge2       = cms($__s, 'hero_badge2_text');
$_heroCtaText      = cms($__s, 'homepage_cta_text');
$_heroCtaUrl       = trim($__s['homepage_cta_url'] ?? '');
$_heroCtaSecondary = cms($__s, 'hero_cta_secondary');
// ── Hero slider setup ────────────────────────────────────────────────
$_ctaHref  = trim($__s['homepage_cta_url'] ?? '') ?: url('contact.php');
$_ctaLabel = cms($__s,'homepage_cta_text') ?: __('home_hero_book_demo');
$_heroSlides = [];
// Primary source: site settings slides (Settings → Homepage → Hero Section)
for ($_hsi = 1; $_hsi <= 3; $_hsi++) {
  $_himg = trim($__s["hero_image_{$_hsi}"] ?? '');
  if (!$_himg) continue;
  $_htit = cms($__s, "hero_slide_{$_hsi}_title");
  $_hsub = cms($__s, "hero_slide_{$_hsi}_subtitle");
  $_heroSlides[] = [
    'img'   => $_himg,
    'title' => $_htit ?: ($_heroTitle ?: (isNepali() ? 'डिजिटाइजेसन र अटोमेसन' : 'IT Solutions & Automation')),
    'sub'   => $_hsub ?: ($_heroSub   ?: (isNepali() ? 'सहकारी एवं वित्तीय संस्थाहरूलाई रूपान्तरण गर्ने सुरक्षित र सहज प्रणाली।' : 'End-to-end software solutions purpose-built for Nepal\'s cooperatives and businesses.')),
    'link'  => '', 'btn' => '',
  ];
}
unset($_hsi, $_himg, $_htit, $_hsub);
// Secondary source: Banners admin (page_target = 'hero') if no explicit settings images exist.
if (empty($_heroSlides)) {
  try {
    $_heroBanners = query("SELECT * FROM banners WHERE page_target='hero' AND active=1 ORDER BY position ASC, id ASC LIMIT 5");
    foreach ($_heroBanners as $_hb) {
      $_heroSlides[] = [
        'img'   => trim($_hb['image_url'] ?? ''),
        'title' => trim($_hb['title'] ?? '') ?: ($_heroTitle ?: (isNepali() ? 'डिजिटाइजेसन र अटोमेसन' : 'IT Solutions & Automation')),
        'sub'   => trim($_hb['subtitle'] ?? '') ?: ($_heroSub ?: (isNepali() ? 'सहकारी एवं वित्तीय संस्थाहरूलाई रूपान्तरण गर्ने सुरक्षित र सहज प्रणाली।' : 'End-to-end software solutions purpose-built for Nepal\'s cooperatives and businesses.')),
        'link'  => trim($_hb['link_url'] ?? ''),
        'btn'   => trim($_hb['btn_text'] ?? ''),
      ];
    }
    unset($_heroBanners, $_hb);
  } catch(\Throwable $e) {}
}
// Final fallback slide if no hero content is configured.
if (empty($_heroSlides)) {
  $_heroSlides[] = [
    'img'   => '',
    'title' => $_heroTitle ?: (isNepali() ? 'डिजिटाइजेसन र अटोमेसन' : 'IT Solutions & Automation'),
    'sub'   => $_heroSub   ?: (isNepali() ? 'सहकारी एवं वित्तीय संस्थाहरूलाई रूपान्तरण गर्ने सुरक्षित र सहज प्रणाली।' : 'End-to-end software solutions purpose-built for Nepal\'s cooperatives and businesses.'),
    'link'  => $_ctaHref,
    'btn'   => $_ctaLabel,
  ];
}
// Bento section
$_bentoEyebrow = cms($__s, 'home_bento_eyebrow');
$_bentoTitle   = cms($__s, 'home_bento_title');
$_bentoSub     = cms($__s, 'home_bento_subtitle');
// "See it in action" section
$_inActionTitle = cms($__s, 'home_in_action_title');
$_inActionSub   = cms($__s, 'home_in_action_subtitle');
// SEO — override page title/desc when admin sets meta
if (!empty($__s['meta_description'])) $pageDesc = $__s['meta_description'];
$_metaTitle = cms($__s, 'home_meta_title');
if ($_metaTitle) $pageTitle = $_metaTitle;

$_stepIcons = ['calendar','file-check','settings','rocket'];
$_stepDefsT = isNepali()
  ? ['पहिलो परामर्श','कस्टम प्रस्ताव','सेटअप र माइग्रेसन','लाइभ जानुस']
  : ['Discovery Call','Proposal','Setup & Training','Go Live'];
$_stepDefsD = isNepali()
  ? ['तपाईंको आवश्यकता बुझ्छौं — निःशुल्क, कुनै बाध्यता छैन।',
     'विस्तृत प्रस्ताव २ कार्यदिवसभित्र पठाइन्छ।',
     'डाटा माइग्रेसन, कन्फिगरेसन र स्टाफ तालिम।',
     '२ हप्तामा ल������इभ। लन्च पछि ३० दिन अन-कल सहयोग।']
  : ['We learn your needs — free, no commitment.',
     'Detailed proposal with price & timeline in 2 days.',
     'We migrate data, configure the system and train staff.',
     'Live in 2 weeks. On-call support for 30 days post-launch.'];
$processSteps = [];
for ($__pi = 0; $__pi < 4; $__pi++) {
  $__n = $__pi + 1;
  $processSteps[] = [
    $_stepIcons[$__pi],
    cms($__s, "home_step{$__n}_title") ?: $_stepDefsT[$__pi],
    cms($__s, "home_step{$__n}_desc")  ?: $_stepDefsD[$__pi],
  ];
}
unset($__pi,$__n,$_stepIcons,$_stepDefsT,$_stepDefsD);

// Fonts and theme tokens are already loaded globally via includes/head.php (Poppins + Noto Sans Devanagari).
// No duplicate font loading needed here.

include 'includes/header.php';
?>

<!-- ══════════════════════════════════════════════
  § 1 — HERO SLIDER (full-width background image carousel)
══════════════════════════════════════════════ -->
<style>
.hero-ca .hero-h1  { animation: heroUp  .65s cubic-bezier(.22,.61,.36,1) both; }
.hero-ca .hero-bar { animation: heroBar .45s ease .15s both; }
.hero-ca .hero-sub { animation: heroUp  .65s cubic-bezier(.22,.61,.36,1) .28s both; }
.hero-ca .hero-cta { animation: heroUp  .65s cubic-bezier(.22,.61,.36,1) .42s both; }
@keyframes heroUp  { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
@keyframes heroBar { from { width:0; opacity:0; } to { width:3rem; opacity:1; } }
</style>
<div id="hero-slider"
  x-data="{
    cur: 0,
    total: <?= count($_heroSlides) ?>,
    timer: null,
    init() { if (this.total > 1) this.timer = setInterval(() => this.next(), 5500); },
    next() { this.cur = (this.cur + 1) % this.total; },
    prev() { this.cur = (this.cur - 1 + this.total) % this.total; },
    go(i)  { this.cur = i; clearInterval(this.timer); if (this.total > 1) this.timer = setInterval(() => this.next(), 5500); }
  }"
  x-init="init()"
  style="position:relative;overflow:hidden;height:clamp(420px,58vh,580px);background:#0a1023;">

  <!-- ── Slides ── -->
  <?php foreach($_heroSlides as $_hk => $_hs):
    $_hasImage = !empty($_hs['img']);
    $_bgStyle = $_hasImage
      ? "background-image:url('" . e($_hs['img']) . "');background-size:cover;background-position:center;filter:brightness(1.05);"
      : "background:linear-gradient(135deg,#0a1023 0%,#0f2057 50%,#1a0a3d 100%);";
    $_overlay = $_hasImage
      ? 'background:linear-gradient(100deg,rgba(8,14,30,.85) 0%,rgba(8,14,30,.70) 42%,rgba(8,14,30,.45) 100%);'
      : "background:linear-gradient(100deg,rgba(8,14,30,.75) 0%,rgba(8,14,30,.60) 42%,rgba(8,14,30,.35) 100%);";
    $_slideLink = !empty($_hs['link']) ? $_hs['link'] : $_ctaHref;
    $_slideBtn  = !empty($_hs['btn'])  ? $_hs['btn']  : $_ctaLabel;
  ?>
  <div
    :style="(cur === <?= $_hk ?> ? 'opacity:1;z-index:1; ' : 'opacity:0;z-index:0; ') + 'position:absolute;inset:0;<?= addslashes($_bgStyle) ?>transition:opacity .75s ease;will-change:opacity;'">
    <div style="position:absolute;inset:0;<?= $_overlay ?>"></div>
    <div class="container" style="position:relative;height:100%;display:flex;align-items:center;padding-top:2rem;">
      <div :class="cur==<?=$_hk?>?'hero-ca':''" style="max-width:36rem;">
        <h1 class="hero-h1" style="font-family:var(--font-display);font-size:clamp(1.75rem,4vw,2.5rem);font-weight:800;color:#fff;line-height:1.1;margin:0 0 .875rem;letter-spacing:-.025em;text-shadow:0 4px 20px rgba(0,0,0,.6), 0 2px 8px rgba(0,0,0,.5);">
          <?= nl2br(e($_hs['title'])) ?>
        </h1>
        <div class="hero-bar" style="width:3rem;height:3px;background:var(--primary);border-radius:2px;margin-bottom:1rem;"></div>
        <?php if(!empty($_hs['sub'])): ?>
        <p class="hero-sub" style="font-size:clamp(0.9375rem,1.8vw,1rem);color:#fff;line-height:1.72;margin:0 0 1.75rem;max-width:28rem;text-shadow:0 3px 12px rgba(0,0,0,.5), 0 1px 4px rgba(0,0,0,.4);font-weight:500;">
          <?= e($_hs['sub']) ?>
        </p>
        <?php endif; ?>
        <a href="<?= e($_slideLink) ?>" class="btn btn-primary hero-cta" style="font-size:var(--text-sm);padding:.6875rem 1.5rem;gap:.5rem;">
          <?= e($_slideBtn) ?> <i data-lucide="arrow-right" class="ic-14"></i>
        </a>
      </div>
    </div>
  </div>
  <?php endforeach; unset($_hk, $_hs, $_bgStyle, $_slideLink, $_slideBtn); ?>

  <?php if(count($_heroSlides) > 1): ?>
  <!-- Prev arrow -->
  <button @click="prev(); clearInterval(timer); timer=setInterval(()=>next(),5500)"
    aria-label="<?= isNepali()?'अघिल्लो':'Previous slide' ?>"
    style="position:absolute;left:1.25rem;top:50%;transform:translateY(-50%);z-index:20;width:2.75rem;height:2.75rem;border-radius:50%;background:rgba(255,255,255,.18);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.28);color:#fff;cursor:pointer;display:grid;place-items:center;transition:background .2s;padding:0;"
    onmouseover="this.style.background='rgba(255,255,255,.32)'" onmouseout="this.style.background='rgba(255,255,255,.18)'">
    <i data-lucide="chevron-left" style="width:20px;height:20px;pointer-events:none;"></i>
  </button>
  <!-- Next arrow -->
  <button @click="next(); clearInterval(timer); timer=setInterval(()=>next(),5500)"
    aria-label="<?= isNepali()?'अर्को':'Next slide' ?>"
    style="position:absolute;right:1.25rem;top:50%;transform:translateY(-50%);z-index:20;width:2.75rem;height:2.75rem;border-radius:50%;background:rgba(255,255,255,.18);backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.28);color:#fff;cursor:pointer;display:grid;place-items:center;transition:background .2s;padding:0;"
    onmouseover="this.style.background='rgba(255,255,255,.32)'" onmouseout="this.style.background='rgba(255,255,255,.18)'">
    <i data-lucide="chevron-right" style="width:20px;height:20px;pointer-events:none;"></i>
  </button>
  <!-- Dot indicators -->
  <div style="position:absolute;bottom:1.25rem;left:50%;transform:translateX(-50%);display:flex;align-items:center;gap:.5rem;z-index:20;">
    <?php foreach($_heroSlides as $_hk => $_): ?>
    <button @click="go(<?= $_hk ?>)"
      :style="cur==<?= $_hk ?> ? 'width:1.75rem;background:#fff;opacity:1;' : 'width:.5rem;background:rgba(255,255,255,.5);'"
      aria-label="Slide <?= $_hk+1 ?>"
      style="height:.5rem;border-radius:9999px;border:none;cursor:pointer;transition:all .35s ease;padding:0;"></button>
    <?php endforeach; unset($_hk,$_); ?>
  </div>
  <?php endif; ?>

</div>

<!-- ══════════════════════════════════════════════
  § 2 — STATS BAR
══════════════════════════════════════════════ -->
<?php
$statsBarAnimate = true;
include 'includes/stats-bar.php';
?>

<!-- ══════════════════════════════════════════════
  § 3 — CLIENT LOGO MARQUEE
══════════════════════════════════════════════ -->
<?php if($__logoClients): ?>
<section class="band-tinted" style="overflow:hidden;border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
  <style>
  @keyframes live-pulse {
    0%,100%{box-shadow:0 0 0 2px rgba(34,197,94,.25);}
    50%{box-shadow:0 0 0 4px rgba(34,197,94,.12);}
  }
  </style>

  <!-- Section header — uniform with all other sections -->
  <div class="container">
    <div class="animate-fade-up section-head">
      <div class="section-eyebrow section-eyebrow-green mb-card">
        <i data-lucide="building-2" class="ic-11"></i>
        <?= e(cms($__s,'home_trust_eyebrow') ?: (isNepali() ? 'हाम्रा साझेदार' : 'Our Partners')) ?>
      </div>
      <h2 class="section-title">
        <?= cms($__s,'home_trust_title') ?: (isNepali()
          ? 'नेपालभरका अग्रणी <span class="tg">संस्थाहरूको</span> भरोसा'
          : 'Trusted by leading <span class="tg">institutions</span> across Nepal') ?>
      </h2>
      <p class="section-lede">
        <span style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);border-radius:9999px;padding:.2rem .75rem .2rem .5rem;font-size:.75rem;font-weight:700;color:var(--success-fg);vertical-align:middle;">
          <span style="width:.45rem;height:.45rem;border-radius:9999px;background:#22c55e;box-shadow:0 0 0 2px rgba(34,197,94,.25);animation:live-pulse 2s ease-in-out infinite;flex-shrink:0;"></span>
          <span id="trust-count"><?= e(trim($__s['stat_1_value'] ?? '') ?: '120') ?></span>+&nbsp;<?= e(trim($__s['home_trust_unit']??'')?:(isNepali()?'ग्राहकहरू':'clients')) ?>
        </span>
        &nbsp;<?= e(trim($__s['home_trust_sub']??'')?:(isNepali()?'ग्राहकहरूले विश्वास गर्छन्':'clients and businesses already on board') ) ?>
      </p>
    </div>
  </div>

  <script>
  (function(){
    var el=document.getElementById('trust-count');
    if(!el||window.matchMedia('(prefers-reduced-motion:reduce)').matches)return;
    var target=parseInt(el.textContent,10)||120;
    var done=false;
    var io=new IntersectionObserver(function(e){
      if(done||!e[0].isIntersecting)return;
      done=true;io.disconnect();
      var st=Date.now(),d=900;
      (function tick(){
        var p=Math.min((Date.now()-st)/d,1);
        p=1-Math.pow(1-p,3);
        el.textContent=Math.round(target*p);
        if(p<1)requestAnimationFrame(tick);
        else el.textContent=target;
      })();
    },{threshold:0.5});
    io.observe(el);
  })();
  </script>

  <div class="marquee-wrap" style="margin-top:.5rem;padding-bottom:2.5rem;">
    <div style="display:flex;gap:1rem;align-items:center;width:max-content;animation:logo-sc 36s linear infinite;" onmouseover="this.style.animationPlayState='paused'" onmouseout="this.style.animationPlayState='running'">
      <?php for($r=0;$r<2;$r++): foreach($__logoClients as $lc): ?>
      <div style="flex-shrink:0;display:flex;align-items:center;padding:0 .375rem;">
        <?php if (!empty($lc['logo_url'])): ?>
        <img src="<?= e($lc['logo_url']) ?>" alt="<?= e($lc['org_name']) ?>" loading="lazy" decoding="async"
             class="st-marq-logo">
        <?php else: ?>
        <span class="st-marq-label">
          <i data-lucide="building-2" style="width:13px;height:13px;flex-shrink:0;color:var(--primary);opacity:.8;"></i><?= e($lc['org_name']) ?>
        </span>
        <?php endif; ?>
      </div>
      <?php endforeach; endfor; ?>
    </div>
  </div>
</section>
<?php endif; ?>
<!-- § 4 hidden — "What we build" bento grid removed -->
<?php if(false): // hidden — bento grid only ?>
<section class="band-tinted">
  <div class="container">
    <div class="animate-fade-up section-head">
      <div class="section-eyebrow section-eyebrow-primary mb-card">
        <i data-lucide="sparkles" class="ic-11"></i>
        <?= e($_bentoEyebrow ?: 'What we build') ?>
      </div>
      <h2 class="section-title">
        <?= $_bentoTitle ?: 'Everything your business needs. <span class="tg">One platform.</span>' ?>
      </h2>
      <p style="max-width:40rem;margin:0 auto;color:var(--muted-foreground);font-size:var(--text-md);line-height:1.75;">
        <?= e($_bentoSub ?: "Built from the ground up for Nepali businesses — practical, reliable and locally supported.") ?>
      </p>
    </div>

    <div id="bento" style="display:grid;grid-template-columns:1fr;gap:1.25rem;" class="stagger-children">
      <?php foreach($homeProducts as $prod):
        $prodFeats = json_decode($prod['features'] ?? '[]', true) ?: [];
        $prodHighs = json_decode($prod['highlights'] ?? '[]', true) ?: [];
        $isDark    = !empty($prod['home_card_dark']);
        $isWide    = !empty($prod['home_card_wide']);
        $iconColor = $prod['icon_color'] ?? 'blue';
        $bgCss     = $prod['home_bg_css'] ?? '';
        $textC     = $isDark ? '#f1f5f9'                : 'var(--foreground)';
        $mutedC    = $isDark ? 'rgba(241,245,249,.65)'  : 'var(--muted-foreground)';
        $chipBg    = $isDark ? 'rgba(255,255,255,.08)'  : 'rgba(37,99,235,.08)';
        $chipBord  = $isDark ? 'rgba(255,255,255,.14)'  : 'rgba(37,99,235,.15)';
        $chipCol   = $isDark ? '#93c5fd'                : 'var(--primary)';
        $cardBg    = $bgCss ?: ($isDark
          ? 'background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 100%)'
          : 'background:linear-gradient(135deg,var(--primary-light) 0%,var(--success-soft) 100%)');
        $cardStyle = $cardBg . ($isDark ? ';border-color:#1e293b' : '');
      ?>
      <div class="bc <?= $isWide ? 'bw' : '' ?>" style="<?= e($cardStyle) ?>">
        <?php if($isDark): ?>
        <div style="position:absolute;top:-2rem;right:1.5rem;width:8rem;height:8rem;border-radius:9999px;background:radial-gradient(circle,rgba(37,99,235,.35),transparent);filter:blur(16px);pointer-events:none;"></div>
        <?php endif; ?>
        <div style="position:relative;display:flex;align-items:flex-start;gap:1rem;margin-bottom:1.25rem;">
          <div class="icon-box icon-box-<?= e($iconColor) ?>" style="width:2.75rem;height:2.75rem;border-radius:.875rem;flex-shrink:0;">
            <?php if(!empty($prod['lucide_icon'])): ?>
              <i data-lucide="<?= e($prod['lucide_icon']) ?>" style="width:18px;height:18px;color:#fff;"></i>
            <?php else: ?>
              <span style="font-size:1.25rem;line-height:1;"><?= e($prod['icon'] ?? '📦') ?></span>
            <?php endif; ?>
          </div>
          <div>
            <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.375rem;flex-wrap:wrap;">
              <h3 style="font-family:var(--font-display);font-weight:800;color:<?= $textC ?>;margin:0;"><?= e($prod['name']) ?></h3>
              <?php if(!empty($prod['badge'])): ?>
              <span style="font-size:var(--text-3xs);padding:.15rem .5rem;border-radius:9999px;background:#dbeafe;color:var(--primary-dark);font-weight:700;"><?= e($prod['badge']) ?></span>
              <?php endif; ?>
            </div>
            <p style="color:<?= $mutedC ?>;font-size:var(--text-sm);line-height:1.65;margin:0;"><?= e($prod['summary'] ?? '') ?></p>
          </div>
        </div>
        <?php if($prodFeats): ?>
        <div style="position:relative;display:flex;flex-wrap:wrap;gap:.5rem;">
          <?php foreach(array_slice($prodFeats,0,10) as $chip): ?>
          <span style="display:inline-flex;align-items:center;gap:.25rem;padding:.2rem .7rem;border-radius:9999px;background:<?= $chipBg ?>;border:1px solid <?= $chipBord ?>;font-size:var(--text-xs);font-weight:600;color:<?= $chipCol ?>;">
            <i data-lucide="check" class="ic-9"></i><?= e($chip) ?>
          </span>
          <?php endforeach; ?>
        </div>
        <?php elseif($prodHighs): ?>
        <div style="position:relative;display:flex;flex-direction:column;gap:.5rem;">
          <?php foreach(array_slice($prodHighs,0,6) as $f): ?>
          <div style="display:flex;align-items:center;gap:.5rem;font-size:var(--text-xs);font-weight:600;color:<?= $textC ?>;">
            <i data-lucide="check-circle" style="width:13px;height:13px;color:<?= $isDark?'var(--success-border)':'var(--primary)' ?>;flex-shrink:0;"></i><?= e($f) ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div><!-- /bento -->

    <div class="animate-fade-up section-foot">
      <a href="<?= url('services.php') ?>" class="btn btn-outline btn-md">
        <i data-lucide="layers" class="ic-15"></i>
        See all services in detail
      </a>
    </div>
  </div>
</section>
<?php endif; // end hidden § 4 bento grid ?>

<!-- ══════════════════════════════════════════════
  § 5 — PRODUCT EXPLORER  (left sidebar list + right detail panel)
══════════════════════════════════════════════ -->
<section class="band">
  <div class="container">
    <div class="animate-fade-up section-head">
      <div class="section-eyebrow section-eyebrow-violet mb-card">
        <i data-lucide="monitor" class="ic-11"></i>
        <?= e(cms($__s,'home_products_eyebrow') ?: (isNepali() ? 'उत्पादनहरू' : 'Our Products')) ?>
      </div>
      <h2 style="font-family:var(--font-display);font-weight:800;letter-spacing:-.025em;color:var(--foreground);">
        <?= e($_inActionTitle ?: (isNepali() ? 'कार्यमा हेर्नुस' : 'See it in action')) ?>
      </h2>
      <p style="max-width:34rem;margin:.875rem auto 0;color:var(--muted-foreground);">
        <?= e($_inActionSub ?: (isNepali() ? 'तपाईंको टोलीले दैनिक प्रयोग गर्ने स्क्रिनहरू।' : 'The actual screens your team will use every day.')) ?>
      </p>
    </div>

    <?php if($homeProducts): ?>
    <!-- Two-column: left list + right panel -->
    <div id="prod-explorer" style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,2fr);gap:1.5rem;align-items:start;max-width:72rem;margin:0 auto;">

      <!-- ── LEFT: product list ── -->
      <div id="prod-list" style="border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;background:var(--card);box-shadow:0 2px 12px rgba(15,23,42,.06);">
        <?php foreach($homeProducts as $i=>$prod):
          $tSlug  = $prod['slug'];
          $tLabel = $prod['tab_label'] ?: $prod['name'];
          $tIcon  = $prod['lucide_icon'] ?: 'box';
          $tColor = $prod['icon_color'] ?? 'blue';
          $isLast = $i === count($homeProducts) - 1;
        ?>
        <button
          onclick="sTab('<?= e($tSlug) ?>')"
          data-tab="<?= e($tSlug) ?>"
          class="prod-sidebar-item <?= $i===0?'active':'' ?>"
          style="width:100%;display:flex;align-items:center;gap:.875rem;padding:1.0625rem 1.25rem;text-align:left;background:none;border:none;cursor:pointer;border-bottom:<?= $isLast?'none':'1px solid var(--border)' ?>;position:relative;transition:background .15s;">
          <!-- Active left accent bar -->
          <span class="prod-accent" style="position:absolute;left:0;top:0;bottom:0;width:3px;border-radius:0 2px 2px 0;background:var(--primary);opacity:0;transition:opacity .15s;"></span>
          <!-- Icon -->
          <div class="icon-box icon-box-<?= e($tColor) ?>" style="width:2.25rem;height:2.25rem;border-radius:.625rem;flex-shrink:0;transition:opacity .15s;">
            <i data-lucide="<?= e($tIcon) ?>" style="width:15px;height:15px;color:#fff;pointer-events:none;"></i>
          </div>
          <span style="flex:1;font-size:var(--text-sm);font-weight:600;color:var(--foreground);line-height:1.35;"><?= e($tLabel) ?></span>
          <i data-lucide="chevron-right" class="prod-chevron" style="width:15px;height:15px;color:var(--muted-foreground);flex-shrink:0;transition:transform .2s,color .15s;pointer-events:none;"></i>
        </button>
        <?php endforeach; ?>
      </div>

      <!-- ── RIGHT: detail panels ── -->
      <div id="prod-panel">
        <?php foreach($homeProducts as $i=>$prod):
          $tSlug  = $prod['slug'];
          $pFeats = json_decode($prod['features']   ?? '[]', true) ?: [];
          $tIcon  = $prod['lucide_icon'] ?: 'box';
          $tColor = $prod['icon_color'] ?? 'blue';
          $hasSS  = !empty($prod['demo_screenshot_url']);
        ?>
        <div id="tab-<?= e($tSlug) ?>" class="tab-pane <?= $i===0?'active':'' ?>">
          <?php if($hasSS): ?>
          <div style="border-radius:var(--radius-2xl);overflow:hidden;box-shadow:0 24px 80px rgba(15,23,42,.12);border:1px solid var(--border);">
            <div class="wc">
              <span class="wd dot-danger"></span><span class="wd dot-warning"></span><span class="wd dot-success"></span>
              <div class="pill-row">
                <i data-lucide="lock" class="ic-9 text-success"></i>
                <span class="mono-meta"><?= e($prod['name']) ?></span>
              </div>
            </div>
            <img src="<?= e($prod['demo_screenshot_url']) ?>" alt="<?= e($prod['name']) ?>" loading="lazy"
                 style="width:100%;display:block;max-height:32rem;object-fit:cover;object-position:top;">
          </div>
          <?php else: ?>
          <div style="border-radius:var(--radius-2xl);border:1px solid var(--border);overflow:hidden;box-shadow:0 8px 32px rgba(15,23,42,.07);">
            <div class="wc">
              <span class="wd dot-danger"></span><span class="wd dot-warning"></span><span class="wd dot-success"></span>
              <div class="pill-row">
                <i data-lucide="lock" class="ic-9 text-success"></i>
                <span class="mono-meta"><?= e($prod['name']) ?></span>
              </div>
            </div>
            <div style="background:var(--card);padding:1.75rem 2rem;">
              <div style="display:flex;align-items:center;gap:.875rem;margin-bottom:1.375rem;">
                <div class="icon-box icon-box-<?= e($tColor) ?>" style="width:3rem;height:3rem;border-radius:1rem;flex-shrink:0;">
                  <i data-lucide="<?= e($tIcon) ?>" style="width:20px;height:20px;color:#fff;"></i>
                </div>
                <div>
                  <h3 style="font-family:var(--font-display);font-weight:800;color:var(--foreground);margin:0 0 .2rem;"><?= e($prod['name']) ?></h3>
                  <?php if($prod['tagline']): ?>
                  <p style="color:var(--muted-foreground);font-size:var(--text-sm);margin:0;"><?= e($prod['tagline']) ?></p>
                  <?php endif; ?>
                </div>
              </div>
              <?php if($pFeats): ?>
              <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(13rem,1fr));gap:.5rem;margin-bottom:1.375rem;">
                <?php foreach(array_slice($pFeats,0,8) as $f): ?>
                <div style="display:flex;align-items:center;gap:.5rem;padding:.625rem .875rem;background:var(--muted);border-radius:.625rem;font-size:var(--text-sm);font-weight:600;color:var(--foreground);">
                  <i data-lucide="check-circle" style="width:13px;height:13px;color:var(--primary);flex-shrink:0;"></i><?= e($f) ?>
                </div>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
              <?php if($prod['summary']): ?>
              <p style="margin:0 0 1.375rem;color:var(--muted-foreground);font-size:var(--text-sm);line-height:1.75;"><?= e($prod['summary']) ?></p>
              <?php endif; ?>
              <a href="<?= url('contact.php?product='.urlencode($prod['name'])) ?>" class="btn btn-primary btn-md">
                <i data-lucide="calendar" class="ic-14"></i>
                <?= e(cms($__s,'home_tab_demo_cta') ?: __('cta_demo')) ?> — <?= e($prod['name']) ?>
              </a>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div><!-- /prod-panel -->

    </div><!-- /prod-explorer -->

    <?php else: ?>
    <p style="text-align:center;color:var(--muted-foreground);padding:3rem 0;">
      <?= e(isNepali() ? 'कुनै उत्पादन कन्फिगर गरिएको छैन।' : 'No products configured.') ?>
      <a href="<?= url('admin/products.php') ?>" class="text-primary"><?= e(isNepali() ? 'Admin → उत्पादनहरूबाट थप्नुस।' : 'Add products from the admin panel.') ?></a>
    </p>
    <?php endif; ?>

  </div>
</section>
<style>
/* Product explorer sidebar */
#prod-explorer { --prod-stack-bp: 700px; }
@media (max-width:700px) {
  #prod-explorer { grid-template-columns:1fr !important; }
}
.prod-sidebar-item.active { background:var(--primary-light,#eff6ff); }
.prod-sidebar-item.active .prod-accent { opacity:1 !important; }
.prod-sidebar-item.active .prod-chevron { color:var(--primary) !important; transform:translateX(2px); }
.prod-sidebar-item:hover:not(.active) { background:var(--muted); }
</style>
<script>
function sTab(slug){
  document.querySelectorAll('.prod-sidebar-item').forEach(function(b){ b.classList.toggle('active', b.dataset.tab===slug); });
  document.querySelectorAll('.tab-pane').forEach(function(p){ p.classList.toggle('active', p.id==='tab-'+slug); });
}
</script>

<!-- ══════════════════════════════════════════════
  § 6 — PROCESS  (4 steps from call to go-live)
  Unique section — not mentioned anywhere else
══════════════════════════════════════════════ -->
<section class="band-tinted">
  <div class="container">
    <div class="animate-fade-up section-head">
      <div class="section-eyebrow section-eyebrow-amber mb-card">
        <i data-lucide="map" class="ic-11"></i>
        <?= e(cms($__s,'home_process_eyebrow') ?: (isNepali() ? 'कसरी सुरु गर्ने' : 'Getting started')) ?>
      </div>
      <h2 class="section-title"><?= cms($__s,'home_process_title') ?: (isNepali() ? 'पहिलो कलदेखि लाइभसम्म — <span class="tg">४ चरण</span>' : 'From first call to go-live — <span class="tg">4 steps</span>') ?></h2>
      <p style="color:var(--muted-foreground);max-width:38rem;margin:0 auto;"><?= e(cms($__s,'home_process_sub') ?: (isNepali() ? 'डाटा माइग्रेसन, स्टाफ तालिम र ३०-दिन पोस्ट-लन्च सहयोगसहित सम्पूर्ण कार्यान्वयन हामी गर्छौं।' : 'We handle the full implementation — data migration, staff training and 30-day post-launch support.')) ?></p>
    </div>
    <style>
    #proc-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.25rem;
    }
    @media (max-width: 540px) {
      #proc-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    }
    @media (max-width: 320px) {
      #proc-grid { grid-template-columns: 1fr; }
    }
    #proc-grid .pi { position: relative; }
    .proc-con {
      display: none;
    }
    @media (min-width: 541px) {
      .proc-con {
        display: block;
        position: absolute;
        top: 2.875rem;
        left: calc(50% + 2rem);
        right: calc(-50% + 1rem);
        height: 1px;
        background: linear-gradient(90deg, rgba(37,99,235,.35), rgba(37,99,235,.1));
        pointer-events: none;
      }
    }
    </style>
    <div id="proc-grid" class="stagger-children">
      <?php foreach($processSteps as $i=>[$icon,$title,$desc]): ?>
      <div class="pi" style="text-align:center;padding:1.75rem 1.25rem;background:var(--card);border:1px solid var(--border);border-radius:var(--radius-2xl);">
        <?php if($i<3): ?><div class="proc-con"></div><?php endif; ?>
        <div style="position:relative;display:inline-flex;align-items:center;justify-content:center;width:3.5rem;height:3.5rem;border-radius:9999px;background:var(--primary-light);border:2px solid rgba(37,99,235,.2);margin-bottom:1.25rem;">
          <i data-lucide="<?= e($icon) ?>" style="width:20px;height:20px;color:var(--primary);"></i>
          <span style="position:absolute;top:-6px;right:-6px;width:1.375rem;height:1.375rem;border-radius:9999px;background:var(--primary);color:#fff;font-size:var(--text-2xs);font-weight:800;display:grid;place-items:center;font-family:var(--font-display);"><?= $i+1 ?></span>
        </div>
        <h3 style="font-family:var(--font-display);font-weight:700;color:var(--foreground);margin-bottom:.625rem;font-size:var(--text-sm);"><?= e($title) ?></h3>
        <p style="font-size:var(--text-xs);color:var(--muted-foreground);line-height:1.65;margin:0 auto;"><?= e($desc) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="animate-fade-up section-foot">
      <a href="<?= url('contact.php') ?>" class="btn btn-primary btn-md">
        <i data-lucide="calendar" class="ic-15"></i>
        <?= e(cms($__s,'home_process_cta') ?: (isNepali() ? 'परामर्श बुक गर्नुस' : 'Book a discovery call')) ?>
      </a>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════
  § 7 — TESTIMONIALS  (auto-scroll two-row marquee)
══════════════════════════════════════════════ -->
<?php if($testimonials):
  $tRow1 = array_values(array_filter($testimonials, fn($i) => true, ARRAY_FILTER_USE_KEY));
  $tRow2 = array_reverse($tRow1);
?>
<section class="band" style="overflow:hidden;">

  <div class="container section-head">
    <div class="section-eyebrow section-eyebrow-primary mb-card">
      <i data-lucide="star" class="ic-11" style="fill:currentColor;"></i>
      <?= e(__('home_testi_eyebrow')) ?>
    </div>
    <h2 class="section-title whitespace-nowrap"><?= __('home_testi_title') ?></h2>
    <p class="section-lede"><?= e(__('home_testi_sub')) ?></p>
  </div>

  <?php
  $renderCard = function(array $t, bool $dark = false) {
    $cls = $dark ? 'testi-card--dark' : 'testi-card--light';
    $rating = (int)($t['rating'] ?? 5);
    ?>
    <div class="testi-card <?= $cls ?>">
      <span class="testi-card__quote-mark" aria-hidden="true">"</span>
      <div class="testi-card__stars" aria-label="<?= $rating ?> out of 5 stars">
        <?php for ($i = 0; $i < $rating; $i++): ?>
        <svg viewBox="0 0 24 24" aria-hidden="true"><polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/></svg>
        <?php endfor; ?>
      </div>
      <p class="testi-card__text">"<?= e($t['quote']) ?>"</p>
      <div class="testi-card__author">
        <div class="testi-card__avatar"><?= strtoupper(substr($t['author_name'], 0, 1)) ?></div>
        <div>
          <div class="testi-card__name"><?= e($t['author_name']) ?></div>
          <div class="testi-card__role"><?= e(trim(($t['author_role'] ?? '') . ($t['author_org'] ? ' · ' . $t['author_org'] : ''))) ?></div>
        </div>
      </div>
    </div>
    <?php
  };
  ?>

  <!-- Row 1: scroll left -->
  <div class="marquee-wrap">
    <div class="testi-track testi-track-l" style="animation:testi-l 40s linear infinite;"
         onmouseover="this.style.animationPlayState='paused'" onmouseout="this.style.animationPlayState='running'">
      <?php for($r=0;$r<2;$r++): foreach($tRow1 as $idx=>$t): $renderCard($t, $idx===1||$idx===4); endforeach; endfor; ?>
    </div>
  </div>

</section>
<?php endif; ?>
<!-- ══════════════════════════════════════════════
  § 8 — PRICING TEASER  (3 plans)
══════════════════════════════════════════════ -->
<section class="band-tinted">
  <div class="container">
    <div class="animate-fade-up section-head">
      <div class="section-eyebrow section-eyebrow-primary mb-card">
        <i data-lucide="tag" class="ic-11"></i>
        <?= e(cms($__s,'home_pricing_eyebrow') ?: (isNepali() ? 'मूल्य निर्धारण' : 'Simple pricing')) ?>
      </div>
      <h2 class="section-title"><?= cms($__s,'home_pricing_title') ?: (isNepali() ? 'हरेक व्यवसायका लागि <span class="tg">योजना</span>' : 'Plans for <span class="tg">every business</span>') ?></h2>
      <p class="section-lede"><?= e(cms($__s,'home_pricing_sub') ?: (isNepali() ? 'कुनै लुकेको शुल्क छैन। जुनसुकै बेला अपग्रेड। हरेक योजनामा स्थानीय सहयोग।' : 'No hidden fees. Upgrade any time. Local support in every plan.')) ?></p>
    </div>
    <?php include 'includes/pricing-teaser.php'; ?>
    <div class="section-foot animate-fade-up">
      <a href="<?= url('pricing.php') ?>" class="arr">
        <?= e(__('home_pricing_link')) ?> <i data-lucide="arrow-right" class="ic-14"></i>
      </a>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════
  § 9 — NEWS / BLOG TEASER  (only if DB has records)
══════════════════════════════════════════════ -->
<?php if($newsItems): ?>
<section class="band">
  <div class="container">
    <div style="position:relative;text-align:center;margin-bottom:3rem;" class="animate-fade-up">
      <div class="section-eyebrow section-eyebrow-rose" style="margin-bottom:.75rem;display:inline-block;"><?= e(cms($__s,'home_news_eyebrow') ?: (isNepali() ? 'समाचार' : 'Latest from us')) ?></div>
      <h2 style="font-family:var(--font-display);font-weight:800;letter-spacing:-.025em;color:var(--foreground);margin:0;"><?= e(cms($__s,'home_news_title') ?: (isNepali() ? 'समाचार र अपडेट' : 'News & updates')) ?></h2>
      <a href="<?= url('news.php') ?>" class="btn btn-outline btn-sm" style="position:absolute;right:0;top:50%;transform:translateY(-50%);"><?= e(__('home_news_view_all')) ?> <i data-lucide="arrow-right" class="ic-13"></i></a>
    </div>
    <div class="news-grid stagger-children">
      <?php foreach($newsItems as $article): ?>
      <article class="st-card news-card">
        <?php if(!empty($article['cover'])): ?>
        <img src="<?= e($article['cover']) ?>" alt="<?= e($article['title']) ?>" loading="lazy" decoding="async" class="news-card__media">
        <?php else: ?>
        <div class="news-card__media--placeholder">
          <i data-lucide="newspaper" class="ic-24" style="color:rgba(255,255,255,0.35);"></i>
        </div>
        <?php endif; ?>
        <div class="news-card__body">
          <div class="news-card__meta">
            <?php if(!empty($article['category'])): ?>
            <span style="color:var(--primary);font-weight:600;"><?= e($article['category']) ?></span>
            <span>·</span>
            <?php endif; ?>
            <?php if(!empty($article['published_at'])): ?>
            <span><?= date('d M Y', strtotime($article['published_at'])) ?></span>
            <?php endif; ?>
          </div>
          <h3 class="news-card__title"><?= e($article['title']) ?></h3>
          <?php if(!empty($article['excerpt'])): ?>
          <p class="news-card__excerpt"><?= e($article['excerpt']) ?></p>
          <?php endif; ?>
          <a href="<?= url('news-post.php?slug='.urlencode($article['slug']??'')) ?>" class="news-card__link">
            <?= e(__('cta_read_more')) ?>
            <i data-lucide="arrow-right" class="ic-13"></i>
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ══════════════════════════════════════════════
  § 10 — FINAL CTA BANNER
══════════════════════════════════════════════ -->
<?php
$ctaEyebrow     = trim($__s['home_cta_eyebrow']??'') ?: (isNepali() ? 'तपाईं तयार हुँदा हामी पनि' : 'Ready when you are');
$ctaEyebrowIcon = 'rocket';
$ctaTitle       = __('cta_title');
$ctaSubtitle    = __('cta_sub');
$ctaPrimary     = ['label' => __('home_hero_book_demo'), 'url' => url('contact.php'), 'icon' => 'calendar'];
$ctaSecondary   = ['label' => __('cta_see_pricing'), 'url' => url('pricing.php'), 'icon' => 'tag'];
$ctaTrustPills  = [
  ['check',  __('trust_no_contract')],
  ['phone',  __('trust_local_support')],
  ['lock',   __('trust_data_nepal')],
  ['shield', __('trust_nrb')],
];
include 'includes/cta-banner.php';
?>
<?php include 'includes/footer.php'; ?>
