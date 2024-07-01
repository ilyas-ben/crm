
function showNotAllowed(message) {
    Swal.fire({
        icon: "error",
        title: "Accès interdit",
        text: message ?? "Action non autorisée !",
        confirmButtonColor: "#007bff"
    });
}

/***********     USERS     ********************* */

function getUsers() {
    fetch('/users')
        .then(response => {
            if (!response.ok) {
                console.error('Failed to fetch users:', response.status);
                return;
            }
            return response.json();
        })
        .then(users => {
            $('#example').DataTable().destroy();
            const table = document.getElementById('usersTable');
            table.innerHTML = '';
            console.log("doing it");
            users.forEach(user => {
                // Construct HTML for a row with concatenated strings
                const rowHtml = '<tr>' +

                    '<td>' + user.username + '</td>' +
                    '<td>' + user.email + '</td>' +
                    '<td>' + user.address + '</td>' +
                    '<td>' + user.phone + '</td>' +
                    '<td>' + user.profile.name + '</td>' +
                    '<td>' +
                    '<a href="#" onclick="editUserForm(' + user.id + ')">Modifier</a> ' +
                    '<a href="#" onclick="deleteUserById(' + user.id + ')">Supprimer</a>' +
                    '</td>' +
                    '</tr>';

                // Append the row HTML to the table
                table.innerHTML += rowHtml;
            });
            $('#example').DataTable().destroy();
            $('#example').DataTable();
        })
        .catch(error => {
            console.error('Error fetching users:', error);
        });
}


function addUser() {
    // Récupération des valeurs des champs
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const email = document.getElementById('email').value;
    const address = document.getElementById('address').value;
    const phone = document.getElementById('phone').value;
    const profileId = document.getElementById('profileSelect').value;
    const image = document.getElementById('image');


    // Création de l'objet utilisateur
    const user = {
        username: username,
        password: password,
        email: email,
        address: address,
        phone: parseInt(phone), // Conversion en entier pour le numéro de téléphone
        profile: { id: profileId },
        image : image
    };

    // Envoi de la requête POST avec Fetch
    fetch('http://localhost:8000/users', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(user),
    })
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) showNotAllowed();
                { console.log("status :".response.status); }

            }

            // Réinitialisation du formulaire (facultatif)


        }).then(data => {
            Swal.fire({
                title: "L'utilisateur a été ajouté avec succès !",
                text: "Souhaitez-vous ajouter un autre utilisateur ?",
                icon: "success",
                confirmButtonColor: '#0d6efd',
                confirmButtonText: "Oui",
                cancelButtonColor: '#d33',
                cancelButtonText: "Non",
                showCancelButton: true
            }).then(result => {
                if (result.isConfirmed) {
                    location.href = "/users/add";
                }
                else {
                    location.href = '/users/index'
                }
            });
        })
}



async function editUserForm(id) {
    // Fetch user data by ID from server (assuming /users/{id} endpoint)
    fetch('/users/byid/' + id)
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) {
                    showNotAllowed();
                }
                throw new Error('Failed to fetch user');
            }
            return response.json();
        })
        .then(async user => {
            // Fill form inputs with user data
            document.getElementById('userId').value = user.id;
            document.getElementById('username').value = user.username;
            document.getElementById('email').value = user.email;
            document.getElementById('address').value = user.address;
            document.getElementById('phone').value = user.phone;
            document.getElementById('password').value = user.password;

            await getAllProfilesInSelect();

            const profileSelect = document.getElementById("profileSelect");

            for (let i = 0; i < profileSelect.options.length; i++) {
                if (parseInt(profileSelect.options[i].value) === parseInt(user.profile.id)) {
                    profileSelect.options[i].selected = true;
                }
            }
            // Display the modal
            var myModal = new bootstrap.Modal(document.getElementById('editModal'));
            await myModal.show();
        })
        .catch(error => {
            console.error('Error fetching user:', error);
        });
}


function editUser() {
    var userId = document.getElementById('userId').value;
    var username = document.getElementById('username').value;
    var email = document.getElementById('email').value;
    var address = document.getElementById('address').value;
    var phone = document.getElementById('phone').value;
    var password = document.getElementById('password').value;
    const profileId = document.getElementById('profileSelect').value;

    // Construct user object
    var user = {
        username: username,
        email: email,
        address: address,
        phone: parseInt(phone),
        password: password,
        profile: { id: parseInt(profileId) }
    };

    const url = 'http://localhost:8000/users/' + userId

    // Send PUT request to update user
    fetch(url, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(user)
    })
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) {
                    showNotAllowed();
                }
                var myModal = new bootstrap.Modal(document.getElementById('editModal'));
                myModal.show();
                throw new Error(response.status);
            }
            Swal.fire({
                text: "L'utilisateur a été modifié avec succès !",
                icon: "success",
                confirmButtonColor: "#007BFF"
            });
            getUsers();
            console.log("come on");
            return response.json();
        })
        .then(data => {
            // Display success message and hide modal
        })
        .catch(error => {
            console.error("error : ", error)
        });
}


