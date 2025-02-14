const form = document.querySelector("#loginform");

form.addEventListener("submit", async event => {
    event.preventDefault();
    const formData = new FormData(form);
    //formData.append("login", document.querySelector("#login-input").value); ele capturou direto na instância

    const data = await fetch("http://localhost/teste/backend/login.php", {
        method: "POST",
        mode: "cors",
        body: formData,

    })

    const response = await data.json();
    
    if (response.status === "success") {
        // Redireciona com base no tipo de usuário
        window.location.href = ""+response.tipo_usuario;
    } else {
        console.log(response.message); // Mostra o erro no console
    }

})