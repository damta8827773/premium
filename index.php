<?php $page_title = "Premium App - Akun Premium Murah, Resmi & Bergaransi"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-auth-compat.js"></script>
<script src="frontend/assets/js/firebase-init.js"></script>
<style>
/* ══════════════════════════════════════
   ROOT
══════════════════════════════════════ */
:root{
  --gold:#EAB308;--gold-l:#FCD34D;--gold-d:#ca8a04;
  --green:#1B3528;--green-l:#2d5a42;
  --bg:#020804;
  --cursor-x:50%;--cursor-y:50%;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{
  font-family:'Plus Jakarta Sans',sans-serif;
  background:var(--bg);
  color:#e8f0ea;
  -webkit-font-smoothing:antialiased;
  overflow-x:hidden;
  cursor:none;
}
a{text-decoration:none;color:inherit}
img{display:block}
::selection{background:rgba(234,179,8,0.3);color:#fff}

/* ══════════════════════════════════════
   SCANLINE HOLOGRAM TEXTURE
══════════════════════════════════════ */
body::before{
  content:'';position:fixed;inset:0;z-index:9000;pointer-events:none;
  background:repeating-linear-gradient(0deg,transparent,transparent 2px,rgba(0,0,0,0.03) 2px,rgba(0,0,0,0.03) 4px);
  animation:scanMove 8s linear infinite;
}
@keyframes scanMove{to{background-position:0 100px}}

/* ══════════════════════════════════════
   CURSOR SPOTLIGHT OVERLAY
══════════════════════════════════════ */
#spotlight{
  position:fixed;inset:0;z-index:8000;pointer-events:none;
  background:radial-gradient(
    650px circle at var(--cursor-x) var(--cursor-y),
    transparent 0%,
    rgba(2,8,4,0.55) 55%,
    rgba(2,8,4,0.92) 80%
  );
  transition:background 0.08s;
}

/* ══════════════════════════════════════
   CUSTOM CURSOR
══════════════════════════════════════ */
#cur{
  position:fixed;z-index:9999;pointer-events:none;
  width:12px;height:12px;border-radius:50%;
  background:var(--gold);
  box-shadow:0 0 20px 6px rgba(234,179,8,0.5),0 0 60px 12px rgba(234,179,8,0.15);
  transform:translate(-50%,-50%);
  transition:width .25s,height .25s,box-shadow .25s;
  mix-blend-mode:normal;
}
#cur.big{width:60px;height:60px;background:rgba(234,179,8,0.08);box-shadow:0 0 0 1px rgba(234,179,8,0.35),0 0 40px 4px rgba(234,179,8,0.1)}

