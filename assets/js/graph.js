var base_url = "http://150.60.40.88:360/360/";
var site_url = base_url+"index.php/";

/**
 * Teste les choix de l'utilisateur dans la génération d'un graphique afin de voir s'il n'y a pas une incohérence
 */
function verif(){
    if(document.getElementById("wording") != null){
        if(document.getElementById("wording").value==document.getElementById("group").value){
            document.getElementById("submit").disabled =true;
            document.getElementById("wording").style.borderColor = "red";
            document.getElementById("group").style.borderColor = "red";
            document.getElementById("messageErreur").innerHTML = "Le libelle et le type ne doivent être different !";
        }else{
            document.getElementById("submit").disabled =false;
            document.getElementById("wording").style.borderColor = "";
            document.getElementById("group").style.borderColor = "";
            document.getElementById("messageErreur").innerHTML = "";
        }
    }else{
        if(document.getElementById("wording0").value==document.getElementById("group").value){
            document.getElementById("submit").disabled =false;
            document.getElementById("wording0").style.borderColor = "";
            document.getElementById("group").style.borderColor = "";
            document.getElementById("messageErreur").innerHTML = "";
        }else if(document.getElementById("wording1") && document.getElementById("wording1").value==document.getElementById("group").value){
            document.getElementById("submit").disabled =false;
            document.getElementById("wording1").style.borderColor = "";
            document.getElementById("group").style.borderColor = "";
            document.getElementById("messageErreur").innerHTML = "";
        }else if(document.getElementById("wording1") && document.getElementById("wording0").value==document.getElementById("wording1").value){
            document.getElementById("submit").disabled =false;
            document.getElementById("wording0").style.borderColor = "";
            document.getElementById("wording1").style.borderColor = "";
            document.getElementById("messageErreur").innerHTML = "";
        }
    }

}

/**
 * Ajoute un filtre dans la liste des filtres (requete AJAX)
 * @param bdd
 */
function addFilter(source, database, table) {

    //Récuperation du champs à ajouter
    var choix = document.getElementById("filters").value;
    var choix_notype = choix.split('::')[0];
    //Vérification que le filtre n'existe pas
    if (document.getElementById(choix_notype + "Box") || document.getElementById(choix + "Wait")) {
        return;
    }
    //document.getElementById("filtersBox").innerHTML +=
    if(typeof nbFilterLoad === 'undefined'){
        nbFilterLoad = 1;
    }else{
        nbFilterLoad++;
    }
    document.getElementById("submit").disabled =true;
    var jq=jQuery.noConflict();
    jq('#filtersBox').append("<div id=\"" + choix_notype + "Wait\" class=\"filterBox\"><label>" + choix_notype + "</label><br/>Generation du formulaire<br/><div id=\"floatingCirclesG\">\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_01\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_02\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_03\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_04\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_05\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_06\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_07\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_08\"></div>\n" +
        "</div></div>");
    var typeData = "";
    var urlApi = site_url + 'api/getDiffValueColumn';
    console.log(urlApi + "?&source=" + source + "&database=" + database + "&table=" + table + "&column=" + choix);
    jq.ajax({
        type: "POST",
        url: urlApi,
        data: "source=" + source + "&database=" + database + "&table=" + table + "&column=" + choix,
        success:
        function(retour){
            console.log(retour);
            champs.forEach(function (t) {
                if (t.name == choix_notype) {
                    typeData=t.type;
                }
            });
            if(typeData=="timestamp" || typeData=="date"){
                generateFormDate(choix, retour, null);
            }else{
                generateFormChosen(choix, retour);
            }

            document.getElementById(choix_notype + "Wait").remove();
            nbFilterLoad--;
            if(nbFilterLoad<=0){
                document.getElementById("submit").disabled =false;
            }
        }
    });
    if(nbFilterLoad<=0){
        document.getElementById("submit").disabled =false;
    }
}

/**
 * Suppression du filtre
 * @param id nom de la colonne à filtrer
 */
function delFilter(id){
    document.getElementById(id+"Box").outerHTML = "";
}

/**
 * Génération d'un filtre de type Chosen (Librairy)
 * @param choix nom de la colonne à filtrer
 * @param value Liste des valeurs à mettre dans le formulaire
 * @param arrayValue Listes des valeurs à sélectionner par défaut
 * @param operators Choix d'inclusion par défaut à attribué au filtre (inclure|exclure)
 */
function generateFormChosen(choix, value, arrayValue = [], operators = ""){
    loadFilter = typeof loadFilter !== 'undefined' ? loadFilter : false;
    var jq=jQuery.noConflict();
    var data = JSON.parse(value);

    var div_box = document.createElement("div");
    div_box.id = choix+"Box";
    div_box.className = "filterBox";

    var button_remove = document.createElement("button");
    button_remove.type = "button";
    button_remove.setAttribute('onclick',"delFilter('"+choix+"');");
    button_remove.className = "btn btn-danger btn-xs";
    button_remove.innerHTML = "X";
    div_box.appendChild(button_remove);
    var label = document.createElement("label");
    label.for = choix;
    label.innerHTML=choix;
    div_box.appendChild(label);

    var form_operator = document.createElement("select");
    form_operator.name = "operators["+choix+"]";
    form_operator.size = "1";
    ["inclure","exclure"].forEach(function(data){
        var option = document.createElement("option");
        option.innerHTML = data;
        if(data==operators){
            option.selected = true;
        }
        form_operator.appendChild(option);
    });
    div_box.appendChild(document.createElement("br"));
    div_box.appendChild(form_operator);
    var form_create = document.createElement("select");
    form_create.name = "filtres["+choix+"][]";
    form_create.id = choix;
    form_create.className = "selectchoice";
    form_create.multiple=true;
    if(arrayValue.length !=0){
        data.forEach(function(data){
            var option = document.createElement("option");

            if(data.value == null){
                option.innerHTML = "NULL";
            }else if(data.value == ""){
                option.innerHTML = "VIDE";
                option.value = "";
            }else{
                option.innerHTML = data.value;
            }

            if(arrayValue.includes(data.value)){
                option.selected = true;
            }else if(data.value == null && arrayValue.includes("NULL")){
                option.selected = true;
            }else if(data.value == "" && arrayValue.includes("VIDE")){
                option.selected = true;
            }
            form_create.appendChild(option);
        });
    }else{
        data.forEach(function(data){
            var option = document.createElement("option");
            if(data.value == null){
                option.innerHTML = "NULL";
            }else if(data.value == ""){
                option.innerHTML = "VIDE";
                option.value = "";
            }else{
                option.innerHTML = data.value;
            }
            form_create.appendChild(option);
        });
    }

    div_box.appendChild(document.createElement("br"));
    div_box.appendChild(form_create);
    jq('#filtersBox').append(div_box);
    jq("#"+choix).chosen({max_shown_results:50, no_results_text: "Aucun résultat pour"});
    //Permet de resoudre le problème des listes qui était partielement caché par le overflow de filtersBox
    //L'idée est d'agrandir filtersBox le temps d'afficher la liste
    jq("#"+choix).on('chosen:showing_dropdown', function(evt, params) {
        jq('#filtersBox').height(jq('#filtersBox').height()+250);
    });
    jq("#"+choix).on('chosen:hiding_dropdown', function(evt, params) {
        jq('#filtersBox').height(jq('#filtersBox').height()-250);
    });
}

/**
 *
 * @param choix Nom de la colonne à filtrer
 * @param value Liste des valeurs à mettre dans le formulaire
 * @param arrayValue Listes des valeurs à sélectionner par défaut
 */
