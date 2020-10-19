<?php require_once("init.php"); ?>
<!DOCTYPE html>
<html lang="<?php echo $lang ?>">
    <head>
        <?php 
            $headTitle = ${'contact_' . $lang};
            require_once("head.php"); 
        ?>
    </head>
    <body>
        <div class="container mx-auto">
        
            <?php require_once("banner.php"); ?>
            
            <div>
                <p>
                    <strong>Tchoukball Promotion<br/>3 Av. Edmond Vaucher<br>1219 Châtelaine, Switzerland</strong>
                </p>
                <p>
                    <strong>Tchoukball Promotion Eurl<br>606 chemin des Hautins<br>01280 Prévessin Moens<br>France</strong>
                </p>
                <p>
                    <strong>T. +41 (0) 22 368 00 41<br>F. +41 (0) 22 368 00 28<br>E. <a href="mailto:info@tchouk.com">info@tchouk.com</a></strong>
                </p>
                <p>
                    <strong>Tchoukball Promotion sell their products mostly in Europe. All information and communication material can be delivered worldwide.</strong>
                </p>
            </div>
    
            <?php require_once("footer.php"); ?>
            
        </div>
    
        <?php require_once("finish.php"); ?>
        
    </body>
</html>