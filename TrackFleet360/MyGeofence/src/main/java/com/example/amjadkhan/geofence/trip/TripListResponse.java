package com.example.amjadkhan.geofence.trip;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class TripListResponse {

        @SerializedName("trip_count")
        @Expose
        private Integer tripCount;
        @SerializedName("status")
        @Expose
        private String status;
        @SerializedName("test_trips")
        @Expose
        private List<Trip> trips = null;

        public Integer getTripCount() {
            return tripCount;
        }

        public void setTripCount(Integer tripCount) {
            this.tripCount = tripCount;
        }

        public String getStatus() {
            return status;
        }

        public void setStatus(String status) {
            this.status = status;
        }

        public List<Trip> getTrips() {
            return trips;
        }

        public void setTrips(List<Trip> trips) {
            this.trips = trips;
        }

    }






