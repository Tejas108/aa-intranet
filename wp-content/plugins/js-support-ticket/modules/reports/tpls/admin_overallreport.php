<?php
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
?>
<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
<script type="text/javascript">
            jQuery(document).ready(function ($) {
    });
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawBarChart);
            function drawBarChart() {
            var data = google.visualization.arrayToDataTable([
                    ['<?php echo __('Status', 'js-support-ticket'); ?>', '<?php echo __('Tickets By Status', 'js-support-ticket'); ?>', { role: 'style' }],
<?php echo jssupportticket::$_data['bar_chart']; ?>
            ]);
                    var view = new google.visualization.DataView(data);
                    view.setColumns([0, 1,
                    { calc: "stringify",
                            sourceColumn: 1,
                            type: "string",
                            role: "annotation" },
                            2]);
                    var options = {
                    //title: "Density of Precious Metals, in g/cm^3",
                    width: '95%',
                            bar: {groupWidth: "95%"},
                            legend: { position: "none" },
                    };
                    var chart = new google.visualization.ColumnChart(document.getElementById("bar_chart"));
                    chart.draw(view, options);
            }

    google.setOnLoadCallback(drawStackChart);
            function drawStackChart() {
            var data = google.visualization.arrayToDataTable([
                    ['<?php echo __('Tickets', 'js-support-ticket'); ?>', '<?php echo __('Direct', 'js-support-ticket'); ?>', '<?php echo __('Email', 'js-support-ticket'); ?>', { role: 'annotation' } ],
<?php echo jssupportticket::$_data['stack_data']; ?>
            ]);
                    var view = new google.visualization.DataView(data);
                    var options = {
                    width: '95%',
                            //height: 400,
                            legend: { position: 'top', maxLines: 3 },
                            bar: { groupWidth: '75%' },
                            isStacked: true,
                    };
                    var chart = new google.visualization.ColumnChart(document.getElementById("stack_chart"));
                    chart.draw(view, options);
            }

    google.setOnLoadCallback(drawPie3d1Chart);
            function drawPie3d1Chart() {
            var data = google.visualization.arrayToDataTable([
                    ['<?php echo __('Departments', 'js-support-ticket'); ?>', '<?php echo __('Tickets By Department', 'js-support-ticket'); ?>'],
<?php echo jssupportticket::$_data['pie3d_chart1']; ?>
            ]);
                    var options = {
                    title: '<?php echo __('Ticket by departments', 'js-support-ticket'); ?>',
                            chartArea :{width:450, height:350, top:80, left:80},
                            is3D: true,
                    };
                    var chart = new google.visualization.PieChart(document.getElementById('pie3d_chart1'));
                    chart.draw(data, options);
            }

    google.setOnLoadCallback(drawPie3d2Chart);
            function drawPie3d2Chart() {
            var data = google.visualization.arrayToDataTable([
                    ['<?php echo __('Priorities', 'js-support-ticket'); ?>', '<?php echo __('Tickets By Priority', 'js-support-ticket'); ?>'],
<?php echo jssupportticket::$_data['pie3d_chart2']; ?>
            ]);
                    var options = {
                    title: '<?php echo __('Tickets by priorities', 'js-support-ticket'); ?>',
                            chartArea :{width:450, height:350, top:80, left:80},
                            is3D: true,
                    };
                    var chart = new google.visualization.PieChart(document.getElementById('pie3d_chart2'));
                    chart.draw(data, options);
            }

    google.setOnLoadCallback(drawStackChartHorizontal);
            function drawStackChartHorizontal() {
            var data = google.visualization.arrayToDataTable([
<?php
echo jssupportticket::$_data['stack_chart_horizontal']['title'] . ',';
echo jssupportticket::$_data['stack_chart_horizontal']['data'];
?>
            ]);
                    var view = new google.visualization.DataView(data);
                    var options = {
                    legend: { position: 'top', maxLines: 3 },
                            bar: { groupWidth: '75%' },
                            isStacked: true
                    };
                    var chart = new google.visualization.BarChart(document.getElementById("stack_chart_horizontal"));
                    chart.draw(view, options);
            }
</script>
<?php JSSTmessage::getMessage(); ?>
<span class="js-admin-title"><?php echo __("Overall Report", 'js-support-ticket') ?></span>
<div id="bar_chart" style="height:500px;width:100%; "></div>
<div class="js-col-md-6">
    <span class="js-admin-subtitle box1"><?php echo __('Tickets By Departments', 'js-support-ticket'); ?></span>
    <div id="pie3d_chart1" style="height:400px;width:100%;"></div>
</div>
<div class="js-col-md-6">
    <span class="js-admin-subtitle box2"><?php echo __('Tickets By Priorities', 'js-support-ticket'); ?></span>
    <div id="pie3d_chart2" style="height:400px;width:100%;"></div>
</div>
<div class="js-col-md-6">
    <span class="js-admin-subtitle box3"><?php echo __('Tickets By Status And Priorities', 'js-support-ticket'); ?></span>
    <div id="stack_chart_horizontal" style="height:400px;width:100%;"></div>
</div>
<div class="js-col-md-6">
    <span class="js-admin-subtitle box4"><?php echo __('Tickets By Channel', 'js-support-ticket'); ?></span>
    <div id="stack_chart" style="height:400px;width:100%;"></div>
</div>
