//console.log("test de liaison js ");
"use strict";

/**
 * fonction preview pour images lors du chargement d'une imag(upload) 
 * ---- écoute d'évenement pour remplacer le onchange ---- 
 * pages->  productAdd, ProductModify, carouselAddOrModify
 */

if(document.querySelector("#uploadImage")!== null){
  const input = document.querySelector("#uploadImage");
  input.addEventListener("change", PreviewImage);
  function PreviewImage(event){
    if(event.target.files.length > 0){
      const src = URL.createObjectURL(event.target.files[0]);
      // on cible et remplie la zone de préview.
      const contener = document.querySelector("form .preview");
      contener.innerHTML='<img id="uploadPreview" src='+src+' alt="prévisualisation">';
      contener.style.display = "block"; // demasque l'affichage 
    }
  }
}

/** *
 * Pour modal set de la src et affichage
 */
if(document.querySelectorAll(".modal-trigger")!== null){
  const btns = document.querySelectorAll(".modal-trigger");
  btns.forEach(function(btn) {
    // ecoute d'évenement click sur les container des vignettes
    // pour que la loupe soit aussi cliquable 
    btn.addEventListener('click',()=>{
      // on récupère la valeur de la src de l'image 
      const srcLoc  = btn.querySelector(" img ").src;
      // et on l'insere dans la div cible
      const div = document.querySelector(".modal-window");
      div.innerHTML='<img id="largePic" src='+srcLoc+' alt="grande image">';
      // on rend visible modal
      document.querySelector(".modal-window").classList.toggle("visible")
    });
  });
}


/** 
 * pour re-masquer une grande image (toggle de classe) / modal
 */
if(document.querySelector(".modal-window")!== null){
  const modal = document.querySelector('.modal-window')   
    // ecoute au click sur le modal 
    modal.addEventListener('click',()=>{
      document.querySelector(".modal-window").classList.toggle("visible")
    });
}

/** Script de toogle class pour le bouton burger 
 * Pas de toggle de class sur isClosed / isOpen car pas de class initialement
 * (le set d'une classe délanche une animation)
 */
let isClosed, trigger, totoggle;
if(document.querySelector("#hamburger")!== null){
  isClosed = true;
  trigger = document.querySelector("#hamburger");
  //totoggle = document.querySelector(".navbar ul.nav-menu");
  totoggle = document.querySelector(".nav-menu");
  trigger.addEventListener('click', burgerTime);
  function burgerTime(){
     if (isClosed == true) {
      trigger.classList.remove("is-closed");
      trigger.classList.add("is-open");
    } else {
      trigger.classList.remove("is-open");
      trigger.classList.add("is-closed");
    }
    isClosed = !isClosed;
    totoggle.classList.toggle('displayNone')
  }
};

/** 
 * pour assombrir si souris sur menu
 */
let toshadow, tohide;
if(document.querySelector(".nav-menu")!== null){
  toshadow = document.querySelector('.nav-menu')   
  tohide = document.querySelector('.shadow')
    toshadow.addEventListener('mouseover',()=>{
      tohide.classList.remove('displayNone')
    });
     toshadow.addEventListener('mouseout',()=>{
      tohide.classList.add('displayNone')
    });
}



/** Ecoute evenement sur packaging / prix de la page produit publique
 */
function updatePrice(){
  if (document.querySelectorAll(".selectItems") !== null){

    const myForms =document.querySelectorAll(".selectItems")

    myForms.forEach(function(form) {
      // recupère l'id et la valeur selectionnée (par défaut) pour chaque form 
      const id    = form.id
      const value = form.value

      // écoute d'évenement change sur les formulaires 
      form.addEventListener('change',()=>{
        const id    = form.id
        const value = form.value
        // on reconstitue l'id de la div d'affichage :
        const idAffichage = "idPrix"+id.substring(10)

        // on cherche le prix qui correspond à value (qui est l'id de l'item )

        let requestPrice = new Request ('index.php?route=items&action=ajaxPrice',{
          method : 'POST',
          body : JSON.stringify({ idToFind : value})
        })
        // traitement de la promesse > recup du texte et affichage dans div cible
        fetch(requestPrice)
        .then(res => res.text())
        .then(res => { document.getElementById(idAffichage).innerHTML = res;})
      })
    })
  }
}

updatePrice();


/** 
 * Recherche par mot cle -> Nos produits -> tout voir
 */
if (document.querySelector("#searchProduct") !== null){

  let inputbox = document.querySelector("#searchProduct");
  // Ecoute d'évènement au keyup (recherche pour chaque caractère de +)
  inputbox.addEventListener('keyup', () => { 
    // Récupérer le texte de l'imput 
    let textToFind = document.querySelector('#searchProduct').value;
    // Faire un objet de type request
    let myRequest = new Request('index.php?route=products&action=getProductsByRequest', {
        method  : 'POST',
        body    : JSON.stringify({ textToFind : textToFind })
    })
    // traitement de la réponse de 'getProductsByRequest' (array de produits)
    fetch(myRequest)
      // Récupère les données
    .then(res => res.text())
      // Exploite les données
    .then(res => {
        const anchor = document.getElementById("targetSearchProduct")
        anchor.innerHTML = res;
        // reconstruction de la nodelist des fenêtres de prix  
        updatePrice();
    })
  })
}

/** Initialisation du slider ( nécessite jquery) 
 *  Plus d'infos sur https://kenwheeler.github.io/slick/ 
 */
$('.sliderSlick').slick({
  dots: true,
  infinite: true,
  speed: 300,
  slidesToShow: 1,
  slidesToScroll: 1,
  centerMode: true,
  variableWidth: true,
  autoplay: true,
  autoplaySpeed: 2000,
  rows: 0,
});


/**
 * Confirmation de suppression
 */
if(document.querySelectorAll(".formDelete")!== null){
  const forms = document.querySelectorAll(".formDelete");
  forms.forEach(function(form) {
    // on selectionne le bouton delete qui est dans le form
    const btn = form.querySelector(".bouton")
    // ecoute d'évenement click 
    btn.addEventListener('click',()=>{
        if (confirm("Confirmez vous la suppression? "))
        {
        form.submit();
        }
    });
  });
}
/**
 * Pour voir en clair password
 */
let seePass, inputPass;
if(document.querySelector('.seePass')!==null){
  seePass = document.querySelector('.seePass');
  inputPass = document.querySelector('#password');
  seePass.addEventListener('mouseover',()=>{
    inputPass.setAttribute('type','text');
  });
  seePass.addEventListener('mouseleave',()=>{
    inputPass.setAttribute('type','password');
  });
}
















