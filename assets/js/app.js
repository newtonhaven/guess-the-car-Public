// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
// Shared helpers for every page.
(function () {
  "use strict";

  const GTC = (window.GTC = window.GTC || {});
  GTC.base = document.body.dataset.base || "";
  GTC.url = (path) => GTC.base + "/" + String(path).replace(/^\/+/, "");

  // ---------- storage that never throws (private mode, blocked storage...) ----------
  GTC.store = {
    get(key, fallback) {
      try {
        const raw = localStorage.getItem(key);
        return raw === null ? fallback : JSON.parse(raw);
      } catch (e) {
        return fallback;
      }
    },
    set(key, value) {
      try {
        localStorage.setItem(key, JSON.stringify(value));
      } catch (e) { /* ignore */ }
    },
  };

  // ---------- Guess the Car progress, kept in this browser like One Shot ----------
  // localStorage "gtc:v1" = { days: { "16": { guesses: ["Ford Focus"], status: "won", answer: "…" } } }
  const PROGRESS_KEY = "gtc:v1";
  GTC.progress = {
    all() {
      const data = GTC.store.get(PROGRESS_KEY, {});
      return data && typeof data.days === "object" && data.days ? data.days : {};
    },
    get(day) {
      const g = this.all()[day] || {};
      return {
        guesses: Array.isArray(g.guesses) ? g.guesses.map(String) : [],
        status: ["playing", "won", "lost"].includes(g.status) ? g.status : "new",
        answer: typeof g.answer === "string" ? g.answer : null,
      };
    },
    set(day, game) {
      const days = this.all();
      days[day] = game;
      GTC.store.set(PROGRESS_KEY, { days: days });
    },
    // played / won / streak (won days in a row up to today) / best streak
    stats(today) {
      const days = this.all();
      const won = new Set();
      let played = 0;
      Object.keys(days).forEach((key) => {
        const status = days[key] && days[key].status;
        if (status === "won" || status === "lost") played++;
        if (status === "won") won.add(Number(key));
      });
      let day = today;
      const todayStatus = days[today] && days[today].status;
      if (todayStatus !== "won" && todayStatus !== "lost") day--; // today not finished yet: count from yesterday
      let streak = 0;
      while (won.has(day)) {
        streak++;
        day--;
      }
      let best = 0;
      won.forEach((d) => {
        if (won.has(d - 1)) return;
        let run = 1;
        while (won.has(d + run)) run++;
        best = Math.max(best, run);
      });
      return { played: played, won: won.size, streak: streak, best: best };
    },
    // Fill in everything the server rendered as "unknown": day tiles and stat numbers.
    paint() {
      const days = this.all();
      document.querySelectorAll("[data-gtc-day]").forEach((tile) => {
        const status = (days[tile.dataset.gtcDay] || {}).status;
        tile.classList.toggle("is-won", status === "won");
        tile.classList.toggle("is-lost", status === "lost");
        const icon = tile.querySelector(".day-status");
        if (icon) icon.textContent = status === "won" ? "✅" : status === "lost" ? "❌" : "🔘";
      });
      document.querySelectorAll("[data-gtc-stats]").forEach((box) => {
        const s = this.stats(Number(box.dataset.today));
        box.querySelectorAll("[data-gtc-stat]").forEach((el) => {
          const key = el.dataset.gtcStat;
          el.textContent = key === "win" ? (s.played ? Math.round((100 * s.won) / s.played) : 0) + "%" : s[key];
        });
      });
    },
  };
  GTC.progress.paint();

  // ---------- countdown to the next daily car: <span data-countdown="secondsLeft"> ----------
  document.querySelectorAll("[data-countdown]").forEach((el) => {
    const end = Date.now() + Number(el.dataset.countdown) * 1000;
    const pad = (n) => String(n).padStart(2, "0");
    const tick = () => {
      const left = Math.max(0, Math.round((end - Date.now()) / 1000));
      el.textContent = pad(Math.floor(left / 3600)) + ":" + pad(Math.floor(left / 60) % 60) + ":" + pad(left % 60);
      if (left === 0) {
        clearInterval(timer);
        setTimeout(() => location.reload(), 1500); // the new car is out
      }
    };
    const timer = setInterval(tick, 1000);
    tick();
  });

  // ---------- back to top ----------
  const toTop = document.getElementById("toTop");
  if (toTop) {
    const onScroll = () => toTop.classList.toggle("show", window.scrollY > 400);
    window.addEventListener("scroll", onScroll, { passive: true });
    onScroll();
    toTop.addEventListener("click", () => window.scrollTo({ top: 0, behavior: "smooth" }));
  }

  // ---------- confetti (github.com/Agezao/confetti-js) ----------
  GTC.celebrate = function () {
    if (typeof window.ConfettiGenerator !== "function") return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
    const canvas = document.createElement("canvas");
    canvas.id = "confetti-" + Date.now();
    canvas.className = "confetti-canvas";
    document.body.appendChild(canvas);
    const confetti = new window.ConfettiGenerator({
      target: canvas.id,
      max: 120,
      size: 1.2,
      animate: true,
      props: ["circle", "square", "triangle"],
      colors: [[224, 112, 58], [172, 191, 128], [31, 155, 230], [253, 214, 126], [165, 104, 246]],
      clock: 25,
      rotate: true,
      width: window.innerWidth,
      height: window.innerHeight,
      start_from_edge: true,
      respawn: false,
    });
    confetti.render();
    setTimeout(() => {
      confetti.clear();
      canvas.remove();
    }, 9000);
  };

  // ---------- Wikipedia images (one batched request per 50 titles, cached for a week) ----------
  const WIKI_API = "https://en.wikipedia.org/w/api.php";
  const WIKI_TTL = 7 * 24 * 3600 * 1000;

  GTC.wiki = async function (titles, size) {
    size = size || 640;
    const result = {};
    const missing = [];
    titles.forEach((t) => {
      const cached = GTC.store.get("wiki:" + size + ":" + t, null);
      if (cached && Date.now() - cached.at < WIKI_TTL) result[t] = cached.data;
      else missing.push(t);
    });

    for (let i = 0; i < missing.length; i += 50) {
      const chunk = missing.slice(i, i + 50);
      const params = new URLSearchParams({
        action: "query",
        prop: "pageimages|info",
        inprop: "url",
        piprop: "thumbnail",
        pithumbsize: String(size),
        titles: chunk.join("|"),
        redirects: "1",
        format: "json",
        formatversion: "2",
        origin: "*",
      });
      let data;
      try {
        const res = await fetch(WIKI_API + "?" + params.toString());
        data = await res.json();
      } catch (e) {
        continue;
      }
      const q = data.query || {};
      const alias = {};
      (q.normalized || []).forEach((n) => (alias[n.from] = n.to));
      (q.redirects || []).forEach((r) => (alias[r.from] = r.to));
      const pages = {};
      (q.pages || []).forEach((p) => (pages[p.title] = p));
      chunk.forEach((t) => {
        let key = t;
        for (let hop = 0; hop < 3 && alias[key]; hop++) key = alias[key];
        const page = pages[key];
        if (!page || page.missing) return;
        const info = {
          title: page.title,
          url: page.fullurl || "https://en.wikipedia.org/wiki/" + encodeURIComponent(page.title.replace(/ /g, "_")),
          thumb: page.thumbnail ? page.thumbnail.source : "",
        };
        result[t] = info;
        GTC.store.set("wiki:" + size + ":" + t, { at: Date.now(), data: info });
      });
    }
    return result;
  };

  GTC.wikiExtract = async function (title) {
    const params = new URLSearchParams({
      action: "query",
      prop: "extracts",
      exintro: "1",
      explaintext: "1",
      exsentences: "4",
      titles: title,
      redirects: "1",
      format: "json",
      formatversion: "2",
      origin: "*",
    });
    try {
      const res = await fetch(WIKI_API + "?" + params.toString());
      const data = await res.json();
      const page = (data.query && data.query.pages && data.query.pages[0]) || {};
      return page.extract || "";
    } catch (e) {
      return "";
    }
  };

  // Fill every [data-wiki] element inside root with its Wikipedia lead image.
  GTC.loadThumbs = async function (root) {
    const els = Array.from((root || document).querySelectorAll("[data-wiki]"));
    if (!els.length) return;
    const titles = [...new Set(els.map((el) => el.dataset.wiki))];
    const info = await GTC.wiki(titles, 640);
    els.forEach((el) => {
      const item = info[el.dataset.wiki];
      if (!item || !item.thumb) return;
      if (el.tagName === "IMG") el.src = item.thumb;
      else el.style.backgroundImage = 'url("' + item.thumb.replace(/"/g, "%22") + '")';
      el.classList.add("loaded");
    });
  };

  // ---------- before / after slider (test mule -> real car) ----------
  // Returns a .compare element: the "before" image covers the left part, dragging reveals the "after" image.
  GTC.compare = function (beforeSrc, afterSrc, opts) {
    opts = Object.assign({ pos: 50, before: "Test mule", after: "Real car", alt: "" }, opts);
    const box = document.createElement("div");
    box.className = "compare";
    const after = new Image();
    after.className = "compare-after";
    after.src = afterSrc;
    after.alt = opts.alt ? opts.alt + " (real car)" : "";
    const before = new Image();
    before.className = "compare-before";
    before.src = beforeSrc;
    before.alt = opts.alt ? opts.alt + " (test mule)" : "";
    if (opts.lazy) after.loading = before.loading = "lazy";
    const range = document.createElement("input");
    range.type = "range";
    range.min = "0";
    range.max = "100";
    range.value = String(opts.pos);
    range.setAttribute("aria-label", "Slide to compare the test mule with the real car");
    const line = document.createElement("div");
    line.className = "compare-line";
    const labels = [["before", opts.before], ["after", opts.after]].map(([side, text]) => {
      const span = document.createElement("span");
      span.className = "compare-label " + side;
      span.textContent = text;
      return span;
    });
    const set = (v) => box.style.setProperty("--pos", v + "%");
    range.addEventListener("input", () => set(range.value));
    set(opts.pos);
    box.append(after, before, range, line, ...labels);
    return box;
  };

  // ---------- text helpers ----------
  GTC.normalize = (s) =>
    String(s || "")
      .normalize("NFD")
      .replace(/[̀-ͯ]/g, "")
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, " ")
      .trim();

  GTC.share = async function (text, button) {
    try {
      if (navigator.share && window.matchMedia("(pointer: coarse)").matches) {
        await navigator.share({ text: text });
        return;
      }
      await navigator.clipboard.writeText(text);
      if (button) {
        const old = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check2 me-1"></i>Copied!';
        setTimeout(() => (button.innerHTML = old), 1800);
      }
    } catch (e) { /* user cancelled */ }
  };

  // ---------- autocomplete ----------
  // source(query) -> Promise<string[]> | string[]
  GTC.autocomplete = function (input, source, onSelect) {
    const list = document.createElement("div");
    list.className = "ac-list";
    list.setAttribute("role", "listbox");
    list.id = input.id + "-list";
    input.setAttribute("aria-controls", list.id);
    input.setAttribute("aria-autocomplete", "list");
    input.parentNode.appendChild(list);
    let items = [];
    let active = -1;
    let timer = null;
    let requestId = 0;

    const close = () => {
      list.classList.remove("show");
      active = -1;
    };
    const choose = (value) => {
      input.value = value;
      close();
      if (onSelect) onSelect(value);
    };
    const render = () => {
      list.innerHTML = "";
      items.forEach((value, i) => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "ac-item" + (i === active ? " active" : "");
        btn.setAttribute("role", "option");
        btn.textContent = value;
        btn.addEventListener("mousedown", (ev) => {
          ev.preventDefault();
          choose(value);
        });
        list.appendChild(btn);
      });
      list.classList.toggle("show", items.length > 0);
    };

    input.addEventListener("input", () => {
      clearTimeout(timer);
      const q = input.value.trim();
      if (!q) {
        items = [];
        close();
        return;
      }
      timer = setTimeout(async () => {
        const id = ++requestId;
        const found = await source(q);
        if (id !== requestId) return;
        items = (found || []).slice(0, 8);
        active = -1;
        render();
      }, 150);
    });
    input.addEventListener("keydown", (ev) => {
      if (!list.classList.contains("show")) return;
      if (ev.key === "ArrowDown" || ev.key === "ArrowUp") {
        ev.preventDefault();
        const step = ev.key === "ArrowDown" ? 1 : -1;
        active = (active + step + items.length) % items.length;
        render();
      } else if (ev.key === "Enter" && active >= 0) {
        ev.preventDefault();
        choose(items[active]);
      } else if (ev.key === "Escape") {
        close();
      }
    });
    input.addEventListener("blur", () => setTimeout(close, 120));
  };
})();
