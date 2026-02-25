/**
 * login.js — New Path
 * Ported from Java project login.js.
 * Handles: password toggle, field validation, submit loading state.
 */
document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const passwordToggle = document.getElementById("passwordToggle");
  const submitBtn = document.querySelector(".form-submit-btn");

  if (!loginForm) return;

  // ── Password visibility toggle ──────────────────────────
  if (passwordToggle) {
    passwordToggle.addEventListener("click", function () {
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      const icon = passwordToggle.querySelector("svg");
      if (type === "text") {
        icon.innerHTML = `
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                `;
      } else {
        icon.innerHTML = `
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>
                `;
      }
    });
  }

  // ── Validation helpers ──────────────────────────────────
  function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function validatePassword(password) {
    return password.length >= 6;
  }

  function showFieldError(input, message) {
    clearFieldError(input);
    input.style.borderColor = "#e74c3c";
    const err = document.createElement("div");
    err.className = "field-error";
    err.style.color = "#e74c3c";
    err.style.fontSize = "12px";
    err.style.marginTop = "4px";
    err.textContent = message;
    input.parentNode.appendChild(err);
  }

  function clearFieldError(input) {
    const existing = input.parentNode.querySelector(".field-error");
    if (existing) existing.remove();
    input.style.borderColor = "";
  }

  // ── Real-time validation on blur ────────────────────────
  if (emailInput) {
    emailInput.addEventListener("blur", function () {
      if (this.value && !validateEmail(this.value)) {
        showFieldError(this, "Please enter a valid email address");
      } else {
        clearFieldError(this);
      }
    });
    emailInput.addEventListener("input", function () {
      clearFieldError(this);
    });
  }

  if (passwordInput) {
    passwordInput.addEventListener("blur", function () {
      if (this.value && !validatePassword(this.value)) {
        showFieldError(this, "Password must be at least 6 characters long");
      } else {
        clearFieldError(this);
      }
    });
    passwordInput.addEventListener("input", function () {
      clearFieldError(this);
    });
  }

  // ── Form submit — validate + loading state ──────────────
  loginForm.addEventListener("submit", function (e) {
    const email = emailInput ? emailInput.value.trim() : "";
    const password = passwordInput ? passwordInput.value.trim() : "";
    let isValid = true;

    if (emailInput) clearFieldError(emailInput);
    if (passwordInput) clearFieldError(passwordInput);

    if (!email) {
      showFieldError(emailInput, "Email is required");
      isValid = false;
    } else if (!validateEmail(email)) {
      showFieldError(emailInput, "Please enter a valid email address");
      isValid = false;
    }

    if (!password) {
      showFieldError(passwordInput, "Password is required");
      isValid = false;
    } else if (!validatePassword(password)) {
      showFieldError(
        passwordInput,
        "Password must be at least 6 characters long",
      );
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
      return;
    }

    
  });

  // ── Auto-focus email ────────────────────────────────────
  if (emailInput) {
    emailInput.focus();
  }
});
