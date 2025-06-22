let password = document.getElementById("pass");
let icon = document.getElementById("btnView");

function fnView() {
  if (password.value !== "" && password.type === "password") {
    password.type = "text";
    icon.innerHTML = '<i class="fa-solid fa-eye"></i>';
    icon.title = "sembunyikan password";
  } else {
    password.type = "password";
    icon.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
    icon.title = "lihat password";
  }
}
