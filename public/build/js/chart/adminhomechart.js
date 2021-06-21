window.onload = function () {

    var app = new Vue({
        el: '#app',
        delimiters: ['${', '}'],
        data: {
            isLoading: true
        },

        watch: {
        },

        mounted (){
            self = this
            axios.get('/xhr-admin-get-chart')
                .then(function (response){
                    console.log(response)
                    self.isLoading = false
                    self.createPieChart(response.data.categorie_chart,"cat")
                    self.createPieChart(response.data.source_chart,"source")
                    self.createLineChart(response.data.date_chart)
                })
                .catch(function (error){
                    self.isLoading = false
                    console.log(error)
                })
        },

        beforeDestroy() {
            // remove pickadate according to its API
        },

        methods : {
            createPieChart(data,type){
                let labels = []
                let datas = []
                let colors = []

                if (type === "cat"){
                    for (let i = 0; i < data.length; i++){
                        labels.push(data[i].nom);
                        datas.push(data[i].recueils_count)
                        colors.push(data[i].color)
                    }
                }

                if (type === "source"){
                    for (let i = 0; i < data.length; i++){
                        labels.push(data[i].libelle);
                        datas.push(data[i].recueils_count)
                        colors.push(data[i].color)
                    }
                }
                console.log(labels)
                console.log(datas)

                if (type === "cat"){
                    var chartDiv = $("#catChart");
                }
                if (type === "source"){
                    var chartDiv = $("#sourceChart");
                }
                var myChart = new Chart(chartDiv, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                data: datas,
                                backgroundColor: colors
                            }]
                    },
                    options: {
                        title: {
                            display: true,
                            text: 'Pie Chart'
                        },
                        responsive: true,
                        maintainAspectRatio: false,
                    }
                });
            },

            createLineChart(data){

                let labels = []
                let datas = []

                for (let i = 0; i < data.length; i++){
                    labels.push(data[i].date);
                    datas.push(data[i].recueils_total)
                }

                var ctx = document.getElementById("dayChart").getContext('2d');

                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Nombre  de recueils',
                            data: datas,
                            fill: false,
                            borderColor: '#2196f3', // Add custom color border (Line)
                            backgroundColor: '#2196f3', // Add custom color background (Points and Fill)
                            borderWidth: 1 // Specify bar border width
                        }]},
                    options: {
                        responsive: true, // Instruct chart js to respond nicely.
                        maintainAspectRatio: false, // Add to prevent default behaviour of full-width/height
                    }
                });
            }
        }
    })
}