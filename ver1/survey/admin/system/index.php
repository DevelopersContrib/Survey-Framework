<?php if(!defined("APP")) die()?>
<div class="row stats">
  <div class="col-md-2">
    <div class="panel panel-default">
      <div class="panel-body">
        <span><?php echo $this->db->count("poll") ?></span>
        Polls
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="panel panel-default">
      <div class="panel-body">
        <span><?php echo $this->db->count("vote") ?></span>
        Votes
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="panel panel-default">
      <div class="panel-body">
        <span><?php echo $this->db->count("user") ?></span>
        Users
      </div>
    </div>
  </div>
  <div class="col-md-2">
    <div class="panel panel-default">
      <div class="panel-body">
        <span><?php echo $this->db->count("user","membership='pro'") ?></span>
        Pro Users
      </div>
    </div>
  </div>   
  <div class="col-md-2">
    <div class="panel panel-default">
      <div class="panel-body">
        <span><?php echo Main::currency($this->config["currency"],$this->db->count("payment","(MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE())) AND status='Completed'","amount")) ?></span>
        This Month Revenue
      </div>
    </div>
  </div>     
  <div class="col-md-2">
    <div class="panel panel-default">
      <div class="panel-body">
        <span><?php echo Main::currency($this->config["currency"],$this->db->count("payment","status='Completed'","amount")) ?></span>
        Total Revenue
      </div>
    </div>
  </div>            
</div>
<div class="row">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Polls Chart</div>      
      <div class="panel-body">
        <div id="poll-chart" class='chart'></div>
      </div>
    </div>    
  </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">User Chart</div>      
      <div class="panel-body">
        <div id="user-chart" class='chart'></div>
      </div>
    </div>      
  </div>
</div>
<div class="panel panel-default panel-dark">
  <div class="panel-heading">Country Analysis</div>      
  <div class="panel-body">
    <div class="col-md-6">
      <div id="country-map" class='chart'></div>
    </div>
    <div class="col-md-6">
      <ol>
      <?php foreach ($topcountries as $country => $count):?>
        <li><?php echo $country ?> <span class="label label-primary pull-right"><?php echo $count ?></span></li>
      <?php endforeach ?>
      </ol>
    </div>     
  </div>  
</div>
<div class="row">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Latest Polls</div>      
      <div class="panel-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Question</th>
              <th>Options</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($polls as $poll): ?>
              <tr>
                <td><?php echo $poll->question ?></td>
                <td>
                  <a href="<?php echo Main::href($poll->uniqueid) ?>" class="btn btn-xs btn-success">View</a>
                  <a href="<?php echo Main::ahref("polls/edit/{$poll->id}") ?>" class="btn btn-xs btn-primary">Edit</a>
                  <a href="<?php echo Main::ahref("polls/delete/{$poll->id}") ?>" class="btn btn-xs btn-danger delete">Delete</a>
                </td>
              </tr>                  
            <?php endforeach ?>
          </tbody>
        </table>        
      </div>
    </div>
  </div>  
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Latest Users</div>      
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Email</th>
                <th>Membership Type</th>                
                <th>Registration Date</th>
                <th>Expiry Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $user): ?>
              <?php if($this->config["demo"]) $user->email="Hidden in demo to protect privacy"; ?>
                <tr>
                  <td><?php echo $user->email ?></td>
                  <td><?php echo ucfirst($user->membership) ?></td>                  
                  <td><?php echo date("M d, Y",strtotime($user->date)) ?></td>
                  <td><?php echo ($user->membership=="free"?"Never":date("M d, Y",strtotime($user->expires))) ?></td>
                </tr>                  
              <?php endforeach ?>     
            </tbody>
          </table>
        </div>         
      </div>
    </div>
  </div> 
</div>
<div class="panel panel-default">
  <div class="panel-heading">Latest Payments</div>      
  <div class="panel-body">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Transaction ID</th>
            <th>Paypal Transaction ID</th>
            <th>User</th>
            <th>Date</th>
            <th>Expiry Date</th>
            <th>Amount</th>
            <th>Method</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($payments as $payment): ?>
            <tr>
              <td><?php echo $payment->id ?></td>
              <td><?php echo $payment->tid ?></td>
              <td><?php echo $payment->userid ?></td>
              <td><?php echo date("M d, Y - H:i",strtotime($payment->date)) ?></td>
              <td><?php echo date("M d, Y - H:i",strtotime($payment->expires)) ?></td>
              <td><?php echo $payment->amount ?></td>
              <td><?php echo ucfirst($payment->method) ?></td>
            </tr>                  
          <?php endforeach ?>     
        </tbody>
      </table>
    </div>    
  </div>
</div>