function deleteUserById(userId) {

    Swal.fire({
        title: "Voulez-vous vraiment supprimer l'utilisateur n°" + userId + " ?",
        icon: "warning",
        showCancelButton: true,
        cancelButtonColor: "#d33",
        cancelButtonText: "Annuler",
        confirmButtonText: "Oui",
        confirmButtonColor: "#007bff"
    }).then((result) => {
        if (result.isConfirmed) {
            const url = `/users/` + userId;

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
                        throw new Error(`Failed to delete user with ID ${userId}. Status: ${response.status}`);
                    }
                    console.log(`User with ID ${userId} deleted successfully.`);
                    getUsers();

                }).then(data => {
                    Swal.fire({
                        title: "Utilisateur supprimé !",
                        icon: "success",
                        customClass: {
                            confirmButton: 'btn btn-success'
                        },
                    });
                })
                .catch(error => {
                    console.error('Error deleting user:', error);
                });

        }
    });
}


/** profile.js */
/*****************************************************  PROFILES ************************************************** */


function getProfiles() {
    fetch('http://localhost:8000/profiles')
        .then(response => {
            if (!response.ok) {
                Swal.fire({
                    title: "Erreur !",
                    text: "Impossible de charger les profiles dû à une erreur de la part du serveur, veuillez le vérifier puis rafraichir. ",
                    icon: "error"
                })
            }
            return response.json();
        })
        .then(profiles => {
            $('#example').DataTable().destroy();
            const table = document.getElementById('profilesList');
            table.innerHTML = '';

            profiles.forEach(profile => {
                // Construction du HTML pour une ligne avec des chaînes concaténées
                const rowHtml = '<tr>' +

                    '<td>' + profile.name + '</td>' +
                    '<td>' +
                    '<a href="#" onclick="editProfileModal(' + profile.id + ')">Modifier</a> ' +
                    '<a href="#" onclick="displayRoleByProfileId(' + profile.id + ')">Roles</a>' +
                    '</td>' +
                    '</tr>';

                // Ajout du HTML de la ligne au tableau
                table.innerHTML += rowHtml;
            });
            $('#example').DataTable().destroy();
            $('#example').DataTable();
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des profils :', error);
        });
}

async function getAllProfilesInSelect() {

    const url = "http://localhost:8000/profiles";

    await fetch(url)
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) showNotAllowed();
                throw new Error(response.status);
            }
            return response.json();
        })
        .then(data => {
            profiles = data;
            console.log(data);
            profileSelect = document.getElementById("profileSelect");
            profileSelect.innerHTML = "";

            profiles.forEach(profile => {
                const option = document.createElement('option');
                option.textContent = profile.name;
                option.value = profile.id;
                profileSelect.appendChild(option);
            })
        })
        .catch(error => {
            console.error("Erreur pour les profil ", error);
        })

    return 1;
}


function addProfile() {
    const profileName = document.getElementById('profileName').value;

    // Collect selected roles
    const selectedRoles = [];
    const checkboxes = document.querySelectorAll('#rolesList input[type="checkbox"].roleitem');
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) selectedRoles.push({ id: checkbox.value });
    });

    // Create profile object
    const profile = {
        name: profileName,
        roles: selectedRoles
    };

    console.log(profile);

    // Send POST request to create profile
    fetch('http://localhost:8000/profiles', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(profile),
    })
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) showNotAllowed();
                Swal.fire({
                    title: "Erreur de la part du serveur",
                    text: "Impossible d'ajouter le profil maintenant, veuillez vérifier le serveur puis réessayer",
                    icon: "error",
                    confirmButtonColor: '#0d6efd'
                });

                throw new Error('Failed to add profile' + response.status);
            }

            return response.json();
        })
        .then(data => {
            console.log('Profile added successfully:', data);
            Swal.fire({
                title: "Le profil a été ajouté avec succès !",
                text: "Souhaitez-vous ajouter un autre profil ?",
                icon: "success",
                confirmButtonColor: '#0d6efd',
                confirmButtonText: "Oui",
                denyButtonColor: '#d33',
                showDenyButton: true,
                denyButtonText: "Noan"
            }).then(result => {
                if (result.isConfirmed) {
                    location.href = "/profiles/index";
                }
                else location.href = "/profiles/add"
            });
        })
        .catch(error => {
            console.error('Error adding profile:', error);
            // Optionally, handle error (e.g., show an error message)
        });
}

