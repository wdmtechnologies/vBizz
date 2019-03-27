package com.example.amjadkhan.geofence.trip;

import com.google.gson.annotations.SerializedName;

public class NewTripResponse {

    @SerializedName("error")
    public String error;

    @SerializedName("trip_id")
    public String tripId;



    public String getError() {
        return error;
    }

    public String getTripId() {
        return tripId;
    }
}
