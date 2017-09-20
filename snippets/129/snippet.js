
$(document).ready(function() {
    
  // Docs at http://simpleweatherjs.com
  $.simpleWeather({
    location: 'New York, NY',
    woeid: '',
    unit: 'f',
    success: function(weather) {
      current = weather.temp+'° <i class="icon-'+weather.code+'"></i>';
      hiTemp = 'Hi '+weather.high+'°';
      wind = weather.wind.speed+' '+weather.units.speed;

      $("#weather-widget #current").html(current);
      $("#weather-widget #hiTemp").html(hiTemp);
      $("#weather-widget #wind").html(wind);
    },
    error: function(error) {
      $("#weather").html('<p>'+error+'</p>');
    }
  });
  
  

  //Docs at http://www.chartjs.org 
    var pie_data = [
        {
            value: 300,
            color:"#4DAF7C",
            highlight: "#55BC75",
            label: "Video"
        },
        {
            value: 50,
            color: "#EAC85D",
            highlight: "#f9d463",
            label: "Audio"
        },
        {
            value: 100,
            color: "#E25331",
            highlight: "#f45e3d",
            label: "Photos"
        },
        {
            value: 35,
            color: "#F4EDE7",
            highlight: "#e0dcd9",
            label: "Remaining"
        }
    ]
    
    var line_data = {
    labels: ["10:00am", "10:05am", "10:10am", "10:15am", "10:20am", "10:25am", "10:30am", "10:35am", "10:40am", "10:45am", "10:50am", "10:55am", "11:00am", "11:05am"],
    datasets: [
        {
            label: "My Second dataset",
            fillColor: "rgba(77, 175, 124,1)",
            strokeColor: "rgba(255,255,255,1)",
            pointColor: "rgba(255,255,255,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: [107.18, 107.13, 107.00, 106.89, 106.91, 107.12, 107.06, 107.04, 107.10, 107.14, 107.16, 107.20, 107.21, 107.26]
        }
    ]
    };
    
    
    var bar_data = {
    labels: ["Monday", "Tuesday", "Wednesday", "Thrusday", "May", "June", "July"],
    datasets: [
        {
            fillColor: "rgba(226,83,49,1)",
            strokeColor: "rgba(226,83,49,1)",
            highlightFill: "rgba(226,83,49,0.5)",
            highlightStroke: "rgba(226,83,49,0.5)",
            data: [65, 59, 80, 81, 56, 55, 40]
        }
    ]
    };
    
    
    // PIE CHART WIDGET
    var ctx = document.getElementById("myPieChart").getContext("2d");
    var myDoughnutChart = new Chart(ctx).Doughnut(pie_data,
            {
                responsive:true, 
                tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %> Gb"
            });
    
    
    // LINE CHART WIDGET
    var ctx2 = document.getElementById("myLineChart").getContext("2d");
    var myLineChart = new Chart(ctx2).Line(line_data,
            {
                responsive:true,
                scaleShowGridLines : false,
                scaleShowLabels: false,
                showScale: false,
                pointDot : true,
                bezierCurveTension : 0.2,
                pointDotStrokeWidth : 1,
                pointHitDetectionRadius : 5,
                datasetStroke : false,
                tooltipTemplate: "<%= value %><%if (label){%> - <%=label%><%}%>"
            });
            
        // BAR CHART ON LINE WIDGET    
        var ctx3 = document.getElementById("myBarChart").getContext("2d");
        var myBarChart = new Chart(ctx3).Bar(bar_data,
            {
                responsive:true,
                scaleShowGridLines : false,
                scaleShowLabels: false,
                showScale: false,
                pointDot : true, 
                datasetStroke : false,
                tooltipTemplate: "<%= value %><%if (label){%> - <%=label%><%}%>"
            });
    
});