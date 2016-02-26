<?php
error_reporting(E_ALL);

define('__ROOT__', dirname(dirname(__FILE__))."/republic");
if(file_exists(__ROOT__."/libraries.php")){
  require_once(__ROOT__."/libraries.php");
  $book = new Book;
}
else {
  echo "File doesn't exist";
}

?>

<!DOCTYPE html>
<html>
  <head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="all.css">
    <script type="text/javascript" src="all.js"></script>
  </head>

  <body>
    <div class="container">

      <div class="starter-template">
        <h1 align="center">Library Book Manager</h1>
        <div class="row col-sm-12">
          <div id="buttons" class="col-sm-5">
            <div>
              <a href="#" class="btn btn-primary btn-block" title="checkOutForm">Checkout A Book</a>
            </div>
            <div>
              <a href="#" class="btn btn-primary btn-block" title="checkInForm">Checkin A Book</a>
            </div>
            <div>
              <a href="#" class="btn btn-primary btn-block" title="checkedout">Books Currently Checked Out</a>
            </div>
          </div>
          <div id="contentForm" class="col-sm-7">
            <div id="checkedout" class="boxPanel" style="display:none;">
              <table>
                <thead>
                  <tr>
                    <th>Scholar Name</th>
                    <th>Book Name</th>
                    <th>Home Room</th>
                    <th>Checked-out Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  foreach($book->fullList as $hr => $row){
                    foreach($row as $r):
                      if($r['Avail'] == FALSE){
                        echo "<tr>";
                        echo "<td>".$r['Scholar']."</td>";
                        echo "<td>".$r['BookName']."</td>";
                        echo "<td>".$hr."</td>";
                        echo "<td>".date("m/d/Y g:ia", strtotime($r['LastOut']))."</td>";
                      }
                    endforeach;
                  }
                  ?>
                </tbody>
              </table>
            </div>

            <div id="checkOutForm" class="boxPanel">
              Select from a book that's available to checkout:<br/>
              <form id="form-checkout" method="POST">
                <label for="scholar">Scholar Name:</label>
                <select name="scholar" class="form-control">
                  <?php
                  foreach($book->scholars as $k => $r){
                    echo "<option value='".$k."'>".$r."</option>";
                  }
                  ?>
                </select>

                <label for="book">Book Name:</label>
                <select name="book" class="form-control">
                  <?php
                  foreach($book->fullList as $k => $row){
                    echo "<optgroup label='$k'>";
                    foreach($row as $r):
                      if($r['Avail'] == TRUE) echo "<option value='".$r['BookID']."'>".$r['BookName']."</option>";
                      else echo "<option value='".$r['BookID']."' disabled>".$r['BookName']."</option>";
                    endforeach;
                    echo "</optgroup>";
                  }
                  ?>
                </select>
                <input type="submit" value="Check-out Book" class="btn-info btn-sm" style="margin-top:10px;" />
              </form>
            </div>

            <div id="checkInForm" style="display:none;" class="boxPanel">
              Select your name, then a book that's available to checkin:<br/>
              <form id="form-checkin" method="POST">

                <label for="scholar">Scholar Name:</label>
                <select name="scholar" class="form-control">
                  <?php
                  foreach($book->scholars as $k => $r){
                    echo "<option value='".$k."'>".$r."</option>";
                  }
                  ?>
                </select>

                <label for="book">Book Name:</label>
                <select name="book" class="form-control">
                  <?php
                  foreach($book->fullList as $k => $row){
                    echo "<optgroup label='$k'>";
                    foreach($row as $r):
                      if($r['Avail'] == FALSE) echo "<option value='".$r['BookID']."'>".$r['BookName']."</option>";
                    endforeach;
                    echo "</optgroup>";
                  }
                  ?>
                </select>
                <input type="submit" value="Check-in Book" class="btn-info btn-sm" style="margin-top:10px;" />
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </body>
</html>
