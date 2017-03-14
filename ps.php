<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <script language="javascript" src="http://sol.synchrotron.org.au/js/jquery.js?20120216"></script>
    <script language="javascript" src="http://sol.synchrotron.org.au/js/jquery-ui-1.8.2.custom.min.js?20120216"></script>
	<script language="javascript" src="http://sol.synchrotron.org.au/js/highcharts/js/highcharts.js"></script>
    <script language="javascript" src="http://sol.synchrotron.org.au/js/highcharts/js/modules/exporting.js"></script>
	
	<script type="text/javascript">
	window.onload = function () {
		//default chart arrays
		var x = []; //array for length of booster cycle
		var y = [];//array for booster charge data
		var y2 = [];//array for booster magnet current
		
		//Populate both arrays
		for (var i = 0; i < 1000; i++) {		
				x.push(i);
				y.push(0);
				y2.push(0);
			}
		var elem = this;
		var pvs = ["BR01PSC01:CHANNEL_A_MONITOR","BR01PSC01:CHANNEL_B_MONITOR"];
		var buckets = 1000;
		
		var chart = new Highcharts.Chart({
			chart: {
				type: 'line',
				renderTo : 'myChart',
				backgroundColor: 'Black'
				},
				title: {
					text: 'Booster Picoscope'
					},
				xAxis: {
					categories: x,
					crosshair: true
				},
				yAxis: [{
					
					title: {
						text: 'Charge'
					}
				},{
					min:-.5,
					max:8,
					title:{
						text: 'Magnet'
					},
					opposite: true
					
				}
				
				],
				tooltip: {
					headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					'<td style="padding:0"><b>{point.y:.3f} </b></td></tr>',
					footerFormat: '</table>',
					shared: true,
					useHTML: true
					},
				plotOptions: {
					column: {
						pointPadding: 0.3,
						borderWidth: 0
					}
				},
				series: [{
						name: 'Charge',
						color: 'red',
						yAxis: 0,
						data: y
						},{
						name: 'Magnet',
						color: 'green',
						yAxis: 1,
						data: y2}]
			});

		//Get IP address from php and direct accordingly
		var ipadd = "<?php echo $_SERVER['REMOTE_ADDR']; ?>";
		if (ipadd.indexOf("10.17")!==-1){
			var ws = new WebSocket("ws://10.17.100.199:8888/monitor");
		}else{
			var ws = new WebSocket("ws://10.6.100.199:8888/monitor");
		}

		//Open WS connection
		ws.onopen = function() {
			for (var i = 0; i < pvs.length; i++) {		
				ws.send(pvs[i]);
			}
    		};
		//receive FPM data
		ws.onmessage = function(evt) {
      			var data = JSON.parse(evt.data);
      			if (data.msg_type === "monitor") {
					if (data.pvname === "BR01PSC01:CHANNEL_A_MONITOR"){
						chart.series[0].setData(data.value,true);
					}else if (data.pvname === "BR01PSC01:CHANNEL_B_MONITOR"){
						chart.series[1].setData(data.value,true);
					}
				}
				
		}

	}
	</script>
<link rel="stylesheet" type="text/css" href="./ps.css">
</head>
<title>Booster Picoscope</title>
<div id="body">
<body bgcolor="#000000">
	<! Last row for charts>
	<fieldset class="qtBorder"><legend class="qtLabel">Scope:</legend>
	<center><div id = "myChart"></div></center>

</body>
</div>
</html>