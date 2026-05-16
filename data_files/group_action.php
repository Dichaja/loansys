<?php
require_once('../xsert/connect.php');
//check_sess();

if(isset($_POST['edit_group_id'])) {
    $id = $_POST['edit_group_id'];
    $group_type = $_POST['group_type'];
    $group_name = $_POST['group_name'];
    $date_created = $_POST['date_created'];
    $village = $_POST['village'];
    $meeting_day = $_POST['meeting_day'];
    $contact_number = $_POST['contact_number'];
    $contact_person = $_POST['contact_person'];
    $sql = "UPDATE `groups` SET group_type=?, group_name=?, date_created=?, village=?, meeting_day=?, contact_number=?, contact_person=? WHERE id=?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('ssssssss', $group_type, $group_name, $date_created, $village, $meeting_day, $contact_number, $contact_person, $id);
    $stmt->execute();
    echo 'success';
    exit;
}

if(isset($_POST['delete_group_id'])) {
    $id = $_POST['delete_group_id'];
    $sql = "DELETE FROM `groups` WHERE id=?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    echo 'success';
    exit;
}

if(isset($_POST['get_group_id'])) {
    $id = $_POST['get_group_id'];
    $sql = "SELECT * FROM `groups` WHERE id=?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    echo json_encode($row);
    exit;
}
