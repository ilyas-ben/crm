
function showNotAllowed() {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "You're not allowed to this",
    });
}


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
            const table = document.getElementById('usersTable');
            table.innerHTML = '';

            users.forEach(user => {
                // Construct HTML for a row with concatenated strings
                const rowHtml = '<tr>' +
                    '<td>' + user.id + '</td>' +
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

                console.log(user);

                // Append the row HTML to the table
                table.innerHTML += rowHtml;
            });
        })
        .catch(error => {
            console.error('Error fetching users:', error);
        });
}


// Function to display the modal and fill the form with user data
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
            //const profileOption = profileSelect.querySelector(`option[value="value2"]`);

            // this select the already selected profile
            for (let i = 0; i < profileSelect.options.length; i++) {
                if (parseInt(profileSelect.options[i].value) === parseInt(user.profile.id)) {
                    profileSelect.options[i].selected = true;
                }
            }
            // Display the modal
            var myModal = new bootstrap.Modal(document.getElementById('editModal'));
            myModal.show();
        })
        .catch(error => {
            console.error('Error fetching user:', error);
        });
}

// Function to handle form submission (edit user)
function edit() {
    // Retrieve input values
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

    console.log(user);

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
                if (response.status == 403) showNotAllowed();
                var myModal = new bootstrap.Modal(document.getElementById('editModal'));
                myModal.show();
                throw new Error(response.status);
            }

            alert("The user have been scucccesfully modified ! ");
            getUsers();
            return response.json();
        })
        .then(data => {
            // Display success message and hide modal
        })
        .catch(error => {
            // Display error message
            alert("Error durig edit, status :");

        });
}


function deleteUserById(userId) {
    // Replace 'userId' with the actual ID of the user you want to delete
    if (confirm("Do you really wanna delete this user ?")) {
        const url = `/users/` + userId;

        fetch(url, {
            method: 'DELETE',
        })
            .then(response => {
                if (!response.ok) {
                    if (response.status == 403) showNotAllowed();
                    throw new Error(`Failed to delete user with ID ${userId}. Status: ${response.status}`);
                }
                console.log(`User with ID ${userId} deleted successfully.`);
                getUsers();
                // Optionally, perform any additional actions upon successful deletion
            })
            .catch(error => {
                console.error('Error deleting user:', error);
                // Handle errors here, e.g., show a message to the user
            });
    }
}


function addUser() {
    // Récupération des valeurs des champs
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const email = document.getElementById('email').value;
    const address = document.getElementById('address').value;
    const phone = document.getElementById('phone').value;
    const profileId = document.getElementById('profileSelect').value;


    // Création de l'objet utilisateur
    const user = {
        username: username,
        password: password,
        email: email,
        address: address,
        phone: parseInt(phone), // Conversion en entier pour le numéro de téléphone
        profile: { id: profileId }
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
                console.log("status :".response.status);
            }

            // Réinitialisation du formulaire (facultatif)
            Swal.fire({
                title: "User Added !",
                confirmButtonColor: '#0d6efd',  // Change this to your desired color
                cancelButtonColor: '#d33'  // Change this to your desired color
            });
            location.href = "/users/index"
        })
}



function getProfiles() {
    fetch('http://localhost:8000/profiles')
        .then(response => {
            if (!response.ok) {
                console.error('Failed to fetch profiles:', response.status);
                return;
            }
            return response.json();
        })
        .then(profiles => {
            const table = document.getElementById('profilesList');
            table.innerHTML = '';

            profiles.forEach(profile => {
                // Construction du HTML pour une ligne avec des chaînes concaténées
                const rowHtml = '<tr>' +
                    '<td>' + profile.id + '</td>' +
                    '<td>' + profile.name + '</td>' +
                    '<td>' +
                    '<a href="#" onclick="editProfileModal(' + profile.id + ')">Modifier</a> ' +
                    '<a href="#" onclick="displayRoleByProfileId(' + profile.id + ')">Roles</a>' +
                    '</td>' +
                    '</tr>';

                // Ajout du HTML de la ligne au tableau
                table.innerHTML += rowHtml;
            });
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

    return 1;
}



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
                const li = document.createElement('li');
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


            /*  const rolesList = document.getElementById('rolesList');
             rolesList.innerHTML = '';
 
             roles.forEach(role => {
                 const roleItem = document.createElement('li');
                 roleItem.textContent = role.name;
                 rolesList.appendChild(roleItem);
             });
 
             // Afficher la modal Bootstrap
             $('#rolesModal').modal('show'); // Utilisation de jQuery pour montrer la modal */
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des rôles :', error);
            alert('Une erreur est survenue lors du chargement des rôles.');
        });
}

