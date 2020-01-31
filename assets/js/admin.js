/**
 * Cette partie va contenir les fonctions utilisé par les administrateurs du site
 */



function resetBddManagementButton() {
    if (!inTestMode) {
        var test_database = document.getElementById("test_database");
        test_database.className = "btn btn-info";
        test_database.value = "Test la base de donnée";
        document.getElementById("save_database").disabled = true;
    }

}

var lastDatabaseTestSucess = "";
var lastEngineTestSucess = "";
var inTestMode = false;

function databaseConnexionTest() {
    inTestMode = true;
    var url = document.getElementById('database_url').value;
    console.log(site_url + "api/getMysqlCheck");
    var test_database = document.getElementById("test_database");
    var engine = document.getElementById("database_engine").value;

    if (url != null && url != "" && engine == "mysql") {
        document.getElementById("save_database").disabled = true;
        test_database.className = "btn btn-info";
        test_database.value = "Test la base de donnée (en cours)";
        test_database.disabled = true;
        document.getElementById("database_engine").disabled = true;
        document.getElementById('database_url').disabled = true;
        var jq = jQuery.noConflict();
        jq.post(site_url + "api/getMysqlCheck", {url: url}, function (data) {
            var success = false;
            var test_database = document.getElementById("test_database");
            if (data == "SUCCESS") {
                success = true;
                lastDatabaseTestSucess = url;
                lastEngineTestSucess = engine;
                console.log("SUCCESS");
                test_database.title = "";
            } else if (data == "ERROR_URL_EMPTY") {
                console.log("url vide");
            } else if (data.includes("Access denied for user")) {
                console.log("L'authentification est refusé!");
                test_database.title = "L'authentification est refusé!";
            } else if (data.includes("Can't connect to MySQL server on")) {
                console.log("MySQL n'est pas disponible!");
                test_database.title = "MySQL n'est pas disponible!";
            } else if (data.includes("Unknown MySQL server host")) {
                console.log("MySQL n'est pas disponible!");
                test_database.title = "MySQL n'est pas disponible!";
            } else if (data.includes("Unknown database")) {
                console.log("La base de donnée n'existe pas!");
                test_database.title = "La base de donnée n'existe pas!";
            } else {
                console.log("success error");
                test_database.title = "Erreur inconnu!";
                console.log(data);
            }
            if (success) {
                document.getElementById("save_database").disabled = false;
                test_database.className = "btn btn-success";
                test_database.value = "Test la base de donnée (reussi)";
            } else {
                document.getElementById("save_database").disabled = true;
                test_database.className = "btn btn-danger";
                test_database.value = "Test la base de donnée (échoué)";
            }
            document.getElementById("database_engine").disabled = false;
            document.getElementById('database_url').disabled = false;
            test_database.disabled = false;
            inTestMode = false;

        }).fail(function (data) {
            var test_database = document.getElementById("test_database");
            if (data.responseText.includes("Invalid DB driver")) {
                console.log("Le pilote de base de données n'est pas disponible!");
                test_database.title = "Le pilote de base de données n'est pas disponible!";
            } else if (data.responseText.includes("Invalid DB Connection String")) {
                console.log("L'url de connexion est fausse!");
                test_database.title = "L'url de connexion est fausse!";
            } else {
                console.log("error");
                test_database.title = "Erreur inconnu!";
                console.log(data);
            }
            document.getElementById("database_engine").disabled = false;
            document.getElementById('database_url').disabled = false;
            test_database.disabled = false;
            document.getElementById("save_database").disabled = true;

            test_database.className = "btn btn-danger";
            test_database.value = "Test la base de donnée (échoué)";
            inTestMode = false;
        });
    } else if (engine == "elasticsearch") {
        document.getElementById("save_database").disabled = true;
        test_database.className = "btn btn-info";
        test_database.value = "Test la base de donnée (en cours)";
        test_database.disabled = true;
        document.getElementById("database_engine").disabled = true;
        document.getElementById('database_url').disabled = true;
        var jq = jQuery.noConflict();
        jq.post(site_url + "api/getElasticCheck", {url: url}, function (data) {
            var success = false;
            var test_database = document.getElementById("test_database");
            if (data == "1") {
                success = true;
                lastDatabaseTestSucess = url;
                lastEngineTestSucess = engine;
                console.log("SUCCESS");
                test_database.title = "";
            } else if (data == "ERROR_URL_EMPTY") {
                console.log("url vide");
            } else {
                console.log("success error");
                test_database.title = "Erreur inconnu!";
                console.log(data);
            }
            if (success) {
                document.getElementById("save_database").disabled = false;
                test_database.className = "btn btn-success";
                test_database.value = "Test la base de donnée (reussi)";
            } else {
                document.getElementById("save_database").disabled = true;
                test_database.className = "btn btn-danger";
                test_database.value = "Test la base de donnée (échoué)";
            }
            document.getElementById("database_engine").disabled = false;
            document.getElementById('database_url').disabled = false;
            test_database.disabled = false;
            inTestMode = false;

        }).fail(function (data) {
            var test_database = document.getElementById("test_database");
            console.log("error");
            test_database.title = "Erreur inconnu!";
            console.log(data);

            document.getElementById("database_engine").disabled = false;
            document.getElementById('database_url').disabled = false;
            test_database.disabled = false;
            document.getElementById("save_database").disabled = true;

            test_database.className = "btn btn-danger";
            test_database.value = "Test la base de donnée (échoué)";
            inTestMode = false;
        });
    } else {
        test_database.className = "btn btn-danger";
        test_database.value = "Test la base de donnée (l'url est vide!)";
        document.getElementById("save_database").disabled = true;
        inTestMode = false;
    }
}

