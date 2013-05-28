              <div class="saveBox noMarginTop">
                <div class="header">
                  <span><?php echo $amounts[0];?></span>
                  <span><?php echo $amounts[1];?></span>
                  <span><?php echo $amounts[2];?></span>
                  <span><?php echo $amounts[3];?></span>
                  <span><?php echo $amounts[4];?></span>
                  <span><?php echo $amounts[5];?></span>
                  <span class="last"><?php echo $amounts[6];?></span>
                </div>
                <div class="text">
                  <p>Economisés par les <span>utilisateurs d'UneMeilleureOffre</span></p>
                  <p>
                    <a href="<?php echo $view['router']->generate("bestOffers", array());?>" class="btn"><span>Les meilleures ventes</span></a>
                    <a href="<?php echo $view['router']->generate("bestAds", array());?>" class="btn"><span>Les meilleurs achats</span></a>
                  </p>
                </div>
              </div><!-- saveBox-->