<?php
require_once('../xsert/connect.php');
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');
check_sess();
error_reporting(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Groups Report</title>
    <?php include('../data_files/link_docs.php'); ?>
</head>
<body>
<?php
$limit = 40;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$search = isset($_POST['group_search']) ? trim($_POST['group_search']) : (isset($_GET['group_search']) ? trim($_GET['group_search']) : '');

$where = '';
if ($search !== '') {
        $where = "WHERE group_name LIKE '%" . mysqli_real_escape_string($connect, $search) . "%' ";
}

$qry_total = "SELECT * FROM groups $where";
$qry = $qry_total . " ORDER BY date_created DESC LIMIT $start, $limit";
$result_total = mysqli_query($connect, $qry_total);
$total_pages = mysqli_num_rows($result_total);
$result = mysqli_query($connect, $qry);
$lastpage = ceil($total_pages/$limit);
if ($page == 0) $page = 1;
$prev = $page - 1;
$next = $page + 1;
$target_page = "groups_report.php";
?>
<div class="main_bd_wrap">
    <?php tp_hdr(); side_menu_content(); ?>
    <div class="main-sidebar col-lg-9">
        <div class="report_wrap">
            <div class="form_header">Groups Report<?php echo $search ? ' - '.htmlspecialchars($search) : '' ?></div>
            <div style="text-align: right; margin-bottom: 10px;">
                <form name="form1" method="post" action="groups_report.php" id="form1" style="display:flex;align-items:center;gap:12px;justify-content:flex-end;margin-bottom:8px;">
                    <input type="text" class="search_text" name="group_search" placeholder="Search Group Name" value="<?php echo htmlspecialchars($search) ?>" style="height:38px;min-width:220px;" />
                    <input type="submit" name="search" value="Search" class="button_search" style="height:38px;min-width:80px;" />
                    <span style="padding:5px;font-size:13px;">Page <?php echo $page ?> <b>of</b> <?php echo $lastpage ?></span>
                    <span style="background:#ddd;padding:5px 10px;border-radius:5px;margin-left:6px;font-size:12px;"><?php if($next<=$lastpage) echo "<a href='$target_page?page=$next&group_search=$search'>Next</a>"; ?></span>
                    <?php if($page!=1) { $prev = $page - 1; ?>
                    <span style="background:#ddd;padding:5px 10px;border-radius:5px;margin-left:6px;font-size:12px;"><?php echo "<a href='$target_page?page=$prev&group_search=$search'>Back</a>"; ?></span>
                    <?php } ?>
                    <span style="background:#ddd;padding:5px 10px;border-radius:5px;margin-left:6px;font-size:12px;"><?php echo "<a href='$target_page?page=1&group_search=$search'>View All</a>"; ?></span>
                </form>
            </div>
            <table align="center" cellpadding="5" cellspacing="0" class="report_display" width="100%">
                <tr>
                    <td>#</td>
                    <td>Group Type</td>
                    <td>Group Name</td>
                    <td>Date Created</td>
                    <td>Village</td>
                    <td>Meeting Day</td>
                    <td>Contact Number</td>
                    <td>Contact Person</td>
                    <td>Action</td>
                </tr>
                <?php
                if(mysqli_num_rows($result)){
                    $count = $start;
                    while($row = mysqli_fetch_assoc($result)){
                        echo '<tr>';
                        echo '<td>' . (++$count) . '</td>';
                        echo '<td>' . htmlspecialchars($row['group_type']) . '</td>';
                        echo '<td><a href="group_details_report.php?group_id=' . htmlspecialchars($row['id']) . '" style="color:#145FA7;font-weight:bold;">' . htmlspecialchars($row['group_name']) . '</a></td>';
                        echo '<td>' . htmlspecialchars($row['date_created']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['village']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['meeting_day']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['contact_number']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['contact_person']) . '</td>';
                        echo '<td id="row">';
                        echo '<select name="select_action" id="group_' . htmlspecialchars($row['id']) . '" class="text-input group-action-select" style="width:80px;">';
                        echo '<option value="">Action</option>';
                        echo '<option value="edit_' . htmlspecialchars($row['id']) . '">Edit</option>';
                        echo '<option value="delete_' . htmlspecialchars($row['id']) . '">Delete</option>';
                        echo '</select>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="9">No groups found.</td></tr>';
                }
                ?>
            </table>
            <!-- Edit Modal -->
            <div id="editGroupModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);z-index:1000;align-items:center;justify-content:center;">
                <div class="modal-content modal-small-size" style="background:#fff;padding:20px;max-width:400px;margin:100px auto;position:relative;">
                    <form id="editGroupForm">
                        <input type="hidden" name="edit_group_id" id="edit_group_id" />
                        <div class="form-group"><label>Group Type</label><input type="text" name="group_type" id="edit_group_type" class="text-input" required /></div>
                        <div class="form-group"><label>Group Name</label><input type="text" name="group_name" id="edit_group_name" class="text-input" required /></div>
                        <div class="form-group"><label>Date Created</label><input type="date" name="date_created" id="edit_date_created" class="text-input" required /></div>
                        <div class="form-group"><label>Village</label><input type="text" name="village" id="edit_village" class="text-input" required /></div>
                        <div class="form-group"><label>Meeting Day</label><input type="text" name="meeting_day" id="edit_meeting_day" class="text-input" required /></div>
                        <div class="form-group"><label>Contact Number</label><input type="text" name="contact_number" id="edit_contact_number" class="text-input" required /></div>
                        <div class="form-group"><label>Contact Person</label><input type="text" name="contact_person" id="edit_contact_person" class="text-input" required /></div>
                        <div style="text-align:right;margin-top:10px;">
                            <button type="submit" class="button-input">Save</button>
                            <button type="button" id="closeEditModal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.group-action-select').forEach(function(sel) {
                    sel.addEventListener('change', function() {
                        var val = this.value;
                        var select = val.split('_');
                        if(select[0] === 'edit') {
                            var id = select[1];
                            fetch('../data_files/group_action.php', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: 'get_group_id=' + encodeURIComponent(id)
                            })
                            .then(response => response.json())
                            .then(data => {
                                document.getElementById('edit_group_id').value = data.id;
                                document.getElementById('edit_group_type').value = data.group_type;
                                document.getElementById('edit_group_name').value = data.group_name;
                                document.getElementById('edit_date_created').value = data.date_created;
                                document.getElementById('edit_village').value = data.village;
                                document.getElementById('edit_meeting_day').value = data.meeting_day;
                                document.getElementById('edit_contact_number').value = data.contact_number;
                                document.getElementById('edit_contact_person').value = data.contact_person;
                                document.getElementById('editGroupModal').style.display = 'flex';
                            });
                        } else if(select[0] === 'delete') {
                            if(confirm('Are you sure you want to delete this group?')) {
                                var id = select[1];
                                fetch('../data_files/group_action.php', {
                                    method: 'POST',
                                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                    body: 'delete_group_id=' + encodeURIComponent(id)
                                })
                                .then(response => response.text())
                                .then(data => {
                                    if(data.trim() === 'success') {
                                        location.reload();
                                    } else {
                                        alert('Delete failed.');
                                    }
                                });
                            }
                        }
                        this.value = '';
                    });
                });
                // Close modal
                document.getElementById('closeEditModal').onclick = function() {
                    document.getElementById('editGroupModal').style.display = 'none';
                };
                // Edit form submit
                document.getElementById('editGroupForm').onsubmit = function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    fetch('../data_files/group_action.php', {
                        method: 'POST',
                        body: new URLSearchParams(formData)
                    })
                    .then(response => response.text())
                    .then(data => {
                        if(data.trim() === 'success') {
                            location.reload();
                        } else {
                            alert('Update failed.');
                        }
                    });
                };
            });
            </script>
        </div>
        <?php echo footer_sec(); ?>
    </div>
</div>
</body>
</html>