async function getRolesByGroup(rolesList) {
    console.log("inside : " + rolesList);
    rolesList.innerHTML = '';

    let rolesCheckBoxes = [];

    data = await fetchRolesByGroup();

    Object.keys(data).forEach(groupKey => {
        const group = data[groupKey];

        // Create a list item for group name
        const groupLi = document.createElement('li');

        groupLi.textContent = groupKey; // Use group key as the group name
        rolesList.appendChild(groupLi);

        // Iterate over roles in the group
        group.forEach(role => {
            const roleCheckbox = document.createElement('input');
            roleCheckbox.type = 'checkbox';
            roleCheckbox.value = role.id;
            roleCheckbox.id = 'role_' + role.id;

            rolesCheckBoxes.push(roleCheckbox);

            const roleLabel = document.createElement('label');
            roleLabel.htmlFor = 'role_' + role.id;
            roleLabel.textContent = role.name;

            const roleLi = document.createElement('li');
            roleLi.classList.add('list-group-item');
            roleLi.appendChild(roleCheckbox);
            roleLi.appendChild(roleLabel);
            rolesList.appendChild(roleLi);
        });
    });

    return rolesCheckBoxes;
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




// addProfile.js

// Function to add a new profile
function addProfile() {
    const profileName = document.getElementById('profileName').value;

    // Collect selected roles
    const selectedRoles = [];
    const checkboxes = document.querySelectorAll('#rolesList input[type="checkbox"]:checked');
    checkboxes.forEach(checkbox => {
        selectedRoles.push({ id: checkbox.value });
    });

    // Create profile object
    const profile = {
        name: profileName,
        roles: selectedRoles
    };

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
                throw new Error('Failed to add profile');
            }
            return response.json();
        })
        .then(data => {
            console.log('Profile added successfully:', data);
            // Optionally, handle success (e.g., show a success message)
            location.href = "/profiles/index";
        })
        .catch(error => {
            console.error('Error adding profile:', error);
            // Optionally, handle error (e.g., show an error message)
        });
}




async function editProfileModal(profileId) {

    await fetch(`http://localhost:8000/profiles/byid/` + profileId)
        .then(response => {
            if (response.status == 403) {
                showNotAllowed(); }
            return response.json();
        })
        .then(async data => {
            // Fill profile name input

            const idInput = document.getElementById('profileId');
            idInput.value = data.id;

            const editProfileNameInput = document.getElementById('editProfileName');
            editProfileNameInput.value = data.name;


            const editRolesList = document.getElementById("editRolesList");
            await getRolesByGroup(editRolesList);

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

    let checkedRoles = document.getElementById("editRolesList").querySelectorAll("input:checked");
    let rolesArray = [];

    checkedRoles.forEach(checkbox => {
        var role = { id: checkbox.value };
        rolesArray.push(role);
    });

    const roles = rolesArray;
    const url = "http://localhost:8000/profiles/" + id;

    const profile = {
        name: name,
        roles: roles
    }

    const profileJson = JSON.stringify(profile);

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
                if(response.status == 403) showNotAllowed();
                new bootstrap.Modal(document.getElementById('editProfileModal')).show();
                throw new Error();
            }
            response.json();
        })
        .then(data => {
            getProfiles();
            console.log('Success:', data);
            alert("Profile modified !");
            const myModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
            myModal.hide();

            // You can add code here to handle the successful response
        })
        .catch((error) => {

            console.error('Error:', error);
            // You can add code here to handle errors
        });
}




