<?php


ob_start(); 

include("model_candidat/m_compte/m_compte.php");
 
 ?>

        <p> Nom: <?php if(isset($_SESSION['nom'])) echo $_SESSION['nom']; ?> </p>

        <p> Code: <?php if(isset($_SESSION['code'])) echo $_SESSION['code']; ?>  </p>

        <p> Mail: <?php if(isset($_SESSION['mail'])) echo $_SESSION['mail']; ?> </p>

        <p> Domaine: <?php if(isset($_SESSION['domaine'])) echo $_SESSION['domaine']; ?> </p>

        <p> Type de contrat recherché: <?php if(isset($_SESSION['type_contrat'])) 
                foreach($_SESSION['type_contrat'] as $contrat) echo $contrat." "; ?> </p>

        <p> Compétences: <?php if(isset($_SESSION['competences'])) echo $_SESSION['competences']; ?> </p>

        <p> Villes possibles: <?php if(isset($_SESSION['villes_possibles'])) echo $_SESSION['villes_possibles']; ?> </p>

        <p> Telephone: <?php if(isset($_SESSION['telephone'])) echo $_SESSION['telephone']; ?> </p>

        <p> Pays: <?php if(isset($_SESSION['pays'])) echo $_SESSION['pays']; ?> </p>

        <form method="post" action="../candidat/candidat.php?cc&action=modifcompte">

            <input type="submit" name="modifier_compte" value="Modifier" />
       </form>
       <p> Date de création du compte: <?php $date_crea = date_creation_compte();
                                          echo $date_crea;?> </p>
        
        <?php

        $formations = formations();
        if($formations!="")
        {
            echo"<br><br>Formations<br><br>";
            while($formation = $formations->fetch())
            {
                echo"Nom : ".$formation["nom_structure"]."<br>"; 
                echo"Diplôme: ".$formation["diplome"]."<br>";
                echo"Dates : ".format_date_zone($formation["date_debut"])." - ".format_date_zone($formation["date_fin"])."<br>";
                echo"Description: ".$formation["description_formation"]."<br><br>";
            }
        }

        $experiences = experiences();
        if($experiences!="")
        {
            echo"<br><br>Experiences<br><br>";
            while($experience = $experiences->fetch())
            {
                echo"Poste: ".$experience["nom_poste"]."<br>"; 
                echo"organisation: ".$experience["organisation"]."<br>";
                echo"Dates: ".format_date_zone($experience["date_debut"])." - ".format_date_zone($experience["date_fin"])."<br>";
                echo"Description: ".$experience["description_exp"]."<br><br>";
            }
        }
        
        ?>


<?php $content = ob_get_clean(); ?>

<?php require('vues_candidat/template_c.php'); ?>