function generateFormDate(choix, value, arrayValue){
    choix = choix.split('::')[0];
    var data = JSON.parse(value);
    var maxDate = "";
    var minDate = "";
    var defautDate1 = "";
    var defautDate2 = "";
    if(arrayValue!=null && arrayValue.min!=null && arrayValue.max!=null){
        defautDate1=arrayValue.min;
        defautDate2=arrayValue.max;
    }
    data.forEach(function (t) {
        if(maxDate==""){
            maxDate= t.value;
            minDate = t.value;
        }
        if(maxDate.localeCompare(t.value)===-1){
            maxDate = t.value;
        }
        if(minDate.localeCompare(t.value)===1){
            minDate = t.value;
        }
    });
    label = "<label for=\""+choix+"\">"+choix+"</label>";
    resultat = "";
    form = "<div class=\"input-daterange input-group\" id=\"datepicker\">\n" +
        "<span class=\"input-group-addon\">Du</span>\n" +
        "<input type=\"text\" class=\"input-sm form-control\" id=\"from_"+choix+"\" name=\"filtres["+choix+"][min]\">"+
        "</div><br/>" +
        "<div class=\"input-daterange input-group\" id=\"datepicker\">\n" +
        "<span class=\"input-group-addon\">Au</span>\n" +
        "<input type=\"text\" class=\"input-sm form-control\" id=\"to_"+choix+"\" name=\"filtres["+choix+"][max]\">"+
        "</div>";
    //Ajout du formulaire
    var $j = jQuery.noConflict();
    $j('#filtersBox').append("<div id=\""+choix+"Box\" class=\"filterBox\">"+
        "<button id=\""+choix+"\" type=\"button\" onclick=\"delFilter('"+choix+"')\" class='btn btn-danger btn-xs'>X</button> "+
        label+"<br/>"+form+"<br/><input id=\"operator_"+choix+"\" name=\"operators["+choix+"]\" value=\"date\" type=\"hidden\"></div>");

    $j( function($) {
        var dateFormat = "yy-mm-dd",
            from = $( "#from_"+choix )
                .datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    changeYear: true,
                    numberOfMonths: 1,
                    dateFormat: "yy-mm-dd",
                    minDate:minDate,
                    maxDate:maxDate,
                    showWeek: true,
                    dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
                    dayNamesShort: [ "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam" ],
                    dayNames: [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ]
                }).datepicker("setDate", defautDate1)
                .on( "change", function() {
                    to.datepicker( "option", "minDate", getDate( this ) );
                }),
            to = $( "#to_"+choix ).datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                dateFormat: "yy-mm-dd",
                minDate:minDate,
                maxDate:maxDate,
                showWeek: true,
                dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
                dayNamesShort: [ "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam" ],
                dayNames: [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ]
            }).datepicker("setDate", defautDate2).on( "change", function() {
                from.datepicker( "option", "maxDate", getDate( this ) );
            });

        function getDate( element ) {
            var date;
            try {
                date = $.datepicker.parseDate( dateFormat, element.value );
            } catch( error ) {
                date = null;
            }

            return date;
        }
    } );
}

var addManualFilterArray=[];
var addFilterArray=[];
/**
 * Ajoute les filtres dans la liste des filtres à partir d'une configuration (requete AJAX)
 * @param configJSON Configuration du graphique
 */
function addFilterAuto(configJSON) {
    var config = JSON.parse(configJSON);
    if(config.filtres == undefined || config.operators == undefined ){
        return;
    }
    if(typeof nbFilterLoad === 'undefined'){
        nbFilterLoad = 0;
    }
    document.getElementById("submit").disabled =true;
    var jq=jQuery.noConflict();
    jq.each(config.filtres, function(index, value) {
        var position = addFilterArray.indexOf(index);
        var operators = config.operators[index];
        if(position==-1) {
            addFilterArray.push(index);
            if (!document.getElementById(index + "Box")) {
                jq('#filtersBox').append("<div id=\""+index+"Wait\" class=\"filterBox\"><label>"+index+"</label><br/>Generation du formulaire<br/><div id=\"floatingCirclesG\">\n" +
                    "\t<div class=\"f_circleG\" id=\"frotateG_01\"></div>\n" +
                    "\t<div class=\"f_circleG\" id=\"frotateG_02\"></div>\n" +
                    "\t<div class=\"f_circleG\" id=\"frotateG_03\"></div>\n" +
                    "\t<div class=\"f_circleG\" id=\"frotateG_04\"></div>\n" +
                    "\t<div class=\"f_circleG\" id=\"frotateG_05\"></div>\n" +
                    "\t<div class=\"f_circleG\" id=\"frotateG_06\"></div>\n" +
                    "\t<div class=\"f_circleG\" id=\"frotateG_07\"></div>\n" +
                    "\t<div class=\"f_circleG\" id=\"frotateG_08\"></div>\n" +
                    "</div></div>");
                nbFilterLoad++;
                jq.ajax({
                    type: "POST",
                    url: site_url + 'api/getDiffValueColumn',
                    data: "source=" + config.source + "&database=" + config.database + "&table=" + config.table + "&column=" + index,
                    success: function (retour) {
                        champs.forEach(function (t) {
                            if(t.name==index){
                                typeData=t.type;
                            }
                        });
                        if(typeData=="timestamp" || typeData=="date"){
                            generateFormDate(index, retour, value);
                        }else{
                            generateFormChosen(index, retour, value, operators);
                        }
                        addManualFilterArray.splice(position, 1);
                        document.getElementById(index+"Wait").remove();
                        nbFilterLoad--;
                        if(nbFilterLoad<=0){
                            document.getElementById("submit").disabled =false;
                        }
                    }
                });
            }
        }
    });
    if(nbFilterLoad<=0){
        document.getElementById("submit").disabled =false;
    }
}

/**
 * Ajoute le fichier js généré pour les graphiques de Chartjs
 * @param script Contenu du script à ajouter
 * @param mode_show False pour que le script soit mis dans le formulaire de sauvegarde de graphique
 */
function addJsDynamically(script,mode_show){
    var se = document.createElement('script');
    se.setAttribute('type', 'text/javascript');
    se.appendChild(document.createTextNode(script));
    document.getElementsByTagName('head').item(0).appendChild(se);

    showGraph();
    document.getElementById('chargement-message').innerHTML = "";
    document.getElementById('chargement').innerHTML = "";
}
/**
 * Ajoute la valeur selectionné de la liste deroulante du champ dans les filtres active de ce champ
 * @param select
 */
function selectFiltre(select)
{
    var idForm =  select.id;
    var option = select.options[select.selectedIndex];
    if(option.value=="Choix filtre"){
        return;
    }
    //var ul = select.parentNode.getElementsByTagName('ul')[0];

    var ul = document.getElementById('data'+idForm);
    var choices = ul.getElementsByTagName('input');
    for (var i = 0; i < choices.length; i++) {
        if (choices[i].value == option.value){
            return;
        }
    }
    var li = document.createElement('li');
    var input = document.createElement('input');
    var text = document.createTextNode(option.firstChild.data);
    input.type = 'hidden';
    input.name = 'filtres['+idForm+'][]';
    input.value = option.value;

    var button = document.createElement("img");
    //button.type = "image";
    button.src = base_url+"assets/img/minus.png";
    button.className = "removeButton";
    button.setAttribute('onclick', "this.parentNode.parentNode.removeChild(this.parentNode);");
    li.appendChild(button);
    li.appendChild(input);
    li.appendChild(text);
    ul.appendChild(li);
}

function changeColor(x, typeGraph){
    if(typeGraph=="pie"){
        var newValue = document.getElementById(x+"_color").value;
        barChartData.datasets[0].backgroundColor[x] = newValue;
    }else {
        var newValue = document.getElementById(x + "_color").value;
        var position = 0;
        for (dataset in barChartData.datasets) {
            if(barChartData.datasets[dataset].label===x) {
                position=dataset;
                break;
            }
        }
        barChartData.datasets[position].borderColor = newValue;
        barChartData.datasets[position].backgroundColor = newValue;
    }
    showGraph();
}
function changeColorHisto(x){
    var newValue = document.getElementById(x+"_color").value;
    if(configGraph["color"]==null){
        configGraph["color"] = {};
    }
    configGraph["color"][x+"_color"] = newValue;

    generateHistoGraph(data,configGraph);
}
function changeColorPie(x){
    var newValue = document.getElementById(x+"_color").value;
    if(configGraph["color"]==null){
        configGraph["color"] = {};
    }
    configGraph["color"][x+"_color"] = newValue;

    generatePieGraph(data,configGraph);
}

function monthTranslate(nbmonth){
    var month = ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre"];
    return month[nbmonth-1];
}

