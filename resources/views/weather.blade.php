@extends('app')
@section('content')
@section('pageTitle', 'Weather Forecast')


<label for="lat">Lat:</label>
<input id="lat" type="text" v-model="lat"  />

<label for="longitude">Lon:</label>
<input id="longitude" type="text" v-model="longitude" />

<button  class="btn btn-secondary btn-sm" @click="callOpenApi">Refresh</button>



<!-- <p>@{{ name }}</p> -->

<div class="flex-wrap">
    <span class="message">@{{message}}</span>
</div><br>

<!-- <div class="flex-wrap">
    <span class="city">@{{city}}</span>
</div> -->
<div class="flex-wrap">
    <span class="city">@{{charthescription}}</span>
</div>

<div class="flex-wrap rows">
    <div class="chart" id="chart">
        <canvas id="weather-chart"  ></canvas>
    </div>
    
    <table>
        <thead>
            <th>Date</th>
            <th>Duration</th>
            <th>Temperature</th>
            <th>Weather</th>
            <th>WindSpeed</th>
            <th>WindDirection</th>

            <th>Description</th>
        </thead>
        <tbody>
            <tr v-for="row in weatherData">
                <td>@{{ row.date }}</td>
                <td>@{{ row.name }}</td>
                <td>@{{ row.temperature }}</td>
                <td><img :src="`${row.weather}`" alt="Weather" style="width: 53%;" /></td>
                <td>@{{ row.wind }}</td>
                <td>@{{ row.windDirection }}</td>
                <td>@{{ row.description }}</td>

            </tr>
        </tbody>
    </table>
</div>




<script>


new Vue({

  el: '#app',
  mounted() {
    
    this.callOpenApi()
    },
  data: {
    lat: '38.8894' ,
    longitude:'-77.0352',
    message:'',
    city:'',
    charthescription:'',
    weatherData: [],
    
   
  },

  methods:{

      callOpenApi(){
        this.weatherData=[]
        var chart = document.getElementById('chart');
        var div = document.getElementById('weather-chart');
        div.remove();
        // $("#chart").after('<canvas id="weather-chart"></canvas>');
        chart.innerHTML = '<canvas id="weather-chart"></canvas>';
        this.message='';

        var url = "https://api.weather.gov/points/"+this.lat+","+this.longitude;
       
        axios.get(url).then(response => {
            console.log(response);
            this.city = response.data.properties.relativeLocation.properties['city']+" ,"+response.data.properties.relativeLocation.properties['state']+" ";
            this.city=this.city+" Weather Status";
            this.charthescription = this.city;

            this.forecast(response.data.properties['forecast']);

          }) .catch(error => {
              this.message = "Error :" + error.response.data.status +" ,"+error.response.data.title

                // console.log(error.response.data.status)
            });
      },

       forecast(url) {

        axios.get(url).then(response => {
                // console.log(response.data.properties.periods)
                this.loadchart(response.data.properties.periods);

          });
      },


      loadchart(charthata){
        console.log(charthata)

        var chartdata_from_date = '';
        var chartdata_to_date = '';

        var label_temperature = [];
        var label_windspeed = [];
        var label_duration = [];
        
        this.weatherData = [];
        for(var i = 0 ; i<charthata.length; i++){
            let dt = new Date(charthata[i].endTime);
            dt = dt.toLocaleString('en-US', { timeZone: 'America/New_York' })

            if(i==0){
                chartdata_from_date = moment(dt).format("MMM Do YY");    
            }
            if(i== charthata.length-1){
                chartdata_to_date = moment(dt).format("MMM Do YY"); 
            }
           

            momentdt = moment(dt).format("ddd, hA");      
            dt = dt.split(",")
            var time = dt[1];
            var date = dt[0];
            let day = date+": "+charthata[i].name;
            
            console.log(time)
            label_temperature.push(charthata[i].temperature);
            label_duration.push(momentdt);
            // label_windspeed.push(charthata[i].windSpeed)


    
            
            this.weatherData.push({
                name: charthata[i].name,
                temperature: charthata[i].temperature,
                weather: charthata[i].icon,
                wind: charthata[i].windSpeed,
                description: charthata[i].shortForecast,
                windDirection:charthata[i].windDirection,
                date:momentdt

            });

        }
        this.charthescription = this.charthescription +" : From "+chartdata_from_date+" To "+chartdata_to_date

        console.log(this.weatherData);
        


        var data = {
        labels: label_duration,
        datasets: [{
            label: 'Temperature(°F)',
            // backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: label_temperature,
            fill: true,
        }],
   
        };


        // const footer = (tooltipItems) => {
        //     let sum = 0;
        //     count = 0 ; 
        //     tooltipItems.forEach(function(tooltipItem) {
        //         // console.log(label_windspeed);
        //         sum += tooltipItem.parsed.y;
        //     });
        //     return 'Temperature: ' + sum;
        // };

        // console.log(footer);

        const config = {
        type: 'line',
        data,
         
        options: {
            interaction: {
                intersect: false,
                mode: 'index',
            },
        // plugins: {
        //     tooltip: {
        //         callbacks: {
        //         footer: footer,
        //         }
        //     }
        // }
        },
        
        };

        var myChart = new Chart(
            document.getElementById('weather-chart'),
            config
        );

      }
    
  }
})
</script>

@endsection

