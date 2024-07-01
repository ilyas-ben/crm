/** tiers.js */

function getTiers() {
    fetch('/tiers') // Assurez-vous que l'URL correspond à votre route pour récupérer les tiers
        .then(response => {
            if (!response.ok) {
                Swal.fire({
                    title: "Impossible de charger la liste des tiers",
                    text: "Une erreur inattendue s'est produite du côté du serveur",
                    icon: "error",
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    },
                });
                throw new Error(response.status);
            }
            return response.json();
        })
        .then(tiers => {
            $('#example').DataTable().destroy(); // Détruire la table DataTable existante si elle existe
            const table = document.getElementById('tiersTable');
            table.innerHTML = ''; // Réinitialiser le contenu du tableau

            tiers.forEach(tier => {
                // Construire le HTML pour une ligne avec des chaînes concaténées
                const rowHtml = '<tr>' +
                    '<td>' + tier.raisonSociale + '</td>' +
                    '<td>' + tier.address + '</td>' +
                    '<td>' + tier.phone + '</td>' +
                    '<td>' + tier.email + '</td>' +
                    '<td>' + tier.type.nameType + '</td>' +
                    /* '<td> <a href="#"  onclick="getAdditionalInfoByTiersId(idTiers)">Voir les infos. additionnelles .</a></td > ' + */
                    '<td>' +
                    '<a href="#" onclick="editTierForm(' + tier.id + ')">Modifier</a> ' +
                    '<a href="#" onclick="deleteTierById(' + tier.id + ')">Supprimer</a><br>' +
                    '<a href="#"  onclick="getAdditionalInfoByTierId(' + tier.id + ')">Infos additionnelles</a>'
                '</td>' +
                    '</tr>';

                // Ajouter le HTML de la ligne au tableau
                table.innerHTML += rowHtml;
            });

            $('#example').DataTable().destroy(); // Détruire à nouveau au cas où
            $('#example').DataTable(); // Initialiser à nouveau DataTable
        })
        .catch(error => {
            console.error('Error fetching tiers:', error);
        });
}




function addTiers() {
    // Récupération des valeurs des champs
    const raisonSociale = document.getElementById('raisonSociale').value;
    const address = document.getElementById('address').value;
    const phone = document.getElementById('phone').value;
    const email = document.getElementById('email').value;
    const typeSelect = document.getElementById("typeSelect").value;

    const tier = {
        raisonSociale: raisonSociale,
        address: address,
        phone: parseInt(phone), // Conversion en entier pour le numéro de téléphone
        email: email,
        additionalInfos: [],  // Tableau pour stocker les informations additionnelles
        type: { id: parseInt(typeSelect) }
    };

    // Récupération des valeurs des champs d'info additionnelles
    // Récupération des valeurs des champs d'info additionnelles
    $('#dynamicForm .form-row').each(function () {

        const selectValue = $(this).find('.selectField').val();
        if (selectValue != 0) {
            const inputValue = $(this).find('.inputField').val();
            const selectValue = $(this).find('.selectField').val();
            tier.additionalInfos.push({
                value: inputValue,
                additionalInfoField: { id: parseInt(selectValue) }
            });
        }
        else console.log("it's zero !");
    });

    console.log(JSON.stringify(tier));

    // Envoi de la requête POST avec Fetch
    fetch('http://localhost:8000/tiers', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(tier),
    })
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) {
                    showNotAllowed();
                    throw new Error("not allowed !");
                }
                {
                    Swal.fire({
                        title: "Impossible d'ajouter",
                        text: "Une erreur inattendue s'est produite du côté du serveur.",
                        icon: "error",
                        customClass: {
                            confirmButton: 'btn btn-danger'
                        },
                    });

                    console.log("status :".response.status);
                }

            }

        }).then(data => {
            Swal.fire({
                title: "Le client a été ajouté avec succès!",
                text: "Souhaitez-vous ajouter un autre client ?",
                icon: "success",
                confirmButtonColor: '#0d6efd',
                confirmButtonText: "Oui",
                cancelButtonColor: '#d33',
                cancelButtonText: "Non",
                showCancelButton: true
            }).then(result => {
                if (result.isConfirmed) {
                    location.href = "/tiers/add";
                }
                else {
                    location.href = '/tiers/index'
                }
            });
        })
}




