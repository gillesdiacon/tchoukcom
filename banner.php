            <div id="header" class="masthead mb-3 row row-eq-height no-gutters">
                <div class="col-1"></div>
                <div id="bannerLogo" class="text-left col-md-auto pt-3">
                    <a href="http://tchouk.com/index.php" title="Tchoukball Promotion">
                        <img src="images/logo_<?php echo $lang; ?>.png" alt="Tchoukball Promotion">
                    </a>
                </div>
                <div id="globalMenu" class="col d-flex align-items-end pl-5">
                    <ul>
                        <li class="active last">
                            <a class="row row-eq-height no-gutters" href="index.php<?php if($lang!="fr"){echo"?lang=".$lang;}?>">
                                <div class="col-md-auto">
                                    <img src="images/pageTitle_left_act.png" />
                                </div>
                                <div class="col-md-auto pageTitleCenterAct font-italic text-white d-flex align-items-end pr-2">
                                    <strong>Shop</strong>
                                </div>
                                <div class="col-md-auto">
                                    <img src="images/pageTitle_right_act.png" />
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
                <div id="bannerText" class="text-right col-md-auto">
                    <img src="images/bannerText_<?php echo $lang; ?>.png" alt="Faisons tchouker le monde!" title="Faisons tchouker le monde!">
                </div>
                <div id="languageMenu" class="col-md-auto pr-3">
                    <ul>
                        <li class="<?php if($lang=="fr"){echo 'active';} ?>">
                            <a class="rounded px-1" href="<?php echo changeParam("lang", "fr") ?>" title="Afficher cette page en français" xml:lang="fr" lang="fr">FR</a>
                        </li>
                        <li class="<?php if($lang=="de"){echo 'active';} ?>">
                            <a class="rounded px-1" href="<?php echo changeParam("lang", "de") ?>" title="Show this page in German" xml:lang="de" lang="de">DE</a>
                        </li>
                        <li class="<?php if($lang=="en"){echo 'active';} ?>">
                            <a class="rounded px-1" href="<?php echo changeParam("lang", "en") ?>" title="Show this page in English" xml:lang="en" lang="en">EN</a>
                        </li>
                        <li class="<?php if($lang=="it"){echo 'active';} ?>">
                            <a class="rounded px-1" href="<?php echo changeParam("lang", "it") ?>" title="Show this page in Italian" xml:lang="it" lang="it">IT</a>
                        </li>
                    </ul>
                </div>
            </div>