async function editProfileModal(profileId) {

    await fetch(`http://localhost:8000/profiles/byid/` + profileId)
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) showNotAllowed();
                Swal.fire({
                    title: "Erreur de la part du serveur",
                    text: "Impossible d'ajouter le profil maintenant, veuillez vérifier le serveur puis réessayer",
                    icon: "error",
                    confirmButtonColor: '#0d6efd'
                })
            }
            return response.json();
        })
        .then(
            async data => {
                // Fill profile name input
                const idInput = document.getElementById('profileId');
                idInput.value = data.id;

                const editProfileNameInput = document.getElementById('editProfileName');
                editProfileNameInput.value = data.name;

                const editRolesList = document.getElementById("editRolesList");
                await getRolesByGroup(editRolesList);

                document.getElementById("editProfileModalLabel").innerHTML = "Rôles du profil "+ data.name;

                var myModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
                myModal.show();

                data.roles.forEach(role => {
                    const roleCheckBox = editRolesList.querySelector("#role_" + role.id);
                    roleCheckBox.checked = true;
                });

            })
        .catch(error => console.error('Error fetching profile:', error));
}


function editProfile() {
    const id = document.getElementById('profileId').value;
    const name = document.getElementById('editProfileName').value; // Get the value from the input

    const selectedRoles = [];
    const checkboxes = document.querySelectorAll('#editRolesList input[type="checkbox"].roleitem');

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) selectedRoles.push({ id: parseInt(checkbox.value) });
    });

    const roles = selectedRoles;
    const url = "http://localhost:8000/profiles/" + id;

    const profile = {
        name: name,
        roles: roles
    }

    const profileJson = JSON.stringify(profile);
    console.log(profileJson);

    fetch(url, {
        method: 'PUT', // Assuming you want to update an existing profile
        headers: {
            'Content-Type': 'application/json'
        },
        body: profileJson
    })
        .then(response => {
            console.log(profile);
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
                new bootstrap.Modal(document.getElementById('editProfileModal')).show();
                throw new Error();
            }
            response.json();
        })
        .then(data => {
            getProfiles();
            console.log('Success:', data);
            Swal.fire({
                title: "Utilisateur a été modifié avec succès !",
                icon: "success",
                confirmButtonColor: "#007bff"
            })
            // You can add code here to handle the successful response
        })
        .catch((error) => {
            console.error('Error:', error);
            // You can add code here to handle errors
        });
}



/********************** ROLES **************************** */


function displayRoleByProfileId(profileId) {
    fetch('http://localhost:8000/profiles/' + profileId + '/roles')
        .then(response => {
            if (!response.ok) {
                if (response.status == 403) showNotAllowed();
                throw new Error('Failed to fetch roles for profile');
            }
            return response.json();
        })
        .then(data => {

            const array2D = [];
            // Iterate over the entries of the object and construct the 2D array
            Object.entries(data).forEach(([key, value]) => {
                array2D.push([key, value]);
            });

            console.log("2D Array:", array2D);

            const rolesList = document.getElementById('rolesList');
            rolesList.innerHTML = "";

            // Iterate over array2D and create list items
            array2D.forEach(([group, value]) => {
                // Create a list item
                const li = document.createElement('div');
                li.className = "col"
                var licontent = group + " : ";
                // Set the text content of the list item
                licontent += "<ul>";
                value.forEach((role) => {
                    licontent += "<li>" + role.name + "</li>";
                })
                licontent += "</ul>";
                li.innerHTML = licontent;
                // Append the list item to the ul
                rolesList.appendChild(li);
            });

            // Afficher la modal Bootstrap
            $('#rolesModal').modal('show'); // Utilisation de jQuery pour montrer la modal */
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des rôles :', error);
            alert('Une erreur est survenue lors du chargement des rôles.');
        });
}


// Function to fetch roles by group and populate rolesList
async function fetchRolesByGroup() {

    let dataReturn;

    await fetch('http://localhost:8000/roles/bygroup')
        .then(response => response.json())
        .then(data => {
            dataReturn = data;
        }).catch(error => console.error('Error fetching roles:', error));


    return dataReturn
}






