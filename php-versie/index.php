<?php
// Studenten-app: identieke reis als de GitHub Pages-versie, maar met
// CENTRALE opslag: iedereen ziet elkaars ideeën live.
?><!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reis door het Land van Thee — Foodinnovation &amp; Concepts</title>
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍵</text></svg>">
<style>
  :root{
    --groen:#2d5a3d; --groen-donker:#1e3d29; --amber:#d9a441; --creme:#faf4e8;
    --kaart:#fffdf7; --rood:#c05b4d; --inkt:#2b2620;
  }
  *{box-sizing:border-box; margin:0; padding:0;}
  html{scroll-behavior:smooth;}
  body{font-family:'Segoe UI', system-ui, -apple-system, sans-serif; background:var(--creme); color:var(--inkt); line-height:1.6;}
  header{
    background:linear-gradient(135deg, var(--groen-donker), var(--groen));
    color:var(--creme); padding:3rem 1.5rem 2.5rem; text-align:center;
    border-bottom:6px solid var(--amber);
  }
  header h1{font-size:clamp(1.6rem, 4vw, 2.6rem); letter-spacing:.5px;}
  header .sub{margin-top:.5rem; font-size:1.05rem; opacity:.9;}
  header .vraag{
    margin:1.5rem auto 0; max-width:640px; background:rgba(255,255,255,.08);
    border:1px dashed var(--amber); border-radius:14px; padding:1rem 1.2rem;
    font-style:italic; font-size:1.1rem;
  }
  nav{
    position:sticky; top:0; z-index:10; background:var(--creme);
    border-bottom:2px solid var(--groen); padding:.5rem; overflow-x:auto;
    display:flex; gap:.4rem; justify-content:center; flex-wrap:wrap;
  }
  nav a{
    text-decoration:none; color:var(--groen-donker); font-weight:600; font-size:.85rem;
    padding:.35rem .7rem; border-radius:999px; white-space:nowrap;
  }
  nav a:hover, nav a.actief{background:var(--groen); color:var(--creme);}
  main{max-width:900px; margin:0 auto; padding:2rem 1.2rem 4rem;}
  .station{margin-bottom:3.5rem; scroll-margin-top:4.5rem;}
  .station-nummer{
    display:inline-flex; align-items:center; justify-content:center;
    width:2.4rem; height:2.4rem; border-radius:50%;
    background:var(--amber); color:var(--groen-donker);
    font-weight:800; margin-right:.6rem; font-size:1.1rem;
  }
  .station h2{display:inline; color:var(--groen-donker); font-size:1.5rem;}
  .station .hint{
    background:var(--kaart); border-left:4px solid var(--amber);
    padding:.8rem 1rem; border-radius:0 10px 10px 0; margin:1rem 0 1.2rem;
    font-size:.95rem;
  }
  .bord{display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:.9rem;}
  .briefje{
    background:#fff7d6; border-radius:4px 14px 4px 14px; padding:.8rem .9rem;
    box-shadow:2px 3px 6px rgba(0,0,0,.12); position:relative;
    animation:plons .25s ease;
  }
  .briefje:nth-child(3n){background:#e7f4e0;}
  .briefje:nth-child(3n+1){background:#ffe9dd;}
  .briefje p{font-size:.95rem; white-space:pre-wrap;}
  .briefje img{max-width:100%; border-radius:8px; margin-top:.4rem; display:block;}
  .uploaden{font-size:.85rem; opacity:.7; margin:.2rem 0 .8rem; display:none;}
  .uploaden.zeer{display:block;}
  .briefje .meta{margin-top:.5rem; font-size:.75rem; opacity:.65; display:flex; justify-content:space-between; gap:.5rem;}
  .briefje .del{background:none; border:none; cursor:pointer; font-size:1rem; opacity:.5;}
  .briefje .del:hover{opacity:1; color:var(--rood);}
  .briefje .kiestilm{cursor:pointer; border:none; background:none; font-size:.8rem; color:inherit; opacity:.6;}
  .briefje.gekozen{outline:3px solid var(--amber);}
  .briefje .kiestilm.gekozen{opacity:1; font-weight:800;}
  .briefje .reactie{background:#fff; border-left:4px solid var(--groen); padding:.4rem .6rem; margin-top:.5rem; font-size:.85rem; border-radius:0 8px 8px 0; opacity:.9;}
  .nieuw{display:flex; gap:.6rem; margin-bottom:.4rem; flex-wrap:wrap; align-items:flex-start;}
  .nieuw textarea{
    flex:1; min-width:240px; padding:.6rem .8rem; border:1.5px solid var(--groen);
    border-radius:10px; font-size:1rem; background:var(--kaart); font-family:inherit; resize:vertical;
  }
  .knoppenrij{display:flex; flex-direction:column; gap:.4rem;}
  .knop.foto{background:#5a7d9a;}
  .tip{font-size:.8rem; opacity:.7; margin:0 0 1rem;}
  .knop{
    background:var(--groen); color:var(--creme); border:none; border-radius:10px;
    padding:.6rem 1.1rem; font-size:1rem; font-weight:700; cursor:pointer; font-family:inherit;
  }
  .knop:hover{background:var(--groen-donker);}
  .knop.kikkuh{background:var(--amber); color:var(--groen-donker);}
  .kikkuh-bak{
    margin:0 0 1.2rem; padding:1rem 1.2rem; border-radius:12px;
    background:var(--kaart); border:1.5px dashed var(--amber); display:none;
  }
  .kikkuh-bak.zeer{display:block;}
  .kikkuh-bak strong{color:var(--groen-donker);}
  .kikker{font-size:1.4rem;}
  .live{
    text-align:center; margin:-1.5rem 1.2rem 1.5rem; font-size:.85rem; opacity:.75;
  }
  .live .stip{display:inline-block; width:.6rem; height:.6rem; border-radius:50%; background:#3a9; margin-right:.3rem; animation:knip 2s infinite;}
  @keyframes knip{50%{opacity:.3;}}
  .plantabel{width:100%; border-collapse:collapse; background:var(--kaart); border-radius:10px; overflow:hidden; font-size:.9rem;}
  .plantabel th{background:var(--groen); color:var(--creme); text-align:left; padding:.5rem .6rem; font-size:.85rem;}
  .plantabel td{border-bottom:1px solid #e8dfc9; padding:.35rem .6rem;}
  .plantabel td.stap{font-weight:600; color:var(--groen-donker);}
  .plantabel input{width:100%; border:1px solid #d8cfae; border-radius:6px; padding:.3rem .4rem; font-family:inherit; font-size:.9rem; background:#fffef9;}
  footer{
    text-align:center; padding:2rem 1rem 3rem; color:var(--groen-donker);
    font-size:.9rem; border-top:4px solid var(--amber);
  }
  footer .kikker{font-size:2rem;}
  @keyframes plons{from{transform:scale(.9); opacity:0;} to{transform:scale(1); opacity:1;}}
  @media print{
    nav, .nieuw, .knop, .briefje .del, .briefje .kiestilm, .live{display:none;}
    body{background:#fff;}
  }
</style>
</head>
<body>

<header>
  <h1>🍵 Reis door het Land van Thee</h1>
  <p class="sub">Practoraat Foodinnovation &amp; Concepts — brainstormreis voor het theekoncept van Invitez</p>
  <div class="vraag">
    “Hoe ontwikkelen wij als groep een verrassend theekoncept voor Invitez —
    als beleving, met kennisoverdracht, of iets dat nog niemand bedacht heeft?
    Alles kan. De groep beslist wat, wie, waar en hoe.”
  </div>
</header>

<nav id="nav"></nav>

<main id="main"></main>

<footer>
  <div class="kikker">🐸</div>
  <p><strong>Kikkûh leuk.</strong> Meedoen = ervaring, je cv, conceptueel denken én een stevige motivatiebrief naar de Hogeschool.</p>
</footer>

<script>
const STATIONS = [
  {
    id:"vertrek", nr:1, titel:"Vertrekpunt — wat weten we al?",
    hint:"Verzamel alles wat je al weet over thee en over Invitez. Nog geen mening, alleen feiten, associaties en vragen. Wat is thee eigenlijk? Wie drinkt het? Waarom?",
    prompts:[
      "Wat weet je zeker over thee?",
      "Wat vind je zelf het leukste aan thee?",
      "Wat weet je al over Invitez?",
      "Welke vraag heb je nog over thee of Invitez?",
      "Waar word jij blij van, bij thee?",
    ]
  },
  {
    id:"inspiratie", nr:2, titel:"Inspiratie — waar gaan we heen?",
    hint:"De groep beslist waar jullie voor inspiratie heen gaan. Denk aan een theewinkel, een theetuin, een Aziatische supermarkt, een museum, een koffiezaak die je kunt leren van... Of juist iets totaal anders.",
    prompts:[
      "Welke plek ruikt naar inspiratie?",
      "Waar wil jij graag een keer heen?",
      "Welke winkel of plek kan ons iets leren?",
      "Wat zou je willen proeven of ruiken?",
      "Wie zouden we kunnen interviewen?",
    ]
  },
  {
    id:"doelgroep", nr:3, titel:"Doelgroep — voor wie doen we het?",
    hint:"Wie is de theeklant van Invitez? Jong, oud, student, zakenman? Bedenk meerdere doelgroepen en schrijf op wat die groep zou willen beleven.",
    prompts:[
      "Voor wie is dit concept bedoeld?",
      "Wat wil die doelgroep echt ervaren?",
      "Wanneer zou die persoon thee drinken?",
      "Wat vindt die doelgroep saai aan thee?",
      "Welke doelgroep is nog helemaal vergeten?",
    ]
  },
  {
    id:"beleving", nr:4, titel:"Beleving & kennis — hoe voelt het?",
    hint:"Ga helemaal los. Thee als ritueel, als verhaal, als reis door landen, als proeflessen, als kunst... Hoe geef je kennisoverdracht zonder dat het een les voelt? Wat maakt het een échte beleving?",
    prompts:[
      "Wat maakt thee bijzonder als beleving?",
      "Hoe vertel je een verhaal bij een kop thee?",
      "Welke zintuigen kun je gebruiken?",
      "Wat zou je nooit durven voorstellen?",
      "Hoe maak je kennisoverdracht speels?",
      "Welk land of verhaal hoort bij welke thee?",
    ]
  },
  {
    id:"concept", nr:5, titel:"Het concept — wat gaan we maken?",
    hint:"Kies uit alle losse ideeën één richting. Geef het concept een naam, een vorm en een plek. Durf te kiezen — jullie beslissen samen.",
    prompts:[
      "Welk idee verdient het om uitgewerkt te worden?",
      "Wat is de naam van jullie concept?",
      "Waar vindt het plaats?",
      "Wat maakt het anders dan alles wat er al is?",
      "Welk liedje of motto hoort erbij?",
    ]
  },
  {
    id:"uitwerking", nr:6, titel:"Uitwerking — hoe pakken we het aan?",
    hint:"Maak het concreet: wie doet wat, wat hebben jullie nodig, wat is de planning? Dit is de route naar jullie eindpresentatie bij Invitez.",
    prompts:[
      "Wie neemt welke rol in de groep?",
      "Wat moeten we regelen of kopen?",
      "Wat is de eerste stap volgende week?",
      "Hoe testen we ons concept bij echte gasten?",
      "Wanneer presenteren we aan Invitez?",
    ]
  },
  {
    id:"planning", nr:7, titel:"Planning — de route naar de pitch",
    hint:"Vul samen de reis in: wat doen we per stap, wanneer en wie is verantwoordelijk? Iedereen ziet dezelfde planning live.",
    prompts:[
      "Welke datum staat bij welke stap?",
      "Wie is verantwoordelijk voor welke stap?",
    ]
  }
];

const PLANNING_STAPPEN = [
  "Kick-off: ieder vult thuis station 1 t/m 4 in",
  "Groepsbijeenkomst 1: clustering en ⭐-keuzes",
  "Excursie: inspiratieplek bezoeken (station 2)",
  "Groepsbijeenkomst 2: concept uitwerken (station 5)",
  "Test: mini-versie aan echte gasten laten zien",
  "Presentatie / pitch aan Invitez",
  "Reflectie: wat leerde ik (voor cv & motivatiebrief)",
  "",""
];

let data = {ideeen:[], planning:[]};
let naam = localStorage.getItem("thee-naam") || "";

function api(action, body){
  return fetch("api.php?action="+action, {
    method:"POST",
    headers:{"Content-Type":"application/json"},
    body: JSON.stringify(Object.assign({naam:naamVeld()}, body||{}))
  }).then(r => r.json());
}

const nav = document.getElementById("nav");
const main = document.getElementById("main");
const navLinks = {};

STATIONS.forEach(st => {
  const a = document.createElement("a");
  a.href = "#"+st.id;
  a.textContent = st.nr+". "+st.titel.split(" — ")[0];
  nav.appendChild(a);
  navLinks[st.id] = a;

  const sec = document.createElement("section");
  sec.className = "station"; sec.id = st.id;
  sec.innerHTML = `
    <span class="station-nummer">${st.nr}</span><h2>${st.titel}</h2>
    <p class="hint">${st.hint}</p>
    <div class="nieuw">
      <textarea rows="3" maxlength="4000" placeholder="Schrijf je idee… (Enter = plakken, Shift+Enter = nieuwe regel)" data-invoer="${st.id}"></textarea>
      <div class="knoppenrij">
        <button class="knop" data-toevoegen="${st.id}">Plak op het bord</button>
        <button class="knop kikkuh" data-kikkuh="${st.id}">🐸 Kikkûh!</button>
        <button class="knop foto" data-foto="${st.id}">📷 Foto</button>
        <input type="file" accept="image/*" hidden data-fotobestand="${st.id}">
      </div>
    </div>
    <p class="tip">💡 Na het plakken kun je <strong>meteen weer een nieuw idee</strong> in hetzelfde vak typen. Lange teksten mogen ook — en met 📷 zet je een foto op het bord (max. 2 MB, wordt automatisch verkleind).</p>
    <div class="kikkuh-bak" data-bak="${st.id}"></div>
    <div class="bord" data-bord="${st.id}"></div>
  `;
  main.appendChild(sec);

  if(st.id === "planning"){
    const t = document.createElement("div");
    t.className = "plantabel-bak";
    sec.appendChild(t);
  }
});

const liveBak = document.createElement("div");
liveBak.className = "live";
liveBak.innerHTML = "<span class='stip'></span><span id='livestatus'>Live verbonden — iedereen ziet elkaar ideeën meteen</span>";
main.prepend(liveBak);

const naamBak = document.createElement("div");
naamBak.style.cssText = "text-align:center; margin-bottom:1.5rem;";
naamBak.innerHTML = `
  <input id="naam" type="text" placeholder="Jouw naam (optioneel)" value="${naam}"
    style="padding:.55rem .8rem; border:1.5px solid var(--groen); border-radius:10px; margin-right:.5rem; font-family:inherit;">
  <button class="knop" id="exportknop">⬇️ Exporteer de reis</button>
`;
main.prepend(naamBak);

function naamVeld(){
  const n = document.getElementById("naam");
  return n ? n.value.trim() : "";
}
document.getElementById("naam").addEventListener("input", () => {
  localStorage.setItem("thee-naam", naamVeld());
});

function toon(){
  STATIONS.forEach(st => {
    const bord = document.querySelector(`[data-bord="${st.id}"]`);
    bord.innerHTML = "";
    data.ideeen.filter(b => b.station === st.id).forEach(b => {
      const d = document.createElement("div");
      d.className = "briefje" + (b.gekozen ? " gekozen" : "");
      d.innerHTML = `
        <p></p>
        <div class="meta">
          <span>${esc(b.naam||"anoniem")} · ${esc(b.tijd||"")}</span>
          <span>
            <button class="kiestilm ${b.gekozen?"gekozen":""}" title="Groepskeuze">⭐</button>
            <button class="del" title="Weg ermee">🗑️</button>
          </span>
        </div>`;
      if(b.type === "foto"){
        const img = document.createElement("img");
        img.src = b.url; img.alt = "foto op het bord"; img.loading = "lazy";
        d.querySelector("p").after(img);
      }
      if(b.commentaar){
        const r = document.createElement("div");
        r.className = "reactie";
        r.innerHTML = "🐸 <strong>Reactie practor:</strong> " + esc(b.commentaar);
        d.querySelector(".meta").after(r);
      }
      d.querySelector("p").textContent = b.tekst;
      d.querySelector(".del").onclick = () => { if(confirm("Dit idee verwijderen?")) api("del", {id:b.id}).then(ververs); };
      d.querySelector(".kiestilm").onclick = () => { api("star", {id:b.id}).then(ververs); };
      bord.appendChild(d);
    });
  });
  tekenPlan();
}

function tekenPlan(){
  const bak = document.querySelector(".plantabel-bak");
  if(!bak) return;
  let rows = "";
  PLANNING_STAPPEN.forEach((stap, i) => {
    const r = (data.planning && data.planning[i]) || ["","",""];
    rows += `<tr>
      <td class="stap">${stap || "&nbsp;"}</td>
      <td><input data-plan="${i}-0" value="${esc(r[0])}" placeholder="datum"></td>
      <td><input data-plan="${i}-1" value="${esc(r[1])}" placeholder="wie doet wat?"></td>
      <td><input data-plan="${i}-2" value="${esc(r[2])}" placeholder="af?"></td>
    </tr>`;
  });
  bak.innerHTML = `<table class="plantabel">
    <thead><tr><th>Stap</th><th>Wanneer?</th><th>Wie doet wat?</th><th>Af?</th></tr></thead>
    <tbody oninput="planWijzig(event)">${rows}</tbody>
  </table>`;
}

function planWijzig(e){
  clearTimeout(planWijzig.timer);
  planWijzig.wijzig = planWijzig.wijzig || {};
  planWijzig.wijzig[e.target.dataset.plan] = e.target.value;
  planWijzig.timer = setTimeout(() => {
    const rows = PLANNING_STAPPEN.map((s, i) => {
      const r = ((data.planning && data.planning[i]) || ["","",""]).slice();
      ["0","1","2"].forEach(c => {
        if(planWijzig.wijzig[i+"-"+c] !== undefined) r[+c] = planWijzig.wijzig[i+"-"+c];
      });
      return r;
    });
    api("planning", {rows}).then(d => { if(d.ok) data = d; });
    planWijzig.wijzig = {};
  }, 600);
}

function ververs(d){
  if(!d || !d.ok) return;
  const focus = document.activeElement;
  const aanHetTypen = focus && (focus.dataset.invoer !== undefined || focus.dataset.plan !== undefined || focus.id === "naam");
  data = d;
  if(!aanHetTypen) toon();
  const s = document.getElementById("livestatus");
  if(s) s.textContent = "Live · " + data.ideeen.length + " ideeën op het bord";
}

document.addEventListener("click", e => {
  const toe = e.target.closest("[data-toevoegen]");
  if(toe){
    const stId = toe.dataset.toevoegen;
    const invoer = document.querySelector(`[data-invoer="${stId}"]`);
    const t = invoer.value.trim();
    if(!t) return;
    api("add", {station:stId, tekst:t}).then(ververs);
    invoer.value = ""; invoer.focus();
  }
  const fk = e.target.closest("[data-foto]");
  if(fk){
    document.querySelector(`[data-fotobestand="${fk.dataset.foto}"]`).click();
  }
  const kik = e.target.closest("[data-kikkuh]");
  if(kik){
    const stId = kik.dataset.kikkuh;
    const st = STATIONS.find(s => s.id === stId);
    const bak = document.querySelector(`[data-bak="${stId}"]`);
    const p = st.prompts[Math.floor(Math.random()*st.prompts.length)];
    bak.innerHTML = `<span class="kikker">🐸</span> <strong>Kikkûh-vraag:</strong> ${p}`;
    bak.classList.add("zeer");
  }
  if(e.target.id === "exportknop"){
    exporteer();
  }
});

document.addEventListener("keydown", e => {
  if(e.key === "Enter" && e.target.dataset && e.target.dataset.invoer && !e.shiftKey){
    e.preventDefault();
    const stId = e.target.dataset.invoer;
    const t = e.target.value.trim();
    if(!t) return;
    api("add", {station:stId, tekst:t}).then(ververs);
    e.target.value = "";
  }
});

document.addEventListener("change", e => {
  if(!e.target.dataset || !e.target.dataset.fotobestand) return;
  const bestand = e.target.files[0];
  e.target.value = "";
  if(!bestand) return;
  const stId = e.target.dataset.fotobestand;
  const meld = document.querySelector(`[data-bak="${stId}"]`);
  const oudeTekst = meld.innerHTML;
  meld.innerHTML = "📷 Foto laden en verkleinen…";
  meld.classList.add("zeer");
  const img = new Image();
  const fr = new FileReader();
  fr.onload = () => {
    img.onload = () => {
      const schaal = Math.min(1, 1200 / Math.max(img.width, img.height));
      const c = document.createElement("canvas");
      c.width = Math.round(img.width * schaal);
      c.height = Math.round(img.height * schaal);
      c.getContext("2d").drawImage(img, 0, 0, c.width, c.height);
      const data = c.toDataURL("image/jpeg", 0.8);
      api("foto", {station:stId, data, tekst:""}).then(d => {
        if(d.ok){ ververs(d); meld.innerHTML = oudeTekst; }
        else { meld.innerHTML = "⚠️ Foto te groot of niet geldig (max. 2 MB)."; }
      });
    };
    img.src = fr.result;
  };
  fr.readAsDataURL(bestand);
});

function exporteer(){
  let txt = "REIS DOOR HET LAND VAN THEE — Foodinnovation & Concepts\n"
          + "Onderzoeksvraag: ontwikkelen van een theekoncept voor Invitez\n"
          + "Export: " + new Date().toLocaleString("nl-NL") + "\n\n";
  STATIONS.forEach(st => {
    txt += "— " + st.nr + ". " + st.titel + " —\n";
    const lijst = data.ideeen.filter(b => b.station === st.id);
    if(!lijst.length){ txt += "(nog niets)\n\n"; return; }
    lijst.forEach(b => {
      if(b.type === "foto"){
        txt += (b.gekozen ? "⭐ " : "• ") + "[foto: " + b.url + "]" + (b.naam ? "  ["+b.naam+"]" : "") + "\n";
        return;
      }
      txt += (b.gekozen ? "⭐ " : "• ") + b.tekst + (b.naam ? "  ["+b.naam+"]" : "") + "\n";
      if(b.commentaar) txt += "   🐸 reactie practor: " + b.commentaar + "\n";
    });
    txt += "\n";
  });
  const heeftPlan = (data.planning||[]).some(r => r && r.some(c => c && c.trim()));
  if(heeftPlan){
    txt += "— PLANNING —\n";
    PLANNING_STAPPEN.forEach((stap, i) => {
      const r = (data.planning && data.planning[i]) || [];
      if(stap || r.some(c => c)) txt += "• " + (stap||"?") + " | wanneer: " + (r[0]||"-") + " | " + (r[1]||"-") + " | af: " + (r[2]||"-") + "\n";
    });
  }
  const blob = new Blob([txt], {type:"text/plain"});
  const a = document.createElement("a");
  a.href = URL.createObjectURL(blob);
  a.download = "thee-reis-brainstorm.txt";
  a.click();
  URL.revokeObjectURL(a.href);
}

function esc(s){ return String(s||"").replace(/[&<>"']/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c])); }

api("load").then(ververs);
setInterval(() => api("load").then(ververs), 5000);

const sections = [...document.querySelectorAll(".station")];
window.addEventListener("scroll", () => {
  let huidige = sections[0];
  sections.forEach(s => { if(s.getBoundingClientRect().top < 120) huidige = s; });
  Object.values(navLinks).forEach(a => a.classList.remove("actief"));
  if(huidige && navLinks[huidige.id]) navLinks[huidige.id].classList.add("actief");
}, {passive:true});
</script>
</body>
</html>
