document.addEventListener("DOMContentLoaded", function () {
    const avatars = [
        "avatar 1.png", 
        "avatar 2.png", 
        "avatar 3.webp", 
        "avatar 4.png", 
        "avatar 5.jpg", 
        "avatar 6.jpg",
        
    ];

    // Seleccionar avatar aleatorio
    const randomAvatar = avatars[Math.floor(Math.random() * avatars.length)];

    // Asignar avatar al campo oculto y mostrarlo
    document.getElementById("avatar").value = randomAvatar;
    document.getElementById("avatarPreview").innerHTML = `<img src="avatars/${randomAvatar}" alt="Avatar" width="100">`;
});
