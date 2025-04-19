function register(event) {
    event.preventDefault(); // prevent default form submission
  
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
  
    if (!name || !email || password.length < 6) {
      alert("Please fill out the form correctly.");
      return;
    }
  
    const formData = new FormData();
    formData.append("name", name);
    formData.append("email", email);
    formData.append("password", password);
  
    fetch("../auth/register.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          alert("Registered successfully!");
          window.location.href = "../mainHome.html";
        } else {
          alert(data.message || "Registration failed.");
        }
      })
      .catch((err) => {
        console.error("Error:", err);
        alert("Something went wrong.");
      });
}