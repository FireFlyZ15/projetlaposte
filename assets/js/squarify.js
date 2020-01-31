/**
 * Created by pslj484 on 29/03/2017.
 * Fonctions de génération de treemap inspiré du script python
 * https://github.com/laserson/squarify
 * sous licence Apache 2.0
 */
/**
 *  Creation du TreeMap
 */
var dataTreeMap=[];
var dataGraph = [];
/**
 * Change la couleur sur le graphique
 * @param type : le nom du type à qui on doit changer la couleur
 */
function changeColor(type){
    var name = type.replace(/\s/g, "_");
    var newValue = document.getElementById(name + "_color").value;
    configGraph.color[name + "_color"] = newValue;
    for(var x in dataGraph){
        if(dataGraph[x].typesGraph==type){
            console.log(dataGraph[x].typesGraph + " = " + type);
            dataGraph[x].color = newValue;
        }
    }
    draw(dataGraph);
}
/**
 * Configuration des couleurs de la treemap puis dessine la treemap
 * @param data Les données du graphe
 * @param configJson La configuration du graphe
 */
function appliColor(data,configJSON, viewMode = false){

    if(data.length==0){
        document.getElementById('chargement').innerHTML = 'Aucune donnée disponible pour votre recherche';
        return;
    }
    var color = [];
    var config = JSON.parse(configJSON);
    console.log(config);
    for(var x in data){
        if(data[x].typesGraph==null){
            data[x].typesGraph="NULL";
        }
        if((config.wording=="mois" || config.wording=="month") && /^\+?\d+$/.test(data[x].libellesGraph)){
            data[x].libellesGraph = monthTranslate(data[x].libellesGraph);
        }else if((config.group=="mois" || config.group=="month") && /^\+?\d+$/.test(data[x].typesGraph)){
            data[x].typesGraph = monthTranslate(data[x].typesGraph);
        }

        if(color[data[x].typesGraph]==null){
            var key = data[x].typesGraph.replace(/\s/g,"_")+"_color";
            if(config.color != undefined && config.color[key] != undefined){
                color[data[x].typesGraph] = config.color[key];
            }else{
                color[data[x].typesGraph] = getRandomColor();
            }
        }

        data[x].color = color[data[x].typesGraph];
    }
    dataGraph = data;
    var success = draw(data);
    if (success && !viewMode) {
        getColorGraph();
    }



}
var color_flag = false;


function getColorGraph(){
    if(color_flag){
        return;
    }
    color_flag=true;
    document.getElementById('colormanager').innerHTML = "";
    types = [];
    if (configGraph.color == null) {
        configGraph.color = {};
    }
    for(x in dataGraph){
        if(types[dataGraph[x].typesGraph]==null){
            types[dataGraph[x].typesGraph]=true;
            var name = dataGraph[x].typesGraph.replace(/\s/g, "_");
            document.getElementById('colormanager').innerHTML += '<input type="color" class="btn btn-outline-primary" value="' + dataGraph[x].color + '" id="' + name + '_color" name="' + name + '_color" onchange="changeColor(\'' + dataGraph[x].typesGraph + '\', configJSON)"><label for="' + name + '_color">' + dataGraph[x].typesGraph + '</label><br>';

            configGraph.color[name + '_color'] = dataGraph[x].color;
        }
    }
    color_flag=false;
}
/**
 * Creation du treeMap
 * @param dataRaw Les données du graphe
 */
