// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
// One Shot Prototype: a photo of a test mule, up to three text hints, one guess.
// After the shot a slider reveals the real car. Rounds: data/oneshot.json, images: Res/oneshot/<id>/1.webp + 2.webp.
//
// The rounds are played in order (the order of data/oneshot.json), one shot each. "Next" goes to the next
// round; when they are all played the game is over until new rounds are added. Progress is kept in this
// browser (localStorage "oneshot:v2" = { results: { "<round id>": { guess, correct, hints } } }).
(function () {
  "use strict";

  const STORE_KEY = "oneshot:v2";
  const $ = (id) => document.getElementById(id);
  const els = {
    stage: $("stage"),
    img: $("shotImage"),
    loading: $("stageLoading"),
    compareHelp: $("compareHelp"),
    form: $("shotForm"),
    input: $("shotInput"),
    hintBox: $("hintBox"),
    hintBtns: document.querySelectorAll("#hintBox .hint-btn"),
    hintList: $("hintList"),
    result: $("result"),
    banner: $("resultBanner"),
    headline: $("resultHeadline"),
    name: $("resultName"),
    meta: $("resultMeta"),
    fact: $("resultFact"),
    guess: $("resultGuess"),
    next: $("nextBtn"),
    share: $("shareBtn"),
    allDone: $("allDone"),
    roundLabel: $("roundLabel"),
  };

  let rounds = [];
  let suggestions = [];
  let current = null;
  let hintsOpen = 0;
  let lastShare = "";

  const results = (() => {
    const s = GTC.store.get(STORE_KEY, {});
    return s && typeof s.results === "object" && s.results ? s.results : {};
  })();
  const save = () => GTC.store.set(STORE_KEY, { results: results });

  const imageUrl = (item, n) => GTC.url("Res/oneshot/" + item.id + "/" + n + ".webp");
  const hintsOf = (item) => (item.hints || []).map((h) => String(h || "").trim());
  const isPlayed = (item) => Object.prototype.hasOwnProperty.call(results, item.id);
  const roundNumber = (item) => rounds.indexOf(item) + 1;
  const nextUnplayed = () => rounds.find((r) => !isPlayed(r)) || null;

  // ---------- stats, worked out from the results in round order ----------
  function stats() {
    let played = 0, won = 0, run = 0, best = 0, streak = 0;
    rounds.forEach((r) => {
      if (!isPlayed(r)) return;
      played++;
      if (results[r.id].correct) {
        won++;
        run++;
        best = Math.max(best, run);
      } else {
        run = 0;
      }
      streak = run; // hits in a row up to the latest played round
    });
    return { played: played, won: won, streak: streak, best: best };
  }

  function renderStats() {
    const s = stats();
    $("statPlayed").textContent = s.played;
    $("statWin").textContent = s.played ? Math.round((100 * s.won) / s.played) + "%" : "0%";
    $("statStreak").textContent = s.streak;
    $("statBest").textContent = s.best;
  }

  // ---------- question: the test mule photo ----------
  function showQuestion(item) {
    const old = els.stage.querySelector(".compare");
    if (old) old.remove();
    els.compareHelp.classList.add("d-none");
    els.img.classList.add("d-none");
    els.loading.classList.remove("d-none");
    els.loading.innerHTML = '<div class="spinner-border" role="status"><span class="visually-hidden">Loading</span></div>';
    els.img.onload = () => {
      if (current !== item) return;
      els.loading.classList.add("d-none");
      els.img.classList.remove("d-none");
    };
    els.img.onerror = () => {
      els.loading.innerHTML = '<span class="text-body-secondary"><i class="bi bi-image me-2"></i>Image unavailable right now.</span>';
    };
    els.img.src = imageUrl(item, 1);
  }

  // ---------- answer: slider between the test mule and the real car, starting mostly on the mule ----------
  function showAnswer(item) {
    const old = els.stage.querySelector(".compare");
    if (old) old.remove();
    els.img.classList.add("d-none");
    els.loading.classList.add("d-none");
    els.stage.append(GTC.compare(imageUrl(item, 1), imageUrl(item, 2), { pos: 70, alt: item.name }));
    els.compareHelp.classList.remove("d-none");
  }

  // ---------- hints: opened one by one; empty hints stay hidden ----------
  function renderHints(item, open, locked) {
    const hints = hintsOf(item).filter(Boolean);
    hintsOpen = Math.min(open, hints.length);
    els.hintBox.classList.toggle("d-none", hints.length === 0);
    els.hintBtns.forEach((btn, i) => {
      const n = i + 1;
      btn.classList.toggle("d-none", n > hints.length);
      btn.disabled = locked || n !== hintsOpen + 1;
      btn.classList.toggle("btn-primary", n <= hintsOpen);
      btn.classList.toggle("btn-outline-secondary", n > hintsOpen);
    });
    els.hintList.replaceChildren(...hints.slice(0, hintsOpen).map((text) => {
      const li = document.createElement("li");
      li.className = "hint-text mb-1";
      li.textContent = text;
      return li;
    }));
  }

  function setLabel(item) {
    els.roundLabel.textContent = "Test mule " + roundNumber(item) + " of " + rounds.length;
  }

  // A round that hasn't been played yet.
  function startRound(item) {
    current = item;
    setLabel(item);
    els.result.classList.add("d-none");
    els.form.classList.remove("d-none");
    els.input.value = "";
    renderHints(item, 0, false);
    showQuestion(item);
  }

  // A played round: show the result, the slider and the way forward.
  function showResult(item, animate) {
    current = item;
    setLabel(item);
    const r = results[item.id];
    els.form.classList.add("d-none");
    els.result.classList.remove("d-none");
    els.banner.classList.toggle("win", r.correct);
    els.banner.classList.toggle("lose", !r.correct);
    els.headline.textContent = r.correct ? "Bullseye! 🎯 It became the" : "Missed! 💨 It became the";
    els.name.textContent = item.name;
    els.meta.textContent = [item.year, item.maker].filter(Boolean).join(" · ");
    els.fact.textContent = item.fact || "";
    els.guess.classList.toggle("d-none", r.correct || !r.guess);
    els.guess.textContent = r.guess ? "Your shot: " + r.guess : "";
    renderHints(item, r.hints || 0, true);
    showAnswer(item);

    const next = nextUnplayed();
    els.next.classList.toggle("d-none", !next);
    els.allDone.classList.toggle("d-none", !!next);
    if (!next) {
      const s = stats();
      els.allDone.textContent = "That's all " + rounds.length + " test mules: you hit " + s.won + " of " + s.played +
        ". New ones are coming, check back later.";
    }

    const n = roundNumber(item);
    lastShare = "One Shot Prototype · test mule " + n + "/" + rounds.length + "\n" +
      (r.correct ? "🎯 Hit!" : "💨 Missed") + (r.hints ? " · " + r.hints + " hint" + (r.hints === 1 ? "" : "s") : " · no hints") +
      "\n" + window.location.origin + GTC.url("oneshot.php");
    if (r.correct && animate) GTC.celebrate();
  }

  function isCorrect(item, guess) {
    const g = GTC.normalize(guess);
    return [item.name].concat(item.aliases || []).some((n) => GTC.normalize(n) === g);
  }

  function takeShot(ev) {
    ev.preventDefault();
    const guess = els.input.value.trim();
    if (!guess || !current || isPlayed(current)) {
      els.input.classList.add("is-invalid");
      setTimeout(() => els.input.classList.remove("is-invalid"), 1200);
      return;
    }
    results[current.id] = { guess: guess, correct: isCorrect(current, guess), hints: hintsOpen };
    save();
    renderStats();
    showResult(current, true);
  }

  // Open the first round that hasn't been played; when all are played, show the last result.
  function resume() {
    const next = nextUnplayed();
    if (next) startRound(next);
    else showResult(rounds[rounds.length - 1], false);
  }

  // ---------- init ----------
  fetch(GTC.url("data/oneshot.json"))
    .then((r) => r.json())
    .then((data) => {
      rounds = data.rounds || [];
      if (!rounds.length) {
        els.loading.innerHTML = '<span class="text-body-secondary">No test mules yet. Check back soon.</span>';
        els.form.classList.add("d-none");
        return;
      }
      const names = new Set(rounds.map((p) => p.name).concat(data.decoys || []));
      suggestions = Array.from(names).sort((a, b) => a.localeCompare(b));
      renderStats();
      GTC.autocomplete(els.input, (q) => {
        const nq = GTC.normalize(q);
        return suggestions.filter((n) => GTC.normalize(n).includes(nq));
      });
      resume();
    })
    .catch(() => {
      els.loading.innerHTML = '<span class="text-body-secondary">Could not load the test mules. Please refresh.</span>';
    });

  els.hintBtns.forEach((btn) =>
    btn.addEventListener("click", () => {
      if (current && !btn.disabled) renderHints(current, hintsOpen + 1, false);
    })
  );
  els.form.addEventListener("submit", takeShot);
  els.next.addEventListener("click", resume);
  if (els.share) els.share.addEventListener("click", () => GTC.share(lastShare, els.share));
})();
