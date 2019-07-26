<?php

  class user {
      var $id = 0;
      var $user = "";
      var $name = "";
      var $email = "";
      var $god = 0;

      var $phone = "";
      var $fax = "";
      var $mobile = "";
      var $addr_1 = "";
      var $addr_2 = "";
      var $city = "";
      var $postcode = "";
      var $state = "";

      function user($login) {

          $res = @mysql_query("SELECT u.id AS id,u.user AS user,u.name AS name,u.email AS email,g.user AS god FROM users AS u LEFT JOIN gods AS g on u.id=g.user WHERE u.user='$login'");
          $row = @mysql_fetch_array($res);

          $this->id = $row['id'];
          $this->user = $row['user'];
          $this->name = $row['name'];
          $this->email = $row['email'];
          $this->god = $row['god'];

      }

      function load_address() {
          $res = @mysql_query("SELECT phone,fax,mobile,address_1,address_2,city,postcode,state FROM users WHERE id=$this->id");
          $row = @mysql_fetch_array($res);

      
      }
          
  }

  class document {
      var $id = 0;
      var $name = "";
      var $type = "";
      var $size = 0;
      var $author = NULL;
      var $maintainer = NULL;
      var $revision = 0;
      var $created = "";
      var $modified = "";
      var $info = "";
      var $keywords = NULL;
      var $level = "";

      function document($id) {
          $i = 0;

          if($id == 0)
            die("Document ID not set");

          $res = @mysql_query("SELECT d.id AS id,d.name AS name,d.type AS type,d.size AS size,u.user AS author,d.maintainer AS maintainer,d.revision AS revision,DATE_FORMAT(d.created, '%W, %d %M %Y, %H:%i:%S') AS created,DATE_FORMAT(d.modified, '%W, %d %M %Y, %H:%i:%S') AS modified,i.info AS info FROM documents AS d LEFT JOIN users AS u on u.id=d.author LEFT JOIN documents_info AS i ON d.id=i.id WHERE d.id=$id");
          $row = @mysql_fetch_array($res);
          $this->id = $row['id'];
          $this->name = $row['name'];
          $this->type = $row['type'];
          $this->size = $row['size'];
          $this->author = new user($row['author']);
          $this->maintainer = $row['maintainer'];
          $this->revision = $row['revision'];
          $this->created = $row['created'];
          $this->modified  = ($this->modified == 0) ? "" : $row['modified'];
          $this->info = $row['info'];

          $res = @mysql_query("SELECT keyword FROM documents_keywords WHERE id=$this->id");
          while( $row = @mysql_fetch_array($res) ) {
              $this->keywords[$i] = $row['keyword'];
              $i++;
          }
       }

       function print_keywords() {
           echo "<ul>\n";
           foreach($this->keywords as $kw)
               echo "<li>$kw\n";
           echo "</ul>\n";
       }

       function get_access($user_id) {
           if( may_god($user_id,$this->id) ) {
               $this->level = "G";
               return;
           }
           $res = @mysql_query("SELECT level FROM ACL WHERE document_id=$this->id AND user_id=$user_id");
           if( @mysql_num_rows($res) != 1)
               return;
           $row = @mysql_fetch_array($res);
           $this->level = $row['level'];
       }
    }
?>
