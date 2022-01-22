<?php

ob_start(); 

 
 include("../candidat/model_candidat/m_compte/m_modifier_compte.php"); ?>

        <!-- Modification du compte candidat -->


        <?php

        if(isset($_POST["enregistrer_compte"]))
        {
            
            modifier_compte_candidat($_POST["modifier_mail"],$_POST["modifier_nom"],$_POST["modifier_mdp"],
            $_POST["modifier_domaine"],$_POST["competences"],$_POST['villes_possibles'],$_POST['telephone'],
            $_POST["c_donnees_timezone"], $_POST["choix_type_contrat"]);
            
        }
        if(isset($_POST["maj_exp"]))
        {
            maj_exp();
        }

        if(isset($_POST["maj_formation"]))
        {
            maj_formation();
        }

        infos_candidat();
        
        ?>

         

        <form method="post" action="../candidat/candidat.php?cc&action=modifcompte">
            Nom: <input type="text" name="modifier_nom" value="<?php if(isset($_SESSION['nom'])) echo $_SESSION['nom']; ?>" /> <br>

            Mail: <input type="email" name="modifier_mail" value=<?php echo $_SESSION['mail']; ?> /> 
            <?php 
                if(isset($_GET["doublon"]))
                {
                    echo "mail déja existant";
                }
            ?><br>

            Mot de passe: <input type="password" name="modifier_mdp"/><br>

            Domaine: <input type="text" name="modifier_domaine" value="<?php if(isset($_SESSION['domaine']))  echo $_SESSION['domaine']; ?>" />  <br>
            
            Contrat(s) recherché(s): <?php include("choisir_type_contrat.php"); ?> <br>

            Compétences: <textarea name="competences"><?php if(isset($_SESSION['competences']))  echo $_SESSION['competences']; ?></textarea> <br>
            
            Villes possibles: <textarea name="villes_possibles"><?php  if(isset($_SESSION['villes_possibles'])) echo $_SESSION['villes_possibles']; ?></textarea> <br>
            
            Telephone: <input type="text" name="telephone" value="<?php if(isset($_SESSION['telephone']))  echo $_SESSION['telephone']; ?>" /> <br>
            
            Pays: <?php include("include_pays.html");?> <br><br>
            Nombre d'experience à mettre <input type="number" id="nb_exp" name="nb_exp"> <button type="button" onclick="mettre_experience()"> Valider </button>
            
            <div id="experience"></div><br><br>

            Nombre de diplômes à mettre <input type="number" id="nb_formation" name="nb_formation"> <button type="button" onclick="mettre_formation()"> Valider </button>
            
            <div id="formation"></div>
        

            <input type="submit" name="enregistrer_compte" value="Enregister" /><br><br>


        </form>

        <script> //ajouter le champ permettant de mettre l'experience
                function mettre_experience() {

                var nb_exp = document.getElementById("nb_exp").value;

                var exp="";
                for (i = 0; i <nb_exp; i++) {

                    exp+="Nom du poste: <input type='text' name='exp_nom_poste"+i+"'/><br>";
                    exp+="Entreprise: <input type='text' name='exp_nom_entreprise"+i+"'/><br>";
                    exp+="Début: <input type='date' name='exp_date_debut"+i+"'/> fin: <input type='date' name='exp_date_fin"+i+"'/><br>";
                    exp+="Description: <textarea name='exp_description"+i+"'></textarea><br><br>";
                }
                
                document.getElementById("experience").innerHTML = exp;
                }

                //ajouter le champ permettant de mettre la formation
                function mettre_formation() {

                var nb_formation = document.getElementById("nb_formation").value;

                var formation="";
                for (i = 0; i <nb_formation; i++) {

                    formation+="Nom structure: <input type='text' name='formation_nom_structure"+i+"'/><br>";
                    formation+="Diplôme: <input type='text' name='formation_diplome"+i+"'/><br>";
                    formation+="Début: <input type='date' name='formation_date_debut"+i+"'/> fin: <input type='date' name='formation_date_fin"+i+"'/><br>";
                    formation+="Description: <textarea name='formation_description"+i+"'></textarea><br><br>";
                }

                document.getElementById("formation").innerHTML = formation;
                }
        </script>

    
            <?php

                    $formations = formations();

                    if($formations!="")
                    {
                        echo"Formations<br><br>";
                        while($formation = $formations->fetch())
                        {
                            $formation_id = $formation["id"];
                            echo"<form method='post' action='../candidat/candidat.php?cc&action=modifcompte'>";
                            echo"Nom structure: <input type='text' value='".$formation["nom_structure"]."' name='nom_structure$formation_id' /> <br>"; 
                            echo"Diplome: <input type='text' value='".$formation["diplome"]."' name='diplome$formation_id' /><br>";
                            echo"Dates: <input type='date' value='".$formation["date_debut"]."' name='formation_date_debut$formation_id' /> - <input type='date' value='".$formation["date_fin"]."' name='formation_date_fin$formation_id' /><br>";
                            echo"Description: <input type='text' value='".$formation["description_formation"]."' name='description_formation$formation_id' /><br><br>";
                            echo"<input type='submit' value='mettre à jour' name='maj_formation'/>";
                            echo"<input type='hidden' value='$formation_id' name='formation_id'>";
                            echo"</form><br><br>";
                            
                        }
                    }


                    $experiences = experiences();

                    if($experiences!="")
                    {
                        echo"Experiences<br><br>";
                        while($experience = $experiences->fetch())
                        {
                            $exp_id = $experience["id"];
                            echo"<form method='post' action='../candidat/candidat.php?cc&action=modifcompte'>";
                            echo"Poste: <input type='text' value='".$experience["nom_poste"]."' name='nom_poste$exp_id' /> <br>"; 
                            echo"organisation: <input type='text' value='".$experience["organisation"]."' name='nom_orga$exp_id' /><br>";
                            echo"Dates: <input type='date' value='".$experience["date_debut"]."' name='exp_date_debut$exp_id' /> - <input type='date' value='".$experience["date_fin"]."' name='exp_date_fin$exp_id' /><br>";
                            echo"Description: <input type='text' value='".$experience["description_exp"]."' name='exp_orga$exp_id' /><br><br>";
                            echo"<input type='submit' value='mettre à jour' name='maj_exp'/>";
                            echo"<input type='hidden' value='$exp_id' name='exp_id'>";
                            echo"</form><br><br>";
                            
                        }
                    }

            ?>



<?php $content = ob_get_clean(); ?>

<?php 

if(compte_complet()) 
    require('vues_candidat/template_c.php');
else
    require('vues_candidat/template_c_incomplet.php'); ?>
                
