document.addEventListener('DOMContentLoaded', function () {
    function handleSuccess(data) {
        const products = document.getElementById("products");
        products.innerHTML = "";

        if (Array.isArray(data)) {
            if (data.length > 0) {
                data.forEach(product => {
                    const productElement = document.createElement("div");
                    productElement.className = "product";
                    productElement.innerHTML = `
                        <h2><a href='products.php?product_id=${product.product_id}'>${product.name}</a></h2>
                        <p>Описание: ${product.description}</p>
                        <p>Цена: ${product.price}</p>
                        <img src='${product.image_path}' alt='${product.name}' width='200' height='200'>
                    `;
                    products.appendChild(productElement);
                });
            } else {
                products.innerHTML = "Нет результатов для отображения.";
            }
        } else {
            products.innerHTML = data;
        }

        hideLoadingIndicator();
    }

    function handleError(error) {
        console.error("Ошибка при запросе: " + error);
        hideLoadingIndicator();
    }

    function hideLoadingIndicator() {
        const loadingIndicator = document.getElementById("loadingIndicator");
        loadingIndicator.style.display = "none";
    }

    function showLoadingIndicator() {
        const loadingIndicator = document.getElementById("loadingIndicator");
        loadingIndicator.style.display = "block";
    }

    document.getElementById("searchButton").addEventListener("click", function () {
        console.log("Button clicked");
        const searchQuery = document.getElementById("searchBox").value;
        const categorySelect = document.getElementById("categorySelect");
        const categoryFilter = categorySelect.value;

        showLoadingIndicator();

        fetch(`search_products.php?category=${categoryFilter}&search=${searchQuery}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(handleSuccess)
            .catch(handleError);
    });
});
