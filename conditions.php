<?php require_once("init.php"); ?>
<!DOCTYPE html>
<html lang="<?php echo $lang ?>">
    <head>
        <?php 
            $headTitle = ${'agb_' . $lang};
            require_once("head.php");
        ?>
    </head>
    <body>
        <div class="container mx-auto">
        
            <?php require_once("banner.php"); ?>
            
            <div>
                <?php
                
                if ($lang == "de") {
                    require_once("conditions_de.php");
                } else if ($lang == "en") {
                    require_once("conditions_en.php");
                } else if ($lang == "it") {
                    require_once("conditions_it.php");
                } else {
                    require_once("conditions_fr.php");
                }
            
                ?>
            </div>
    
            <?php require_once("footer.php"); ?>
            
        </div>
    
        <?php require_once("finish.php"); ?>
        
    </body>
</html>