async function editTierForm(id) {
    // Fetch tier data by ID from server (assuming /tiers/{id} endpoint)
    fetch('/tiers/byid/' + id)
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) {
                    showNotAllowed();
                } else {
                    Swal.fire({
                        title: 'Erreur!',
                        text: `Échec de la récupération du tiers. Statut: ${response.status}`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
                throw new Error('Failed to fetch tier');
            }
            return response.json();
        })
        .then(async tier => {


            $('#dynamicForm .form-row').remove();

            // Fill form inputs with tier data
            document.getElementById('tierId').value = tier.id;
            document.getElementById('raisonSociale').value = tier.raisonSociale;
            document.getElementById('email').value = tier.email;
            document.getElementById('address').value = tier.address;
            document.getElementById('phone').value = tier.phone;
            
            getAllTiersTypesInSelect();
            


            await $(document).ready(async function () {
                const additionalInfoFields = await getAdditionalInfoFields();

                function addRow(fieldSelectedId, valueAdditionalInfo) {
                    var newRow = `
                        <div class="form-row mb-3 addInfos">
                            <div class="col">
                                <select class="form-control selectField">
                                    <!-- Options will be dynamically added -->
                                </select>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control inputField" placeholder="Enter value">
                            </div>
                        </div>
                    `;

                    $('#dynamicForm').append(newRow);
                    // Populate options in the select field
                    const options = additionalInfoFields.map(field => `<option value="${field.id}">${field.fieldName}</option>`);
                    $('.selectField:last').html(options.join(''));
                    $('.selectField:last').append('<option value="0" selected>(Champ vide)</option>');
                    if (fieldSelectedId !== undefined) {
                        $('.selectField:last').val(fieldSelectedId);
                        $('.inputField:last').val(valueAdditionalInfo);

                        console.log($('.inputField:last'));

                    }
                    document.getElementById("typeSelect").value = tier.type.id;
                }



                // Add row button click handler
                $('#addRowBtn').on('click', function () {
                    addRow();
                });

                // Remove row button click handler
                $('#removeRowBtn').on('click', function () {
                    $('#dynamicForm .form-row:last').remove();
                });

                tier.additionalInfo.forEach(item => {
                    addRow(item.additionalInfoField.id, item.value);
                });

            });

            console.log(tier.additionalInfo);
            console.log(tier.type.id);
            

            // Display the modal
            var myModal = new bootstrap.Modal(document.getElementById('editModal'));
            await myModal.show();
        })
        .catch(error => {
            console.error('Error fetching tier:', error);
        });
}


function editTier() {
    const tierId = document.getElementById('tierId').value;
    // Récupération des valeurs des champs
    const raisonSociale = document.getElementById('raisonSociale').value;
    const address = document.getElementById('address').value;
    const phone = document.getElementById('phone').value;
    const email = document.getElementById('email').value;
    const type = document.getElementById('typeSelect').value;

    const tier = {
        raisonSociale: raisonSociale,
        address: address,
        phone: parseInt(phone), // Conversion en entier pour le numéro de téléphone
        email: email,
        additionalInfos: [],  // Tableau pour stocker les informations additionnelles
        type : {id : parseInt(type)}
    };

    // Récupération des valeurs des champs d'info additionnelles
    $('#dynamicForm .form-row').each(function () {

        const selectValue = $(this).find('.selectField').val();
        if (selectValue != 0) {
            const inputValue = $(this).find('.inputField').val();
            const selectValue = $(this).find('.selectField').val();
            tier.additionalInfos.push({
                value: inputValue,
                additionalInfoField: { id: parseInt(selectValue) }
            });
        }
        else console.log("it's zero !");
    });

    console.log(JSON.stringify(tier));
    const url = 'http://localhost:8000/tiers/' + tierId;

    // Envoyer une requête PUT pour mettre à jour le tier
    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(tier)
    })
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) {
                    showNotAllowed();
                }
                Swal.fire({
                    title: "Impossible de modifer",
                    text: "Une erreur inattendue s'est produite sur le serveur, veuillez réessayez",
                    icon: "error",
                    confirmButtonColor: "#007BFF"
                });
                var myModal = new bootstrap.Modal(document.getElementById('editModal'));
                myModal.show();
                throw new Error(response.status);
            }
            Swal.fire({
                text: "Le tier a été modifié avec succès !",
                icon: "success",
                confirmButtonColor: "#007BFF"
            });
            getTiers(); // Appeler la fonction pour récupérer à nouveau les tiers mis à jour
            return response.json();
        })
        .then(data => {
            // Afficher le message de succès et masquer la modale si nécessaire
        })
        .catch(error => {
            console.error("Erreur : ", error);
        });
}

