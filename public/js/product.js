// product.js

$(document).ready(function () {
    $("#searchId").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#productList tr").filter(function () {
            $(this).toggle($(this).find("td:first").text().toLowerCase().indexOf(value) > -1);
        });
    });
});

$(document).ready(function () {
    $("#searchName").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#productList tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

function addProduct() {
    const name = document.getElementById('name').value;
    const price = document.getElementById('price').value;
    const description = document.getElementById('description').value;
    const categoryId = document.getElementById('categorie').value;

    const product = {
        name: name,
        price: parseFloat(price),
        description: description,
        category: {
            id: parseInt(categoryId)
        }
    };

    fetch('http://localhost:8000/products', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(product),
    })
        .then(response => {
            if (!response.ok) {
                Swal.fire({
                    title: 'Erreur!',
                    text: 'Erreur de serveur inattendue',
                    icon: 'error',
                });

                // Show the modal if an error occurs
                const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
                modal.show();
                throw new Error('Erreur de serveur inattendue');
            }
            return response.json();
        })
        .then(async data => {
            Swal.fire({
                title: 'Produit ajouté avec succès',
                icon: 'success',
            });

            await displayProducts();
        })
        .catch(error => {
            console.error('Error:', error);
        });

}


// Function to fetch and display products
async function getProducts() {
    try {
        const response = await fetch('/products');
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        const data = await response.json();
        return data;
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: "Impossible d'obtenir la liste des produits."
        });
        console.error('Error fetching products:', error);
    }
}


// Function to display products in the table
async function displayProducts() {
    const products = await getProducts();
    var productTableBody = $('#productList');
    productTableBody.empty();

    products.forEach(function (product) {
        var row = $('<tr>');
        row.append('<td>' + product.name + '</td>');
        row.append('<td>' + product.price + '</td>');
        row.append('<td>' + product.description + '</td>');
        row.append('<td>' + product.category.name + '</td>'); // Assuming category has a 'name' property
        row.append('<td><a href="#" onclick="editProductModal(' + product.id + ')">Modifier</a></td>');
        row.append('<td><a href="#" onclick="displayUnitModal(' + product.id + ')">Unité de vente</a></td>');
        productTableBody.append(row);
    });
}

// Function to fetch product details and populate edit modal
function editProductModal(id) {
    $.ajax({
        url: '/products/byid/' + id,
        method: 'GET',
        success: async function (product) {
            const categories = await getCategories();
            $('#editProductId').val(product.id);
            $('#editProductName').val(product.name);
            $('#editProductPrice').val(product.price);
            $('#editProductDescription').val(product.description);
            // Populate category dropdown (example)
            var categorySelect = $('#editProductCategory');
            categorySelect.empty();
            categories.forEach(function (category) {
                categorySelect.append($('<option>').text(category.name).attr('value', category.id));
            });

            categorySelect.val(product.category.id)
            $('#editProductModal').modal('show');
        },
        error: function (error) {
            console.error('Error fetching product details:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur s\'est produite lors de la récupération des détails du produit.'
            });
            $('#editProductModal').modal('show');
        }
    });
}

// Function to update product via AJAX PUT request
function updateProduct() {
    var productId = $('#editProductId').val();
    var productName = $('#editProductName').val();
    var productPrice = $('#editProductPrice').val();
    var productDescription = $('#editProductDescription').val();
    var productCategoryId = $('#editProductCategory').val();

    var productData = {
        id: productId,
        name: productName,
        price: productPrice,
        description: productDescription,
        categoryId: productCategoryId
    };

    $.ajax({
        url: '/products/' + productId,
        method: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify(productData),
        success: function () {
            Swal.fire({
                icon: 'success',
                title: 'Produit Modifié',
                text: 'Le produit a été modifié avec succès.'
            });
            getProducts(); // Refresh the product list after modification
        },
        error: function (error) {
            console.error('Error updating product:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur s\'est produite lors de la modification du produit.'
            });
        }
    });
}

function editUnit() {
    fetch('http://localhost:8000/units/' + $('#unitId').val(), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            conversionRate: $('#conversionRate').val(),
            id: $('#unitId').val()
        })
    }).then(response => {
            if (!response.ok) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur de modification de l\'unité',
                    text:  'Erreur de la part du serveur'
                });

                // Show the unit modal again
                var myModal = new bootstrap.Modal(document.getElementById('unitModal'));
                myModal.show();

                throw new Error();
            }
            return response.json();
        })
        .then(data => {
            console.log('Success:', data);
            Swal.fire({
                icon: 'success',
                title: 'Unité modifiée',
                text: 'L\'unité a été modifiée avec succès.'
            });
        })
        .catch((error) => {
            console.error('Error:', error);

        });
}

async function displayUnitModal(id) {
    try {
        const response = await fetch(`http://localhost:8000/units/byProductId/${id}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const unitData = await response.json();

        // Populate the modal with the fetched data
        $('#unitId').val(unitData.id);
        $('#conversionRate').val(unitData.conversionRate);

        // Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('unitModal'));
        myModal.show();
    } catch (error) {
        console.error('Error:', error);
    }
}
