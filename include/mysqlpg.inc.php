<?php

require_once ($topdir."/config.php");

class mysqlpg extends mysql {

  function mysqlpg ($type = "ro") {

    $this->mysql(MYSQL_PETITGENI_USERNAME, MYSQL_PETITGENI_PASSWORD, MYSQL_PETITGENI_HOSTNAME, MYSQL_PETITGENI_DATABASE);

  }
}
?>
