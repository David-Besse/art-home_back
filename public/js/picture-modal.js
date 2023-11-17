// Get the modal
const modal = document.querySelector("#myImgModal");

// Get the image and insert it inside the modal - use its "alt" text as a caption
const img = document.querySelectorAll(".myImg");

// loop on img array
// add an event listener
for (let key of img) {

  key.addEventListener("click", ZoomOnClick);

}

// function to open the modal
function ZoomOnClick(event)
{
    const currentImg = event.currentTarget;

    const modalImg = document.getElementById('modalImg');

    modal.style.display = "block";

    modalImg.src = `${currentImg.src}`;

}

// Get the <span> element that closes the modal
const span = document.querySelectorAll(".close");

// When the user clicks on <span> (x), close the modal
const closeModal = function(){
  modal.style.display = "none";
}

//loop on span element
// add an event listener
for (const i of span) {
  
  i.addEventListener("click", closeModal)
}
