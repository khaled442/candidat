<?php

ob_start(); 

 
 include("../candidat/model_candidat/m_offres/m_offres.php"); ?>



<?php 
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

        if(!isset($_SESSION["aff_max_of"]))
        {
            $_SESSION["aff_max_of"] = 10;

        }

        if(!isset($_SESSION["of_depart"]))
            {
                $_SESSION["of_depart"] = 0;
                $_SESSION["of_n_page_actuelle"] = 1; 
            }

        if(isset($_POST["form_nb_aff_max_of"]))
        {
                $_SESSION["aff_max_of"] = $_POST["nb_max_aff_of"];

                $_SESSION["of_depart"] = 0;
                $_SESSION["of_n_page_actuelle"] = 1; 
        }

        if(isset($_POST["recherche_type_contrat"]))
                $_SESSION["recherche_type_contrat"]=$_POST["recherche_type_contrat"];

        ?>

        <form method="post" action="../candidat/candidat.php?co&action=voiroffres">
         <input size="50" type="search" name="recherche_offre_candidat" />
         <br>Contrats:<br>
            <input type="checkbox" name="recherche_type_contrat[]" value="CDI" <?php if(isset($_SESSION["recherche_type_contrat"]) && in_array("CDI",$_SESSION["recherche_type_contrat"])) echo "checked"; ?> >CDI<br>
            <input type="checkbox" name="recherche_type_contrat[]" value="CDD" <?php if(isset($_SESSION["recherche_type_contrat"]) && in_array("CDD",$_SESSION["recherche_type_contrat"])) echo "checked"; ?> >CDD<br>
            <input type="checkbox" name="recherche_type_contrat[]" value="Stage" <?php if(isset($_SESSION["recherche_type_contrat"]) && in_array("Stage",$_SESSION["recherche_type_contrat"])) echo "checked"; ?>>Stage<br>
            <input type="checkbox" name="recherche_type_contrat[]" value="Alternance" <?php if(isset($_SESSION["recherche_type_contrat"]) && in_array("Alternance",$_SESSION["recherche_type_contrat"])) echo "checked"; ?> >Alternance<br><br>
         <input type="submit" name="valider_recherche" value="Rechercher" />
        </form>



        <p> Offres: Max à afficher par page   
        <form method="post" action="../candidat/candidat.php?co&action=voiroffres">
            <select name="nb_max_aff_of">
                <option value="10" 
                    <?php if($_SESSION["aff_max_of"]==10) echo " selected"; ?>
                >10</option>
                <option value="15" 
                    <?php if($_SESSION["aff_max_of"]==15) echo " selected"; ?>
                >15</option>
                <option value="20" 
                    <?php if($_SESSION["aff_max_of"]==20) echo " selected"; ?>
                >20</option>
            </select>
              
            <input type="submit" name="form_nb_aff_max_of" value="Changer">
            </form>
        </p>


        <?php

                 
                if(isset($_POST["valider_recherche"]) && $_POST["recherche_offre_candidat"]!=null)
                {
                   
                    $_SESSION["recherche_offre_candidat"]=$_POST["recherche_offre_candidat"];


                    $reponse = recherche_offre_candidat();

                    if($reponse!="rien")
                    {
                        obtenir_nb_pages();

                        $e = 0; //pour limiter le nombre d'entretiens' à affiché dans une page
                        $a = 0; //le compteur qui détermine avec la variable re_depart à partir de quelle offre
                    //commencé l'affichage

                        foreach($reponse as $offres)
                        {
                            if($a==$_SESSION["of_depart"] && $e<$_SESSION["aff_max_of"])
                            {
                                echo "<p> organisation: ".$offres["nom_organisation"]."<br> Poste: ".$offres["poste"]."<br>Lieu :"
                                .$offres["lieu"]."<br>Type de contrat :".$offres["type_contrat"]."<br>".
                                "Date de publication :".format_date_zone($offres["date_publication"])."<br></p>";
                                if($offres["date_modification"]!=null)
                                    echo "<p>Offre mise à jour le: ".$offres["date_modification"]."</p>";

                                if(deja_postulé($offres["id"]))
                                    {
                                        echo "Candidature déja envoyée </p>";
                                    }

                                    echo '<form method="post" action="../candidat/candidat.php?co&action=detailoffre&ido='.$offres["id"].'">
                                    <input type="submit" name="voir_offre" value="Voir" />
                                    </form><br>';
                                
                                        $e++;
                            }
                            if($a!=$_SESSION["of_depart"])
                            {
                                $a++;
                            }
                        }

                        echo "Page ".$_SESSION["of_n_page_actuelle"]."/".$_SESSION["nb_pages_of"];

                        if($_SESSION["of_n_page_actuelle"]!=$_SESSION["nb_pages_of"])
                            echo'
                            <form method="post" action="../candidat/candidat.php?co&action=voiroffres">
                            <input type="submit" name="direction_page_droite" value=">">
                            </form> ';


                            if($_SESSION["of_n_page_actuelle"]!=1)
                            echo'
                            <form method="post" action="../candidat/candidat.php?co&action=voiroffres">
                            <input type="submit" name="direction_page_gauche" value="<">
                            </form> ';

                    }

                    
                }
                //Je sauvegarde la recherche du candidat si il change de page et revient sur la page de recherche d'offre
              if(isset($_SESSION["recherche_offre_candidat"]) && !isset($_POST["valider_recherche"]))
                { 

                    $reponse = recherche_offre_candidat();

                    if($reponse!="rien")
                    {

                        obtenir_nb_pages();

                        $e = 0;
                        $a = 0;

                        foreach($reponse as $offres)
                        {
                            if($a==$_SESSION["of_depart"] && $e<$_SESSION["aff_max_of"])
                            {
                                echo "<p> organisation: ".$offres["nom_organisation"]."<br> Poste: ".$offres["poste"]."<br>Lieu :"
                                .$offres["lieu"]."<br>Type de contrat :".$offres["type_contrat"]."<br>".
                                "Date de publication :".format_date_zone($offres["date_publication"])."<br></p>";
                                if($offres["date_modification"]!=null)
                                    echo "<p>Offre mise à jour le: ".$offres["date_modification"]."</p>";

                                echo "id_offre ".$offres["id"];
                                if(deja_postulé($offres["id"]))
                                {
                                    echo "Candidature déja envoyée </p>";
                                }

                                echo '<form method="post" action="../candidat/candidat.php?co&action=detailoffre&ido='.$offres["id"].'">
                                <input type="submit" name="voir_offre" value="Voir" />
                                </form><br>';
                                
                                        $e++;
                            }

                            if($a!=$_SESSION["of_depart"])
                            {
                                $a++;
                            }
                        }

                        echo "Page ".$_SESSION["of_n_page_actuelle"]."/".$_SESSION["nb_pages_of"];

                            if($_SESSION["of_n_page_actuelle"]!=$_SESSION["nb_pages_of"])
                            echo'
                            <form method="post" action="../candidat/candidat.php?co&action=voiroffres">
                            <input type="submit" name="direction_page_droite" value=">">
                            </form> ';


                            if($_SESSION["of_n_page_actuelle"]!=1)
                            echo'
                            <form method="post" action="../candidat/candidat.php?co&action=voiroffres">
                            <input type="submit" name="direction_page_gauche" value="<">
                            </form> ';
                    }
                }


        ?>

        
<?php $content = ob_get_clean(); ?>

<?php require('vues_candidat/template_c.php'); ?>
                