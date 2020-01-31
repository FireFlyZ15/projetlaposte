<body>
<div class="row">
    <div class="col-xs-6">
        <h2>A propos</h2>
        <p>
            <?= PROJECT_NAME ?> est une application de génération et de partage de graphiques à partir des données
            issues de TAE.<br>
        </p>
        <p>
            Plusieurs sources de données sont disponibles :
            <ul>
                <li>
                    Syslog : Les Unités de Traitement de l'Enveloppe enregistrent les informations générées lors du tri sur les différentes Plateformes Industrielles du Courrier.

                    Les fichiers générés sont collectés et traités sur le cluster Hadoop de la Direction Technique. Les informations qu'ils contiennent sont ensuite analysées, traitées et agglomérées sous forme de fiches enveloppes.
                    Des bases de données simplifiées sont mises à disposition du site <?= PROJECT_NAME ?> pour
                    réalisation de tableaux d'indicateurs.
                    Les données disponibles sur le site sont mises à jour mensuellement.
                </li>
                <li>
                    Kafka : Des flux d'événements de tri courrier transitant par technologie Kafka sont analysés et traités. Des informations sur le Vidéocodage en cours et réalisées sont mises à disposition au fil de l'eau.
                    Les données disponibles sur le site sont mises à jour dynamiquement.
                </li>
            </ul>
        </p>
        <p>Le traitement des données, leur mise en forme et leur mise à disposition sont réalisés par la Direction
            Technique. L'outil de restitution et publication "<?= PROJECT_NAME ?>" a été développé par
            I3AP/BUILD/IT.</p>
        <h2>Aides</h2>
        <p>
            <ul>
                <li>
                    Par défaut, les nouveaux utilisateurs ont seulement un droit de lecture des graphiques préconstitués. Pour pouvoir créer des graphiques, merci de prendre contact.
                </li>
                <li>
                    Besoin d’une donnée indisponible ? Création de nouveau graphique ? Merci de nous contacter.
                </li>
            </ul>
        </p>
        <h2>Technologies utilisées</h2>
        <p>
        <ul>
            <li>
                <a href="https://getbootstrap.com/">Boostrap 3.4</a> : Framework HTML/CSS (License MIT)
            </li>
            <li>
                <a href="https://www.chartjs.org/">Chart.js</a> : Histogramme, diagramme circulaire (License MIT)
            </li>
            <li>
                <a href="https://harvesthq.github.io/chosen/">Chosen</a> : Formulaire select en HTML (License MIT)
            </li>
            <li>
                <a href="https://harvesthq.github.io/chosen/">CodeIgniter 3</a> : Framework PHP (License MIT)
            </li>
            <li>
                <a href="https://github.com/niklasvh/html2canvas">Html2canvas</a> : Mise en image d'un canvas (License
                MIT)
            </li>
            <li>
                <a href="http://jquery.com/">JQuery</a> : Bibliothèque JS (License MIT)
            </li>
            <li>
                <a href="https://jqueryui.com/">JQuery UI</a> : Collection de widgets (License MIT)
            </li>
            <li>
                <a href="https://github.com/laserson/squarify">Squarify</a> : Generateur de Treemap en python (License
                Apache) traduit et modifié en JS sur <?= PROJECT_NAME ?>
            </li>
        </ul>
        </p>
    </div>
    <div class="col-xs-6">
        <h2>Nous contacter</h2>
        <h3>Adresse</h3>
        <p>CS86334 <br/>
            10 RUE DE L'ILE MABON<br/>
            44263 NANTES CEDEX 2</p>
        <h3>Mail</h3>
        <p>
            <ul>
                <li><a href="mailto:sebastien.villalon@laposte.fr">VILLALON Sébastien - Développeur</a></li>
                <li><a href="mailto:frederic.briand@laposte.fr">BRIAND Frederic - Chef de projet</a></li>
            </ul>
        </p>

    </div>
</div>
<footer class="center">Copyright © 2017 DIRECTION TECHNIQUE</footer>

</body>