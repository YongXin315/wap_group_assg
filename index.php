<?php
require 'db.php';

$users = $db->users;

// Insert sample
$users->insertOne([
  'name' => 'root',
  'email' => 'waiyee@example.com'
]);

// Read
$result = $users->find();
foreach ($result as $doc) {
  echo $doc['name'] . " - " . $doc['email'] . "<br>";
}