function deleteTierById(tierId) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer le tiers n°" + tierId + " ?",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: "#d33",
        cancelButtonText: "Annuler",
        confirmButtonText: "Oui",
        confirmButtonColor: "#007bff"
    }).then((result) => {
        if (result.isConfirmed) {
            const url = `/tiers/` + tierId; // Assurez-vous que l'URL correspond à votre route pour supprimer un tiers

            fetch(url, {
                method: 'DELETE',
            })
                .then(response => {
                    if (!response.ok) {
                        if (response.status == 403) {
                            response.text().then(body => {
                                showNotAllowed(body);
                            });
                        }
                        Swal.fire({
                            title: "Impossible de suppprimer",
                            text: "Une erreur inattendue s'est produite du côté du serveur.",
                            icon: "error",
                            confirmButtonColor: "d33",
                            customClass: {
                                confirmButton: 'btn btn-danger'
                            },
                        });
                        throw new Error(`Failed to delete tier with ID ${tierId}. Status: ${response.status}`);
                    }
                    console.log(`Tier with ID ${tierId} deleted successfully.`);
                    // Mettez à jour ou rafraîchissez la liste des tiers si nécessaire

                }).then(data => {
                    Swal.fire({
                        title: "Tier supprimé !",
                        icon: "success",
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                    });
                    getTiers();
                })
                .catch(error => {
                    console.error('Error deleting tier:', error);
                });

        }
    });
}


/****************************************** AdditionalinfosHandling ****************************************** */


function getAdditionalInfoByTierId(idTiers) {
    fetch(`/tiers/additional-info/${idTiers}`)
        .then(response => {
            if (!response.ok) {
                Swal.fire({
                    title: "Impossible d'afficher les infos add.'",
                    text: "Une erreur inattendue s'est produite sur le serveur, veuillez réessayez",
                    icon: "error",
                    confirmButtonColor: "#007BFF"
                });
            }
            return response.json()
        })
        .then(data => {
            const additionalInfoTableBody = document.getElementById('additionalInfoTableBody');
            additionalInfoTableBody.innerHTML = ''; // Clear previous rows

            data.forEach(info => {
                const row = document.createElement('tr');
                row.innerHTML = `
          <td>${info.additionalInfoField.fieldName}</td>
          <td>${info.value}</td>
        `;
                additionalInfoTableBody.appendChild(row);
            });

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('additionalInfoModal'), {
                keyboard: false
            });
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching additional info:', error);
        });
}

async function getAdditionalInfoFields() {
    const response = await fetch('/additional-info-fields');
    if (!response.ok) {
        throw new Error('Failed to fetch additional info fields');
    }
    const data = await response.json();
    return data;
}

/*****************************************************  TIERTYPES **************************************************/

function getTierTypes() {
    fetch('/tierstypes')
        .then(response => {
            if (!response.ok) {
                Swal.fire({
                    title: "Erreur !",
                    text: "Impossible de charger les tier types dû à une erreur de la part du serveur, veuillez le vérifier puis rafraichir. ",
                    icon: "error"
                });
            }
            return response.json();
        })
        .then(tierTypes => {
            $('#example').DataTable().destroy();
            const table = document.getElementById('tierTypesList');
            table.innerHTML = '';

            tierTypes.forEach(tierType => {
                const rowHtml = '<tr>' +
                    '<td>' + tierType.nameType + '</td>' +
                    '<td>' +
                    '<a href="#" onclick="editTierTypeModal(' + tierType.id + ')">Modifier</a> ' +
                    '<a href="#" onclick="deleteTierType(' + tierType.id + ')">Supprim</a>' +
                    '</td>' +
                    '</tr>';

                table.innerHTML += rowHtml;
            });
            $('#example').DataTable().destroy();
            $('#example').DataTable();
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des tier types :', error);
        });
}

function addTierType() {
    const nameType = document.getElementById('tierTypeName').value;

    // Create tierType object
    const tierType = {
        nameType: nameType,
        // Add additional fields if required
    };

    console.log(tierType);

    // Send POST request to create tierType
    fetch('http://localhost:8000/tierstypes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(tierType),
    })
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) showNotAllowed();
                Swal.fire({
                    title: "Erreur de la part du serveur",
                    text: "Impossible d'ajouter le tier type maintenant, veuillez vérifier le serveur puis réessayer",
                    icon: "error",
                    confirmButtonColor: '#0d6efd'
                });

                throw new Error('Failed to add tier type' + response.status);
            }

            return response.json();
        })
        .then(data => {
            console.log('Tier Type added successfully:', data);
            Swal.fire({
                title: "Le tier type a été ajouté avec succès !",
                text: "Souhaitez-vous ajouter un autre tier type ?",
                icon: "success",
                confirmButtonColor: '#0d6efd',
                confirmButtonText: "Oui",
                denyButtonColor: '#d33',
                showDenyButton: true,
                denyButtonText: "Non"
            }).then(result => {
                if (result.isConfirmed) {
                    location.href = "/tierstypes/add";
                } else {
                    location.href = "/tierstypes/index";
                }
            });
        })
        .catch(error => {
            console.error('Error adding tier type:', error);
            // Optionally, handle error (e.g., show an error message)
        });
}