/* ══════════════════════════════════════
   NAVBAR
══════════════════════════════════════ */
#nav{
  position:fixed;top:0;left:0;right:0;z-index:600;height:64px;
  display:flex;align-items:center;
  transition:background .4s,backdrop-filter .4s;
}
#nav.scrolled{background:rgba(2,8,4,0.85);backdrop-filter:blur(24px);border-bottom:1px solid rgba(234,179,8,0.08)}
.nav-inner{max-width:1100px;margin:0 auto;padding:0 28px;width:100%;display:flex;align-items:center;justify-content:space-between}
.logo{display:flex;align-items:center;gap:10px}
.logo-mark{
  width:34px;height:34px;border-radius:9px;
  border:1px solid rgba(234,179,8,0.4);
  display:flex;align-items:center;justify-content:center;
  background:rgba(234,179,8,0.06);
  box-shadow:0 0 16px rgba(234,179,8,0.15),inset 0 0 8px rgba(234,179,8,0.05);
}
.logo-text{font-weight:900;font-size:17px;letter-spacing:.06em;
  background:linear-gradient(135deg,#fff,rgba(255,255,255,0.6));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.nav-links{display:flex;gap:28px}
.nav-links a{font-size:13px;font-weight:500;color:rgba(255,255,255,0.45);transition:color .2s}
.nav-links a:hover{color:rgba(255,255,255,0.9)}
.nav-actions{display:flex;align-items:center;gap:10px}
.btn-nav-in{font-size:13px;font-weight:600;color:rgba(255,255,255,0.5);padding:8px 14px;transition:color .2s}
.btn-nav-in:hover{color:#fff}
.btn-nav-reg{
  font-size:13px;font-weight:800;
  background:linear-gradient(135deg,var(--gold-l),var(--gold));
  color:#000;padding:9px 22px;border-radius:10px;
  box-shadow:0 0 20px rgba(234,179,8,0.3),0 0 60px rgba(234,179,8,0.08);
  transition:all .25s;letter-spacing:.01em;
}
.btn-nav-reg:hover{box-shadow:0 0 40px rgba(234,179,8,0.6),0 0 80px rgba(234,179,8,0.15);transform:translateY(-1px)}

/* ══════════════════════════════════════
   HERO
══════════════════════════════════════ */
.hero{
  position:relative;min-height:100vh;display:flex;
  flex-direction:column;justify-content:center;
  padding-top:64px;overflow:hidden;background:var(--bg);
}
#liquid-canvas{position:absolute;inset:0;width:100%;height:100%;opacity:.7}
.hero-grid{
  position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(234,179,8,0.04) 1px,transparent 1px),
    linear-gradient(90deg,rgba(234,179,8,0.04) 1px,transparent 1px);
  background-size:60px 60px;
  mask-image:radial-gradient(ellipse 70% 70% at 50% 50%,black 30%,transparent 100%);
}
.hero-inner{
  max-width:1000px;margin:0 auto;padding:80px 28px 60px;
  position:relative;z-index:2;text-align:center;
}

/* Live badge */
.live-badge{
  display:inline-flex;align-items:center;gap:10px;
  border:1px solid rgba(234,179,8,0.2);border-radius:999px;
  padding:8px 20px;margin-bottom:36px;
  background:rgba(234,179,8,0.05);
  backdrop-filter:blur(12px);
}
.live-dot{width:8px;height:8px;border-radius:50%;background:#4ade80;
  box-shadow:0 0 0 0 rgba(74,222,128,0.4);animation:livePulse 1.8s ease-out infinite}
@keyframes livePulse{0%{box-shadow:0 0 0 0 rgba(74,222,128,0.5)}70%{box-shadow:0 0 0 10px rgba(74,222,128,0)}100%{box-shadow:0 0 0 0 rgba(74,222,128,0)}}
.live-text{font-size:11px;font-weight:700;color:rgba(255,255,255,0.55);letter-spacing:.12em}

/* Headline */
.hero-h1{
  font-size:clamp(2.8rem,6vw,5.2rem);
  font-weight:900;line-height:1.05;letter-spacing:-.03em;
  margin-bottom:20px;
}
.h1-line1{
  display:block;color:#fff;
  text-shadow:0 0 80px rgba(255,255,255,0.1);
}
.h1-line2{
  display:block;
  background:linear-gradient(90deg,var(--gold-l),var(--gold),#f97316,var(--gold-l));
  background-size:300% auto;
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
  animation:holoShift 5s linear infinite, textGlitch 7s steps(1) infinite;
}
@keyframes holoShift{to{background-position:300% center}}
@keyframes textGlitch{
  0%,88%,100%{transform:none;filter:drop-shadow(0 0 28px rgba(234,179,8,0.4))}
  89%{transform:skewX(4deg) translateX(4px);filter:drop-shadow(5px 0 rgba(255,50,100,0.6)) drop-shadow(-5px 0 rgba(0,200,255,0.5))}
  90%{transform:skewX(-3deg) translateX(-4px);filter:drop-shadow(-4px 0 rgba(255,50,100,0.5)) drop-shadow(4px 0 rgba(0,200,255,0.4))}
  91%{transform:translateX(3px);filter:drop-shadow(3px 0 rgba(0,200,255,0.5))}
  92%{transform:none;filter:drop-shadow(0 0 28px rgba(234,179,8,0.4))}
  94%{transform:skewX(2deg) translateX(-2px);filter:drop-shadow(-3px 0 rgba(255,50,100,0.4))}
  95%{transform:none;filter:drop-shadow(0 0 28px rgba(234,179,8,0.4))}
}

.hero-sub{
  font-size:clamp(1rem,1.6vw,1.1rem);
  color:rgba(255,255,255,0.38);
  max-width:520px;margin:0 auto 40px;line-height:1.8;
}
.hero-actions{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-bottom:70px}

.btn-glow{
  display:inline-flex;align-items:center;gap:8px;
  background:linear-gradient(135deg,var(--gold-l),var(--gold));
  color:#000;font-weight:800;font-size:15px;padding:15px 34px;
  border-radius:14px;border:none;cursor:none;
  box-shadow:0 0 30px rgba(234,179,8,0.4),0 0 80px rgba(234,179,8,0.1);
  transition:all .3s;position:relative;overflow:hidden;
}
.btn-glow::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,transparent 40%,rgba(255,255,255,0.3) 50%,transparent 60%);
  transform:translateX(-100%);transition:transform .5s;
}
.btn-glow:hover::before{transform:translateX(100%)}
.btn-glow:hover{
  box-shadow:0 0 60px rgba(234,179,8,0.7),0 0 120px rgba(234,179,8,0.2);
  transform:translateY(-2px);
}
.btn-ghost{
  display:inline-flex;align-items:center;gap:8px;
  background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.65);
  font-weight:600;font-size:15px;padding:15px 34px;border-radius:14px;
  border:1px solid rgba(255,255,255,0.1);cursor:none;
  backdrop-filter:blur(12px);transition:all .3s;
}
.btn-ghost:hover{background:rgba(255,255,255,0.08);border-color:rgba(255,255,255,0.2);color:#fff}

/* Stat bar */
.stat-bar{
  display:flex;justify-content:center;max-width:560px;margin:0 auto;
  border:1px solid rgba(234,179,8,0.12);border-radius:20px;
  background:rgba(234,179,8,0.03);backdrop-filter:blur(20px);overflow:hidden;
}
.stat{flex:1;padding:22px 12px;text-align:center;border-right:1px solid rgba(234,179,8,0.08)}
.stat:last-child{border-right:none}
.stat-n{
  font-size:1.8rem;font-weight:900;
  background:linear-gradient(135deg,#fff,rgba(234,179,8,0.8));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
  font-variant-numeric:tabular-nums;
}
.stat-l{font-size:10px;color:rgba(255,255,255,0.3);margin-top:3px;font-weight:600;letter-spacing:.08em;text-transform:uppercase}

/* Wave */
.hero-wave{display:block;width:100%;position:relative;z-index:2;margin-top:60px}

/* ══════════════════════════════════════
   PRICING SECTION
══════════════════════════════════════ */
#harga{background:#fff;padding:90px 28px}
.section-inner{max-width:1100px;margin:0 auto}
.sec-eyebrow{font-size:11px;font-weight:800;letter-spacing:.14em;text-transform:uppercase;color:var(--green);margin-bottom:8px}
.sec-title{font-size:clamp(1.9rem,3vw,2.7rem);font-weight:900;color:#0a1410;margin-bottom:10px;letter-spacing:-.025em;line-height:1.15}
.sec-sub{font-size:15px;color:#6b7280;line-height:1.75}

/* Billing toggle */
.bill-wrap{display:inline-flex;background:#f3f4f6;border-radius:12px;padding:4px;margin-bottom:44px}
.bill-btn{padding:9px 22px;border-radius:9px;font-size:14px;font-weight:600;border:none;background:transparent;color:#6b7280;cursor:none;transition:all .2s}
.bill-btn.on{background:#fff;color:#111;box-shadow:0 2px 12px rgba(0,0,0,0.1)}
.save-tag{background:var(--green);color:#fff;font-size:9px;font-weight:800;padding:2px 7px;border-radius:5px;margin-left:5px;vertical-align:middle}

/* Grid */
.pgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:18px}

/* ── HOLOGRAPHIC CARD ── */
.pcard{
  border-radius:22px;padding:26px;
  background:#0c130e;
  border:1px solid rgba(255,255,255,0.08);
  position:relative;display:flex;flex-direction:column;
  overflow:hidden;
  transform-style:preserve-3d;
  transition:transform .15s,box-shadow .15s;
  cursor:none;
  --hx:.5;--hy:.5;--hdeg:0deg;--hop:0;
}
/* Holographic iridescent overlay */
.pcard::before{
  content:'';position:absolute;inset:0;border-radius:inherit;
  background:
    linear-gradient(
      calc(var(--hdeg) + 0deg),
      rgba(255,0,128,0.25) 0%,
      rgba(255,140,0,0.2) 16%,
      rgba(255,255,0,0.15) 33%,
      rgba(0,255,150,0.2) 50%,
      rgba(0,150,255,0.2) 67%,
      rgba(150,0,255,0.25) 83%,
      rgba(255,0,128,0.25) 100%
    );
  mix-blend-mode:screen;
  opacity:var(--hop);
  transition:opacity .4s;
  pointer-events:none;
}
/* Shine spot */
.pcard::after{
  content:'';position:absolute;inset:0;border-radius:inherit;
  background:radial-gradient(
    circle at calc(var(--hx)*100%) calc(var(--hy)*100%),
    rgba(255,255,255,0.14) 0%,
    rgba(255,255,255,0.04) 35%,
    transparent 65%
  );
  pointer-events:none;
}
/* Scanline on card */
.pcard-scan{
  position:absolute;inset:0;border-radius:inherit;
  background:repeating-linear-gradient(0deg,transparent,transparent 3px,rgba(0,255,100,0.018) 3px,rgba(0,255,100,0.018) 4px);
  pointer-events:none;z-index:1;
}
.pcard:hover{
  box-shadow:0 30px 80px rgba(0,0,0,0.5),0 0 40px rgba(234,179,8,0.08);
  transform:perspective(800px) rotateX(var(--trx,0deg)) rotateY(var(--try,0deg)) scale(1.02);
}
.pcard.popular{border-color:rgba(234,179,8,0.35)}
.pop-badge{
  position:absolute;top:-1px;left:50%;transform:translateX(-50%);
  background:linear-gradient(135deg,var(--gold-l),var(--gold));color:#000;
  font-size:10px;font-weight:800;padding:5px 16px;border-radius:0 0 10px 10px;
  white-space:nowrap;z-index:3;letter-spacing:.04em;
}
.pcard>*:not(.pcard-scan){position:relative;z-index:2}

.clogo{width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;margin-bottom:14px;transition:transform .3s,box-shadow .3s}
.pcard:hover .clogo{transform:translateZ(8px) scale(1.06);box-shadow:0 8px 24px rgba(0,0,0,0.3)}
.clogo img{width:30px;height:30px;object-fit:contain}
.cname{font-size:15px;font-weight:800;color:#f0f7f1;margin-bottom:3px}
.ctype{font-size:11px;color:rgba(255,255,255,0.3);font-weight:500;margin-bottom:16px}
.cprice-amount{font-size:1.8rem;font-weight:900;color:#fff;transition:all .3s}
.cprice-per{font-size:12px;color:rgba(255,255,255,0.3)}
.cprice-note{font-size:11px;color:rgba(255,255,255,0.25);margin-bottom:14px}
.cdivider{height:1px;background:rgba(255,255,255,0.07);margin:12px 0}
.cfeat{list-style:none;display:flex;flex-direction:column;gap:7px;margin-bottom:18px;flex:1}
.cfeat li{font-size:12px;color:rgba(255,255,255,0.45);display:flex;align-items:flex-start;gap:7px}
.cfeat li::before{content:'✦';color:var(--gold);font-size:9px;margin-top:2px;flex-shrink:0;opacity:.8}
.cbuy{
  display:block;text-align:center;width:100%;padding:11px;border-radius:11px;
  font-size:13px;font-weight:700;letter-spacing:.02em;
  background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.8);
  border:1px solid rgba(255,255,255,0.1);cursor:none;
  transition:all .25s;
}
.cbuy:hover{background:rgba(234,179,8,0.15);border-color:rgba(234,179,8,0.4);color:var(--gold);box-shadow:0 0 20px rgba(234,179,8,0.1)}
.pcard.popular .cbuy{background:linear-gradient(135deg,var(--gold-l),var(--gold));color:#000;border-color:transparent}
.pcard.popular .cbuy:hover{box-shadow:0 0 40px rgba(234,179,8,0.4)}

/* ══════════════════════════════════════
   BENEFITS - dark glass cards
══════════════════════════════════════ */
#keunggulan{background:#050e07;padding:90px 28px;position:relative;overflow:hidden}
.green-glow{position:absolute;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(27,53,40,0.6),transparent 70%);pointer-events:none}
.bgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;position:relative;z-index:1}
.bcard{
  padding:28px;border-radius:20px;
  background:rgba(255,255,255,0.03);
  border:1px solid rgba(255,255,255,0.06);
  backdrop-filter:blur(8px);
  transition:all .3s;position:relative;overflow:hidden;
}
.bcard::before{
  content:'';position:absolute;bottom:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,var(--gold),var(--green-l),var(--gold));
  transform:scaleX(0);transform-origin:left;
  transition:transform .4s cubic-bezier(.16,1,.3,1);
}
.bcard:hover{background:rgba(255,255,255,0.05);border-color:rgba(234,179,8,0.15);transform:translateY(-3px);box-shadow:0 20px 50px rgba(0,0,0,0.3)}
.bcard:hover::before{transform:scaleX(1)}
.bicon{font-size:2rem;margin-bottom:14px;display:block;filter:drop-shadow(0 4px 12px rgba(0,0,0,0.3))}
.btitle{font-size:14px;font-weight:800;color:rgba(255,255,255,0.85);margin-bottom:7px}
.bdesc{font-size:12px;color:rgba(255,255,255,0.35);line-height:1.75}

/* ══════════════════════════════════════
   HOW IT WORKS
══════════════════════════════════════ */
#cara-kerja{background:#fff;padding:90px 28px}
.steps{display:grid;grid-template-columns:1fr 32px 1fr 32px 1fr;align-items:start;gap:0}
.step{text-align:center;padding:28px 16px}
.step-n{
  width:54px;height:54px;border-radius:16px;margin:0 auto 16px;
  background:linear-gradient(135deg,var(--green),var(--green-l));
  color:#fff;font-weight:900;font-size:19px;
  display:flex;align-items:center;justify-content:center;
  box-shadow:0 8px 28px rgba(27,53,40,0.35);
  transition:transform .3s,box-shadow .3s;
}
.step:hover .step-n{transform:scale(1.1) rotate(-3deg);box-shadow:0 14px 40px rgba(27,53,40,0.5)}
.step-t{font-size:15px;font-weight:800;color:#0a1410;margin-bottom:8px}
.step-d{font-size:13px;color:#6b7280;line-height:1.75}
.step-arr{display:flex;align-items:center;justify-content:center;padding-top:28px;color:#d1d5db}

/* ══════════════════════════════════════
   TESTIMONIALS
══════════════════════════════════════ */
#testi{background:#f9fafb;padding:80px 28px}
.tgrid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:14px}
.tcard{background:#fff;border:1.5px solid #f0f0f0;border-radius:20px;padding:24px;transition:all .3s}
.tcard:hover{border-color:#e0e0e0;transform:translateY(-3px);box-shadow:0 14px 40px rgba(0,0,0,0.06)}
.tstars{color:var(--gold);font-size:13px;letter-spacing:2px;margin-bottom:12px}
.ttext{font-size:13px;color:#374151;line-height:1.8;margin-bottom:16px;font-style:italic}
.tauthor{display:flex;align-items:center;gap:10px}
.tavatar{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;color:#fff;flex-shrink:0}
.tname{font-size:13px;font-weight:700;color:#111}
.trole{font-size:11px;color:#9ca3af}

/* ══════════════════════════════════════
   FAQ
══════════════════════════════════════ */
#faq{background:#fff;padding:80px 28px}
.flist{max-width:700px;margin:0 auto;display:flex;flex-direction:column;gap:8px}
.fitem{border:1.5px solid #f0f0f0;border-radius:16px;overflow:hidden;transition:border-color .2s,box-shadow .2s}
.fitem.open{border-color:var(--green);box-shadow:0 4px 24px rgba(27,53,40,0.08)}
.fq{padding:18px 22px;font-size:14px;font-weight:700;color:#111;cursor:none;display:flex;align-items:center;justify-content:space-between;gap:12px;transition:background .15s}
.fq:hover{background:#f9fafb}
.fq svg{flex-shrink:0;color:#9ca3af;transition:transform .3s cubic-bezier(.16,1,.3,1)}
.fitem.open .fq svg{transform:rotate(180deg)}
.fa{font-size:13px;color:#6b7280;line-height:1.8;padding:0 22px;max-height:0;overflow:hidden;transition:max-height .35s ease,padding .3s}
.fitem.open .fa{max-height:300px;padding:0 22px 18px}

/* ══════════════════════════════════════
   CTA - dark holographic
══════════════════════════════════════ */
#cta{
  background:var(--bg);padding:110px 28px;text-align:center;
  position:relative;overflow:hidden;
}
.cta-bg-glow{position:absolute;width:800px;height:800px;border-radius:50%;border:1px solid rgba(234,179,8,0.06);top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none;animation:ringPulse 4s ease-in-out infinite}
.cta-bg-glow2{position:absolute;width:500px;height:500px;border-radius:50%;border:1px solid rgba(234,179,8,0.1);top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none;animation:ringPulse 4s ease-in-out infinite .8s}
.cta-bg-glow3{position:absolute;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,rgba(234,179,8,0.08),transparent);top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none}
@keyframes ringPulse{0%,100%{opacity:.3;transform:translate(-50%,-50%) scale(1)}50%{opacity:.8;transform:translate(-50%,-50%) scale(1.04)}}
.cta-inner{max-width:600px;margin:0 auto;position:relative;z-index:2}
.cta-t{font-size:clamp(2rem,3.5vw,3rem);font-weight:900;color:#fff;margin-bottom:14px;letter-spacing:-.025em;line-height:1.1}
.cta-t em{font-style:normal;
  background:linear-gradient(90deg,var(--gold-l),var(--gold));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
  filter:drop-shadow(0 0 20px rgba(234,179,8,0.4));
}
.cta-s{font-size:15px;color:rgba(255,255,255,0.38);margin-bottom:36px;line-height:1.75}
.cta-acts{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}

/* ══════════════════════════════════════
   FOOTER
══════════════════════════════════════ */
footer{background:#020804;padding:48px 28px 28px;border-top:1px solid rgba(234,179,8,0.06)}
.footer-inner{max-width:1100px;margin:0 auto}
.footer-top{display:flex;justify-content:space-between;align-items:flex-start;gap:32px;flex-wrap:wrap;margin-bottom:36px}
.flogo-t{font-weight:900;font-size:17px;letter-spacing:.06em;color:#fff}
.fbrand-p{font-size:13px;color:rgba(255,255,255,0.22);margin-top:8px;max-width:210px;line-height:1.65}
.flinks h4{font-size:10px;font-weight:800;color:rgba(255,255,255,0.25);letter-spacing:.12em;text-transform:uppercase;margin-bottom:14px}
.flinks ul{list-style:none;display:flex;flex-direction:column;gap:10px}
.flinks ul a{font-size:13px;color:rgba(255,255,255,0.28);transition:color .2s}
.flinks ul a:hover{color:rgba(255,255,255,0.7)}
.footer-bottom{border-top:1px solid rgba(255,255,255,0.04);padding-top:24px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px}
.footer-bottom p{font-size:12px;color:rgba(255,255,255,0.14)}
.wa-pill{display:inline-flex;align-items:center;gap:7px;background:rgba(74,222,128,0.08);border:1px solid rgba(74,222,128,0.2);color:#4ade80;font-size:12px;font-weight:700;padding:8px 16px;border-radius:999px;transition:all .2s;letter-spacing:.02em}
.wa-pill:hover{background:rgba(74,222,128,0.15);border-color:rgba(74,222,128,0.4)}

/* ══════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════ */
@media(max-width:900px){.steps{grid-template-columns:1fr}.step-arr{display:none}}
@media(max-width:680px){.nav-links{display:none}.stat-bar{flex-direction:column;max-width:260px}.stat{border-right:none;border-bottom:1px solid rgba(234,179,8,0.07)}.stat:last-child{border-bottom:none}.footer-top{flex-direction:column}#cur{display:none}body{cursor:auto}#spotlight{display:none}}
</style>
</head>
<body>

<div id="spotlight"></div>
<div id="cur"></div>

<!-- ═════════════ NAVBAR ═════════════ -->
<nav id="nav">
  <div class="nav-inner">
    <a href="index.php" class="logo">
      <div class="logo-mark"><span style="color:var(--gold);font-weight:900;font-size:14px">P</span></div>
      <span class="logo-text">PREMIUM</span>
    </a>
    <div class="nav-links">
      <a href="#harga">Harga</a><a href="#keunggulan">Keunggulan</a>
      <a href="#cara-kerja">Cara Kerja</a><a href="#faq">FAQ</a>
    </div>
    <div class="nav-actions">
      <a href="login.php" class="btn-nav-in">Masuk</a>
      <a href="register.php" class="btn-nav-reg">Daftar Gratis</a>
    </div>
  </div>
</nav>

<!-- ═════════════ HERO ═════════════ -->
<section class="hero">
  <canvas id="liquid-canvas"></canvas>
  <div class="hero-grid"></div>
  <div class="hero-inner">
    <div class="live-badge">
      <span class="live-dot"></span>
      <span class="live-text">AKUN PREMIUM MULAI RP 3.000/BULAN</span>
    </div>
    <div class="hero-h1">
      <span class="h1-line1">Aplikasi Premium</span>
      <span class="h1-line2">Harga Nggak Bikin Jebol</span>
    </div>
    <p class="hero-sub">Canva Pro, Netflix, Spotify, YouTube Premium, CapCut - semua bergaransi resmi, harga jauh lebih hemat dari langganan langsung.</p>
    <div class="hero-actions">
      <a href="register.php" class="btn-glow">Mulai Belanja <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg></a>
      <a href="#harga" class="btn-ghost">Lihat Harga</a>
    </div>
    <div class="stat-bar">
      <div class="stat"><div class="stat-n" data-target="500" data-suffix="+">0</div><div class="stat-l">Pelanggan Aktif</div></div>
      <div class="stat"><div class="stat-n" data-target="6" data-suffix="+">0</div><div class="stat-l">Produk Premium</div></div>
      <div class="stat"><div class="stat-n" data-target="99" data-suffix="%">0</div><div class="stat-l">Kepuasan</div></div>
      <div class="stat"><div class="stat-n">24/7</div><div class="stat-l">Support</div></div>
    </div>
  </div>
  <svg class="hero-wave" viewBox="0 0 1440 80" fill="none" preserveAspectRatio="none" style="height:80px">
    <path d="M0 80V35C240 8 480 65 720 42C900 24 1080 60 1260 44C1360 35 1420 38 1440 36V80H0Z" fill="#ffffff"/>
  </svg>
</section>

<!-- ═════════════ PRICING ═════════════ -->
<section id="harga" style="background:#fff;padding:90px 28px">
  <div class="section-inner">
    <div style="text-align:center;margin-bottom:14px">
      <p class="sec-eyebrow">Daftar Harga</p>
      <h2 class="sec-title">Pilih Produk, Pilih Durasi</h2>
      <p class="sec-sub" style="margin:0 auto 28px;max-width:480px">Harga transparan, tidak ada biaya tersembunyi.</p>
      <div class="bill-wrap">
        <button class="bill-btn on" id="bb" onclick="setBill('b')">Per Bulan</button>
        <button class="bill-btn" id="bt" onclick="setBill('t')">Per Tahun <span class="save-tag">HEMAT 17%</span></button>
      </div>
    </div>
    <?php
    $P=[
      ['Canva Pro','Design & Kreatif','image/Canva_logo.png',5000,false,['Semua template premium','Remove background instan','Brand Kit lengkap','100GB cloud storage','Magic Resize unlimited']],
      ['YouTube Premium','Streaming Video','image/YouTube_logo.png',3000,false,['Nonton tanpa iklan','Download video offline','YouTube Music gratis','Background play aktif','Akses Originals']],
      ['Spotify Premium','Streaming Musik','image/Spotify_logo.png',3000,false,['Dengarkan tanpa iklan','Download lagu offline','Kualitas audio tinggi','Skip unlimited','Semua perangkat']],
      ['AlightMotion Pro','Video & Motion Edit','image/AlightMotion_logo.png',5000,true,['Efek motion graphics','Ekspor tanpa watermark','Semua layer premium','Resolusi tinggi','Update otomatis']],
      ['Netflix','Streaming & Film','image/Netflix_logo.png',10000,false,['Ribuan film & serial','Netflix Original eksklusif','Download offline','Full HD / 4K','Multi-perangkat']],
      ['CapCut Pro','Video Editing','image/CapCut_logo.png',5000,false,['Template premium','Filter eksklusif','Ekspor tanpa watermark','Auto-caption AI','Resolusi 4K']],
    ];?>
    <div class="pgrid">
    <?php foreach($P as $i=>[$n,$t,$l,$m,$pop,$ft]):
      $a=$m*10;$f=fn($v)=>'Rp '.number_format($v,0,',','.');?>
      <div class="pcard <?=$pop?'popular':''?>">
        <div class="pcard-scan"></div>
        <?php if($pop):?><div class="pop-badge">⭐ TERLARIS</div><?php endif?>
        <div class="clogo">
          <img src="<?=$l?>" alt="<?=$n?>" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
          <span style="display:none;font-weight:900;color:var(--gold);font-size:1.1rem"><?=substr($n,0,1)?></span>
        </div>
        <div class="cname"><?=$n?></div>
        <div class="ctype"><?=$t?></div>
        <div>
          <span class="cprice-amount" data-m="<?=$m?>" data-a="<?=$a?>"><?=$f($m)?></span>
          <span class="cprice-per">/bulan</span>
        </div>
        <div class="cprice-note" data-ml="Tagih per bulan" data-al="Tagih <?=$f($a)?>/tahun">Tagih per bulan</div>
        <div class="cdivider"></div>
        <ul class="cfeat"><?php foreach($ft as $x):?><li><?=$x?></li><?php endforeach?></ul>
        <a href="register.php" class="cbuy">Beli Sekarang</a>
      </div>
    <?php endforeach?>
    </div>
    <p style="text-align:center;margin-top:26px;font-size:13px;color:#9ca3af">🔒 Pembayaran aman · Garansi uang kembali · Akun langsung aktif</p>
  </div>
</section>

<!-- ═════════════ KEUNGGULAN ═════════════ -->
<section id="keunggulan">
  <div class="green-glow" style="top:-200px;right:-150px"></div>
  <div class="green-glow" style="bottom:-200px;left:-150px;animation-delay:-5s"></div>
  <div class="section-inner">
    <div style="text-align:center;margin-bottom:48px">
      <p class="sec-eyebrow" style="color:rgba(234,179,8,0.7)">Kenapa Kami</p>
      <h2 class="sec-title" style="color:#fff">Aman, Terpercaya, Terjangkau</h2>
    </div>
    <div class="bgrid">
      <?php foreach([
        ['🔐','Garansi Penuh','Setiap akun bergaransi resmi. Bermasalah? Kami ganti tanpa pertanyaan tambahan.'],
        ['⚡','Proses Instan','Akun tersedia detik itu juga setelah bayar. Tidak ada antrian, tidak ada menunggu.'],
        ['💳','Banyak Metode Bayar','Transfer bank, QRIS, dan metode lokal. Mulai Rp 3.000 saja.'],
        ['🛟','Support 24 Jam','Admin aktif via WhatsApp setiap saat. Respon cepat, solusi tepat.'],
        ['👥','Sistem Sharing Aman','Tidak melanggar TOS platform. Privasi dan keamanan kamu terjaga.'],
        ['📱','Akses Multi-Device','Gunakan di HP, laptop, tablet sesuai kebijakan masing-masing platform.'],
      ] as [$ic,$bt,$bd]):?>
      <div class="bcard">
        <span class="bicon"><?=$ic?></span>
        <div class="btitle"><?=$bt?></div>
        <div class="bdesc"><?=$bd?></div>
      </div>
      <?php endforeach?>
    </div>
  </div>
</section>

<!-- ═════════════ CARA KERJA ═════════════ -->
<section id="cara-kerja" style="background:#fff;padding:90px 28px">
  <div class="section-inner">
    <div style="text-align:center;margin-bottom:52px">
      <p class="sec-eyebrow">Proses Pembelian</p>
      <h2 class="sec-title">3 Langkah, Akun Langsung Aktif</h2>
    </div>
    <div class="steps">
      <?php foreach([
        ['01','Daftar Akun','Buat akun gratis pakai email atau login Google. Cuma 30 detik.','M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ['02','Deposit Saldo','Isi saldo via transfer bank atau QRIS. Bebas mulai Rp 10.000.','M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['03','Beli & Nikmati','Pilih produk, klik beli. Kredensial akun langsung muncul di dashboard.','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
      ] as $si=>[$sn,$st,$sd,$sp]):?>
      <div class="step">
        <div class="step-n"><?=$sn?></div>
        <svg style="width:28px;height:28px;color:var(--green);margin:0 auto 12px;display:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="<?=$sp?>"/></svg>
        <div class="step-t"><?=$st?></div>
        <div class="step-d"><?=$sd?></div>
      </div>
      <?php if($si<2):?><div class="step-arr"><svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></div><?php endif;endforeach?>
    </div>
  </div>
</section>

<!-- ═════════════ TESTIMONI ═════════════ -->
<section id="testi">
  <div class="section-inner">
    <div style="text-align:center;margin-bottom:44px">
      <p class="sec-eyebrow">Testimoni</p>
      <h2 class="sec-title">Kata Mereka yang Sudah Beli</h2>
    </div>
    <div class="tgrid">
      <?php foreach([
        ['R','#6366f1','Rizky A.','Content Creator','⭐⭐⭐⭐⭐','"8 bulan langganan Canva Pro di sini. Harganya jauh lebih murah dari resmi, admin super responsif. Recommended!"'],
        ['S','#0ea5e9','Sarah M.','Mahasiswi','⭐⭐⭐⭐⭐','"Coba beli Spotify dan YouTube sekaligus. Prosesnya cepat, akun langsung bisa dipakai. Mau repeat order terus!"'],
        ['D','#f59e0b','Dimas P.','Freelancer','⭐⭐⭐⭐⭐','"Netflix 10k sebulan doang, gila murah! Admin fast response, garansi beneran diproses kalau ada kendala."'],
        ['A','#ec4899','Andini R.','Graphic Designer','⭐⭐⭐⭐⭐','"Canva Pro dan CapCut Pro sekaligus. Hemat banget. Udah rekomendasiin ke semua teman-teman designerku!"'],
        ['F','#10b981','Fajar K.','Pelajar','⭐⭐⭐⭐⭐','"AlightMotion Pro 5k sebulan, solusi banget buat anak kos budget tipis. Kualitas sama persis kayak aslinya!"'],
        ['N','#8b5cf6','Nadia L.','Social Media Manager','⭐⭐⭐⭐⭐','"Paling amanah dari semua seller yang pernah saya coba. Tepat waktu, garansi berlaku, admin 24 jam."'],
      ] as [$av,$ac,$an,$ar,$as,$at]):?>
      <div class="tcard">
        <div class="tstars"><?=$as?></div>
        <p class="ttext"><?=$at?></p>
        <div class="tauthor">
          <div class="tavatar" style="background:<?=$ac?>"><?=$av?></div>
          <div><div class="tname"><?=$an?></div><div class="trole"><?=$ar?></div></div>
        </div>
      </div>
      <?php endforeach?>
    </div>
  </div>
</section>

<!-- ═════════════ FAQ ═════════════ -->
<section id="faq">
  <div class="section-inner">
    <div style="text-align:center;margin-bottom:44px">
      <p class="sec-eyebrow">FAQ</p>
      <h2 class="sec-title">Pertanyaan yang Sering Ditanyakan</h2>
    </div>
    <div class="flist">
      <?php foreach([
        ['Apakah akun ini aman?','Ya, aman. Semua akun menggunakan sistem sharing yang tidak melanggar TOS platform. Data pribadi kamu tidak akan diekspos.'],
        ['Apa bedanya dengan langganan langsung?','Harga kami jauh lebih hemat karena menggunakan sistem family/group sharing yang resmi. Akses yang kamu dapat persis sama.'],
        ['Berapa lama setelah pembayaran?','Instan! Begitu bayar dan beli produk, kredensial akun langsung muncul di dashboard. Tidak perlu konfirmasi manual.'],
        ['Bagaimana sistem garansinya?','Setiap pembelian dilindungi garansi penuh. Akun bermasalah dalam masa garansi akan kami ganti tanpa biaya tambahan.'],
        ['Metode pembayaran apa saja?','Transfer bank (BCA, Mandiri, BNI, BRI), QRIS dari semua e-wallet dan m-banking, serta metode Midtrans lainnya.'],
        ['Bisa dipakai di berapa perangkat?','Bergantung kebijakan masing-masing platform, biasanya 1-2 perangkat aktif bersamaan. Detail tertera di halaman produk.'],
      ] as $fi=>[$fq,$fa]):?>
      <div class="fitem" id="fi<?=$fi?>">
        <div class="fq" onclick="tFaq(<?=$fi?>)"><span><?=$fq?></span><svg width="17" height="17" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
        <div class="fa"><?=$fa?></div>
      </div>
      <?php endforeach?>
    </div>
  </div>
</section>

<!-- ═════════════ CTA ═════════════ -->
<section id="cta">
  <div class="cta-bg-glow"></div><div class="cta-bg-glow2"></div><div class="cta-bg-glow3"></div>
  <div class="cta-inner">
    <h2 class="cta-t">Hemat lebih banyak<br><em>mulai hari ini?</em></h2>
    <p class="cta-s">Daftar gratis, tidak perlu kartu kredit. Deposit Rp 10.000 dan langsung bisa beli akun premium favoritmu.</p>
    <div class="cta-acts">
      <a href="register.php" class="btn-glow">Buat Akun Gratis</a>
      <a href="login.php" class="btn-ghost">Sudah punya akun →</a>
    </div>
  </div>
</section>

<!-- ═════════════ FOOTER ═════════════ -->
<footer>
  <div class="footer-inner">
    <div class="footer-top">
      <div class="footer-brand">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
          <div style="width:30px;height:30px;background:rgba(234,179,8,0.08);border:1px solid rgba(234,179,8,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center"><span style="color:var(--gold);font-weight:900;font-size:12px">P</span></div>
          <span class="flogo-t">PREMIUM</span>
        </div>
        <p class="fbrand-p">Penyedia akun premium terpercaya dengan harga terbaik dan garansi resmi.</p>
      </div>
      <div class="flinks"><h4>Produk</h4><ul><li><a href="#harga">Canva Pro</a></li><li><a href="#harga">Netflix</a></li><li><a href="#harga">Spotify</a></li><li><a href="#harga">YouTube Premium</a></li><li><a href="#harga">CapCut Pro</a></li></ul></div>
      <div class="flinks"><h4>Navigasi</h4><ul><li><a href="register.php">Daftar</a></li><li><a href="login.php">Masuk</a></li><li><a href="#cara-kerja">Cara Kerja</a></li><li><a href="#faq">FAQ</a></li></ul></div>
      <div class="flinks">
        <h4>Kontak</h4>
        <ul><li><a href="https://wa.me/62XXXXXXXXXXX" target="_blank">WhatsApp Admin 1</a></li><li><a href="https://wa.me/62XXXXXXXXXXX" target="_blank">WhatsApp Admin 2</a></li></ul>
        <a href="https://wa.me/62XXXXXXXXXXX" target="_blank" class="wa-pill" style="margin-top:14px">
          <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.787"/></svg>
          Chat Admin
        </a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?=date('Y')?> Premium App. All rights reserved.</p>
      <p>Dibuat untuk menghemat pengeluaran kamu.</p>
    </div>
  </div>
</footer>

<script>
/* ── Firebase ── */
auth.onAuthStateChanged(async u=>{
  if(!u)return;
  try{const s=await firebase.firestore().collection('users').doc(u.uid).get();
    window.location.href=(s.exists&&s.data().role==='admin')?'admin/index.php':'dashboard.php';
  }catch(e){window.location.href='dashboard.php';}
});

/* ══════════════════════════════════════
   CURSOR - glow dot with smooth lerp
══════════════════════════════════════ */
const cur=document.getElementById('cur');
const spot=document.getElementById('spotlight');
let tx=window.innerWidth/2,ty=window.innerHeight/2,cx=tx,cy=ty;
document.addEventListener('mousemove',e=>{tx=e.clientX;ty=e.clientY;},{passive:true});
(function loop(){
  cx+=(tx-cx)*0.14;cy+=(ty-cy)*0.14;
  cur.style.left=cx+'px';cur.style.top=cy+'px';
  spot.style.setProperty('--cursor-x',tx+'px');
  spot.style.setProperty('--cursor-y',ty+'px');
  /* chromatic trail on fast movement */
  const spd=Math.sqrt((tx-cx)**2+(ty-cy)**2);
  document.documentElement.style.setProperty('--cursor-x',tx+'px');
  document.documentElement.style.setProperty('--cursor-y',ty+'px');
  requestAnimationFrame(loop);
})();
document.querySelectorAll('a,button,.pcard,.bcard,.tcard,.fq').forEach(el=>{
  el.addEventListener('mouseenter',()=>cur.classList.add('big'));
  el.addEventListener('mouseleave',()=>cur.classList.remove('big'));
});

/* ══════════════════════════════════════
   LIQUID CANVAS BACKGROUND
   Metaball-style fluid in hero
══════════════════════════════════════ */
(function(){
  const canvas=document.getElementById('liquid-canvas');
  const ctx=canvas.getContext('2d');
  let W,H;
  const blobs=[
    {x:.25,y:.35,vx:.0007,vy:.0005,r:.28,hue:140},
    {x:.75,y:.25,vx:-.0006,vy:.0008,r:.22,hue:165},
    {x:.5,y:.7,vx:.0008,vy:-.0006,r:.25,hue:120},
    {x:.15,y:.75,vx:.0005,vy:.0007,r:.18,hue:150},
    {x:.85,y:.6,vx:-.0007,vy:-.0005,r:.2,hue:135},
  ];
  let t=0;
  function resize(){W=canvas.width=canvas.offsetWidth;H=canvas.height=canvas.offsetHeight}
  resize();new ResizeObserver(resize).observe(canvas);
  function draw(){
    t+=.003;
    ctx.clearRect(0,0,W,H);
    blobs.forEach((b,i)=>{
      b.x+=b.vx+Math.sin(t+i)*0.0003;
      b.y+=b.vy+Math.cos(t+i*1.3)*0.0003;
      if(b.x<0||b.x>1)b.vx*=-1;
      if(b.y<0||b.y>1)b.vy*=-1;
      const grd=ctx.createRadialGradient(b.x*W,b.y*H,0,b.x*W,b.y*H,b.r*Math.min(W,H));
      const h=b.hue+Math.sin(t+i)*15;
      grd.addColorStop(0,`hsla(${h},60%,18%,0.9)`);
      grd.addColorStop(.5,`hsla(${h},50%,10%,0.5)`);
      grd.addColorStop(1,`hsla(${h},40%,5%,0)`);
      ctx.fillStyle=grd;ctx.beginPath();ctx.ellipse(b.x*W,b.y*H,b.r*Math.min(W,H),b.r*Math.min(W,H)*0.85,t*0.1+i,0,Math.PI*2);ctx.fill();
    });
    requestAnimationFrame(draw);
  }
  draw();
})();

/* ══════════════════════════════════════
   HOLOGRAPHIC CARD EFFECT
══════════════════════════════════════ */
document.querySelectorAll('.pcard').forEach(card=>{
  card.addEventListener('mousemove',e=>{
    const r=card.getBoundingClientRect();
    const x=(e.clientX-r.left)/r.width;
    const y=(e.clientY-r.top)/r.height;
    const rotX=(y-.5)*-18;
    const rotY=(x-.5)*18;
    const hue=(x+y)*180;
    card.style.setProperty('--hx',x);
    card.style.setProperty('--hy',y);
    card.style.setProperty('--hdeg',hue+'deg');
    card.style.setProperty('--hop','0.35');
    card.style.setProperty('--trx',rotX+'deg');
    card.style.setProperty('--try',rotY+'deg');
    card.style.transform=`perspective(800px) rotateX(${rotX}deg) rotateY(${rotY}deg) scale(1.03)`;
    card.style.boxShadow=`0 30px 80px rgba(0,0,0,0.5), ${-rotY*1.5}px ${rotX*1.5}px 40px rgba(234,179,8,0.12)`;
  });
  card.addEventListener('mouseleave',()=>{
    card.style.setProperty('--hop','0');
    card.style.transform='';
    card.style.boxShadow='';
  });
});

/* ══════════════════════════════════════
   NUMBER SCRAMBLE
══════════════════════════════════════ */
const CHARS='0123456789';
function scramble(el,target,suffix=''){
  let frame=0,total=40;
  const tick=setInterval(()=>{
    frame++;
    const progress=frame/total;
    const revealed=Math.floor(progress*target.toString().length);
    el.textContent=target.toString().split('').map((c,i)=>{
      if(i<revealed)return c;
      return CHARS[Math.floor(Math.random()*10)];
    }).join('')+suffix;
    if(frame>=total){el.textContent=target+suffix;clearInterval(tick);}
  },28);
}
const io=new IntersectionObserver(entries=>{
  entries.forEach(e=>{
    if(!e.isIntersecting)return;
    const el=e.target;
    const t=+el.dataset.target;const s=el.dataset.suffix||'';
    scramble(el,t,s);
    io.unobserve(el);
  });
},{threshold:.6});
document.querySelectorAll('[data-target]').forEach(el=>io.observe(el));

/* ══════════════════════════════════════
   NAVBAR SCROLL
══════════════════════════════════════ */
const nav=document.getElementById('nav');
window.addEventListener('scroll',()=>nav.classList.toggle('scrolled',scrollY>40),{passive:true});

/* ══════════════════════════════════════
   BILLING TOGGLE
══════════════════════════════════════ */
let bill='b';
function setBill(m){
  bill=m;
  document.getElementById('bb').classList.toggle('on',m==='b');
  document.getElementById('bt').classList.toggle('on',m==='t');
  document.querySelectorAll('.cprice-amount').forEach(el=>{
    const mv=+el.dataset.m,av=+el.dataset.a;
    const v=m==='b'?mv:Math.round(av/12);
    el.style.opacity='0';el.style.transform='translateY(-10px)';
    setTimeout(()=>{
      el.textContent='Rp '+v.toLocaleString('id-ID');
      el.style.transition='opacity .3s,transform .3s';
      el.style.opacity='1';el.style.transform='';
    },160);
  });
  document.querySelectorAll('.cprice-note').forEach(el=>{
    el.textContent=m==='b'?el.dataset.ml:el.dataset.al;
  });
}

/* ══════════════════════════════════════
   FAQ ACCORDION
══════════════════════════════════════ */
function tFaq(i){
  const item=document.getElementById('fi'+i);
  const open=item.classList.contains('open');
  document.querySelectorAll('.fitem.open').forEach(el=>el.classList.remove('open'));
  if(!open)item.classList.add('open');
}

/* Smooth scroll */
document.querySelectorAll('a[href^="#"]').forEach(a=>{
  a.addEventListener('click',e=>{
    const el=document.getElementById(a.getAttribute('href').slice(1));
    if(el){e.preventDefault();el.scrollIntoView({behavior:'smooth',block:'start'});}
  });
});
</script>
</body>
</html>
