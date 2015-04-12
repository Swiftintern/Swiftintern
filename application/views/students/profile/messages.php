<?php require_once $dir_student.'requires/header.php';?>
<!-- the middle contents -->
<section class="container">
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="home">Home</a></li>
			<li><a href="student">Profile</a></li>
			<li class="active">Messages</li>
		</ol>
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
                    <hr>
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
                    <hr>
                </div>

            </div>

        </div>
    </div>
</section>
<script type="text/javascript">
var inbox    = 1;
var outbox   = 1;
var per_page = 5;
var validity = 1;
</script>
<?php require_once $dir_public.'requires/footer.php';?>