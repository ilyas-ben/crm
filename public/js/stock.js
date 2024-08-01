$(document).ready(function () {
    $("#searchId").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#stockList tr").filter(function () {
            $(this).toggle($(this).find("td:first").text().toLowerCase().indexOf(value) > -1);
        });
    });

    $("#searchProductName").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#stockList tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

// Function to fetch and display stocks
async function getStocks() {
    try {
        const response = await fetch('/stocks');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data; // Return the stocks data
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: "Impossible d'obtenir la liste des stocks."
        });
        console.error('Error fetching stocks:', error);
        return []; // Return an empty array or handle error as needed
    }
}

// fetch stock by ware house id
async function getStocksByWarehouseId(warehouseId) {
    try {
        const response = await fetch(`http://localhost:8000/stocks/bywarehouseid/${warehouseId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            Swal.fire("Impossible de décharger le stock de l'entrepôt, erreur du serveur. Veuillez réessayer. :  " + response.status);
            throw new Error("status code" + response.status);
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching data:', error);
        return null; // or handle error as needed
    }
}

// Function to display stocks in the table
async function displayStocks(stocks) {


    var stockTableBody = $('#stockList');
    console.log(stockTableBody);
    stockTableBody.empty();

    stocks.forEach(function (stock) {
        var row = $('<tr>');
        row.append('<td>' + stock.product.name + '</td>');
        row.append('<td>' + stock.quantity + '</td>');
        row.append('<td><a href="#" onclick="editStockModal(' + stock.id + ')">Modifier</a><a href="#" onclick="transfertStockForm(' + stock.id + ')">Transférer</a></td>');
        stockTableBody.append(row);
    });

    return 1;
}

// Function to fetch stock details and populate edit modal
function editStockModal(id) {
    $.ajax({
        url: '/stocks/byid/' + id,
        method: 'GET',
        success: async function (stock) {
            const products = await getProducts();
            const warehouses = await getWarehouses();

            $('#editStockId').val(stock.id);
            // Populate product dropdown
            var productSelect = $('#editStockProduct');
            productSelect.empty();
            products.forEach(function (product) {
                productSelect.append($('<option>').text(product.name).attr('value', product.id));
            });
            productSelect.val(stock.product.id);

            // Populate warehouse dropdown
            var warehouseSelect = $('#editStockWareHouse');
            warehouseSelect.empty();
            warehouses.forEach(function (warehouse) {
                warehouseSelect.append($('<option>').text(warehouse.label).attr('value', warehouse.id));
            });
            warehouseSelect.val(stock.wareHouse.id);

            $('#editStockQuantity').val(stock.quantity);
            $('#editStockModal').modal('show');
        },
        error: function (error) {
            console.error('Error fetching stock details:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur s\'est produite lors de la récupération des détails du stock.'
            });
        }
    });
}

// Function to update stock via AJAX PUT request
function updateStock() {
    var stockId = $('#editStockId').val();
    var productId = $('#editStockProduct').val();
    var warehouseId = $('#editStockWareHouse').val();
    var quantity = $('#editStockQuantity').val();

    var stockData = {
        id: stockId,
        product: { id: parseInt(productId) },
        wareHouse: { id: parseInt(warehouseId) },
        quantity: quantity
    };

    $.ajax({
        url: '/stocks/' + stockId,
        method: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify(stockData),
        success: async function () {
            Swal.fire({
                icon: 'success',
                title: 'Stock Modifié',
                text: 'Le stock a été modifié avec succès.'
            });
            console.log(stocks);
            stocks = await getStocks();
            displayStocks(stocks); // Refresh the stock list after modification
        },
        error: function (error) {
            console.error('Error updating stock:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur s\'est produite lors de la modification du stock.'
            });
        }
    });

}
function displayTransfertModal() {
    const stocksSelect = document.getElementById('stocks');
    const warehousesSelect = document.getElementById('warehouses');

    console.log(stocks);

    function populateSelectStock(selectElement, options) {
        selectElement.innerHTML = options.map(option =>
            `<option value="${option.id}">${option.product.name}, ${option.quantity}, ${option.wareHouse.label}</option>`
        ).join('');
    }

    function populateSelectwareHouse(selectElement, options) {
        selectElement.innerHTML = options.map(option =>
            `<option value="${option.id}"> ${option.label}</option>`
        ).join('');
    }
    // Populate stocks select options
    populateSelectStock(stocksSelect, stocks);

    // Call function to get warehouses and populate options
    getWarehouses().then(warehouses => {
        populateSelectwareHouse(warehousesSelect, warehouses);
    }).catch(error => {
        console.error('Error fetching warehouses:', error);
    });

    // Show the modal (assuming you have Bootstrap modal already)
    //        $('#transferModal').modal('show');
}

async function transfertStockForm(stockId) {

    // Fill #stocks select input
    let stocksSelect = $("#stocksToTransfer");
    stocksSelect.empty(); // Clear any existing options

    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.id = 'stockId';
    hiddenInput.name = 'stockId';
    hiddenInput.value = stockId;

    document.getElementById('transfertForm').appendChild(hiddenInput);

    // Fill #warehouseDestination select input
    let warehouseSelect = $("#warehouseDestination");
    warehouseSelect.empty(); // Clear any existing options


    let warehouses = await getWarehouses();
    console.log(warehouses);
    warehouses.forEach(warehouse => {
        let option = $("<option></option>")
            .attr("value", warehouse.id)
            .text(warehouse.label); // Assuming warehouses have id and name properties
        warehouseSelect.append(option);
    });

    new bootstrap.Modal(document.getElementById('transferStockModal')).show();

}



function transfertStock() {
    // Retrieve form data
    const stockId = document.getElementById('stockId').value;
    const quantityToTransfer = document.getElementById('quantityToTransfer').value;
    const warehouseDestinationId = document.getElementById('warehouseDestination').value;

    // Construct the request body object
    const parameters = {
        stockId: parseInt(stockId),
        quantityToTransfer: parseInt(quantityToTransfer),
        warehouseDestinationId: parseInt(warehouseDestinationId),
    };

    console.log(parameters);

    // Perform the fetch request
    fetch('http://localhost:8000/stocks/transfert', {
        method: 'post',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(parameters),
    })
        .then(response => {
            if (!response.ok) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: "Une erreur s'est produite lors du transfert de stock."
                });
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(async data => {
            // Handle success response here if needed
            Swal.fire({ icon: "success", title: "Transfert initié !", text: "N'oubliez pas de préciser les réception." });
            stocks = await getStocks();
            displayStocks(stocks);
        })
        .catch(error => {
            console.error('Error transferring stock:', error);
            // Handle error scenario (e.g., show error message)

        });
}








