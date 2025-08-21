@if ($view == 'dashboard')
    <script>
        // Revenue
        if ($('#revenue_charts').length > 0) {
            var sColStacked = {
                chart: {
                    height: 360,
                    type: 'bar',
                    stacked: true,
                    toolbar: {
                        show: false,
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {}
                }],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 5,
                        borderRadiusWhenStacked: 'all',
                        endingShape: 'rounded',
                        columnWidth: '40%'
                    },
                },
                legend: {
                    show: false,
                },
                dataLabels: {
                    enabled: false
                },
                label: {
                    show: false,
                },
                colors: ['#7539FF', '#F8F5FF'],
                series: [{
                    name: 'Outstanding',
                    data: {!! $data_sales_received !!}
                }, {
                    name: 'Received ',
                    data: {!! $data_sales !!}
                }, ],
                grid: {
                    borderColor: '#E2E4E6',
                    strokeDashArray: 5,
                    padding: {
                        right: -10,
                        left: -10,
                    },
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
                },
                yaxis: {
                    min: 0,
                    labels: {
                        formatter: function(val) {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                maximumFractionDigits: 0
                            }).format(val);
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
            }

            var chart = new ApexCharts(
                document.querySelector("#revenue_charts"),
                sColStacked
            );

            chart.render();
        }
    </script>
    <script>
        if ($('#chart_sales').length > 0) {
            var options = {
                series: [35, 40, 25], // Percentages for each section
                chart: {
                    type: 'donut',
                    height: 300,
                },
                labels: ['Dell XPS 13', 'Nike T-shirt', 'Apple iPhone 15'], // Labels for the data
                colors: ['#F38BBB', '#5297FE', '#7DCEA0'], // Colors from the image
                plotOptions: {
                    pie: {
                        startAngle: -110, // Start from the top
                        endAngle: 110, // End at the bottom
                        donut: {
                            size: '60%',
                            labels: {
                                show: false,
                                total: {
                                    show: true,
                                    label: 'Leads',
                                    formatter: function(w) {
                                        return '589';
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: false,
                },
                label: {
                    show: false,
                }
            };

            var chart = new ApexCharts(document.querySelector("#chart_sales"), options);
            chart.render();
        }
    </script>
@endif
