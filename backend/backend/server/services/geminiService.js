class LocationService {
    constructor() {
        this.locations = [
            {
                street: "V&A Waterfront",
                description: "Popular waterfront with shops and restaurants.",
                riskTimes: "Best visited during daylight hours",
                position: {
                    lat: -33.9033,
                    lng: 18.4197
                }
            },
            {
                street: "Long Street",
                description: "Historic street with cafes and boutiques.",
                riskTimes: "Busiest during business hours",
                position: {
                    lat: -33.9201,
                    lng: 18.4183
                }
            },
            {
                street: "Camps Bay Strip",
                description: "Scenic beachfront with dining options.",
                riskTimes: "Most active from morning to sunset",
                position: {
                    lat: -33.9507,
                    lng: 18.3783
                }
            }
        ];
    }

    analyzeDangerZones(startLocation, endLocation) {
        // Simply return the hardcoded locations without any API calls
        return Promise.resolve(this.locations);
    }
}

// Export an instance of LocationService instead of GeminiService
module.exports = new LocationService();