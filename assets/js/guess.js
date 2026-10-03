// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
// Guess the Car: five picture hints, five guesses, one car per day.
// Progress is kept in this browser (GTC.progress in app.js, like One Shot); the server only
// checks each guess (serverThings/guess.php), so the answer stays secret until the game is over.
(function () {
  "use strict";

  const cfg = JSON.parse(document.getElementById("gtcGame").textContent);
  const $ = (id) => document.getElementById(id);
  const els = {
    img: $("hintImage"),
    hints: document.querySelectorAll(".hint-btn"),
    dots: document.querySelectorAll("#guessDots span"),
    msg: $("guessMsg"),
    result: $("result"),
    headline: $("resultHeadline"),
    answer: $("resultAnswer"),
    form: $("guessForm"),
    input: $("guessInput"),
    submit: $("guessSubmit"),
    left: $("guessLeft"),
    wrong: $("wrongList"),
    finished: $("finishedActions"),
    share: $("shareBtn"),
  };
  let game = GTC.progress.get(cfg.day);

  const isFinished = () => game.status === "won" || game.status === "lost";

  function showHint(n) {
    els.img.src = GTC.url("Res/games/" + cfg.day + "/" + n + ".webp");
    els.img.alt = "Hint " + n + " for day " + cfg.day;
    els.hints.forEach((btn) => {
      const active = Number(btn.dataset.hint) === n;
      btn.classList.toggle("btn-primary", active);
      btn.classList.toggle("btn-outline-secondary", !active);
    });
  }

  function flash(text) {
    els.msg.textContent = text || "";
    els.msg.classList.toggle("d-none", !text);
  }

  function render() {
    const finished = isFinished();
    const wrong = game.guesses.length;
    const unlocked = finished ? cfg.maxGuesses : Math.min(cfg.maxGuesses, wrong + 1);

    els.hints.forEach((btn) => {
      const n = Number(btn.dataset.hint);
      btn.disabled = n > unlocked;
      btn.innerHTML = n > unlocked ? '<i class="bi bi-lock-fill"></i>' : String(n);
      btn.setAttribute("aria-label", n > unlocked ? "Hint " + n + " (locked)" : "Show hint " + n);
    });
    showHint(unlocked);

    els.dots.forEach((dot, i) => {
      dot.className = i < wrong ? "used" : i === wrong && game.status === "won" ? "win" : "";
    });

    els.wrong.replaceChildren(...game.guesses.map((guess, i) => {
      const li = document.createElement("li");
      li.className = "small py-1";
      const icon = document.createElement("i");
      icon.className = "bi bi-x-circle me-2";
      li.append(icon, "#" + (i + 1) + ": it's not " + guess);
      return li;
    }));

    if (els.form) {
      els.form.classList.toggle("d-none", finished);
      const left = cfg.maxGuesses - wrong;
      els.left.textContent = left + " guess" + (left === 1 ? "" : "es") + " left.";
    }

    els.result.classList.toggle("d-none", !finished);
    els.result.classList.toggle("win", game.status === "won");
    els.result.classList.toggle("lose", game.status === "lost");
    els.headline.textContent = game.status === "won" ? "Correct! 🎉 It was" : "Game over. It was";
    els.answer.textContent = game.answer || "";
    els.finished.classList.toggle("d-none", !finished);

    GTC.progress.paint(); // stats tiles
  }

  async function submitGuess(ev) {
    ev.preventDefault();
    if (isFinished()) return;
    const guess = els.input.value.trim();
    if (!guess) return flash("Type a car name first.");
    if (game.guesses.some((g) => GTC.normalize(g) === GTC.normalize(guess))) {
      return flash('You already tried "' + guess + '".');
    }
    flash("");
    els.submit.disabled = true;

    let res = null;
    let data = {};
    try {
      res = await fetch(GTC.url("serverThings/guess.php"), {
        method: "POST",
        body: new URLSearchParams({ day: cfg.day, guess: guess, attempt: game.guesses.length + 1 }),
      });
      data = await res.json().catch(() => ({}));
    } catch (e) { /* offline: handled below */ }
    els.submit.disabled = false;
    if (!res || !res.ok) {
      return flash(data.error || "The game server is taking a pit stop. Please try again in a minute.");
    }

    if (data.correct) {
      game.status = "won";
      game.answer = data.answer;
    } else {
      game.guesses.push(guess);
      game.status = game.guesses.length >= cfg.maxGuesses ? "lost" : "playing";
      if (game.status === "lost") game.answer = data.answer;
    }
    GTC.progress.set(cfg.day, game);
    els.input.value = "";
    render();
    if (game.status === "won") GTC.celebrate();
  }

  // ---------- init ----------
  els.hints.forEach((btn) =>
    btn.addEventListener("click", () => {
      if (!btn.disabled) showHint(Number(btn.dataset.hint));
    })
  );

  if (els.form) {
    els.form.addEventListener("submit", submitGuess);
    GTC.autocomplete(els.input, (q) =>
      fetch(GTC.url("serverThings/search.php?query=" + encodeURIComponent(q)))
        .then((r) => (r.ok ? r.json() : []))
        .then((rows) => rows.map((r) => r.answer))
        .catch(() => [])
    );
  }

  els.share.addEventListener("click", () => {
    const wrong = game.guesses.length;
    const won = game.status === "won";
    const squares = "🟥".repeat(wrong) + (won ? "🟩" : "") + "⬜".repeat(Math.max(0, cfg.maxGuesses - wrong - (won ? 1 : 0)));
    const text = "Guess the Car · Day " + cfg.day + " " + (won ? wrong + 1 : "X") + "/" + cfg.maxGuesses + "\n" +
      squares + "\n" + location.origin + GTC.url("daily.php");
    GTC.share(text, els.share);
  });

  render();
})();
