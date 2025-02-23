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

// Function to generate pagination links based on current page and total pages
function generatePaginationLinks(currentPage, totalPages) {
  let pages = [];
  if (totalPages <= 6) {
    for (let i = 1; i <= totalPages; i++) {
      pages.push(i);
    }
  } else {
    if (currentPage <= 3) {
      pages = [1, 2, 3, "...", totalPages - 1, totalPages];
    } else if (currentPage >= totalPages - 2) {
      pages = [1, 2, "...", totalPages - 2, totalPages - 1, totalPages];
    } else {
      pages = [1, "...", currentPage, currentPage + 1, "...", totalPages];
    }
  }
  return pages;
}

// Function to render the pagination UI in the container with id "paginationContainer"
function renderPagination(totalPages, currentPage) {
  const container = document.getElementById("paginationContainer");
  container.innerHTML = ""; // Clear previous pagination

  // Create Previous button
  const prevLi = document.createElement("li");
  prevLi.className = "page-item" + (currentPage === 1 ? " disabled" : "");
  prevLi.innerHTML = `<a class="page-link" href="#" data-page="${
    currentPage - 1
  }"><i class="bi bi-chevron-left"></i></a>`;
  container.appendChild(prevLi);

  // Generate pagination links
  const pages = generatePaginationLinks(currentPage, totalPages);
  pages.forEach((page) => {
    const li = document.createElement("li");
    if (page === "...") {
      li.className = "page-item disabled";
      li.innerHTML = `<span class="page-link">${page}</span>`;
    } else {
      li.className = "page-item" + (page === currentPage ? " active" : "");
      li.innerHTML = `<a class="page-link" href="#" data-page="${page}">${page}</a>`;
    }
    container.appendChild(li);
  });

  // Create Next button
  const nextLi = document.createElement("li");
  nextLi.className =
    "page-item" + (currentPage === totalPages ? " disabled" : "");
  nextLi.innerHTML = `<a class="page-link" href="#" data-page="${
    currentPage + 1
  }"><i class="bi bi-chevron-right"></i></a>`;
  container.appendChild(nextLi);
}

// Function to render the product grid based on the server response
function renderProducts(responseData) {
  const gridContainer = document.getElementById("productsGrid");
  gridContainer.innerHTML = ""; // Clear previous items

  responseData.data.forEach((item) => {
    const col = document.createElement("div");
    col.className = "col";
    col.innerHTML = `
      <div class="card product-card shadow-sm mb-4 p-3">
        <div class="position-relative">
          <img src="${item.image}" class="card-img-top" alt="${item.bottle}" />
          <a href="/liqueurs/${item.id}" class="stretched-link"></a>
        </div>
        <div class="card-body">
          <h5 class="card-title mb-2">${item.bottle}</h5>
          <p class="text-danger fw-bold fs-5 mb-2">${item.value}</p>
          <p class="mb-1 text-muted">
            <small>${item.distiller} &bull; ${item.type} &bull; ${item.region}</small>
          </p>
          <p class="mb-3 text-muted">
            <small>Proof: ${item.proof} | Age: ${item.age}</small>
          </p>
          <button class="btn btn-outline-danger w-100 rounded-0 add-to-cart">Add to Cart</button>
        </div>
      </div>
    `;
    gridContainer.appendChild(col);
  });

  // Render the pagination UI using metadata from the response
  renderPagination(responseData.total_pages, responseData.current_page);
}

// --- Initial Setup on Page Load ---
document.addEventListener("DOMContentLoaded", async () => {
  // Pagination click event delegation
  document
    .getElementById("paginationContainer")
    .addEventListener("click", function (e) {
      e.preventDefault();
      const target = e.target;
      if (
        target.tagName === "A" &&
        !target.parentElement.classList.contains("disabled") &&
        !target.parentElement.classList.contains("active")
      ) {
        const newPage = target.getAttribute("data-page");
        if (newPage) {
          const url = new URL(window.location.href);
          url.searchParams.set("page", newPage);
          window.history.pushState(null, "", url.toString());
          fetchLiqueursData();
        }
      }
    });

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