/**
 * Derniere verification
 * @returns {boolean}
 */
function databaseConnexionAdd() {
    if (document.getElementById('database_url').value == lastDatabaseTestSucess && document.getElementById("database_engine").value == lastEngineTestSucess) {
        return true;
    } else {
        return false;
        test_database.className = "btn btn-danger";
        test_database.value = "Test la base de donnée (L'url n'a pas été testé!)";
        document.getElementById("save_database").disabled = true;
    }
}

/**
 * Génération des tableaux de table autorisé pour des générations de graphique
 */
function getlistAuthorizedData() {
    //Suppression des formualaires contenu dans les class dataAuthorizedselectAddDiv
    var dataAuthorizedselectAddDiv = document.getElementsByClassName("dataAuthorizedselectAddDiv")
    for (var div in dataAuthorizedselectAddDiv) {
        dataAuthorizedselectAddDiv[div].innerHTML = "";
    }


    var jq = jQuery.noConflict();
    jq.get(site_url + "api/getlistAuthorizedData", function (data) {
        for (var source in data) {
            //Source de donnée
            //console.log(source);
            var div_source = document.getElementById("dataAuthorizedDiv_" + source);
            var tableHTML = document.createElement('table');
            tableHTML.id = "dataAuthorizedTable_" + source;
            tableHTML.className = "table table-striped";
            var tbody = document.createElement('tbody');
            tbody.id = "dataAuthorizedTbody_" + source;
            div_source.innerHTML = "";
            //$HTML.='<tr><th>Nom de la table</th><th>Type de calcul</th><th>Champ par défaut</th></tr>';
            var tr = document.createElement('tr');
            tr.appendChild(document.createElement('th'));
            var th_database = document.createElement('th');
            th_database.innerText = "Nom de la base de donnée";
            tr.appendChild(th_database);
            var th_table = document.createElement('th');
            th_table.innerText = "Nom de la table";
            tr.appendChild(th_table);
            var th_calc_type = document.createElement('th');
            th_calc_type.innerText = "Type de calcul";
            tr.appendChild(th_calc_type);
            var th_default_colomn = document.createElement('th');
            th_default_colomn.innerText = "Champ par défaut";
            tr.appendChild(th_default_colomn);
            tbody.appendChild(tr);

            for (var database in data[source]) {
                //Base de donnée
                for (var table in data[source][database]) {
                    //Table
                    var id = source + "_" + database + "_" + table;
                    var tr = document.createElement('tr');
                    tr.id = "rowAuthorizedData_" + id;
                    var td_button = document.createElement('td');
                    td_button.id = "td_button_" + id;
                    //Première colonne qui contiendra le bouton de suppression en mode modification
                    tr.appendChild(td_button)

                    //Nom de la base de données
                    var td_database = document.createElement('td');
                    td_database.innerText = database;
                    var td_database_input = document.createElement('input');
                    td_database_input.type = "hidden";
                    td_database_input.id = "database_" + id;
                    //td_database_input.name = "database[]";
                    td_database_input.value = database;
                    td_database.appendChild(td_database_input);
                    tr.appendChild(td_database);


                    //Nom de la table
                    var td_table = document.createElement('td');
                    td_table.innerText = table;
                    var td_table_input = document.createElement('input');
                    td_table_input.type = "hidden";
                    td_table_input.id = "table_" + id;
                    //td_table_input.name = "table[]";
                    td_table_input.value = table;
                    td_table.appendChild(td_table_input);
                    tr.appendChild(td_table);


                    //Type de calcule par défaut
                    var td_calc_type = document.createElement('td');
                    td_calc_type.id = "calc_type_" + id;
                    td_calc_type.innerText = data[source][database][table]['calc_type'];
                    tr.appendChild(td_calc_type);


                    //Champ par defaut pour le calcul
                    var td_default_colomn = document.createElement('td');
                    td_default_colomn.id = "default_colomn_" + id;

                    td_default_colomn.innerText = data[source][database][table]['default_colomn'];
                    tr.appendChild(td_default_colomn);
                    tbody.appendChild(tr);
                }
            }

            tableHTML.appendChild(tbody);
            div_source.append(tableHTML);
            document.getElementById("dataAuthorizedButtonModif_" + source).disabled = false;
            document.getElementById("dataAuthorizedButtonSubmit_" + source).disabled = true;
        }

        //console.log(data);

        document.getElementById("lastDateUpdateDataAuthorized").innerText = "Dernière mise à jour : " + new Date().toLocaleString();
    }).fail(function (data) {
        console.log(data);
    });

}

