<?php

ob_start(); 

 
      include("../candidat/model_candidat/m_offres/m_candidatures.php");

       

        //Si on clique sur -> direction page de droite
        if(isset($_POST["direction_page_droite"]))
        {
            vers_page_droite();
        }

        //Si on clique sur <- direction page de gauche
        if(isset($_POST["direction_page_gauche"]))
        {
            vers_page_gauche();
        }

        //Si l'utilisateur n'a pas définit d'affichage max de candidature, une valeur par defaut est mise
        if(!isset($_SESSION["aff_max_candidatures"]))
        {
            $_SESSION["aff_max_candidatures"] = 10;

        }

        if(!isset($_SESSION["candidatures_depart"]))
        {
            $_SESSION["candidatures_depart"] = 0;
            $_SESSION["candidatures_n_page_actuelle"] = 1; 
        }

        //Si l'utilisateur change l'affichage max
        if(isset($_POST["form_nb_aff_max_candidatures"]))
        {
            $_SESSION["aff_max_candidatures"] = $_POST["nb_max_aff_candidature"];

            $_SESSION["candidatures_depart"] = 0;
            $_SESSION["candidatures_n_page_actuelle"] = 1; 
        

        }

        if(isset($_POST["form_nb_aff_max_candidatures"]))
        {
            $_SESSION["aff_max_candidatures"] = $_POST["nb_max_aff_candidature"];
        }

        if(isset($_POST["form_postuler"]))
        {
            c_postuler();
        }

        $afficher_candidatures = afficher_candidatures();
        
        if($afficher_candidatures != "rien")
            obtenir_nb_pages();
        ?>

        <p> Candidatures: Max de candidatures à afficher par page
        <form method="post" action="../candidat/candidat.php?co&action=candidatures">
            <select name="nb_max_aff_candidature">
                <option value="10" 
                    <?php if($_SESSION["aff_max_candidatures"]==10) echo " selected"; ?>
                >10</option>
                <option value="15" 
                    <?php if($_SESSION["aff_max_candidatures"]==15) echo " selected"; ?>
                >15</option>
                <option value="20" 
                    <?php if($_SESSION["aff_max_candidatures"]==20) echo " selected"; ?>
                >20</option>
            </select>
              
            <input type="submit" name="form_nb_aff_max_candidatures" value="Changer">
            </form>
        </p>



        <?php


       

        if($afficher_candidatures != "rien")
        {
            echo "Page ".$_SESSION["candidatures_n_page_actuelle"]."/".$_SESSION["nb_pages_candidatures"]."<br>";
            $i = 0; //pour limiter le nombre de candidature affiché
            $a = 0; //le compteur qui détermine avec la variable candidatures_depart à partir de quel activité
            //commencé l'affichage

            foreach($afficher_candidatures as $offre)
            {
                if($a==$_SESSION["candidatures_depart"])
                {
                    if($i<$_SESSION["aff_max_candidatures"])
                    {
                        echo "Organisation: ".$offre["nom_organisation"]."<br>"."Poste: ".$offre["poste"]."<br>";
                        echo "Description: ".$offre["description_offre"]."<br>";
                        echo "Date candidature: ".date_candidature($_SESSION["id"],$offre["id"])."<br>";
                        $statut_candidature = statut_candidature($_SESSION["id"],$offre["id"]);
                        echo "statut candidature: ".$statut_candidature["statut"]."<br><br>";

                        if(interaction_candidat_uo($offre['id_createur']))
                        {
                            echo"<form method='post' action='../candidat/candidat.php?ce&action=pleconverser&iduo=".$offre['id_createur']."&ido=".$offre['id']."'>
                            <input type='submit' value='Converser'>
                            </form>";
                        }


                        echo "<br><br>";

                        $i++;
                    }
                }

                if($a!=$_SESSION["candidatures_depart"])
                    {
                        $a++;
                    }
            }
        }

        


        ?>

        <?php
            //Afficher les boutons uniquement tant qu'il y a des infos à affiché
            //dans tel ou tel direction

            
            if($afficher_candidatures != "rien")
            {
                
                echo "Page ".$_SESSION["candidatures_n_page_actuelle"]."/".$_SESSION["nb_pages_candidatures"];

                if($_SESSION["candidatures_n_page_actuelle"]!=$_SESSION["nb_pages_candidatures"])
                echo'
                <form method="post" action="../candidat/candidat.php?co&action=candidatures">
                <input type="submit" name="direction_page_droite" value=">">
                </form> ';


                if($_SESSION["candidatures_n_page_actuelle"]!=1)
                echo'
                <form method="post" action="../candidat/candidat.php?co&action=candidatures">
                <input type="submit" name="direction_page_gauche" value="<">
                </form> ';
            
            
            }
        ?>
<?php $content = ob_get_clean(); ?>

<?php require('vues_candidat/template_c.php'); ?>