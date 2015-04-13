<?php require_once $dir_employer.'requires/header.php';?>
<?php require_once $dir_employer.'modal/message.php';?>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Members</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">

            <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
                <li class="active"><a href="#all_members" role="tab" data-toggle="tab">Members of <?php echo $company->name;?></a></li>
                <li><a href="#members_of" role="tab" data-toggle="tab"><?php echo $session->name;?> Managing</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="all_members">
                    <form class="form-inline" role="form" id="invite_member">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="sr-only">Email address</label>
                                <div class="input-group-addon">@</div>
                                <input type="email" name="email" class="form-control" placeholder="Enter email">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <label class="sr-only">Authority</label>
                                <select name="authority" class="form-control">
                                    <option value="editor">Editor</option>
                                    <option value="analyst">Analyst</option>
                                    <option value="selected">Selected</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="organization_id" value="<?php echo $company->id;?>">
                        <button type="submit" class="btn btn-primary">Invite</button>
                    </form>
                    <hr>
                    <div class="panel panel-default">
                        <div class="panel-body" id="result_status">
                            <p>Note : you can invite other members with different authority to manage your profile/organization.</p>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Joined</th>
                                    <th>Designation</th>
                                    <th>Authority</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="results">
                            <?php
                                foreach ($allmembers as $employer) {
                                    $member     = Member::find_by_id('id', $employer->id);
                                    $user       = User::find_name($member->user_id);
                                    $data     = '<tr>';
                                    if ($user) {
                                        $data     .= '<td>'.$user->name.'</td>';
                                        $data     .= '<td><small>'.datetime_to_text($member->created).'<small></td>';
                                        $data     .= '<td>'.$member->designation.'</td>';
                                        $data     .= '<td>'.ucfirst($member->authority).'</td>';

                                        if ($member->authority != 'admin') {
                                            $data .= '<td><button name="" value="" class="btn btn-danger btn-xs"><i class="fa fa-trash fa-fw"></i> Delete</button></td>';
                                        }else {
                                            $data .= '<td><button name="message" value="'.$user->id.'" class="btn btn-warning btn-xs"><i class="fa fa-envelope fa-fw"></i> Message</button></td>';
                                        }
                                    } else {
                                        $data = '<tr>
                                                <td>Invited</td>
                                                <td><small>'.datetime_to_text($member->created).'<small></td>
                                                <td>'.$member->designation.'</td>
                                                <td>'.ucfirst($member->authority).'</td>
                                                <td><button name="member" value="'.$member->id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash fa-fw"></i> Delete</button></td>
                                            </tr>';
                                    }
                                    echo $data;
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane" id="members_of">
                    <div class="panel panel-default">
                        <div class="panel-body" id="result_status">
                            <p>Note : you can leave any profile/organization.</p>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Organization</th>
                                    <th>Joined</th>
                                    <th>Designation</th>
                                    <th>Authority</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="results">
                            <?php
                                foreach ($membersof as $mem) {
									$mem 	= Member::find_by_id('id', $mem->id);
                                    $org 	= Organization::find_name($mem->organization_id);
									
                                    $data   = '<tr>';
									$data     .= '<td>'.$org->name.'</td>';
									$data     .= '<td><small>'.datetime_to_text($mem->created).'<small></td>';
									$data     .= '<td>'.$mem->designation.'</td>';
									$data     .= '<td>'.ucfirst($mem->authority).'</td>';
									$data .= '<td><button name="delete" value="member" id="'.$mem->id.'" class="btn btn-danger btn-xs"><i class="fa fa-sign-out"></i> Leave</button></td>';
                                    
									echo $data;
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php require_once $dir_employer.'requires/footer.php';?>