$(document).ready(function () {
    $("#searchId").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#categoryList tr").filter(function () {
            $(this).toggle($(this).find("td:first").text().toLowerCase().indexOf(value) > -1);
        });
    });
});

$(document).ready(function () {
    $("#searchName").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#categoryList tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

// Function to fetch and display categories
async function getCategories() {
    try {
        const response = await fetch('/categories', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Unable to fetch categories.');
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching categories:', error);
        Swal.fire({
            title: "Erreur de la part du serveur",
            text: "Impossible d'obtenir la liste des catégories",
            icon: "error"
        });
    }
}


// Function to display categories in the table
async function displayCategories() {
    const categories = await getCategories();
    var categoryTableBody = $('#categoryList');
    categoryTableBody.empty();

    categories.forEach(function (category) {
        var row = $('<tr>');
        row.append('<td>' + category.name + '</td>');
        row.append('<td><button class="btn btn-primary" onclick="editCategoryModal(' + category.id + ')">Modifier</button></td>');
        categoryTableBody.append(row);
    });
}

// Function to fetch category details and populate edit modal
function editCategoryModal(id) {
    $.ajax({
        url: '/categories/byid/' + id,
        method: 'GET',
        success: function (category) {
            $('#editCategoryId').val(category.id);
            $('#editCategoryName').val(category.name);
            $('#editCategoryModal').modal('show');
        },
        error: function (error) {
            console.error('Error fetching category details:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur s\'est produite lors de la récupération des détails de la catégorie.'
            });
            $('#editCategoryModal').modal('show');
        }
    });
}

// Function to update category via AJAX PUT request
function updateCategory() {
    var categoryId = $('#editCategoryId').val();
    var categoryName = $('#editCategoryName').val();

    var categoryData = {
        id: categoryId,
        name: categoryName
    };

    $.ajax({
        url: '/categories/' + categoryId,
        method: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify(categoryData),
        success: function () {
            Swal.fire({
                icon: 'success',
                title: 'Categorie Modifiée',
                text: 'La catégorie a été modifiée avec succès.'
            });
            displayCategories();
        },
        error: function (error) {
            console.error('Error updating category:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur s\'est produite lors de la modification de la catégorie.'
            });
        }
    });
}

function addCategory() {
    const name = $("#name").val();  // Assuming you are using jQuery to get the input value

    const data = {
        name: name
    };

    fetch('http://localhost:8000/categories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then(response => {
            if (!response.ok) {
                Swal.fire({
                    title: 'ERREUR!',
                    text: 'Erreur inattendue du serveur',
                    icon: 'error',
                });
                throw new Error('Unexpected server error');
            }
            displayCategories();
            Swal.fire({
                title: 'Catégorie ajoutée avec succès',
                icon: 'success',
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
}




