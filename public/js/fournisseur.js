function showNotAllowed(message) {
    Swal.fire({
        icon: "error",
        title: "Accès interdit",
        text: message ?? "Action non autorisée !",
        confirmButtonColor: "#007bff"
    });
}