function saveGraph(){
    //Verification que le graphique n'est pas vide
    var name = document.getElementById("name");
    if (name.value == "") {
        name.style.borderColor = "red";
        name.onchange=function(){
            name.style.borderColor="";
        };
        alert("Les graphiques sans nom ne peuvent pas être sauvegardés !");
        return false;
    }
    var canvas = document.getElementById("canvas");
    if(canvas!=null){
        var d=canvas.toDataURL("image/png");
        document.getElementById("image").value = d;
    }
    document.getElementById("config").value = JSON.stringify(configGraph);

    var formData = new FormData();
    formData.append("name", document.getElementById("name").value);
    formData.append("description", document.getElementById("description").value);
    formData.append("group", document.getElementById("groupUser").value);
    formData.append("id", document.getElementById("id").value);
    formData.append("script", "");
    formData.append("type", document.getElementById("type").value);
    formData.append("config", JSON.stringify(configGraph));
    formData.append("image", document.getElementById("image").value);
    formData.append("image_name", document.getElementById("image_name").value);

    if (document.getElementById("type").value == "pie" || document.getElementById("type").value == "histogram" || document.getElementById("type").value == "treemap") {
        for (var x in configGraph.color) {
            if (document.getElementById(x)) {
                formData.append(x, document.getElementById(x).value);
            }

        }
    }

    if(document.getElementById("live").checked){
        formData.append("live", "on");
    }else{
        formData.append("live", "off");
    }
    if(document.getElementById("public").checked){
        formData.append("public", "on");
    }else{
        formData.append("public", "off");
    }

    var request = new XMLHttpRequest();
    request.open("POST", site_url+"graph/saveGraph");
    request.send(formData);
    show_msg_ajax("Sauvegarde des données");
    request.onreadystatechange = function(event) {
        // XMLHttpRequest.DONE === 4
        if (this.readyState === XMLHttpRequest.DONE) {
            if (this.status === 200) {
                console.log("Réponse reçue: %s", this.responseText);
                if(request.responseText==""){
                    //alert("Graphique sauvegardée");
                    window.location.replace(base_url);
                }else{
                    show_error_ajax(this.responseText);
                }
            } else {

                console.log("Status de la réponse: %d (%s)", this.status, this.statusText);
                console.log(request.responseText);
                show_error_ajax(request.responseText);
            }
        }
    };


    return false;
}


function convertJSONtoCSV(json) {
    //Convertie en json si c'est une chaine de caractère
    if (typeof json === 'string') {
        json = JSON.parse(json);
    }
    csv="";
    //Mise en place du header
    for (item in json) {
        for (item2 in json[item]) {
            csv+=item2+";";
        }
        csv=csv.substring(0, csv.length - 1)+"\n";
        break;
    }
    for (item in json) {
        linecsv="";
        for (item2 in json[item]) {
            linecsv+=json[item][item2]+";";
        }
        csv+=linecsv.substring(0, linecsv.length - 1)+"\n";
    }
    return csv;
}

function convertJSONtoEXCEL(json) 
{    
    //Convertie en json si c'est une chaine de caractère
    if (typeof json === 'string' || typeof json === 'number') {
        json = JSON.parse(json);
    }
    excel="";
    for (item in json) {
        for (item2 in json[item]) {
            excel+=item2+";";
        }
        excel=excel.substring(0, excel.length - 1)+"\n";
        break;
    }
    for (item in json) {
        lineexcel="";
        for (item2 in json[item]) {
            lineexcel+=json[item][item2]+";";
        }
        excel+=lineexcel.substring(0, lineexcel.length - 1)+"\n";
    }
    return excel;
}

function copycsv(){
    //document.getElementById('showcsv').select();
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById('showcsv'));
        range.select();
    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById('showcsv'));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
    }
    var successful = document.execCommand('copy');
}

/**
 * Création d'un bouton pour télécharger les données en CSV (source JSON)
 * @param data
 */
function createDownloadCSVButton(data,overwriteFirstLine,filename="data"){
    if (!document.getElementById('downloadurl')) {
        return;
    }
    var csv = convertJSONtoCSV(data);
    console.log(csv);
    if(overwriteFirstLine!=""){
        csv = overwriteFirstLine+"\n"+csv.substring(csv.indexOf('\n')+1);
    }
    var element = document.createElement('a');
    var csvData = new Blob([csv], { type: 'application/vnd.ms-excel;charset=utf-8' });
    element.setAttribute('href', URL.createObjectURL(csvData));
    element.setAttribute('download', filename+".csv");
    element.innerHTML="Téléchargement de l'EXCEL";
    element.className="btn btn-primary";
    document.getElementById('downloadurl').appendChild(element);
    document.getElementById('downloadurl').hidden = false;
}

/**
 * Création d'un bouton pour télécharger les données en CSV (sources data ChartJs)
 * @param typeChart
 * @param overwriteFirstLine
 */
function createDownloadCSVButtonForChartJs(typeChart,overwriteFirstLine,filename="data"){
    if (!document.getElementById('downloadurl')) {
        return;
    }
    var csv = overwriteFirstLine+"\n";
    if(typeChart=="pie"){
        for (idLabel in barChartData.labels) {
            csv +=barChartData.labels[idLabel]+";"+barChartData.datasets[0].data[idLabel]+"\n"
        }
    }else if(typeChart=="histogram"){
        for (idLabel in barChartData.labels) {
            for (idDataset in barChartData.datasets) {

                if(Array.isArray(barChartData.labels[idLabel])){
                    csv +=barChartData.labels[idLabel].join(";")+";"+barChartData.datasets[idDataset].label+";"+barChartData.datasets[idDataset].data[idLabel]+"\n"
                }else{
                    csv +=barChartData.labels[idLabel]+";"+barChartData.datasets[idDataset].label+";"+barChartData.datasets[idDataset].data[idLabel]+"\n"
                }

            }

        }
    }
    var element = document.createElement('a');
    csvData = new Blob([csv], { type: 'text/csv' });
    element.setAttribute('href', URL.createObjectURL(csvData));
    element.setAttribute('download', filename+".csv");
    element.innerHTML="Téléchargement de l'EXCEL";
    element.className="btn btn-primary";
    document.getElementById('downloadurl').appendChild(element);
    document.getElementById('downloadurl').hidden = false;
}
/**
 * Modifie le formulaire typecalculchamp pour avoir des données lié au type de calcul
 * @param champs
 */
function generateCalculForm(champs){
    var typecalcul = document.getElementById('typecalcul');
    var choice  = typecalcul.options[typecalcul.selectedIndex].value;
    var typecalculchamp = document.getElementById('typecalculchamp');
    while (typecalculchamp.options.length > 0) {
        typecalculchamp.remove(typecalculchamp.options.length - 1);
    }
    if(choice=="COUNT"){
        var opt = document.createElement('option');
        opt.text = "";
        opt.value = "";
        typecalculchamp.add(opt, null);
    }
    for (id in champs) {
        if(choice=="COUNT" && (champs[id].type!="int" && champs[id].type!="long" && champs[id].type!="bigint")){
            var opt = document.createElement('option');
            opt.text = champs[id].name;
            opt.value = champs[id].name;
            typecalculchamp.add(opt, null);
        }else if((choice=="SUM"|| choice=="AVG") && (champs[id].type=="int" || champs[id].type=="long" || champs[id].type=="bigint")){
            var opt = document.createElement('option');
            opt.text = champs[id].name;
            opt.value = champs[id].name;
            typecalculchamp.add(opt, null);
        }
    }
    var speed_filters_form = document.getElementById('speed-filters-form');
    var speed_filters_div = document.getElementById('speed-filters-div');
    if(speed_filters_form !=null && choice=="AVG"){
        speed_filters_form.name = '';
        speed_filters_div.hidden = true;
    }else if(speed_filters_form !=null){
        speed_filters_form.name = 'speedfilters[]';
        speed_filters_div.hidden = false;
    }


}
/**
 * Modifie le formulaire typecalculchamp pour avoir des données lié au type de calcul
 * @param champs
 */
