<?php
error_reporting(E_ALL);

class Book {

    // class variables
    public $data = [];
    public $con;
    public $fullList = [];
    public $scholars = [];

    function __construct()
    {
      $this->con = $this->dbConnect();

      // initialize data needed for application startup
      $this->initData();

      // Handles ajax requests specifically
      if(isset($_POST['ajax']) && $_POST['ajax'] == true)
      {
          if(!isset($_POST['method'])) return false;
          switch($_POST['method']){
            # This calls the checkout function
            case 'checkout':
              $params = array();
              parse_str($_POST['post'], $params);
              $this->checkout($params['book'], $params['scholar']);
              exit;
              break;

            # This calls the checkin function
            case 'checkin':
              $params = array();
              parse_str($_POST['post'], $params);
              $this->checkin($params['book'], $params['scholar']);
              exit;
              break;

            default:
              echo "This method doesn't exist";
              break;
          }
      }
    }

    private function initData()
    {
      // Set DB Connection
      $db = $this->con;

      // Gather / Set data needed for web application forms
      $query = $db->query('Select * from book
        inner join homeroom as hr on book.HRID = hr.HRID
        left join scholar on book.ScholarID = scholar.ScholarID
        order by book.BookName desc');

      // Create full list of books with checked out values
      while($r = $query->fetch(PDO::FETCH_OBJ)){

        // Build Array
        $this->fullList[$r->HRName][] = [
          'BookID' => $r->BookID,
          'BookName' => $r->BookName,
          'Avail' => ($r->Avail == 1) ? TRUE : FALSE,
          'LastOut' => $r->CheckoutTime,
          'Scholar' => ($r->Avail == 0) ? $r->Name : NULL
        ];
      }

      // Gather Data For scholars
      $query = $db->query("Select * from scholar order by Name desc");
      while($r = $query->fetch(PDO::FETCH_OBJ)){
        $this->scholars[$r->ScholarID] = $r->Name;
      }
    }

    private function checkout($bid, $sid)
    {
      // Set DB Connection
      $db = $this->con;

      // Check if the book is checked out by someone
      $query = $db->query("Select Avail from book where BookID=$bid and ScholarID IS NULL");
      $a = $query->fetch(PDO::FETCH_OBJ);
      if($a->Avail != 0){
        try {
          $q = $db->prepare("UPDATE book SET ScholarID=$sid, Avail=0, CheckoutTime=CURRENT_TIMESTAMP where BookID=".$bid);
          $q->execute();
          $msg = "Book was successfully checked out by you";

          // Build row for html table
          $query = $db->query('Select * from book
            inner join homeroom as hr on book.HRID = hr.HRID
            left join scholar on book.ScholarID = scholar.ScholarID
            where book.BookID = '.$bid.'
            limit 1');
          $b = $query->fetch(PDO::FETCH_OBJ);
          $html = "<tr id=''".$b->BookID."'><td>".$b->Name."</td><td>".$b->BookName."</td><td>".$b->HRName."</td><td>"
                  .date("m/d/Y g:ia", strtotime($b->CheckoutTime))."</td></tr>";
        } catch(PDOException $e){
          $msg =  $e->getMessage();
        }
      } else {
        $error = true;
        $msg = "Book is currently checked out.";
      }

      echo json_encode([
        'error' => isset($error) ? $error : false,
        'msg' => isset($msg) ? $msg : '',
        'bid' => $bid,
        'row' => $html
      ]);
      die();
    }

    // Checks Database if checkedout, and allows to checkIn (removes row from checkout)
    private function checkIn($bid, $sid){
      // Set DB Connection
      $db = $this->con;

      // Check if the book is checked out by someone
      $query = $db->query("Select Avail, ScholarID from book where BookID=$bid");
      $a = $query->fetch(PDO::FETCH_OBJ);

      if($a->Avail == 0){
        try {
          if($a->ScholarID == $sid){
            $q = $db->prepare("UPDATE book SET ScholarID=NULL, Avail=1, CheckoutTime=CURRENT_TIMESTAMP where BookID=".$bid);
            $q->execute();
            $msg = "Book was successfully checked in by you";
          } else {
            $msg = "Book is not checked out by you.";
            $error = "true";
          }
        } catch(PDOException $e){
          $msg =  $e->getMessage();
        }
      } else {

        $error = true;
        $msg = "Book is already available.";
      }

      echo json_encode([
        'error' => isset($error) ? $error : false,
        'msg' => isset($msg) ? $msg : '',
        'bid' => $bid
      ]);
      die();
    }

    public function dbConnect(){
      $dbname = 'republic';
      $host = 'mysql:host=localhost;dbname='.$dbname;
      $user = 'republic';
      $pass = 'republic';

      try {
        $con = new PDO($host, $user, $pass);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        echo $e->getMessage();
      }

      return $con;
    }
}

?>
