// Select all main dropdown buttons
const dropdownButtons = document.querySelectorAll(".dropdown-btn");

// Select all nested dropdown buttons
const subDropdownButtons = document.querySelectorAll(".sub-dropdown-btn");

// Store dropdown instances
const dropdownInstances = new Map();

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
  button.addEventListener("click", (event) => {
    event.preventDefault();

    // Prevent parent dropdown from closing
    event.stopPropagation();

    // Toggle the nested dropdown manually
    let nestedMenu = button.nextElementSibling;
    if (nestedMenu) {
      let isOpen = nestedMenu.classList.contains("show");
      document
        .querySelectorAll(".dropdown-menu .dropdown-menu")
        .forEach((menu) => menu.classList.remove("show"));

      if (!isOpen) {
        nestedMenu.classList.add("show");
      }
    }
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