var cacheBddInfo = [];

/**
 * Met en mode modification/lecture seul le tableau des tables autorisé de la source selectionné
 */
function authorizedDataTableSwitch(source, engine) {
    console.log("authorizedDataTableSwitch(" + source + ")")

    var jq = jQuery.noConflict();
    if (engine == "mysql") {
        jq.post(site_url + "api/getExpertBDDInfo", {id: source}, function (data) {
            if (data.type != null && data.type == "error") {
                console.log("error");
            } else {
                console.log("success");
                document.getElementById("dataAuthorizedButtonModif_" + source).disabled = true;
                cacheBddInfo[source] = data;
                //Ajout des tables dans le formulaire et le tableau
                var calc_array = ["COUNT", "SUM", "AVG"];
                var divSelectAdd = document.getElementById("dataAuthorizedselectAddDiv_" + source);
                var selectAdd = document.createElement('select');
                selectAdd.id = "dataAuthorizedselectAddSelect_" + source;
                selectAdd.className = "form-control";
                data.forEach(function (row) {
                    var id = source + "_" + row['TABLE_SCHEMA'] + "_" + row['TABLE_NAME'];
                    var td_button = document.getElementById("td_button_" + id);
                    var optionAdd = document.createElement('option');
                    optionAdd.innerHTML = row['TABLE_SCHEMA'] + "." + row['TABLE_NAME'];
                    optionAdd.value = row['TABLE_SCHEMA'] + "<->" + row['TABLE_NAME'];
                    selectAdd.appendChild(optionAdd);
                    if (td_button != null) {

                        td_button.innerHTML = "";
                        var button = document.createElement('button');
                        button.className = "btn btn-xs btn-danger";
                        button.setAttribute('onclick', "document.getElementById('rowAuthorizedData_" + id + "').remove();");
                        var trash_span = document.createElement('span');
                        trash_span.className = "glyphicon glyphicon-trash";
                        button.appendChild(trash_span);
                        td_button.append(button);

                        document.getElementById("database_" + id).name = "database[]";
                        document.getElementById("table_" + id).name = "table[]";

                        var td_calc_type = document.getElementById("calc_type_" + id);
                        var td_calc_type_value = td_calc_type.innerText;
                        td_calc_type.innerHTML = "";
                        var td_calc_type_select = document.createElement('select');
                        td_calc_type_select.className = "form-control";
                        td_calc_type_select.name = "calc_type[]";
                        td_calc_type_select.id = "calc_type_select_" + id;
                        td_calc_type_select.setAttribute('onchange', "generateCalculFormAdminv2('calc_type_select_" + id + "', 'default_colomn_select_" + id + "', 'mysql','" + source + "', '" + row['TABLE_SCHEMA'] + "', '" + row['TABLE_NAME'] + "');");
                        calc_array.forEach(function (calc) {
                            var option = document.createElement('option')
                            option.innerHTML = calc;
                            if (calc == td_calc_type_value) {
                                option.selected = true;
                            }
                            td_calc_type_select.appendChild(option);
                        });
                        //td_calc_type_select.value=table;
                        td_calc_type.appendChild(td_calc_type_select);


                        var td_default_colomn = document.getElementById("default_colomn_" + id);
                        var td_default_colomn_value = td_default_colomn.innerText;
                        td_default_colomn.innerHTML = "";

                        var td_default_colomn_select = document.createElement('select');
                        td_default_colomn_select.className = "form-control";
                        td_default_colomn_select.name = "default_colomn[]";
                        td_default_colomn_select.id = "default_colomn_select_" + id;
                        td_default_colomn.appendChild(td_default_colomn_select);
                        generateCalculFormAdminv2("calc_type_select_" + id, "default_colomn_select_" + id, "mysql", source, row['TABLE_SCHEMA'], row['TABLE_NAME']);
                    }

                });
                var button_add = document.createElement('button');
                button_add.className = "btn btn-default";
                //'"++"'
                button_add.setAttribute('onclick', "addAuthorizedDataInTable('" + source + "', 'dataAuthorizedselectAddSelect_" + source + "');return false;");
                var span_add = document.createElement('span');
                span_add.className = "glyphicon glyphicon-plus";
                button_add.appendChild(span_add);
                divSelectAdd.append(selectAdd);
                divSelectAdd.append(button_add);
                document.getElementById("dataAuthorizedButtonSubmit_" + source).disabled = false;
            }
            console.log(data);
        }).fail(function (data_raw) {
            //Gestion des errors qui ne peuvent pas être attrapé en PHP
            console.log("error");
            console.log(data_raw);
        });
    } else if (engine == "elasticsearch") {
        jq.post(site_url + "api/getElasticInfo", {id: source}, function (data) {
            console.log(data);

            if (data.type != null && data.type == "error") {
                console.log("error");
            } else {
                console.log("success");
                document.getElementById("dataAuthorizedButtonModif_" + source).disabled = true;
                cacheBddInfo[source] = data;
                //Ajout des tables dans le formulaire et le tableau
                var calc_array = ["COUNT", "SUM", "AVG"];
                var divSelectAdd = document.getElementById("dataAuthorizedselectAddDiv_" + source);
                var selectAdd = document.createElement('select');
                selectAdd.id = "dataAuthorizedselectAddSelect_" + source;
                selectAdd.className = "form-control";
                Object.keys(data).forEach(function (row) {
                    var id = source + "_elasticsearch_" + row;
                    var td_button = document.getElementById("td_button_" + id);
                    var optionAdd = document.createElement('option');
                    optionAdd.innerHTML = row;
                    optionAdd.value = "elasticsearch<->" + row;
                    selectAdd.appendChild(optionAdd);
                    if (td_button != null) {

                        td_button.innerHTML = "";
                        var button = document.createElement('button');
                        button.className = "btn btn-xs btn-danger";
                        button.setAttribute('onclick', "document.getElementById('rowAuthorizedData_" + id + "').remove();");
                        var trash_span = document.createElement('span');
                        trash_span.className = "glyphicon glyphicon-trash";
                        button.appendChild(trash_span);
                        td_button.append(button);
                        var td_calc_type = document.getElementById("calc_type_" + id);
                        var td_calc_type_value = td_calc_type.innerText;
                        td_calc_type.innerHTML = "";
                        var td_calc_type_select = document.createElement('select');
                        td_calc_type_select.className = "form-control";
                        td_calc_type_select.name = "calc_type[]";
                        td_calc_type_select.id = "calc_type_select_" + id;
                        td_calc_type_select.setAttribute('onchange', "generateCalculFormAdminv2('calc_type_select_" + id + "', 'default_colomn_select_" + id + "', 'mysql','" + source + "', '" + row + "');");
                        calc_array.forEach(function (calc) {
                            var option = document.createElement('option')
                            option.innerHTML = calc;
                            if (calc == td_calc_type_value) {
                                option.selected = true;
                            }
                            td_calc_type_select.appendChild(option);
                        });
                        //td_calc_type_select.value=table;
                        td_calc_type.appendChild(td_calc_type_select);


                        var td_default_colomn = document.getElementById("default_colomn_" + id);
                        var td_default_colomn_value = td_default_colomn.innerText;
                        td_default_colomn.innerHTML = "";

                        var td_default_colomn_select = document.createElement('select');
                        td_default_colomn_select.className = "form-control";
                        td_default_colomn_select.name = "default_colomn[]";
                        td_default_colomn_select.id = "default_colomn_select_" + id;
                        td_default_colomn.appendChild(td_default_colomn_select);
                        generateCalculFormAdminv2("calc_type_select_" + id, "default_colomn_select_" + id, "mysql", source, row);
                    }

                });
                var button_add = document.createElement('button');
                button_add.className = "btn btn-default";
                //'"++"'
                button_add.setAttribute('onclick', "addAuthorizedDataInTable('" + source + "', 'dataAuthorizedselectAddSelect_" + source + "');return false;");
                var span_add = document.createElement('span');
                span_add.className = "glyphicon glyphicon-plus";
                button_add.appendChild(span_add);
                divSelectAdd.append(selectAdd);
                divSelectAdd.append(button_add);
                document.getElementById("dataAuthorizedButtonSubmit_" + source).disabled = false;
            }
            console.log(data);
        }).fail(function (data_raw) {
            //Gestion des errors qui ne peuvent pas être attrapé en PHP
            console.log("error");
            console.log(data_raw);
        });
    }
}


