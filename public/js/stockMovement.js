
async function getIncomingStockMovementByWarehouseId(warehouseId) {
    try {
        const response = await fetch('http://localhost:8000/stock-movements/incoming/'+ warehouseId);
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const stockMovement = await response.json();
        return stockMovement;
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: `Impossible d'obtenir la liste des mouvements de stocks : erreur du serveur`,
        });
        console.error('Error fetching stock movement:', error);
        throw error; // Re-throw the error to handle it further if needed
    }
}


/* Write a botstrap modal, only the modal,  that contiainnss a form in french  to add a Reception  

two field : 

a select input of StockMvoements, "Mouvement de stock entrant "

and a number input  Quantité reçue id "quantity"




Modifier data bs dimiss the modal ,  onclick   addReception()


a second block code that contains  only the foollowing funcitons :

showAddModal()

await getstockmovements  */