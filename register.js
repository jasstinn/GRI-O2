import { loadFormState, saveFormState } from "./localStorage.js";
import { bindValidation } from "./events.js";
export default function initRegister() {
  // localStorage.clear(); // clear local storage

  const form = document.getElementById("register-form");
  form.noValidate = true;
  if (!form) return;

  bindValidation(form);
  loadFormState(form);

  form.addEventListener("input", () => saveFormState(form));
  form.addEventListener("change", () => saveFormState(form));

  const nextButtons = form.querySelectorAll(".btn-next");
  const prevButtons = form.querySelectorAll(".btn-prev");
  const steps = form.querySelectorAll(".form-step");
  const circles = document.querySelectorAll(".step-circle");
  let currentStep = 0;

  nextButtons.forEach((button) => {
    button.addEventListener("click", () => {
      if (validateStep(currentStep)) {
        currentStep++;
        showStep(currentStep);
      }
    });
  });

  prevButtons.forEach((button) => {
    button.addEventListener("click", () => {
      currentStep--;
      showStep(currentStep);
    });
  });

  function showStep(stepIndex) {
    steps.forEach((step, index) => {
      step.classList.toggle("active", index === stepIndex);
    });
    circles.forEach((circle, index) => {
      circle.classList.toggle("active", index <= stepIndex);
    });
  }

  function validateStep(stepIndex) {
    const step = steps[stepIndex];
    let isValid = true;
    step.querySelectorAll("[data-validate]").forEach((field) => {
      field.dispatchEvent(new Event("input"));
      if (field.classList.contains("is-invalid")) {
        isValid = false;
      }
    });
    return isValid;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!validateStep(currentStep)) {
      return;
    }

    const data = new FormData(form);
    const result = await submitRegister(data);
    console.log(result);
    if (result.success) {
      window.location.href = "login.php";
      localStorage.clear();
    }
  });

  async function submitRegister(data) {
    const url = "/php/register_submit.php";
    const res = await fetch(url, { method: "POST", body: data });
    const json = await res.json();
    return json;
  }

  //Eye toggle
  const toggles = document.querySelectorAll(".toggle-password-btn");

  toggles.forEach((btn) => {
    btn.addEventListener("click", () => {
      // const input = document.querySelector(btn.getAttribute("toggle"));
      const input = document.querySelector(
        `input[name="${btn.dataset.type}"]`
      );
      const icon = btn.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
      }
    });
  });

  localStorage.clear();

  //   test load
  //   window.addEventListener('load',()=>{
  //     form.querySelectorAll("[data-validate]").forEach(field => field.dispatchEvent(new Event("input")));
  //   });
}