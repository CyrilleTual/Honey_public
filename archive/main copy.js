//console.log("test de liaison js ");

/*********************************************************************
 *fonction preview pour images lors du chargement d'une image (upload) 
 * ---- écoute d'évenement pour remplacer le onchange ---- 
 * pages->  productAdd, ProductModify, carouselAddOrModify
 */

if(document.querySelector("#uploadImage")!== null){
  const input = document.querySelector("#uploadImage");
  input.addEventListener("change", PreviewImage);
  function PreviewImage(event){
    if(event.target.files.length > 0){
      var src = URL.createObjectURL(event.target.files[0]);
      // on cible et remplie la zone de préview.
      var contener = document.querySelector("form .preview");
      var preview = document.getElementById("uploadPreview"); 
      contener.style.display = "block"; // demasque l'affichage 
      preview.src = src;
    }
  }
}

/*********************************************************************
 * pour de-masquer une grande image (toggle de classe) / modal
 * ---- pour ne pas utiliser le onclick dans le html ---------
 */
if(document.querySelectorAll(".modal-trigger")!== null){

  const btns = document.querySelectorAll(".modal-trigger");

  btns.forEach(function(btn) {
    // ecoute d'évenement click sur les vignettes
    btn.addEventListener('click',()=>{
      // on récupère la valeur de la src de la vignette 
      const info  = btn.src;
      // on reconstitue la valeur de l'id du modal en recupérant le texte
      // du chemin src depuis le mot "public"
      var result = info.indexOf('public/uploads/')
      var place = result+15 // longeur de la chaine "public/uploads/"
      var idModal = info.slice(place); 
      document.getElementById(idModal).classList.toggle("visible")
    });
  });
}

/*********************************************************************
 * pour re-masquer une grande image (toggle de classe) / modal
 */
if(document.querySelectorAll(".modal-window")!== null){
  const modals = document.querySelectorAll('.modal-window')
  // ecoute du click sur les modals
  modals.forEach(function(modal){    
     modal.addEventListener('click',()=>{
      // recupération de l'id 
      const idModal = modal.id;
      //toggle de la classe visible 
      document.getElementById(idModal).classList.toggle("visible")
    });
  }
  );
}

/***********************************************************************
 * script de toogle class pour le bouton burger
 */
if(document.querySelector(".burger")!== null){
  const btn = document.querySelector(".burger");
  btn.addEventListener("click", deploy);
  function deploy() {
    btn.classList.toggle('active')
  }
}


/***********************************************************************
 * ecoute evenement sur packaging / prix de la page produit publique
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




/************************************************************
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

/**************************************************************************
 * initialisation du slider flipster ( nécessite jquery)
 * infos sur https://github.com/drien/jquery-flipster 
 */


// //
//  initialisation au chargement; 
//sliderInit();

// re- initialisation au resize   ( écoute sur le resize )

// window.addEventListener('resize', () => {

//    //////// on teste si on est sur un breakpoint 
//    let slider = (document.getElementById("targetSlider")).children[0];
//    //recuperation des classes 
//    let classes =  slider.className;
//    // on teste si la classe flipster--flat est présente ( )
//     let isFlat = classes.search(/flipster--flat/); // retourne un entier ou -1 si n'existe pas 
//     // si passage du breakpoint -> on déclanche le changment de type de slider 
//     if ((isFlat>0 && (window.innerWidth>768)) || ((isFlat==-1 && (window.innerWidth<768)))){
//       ///////////  changement  ///////
//       var xhttp = new XMLHttpRequest();
//       xhttp.onreadystatechange = function() {
//       if (this.readyState == 4 && this.status == 200) {
//       document.getElementById("targetSlider").innerHTML = this.responseText;
//       sliderInit();
//     }
//   };        
//   xhttp.open("GET", "index.php?route=homePage&action=refreshSlider", true);
//   xhttp.send();
//      }

// });

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




















