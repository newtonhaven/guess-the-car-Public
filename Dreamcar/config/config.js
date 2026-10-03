// This software is licensed under the MIT License.
// See the LICENSE file in the root directory for details.
(function () {
  "use strict";

  // engine placement + driving -> available induction types -> car image (carIMGs/<name>.webp)
  const CARS = {
    frontfwd: [["avantime", "Turbocharged"], ["a2", "Supercharged"]],
    frontrwd: [["cygnet", "Naturally Aspirated"]],
    frontawd: [["alto", "Turbocharged"]],
    midfwd: [["europa", "Turbocharged"]],
    midrwd: [["autozam", "Turbocharged"], ["previa", "Supercharged"], ["nano", "Naturally Aspirated"]],
    midawd: [["previav8", "Turbocharged"], ["hondaz", "Naturally Aspirated"]],
    rearfwd: [["gregory", "Naturally Aspirated"]],
    rearrwd: [["meow", "Electric"], ["smart", "Turbocharged"], ["peaCar", "Naturally Aspirated"]],
    rearawd: [["itaipu", "Electric"]],
  };

  const engine = document.getElementById("enginePlacement");
  const driving = document.getElementById("driving");
  const induction = document.getElementById("induction");
  const button = document.getElementById("findBtn");
  const result = document.getElementById("configResult");

  function updateInduction() {
    const options = CARS[engine.value + driving.value];
    induction.innerHTML = "";
    const placeholder = new Option(options ? "Choose…" : "Pick the first two", "", true, true);
    placeholder.disabled = true;
    induction.add(placeholder);
    (options || []).forEach(([car, label]) => induction.add(new Option(label, car)));
    induction.disabled = !options;
    updateButton();
  }

  function updateButton() {
    button.disabled = !(engine.value && driving.value && induction.value);
  }

  engine.addEventListener("change", updateInduction);
  driving.addEventListener("change", updateInduction);
  induction.addEventListener("change", updateButton);

  document.getElementById("configForm").addEventListener("submit", (ev) => {
    ev.preventDefault();
    if (!induction.value) return;
    result.classList.remove("fade-in");
    void result.offsetWidth; // restart the animation
    result.src = GTC.url("Dreamcar/carIMGs/" + induction.value + ".webp");
    result.alt = "Your dream car";
    result.classList.add("fade-in");
    if (window.innerWidth < 992) result.scrollIntoView({ behavior: "smooth", block: "center" });
  });
})();
