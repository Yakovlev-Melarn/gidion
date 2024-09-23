(function ($) {
    "use strict"
    var dzSparkLine = function () {
        let draw = Chart.controllers.line.__super__.draw;
        //var screenWidth = $(window).width();
        var lineChart3 = function (resultData) {
            if (jQuery('#lineChart_3').length > 0) {
                const lineChart_3 = document.getElementById("lineChart_3").getContext('2d');
                const lineChart_3gradientStroke1 = lineChart_3.createLinearGradient(500, 0, 100, 0);
                lineChart_3gradientStroke1.addColorStop(0, "rgb(255,171,45)");
                lineChart_3gradientStroke1.addColorStop(1, "rgb(255,171,45)");
                const lineChart_3gradientStroke2 = lineChart_3.createLinearGradient(500, 0, 100, 0);
                lineChart_3gradientStroke2.addColorStop(0, "rgb(106,255,0)");
                lineChart_3gradientStroke2.addColorStop(1, "rgba(106,255,0)");
                Chart.controllers.line = Chart.controllers.line.extend({
                    draw: function () {
                        draw.apply(this, arguments);
                        let nk = this.chart.chart.ctx;
                        let _stroke = nk.stroke;
                        nk.stroke = function () {
                            nk.save();
                            nk.shadowColor = 'rgba(0, 0, 0, 0)';
                            nk.shadowBlur = 10;
                            nk.shadowOffsetX = 0;
                            nk.shadowOffsetY = 10;
                            _stroke.apply(this, arguments)
                            nk.restore();
                        }
                    }
                });
                lineChart_3.height = 100;
                new Chart(lineChart_3, {
                    type: 'line',
                    data: {
                        defaultFontFamily: 'Poppins',
                        labels: ["00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23"],
                        datasets: [
                            {
                                label: "Заказали",
                                data: resultData.orders,
                                borderColor: lineChart_3gradientStroke1,
                                borderWidth: "2",
                                backgroundColor: 'transparent',
                                pointBackgroundColor: 'rgb(255,171,45)'
                            }, {
                                label: "Выкупили",
                                data: resultData.sales,
                                borderColor: lineChart_3gradientStroke2,
                                borderWidth: "2",
                                backgroundColor: 'transparent',
                                pointBackgroundColor: 'rgb(106,255,0)'
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        legend: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    min: 0,
                                    stepSize: resultData.stepSize,
                                    padding: 10
                                }
                            }],
                            xAxes: [{
                                ticks: {
                                    padding: 5
                                }
                            }]
                        }
                    }
                });
            }
        }
        return {
            init: function () {
            },
            load: function (data) {
                lineChart3(data);
            },
            resize: function () {
                // barChart1();
                // barChart2();
                // barChart3();
                // lineChart1();
                // lineChart2();
                // lineChart3();
                // lineChart03();
                // areaChart1();
                // areaChart2();
                // areaChart3();
                // radarChart();
                // pieChart();
                // doughnutChart();
                // polarChart();
            }
        }

    }();


    jQuery(window).on('load', function () {
        $.post('/', {date: $('.selectedDate').val()}).done(function (data) {
            dzSparkLine.load(data);
        });
    });

    /*jQuery(window).on('resize', function () {
        //dzSparkLine.resize();
        setTimeout(function () {
            dzSparkLine.resize();
        }, 1000);
    });*/

})(jQuery);
