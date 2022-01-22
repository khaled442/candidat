<?php

ob_start(); 

 
 ?>
    <?php 
        include("model_candidat/m_journal.php");
        infos_candidat();


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

        $informations = afficher_journal_bord();


        //Si l'utilisateur n'a pas définit d'affichage max d'activité, une valeur par defaut est mise
        if(!isset($_SESSION["aff_max_activite"]))
        {
            $_SESSION["aff_max_activite"] = 10;

        }
        //aussi la variable qui indique à partir de quel activité on commence l'affichage dans tel ou tel page
        //nommé acti_depart
        if(!isset($_SESSION["acti_depart"]))
        {
            $_SESSION["acti_depart"] = 0;
            $_SESSION["n_page_actuelle"] = 1; 
            //nécessaire pour ne plus afficher le bouton allant vers la droite si on est à la dernière page
            //pareil aussi pour ne plus aller vers la gauche lorsqu'on se situe à la première page
        }

        //Si l'utilisateur change l'affichage max
        if(isset($_POST["form_nb_aff_max"]))
        {
            $_SESSION["aff_max_activite"] = $_POST["nb_max_aff_act"];
        
            //réinitialision des variables acti_depart et n_page_actuelle
            $_SESSION["acti_depart"] = 0;
            $_SESSION["n_page_actuelle"] = 1; 

        }

        if($informations != "rien")
            obtenir_nb_pages();

        ?>

        
        <p> Journal de bord: Max d'activité à afficher par page
        <form method="post" action="../candidat/candidat.php?ccjd&action=journal">
            <select name="nb_max_aff_act" id="nb_max_aff_activite">
                <option value="10" 
                    <?php if($_SESSION["aff_max_activite"]==10) echo " selected"; ?>
                >10</option>
                <option value="15" 
                    <?php if($_SESSION["aff_max_activite"]==15) echo " selected"; ?>
                >15</option>
                <option value="20" 
                    <?php if($_SESSION["aff_max_activite"]==20) echo " selected"; ?>
                >20</option>
            </select>
              
            <input type="submit" name="form_nb_aff_max" value="Changer">
            </form>
        </p>

        
        <?php
            
            if($informations != "rien")
            {
                echo "Page ".$_SESSION["n_page_actuelle"]."/".$_SESSION["nb_pages"]."<br>";

                $i = 0; //pour limiter le nombdre d'activité affiché
                $a = 0; //le compteur qui détermine avec la variable acti_depart à partir de quel activité
                //commencé l'affichage
                
                foreach($informations as $activite)
                {
                    if($a==$_SESSION["acti_depart"])
                    {
                        if($i<$_SESSION["aff_max_activite"])
                        {
                            echo "Date activité: ".format_date_zone($activite["date_activite"])." : ".$activite["description_activite"]."<br><br>";
                            $i++;
                        }
                    }
                    //tant que a n'a pas la valeur de acti_depart on saute les activité à ne pas afficher sur telle page
                    if($a!=$_SESSION["acti_depart"])
                    {
                        $a++;
                    }
                }
            } 
        
        ?>
        </div>
        <?php
            //Afficher les boutons uniquement tant qu'il y a des infos à affiché
            //dans tel ou tel direction
            if($informations != "rien")
            {
                
                echo "Page ".$_SESSION["n_page_actuelle"]."/".$_SESSION["nb_pages"];

                if($_SESSION["n_page_actuelle"]!=$_SESSION["nb_pages"])
                echo'
                <form method="post" action="../candidat/candidat.php?ccjd&action=journal">
                <input type="submit" name="direction_page_droite" value=">">
                </form> ';


                if($_SESSION["n_page_actuelle"]!=1)
                echo'
                <form method="post" action="../candidat/candidat.php?ccjd&action=journal">
                <input type="submit" name="direction_page_gauche" value="<">
                </form> ';
            
            
            }
        ?>

<?php $content = ob_get_clean(); ?>

<?php require('template_c.php'); ?>
        