function generateCalculFormAdmin(champs, tableName, typeCalulID, typeCalculChampID, engine="mysql") {
    var typecalcul = document.getElementById(typeCalulID);
    var choice  = typecalcul.options[typecalcul.selectedIndex].value;
    var typecalculchamp = document.getElementById(typeCalculChampID);
    while (typecalculchamp.options.length > 0) {
        typecalculchamp.remove(typecalculchamp.options.length - 1);
    }
    var nb = 0;
    if(choice=="COUNT"){
        var opt = document.createElement('option');
        opt.text = "";
        opt.value = "";
        typecalculchamp.add(opt, null);
        nb++;
    }

    if (engine == "elastic") {

        for (id in champs_elastic[tableName]) {
            if (choice == "COUNT" && (champs_elastic[tableName][id].type != "int" && champs_elastic[tableName][id].type != "long" && champs_elastic[tableName][id].type != "bigint" && champs_elastic[tableName][id].type != "bigint")) {
                var opt = document.createElement('option');
                opt.text = champs_elastic[tableName][id].name;
                opt.value = champs_elastic[tableName][id].name;
                typecalculchamp.add(opt, null);
            } else if ((choice == "SUM" || choice == "AVG") && (champs_elastic[tableName][id].type == "int" || champs_elastic[tableName][id].type == "long" || champs_elastic[tableName][id].type == "bigint")) {
                var opt = document.createElement('option');
                opt.text = champs_elastic[tableName][id].name;
                opt.value = champs_elastic[tableName][id].name;
                typecalculchamp.add(opt, null);
                nb++;
            }
        }
    } else {
        for (id in champs) {
            if (champs[id].TABLE_NAME == tableName && choice == "COUNT" && (champs[id].DATA_TYPE != "int" && champs[id].DATA_TYPE != "long" && champs[id].DATA_TYPE != "bigint")) {
                var opt = document.createElement('option');
                opt.text = champs[id].COLUMN_NAME;
                opt.value = champs[id].COLUMN_NAME;
                typecalculchamp.add(opt, null);
            } else if (champs[id].TABLE_NAME == tableName && (choice == "SUM" || choice == "AVG") && (champs[id].DATA_TYPE == "int" || champs[id].DATA_TYPE == "long" || champs[id].DATA_TYPE == "bigint")) {
                var opt = document.createElement('option');
                opt.text = champs[id].COLUMN_NAME;
                opt.value = champs[id].COLUMN_NAME;
                typecalculchamp.add(opt, null);
                nb++;
            }
        }
    }
    if (nb == 0) {
        document.getElementById("type_calcul_submit").disabled = true;
        document.getElementById(typeCalculChampID).style.borderColor = "red";
        document.getElementById(typeCalulID).style.borderColor = "red";
        document.getElementById("messageErreur").innerHTML = "Le type calcul SUM et AVG ont besoin d'un champ par défault";
    } else {
        document.getElementById("type_calcul_submit").disabled = false;
        document.getElementById(typeCalculChampID).style.borderColor = "";
        document.getElementById(typeCalulID).style.borderColor = "";
        document.getElementById("messageErreur").innerHTML = "";
    }

}
/**
 * Permet de changer la taille du menu de configuration des graphiques/tableaux
 */
function resizableOn(){
    var jq=jQuery.noConflict();
    jq( function() {
        jq( "#resizableLeft" ).resizable({
            handles : "e"
        });
    } );
    jq('#resizableLeft').resize(function(){
        jq('#resizableRight').width(jq("#resizableParent").width()-jq("#resizableLeft").width()-50);
    });
}

/**
 * Affiche la liste des bases de données, tables et colonnes
 */
function showDatabase(json, source) {
    var jq=jQuery.noConflict();
    for(database in json){
        jq('#database').append('<a data-toggle="collapse" href="#collapse_'+database+'" aria-expanded="false" aria-controls="collapseExample">'+database+'</a><br/>');
        jq('#database').append('<div class="collapse databaseShow" id="collapse_'+database+'"></div>');
        for(table in json[database]){
            jq('#collapse_'+database).append('<a data-toggle="collapse" href="#collapse_'+database+table+'" aria-expanded="false" aria-controls="collapseExample">'+table+'</a>');
            jq('#collapse_' + database).append('<a onclick="showSample(\'' + source + '\',\'' + database + '\',\'' + table + '\');"><span class="glyphicon glyphicon-th-list btn-xs"></span></a>');
            jq('#collapse_'+database).append('<div class="collapse databaseShow" id="collapse_'+database+table+'"></div><br/>');
            for(row in json[database][table]){
                jq('#collapse_'+database+table).append(json[database][table][row].name + ' ('+json[database][table][row].type+')');
                jq('#collapse_' + database + table).append('<a onclick="showColomnDiff(\'' + source + '\', \'' + database + '\',\'' + table + '\',\'' + json[database][table][row].name + '\')"><span class="glyphicon glyphicon-th-list btn-xs"></span></a><br/>');
            }
        }
    }
}

/**
 * Affiche une fenetre modale avec un tableau contenant un extrait de la table
 * @param database Nom de la base de donnée
 * @param table Nom de la table
 */
function showSample(source, database, table) {
    var jq=jQuery.noConflict();
    document.getElementById('myModalLabel').innerHTML = "Extrait de "+ database+"." + table;
    var urlDatabase = base_url + 'index.php/api/getSampleData?source=' + source + '&database=' + database + '&table=' + table;
    console.log(urlDatabase);

    jq('#test').append("<div class=\"filterBox\"><div id=\"floatingCirclesG\">\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_01\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_02\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_03\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_04\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_05\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_06\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_07\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_08\"></div>\n" +
        "</div></div>");
    
    jq('#myModal').modal('show');
    jq('#modalTableSample').append("<div class=\"filterBox\"><div id=\"floatingCirclesG\">\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_01\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_02\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_03\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_04\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_05\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_06\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_07\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_08\"></div>\n" +
        "</div></div>");
    jq.get(urlDatabase, function(data) {
        //tableCreateSampleModal(data,'modalTableSample');
        tableCreate1D(JSON.parse(data), "", "", -1, 'modalTableSample', false, true);
        tableCreate1D(JSON.parse(data), "", "", -1, 'test', false, true);
    });
}

function showTable(source, database, table) {
    var jq=jQuery.noConflict();
    //document.getElementById('myTable').innerHTML = "Extrait de "+ database+"." + table;
    var urlDatabase = base_url + 'index.php/api/getSampleData?source=' + source + '&database=' + database + '&table=' + table;
    console.log(urlDatabase);
    
    jq('#myTable').add("<div class=\"filterBox\"><div id=\"floatingCirclesG\">\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_01\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_02\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_03\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_04\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_05\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_06\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_07\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_08\"></div>\n" +
        "</div></div>");
    
    jq.get(urlDatabase, function(data) {
        //tableCreateSampleModal(data,'modalTableSample');
        tableCreate1D(JSON.parse(data), "", "", -1, 'myTable', false, true);
    });
}

/**
 * Affiche une fenetre modale avec un tableau contenant un les valeurs disponible pour un chammp
 * @param database nom de la base de donnée
 * @param table nom de la table
 * @param colimn nom de la colonne
 */
function showColomnDiff(source, database, table, colomn) {
    var jq=jQuery.noConflict();
    document.getElementById('myModalLabel').innerHTML = "Liste des valeurs disponible dans "+ database+"." + table + " pour la colonne "+colomn;

    var urlDatabase = base_url + 'index.php/api/getDiffValueColumn?source=' + source + '&database=' + database + '&table=' + table + '&column=' + colomn + '&database=' + database;
    console.log(urlDatabase);
    jq('#myModal').modal('show');
    if(document.getElementById('modalTableSample')){
        document.getElementById('modalTableSample').innerHTML = '';
    }
    jq('#modalTableSample').append("<div class=\"filterBox\"><div id=\"floatingCirclesG\">\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_01\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_02\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_03\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_04\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_05\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_06\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_07\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_08\"></div>\n" +
        "</div></div>");
    jq.get(urlDatabase, function(data) {
        tableCreate1D(JSON.parse(data),"","",-1,'modalTableSample');
    });
}

/**
 * BETA : Requete ajax pour afficher les alias
 * @param value nom de la table
 */
function showAliasTable(value){
    document.getElementById("aliasBox").innerText="";
    if(value==""){
        return;
    }
    var url=base_url+'index.php/api/getAlias?type=table&table='+value;
    var jq=jQuery.noConflict();
    jq.get(url, function(data) {
        var json = JSON.parse(data);
        //document.getElementById("aliasBox").innerText=data;
        tableCreate1D(JSON.parse(data),"","",-1,'aliasBox');
    });
}

/**
 * Permet dans la partie admin du site d'afficher un texte qui peut être afficher en mode normal ou en mode modification
 */
function switch_descriptiondatabase_mode(){
    var readmode = document.getElementById("descriptiondatabase_readmode");
    var writemode = document.getElementById("descriptiondatabase_writemode");
    if(writemode.style.display =="none"){
        writemode.style.display ="block"
        readmode.style.display ="none";
    }else{
        readmode.style.display ="block";
        writemode.style.display ="none";
    }
}

/**
 * Affiche un modal contenant les erreurs issue de la requete ajax
 * @param data Données de la requete ajax
 */
function show_error_ajax(data){
    var error_modal_body = document.getElementById("error_modal_body");
    var jq=jQuery.noConflict();
    iframe = document.createElement('iframe');
    iframe.id = "iframe_error_modal_body";
    error_modal_body.innerHTML="";
    error_modal_body.appendChild(iframe);
    iframe.contentWindow.document.write(data.responseText);

    jq('#error_modal').modal('show');

}
/**
 * Affiche un modal contenant le message voulue
 * @param data Données de la requete ajax
 */
