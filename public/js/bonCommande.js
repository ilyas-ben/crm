async function getAllBonCommandes() {
    try {
        const response = await fetch('http://localhost:8000/bon-commandes');
        if (!response.ok) {
            throw new Error('Failed to fetch BonCommandes');
        }
        const bonCommandes = await response.json();
        console.log("get : " + response);
        return bonCommandes;
    } catch (error) {
        console.error('Error fetching BonCommandes:', error.message);
        return null;
    }
}

async function addBonCommande() {
    let bonCommande = {
        orderNumber: 123456789, // Example hexdec(uniqid())
        fournisseur: {
            id: $('#supplier').val() // Replace with actual fournisseur ID
        },
        orderDate: new Date($('#orderDate').val()), // Example date format
        isFullyReceived: false, // Example boolean value
        notes: $('#notes').val(), // Example notes text
        items : []
    }
    await $('#dynamicForm .form-row').each(function () {
        if ($(this).find('.consider:first').prop('checked')) {

            const item = {
                product: { id: $(this).find('.productId:first').val() },
                quantity: parseInt($(this).find('.quantity:first').val()),
                designation: $(this).find('.designation:first').val(),
                label: $(this).find('.label:first').val()
            }

            bonCommande.items.push(item);
        }
    });

    console.log(bonCommande);

    await fetch('http://localhost:8000/bon-commandes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(bonCommande)
    })
        .then(response => {
            if (!response.ok) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur d\'ajout',
                    text: 'Erreur de la part du serveur'
                });
                throw new Error('Server error');
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Bon commande ajouté !',
                    text: 'Ajouter un autre bon de commande ?',
                    showCancelButton: true,
                    confirmButtonText: 'Oui',
                    cancelButtonText: 'Non'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If user clicks "Oui"
                        window.location.href = 'http://localhost:8000/bon-commandes/addPage';
                    } else {
                        // If user clicks "Non" or closes the dialog
                        window.location.href = 'http://localhost:8000/bon-commandes/index';
                    }
                });

            }
        })
        .catch(error => {
            console.error('Error:', error);
        });

}


function displayBonCommandeInTable(bonCommandes) {
    const bonCommandeList = document.getElementById('bonCommandeList');
    bonCommandeList.innerHTML = ''; // Clear previous content
    console.log("display : " + bonCommandes);
    bonCommandes.forEach(bonCommande => {
        const tr = document.createElement('tr');

        const numeroBon = document.createElement('td');
        numeroBon.textContent = bonCommande.orderNumber;

        const fournisseur = document.createElement('td');
        fournisseur.textContent = bonCommande.fournisseur.raisonSociale;

        const dateEmission = document.createElement('td');
        dateEmission.textContent = bonCommande.orderDate;

        const receptionTotale = document.createElement('td');
        receptionTotale.textContent = bonCommande.isFinished ? 'Oui' : 'Non';

        const elementsCommande = document.createElement('td');
        const elementsLink = document.createElement('a');
        elementsLink.href = '#'; // Replace with actual href if needed
        elementsLink.textContent = 'Voir les éléments'; // Replace with appropriate text
        elementsLink.onclick = displayItems(bonCommande);
        elementsLink.setAttribute('data-bs-toggle', 'modal');
        elementsLink.setAttribute('data-bs-target', '#detailsBonCommandeModal');
        elementsCommande.appendChild(elementsLink);
        /** Rani mride bzzf  o9ssim billah,  validi lia ou nmchi f7ali */
        tr.appendChild(numeroBon);
        tr.appendChild(fournisseur);
        tr.appendChild(dateEmission);
        tr.appendChild(receptionTotale);
        tr.appendChild(elementsCommande);

        bonCommandeList.appendChild(tr);
    });
}

function displayItems(bonCommande) {
    // Assuming bonCommande.items is an array of DetailsBonCommande objects
    var tbody = document.getElementById('detailsBonCommandeTableBody');
    tbody.innerHTML = ''; // Clear existing rows

    console.log(bonCommande.items);

    bonCommande.items.forEach(function (item) {
        var row = '<tr>' +
            '<td>' + (item.produit ? item.produit.name : 'N/A') + '</td>' +
            '<td>' + item.quantity + '</td>' +
            '<td>' + (item.purchaseUnit ? item.purchaseUnit.name : 'N/A') + '</td>' +
            '<td>';

        if (item.additionalInfos.length > 0) {
            row += '<ul>';
            item.additionalInfos.forEach(function (info) {
                row += '<li><b>' + info.additionalInfoField.fieldName + ': </b>' + info.value + '</li>';
            });
            row += '</ul>';
        } else {
            row += 'N/A';
        }

        row += '</td>' +
            '</tr>';

        tbody.innerHTML += row;
    });
}

