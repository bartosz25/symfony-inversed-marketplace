<script type="text/javascript" src="/js/users/importPrestashopItem.js"></script>
<div id="errorImportPresta" class="hidden"><?php echo $view->render('::frontend_error_box.html.php', array('text' => "")); ?></div>
<form id="prestaForm" method="post" action="<?php echo $view['router']->generate('ajaxImportPrestaWindow');?>">
  <fieldset class="defaultForm">
    <div class="formLine twoBoxes">
      <div class="formBox">
        <label for="item">Id du produit</label>
      </div>
      <div class="formBox">
        <label for="store">Adresse de la boutique</label>
      </div>
    </div>
    <div class="formLine fieldLine twoBoxes">
      <div class="formBox noMarginLeft">
        <input type="text" name="item" id="item" class="text"/>
      </div>
      <div class="formBox">
        <input type="text" name="store" id="store" class="text" />
      </div>
    </div>
    <div class="formLine twoBoxes">
      <div class="formBox">
        <label for="key">Clé</label>
      </div>
    </div>
    <div class="formLine fieldLine twoBoxes">
      <div class="formBox noMarginLeft">
        <input type="text" name="key" id="key" class="text"/>
      </div>
    </div>
    <div class="formLine btnLine">
      <input type="submit" name="importPrestashop" id="importPrestashopBtn" value="Importer" class="button" />
      <img src="/images/loader_small.gif" id="loaderPrestashop" style="display:none;" />
    </div>
  </fieldset>
</form>