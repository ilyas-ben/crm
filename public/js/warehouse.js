// warehouse.js

$(document).ready(function () {
    $("#searchId").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#warehouseList tr").filter(function () {
            $(this).toggle($(this).find("td:first").text().toLowerCase().indexOf(value) > -1);
        });
    });
});

$(document).ready(function () {
    $("#searchName").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#warehouseList tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

// Function to fetch and display warehouses
async function getWarehouses() {
    try {
        const response = await fetch('/warehouses');
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        const data = await response.json();
        return data;
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: "Impossible d'obtenir la liste des entrepôts."
        });
        console.error('Error fetching warehouses:', error);
        return undefined;
    }
}



// Function to display warehouses in the table
async function displayWarehouses() {
    const wareHouses = await getWarehouses();
    var warehouseTableBody = $('#warehouseList');
    warehouseTableBody.empty();

    wareHouses.forEach(function (warehouse) {
        var row = $('<tr>');
        row.append('<td>' + warehouse.label + '</td>');
        row.append('<td>' + warehouse.addressLine + '</td>');
        row.append('<td>' + warehouse.city + '</td>');
        row.append('<td><a href="' + warehouse.id + '/stocks">Consulter stocks</a></td>');
        warehouseTableBody.append(row);
    });
}

// Function to fetch warehouse details and populate edit modal
function editWarehouseModal(id) {
    $.ajax({
        url: '/warehouses/byid/' + id,
        method: 'GET',
        success: function (warehouse) {
            $('#editWarehouseId').val(warehouse.id);
            $('#editWarehouseLabel').val(warehouse.label);
            $('#editWarehouseAddress').val(warehouse.addressLine);
            $('#editWarehouseCity').val(warehouse.city);
            $('#editWarehouseModal').modal('show');
        },
        error: function (error) {
            console.error('Error fetching warehouse details:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur s\'est produite lors de la récupération des détails de l\'entrepôt.'
            });
            $('#editWarehouseModal').modal('show');
        }
    });
}


async function getWareHouseById(warehouseId) {

    const response = await fetch("/warehouse/byid/" + warehouseId)

    const warehouse = await response.json();

    return warehouse;

}

// Function to update warehouse via AJAX PUT request
function updateWarehouse() {
    var warehouseId = $('#editWarehouseId').val();
    var warehouseLabel = $('#editWarehouseLabel').val();
    var warehouseAddress = $('#editWarehouseAddress').val();
    var warehouseCity = $('#editWarehouseCity').val();

    var warehouseData = {
        id: warehouseId,
        label: warehouseLabel,
        addressLine: warehouseAddress,
        city: warehouseCity
    };

    $.ajax({
        url: '/warehouses/' + warehouseId,
        method: 'PUT',
        contentType: 'application/json',
        data: JSON.stringify(warehouseData),
        success: function () {
            Swal.fire({
                icon: 'success',
                title: 'Entrepôt Modifié',
                text: 'L\'entrepôt a été modifié avec succès.'
            });
            getWarehouses(); // Refresh the warehouse list after modification
        },
        error: function (error) {
            console.error('Error updating warehouse:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Une erreur s\'est produite lors de la modification de l\'entrepôt.'
            });
        }
    });
}

function displayWarehouseReceptions(warehouseId) {
    fetch(`/receptions/destination/${warehouseId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const receptionList = document.getElementById('receptionList');
            receptionList.innerHTML = ''; // Clear previous data

            data.forEach(reception => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${reception.product.name}</td>
                    <td>${reception.date}</td>
                    <td>${reception.quantity}</td>
                    <td>${reception.origin.label}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="editReception(${reception.id})">Modifier</button>
                    </td>
                `;
                receptionList.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error fetching receptions:', error);
            // Handle error scenario (e.g., show error message)
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: "Une erreur s'est produite lors du chargement des réceptions."
            });
        });
}


async function getStocksMovementsByWarehouseId(warehouseId) {
    try {
        const response = await fetch(`http://localhost:8000/stock-movements/bywarehouseid/${warehouseId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return await response.json();
    } catch (error) {
        console.error('There was a problem with the fetch operation:', error);
    }
}

async function displaySMModal(warehouseId) {
    const modal = document.getElementById('stockMovementsModal');
    console.log(document.getElementById('stockMovementsData'))
    const tableBody = document.getElementById('stockMovementsData');
    tableBody.innerHTML = '';
    const data = await getStocksMovementsByWarehouseId(warehouseId);
    data.forEach(sm => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${sm.date}</td>
            <td>${sm.product.name}</td>
            <td>${sm.quantity}</td>
            <td>${sm.origin && sm.origin.id === warehouseId ? 'Cet entrepôt' : sm.origin ? sm.origin.label : 'N/A'}</td>
            <td>${sm.destination && sm.destination.id === warehouseId ? 'Cet entrepôt' : sm.destination.label}</td>
        `;
        tableBody.appendChild(row);
    });
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
}