function show_msg_ajax(data){
    var error_modal_body = document.getElementById("error_modal_body");
    var error_modal_label = document.getElementById("error_modal_label");
    var jq=jQuery.noConflict();
    var iframe = document.createElement('iframe');
    iframe.id = "iframe_error_modal_body";
    error_modal_body.innerHTML="";
    error_modal_label.innerHTML="Traitement en cours";
    jq('#error_modal_body').append("<h2>"+data+"</h2><div class=\"filterBox\"><div id=\"floatingCirclesG\">\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_01\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_02\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_03\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_04\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_05\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_06\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_07\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_08\"></div>\n" +
        "</div></div>");
    jq('#error_modal').modal('show');

}
/**
 * Ajoute une copie du champ dans le formulaire (limité à deux champs)
 * @param id identifiant du champs
 */
function addCopieSelect(id){
    var nb_max_form = 2;
    var select1 = document.getElementById(id + 0);
    var nbSelect = document.getElementsByClassName("wording").length;
    if (nbSelect >= nb_max_form) {
        return;
    }
    select1.name = "wording[]";
    var select2 = document.createElement("select");
    select2.innerHTML = select1.innerHTML;
    select2.className = "form-control selectFilter wording";
    select2.name = "wording[]";
    select2.size = 1;
    select2.id = "wording"+nbSelect;
    select1.parentNode.appendChild(select2);
    var remove_button = document.createElement("button");
    //remove_button.className = "removeButton";
    remove_button.className = "btn btn-default";
    remove_button.type = "button";
    remove_button.setAttribute('onclick', "removeCopieSelect('wording',"+nbSelect+");return false;");
    remove_button.id="wording"+nbSelect+"_button";
    var span_remove = document.createElement("span");
    span_remove.className = "glyphicon glyphicon-minus";
    remove_button.appendChild(span_remove);
    select2.parentNode.appendChild(remove_button);

    if (document.getElementsByClassName("wording").length >= 2) {
        document.getElementById("addWordingFormButton").disabled = true;
    }
}

/**
 * Retire le champs du formulaire en mode multi champ
 * @param id identifiant du champs
 * @param nb numéro du champs
 */
function removeCopieSelect(id,nb){
    var nb_max_form = 2;
    document.getElementById(id+nb).outerHTML = "";
    document.getElementById(id+nb+"_button").outerHTML = "";
    var nbSelect = document.getElementsByClassName("wording").length;
    if (nbSelect < nb_max_form) {
        document.getElementById(id+0).name="wording";
        document.getElementById("addWordingFormButton").disabled = false;
    }
}
/**
 * Generation d'un histogramme à partir des données disponnible
 * @param data Données à afficher
 * @param configGraph Configuration du graphique
 */
