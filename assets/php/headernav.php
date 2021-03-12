<style>
.sidenav {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  background-color: #111;
  overflow-x: hidden;
  transition: 0.5s;
  padding-top: 60px;
}

.sidenav a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 25px;
  color: #818181;
  display: block;
  transition: 0.3s;
}

.sidenav a:hover {
  color: #f1f1f1;
}

.sidenav .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}

#title {
  transition: margin-left .5s;
  padding-left: 8px;
}

#title a {
  color: black;
  text-decoration: none;
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}
</style>

<div id="mySidenav" class="sidenav" style="width: 0px;">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="/new.php">New Game</a>
  <a href="/my-games.php">Continue Game</a>
  <a href="/previous-games.php">Previous Games</a>
  <a href="/online/search-games.php">Search Games</a>
  <a href="http://www.playcrumble.com/rules" target="_blank">How to Play</a>
  <a href="/contact.php">Contact</a>
</div>

<div id="title">
  <h1>
    <span style="font-size:30px;cursor:pointer" onclick="toggleNav()">&#9776;</span>
    &nbsp;&nbsp;<a href="/">Crumble</font></a>
  </h1>
</div>

<script>
  function toggleNav() {
    if(document.getElementById("mySidenav").style.width == "0px") {
      openNav()
    } else {
      closeNav()
    }
  }

function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
  document.getElementById("title").style.marginLeft = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  document.getElementById("title").style.marginLeft= "0";
  document.getElementById("main").style.marginLeft = "0";
}
</script>
