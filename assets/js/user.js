/**
 * Selectionne tous les checkbox avec comme name la valeur de nameForm
 * @param checked
 * @param nameForm
 */
function selectAll(checked,nameForm){
    var forms = document.getElementsByName(nameForm);
    forms.forEach(function(form){
        form.checked = checked;
    });

}


/**
 * Modifie le formulaire des tables disponnible pour avoir des données lié au moteur de données
 * @param champs
 */
function generateTableFormV2() {
    if (typeof listAuthorizedData === 'undefined' || listAuthorizedData == null) {
        console.log("La fonction a besoin de la variable listAuthorizedData !");
        return;
    }
    if (typeof user === 'undefined' || user == null) {
        console.log("La fonction a besoin de la variable user !");
        return;
    }

    var sourceForm = document.getElementById('sourceForm');
    var tableForm = document.getElementById('tableForm');

    var sourcechoice = sourceForm.value;
    var tablechoice = tableForm.value;
    var nochoice = false;
    if (sourcechoice == "") {
        sourcechoice = user['actual_source'];
        tablechoice = user['actual_database'] + "." + user['actual_table'];
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
    console.log(sourcechoice + " " + tablechoice + " " + nochoice);
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