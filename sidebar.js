// Global filter state
const filterState = {
  categories: [],
  types: [],
  regions: [],
  price1oz: [],
  pricehalfoz: [],
  proof: [],
  age: [],
};

// --- Filter List Rendering for Categories, Types, and Regions ---
// Generic function to update a filter list (for any list filter)
const updateFilterList = (
  container,
  searchInput,
  seeMoreButton,
  items,
  prefix
) => {
  const expanded = seeMoreButton.getAttribute("data-expanded") === "true";
  const filterText = searchInput.value.toLowerCase();
  const filtered = items.filter((item) =>
    item.toLowerCase().includes(filterText)
  );
  const itemsToShow =
    filterText !== "" ? filtered : expanded ? filtered : filtered.slice(0, 5);

  // Show or hide the See More/See Less button
  if (filterText !== "" || filtered.length <= 5) {
    seeMoreButton.style.display = "none";
  } else {
    seeMoreButton.style.display = "inline-block";
    seeMoreButton.textContent = expanded ? "See less" : "See more";
  }

  // Render the list items with checkboxes
  container.innerHTML = "";
  itemsToShow.forEach((item) => {
    const div = document.createElement("div");
    div.className = "mb-1 d-flex align-items-center gap-1";
    const id = prefix + "-" + item.replace(/\s+/g, "-").toLowerCase();
    const checkbox = document.createElement("input");
    checkbox.type = "checkbox";
    checkbox.classList.add(prefix + "-checkbox", "form-check-input");
    // Data attribute uses the prefix (e.g., data-cat, data-type, data-region)
    checkbox.setAttribute("data-" + prefix, item);
    checkbox.id = id;

    const label = document.createElement("label");
    label.classList.add("form-check-label");
    label.htmlFor = id;
    label.textContent = item;

    div.appendChild(checkbox);
    div.appendChild(document.createTextNode(" "));
    div.appendChild(label);
    container.appendChild(div);
  });
};

// Helper function to set up event listeners for a filter UI
function setupFilterUI(
  containerId,
  searchInputId,
  seeMoreBtnId,
  items,
  prefix
) {
  const container = document.getElementById(containerId);
  const searchInput = document.getElementById(searchInputId);
  const seeMoreButton = document.getElementById(seeMoreBtnId);

  // Initialize with current values
  updateFilterList(container, searchInput, seeMoreButton, items, prefix);

  // Update list on search input change
  searchInput.addEventListener("input", () => {
    updateFilterList(container, searchInput, seeMoreButton, items, prefix);
  });

  // Toggle between full list and limited list on See More/See Less click
  seeMoreButton.addEventListener("click", () => {
    const expanded = seeMoreButton.getAttribute("data-expanded") === "true";
    seeMoreButton.setAttribute("data-expanded", !expanded);
    updateFilterList(container, searchInput, seeMoreButton, items, prefix);
  });
}

// --- Update Global Filter State on Checkbox Change ---
// Categories
document.getElementById("categoryList").addEventListener("change", (e) => {
  if (e.target.classList.contains("cat-checkbox")) {
    const category = e.target.getAttribute("data-cat");
    if (e.target.checked) {
      if (!filterState.categories.includes(category)) {
        filterState.categories.push(category);
      }
    } else {
      filterState.categories = filterState.categories.filter(
        (cat) => cat !== category
      );
    }
  }
});
// Types
document.getElementById("typeList").addEventListener("change", (e) => {
  if (e.target.classList.contains("type-checkbox")) {
    const type = e.target.getAttribute("data-type");
    if (e.target.checked) {
      if (!filterState.types.includes(type)) {
        filterState.types.push(type);
      }
    } else {
      filterState.types = filterState.types.filter((t) => t !== type);
    }
  }
});
// Regions
document.getElementById("regionList").addEventListener("change", (e) => {
  if (e.target.classList.contains("region-checkbox")) {
    const region = e.target.getAttribute("data-region");
    if (e.target.checked) {
      if (!filterState.regions.includes(region)) {
        filterState.regions.push(region);
      }
    } else {
      filterState.regions = filterState.regions.filter((r) => r !== region);
    }
  }
});

