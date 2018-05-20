<?php

require_once('includes/config.php');
require_once('includes/class/main.class.php');
require_once('includes/templates/header.php');

$main = New Main();

?>
<div class="container-fluid content">
  <div class="table-responsive">
  <table id="coins-info-table" class="display coins-table dataTable table table-striped">
      <thead>
          <tr role="row">
              <th class="sorting" tabindex="0" aria-controls="coins-info-table">Coin Name</th>
              <th class="sorting" tabindex="0" aria-controls="coins-info-table">Market Cap</th>
              <th class="sorting" tabindex="0" aria-controls="coins-info-table">Price</th>
              <th class="sorting" tabindex="0" aria-controls="coins-info-table">Volume (24hr)</th>
              <th class="sorting" tabindex="0" aria-controls="coins-info-table">Supply</th>
              <th class="sorting" tabindex="0" aria-controls="coins-info-table">Change (24hr)</th>
              <th class="sorting" tabindex="0" aria-controls="coins-info-table">Actions</th>
          </tr>
      </thead>
        <tbody>
        </div>
      </div>
    </div>
<?php

$data = json_decode($main->jsonCache('300'), 2);


foreach($data as $coin) {
  echo '<tr id="BTC_' . $coin['short'] . '" data-name="' . $coin['long'] . '" role="row" class="odd">
  <td><span class="sprite sprite-' .  str_replace(' ', '', strtolower($coin['long'])) . ' small_coin_logo"></span> <strong><a href="' . 'coin/' . $coin['short'] . '">' . $coin['long'] . '</a></strong></td>
  <td class="market_capital">$' . number_format($coin['mktcap']) . '</td>
  <td class="increment price" data-usd="' . number_format($coin['price'], 4) . '">$' . number_format($coin['price'], 4) . '</td>
  <td class="volume">$' . number_format($coin['usdVolume']) . '</td>
  <td class="supply">' . $coin['supply'] . '</td>
  <td class="increment change">' . $coin['cap24hrChange'] . '%</td>
  <td class="actions"><a href="' . 'coin/' . $coin['short'] . '" class="btn btn-xs btn-default">Coin Info</a></td>
</tr>';
} ?>
</tbody>
    </table>
  </div>
</div>

<?php
require_once('includes/templates/footer.php');
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" language="javascript" src="assets/js/main.js"></script>