function draw(dataRaw, min=-1, max=-1) {
    console.log("DEBUT draw : " + Date());
    //Reset de l'affichage du graphique
    var canvasDiv = document.getElementById("canvas-div");
    canvasDiv.innerHTML = "<canvas id=\"canvas\" width=\"100%\" height=\"800px\" onmousemove=\"canvasMouseEvent(event)\" onmouseout=\"endCanvasMouseEvent(this)\" onclick=\"canvasClickEvent(event)\"></canvas>";
    var canvas = document.getElementById("canvas");
    var ctx = canvas.getContext("2d");
    if(dataRaw.length==0){
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        console.log("FIN 1 draw : " + Date());
        return false;
    }else {
        document.getElementById('chargement-message').innerHTML = "Génération du graphique";
        document.getElementById('chargement').className="alert alert-info";
        document.getElementById('chargement').style.display = 'block';
    }
    var lv2 = [];
    var sizesCat = [];
    var sumType = [];
    var sumTotal = 0;
    var heigth = 800;
    var width = 1250;

    var sizeTypeString = 0;
    var filter_mode = true;
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
    var newdata = [];
    for(var x in dataRaw){
        var valid = true;
        Object.keys(values_show).forEach(function(k){
            var value = dataRaw[x][k];
            if(values_show[k].length==0){
            }else if((value==null && values_show[k].includes("NULL")) || values_show[k].includes(value)){
                valid = valid && true;
            }else if((value=="" && values_show[k].includes("VIDE")) || values_show[k].includes(value)){
                valid = valid && true;
            }else{
                valid = valid && false;
            }
        },x);

        if(!filter_mode || valid){
            var key = dataRaw[x].libellesGraph+";"+dataRaw[x].typesGraph;
            if(newdata[key]!=null){
                newdata[key].nb = Number(dataRaw[x].nb) + Number(newdata[key].nb);
            }else{
                var o = {};
                o.libellesGraph = dataRaw[x].libellesGraph;
                o.typesGraph = dataRaw[x].typesGraph;
                o.nb = dataRaw[x].nb;
                o.color = dataRaw[x].color;
                newdata[key] = o;
            }
        }

    }

    dataRaw = [];
    for (var o in newdata){
        dataRaw.push(newdata[o]);
    }
    if (dataRaw.length == 0) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        console.log("FIN 3 draw : " + Date());
        return false;

    }
    var dataShow = false;
    var nbShow = 0;
    for(var x in dataRaw){
        if (min == -1 || max == -1 || (dataRaw[x].nb >= min && dataRaw[x].nb <= max)) {
            dataShow = true;
            nbShow++;
            if (lv2[dataRaw[x].typesGraph] == null)
                lv2[dataRaw[x].typesGraph] = [];
            var libellesGraph = dataRaw[x].libellesGraph;
            var typesGraph = dataRaw[x].typesGraph;
            /*if(config.wording=="mois" || config.wording=="month") {
                libellesGraph = monthTranslate(dataRaw[x].libellesGraph);
            }else if(config.group=="mois" || config.group=="month"){
                typesGraph = monthTranslate(dataRaw[x].typesGraph);
            }*/
            lv2[dataRaw[x].typesGraph].push({
                libelle: libellesGraph,
                type: typesGraph,
                color: dataRaw[x].color,
                values: parseInt(dataRaw[x].nb),
                sizes: parseInt(dataRaw[x].nb)
            });
            var sum = 0;
            if (sizesCat[dataRaw[x].typesGraph] == null) {
                sum = parseInt(dataRaw[x].nb);
            } else {
                sum = parseInt(sizesCat[dataRaw[x].typesGraph].values) + parseInt(dataRaw[x].nb);
            }
            if (sumType[dataRaw[x].typesGraph] == null)
                sumType[dataRaw[x].typesGraph] = 0;
            sizesCat[dataRaw[x].typesGraph] = {
                libelle: dataRaw[x].libellesGraph,
                type: dataRaw[x].typesGraph,
                color: dataRaw[x].color,
                values: sum,
                sizes: sum
            };
            //console.log(dataRaw[x].typesGraph+" "+dataRaw[x].libellesGraph+" "+dataRaw[x].nb);
            sumType[dataRaw[x].typesGraph] += parseInt(dataRaw[x].nb);
            sumTotal += parseInt(dataRaw[x].nb);
            if (sizeTypeString < dataRaw[x].typesGraph.length * 7) {
                sizeTypeString = dataRaw[x].typesGraph.length * 7
            }
        }

    }
    if (document.getElementById('number_result')) {
        document.getElementById('number_result').innerText = nbShow;
    }

    if (!dataShow) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Aucune donnée disponible pour votre recherche';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        console.log("FIN 5 draw : " + Date());
        return false;
    } else if (nbShow >= 10000) {
        document.getElementById('chargement').className = "alert alert-warning";
        document.getElementById('chargement-message').innerHTML = 'Il y a trop d’occurrences pour afficher le résultat (' + nbShow + ') ! Le treemap est capable d’afficher que 10 000 résultats.';
        document.getElementById('loader-img').style.display = 'none';
        document.getElementById('chargement').style.display = 'block';
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        console.log("FIN 6 draw : " + Date());
        return false;
    }
    //Calcule de la taille pour la legende
    var depassement = ctx.canvas.width-width-50-sizeTypeString;
    if(depassement<0){
        //console.log("Depassement X : "+x.type);
        //console.log("Depassement = "+depassement);

        ctx.canvas.width = ctx.canvas.width - depassement;
    }
    //Netoyage du canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    var i = 0;
    var sizesCat2 = [];
    for(var x in  sizesCat){
        //sizesCat[x].color = getRandomColor();
        sizesCat2[i] = sizesCat[x];
        i++;
    }
    var i2 = 0;
    for(var x in  lv2){
        lv2[x].forEach(function(value){
            value.percentLibInType = value.values/sumType[value.type]*100;
            value.percentLibInTotal = value.values/sumTotal*100;
            value.percentType = sizesCat[x].values/sumTotal*100;
            //value.color = sizesCat[x].color;
        });
    }
    var cat1 = normalize_sizes(sizesCat2, width, heigth);
    cat1 = squarify(cat1, 0,0, width, heigth);
    var jsRect = [];
    var nb = 0;
    //Génération de la treemap dans le canvas
    cat1.forEach(function(x){
        ctx.fillStyle = x.color;
        ctx.fillRect(x.sizes.x, x.sizes.y, x.sizes.dx, x.sizes.dy);
        ctx.strokeStyle = "white";
        //Contour du rectangle
        ctx.strokeRect(x.sizes.x, x.sizes.y, x.sizes.dx, x.sizes.dy);

        ctx.fillRect(width+5, (nb*15)+5, 40, 10);
        ctx.fillStyle = "black";
        ctx.textAlign="left";
        ctx.textBaseline = "top";
        ctx.fillText(x.type, width+50, (nb*15)+5);

        if((nb*15)+5>heigth){
            //console.log("Depassement Y : "+x.type);
        }

        //Creation des enfants
        var width2 = x.sizes.dx;
        var heigth2 = x.sizes.dy;
        test2 = normalize_sizes(lv2[x.type], width2, heigth2);
        test2 = squarify(test2, 0,0, width2, heigth2);
        var canvas = document.getElementById("canvas");
        test2.forEach(function(z){

            var x_margin = x.sizes.x;
            var y_margin = x.sizes.y;
            ctx.fillStyle = z.color;
            z.sizes.x=z.sizes.x+x_margin;
            z.sizes.y=z.sizes.y+y_margin;
            ctx.fillRect(z.sizes.x, z.sizes.y, z.sizes.dx, z.sizes.dy);
            ctx.strokeStyle = "white";
            //Contour du rectangle
            ctx.strokeRect(z.sizes.x, z.sizes.y, z.sizes.dx, z.sizes.dy);
            if(z.libelle==null){
                z.libelle="NULL";
            }
            if(z.sizes.dx>z.libelle.length*10 && z.sizes.dy >= 10){
                //ctx.fillStyle = textColor(color);
                ctx.fillStyle = "white";
                ctx.strokeStyle = "#1a1a1a";
                ctx.textAlign="center";
                ctx.textBaseline = "middle";
                ctx.strokeText(z.libelle, (z.sizes.x*2+z.sizes.dx)/2, (z.sizes.y*2+z.sizes.dy)/2);
                ctx.fillText(z.libelle, (z.sizes.x*2+z.sizes.dx)/2, (z.sizes.y*2+z.sizes.dy)/2);
                if(z.sizes.dx>z.type.length*10 && z.sizes.dy >= 30 && z.type!="couleur"){
                    ctx.strokeText(z.type, (z.sizes.x*2+z.sizes.dx)/2, (z.sizes.y*2+z.sizes.dy)/2+10);
                    ctx.fillText(z.type, (z.sizes.x*2+z.sizes.dx)/2, (z.sizes.y*2+z.sizes.dy)/2+10);
                }

            }
            jsRect.push(z);
        });
        nb++;
    });
    dataTreeMap = jsRect;
    document.getElementById('chargement-message').innerHTML = "";
    document.getElementById('chargement').className="";
    document.getElementById('chargement').style.display = 'none';
    document.getElementById('loader-img').style.display = 'none';
    console.log("FIN draw : " + Date());
    return true;

}

