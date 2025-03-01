// Select all main dropdown buttons
const dropdownButtons = document.querySelectorAll(".dropdown-btn");

// Select all nested dropdown buttons
const subDropdownButtons = document.querySelectorAll(".sub-dropdown-btn");

// Store dropdown instances
const dropdownInstances = new Map();
const subDropdownInstances = new Map();

// Initialize all main dropdowns
dropdownButtons.forEach((button) => {
  const dropdown = new bootstrap.Dropdown(button);
  dropdownInstances.set(button, dropdown);

  button.addEventListener("click", (event) => {
    event.preventDefault();

    // Close all other dropdowns before opening the clicked one
    dropdownInstances.forEach((instance, btn) => {
      if (btn !== button) {
        instance.hide();
      }
    });

    // Toggle the clicked dropdown
    dropdown.toggle();
  });
});

// Handle nested dropdowns separately
subDropdownButtons.forEach((button) => {
  const subDropdown = new bootstrap.Dropdown(button);
  subDropdownInstances.set(button, subDropdown);

  button.addEventListener("click", (event) => {
    event.preventDefault();

    subDropdownInstances.forEach((instance, btn) => {
      if (btn !== button) {
        instance.hide();
      }
    });

    subDropdown.toggle();
  });
});

// Close dropdowns when clicking outside
document.addEventListener("click", function (event) {
  let isDropdownButton = false;

  dropdownButtons.forEach((button) => {
    if (button.contains(event.target)) {
      isDropdownButton = true;
    }
  });

  subDropdownButtons.forEach((button) => {
    if (button.contains(event.target)) {
      isDropdownButton = true;
    }
  });

  if (!isDropdownButton) {
    dropdownInstances.forEach((instance) => instance.hide());
    document
      .querySelectorAll(".dropdown-menu .dropdown-menu")
      .forEach((menu) => menu.classList.remove("show"));
  }
});

document.addEventListener("DOMContentLoaded", () => {
  document
    .getElementById("navbar-form")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      const input = this.querySelector("input[type='search']");
      const searchTerm = input.value.trim();
      // Build a new URL that navigates to /search
      const url = new URL(window.location.origin + "/search");
      if (searchTerm !== "") {
        url.searchParams.set("bottle", searchTerm);
      }
      // Reset page number to 1
      url.searchParams.set("page", "1");
      // Navigate to the new URL
      window.location.href = url.toString();
    });

  const urlParams = new URLSearchParams(window.location.search);
  const bottleParam = urlParams.get("bottle");
  if (bottleParam) {
    const searchInput = document.querySelector(
      "#navbar-form input[type='search']"
    );
    if (searchInput) {
      searchInput.value = bottleParam;
    }
  }
});
