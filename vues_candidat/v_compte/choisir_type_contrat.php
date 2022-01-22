<br>
<input type="checkbox" name="choix_type_contrat[]" value="CDI" <?php if(isset($_SESSION['type_contrat']) && in_array("CDI",$_SESSION['type_contrat'])) echo "checked"; ?> >CDI<br>
<input type="checkbox" name="choix_type_contrat[]" value="CDD" <?php if(isset($_SESSION['type_contrat']) && in_array("CDD",$_SESSION['type_contrat'])) echo "checked"; ?> >CDD<br>
<input type="checkbox" name="choix_type_contrat[]" value="Stage" <?php if(isset($_SESSION['type_contrat']) && in_array("Stage",$_SESSION['type_contrat'])) echo "checked"; ?> >Stage<br>
<input type="checkbox" name="choix_type_contrat[]" value="Alternance" <?php if(isset($_SESSION['type_contrat']) && in_array("Alternance",$_SESSION['type_contrat'])) echo "checked"; ?> >Alternance<br>