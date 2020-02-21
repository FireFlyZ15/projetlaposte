/**
 * Création d'un tableau à deux dimensions
 * @param data Liste de données
 * @param configJSON Configuration du graphique
 */
function tableCreate(datalist, config, min=-1, max=-1) {
    var tbl = document.getElementById('table');
    tbl.innerHTML = '';
    if(datalist.length==0){
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        //Affichage des statistiques sur le tableau
        if (document.getElementById('nb_line') != null && document.getElementById('nb_line_show') != null && document.getElementById('nb_column') != null && document.getElementById('nb_column_show') != null) {
            document.getElementById('nb_line').innerHTML = "0";
            document.getElementById('nb_line_show').innerHTML = "0";
            document.getElementById('nb_column').innerHTML = "0";
            document.getElementById('nb_column_show').innerHTML = "0";
        }
        return;
    }else {
        document.getElementById('chargement-message').innerHTML = "Génération du tableau";
        document.getElementById('chargement').className="alert alert-info";
        document.getElementById('chargement').style.display = 'block';
    }

    var dataNoFilter = datalist.slice();
    var filter_mode = true;

    var values_show = [];

    if(configGraph === undefined || configGraph.expert || configGraph.speedfilters === undefined){
        filter_mode = false;
    }else{
        //Recupération des filtres rapides
        jq=jQuery.noConflict();
        speedfilters = configGraph.speedfilters;
        configGraph.speedfilters.forEach(function(f){
            var values = [];
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
    var nb_line = 0;
    var nb_colomn = 0;

    //console.log("filter_mode : " + filter_mode);

    if(filter_mode){
        var line_array = [];
        var colomn_array = [];
        var newdata = [];
        for(var x in dataNoFilter){
            var valid = true;
            colomn_array[dataNoFilter[x]["libellesGraph"] + ""] = "";
            line_array[dataNoFilter[x]["typesGraph"] + ""] = "";
            Object.keys(values_show).forEach(function(k){
                var value = dataNoFilter[x][k];
                if(values_show[k].length==0){
                } else if ((value == null && values_show[k].includes("NULL")) || (value == "" && values_show[k].includes("VIDE")) || values_show[k].includes(value)) {
                    valid = valid && true;
                }else{
                    valid = valid && false;
                }
            },x);

            if(!filter_mode || valid){
                var key = dataNoFilter[x].libellesGraph+";"+dataNoFilter[x].typesGraph;
                if(newdata[key]!=null){
                    newdata[key].nb = Number(dataNoFilter[x].nb) + Number(newdata[key].nb);
                }else{
                    var o = {};
                    o.libellesGraph = dataNoFilter[x].libellesGraph;
                    o.typesGraph = dataNoFilter[x].typesGraph;
                    o.nb = dataNoFilter[x].nb;
                    newdata[key] = o;
                }
            }

        }
        data = [];
        var nbShow = 0;
        for (var o in newdata){
            if (min == -1 || max == -1 || (newdata[o].nb >= min && newdata[o].nb <= max)) {
                nbShow++;
                data.push(newdata[o]);
            }

        }
        if (document.getElementById('number_result')) {
            document.getElementById('number_result').innerText = nbShow;
        }
        delete newdata;
        nb_line = Object.keys(line_array).length;
        delete line_array;
        nb_colomn = Object.keys(colomn_array).length;
        delete colomn_array;
    }else{
        var data = dataNoFilter;
    }

    delete dataNoFilter;
    delete values_show;

    if(data.length==0){
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        //Affichage des statistiques sur le tableau
        if (document.getElementById('nb_line') != null && document.getElementById('nb_line_show') != null && document.getElementById('nb_column') != null && document.getElementById('nb_column_show') != null) {
            document.getElementById('nb_line').innerHTML = nb_line;
            document.getElementById('nb_line_show').innerHTML = "0";
            document.getElementById('nb_column').innerHTML = nb_colomn;
            document.getElementById('nb_column_show').innerHTML = "0";
        }
        return;
    }

    var thead = document.createElement('thead');
    var tbdy = document.createElement('tbody');
    tbdy.className = "table-overflow";
    //La classe des celules total du tableau
    var classTotal = "info"
    var tr = document.createElement('tr');

    var array = [];
    var arrayLib = [];
    var xTotal = [];
    var yTotal = [];
    for (item in data) {
        var l2;
        if(config.wording=="mois" || config.wording=="month"){
            l2 = monthTranslate(data[item].libellesGraph);
        }else{
            l2 = data[item].libellesGraph;
        }
        var l1;
        if(config.group=="mois" || config.group=="month"){
            l1 = monthTranslate(data[item].typesGraph);
        }else{
            l1 = data[item].typesGraph;
        }

        if(array[l1]==null){
            array[l1] = [];
        }
        array[l1][l2] = data[item].nb;
        arrayLib[l2] = "";



    }
    //Affichage de l'entete du tableau
    var i = 0;
    for (var item in arrayLib) {
        if(i==0){
            var th = document.createElement('th');
            th.appendChild(document.createTextNode("#"));
            tr.appendChild(th);
        }
        var th2 = document.createElement('th');
        th2.appendChild(document.createTextNode(item));
        tr.appendChild(th2);
        //Initialisation du tableau pour le calcule des totals des colones
        yTotal[item]=0;

        i++;
    }
    if(config.typecalcul!="AVG") {
        var th2 = document.createElement('th');
        th2.className = classTotal;
        th2.appendChild(document.createTextNode("TOTAL"));
        tr.appendChild(th2);
    }
    thead.appendChild(tr);
    tbl.appendChild(thead);
    var i = 0;

    for (var item in array) {
        xTotal[item]=0;
        var tr = document.createElement('tr');
        var th = document.createElement('th');
        th.appendChild(document.createTextNode(item));
        tr.appendChild(th);

        for (var item2 in arrayLib) {
            var td = document.createElement('td');
            var nb = array[item][item2];
            if(nb==null){
                td.appendChild(document.createTextNode(""));
            }else{
                td.appendChild(document.createTextNode(Intl.NumberFormat().format(nb)));

                xTotal[item]+=Number(nb);
                yTotal[item2]+=Number(nb);
            }

            tr.appendChild(td);
        }
        //Affichage du total
        if(config.typecalcul!="AVG") {
            var th = document.createElement('th');
            th.className = classTotal;
            th.appendChild(document.createTextNode(Intl.NumberFormat().format(xTotal[item])));
            tr.appendChild(th);
        }
        //Passage ligne suivante
        tbdy.appendChild(tr);
        i++;
    }
    tbl.appendChild(tbdy);
    if(config.typecalcul!="AVG"){
        var tr = document.createElement('tr');
        var th = document.createElement('th');
        th.className=classTotal;
        th.appendChild(document.createTextNode("TOTAL"));
        tr.appendChild(th);
        //Affichage du total par colonne
        var total = 0;
        for (var item3 in yTotal) {
            var th = document.createElement('th');
            th.className=classTotal;
            total += Number(yTotal[item3]);
            th.appendChild(document.createTextNode(Intl.NumberFormat().format(yTotal[item3])));
            tr.appendChild(th);
        }

        //Affichage du total du tableau
        var th = document.createElement('th');
        th.className=classTotal;
        th.appendChild(document.createTextNode(Intl.NumberFormat().format(total)));
        tr.appendChild(th);
    }


    tbdy.appendChild(tr);
    document.getElementById('chargement-message').innerHTML = "";
    document.getElementById('chargement').className="";
    document.getElementById('chargement').style.display = 'none';
    document.getElementById('loader-img').style.display = 'none';

    //Affichage des statistiques sur le tableau
    if (document.getElementById('nb_line') != null && document.getElementById('nb_line_show') != null && document.getElementById('nb_column') != null && document.getElementById('nb_column_show') != null) {

        var nb_column_show = Object.keys(arrayLib).length;
        var nb_line_show = Object.keys(array).length;
        if (!filter_mode) {
            nb_line = nb_line_show;
            nb_colomn = nb_column_show;
        }
        document.getElementById('nb_line').innerHTML = nb_line;
        document.getElementById('nb_line_show').innerHTML = nb_line_show;
        document.getElementById('nb_column').innerHTML = nb_colomn;
        document.getElementById('nb_column_show').innerHTML = nb_column_show;
    }


}

/**
 * Création d'un tableau à une dimension permettant un système de tri
 * @param datalist (List d'objet)
 * @param sorting_colomn Le nom de la colonne à trier
 * @param sorting_type Le type de triage (asc, desc)
 */
function tableCreate1D(datalist, sorting_colomn, sorting_type, page = -1, id="table", speed_filter_flag=true, mode_sample=false) {
    var slider_mode = false;
    //Prise en compte de l'intervalle
    if (!mode_sample && document.getElementById("slider-range")) {
        slider_mode = true;
        var min = document.getElementById("min_input").value;
        var max = document.getElementById("max_input").value;
        console.log(min + "-" + max);
    }
    //Copie la liste pour garder la version sans triage
    var data = datalist.slice();
    var nb_line = data.length;
    var tbl = document.getElementById(id);
    tbl.innerHTML = '';
    if (!mode_sample) {
        if (data.length == 0) {
            document.getElementById('chargement').className = "alert alert-warning";
            document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
            document.getElementById('loader-img').style.display = 'none';
            document.getElementById('chargement').style.display = 'block';
            //Affichage des statistiques sur le tableau
            if (document.getElementById('nb_line') != null && document.getElementById('nb_line_show') != null && document.getElementById('nb_column') != null) {
                document.getElementById('nb_line').innerHTML = 0;
                document.getElementById('nb_line_show').innerHTML = 0;
                document.getElementById('nb_column').innerHTML = 0;
            }
            return;
        } else {
            document.getElementById('chargement-message').innerHTML = "Génération du tableau";
            document.getElementById('chargement').className = "alert alert-info";
            document.getElementById('chargement').style.display = 'block';
        }
    } else {
        if (data.length == 0) {
            document.getElementById(id).innerHTML = "<div class='alert alert-warning'>Aucune donnée disponible!</div>";
        }
    }

    var nb_column = Object.keys(data[0]).length;
    var colomns_hide = [];
    var speedfilters = [];
    values_show = [];
    var filter_mode = true;
    if (!speed_filter_flag || configGraph === undefined || (configGraph.expertmode && configGraph.speedfilters !== undefined && configGraph.speedfilters.length == 0) || configGraph.speedfilters === undefined) {

    }else{
        jq=jQuery.noConflict();
        speedfilters = configGraph.speedfilters;
        configGraph.speedfilters.forEach(function(f){
            if (!configGraph.expertmode && !configGraph.champs.includes(f)) {
                console.log("ajout liste hide " + f);
                console.log(configGraph.expertmode);
                colomns_hide.push(f);
            }
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
    var thead = document.createElement('thead');
    var tbdy = document.createElement('tbody');
    tbdy.className = "table-overflow";
    //La classe des celules total du tableau
    var classTotal = "info"
    var tr = document.createElement('tr');

    var arrayLib = [];
    //Génération de l'entête du tableau
    Object.keys(data[0]).forEach(function(l){
        if(!colomns_hide.includes(l)){
            arrayLib.push(l);
            var th2 = document.createElement('th');
            th2.appendChild(document.createTextNode(l));

            //Ajout du bouton de tri par ordre croissant/alphabetique
            var button_image_desc = document.createElement('span');
            button_image_desc.className = "glyphicon glyphicon-chevron-up";
            var button_desc = document.createElement('button');
            button_desc.id = "button_sort_"+l+"_desc";
            button_desc.className = "btn btn-default btn-xs right-float";
            button_desc.appendChild(button_image_desc);
            button_desc.onclick = function () {
                if(sorting_colomn==l && sorting_type=="desc"){

                    tableCreate1D(datalist,'','',page,id,speed_filter_flag);
                    //document.getElementById("button_sort_"+l+"_desc").className = "btn btn-default btn-xs right-float";
                }else{
                    tableCreate1D(datalist,l,'desc',page,id,speed_filter_flag);
                    //document.getElementById("button_sort_"+l+"_desc").className = "btn btn-success btn-xs right-float";
                }
            };
            th2.appendChild(button_desc);
            var button_image_asc = document.createElement('span');
            button_image_asc.className = "glyphicon glyphicon-chevron-down";

            //Ajout du bouton de tri par ordre decroissant/dealphabetique
            var button_asc = document.createElement('button');
            button_asc.id = "button_sort_"+l+"_asc";
            button_asc.className = "btn btn-default btn-xs right-float";
            button_asc.appendChild(button_image_asc);
            button_asc.onclick = function () {
                if(sorting_colomn==l && sorting_type=="asc"){
                    tableCreate1D(datalist,'','',page,id,speed_filter_flag);
                }else{
                    tableCreate1D(datalist,l,'asc',page,id,speed_filter_flag);
                }
            };
            th2.appendChild(button_asc);
            tr.appendChild(th2);
        }

    });

    if (!mode_sample && !configGraph.expertmode && speed_filter_flag) {
        //Ajout d'une colonne info
        var th2 = document.createElement('th');
        th2.appendChild(document.createTextNode("info"));
        tr.appendChild(th2);
    }

    thead.appendChild(tr);
    tbl.appendChild(thead);

    if(page!=-1){
        var nbmin = page*10000;
        var nbmax = nbmin+10000;
    }else{
        var nbmin = 0;
        var nbmax = Number.MAX_SAFE_INTEGER;
    }
    var nb = 0;
    var newdata = [];
    //console.log(newdata);
    //Filtrage et rassemblement des valeurs pour prendre en compte les filtres rapide
    data.forEach(function (o) {
        var valid = true;
        //Verifie si le mode filtrage rapide est activé
        if(filter_mode){
            Object.keys(o).forEach(function(k){
                if(speedfilters.includes(k) && values_show[k].length!=0){
                    if(o[k]==null){
                        o[k]="NULL";
                    }else if(o[k]==""){
                        o[k]="VIDE";
                    }
                    if(values_show[k].includes(o[k])){
                        valid = valid && true;
                    }else{
                        valid = valid && false;
                    }
                }
            });
        }
        if(!filter_mode || valid) {
            var key = "";
            var keyvalue = "";
            arrayLib.forEach(function (t) {
                if (t.indexOf("SUM(") !== -1 || t.indexOf("COUNT(") !== -1 || t.indexOf("sum(") !== -1 || t.indexOf("count(") !== -1) {
                    keyvalue = t;
                }else{
                    key += o[t]+";";
                }

            });
            if(newdata[key]!=null){
               newdata[key][keyvalue] = Number(o[keyvalue]) + Number(newdata[key][keyvalue]);
            }else{
                newdata[key] = JSON.parse(JSON.stringify(o));
            }
        }


    });
    var data = [];
    var nbShow = 0;
    var calc = configGraph.typecalcul.toLowerCase() + "(" + configGraph.typecalculchamp + ")";

    for (var o in newdata){
        var value = parseInt(newdata[o][calc]);
        if (!slider_mode || (value >= min && value <= max)) {
            nbShow++;
            data.push(newdata[o]);
        }
    }
    if (document.getElementById('number_result')) {
        document.getElementById('number_result').innerText = nbShow;
    }
    if (!mode_sample && nbShow == 0) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        //Affichage des statistiques sur le tableau
        if (document.getElementById('nb_line') != null && document.getElementById('nb_line_show') != null && document.getElementById('nb_column') != null) {
            document.getElementById('nb_line').innerHTML = nb_line;
            document.getElementById('nb_line_show').innerHTML = 0;
            document.getElementById('nb_column').innerHTML = nb_column;
        }
        tbl.innerHTML = '';
        document.getElementById("pagination").innerHTML = "";
        return;
    }
    //Tri les données celon la preference de l'utilisateur
    if(sorting_type=="desc"){
        if (sorting_colomn.indexOf("SUM(") !== -1 || sorting_colomn.indexOf("COUNT(") !== -1 || sorting_colomn.indexOf("sum(") !== -1 || sorting_colomn.indexOf("count(") !== -1) {
            data.sort(function(a, b) {
                return (parseInt(a[sorting_colomn], 10) - parseInt(b[sorting_colomn], 10));
            });
        }else if (sorting_colomn.indexOf("AVG(") !== -1 || sorting_colomn.indexOf("avg(") !== -1) {
            data.sort(function(a, b) {
                return (parseFloat(a[sorting_colomn],10) - parseFloat(b[sorting_colomn],10));
            });
        }else {
            data.sort(function(a, b) {
                if(a[sorting_colomn]=== b[sorting_colomn]){
                    return 0;
                }else if(a[sorting_colomn]===null){
                    return 1;
                }else if(b[sorting_colomn]===null){
                    return -1;
                }else{
                    return ((a[sorting_colomn] < b[sorting_colomn]) ? -1 : 1);
                }

            });
        }
    }else if (sorting_type=="asc"){

        if (sorting_colomn.indexOf("SUM(") !== -1 || sorting_colomn.indexOf("sum(") !== -1 || sorting_colomn.indexOf("COUNT(") !== -1 || sorting_colomn.indexOf("count(") !== -1) {
            data.sort(function(a, b) {
                return (parseInt(b[sorting_colomn], 10) - parseInt(a[sorting_colomn], 10));
            });
        }else if (sorting_colomn.indexOf("AVG(") !== -1 || sorting_colomn.indexOf("avg(") !== -1) {
            data.sort(function(a, b) {
                return (parseFloat(b[sorting_colomn], 10) - parseFloat(a[sorting_colomn], 10));
            });
        } else {
            data.sort(function(a, b) {
                if(a[sorting_colomn]===b[sorting_colomn]){
                    return 0;
                }else if(a[sorting_colomn]===null){
                    return -1;
                }else if(b[sorting_colomn]===null){
                    return 1;
                }else{
                    return ((b[sorting_colomn] < a[sorting_colomn]) ? -1 : 1);
                }

            });
        }
    }
    var nb_line_show = 0;
    data.forEach(function (o) {
        if(nb>=nbmin && nb<nbmax){
            nb_line_show++;
            var tr = document.createElement('tr');
            var nbFocus=-1;
            var config = [];
            //Ajout des données dans le tableau
            arrayLib.forEach(function (t) {
                var td = document.createElement('td');
                if (t.indexOf("AVG(") !== -1 || t.indexOf("avg(") !== -1) {
                    td.appendChild(document.createTextNode(Intl.NumberFormat().format(o[t])));
                } else if (t.indexOf("SUM(") !== -1 || t.indexOf("COUNT(") !== -1 || t.indexOf("sum(") !== -1 || t.indexOf("count(") !== -1) {
                    td.appendChild(document.createTextNode(Intl.NumberFormat().format(o[t])));
                    nbFocus=o[t];
                }else{
                    if(o[t]==null){
                        o[t]="NULL";
                    }else if(o[t]==""){
                        o[t]="VIDE";
                    }
                    config[t]=o[t];

                    td.appendChild(document.createTextNode(o[t]));
                }

                tr.appendChild(td);
            });


            if (!mode_sample && !configGraph.expertmode && speed_filter_flag) {
                var td = document.createElement('td');
                var a = document.createElement("a");
                a.className="btn btn-default btn-xs";
                a.innerHTML = "exemple";
                //console.log(config);

                a.onclick = function () {
                    showLotForTable(config,configGraph,nbFocus,false);
                    return false;
                };
                td.appendChild(a);

                tr.appendChild(td);
            }


            tbdy.appendChild(tr);
        }
        nb++;
    });
    var nbpage_max = Math.ceil(nb/10000);
    tbl.appendChild(tbdy);
    if(sorting_colomn!='' && sorting_type!=''){
        document.getElementById("button_sort_"+sorting_colomn+"_"+sorting_type).className = "btn btn-success btn-xs right-float";
    }

    if(nbpage_max>1){
        //Generation des paginations
        var ul_pagination = document.getElementById("pagination");
        ul_pagination.innerHTML="";
        var li_pagination = document.createElement('li');
        var a_pagination = document.createElement('a');
        a_pagination.innerHTML  = "<span aria-hidden=\"true\">«</span>";
        if(page>0){
            a_pagination.setAttribute("onclick", "tableCreate1D(datalist,'" + sorting_colomn + "','" + sorting_type + "'," + (page - 1) + ",'" + id + "'," + speed_filter_flag + ");return false;");
            a_pagination.href="";
        }
        li_pagination.appendChild(a_pagination);
        ul_pagination.appendChild(li_pagination);

        for (var nb_page=0;nb_page<nbpage_max;nb_page++){
            var li_pagination = document.createElement('li');
            var a_pagination = document.createElement('a');
            a_pagination.innerHTML  = nb_page+1;
            if(nb_page==page){
                li_pagination.className = "active";
            }else{
                a_pagination.setAttribute("onclick", "tableCreate1D(datalist,'" + sorting_colomn + "','" + sorting_type + "'," + nb_page + ",'" + id + "'," + speed_filter_flag + ");return false;");
                a_pagination.href="";
            }
            li_pagination.appendChild(a_pagination);
            ul_pagination.appendChild(li_pagination);
        }


        var li_pagination = document.createElement('li');
        var a_pagination = document.createElement('a');
        a_pagination.innerHTML  = "<span aria-hidden=\"true\">»</span>";
        if(page+1<nbpage_max){
            var new_page = page+1;
            a_pagination.setAttribute("onclick", "tableCreate1D(datalist,'" + sorting_colomn + "','" + sorting_type + "'," + new_page + ",'" + id + "'," + speed_filter_flag + ");return false;");
            a_pagination.href="";
        }
        li_pagination.appendChild(a_pagination);
        ul_pagination.appendChild(li_pagination);
    }else{
        if(document.getElementById("pagination")){
            document.getElementById("pagination").innerHTML="";
        }

    }
    /*for (var i = 0; i < pagination.length; i++) {
        pagination[i].disabled = false;
    }*/
    if (!mode_sample) {
        document.getElementById('chargement-message').innerHTML = "";
        document.getElementById('chargement').className = "";
        document.getElementById('chargement').style.display = 'none';
        if (document.getElementById('loader-img')) {
            document.getElementById('loader-img').style.display = 'none';
        }

        //Affichage des statistiques sur le tableau
        if (document.getElementById('nb_line') != null && document.getElementById('nb_line_show') != null && document.getElementById('nb_column') != null) {
            document.getElementById('nb_line').innerHTML = nb_line;
            document.getElementById('nb_line_show').innerHTML = nb_line_show;
            document.getElementById('nb_column').innerHTML = nb_column;
        }
    }
}

function tableCreate1DReporting(datalist, sorting_colomn, sorting_type, page = -1, id="table", speed_filter_flag=true, mode_sample=false) {
    var slider_mode = false;
    //Prise en compte de l'intervalle
    if (!mode_sample && document.getElementById("slider-range")) {
        slider_mode = true;
        var min = document.getElementById("min_input").value;
        var max = document.getElementById("max_input").value;
        console.log(min + "-" + max);
    }
    //Copie la liste pour garder la version sans triage
    var data = datalist.slice();
    var nb_line = data.length;
    var tbl = document.getElementById(id);
    tbl.innerHTML = '';
    if (!mode_sample) {
        if (data.length == 0) {
            document.getElementById('chargement').className = "alert alert-warning";
            document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
            document.getElementById('loader-img').style.display = 'none';
            document.getElementById('chargement').style.display = 'block';
            //Affichage des statistiques sur le tableau
            if (document.getElementById('nb_line') != null && document.getElementById('nb_line_show') != null && document.getElementById('nb_column') != null) {
                document.getElementById('nb_line').innerHTML = 0;
                document.getElementById('nb_line_show').innerHTML = 0;
                document.getElementById('nb_column').innerHTML = 0;
            }
            return;
        } else {
            document.getElementById('chargement-message').innerHTML = "Génération du tableau";
            document.getElementById('chargement').className = "alert alert-info";
            document.getElementById('chargement').style.display = 'block';
        }
    } else {
        if (data.length == 0) {
            document.getElementById(id).innerHTML = "<div class='alert alert-warning'>Aucune donnée disponible!</div>";
        }
    }

    var nb_column = Object.keys(data[0]).length;
    var colomns_hide = [];
    var speedfilters = [];
    values_show = [];
    var filter_mode = true;
    if (!speed_filter_flag || configGraph === undefined || (configGraph.expertmode && configGraph.speedfilters !== undefined && configGraph.speedfilters.length == 0) || configGraph.speedfilters === undefined) {

    }else{
        jq=jQuery.noConflict();
        speedfilters = configGraph.speedfilters;
        configGraph.speedfilters.forEach(function(f){
            if (!configGraph.expertmode && !configGraph.champs.includes(f)) {
                console.log("ajout liste hide " + f);
                console.log(configGraph.expertmode);
                colomns_hide.push(f);
            }
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
    var thead = document.createElement('thead');
    var tbdy = document.createElement('tbody');
    tbdy.className = "table-overflow";
    //La classe des celules total du tableau
    var classTotal = "info"
    var tr = document.createElement('tr');

    var arrayLib = [];
    //Génération de l'entête du tableau
    Object.keys(data[0]).forEach(function(l){
        if(!colomns_hide.includes(l)){
            arrayLib.push(l);
            var th2 = document.createElement('th');
            if(l == "dateVerification::DATE" || l == "dateVerification::YEARMONTH"){
                l = "Date de verification";
                th2.appendChild(document.createTextNode(l));
            }else if(l == "statut"){
                l = "Statut Balance";
                th2.appendChild(document.createTextNode(l));
            }else if(l == "tranche"){
                l = "Tranche Balance";
                th2.appendChild(document.createTextNode(l));
            }else if(l == 'codeRegate'){
                l = "Code Régate Entité";
                th2.appendChild(document.createTextNode(l));
            }else if(l == 'codePostal'){
                l = "Code Postal Entité";
                th2.appendChild(document.createTextNode(l));
            }else if(l == 'codeSource'){
                l = "Code Source Entité";
                th2.appendChild(document.createTextNode(l));
            }else if(l == 'numeroSerie'){
                l = "Numéro de Série";
                th2.appendChild(document.createTextNode(l));
            }else if(l == 'codeArticle'){
                l = "Code Article";
                th2.appendChild(document.createTextNode(l));
            }else if(l == 'numeroLot'){
                l = "Numéro de Lot";
                th2.appendChild(document.createTextNode(l));
            }else if(l == 'codeActif'){
                l = "Code Actif";
                th2.appendChild(document.createTextNode(l));
            }else{
                th2.appendChild(document.createTextNode(l));
            }
                
            //Ajout du bouton de tri par ordre croissant/alphabetique
            var button_image_desc = document.createElement('span');
            button_image_desc.className = "glyphicon glyphicon-chevron-up";
            var button_desc = document.createElement('button');
            button_desc.id = "button_sort_"+l+"_desc";
            button_desc.className = "btn btn-default btn-xs right-float";
            button_desc.appendChild(button_image_desc);
            button_desc.onclick = function () {
                if(sorting_colomn==l && sorting_type=="desc"){

                    tableCreate1D(datalist,'','',page,id,speed_filter_flag, true);
                    //document.getElementById("button_sort_"+l+"_desc").className = "btn btn-default btn-xs right-float";
                }else{
                    tableCreate1D(datalist,l,'desc',page,id,speed_filter_flag, true);
                    //document.getElementById("button_sort_"+l+"_desc").className = "btn btn-success btn-xs right-float";
                }
            };
            th2.appendChild(button_desc);
            var button_image_asc = document.createElement('span');
            button_image_asc.className = "glyphicon glyphicon-chevron-down";

            //Ajout du bouton de tri par ordre decroissant/dealphabetique
            var button_asc = document.createElement('button');
            button_asc.id = "button_sort_"+l+"_asc";
            button_asc.className = "btn btn-default btn-xs right-float";
            button_asc.appendChild(button_image_asc);
            button_asc.onclick = function () {
                if(sorting_colomn==l && sorting_type=="asc"){
                    tableCreate1D(datalist,'','',page,id,speed_filter_flag, true);
                }else{
                    tableCreate1D(datalist,l,'asc',page,id,speed_filter_flag, true);
                }
            };
            th2.appendChild(button_asc);
            tr.appendChild(th2);
        }

    });

    if (!mode_sample && !configGraph.expertmode && speed_filter_flag) {
        //Ajout d'une colonne info
        var th2 = document.createElement('th');
        th2.appendChild(document.createTextNode("info"));
        tr.appendChild(th2);
    }

    thead.appendChild(tr);
    tbl.appendChild(thead);

    if(page!=-1){
        var nbmin = page*10000;
        var nbmax = nbmin+10000;
    }else{
        var nbmin = 0;
        var nbmax = Number.MAX_SAFE_INTEGER;
    }
    var nb = 0;
    var newdata = [];
    //console.log(newdata);
    //Filtrage et rassemblement des valeurs pour prendre en compte les filtres rapide
    data.forEach(function (o) {
        var valid = true;
        //Verifie si le mode filtrage rapide est activé
        if(filter_mode){
            Object.keys(o).forEach(function(k){
                if(speedfilters.includes(k) && values_show[k].length!=0){
                    if(o[k]==null){
                        o[k]="NULL";
                    }else if(o[k]==""){
                        o[k]="VIDE";
                    }
                    if(values_show[k].includes(o[k])){
                        valid = valid && true;
                    }else{
                        valid = valid && false;
                    }
                }
            });
        }
        if(!filter_mode || valid) {
            var key = "";
            var keyvalue = "";
            arrayLib.forEach(function (t) {
                if (t.indexOf("SUM(") !== -1 || t.indexOf("COUNT(") !== -1 || t.indexOf("sum(") !== -1 || t.indexOf("count(") !== -1) {
                    keyvalue = t;
                }else{
                    key += o[t]+";";
                }

            });
            if(newdata[key]!=null){
               newdata[key][keyvalue] = Number(o[keyvalue]) + Number(newdata[key][keyvalue]);
            }else{
                newdata[key] = JSON.parse(JSON.stringify(o));
            }
        }


    });
    var data = [];
    var nbShow = 0;
    var calc = configGraph.typecalcul.toLowerCase() + "(" + configGraph.typecalculchamp + ")";

    for (var o in newdata){
        var value = parseInt(newdata[o][calc]);
        if (!slider_mode || (value >= min && value <= max)) {
            nbShow++;
            data.push(newdata[o]);
        }
    }
    if (document.getElementById('number_result')) {
        document.getElementById('number_result').innerText = nbShow;
    }
    if (!mode_sample && nbShow == 0) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        //Affichage des statistiques sur le tableau
        if (document.getElementById('nb_line') != null && document.getElementById('nb_line_show') != null && document.getElementById('nb_column') != null) {
            document.getElementById('nb_line').innerHTML = nb_line;
            document.getElementById('nb_line_show').innerHTML = 0;
            document.getElementById('nb_column').innerHTML = nb_column;
        }
        tbl.innerHTML = '';
        document.getElementById("pagination").innerHTML = "";
        return;
    }
    //Tri les données celon la preference de l'utilisateur
    if(sorting_type=="desc"){
        if (sorting_colomn.indexOf("SUM(") !== -1 || sorting_colomn.indexOf("COUNT(") !== -1 || sorting_colomn.indexOf("sum(") !== -1 || sorting_colomn.indexOf("count(") !== -1) {
            data.sort(function(a, b) {
                return (parseInt(a[sorting_colomn], 10) - parseInt(b[sorting_colomn], 10));
            });
        }else if (sorting_colomn.indexOf("AVG(") !== -1 || sorting_colomn.indexOf("avg(") !== -1) {
            data.sort(function(a, b) {
                return (parseFloat(a[sorting_colomn],10) - parseFloat(b[sorting_colomn],10));
            });
        }else {
            data.sort(function(a, b) {
                if(a[sorting_colomn]=== b[sorting_colomn]){
                    return 0;
                }else if(a[sorting_colomn]===null){
                    return 1;
                }else if(b[sorting_colomn]===null){
                    return -1;
                }else{
                    return ((a[sorting_colomn] < b[sorting_colomn]) ? -1 : 1);
                }

            });
        }
    }else if (sorting_type=="asc"){

        if (sorting_colomn.indexOf("SUM(") !== -1 || sorting_colomn.indexOf("sum(") !== -1 || sorting_colomn.indexOf("COUNT(") !== -1 || sorting_colomn.indexOf("count(") !== -1) {
            data.sort(function(a, b) {
                return (parseInt(b[sorting_colomn], 10) - parseInt(a[sorting_colomn], 10));
            });
        }else if (sorting_colomn.indexOf("AVG(") !== -1 || sorting_colomn.indexOf("avg(") !== -1) {
            data.sort(function(a, b) {
                return (parseFloat(b[sorting_colomn], 10) - parseFloat(a[sorting_colomn], 10));
            });
        } else {
            data.sort(function(a, b) {
                if(a[sorting_colomn]===b[sorting_colomn]){
                    return 0;
                }else if(a[sorting_colomn]===null){
                    return -1;
                }else if(b[sorting_colomn]===null){
                    return 1;
                }else{
                    return ((b[sorting_colomn] < a[sorting_colomn]) ? -1 : 1);
                }

            });
        }
    }
    var nb_line_show = 0;
    data.forEach(function (o) {
        if(nb>=nbmin && nb<nbmax){
            nb_line_show++;
            var tr = document.createElement('tr');
            var nbFocus=-1;
            var config = [];
            //Ajout des données dans le tableau
            arrayLib.forEach(function (t) {
                var td = document.createElement('td');
                if (t.indexOf("AVG(") !== -1 || t.indexOf("avg(") !== -1) {
                    td.appendChild(document.createTextNode(Intl.NumberFormat().format(o[t])));
                } else if (t.indexOf("SUM(") !== -1 || t.indexOf("COUNT(") !== -1 || t.indexOf("sum(") !== -1 || t.indexOf("count(") !== -1) {
                    td.appendChild(document.createTextNode(Intl.NumberFormat().format(o[t])));
                    nbFocus=o[t];
                }else{
                    if(o[t]==null){
                        o[t]="NULL";
                    }else if(o[t]==""){
                        o[t]="VIDE";
                    }
                    config[t]=o[t];

                    td.appendChild(document.createTextNode(o[t]));
                }

                tr.appendChild(td);
            });


            if (!mode_sample && !configGraph.expertmode && speed_filter_flag) {
                var td = document.createElement('td');
                var a = document.createElement("a");
                a.className="btn btn-default btn-xs";
                a.innerHTML = "exemple";
                //console.log(config);

                a.onclick = function () {
                    showLotForTable(config,configGraph,nbFocus,false);
                    return false;
                };
                td.appendChild(a);

                tr.appendChild(td);
            }


            tbdy.appendChild(tr);
        }
        nb++;
    });
    var nbpage_max = Math.ceil(nb/10000);
    tbl.appendChild(tbdy);
    if(sorting_colomn!='' && sorting_type!=''){
        document.getElementById("button_sort_"+sorting_colomn+"_"+sorting_type).className = "btn btn-success btn-xs right-float";
    }

    if(nbpage_max>1){
        //Generation des paginations
        var ul_pagination = document.getElementById("pagination");
        ul_pagination.innerHTML="";
        var li_pagination = document.createElement('li');
        var a_pagination = document.createElement('a');
        a_pagination.innerHTML  = "<span aria-hidden=\"true\">«</span>";
        if(page>0){
            a_pagination.setAttribute("onclick", "tableCreate1D(datalist,'" + sorting_colomn + "','" + sorting_type + "'," + (page - 1) + ",'" + id + "'," + speed_filter_flag + ");return false;");
            a_pagination.href="";
        }
        li_pagination.appendChild(a_pagination);
        ul_pagination.appendChild(li_pagination);

        for (var nb_page=0;nb_page<nbpage_max;nb_page++){
            var li_pagination = document.createElement('li');
            var a_pagination = document.createElement('a');
            a_pagination.innerHTML  = nb_page+1;
            if(nb_page==page){
                li_pagination.className = "active";
            }else{
                a_pagination.setAttribute("onclick", "tableCreate1D(datalist,'" + sorting_colomn + "','" + sorting_type + "'," + nb_page + ",'" + id + "'," + speed_filter_flag + ");return false;");
                a_pagination.href="";
            }
            li_pagination.appendChild(a_pagination);
            ul_pagination.appendChild(li_pagination);
        }


        var li_pagination = document.createElement('li');
        var a_pagination = document.createElement('a');
        a_pagination.innerHTML  = "<span aria-hidden=\"true\">»</span>";
        if(page+1<nbpage_max){
            var new_page = page+1;
            a_pagination.setAttribute("onclick", "tableCreate1D(datalist,'" + sorting_colomn + "','" + sorting_type + "'," + new_page + ",'" + id + "'," + speed_filter_flag + ");return false;");
            a_pagination.href="";
        }
        li_pagination.appendChild(a_pagination);
        ul_pagination.appendChild(li_pagination);
    }else{
        if(document.getElementById("pagination")){
            document.getElementById("pagination").innerHTML="";
        }

    }
    /*for (var i = 0; i < pagination.length; i++) {
        pagination[i].disabled = false;
    }*/
    if (!mode_sample) {
        document.getElementById('chargement-message').innerHTML = "";
        document.getElementById('chargement').className = "";
        document.getElementById('chargement').style.display = 'none';
        if (document.getElementById('loader-img')) {
            document.getElementById('loader-img').style.display = 'none';
        }

        //Affichage des statistiques sur le tableau
        if (document.getElementById('nb_line') != null && document.getElementById('nb_line_show') != null && document.getElementById('nb_column') != null) {
            document.getElementById('nb_line').innerHTML = nb_line;
            document.getElementById('nb_line_show').innerHTML = nb_line_show;
            document.getElementById('nb_column').innerHTML = nb_column;
        }
    }
}


/**
 * Créé un canvas à partir d'une table HTML
 *
 */
function getTableImg(){
    html2canvas([document.getElementById('table')], {
        onrendered: function (canvas) {
            canvas.setAttribute("id","canvas");
            document.getElementById('canvasDiv').appendChild(canvas);
        }
    });
}

/**
 * Affiche une fenetre modale avec un tableau contenant un extrait de la table
 * @param database
 * @param table
 */
function showLotForTable(configs,configGraph,nbFocus){
    var database="hadoopviewer_data";
    var jq=jQuery.noConflict();
    urlDatabase=base_url+'index.php/api/getSampleDataWhere?';
    document.getElementById('myModalLabel').innerHTML = "Extrait de la source : " + configGraph.source_name + "<br> Table : " + configGraph.database + "." + configGraph.table;
    document.getElementById('myModalLabel').innerHTML += "<br> Filtres du graphique : <br>"+ JSON.stringify(configGraph.filtres) + " "+JSON.stringify(configGraph.operators);
    //document.getElementById('myModalLabel').innerHTML += "<br> Choix : <br>{"+configGraph.wording+"="+libelleFocus+", "+configGraph.group+"="+groupementFocus+"}";
    urlDatabase+=jQuery.param(configGraph);

    document.getElementById('myModalLabel').innerHTML += "<br> Choix : <br>{";
    for(var config in configs){
        //console.log(config+" "+configs[config]);
        document.getElementById('myModalLabel').innerHTML +=config+"="+configs[config]+",";
        urlDatabase += "&filtres2[" + encodeURI(config) + "]=" + encodeURI(configs[config]);
    }
    if(configGraph.speedfilters){
        //gestion des filtres rapides
        configGraph.speedfilters.forEach(function(f){
            values = [];
            var ids = f.split("::");
            var id = ids[0];
            if(ids.length>1){
                id +=ids[1];
            }
            jq("#"+id+"_select :selected").each(function(){
                urlDatabase += "&filtres2[" + encodeURI(f) + "]=" + encodeURI(jq(this).val());
                document.getElementById('myModalLabel').innerHTML +=f+"="+jq(this).val()+",";
            });
        });

    }
    document.getElementById('myModalLabel').innerHTML +="}";
    //urlDatabase+="&filtres2["+configGraph.wording+"]="+libelleFocus;
    urlDatabase+="&nbocc="+nbFocus;
    jq('#myModal').modal('show');
    document.getElementById('modalTableSample').innerHTML = "<div class=\"filterBox\"><div id=\"floatingCirclesG\">\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_01\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_02\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_03\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_04\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_05\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_06\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_07\"></div>\n" +
        "\t<div class=\"f_circleG\" id=\"frotateG_08\"></div>\n" +
        "</div></div>";
    jq.get(urlDatabase, function(data) {
        //tableCreateSampleModal(data,'modalTableSample');
        tableCreate1D(JSON.parse(data), "", "", -1, 'modalTableSample', false, true);

    });
}

/**
 * Creation des champs pour les filtres rapides
 * @param data données du tableau
 * @param config configuration du tableau
 */
function createSpeedFilters(data, config){
    config.speedfilters.forEach(function (o) {
        var select = document.createElement('select');
        var ids = o.split("::");
        var title = ids[0];
        var id = ids[0];
        if(ids.length>1){
            title +=" ("+ids[1]+")";
            id +=ids[1];
        }
        select.id = id+"_select";
        select.class = "chosen-select";
        select.placeholder = "Ajouter une valeur";
        select.multiple=true;
        select.onchange=function(){
            tableCreate1D(datalist,"","",0);
        };
        var options = new Set();
         data.forEach(function (line) {
             options.add(line[o]);
         });
         options.forEach(function (op) {
             var option = document.createElement('option');
             if(op==null){
                 option.innerHTML = "NULL";
             }else if(op==""){
                 option.innerHTML = "VIDE";
             }else{
                 option.innerHTML = op;
             }

             select.appendChild(option);
         });
        document.getElementById("speed-filters").appendChild(document.createTextNode(title));
        document.getElementById("speed-filters").appendChild(document.createElement("br"));
        document.getElementById("speed-filters").appendChild(select);
        document.getElementById("speed-filters").appendChild(document.createElement("br"));
        jq("#"+select.id).chosen({max_shown_results:50, no_results_text: "Aucun résultat pour", width: "100%"});
    });


}

/**
 * Creation des champs pour les filtres rapides
 * @param data données du tableau
 * @param config configuration du tableau
 */
function createSpeedFilterExpertMode(data, column_name) {
    o = column_name;
    var select = document.createElement('select');
    var ids = o.split("::");
    var title = ids[0];
    var id = ids[0];
    if (ids.length > 1) {
        title += " (" + ids[1] + ")";
        id += ids[1];
    }
    select.id = id + "_select";
    select.className = "chosen-select";
    select.placeholder = "Ajouter une valeur";
    select.multiple = true;
    select.onchange = function () {
        tableCreate1D(datalist, "", "", 0);
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
    var button = document.createElement('button');
    button.className = "btn btn-default";
    button.type = "button";
    button.onclick = function () {
        document.getElementById(title + "_speedfilter").remove();
        configGraph.speedfilters.pop(document.getElementById("speedfiltersadd").value);
        tableCreate1D(datalist, "", "", 0);
    };
    button.innerHTML = "<span id=\"addFiltersButton\" class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>";
    var div = document.createElement('div');
    div.id = title + "_speedfilter";
    div.appendChild(button);
    div.appendChild(document.createTextNode(title));
    div.appendChild(document.createElement("br"));
    div.appendChild(select);

    document.getElementById("speed-filters").appendChild(div);
    jq = jQuery.noConflict();
    jq("#" + select.id).chosen({max_shown_results: 50, no_results_text: "Aucun résultat pour", width: "100%"});


}

/**
 * Creation des champs pour les filtres rapides
 * @param data données du tableau
 * @param config configuration du tableau
 */
function createSpeedFiltersFormAdd(data) {
    console.log("createSpeedFiltersFormAdd");
    var title = "Ajout d'un filtre rapide (mode expert)";
    var div_inline = document.createElement('div');
    div_inline.className = "form-inline";
    var select = document.createElement('select');
    select.id = "speedfiltersadd";
    select.className = "form-control selectFilter";

    for (var k in data[0]) {
        var option = document.createElement('option');
        option.innerHTML = k;
        select.appendChild(option);
    }
    div_inline.append(select);
    var button = document.createElement('button');
    button.className = "btn btn-default";
    button.type = "button";
    button.onclick = function () {

        configGraph.speedfilters.push(document.getElementById("speedfiltersadd").value);
        createSpeedFilterExpertMode(data, document.getElementById("speedfiltersadd").value);
    };
    button.innerHTML = "<span id=\"addFiltersButton\" class=\"glyphicon glyphicon-plus\" aria-hidden=\"true\"></span>";
    div_inline.append(button);
    document.getElementById("speed-filters").appendChild(document.createTextNode(title));
    document.getElementById("speed-filters").appendChild(document.createElement("br"));
    document.getElementById("speed-filters").appendChild(div_inline);
    document.getElementById("speed-filters").appendChild(document.createElement("br"));
}

/**
 * Creation des champs pour les filtres rapides pour les graphique de table2D
 * @param data données du tableau
 * @param config configuration du tableau
 */
function createSpeedFiltersTable2D(data, config){
    config.speedfilters.forEach(function (o) {
        var select = document.createElement('select');
        var ids = o.split("::");
        var title = ids[0];
        var id = ids[0];
        if(ids.length>1){
            title +=" ("+ids[1]+")";
            id +=ids[1];
        }
        select.id = id+"_select";
        select.class = "chosen-select";
        select.placeholder = "Ajouter une valeur";
        select.multiple=true;
        select.onchange=function(){
            tableCreate(data, config, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
        };
        var options = new Set();
        data.forEach(function (line) {
            options.add(line[o]);
        });
        options.forEach(function (op) {
            var option = document.createElement('option');
            if(op==null){
                option.innerHTML = "NULL";
            }else if(op==""){
                option.innerHTML = "VIDE";
            }else{
                option.innerHTML = op;
            }

            select.appendChild(option);
        });
        document.getElementById("speed-filters").appendChild(document.createTextNode(title));
        document.getElementById("speed-filters").appendChild(document.createElement("br"));
        document.getElementById("speed-filters").appendChild(select);
        document.getElementById("speed-filters").appendChild(document.createElement("br"));
        jq("#"+select.id).chosen({max_shown_results:50, no_results_text: "Aucun résultat pour", width: "100%"});
    });
}

/**
 * Génération d'un formulaire d'intervalle pour modifier une treemap
 * @param data Donnée pour le graphique
 */
function createSpeedFilterRangeTable1D(data) {
    console.log("DEBUT createSpeedFilterRangeTable1D : " + Date());
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
        var id = "";
        libs.forEach(function (lib) {
            if (value[lib] == null) {
                id += "NULL"
            } else {
                id += value[lib];
            }
        });
        if (values[id] != null) {
            values[id] = values[id] + Number(value[calc]);
        } else {
            values[id] = Number(value[calc]);
        }
    });
    for (var key in values) {
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
    label.innerText = data.length;
    speed_filters_div.appendChild(label);

    var label = document.createElement("span");
    label.innerText = " informations à afficher.";
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
    span.className = "input-group-btn";


    var button = document.createElement("button");
    button.type = "button";
    button.className = "btn btn-default";
    button.innerText = "X";
    button.setAttribute("onclick", "updateSlider('min_input', " + 1 + ")");
    span.appendChild(button);

    input_group.appendChild(span);

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
                jq("#min_input").val(ui.values[0]);
                jq("#max_input").val(ui.values[1]);
                tableCreate1D(datalist, "", "", 0);
            }
        });
        jq("#min_input").val(1);
        jq("#max_input").val(maxValue);
        jq("#min_input").on("keyup", function () {
            console.log(this.value);
            jq("#slider-range").slider("values", 0, this.value);
            tableCreate1D(datalist, "", "", 0);
        });
        jq("#max_input").on("keyup", function () {
            console.log(this.value);
            jq("#slider-range").slider("values", 1, this.value);
            tableCreate1D(datalist, "", "", 0);
        });
    });
    console.log("FIN createSpeedFilterRangeTable1D : " + Date());
}

/**
 * Génération d'un formulaire d'intervalle pour modifier une table2D
 * @param data Donnée pour le graphique
 */
function createSpeedFilterRangeTable2D(data, configGraph) {
    console.log("DEBUT createSpeedFilterRangeTable2D : " + Date());
    if (!document.getElementById("speed-filters")) {
        return;
    }
    //Calcule du maximum
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
    label.innerText = " informations à afficher (<=10000 résultats pour afficher un graphique).";
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
                tableCreate(data, configGraph, ui.values[0], ui.values[1]);
            }
        });
        jq("#min_input").val(1);
        jq("#max_input").val(maxValue);
        jq("#min_input").on("keyup", function () {
            //Activation quand on modifie le champs min
            jq("#slider-range").slider("values", 0, this.value);
            tableCreate(data, configGraph, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
        });
        jq("#max_input").on("keyup", function () {
            //Activation quand on modifie le champs max
            jq("#slider-range").slider("values", 1, this.value);
            tableCreate(data, configGraph, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
        });
    });
    console.log("FIN createSpeedFilterRangeTreemap : " + Date());
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