// Price 1 oz filtering
document.querySelectorAll(".price-1oz-range-checkbox").forEach((chk) => {
  chk.addEventListener("change", () => {
    let selected = [];
    document.querySelectorAll(".price-1oz-range-checkbox").forEach((cb) => {
      if (cb.checked) {
        const min = cb.getAttribute("data-min");
        const max = cb.getAttribute("data-max");
        selected.push(max === "Infinity" ? min + "+" : min + "-" + max);
      }
    });
    filterState.price1oz = selected;
  });
});

// Price Half oz filtering
document.querySelectorAll(".price-halfoz-range-checkbox").forEach((chk) => {
  chk.addEventListener("change", () => {
    let selected = [];
    document.querySelectorAll(".price-halfoz-range-checkbox").forEach((cb) => {
      if (cb.checked) {
        const min = cb.getAttribute("data-min");
        const max = cb.getAttribute("data-max");
        selected.push(max === "Infinity" ? min + "+" : min + "-" + max);
      }
    });
    filterState.pricehalfoz = selected;
  });
});

// Proof filtering
document.querySelectorAll(".proof-range-checkbox").forEach((chk) => {
  chk.addEventListener("change", () => {
    let selected = [];
    document.querySelectorAll(".proof-range-checkbox").forEach((cb) => {
      if (cb.checked) {
        const min = cb.getAttribute("data-min");
        const max = cb.getAttribute("data-max");
        selected.push(max === "Infinity" ? min + "+" : min + "-" + max);
      }
    });
    filterState.proof = selected;
  });
});

// Age Range filtering
document.querySelectorAll(".age-range-checkbox").forEach((chk) => {
  chk.addEventListener("change", () => {
    let selected = [];
    document.querySelectorAll(".age-range-checkbox").forEach((cb) => {
      if (cb.checked) {
        const min = cb.getAttribute("data-min");
        const max = cb.getAttribute("data-max");
        selected.push(max === "Infinity" ? min + "+" : min + "-" + max);
      }
    });
    filterState.age = selected;
  });
});

// --- Generic function for updating checkboxes based on URL parameters and updating filterState ---
function updateCheckboxesFromUrl(
  paramName,
  checkboxClass,
  dataAttr,
  formatter
) {
  const urlParams = new URLSearchParams(window.location.search);
  const paramValue = urlParams.get(paramName);
  let selected = [];
  if (paramValue) {
    selected = paramValue.split(",").map((item) => item.trim());
  }
  // Update the corresponding filterState property based on paramName
  switch (paramName) {
    case "categories":
      filterState.categories = selected;
      break;
    case "types":
      filterState.types = selected;
      break;
    case "region":
      filterState.regions = selected;
      break;
    case "price_1_oz":
      filterState.price1oz = selected;
      break;
    case "price_half_oz":
      filterState.pricehalfoz = selected;
      break;
    case "proof":
      filterState.proof = selected;
      break;
    case "age":
      filterState.age = selected;
      break;
  }
  document.querySelectorAll(`.${checkboxClass}`).forEach((chk) => {
    let value;
    if (formatter && typeof formatter === "function") {
      value = formatter(chk);
    } else if (dataAttr) {
      value = chk.getAttribute("data-" + dataAttr);
    }
    chk.checked = selected.includes(value);
  });
}

// Formatter for range checkboxes: formats as "min-max" or "min+"
function priceFormatter(chk) {
  const min = chk.getAttribute("data-min");
  const max = chk.getAttribute("data-max");
  return max === "Infinity" ? min + "+" : min + "-" + max;
}

// Formatter for age range checkboxes: similar to priceFormatter
function ageFormatter(chk) {
  const min = chk.getAttribute("data-min");
  const max = chk.getAttribute("data-max");
  return max === "Infinity" ? min + "+" : min + "-" + max;
}