function generateHistoGraph(data, configGraph, min=-1, max=-1) {
    //Reset de l'affichage du graphique
    var canvasDiv = document.getElementById("canvasDiv");
    canvasDiv.innerHTML = "<canvas id=\"canvas\" width=\"100%\"></canvas>";
    var ctx = document.getElementById("canvas").getContext("2d");
    if(data.length==0){
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        return;
    }else {
        document.getElementById('chargement-message').innerHTML = "Génération du graphique";
        document.getElementById('chargement').className="alert alert-info";
        document.getElementById('chargement').style.display = 'block';
    }

    var datasets = [];
    var libelleGraph = new Set();
    var typeGraph = new Set();
    nb = [];
    if(configGraph.color==null){
        configGraph.color={};
    }

    var filter_mode = false;
    values_show = [];
    if(configGraph === undefined || configGraph.expert || configGraph.speedfilters === undefined){
        filter_mode = false;
    }else{
        jq=jQuery.noConflict();
        speedfilters = configGraph.speedfilters;
        configGraph.speedfilters.forEach(function(f){
            values = [];
            var ids = f.split("::");
            var id = ids[0];
            if(ids.length>1){
                id +=ids[1];
            }
            jq("#"+id+"_select :selected").each(function(){
                values.push(jq(this).val());
                filter_mode = true;
            });

            values_show[f] = values;
        });

    }
    data.forEach(function(v){
        var valid = true;
        Object.keys(values_show).forEach(function(k){

            var value = v[k];
            if(values_show[k].length==0){
            }else if((value==null && values_show[k].includes("NULL")) || values_show[k].includes(value)){
                valid = valid && true;
            }else if((value=="" && values_show[k].includes("VIDE")) || values_show[k].includes(value)){
                valid = valid && true;
            }else{
                valid = valid && false;
            }
        },v);

        if(!filter_mode || valid){
            if(v.libellesGraph==null){
                var libelle = "NULL";
            }else if(v.libellesGraph==""){
                var libelle = "VIDE";
            }else{
                var libelle = v.libellesGraph;
            }
            if(v.typesGraph==null){
                var type = "NULL";
            }else if(v.typesGraph==""){
                var type = "VIDE";
            }else{
                var type = v.typesGraph;
            }
            if(nb[libelle]==null){
                nb[libelle]=[];
            }
            if(nb[libelle][type]!=null){
                nb[libelle][type] += parseInt(v.nb);
            }else{
                nb[libelle][type] = parseInt(v.nb);
                libelleGraph.add(libelle);
                typeGraph.add(type);
            }
        }

    });
    total = [];
    if(configGraph.stacked=="100%"){
        libelleGraph.forEach(function(l){
            total[l] = 0;
            typeGraph.forEach(function(t){
                if(nb[l][t]!=null){
                    total[l] += nb[l][t];
                }else{
                    nb[l][t] = 0;
                }

            });
            typeGraph.forEach(function(t){
                nb[l][t] = nb[l][t]/total[l]*100;
                //console.log(l+" - "+t+" = "+nb[l][t]+" "+total);

            });

        });

    }
    var nbOcc = libelleGraph.size;

    console.log(nbOcc + " - " + nbType);
    if (libelleGraph.size == 0) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        return;

    }
    var nbType = 0;
    //Génération du datasets du graphique
    typeGraph.forEach(function(t){
        var name = t.replace(/\s/g,"_");

        if(configGraph.color[name+'_color']!=null){
            var color = configGraph.color[name+'_color'];
        }else{
            var color = getRandomColor();
            configGraph.color[name+'_color']=color;
        }
        var fill = true;
        if (configGraph.stacked == "aucun") {
            fill = false;
        }
        var dataset = {
            label : t,
            borderColor : color,
            backgroundColor : color,
            fill: fill,
            data : []
        };
        var typeShow = false;
        libelleGraph.forEach(function(l){
            if(configGraph.stacked=="100%"){
                var nb_temp=nb[l][t]/100*total[l];
            }else{
                var nb_temp=nb[l][t];
            }
            if (min == -1 || max == -1 || (configGraph.stacked!="100%" && nb_temp >= min && nb_temp <= max) || (configGraph.stacked=="100%" && nb_temp>= min && nb_temp<= max)) {
                typeShow = true;
                if (nb[l][t] == null) {
                    dataset.data.push(0);
                } else {
                    dataset.data.push(nb[l][t]);
                }
            } else {
                dataset.data.push(0);
            }
        });
        //Ajoute les données s'il y a au moins un libellé avec plus de 0 occurences
        if (typeShow) {
            nbType++;
            datasets.push(dataset);
        }

    });
    if (document.getElementById('number_result')) {
        document.getElementById('number_result').innerText = nbOcc + "-" + nbType;
    }
    if (nbOcc == 0) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        return;
    } else if (nbOcc > 500) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Il y a trop d’occurrences pour afficher le résultat (' + nbOcc + ') ! Le diagramme est capable d’afficher que 500 résultats';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        return;
    }
    if (nbType > 100) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Il y a trop d’occurrences de séparation pour afficher le résultat (' + nbType + ') ! Le diagramme est capable d’afficher que 100 résultats de séparation';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        return;
    } else if (nbType == 0) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        return;
    }


    barChartData = {
        labels: Array.from(libelleGraph),
        datasets: datasets
    };
    if(document.getElementById("canvas")){
        document.getElementById("canvas").remove();
        var jq=jQuery.noConflict();
        jq('#canvasDiv').append('<canvas id="canvas"><canvas>');
        var ctx = document.getElementById("canvas").getContext("2d");
    }
    if(configGraph.mode=="horizontalBar" && configGraph.stacked == "aucun" && nbOcc*nbType>40){
        var value = nbOcc*20*nbType;
        document.getElementById('canvasDiv').style.overflow="scroll"
        document.getElementById('canvasDiv').style.height="750px";
        //document.getElementById('canvas').style.height=value+"px";
        document.getElementById('canvas').height=value;
        //document.getElementById('canvas').style.width="1500px";
        document.getElementById('canvas').width=1500;

    }else if(configGraph.mode=="horizontalBar" && configGraph.stacked != "aucun" && nbOcc>40){
        var value = nbOcc*20;
        document.getElementById('canvasDiv').style.overflow="scroll"
        document.getElementById('canvasDiv').style.height="750px";
        //document.getElementById('canvas').style.height=value+"px";
        document.getElementById('canvas').height=value;
        //document.getElementById('canvas').style.width="1500px";
        document.getElementById('canvas').width=1500;
    }else{
        document.getElementById('canvasDiv').style.overflow="hidden"
        document.getElementById('canvas').style.height="auto";
    }

    var responsive = false;
    var stacked = false;
    var date_mode=false;
    if(configGraph.mode=="horizontalBar" && nbOcc>50){
        responsive = true;
    }
    if(configGraph.stacked!="aucun"){
        stacked=true;
    }
    var configChartJS = {
            type: configGraph.mode,
            data: barChartData,
            maintainAspectRatio: false,
            options: {
            title:{
                display:false,
                    text:"Evolution du nombre d'enveloppes traités"
            },
            legend:{
                position:'right'
            },
            tooltips: {
                mode: 'label',
                    //Change le message du tooltips
                    callbacks: {
                    label: function(tooltipItems, data){
                        var label = data.datasets[tooltipItems.datasetIndex].label;
                        var valueX = tooltipItems.xLabel;
                        var valueXModePercent = tooltipItems.xLabel;
                        var valueY = tooltipItems.yLabel;
                        var valueYModePercent = tooltipItems.yLabel;

                        var nbLabel="";
                        if(configGraph.mode=="bar" || configGraph.mode=="line"){
                            for (var labelD in data.labels) {
                                if(data.labels[labelD]===valueX){
                                    nbLabel=labelD;
                                }
                            }
                            var totalV = 0;
                            for (var i in data.datasets) {
                                for (var j in data.datasets[i].data) {
                                    if(j==nbLabel){
                                        totalV += Number(data.datasets[i].data[j]);
                                    }
                                }
                            }
                            var tooltipPercentage = (valueY / totalV) * 100;
                            if(configGraph.stacked=="100%"){
                                var value = window.total[valueX]/100*valueY;
                                return label + " : " + value.toLocaleString() + " (" + tooltipPercentage.toLocaleString()+"%)";

                            }else{
                                return label + " : " + valueY.toLocaleString() + " (" + tooltipPercentage.toLocaleString()+"%)";

                            }
                        }else{
                            for (var labelD in data.labels) {
                                if(data.labels[labelD]===valueY){
                                    nbLabel=labelD;
                                }
                            }
                            var totalV = 0;
                            for (var i in data.datasets) {
                                for (var j in data.datasets[i].data) {
                                    if(j==nbLabel){
                                        totalV += Number(data.datasets[i].data[j]);
                                    }
                                }
                            }
                            var tooltipPercentage = (valueX / totalV) * 100;
                            if(configGraph.stacked=="100%"){
                                var value = window.total[valueY]/100*valueX;
                                return  label + " : " + value.toLocaleString() + " (" + tooltipPercentage.toLocaleString()+"%)";

                            }else{
                                return  label + " : " + valueX.toLocaleString() + " (" + tooltipPercentage.toLocaleString()+"%)";

                            }
                        }

                    }
                },
                responsive: responsive
            },
            scales : {
                yAxes: [{
                    stacked: stacked,
                    ticks: {
                        beginAtZero: true,
                        //Change le format du nombre de l'axe X
                        userCallback: function(value, index, values) {
                            if(configGraph.mode== "bar") {
                                if(configGraph.stacked=="100%"){
                                    var percent = "%";
                                }else{
                                    var percent = "";
                                }

                                return value.toLocaleString()+percent;
                            } else if(date_mode) {
                                return value.toISOString().slice(0, 10);
                            }else{
                                return value;
                            }
                        }
                    },
                }],
                    xAxes: [{
                    stacked: stacked,
                    ticks: {
                        beginAtZero: true,
                        //Change le format du nombre de l'axe Y
                        userCallback: function(value, index, values) {
                            if(configGraph.mode== "horizontalBar") {
                                if(configGraph.stacked=="100%"){
                                    var percent = "%";
                                }else{
                                    var percent = "";
                                }
                                return value.toLocaleString()+percent;
                            } else if(date_mode) {
                                return value.toISOString().slice(0, 10);
                            }else{
                                return value;
                            }
                        }
                    },
                }]
            }
        }
    };
    if(configGraph.stacked=="100%" && (configGraph.mode == "bar" || configGraph.mode== "line")){
        configChartJS.options.scales.yAxes[0].ticks.max=100;
    }else if(configGraph.stacked=="100%" && (configGraph.mode == "horizontalBar")){
        configChartJS.options.scales.xAxes[0].ticks.max=100;
    }
    myBar = new Chart(ctx, configChartJS);
    //if(){
    //    myBar.options.scales.yAxes
    //}
    document.getElementById('chargement-message').innerHTML = "";
    document.getElementById('chargement').className="";
    document.getElementById('chargement').style.display = 'none';
    document.getElementById('loader-img').style.display = 'none';
}
/**
 * Generation d'un diagramme circulaire à partir des données disponnible
 * @param data Données à afficher
 * @param configGraph Configuration du graphique
 */
