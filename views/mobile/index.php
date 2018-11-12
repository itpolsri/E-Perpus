<!DOCTYPE html>
<?php include_once "include/head.php"?>
<body> 
  <ons-page>

    <ons-tabbar position="auto">
      <ons-tab id="dashboard" page="dashboard.html" label="Home" icon="ion-ios-home-outline" active-icon="ion-ios-home" active></ons-tab>
      <ons-tab id="profile" page="profile.html" label="Profile" icon="ion-ios-contact-outline" active-icon="ion-ios-contact"></ons-tab>
      <ons-tab id="setting" page="about" label="About" icon="ion-ios-people-outline" active-icon="ion-ios-people"  ></ons-tab>
    </ons-tabbar>
    
  </ons-page>

  <?php include_once "include/home.php" ?>

  <ons-template id="profile.html">
    <ons-page id="profs">
      <ons-toolbar>
        <div class="center">Profile</div>
      </ons-toolbar>
      <ons-card>
        <ons-icon icon="md-spinner" size="32px" spin></ons-icon>
      </ons-card>
    </ons-page>
  </ons-template>

  <?php include_once "include/about.php" ?>

  

<script src="views/mobile/assets/js/app.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
<script>
  if ('serviceWorker' in navigator) {
    console.log("Will the service worker register?");
    navigator.serviceWorker.register('views/mobile/service-worker.js')
      .then(function(reg){
        console.log("Yes, it did.");
      }).catch(function(err) {
        console.log("No it didn't. This happened: ", err)
      });
  }

</script>

</html>