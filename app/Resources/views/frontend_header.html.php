            <div class="left">
              <a href="/" class="logo"><span class="hidden">UneMeilleureOffre.com</span></a>
             <div class="slogan">L'endroit pour <h1>acheter moins cher</h1></div>
            </div>
            <div class="banner">
              <p><a href="#">Importez vos produits depuis Prestashop</a> et répondez aux <span class="distinct">annonces des utilisateurs UneMeilleureOffre</span></p>
              <a href="#" class="right">En savoir plus</a>
            </div><!-- banner-->
<?php if($isConnected) { ?>
            <div class="logged">
              <div class="login"><a href="#"><?php echo $user->getUsername();?></a></div>
              <div id="userMenuBox" class="menu"><p><a href="#" class="showUserMenu">Mon compte</a><span class="showUserMenu"></span></p></div>
              <div class="links">
                <a href="<?php echo $view['router']->generate('adsAdd', array());?>">J'ajoute une annonce</a>
                <a href="<?php echo $view['router']->generate('offersAdd', array());?>" class="last">J'ajoute une offre</a>
              </div><!-- links-->
            </div><!-- logged-->
            <div id="userMenu" class="hidden userMenu">
              <ul class="menu">
                <li><a href="#" rel="#addressMenu" class="showUserSubmenu">Adresses<span></span></a>
                  <ul id="addressMenu" class="hidden">
                    <li><a href="<?php echo $view['router']->generate('addressAd');?>">ajouter une adresse</a></li>
                    <li class="last"><a href="<?php echo $view['router']->generate('addressesList', array('column' => 'intitule', 'how' => 'asc'));?>">mes adresses</a></li>
                  </ul>
                </li>
                <li><a href="#" rel="#alertsMenu" class="showUserSubmenu">Alertes<span></span></a>
                  <ul id="alertsMenu" class="hidden">
                    <li class="last"><a href="<?php echo $view['router']->generate('alertsList', array('type' => 'annonces'));?>">mes alertes</a></li>
                  </ul>
                </li>
                <li><a href="#" rel="#adsMenu" class="showUserSubmenu">Annonces<span></span></a>
                  <ul id="adsMenu" class="hidden">
                    <li><a href="<?php echo $view['router']->generate('adsAdd', array());?>">ajouter une annonce</a></li>
                    <li><a href="<?php echo $view['router']->generate('adsMyList', array());?>">mes annonces</a></li>
                    <li class="last"><a href="<?php echo $view['router']->generate('adsShowOffersList');?>">offres pour mes annonces</a></li>
                  </ul>
                </li>
                <li><a href="#" rel="#adsQuesMenu" class="showUserSubmenu">Annonces - questions<span></span></a>
                  <ul id="adsQuesMenu" class="hidden">
                    <li class="last"><a href="<?php echo $view['router']->generate('adsQuestionList', array());?>">mes questions</a></li>
                  </ul>
                </li>
                <li><a href="#" rel="#adsRespMenu" class="showUserSubmenu">Annonces - réponses<span></span></a>
                  <ul id="adsRespMenu" class="hidden">
                    <li class="last"><a href="<?php echo $view['router']->generate('repliesList', array());?>">mes réponses</a></li>
                  </ul>
                </li>
                <li><a href="#" rel="#catMenu" class="showUserSubmenu">Catalogues<span></span></a>
                  <ul id="catMenu" class="hidden">
                    <li><a href="<?php echo $view['router']->generate('catalogueAdd', array());?>">ajouter un catalogue</a></li>
                    <li class="last"><a href="<?php echo $view['router']->generate('catalogueMyList', array());?>">mes catalogues</a></li>
                  </ul>
                </li>
                <li><a href="#" rel="#commMenu" class="showUserSubmenu">Commandes<span></span></a>
                  <ul id="commMenu" class="hidden">
                    <li><a href="<?php echo $view['router']->generate('opinionsList', array('type' => 'recus'));?>">commentaires reçus et donnés</a></li>					
                    <li class="last"><a href="<?php echo $view['router']->generate('ordersList');?>">mes commandes</a></li>					
                  </ul>
                </li>
                <li><a href="#" rel="#comMenu" class="showUserSubmenu">Compte<span></span></a>
                  <ul id="comMenu" class="hidden">
                    <li><a href="<?php echo $view['router']->generate('accountCard', array());?>">changer la carte de visite</a></li>
                    <li><a href="<?php echo $view['router']->generate('accountEmail', array());?>">changer l'e-mail</a></li>
                    <li class="last"><a href="<?php echo $view['router']->generate('accountPassword', array());?>">changer le mot de passe</a></li> 
                  </ul>
                </li>
                <li><a href="#" rel="#conMenu" class="showUserSubmenu">Contacts<span></span></a>
                  <ul id="conMenu" class="hidden">
                    <li class="last"><a href="<?php echo $view['router']->generate('contactsList', array());?>">mes contacts</a></li>
                  </ul>
                </li>
                <li><a href="#" rel="#ebayMenu" class="showUserSubmenu">eBay<span></span></a>
                  <ul id="ebayMenu" class="hidden">
                    <li class="last"><a href="<?php echo $view['router']->generate('synchronizeEbay');?>">synchroniser</a></li>
                  </ul>
                </li>
                <li><a href="#" rel="#msgMenu" class="showUserSubmenu">Messages<span></span></a>
                  <ul id="msgMenu" class="hidden">
                    <li><a href="<?php echo $view['router']->generate('messagesList', array());?>">mes messages</a></li>
                    <li class="last"><a href="<?php echo $view['router']->generate('messageWrite', array('id' => 0));?>">écrire un message</a></li>
                  </ul>
                </li>
                <li><a href="#" rel="#offersMenu" class="showUserSubmenu">Offres<span></span></a>
                  <ul id="offersMenu" class="hidden">
                    <li><a href="<?php echo $view['router']->generate('offersAdd', array());?>">ajouter une offre</a></li>
                    <li><a href="<?php echo $view['router']->generate('offersMyList', array());?>">mes offres</a></li>
                    <li><a href="<?php echo $view['router']->generate('offersInAds');?>">mes offres dans les annonces</a></li>
                    <li class="last"><a href="<?php echo $view['router']->generate('offerPropositions');?>">propositions d'achat</a></li>
                  </ul>
                </li>
                <li><a href="#" rel="#offersImgMenu" class="showUserSubmenu">Offres - images<span></span></a>
                  <ul id="offersImgMenu" class="hidden">
                    <li><a href="<?php echo $view['router']->generate('offersImagesAdd', array('column' => 'offre', 'how' => 'asc', 'id' => 0));?>">ajouter l'image à une offre</a></li>
                    <li class="last"><a href="<?php echo $view['router']->generate('offersImagesList', array('id' => 0));?>">images de mes offres</a></li>
                  </ul>
                </li>
                <li class="last"><a href="#" rel="#prestashopMenu" class="showUserSubmenu">Prestashop<span></span></a>
                  <ul id="prestashopMenu" class="hidden last">
                    <li class="last"><a href="<?php echo $view['router']->generate('synchronizePrestashop');?>">synchroniser</a></li>
                  </ul>
                </li>
              </ul>
            </div><!-- userMenu-->
<?php } else { ?>
            <div class="connect">
              <p class="account">Nouveau sur UneMeilleureOffre ? <a href="<?php echo $view['router']->generate('register', array());?>">Je m'inscris</a></p>
              <a href="<?php echo $view['router']->generate('login', array());?>" class="submit login">Connexion</a>
              <p class="links">
                <a href="<?php echo $view['router']->generate('adsAdd', array());?>" class="add">J'ajoute une annonce</a>
                <a href="<?php echo $view['router']->generate('offersAdd', array());?>" class="add">J'ajoute une offre</a>
              </p><!-- links-->
            </div><!-- connect-->
<?php } ?>
            <form method="post" action="<?php echo $view['router']->generate('search', array());?>" id="searchForm" class="search">
              <fieldset>
                <div id="categoriesSearch" class="hidden categoriesList">
                  <ul>
<?php foreach($categories as $p => $parent) { ?>
  <?php foreach($parent['children'] as $c => $child) { ?>
                    <li><input type="checkbox" name="cat[]" value="<?php echo $child['id'];?>" id="cat<?php echo $child['id'];?>" /><label for="cat<?php echo $child['id'];?>"><?php echo $child['name'];?></label></li>
  <?php } ?>
<?php } ?>
                  </ul>
                </div><!-- categoriesList-->
                <input type="text" name="word" placeholder="Votre mot..." class="word" />
                <span id="categoriesLabel" class="imitSelect">toutes les catégories</span>
                <span id="categoriesManip" class="openCategories"></span>
                <span class="label">Chercher dans : </span>
                <input type="radio" name="placeSearch" id="placeAds" value="1" class="checkbox" />
                <label for="placeAds">annonces</label>
                <input type="radio" name="placeSearch" id="placeOffers" value="2" class="checkbox" />
                <label for="placeOffers">offres</label>
                <input type="hidden" name="all" id="allSearch" value="1" /><input type="submit" name="search" id="search" class="submit submitSearch" value="Rechercher" />
              </fieldset>
            </form><!-- form.search -->