/**
 * Donne une couleur aléatoire en hexadecimal
 */
function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
/**
 * Inversion de la couleur donnée
 */
function invertColor(hex) {
    if (hex.indexOf('#') === 0) {
        hex = hex.slice(1);
    }
    // convert 3-digit hex to 6-digits.
    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    if (hex.length !== 6) {
        throw new Error('Invalid HEX color.');
    }

    // invert color components
    var r = (255 - parseInt(hex.slice(0, 2), 16)).toString(16),
        g = (255 - parseInt(hex.slice(2, 4), 16)).toString(16),
        b = (255 - parseInt(hex.slice(4, 6), 16)).toString(16);
    // pad each with zeros and return
    return "#" + padZero(r) + padZero(g) + padZero(b);
}

function textColor(hex){
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    if ((result.r*0.299 + result.g*0.587 + result.b*0.114) > 186) {
        return "#000000";
    }else {
        return "#ffffff";
    }
}

function padZero(str, len) {
    len = len || 2;
    var zeros = new Array(len).join('0');
    return (zeros + str).slice(-len);
}

/**
 *
 * Somme des valeurs egale au total de la zone
 * @param values
 * @param width
 * @param height
 */
function normalize_sizes(sizes, width, height){
    //Sommes des valeurs
    var total_size = sizes.reduce(function(a,b){
        if(a.hasOwnProperty('sizes'))
            a = a.sizes;
        return a + b.sizes;
    },0);
    var total_area = width * height;
    sizes.sort(function(a,b){
        return b.sizes - a.sizes;
    });
    sizes = sizes.map(function(a){
        a.sizes = a.sizes * total_area / total_size;
        return a;
    });
    return sizes
}
function leftoverrow(sizes, x, y, dx, dy){
    covered_area = sizes.reduce(function(a,b){
        if(a.hasOwnProperty('sizes'))
            a = a.sizes;
        return a + b.sizes;
    },0);
    width = covered_area / dy;
    leftover_x = x + width;
    leftover_y = y;
    leftover_dx = dx - width;
    leftover_dy = dy;
    return [leftover_x, leftover_y, leftover_dx, leftover_dy];
}
function leftovercol(sizes, x, y, dx, dy){
    covered_area = sizes.reduce(function(a,b){
        if(a.hasOwnProperty('sizes'))
            a = a.sizes;
        return a + b.sizes;
    },0);
    height = covered_area / dx;
    leftover_x = x;
    leftover_y = y + height;
    leftover_dx = dx;
    leftover_dy = dy - height;
    return [leftover_x, leftover_y, leftover_dx, leftover_dy];
}
function leftover(sizes, x, y, dx, dy){
    if(dx>=dy){
        return leftoverrow(sizes, x, y, dx, dy);
    }else{
        return leftovercol(sizes, x, y, dx, dy);
    }
}
function squarify(sizes, x, y, dx, dy){
    //sizes should be pre-normalized wrt dx * dy (i.e., they should be same units)
    //or dx * dy == sum(sizes)
    //sizes should be sorted biggest to smallest
    sizes.sort(function(a,b){ return b.sizes - a.sizes;});
    if(sizes.length==0){
        return [];
    }else if(sizes.length==1){
        return layout(sizes, x, y, dx, dy);
    }
    //figure out where 'split' should be
    i = 1;
    while(i<sizes.length && worst_ratio(sizes.slice(0,i), x, y, dx, dy) >= worst_ratio(sizes.slice(0,i+1), x, y, dx, dy)){
        i++;
    }
    var current = sizes.slice(0,i);
    var remaining = sizes.slice(i);
    [leftover_x, leftover_y, leftover_dx, leftover_dy] = leftover(current, x, y, dx, dy);
    var list = [];
    list.push.apply(list,layout(current, x, y, dx, dy));
    list.push.apply(list,squarify(remaining, leftover_x, leftover_y, leftover_dx, leftover_dy));
    return list;
}

