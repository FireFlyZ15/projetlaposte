function checkPassword(passwdId, passwd_verifyId){
    var passwdForm = document.getElementById(passwdId);
    var passwd_verifyForm = document.getElementById(passwd_verifyId);
    passwdForm.style.borderColor = "";
    passwd_verifyForm.style.borderColor = "";
    if(passwdForm.value==passwd_verifyForm.value){
        return true;
    }
    console.log("Mot de passes differents");
    passwdForm.style.borderColor = "red";
    passwd_verifyForm.style.borderColor = "red";
    return false;
}

function checkRegiterForm(emailId, passwdId, passwd_verifyId){
    return checkPassword(passwdId, passwd_verifyId);
}

/**
 * Modifie le formulaire des tables disponnible pour avoir des données lié au moteur de données
 * @param champs
 */
function generatetableForm() {
    var engine = document.getElementById('engine');
    var choice = engine.options[engine.selectedIndex].value;
    var bdd = document.getElementById('bdd');
    while (bdd.options.length > 0) {
        bdd.remove(bdd.options.length - 1);
    }
    if (choice == "elasticsearch") {
        for (id in listElastic) {
            var opt = document.createElement('option');
            opt.text = id;
            opt.value = id;
            bdd.add(opt, null);
        }
    } else {
        for (id in listBDD) {
            var opt = document.createElement('option');
            opt.text = listBDD[id].TABLE_NAME + " : " + listBDD[id].TABLE_ROWS + " lignes en base. Mise à jour le " + listBDD[id].UPDATE_TIME;
            opt.value = listBDD[id].TABLE_NAME;
            bdd.add(opt, null);
        }
    }
}