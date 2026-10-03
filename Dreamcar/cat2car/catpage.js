// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
(function () {
  "use strict";

  // cat -> [car, reason]; the image is catIMGs/<cat>.webp
  const CATS = {
    persian: ["Rolls Royce Phantom", "*Money, money, money*"],
    siamese: ["Porsche 911 Sport Classic", "Two icons together."],
    mainecoon: ["Ford F-250 Super Duty", "Both are oversized for their kind, and one of them still counts as a car and the other one as a cat."],
    savannah: ["Mega Track", "I think their looks explain everything (and both are beautiful and one of their kind)."],
    scottish: ["WILL.I.AMG", "Both shouldn't exist. (This cat is born with muscle pain and heart problems.)"],
    sphynx: ["EQB", "Both are hairless and both are ugly. (Sorry sphynx owners, but you know it's true.)"],
    bornana: ["GTV6", "Beautiful, fun but problematic. Everybody admires them but nobody wants to take care of them."],
    black: ["E39 M5", "Elegant, priceless(!), and some people think it brings bad luck (or insane repair costs)."],
    zelda: ["Jaguar XKE", "Because she was beautiful, elegant and timeless. Still miss you little one <3"],
  };

  const select = document.getElementById("catSelection");
  const img = document.getElementById("catResult");
  const name = document.getElementById("carName");
  const text = document.getElementById("carText");

  document.getElementById("catForm").addEventListener("submit", (ev) => {
    ev.preventDefault();
    const match = CATS[select.value];
    if (!match) return;
    img.src = GTC.url("Dreamcar/catIMGs/" + select.value + ".webp");
    img.alt = match[0];
    name.textContent = match[0];
    text.textContent = match[1];
    const card = document.getElementById("catResultCard");
    card.classList.remove("fade-in");
    void card.offsetWidth;
    card.classList.add("fade-in");
    if (window.innerWidth < 992) card.scrollIntoView({ behavior: "smooth", block: "start" });
  });
})();