function worst_ratio(sizes, x, y, dx, dy){
    var rects = layout(sizes, x, y, dx, dy);
    var result = 0;
    for(var rect in rects){
        result = Math.max(Math.max(rects[rect]['sizes']['dx'] / rects[rect]['sizes']['dy'], rects[rect]['sizes']['dy'] / rects[rect]['sizes']['dx']),result);
    }
    return result;
}

function layout(sizes, x, y, dx, dy){
    if (dx >= dy){
        return layoutrow(sizes, x, y, dx, dy)
    } else {
        return layoutcol(sizes, x, y, dx, dy);
    }
}
/**
 * Tester avec python
 * @param sizes
 * @param x
 * @param y
 * @param dx
 * @param dy
 * @returns {Array}
 */
function layoutrow(sizes, x, y, dx, dy){
    //generate rects for each size in sizes
    //dx >= dy
    //they will fill up height dy, and width will be determined by their area
    //sizes should be pre-normalized wrt dx * dy (i.e., they should be same units)
    var covered_area = sizes.reduce(function(a,b){
        if(a.hasOwnProperty('sizes'))
            a = a.sizes;
        return a + b.sizes;
    },0);
    var width = covered_area / dy;
    var rects = [];
    for (var size in sizes) {
        var rect = JSON.parse(JSON.stringify(sizes[size]));

        rect.sizes = {'x': x, 'y': y, 'dx': width, 'dy': sizes[size].sizes / width};
        rects.push(rect);
        y += sizes[size].sizes / width;
    }

    return rects
}