async function editTierTypeModal(tierTypeId) {
    await fetch(`http://localhost:8000/tierstypes/byid/` + tierTypeId)
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) showNotAllowed();
                Swal.fire({
                    title: "Erreur de la part du serveur",
                    text: "Impossible de modifier le tier type maintenant, veuillez vérifier le serveur puis réessayer",
                    icon: "error",
                    confirmButtonColor: '#0d6efd'
                });
            }
            return response.json();
        })
        .then(
            async data => {
                const idInput = document.getElementById('tierTypeId');
                idInput.value = data.id;

                const editTierTypeNameInput = document.getElementById('editTierTypeName');
                editTierTypeNameInput.value = data.nameType;

                document.getElementById("editTierTypeModalLabel").innerHTML = "Modifier le tier type " + data.nameType;
               

                var myModal = new bootstrap.Modal(document.getElementById('editTierTypeModal'));
                myModal.show();

                // Additional code to handle other fields if necessary
            })
        .catch(error => console.error('Error fetching tier type:', error));
}

function editTierType() {
    const id = document.getElementById('tierTypeId').value;
    const nameType = document.getElementById('editTierTypeName').value;

    const tierType = {
        nameType: nameType,
        // Add additional fields if required
    };

    const url = "http://localhost:8000/tierstypes/" + id;
    const tierTypeJson = JSON.stringify(tierType);
    console.log(tierTypeJson);

    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: tierTypeJson
    })
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) showNotAllowed();
                else {
                    Swal.fire({
                        icon: "error",
                        title: "Une erreur est survenue avec le serveur",
                        text: "Veuillez réessayer",
                        confirmButton: "#007bff"
                    });
                }
                new bootstrap.Modal(document.getElementById('editTierTypeModal')).show();
                throw new Error();
            }
            return response.json();
        })
        .then(data => {
            getTierTypes();
            console.log('Success:', data);
            Swal.fire({
                title: "Tier Type a été modifié avec succès !",
                icon: "success",
                confirmButtonColor: "#007bff"
            });
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}

async function deleteTierType(tierTypeId) {
    const url = `http://localhost:8000/tierstypes/${tierTypeId}`;

    await fetch(url, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        }
    })
        .then(response => {
            if (!response.ok) {
                Swal.fire({
                    title: "Erreur de la part du serveur",
                    text: "Impossible de supprimer le type de tier, veuillez vérifier le serveur puis réessayer",
                    icon: "error",
                    confirmButtonColor: '#0d6efd'
                });
                throw new Error('Erreur interne du serveur');
            }
            return response.json();
        })
        .then(data => {
            Swal.fire({
                title: "Succès",
                text: "Le type de tier a été supprimé avec succès.",
                icon: "success",
                confirmButtonColor: '#0d6efd'
            });
            getTierTypes();
        })
        .catch(error => {
            console.error('Erreur lors de la suppression du type de tier :', error);
        });
}

async function getAllTiersTypesInSelect(actualType=null) {

    const url = "http://localhost:8000/tierstypes";

    await fetch(url)
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) showNotAllowed();
                Swal.fire({
                    title: "Erreur de la part du serveur",
                    text: "Impossible d'afficher les types de tiers pour le moment, veuillez vérifier le serveur puis réessayer",
                    icon: "error",
                    confirmButtonColor: '#0d6efd'
                });
                throw new Error(response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            typeSelect = document.getElementById("typeSelect");
            typeSelect.innerHTML = "";

            data.forEach(tierType => {
                const option = document.createElement('option');
                option.textContent = tierType.nameType;
                option.value = tierType.id;
                typeSelect.appendChild(option);
            })

            if(actualType!=null) { document.getElementById("typeSelect").value = actualType;}
        })
        .catch(error => {
            console.error("Erreur pour les profil ", error);
        })

    return 1;
}

