/*
    Temporise avant l'envoie de la requ�te pour �viter un bug
*/
function attendEnvoie() {
    setTimeout(testProgress,50);
}

/*
    Construit la requ�te xmlHttpRequest 
*/
function testProgress() {
    var idProgress=document.getElementById("progress_key").value;
    
    var xhr=new XMLHttpRequest();
    xhr.onload=tcb; ////la fonction de rappel qui g�re la r�ponse du serveur
    //la requ�te est envoy� en mode asynchrone(param�tre true) pour �viter de geler le navigateur
      xhr.open("GET","core/progress.xhr.php?progress_key="+idProgress,true);
      xhr.send(null);
}

/*
    Permet d'annuler l'envoie des infos par le navigateur via la m�thode stop() de l'objet window
    Peut provoquer le plantage du navigateur(ff2), � affiner ^^
*/
function annule() {
    //r�cup�ration de l'objet window de l'iframe
    var winIfrm=document.getElementById("tfrm").contentWindow;
    winIfrm.stop();
}

/*
    La fonction de rappel de l'objet xmlHttpRequest
*/
function tcb() {
    var repXhr=this.responseText; //r�cup�ration de la r�ponse du serveur via l'objet xmlHttpRequest (this)
    
    /*
        La r�ponse envoy� par le serveur �tant au format texte il faut utiliser eval() pour la manipuler
        La r�ponse au format json ne peut �tre exploit� directement par eval,
        il faut l'entourer de parenth�ses via une concat�nation pour �viter un bug
    */
    var objRep=eval("("+repXhr+")");
    
    /*document.getElementById("enCours").innerHTML=objRep.current;
    document.getElementById("total").innerHTML=objRep.total;
    document.getElementById("tab").innerHTML=repXhr;*/
	$(function() {$("#progressbar").progressbar('option', 'value', objRep.current/objRep.total*100)});
    
    //tant que l'upload est en cours le serveur est r�interrog�
    if (objRep.done==0) { testProgress(); }
}