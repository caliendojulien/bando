/**
 * Fonction qui renvoie la liste des lieux pour une ville
 * @param idVille
 */
function chargerLieux( idVille){
    let selectlieu=document.getElementById("choixLieux");
    selectlieu.length=0;
    fetch("/listerLieux/"+idVille)
    // // reponse = retour de la commande précédente, puis je le transforme en JSON
.then((reponse) => reponse.json())
        //json est donc le json récupéré, je l'ajoute dans le select
        .then((json) =>{
            let momOptionDisable = document.createElement('option')
            momOptionDisable.disabled=true;
            momOptionDisable.selected=true;
            if (json.length>0) {
                momOptionDisable.innerText = "Choisissez un lieu";
                selectlieu.appendChild(momOptionDisable);
                for (let lieu of json) {
                    let momOption = document.createElement('option');
                    momOption.value = lieu.id;
                    momOption.innerText = lieu.nom;
                    selectlieu.appendChild(momOption);
                }
            }else{
                momOptionDisable.innerText = "Aucun lieu n'existe pour cette ville";
                selectlieu.appendChild(momOptionDisable);
                 }
        });

}
function chargerLesLieux(idLieu){
    fetch("/listerLieux/"+idLieu)
        .then((reponse) => reponse.text())
        .then((texte) =>{
            console.log(texte);
            let container = document.getElementById('choixLieux');
            container.innerHTML = texte;
        });
}

/**
 * Fonction qui affiche le détail d'un lieu
 * @param idLieu
 */
function afficherUnLieu(idLieu){
    fetch("/AfficherLieu/"+idLieu)
        .then((reponse) => reponse.text())
        .then((texte) =>{
            console.log(texte);
            let container = document.getElementById('afficheLieu');
           container.innerHTML = texte;
        });
}