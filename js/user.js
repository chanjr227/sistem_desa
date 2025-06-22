window.addEventListener("scroll", function () {
  const box = document.getElementById("sambutan");
  const position = box.getBoundingClientRect().top;
  const windowHeight = window.innerHeight;

  if (position < windowHeight - 100) {
    box.classList.add("visible");
  } else {
    box.classList.remove("visible");
  }
});
