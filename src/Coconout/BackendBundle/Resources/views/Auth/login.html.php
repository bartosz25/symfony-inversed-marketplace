<?php $view->extend('::backend_base.html.php') ?>

<h1>Login</h1>
<form action="<?php echo $view['router']->generate('loginDo', array()); ?>" method="post">
  <div><label for="username">Login</label><input type="text" name="username" id="username" /></div>
  <div><label for="password">Mot de passe</label><input type="password" name="password" id="password" /></div>
  <input type="submit" />
</form> 