<script type="text/javascript" src="/js/users/importEbayItem.js"></script>
<div id="errorImportEbay" class="hidden"><?php echo $view->render('::frontend_error_box.html.php', array('text' => "")); ?></div>
<form id="ebayForm" method="post" action="<?php echo $view['router']->generate('ajaxImportEbayWindow');?>">
  <fieldset class="defaultForm">
    <div class="formLine twoBoxes">
      <div class="formBox">
        <label for="key">Id de l'enchère</label>
      </div>
    </div>
    <div class="formLine fieldLine twoBoxes">
      <div class="formBox noMarginLeft">
        <input type="text" name="bidId" id="bidId" class="text"/>
      </div>
    </div>
    <div class="formLine btnLine">
      <input type="submit" name="importEbay" id="importEbayBtn" value="Importer" class="button" />
      <img src="/images/loader_small.gif" id="loaderEbayImg" style="display:none;" />
    </div>
  </fieldset>
</form>