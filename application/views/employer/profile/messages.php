<?php require_once $dir_employer.'requires/header.php';?>
<?php require_once $dir_employer.'modal/message.php';?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Messages</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
                <li class="active"><a href="#inbox" role="tab" data-toggle="tab">Inbox</a></li>
                <li><a href="#outbox" role="tab" data-toggle="tab">Outbox</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="inbox">
                    <div class="panel panel-default">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>From</th>
                                    <th>Message</th>
                                    <th>Received at</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="to_user_id">
							<?php
                                foreach ($inboxs as $inbox) {
                                    $sender = User::find_name($inbox->from_user_id);
                                    $inbox  = Message::find_by_id('id', $inbox->id);
                                    echo '<tr>
                                            <td>'.$sender->name.'</td>
                                            <td>'.$inbox->message.'</td>
                                            <td><small>'.datetime_to_text($inbox->created).'</small></td>
                                            <td>
                                                <button name="message" value="'.$sender->id.'" class="btn btn-warning btn-xs"><i class="fa fa-envelope fa-fw"></i> Message</button> 
                                                <button name="delete" value="'.$inbox->id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash fa-fw"></i> Delete</button> 
                                            </td>
                                        </tr>';
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center"><button id="loadmorebtn_to_user_id" onclick="loadmoreMessages(++inbox, 'to_user_id', '<?php echo $session->user_id;?>')" class="btn btn-primary">Load More</button></div>
                </div>

                <div class="tab-pane" id="outbox">
                    <div class="panel panel-default">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>To</th>
                                    <th>Message</th>
                                    <th>Sent at</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="from_user_id">
                            <?php
                                foreach ($outboxs as $outbox) {
                                    $receipient = User::find_name($outbox->to_user_id);
                                    $outbox     = Message::find_by_id('id', $outbox->id);
                                    echo '<tr>
                                            <td>'.$receipient->name.'</td>
                                            <td>'.$outbox->message.'</td>
                                            <td><small>'.datetime_to_text($outbox->created).'</small></td>
                                            <td>
                                                <button name="message" value="'.$receipient->id.'" class="btn btn-warning btn-xs"><i class="fa fa-envelope fa-fw"></i> Message</button> 
                                                <button name="delete" value="'.$outbox->id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash fa-fw"></i> Delete</button> 
                                            </td>
                                        </tr>';
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center"><button id="loadmorebtn_from_user_id" onclick="loadmoreMessages(++outbox, 'from_user_id', '<?php echo $session->user_id;?>')" class="btn btn-primary">Load More</button></div>
                </div>

            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $(document).on("click","button[name=delete]",function(e){
        var field       = 'validity';
        var message_id  = this.value;
        var value       = '0';
        var element     = this;
        updateMessage(field, value, message_id).success(function (data) {
            $(element).closest('tr').hide();
            bootbox.alert(data);
        })
    });
});

var inbox    = 1;
var outbox   = 1;
var per_page = 5;
var validity = 1;
</script>
<?php require_once $dir_employer.'requires/footer.php';?>