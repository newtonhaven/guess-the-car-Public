// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
(function () {
  "use strict";

  const data = JSON.parse(document.getElementById("dbData").textContent);
  const byKey = {};
  data.cars.forEach((c) => (byKey["car:" + c.id] = { kind: "car", item: c }));
  data.concepts.forEach((c) => (byKey["concept:" + c.id] = { kind: "concept", item: c }));

  // ---------- tabs <-> URL hash ----------
  const tabFor = (hash) => {
    if (/^#(car|concept)-/.test(hash)) return "#cars";
    if (/^#mule-/.test(hash)) return "#mules";
    return ["#cars", "#mules", "#trees"].includes(hash) ? hash : null;
  };
  const showTab = (target) => {
    const btn = document.querySelector('[data-bs-target="' + target + '"]');
    if (btn) bootstrap.Tab.getOrCreateInstance(btn).show();
  };
  document.querySelectorAll(".db-tabs [data-bs-toggle]").forEach((btn) => {
    btn.addEventListener("shown.bs.tab", () => {
      history.replaceState(null, "", btn.dataset.bsTarget);
      const pane = document.querySelector(btn.dataset.bsTarget);
      GTC.loadThumbs(pane);
      if (btn.dataset.bsTarget === "#mules") buildMuleSliders();
    });
  });

  // ---------- test mules: before/after slider  ----------
  function buildMuleSliders() {
    document.querySelectorAll(".mule-compare:empty").forEach((el) => {
      el.append(GTC.compare(el.dataset.before, el.dataset.after, { pos: 50, alt: el.dataset.name, lazy: true }));
    });
  }

  // ---------- search filters ----------
  function filterGrid(inputId, gridId, emptyId, extra) {
    const input = document.getElementById(inputId);
    const run = () => {
      const q = GTC.normalize(input.value);
      let shown = 0;
      document.querySelectorAll("#" + gridId + " > .col").forEach((col) => {
        const ok = (!q || GTC.normalize(col.dataset.search).includes(q)) && (!extra || extra(col));
        col.classList.toggle("d-none", !ok);
        if (ok) shown++;
      });
      document.getElementById(emptyId).classList.toggle("d-none", shown > 0);
    };
    input.addEventListener("input", run);
    return run;
  }
  const eraSelect = document.getElementById("carEra");
  const runCars = filterGrid("carSearch", "carGrid", "carEmpty", (col) => !eraSelect.value || col.dataset.era === eraSelect.value);
  eraSelect.addEventListener("change", runCars);
  filterGrid("muleSearch", "muleGrid", "muleEmpty");

  const treeInput = document.getElementById("treeSearch");
  const treeCountry = document.getElementById("treeCountry");
  const runTrees = () => {
    const q = GTC.normalize(treeInput.value);
    const country = treeCountry ? treeCountry.value : "";
    let shown = 0;
    document.querySelectorAll("#treeGrid .tree-col").forEach((col) => {
      let hit = !q || GTC.normalize(col.querySelector(".tree-root").textContent).includes(q);
      col.querySelectorAll(".chip").forEach((chip) => {
        const match = q && GTC.normalize(chip.textContent).includes(q);
        chip.classList.toggle("match", !!match);
        if (match) hit = true;
      });
      if (country && col.dataset.country !== country) hit = false;
      col.classList.toggle("d-none", !hit);
      if (hit) shown++;
    });
    document.getElementById("treeEmpty").classList.toggle("d-none", shown > 0);
  };
  treeInput.addEventListener("input", runTrees);
  if (treeCountry) treeCountry.addEventListener("change", runTrees);

  // ---------- detail modal ----------
  const modalEl = document.getElementById("detailModal");
  const modal = new bootstrap.Modal(modalEl);
  const specFields = {
    car: [["maker", "Maker"], ["country", "Country"], ["years", "Built"], ["body", "Body"], ["layout", "Layout"], ["engine", "Engine"], ["power", "Power"], ["built", "Units"]],
    concept: [["year", "Year"], ["maker", "Maker"], ["designer", "Designer"]],
  };

  async function openDetail(key) {
    const entry = byKey[key];
    if (!entry) return;
    const item = entry.item;
    document.getElementById("detailTitle").textContent = item.name;
    const specs = document.getElementById("detailSpecs");
    specs.innerHTML = "";
    specFields[entry.kind].forEach(([field, label]) => {
      if (!item[field] || item[field] === "n/a") return;
      const dt = document.createElement("dt");
      dt.textContent = label;
      const dd = document.createElement("dd");
      dd.textContent = item[field];
      specs.append(dt, dd);
    });
    document.getElementById("detailFact").textContent = item.fact || "";
    const extract = document.getElementById("detailExtract");
    extract.textContent = "Loading from Wikipedia…";
    const img = document.getElementById("detailImage");
    img.style.backgroundImage = "";
    img.classList.remove("loaded");
    img.dataset.wiki = item.wiki;
    document.getElementById("detailWiki").href = "https://en.wikipedia.org/wiki/" + encodeURIComponent(item.wiki.replace(/ /g, "_"));
    modal.show();

    const [info, text] = await Promise.all([GTC.wiki([item.wiki], 1000), GTC.wikiExtract(item.wiki)]);
    if (img.dataset.wiki !== item.wiki) return;
    const found = info[item.wiki];
    if (found && found.thumb) {
      img.style.backgroundImage = 'url("' + found.thumb.replace(/"/g, "%22") + '")';
      img.style.backgroundSize = "contain";
      img.classList.add("loaded");
    }
    extract.textContent = text;
  }

  document.addEventListener("click", (ev) => {
    const link = ev.target.closest("[data-detail]");
    if (!link) return;
    ev.preventDefault();
    history.replaceState(null, "", link.getAttribute("href"));
    openDetail(link.dataset.detail);
  });
  modalEl.addEventListener("hidden.bs.modal", () => {
    const active = document.querySelector(".db-tabs .nav-link.active");
    if (active) history.replaceState(null, "", active.dataset.bsTarget);
  });

  // ---------- initial state & links like database.php#concept-ford-gt90 or #mule-3 ----------
  function applyHash() {
    // Read the hash first: showing a tab rewrites it (see shown.bs.tab above).
    const hash = location.hash;
    const deep = hash.match(/^#(car|concept)-(.+)$/);
    const tab = tabFor(hash);
    if (tab) showTab(tab);
    if (/^#mule-/.test(hash)) {
      buildMuleSliders();
      setTimeout(() => {
        const el = document.getElementById(hash.slice(1));
        if (el) el.scrollIntoView({ block: "center" });
      }, 250);
    }
    if (deep) {
      setTimeout(() => {
        const el = document.getElementById(deep[1] + "-" + deep[2]);
        if (el) el.scrollIntoView({ block: "center" });
        openDetail(deep[1] + ":" + deep[2]);
      }, 250);
    }
  }
  window.addEventListener("hashchange", applyHash);
  applyHash();
  GTC.loadThumbs(document.querySelector(".tab-pane.active"));
})();