// --- Apply & Clear Filter Buttons (Unified for both Desktop and Mobile) ---
document.getElementById("applyFilters").addEventListener("click", () => {
  const url = new URL(window.location.href);
  if (filterState.categories.length > 0) {
    url.searchParams.set("categories", filterState.categories.join(","));
  } else {
    url.searchParams.delete("categories");
  }
  if (filterState.types.length > 0) {
    url.searchParams.set("types", filterState.types.join(","));
  } else {
    url.searchParams.delete("types");
  }
  if (filterState.regions.length > 0) {
    url.searchParams.set("region", filterState.regions.join(","));
  } else {
    url.searchParams.delete("region");
  }
  if (filterState.price1oz.length > 0) {
    url.searchParams.set("price_1_oz", filterState.price1oz.join(","));
  } else {
    url.searchParams.delete("price_1_oz");
  }
  if (filterState.pricehalfoz.length > 0) {
    url.searchParams.set("price_half_oz", filterState.pricehalfoz.join(","));
  } else {
    url.searchParams.delete("price_half_oz");
  }
  if (filterState.proof.length > 0) {
    url.searchParams.set("proof", filterState.proof.join(","));
  } else {
    url.searchParams.delete("proof");
  }
  if (filterState.age.length > 0) {
    url.searchParams.set("age", filterState.age.join(","));
  } else {
    url.searchParams.delete("age");
  }
  url.searchParams.set("page", "1");
  window.history.pushState(null, "", url.toString());
  fetchliquorsData();
});

document.getElementById("clearFilters").addEventListener("click", () => {
  // Reset global filterState
  filterState.categories = [];
  filterState.types = [];
  filterState.regions = [];
  filterState.price1oz = [];
  filterState.pricehalfoz = [];
  filterState.proof = [];
  filterState.age = [];
  // Uncheck all filter checkboxes
  document
    .querySelectorAll(
      "input.cat-checkbox, input.type-checkbox, input.region-checkbox, input.price-1oz-range-checkbox, input.price-halfoz-range-checkbox, input.proof-range-checkbox, input.age-range-checkbox"
    )
    .forEach((chk) => {
      chk.checked = false;
    });
  const url = new URL(window.location.href);
  url.searchParams.delete("categories");
  url.searchParams.delete("types");
  url.searchParams.delete("region");
  url.searchParams.delete("price_1_oz");
  url.searchParams.delete("price_half_oz");
  url.searchParams.delete("proof");
  url.searchParams.delete("age");
  url.searchParams.set("page", "1");
  window.history.pushState(null, "", url.toString());
  fetchliquorsData();
});

// --- Initial Setup on Page Load ---
document.addEventListener("DOMContentLoaded", async () => {
  // Setup filter UI for Categories & Types & Regions using unified IDs
  // Categories
  setupFilterUI(
    "categoryList",
    "catSearch",
    "seeMore",
    sidebarData.categories,
    "cat"
  );
  // Types
  setupFilterUI(
    "typeList",
    "typeSearch",
    "seeMoreType",
    sidebarData.types,
    "type"
  );
  // Regions
  setupFilterUI(
    "regionList",
    "regionSearch",
    "seeMoreRegion",
    sidebarData.regions,
    "region"
  );

  // Update checkboxes from URL parameters (if filters already applied) and update filterState
  updateCheckboxesFromUrl("categories", "cat-checkbox", "cat");
  updateCheckboxesFromUrl("types", "type-checkbox", "type");
  updateCheckboxesFromUrl("region", "region-checkbox", "region");
  updateCheckboxesFromUrl(
    "price_1_oz",
    "price-1oz-range-checkbox",
    null,
    priceFormatter
  );
  updateCheckboxesFromUrl(
    "price_half_oz",
    "price-halfoz-range-checkbox",
    null,
    priceFormatter
  );
  updateCheckboxesFromUrl(
    "proof",
    "proof-range-checkbox",
    null,
    priceFormatter
  );
  updateCheckboxesFromUrl("age", "age-range-checkbox", null, ageFormatter);
});