async function getRolesByGroup(rolesList) {
    rolesList.innerHTML = '';

    //**** Select all checkboxes checkbox ****/
    const selectAllCheckboxesCheckboxLabel = document.createElement('label');
    selectAllCheckboxesCheckboxLabel.htmlFor = 'selectAll';
    selectAllCheckboxesCheckboxLabel.textContent = 'Séléctionner tout';
    const selectAllCheckboxesCheckbox = document.createElement('input');
    selectAllCheckboxesCheckbox.type = 'checkbox';
    selectAllCheckboxesCheckbox.id = 'selectAll';
    const selectAllCheckboxesLi = document.createElement('li');
    selectAllCheckboxesLi.style.listStyleType = 'none';
    selectAllCheckboxesLi.appendChild(selectAllCheckboxesCheckbox);
    selectAllCheckboxesLi.appendChild(selectAllCheckboxesCheckboxLabel);
    rolesList.appendChild(selectAllCheckboxesLi);
    selectAllCheckboxesCheckbox.onchange = function () {
        if (selectAllCheckboxesCheckbox.checked) {
            const groupCheckboxes = rolesList.querySelectorAll('input[type="checkbox"]');
            groupCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            })
        } else if (!selectAllCheckboxesCheckbox.checked) {
            const groupCheckboxes = rolesList.querySelectorAll('input[type="checkbox"]');
            groupCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            })
        }
    }
    /***********************/

    let rolesCheckBoxes = [];
    const data = await fetchRolesByGroup();

    Object.keys(data).forEach(groupKey => {

        const group = data[groupKey];

        // Create a list item for group name
        const groupLi = document.createElement('div');
        groupLi.className = "col";

        // Create group checkbox
        const groupCheckbox = document.createElement('input');
        groupCheckbox.type = 'checkbox';
        groupCheckbox.id = groupKey + 'Group';
        groupCheckbox.className = "group";
        groupCheckbox.addEventListener('change', function () {
            // When group checkbox changes, update child checkboxes
            const childCheckboxes = groupLi.querySelectorAll('input[type="checkbox"]');
            childCheckboxes.forEach(checkbox => {
                checkbox.checked = groupCheckbox.checked;
            });
        });
        groupLi.appendChild(groupCheckbox);

        // Create label for group checkbox
        const groupLabel = document.createElement('label');
        groupLabel.htmlFor = groupKey + 'Group';
        groupLabel.textContent = groupKey + ":";
        groupLi.appendChild(groupLabel);

        // Append group li to rolesList
        rolesList.appendChild(groupLi);

        // Create a container for role checkboxes
        const roleContainer = document.createElement('ul');
        groupLi.appendChild(roleContainer);

        // Iterate over roles in the group
        group.forEach(role => {
            const roleCheckbox = document.createElement('input');
            roleCheckbox.type = 'checkbox';
            roleCheckbox.value = role.id;
            roleCheckbox.id = 'role_' + role.id;
            roleCheckbox.className = 'roleitem';

            // Add event listener to role checkbox
            roleCheckbox.addEventListener('change', function () {
                updateGroupCheckbox(groupCheckbox, roleContainer);
            });

            // Create label for role checkbox
            const roleLabel = document.createElement('label');
            roleLabel.htmlFor = 'role_' + role.id;
            roleLabel.textContent = role.name;

            // Create list item for role
            const roleLi = document.createElement('li');
            roleLi.classList.add('list-group-item');
            roleLi.appendChild(roleCheckbox);
            roleLi.appendChild(roleLabel);

            // Append role li to roleContainer
            roleContainer.appendChild(roleLi);

            // Push role checkbox into rolesCheckBoxes array
            rolesCheckBoxes.push(roleCheckbox);
        });
    });

    return rolesCheckBoxes;
}

/* dont matter about this  */
function updateGroupCheckbox(groupCheckbox, roleContainer) {
    const childCheckboxes = roleContainer.querySelectorAll('input[type="checkbox"]');
    const checkedChildCheckboxes = roleContainer.querySelectorAll('input[type="checkbox"]:checked');

    if (childCheckboxes.length === checkedChildCheckboxes.length) {
        groupCheckbox.checked = true;
        groupCheckbox.indeterminate = false;
    } else if (checkedChildCheckboxes.length === 0) {
        groupCheckbox.checked = false;
        groupCheckbox.indeterminate = false;
    } else {
        groupCheckbox.checked = false;
        groupCheckbox.indeterminate = true;
    }
}
/**************************************************************** */







