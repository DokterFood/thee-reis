<?php
// Beheerpagina voor de practor: live alle borden, commentaar geven en sturen.
// Wachtwoord staat in api.php ($ADMIN_WACHTWOORD).
?><!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Beheer — Reis door het Land van Thee</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Segoe UI',system-ui,sans-serif;background:#f4efe2;color:#2b2620;padding:1.5rem;}
  h1{color:#1e3d29;font-size:1.5rem;margin-bottom:.2rem;}
  .sub{font-size:.9rem;opacity:.75;margin-bottom:1.2rem;}
  #login{max-width:380px;margin:3rem auto;background:#fffdf7;border:1.5px solid #2d5a3d;border-radius:14px;padding:1.5rem;}
  #login input{width:100%;padding:.6rem .8rem;border:1.5px solid #2d5a3d;border-radius:10px;font-size:1rem;margin:.6rem 0;}
  #login button{width:100%;background:#2d5a3d;color:#faf4e8;border:none;border-radius:10px;padding:.6rem;font-size:1rem;font-weight:700;cursor:pointer;}
  #app{display:none;}
  .stat{margin-bottom:2.5rem;}
  .stat h2{color:#1e3d29;font-size:1.15rem;border-bottom:2px solid #d9a441;padding-bottom:.2rem;margin-bottom:.8rem;}
  .briefje{background:#fff7d6;border-radius:4px 12px 4px 12px;padding:.7rem .9rem;margin-bottom:.6rem;box-shadow:2px 2px 5px rgba(0,0,0,.1);}
  .briefje p{white-space:pre-wrap;font-size:.95rem;}
  .briefje.gekozen{outline:3px solid #d9a441;}
  .meta{font-size:.75rem;opacity:.65;margin-top:.3rem;}
  .reactie{background:#e7f4e0;border-left:4px solid #2d5a3d;padding:.4rem .6rem;margin-top:.5rem;font-size:.85rem;border-radius:0 8px 8px 0;}
  .knop{background:#2d5a3d;color:#faf4e8;border:none;border-radius:8px;padding:.35rem .7rem;font-size:.8rem;cursor:pointer;margin-top:.4rem;}
  .knop.rood{background:#c05b4d;}
  .invoer{width:100%;border:1px solid #d8cfae;border-radius:6px;padding:.3rem .4rem;font-family:inherit;font-size:.85rem;margin-top:.4rem;}
  .balk{display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;margin-bottom:1.2rem;}
  .balk button{background:#c05b4d;color:#fff;border:none;border-radius:8px;padding:.4rem .8rem;font-size:.8rem;cursor:pointer;}
  .balk .info{font-size:.8rem;opacity:.7;}
  table{width:100%;border-collapse:collapse;background:#fffdf7;font-size:.85rem;}
  th{background:#2d5a3d;color:#faf4e8;text-align:left;padding:.4rem .5rem;}
  td{border-bottom:1px solid #e8dfc9;padding:.3rem .5rem;}
  td input{width:100%;border:1px solid #d8cfae;border-radius:5px;padding:.25rem .4rem;font-family:inherit;font-size:.85rem;}
  .briefje img{max-width:100%;}
</style>
</head>
<body>
<h1>🍵 Beheer — Reis door het Land van Thee</h1>
<p class="sub">Practoraat Foodinnovation &amp; Concepts · live overzicht van alle borden</p>

<div id="login">
  <strong>Beheer inloggen</strong>
  <input type="password" id="ww" placeholder="Beheer-wachtwoord" onkeydown="if(event.key==='Enter')inloggen()">
  <button onclick="inloggen()">Inloggen</button>
  <p id="fout" style="color:#c05b4d;font-size:.85rem;margin-top:.5rem;"></p>
</div>

<div id="app">
  <div class="balk">
    <span class="info" id="status">Laden…</span>
    <button onclick="wisAlles()">🗑 Wis alles (nieuwe groep)</button>
    <a href="index.php" style="font-size:.85rem;color:#1e3d29;">← naar de studenten-app</a>
  </div>
  <div id="inhoud"></div>
</div>

<script>
const STATIONS = [
  ["vertrek","1. Vertrekpunt — wat weten we al?"],
  ["inspiratie","2. Inspiratie — waar gaan we heen?"],
  ["doelgroep","3. Doelgroep — voor wie doen we het?"],
  ["beleving","4. Beleving & kennis — hoe voelt het?"],
  ["concept","5. Het concept — wat gaan we maken?"],
  ["uitwerking","6. Uitwerking — hoe pakken we het aan?"],
  ["planning","7. Planning — de route naar de pitch"]
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
let ww = sessionStorage.getItem("thee-ww") || "";
let data = {ideeen:[],planning:[]};

function api(action, body){
  return fetch("api.php?action="+action, {
    method:"POST",
    headers:{"Content-Type":"application/json"},
    body: JSON.stringify(Object.assign({wachtwoord:ww}, body||{}))
  }).then(r => r.json());
}

function inloggen(){
  ww = document.getElementById("ww").value;
  api("load").then(d => {
    if(d.ok){
      sessionStorage.setItem("thee-ww", ww);
      document.getElementById("login").style.display="none";
      document.getElementById("app").style.display="block";
      ververs(d);
      setInterval(() => api("load").then(ververs), 5000);
    } else {
      document.getElementById("fout").textContent = "Wachtwoord onjuist.";
    }
  });
}

function ververs(d){
  if(!d || !d.ok) return;
  data = d;
  document.getElementById("status").textContent =
    "Live · " + data.ideeen.length + " ideeën · ververs automatisch elke 5 sec";
  const bak = document.getElementById("inhoud");
  bak.innerHTML = "";
  STATIONS.forEach(([id, titel]) => {
    const lijst = data.ideeen.filter(i => i.station === id);
    let html = "<div class='stat'><h2>"+titel+" ("+lijst.length+")</h2>";
    if(id === "planning") html += planTabel();
    lijst.forEach(i => {
      html += "<div class='briefje"+(i.gekozen?" gekozen":"")+"' data-id='"+i.id+"'>"
        + (i.type === "foto" ? "<p>[📷 foto] <a href='"+esc(i.url)+"' target='_blank'>bekijk</a></p><img src='"+esc(i.url)+"' style='max-width:180px;border-radius:6px;display:block;margin-top:.3rem;'>" : "<p>"+esc(i.tekst)+"</p>")
        + "<div class='meta'>"+esc(i.naam||"anoniem")+" · "+esc(i.tijd||"")+(i.gekozen?" · ⭐ groepskeuze":"")+"</div>"
        + (i.commentaar ? "<div class='reactie'>🐸 "+esc(i.commentaar)+"</div>" : "")
        + "<input class='invoer' placeholder='Commentaar voor de groep…' data-cmnt='"+i.id+"'>"
        + "<button class='knop' onclick=\"comment('"+i.id+"')\">💬 Reactie plaatsen</button> "
        + "<button class='knop rood' onclick=\"verwijder('"+i.id+"')\">🗑 Verwijderen</button>"
        + "</div>";
    });
    if(!lijst.length && id !== "planning") html += "<p style='font-size:.85rem;opacity:.6;'>Nog geen ideeën.</p>";
    html += "</div>";
    bak.insertAdjacentHTML("beforeend", html);
  });
}

function planTabel(){
  let rows = "";
  PLANNING_STAPPEN.forEach((stap, i) => {
    const r = (data.planning && data.planning[i]) || ["","",""];
    rows += "<tr><td><b>"+(stap||"&nbsp;")+"</b></td>"
      + "<td><input value='"+esc(r[0])+"' data-plan='"+i+"-0'></td>"
      + "<td><input value='"+esc(r[1])+"' data-plan='"+i+"-1'></td>"
      + "<td><input value='"+esc(r[2])+"' data-plan='"+i+"-2'></td></tr>";
  });
  return "<table><thead><tr><th>Stap</th><th>Wanneer?</th><th>Wie doet wat?</th><th>Af?</th></tr></thead>"
       + "<tbody oninput='planWijzig(event)'>"+rows+"</tbody></table>";
}

function planWijzig(e){
  const [ri, ci] = e.target.dataset.plan.split("-").map(Number);
  const rows = PLANNING_STAPPEN.map((s, i) => {
    const r = ((data.planning && data.planning[i]) || ["","",""]).slice();
    if(i === ri) r[ci] = e.target.value;
    return r;
  });
  api("planning", {rows}).then(d => { if(d.ok){ data = d; } });
}

function comment(id){
  const inp = document.querySelector("[data-cmnt='"+id+"']");
  api("comment", {id, tekst: inp.value});
  inp.value = "";
}
function verwijder(id){ if(confirm("Dit idee verwijderen?")) api("del", {id}); }
function wisAlles(){ if(confirm("Alle ideeën en de planning wissen? Start schoon met een nieuwe groep.")) api("reset"); }

function esc(s){ return String(s||"").replace(/[&<>"']/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c])); }

if(ww){ inloggen(); }
</script>
</body>
</html>