function generatePieGraph(data, configGraph, min=-1, max=-1) {
    //Reset de l'affichage du graphique
    var canvasDiv = document.getElementById("canvasDiv");
    canvasDiv.innerHTML = "<canvas id=\"canvas\" width=\"100%\"></canvas>";
    var ctx = document.getElementById("canvas").getContext("2d");
    if(data.length==0){
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        return;
    }else {
        document.getElementById('chargement-message').innerHTML = "Génération du graphique";
        document.getElementById('chargement').className="alert alert-info";
        document.getElementById('chargement').style.display = 'block';
    }

    var datasets = {
        data : [],
        backgroundColor : []
    };
    datasets['data'] = [];
    datasets['backgroundColor'] = [];
    var libelleGraph = new Set();
    var nb = [];
    if(configGraph.color==null){
        configGraph.color={};
    }

    var filter_mode = false;
    values_show = [];
    if(configGraph === undefined || configGraph.expert || configGraph.speedfilters === undefined){
        filter_mode = false;
    }else{
        jq=jQuery.noConflict();
        speedfilters = configGraph.speedfilters;
        configGraph.speedfilters.forEach(function(f){
            values = [];
            var ids = f.split("::");
            var id = ids[0];
            if(ids.length>1){
                id +=ids[1];
            }
            jq("#"+id+"_select :selected").each(function(){
                values.push(jq(this).val());
                filter_mode = true;
            });

            values_show[f] = values;
        });

    }

    data.forEach(function(v){
        var valid = true;
        Object.keys(values_show).forEach(function(k){

            var value = v[k];
            if(values_show[k].length==0){
            }else if((value==null && values_show[k].includes("NULL")) || values_show[k].includes(value)){
                valid = valid && true;
            }else if((value=="" && values_show[k].includes("VIDE")) || values_show[k].includes(value)){
                valid = valid && true;
            }else{
                valid = valid && false;
            }
        },v);

        if(!filter_mode || valid){
            if(v.libellesGraph==null){
                var libelle = "NULL";
                if(nb[libelle]!=null){
                    nb[libelle] += parseInt(v.nb);
                }else{
                    nb[libelle] = parseInt(v.nb);
                    libelleGraph.add(libelle);
                }

            }else if(v.libellesGraph==""){
                var libelle = "VIDE";

                if(nb[libelle]!=null){
                    nb[libelle] += parseInt(v.nb);
                }else{
                    nb[libelle] = parseInt(v.nb);
                    libelleGraph.add(libelle);
                }

            }else{
                if(nb[v.libellesGraph]!=null){
                    nb[v.libellesGraph] += parseInt(v.nb);
                }else{
                    nb[v.libellesGraph] = parseInt(v.nb);
                    libelleGraph.add(v.libellesGraph);
                }
            }
        }

    });
    var nbShow = 0;
    //Génération du datasets du graphique
    libelleGraph.forEach(function (v) {
        if (min == -1 || max == -1 || (nb[v] >= min && nb[v] <= max)) {
            nbShow++;
            var name = v.replace(/\s/g, "_")
            if (configGraph.color[name + '_color'] != null) {
                datasets.backgroundColor.push(configGraph.color[name + '_color']);
            } else {
                var color = getRandomColor();
                datasets.backgroundColor.push(color);
                configGraph.color[name + '_color'] = color;
            }

            datasets.data.push(nb[v]);
        } else {
            libelleGraph.delete(v);
        }


    });

    if (document.getElementById('number_result')) {
        document.getElementById('number_result').innerText = nbShow;
    }
    if (nbShow > 500) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Il y a trop d’occurrences pour afficher le résultat (' + nbShow + ') ! Le diagramme est capable d’afficher que 500 résultats';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        return;
    } else if (nbShow == 0) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        return;

    }


    barChartData = {
        labels: Array.from(libelleGraph),
        datasets: [datasets]
    };
    if(document.getElementById("canvas")){
        document.getElementById("canvas").remove();
        var jq=jQuery.noConflict();
        jq('#canvasDiv').append('<canvas id="canvas"><canvas>');
        var ctx = document.getElementById("canvas").getContext("2d");
    }

    myChart = new Chart(ctx, {
        type: configGraph.mode,
        data: barChartData,
        options: {
            title:{
                display:false,
                text:"Evolution du nombre d'enveloppes traités"
            },
            legend:{
                position:'right'
            },
            tooltips: {
                mode: 'label',
                //Change le message du tooltips
                callbacks: {
                    label: function(tooltipItems, data) {
                        var indice = tooltipItems.index;
                        var sum = 0;
                        for(x in data.datasets[0].data){
                            sum+=parseInt(data.datasets[0].data[x]);
                        }
                        var percent = parseInt(data.datasets[0].data[indice])/sum*100;
                        return  data.labels[indice] +': '+parseInt(+data.datasets[0].data[indice]).toLocaleString() + '('+percent.toLocaleString()+'%)';
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: true,
            animation: false
        }
    });
    document.getElementById('chargement-message').innerHTML = "";
    document.getElementById('chargement').className="";
    document.getElementById('chargement').style.display = 'none';
    document.getElementById('loader-img').style.display = 'none';
}

/**
 * Genération des formulaires de changement de couleur pour les histogramme
 * @param config configuration des graphiques
 */
function getHistoColorGraph(config){
    if(!document.getElementById('colormanager')){
        return;
    }
    if(config.mode=="line"){
        graphColor = "borderColor";
    }else{
        graphColor = "backgroundColor";
    }
    document.getElementById('colormanager').innerHTML = "";
    for(x in barChartData.datasets){
        var name = barChartData.datasets[x].label.replace(/\s/g,"_");
        document.getElementById('colormanager').innerHTML += '<input type="color" class="btn btn-outline-primary" value="'+barChartData.datasets[x][graphColor]+'" id="'+name+'_color" name="'+barChartData.datasets[x].label+'_color" onchange="changeColorHisto(\''+name+'\')"><label for="'+barChartData.datasets[x].label+'_color">'+barChartData.datasets[x].label+'</label><br>';
    }
}

/**
 * Genération des formulaires de changement de couleur pour les diagrammes circulaire
 */
function getPieColorGraph(){
    if(!document.getElementById('colormanager')){
        return;
    }
    document.getElementById('colormanager').innerHTML = "";
    for(x in barChartData.labels){
        var name = barChartData.labels[x].replace(/\s/g,"_");
        document.getElementById('colormanager').innerHTML += '<input type="color" class="btn btn-outline-primary" value="'+barChartData.datasets[0].backgroundColor[x]+'" id="'+name+'_color" name="'+x+'_color" onchange="changeColorPie(\''+name+'\')"><label for="'+x+'_color">'+barChartData.labels[x]+'</label><br>';
    }
}
/**
 * Creation des champs pour les filtres rapides
 * @param data données du tableau
 * @param config configuration du tableau
 */
function createSpeedFiltersHisto(data, config) {
    var jq = jQuery.noConflict();
    if(!config.speedfilters){
        return;
    }
    config.speedfilters.forEach(function (o) {
        var select = document.createElement('select');
        var ids = o.split("::");
        var title = ids[0];
        var id = ids[0];
        if (ids.length > 1) {
            title += " (" + ids[1] + ")";
            id += ids[1];
        }
        select.id = id + "_select";
        select.class = "chosen-select";
        select.placeholder = "Ajouter une valeur";
        select.multiple = true;
        select.onchange = function () {
            generateHistoGraph(data, configGraph, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
        };
        var options = new Set();
        data.forEach(function (line) {
            options.add(line[o]);
        });
        options.forEach(function (op) {
            var option = document.createElement('option');
            if (op == null) {
                option.innerHTML = "NULL";
            } else if (op == "") {
                option.innerHTML = "VIDE";
            } else {
                option.innerHTML = op;
            }

            select.appendChild(option);
        });
        document.getElementById("speed-filters").appendChild(document.createTextNode(title));
        document.getElementById("speed-filters").appendChild(document.createElement("br"));
        document.getElementById("speed-filters").appendChild(select);
        document.getElementById("speed-filters").appendChild(document.createElement("br"));
        jq("#" + select.id).chosen({
            max_shown_results: 50,
            no_results_text: "Aucun résultat pour",
            width: "100%",
            placeholder_text_multiple: "Toutes les valeurs"
        });
    });
}
/**
 * Creation des champs pour les filtres rapides
 * @param data données du tableau
 * @param config configuration du tableau
 */
function createSpeedFiltersPie(data, config) {
    var jq = jQuery.noConflict();
    if(!config.speedfilters){
        return;
    }
    config.speedfilters.forEach(function (o) {
        var select = document.createElement('select');
        var ids = o.split("::");
        var title = ids[0];
        var id = ids[0];
        if (ids.length > 1) {
            title += " (" + ids[1] + ")";
            id += ids[1];
        }
        select.id = id + "_select";
        select.class = "chosen-select";
        select.placeholder = "Ajouter une valeur";
        select.multiple = true;
        select.onchange = function () {
            generatePieGraph(data, configGraph, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
        };
        var options = new Set();
        data.forEach(function (line) {
            options.add(line[o]);
        });
        options.forEach(function (op) {
            var option = document.createElement('option');
            if (op == null) {
                option.innerHTML = "NULL";
            } else if (op == "") {
                option.innerHTML = "VIDE";
            } else {
                option.innerHTML = op;
            }

            select.appendChild(option);
        });
        document.getElementById("speed-filters").appendChild(document.createTextNode(title));
        document.getElementById("speed-filters").appendChild(document.createElement("br"));
        document.getElementById("speed-filters").appendChild(select);
        document.getElementById("speed-filters").appendChild(document.createElement("br"));
        jq("#" + select.id).chosen({
            max_shown_results: 50,
            no_results_text: "Aucun résultat pour",
            width: "100%",
            placeholder_text_multiple: "Toutes les valeurs"
        });
    });
}


/**
 * Génération d'un formulaire d'intervalle pour modifier une pie
 * @param data Donnée pour le graphique
 */
function createSpeedFilterRangePie(data, configGraph) {
    console.log("DEBUT createSpeedFilterRangePie : " + Date());
    if (!document.getElementById("speed-filters")) {
        return;
    }
    //Calcule du minimum et du maximum
    var maxValue = 0;
    var values = [];
    var libs = configGraph.champs;
    var calc = configGraph.typecalcul.toLowerCase() + "(" + configGraph.typecalculchamp + ")";

    //Calcule valeur pour le couple lib et type
    data.forEach(function (value) {
        var lib = "";
        if (value.libellesGraph == null) {
            lib = "NULL"
        } else {
            lib = value.libellesGraph;
        }
        if (values[lib] != null) {
            values[lib] = values[lib] + Number(value.nb);
        } else {
            values[lib] = Number(value.nb);
        }
    });
    var nbDataShow = 0;
    for (var key in values) {
        nbDataShow++;
        //Recherche de la valeur la plus grande
        var value = values[key];
        if (value > maxValue) {
            maxValue = value;
        }
    }
    delete values;
    if (maxValue == Number.MAX_VALUE) {
        return;
    }

    var speed_filters_div = document.getElementById("speed-filters");
    var label = document.createElement("span");
    label.innerText = "Intervalle : Il y a ";
    speed_filters_div.appendChild(label);

    var label = document.createElement("label");
    label.id = "number_result";
    label.innerText = nbDataShow;
    speed_filters_div.appendChild(label);

    var label = document.createElement("span");
    label.innerText = " informations à afficher (<=500 résultats pour afficher un graphique).";
    speed_filters_div.appendChild(label);

    var input_group = document.createElement("div");
    input_group.className = "input-group";

    var span = document.createElement("span");
    span.id = "min_span";
    span.innerText = "Min";
    span.className = "input-group-addon";
    input_group.appendChild(span);
    var input = document.createElement("input");
    input.type = "text";
    input.id = "min_input";
    input.placeholder = 1;
    input.className = "form-control";
    input.setAttribute("aria-describedby", "min_span");
    input_group.appendChild(input);

    var span = document.createElement("span");
    span.innerText = "Min";
    span.className = "input-group-btn";


    var button = document.createElement("button");
    button.type = "button";
    button.className = "btn btn-default";
    button.innerText = "X";
    button.setAttribute("onclick", "updateSlider('min_input', " + 1 + ")");
    span.appendChild(button);

    input_group.appendChild(span);

    speed_filters_div.appendChild(input_group);


    var span = document.createElement("span");
    span.id = "max_span";
    span.innerText = "Max";
    span.className = "input-group-addon";
    input_group.appendChild(span);
    var input = document.createElement("input");
    input.type = "text";
    input.id = "max_input";
    input.placeholder = maxValue;
    input.className = "form-control";

    input.setAttribute("aria-describedby", "max_span");
    input_group.appendChild(input);

    var span = document.createElement("span");
    span.innerText = "Max";
    span.className = "input-group-btn";


    var button = document.createElement("button");
    button.type = "button";
    button.className = "btn btn-default";
    button.innerText = "X";
    button.setAttribute("onclick", "updateSlider('max_input', " + maxValue + ")");
    span.appendChild(button);

    input_group.appendChild(span);

    speed_filters_div.appendChild(input_group);
    speed_filters_div.appendChild(document.createElement("br"));
    var divSlider = document.createElement("div");
    divSlider.id = "slider-range";
    speed_filters_div.appendChild(divSlider);
    jq = jQuery.noConflict();
    var slider = jq(function () {
        jq("#slider-range").slider({
            range: true,
            min: 1,
            max: maxValue,
            values: [1, maxValue],
            slide: function (event, ui) {
                //Activation quand on utilise le slide
                jq("#min_input").val(ui.values[0]);
                jq("#max_input").val(ui.values[1]);
                //console.log(jq( "#slider-range" ).slider( "values", 0 )+" - "+jq( "#slider-range" ).slider( "values", 1 ));
                //tableCreate(data, configGraph,ui.values[0],ui.values[1]);
                generatePieGraph(data, configGraph, ui.values[0], ui.values[1]);
            }
        });
        jq("#min_input").val(1);
        jq("#max_input").val(maxValue);
        jq("#min_input").on("keyup", function () {
            //Activation quand on modifie le champs min
            jq("#slider-range").slider("values", 0, this.value);
            generatePieGraph(data, configGraph, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
        });
        jq("#max_input").on("keyup", function () {
            //Activation quand on modifie le champs max
            jq("#slider-range").slider("values", 1, this.value);
            generatePieGraph(data, configGraph, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
        });
    });
    console.log("FIN createSpeedFilterRangePie : " + Date());
}

/**
 * Génération d'un formulaire d'intervalle pour modifier une histo
 * @param data Donnée pour le graphique
 */
function createSpeedFilterRangeHisto(data, configGraph) {
    console.log("DEBUT createSpeedFilterRangeHisto : " + Date());
    if (!document.getElementById("speed-filters")) {
        return;
    }
    //Calcule du minimum et du maximum
    var maxValue = 0;
    var values = [];
    var libs = configGraph.champs;
    var calc = configGraph.typecalcul.toLowerCase() + "(" + configGraph.typecalculchamp + ")";

    //Calcule valeur pour le couple lib et type
    data.forEach(function (value) {
        var type = "";
        if (value.typesGraph == null) {
            type = "NULL"
        } else {
            type = value.typesGraph;
        }
        var lib = "";
        if (value.libellesGraph == null) {
            lib = "NULL"
        } else {
            lib = value.libellesGraph;
        }
        var id = lib + "" + type;
        if (values[id] != null) {
            values[id] = values[id] + Number(value.nb);
        } else {
            values[id] = Number(value.nb);
        }
    });
    var nbDataShow = 0;
    for (var key in values) {
        nbDataShow++;
        //Recherche de la valeur la plus grande
        var value = values[key];
        if (value > maxValue) {
            maxValue = value;
        }
    }
    delete values;

    var speed_filters_div = document.getElementById("speed-filters");
    var label = document.createElement("span");
    label.innerText = "Intervalle : Il y a ";
    speed_filters_div.appendChild(label);

    var label = document.createElement("label");
    label.id = "number_result";
    label.innerText = nbDataShow;
    speed_filters_div.appendChild(label);

    var label = document.createElement("span");
    label.innerText = " informations à afficher (<=500-100 résultats pour afficher un graphique).";
    speed_filters_div.appendChild(label);

    var input_group = document.createElement("div");
    input_group.className = "input-group";

    var span = document.createElement("span");
    span.id = "min_span";
    span.innerText = "Min";
    span.className = "input-group-addon";
    input_group.appendChild(span);
    var input = document.createElement("input");
    input.type = "text";
    input.id = "min_input";
    input.placeholder = 1;
    input.className = "form-control";
    input.setAttribute("aria-describedby", "min_span");
    input_group.appendChild(input);

    var span = document.createElement("span");
    span.innerText = "Min";
    span.className = "input-group-btn";


    var button = document.createElement("button");
    button.type = "button";
    button.className = "btn btn-default";
    button.innerText = "X";
    button.setAttribute("onclick", "updateSlider('min_input', " + 1 + ")");
    span.appendChild(button);

    input_group.appendChild(span);

    speed_filters_div.appendChild(input_group);


    var span = document.createElement("span");
    span.id = "max_span";
    span.innerText = "Max";
    span.className = "input-group-addon";
    input_group.appendChild(span);
    var input = document.createElement("input");
    input.type = "text";
    input.id = "max_input";
    input.placeholder = maxValue;
    input.className = "form-control";

    input.setAttribute("aria-describedby", "max_span");
    input_group.appendChild(input);

    var span = document.createElement("span");
    span.innerText = "Max";
    span.className = "input-group-btn";


    var button = document.createElement("button");
    button.type = "button";
    button.className = "btn btn-default";
    button.innerText = "X";
    button.setAttribute("onclick", "updateSlider('max_input', " + maxValue + ")");
    span.appendChild(button);

    input_group.appendChild(span);

    speed_filters_div.appendChild(input_group);
    speed_filters_div.appendChild(document.createElement("br"));
    var divSlider = document.createElement("div");
    divSlider.id = "slider-range";
    speed_filters_div.appendChild(divSlider);
    jq = jQuery.noConflict();
    var slider = jq(function () {
        jq("#slider-range").slider({
            range: true,
            min: 1,
            max: maxValue,
            values: [1, maxValue],
            slide: function (event, ui) {
                //Activation quand on utilise le slide
                jq("#min_input").val(ui.values[0]);
                jq("#max_input").val(ui.values[1]);
                generateHistoGraph(data, configGraph, ui.values[0], ui.values[1]);
            }
        });
        jq("#min_input").val(1);
        jq("#max_input").val(maxValue);
        jq("#min_input").on("keyup", function () {
            //Activation quand on modifie le champs min
            jq("#slider-range").slider("values", 0, this.value);
            generateHistoGraph(data, configGraph, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
        });
        jq("#max_input").on("keyup", function () {
            //Activation quand on modifie le champs max
            jq("#slider-range").slider("values", 1, this.value);
            generateHistoGraph(data, configGraph, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
        });
    });
    console.log("FIN createSpeedFilterRangePie : " + Date());
}

/**
 * Remise à zéro du formulaire min ou max et mise à jour de l'intervalle et du graphique
 * @param id Identifiant du formulaire
 * @param value Valeur à appliquer
 */
function updateSlider(id, value) {
    var input = document.getElementById(id);
    input.value = value;
    //Force l'event keyup pour activer la mise à jour du graphique et de l'intervalle
    input.dispatchEvent(new Event('keyup'));
}