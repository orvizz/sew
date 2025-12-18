class Ciudad{
    #nombre;
    #pais;
    #gentilicio
    #poblacion;
    #latCentro;
    #lonCentro;
    constructor(nombre, pais, gentilicio){
        this.#nombre = nombre;
        this.#pais = pais;
        this.#gentilicio = gentilicio;
    }

    rellenarDatos(poblacion, latCentro, lonCentro){
        this.#poblacion = poblacion;
        this.#latCentro = latCentro;
        this.#lonCentro = lonCentro;
    }

    getNombre(){
        return this.#nombre;
    }

    getPais(){
        return this.#pais;
    }

    getGentilicioYPoblacion(){
        return `<li>Gentilicio: ${this.#gentilicio}</li><li>Población: ${this.#poblacion} habitantes</li>`;
    }

    writeTitle() {
        const h3 = document.createElement("h3");
        h3.textContent = `Ciudad: ${this.getNombre()} (${this.getPais()})`;
        document.body.appendChild(h3);
    }

    writeGentilicioYPoblacion(){
        const ul = document.createElement("ul");
        ul.innerHTML = this.getGentilicioYPoblacion();
        document.body.appendChild(ul);
    }

    writeCoordenadas(){
        const p = document.createElement("p");
        p.textContent = `Coordenadas: Longitud ${this.#lonCentro} - Latitud ${this.#latCentro}`;
        document.body.appendChild(p);
    }

    getMeteorologiaCarrera(){
        let endpoint = "https://archive-api.open-meteo.com/v1/archive?";
        let data_requested = "&daily=sunrise,sunset&hourly=temperature_2m,apparent_temperature,relative_humidity_2m,wind_speed_10m,wind_direction_10m,rain&timezone=Europe%2FLondon";
        let date = "&start_date=2025-05-25&end_date=2025-05-25"
        $.ajax({
            url: endpoint + "latitude="+this.#latCentro + "&longitude=" + this.#lonCentro + date + data_requested,
            method: "GET",
            dataType: "json",
            success: (data) => {
                console.log(data);
                this.procesarJSONCarrera(data);
                this.getMeteorologiaEntrenos();
            },
            error: function (error) {
                console.error("Error al obtener los datos meteorológicos:", error);
            }
        });
    }

    procesarJSONCarrera(apiResponse) {
        if (!apiResponse) throw new Error("No se recibió respuesta de la API");

        const {
            latitude,
            longitude,
            timezone,
            elevation,
            hourly,
            daily,
            hourly_units,
            daily_units,
        } = apiResponse;

        // Procesar datos horarios (arrays paralelos)
        const hourlyData = hourly.time.map((t, index) => ({
            time: new Date(t),
            temperature: hourly.temperature_2m[index],
            apparentTemperature: hourly.apparent_temperature[index],
            windSpeed: hourly.wind_speed_10m?.[index],
            windDirection: hourly.wind_direction_10m?.[index],
            humidity: hourly.relative_humidity_2m?.[index],
            units: hourly_units,
            rain: hourly.rain?.[index] || 0
        }));

        console.log("Datos horarios:");
        console.log(hourlyData);

        // Procesar datos diarios
        const dailyData = daily.time.map((t, index) => ({
            date: new Date(t),
            sunrise: new Date(daily.sunrise[index]),
            sunset: new Date(daily.sunset[index]),
            units: daily_units
        }));

        console.log("Datos diarios:");
        console.log(dailyData);

        for (let hour of hourlyData) {
            const hourStr = hour.time.toTimeString().split(" ")[0];
            console.log(`Hora: ${hourStr}, Temp: ${hour.temperature}°C, Sensación: ${hour.apparentTemperature}°C, Viento: ${hour.windSpeed} km/h, Humedad: ${hour.humidity}%, Rain: ${hour.rain} mm`);
            $("main").append(document.createElement("section").innerHTML = `
                <h4>Hora: ${hourStr}</h4>
                <ul>
                    <li>Temperatura: ${hour.temperature} °C</li>
                    <li>Temperatura Aparente: ${hour.apparentTemperature} °C</li>
                    <li>Precipitación: ${hour.rain} mm</li>
                    <li>Velocidad del Viento: ${hour.windSpeed} km/h</li>
                    <li>Dirección del Viento: ${hour.windDirection} °</li>
                    <li>Humedad Relativa: ${hour.humidity} %</li>
                </ul>
            `);
        }
        // Construimos una salida organizada
        return {
            location: {
                latitude,
                longitude,
                timezone,
                elevation
            },
            hourly: hourlyData,
            daily: dailyData
        };
    }

    getMeteorologiaEntrenos() {
        let endpoint = "https://archive-api.open-meteo.com/v1/archive?";
        let data_requested = "&hourly=temperature_2m,relative_humidity_2m,wind_speed_10m,rain&timezone=Europe%2FLondon";
        let date = "&start_date=2025-05-22&end_date=2025-05-24"
        $.ajax({
            url: endpoint + "latitude="+this.#latCentro + "&longitude=" + this.#lonCentro + date + data_requested,
            method: "GET",
            dataType: "json",
            success: (data) => {
                console.log(data);
                this.procesarJSONEntrenos(data);
            },
            error: function (error) {
                console.error("Error al obtener los datos meteorológicos:", error);
            }
        });
    }

    procesarJSONEntrenos(apiResponse) {
        if (!apiResponse) throw new Error("No se recibió respuesta de la API");
        const {
            latitude,
            longitude,
            timezone,
            elevation,
            hourly,
            hourly_units,
        } = apiResponse;
        // Procesar datos horarios (arrays paralelos)
        const hourlyData = hourly.time.map((t, index) => ({
            time: new Date(t),
            temperature: hourly.temperature_2m[index],
            windSpeed: hourly.wind_speed_10m?.[index],
            humidity: hourly.relative_humidity_2m?.[index],
            units: hourly_units,
            rain: hourly.rain?.[index] || 0
        }));
        console.log("Datos horarios de entrenos:");
        console.log(hourlyData);
        // Agrupar por día y calcular medias para cada variable
        const aggregates = {};
        for (const h of hourlyData) {
            // Ajustar la hora a la zona GMT+2 antes de extraer la fecha
            const offsetHours = 2; // GMT+2
            const localDate = new Date(h.time.getTime() + offsetHours * 60 * 60 * 1000);
            const dateStr = localDate.toISOString().split('T')[0];
            if (!aggregates[dateStr]) {
                aggregates[dateStr] = {
                    tempSum: 0, tempCount: 0,
                    windSum: 0, windCount: 0,
                    humSum: 0, humCount: 0,
                    rainSum: 0, rainCount: 0
                };
            }
            const a = aggregates[dateStr];
            if (typeof h.temperature === 'number' && !isNaN(h.temperature)) { a.tempSum += h.temperature; a.tempCount++; }
            if (typeof h.windSpeed === 'number' && !isNaN(h.windSpeed)) { a.windSum += h.windSpeed; a.windCount++; }
            if (typeof h.humidity === 'number' && !isNaN(h.humidity)) { a.humSum += h.humidity; a.humCount++; }
            if (typeof h.rain === 'number' && !isNaN(h.rain)) { a.rainSum += h.rain; a.rainCount++; }
        }

        const dailyAverages = Object.keys(aggregates).sort().map(date => {
            const a = aggregates[date];
            return {
                date,
                avgTemperature: a.tempCount ? +(a.tempSum / a.tempCount).toFixed(2) : null,
                avgWindSpeed: a.windCount ? +(a.windSum / a.windCount).toFixed(2) : null,
                avgHumidity: a.humCount ? +(a.humSum / a.humCount).toFixed(2) : null,
                avgRain: a.rainCount ? +(a.rainSum / a.rainCount).toFixed(2) : 0
            };
        });

        console.log('Medias diarias de entrenos:', dailyAverages);

        // Presentar en el DOM (main o body si no existe)
        const main = document.querySelector('main') || document.body;
        for (const d of dailyAverages) {
            
            $("main").append(document.createElement("section").innerHTML = `
                <h4>Día: ${d.date}</h4>
                <ul>
                    <li>Temperatura media: ${d.avgTemperature} °C</li>
                    <li>Precipitación media: ${d.avgRain} mm</li>
                    <li>Velocidad media del viento: ${d.avgWindSpeed} km/h</li>
                    <li>Humedad media: ${d.avgHumidity} %</li>
                </ul>
            `);
        }

        return {
            location: { latitude, longitude, timezone, elevation },
            hourly: hourlyData,
            dailyAverages
        };
    }
}
