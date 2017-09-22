;
/**
 *
 * @param args
 * @constructor
 */
function Hcharts(args) {
    this.api = args.api;
    this.container = args.container;
    this.title = args.title;
    this.subtitle = args.subtitle;
    this.param = args.param;
}

Hcharts.prototype = {
    //饼图
    showPie: function(){
        var _this = this;
        $.getJSON(this.api, this.param, function(data) {
            Highcharts.chart(_this.container, {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: _this.title
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: data.data.series
            });

        });
    },
    //曲线图
    showSpline: function() {
        var _this = this;
        $.getJSON(this.api, this.param, function(data) {
            Highcharts.chart(_this.container, {
                chart: {
                    type: 'spline'
                },
                title: {
                    text: _this.title
                },
                subtitle: {
                    text: _this.subtitle
                },
                xAxis: {'categories':data.data.xAxis},
                yAxis: {
                },
                tooltip: {
                    shared: true
                },
                plotOptions: {
                    spline: {
                        marker: {
                            enabled: true
                        }
                    }
                },
                series:data.data.series
            });
        })
    }
};

