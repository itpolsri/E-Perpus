<?php use Package\App\Session; ?>
<div class="navbar-fixed">
  <nav id="nav" class="deep-purple darken-3 z-depth-2" role="navigation">
    <div class="nav-wrapper  background--mind_2" style="padding: 0 15px">
      <a href="<?= baseurl() ?>/" class="brand-logo">Peirtual</a>
      <ul class="right hide-on-med-and-down">
        <?php if (!Session::get('userlogin')): ?>
          <li><a href="<?= baseurl() ?>/login">Login</a></li>
          <li><a href="<?= baseurl() ?>/register">Register</a></li>
        <?php else: ?>
          <li><a href="<?= baseurl() ?>/home">Home</a></li>
          <li><a href="<?= baseurl().'/users/'.Session::get('username') ?>"><?= substr(Session::get('usernama'), 0, strpos(Session::get('usernama'), " ")) ?></a></li>
          <li><a style="display: inline" class="nav-user-avatar sidenav-trigger" href='#' data-target='side-nav'><img class="circle" src="<?= Session::get('useravatar') ?>" /></a></li>
        <?php endif; ?>
      </ul>
      <a id="nav-trigger" href="#" data-target="side-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
    </div>
  </nav>
</div>


<ul id="side-nav" class="sidenav darken-3 background--mind_3">
  <?php if (!Session::get('userlogin')): ?>
    <li><a class="white-text" href="<?= baseurl() ?>/login">Login</a></li>
    <li><a class="white-text" href="<?= baseurl() ?>/register">Register</a></li>
  <?php else: ?>
    <li>
      <div class="user-view">
        <div class="background">
          <img src="https://materializecss.com/images/office.jpg">
        </div>
        <img class="circle" src="<?= Session::get('useravatar') ?>" />
        <span class="white-text name"><?= Session::get('usernama') ?></span>
        <span class="white-text email"><?= Session::get('useremail') ?></span>
      </div>
    </li>
    <li><a class="white-text" href="<?= baseurl() ?>/home"><i class="material-icons">home</i><b>Home</b></a></li>
    <?php if (Session::get('userauth')): ?>
      <li><a class="white-text" href="<?= baseurl().'/users/'.Session::get('username'); ?>"><i class="material-icons">account_circle</i><b>Profile</b></a></li>
      <li class="divider" tabindex="-1"></li>
    <?php endif; ?>
    <li><a class="white-text" href="<?= baseurl() ?>/logout"><i class="material-icons">power_settings_new</i><b>Logout</b></a></li>
  <?php endif; ?>
</ul>
