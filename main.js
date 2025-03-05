// Loading spinner functions
function showLoading() {
  const gridContainer = document.getElementById("productsGrid");
  gridContainer.innerHTML = ""; // Clear previous items
  document.getElementById("loading").style.display = "block";
}

function hideLoading() {
  document.getElementById("loading").style.display = "none";
}

// Reusable async function that builds the API URL with query parameters from the current URL and fetches data
async function fetchLiqueursData() {
  showLoading(); // Show the loading indicator before starting the fetch
  const baseUrl = "/api";
  const apiUrl = baseUrl + window.location.search; // Append query parameters from current URL
  try {
    const response = await fetch(apiUrl);
    if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
    const responseData = await response.json();
    renderProducts(responseData);
  } catch (error) {
    console.error("Fetch error:", error);
    throw error;
  } finally {
    hideLoading(); // Hide the loading indicator when done
  }
}

// Function to render the product grid based on the server response
function renderProducts(responseData) {
  const gridContainer = document.getElementById("productsGrid");
  gridContainer.innerHTML = ""; // Clear previous items

  if (responseData.length === 0) {
    gridContainer.innerHTML = `<p style="flex: 0 0 100%; max-width: 100%;" class="fs-3 text-center">No Results Found</p>`;
    return;
  }

  responseData.forEach((item) => {
    let ageText;
    if (item.age === "NAS") {
      ageText = "NAS";
    } else if (parseFloat(item.age) === 1) {
      ageText = "1 Year";
    } else {
      ageText = item.age + " Years";
    }

    const col = document.createElement("div");
    col.className = "col";
    col.innerHTML = `
      <div class="card product-card shadow-sm mb-4 p-3">
        <div class="position-relative">
          <img src="${item.image}" class="" alt="${item.bottle}" />
          <a href="/liqueurs/${item.id}" class="stretched-link"></a>
        </div>
        <div class="card-body">
          <h6 class="text-muted">${item.distiller}</h6>
          <h5 class="card-title mb-2">${item.bottle}</h5>
          <p class="mb-2"><span class="text-danger fw-bold fs-5">\$${item.price_half_oz}</span><small class="text-muted"> per 1/2 oz</small></p>
          <p class="mb-2"><span class="fw-bold fs-6">\$${item.price_1_oz}</span><small class="text-muted"> per oz</small></p>
          <p class="mb-1 text-muted">
            <small>${item.type} &bull; ${item.region}</small>
          </p>
          <p class="mb-3 text-muted">
            <small>Proof: ${item.proof} | Age: ${ageText}</small>
          </p>
          <button class="btn btn-outline-danger w-100 rounded-0 add-to-cart">Add to Cart</button>
        </div>
      </div>
    `;
    gridContainer.appendChild(col);
  });
}

// --- Initial Setup on Page Load ---
document.addEventListener("DOMContentLoaded", async () => {
  // Sorting
  document
    .getElementById("sortSelect")
    .addEventListener("change", function (e) {
      const sortParam = e.target.value; // e.g. "price,asc"
      const url = new URL(window.location.href);
      if (sortParam) {
        url.searchParams.set("sort", sortParam);
      } else {
        url.searchParams.delete("sort");
      }
      url.searchParams.set("page", "1");
      window.history.pushState(null, "", url.toString());
      fetchLiqueursData();
    });

  await fetchLiqueursData();
});
