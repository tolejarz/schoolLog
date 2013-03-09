/*
    Temporise avant l'envoie de la requête pour éviter un bug
*/
function attendEnvoie() {
    setTimeout(testProgress,50);
}

/*
    Construit la requête xmlHttpRequest 
*/
function testProgress() {
    var idProgress=document.getElementById("progress_key").value;
    
    var xhr=new XMLHttpRequest();
    xhr.onload=tcb; ////la fonction de rappel qui gère la réponse du serveur
    //la requête est envoyé en mode asynchrone(paramètre true) pour éviter de geler le navigateur
      xhr.open("GET","core/progress.xhr.php?progress_key="+idProgress,true);
      xhr.send(null);
}

/*
    Permet d'annuler l'envoie des infos par le navigateur via la méthode stop() de l'objet window
    Peut provoquer le plantage du navigateur(ff2), à affiner ^^
*/
function annule() {
    //récupération de l'objet window de l'iframe
    var winIfrm=document.getElementById("tfrm").contentWindow;
    winIfrm.stop();
}

/*
    La fonction de rappel de l'objet xmlHttpRequest
*/
function tcb() {
    var repXhr=this.responseText; //récupération de la réponse du serveur via l'objet xmlHttpRequest (this)
    
    /*
        La réponse envoyé par le serveur étant au format texte il faut utiliser eval() pour la manipuler
        La réponse au format json ne peut être exploité directement par eval,
        il faut l'entourer de parenthèses via une concaténation pour éviter un bug
    */
    var objRep=eval("("+repXhr+")");
    
    /*document.getElementById("enCours").innerHTML=objRep.current;
    document.getElementById("total").innerHTML=objRep.total;
    document.getElementById("tab").innerHTML=repXhr;*/
	$(function() {$("#progressbar").progressbar('option', 'value', objRep.current/objRep.total*100)});
    
    //tant que l'upload est en cours le serveur est réinterrogé
    if (objRep.done==0) { testProgress(); }
}