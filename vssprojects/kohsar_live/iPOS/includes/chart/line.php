<script type="text/javascript" src="../includes/js/jquery.js"></script>
<!-- 1. Add these JavaScript inclusions in the head of your page -->
<script type="text/javascript" src="../includes/js/highcharts.js"></script>
<!-- 1a) Optional: the exporting module -->
<script type="text/javascript" src="../includes/js/modules/exporting.js"></script>
<!-- 2. Add the JavaScript to initialize the chart on document ready -->
<script type="text/javascript">
var chart;
$(document).ready(function() {
    chart = new Highcharts.Chart({
        chart: {
            renderTo: 'container',
            defaultSeriesType: 'line',
            marginRight: 130,
            marginBottom: 25
        },
        title: {
            text: '<?php echo $title;?>',
            x: -20 //center
        },
        subtitle: {
            text: '<?php echo $subtitle;?>',
            x: -20
        },
        xAxis: {
            categories: <?php echo $xaxis;?>
        },
        yAxis: {
            title: {
                text: '<?php echo $yaxislabel;?>'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                    this.x +': <?php echo $yaxisparam;?>. '+ this.y;
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: -10,
            y: 100,
            borderWidth: 0
        },
        series: [
				 	<?php echo $valstring;?>
        		]
    });
});
</script>