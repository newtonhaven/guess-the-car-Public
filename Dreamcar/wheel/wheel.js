// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
(function () {
  "use strict";

  const wheel = document.getElementById("wheelImage");
  const spinButton = document.getElementById("spinBtn");
  const carImage = document.getElementById("wheelCar");
  const carName = document.getElementById("wheelCarName");
  const carText = document.getElementById("wheelCarText");
  const resultCard = document.getElementById("wheelResult");
  const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  let rotation = 0;
  let cars = [];
  let lastIndex = -1;

  fetch(GTC.url("Dreamcar/wheel/wheel.json"))
    .then((response) => response.json())
    .then((data) => (cars = data))
    .catch(() => {
      carText.textContent = "Sorry, our bad: the wheel fell off. Please refresh the page.";
    });

  spinButton.addEventListener("click", () => {
    if (!cars.length) return;
    // Random pick over however many cars wheel.json has (no need to edit this when adding cars).
    let index;
    do {
      index = Math.floor(Math.random() * cars.length);
    } while (cars.length > 1 && index === lastIndex);
    lastIndex = index;
    const car = cars[index];

    spinButton.disabled = true;
    rotation += 3600 + Math.floor(Math.random() * 360);
    wheel.style.transform = "rotate(" + rotation + "deg)";

    setTimeout(() => {
      carImage.src = GTC.url("Dreamcar/" + car.path);
      carImage.alt = car.title;
      carName.textContent = car.title;
      carText.textContent = (car.description || "").trim();
      resultCard.classList.remove("fade-in");
      void resultCard.offsetWidth;
      resultCard.classList.add("fade-in");
      spinButton.disabled = false;
      if (window.innerWidth < 992) resultCard.scrollIntoView({ behavior: "smooth", block: "center" });
    }, reducedMotion ? 1000 : 4000);
  });
})();