async function getReceptionsByWarehouseId(warehouseId) {
    try {
        const response = await fetch(`http://localhost:8000/warehouse-receptions/destination/${warehouseId}`);
        if (!response.ok) {
            Swal.fire({
                icon: 'error',
                title: "Impossible de charger les réceptions de cet entrepôt",
                text: "Erreur de la part du serveur"

            });

            throw new Error('Network response was not ok');
        }
        data = await response.json();
        console.log(data);
        return data;

    } catch (error) {
        console.error('There was a problem with the fetch operation:', error);
        return []; // Return an empty array or handle the error as appropriate
    }
}

async function displayReceptions(data) {
    const receptionList = document.getElementById('receptionList');
    receptionList.innerHTML = ''; // Clear previous content
    console.log(data[0].stockMovement);
    try {
        data.forEach(reception => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${reception.product.name}</td>
                <td>${reception.date}</td>
                <td>${reception.quantity}</td>
                <td>${reception.stockMovement.origin ? reception.stockMovement.origin.label : 'N/A'}</td>
                <td>${!reception.receptionBonCommande ? 'Transfert d\'entrepôt' : 'Bon de Commande N°X'}</td>
                <td>    <a href="#" onclick="displayReceptionDetailsModal(${reception})">Détails</a> </td>
            `;
            receptionList.appendChild(row);
        });
    } catch (error) {
        console.error('Error fetching receptions:', error);
        // Optionally handle the error or display a message to the user
    }
}

async function showAddRFormModal(warehouseId) {
    const data = await getIncomingStockMovementByWarehouseId(warehouseId);
    const stockMovementSelect = document.getElementById('stockMovementSelect');

    // Clear existing options
    stockMovementSelect.innerHTML = '';

    // Populate select options
    data.forEach(movement => {
        const option = document.createElement('option');
        option.value = movement.id;
        option.textContent = `${movement.quantity} ${movement.product.name} ${movement.date} restant: ${movement.remainingNotArrivedStock}  `;
        stockMovementSelect.appendChild(option);
    });

    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('addReceptionModal'));
    modal.show();
}


async function addReception(warehouseId) {
    const stockMovementId = document.getElementById('stockMovementSelect').value;
    const quantity = document.getElementById('quantityReceived').value;

    const reception = {
        stockMovementId: stockMovementId,
        quantity: quantity
    };

    try {
        const response = await fetch('http://localhost:8000/warehouse-receptions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(reception)
        });

        if (!response.ok) {
            const modal = new bootstrap.Modal(document.getElementById('addReceptionModal'));
            modal.show();
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: `Impossible d'enregistrer une réception : erreur du serveur`
            });
            throw new Error('Network response was not ok ' + response.statusText);
        }

        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: 'La réception a été enregistrée !'
        });

        // Display updated receptions
        displayReceptions(await getReceptionsByWarehouseId(warehouseId));
    } catch (error) {
        console.error('Error adding reception:', error);

    }
}

async function displayIncomingSMInModal(warehouseID) {
    const data = await getIncomingStockMovementByWarehouseId(warehouseID);
    const tbody = document.getElementById('incomingStockMovements');

    // Clear existing table rows
    tbody.innerHTML = '';

    // Populate table rows
    data.forEach(movement => {
        if (!movement.isFinished) {
            const tr = document.createElement('tr');

            const productNameTd = document.createElement('td');
            productNameTd.textContent = movement.product.name;
            tr.appendChild(productNameTd);

            const quantityTd = document.createElement('td');
            quantityTd.textContent = movement.quantity;
            tr.appendChild(quantityTd);

            const dateTd = document.createElement('td');
            dateTd.textContent = movement.date;
            tr.appendChild(dateTd);


            console.log(movement.origin);
            const senderWarehouseTd = document.createElement('td');
            senderWarehouseTd.textContent = movement.origin ? movement.origin.label : "Reception bon de commande" ;

            tr.appendChild(senderWarehouseTd);

            const quantityNotReceivedTd = document.createElement('td');
            quantityNotReceivedTd.textContent = movement.remainingNotArrivedStock;
            tr.appendChild(quantityNotReceivedTd);

            const completedTd = document.createElement('td');
            completedTd.textContent = movement.isFinished ? 'Oui' : 'Non';
            tr.appendChild(completedTd);

            tbody.appendChild(tr);
        }
    });

    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('incomingStockMovementsModal'));
    modal.show();
}

function displayReceptionDetailsModal(r) {
    document.getElementById('productName').textContent = r.sm.product.name;
    document.getElementById('quantity').textContent = r.sm.quantity;
    document.getElementById('originLabel').textContent = r.sm.origin.label;
    document.getElementById('date').textContent = r.sm.date;
    document.getElementById('isFinished').textContent = r.sm.isFinished ? 'Oui' : 'Non';
    document.getElementById('remainingNotArrivedStock').textContent = r.sm.remainingNotArrivedStock;
    document.getElementById('receptionBonCommande').textContent = r.sm.receptionBonCommandev;

    // Display the modal
    $('#stockMovementModal').modal('show');
}