/**
 * Modifie le formulaire typecalculchamp pour avoir des données lié au type de calcul
 * @param champs
 */
function generateCalculFormAdminv2(typeCalID, typeCalChampID, engine, source, database, tableName) {
    var typecalcul = document.getElementById(typeCalID);
    var choice = typecalcul.options[typecalcul.selectedIndex].value;
    var typecalculchamp = document.getElementById(typeCalChampID);

    while (typecalculchamp.options.length > 0) {
        typecalculchamp.remove(typecalculchamp.options.length - 1);
    }
    var nb = 0;
    if (choice == "COUNT") {
        var opt = document.createElement('option');
        opt.text = "";
        opt.value = "";
        typecalculchamp.add(opt, null);
        nb++;
    }
    if (engine == "elastic") {

        for (var id in champs_elastic[tableName]) {
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
        var tables = cacheBddInfo[source];
        for (var table in tables) {
            if (tables[table].TABLE_NAME == tableName && tables[table].TABLE_SCHEMA == database) {
                var champs = tables[table]['tables'];
                for (id in champs) {
                    if (choice == "COUNT" && (champs[id].type != "int" && champs[id].type != "long" && champs[id].type != "bigint")) {
                        var opt = document.createElement('option');
                        opt.text = champs[id].name;
                        opt.value = champs[id].name;
                        typecalculchamp.add(opt, null);
                    } else if ((choice == "SUM" || choice == "AVG") && (champs[id].type == "int" || champs[id].type == "long" || champs[id].type == "bigint")) {
                        var opt = document.createElement('option');
                        opt.text = champs[id].name;
                        opt.value = champs[id].name;
                        typecalculchamp.add(opt, null);
                        nb++;
                    }
                }
            }
        }
    }
}

function addAuthorizedDataInTable(source, idselect) {
    var row_choise = document.getElementById(idselect).value;
    if (row_choise == "") {
        console.log("Aucune table!")
        return;
    }
    var choise_array = row_choise.split("<->");
    var database = choise_array[0];
    var table = choise_array[1];
    var id = source + "_" + database + "_" + table;
    //Ne pas ajouter la table s'il est déjà présent dans la liste
    if (!document.getElementById("rowAuthorizedData_" + id)) {
        if (!document.getElementById("dataAuthorizedTbody_" + source)) {
            var div_source = document.getElementById("dataAuthorizedDiv_" + source);
            var tableHTML = document.createElement('table');
            tableHTML.id = "dataAuthorizedTable_" + source;
            tableHTML.className = "table table-striped";
            var tbody = document.createElement('tbody');
            tbody.id = "dataAuthorizedTbody_" + source;
            div_source.innerHTML = "";
            var tr = document.createElement('tr');
            tr.appendChild(document.createElement('th'));
            var th_database = document.createElement('th');
            th_database.innerText = "Nom de la base de donnée";
            tr.appendChild(th_database);
            var th_table = document.createElement('th');
            th_table.innerText = "Nom de la table";
            tr.appendChild(th_table);
            var th_calc_type = document.createElement('th');
            th_calc_type.innerText = "Type de calcul";
            tr.appendChild(th_calc_type);
            var th_default_colomn = document.createElement('th');
            th_default_colomn.innerText = "Champ par défaut";
            tr.appendChild(th_default_colomn);
            tbody.appendChild(tr);
            tableHTML.appendChild(tbody);
            div_source.append(tableHTML);
        }
        var tr = document.createElement("tr");
        tr.id = "rowAuthorizedData_" + id;
        var td_trash = document.createElement("td");
        var button_trash = document.createElement("button");
        button_trash.className = "btn btn-xs btn-danger";
        button_trash.setAttribute('onclick', "document.getElementById('rowAuthorizedData_" + id + "').remove();return false;");
        var span_trash = document.createElement("span");
        span_trash.className = "glyphicon glyphicon-trash";
        button_trash.appendChild(span_trash);
        td_trash.appendChild(button_trash);
        tr.appendChild(td_trash);

        //Nom de la base de donnée
        var td_database = document.createElement("td");
        td_database.innerHTML = database;
        var input_database = document.createElement("input");
        input_database.type = "hidden";
        input_database.name = "database[]";
        input_database.value = database;
        td_database.append(input_database);
        tr.appendChild(td_database);

        //Nom de la table
        var td_table = document.createElement("td");
        td_table.innerHTML = table;
        var input_table = document.createElement("input");
        input_table.type = "hidden";
        input_table.name = "table[]";
        input_table.value = table;
        td_table.append(input_table);
        tr.appendChild(td_table);

        //Type de calcule par défaut
        var td_calc_type = document.createElement('td');
        td_calc_type.id = "calc_type_" + id;
        var td_calc_type_select = document.createElement('select');
        td_calc_type_select.className = "form-control";
        td_calc_type_select.name = "calc_type[]";
        td_calc_type_select.id = "calc_type_select_" + id;
        td_calc_type_select.setAttribute('onchange', "generateCalculFormAdminv2('calc_type_select_" + id + "', 'default_colomn_select_" + id + "', 'mysql','" + source + "', '" + database + "', '" + table + "');");
        var calc_array = ["COUNT", "SUM", "AVG"];
        calc_array.forEach(function (calc) {
            var option = document.createElement('option')
            option.innerHTML = calc;
            td_calc_type_select.appendChild(option);
        });
        //td_calc_type_select.value=table;
        td_calc_type.appendChild(td_calc_type_select);

        tr.appendChild(td_calc_type);


        //Champ par defaut pour le calcul
        var td_default_colomn = document.createElement('td');
        td_default_colomn.id = "default_colomn_" + id;
        var td_default_colomn_select = document.createElement('select');
        td_default_colomn_select.className = "form-control";
        td_default_colomn_select.name = "default_colomn[]";
        td_default_colomn_select.id = "default_colomn_select_" + id;
        td_default_colomn.appendChild(td_default_colomn_select);
        tr.appendChild(td_default_colomn);


        document.getElementById("dataAuthorizedTbody_" + source).append(tr)
        generateCalculFormAdminv2("calc_type_select_" + id, "default_colomn_select_" + id, "mysql", source, database, table);

    }

}

/**
 * Modifie le formulaire des tables disponnible pour avoir des données lié au moteur de données
 * @param champs
 */
function generateDefaultChoiceForm(default_source, default_database, default_table) {
    if (default_source == null || default_table == null) {
        console.log("La fonction a besoin de la variable listAuthorizedData !");
        return;
    }

    var sourceForm = document.getElementById('sourceForm');
    var tableForm = document.getElementById('tableForm');

    var sourcechoice = sourceForm.value;
    var tablechoice = tableForm.value;
    var nochoice = false;

    if (sourcechoice == "") {
        sourcechoice = default_source;
        tablechoice = default_database + "." + default_table;
        if (sourcechoice == "") {
            nochoice = true;
        }
    }
    if (typeof listAuthorizedData[sourcechoice] === 'undefined') {
        console.log("mode default");
        nochoice = true;
    }
    while (sourceForm.length > 0) {
        sourceForm.remove(0);
    }
    while (tableForm.length > 0) {
        tableForm.remove(0);
    }
    //console.log(sourcechoice+" "+tablechoice + " "+nochoice);
    Object.keys(listAuthorizedData).forEach(function (source) {
        var opt = document.createElement('option');
        opt.text = listAuthorizedData[source]['source_name'] + " (" + listAuthorizedData[source]['engine'] + ")";
        if (nochoice || sourcechoice == source) {
            opt.selected = true;
            listAuthorizedData[source]['tables'].forEach(function (table) {
                var opt = document.createElement('option');
                opt.text = table['database'] + "." + table['table'];
                if (sourcechoice == source && tablechoice == table['database'] + "." + table['table']) {
                    opt.selected = true;
                }
                opt.value = table['database'] + "." + table['table'];
                tableForm.append(opt);
            });
            nochoice = false;
        }
        opt.value = source;
        sourceForm.append(opt);


    });
}

