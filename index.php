<?php
include_once('src/Controller/CsvController.php');

$csvController = new \App\Controller\CsvController();
$data = $csvController->csvGetRecordsByCountry();

if(isset($_POST["upload"]))
{
    if($_FILES['file']['name'])
    {
        $csvController->csvUpload();
        $info_code = 'File imported';
        header('Location:/');
    }
}
?>

<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        <?php

        echo "var jsArray = ". $data . ";\n";

        ?>
        var regex = new RegExp("(.*?)\.(csv)$");

        function validateCSV(el) {
            if (!(regex.test(el.value.toLowerCase()))) {
                el.value = '';
                alert('Please select correct file format');
            }
        }

if (jsArray.length > 0) {
    google.charts.load('current', {packages: ['corechart', 'bar']});
    google.charts.setOnLoadCallback(drawMultSeries);

    function drawMultSeries() {
        chartArray = [['Country', 'Number of people']];
        for (var i = 0; i < jsArray.length; i++) {
            count = jsArray[i].number;
            country = jsArray[i].country;
            data = [country, parseInt(count)];
            chartArray.push(data);
        }
        var data = google.visualization.arrayToDataTable(chartArray);

        var options = {
            title: 'Number of people by country',
            chartArea: {width: '50%'},
            hAxis: {
                title: 'Number of people',
                minValue: 0
            },
            vAxis: {
                title: 'Country'
            }
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
}
    </script>
</head>
<body>



<form method="post" enctype="multipart/form-data" action="">
    <div align="center">
        <label>Import CSV:</label>
        <input type="file" name="file" class="btn" accept=".csv" onchange='validateCSV(this)'/>
        <br />
        <input type="submit" name="upload" value="Import" class="btn btn-info" />
    </div>
</form>

<div id="chart_div" style="width: 100%; height: 500px;"></div>
</body>
</html>