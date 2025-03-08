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
        url.searchParams.set("keyword", searchTerm);
      }
      // Reset page number to 1
      url.searchParams.set("page", "1");
      // Navigate to the new URL
      window.location.href = url.toString();
    });

  const urlParams = new URLSearchParams(window.location.search);
  const keywordParam = urlParams.get("keyword");
  if (keywordParam) {
    const searchInput = document.querySelector(
      "#navbar-form input[type='search']"
    );
    if (searchInput) {
      searchInput.value = keywordParam;
    }
  }
});
