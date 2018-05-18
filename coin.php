<?php

require_once 'includes/config.php';
require_once 'includes/class/main.class.php';

$main = new Main();
$code = $_GET['code'];
$data = json_decode($main->jsonCache('300'), 2);

foreach ($data as $coin) {
    if ($coin['short'] == $code) {
        $coin_data = array(
            'name' => $coin['long'],
            'code' => $coin['short'],
            'market_cap' => $coin['mktcap'],
            'price_usd' => $coin['price'],
            'volume' => $coin['volume'],
            'supply' => $coin['supply'],
            '24hr_change' => $coin['cap24hrChange'],
        );
    }
}

if (!isset($coin_data['name'])) {
    header("Location: " . $site_config['website_url']);
    die();
}

$page_var['title'] = $coin_data['name'] . ' (' . $coin_data['code'] . ') - Live Cryptocurrency Price & Market Data';
$page_var['description'] = 'Live cryptocurrency prices for ' . $coin_data['name'] . ', view live market data, prices, advanced information and use ' . $coin_data['name'] . ' tools.';
$page_var['img'] = 'img.png';

require_once 'includes/templates/header.php';
?>
<div class="container content">
<div class="row">
<div class="col-md-4">
<h2><span class="sprite sprite-<?php echo str_replace(' ', '', strtolower($coin_data['name'])); ?> small_coin_logo"></span> <?php echo $coin_data['name']; ?> <small>(<?php echo $coin_data['code']; ?>)</small></h2>
<p class="text-muted"><?php echo number_format($coin_data['supply']); ?> Circulating <?php echo $coin_data['code']; ?> Supply</p>
</div>
<div class="col-md-8">
<div class="row">

<div class="col-md-4 col-sm-4 col-xs-12">
<div class="well"><h4><span id="price">$<?php echo number_format($coin_data['price_usd'], 2); ?></span> (<span id="change"><?php echo number_format($coin_data['24hr_change'], 2); ?></span>%)</h4><p>Current Value</p></div>
</div>
<div class="col-md-4 col-sm-4 col-xs-12">
<div class="well"><h4><span id="cap">$<?php echo number_format($coin_data['market_cap'], 2); ?></span></h4><p>Market Cap</p></div>
</div>
<div class="col-md-4 col-sm-4 col-xs-12">
<div class="well"><h4><span id="volume">$<?php echo number_format($coin_data['volume'], 2); ?></span></h4><p>24hr Volume</p></div>
</div>
</div>
</div>
</div>
</div>
<div class="container content chart">
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto;"></div>
</div>
<div class="container content">
  <h2 class="coin-header">Live Update Feed</h2>
  <div class="table-responsive">
  <table id="txfeed" class="table table-striped">
   <thead>
      <tr>
         <th>Coin</th>
         <th>Price</th>
         <th>Market Cap</th>
         <th>Volume</th>
         <th>Change</th>
      </tr>
   </thead>
   <tbody>
   </tbody>
</table>
</div>
</div>
<?php
require_once 'includes/templates/footer.php';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="assets/js/bootstrap.min.js"></script>


<script>
$.getJSON('https://coincap.io/history/<?php echo $coin_data['code']; ?>', function (data) {

    Highcharts.chart('container', {
        chart: {
            zoomType: 'x',
            backgroundColor: null
        },
        title: {
            text: null
        },
        subtitle: {
            text: null
        },
        xAxis: {
            type: 'datetime'
        },
        yAxis: {
            title: {
                text: null
            }
        },
        legend: {
            enabled: false,
        },
        plotOptions: {
            area: {
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        exporting: { enabled: false },

        series: [{
            type: 'area',
            name: '<?php echo $coin_data['code']; ?> value in USD',
            data: data.price,
            color: '#01ACFF'
        }]});
});

var formatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'USD',
  minimumFractionDigits: 2,
});

var socket = io.connect('https://socket.coincap.io');

function pushTx(coindata) {
  var txs = $('#txfeed .tx');
  var txcount = txs.length;
  if (txcount == 10) {
    txs.last().remove();
  }
  $('table#txfeed').prepend('<tr class="tx"><td><?php echo $coin_data['name']; ?> (<?php echo $coin_data['code']; ?>)</td><td><span class="label label-success">' + formatter.format(coindata.price) + '</span></td><td>' + formatter.format(coindata.mktcap) + '</td><td>' + formatter.format(coindata.volume) + '</td><td>' + coindata.cap24hrChange + '%</td></tr>').fadeIn();
}

socket.on('trades', function (data) {
    var coin = data.coin;
    var coindata = data.msg;
    if (coin == "<?php echo $coin_data['code']; ?>") {
      console.log(coindata);
      pushTx(coindata);
        document.getElementById("price").textContent = formatter.format(coindata.price);
        document.getElementById("cap").textContent = formatter.format(coindata.mktcap);
        document.getElementById("volume").textContent =  formatter.format(coindata.volume);
        document.getElementById("change").textContent =  coindata.cap24hrChange;
    }
});
</script>
