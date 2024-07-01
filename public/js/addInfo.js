async function getAdditionalInfoFields() {
    let addFields ;
    await fetch('/additional-info-fields')
        .then(response => {
            if (!response.ok) {
                Swal.fire({
                    title: "Impossible de charger la liste des champs d'information supplémentaire",
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
        .then(fields => {
            $('#example').DataTable().destroy();
            const table = document.getElementById('additionalInfoFieldsTable');
            table.innerHTML = '';

            fields.forEach(field => {
                const rowHtml = '<tr>' +
                    '<td>' + field.fieldName + '</td>' +
                    '<td>' + field.entityType + '</td>' +
                    '<td>' +
                    '<a href="#" onclick="editAdditionalInfoFieldForm(' + field.id + ')">Modifier</a> ' +
                    '<a href="#" onclick="deleteAdditionalInfoFieldById(' + field.id + ')">Supprimer</a>' +
                    '</td>' +
                    '</tr>';

                table.innerHTML += rowHtml;
            });

            $('#example').DataTable().destroy();
            $('#example').DataTable();
            addFields = fields;
            
        })
        .catch(error => {
            console.error('Error fetching additional info fields:', error);
        });
        console.log(addFields);
        return addFields;
}

function addAdditionalInfoField() {
    const fieldName = document.getElementById('fieldName').value;
    const entityType = document.getElementById('entityType').value;

    const field = {
        fieldName: fieldName,
        entityType: parseInt(entityType)
    };

    fetch('http://localhost:8000/additional-info-fields', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(field),
    })
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) {
                    showNotAllowed();
                    throw new Error("not allowed !");
                }
                Swal.fire({
                    title: "Erreur inattendue du serveur",
                    text: "Une erreur inattendue s'est produite du côté du serveur.",
                    icon: "error",
                    customClass: {
                        confirmButton: 'btn btn-danger'
                    },
                });
                console.log("status :" + response.status);
            }
            return response.json();
        })
        .then(data => {
            Swal.fire({
                title: "Le champ d'information supplémentaire a été ajouté avec succès !",
                text: "Souhaitez-vous ajouter un autre champ ?",
                icon: "success",
                confirmButtonColor: '#0d6efd',
                confirmButtonText: "Oui",
                cancelButtonColor: '#d33',
                cancelButtonText: "Non",
                showCancelButton: true
            }).then(result => {
                if (result.isConfirmed) {
                    location.href = "/additional-info-fields/add";
                } else {
                    location.href = '/additional-info-fields/index'
                }
            });
        })
        .catch(error => {
            console.error('Error adding additional info field:', error);
        });
}


function deleteAdditionalInfoFieldById(fieldId) {
    Swal.fire({
        title: "Attention !",
        text: "La suppression de ce champ va entrainer la suppression de ses valeurs auprès des clients qui ont des information supplémentaire de ce type de champ, Voulez-vous continuer ?",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: "#d33",
        cancelButtonText: "Annuler",
        confirmButtonText: "Oui",
        confirmButtonColor: "#007bff"
    }).then((result) => {
        if (result.isConfirmed) {
            const url = `/additional-info-fields/` + fieldId;

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
                            title: "Impossible de supprimer le champ.",
                            text: "Une erreur inattendue de la part du serveur",
                            icon: "error",
                            confirmButtonColor: "#007BFF"
                        });

                        throw new Error(`Failed to delete additional info field with ID ${fieldId}. Status: ${response.status}`);
                    }
                    console.log(`Additional info field with ID ${fieldId} deleted successfully.`);
                })
                .then(data => {
                    Swal.fire({
                        title: "Champ d'information supplémentaire supprimé !",
                        icon: "success",
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                    });
                    getAdditionalInfoFields(); // Rafraîchir la liste des champs après suppression
                })
                .catch(error => {
                    console.error('Error deleting additional info field:', error);
                });
        }
    });
}

async function editAdditionalInfoFieldForm(id) {
    fetch('/additional-info-fields/byid/' + id)
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) {
                    showNotAllowed();
                }
                Swal.fire({
                    title: "Impossible d'obtenir les informations du champ.",
                    text: "Une erreur inattendue de la part du serveur",
                    icon: "error",
                    confirmButtonColor: "#007BFF"
                });
                throw new Error('Failed to fetch additional info field');
            }
            return response.json();
        })
        .then(async field => {
            // Remplir les champs du formulaire avec les données du champ d'information supplémentaire
            document.getElementById('fieldId').value = field.id;
            document.getElementById('fieldName').value = field.fieldName;
            document.getElementById('entityType').value = field.entityType;

            // Afficher le modal d'édition
            var myModal = new bootstrap.Modal(document.getElementById('editModal'));
            await myModal.show();
        })
        .catch(error => {
            console.error('Error fetching additional info field:', error);
        });
}

function editAdditionalInfoField() {
    var fieldId = document.getElementById('fieldId').value;
    var fieldName = document.getElementById('fieldName').value;
    var entityType = document.getElementById('entityType').value;

    // Construire l'objet champ d'information supplémentaire
    var field = {
        id: fieldId,
        fieldName: fieldName,
        entityType: parseInt(entityType)
    };

    const url = 'http://localhost:8000/additional-info-fields/' + fieldId;

    // Envoyer la requête PUT pour mettre à jour le champ d'information supplémentaire
    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(field)
    })
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) {
                    showNotAllowed();
                }
                Swal.fire({
                    title: "Impossible de modifier le champ.",
                    text: "Une erreur inattendue de la part du serveur",
                    icon: "error",
                    confirmButtonColor: "#007BFF"
                });
                var myModal = new bootstrap.Modal(document.getElementById('editModal'));
                myModal.show();
                throw new Error(response.status);
            }
            Swal.fire({
                text: "Le champ d'information supplémentaire a été modifié avec succès !",
                icon: "success",
                confirmButtonColor: "#007BFF"
            });
            getAdditionalInfoFields(); // Rafraîchir la liste des champs d'information supplémentaire après modification
            return response.json();
        })
        .then(data => {
            // Afficher un message de succès et cacher le modal
        })
        .catch(error => {
            console.error("Erreur :", error);
        });
}

