/* Isakram · Fødevarecentralen-sektion til hjemmesiden.
   Tilføjer "Fødevarecentralen" i menuen og en side, der viser appen i en telefonramme.
   Indsæt i index.html lige før </body>:
   <script src="fodevarecentralen-sektion.js"></script>
   Kræver at fodevarecentralen.html ligger i samme mappe som index.html. */
(function(){
  var APP_URL = 'fodevarecentralen.html?v=9';  // hæv tallet, når appen opdateres, så browsere henter den nye version

  var css = '' +
  '#page-app{max-width:1100px}' +
  '#page-app .fc-lead{font-family:"Fraunces",Georgia,serif;font-size:clamp(1.25rem,2.4vw,1.6rem);line-height:1.4;color:var(--ink);max-width:36ch}' +
  '.fc-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(300px,400px);gap:56px;align-items:start;margin-top:34px}' +
  '.fc-steps{list-style:none;margin:0 0 26px;padding:0;counter-reset:fc}' +
  '.fc-steps li{counter-increment:fc;display:grid;grid-template-columns:44px 1fr;gap:14px;padding:16px 0;border-bottom:1px solid var(--line)}' +
  '.fc-steps li:first-child{border-top:1px solid var(--line)}' +
  '.fc-steps li::before{content:counter(fc);font-family:"Fraunces",Georgia,serif;font-size:1.8rem;line-height:1;color:var(--caramel)}' +
  '.fc-steps b{display:block;font-family:"Fraunces",Georgia,serif;font-weight:500;font-size:1.2rem;margin-bottom:3px;color:var(--ink)}' +
  '#page-app .fc-steps span{font-size:.98rem;color:var(--ink-soft);line-height:1.55}' +
  '.fc-facts{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:0 0 26px}' +
  '.fc-facts div{background:var(--cream);border:1px solid var(--line);border-radius:14px;padding:16px}' +
  '.fc-facts b{display:block;font-family:"Fraunces",Georgia,serif;font-weight:400;font-size:1.15rem;margin-bottom:4px}' +
  '.fc-facts span{font-size:.86rem;color:var(--ink-soft);line-height:1.45}' +
  '#page-app .fc-note{font-size:.9rem;margin:14px 0 0}' +
  '.fc-cta{display:inline-block;text-decoration:none}' +
  '.fc-phone{position:sticky;top:24px;border:10px solid var(--ink);border-radius:46px;overflow:hidden;height:min(820px,82vh);background:var(--oat);box-shadow:0 30px 60px rgba(92,67,43,.25)}' +
  '.fc-phone iframe{display:block;width:100%;height:100%;border:0;background:var(--oat)}' +
  '@media(max-width:860px){.fc-grid{grid-template-columns:1fr;gap:30px}.fc-phone{position:static;height:80vh;max-width:430px;margin:0 auto;border-width:8px;border-radius:36px}.fc-facts{grid-template-columns:1fr}}';
  var st = document.createElement('style'); st.textContent = css; document.head.appendChild(st);

  var html = '' +
  '<span class="eyebrow">Nyt fra Isakram</span>' +
  '<h1>Fødevarecentralen</h1>' +
  '<p class="fc-lead">Lokale producenter lægger varer med kort dato ud på et kort. Du reserverer, henter og redder maden.</p>' +
  '<p>Det, ingen når at hente, laver vi til mad i køkkenet på Aagade 30. Producenterne kan også sælge deres faste sortiment gennem appen, og Isakram tager 10 % af salgsprisen.</p>' +
  '<div class="fc-grid">' +
    '<div>' +
      '<div class="fc-facts">' +
        '<div><b>Prisen følger datoen</b><span>20 % rabat ved 3 dage, 35 % ved 2 dage og halv pris sidste dag.</span></div>' +
        '<div><b>90 % til producenten</b><span>Intet oprettelsesgebyr og intet abonnement.</span></div>' +
        '<div><b>Intet bliver spild</b><span>Køkkenet får foreslået retter ud fra lager og varer på vej.</span></div>' +
      '</div>' +
      '<h2 style="margin-top:0">Prøv appen</h2>' +
      '<ol class="fc-steps">' +
        '<li><span><b>Som kunde</b>Tryk på en nål på kortet, vælg varer og reservér. Du får en afhentningskode.</span></li>' +
        '<li><span><b>Som producent</b>Søg om at komme med under Bliv producent. Når Isakram har godkendt jer, åbner fanen Din shop med ordrer, produkter og tilbud. Du kan også logge ind som en eksisterende producent nederst.</span></li>' +
        '<li><span><b>Som Isakram</b>Godkend producenter og varer, slå op i alle varer, og se køkkenets uge med forslag fra Madam Mangors kogebog.</span></li>' +
      '</ol>' +
      '<a class="ab-btn fc-cta" href="' + APP_URL + '" target="_blank" rel="noopener">Åbn appen i fuld skærm</a>' +
      '<p class="fc-note">Det er en demo. Intet gemmes, og alt starter forfra, når du genindlæser.</p>' +
    '</div>' +
    '<div class="fc-phone"><iframe src="' + APP_URL + '" title="Isakram Fødevarecentralen, interaktiv demo" loading="lazy"></iframe></div>' +
  '</div>';

  function install(){
    var menu = document.getElementById('navMenu');
    if(!menu || document.getElementById('page-app')) return;
    var art = document.createElement('article');
    art.id = 'page-app'; art.className = 'page-content wide'; art.hidden = true; art.innerHTML = html;
    var last = document.getElementById('page-suppliers') || document.getElementById('page-shop');
    if(last && last.parentNode) last.parentNode.insertBefore(art, last.nextSibling); else document.body.appendChild(art);

    var btn = document.createElement('button');
    btn.setAttribute('data-sec','app'); btn.textContent = 'Fødevarecentralen';
    menu.appendChild(btn);

    // Gør siden kendt af hjemmesidens egen sektionsmenu
    try { if (typeof PAGES !== 'undefined' && PAGES.indexOf('app') < 0) PAGES.push('app'); } catch(e){}

    // Direkte link: dinside.dk/#fodevarecentralen
    function fromHash(){ if(location.hash === '#fodevarecentralen' && typeof showSection === 'function') showSection('app'); }
    window.addEventListener('hashchange', fromHash); fromHash();
  }
  if(document.readyState === 'loading') document.addEventListener('DOMContentLoaded', install); else install();
})();