function layoutcol(sizes, x, y, dx, dy){
    //generate rects for each size in sizes
    //dx < dy
    //they will fill up width dx, and height will be determined by their area
    //sizes should be pre-normalized wrt dx * dy (i.e., they should be same units)
    var covered_area = sizes.reduce(function(a,b){
        if(a.hasOwnProperty('sizes'))
            a = a.sizes;
        return a + b.sizes;
    },0);
    var height = covered_area / dx;
    var rects = [];
    for (var size in sizes) {
        var rect = JSON.parse(JSON.stringify(sizes[size]));
        rect.sizes = {'x': x, 'y': y, 'dx':  sizes[size].sizes /height, 'dy': height};
        rects.push(rect);
        x += sizes[size].sizes / height;
    }
    return rects
}


/**
 * Affichage d'une infobulle quand la souris survolle le canvas
 * @param event
 */
function canvasMouseEvent(event){
    if((typeof clickeventFlag !== 'undefined') && clickeventFlag){
        return;
    }else{
        clickeventFlag = false;
    }

    var x = event.layerX;
    var y = event.layerY;
    var jq=jQuery.noConflict();
    var sizeX = jq(document).width();
    var sizeY = jq(document).height();
    //Suppression de l'infobulle existant
    if(document.getElementById("tooltip"))
        document.getElementById("tooltip").remove();
    dataTreeMap.forEach(function(value){
        //Recherche de l'information qui a la meme position que la souris
        if(value.sizes.x < x && (value.sizes.x+value.sizes.dx) > x && value.sizes.y < y && (value.sizes.y+value.sizes.dy) > y){
            var iDiv = document.createElement('div');
            iDiv.id = 'tooltip';
            iDiv.className = 'tooltip';
            libelleFocus = value.libelle;
            groupementFocus = value.type;
            nbFocus = value.values;
            iDiv.innerHTML = "Libelle : " + value.libelle + "<br>Groupement : " + value.type + "<br>Valeur : "+ value.values.toLocaleString()
                + "<br>Pourcentage du groupement : "+ value.percentType.toLocaleString() + "%<br>Pourcentage sur le groupement : "+ value.percentLibInType.toLocaleString()
                + "%<br>Pourcentage sur tous : "+ value.percentLibInTotal.toLocaleString()+"%";
            iDiv.style.left =  (event.clientX+10)+'px';
            iDiv.style.top = (event.clientY+10)+'px';
            //Changement de la possition de l'infobulle pour éviter qu'elle ne sorte de l'écran
            if(sizeY - (event.clientY +140)<=0){
                iDiv.style.top = (event.clientY-110)+'px';
            }
            if(sizeX - (event.clientX +200)<=0){
                iDiv.style.left =  (event.clientX-300)+'px';
            }

            document.getElementsByTagName('body')[0].appendChild(iDiv);
            return;
        }
    });
}

/**
 * Affichage d'une infobulle quand la souris survolle le canvas
 * @param event
 */
function canvasClickEvent(event){
    if((typeof clickeventFlag !== 'undefined')  && clickeventFlag){
        //desactive le focus
        if(document.getElementById("tooltip")!=null)
            document.getElementById("tooltip").remove();
        clickeventFlag= false;
        canvasMouseEvent(event);
        return;
    }

    canvasMouseEvent(event);
    //Active le drapeau pour bloquer le tooltip
    clickeventFlag=true;
    var tooltip= document.getElementById("tooltip");
    var doc = document.documentElement;
    //Position du scroll de la page
    var scrolltop = (window.pageYOffset || doc.scrollTop)  - (doc.clientTop || 0);
    var scrollleft = (window.pageXOffset || doc.scrollLeft) - (doc.clientLeft || 0);

    var mytop = tooltip.offsetTop+scrolltop;
    var myleft = tooltip.offsetLeft+scrollleft;

    tooltip.style.position="absolute";
    tooltip.style.left = myleft+"px";
    tooltip.style.top = mytop+"px";
    var div = document.createElement("div");
    div.innerHTML="Actions : <br>";
    var a = document.createElement("a");
    a.innerHTML="Afficher plus d'information (lot de 500)";
    a.onclick=function (ev) {
        showLot();
        return false;
    };
    a.className="btn btn-default btn-xs";
    div.appendChild(a);
    /*var a2 = document.createElement("a");
    a2.innerHTML="Afficher plus d'information (tous)";
    a2.onclick=function (ev) {
        showLot();
        return false;
    };
    a2.className="btn btn-default btn-xs";
    div.appendChild(a2);*/
    tooltip.appendChild(div);
}
/**
 * Suppression de l'infobulle quand la souris quitte le canvas
 * @param event
 */
function endCanvasMouseEvent(event){
    if((typeof clickeventFlag !== 'undefined') && clickeventFlag){
        return;
    }else{
        clickeventFlag = false;
    }
    //Suppression de l'infobulle existant
    if(document.getElementById("tooltip"))
        document.getElementById("tooltip").remove();
}

function monthTranslate(nbmonth){
    var month = ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Decembre"];
    return month[nbmonth-1];
}


/**
* Affiche une fenetre modale avec un tableau contenant un extrait de la table
* @param database
* @param table
*/
function showLot(){
    var database="hadoopviewer_data";
    var jq=jQuery.noConflict();
    document.getElementById('myModalLabel').innerHTML = "Extrait de la source : " + configGraph.source_name + "<br> Table : " + configGraph.database + "." + configGraph.table;
    document.getElementById('myModalLabel').innerHTML += "<br> Filtres du graphique : <br>"+ JSON.stringify(configGraph.filtres) + " "+JSON.stringify(configGraph.operators);
    document.getElementById('myModalLabel').innerHTML += "<br> Choix : <br>{"+configGraph.wording+"="+libelleFocus+", "+configGraph.group+"="+groupementFocus+",";
    urlDatabase=base_url+'index.php/api/getSampleDataWhere?';
    configWithoutColor = JSON.parse(JSON.stringify(configGraph))
    configWithoutColor.color = null;
    console.log(configWithoutColor);
    urlDatabase += jQuery.param(configWithoutColor);
    urlDatabase += "&filtres2[" + encodeURI(configWithoutColor.wording) + "]=" + encodeURI(libelleFocus);
    urlDatabase+="&nbocc="+nbFocus;
    if(groupementFocus!="couleur"){
        urlDatabase += "&filtres2[" + encodeURI(configWithoutColor.group) + "]=" + encodeURI(groupementFocus);
    }
    if (configWithoutColor.speedfilters) {
        //gestion des filtres rapides
        configWithoutColor.speedfilters.forEach(function (f) {
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
    console.log(urlDatabase);
    //urlDatabase=encodeURI(urlDatabase);
    document.getElementById('myModalLabel').innerHTML += "}";
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
function createSpeedFiltersTreemap(data, config){
    if (!document.getElementById("speed-filters")) {
        return;
    }
    var speed_filters_div = document.getElementById("speed-filters");
    var jq=jQuery.noConflict();
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
            draw(data, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
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
        speed_filters_div.appendChild(document.createTextNode(title));
        speed_filters_div.appendChild(document.createElement("br"));
        speed_filters_div.appendChild(select);
        speed_filters_div.appendChild(document.createElement("br"));
        jq("#"+select.id).chosen({max_shown_results:50, no_results_text: "Aucun résultat pour", width: "100%",placeholder_text_multiple:"Toutes les valeurs"});
    });
    //speed_filters_div.

}

/**
 * Génération d'un formulaire d'intervalle pour modifier une treemap
 * @param data Donnée pour le graphique
 */
function createSpeedFilterRangeTreemap(data) {
    console.log("DEBUT createSpeedFilterRangeTreemap : " + Date());
    console.log(data);
    if (!document.getElementById("speed-filters")) {
        return;
    }
//Calcule du minimum et du maximum
    var maxValue = 0;
    var values = [];
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
                draw(data, ui.values[0], ui.values[1]);
            }
        });
        jq("#min_input").val(1);
        jq("#max_input").val(maxValue);
        jq("#min_input").on("keyup", function () {
            console.log(this.value);
            //Activation quand on modifie le champs min
            jq("#slider-range").slider("values", 0, this.value);
            draw(data, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
        });
        jq("#max_input").on("keyup", function () {
            //Activation quand on modifie le champs max
            jq("#slider-range").slider("values", 1, this.value);
            draw(data, jq("#slider-range").slider("values", 0), jq("#slider-range").slider("values", 1));
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